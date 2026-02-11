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

// Sync mahasiswa data setiap hari (Pukul 02:00)
// Gunakan withoutOverlapping agar proses tidak tumpang tindih jika lambat
Schedule::command('mahasiswa:sync')->dailyAt('02:00')->withoutOverlapping();
