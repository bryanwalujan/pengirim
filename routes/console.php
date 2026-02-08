<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Console\ClosureCommand;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Penjadwalan command diletakkan di sini
Schedule::command('peminjaman:update-status')->everyMinute();

// Sync mahasiswa data setiap minggu (Minggu pukul 03:00)
// Untuk sync manual: php artisan mahasiswa:sync
Schedule::command('mahasiswa:sync')->weeklyOn(0, '03:00');
