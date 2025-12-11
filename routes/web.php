<?php

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\KopSuratController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\KomisiHasilController;
use App\Http\Controllers\User\SuratPindahController;
use App\Http\Controllers\User\UserServiceController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\User\TrackingSuratController;
use App\Http\Controllers\Admin\PembayaranUktController;
use App\Http\Controllers\User\KomisiProposalController;
use App\Http\Controllers\DocumentVerificationController;
use App\Http\Controllers\User\SuratIjinSurveyController;
use App\Http\Controllers\User\SuratAktifKuliahController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\AdminKomisiHasilController;
use App\Http\Controllers\Admin\AdminSuratPindahController;
use App\Http\Controllers\User\SuratCutiAkademikController;
use App\Http\Controllers\User\PeminjamanProyektorController;
use App\Http\Controllers\Admin\AdminKomisiProposalController;
use App\Http\Controllers\Admin\AdminSuratIjinSurveyController;
use App\Http\Controllers\User\JadwalSeminarProposalController;
use App\Http\Controllers\User\PendaftaranUjianHasilController;
use App\Http\Controllers\Admin\AdminSuratAktifKuliahController;
use App\Http\Controllers\User\PeminjamanLaboratoriumController;
use App\Http\Controllers\Admin\AdminSuratCutiAkademikController;
use App\Http\Controllers\Admin\AdminPeminjamanProyektorController;
use App\Http\Controllers\User\PendaftaranSeminarProposalController;
use App\Http\Controllers\Admin\AdminJadwalSeminarProposalController;
use App\Http\Controllers\Admin\AdminPendaftaranUjianHasilController;
use App\Http\Controllers\Admin\AdminPeminjamanLaboratoriumController;
use App\Http\Controllers\Admin\AdminPendaftaranSeminarProposalController;

Route::get('/preview-komisi-proposal-pdf', [AdminKomisiProposalController::class, 'previewPdf'])
    ->name('preview.komisi-proposal.pdf')
    ->middleware('auth');

Route::get('/preview-komisi-hasil-pdf', [AdminKomisiHasilController::class, 'previewPdf'])
    ->name('preview.komisi-hasil.pdf')
    ->middleware('auth');

Route::get('/preview-surat-usulan-sempro-pdf', [AdminPendaftaranSeminarProposalController::class, 'previewPdf'])
    ->name('preview.surat-usulan-sempro.pdf')
    ->middleware('auth');

// Untuk User (Mahasiswa)
Route::get('/', [HomeController::class, 'index'])->name('user.home.index');

