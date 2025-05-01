<?php

namespace App\Notifications;

use App\Models\SuratAktifKuliah;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SuratTakenNotification extends Notification
{
    use Queueable;

    protected $surat;

    public function __construct(SuratAktifKuliah $surat)
    {
        $this->surat = $surat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Mahasiswa telah mengkonfirmasi pengambilan surat',
            'mahasiswa_name' => $this->surat->mahasiswa->name ?? 'Mahasiswa',
            'mahasiswa_nim' => $this->surat->mahasiswa->nim ?? 'NIM tidak tersedia',
            'surat_type' => 'Surat Aktif Kuliah',
            'url' => route('admin.surat-aktif-kuliah.show', $this->surat->id),
            'confirmed_at' => now()->format('d/m/Y H:i'),
        ];
    }

}