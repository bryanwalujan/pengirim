<?php

use App\Models\User;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\KopSuratController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\SuratPindahController;
use App\Http\Controllers\User\UserServiceController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\User\TrackingSuratController;
use App\Http\Controllers\Admin\PembayaranUktController;
use App\Http\Controllers\DocumentVerificationController;
use App\Http\Controllers\User\SuratIjinSurveyController;
use App\Http\Controllers\User\SuratAktifKuliahController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\AdminSuratPindahController;
use App\Http\Controllers\User\SuratCutiAkademikController;
use App\Http\Controllers\Admin\AdminSuratIjinSurveyController;
use App\Http\Controllers\Admin\AdminSuratAktifKuliahController;
use App\Http\Controllers\Admin\DosenSuratAktifKuliahController;
use App\Http\Controllers\Admin\AdminSuratCutiAkademikController;

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

Route::get('/preview-dummy-pdf', function () {
    $pdf = Pdf::loadView('admin.surat-aktif-kuliah.pdf', [
        'surat' => new SuratAktifKuliah([
            'nomor_surat' => 'PREVIEW/2023',
            'tanggal_surat' => now(),
            'tujuan_pengajuan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
            'tahun_ajaran' => '2023/2024',
            'semester' => 'ganjil',
            'jabatan_penandatangan' => 'Pimpinan Jurusan PTIK',
            'jabatan_penandatangan_kaprodi' => 'Koordinator Program Studi',
            'verification_code' => 'PREVIEW123'
        ]),
        'semester_roman' => 'IV (Empat)',
        'mahasiswa' => new User([
            'name' => 'Nama Mahasiswa Contoh',
            'nim' => '20230001'
        ]),
        'penandatangan' => new User([
            'name' => 'Dr. Contoh Penandatangan, M.Kom',
            'nip' => '197001012000121001'
        ]),
        'penandatanganKaprodi' => new User([
            'name' => 'Dr. Contoh Kaprodi, M.Kom',
            'nip' => '197001012000121002'
        ]),
        'show_qr_signature' => true,
        'pimpinan_qr' => 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(120)->margin(1)->errorCorrection('H')->generate('PREVIEW123')
        ),
        'kaprodi_qr' => 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(120)->margin(1)->errorCorrection('H')->generate('PREVIEW123')
        ),
        'jabatanPimpinan' => 'Pimpinan Jurusan PTIK',
        'jabatanKoordinator' => 'Koordinator Program Studi',
        'qr_type' => 'pimpinan',
    ])->setPaper('a4');

    return $pdf->stream('preview-surat-aktif-kuliah.pdf');
})->name('preview.dummy');

// Student service routes
Route::middleware(['auth', 'verified', 'role:mahasiswa', 'check.ukt'])->group(function () {
    Route::prefix('layanan')->name('user.services.')->group(function () {
        Route::get('/', [UserServiceController::class, 'index'])->name('index');
        Route::get('/{service}/ajukan', [UserServiceController::class, 'create'])->name('create');
    });

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
});

// Route untuk halaman alert pembayaran
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->get('/payment-alert', function () {
    $tahunAktif = TahunAjaran::where('status_aktif', true)->first();
    return view('user.payment.alert', compact('tahunAktif'));
})->name('user.payment.alert');

// Untuk Mahasiswa
// Route::middleware(['auth', 'verified', 'role:mahasiswa'])->prefix('surat-aktif-kuliah')->name('user.surat-aktif-kuliah.')->group(function () {
//     Route::get('/', [SuratAktifKuliahController::class, 'index'])->name('index');
//     Route::get('/ajukan', [SuratAktifKuliahController::class, 'create'])->name('create');
//     Route::post('/', [SuratAktifKuliahController::class, 'store'])->name('store');
//     Route::get('/{surat}', [SuratAktifKuliahController::class, 'show'])->name('show');
//     Route::get('/{surat}/download', [SuratAktifKuliahController::class, 'download'])->name('download');
// });

// Layanan khusus

