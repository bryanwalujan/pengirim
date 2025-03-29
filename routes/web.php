<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\SuratAktifKuliahController;

// Public routes (accessible without authentication)
Route::get('/', [HomeController::class, 'index'])->name('user.home.index');

Route::middleware(['auth'])->prefix('surat')->group(function () {
    Route::get('aktif-kuliah/create', [SuratAktifKuliahController::class, 'create'])
        ->name('surat.aktif-kuliah.create');

    Route::post('aktif-kuliah', [SuratAktifKuliahController::class, 'store'])
        ->name('surat.aktif-kuliah.store');
});

// Untuk Admin
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     Route::resource('surat-aktif-kuliah', \App\Http\Controllers\Admin\SuratAktifKuliahController::class)
//         ->except(['create', 'store']);
// });


// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
