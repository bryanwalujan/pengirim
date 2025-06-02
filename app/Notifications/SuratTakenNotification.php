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

    // Mapping of model classes to their display names and routes
    protected const SURAT_TYPES = [
        'App\Models\SuratAktifKuliah' => [
            'name' => 'Surat Aktif Kuliah',
            'route' => 'admin.surat-aktif-kuliah.show'
        ],
        'App\Models\SuratIjinSurvey' => [
            'name' => 'Surat Ijin Survey',
            'route' => 'admin.surat-ijin-survey.show'
        ],
        'App\Models\SuratCutiAkademik' => [
            'name' => 'Surat Cuti Akademik',
            'route' => 'admin.surat-cuti-akademik.show'
        ]
    ];

    public function __construct($surat)
    {
        $this->surat = $surat;
        $this->setSuratType();
    }

    protected function setSuratType()
    {
        $class = get_class($this->surat);

        if (array_key_exists($class, self::SURAT_TYPES)) {
            $this->suratType = self::SURAT_TYPES[$class]['name'];
            $this->routeName = self::SURAT_TYPES[$class]['route'];
        } else {
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
            'surat_id' => $this->surat->id,
            'surat_class' => get_class($this->surat),
            'url' => route($this->routeName, $this->surat->id),
            'confirmed_at' => now()->format('d/m/Y H:i'),
        ];
    }
}