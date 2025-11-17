<?php
// filepath: /c:/laragon/www/eservice-app/app/Notifications/KomisiHasilNeedApprovalNotification.php

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
            'korprodi' => 'Koordinator Prodi',
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
            // Data Komisi Hasil
            'komisi_hasil_id' => $this->komisiHasil->id,
            'mahasiswa_name' => $this->komisiHasil->user->name,
            'mahasiswa_nim' => $this->komisiHasil->user->nim,
            'judul_skripsi' => $this->komisiHasil->judul_skripsi,

            // Approval Info
            'type' => $this->approvalType,
            'approval_type_text' => $approvalTypeText,
            'status' => $this->komisiHasil->status,

            // Timestamps
            'created_at' => $this->komisiHasil->created_at->format('d M Y H:i'),

            // URL untuk redirect ke detail (dengan auto-open modal)
            'url' => route('admin.komisi-hasil.index', ['open' => $this->komisiHasil->id]),

            // UI Elements
            'icon' => $icon,
            'badge_class' => $badgeClass,
            'message' => "Komisi Hasil membutuhkan persetujuan Anda sebagai {$approvalTypeText}",

            // Additional Info
            'pembimbing1_name' => $this->komisiHasil->pembimbing1->name ?? '-',
            'pembimbing2_name' => $this->komisiHasil->pembimbing2->name ?? '-',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->approvalType) {
            'pembimbing1' => 'Persetujuan Komisi Hasil sebagai Pembimbing 1',
            'pembimbing2' => 'Persetujuan Komisi Hasil sebagai Pembimbing 2',
            'korprodi' => 'Persetujuan Komisi Hasil sebagai Koordinator Prodi',
            default => 'Persetujuan Komisi Hasil'
        };

        $approvalTypeText = match ($this->approvalType) {
            'pembimbing1' => 'Pembimbing 1',
            'pembimbing2' => 'Pembimbing 2',
            'korprodi' => 'Koordinator Prodi',
            default => 'Pihak Terkait'
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Anda mendapat pengajuan komisi hasil yang perlu disetujui sebagai **{$approvalTypeText}**.")
            ->line('**Detail Mahasiswa:**')
            ->line('Nama: ' . $this->komisiHasil->user->name)
            ->line('NIM: ' . $this->komisiHasil->user->nim)
            ->line('**Judul Skripsi:**')
            ->line($this->komisiHasil->judul_skripsi)
            ->line('**Pembimbing:**')
            ->line('Pembimbing 1: ' . ($this->komisiHasil->pembimbing1->name ?? '-'))
            ->line('Pembimbing 2: ' . ($this->komisiHasil->pembimbing2->name ?? '-'))
            ->action('Lihat Detail & Setujui', route('admin.komisi-hasil.index', ['open' => $this->komisiHasil->id]))
            ->line('Silakan login ke sistem untuk memproses persetujuan.')
            ->line('Terima kasih!')
            ->salutation('Salam, ' . config('app.name'));
    }

    /**
     * Get the notification's database type (untuk filtering di UI).
     */
    public function databaseType(object $notifiable): string
    {
        return 'komisi_hasil_approval';
    }
}