// Surat Keterangan
// Route::prefix('surat-keterangan')->name('user.surat-keterangan.')->group(function () {
//     Route::get('/ajukan', [SuratKeteranganController::class, 'create'])->name('create');
//     Route::post('/', [SuratKeteranganController::class, 'store'])->name('store');
// });

// Transkrip Nilai
// Route::prefix('transkrip')->name('user.transkrip.')->group(function () {
//     Route::get('/ajukan', [TranskripController::class, 'create'])->name('create');
//     Route::post('/', [TranskripController::class, 'store'])->name('store');
// });



// Untuk Staff
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Route untuk menampilkan notifikasi
    Route::post('/notifications/{notification}/mark-as-read', function ($notificationId) {
        $notification = \Illuminate\Support\Facades\Auth::user()->unreadNotifications->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Notification not found'], 404);
    })->name('notifications.mark-as-read');

    // Route untuk menampilkan semua notifikasi
    Route::post('/notifications/{notification}/read', function ($notificationId) {
        $notification = Auth::user()->notifications->find($notificationId);
        $notification->markAsRead();
        return redirect($notification->data['url']);
    })->name('notifications.read');

    Route::post('/notifications/{notification}/read-and-redirect', function ($notificationId) {
        $notification = User::find(Auth::id())->notifications()->findOrFail($notificationId);

        // Mark as read
        $notification->markAsRead();

        // Redirect to the notification URL
        return redirect($notification->data['url']);
    })->name('notifications.read-and-redirect');

    // Route untuk menampilkan daftar notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    // Route untuk menandai notifikasi sebagai dibaca
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

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
            Route::post('/import', [UserController::class, 'importMahasiswa'])->name('import');
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
        Route::get('/', [AdminSuratAktifKuliahController::class, 'index'])->name('index');
        Route::get('/{surat}', [AdminSuratAktifKuliahController::class, 'show'])->name('show');

        // Hanya staff
        Route::middleware('role:staff')->group(function () {
            Route::put('/{surat}/status', [AdminSuratAktifKuliahController::class, 'updateStatus'])
                ->name('update-status');
        });
        // Hanya dosen
        Route::middleware('role:dosen')->group(function () {
            Route::put('/{surat}/approve', action: [AdminSuratAktifKuliahController::class, 'approveByDosen'])
                ->name('approve');
        });

        Route::get('/{surat}/download', [AdminSuratAktifKuliahController::class, 'download'])->name('download'); // Opsional
        // Dokumen pendukung surat aktif kuliah
        Route::get('/{surat}/download-pendukung', [AdminSuratAktifKuliahController::class, 'downloadPendukung'])
            ->name('download-pendukung');
    });

    // Admin Routes untuk Surat Ijin Survey
    Route::prefix('surat-ijin-survey')->name('surat-ijin-survey.')->group(function () {
        Route::get('/', [AdminSuratIjinSurveyController::class, 'index'])->name('index');
        Route::get('/{surat}', [AdminSuratIjinSurveyController::class, 'show'])->name('show');

        // Hanya staff
        Route::middleware('role:staff')->group(function () {
            Route::put('/{surat}/status', [AdminSuratIjinSurveyController::class, 'updateStatus'])
                ->name('update-status');
        });
        // Hanya dosen
        Route::middleware('role:dosen')->group(function () {
            Route::put('/{surat}/approve', action: [AdminSuratIjinSurveyController::class, 'approveByDosen'])
                ->name('approve');
        });

        Route::get('/{surat}/download', [AdminSuratIjinSurveyController::class, 'download'])->name('download'); // Opsional

        // Dokumen pendukung surat aktif kuliah
        Route::get('/{surat}/download-pendukung', [AdminSuratIjinSurveyController::class, 'downloadPendukung'])
            ->name('download-pendukung');
    });

    // Admin Routes untuk Surat Cuti Akademik
    Route::prefix('surat-cuti-akademik')->name('surat-cuti-akademik.')->group(function () {
        Route::get('/', [AdminSuratCutiAkademikController::class, 'index'])->name('index');
        Route::get('/{surat}', [AdminSuratCutiAkademikController::class, 'show'])->name('show');

        // Hanya staff
        Route::middleware('role:staff')->group(function () {
            Route::put('/{surat}/status', [AdminSuratCutiAkademikController::class, 'updateStatus'])
                ->name('update-status');
        });
        // Hanya dosen
        Route::middleware('role:dosen')->group(function () {
            Route::put('/{surat}/approve', action: [AdminSuratCutiAkademikController::class, 'approveByDosen'])
                ->name('approve');
        });

        Route::get('/{surat}/download', [AdminSuratCutiAkademikController::class, 'download'])->name('download');
        Route::get('/{surat}/download-pendukung', [AdminSuratCutiAkademikController::class, 'downloadPendukung'])
            ->name('download-pendukung');
    });

    // Admin Routes untuk Surat Pindah
    Route::prefix('surat-pindah')->name('surat-pindah.')->group(function () {
        Route::get('/', [AdminSuratPindahController::class, 'index'])->name('index');
        Route::get('/{surat}', [AdminSuratPindahController::class, 'show'])->name('show');

        // Hanya staff
        Route::middleware('role:staff')->group(function () {
            Route::put('/{surat}/status', [AdminSuratPindahController::class, 'updateStatus'])
                ->name('update-status');
        });
        // Hanya dosen
        Route::middleware('role:dosen')->group(function () {
            Route::put('/{surat}/approve', action: [AdminSuratPindahController::class, 'approveByDosen'])
                ->name('approve');
        });

        Route::get('/{surat}/download', [AdminSuratPindahController::class, 'download'])->name('download');
        Route::get('/{surat}/download-pendukung', [AdminSuratPindahController::class, 'downloadPendukung'])
            ->name('download-pendukung');
    });

    // Dosen routes untuk Surat Aktif Kuliah
    Route::prefix('dosen/surat-aktif-kuliah')->name('dosen.surat-aktif-kuliah.')->group(function () {
        Route::get('/', [DosenSuratAktifKuliahController::class, 'index'])->name('index');
        Route::get('/{surat}', [DosenSuratAktifKuliahController::class, 'show'])->name('show');
        Route::post('/{surat}/approve', [DosenSuratAktifKuliahController::class, 'approve'])->name('approve');
    });

    Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
        Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
        Route::get('/create', [TahunAjaranController::class, 'create'])->name('create');
        Route::post('/', [TahunAjaranController::class, 'store'])->name('store');
        Route::get('/{tahunAjaran}/edit', [TahunAjaranController::class, 'edit'])->name('edit');
        Route::put('/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('update');
        Route::post('/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])->name('activate');
        Route::delete('/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pembayaran-ukt')->name('pembayaran-ukt.')->group(function () {
        Route::get('/', [PembayaranUktController::class, 'index'])->name('index');
        Route::get('/create', [PembayaranUktController::class, 'create'])->name('create');
        Route::post('/', [PembayaranUktController::class, 'store'])->name('store');
        Route::get('/{pembayaranUkt}/edit', [PembayaranUktController::class, 'edit'])->name('edit');
        Route::put('/{pembayaranUkt}', [PembayaranUktController::class, 'update'])->name('update');
        Route::delete('/{pembayaranUkt}', [PembayaranUktController::class, 'destroy'])->name('destroy');
        Route::get('/import', [PembayaranUktController::class, 'importForm'])->name('import');
        Route::post('/import', [PembayaranUktController::class, 'import'])->name('process-import');
        Route::get('/report', [PembayaranUktController::class, 'report'])->name('report');
        Route::get('/export', [PembayaranUktController::class, 'export'])->name('export');
        Route::put('/{pembayaranUkt}/status', [PembayaranUktController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{pembayaranUkt}', [PembayaranUktController::class, 'destroy'])->name('destroy');
        Route::get('/download-template', [PembayaranUktController::class, 'downloadTemplate'])->name('download-template');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
