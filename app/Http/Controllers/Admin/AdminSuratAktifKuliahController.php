<?php

namespace App\Http\Controllers\Admin;

use App\Models\StatusSurat;
use App\Models\TrackingSurat;
use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSuratAktifKuliahRequest;
use Illuminate\Support\Facades\Auth;

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
            'trackings' => function ($query) {
                $query->latest();
            },
            'penandatangan'
        ]);

        return view('admin.surat-aktif-kuliah.show', compact('surat'));
    }

    public function updateStatus(UpdateSuratAktifKuliahRequest $request, SuratAktifKuliah $surat)
    {
        $validated = $request->validated();

        // Update status
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

        // Buat tracking
        TrackingSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'aksi' => $validated['status'],
            'keterangan' => $validated['catatan_admin'] ?? 'Status diubah',
            'mahasiswa_id' => $surat->mahasiswa_id,
        ]);

        // Jika status disetujui, generate nomor surat dan tanggal
        if ($validated['status'] === 'disetujui') {
            $surat->update([
                'nomor_surat' => $this->generateNomorSurat(),
                'tanggal_surat' => now(),
                'penandatangan_id' => $validated['penandatangan_id'],
                'jabatan_penandatangan' => $validated['jabatan_penandatangan'],
            ]);
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

        return sprintf('%03d', $count + 1) . '/SAK/FTI/' . $month . '/' . $year;
    }


}