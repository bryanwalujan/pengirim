<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateSuratAktifKuliahRequest;

class AdminSuratAktifKuliahController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

        $surats = SuratAktifKuliah::with(['mahasiswa', 'status'])
            ->when($status, function ($query) use ($status) {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%")
                        ->orWhereHas('mahasiswa', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                                ->orWhere('nim', 'like', "%$search%");
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.surat-aktif-kuliah.index', compact('surats', 'status', 'search'));
    }

    public function show(SuratAktifKuliah $surat)
    {
        $surat->load([
            'mahasiswa',
            'status',
            'trackings' => fn($query) => $query->latest(),
            'penandatangan',
        ]);

        $penandatangans = User::role('dosen')->get();

        return view('admin.surat-aktif-kuliah.show', compact('surat', 'penandatangans'));
    }

    public function update(Request $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:50',
            'tanggal_surat' => 'nullable|date',
            'penandatangan_id' => 'nullable|exists:users,id',
        ]);

        $surat->update($validated);

        return redirect()->route('admin.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    public function updateStatus(UpdateSuratAktifKuliahRequest $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validated();

        StatusSurat::updateOrCreate(
            [
                'surat_type' => SuratAktifKuliah::class,
                'surat_id' => $surat->id,
            ],
            [
                'status' => $validated['status'],
                'catatan_admin' => $validated['catatan_admin'],
                'catatan_internal' => $validated['catatan_internal'] ?? null,
                'updated_by' => Auth::id(),
            ]
        );

        TrackingSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'aksi' => $validated['status'],
            'keterangan' => $validated['catatan_admin'],
            'mahasiswa_id' => $surat->mahasiswa_id,
        ]);

        if (in_array($validated['status'], ['disetujui', 'siap_diambil'])) {
            $surat->update([
                'penandatangan_id' => $validated['penandatangan_id'],
                'jabatan_penandatangan' => $validated['jabatan_penandatangan'],
                'nomor_surat' => $surat->nomor_surat ?? $this->generateNomorSurat(),
                'tanggal_surat' => $surat->tanggal_surat ?? now(),
            ]);
        }

        if ($validated['status'] === 'siap_diambil') {
            // Generate or assign file_surat_path
            $surat->update(['file_surat_path' => $this->generateSuratFile($surat)]);
        }

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui');
    }

    protected function generateNomorSurat()
    {
        $year = date('Y');
        $month = date('m');
        $count = SuratAktifKuliah::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        return sprintf('%03d/SAK/FTI/%s/%s', $count + 1, $month, $year);
    }

    protected function generateSuratFile(SuratAktifKuliah $surat)
    {
        // Placeholder: Implement PDF generation (e.g., using DomPDF)
        $path = 'surat-aktif-kuliah/' . $surat->id . '_' . time() . '.pdf';
        // Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }
}