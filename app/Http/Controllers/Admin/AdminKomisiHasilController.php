<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiHasil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminKomisiHasilController extends Controller
{
    public function index(Request $request)
    {
        $query = KomisiHasil::with(['user', 'pembimbing1', 'pembimbing2'])->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $komisiHasils = $query->paginate(10);
        return view('admin.komisi-hasil.index', compact('komisiHasils'));
    }

    public function show(KomisiHasil $komisiHasil)
    {
        // For AJAX requests (modal), return partial view
        if (request()->ajax()) {
            return view('admin.komisi-hasil.detail-modal', [
                'komisi' => $komisiHasil
            ]);
        }

        // For regular requests, return full view (if still needed)
        return view('admin.komisi-hasil.show', [
            'komisiHasil' => $komisiHasil
        ]);
    }

    public function updateStatus(Request $request, KomisiHasil $komisiHasil)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan' => 'nullable|string',
        ]);

        $komisiHasil->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        // Auto generate PDF jika approved
        if ($request->status == 'approved') {
            $this->generatePdf($komisiHasil);
        }

        return redirect()->route('admin.komisi-hasil.index')
            ->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function generatePdf(KomisiHasil $komisiHasil)
    {
        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
        ]);

        $filename = 'persetujuan-komisi-hasil-' . $komisiHasil->user->nim . '.pdf';
        $directory = 'komisi_hasil';

        Storage::disk('public')->makeDirectory($directory);
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        $komisiHasil->update(['file_komisi_hasil' => $path]);

        return $pdf->download($filename);
    }

    public function downloadPdf(KomisiHasil $komisiHasil)
    {
        if (!$komisiHasil->file_komisi_hasil) {
            return back()->with('error', 'File persetujuan belum tersedia.');
        }

        $path = storage_path('app/public/' . $komisiHasil->file_komisi_hasil);
        return response()->download($path);
    }

    public function previewPdf()
    {
        // Create a dummy KomisiProposal for preview purposes
        $dummyKomisi = new KomisiHasil();
        $dummyKomisi->judul_skripsi = "Judul Skripsi Contoh untuk Preview";

        // Create dummy relationships
        $dummyKomisi->user = (object) [
            'name' => 'Nama Mahasiswa Contoh',
            'nim' => 'NIM123456'
        ];

        $dummyKomisi->pembimbing1 = (object) [
            'name' => 'Dr. Pembimbing Contoh, M.Kom.',
            'nip' => '1234567890'
        ];

        $dummyKomisi->pembimbing2 = (object) [
            'name' => 'Dr. Pembimbing Contoh, M.Kom.',
            'nip' => '1234567890'
        ];


        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $dummyKomisi,
        ]);

        return $pdf->stream('preview-komisi-hasil.pdf');
    }
}