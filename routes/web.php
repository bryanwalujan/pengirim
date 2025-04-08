<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\KopSuratController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\UserServiceController;
use App\Http\Controllers\User\SuratAktifKuliahController;
use App\Http\Controllers\Admin\AcademicCalendarController;

// Untuk User (Mahasiswa)
Route::get('/', [HomeController::class, 'index'])->name('user.home.index');

// Student service routes
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->prefix('layanan')->name('user.services.')->group(function () {
    Route::get('/', [UserServiceController::class, 'index'])->name('index');
    Route::get('/{service}/ajukan', [UserServiceController::class, 'create'])->name('create');
});

// Untuk Staff
Route::middleware(['auth', 'role:staff|dosen'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Layanan-layanan
    Route::resource('services', ServiceController::class);

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
});

// Untuk Admin
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     Route::resource('surat-aktif-kuliah', \App\Http\Controllers\Admin\SuratAktifKuliahController::class)
//         ->except(['create', 'store']);
// });


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
