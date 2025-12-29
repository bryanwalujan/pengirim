<?php
// filepath: app/Http/Controllers/Admin/AdminBeritaAcaraSemproController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalSeminarProposal;
use App\Models\BeritaAcaraSeminarProposal;
use App\Models\LembarCatatanSeminarProposal;
use App\Models\User;
use App\Services\PelaksanaanUjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminBeritaAcaraSemproController extends Controller
{
    protected PelaksanaanUjianService $pelaksanaanUjianService;

    public function previewPdf()
    {
        // ✅ PERBAIKAN: Buat dummy BeritaAcaraSeminarProposal sebagai COLLECTION/ARRAY
        // Jangan gunakan stdClass karena akan error saat panggil method model

        $beritaAcara = [
            'id' => 999,
            'catatan_kejadian' => 'Ada beberapa perbaikan yang harus diubah',
            'keputusan' => 'Ya, dengan perbaikan',
            'catatan_tambahan' => 'Mahasiswa menunjukkan pemahaman yang baik terhadap topik penelitian. Presentasi disampaikan dengan jelas dan sistematis.',
            'verification_code' => 'BA-SEMPRO-2025-001-' . strtoupper(substr(md5(time()), 0, 6)),
            'ttd_ketua_penguji_at' => now(),
            'is_signed' => true, // ✅ TAMBAHKAN: Flag untuk cek TTD di view
        ];

        // Convert to object untuk konsistensi dengan view
        $beritaAcara = (object) $beritaAcara;

        // Dummy Jadwal & Pendaftaran
        $mahasiswa = (object) [
            'name' => 'Budi Santoso',
            'nim' => '21011101234',
            'email' => 'budi.santoso@student.unsrat.ac.id',
        ];

        $pembimbing = (object) [
            'name' => 'Cindy Pamela Cornelia Munaiseche, S.T., M.Eng',
            'nip' => '198505152010121001',
        ];

        $pendaftaran = (object) [
            'user' => $mahasiswa,
            'judul_skripsi' => 'Penerapan Algoritma K- Means Pada Sistem Pencarian Berbasis Gambar di Repositori Elektronik Program Studi Teknik Informatika Universitas Negeri Manado',
            'dosenPembimbing' => $pembimbing,
        ];

        $jadwal = (object) [
            'tanggal_ujian' => now()->subDays(3), // 3 hari lalu
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'ruangan' => 'Ruang Lab Komputer A',
            'batch' => 1,
            'pendaftaranSeminarProposal' => $pendaftaran,
            'dosenPenguji' => collect([
                (object) [
                    'id' => 1,
                    'name' => 'Cindy Pamela Cornelia Munaiseche, S.T., M.Eng',
                    'nip' => '198505152010121001',
                    'pivot' => (object) [
                        'posisi' => 'Ketua Penguji',
                        'status_kehadiran' => 'Hadir',
                    ],
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Dr. Siti Nurhaliza, S.Kom., M.Kom.',
                    'nip' => '198803202012122002',
                    'pivot' => (object) [
                        'posisi' => 'Anggota Penguji 1',
                        'status_kehadiran' => 'Hadir',
                    ],
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Ir. Muhammad Yusuf, S.T., M.T.',
                    'nip' => '199001152015051001',
                    'pivot' => (object) [
                        'posisi' => 'Anggota Penguji 2',
                        'status_kehadiran' => 'Hadir',
                    ],
                ],
                (object) [
                    'id' => 4,
                    'name' => 'Dr. Eng. Andi Wijaya, S.T., M.Sc.',
                    'nip' => '198706102014031002',
                    'pivot' => (object) [
                        'posisi' => 'Anggota Penguji 3',
                        'status_kehadiran' => 'Hadir',
                    ],
                ],
            ]),
        ];

        // Tambahkan relasi ke beritaAcara
        $beritaAcara->jadwalSeminarProposal = $jadwal;
        $beritaAcara->ketuaPenguji = $pembimbing;

        // Dummy Lembar Catatan
        $beritaAcara->lembarCatatan = collect([
            (object) [
                'dosen' => (object) [
                    'name' => 'Dr. Siti Nurhaliza, S.Kom., M.Kom.',
                    'nip' => '198803202012122002',
                ],
                'nilai_kebaruan' => 85,
                'nilai_metode' => 80,
                'nilai_data' => 90,
                'total_nilai' => 85,
                'catatan_umum' => 'Secara keseluruhan proposal sudah baik dan layak untuk dilanjutkan dengan perbaikan-perbaikan yang telah disebutkan.',
            ],
            (object) [
                'dosen' => (object) [
                    'name' => 'Ir. Muhammad Yusuf, S.T., M.T.',
                    'nip' => '199001152015051001',
                ],
                'nilai_kebaruan' => 80,
                'nilai_metode' => 85,
                'nilai_data' => 85,
                'total_nilai' => 83,
                'catatan_umum' => 'Proposal menunjukkan pemahaman yang baik tentang topik penelitian. Good luck!',
            ],
        ]);

        // Generate verification URL
        $verificationUrl = url('/verify/berita-acara-sempro/' . $beritaAcara->verification_code);

        // Generate QR Code
        $qrCode = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        // Load view PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.berita-acara-sempro.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
            'pendaftaran' => $pendaftaran,
            'qrCode' => $qrCode,
            'verificationUrl' => $verificationUrl,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '0.7in')
            ->setOption('margin-bottom', '0.7in')
            ->setOption('margin-left', '0.7in')
            ->setOption('margin-right', '0.7in');

        return $pdf->stream('preview-berita-acara-sempro.pdf');
    }

    public function __construct(PelaksanaanUjianService $pelaksanaanUjianService)
    {
        $this->pelaksanaanUjianService = $pelaksanaanUjianService;
    }


    /**
     * ✅ Check if user is Dosen Pembimbing (berdasarkan data mahasiswa)
     */
    private function isPembimbingFor(User $user, JadwalSeminarProposal $jadwal): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $pembimbing = $jadwal->pendaftaranSeminarProposal->dosenPembimbing;

        return $pembimbing && $pembimbing->id === $user->id;
    }

    /**
     * ✅ Check if user is Ketua Penguji untuk jadwal tertentu
     */
    private function isKetuaPengujiFor(User $user, JadwalSeminarProposal $jadwal): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $ketuaPenguji = $jadwal->getKetuaPenguji();

        return $ketuaPenguji && $ketuaPenguji->id === $user->id;
    }

    /**
     * ✅ Check if user is Staff yang bisa override
     */
    private function canOverrideApproval(User $user): bool
    {
        return $user->hasRole(['staff', 'admin']);
    }

    /**
     * ✅ Check if user dapat melihat BA ini
     */
    private function canViewBeritaAcara(User $user, BeritaAcaraSeminarProposal $beritaAcara): bool
    {
        // Staff/Admin bisa lihat semua
        if ($user->hasRole(['staff', 'admin'])) {
            return true;
        }

        // Dosen harus terlibat dalam ujian ini
        if ($user->hasRole('dosen')) {
            $jadwal = $beritaAcara->jadwalSeminarProposal;

            // Cek apakah pembimbing
            if ($this->isPembimbingFor($user, $jadwal)) {
                return true;
            }

            // Cek apakah salah satu penguji
            $isPenguji = $jadwal->dosenPenguji()->where('dosen_id', $user->id)->exists();
            if ($isPenguji) {
                return true;
            }
        }

        return false;
    }

    /**
     * Display listing of berita acara
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $query = BeritaAcaraSeminarProposal::with([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'dosenPembimbingPengisi',
            'dosenPembimbingPenandatangan',
            'ketuaPenguji',
            'lembarCatatan',
        ]);

        // ✅ FILTER BERDASARKAN ROLE & PARAMETER
        if ($user->hasRole('dosen')) {
            $filter = $request->input('filter');
            $userId = $user->id;

            if ($filter === 'pembahas') {
                // ✅ Dosen sebagai pembahas - yang menunggu TTD mereka
                $query->where('status', 'menunggu_ttd_pembahas')
                    ->whereHas('jadwalSeminarProposal.dosenPenguji', function ($q) use ($userId) {
                        $q->where('users.id', $userId)
                            ->where('posisi', '!=', 'Ketua Penguji');
                    })
                    ->where(function ($q) use ($userId) {
                        $q->whereNull('ttd_dosen_pembahas')
                            ->orWhereRaw("NOT JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')", [$userId]);
                    });

            } elseif ($filter === 'pembimbing') {
                // ✅ Dosen sebagai pembimbing/ketua - yang perlu diisi
                $query->where('status', 'menunggu_ttd_pembimbing')
                    ->whereHas('jadwalSeminarProposal', function ($q) use ($userId) {
                        $q->whereHas('pendaftaranSeminarProposal', function ($q2) use ($userId) {
                            $q2->where('dosen_pembimbing_id', $userId);
                        })
                            ->orWhereHas('dosenPenguji', function ($q2) use ($userId) {
                                $q2->where('users.id', $userId)
                                    ->where('posisi', 'Ketua Penguji');
                            });
                    });

            } else {
                // ✅ PERBAIKAN: Default (Riwayat) - HANYA BA yang dosen ini sudah approve
                $query->where('status', 'selesai')
                    ->where(function ($q) use ($userId) {
                        // BA yang dosen ini sudah TTD sebagai pembahas
                        $q->whereRaw("JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')", [$userId])
                            // ATAU BA yang dosen ini sudah TTD sebagai pembimbing
                            ->orWhere('ttd_pembimbing_by', $userId)
                            // ATAU BA yang dosen ini sudah TTD sebagai ketua
                            ->orWhere('ttd_ketua_penguji_by', $userId);
                    });
            }
        }

        // Filter by status (untuk staff)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by keputusan
        if ($request->filled('keputusan')) {
            $query->where('keputusan', $request->keputusan);
        }

        // Filter by signed status
        if ($request->filled('signed')) {
            if ($request->signed === 'yes') {
                $query->whereNotNull('ttd_ketua_penguji_at');
            } else {
                $query->whereNull('ttd_ketua_penguji_at');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal.user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $beritaAcaras = $query->latest()->paginate(20)->withQueryString();

        // ✅ Statistics untuk Dosen
        if ($user->hasRole('dosen')) {
            $stats = [
                'total' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'selesai')
                    ->where(function ($q) use ($user) {
                        $q->whereRaw("JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')", [$user->id])
                            ->orWhere('ttd_pembimbing_by', $user->id)
                            ->orWhere('ttd_ketua_penguji_by', $user->id);
                    })
                    ->count(),
                'menunggu_ttd_pembahas' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')
                    ->whereHas('jadwalSeminarProposal.dosenPenguji', function ($q) use ($user) {
                        $q->where('users.id', $user->id)
                            ->where('posisi', '!=', 'Ketua Penguji');
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNull('ttd_dosen_pembahas')
                            ->orWhereRaw("NOT JSON_CONTAINS(ttd_dosen_pembahas, JSON_OBJECT('dosen_id', ?), '$')", [$user->id]);
                    })
                    ->count(),
                'menunggu_ttd_pembimbing' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')
                    ->whereHas('jadwalSeminarProposal', function ($q) use ($user) {
                        $q->whereHas('pendaftaranSeminarProposal', function ($q2) use ($user) {
                            $q2->where('dosen_pembimbing_id', $user->id);
                        })
                            ->orWhereHas('dosenPenguji', function ($q2) use ($user) {
                                $q2->where('users.id', $user->id)
                                    ->where('posisi', 'Ketua Penguji');
                            });
                    })
                    ->count(),
            ];
        } else {
            // Statistics untuk Staff
            $stats = [
                'total' => \App\Models\BeritaAcaraSeminarProposal::count(),
                'draft' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'draft')->count(),
                'menunggu_ttd_pembahas' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembahas')->count(),
                'menunggu_ttd_pembimbing' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'menunggu_ttd_pembimbing')->count(),
                'selesai' => \App\Models\BeritaAcaraSeminarProposal::where('status', 'selesai')->count(),
            ];
        }

        return view('admin.berita-acara-sempro.index', compact('beritaAcaras', 'stats'));
    }

    /**
     * Show form to create berita acara (HANYA STAFF)
     */
    public function create(JadwalSeminarProposal $jadwal)
    {
        $user = Auth::user();

        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat membuat berita acara.');
        }

        // ✅ PERBAIKAN: Check jadwal sudah dijadwalkan (tidak perlu selesai)
        if ($jadwal->status !== 'dijadwalkan') {
            return back()->with('error', 'Berita acara hanya dapat dibuat untuk jadwal yang sudah dijadwalkan.');
        }

        // Check sudah ada BA
        if ($jadwal->beritaAcaraSeminarProposal()->exists()) {
            return redirect()
                ->route('admin.berita-acara-sempro.show', $jadwal->beritaAcaraSeminarProposal)
                ->with('info', 'Berita acara sudah dibuat untuk jadwal ini.');
        }

        // ✅ PERBAIKAN: Tidak perlu cek tanggal H lagi
        // Staff bisa buat BA kapan saja setelah jadwal dibuat

        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'dosenPenguji',
        ]);

        return view('admin.berita-acara-sempro.create', compact('jadwal'));
    }

    public function store(Request $request, JadwalSeminarProposal $jadwal)
    {
        $user = Auth::user();

        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat membuat berita acara.');
        }

        // Validate
        $request->validate([
            'catatan_tambahan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // ✅ PERBAIKAN: Create BA dengan status menunggu_ttd_pembahas (bukan draft)
            $beritaAcara = BeritaAcaraSeminarProposal::create([
                'jadwal_seminar_proposal_id' => $jadwal->id,
                'catatan_tambahan' => $request->catatan_tambahan,
                'dibuat_oleh_id' => $user->id,
                'status' => 'menunggu_ttd_pembahas',  // ✅ UBAH: langsung menunggu TTD pembahas
            ]);

            DB::commit();

            Log::info('Berita Acara Sempro created (menunggu TTD pembahas)', [
                'ba_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
                'created_by' => $user->id,
            ]);


            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Berita acara berhasil dibuat. Menunggu persetujuan dari dosen pembahas.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create Berita Acara', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat berita acara: ' . $e->getMessage());
        }
    }


    /**
     * Show berita acara detail
     */
    public function show(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = User::find(Auth::id());

        // Load semua relasi yang diperlukan
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

        // ✅ PERSIAPKAN DATA UNTUK VIEW

        // Role checks
        $isDosen = $user->hasRole('dosen');
        $isStaff = $user->hasRole('staff') || $user->hasRole('admin');

        // Check apakah user adalah pembahas
        $isPembahas = false;
        if ($isDosen) {
            $isPembahas = $jadwal->dosenPenguji()
                ->where('dosen_id', $user->id)
                ->exists();
        }

        // Check apakah user adalah pembimbing
        $isPembimbing = false;
        if ($isDosen) {
            $isPembimbing = $pendaftaran->dosen_pembimbing_id === $user->id;
        }

        // Check apakah user adalah ketua penguji
        $isKetua = false;
        $ketuaPenguji = $jadwal->dosenPenguji()
            ->wherePivot('posisi', 'Ketua Penguji')
            ->first();

        if ($ketuaPenguji) {
            $isKetua = $ketuaPenguji->id === $user->id;
        }

        // Get daftar pembahas yang hadir
        $pembahasHadir = $jadwal->dosenPenguji()
            ->get();

        // ✅ DEBUG LOG
        Log::info('Berita Acara Show - User Access', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->roles->pluck('name'),
            'ba_id' => $beritaAcara->id,
            'ba_status' => $beritaAcara->status,
            'is_dosen' => $isDosen,
            'is_staff' => $isStaff,
            'is_pembahas' => $isPembahas,
            'is_pembimbing' => $isPembimbing,
            'is_ketua' => $isKetua,
            'can_sign_pembahas' => $isPembahas && $beritaAcara->canBeSignedByPembahas($user->id),
            'has_signed_pembahas' => $isPembahas && $beritaAcara->hasSignedByPembahas($user->id),
        ]);

        // Return view dengan semua data yang diperlukan
        return view('admin.berita-acara-sempro.show', compact(
            'beritaAcara',
            'isDosen',
            'isStaff',
            'isPembahas',
            'isPembimbing',
            'isKetua',
            'pembahasHadir'
        ));
    }

    /**
     * Edit berita acara (before signing)
     */
    public function edit(BeritaAcaraSeminarProposal $beritaAcara)
    {
        if ($beritaAcara->isSigned()) {
            return back()->with('error', 'Berita acara yang sudah ditandatangani tidak dapat diubah.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'dosenPembimbingPengisi',
            'lembarCatatan.dosen',
        ]);

        return view('admin.berita-acara-sempro.edit', compact('beritaAcara'));
    }

    public function update(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        if ($beritaAcara->isSigned()) {
            return back()->with('error', 'Berita acara yang sudah ditandatangani tidak dapat diedit.');
        }

        // ✅ Update validasi untuk nilai yang baru
        $validated = $request->validate([
            'catatan_kejadian' => 'required|in:Lancar,Ada beberapa perbaikan yang harus diubah',
            'keputusan' => 'required|in:Ya,Ya, dengan perbaikan,Tidak',
            'catatan_tambahan' => 'nullable|string|max:1000',
        ], [
            'catatan_kejadian.required' => 'Catatan kejadian wajib dipilih.',
            'catatan_kejadian.in' => 'Catatan kejadian tidak valid.',
            'keputusan.required' => 'Keputusan wajib dipilih.',
            'keputusan.in' => 'Keputusan tidak valid.',
        ]);

        try {
            $beritaAcara->update($validated);

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Berita acara berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui berita acara.');
        }
    }

    /**
     * ✅ FIXED: Dosen pembahas memberikan persetujuan/TTD
     */
    public function signByPembahas(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // ✅ LOG: Request masuk
        Log::info('📥 signByPembahas - Request received', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'ba_id' => $beritaAcara->id,
            'ba_status' => $beritaAcara->status,
        ]);

        // ✅ VALIDASI: Check permission
        if (!$beritaAcara->canBeSignedByPembahas($user->id)) {
            Log::warning('❌ User tidak bisa sign BA', [
                'user_id' => $user->id,
                'ba_id' => $beritaAcara->id,
                'ba_status' => $beritaAcara->status,
            ]);

            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani berita acara ini.');
        }

        // ✅ VALIDASI: Checkbox confirmation
        $request->validate([
            'confirmation' => 'required|accepted',
        ], [
            'confirmation.required' => 'Anda harus menyetujui pernyataan untuk melanjutkan.',
            'confirmation.accepted' => 'Anda harus mencentang checkbox persetujuan.',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Tambahkan signature ke JSON array
            $signatures = $beritaAcara->ttd_dosen_pembahas ?? [];

            $newSignature = [
                'dosen_id' => $user->id,
                'dosen_name' => $user->name,
                'signed_at' => now()->toDateTimeString(),
            ];

            $signatures[] = $newSignature;

            $beritaAcara->update([
                'ttd_dosen_pembahas' => $signatures,
            ]);

            Log::info('✅ Pembahas signature added', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $user->id,
                'total_signed' => count($signatures),
            ]);

            // ✅ REFRESH model untuk data terbaru
            $beritaAcara->refresh();

            // ✅ Check apakah semua pembahas sudah TTD
            if ($beritaAcara->allPembahasHaveSigned()) {
                // ✅ UBAH STATUS ke menunggu pembimbing
                $beritaAcara->update([
                    'status' => 'menunggu_ttd_pembimbing',
                ]);

                Log::info('🎉 All pembahas have signed - status changed', [
                    'ba_id' => $beritaAcara->id,
                    'new_status' => 'menunggu_ttd_pembimbing',
                ]);

                // TODO: Kirim notifikasi ke Pembimbing/Ketua Penguji
            }

            DB::commit();

            Log::info('✅✅✅ SIGN BY PEMBAHAS SUCCESS', [
                'ba_id' => $beritaAcara->id,
                'user_id' => $user->id,
                'total_signatures' => count($signatures),
                'all_signed' => $beritaAcara->fresh()->allPembahasHaveSigned(),
            ]);

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Persetujuan Anda berhasil dicatat. Terima kasih!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌❌❌ SIGN BY PEMBAHAS FAILED', [
                'ba_id' => $beritaAcara->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal memberikan persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Show approval form for pembahas (dosen penguji)
     */
    public function showApprovePembahas(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Load relasi yang diperlukan
        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'lembarCatatan.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalSeminarProposal;

        // ✅ CEK: Apakah user adalah pembahas untuk jadwal ini?
        $isPembahas = $jadwal->dosenPenguji()
            ->where('dosen_id', $user->id)
            ->exists();

        if (!$isPembahas) {
            Log::warning('User bukan pembahas', [
                'user_id' => $user->id,
                'ba_id' => $beritaAcara->id,
            ]);

            return back()->with('error', 'Anda bukan pembahas untuk ujian ini.');
        }

        // ✅ CEK: Apakah BA dalam status yang tepat?
        if (!$beritaAcara->isMenungguTtdPembahas()) {
            Log::warning('BA bukan dalam status menunggu TTD pembahas', [
                'user_id' => $user->id,
                'ba_id' => $beritaAcara->id,
                'ba_status' => $beritaAcara->status,
            ]);

            return back()->with('error', 'Berita acara tidak dalam status menunggu persetujuan pembahas. Status saat ini: ' . $beritaAcara->status);
        }

        // ✅ CEK: Apakah user sudah sign?
        if ($beritaAcara->hasSignedByPembahas($user->id)) {
            Log::info('User sudah memberikan persetujuan', [
                'user_id' => $user->id,
                'ba_id' => $beritaAcara->id,
            ]);

            return back()->with('info', 'Anda sudah menyetujui berita acara ini.');
        }

        // Get daftar pembahas yang hadir
        $pembahasHadir = $jadwal->dosenPenguji()
            ->get();

        // Debug log
        Log::info('Dosen accessing approve pembahas page', [
            'dosen_id' => $user->id,
            'dosen_name' => $user->name,
            'ba_id' => $beritaAcara->id,
            'ba_status' => $beritaAcara->status,
            'is_pembahas' => $isPembahas,
            'pembahas_hadir_count' => $pembahasHadir->count(),
        ]);

        return view('admin.berita-acara-sempro.approve-pembahas', compact('beritaAcara', 'pembahasHadir'));
    }

    /**
     * ✅ Show form for Dosen Pembimbing to fill BA
     */
    public function fillByPembimbing(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validasi permission
        if (!$beritaAcara->canBeFilledByPembimbing($user->id)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengisi berita acara ini.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.dosenPenguji',
        ]);

        return view('admin.berita-acara-sempro.fill-by-pembimbing', compact('beritaAcara'));
    }

    /**
     * ✅ FIXED: Store data filled by Dosen Pembimbing/Ketua
     */

    public function storeFillByPembimbing(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validasi permission
        if (!$beritaAcara->canBeFilledByPembimbing($user->id)) {
            Log::error('User tidak memiliki akses fill BA', [
                'user_id' => $user->id,
                'ba_id' => $beritaAcara->id,
                'ba_status' => $beritaAcara->status,
            ]);

            return back()->with('error', 'Anda tidak memiliki akses untuk mengisi berita acara ini.');
        }

        // ✅ FIX: Validasi input - PERBAIKI escape koma
        $validated = $request->validate([
            'catatan_kejadian' => 'required|in:Lancar,Ada beberapa perbaikan yang harus diubah',
            'keputusan' => 'required|string', // ✅ UBAH: Gunakan string validation dulu
            'catatan_tambahan' => 'nullable|string|max:1000',
        ], [
            'catatan_kejadian.required' => 'Catatan kejadian wajib dipilih.',
            'catatan_kejadian.in' => 'Catatan kejadian tidak valid.',
            'keputusan.required' => 'Kesimpulan kelayakan wajib dipilih.',
        ]);

        // ✅ MANUAL VALIDATION: Cek nilai keputusan
        $validKeputusan = ['Ya', 'Ya, dengan perbaikan', 'Tidak'];
        if (!in_array($validated['keputusan'], $validKeputusan)) {
            return back()
                ->withInput()
                ->withErrors(['keputusan' => 'Kesimpulan kelayakan tidak valid.']);
        }

        DB::beginTransaction();
        try {
            // ✅ UPDATE: BA langsung selesai setelah pembimbing/ketua TTD
            $beritaAcara->update([
                'catatan_kejadian' => $validated['catatan_kejadian'],
                'keputusan' => $validated['keputusan'],
                'catatan_tambahan' => $validated['catatan_tambahan'] ?? $beritaAcara->catatan_tambahan,
                'diisi_oleh_pembimbing_id' => $user->id,
                'diisi_pembimbing_at' => now(),
                'ttd_pembimbing_by' => $user->id,
                'ttd_pembimbing_at' => now(),
                'ttd_ketua_penguji_by' => $user->id,     // ✅ Sekaligus TTD sebagai ketua
                'ttd_ketua_penguji_at' => now(),
                'status' => 'selesai',
            ]);

            Log::info('Berita Acara updated - before PDF generation', [
                'ba_id' => $beritaAcara->id,
                'catatan_kejadian' => $validated['catatan_kejadian'],
                'keputusan' => $validated['keputusan'],
            ]);

            // ✅ Generate PDF langsung
            $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

            if ($pdfPath) {
                $beritaAcara->update(['file_path' => $pdfPath]);

                Log::info('PDF generated successfully', [
                    'pdf_path' => $pdfPath,
                ]);
            } else {
                Log::warning('PDF generation returned null', [
                    'ba_id' => $beritaAcara->id,
                ]);
            }

            DB::commit();

            Log::info('Berita Acara filled & signed by Pembimbing/Ketua - FINAL', [
                'ba_id' => $beritaAcara->id,
                'user_id' => $user->id,
                'keputusan' => $validated['keputusan'],
                'pdf_generated' => !is_null($pdfPath),
            ]);

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Berita acara berhasil diisi, ditandatangani, dan PDF telah digenerate!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to fill & sign Berita Acara by Pembimbing/Ketua', [
                'ba_id' => $beritaAcara->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Preview PDF sebelum TTD oleh Ketua
     */
    public function previewBeforeSigning(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Check permission
        if (!$beritaAcara->canBeSignedByKetua($user->id)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani berita acara ini.');
        }

        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'dosenPembimbingPengisi',
            'dosenPembimbingPenandatangan',
            'lembarCatatan.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalSeminarProposal;

        // Generate preview PDF
        try {
            $pdfPreview = $this->pelaksanaanUjianService->generateBeritaAcaraPdfPreview($beritaAcara);

            return view('admin.berita-acara-sempro.preview-signing', compact('beritaAcara', 'jadwal', 'pdfPreview'));

        } catch (\Exception $e) {
            Log::error('Failed to generate PDF preview', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal membuat preview PDF: ' . $e->getMessage());
        }
    }

    /**
     * Ketua Penguji menandatangani BA
     */
    public function signByKetua(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Check permission
        if (!$beritaAcara->canBeSignedByKetua($user->id)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani berita acara ini.');
        }

        try {
            DB::beginTransaction();

            // ✅ Sign by Ketua
            $beritaAcara->update([
                'ttd_ketua_penguji_by' => $user->id,
                'ttd_ketua_penguji_at' => now(),
                'status' => 'selesai',
            ]);

            // ✅ Generate final PDF
            $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

            if ($pdfPath) {
                $beritaAcara->update(['file_path' => $pdfPath]);
            }

            DB::commit();

            Log::info('Ketua signed Berita Acara - FINAL', [
                'ba_id' => $beritaAcara->id,
                'ketua_id' => $user->id,
                'pdf_path' => $pdfPath,
            ]);

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', 'Berita acara berhasil ditandatangani! PDF final telah digenerate.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sign by ketua', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menandatangani: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Cancel/Reset BA (by Staff/Admin only)
     */
    public function resetBeritaAcara(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Hanya staff/admin yang bisa reset
        if (!$this->canOverrideApproval($user)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk reset berita acara.');
        }

        if ($beritaAcara->isSelesai()) {
            return back()->with('error', 'Berita acara yang sudah selesai tidak dapat direset.');
        }

        DB::beginTransaction();
        try {
            // Reset ke draft
            $beritaAcara->update([
                'catatan_kejadian' => null,
                'keputusan' => null,
                'diisi_oleh_pembimbing_id' => null,
                'diisi_pembimbing_at' => null,
                'status' => 'draft',
            ]);

            DB::commit();

            Log::info('Berita Acara reset to draft', [
                'ba_id' => $beritaAcara->id,
                'reset_by' => $user->id,
            ]);

            return back()->with('success', 'Berita acara berhasil direset ke status draft.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset Berita Acara', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal mereset berita acara.');
        }
    }

    /**
     * Sign berita acara (by Ketua Penguji)
     */
    public function sign(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validate that current user is ketua penguji
        $ketuaPenguji = $beritaAcara->jadwalSeminarProposal->getKetuaPenguji();

        if (!$ketuaPenguji || $ketuaPenguji->id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menandatangani berita acara ini.');
        }

        if ($beritaAcara->isSigned()) {
            return back()->with('error', 'Berita acara sudah ditandatangani.');
        }

        $success = $this->pelaksanaanUjianService->signBeritaAcara($beritaAcara, $user->id);

        if ($success) {
            return back()->with('success', 'Berita acara berhasil ditandatangani.');
        }

        return back()->with('error', 'Gagal menandatangani berita acara.');
    }

    public function managePembahas(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat mengelola pembahas.');
        }

        if ($beritaAcara->isSelesai()) {
            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('error', 'Berita acara sudah selesai, tidak dapat mengubah pembahas.');
        }

        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $pembimbing = $pendaftaran->dosenPembimbing;

        // ✅ DEBUG: Log data awal
        Log::info('🔍 Manage Pembahas - Initial Data', [
            'ba_id' => $beritaAcara->id,
            'jadwal_id' => $jadwal->id,
        ]);

        // ✅ PERBAIKAN: Get ALL penguji dengan ORDER BY yang benar
        $currentPenguji = $jadwal->dosenPenguji()
            ->withPivot('posisi', 'keterangan', 'dosen_id')
            ->orderByRaw("CASE 
                WHEN posisi = 'Ketua Penguji' THEN 1 
                WHEN posisi = 'Anggota Penguji 1' THEN 2 
                WHEN posisi = 'Anggota Penguji 2' THEN 3 
                WHEN posisi = 'Anggota Penguji 3' THEN 4 
                ELSE 5 END")
            ->get();

        // ✅ DEBUG: Log all penguji
        Log::info('📋 All Penguji dari DB', [
            'total' => $currentPenguji->count(),
            'data' => $currentPenguji->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'posisi' => $d->pivot->posisi,
            ])->toArray(),
        ]);

        // ✅ PERBAIKAN: Pisahkan Ketua dan Anggota - GUNAKAN NILAI DB ASLI
        $ketuaPembahasData = $currentPenguji->firstWhere('pivot.posisi', 'Ketua Penguji');

        $anggotaPenguji = $currentPenguji->filter(function ($dosen) {
            // ✅ PERBAIKAN: Gunakan nilai DB yang benar
            return $dosen->pivot->posisi !== 'Ketua Penguji';
        })->values();

        // ✅ DEBUG: Log filtered data
        Log::info('📊 Filtered Data', [
            'ketua_found' => !is_null($ketuaPembahasData),
            'ketua_name' => $ketuaPembahasData->name ?? 'NULL',
            'anggota_count' => $anggotaPenguji->count(),
            'anggota_list' => $anggotaPenguji->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'posisi' => $d->pivot->posisi,
            ])->toArray(),
        ]);

        // ✅ Get available dosen (exclude pembimbing & yang sudah ditugaskan)
        $availableDosen = User::role('dosen')
            ->where('id', '!=', $pendaftaran->dosen_pembimbing_id)
            ->orderBy('name')
            ->get();

        // ✅ Get signed dosen IDs
        $signedDosenIds = collect($beritaAcara->ttd_dosen_pembahas ?? [])
            ->pluck('dosen_id')
            ->toArray();

        Log::info('✅ Data untuk View', [
            'available_dosen_count' => $availableDosen->count(),
            'signed_ids' => $signedDosenIds,
        ]);

        return view('admin.berita-acara-sempro.manage-pembahas', compact(
            'beritaAcara',
            'jadwal',
            'pendaftaran',
            'pembimbing',
            'currentPenguji',
            'ketuaPembahasData',
            'anggotaPenguji',
            'availableDosen',
            'signedDosenIds',
        ));
    }

    public function updatePembahas(Request $request, BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat mengelola pembahas.');
        }

        if ($beritaAcara->isSelesai()) {
            return back()->with('error', 'Berita acara sudah selesai, tidak dapat mengubah pembahas.');
        }

        // ✅ LOG: Request diterima
        Log::info('📥 updatePembahas - Request received', [
            'ba_id' => $beritaAcara->id,
            'all_input' => $request->all(),
            'pembahas_input' => $request->input('pembahas'),
        ]);

        // ✅ VALIDASI
        try {
            $validated = $request->validate([
                'pembahas' => 'required|array|min:1',
                'pembahas.*.dosen_id' => 'required|exists:users,id',
                'pembahas.*.posisi' => [
                    'required',
                    'string',
                    'in:Anggota Penguji 1,Anggota Penguji 2,Anggota Penguji 3'
                ],
            ], [
                'pembahas.required' => 'Data pembahas wajib diisi.',
                'pembahas.*.dosen_id.required' => 'Dosen wajib dipilih.',
                'pembahas.*.dosen_id.exists' => 'Dosen tidak valid.',
                'pembahas.*.posisi.required' => 'Posisi wajib diisi.',
                'pembahas.*.posisi.in' => 'Posisi harus Anggota Penguji 1, 2, atau 3.',
            ]);

            Log::info('✅ Validation passed', ['validated' => $validated]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;

            // ✅ Ambil signature yang sudah ada
            $existingSignatures = $beritaAcara->ttd_dosen_pembahas ?? [];
            $signedDosenIds = collect($existingSignatures)->pluck('dosen_id')->toArray();

            Log::info('📝 Existing signatures', [
                'signatures' => $existingSignatures,
                'signed_ids' => $signedDosenIds,
            ]);

            $replacements = [];
            $newPembahasData = [];

            // ✅ STEP 1: Collect & Validate
            foreach ($request->input('pembahas', []) as $index => $pembahasData) {
                $posisi = $pembahasData['posisi'];
                $newDosenId = (int) $pembahasData['dosen_id'];

                Log::info("🔄 Processing pembahas [{$index}]", [
                    'posisi' => $posisi,
                    'new_dosen_id' => $newDosenId,
                ]);

                // ✅ Cek dosen lama di posisi ini DARI PIVOT TABLE
                $oldDosenPivot = DB::table('dosen_penguji_jadwal_sempro')
                    ->where('jadwal_seminar_proposal_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->first();

                if ($oldDosenPivot) {
                    Log::info('👤 Found old dosen in pivot', [
                        'pivot_id' => $oldDosenPivot->id,
                        'old_dosen_id' => $oldDosenPivot->dosen_id,
                        'posisi' => $posisi,
                    ]);

                    // Jika dosen berubah
                    if ($oldDosenPivot->dosen_id != $newDosenId) {
                        // Cek apakah dosen lama sudah TTD
                        if (in_array($oldDosenPivot->dosen_id, $signedDosenIds)) {
                            DB::rollBack();

                            $oldDosen = User::find($oldDosenPivot->dosen_id);

                            Log::warning('🚫 Attempt to replace signed dosen', [
                                'posisi' => $posisi,
                                'old_dosen_id' => $oldDosenPivot->dosen_id,
                                'old_dosen_name' => $oldDosen->name,
                                'new_dosen_id' => $newDosenId,
                            ]);

                            return back()->with(
                                'error',
                                "Dosen {$oldDosen->name} di posisi {$posisi} sudah memberikan persetujuan (TTD), tidak dapat diganti."
                            );
                        }

                        $oldDosen = User::find($oldDosenPivot->dosen_id);
                        $newDosen = User::find($newDosenId);

                        $replacements[] = [
                            'posisi' => $posisi,
                            'old_dosen' => $oldDosen->name,
                            'new_dosen' => $newDosen->name,
                        ];

                        Log::info('✏️ Dosen will be replaced', [
                            'posisi' => $posisi,
                            'old_dosen' => $oldDosen->name,
                            'new_dosen' => $newDosen->name,
                        ]);
                    }
                } else {
                    Log::warning('⚠️ No old dosen found at position', ['posisi' => $posisi]);
                }

                $newPembahasData[$posisi] = $newDosenId;
            }

            Log::info('📦 New pembahas data collected', [
                'new_pembahas_data' => $newPembahasData,
                'replacements_count' => count($replacements),
            ]);

            // ✅ STEP 2: Update pivot table LANGSUNG dengan UPDATE query
            foreach ($newPembahasData as $posisi => $newDosenId) {
                $existingPivot = DB::table('dosen_penguji_jadwal_sempro')
                    ->where('jadwal_seminar_proposal_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->first();

                if ($existingPivot) {
                    if ($existingPivot->dosen_id != $newDosenId) {
                        // ✅ LANGSUNG UPDATE dengan DB query
                        $affected = DB::table('dosen_penguji_jadwal_sempro')
                            ->where('id', $existingPivot->id)
                            ->update([
                                'dosen_id' => $newDosenId,
                                'updated_at' => now(),
                            ]);

                        Log::info('✅ UPDATE pivot', [
                            'pivot_id' => $existingPivot->id,
                            'posisi' => $posisi,
                            'old_dosen_id' => $existingPivot->dosen_id,
                            'new_dosen_id' => $newDosenId,
                            'rows_affected' => $affected,
                        ]);

                        // ✅ VERIFY update
                        $verifyUpdate = DB::table('dosen_penguji_jadwal_sempro')
                            ->where('id', $existingPivot->id)
                            ->first();

                        Log::info('🔎 VERIFY after UPDATE', [
                            'dosen_id_after' => $verifyUpdate->dosen_id,
                            'expected' => $newDosenId,
                            'match' => ($verifyUpdate->dosen_id == $newDosenId) ? 'YES ✅' : 'NO ❌',
                        ]);
                    } else {
                        Log::info('⏭️ Skip update - dosen sama', [
                            'posisi' => $posisi,
                            'dosen_id' => $newDosenId,
                        ]);
                    }
                } else {
                    // INSERT (seharusnya tidak terjadi)
                    DB::table('dosen_penguji_jadwal_sempro')->insert([
                        'jadwal_seminar_proposal_id' => $jadwal->id,
                        'dosen_id' => $newDosenId,
                        'posisi' => $posisi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('✅ INSERT new pivot', [
                        'posisi' => $posisi,
                        'dosen_id' => $newDosenId,
                    ]);
                }
            }

            // ✅ STEP 3: Update signatures - Hapus signature dosen yang diganti
            $newSignatures = [];
            $newDosenIds = array_values($newPembahasData);

            foreach ($existingSignatures as $signature) {
                if (in_array($signature['dosen_id'], $newDosenIds)) {
                    $newSignatures[] = $signature;
                } else {
                    Log::info('🗑️ Remove signature', [
                        'dosen_id' => $signature['dosen_id'],
                    ]);
                }
            }

            $beritaAcara->update([
                'ttd_dosen_pembahas' => $newSignatures,
            ]);

            Log::info('✅ Signatures updated', [
                'old_count' => count($existingSignatures),
                'new_count' => count($newSignatures),
            ]);

            // ✅ COMMIT transaction
            DB::commit();

            Log::info('✅✅✅ UPDATE PEMBAHAS SUCCESS', [
                'ba_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
                'replacements' => $replacements,
            ]);

            $message = 'Daftar pembahas berhasil diperbarui.';
            if (count($replacements) > 0) {
                $message .= ' ' . count($replacements) . ' dosen telah diganti.';
            }

            return redirect()
                ->route('admin.berita-acara-sempro.show', $beritaAcara)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌❌❌ UPDATE PEMBAHAS FAILED', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pembahas: ' . $e->getMessage());
        }
    }

    /**
     * Generate/Regenerate PDF
     */
    public function generatePdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat generate PDF.');
        }

        if (!$beritaAcara->isFilledByPembimbing()) {
            return back()->with('error', 'Berita acara belum diisi oleh dosen pembimbing.');
        }

        try {
            $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

            if (!$pdfPath) {
                throw new \Exception('Gagal generate PDF.');
            }

            $beritaAcara->update(['file_path' => $pdfPath]);

            Log::info('PDF regenerated for Berita Acara', [
                'ba_id' => $beritaAcara->id,
                'generated_by' => $user->id,
            ]);

            return back()->with('success', 'PDF berhasil digenerate.');

        } catch (\Exception $e) {
            Log::error('Failed to generate PDF', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF
     */
    public function downloadPdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$this->canViewBeritaAcara($user, $beritaAcara)) {
            abort(403, 'Anda tidak memiliki akses untuk download berita acara ini.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        $mahasiswa = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->user;
        $fileName = "BA_Sempro_{$mahasiswa->nim}_{$mahasiswa->name}.pdf";

        return response()->download(Storage::disk('local')->path($beritaAcara->file_path), $fileName);
    }

    /**
     * View PDF (inline)
     */
    public function viewPdf(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        if (!$this->canViewBeritaAcara($user, $beritaAcara)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat berita acara ini.');
        }

        if (!$beritaAcara->file_path || !Storage::disk('local')->exists($beritaAcara->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('local')->path($beritaAcara->file_path),
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Verify berita acara (public)
     */
    public function verify(string $code)
    {
        $beritaAcara = BeritaAcaraSeminarProposal::where('verification_code', $code)
            ->with([
                'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
                'jadwalSeminarProposal.dosenPenguji',
                'ketuaPenguji',
            ])
            ->first();

        if (!$beritaAcara) {
            return view('public.verify-berita-acara', [
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid.',
            ]);
        }

        return view('public.verify-berita-acara', [
            'valid' => true,
            'beritaAcara' => $beritaAcara,
        ]);
    }

    /**
     * Delete berita acara
     */
    public function destroy(BeritaAcaraSeminarProposal $beritaAcara)
    {
        $user = Auth::user();

        // Validasi permission
        if (!$this->canOverrideApproval($user)) {
            abort(403, 'Hanya staff yang dapat menghapus berita acara.');
        }

        // Tidak bisa delete jika sudah ditandatangani
        if ($beritaAcara->isSigned()) {
            return back()->with('error', 'Berita acara yang sudah ditandatangani tidak dapat dihapus. Gunakan fitur Reset jika perlu.');
        }

        DB::beginTransaction();
        try {
            // Hapus file PDF jika ada
            if ($beritaAcara->file_path && Storage::disk('local')->exists($beritaAcara->file_path)) {
                Storage::disk('local')->delete($beritaAcara->file_path);
            }

            // Delete lembar catatan terkait
            $beritaAcara->lembarCatatan()->delete();

            // Delete BA
            $beritaAcara->delete();

            DB::commit();

            Log::info('Berita Acara deleted', [
                'ba_id' => $beritaAcara->id,
                'deleted_by' => $user->id,
            ]);

            return redirect()
                ->route('admin.berita-acara-sempro.index')
                ->with('success', 'Berita acara berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Berita Acara', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menghapus berita acara: ' . $e->getMessage());
        }
    }
}