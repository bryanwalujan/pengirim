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

        return view('user.surat-pindah.create', compact('service', 'tahunAjaranAktif'));
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

        $this->authorize('view', $surat);

        return view('user.surat-pindah.show', compact('surat'));
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

            $tracking = TrackingSurat::where('surat_type', SuratPindah::class)
                ->where('surat_id', $surat->id)
                ->where('aksi', 'sudah_diambil')
                ->first();

            $downloadDate = $tracking && $tracking->confirmed_at
                ? $tracking->confirmed_at->format('Ymd')
                : now()->format('Ymd');

            $filename = 'Surat_Pindah_' . $surat->mahasiswa->nim . '_' . $downloadDate . '.pdf';

            return response()->download($filePath, $filename);
        } catch (\Exception $e) {
            Log::error('Error saat download PDF untuk surat ID: ' . $surat->id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }

    protected function getDefaultRedirectRoute(): string
    {
        return 'user.surat-pindah.index';
    }
}