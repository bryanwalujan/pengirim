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

    public function toArray($notifiable)
    {
        return [
            'message' => 'Surat aktif kuliah telah diambil oleh mahasiswa',
            'url' => route('admin.surat-aktif-kuliah.show', $this->surat->id),
            'surat_id' => $this->surat->id,
            'mahasiswa' => $this->surat->mahasiswa->name,
        ];
    }
}