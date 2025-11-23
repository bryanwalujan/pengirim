<?php

namespace App\Notifications;

use App\Models\KomisiHasil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KomisiHasilNeedApprovalNotification extends Notification
{
    use Queueable;

    protected $komisiHasil;
    protected $approvalType;

    /**
     * Create a new notification instance.
     *
     * @param KomisiHasil $komisiHasil
     * @param string $approvalType ('pembimbing1', 'pembimbing2', 'korprodi')
     */
    public function __construct(KomisiHasil $komisiHasil, string $approvalType)
    {
        $this->komisiHasil = $komisiHasil;
        $this->approvalType = $approvalType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // Map approval type ke text yang user-friendly
        $approvalTypeText = match ($this->approvalType) {
            'pembimbing1' => 'Pembimbing 1',
            'pembimbing2' => 'Pembimbing 2',
            'korprodi' => 'Koordinator Program Studi',
            default => 'Unknown'
        };

        // Icon berdasarkan approval type
        $icon = match ($this->approvalType) {
            'pembimbing1' => 'bx-user-check',
            'pembimbing2' => 'bx-user-circle',
            'korprodi' => 'bx-crown',
            default => 'bx-bell'
        };

        // Badge color berdasarkan approval type
        $badgeClass = match ($this->approvalType) {
            'pembimbing1' => 'bg-warning',
            'pembimbing2' => 'bg-info',
            'korprodi' => 'bg-primary',
            default => 'bg-secondary'
        };

        return [
            'message' => "Komisi Hasil membutuhkan persetujuan Anda sebagai {$approvalTypeText}",
            'type' => $this->approvalType,
            'komisi_hasil_id' => $this->komisiHasil->id,
            'mahasiswa_name' => $this->komisiHasil->user->name,
            'mahasiswa_nim' => $this->komisiHasil->user->nim,
            'judul_skripsi' => $this->komisiHasil->judul_skripsi,
            'url' => route('admin.komisi-hasil.show', $this->komisiHasil->id),
            'icon' => $icon,
            'badge_class' => $badgeClass,
            'created_at' => now()->translatedFormat('l, d M Y H:i'),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->approvalType) {
            'pembimbing1' => 'Persetujuan Komisi Hasil - Pembimbing 1',
            'pembimbing2' => 'Persetujuan Komisi Hasil - Pembimbing 2',
            'korprodi' => 'Persetujuan Komisi Hasil - Koordinator Program Studi',
            default => 'Persetujuan Komisi Hasil'
        };

        $approvalTypeText = match ($this->approvalType) {
            'pembimbing1' => 'Pembimbing 1',
            'pembimbing2' => 'Pembimbing 2',
            'korprodi' => 'Koordinator Program Studi',
            default => 'Unknown'
        };

        return (new MailMessage)
            ->subject($subject)
            ->line("Mahasiswa {$this->komisiHasil->user->name} ({$this->komisiHasil->user->nim}) telah mengajukan Komisi Hasil.")
            ->line("Judul: {$this->komisiHasil->judul_skripsi}")
            ->line("Diperlukan persetujuan Anda sebagai {$approvalTypeText}.")
            ->action('Lihat Detail', route('admin.komisi-hasil.index', ['open' => $this->komisiHasil->id]))
            ->line('Terima kasih!');
    }

}