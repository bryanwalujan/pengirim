<?php

namespace App\Notifications;

use App\Models\JadwalUjianHasil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class UndanganUjianHasil extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jadwal;
    protected $namaDosen;

    public $tries = 3;
    public $retryAfter = 60;
    public $timeout = 300;

    public function __construct(JadwalUjianHasil $jadwal, string $namaDosen)
    {
        $this->jadwal = $jadwal;
        $this->namaDosen = $namaDosen;
        $this->onQueue('emails');
    }

    public function uniqueId(): string
    {
        return 'undangan-ujian-hasil-' . $this->jadwal->id . '-dosen-' . md5($this->namaDosen) . '-' . time();
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
        $pendaftaran = $this->jadwal->pendaftaranUjianHasil;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $tanggal = $this->jadwal->tanggal_ujian;
        $hariTanggal = $tanggal->translatedFormat('l, d F Y');

        $jamMulai = Carbon::parse($this->jadwal->waktu_mulai)->format('H:i');
        $jamSelesai = Carbon::parse($this->jadwal->waktu_selesai)->format('H:i');

        $subject = sprintf(
            'Undangan Ujian Hasil Skripsi Prodi S1 Teknik Informatika Hari %s, a.n %s Secara Luring',
            $hariTanggal,
            $mahasiswa->name
        );

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.undangan-ujian-hasil', [
                'namaDosen' => $this->namaDosen,
                'hariTanggal' => $hariTanggal,
                'jamMulai' => $jamMulai,
                'jamSelesai' => $jamSelesai,
                'ruangan' => $this->jadwal->ruangan,
                'mahasiswaNama' => $mahasiswa->name,
                'mahasiswaNim' => $mahasiswa->nim,
                'judulSkripsi' => strip_tags($pendaftaran->judul_skripsi),
                'dosenPembimbing1' => $pendaftaran->dosenPembimbing1->name ?? 'N/A',
                'dosenPembimbing2' => $pendaftaran->dosenPembimbing2->name ?? 'N/A',
                'actionUrl' => route('admin.pendaftaran-ujian-hasil.show', $pendaftaran->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $pendaftaran = $this->jadwal->pendaftaranUjianHasil;
        $mahasiswa = $pendaftaran->user;

        Carbon::setLocale('id');
        $hariTanggal = $this->jadwal->tanggal_ujian->translatedFormat('l, d F Y');

        return [
            'type' => 'undangan_ujian_hasil',
            'title' => 'Undangan Ujian Hasil',
            'message' => 'Anda diundang menguji ujian hasil ' . $mahasiswa->name . ' (' . $mahasiswa->nim . ') pada ' . $hariTanggal,
            'jadwal_id' => $this->jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'mahasiswa_nama' => $mahasiswa->name,
            'mahasiswa_nim' => $mahasiswa->nim,
            'judul_skripsi' => $pendaftaran->judul_skripsi,
            'tanggal' => $this->jadwal->tanggal_ujian->format('Y-m-d'),
            'hari_tanggal' => $hariTanggal,
            'waktu_mulai' => $this->jadwal->waktu_mulai,
            'waktu_selesai' => $this->jadwal->waktu_selesai,
            'ruangan' => $this->jadwal->ruangan,
            'action_url' => route('admin.pendaftaran-ujian-hasil.show', $pendaftaran->id),
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
