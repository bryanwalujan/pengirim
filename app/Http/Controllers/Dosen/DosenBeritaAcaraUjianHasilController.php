<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaAcaraUjianHasil\StorePenilaianRequest;
use App\Http\Requests\BeritaAcaraUjianHasil\StoreLembarKoreksiRequest;
use App\Models\BeritaAcaraUjianHasil;
use App\Models\PenilaianUjianHasil;
use App\Models\LembarKoreksiSkripsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DosenBeritaAcaraUjianHasilController extends Controller
{
    /**
     * Self-healing: Ensure PS1 and PS2 are included in the penguji list
     */
    private function ensurePembimbingIncludedInPenguji($jadwal): void
    {
        if (!$jadwal || !$jadwal->pendaftaranUjianHasil) {
            return;
        }

        $pendaftaran = $jadwal->pendaftaranUjianHasil;
        $changes = false;
        $dosenData = [];

        if ($pendaftaran->dosen_pembimbing1_id) {
            $exists = $jadwal->dosenPenguji()
                ->where('users.id', $pendaftaran->dosen_pembimbing1_id)
                ->exists();

            if (!$exists) {
                $dosenData[$pendaftaran->dosen_pembimbing1_id] = [
                    'posisi' => 'Penguji 4 (PS1)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $changes = true;
            }
        }

        if ($pendaftaran->dosen_pembimbing2_id) {
            $exists = $jadwal->dosenPenguji()
                ->where('users.id', $pendaftaran->dosen_pembimbing2_id)
                ->exists();

            if (!$exists) {
                $dosenData[$pendaftaran->dosen_pembimbing2_id] = [
                    'posisi' => 'Penguji 5 (PS2)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $changes = true;
            }
        }

        if ($changes && !empty($dosenData)) {
            $jadwal->dosenPenguji()->syncWithoutDetaching($dosenData);

            Log::info('✅ Self-healing (Dosen): Added missing Pembimbing to Penguji List', [
                'jadwal_id' => $jadwal->id,
                'added_dosen' => array_keys($dosenData),
            ]);
        }
    }
    // ========================================
    // INDEX - List BA yang perlu di-review oleh dosen
    // ========================================

    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all BA where dosen is assigned as penguji
        $beritaAcaras = BeritaAcaraUjianHasil::whereHas('jadwalUjianHasil', function ($query) use ($user) {
            $query->whereHas('dosenPenguji', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })
            ->with([
                'jadwalUjianHasil.pendaftaranUjianHasil.user',
                'jadwalUjianHasil.dosenPenguji',
                'mahasiswa',
                'penilaians',
            ])
            ->latest()
            ->paginate(10);

        return view('dosen.berita-acara-ujian-hasil.index', compact('beritaAcaras'));
    }

    // ========================================
    // SHOW - Detail BA
    // ========================================

    public function show(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->jadwalUjianHasil) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.index')
                ->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);

        // Check if user is penguji for this BA
        $this->authorizeAccess($beritaAcara, $user->id);

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
            'mahasiswa',
            'penilaians.dosen',
            'lembarKoreksis.dosen',
        ]);

        // Get current user's role in this BA
        $pengujiInfo = $this->getPengujiInfo($beritaAcara, $user->id);
        $myPenilaian = $beritaAcara->getPenilaianFrom($user->id);
        $myKoreksi = $beritaAcara->getLembarKoreksiFrom($user->id);

        return view('dosen.berita-acara-ujian-hasil.show', compact(
            'beritaAcara',
            'pengujiInfo',
            'myPenilaian',
            'myKoreksi'
        ));
    }

    // ========================================
    // PENILAIAN - Form & Store
    // ========================================

    public function showPenilaian(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->jadwalUjianHasil) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.index')
                ->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);

        // Check if user is penguji for this BA
        $this->authorizeAccess($beritaAcara, $user->id);

        // Check if already submitted
        $existingPenilaian = $beritaAcara->getPenilaianFrom($user->id);

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'mahasiswa',
        ]);

        $pengujiInfo = $this->getPengujiInfo($beritaAcara, $user->id);

        return view('dosen.berita-acara-ujian-hasil.fill-penilaian', compact(
            'beritaAcara',
            'pengujiInfo',
            'existingPenilaian'
        ));
    }

    public function storePenilaian(StorePenilaianRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->jadwalUjianHasil) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.index')
                ->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);

        // Check if user is penguji for this BA
        $this->authorizeAccess($beritaAcara, $user->id);

        // Check if BA is in correct status
        if (!$beritaAcara->isMenungguTtdPenguji()) {
            return back()->with('error', 'Berita Acara tidak dalam status menunggu penilaian.');
        }

        try {
            // Update or create penilaian
            $penilaian = PenilaianUjianHasil::updateOrCreate(
                [
                    'berita_acara_ujian_hasil_id' => $beritaAcara->id,
                    'dosen_id' => $user->id,
                ],
                $request->validated()
            );

            Log::info('✅ Penilaian submitted', [
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
                'total_nilai' => $penilaian->total_nilai,
            ]);

            // Check if this is PS1/PS2 and needs koreksi
            if ($beritaAcara->isPembimbing($user->id)) {
                return redirect()
                    ->route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara)
                    ->with('success', 'Penilaian berhasil disimpan. Sebagai Pembimbing, silakan lanjutkan mengisi Lembar Koreksi.');
            }

            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('❌ Failed to submit penilaian', [
                'error' => $e->getMessage(),
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
            ]);

            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    // ========================================
    // LEMBAR KOREKSI - Form & Store (Khusus PS1/PS2)
    // ========================================

    public function showKoreksi(BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->jadwalUjianHasil) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.index')
                ->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);

        // Check if user is PS1/PS2
        if (!$beritaAcara->isPembimbing($user->id)) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('error', 'Lembar Koreksi hanya dapat diisi oleh Dosen Pembimbing (PS1/PS2).');
        }

        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'mahasiswa',
        ]);

        $existingKoreksi = $beritaAcara->getLembarKoreksiFrom($user->id);
        $pengujiInfo = $this->getPengujiInfo($beritaAcara, $user->id);

        return view('dosen.berita-acara-ujian-hasil.fill-koreksi', compact(
            'beritaAcara',
            'pengujiInfo',
            'existingKoreksi'
        ));
    }

    public function storeKoreksi(StoreLembarKoreksiRequest $request, BeritaAcaraUjianHasil $beritaAcara)
    {
        $user = Auth::user();

        if (!$beritaAcara->jadwalUjianHasil) {
            return redirect()
                ->route('dosen.berita-acara-ujian-hasil.index')
                ->with('error', 'Jadwal ujian tidak ditemukan.');
        }

        $this->ensurePembimbingIncludedInPenguji($beritaAcara->jadwalUjianHasil);

        // Check if user is PS1/PS2
        if (!$beritaAcara->isPembimbing($user->id)) {
            return back()->with('error', 'Lembar Koreksi hanya dapat diisi oleh Dosen Pembimbing (PS1/PS2).');
        }

        // Check if BA is in correct status
        if (!$beritaAcara->isMenungguTtdPenguji()) {
            return back()->with('error', 'Berita Acara tidak dalam status menunggu penilaian.');
        }

        try {
            // Update or create lembar koreksi
            $koreksi = LembarKoreksiSkripsi::updateOrCreate(
                [
                    'berita_acara_ujian_hasil_id' => $beritaAcara->id,
                    'dosen_id' => $user->id,
                ],
                [
                    'koreksi_data' => $request->getFormattedKoreksiData(),
                ]
            );

            Log::info('✅ Lembar Koreksi submitted', [
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
                'total_koreksi' => $koreksi->total_koreksi,
            ]);

            return redirect()
                ->route('admin.berita-acara-ujian-hasil.show', $beritaAcara)
                ->with('success', 'Lembar Koreksi berhasil disimpan. Anda dapat melanjutkan untuk menandatangani Berita Acara.');

        } catch (\Exception $e) {
            Log::error('❌ Failed to submit lembar koreksi', [
                'error' => $e->getMessage(),
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
            ]);

            return back()->with('error', 'Gagal menyimpan lembar koreksi: ' . $e->getMessage());
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if user has access to this BA
     */
    private function authorizeAccess(BeritaAcaraUjianHasil $beritaAcara, int $userId): void
    {
        $jadwal = $beritaAcara->jadwalUjianHasil;

        if (!$jadwal) {
            abort(404, 'Jadwal tidak ditemukan.');
        }

        $isPenguji = $jadwal->dosenPenguji()
            ->where('users.id', $userId)
            ->exists();

        if (!$isPenguji) {
            abort(403, 'Anda tidak memiliki akses ke Berita Acara ini.');
        }
    }

    /**
     * Get penguji info for current user
     */
    private function getPengujiInfo(BeritaAcaraUjianHasil $beritaAcara, int $userId): ?object
    {
        $jadwal = $beritaAcara->jadwalUjianHasil;

        if (!$jadwal) {
            return null;
        }

        return $jadwal->dosenPenguji()
            ->where('users.id', $userId)
            ->first();
    }
}
