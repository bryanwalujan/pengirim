<?php

use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AdminBeritaAcaraSemproController;
use App\Http\Controllers\Admin\AdminBeritaAcaraUjianHasilController;
use App\Http\Controllers\Admin\AdminJadwalSeminarProposalController;
use App\Http\Controllers\Admin\AdminJadwalUjianHasilController;
use App\Http\Controllers\Admin\AdminKomisiHasilController;
use App\Http\Controllers\Admin\AdminKomisiProposalController;
use App\Http\Controllers\Admin\AdminPeminjamanLaboratoriumController;
use App\Http\Controllers\Admin\AdminPeminjamanProyektorController;
use App\Http\Controllers\Admin\AdminPendaftaranSeminarProposalController;
use App\Http\Controllers\Admin\AdminPendaftaranUjianHasilController;
use App\Http\Controllers\Admin\AdminSkPembimbingController;
use App\Http\Controllers\Admin\AdminSuratAktifKuliahController;
use App\Http\Controllers\Admin\AdminSuratCutiAkademikController;
use App\Http\Controllers\Admin\AdminSuratIjinSurveyController;
use App\Http\Controllers\Admin\AdminSuratPindahController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KopSuratController;
use App\Http\Controllers\Admin\LembarCatatanSemproController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PembayaranUktController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DocumentVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BeritaAcaraSeminarProposalController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\JadwalSeminarProposalController;
use App\Http\Controllers\User\JadwalUjianHasilController;
use App\Http\Controllers\User\KomisiHasilController;
use App\Http\Controllers\User\KomisiProposalController;
use App\Http\Controllers\User\PeminjamanLaboratoriumController;
use App\Http\Controllers\User\PeminjamanProyektorController;
use App\Http\Controllers\User\PendaftaranSeminarProposalController;
use App\Http\Controllers\User\PendaftaranUjianHasilController;
use App\Http\Controllers\User\SkPembimbingController;
use App\Http\Controllers\User\SuratAktifKuliahController;
use App\Http\Controllers\User\SuratCutiAkademikController;
use App\Http\Controllers\User\SuratIjinSurveyController;
use App\Http\Controllers\User\SuratPindahController;
use App\Http\Controllers\User\TrackingSuratController;
use App\Http\Controllers\User\UserServiceController;
use App\Http\Controllers\Sync\SkProposalController;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/admin/debug/file/{id}', function ($id) {
    $pendaftaran = App\Models\PendaftaranUjianHasil::findOrFail($id);
    
    $result = [
        'id' => $pendaftaran->id,
        'mahasiswa' => $pendaftaran->user->name,
        'nim' => $pendaftaran->user->nim,
        'files' => []
    ];
    
    foreach (['file_skripsi', 'file_sk_pembimbing', 'file_proposal'] as $field) {
        $path = $pendaftaran->$field;
        $result['files'][$field] = [
            'path' => $path,
            'exists_local' => $path ? Storage::disk('local')->exists($path) : false,
            'exists_public' => $path ? Storage::disk('public')->exists($path) : false,
            'size_local' => $path && Storage::disk('local')->exists($path) ? Storage::disk('local')->size($path) : 0,
            'size_public' => $path && Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0,
            'mime_local' => $path && Storage::disk('local')->exists($path) ? Storage::disk('local')->mimeType($path) : null,
        ];
    }
    
    return response()->json($result);
})->middleware('auth');

Route::get('/preview-komisi-proposal-pdf', [AdminKomisiProposalController::class, 'previewPdf'])
    ->name('preview.komisi-proposal.pdf')
    ->middleware('auth');

Route::get('/preview-komisi-hasil-pdf', [AdminKomisiHasilController::class, 'previewPdf'])
    ->name('preview.komisi-hasil.pdf')
    ->middleware('auth');

Route::get('/preview-surat-usulan-sempro-pdf', [AdminPendaftaranSeminarProposalController::class, 'previewPdf'])
    ->name('preview.surat-usulan-sempro.pdf')
    ->middleware('auth');

Route::get('/preview-sk-pembimbing-pdf', [AdminSkPembimbingController::class, 'previewPdf'])
    ->name('preview.sk-pembimbing.pdf')
    ->middleware('auth');

Route::get('/preview-surat-usulan-ujian-hasil-pdf', [AdminPendaftaranUjianHasilController::class, 'previewPdf'])
    ->name('preview.surat-usulan-ujian-hasil.pdf')
    ->middleware('auth');

Route::get('/preview-berita-acara-ujian-hasil-pdf', [AdminBeritaAcaraUjianHasilController::class, 'previewPdf'])
    ->name('preview.berita-acara-ujian-hasil.pdf')
    ->middleware('auth');

Route::get('/preview-keputusan-panitia-ujian-hasil-pdf', [AdminBeritaAcaraUjianHasilController::class, 'previewKeputusanPanitiaStaticPdf'])
    ->name('preview.keputusan-panitia-ujian-hasil.pdf')
    ->middleware('auth');

// ========== DEVELOPMENT QUICK LOGIN (Local Only) ==========
Route::middleware('local.only')->prefix('dev')->name('dev.')->group(function () {
    Route::get('/users/{role}', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'getUsersByRole'])
        ->name('get-users')
        ->where('role', 'staff|dosen|mahasiswa');

    Route::post('/login/{role}', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'quickLogin'])
        ->name('quick-login')
        ->where('role', 'staff|dosen|mahasiswa');
});

// Untuk User (Mahasiswa)
Route::get('/', [HomeController::class, 'index'])->name('user.home.index');

// Route untuk PDF Viewer (Tambahkan di bagian atas)
Route::get('/storage/academic-calendars/{filename}', function ($filename) {
    $path = storage_path('app/public/academic-calendars/'.$filename);

    if (! File::exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.$filename.'"',
    ]);
})->name('academic-calendar.view');

// Route untuk verifikasi dokumen
Route::get('/verify/{code}', [DocumentVerificationController::class, 'verify'])
    ->name('document.verify');

Route::get('/verify/{code}/download', [DocumentVerificationController::class, 'download'])
    ->name('document.verify.download');

// Public verification routes (outside auth middleware)
Route::get('/verify/berita-acara-sempro/{code}', [AdminBeritaAcaraSemproController::class, 'verify'])
    ->name('berita-acara-sempro.verify'); // ← Ubah nama route

Route::get('/verify/berita-acara-sempro/{code}/download', [AdminBeritaAcaraSemproController::class, 'verifyAndDownload'])
    ->name('berita-acara-sempro.verify.download'); // ← Ubah nama route

Route::get('/verify/sk-pembimbing/{code}', [AdminSkPembimbingController::class, 'verify'])
    ->name('sk-pembimbing.verify');

// Public verification routes for Berita Acara Ujian Hasil
Route::get('/verify/berita-acara-ujian-hasil/{code}', [AdminBeritaAcaraUjianHasilController::class, 'verify'])
    ->name('berita-acara-ujian-hasil.verify');

Route::get('/verify/berita-acara-ujian-hasil/{code}/download', [AdminBeritaAcaraUjianHasilController::class, 'verifyAndDownload'])
    ->name('berita-acara-ujian-hasil.verify.download');

