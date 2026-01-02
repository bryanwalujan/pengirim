<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcaraSeminarProposal;
use App\Models\JadwalSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraSeminarProposalController extends Controller
{
    /**
     * Display listing of berita acara for current mahasiswa
     */
    public function index()
    {
        $user = Auth::user();

        // Get semua jadwal seminar proposal milik mahasiswa yang sudah punya berita acara
        $beritaAcaras = BeritaAcaraSeminarProposal::whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with([
                'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
                'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
                'jadwalSeminarProposal.dosenPenguji',
                'ketuaPenguji',
                'lembarCatatan.dosen',
            ])
            ->latest()
            ->get();

        // Get jadwal yang sudah selesai tapi belum ada BA (untuk info)
        $jadwalSelesaiTanpaBA = JadwalSeminarProposal::whereHas('pendaftaranSeminarProposal', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', 'selesai')
            ->doesntHave('beritaAcaraSeminarProposal')
            ->count();

        // Statistics
        $stats = [
            'total' => $beritaAcaras->count(),
            'selesai' => $beritaAcaras->where('status', 'selesai')->count(),
            'proses' => $beritaAcaras->whereIn('status', ['draft', 'menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing'])->count(),
            'lulus' => $beritaAcaras->whereIn('keputusan', ['Ya', 'Ya, dengan perbaikan'])->count(),
        ];

        return view('user.berita-acara-sempro.index', compact('beritaAcaras', 'stats', 'jadwalSelesaiTanpaBA'));
    }

    /**
     * Display detail of specific berita acara
     */
    public function show(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validate ownership
        $mahasiswaId = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user_id;

        if ($mahasiswaId !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke berita acara ini.');
        }

        // Load all relationships
        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'dosenPembimbingPengisi',
            'dosenPembimbingPenandatangan',
            'ketuaPenguji',
            'lembarCatatan.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;

        // ✅ PERBAIKAN: Get dosen penguji dengan ORDER BY yang benar
        $dosenPenguji = $jadwal->dosenPenguji()
            ->orderByRaw("CASE WHEN dosen_penguji_jadwal_sempro.posisi = 'Ketua Pembahas' THEN 0 ELSE 1 END")
            ->orderByRaw("CAST(SUBSTRING_INDEX(dosen_penguji_jadwal_sempro.posisi, ' ', -1) AS UNSIGNED)")
            ->get();

        // ✅ TAMBAHAN: Set default prodi jika kosong
        $prodi = $pendaftaran->user->prodi ?? 'Teknik Informatika';

        return view('user.berita-acara-sempro.show', compact(
            'beritaAcara',
            'jadwal',
            'pendaftaran',
            'dosenPenguji',
            'prodi'
        ));
    }

    /**
     * Download PDF berita acara
     */
    public function downloadPdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validate ownership
        $mahasiswaId = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user_id;

        if ($mahasiswaId !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke berita acara ini.');
        }

        // Check if BA is completed and has PDF
        if (!$beritaAcara->isSelesai()) {
            return back()->with('error', 'Berita acara belum selesai diproses.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF berita acara belum tersedia.');
        }

        $mahasiswa = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
        $fileName = "Berita_Acara_Sempro_{$mahasiswa->nim}_{$mahasiswa->name}.pdf";

        Log::info('Mahasiswa download Berita Acara PDF', [
            'user_id' => $user->id,
            'ba_id' => $beritaAcara->id,
        ]);

        return response()->download(
            Storage::disk('local')->path($beritaAcara->file_path),
            $fileName
        );
    }

    /**
     * View PDF inline
     */
    public function viewPdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validate ownership
        $mahasiswaId = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user_id;

        if ($mahasiswaId !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke berita acara ini.');
        }

        // Check if BA is completed and has PDF
        if (!$beritaAcara->isSelesai()) {
            return back()->with('error', 'Berita acara belum selesai diproses.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF berita acara belum tersedia.');
        }

        return response()->file(
            Storage::disk('local')->path($beritaAcara->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }
}