// Route untuk PDF Viewer (Tambahkan di bagian atas)
Route::get('/storage/academic-calendars/{filename}', function ($filename) {
    $path = storage_path('app/public/academic-calendars/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->name('academic-calendar.view');

// Route untuk verifikasi dokumen
Route::get('/verify/{code}', [DocumentVerificationController::class, 'verify'])
    ->name('document.verify');

Route::get('/verify/{code}/download', [DocumentVerificationController::class, 'download'])
    ->name('document.verify.download');

// Route untuk role mahasiswa
Route::middleware(['auth', 'verified', 'role:mahasiswa', 'check.ukt'])->group(function () {

    // Layanan-layanan E-Service 
    Route::prefix('layanan')->name('user.services.')->group(function () {
        Route::get('/', [UserServiceController::class, 'index'])->name('index');
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

    // Layanan Pendaftaran Ujian Hasil
    Route::prefix('pendaftaran-ujian-hasil')->name('user.pendaftaran-ujian-hasil.')->group(function () {
        Route::get('/', [PendaftaranUjianHasilController::class, 'index'])->name('index');
        Route::get('/create', [PendaftaranUjianHasilController::class, 'create'])->name('create');
        Route::post('/', [PendaftaranUjianHasilController::class, 'store'])->name('store');
        Route::get('/{pendaftaran_ujian_hasil}', [PendaftaranUjianHasilController::class, 'show'])->name('show');
    });

    // Layanan Komisi Proposal
    Route::prefix('komisi-proposal')->name('user.komisi-proposal.')->group(function () {
        Route::get('/', [KomisiProposalController::class, 'index'])->name('index');
        Route::get('/create', [KomisiProposalController::class, 'create'])->name('create');
        Route::post('/', [KomisiProposalController::class, 'store'])->name('store');
        Route::get('/{komisiProposal}', [KomisiProposalController::class, 'show'])->name('show');
        Route::get('/{komisiProposal}/download', [KomisiProposalController::class, 'downloadPdf'])->name('download'); // <-- TAMBAHKAN INI
    });

    // Layanan Komisi Hasil
    Route::prefix('komisi-hasil')->name('user.komisi-hasil.')->group(function () {
        Route::get('/', [KomisiHasilController::class, 'index'])->name('index');
        Route::get('/create', [KomisiHasilController::class, 'create'])->name('create');
        Route::post('/', [KomisiHasilController::class, 'store'])->name('store');
        Route::get('/{komisiHasil}', [KomisiHasilController::class, 'show'])->name('show');
        Route::get('/{komisiHasil}/download', [KomisiHasilController::class, 'downloadPdf'])->name('download');
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


// Untuk Staff
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

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

    // Pendaftaran Seminar Proposal
    Route::prefix('pendaftaran-seminar-proposal')->name('pendaftaran-seminar-proposal.')->group(function () {

        // ========== STAFF & DOSEN ROUTES ==========
        Route::middleware(['role:staff|dosen'])->group(function () {
            // Index & Show
            Route::get('/', [AdminPendaftaranSeminarProposalController::class, 'index'])
                ->name('index');

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

    // Jadwal Seminar Proposal
    Route::middleware(['can:manage jadwal sempro'])->group(function () {
        Route::prefix('jadwal-seminar-proposal')->name('jadwal-seminar-proposal.')->group(function () {
            // Index & Calendar
            Route::get('/', [AdminJadwalSeminarProposalController::class, 'index'])->name('index');
            Route::get('/calendar', [AdminJadwalSeminarProposalController::class, 'calendar'])->name('calendar');

            // CRUD Operations
            Route::get('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'show'])->name('show');
            Route::get('/{jadwal}/create', [AdminJadwalSeminarProposalController::class, 'create'])->name('create');

            // ⚠️ PENTING: Route untuk store jadwal (POST)
            Route::post('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'store'])->name('store');

            Route::get('/{jadwal}/edit', [AdminJadwalSeminarProposalController::class, 'edit'])->name('edit');
            Route::put('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'update'])->name('update');

            // SK Actions
            Route::get('/{jadwal}/download-sk', [AdminJadwalSeminarProposalController::class, 'downloadSk'])->name('download-sk');
            Route::get('/{jadwal}/view-sk', [AdminJadwalSeminarProposalController::class, 'viewSk'])->name('view-sk');

            // Other Actions
            Route::post('/{jadwal}/mark-selesai', [AdminJadwalSeminarProposalController::class, 'markAsSelesai'])->name('mark-selesai');
            Route::post('/{jadwal}/kirim-ulang-undangan', [AdminJadwalSeminarProposalController::class, 'kirimUlangUndangan'])->name('kirim-ulang-undangan');

            // ⚠️ PENTING: Bulk Destroy HARUS di atas route DELETE dengan parameter
            Route::post('/bulk-destroy', [AdminJadwalSeminarProposalController::class, 'bulkDestroy'])
                ->name('bulk-destroy');

            // Delete (single) - HARUS di bawah bulk-destroy
            Route::delete('/{jadwal}', [AdminJadwalSeminarProposalController::class, 'destroy'])->name('destroy');

            // AJAX Route to get batch info
            Route::post('/get-batch-info', [AdminJadwalSeminarProposalController::class, 'getBatchInfo'])
                ->name('get-batch-info');
        });
    });

    // Pendaftaran Ujian Hasil
    Route::prefix('pendaftaran-ujian-hasil')->name('pendaftaran-ujian-hasil.')->group(function () {
        Route::get('/', [AdminPendaftaranUjianHasilController::class, 'index'])->name('index');
        Route::get('/{pendaftaranUjianHasil}', [AdminPendaftaranUjianHasilController::class, 'show'])->name('show');
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

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