// ========== USER ROUTES (Mahasiswa) ==========
Route::middleware(['auth', 'verified', 'role:mahasiswa', 'check.ukt'])->group(function () {

    // Layanan-layanan E-Service
    Route::prefix('layanan')->name('user.services.')->group(function () {
        Route::get('/', [UserServiceController::class, 'index'])->name('index');
        Route::post('/search', [UserServiceController::class, 'search'])->name('search'); // AJAX instant search
        Route::get('/{service}/ajukan', [UserServiceController::class, 'create'])->name('create');
    });

    // Fitur Tracking Surat untuk Tracking surat aktif kuliah, ijin survey, cuti akademik dan pindah
    Route::prefix('tracking-surat')->name('user.tracking-surat.')->group(function () {
        Route::get('/', [TrackingSuratController::class, 'index'])->name('index');
        // Route::get('/{surat}', [TrackingSuratController::class, 'show'])->name('show');
    });

    // Layanan Surat Aktif Kuliah
    Route::prefix('surat-aktif-kuliah')->name('user.surat-aktif-kuliah.')->group(function () {
        Route::get('/', [SuratAktifKuliahController::class, 'index'])->name('index');
        Route::get('/ajukan', [SuratAktifKuliahController::class, 'create'])->name('create');
        Route::post('/', [SuratAktifKuliahController::class, 'store'])->name('store');
        Route::get('/{surat}', [SuratAktifKuliahController::class, 'show'])->name('show');
        Route::get('/{surat}/download', [SuratAktifKuliahController::class, 'download'])->name('download');
        Route::post('/{id}/confirm-taken', [SuratAktifKuliahController::class, 'confirmTaken'])
            ->name('confirm-taken');
    });

    // Layanan Surat Ijin Survey
    Route::prefix('surat-ijin-survey')->name('user.surat-ijin-survey.')->group(function () {
        Route::get('/', [SuratIjinSurveyController::class, 'index'])->name('index');
        Route::get('/ajukan', [SuratIjinSurveyController::class, 'create'])->name('create');
        Route::post('/', [SuratIjinSurveyController::class, 'store'])->name('store');
        Route::get('/{surat}', [SuratIjinSurveyController::class, 'show'])->name('show');
        Route::get('/{surat}/download', [SuratIjinSurveyController::class, 'download'])->name('download');
        Route::post('/{id}/confirm-taken', [SuratIjinSurveyController::class, 'confirmTaken'])
            ->name('confirm-taken');
    });

    // Layanan Surat Cuti Akademik
    Route::prefix('surat-cuti-akademik')->name('user.surat-cuti-akademik.')->group(function () {
        Route::get('/', [SuratCutiAkademikController::class, 'index'])->name('index');
        Route::get('/ajukan', [SuratCutiAkademikController::class, 'create'])->name('create');
        Route::post('/', [SuratCutiAkademikController::class, 'store'])->name('store');
        Route::get('/{surat}', [SuratCutiAkademikController::class, 'show'])->name('show');
        Route::get('/{surat}/download', [SuratCutiAkademikController::class, 'download'])->name('download');
        Route::post('/{id}/confirm-taken', [SuratCutiAkademikController::class, 'confirmTaken'])
            ->name('confirm-taken');
    });

    // Layanan Surat Pindah
    Route::prefix('surat-pindah')->name('user.surat-pindah.')->group(function () {
        Route::get('/', [SuratPindahController::class, 'index'])->name('index');
        Route::get('/ajukan', [SuratPindahController::class, 'create'])->name('create');
        Route::post('/', [SuratPindahController::class, 'store'])->name('store');
        Route::get('/{surat}', [SuratPindahController::class, 'show'])->name('show');
        Route::get('/{surat}/download', [SuratPindahController::class, 'download'])->name('download');
        Route::post('/{id}/confirm-taken', [SuratPindahController::class, 'confirmTaken'])
            ->name('confirm-taken');
    });

    // Layanan Peminjaman Proyektor
    Route::prefix('peminjaman-proyektor')->name('user.peminjaman-proyektor.')->group(function () {
        Route::get('/', [PeminjamanProyektorController::class, 'index'])->name('index');
        Route::post('/', [PeminjamanProyektorController::class, 'store'])->name('store');
        Route::put('/{peminjamanProyektor}/kembalikan', [PeminjamanProyektorController::class, 'kembalikan'])->name('kembalikan');
    });

    // Layanan Peminjaman Laboratorium
    Route::prefix('peminjaman-laboratorium')->name('user.peminjaman-laboratorium.')->group(function () {
        Route::get('/', [PeminjamanLaboratoriumController::class, 'index'])->name('index');
        Route::post('/', [PeminjamanLaboratoriumController::class, 'store'])->name('store');
        Route::put('/{peminjamanLaboratorium}', [PeminjamanLaboratoriumController::class, 'update'])->name('update');
    });

    // Layanan Komisi Proposal
    Route::prefix('komisi-proposal')->name('user.komisi-proposal.')->group(function () {
        Route::get('/', [KomisiProposalController::class, 'index'])->name('index');
        Route::get('/create', [KomisiProposalController::class, 'create'])->name('create');
        Route::post('/', [KomisiProposalController::class, 'store'])->name('store');
        Route::get('/{komisiProposal}', [KomisiProposalController::class, 'show'])->name('show');
        Route::get('/{komisiProposal}/download', [KomisiProposalController::class, 'downloadPdf'])->name('download');
    });

    // Layanan Pendaftaran Seminar Proposal
    Route::prefix('pendaftaran-seminar-proposal')->name('user.pendaftaran-seminar-proposal.')->group(function () {
        Route::get('/', [PendaftaranSeminarProposalController::class, 'index'])->name('index');
        Route::get('/create', [PendaftaranSeminarProposalController::class, 'create'])->name('create');
        Route::post('/', [PendaftaranSeminarProposalController::class, 'store'])->name('store');
        Route::get('/{pendaftaranSeminarProposal}', [PendaftaranSeminarProposalController::class, 'show'])->name('show');

        // Download Files
        Route::get('/{pendaftaranSeminarProposal}/download/transkrip', [PendaftaranSeminarProposalController::class, 'downloadTranskrip'])
            ->name('download.transkrip');
        Route::get('/{pendaftaranSeminarProposal}/download/proposal', [PendaftaranSeminarProposalController::class, 'downloadProposal'])
            ->name('download.proposal');
        Route::get('/{pendaftaranSeminarProposal}/download/permohonan', [PendaftaranSeminarProposalController::class, 'downloadPermohonan'])
            ->name('download.permohonan');
        Route::get('/{pendaftaranSeminarProposal}/download/slip-ukt', [PendaftaranSeminarProposalController::class, 'downloadSlipUkt'])
            ->name('download.slip-ukt');

        // Download Surat Usulan (jika sudah digenerate)
        Route::get('/{pendaftaranSeminarProposal}/download-surat', [PendaftaranSeminarProposalController::class, 'downloadSuratUsulan'])
            ->name('download-surat');
    });

    // Layanan Jadwal Seminar Proposal
    Route::prefix('jadwal-seminar-proposal')->name('user.jadwal-seminar-proposal.')->group(function () {
        // View jadwal
        Route::get('/', [JadwalSeminarProposalController::class, 'index'])
            ->name('index');

        // Upload SK Proposal
        Route::post('/upload-sk', [JadwalSeminarProposalController::class, 'storeSkProposal'])
            ->name('upload-sk');

        // Download SK Proposal
        Route::get('/{jadwal}/download-sk', [JadwalSeminarProposalController::class, 'downloadSkProposal'])
            ->name('download-sk');

        // View SK Proposal (inline)
        Route::get('/{jadwal}/view-sk', [JadwalSeminarProposalController::class, 'viewSkProposal'])
            ->name('view-sk');

        // Delete SK Proposal
        Route::delete('/{jadwal}/delete-sk', [JadwalSeminarProposalController::class, 'deleteSkProposal'])
            ->name('delete-sk');
    });

    // Layanan Berita Acara Seminar Proposal
    Route::prefix('berita-acara-sempro')->name('user.berita-acara-sempro.')->group(function () {
        // List berita acara
        Route::get('/', [BeritaAcaraSeminarProposalController::class, 'index'])
            ->name('index');

        // Show detail berita acara
        Route::get('/{beritaAcara}', [BeritaAcaraSeminarProposalController::class, 'show'])
            ->name('show');

        // Download PDF berita acara
        Route::get('/{beritaAcara}/download-pdf', [BeritaAcaraSeminarProposalController::class, 'downloadPdf'])
            ->name('download');

        // View PDF inline
        Route::get('/{beritaAcara}/view-pdf', [BeritaAcaraSeminarProposalController::class, 'viewPdf'])
            ->name('view-pdf');
    });

    // Layanan SK Pembimbing Skripsi
    Route::prefix('sk-pembimbing')->name('user.sk-pembimbing.')->group(function () {
        Route::get('/', [SkPembimbingController::class, 'index'])->name('index');
        Route::get('/create', [SkPembimbingController::class, 'create'])->name('create');
        Route::post('/', [SkPembimbingController::class, 'store'])->name('store');
        Route::get('/{pengajuan}', [SkPembimbingController::class, 'show'])->name('show');
        Route::get('/{pengajuan}/edit', [SkPembimbingController::class, 'edit'])->name('edit');
        Route::put('/{pengajuan}', [SkPembimbingController::class, 'update'])->name('update');
        Route::get('/{pengajuan}/download-sk', [SkPembimbingController::class, 'downloadSk'])->name('download-sk');
        Route::get('/{pengajuan}/view/{type}', [SkPembimbingController::class, 'viewDocument'])->name('view-document');
    });

    // Layanan Komisi Hasil
    Route::prefix('komisi-hasil')->name('user.komisi-hasil.')->group(function () {
        Route::get('/', [KomisiHasilController::class, 'index'])->name('index');
        Route::get('/create', [KomisiHasilController::class, 'create'])->name('create');
        Route::post('/', [KomisiHasilController::class, 'store'])->name('store');
        Route::get('/{komisiHasil}', [KomisiHasilController::class, 'show'])->name('show');
        Route::get('/{komisiHasil}/download', [KomisiHasilController::class, 'downloadPdf'])->name('download');
    });

    // Layanan Pendaftaran Ujian Hasil
    Route::prefix('pendaftaran-ujian-hasil')->name('user.pendaftaran-ujian-hasil.')->group(function () {
        Route::get('/', [PendaftaranUjianHasilController::class, 'index'])->name('index');
        Route::get('/create', [PendaftaranUjianHasilController::class, 'create'])->name('create');
        Route::post('/', [PendaftaranUjianHasilController::class, 'store'])->name('store');
        Route::get('/{pendaftaran_ujian_hasil}', [PendaftaranUjianHasilController::class, 'show'])->name('show');

        // Download Files
        Route::get('/{pendaftaran_ujian_hasil}/download/transkrip', [PendaftaranUjianHasilController::class, 'downloadTranskrip'])
            ->name('download.transkrip');
        Route::get('/{pendaftaran_ujian_hasil}/download/skripsi', [PendaftaranUjianHasilController::class, 'downloadSkripsi'])
            ->name('download.skripsi');
        Route::get('/{pendaftaran_ujian_hasil}/download/permohonan', [PendaftaranUjianHasilController::class, 'downloadPermohonan'])
            ->name('download.permohonan');
        Route::get('/{pendaftaran_ujian_hasil}/download/slip-ukt', [PendaftaranUjianHasilController::class, 'downloadSlipUkt'])
            ->name('download.slip-ukt');
        Route::get('/{pendaftaran_ujian_hasil}/download/sk-pembimbing', [PendaftaranUjianHasilController::class, 'downloadSkPembimbing'])
            ->name('download.sk-pembimbing');

        // Download Surat Usulan (jika sudah digenerate)
        Route::get('/{pendaftaran_ujian_hasil}/download-surat', [PendaftaranUjianHasilController::class, 'downloadSuratUsulan'])
            ->name('download-surat');

        // Delete rejected registration
        Route::delete('/{pendaftaran_ujian_hasil}', [PendaftaranUjianHasilController::class, 'destroy'])
            ->name('destroy');
    });

    // Layanan Jadwal Ujian Hasil
    Route::prefix('jadwal-ujian-hasil')->name('user.jadwal-ujian-hasil.')->group(function () {
        Route::get('/', [JadwalUjianHasilController::class, 'index'])->name('index');
        Route::post('/upload-sk', [JadwalUjianHasilController::class, 'storeSkUjianHasil'])->name('upload-sk');
        Route::get('/{jadwal}/download-sk', [JadwalUjianHasilController::class, 'downloadSkUjianHasil'])->name('download-sk');
        Route::get('/{jadwal}/view-sk', [JadwalUjianHasilController::class, 'viewSkUjianHasil'])->name('view-sk');
        Route::delete('/{jadwal}/delete-sk', [JadwalUjianHasilController::class, 'deleteSkUjianHasil'])->name('delete-sk');
    });

    // Layanan Berita Acara Ujian Hasil (Mahasiswa View - nilai & koreksi)
    Route::prefix('berita-acara-ujian-hasil')->name('user.berita-acara-ujian-hasil.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'index'])
            ->name('index');
        Route::get('/{beritaAcara}', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'show'])
            ->name('show');
        
        // Download PDF berita acara
        Route::get('/{beritaAcara}/download-pdf', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'downloadPdf'])
            ->name('download');
        
        // View PDF inline
        Route::get('/{beritaAcara}/view-pdf', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'viewPdf'])
            ->name('view-pdf');
        
        // Download Keputusan Panitia PDF
        Route::get('/{beritaAcara}/keputusan-panitia/download', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'downloadKeputusanPdf'])
            ->name('keputusan-panitia.download');
        
        // View Keputusan Panitia PDF inline
        Route::get('/{beritaAcara}/keputusan-panitia/view', [\App\Http\Controllers\User\UserBeritaAcaraUjianHasilController::class, 'viewKeputusanPdf'])
            ->name('keputusan-panitia.view');
    });
});

