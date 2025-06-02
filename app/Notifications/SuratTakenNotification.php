<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SuratTakenNotification extends Notification
{
    use Queueable;

    protected $surat;
    protected $suratType;
    protected $routeName;

    public function __construct($surat)
    {
        $this->surat = $surat;
        $this->setSuratType();
    }

    protected function setSuratType()
    {
        $class = get_class($this->surat);

        switch ($class) {
            case 'App\Models\SuratAktifKuliah':
                $this->suratType = 'Surat Aktif Kuliah';
                $this->routeName = 'admin.surat-aktif-kuliah.show';
                break;
            case 'App\Models\SuratIjinSurvey':
                $this->suratType = 'Surat Ijin Survey';
                $this->routeName = 'admin.surat-ijin-survey.show';
                break;
            default:
                $this->suratType = 'Surat';
                $this->routeName = 'admin.dashboard.index';
        }
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Mahasiswa telah mengkonfirmasi pengambilan ' . strtolower($this->suratType),
            'mahasiswa_name' => $this->surat->mahasiswa->name ?? 'Mahasiswa',
            'mahasiswa_nim' => $this->surat->mahasiswa->nim ?? 'NIM tidak tersedia',
            'surat_type' => $this->suratType,
            'surat_id' => $this->surat->id, // Add this line
            'surat_class' => get_class($this->surat),
            'url' => route($this->routeName, $this->surat->id),
            'confirmed_at' => now()->format('d/m/Y H:i'),
        ];
    }
}