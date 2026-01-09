<?php
// filepath: app/Http/Controllers/Admin/LembarCatatanSemproController.php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PelaksanaanUjianService;
use App\Models\BeritaAcaraSeminarProposal;
use App\Models\LembarCatatanSeminarProposal;

class LembarCatatanSemproController extends Controller
{
    protected PelaksanaanUjianService $pelaksanaanUjianService;

    public function __construct(PelaksanaanUjianService $pelaksanaanUjianService)
    {
        $this->pelaksanaanUjianService = $pelaksanaanUjianService;
    }

    /**
     * Show form to create/edit lembar catatan
     */
    public function create(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Check if user is one of the penguji
        $isPenguji = $beritaAcara->jadwalSeminarProposal
            ->dosenPenguji()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isPenguji) {
            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('error', 'Anda tidak memiliki akses untuk mengisi lembar catatan.');
        }

        // Check if already submitted
        $catatan = LembarCatatanSeminarProposal::where('berita_acara_seminar_proposal_id', $beritaAcara->id)
            ->where('dosen_id', $user->id)
            ->first();

        // Load relations
        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.komisiProposal',
        ]);

        return view('admin.lembar-catatan-sempro.create', compact('beritaAcara', 'catatan'));
    }

    /**
     * Store/Update lembar catatan
     */
    public function store(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validate access
        $isPenguji = $beritaAcara->jadwalSeminarProposal
            ->dosenPenguji()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isPenguji) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $validated = $request->validate([
            'catatan_kebaruan' => 'nullable|string|max:5000',
            'catatan_metode' => 'nullable|string|max:5000',
            'catatan_ketersediaan_data' => 'nullable|string|max:5000',
            'catatan_bab1' => 'nullable|string|max:5000',
            'catatan_bab2' => 'nullable|string|max:5000',
            'catatan_bab3' => 'nullable|string|max:5000',
            'catatan_jadwal' => 'nullable|string|max:2000',
            'catatan_referensi' => 'nullable|string|max:2000',
            'catatan_umum' => 'nullable|string|max:3000',
        ], [
            'catatan_kebaruan.max' => 'Catatan kebaruan maksimal 5000 karakter',
            'catatan_metode.max' => 'Catatan metode maksimal 5000 karakter',
            'catatan_ketersediaan_data.max' => 'Catatan ketersediaan data maksimal 5000 karakter',
            'catatan_bab1.max' => 'Catatan BAB I maksimal 5000 karakter',
            'catatan_bab2.max' => 'Catatan BAB II maksimal 5000 karakter',
            'catatan_bab3.max' => 'Catatan BAB III maksimal 5000 karakter',
        ]);

        try {
            $catatan = $this->pelaksanaanUjianService->submitCatatanDosen(
                $beritaAcara,
                $user->id,
                $validated
            );

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Lembar catatan berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan lembar catatan', [
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan lembar catatan: ' . $e->getMessage());
        }
    }

    /**
     * Show individual lembar catatan
     */
    public function show(LembarCatatanSeminarProposal $lembarCatatan)
    {
        $lembarCatatan->load([
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'dosen',
        ]);

        return view('admin.lembar-catatan-sempro.show', compact('lembarCatatan'));
    }

    /**
     * Edit lembar catatan (only if dosen is owner)
     */
    public function edit(LembarCatatanSeminarProposal $lembarCatatan)
    {
        $user = Auth::user();

        if ($lembarCatatan->dosen_id !== $user->id) {
            return redirect()
                ->route('admin.berita-acara-sempro.show', $lembarCatatan->beritaAcara)
                ->with('error', 'Anda tidak memiliki akses untuk mengedit catatan ini.');
        }

        // Check if berita acara already signed
        if ($lembarCatatan->beritaAcara->isSigned()) {
            return back()->with('error', 'Catatan tidak dapat diedit setelah Berita Acara ditandatangani.');
        }

        $beritaAcara = $lembarCatatan->beritaAcara;
        $catatan = $lembarCatatan;

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.komisiProposal',
        ]);

        return view('admin.lembar-catatan-sempro.create', compact('beritaAcara', 'catatan'));
    }

    /**
     * Update lembar catatan
     */
    public function update(Request $request, LembarCatatanSeminarProposal $lembarCatatan)
    {
        $user = Auth::user();

        if ($lembarCatatan->dosen_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($lembarCatatan->beritaAcara->isSigned()) {
            return back()->with('error', 'Catatan tidak dapat diedit setelah ditandatangani.');
        }

        $validated = $request->validate([
            'catatan_kebaruan' => 'nullable|string|max:5000',
            'catatan_metode' => 'nullable|string|max:5000',
            'catatan_ketersediaan_data' => 'nullable|string|max:5000',
            'catatan_bab1' => 'nullable|string|max:5000',
            'catatan_bab2' => 'nullable|string|max:5000',
            'catatan_bab3' => 'nullable|string|max:5000',
            'catatan_jadwal' => 'nullable|string|max:2000',
            'catatan_referensi' => 'nullable|string|max:2000',
            'catatan_umum' => 'nullable|string|max:3000',
        ]);

        try {
            $lembarCatatan->update($validated);

            return redirect()
                ->route('admin.berita-acara-sempro.show', $lembarCatatan->beritaAcara)
                ->with('success', 'Lembar catatan berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui lembar catatan.');
        }
    }

    /**
     * Delete lembar catatan
     */
    public function destroy(LembarCatatanSeminarProposal $lembarCatatan)
    {
        $user = User::find(Auth::id());

        // Only owner or admin can delete
        if ($lembarCatatan->dosen_id !== $user->id && !$user->hasRole('super-admin')) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        if ($lembarCatatan->beritaAcara->isSigned()) {
            return back()->with('error', 'Catatan tidak dapat dihapus setelah ditandatangani.');
        }

        try {
            $beritaAcaraId = $lembarCatatan->berita_acara_seminar_proposal_id;
            $lembarCatatan->delete();

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcaraId)
                ->with('success', 'Lembar catatan berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus lembar catatan.');
        }
    }
}