// Routes submission dengan middleware no-multi-surat (Surat Aktif Kuliah, Ijin Survey, Cuti Akademik dan Pindah)
Route::middleware(['auth', 'verified', 'role:mahasiswa', 'check.ukt', 'no-multi-surat'])->group(function () {
    // Hanya routes create dan store yang perlu dibatasi
    Route::prefix('surat-aktif-kuliah')->name('user.surat-aktif-kuliah.')->group(function () {
        Route::get('/ajukan', [SuratAktifKuliahController::class, 'create'])->name('create');
        Route::post('/', [SuratAktifKuliahController::class, 'store'])->name('store');
    });

    Route::prefix('surat-ijin-survey')->name('user.surat-ijin-survey.')->group(function () {
        Route::get('/ajukan', [SuratIjinSurveyController::class, 'create'])->name('create');
        Route::post('/', [SuratIjinSurveyController::class, 'store'])->name('store');
    });

    Route::prefix('surat-cuti-akademik')->name('user.surat-cuti-akademik.')->group(function () {
        Route::get('/ajukan', [SuratCutiAkademikController::class, 'create'])->name('create');
        Route::post('/', [SuratCutiAkademikController::class, 'store'])->name('store');
    });

    Route::prefix('surat-pindah')->name('user.surat-pindah.')->group(function () {
        Route::get('/ajukan', [SuratPindahController::class, 'create'])->name('create');
        Route::post('/', [SuratPindahController::class, 'store'])->name('store');
    });
});

// Route untuk halaman alert pembayaran
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->get('/payment-alert', function () {
    $tahunAktif = TahunAjaran::where('status_aktif', true)->first();

    return view('user.payment.alert', compact('tahunAktif'));
})->name('user.payment.alert');

