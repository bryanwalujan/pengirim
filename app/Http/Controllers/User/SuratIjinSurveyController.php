<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Service;
use App\Models\StatusSurat;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratIjinSurvey;
use App\Traits\ChecksPendingSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SuratNotificationHelper;
use App\Http\Requests\SuratIjinSurveyRequest;
use App\Notifications\SuratTakenNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuratIjinSurveyController extends Controller
{
    use AuthorizesRequests, ChecksPendingSurat;

    public function __construct()
    {
        $this->initializeChecksPendingSurat();
    }

    public function index(Request $request)
    {
        $service = Service::where('slug', 'surat-ijin-survey')->firstOrFail();

        $surats = SuratIjinSurvey::with(['status', 'trackings', 'mahasiswa'])
            ->where('mahasiswa_id', Auth::id())
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nomor_surat', 'like', '%' . $request->search . '%')
                        ->orWhere('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('tempat_survey', 'like', '%' . $request->search . '%')
                        ->orWhere('keterangan_tambahan', 'like', '%' . $request->search . '%')
                        ->orWhere('semester', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            })
            ->when($request->semester, function ($query) use ($request) {
                $query->where('semester', $request->semester);
            })
            ->latest()
            ->paginate(10);

        return view('user.surat-ijin-survey.index', [
            'service' => $service,
            'surats' => $surats,
        ]);
    }

    public function create()
    {
        // Check if user can submit new surat
        if ($redirect = $this->checkSubmissionPermission('user.surat-ijin-survey.index')) {
            return $redirect;
        }

        $this->authorize('create', SuratIjinSurvey::class);
        $service = Service::where('slug', 'surat-ijin-survey')->firstOrFail();
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();
        return view('user.surat-ijin-survey.create', compact('service', 'tahunAjaranAktif'));
    }

    public function store(SuratIjinSurveyRequest $request)
    {
        // Double-check before storing
        if ($redirect = $this->checkSubmissionPermission('user.surat-ijin-survey.index')) {
            return $redirect;
        }

        $this->authorize('create', SuratIjinSurvey::class);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $surat = SuratIjinSurvey::create([
                'mahasiswa_id' => Auth::id(),
                'judul' => $validated['judul'],
                'tempat_survey' => $validated['tempat_survey'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'],
                'semester' => $validated['semester'],
            ]);

            // Handle file uploads
            if ($request->hasFile('file_pendukung_path')) {
                $surat->attachDokumenPendukung($request->file('file_pendukung_path'));
            }

            // Generate Tracking Code
            $surat->tracking_code = Str::random(12);
            $surat->save();

            StatusSurat::create([
                'surat_type' => SuratIjinSurvey::class,
                'surat_id' => $surat->id,
                'status' => 'diajukan',
                'updated_by' => Auth::id(),
            ]);

            TrackingSurat::create([
                'surat_type' => SuratIjinSurvey::class,
                'surat_id' => $surat->id,
                'aksi' => 'diajukan',
                'keterangan' => 'Pengajuan surat ijin survey baru',
                'mahasiswa_id' => Auth::id(),
            ]);


            // Clear cache after successful submission
            $this->clearSubmissionCache();

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_ijin_survey');
            }

            DB::commit();

            return redirect()->route('user.surat-ijin-survey.index')
                ->with('success', 'Surat ijin survey berhasil diajukan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengajukan surat: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $surat = SuratIjinSurvey::with(['status', 'trackings', 'mahasiswa'])
            ->findOrFail($id);

        $this->authorize('view', $surat);

        return view('user.surat-ijin-survey.show', compact('surat'));
    }

    public function confirmTaken($id)
    {
        $surat = SuratIjinSurvey::with(['status'])
            ->where('mahasiswa_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($surat->status !== 'siap_diambil') {
            return redirect()->back()
                ->with('error', 'Surat belum siap diambil atau sudah diambil sebelumnya');
        }

        DB::beginTransaction();
        try {
            StatusSurat::updateOrCreate(
                [
                    'surat_type' => SuratIjinSurvey::class,
                    'surat_id' => $surat->id,
                ],
                [
                    'status' => 'sudah_diambil',
                    'updated_by' => Auth::id(),
                ]
            );

            TrackingSurat::create([
                'surat_type' => SuratIjinSurvey::class,
                'surat_id' => $surat->id,
                'aksi' => 'sudah_diambil',
                'keterangan' => 'Surat telah diambil oleh mahasiswa',
                'mahasiswa_id' => Auth::id(),
                'confirmed_at' => now(),
            ]);

            $staffs = User::role('staff')->get();
            foreach ($staffs as $staff) {
                $staff->notify(new SuratTakenNotification($surat));
            }

            // Clear cache after status change
            $this->clearSubmissionCache();

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_ijin_survey');
            }

            DB::commit();

            return redirect()->route('user.surat-ijin-survey.show', $surat->id)
                ->with('success', 'Surat telah dikonfirmasi sebagai sudah diambil. Sekarang Anda bisa mengunduh surat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengkonfirmasi surat: ' . $e->getMessage());
        }
    }

    public function download(SuratIjinSurvey $surat)
    {
        try {
            // 1. Security Check: Verify ownership
            if (Auth::id() !== $surat->mahasiswa_id) {
                Log::warning('Unauthorized download attempt', [
                    'user_id' => Auth::id(),
                    'surat_id' => $surat->id,
                    'ip' => request()->ip()
                ]);
                abort(403, 'Akses ditolak.');
            }

            // 2. Security Check: Verify status
            if ($surat->status !== 'sudah_diambil') {
                return redirect()->back()->with('error', 'Anda harus mengkonfirmasi penerimaan surat terlebih dahulu.');
            }

            // 3. Security Check: Verify file exists
            if (!$surat->file_surat_path || !Storage::disk('public')->exists($surat->file_surat_path)) {
                return redirect()->back()->with('error', 'File surat tidak ditemukan.');
            }

            // 4. Generate secure filename with random number
            $nim = preg_replace('/[^a-zA-Z0-9]/', '', $surat->mahasiswa->nim ?? 'unknown');
            $randomNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $timestamp = now()->format('Ymd_His');
            $secureFilename = "Surat_Ijin_Survey_{$nim}_{$timestamp}_{$randomNumber}.pdf";

            // 5. Get file path and download
            $filePath = Storage::disk('public')->path($surat->file_surat_path);

            // 6. Log download activity
            Log::info('Surat ijin survey downloaded', [
                'user_id' => Auth::id(),
                'surat_id' => $surat->id,
                'filename' => $secureFilename,
                'ip' => request()->ip()
            ]);

            // 7. Return secure download with headers
            return response()->download(
                $filePath,
                $secureFilename,
                [
                    'Content-Type' => 'application/pdf',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Error downloading surat ijin survey: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }

    protected function getDefaultRedirectRoute(): string
    {
        return 'user.surat-ijin-survey.index';
    }
}