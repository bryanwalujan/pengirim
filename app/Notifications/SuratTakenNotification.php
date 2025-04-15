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
            'message' => 'Surat Aktif Kuliah telah diambil oleh mahasiswa',
            'surat_id' => $this->surat->id,
            'mahasiswa' => $this->surat->mahasiswa->name,
            'nim' => $this->surat->mahasiswa->nim,
            'link' => route('admin.surat-aktif-kuliah.show', $this->surat->id),
        ];
    }

    // Opsional: Jika ingin mengirim email juga
    //    public function toMail($notifiable)
    //    {
    //        return (new MailMessage)
    //                    ->subject('Surat Aktif Kuliah Telah Diambil')
    //                    ->line('Surat Aktif Kuliah dengan nomor '.$this->surat->nomor_surat.' telah diambil oleh mahasiswa.')
    //                    ->action('Lihat Detail', route('admin.surat-aktif-kuliah.show', $this->surat->id))
    //                    ->line('Terima kasih!');
    //    }
}