// ========== ADMIN ROUTES (Staff & Dosen) ==========
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ✅ PERBAIKAN: Pindahkan route sync ke dalam group admin dengan benar
    Route::middleware(['role:staff'])->prefix('sync')->name('sync.')->group(function () {
        Route::prefix('sk-proposal')->name('sk-proposal.')->group(function () {
            Route::get('/', [SkProposalController::class, 'index'])->name('index');
            Route::get('/{skProposal}', [SkProposalController::class, 'show'])->name('show');
            Route::get('/{skProposal}/preview', [SkProposalController::class, 'preview'])->name('preview');
            Route::get('/{skProposal}/download', [SkProposalController::class, 'download'])->name('download');
            Route::post('/{skProposal}/sync', [SkProposalController::class, 'syncToRepodosen'])->name('sync');
            Route::post('/sync-all', [SkProposalController::class, 'syncAll'])->name('sync-all');
        });
    });
    
    // Notification Routes - PERBAIKAN ROUTE NAMES
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Display notifications
        Route::get('/', [NotificationController::class, 'index'])->name('index');

        // Mark as read (AJAX) - PERBAIKI NAMA ROUTE INI
        Route::post('/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->name('mark-as-read');

        // Mark as read and stay (form POST)
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsReadAndStay'])
            ->name('read');

        // Read and redirect
        Route::post('/{notificationId}/read-and-redirect', [NotificationController::class, 'readAndRedirect'])
            ->name('read-and-redirect');

        // Mark all as read
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])
            ->name('mark-all-read');

        // Delete notification
        Route::delete('/{notificationId}', [NotificationController::class, 'delete'])
            ->name('delete');

        // Get unread count (AJAX)
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])
            ->name('count');
    });

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities');

    // Layanan-layanan
    Route::resource('services', ServiceController::class);

    // Role Management
    Route::resource('roles', RoleController::class)->except('show');

    // Kop Surat
    Route::prefix('kop-surat')->name('kop-surat.')->group(function () {
        Route::get('/', [KopSuratController::class, 'index'])->name('index');
        Route::get('/edit', [KopSuratController::class, 'edit'])->name('edit');
        Route::put('/', [KopSuratController::class, 'update'])->name('update');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/mahasiswa', [UserController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/dosen', [UserController::class, 'dosen'])->name('dosen');
        Route::get('/staff', [UserController::class, 'staff'])->name('staff');

        // Create routes
        Route::get('/mahasiswa/create', [UserController::class, 'createMahasiswa'])->name('mahasiswa.create');
        Route::get('/dosen/create', [UserController::class, 'createDosen'])->name('dosen.create');
        Route::get('/staff/create', [UserController::class, 'createStaff'])->name('staff.create');

        // Store routes
        Route::post('/mahasiswa', [UserController::class, 'storeMahasiswa'])->name('mahasiswa.store');
        Route::post('/dosen', [UserController::class, 'storeDosen'])->name('dosen.store');
        Route::post('/staff', [UserController::class, 'storeStaff'])->name('staff.store');

        // Edit routes
        Route::get('/mahasiswa/{user}/edit', [UserController::class, 'editMahasiswa'])->name('mahasiswa.edit');
        Route::get('/dosen/{user}/edit', [UserController::class, 'editDosen'])->name('dosen.edit');
        Route::get('/staff/{user}/edit', [UserController::class, 'editStaff'])->name('staff.edit');

        // Update routes
        Route::put('/mahasiswa/{user}', [UserController::class, 'updateMahasiswa'])->name('mahasiswa.update');
        Route::put('/dosen/{user}', [UserController::class, 'updateDosen'])->name('dosen.update');
        Route::put('/staff/{user}', [UserController::class, 'updateStaff'])->name('staff.update');

        // Delete route
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Route untuk import/export
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/export', [UserController::class, 'exportMahasiswa'])->name('export');
            Route::get('/import', [UserController::class, 'showImportMahasiswa'])->name('import');
            Route::post('/import', [UserController::class, 'importMahasiswa'])->name('process-import');
            Route::get('/download-template', [UserController::class, 'downloadTemplateMahasiswa'])->name('download-template');
        });

    });

    // Kalender Akademik
    Route::prefix('academic-calendar')->name('academic-calendar.')->group(function () {
        Route::get('/', [AcademicCalendarController::class, 'index'])->name('index');
        Route::get('/create', [AcademicCalendarController::class, 'create'])->name('create');
        Route::post('/', [AcademicCalendarController::class, 'store'])->name('store');
        Route::get('/{academicCalendar}/edit', [AcademicCalendarController::class, 'edit'])->name('edit');
        Route::put('/{academicCalendar}', [AcademicCalendarController::class, 'update'])->name('update');
        Route::delete('/{academicCalendar}', [AcademicCalendarController::class, 'destroy'])->name('destroy');
        Route::post('/{academicCalendar}/set-active', [AcademicCalendarController::class, 'setActive'])->name('set-active');
    });

    // Admin routes untuk Surat Aktif Kuliah
    Route::prefix('surat-aktif-kuliah')->name('surat-aktif-kuliah.')->group(function () {
        // PDF Management Routes - DIPINDAHKAN KE ATAS
        Route::middleware('role:staff')->group(function () {
            Route::get('pdf-rekapan', [AdminSuratAktifKuliahController::class, 'pdfRekapan'])
                ->name('pdf-rekapan');
            Route::post('download-multiple', [AdminSuratAktifKuliahController::class, 'downloadMultiplePdfs'])
                ->name('download-multiple');
            Route::post('cleanup-pdfs', [AdminSuratAktifKuliahController::class, 'cleanupOldPdfs'])
                ->name('cleanup-pdfs');
            Route::get('pdf-info/{id}', [AdminSuratAktifKuliahController::class, 'getPdfInfo'])
                ->name('pdf-info');
            Route::post('regenerate-pdf/{id}', [AdminSuratAktifKuliahController::class, 'regeneratePdf'])
                ->name('regenerate-pdf');
        });

        // Routes that require approval authority
        Route::middleware('check.dosen.jabatan')->group(function () {
            Route::get('/', [AdminSuratAktifKuliahController::class, 'index'])->name('index');
            Route::get('/{surat}', [AdminSuratAktifKuliahController::class, 'show'])->name('show');
            Route::delete('/{surat}', [AdminSuratAktifKuliahController::class, 'destroy'])->name('destroy');

            // Hanya staff
            Route::middleware('role:staff')->group(function () {
                Route::put('/{surat}/status', [AdminSuratAktifKuliahController::class, 'updateStatus'])
                    ->name('update-status');
            });

            // Untuk dosen dengan jabatan approval authority
            Route::middleware('role:dosen')->group(function () {
                Route::put('/{surat}/approve', [AdminSuratAktifKuliahController::class, 'approveByDosen'])
                    ->name('approve');
            });

            Route::get('/{surat}/download', [AdminSuratAktifKuliahController::class, 'download'])->name('download');
            Route::get('/{surat}/download-pendukung', [AdminSuratAktifKuliahController::class, 'downloadPendukung'])
                ->name('download-pendukung');
        });
    });

    // Admin Routes untuk Surat Ijin Survey
    Route::prefix('surat-ijin-survey')->name('surat-ijin-survey.')->group(function () {
        // Routes that require approval authority
        Route::middleware('check.dosen.jabatan')->group(function () {
            Route::get('/', [AdminSuratIjinSurveyController::class, 'index'])->name('index');
            Route::get('/{surat}', [AdminSuratIjinSurveyController::class, 'show'])->name('show');
            Route::delete('/{surat}', [AdminSuratIjinSurveyController::class, 'destroy'])->name('destroy');

            // Hanya staff
            Route::middleware('role:staff')->group(function () {
                Route::put('/{surat}/status', [AdminSuratIjinSurveyController::class, 'updateStatus'])
                    ->name('update-status');
            });

            // Untuk dosen dengan jabatan approval authority
            Route::middleware('role:dosen')->group(function () {
                Route::put('/{surat}/approve', [AdminSuratIjinSurveyController::class, 'approveByDosen'])
                    ->name('approve');
            });

            Route::get('/{surat}/download', [AdminSuratIjinSurveyController::class, 'download'])->name('download');
            Route::get('/{surat}/download-pendukung', [AdminSuratIjinSurveyController::class, 'downloadPendukung'])
                ->name('download-pendukung');
        });
    });

    // Admin Routes untuk Surat Cuti Akademik
    Route::prefix('surat-cuti-akademik')->name('surat-cuti-akademik.')->group(function () {
        // Routes that require approval authority
        Route::middleware('check.dosen.jabatan')->group(function () {
            Route::get('/', [AdminSuratCutiAkademikController::class, 'index'])->name('index');
            Route::get('/{surat}', [AdminSuratCutiAkademikController::class, 'show'])->name('show');
            Route::delete('/{surat}', [AdminSuratCutiAkademikController::class, 'destroy'])->name('destroy');

            // Hanya staff
            Route::middleware('role:staff')->group(function () {
                Route::put('/{surat}/status', [AdminSuratCutiAkademikController::class, 'updateStatus'])
                    ->name('update-status');
            });

            // Untuk dosen dengan jabatan approval authority
            Route::middleware('role:dosen')->group(function () {
                Route::put('/{surat}/approve', [AdminSuratCutiAkademikController::class, 'approveByDosen'])
                    ->name('approve');
            });

            Route::get('/{surat}/download', [AdminSuratCutiAkademikController::class, 'download'])->name('download');
            Route::get('/{surat}/download-pendukung', [AdminSuratCutiAkademikController::class, 'downloadPendukung'])
                ->name('download-pendukung');
        });
    });

    // Admin Routes untuk Surat Pindah
    Route::prefix('surat-pindah')->name('surat-pindah.')->group(function () {
        // Routes that require approval authority
        Route::middleware('check.dosen.jabatan')->group(function () {
            Route::get('/', [AdminSuratPindahController::class, 'index'])->name('index');
            Route::get('/{surat}', [AdminSuratPindahController::class, 'show'])->name('show');
            Route::delete('/{surat}', [AdminSuratPindahController::class, 'destroy'])->name('destroy');

            // Hanya staff
            Route::middleware('role:staff')->group(function () {
                Route::put('/{surat}/status', [AdminSuratPindahController::class, 'updateStatus'])
                    ->name('update-status');
            });

            // Untuk dosen dengan jabatan approval authority
            Route::middleware('role:dosen')->group(function () {
                Route::put('/{surat}/approve', [AdminSuratPindahController::class, 'approveByDosen'])
                    ->name('approve');
            });

            Route::get('/{surat}/download', [AdminSuratPindahController::class, 'download'])->name('download');
            Route::get('/{surat}/download-pendukung', [AdminSuratPindahController::class, 'downloadPendukung'])
                ->name('download-pendukung');
        });
    });

    // Tahun Ajaran
    Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
        Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
        Route::get('/create', [TahunAjaranController::class, 'create'])->name('create');
        Route::post('/', [TahunAjaranController::class, 'store'])->name('store');
        Route::get('/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('edit');
        Route::put('/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('update');
        Route::post('/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])->name('activate');
        Route::delete('/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('destroy');
    });

    // Pembayaran UKT
    Route::prefix('pembayaran-ukt')->name('pembayaran-ukt.')->group(function () {
        // Main CRUD
        Route::get('/', [PembayaranUktController::class, 'index'])->name('index');
        Route::get('/create', [PembayaranUktController::class, 'create'])->name('create');
        Route::post('/', [PembayaranUktController::class, 'store'])->name('store');
        Route::get('/{pembayaranUkt}/edit', [PembayaranUktController::class, 'edit'])->name('edit');
        Route::put('/{pembayaranUkt}', [PembayaranUktController::class, 'update'])->name('update');
        Route::delete('/{pembayaranUkt}', [PembayaranUktController::class, 'destroy'])->name('destroy');

        // Import/Export Features
        Route::get('/import', [PembayaranUktController::class, 'importForm'])->name('import');
        Route::post('/import', [PembayaranUktController::class, 'import'])->name('process-import');
        Route::get('/export', [PembayaranUktController::class, 'export'])->name('export');
        Route::get('/download-template', [PembayaranUktController::class, 'downloadTemplate'])->name('download-template');

        // Status Management
        Route::put('/{pembayaranUkt}/status', [PembayaranUktController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-update-status', [PembayaranUktController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

        // Reporting (Optional)
        Route::get('/report', [PembayaranUktController::class, 'report'])->name('report');
    });

    // Peminjaman Proyektor
    Route::prefix('peminjaman-proyektor')->name('peminjaman-proyektor.')->group(function () {
        Route::get('/', [AdminPeminjamanProyektorController::class, 'index'])->name('index');

        // Proyektor Management routes - MUST come before dynamic {peminjamanProyektor} route
        Route::get('/proyektor-management', [AdminPeminjamanProyektorController::class, 'proyektorManagement'])
            ->name('proyektor-management');
        Route::put('/proyektor-list', [AdminPeminjamanProyektorController::class, 'updateProyektorList'])
            ->name('update-proyektor-list');

        Route::post('/{peminjamanProyektor}/override-return', [AdminPeminjamanProyektorController::class, 'overrideReturn'])
            ->name('admin.peminjaman-proyektor.override-return');

        // Show specific peminjaman - Add this missing route
        Route::get('/{peminjamanProyektor}', [AdminPeminjamanProyektorController::class, 'show'])->name('show');

        // Delete peminjaman
        Route::delete('/{peminjamanProyektor}', [AdminPeminjamanProyektorController::class, 'destroy'])->name('destroy');
    });

    // Peminjaman Laboratorium
    Route::prefix('peminjaman-laboratorium')->name('peminjaman-laboratorium.')->group(function () {
        Route::get('/', [AdminPeminjamanLaboratoriumController::class, 'index'])->name('index');
    });

    // Komisi Proposal - Available for ALL dosen
    Route::prefix('komisi-proposal')->name('komisi-proposal.')->group(function () {
        Route::get('/', [AdminKomisiProposalController::class, 'index'])->name('index');

        // TAMBAHKAN ROUTE INI - untuk load modal content via AJAX
        Route::get('/{komisiProposal}', [AdminKomisiProposalController::class, 'show'])->name('show');

        // Approval routes
        Route::post('/{komisiProposal}/approve-pa', [AdminKomisiProposalController::class, 'approveByPA'])
            ->name('approve-pa');
        Route::post('/{komisiProposal}/approve-korprodi', [AdminKomisiProposalController::class, 'approveByKorprodi'])
            ->name('approve-korprodi');

        // Download
        Route::get('/{komisiProposal}/download', [AdminKomisiProposalController::class, 'downloadPdf'])
            ->name('download');

        // Delete
        Route::delete('/{komisiProposal}', [AdminKomisiProposalController::class, 'destroy'])
            ->name('destroy');
    });

    // Pendaftaran Seminar Proposal
    Route::prefix('pendaftaran-seminar-proposal')->name('pendaftaran-seminar-proposal.')->group(function () {

        // ========== STAFF & DOSEN ROUTES ==========
        Route::middleware(['role:staff|dosen'])->group(function () {
            // Index & Show
            Route::get('/', [AdminPendaftaranSeminarProposalController::class, 'index'])
                ->name('index');

            // Export Status Dosen (MUST be before dynamic route)
            Route::get('/export-status-dosen', [AdminPendaftaranSeminarProposalController::class, 'exportStatusDosen'])
                ->name('export-status-dosen');

            // Status Dosen Page (MUST be before dynamic route)
            Route::get('/status-dosen', [AdminPendaftaranSeminarProposalController::class, 'statusDosenPembahas'])
                ->name('status-dosen');

            Route::get('/{pendaftaranSeminarProposal}', [AdminPendaftaranSeminarProposalController::class, 'show'])
                ->name('show');

            // VIEW Dokumen Pendukung (Inline Preview)
            Route::get('/{pendaftaranSeminarProposal}/view/transkrip', [AdminPendaftaranSeminarProposalController::class, 'viewTranskrip'])
                ->name('view.transkrip');

            Route::get('/{pendaftaranSeminarProposal}/view/proposal', [AdminPendaftaranSeminarProposalController::class, 'viewProposal'])
                ->name('view.proposal');

            Route::get('/{pendaftaranSeminarProposal}/view/permohonan', [AdminPendaftaranSeminarProposalController::class, 'viewPermohonan'])
                ->name('view.permohonan');

            Route::get('/{pendaftaranSeminarProposal}/view/slip-ukt', [AdminPendaftaranSeminarProposalController::class, 'viewSlipUkt'])
                ->name('view.slip-ukt');

            // DOWNLOAD Dokumen Pendukung
            Route::get('/{pendaftaranSeminarProposal}/download/transkrip', [AdminPendaftaranSeminarProposalController::class, 'downloadTranskrip'])
                ->name('download.transkrip');

            Route::get('/{pendaftaranSeminarProposal}/download/proposal', [AdminPendaftaranSeminarProposalController::class, 'downloadProposal'])
                ->name('download.proposal');

            Route::get('/{pendaftaranSeminarProposal}/download/permohonan', [AdminPendaftaranSeminarProposalController::class, 'downloadPermohonan'])
                ->name('download.permohonan');

            Route::get('/{pendaftaranSeminarProposal}/download/slip-ukt', [AdminPendaftaranSeminarProposalController::class, 'downloadSlipUkt'])
                ->name('download.slip-ukt');

            // Download Surat Usulan
            Route::get('/{pendaftaranSeminarProposal}/download-surat', [AdminPendaftaranSeminarProposalController::class, 'downloadSuratUsulan'])
                ->name('download-surat');

            Route::post('/{pendaftaranSeminarProposal}/sync-repodosen', [AdminPendaftaranSeminarProposalController::class, 'syncToRepodosen'])
                ->name('sync-repodosen');
        });

        // ========== STAFF ONLY ROUTES ==========
        Route::middleware(['role:staff'])->group(function () {
            // Assign Pembahas
            Route::get('/{pendaftaranSeminarProposal}/assign-pembahas', [AdminPendaftaranSeminarProposalController::class, 'showAssignPembahasForm'])
                ->name('assign-pembahas');

            Route::post('/{pendaftaranSeminarProposal}/assign-pembahas', [AdminPendaftaranSeminarProposalController::class, 'assignPembahas'])
                ->name('store-pembahas');

            // Reset Pembahas
            Route::post('/{pendaftaranSeminarProposal}/reset-pembahas', [AdminPendaftaranSeminarProposalController::class, 'resetPembahas'])
                ->name('reset-pembahas');

            // Reject Pendaftaran
            Route::post('/{pendaftaranSeminarProposal}/reject', [AdminPendaftaranSeminarProposalController::class, 'reject'])
                ->name('reject');

            // Generate Surat Usulan
            Route::post('/{pendaftaranSeminarProposal}/generate-surat', [AdminPendaftaranSeminarProposalController::class, 'generateSuratUsulan'])
                ->name('generate-surat');

            // Get Next Nomor Surat & Validate Nomor Surat
            Route::get('get-next-nomor-surat', [AdminPendaftaranSeminarProposalController::class, 'getNextNomorSurat'])->name('get-next-nomor-surat');
            Route::post('validate-nomor-surat', [AdminPendaftaranSeminarProposalController::class, 'validateNomorSurat'])->name('validate-nomor-surat');

            // Delete Pendaftaran
            Route::delete('/{pendaftaranSeminarProposal}', [AdminPendaftaranSeminarProposalController::class, 'destroy'])
                ->name('destroy');
        });

        // ========== TTD ROUTES ==========
        Route::middleware(['role:dosen|staff'])->group(function () {
            Route::post('/{pendaftaranSeminarProposal}/ttd-kaprodi', [AdminPendaftaranSeminarProposalController::class, 'ttdKaprodi'])
                ->name('ttd-kaprodi');

            Route::post('/{pendaftaranSeminarProposal}/ttd-kajur', [AdminPendaftaranSeminarProposalController::class, 'ttdKajur'])
                ->name('ttd-kajur');
        });
    });

    // Jadwal Seminar Proposal & Berita Acara - COMPLETE ROUTES
    // ✅ Routes yang bisa diakses dengan 'view jadwal sempro' (Read-only untuk Koordinator Prodi)
    Route::middleware(['can:view jadwal sempro'])->group(function () {
        Route::prefix('jadwal-seminar-proposal')->name('jadwal-seminar-proposal.')->group(function () {
            // Read-only routes (accessible by koordinator prodi)
            Route::get('/', [AdminJadwalSeminarProposalController::class, 'index'])->name('index');
            Route::get('/calendar', [AdminJadwalSeminarProposalController::class, 'calendar'])->name('calendar');
            Route::get('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'show'])->name('show');
            Route::get('/{jadwal}/download-sk', [AdminJadwalSeminarProposalController::class, 'downloadSk'])
                ->name('download-sk');
            Route::get('/{jadwal}/view-sk', [AdminJadwalSeminarProposalController::class, 'viewSk'])
                ->name('view-sk');
        });
    });

    // ✅ Routes yang memerlukan 'manage jadwal sempro' (Full access untuk Staff)
    Route::middleware(['can:manage jadwal sempro'])->group(function () {
        Route::prefix('jadwal-seminar-proposal')->name('jadwal-seminar-proposal.')->group(function () {
            // Create & Store Jadwal
            Route::get('/{jadwal}/create', [AdminJadwalSeminarProposalController::class, 'create'])->name('create');
            Route::post('/{jadwal}/store', [AdminJadwalSeminarProposalController::class, 'store'])->name('store');

            // Edit & Update Jadwal
            Route::get('/{jadwal}/edit', [AdminJadwalSeminarProposalController::class, 'edit'])->name('edit');
            Route::put('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'update'])->name('update');

            // ✅ Route untuk get batch info (AJAX)
            Route::post('/get-batch-info', [AdminJadwalSeminarProposalController::class, 'getBatchInfo'])
                ->name('get-batch-info');

            // Other Actions
            Route::post('/{jadwal}/mark-selesai', [AdminJadwalSeminarProposalController::class, 'markAsSelesai'])
                ->name('mark-selesai');
            Route::post('/{jadwal}/kirim-ulang-undangan', [AdminJadwalSeminarProposalController::class, 'kirimUlangUndangan'])
                ->name('kirim-ulang-undangan');

            // Delete
            Route::delete('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'destroy'])
                ->name('destroy');
            Route::post('/bulk-destroy', [AdminJadwalSeminarProposalController::class, 'bulkDestroy'])
                ->name('bulk-destroy');
        });
    });

    // ✅ BERITA ACARA SEMINAR PROPOSAL
    Route::prefix('berita-acara-sempro')->name('berita-acara-sempro.')->group(function () {
        // ========== STATIC ROUTES (tanpa parameter) ==========
        Route::get('/', [AdminBeritaAcaraSemproController::class, 'index'])
            ->name('index');

        Route::get('/create/{jadwal}', [AdminBeritaAcaraSemproController::class, 'create'])
            ->name('create')
            ->middleware('role:staff|admin');

        Route::post('/create/{jadwal}', [AdminBeritaAcaraSemproController::class, 'store'])
            ->name('store')
            ->middleware('role:staff|admin');



        // ✅ MANAGE PEMBAHAS - PASTIKAN METHOD POST
        Route::get('/{beritaAcara}/manage-pembahas', [AdminBeritaAcaraSemproController::class, 'managePembahas'])
            ->name('manage-pembahas');
        Route::post('/{beritaAcara}/update-pembahas', [AdminBeritaAcaraSemproController::class, 'updatePembahas'])
            ->name('update-pembahas');

        // ========== DYNAMIC ROUTES (dengan parameter {beritaAcara}) ==========
        Route::get('/{beritaAcara}', [AdminBeritaAcaraSemproController::class, 'show'])
            ->name('show');



        // ✅ APPROVE BY PEMBAHAS - Dosen Only
        Route::get('/{beritaAcara}/approve-pembahas', [AdminBeritaAcaraSemproController::class, 'showApprovePembahas'])
            ->name('approve-pembahas')
            ->middleware('role:dosen');

        // ✅ TAMBAHKAN INI - Route yang HILANG!
        Route::post('/{beritaAcara}/sign-pembahas', [AdminBeritaAcaraSemproController::class, 'signByPembahas'])
            ->name('sign-pembahas')
            ->middleware('role:dosen');

        // ✅ NEW: Staff approve on behalf of pembahas
        Route::post('/{beritaAcara}/approve-on-behalf', [AdminBeritaAcaraSemproController::class, 'approveOnBehalfOfPembahas'])
            ->name('approve-on-behalf')
            ->middleware('role:staff|admin');

        // ✅ FILL BY PEMBIMBING/KETUA
        Route::get('/{beritaAcara}/fill-by-pembimbing', [AdminBeritaAcaraSemproController::class, 'fillByPembimbing'])
            ->name('fill-by-pembimbing')
            ->middleware('role:dosen');

        Route::post('/{beritaAcara}/fill-by-pembimbing', [AdminBeritaAcaraSemproController::class, 'storeFillByPembimbing'])
            ->name('store-fill-by-pembimbing')
            ->middleware('role:dosen');

        // ✅ NEW: Staff fill on behalf of pembimbing (OVERRIDE)
        Route::get('/{beritaAcara}/fill-on-behalf', [AdminBeritaAcaraSemproController::class, 'showFillOnBehalfForm'])
            ->name('fill-on-behalf')
            ->middleware('role:staff|admin');

        Route::post('/{beritaAcara}/fill-on-behalf', [AdminBeritaAcaraSemproController::class, 'fillOnBehalfOfPembimbing'])
            ->name('store-fill-on-behalf')
            ->middleware('role:staff|admin');

        // ✅ SIGN BY KETUA (jika terpisah dari pembimbing)
        Route::get('/{beritaAcara}/preview-signing', [AdminBeritaAcaraSemproController::class, 'previewBeforeSigning'])
            ->name('preview-signing')
            ->middleware('role:dosen');

        Route::post('/{beritaAcara}/sign-ketua', [AdminBeritaAcaraSemproController::class, 'signByKetua'])
            ->name('sign-ketua')
            ->middleware('role:dosen');

        // ✅ PDF OPERATIONS
        Route::get('/{beritaAcara}/download-pdf', [AdminBeritaAcaraSemproController::class, 'downloadPdf'])
            ->name('download-pdf');

        Route::get('/{beritaAcara}/view-pdf', [AdminBeritaAcaraSemproController::class, 'viewPdf'])
            ->name('view-pdf');

        Route::post('/{beritaAcara}/generate-pdf', [AdminBeritaAcaraSemproController::class, 'generatePdf'])
            ->name('generate-pdf')
            ->middleware('role:staff|admin');



        // ✅ DELETE BA (Staff only)
        Route::delete('/{beritaAcara}', [AdminBeritaAcaraSemproController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:staff|admin');

        // PDF Management
        Route::get('/{beritaAcara}/preview-pdf', [AdminBeritaAcaraSemproController::class, 'previewPdf'])
            ->name('preview-pdf')
            ->middleware('role:staff|admin');

        Route::post('/{beritaAcara}/generate-pdf', [AdminBeritaAcaraSemproController::class, 'generatePdf'])
            ->name('generate-pdf')
            ->middleware('role:staff|admin');

        Route::get('/{beritaAcara}/download-pdf', [AdminBeritaAcaraSemproController::class, 'downloadPdf'])
            ->name('download-pdf');

        Route::get('/{beritaAcara}/view-pdf', [AdminBeritaAcaraSemproController::class, 'viewPdf'])
            ->name('view-pdf');

        // Legacy
        Route::post('/{beritaAcara}/sign', [AdminBeritaAcaraSemproController::class, 'sign'])
            ->name('sign')
            ->middleware('role:dosen');
    });

    // ✅ LEMBAR CATATAN SEMINAR PROPOSAL
    Route::prefix('lembar-catatan-sempro')->name('lembar-catatan-sempro.')->group(function () {

        // ========== STATIC ROUTES ==========

        // Create Lembar Catatan (Dosen Penguji)
        Route::get('/create/{beritaAcara}', [LembarCatatanSemproController::class, 'create'])
            ->name('create')
            ->middleware('role:dosen');

        // Store Lembar Catatan
        Route::post('/create/{beritaAcara}', [LembarCatatanSemproController::class, 'store'])
            ->name('store')
            ->middleware('role:dosen');

        // ========== DYNAMIC ROUTES ==========

        // Show Lembar Catatan
        Route::get('/{lembarCatatan}', [LembarCatatanSemproController::class, 'show'])
            ->name('show');

        // Edit Lembar Catatan (before BA signed)
        Route::get('/{lembarCatatan}/edit', [LembarCatatanSemproController::class, 'edit'])
            ->name('edit')
            ->middleware('role:dosen');

        // Update Lembar Catatan
        Route::put('/{lembarCatatan}', [LembarCatatanSemproController::class, 'update'])
            ->name('update')
            ->middleware('role:dosen');

        // Delete Lembar Catatan
        Route::delete('/{lembarCatatan}', [LembarCatatanSemproController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:dosen|staff|admin');
    });

    // SK Pembimbing Skripsi
    Route::prefix('sk-pembimbing')->name('sk-pembimbing.')->group(function () {
        Route::get('/', [AdminSkPembimbingController::class, 'index'])->name('index');
        Route::get('/{pengajuan}', [AdminSkPembimbingController::class, 'show'])->name('show');

        // Staff actions
        Route::get('/{pengajuan}/assign-pembimbing', [AdminSkPembimbingController::class, 'showAssignPembimbing'])
            ->name('assign-pembimbing');
        Route::post('/{pengajuan}/store-pembimbing', [AdminSkPembimbingController::class, 'assignPembimbing'])
            ->name('store-pembimbing');
        Route::post('/{pengajuan}/reject', [AdminSkPembimbingController::class, 'reject'])
            ->name('reject');
        Route::delete('/{pengajuan}', [AdminSkPembimbingController::class, 'destroy'])
            ->name('destroy');
        Route::post('/validate-nomor-surat', [AdminSkPembimbingController::class, 'validateNomorSurat'])
            ->name('validate-nomor-surat');

        // Signature routes
        Route::post('/{pengajuan}/sign-korprodi', [AdminSkPembimbingController::class, 'signByKorprodi'])
            ->name('sign-korprodi');
        Route::post('/{pengajuan}/sign-kajur', [AdminSkPembimbingController::class, 'signByKajur'])
            ->name('sign-kajur');

        // Download & View
        Route::get('/{pengajuan}/download-sk', [AdminSkPembimbingController::class, 'downloadSk'])
            ->name('download-sk');
        Route::get('/{pengajuan}/view/{type}', [AdminSkPembimbingController::class, 'viewDocument'])
            ->name('view-document');

        // Statistik
        Route::get('/statistik/pembimbing', [AdminSkPembimbingController::class, 'statistikPembimbing'])
            ->name('statistik-pembimbing');
    });

    // Komisi Hasil - Available for ALL dosen (3-tier approval)
    Route::prefix('komisi-hasil')->name('komisi-hasil.')->group(function () {
        // Index & Show (bisa diakses staff & dosen)
        Route::get('/', [AdminKomisiHasilController::class, 'index'])->name('index');
        Route::get('/{komisiHasil}', [AdminKomisiHasilController::class, 'show'])->name('show');

        // Approval Routes - Pembimbing 1
        Route::post('/{komisiHasil}/approve/pembimbing1', [AdminKomisiHasilController::class, 'approveByPembimbing1'])
            ->name('approve.pembimbing1');

        // Approval Routes - Pembimbing 2
        Route::post('/{komisiHasil}/approve/pembimbing2', [AdminKomisiHasilController::class, 'approveByPembimbing2'])
            ->name('approve.pembimbing2');

        // Approval Routes - Korprodi
        Route::post('/{komisiHasil}/approve/korprodi', [AdminKomisiHasilController::class, 'approveByKorprodi'])
            ->name('approve.korprodi');

        // Download PDF
        Route::get('/{komisiHasil}/download', [AdminKomisiHasilController::class, 'downloadPdf'])
            ->name('download');

        // Delete
        Route::delete('/{komisiHasil}', [AdminKomisiHasilController::class, 'destroy'])
            ->name('destroy');
    });

    // Pendaftaran Ujian Hasil
    Route::middleware(['can:manage pendaftaran ujian hasil'])->prefix('pendaftaran-ujian-hasil')->name('pendaftaran-ujian-hasil.')->group(function () {
        // Staff & Dosen access
        Route::middleware(['role:staff|dosen'])->group(function () {
            Route::get('/', [AdminPendaftaranUjianHasilController::class, 'index'])->name('index');

            // Export Status Dosen - must be before {pendaftaranUjianHasil} to avoid route conflict
            Route::get('/export-status-dosen', [AdminPendaftaranUjianHasilController::class, 'exportStatusDosen'])
                ->name('export-status-dosen');

            // Status Dosen Penguji (Dashboard)
            Route::get('/status-dosen', [AdminPendaftaranUjianHasilController::class, 'statusDosenPenguji'])
                ->name('status-dosen');

            Route::get('/{pendaftaranUjianHasil}', [AdminPendaftaranUjianHasilController::class, 'show'])->name('show');

            // View Files (Inline Preview)
            Route::get('/{pendaftaranUjianHasil}/view/transkrip', [AdminPendaftaranUjianHasilController::class, 'viewTranskrip'])
                ->name('view.transkrip');
            Route::get('/{pendaftaranUjianHasil}/view/skripsi', [AdminPendaftaranUjianHasilController::class, 'viewSkripsi'])
                ->name('view.skripsi');
            Route::get('/{pendaftaranUjianHasil}/view/permohonan', [AdminPendaftaranUjianHasilController::class, 'viewPermohonan'])
                ->name('view.permohonan');
            Route::get('/{pendaftaranUjianHasil}/view/slip-ukt', [AdminPendaftaranUjianHasilController::class, 'viewSlipUkt'])
                ->name('view.slip-ukt');
            Route::get('/{pendaftaranUjianHasil}/view/sk-pembimbing', [AdminPendaftaranUjianHasilController::class, 'viewSkPembimbing'])
                ->name('view.sk-pembimbing');

            // Download Files
            Route::get('/{pendaftaranUjianHasil}/download/transkrip', [AdminPendaftaranUjianHasilController::class, 'downloadTranskrip'])
                ->name('download.transkrip');
            Route::get('/{pendaftaranUjianHasil}/download/skripsi', [AdminPendaftaranUjianHasilController::class, 'downloadSkripsi'])
                ->name('download.skripsi');
            Route::get('/{pendaftaranUjianHasil}/download/permohonan', [AdminPendaftaranUjianHasilController::class, 'downloadPermohonan'])
                ->name('download.permohonan');
            Route::get('/{pendaftaranUjianHasil}/download/slip-ukt', [AdminPendaftaranUjianHasilController::class, 'downloadSlipUkt'])
                ->name('download.slip-ukt');
            Route::get('/{pendaftaranUjianHasil}/download/sk-pembimbing', [AdminPendaftaranUjianHasilController::class, 'downloadSkPembimbing'])
                ->name('download.sk-pembimbing');

            // Download Surat Usulan
            Route::get('/{pendaftaranUjianHasil}/download-surat', [AdminPendaftaranUjianHasilController::class, 'downloadSuratUsulan'])
                ->name('download-surat');

            Route::post('/{pendaftaranUjianHasil}/sync-repodosen',
                [AdminPendaftaranUjianHasilController::class, 'syncToRepodosen']
                )->name('sync-repodosen');
        });

        // Staff only routes
        Route::middleware(['role:staff'])->group(function () {
            // Penguji Assignment
            Route::get('/{pendaftaranUjianHasil}/assign-penguji', [AdminPendaftaranUjianHasilController::class, 'showAssignPengujiForm'])
                ->name('assign-penguji');
            Route::post('/{pendaftaranUjianHasil}/assign-penguji', [AdminPendaftaranUjianHasilController::class, 'assignPenguji'])
                ->name('store-penguji');
            Route::post('/{pendaftaranUjianHasil}/reset-penguji', [AdminPendaftaranUjianHasilController::class, 'resetPenguji'])
                ->name('reset-penguji');

            // Surat Generation
            Route::post('/{pendaftaranUjianHasil}/generate-surat', [AdminPendaftaranUjianHasilController::class, 'generateSuratUsulan'])
                ->name('generate-surat');

            // Utility (AJAX)
            Route::get('/get-next-nomor-surat', [AdminPendaftaranUjianHasilController::class, 'getNextNomorSurat'])
                ->name('get-next-nomor-surat');
            Route::post('/validate-nomor-surat', [AdminPendaftaranUjianHasilController::class, 'validateNomorSurat'])
                ->name('validate-nomor-surat');

            // Reject
            Route::post('/{pendaftaranUjianHasil}/reject', [AdminPendaftaranUjianHasilController::class, 'reject'])
                ->name('reject');

            // Delete
            Route::delete('/{pendaftaranUjianHasil}', [AdminPendaftaranUjianHasilController::class, 'destroy'])
                ->name('destroy');
        });

        // Signature routes (Dosen & Staff)
        Route::middleware(['role:dosen|staff'])->group(function () {
            Route::post('/{pendaftaranUjianHasil}/ttd-kaprodi', [AdminPendaftaranUjianHasilController::class, 'ttdKaprodi'])
                ->name('ttd-kaprodi');
            Route::post('/{pendaftaranUjianHasil}/ttd-kajur', [AdminPendaftaranUjianHasilController::class, 'ttdKajur'])
                ->name('ttd-kajur');
        });
    });

    // Jadwal Ujian Hasil - Complete Routes
    Route::middleware(['can:manage jadwal ujian hasil'])->group(function () {
        Route::prefix('jadwal-ujian-hasil')->name('jadwal-ujian-hasil.')->group(function () {
            // List & Show
            Route::get('/', [AdminJadwalUjianHasilController::class, 'index'])->name('index');
            Route::get('/calendar', [AdminJadwalUjianHasilController::class, 'calendar'])->name('calendar');
            Route::get('/{jadwal}', [AdminJadwalUjianHasilController::class, 'show'])->name('show');

            // Create & Store Jadwal
            Route::get('/{jadwal}/create', [AdminJadwalUjianHasilController::class, 'create'])->name('create');
            Route::post('/{jadwal}/store', [AdminJadwalUjianHasilController::class, 'store'])->name('store');

            // Edit & Update Jadwal
            Route::get('/{jadwal}/edit', [AdminJadwalUjianHasilController::class, 'edit'])->name('edit');
            Route::put('/{jadwal}', [AdminJadwalUjianHasilController::class, 'update'])->name('update');

            // Batch Info (AJAX)
            Route::post('/get-batch-info', [AdminJadwalUjianHasilController::class, 'getBatchInfo'])
                ->name('get-batch-info');

            // SK Actions
            Route::get('/{jadwal}/download-sk', [AdminJadwalUjianHasilController::class, 'downloadSk'])
                ->name('download-sk');
            Route::get('/{jadwal}/view-sk', [AdminJadwalUjianHasilController::class, 'viewSk'])
                ->name('view-sk');

            // Other Actions
            Route::post('/{jadwal}/mark-selesai', [AdminJadwalUjianHasilController::class, 'markAsSelesai'])
                ->name('mark-selesai');
            Route::post('/{jadwal}/kirim-ulang-undangan', [AdminJadwalUjianHasilController::class, 'kirimUlangUndangan'])
                ->name('kirim-ulang-undangan');

            // Delete
            Route::delete('/{jadwal}', [AdminJadwalUjianHasilController::class, 'destroy'])
                ->name('destroy');
            Route::post('/bulk-destroy', [AdminJadwalUjianHasilController::class, 'bulkDestroy'])
                ->name('bulk-destroy');
        });
    });

    // ✅ BERITA ACARA UJIAN HASIL
    Route::prefix('berita-acara-ujian-hasil')->name('berita-acara-ujian-hasil.')->group(function () {
        // ========== STATIC ROUTES ==========
        Route::get('/', [AdminBeritaAcaraUjianHasilController::class, 'index'])
            ->name('index');

        Route::get('/create/{jadwal}', [AdminBeritaAcaraUjianHasilController::class, 'create'])
            ->name('create')
            ->middleware('role:staff|admin');

        Route::post('/create/{jadwal}', [AdminBeritaAcaraUjianHasilController::class, 'store'])
            ->name('store')
            ->middleware('role:staff|admin');

        // ========== DYNAMIC ROUTES ==========
        // ✅ MANAGE PENGUJI (Staff)
        Route::get('/{beritaAcara}/manage-penguji', [AdminBeritaAcaraUjianHasilController::class, 'managePenguji'])
            ->name('manage-penguji')
            ->middleware('role:staff|admin');

        Route::post('/{beritaAcara}/update-penguji', [AdminBeritaAcaraUjianHasilController::class, 'updatePenguji'])
            ->name('update-penguji')
            ->middleware('role:staff|admin');

        Route::get('/{beritaAcara}', [AdminBeritaAcaraUjianHasilController::class, 'show'])
            ->name('show');

        // ✅ APPROVE BY PENGUJI - Dosen Only
        Route::get('/{beritaAcara}/approve-penguji', [AdminBeritaAcaraUjianHasilController::class, 'showApprovePenguji'])
            ->name('approve-penguji')
            ->middleware('role:dosen');

        Route::post('/{beritaAcara}/sign-penguji', [AdminBeritaAcaraUjianHasilController::class, 'signByPenguji'])
            ->name('sign-penguji')
            ->middleware('role:dosen');

        // ✅ Staff approve on behalf of penguji
        Route::post('/{beritaAcara}/approve-on-behalf', [AdminBeritaAcaraUjianHasilController::class, 'approveOnBehalfOfPenguji'])
            ->name('approve-on-behalf')
            ->middleware('role:staff|admin');

        // ✅ FILL BY KETUA
        Route::get('/{beritaAcara}/fill-by-ketua', [AdminBeritaAcaraUjianHasilController::class, 'fillByKetua'])
            ->name('fill-by-ketua')
            ->middleware('role:dosen');

        Route::post('/{beritaAcara}/fill-by-ketua', [AdminBeritaAcaraUjianHasilController::class, 'storeFillByKetua'])
            ->name('store-fill-by-ketua')
            ->middleware('role:dosen');

        // ✅ Staff fill on behalf of ketua (OVERRIDE)
        Route::get('/{beritaAcara}/fill-on-behalf', [AdminBeritaAcaraUjianHasilController::class, 'showFillOnBehalfForm'])
            ->name('fill-on-behalf')
            ->middleware('role:staff|admin');

        Route::post('/{beritaAcara}/fill-on-behalf', [AdminBeritaAcaraUjianHasilController::class, 'fillOnBehalfOfKetua'])
            ->name('store-fill-on-behalf')
            ->middleware('role:staff|admin');

        // ✅ PANITIA SEKRETARIS (KORPRODI) SIGNATURE
        Route::get('/{beritaAcara}/sign-panitia-sekretaris', [AdminBeritaAcaraUjianHasilController::class, 'showSignPanitiaSekretaris'])
            ->name('sign-panitia-sekretaris');

        Route::post('/{beritaAcara}/sign-panitia-sekretaris', [AdminBeritaAcaraUjianHasilController::class, 'signByPanitiaSekretaris'])
            ->name('store-sign-panitia-sekretaris');

        // ✅ PANITIA KETUA (DEKAN) SIGNATURE
        Route::get('/{beritaAcara}/sign-panitia-ketua', [AdminBeritaAcaraUjianHasilController::class, 'showSignPanitiaKetua'])
            ->name('sign-panitia-ketua');

        Route::post('/{beritaAcara}/sign-panitia-ketua', [AdminBeritaAcaraUjianHasilController::class, 'signByPanitiaKetua'])
            ->name('store-sign-panitia-ketua');

        // ✅ PDF OPERATIONS
        Route::get('/{beritaAcara}/download-pdf', [AdminBeritaAcaraUjianHasilController::class, 'downloadPdf'])
            ->name('download-pdf');

        Route::get('/{beritaAcara}/view-pdf', [AdminBeritaAcaraUjianHasilController::class, 'viewPdf'])
            ->name('view-pdf');

        Route::post('/{beritaAcara}/generate-pdf', [AdminBeritaAcaraUjianHasilController::class, 'generatePdf'])
            ->name('generate-pdf')
            ->middleware('role:staff|admin');

        // ✅ KEPUTUSAN PANITIA PDF
        Route::get('/{beritaAcara}/keputusan-panitia/preview', [AdminBeritaAcaraUjianHasilController::class, 'previewKeputusanPdf'])
            ->name('preview-keputusan-pdf');

        Route::get('/{beritaAcara}/keputusan-panitia/download', [AdminBeritaAcaraUjianHasilController::class, 'downloadKeputusanPdf'])
            ->name('download-keputusan-pdf');

        // ✅ DELETE BA (Staff only)
        Route::delete('/{beritaAcara}', [AdminBeritaAcaraUjianHasilController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:staff|admin');
    });

});

// ========== DOSEN SPECIFIC ROUTES ==========
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    // Berita Acara Ujian Hasil (Penilaian & Koreksi)
    Route::resource('berita-acara-ujian-hasil', \App\Http\Controllers\Dosen\DosenBeritaAcaraUjianHasilController::class)
        ->only(['index', 'show']);

    Route::get(
        'berita-acara-ujian-hasil/{beritaAcara}/approve-penguji',
        [\App\Http\Controllers\Admin\AdminBeritaAcaraUjianHasilController::class, 'showApprovePenguji']
    )
        ->name('berita-acara-ujian-hasil.approve-penguji');

    Route::post(
        'berita-acara-ujian-hasil/{beritaAcara}/sign-penguji',
        [\App\Http\Controllers\Admin\AdminBeritaAcaraUjianHasilController::class, 'signByPenguji']
    )
        ->name('berita-acara-ujian-hasil.sign-penguji');

    Route::get(
        'berita-acara-ujian-hasil/{beritaAcara}/penilaian',
        [\App\Http\Controllers\Dosen\DosenBeritaAcaraUjianHasilController::class, 'showPenilaian']
    )
        ->name('berita-acara-ujian-hasil.penilaian');

    Route::post(
        'berita-acara-ujian-hasil/{beritaAcara}/penilaian',
        [\App\Http\Controllers\Dosen\DosenBeritaAcaraUjianHasilController::class, 'storePenilaian']
    )
        ->name('berita-acara-ujian-hasil.penilaian.store');

    Route::get(
        'berita-acara-ujian-hasil/{beritaAcara}/koreksi',
        [\App\Http\Controllers\Dosen\DosenBeritaAcaraUjianHasilController::class, 'showKoreksi']
    )
        ->name('berita-acara-ujian-hasil.koreksi');

    Route::post(
        'berita-acara-ujian-hasil/{beritaAcara}/koreksi',
        [\App\Http\Controllers\Dosen\DosenBeritaAcaraUjianHasilController::class, 'storeKoreksi']
    )
        ->name('berita-acara-ujian-hasil.koreksi.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
