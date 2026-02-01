<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcaraUjianHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBeritaAcaraUjianHasilController extends Controller
{
    // ========================================
    // INDEX - List BA milik mahasiswa
    // ========================================

    public function index()
    {
        $user = Auth::user();

        $beritaAcaras = BeritaAcaraUjianHasil::where('mahasiswa_id', $user->id)
            ->with([
                'jadwalUjianHasil.pendaftaranUjianHasil',
                'jadwalUjianHasil.dosenPenguji',
                'penilaians.dosen',
            ])
            ->latest()
            ->paginate(10);

        return view('user.berita-acara-ujian-hasil.index', compact('beritaAcaras'));
    }

    // ========================================
    // SHOW - Detail BA dengan nilai & koreksi (real-time)
    // ========================================

    public function show(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        // Check if this BA belongs to current user
        if ($beritaAcara->mahasiswa_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke Berita Acara ini.');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil',
            'jadwalUjianHasil.dosenPenguji',
            'penilaians.dosen',
            'lembarKoreksis.dosen',
        ]);

        // Get penilaian summary
        $penilaianSummary = $this->getPenilaianSummary($beritaAcara);

        // Get lembar koreksi (hanya dari PS1/PS2)
        $lembarKoreksis = $beritaAcara->lembarKoreksis()
            ->with('dosen')
            ->get();

        return view('user.berita-acara-ujian-hasil.show', compact(
            'beritaAcara',
            'penilaianSummary',
            'lembarKoreksis'
        ));
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get penilaian summary untuk mahasiswa
     */
    private function getPenilaianSummary(BeritaAcaraUjianHasil $beritaAcara): array
    {
        $penilaians = $beritaAcara->penilaians()->with('dosen')->get();

        if ($penilaians->isEmpty()) {
            return [
                'total_penguji' => 0,
                'submitted' => 0,
                'average_mutu' => null,
                'details' => [],
            ];
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;
        $totalPenguji = $jadwal ? $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count() : 0;

        // Map penilaian dengan posisi
        $details = $penilaians->map(function ($p) use ($jadwal) {
            // Get posisi from jadwal
            $posisi = null;
            $posisiOrder = 999; // Default untuk sorting
            
            if ($jadwal) {
                $pengujiData = $jadwal->dosenPenguji()
                    ->where('users.id', $p->dosen_id)
                    ->first();
                $posisi = $pengujiData?->pivot?->posisi;
                
                // Extract nomor dari posisi untuk sorting (e.g., "Penguji 1" => 1)
                if ($posisi) {
                    if (preg_match('/Penguji (\d+)/', $posisi, $matches)) {
                        $posisiOrder = (int) $matches[1];
                    } elseif (strpos($posisi, 'PS1') !== false) {
                        $posisiOrder = 4;
                    } elseif (strpos($posisi, 'PS2') !== false) {
                        $posisiOrder = 5;
                    } elseif (strpos($posisi, 'Ketua') !== false) {
                        $posisiOrder = 0;
                    }
                }
            }

            return [
                'dosen_name' => $p->dosen?->name ?? 'Unknown',
                'posisi' => $posisi,
                'posisi_order' => $posisiOrder,
                'nilai_mutu' => $p->nilai_mutu,
                'grade' => $p->grade_letter,
                'catatan' => $p->catatan,
                'submitted_at' => $p->updated_at?->format('d M Y H:i'),
            ];
        })->sortBy('posisi_order')->values()->toArray();

        return [
            'total_penguji' => $totalPenguji,
            'submitted' => $penilaians->count(),
            'average_mutu' => $penilaians->whereNotNull('nilai_mutu')->avg('nilai_mutu'),
            'details' => $details,
        ];
    }

    // ========================================
    // DOWNLOAD PDF - Download BA yang sudah selesai
    // ========================================

    public function downloadPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        // Check if this BA belongs to current user
        if ($beritaAcara->mahasiswa_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke Berita Acara ini.');
        }

        // Check if BA is completed and has file
        if ($beritaAcara->status !== 'selesai' || !$beritaAcara->file_path) {
            return redirect()->back()->with('error', 'Dokumen Berita Acara belum tersedia untuk diunduh.');
        }

        if (!\Storage::disk('local')->exists($beritaAcara->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fileName = 'Berita_Acara_Ujian_Hasil_' . str_replace(' ', '_', $beritaAcara->mahasiswa_name) . '.pdf';

        return response()->download(
            \Storage::disk('local')->path($beritaAcara->file_path),
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }

    // ========================================
    // VIEW PDF - View BA inline di browser
    // ========================================

    public function viewPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        // Check if this BA belongs to current user
        if ($beritaAcara->mahasiswa_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke Berita Acara ini.');
        }

        // Check if BA is completed and has file
        if ($beritaAcara->status !== 'selesai' || !$beritaAcara->file_path) {
            return redirect()->back()->with('error', 'Dokumen Berita Acara belum tersedia untuk dilihat.');
        }

        if (!\Storage::disk('local')->exists($beritaAcara->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file(
            \Storage::disk('local')->path($beritaAcara->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }

    // ========================================
    // DOWNLOAD KEPUTUSAN PANITIA PDF
    // ========================================

    public function downloadKeputusanPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        // Check if this BA belongs to current user
        if ($beritaAcara->mahasiswa_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Check if BA is completed
        if (!$beritaAcara->isSelesai()) {
            return redirect()->back()->with('error', 'Keputusan Panitia hanya tersedia setelah berita acara selesai.');
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;

        // Generate PDF on the fly
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keputusan-panitia-ujian-hasil.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
        ]);

        $fileName = 'Keputusan_Panitia_' . str_replace(' ', '_', $beritaAcara->mahasiswa_name) . '.pdf';

        return $pdf->download($fileName);
    }

    // ========================================
    // VIEW KEPUTUSAN PANITIA PDF - View inline
    // ========================================

    public function viewKeputusanPdf(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        // Check if this BA belongs to current user
        if ($beritaAcara->mahasiswa_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Check if BA is completed
        if (!$beritaAcara->isSelesai()) {
            return redirect()->back()->with('error', 'Keputusan Panitia hanya tersedia setelah berita acara selesai.');
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;

        // Generate PDF on the fly
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keputusan-panitia-ujian-hasil.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
        ]);

        return $pdf->stream('Keputusan_Panitia.pdf');
    }
}
