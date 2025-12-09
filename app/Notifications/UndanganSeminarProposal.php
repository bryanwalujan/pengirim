<?php

namespace App\Notifications;

use App\Models\JadwalSeminarProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class UndanganSeminarProposal extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jadwal;
    protected $namaDosen;

    /**
     * Create a new notification instance.
     */
    public function __construct(JadwalSeminarProposal $jadwal, string $namaDosen)
    {
        $this->jadwal = $jadwal;
        $this->namaDosen = $namaDosen;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
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

        $mail = (new MailMessage)
            ->subject('Undangan Seminar Proposal - ' . $mahasiswa->name)
            ->greeting('Kepada Yth. ' . $this->namaDosen)
            ->line('Dengan hormat,')
            ->line('Anda diundang untuk menjadi **Penguji/Pembahas** dalam Seminar Proposal mahasiswa:')
            ->line('**Nama Mahasiswa:** ' . $mahasiswa->name)
            ->line('**NIM:** ' . $mahasiswa->nim)
            ->line('**Judul Skripsi:**')
            ->line('*' . $pendaftaran->judul_skripsi . '*')
            ->line('')
            ->line('### Detail Jadwal Seminar Proposal')
            ->line('📅 **Tanggal:** ' . $this->jadwal->tanggal_formatted)
            ->line('🕐 **Waktu:** ' . $this->jadwal->jam_formatted)
            ->line('📍 **Ruangan:** ' . $this->jadwal->ruangan)
            ->line('')
            ->line('**Dosen Pembimbing:**')
            ->line($pendaftaran->dosenPembimbing->name)
            ->line('')
            ->action('Lihat Detail Pendaftaran', route('admin.pendaftaran-seminar-proposal.show', $pendaftaran->id))
            ->line('Mohon untuk dapat hadir tepat waktu.')
            ->salutation('Hormat kami, Sistem E-Service Jurusan Teknik Informatika');

        // Attach file proposal (dari pendaftaran)
        if ($pendaftaran->file_proposal_penelitian) {
            $proposalPath = Storage::disk('local')->path($pendaftaran->file_proposal_penelitian);
            if (file_exists($proposalPath)) {
                $mail->attach($proposalPath, [
                    'as' => 'Proposal_' . $mahasiswa->nim . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        // Attach SK Proposal
        if ($this->jadwal->file_sk_proposal) {
            $skPath = Storage::disk('public')->path($this->jadwal->file_sk_proposal);
            if (file_exists($skPath)) {
                $mail->attach($skPath, [
                    'as' => 'SK_Proposal_' . $mahasiswa->nim . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray(object $notifiable): array
    {
        $pendaftaran = $this->jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;

        return [
            'type' => 'undangan_sempro',
            'title' => 'Undangan Seminar Proposal',
            'message' => 'Anda diundang menguji seminar proposal ' . $mahasiswa->name . ' (' . $mahasiswa->nim . ')',
            'jadwal_id' => $this->jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'mahasiswa_nama' => $mahasiswa->name,
            'mahasiswa_nim' => $mahasiswa->nim,
            'judul_skripsi' => $pendaftaran->judul_skripsi,
            'tanggal' => $this->jadwal->tanggal->format('Y-m-d'),
            'jam_mulai' => $this->jadwal->jam_mulai,
            'jam_selesai' => $this->jadwal->jam_selesai,
            'ruangan' => $this->jadwal->ruangan,
            'action_url' => route('admin.pendaftaran-seminar-proposal.show', $pendaftaran->id),
        ];
    }

}