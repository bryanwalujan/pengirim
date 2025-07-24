<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiProposal;
use App\Models\KopSurat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AdminKomisiProposalController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan.
     * Dapat diakses oleh admin dan staff.
     */
    public function index(Request $request)
    {
        $query = KomisiProposal::with(['user', 'pembimbing'])->latest();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $komisiProposals = $query->paginate(10);

        return view('admin.komisi-proposal.index', compact('komisiProposals'));
    }

    /**
     * Menampilkan detail satu pengajuan.
     * Dapat diakses oleh admin dan staff.
     */
    public function show(KomisiProposal $komisiProposal)
    {
        // For AJAX requests (modal), return partial view
        if (request()->ajax()) {
            return view('admin.komisi-proposal.detail-modal', [
                'komisi' => $komisiProposal
            ]);
        }

        // For regular requests, return full view (if still needed)
        return view('admin.komisi-proposal.show', [
            'komisiProposal' => $komisiProposal
        ]);
    }

    /**
     * Memperbarui status pengajuan (approve/reject).
     * Dapat diakses oleh admin dan staff.
     */
    /**
     * Memperbarui status pengajuan (approve/reject).
     * Dapat diakses oleh admin dan staff.
     */
    public function updateStatus(Request $request, KomisiProposal $komisiProposal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan' => 'nullable|string',
        ]);

        $komisiProposal->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        // Automatically generate PDF if status is approved
        if ($request->status == 'approved') {
            $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
                'komisi' => $komisiProposal,
            ]);

            $filename = 'persetujuan-komisi-proposal-' . $komisiProposal->user->nim . '.pdf';
            $directory = 'komisi_proposal';

            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store PDF in public storage
            $path = $directory . '/' . $filename;
            Storage::disk('public')->put($path, $pdf->output());

            // Update path file di database (store relative path)
            $komisiProposal->update(['file_komisi' => $path]);
        }

        return redirect()->route('admin.komisi-proposal.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Generate PDF persetujuan.
     * Dapat diakses oleh admin dan staff.
     */
    public function generatePdf(KomisiProposal $komisiProposal)
    {
        if ($komisiProposal->status !== 'approved') {
            return redirect()->back()->with('error', 'Persetujuan hanya bisa digenerate untuk pengajuan yang berstatus APPROVED.');
        }

        $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
            'komisi' => $komisiProposal,
        ]);

        $filename = 'persetujuan-komisi-proposal-' . $komisiProposal->user->nim . '.pdf';
        $directory = 'komisi_proposal';

        // Create directory if it doesn't exist
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store PDF in public storage
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        // Update path file di database (store relative path)
        $komisiProposal->update(['file_komisi' => $path]);

        // Download PDF
        return $pdf->download($filename);
    }

    /**
     * Download PDF yang sudah digenerate
     */
    public function downloadPdf(KomisiProposal $komisiProposal)
    {
        if (!$komisiProposal->file_komisi) {
            return redirect()->back()->with('error', 'File persetujuan belum tersedia.');
        }

        $path = storage_path('app/public/' . $komisiProposal->file_komisi);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($path);
    }

    // Add this method to AdminKomisiProposalController.php
    // public function previewPdf()
    // {
    //     // Create a dummy KomisiProposal for preview purposes
    //     $dummyKomisi = new KomisiProposal();
    //     $dummyKomisi->judul_skripsi = "Judul Skripsi Contoh untuk Preview";

    //     // Create dummy relationships
    //     $dummyKomisi->user = (object) [
    //         'name' => 'Nama Mahasiswa Contoh',
    //         'nim' => 'NIM123456'
    //     ];

    //     $dummyKomisi->pembimbing = (object) [
    //         'name' => 'Dr. Pembimbing Contoh, M.Kom.',
    //         'nip' => '1234567890'
    //     ];


    //     $pdf = Pdf::loadView('admin.komisi-proposal.pdf', [
    //         'komisi' => $dummyKomisi,
    //     ]);

    //     return $pdf->stream('preview-komisi-proposal.pdf');
    // }

}