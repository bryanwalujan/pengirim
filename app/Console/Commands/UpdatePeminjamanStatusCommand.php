<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\PeminjamanLaboratorium;

class UpdatePeminjamanStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peminjaman:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status peminjaman laboratorium yang telah melewati batas waktu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Mengecek status peminjaman pada: {$now}");

        // Cari peminjaman yang masih 'diajukan' dan sudah lewat waktu
        $expiredLoans = PeminjamanLaboratorium::where('status', 'diajukan')->get()->filter(function ($loan) use ($now) {
            // Gabungkan tanggal dan jam selesai menjadi satu objek Carbon
            $endDateTime = Carbon::parse($loan->tanggal_peminjaman . ' ' . $loan->jam_selesai);
            return $now->greaterThan($endDateTime);
        });

        if ($expiredLoans->isEmpty()) {
            $this->info('Tidak ada peminjaman yang kedaluwarsa.');
            return;
        }

        $this->info("Ditemukan {$expiredLoans->count()} peminjaman yang akan diupdate.");

        foreach ($expiredLoans as $loan) {
            $loan->status = 'selesai';
            $loan->save();
            $this->info("Peminjaman ID: {$loan->id} oleh User ID: {$loan->user_id} telah diupdate menjadi 'selesai'.");
        }

        $this->info('Proses update selesai.');
    }
}
