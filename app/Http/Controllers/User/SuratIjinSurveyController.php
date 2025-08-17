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
            if (Auth::id() !== $surat->mahasiswa_id) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengunduh surat ini.');
            }

            if ($surat->status !== 'sudah_diambil') {
                return redirect()->back()->with('error', 'Anda harus mengkonfirmasi penerimaan surat terlebih dahulu sebelum mengunduh.');
            }

            if (!$surat->file_surat_path) {
                return redirect()->back()->with('error', 'File surat belum dihasilkan.');
            }

            $filePath = storage_path('app/public/' . $surat->file_surat_path);
            if (!file_exists($filePath)) {
                Log::error('File PDF tidak ditemukan untuk surat ID: ' . $surat->id . ' di path: ' . $filePath);
                return redirect()->back()->with('error', 'File surat tidak ditemukan.');
            }

            $tracking = TrackingSurat::where('surat_type', SuratIjinSurvey::class)
                ->where('surat_id', $surat->id)
                ->where('aksi', 'sudah_diambil')
                ->first();

            $downloadDate = $tracking && $tracking->confirmed_at
                ? $tracking->confirmed_at->format('Ymd')
                : now()->format('Ymd');

            $filename = 'Surat_Ijin_Survey_' . $surat->mahasiswa->nim . '_' . $downloadDate . '.pdf';

            return response()->download($filePath, $filename);
        } catch (\Exception $e) {
            Log::error('Error saat download PDF untuk surat ID: ' . $surat->id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }

    protected function getDefaultRedirectRoute(): string
    {
        return 'user.surat-ijin-survey.index';
    }
}