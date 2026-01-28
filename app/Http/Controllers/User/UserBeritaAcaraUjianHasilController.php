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
                'average_nilai' => null,
                'details' => [],
            ];
        }

        $jadwal = $beritaAcara->jadwalUjianHasil;
        $totalPenguji = $jadwal ? $jadwal->dosenPenguji()
            ->where('posisi', '!=', 'Ketua Penguji')
            ->count() : 0;

        return [
            'total_penguji' => $totalPenguji,
            'submitted' => $penilaians->count(),
            'average_nilai' => $penilaians->whereNotNull('total_nilai')->avg('total_nilai'),
            'details' => $penilaians->map(function ($p) use ($jadwal) {
                // Get posisi from jadwal
                $posisi = null;
                if ($jadwal) {
                    $pengujiData = $jadwal->dosenPenguji()
                        ->where('users.id', $p->dosen_id)
                        ->first();
                    $posisi = $pengujiData?->pivot?->posisi;
                }

                return [
                    'dosen_name' => $p->dosen?->name ?? 'Unknown',
                    'posisi' => $posisi,
                    'total_nilai' => $p->total_nilai,
                    'grade' => $p->grade_letter,
                    'catatan' => $p->catatan,
                    'submitted_at' => $p->updated_at?->format('d M Y H:i'),
                ];
            })->toArray(),
        ];
    }
}
