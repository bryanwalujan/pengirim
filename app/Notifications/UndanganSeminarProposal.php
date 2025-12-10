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

    // Timeout untuk job (5 menit)
    public $timeout = 300;

    public function __construct(JadwalSeminarProposal $jadwal, string $namaDosen)
    {
        $this->jadwal = $jadwal;
        $this->namaDosen = $namaDosen;

        // Set queue untuk optimasi
        $this->onQueue('emails');
    }

    /**
     * Get unique ID untuk notification ini (mencegah duplicate)
     */
    public function uniqueId(): string
    {
        return 'undangan-sempro-' . $this->jadwal->id . '-dosen-' . md5($this->namaDosen) . '-' . time();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pendaftaran = $this->jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $tanggal = $this->jadwal->tanggal;
        $hariTanggal = $tanggal->translatedFormat('l, d F Y');

        $jamMulai = Carbon::parse($this->jadwal->jam_mulai)->format('H:i');
        $jamSelesai = Carbon::parse($this->jadwal->jam_selesai)->format('H:i');

        $subject = sprintf(
            'Undangan Ujian Seminar Proposal Skripsi Prodi S1 Teknik Informatika Hari %s, a.n %s Secara Luring',
            $hariTanggal,
            $mahasiswa->name
        );

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Dengan Hormat,')
            ->line('Bersama ini kami mengundang Bapak/Ibu Dosen untuk menghadiri pelaksanaan **Seminar Proposal Skripsi** yang akan dilaksanakan pada:')
            ->line('')
            ->line('**Hari / Tanggal:** ' . $hariTanggal)
            ->line('**Jam:** ' . $jamMulai . ' - ' . $jamSelesai . ' WITA')
            ->line('**Tempat:** ' . $this->jadwal->ruangan . ' (Luring)')
            ->line('')
            ->line('**Data Mahasiswa:**')
            ->line('Nama: **' . $mahasiswa->name . '**')
            ->line('NIM: **' . $mahasiswa->nim . '**')
            ->line('')
            ->line('**Judul Skripsi:**')
            ->line('*' . strip_tags($pendaftaran->judul_skripsi) . '*')
            ->line('')
            ->line('**Dosen Pembimbing:**')
            ->line($pendaftaran->dosenPembimbing->name)
            ->line('')
            ->action('Lihat Detail Pendaftaran', route('admin.pendaftaran-seminar-proposal.show', $pendaftaran->id))
            ->line('')
            ->line('Demikian undangan ini, atas kehadiran Bapak/Ibu Dosen kami sampaikan terima kasih.')
            ->salutation('Hormat kami,')
            ->salutation('**Sistem E-Service**')
            ->salutation('**Jurusan Teknik Informatika**');
    }

    public function toArray(object $notifiable): array
    {
        $pendaftaran = $this->jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $hariTanggal = $this->jadwal->tanggal->translatedFormat('l, d F Y');

        return [
            'type' => 'undangan_sempro',
            'title' => 'Undangan Seminar Proposal',
            'message' => 'Anda diundang menguji seminar proposal ' . $mahasiswa->name . ' (' . $mahasiswa->nim . ') pada ' . $hariTanggal,
            'jadwal_id' => $this->jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'mahasiswa_nama' => $mahasiswa->name,
            'mahasiswa_nim' => $mahasiswa->nim,
            'judul_skripsi' => $pendaftaran->judul_skripsi,
            'tanggal' => $this->jadwal->tanggal->format('Y-m-d'),
            'hari_tanggal' => $hariTanggal,
            'jam_mulai' => $this->jadwal->jam_mulai,
            'jam_selesai' => $this->jadwal->jam_selesai,
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