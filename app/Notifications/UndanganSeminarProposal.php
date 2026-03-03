<?php
// filepath: app/Notifications/UndanganSeminarProposal.php

namespace App\Notifications;

use App\Models\JadwalSeminarProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class UndanganSeminarProposal extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jadwal;
    protected $namaDosen;

    public $tries = 3;
    public $retryAfter = 60;
    public $timeout = 300;

    public function __construct(JadwalSeminarProposal $jadwal, string $namaDosen)
    {
        $this->jadwal = $jadwal;
        $this->namaDosen = $namaDosen;
        $this->onQueue('emails');
    }

    public function uniqueId(): string
    {
        return 'undangan-sempro-' . $this->jadwal->id . '-dosen-' . md5($this->namaDosen) . '-' . time();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $pendaftaran = $this->jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $tanggal = $this->jadwal->tanggal_ujian;
        $hariTanggal = $tanggal->translatedFormat('l, d F Y');

        $jamMulai = Carbon::parse($this->jadwal->waktu_mulai)->format('H:i');
        $jamSelesai = Carbon::parse($this->jadwal->waktu_selesai)->format('H:i');

        $subject = sprintf(
            'Undangan Ujian Seminar Proposal Skripsi Prodi S1 Teknik Informatika Hari %s, a.n %s Secara Luring',
            $hariTanggal,
            $mahasiswa->name
        );

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.undangan-seminar-proposal', [
                'namaDosen' => $this->namaDosen,
                'hariTanggal' => $hariTanggal,
                'jamMulai' => $jamMulai,
                'jamSelesai' => $jamSelesai,
                'ruangan' => $this->jadwal->ruangan,
                'mahasiswaNama' => $mahasiswa->name,
                'mahasiswaNim' => $mahasiswa->nim,
                'judulSkripsi' => strip_tags($pendaftaran->judul_skripsi),
                'dosenPembimbing' => $pendaftaran->dosenPembimbing->name,
                'actionUrl' => route('admin.pendaftaran-seminar-proposal.show', $pendaftaran->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $pendaftaran = $this->jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $hariTanggal = $this->jadwal->tanggal_ujian->translatedFormat('l, d F Y');

        return [
            'type' => 'undangan_sempro',
            'title' => 'Undangan Seminar Proposal',
            'message' => 'Anda diundang menguji seminar proposal ' . $mahasiswa->name . ' (' . $mahasiswa->nim . ') pada ' . $hariTanggal,
            'jadwal_id' => $this->jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'mahasiswa_nama' => $mahasiswa->name,
            'mahasiswa_nim' => $mahasiswa->nim,
            'judul_skripsi' => $pendaftaran->judul_skripsi,
            'tanggal' => $this->jadwal->tanggal_ujian->format('Y-m-d'),
            'hari_tanggal' => $hariTanggal,
            'jam_mulai' => Carbon::parse($this->jadwal->waktu_mulai)->format('H:i:s'),
            'jam_selesai' => Carbon::parse($this->jadwal->waktu_selesai)->format('H:i:s'),
            'ruangan' => $this->jadwal->ruangan,
            'action_url' => route('admin.pendaftaran-seminar-proposal.show', $pendaftaran->id),
        ];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'default',
        ];
    }
}