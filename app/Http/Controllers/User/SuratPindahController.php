<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Service;
use App\Models\StatusSurat;
use App\Models\SuratPindah;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Traits\ChecksPendingSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SuratNotificationHelper;
use App\Http\Requests\SuratPindahRequest;
use App\Notifications\SuratTakenNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuratPindahController extends Controller
{
    use AuthorizesRequests, ChecksPendingSurat;

    public function __construct()
    {
        $this->initializeChecksPendingSurat();
    }

    public function index(Request $request)
    {
        $service = Service::where('slug', 'surat-pindah')->firstOrFail();

        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();


        $surats = SuratPindah::with(['status', 'trackings', 'mahasiswa'])
            ->where('mahasiswa_id', Auth::id())
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nomor_surat', 'like', '%' . $request->search . '%')
                        ->orWhere('universitas_tujuan', 'like', '%' . $request->search . '%')
                        ->orWhere('alasan_pengajuan', 'like', '%' . $request->search . '%')
                        ->orWhere('keterangan_tambahan', 'like', '%' . $request->search . '%')
                        ->orWhere('semester', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            })
            ->latest()
            ->paginate(10);

        return view('user.surat-pindah.index', [
            'service' => $service,
            'surats' => $surats,
            'tahunAjaranAktif' => $tahunAjaranAktif,
        ]);
    }

    public function create()
    {
        // Check if user can submit new surat
        if ($redirect = $this->checkSubmissionPermission('user.surat-pindah.index')) {
            return $redirect;
        }

        $this->authorize('create', SuratPindah::class);
        $service = Service::where('slug', 'surat-pindah')->firstOrFail();
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();
        $kopSurat = \App\Models\KopSurat::first();

        // Hitung semester untuk preview
        $semesterRoman = 'I (Satu)';
        if ($tahunAjaranAktif && Auth::user()->nim) {
            $tahunMasuk = 2000 + (int) substr(Auth::user()->nim, 0, 2);
            $tahunParts = explode('/', $tahunAjaranAktif->tahun);
            $tahunMulai = (int) $tahunParts[0];
            $semesterNumber = ($tahunMulai - $tahunMasuk) * 2 + ($tahunAjaranAktif->semester === 'ganjil' ? 1 : 2);
            $semesterNumber = min(max($semesterNumber, 1), 14);

            $map = [
                1 => 'I (Satu)', 2 => 'II (Dua)', 3 => 'III (Tiga)', 4 => 'IV (Empat)',
                5 => 'V (Lima)', 6 => 'VI (Enam)', 7 => 'VII (Tujuh)', 8 => 'VIII (Delapan)',
                9 => 'IX (Sembilan)', 10 => 'X (Sepuluh)', 11 => 'XI (Sebelas)',
                12 => 'XII (Dua Belas)', 13 => 'XIII (Tiga Belas)', 14 => 'XIV (Empat Belas)'
            ];
            $semesterRoman = $map[$semesterNumber] ?? 'I (Satu)';
        }

        return view('user.surat-pindah.create', compact('service', 'tahunAjaranAktif', 'kopSurat', 'semesterRoman'));
    }

    public function store(SuratPindahRequest $request)
    {
        // Double-check before storing
        if ($redirect = $this->checkSubmissionPermission('user.surat-pindah.index')) {
            return $redirect;
        }

        $this->authorize('create', SuratPindah::class);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $surat = SuratPindah::create([
                'mahasiswa_id' => Auth::id(),
                'universitas_tujuan' => $validated['universitas_tujuan'],
                'alasan_pengajuan' => $validated['alasan_pengajuan'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'],
                'semester' => $validated['semester'],
            ]);

            // Handle File Uploads
            if ($request->hasFile('file_pendukung_path')) {
                $surat->attachDokumenPendukung($request->file('file_pendukung_path'));
            }

            // Generate Tracking Code
            $surat->tracking_code = Str::random(12);
            $surat->save();

            StatusSurat::create([
                'surat_type' => SuratPindah::class,
                'surat_id' => $surat->id,
                'status' => 'diajukan',
                'updated_by' => Auth::id(),
            ]);

            TrackingSurat::create([
                'surat_type' => SuratPindah::class,
                'surat_id' => $surat->id,
                'aksi' => 'diajukan',
                'keterangan' => 'Pengajuan surat pindah baru',
                'mahasiswa_id' => Auth::id(),
            ]);

            // Clear cache after successful submission
            $this->clearSubmissionCache();

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_pindah');
            }

            DB::commit();

            return redirect()->route('user.surat-pindah.index')
                ->with('success', 'Surat pindah berhasil diajukan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengajukan surat: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $surat = SuratPindah::with(['status', 'trackings', 'mahasiswa'])
            ->findOrFail($id);

        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();


        $this->authorize('view', $surat);

        return view('user.surat-pindah.show', compact('surat', 'tahunAjaranAktif'));
    }

    public function confirmTaken($id)
    {
        $surat = SuratPindah::with(['status'])
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
                    'surat_type' => SuratPindah::class,
                    'surat_id' => $surat->id,
                ],
                [
                    'status' => 'sudah_diambil',
                    'updated_by' => Auth::id(),
                ]
            );

            TrackingSurat::create([
                'surat_type' => SuratPindah::class,
                'surat_id' => $surat->id,
                'aksi' => 'sudah_diambil',
                'keterangan' => 'Surat telah diambil oleh mahasiswa',
                'mahasiswa_id' => $surat->mahasiswa_id,
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
                SuratNotificationHelper::clearSuratCache('surat_pindah');
            }

            DB::commit();

            return redirect()->route('user.surat-pindah.show', $surat->id)
                ->with('success', 'Surat telah dikonfirmasi sebagai sudah diambil. Sekarang Anda bisa mengunduh surat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengkonfirmasi surat: ' . $e->getMessage());
        }
    }

    public function download(SuratPindah $surat)
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
            $secureFilename = "Surat_Pindah_{$nim}_{$timestamp}_{$randomNumber}.pdf";

            // 5. Get file path and download
            $filePath = Storage::disk('public')->path($surat->file_surat_path);

            // 6. Log download activity
            Log::info('Surat pindah downloaded', [
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
            Log::error('Error downloading surat pindah: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }

    protected function getDefaultRedirectRoute(): string
    {
        return 'user.surat-pindah.index';
    }
}