<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TemplateNotesSelector extends Component
{
    public $textareaId;
    public $textareaName;
    public $currentValue;
    public $userRole;
    public $statusField;

    public function __construct(
        $textareaId = 'catatan_admin',
        $textareaName = 'catatan_admin',
        $currentValue = '',
        $userRole = 'staff',
        $statusField = 'status'
    ) {
        $this->textareaId = $textareaId;
        $this->textareaName = $textareaName;
        $this->currentValue = $currentValue;
        $this->userRole = $userRole;
        $this->statusField = $statusField;
    }

    public function render()
    {
        return view('components.template-notes-selector');
    }

    public function getTemplates()
    {
        $templates = [
            'staff' => [
                'diproses' => [
                    'category' => 'Template Proses Surat',
                    'icon' => 'bx-check-circle',
                    'items' => [
                        ['text' => '📝 Proses Normal', 'template' => 'Surat sedang diproses dan akan segera diteruskan ke Korprodi dan Pimpinan Jurusan PTIK.', 'class' => 'bg-primary'],
                        ['text' => '✅ Verifikasi Data', 'template' => 'Dokumen lengkap, surat dalam proses verifikasi data mahasiswa.', 'class' => 'bg-primary'],
                        ['text' => '📋 Tahap Administrasi', 'template' => 'Pengajuan diterima dan sedang dalam tahap pemrosesan administrasi.', 'class' => 'bg-primary'],
                    ]
                ],
                'ditolak' => [
                    'category' => 'Template Penolakan',
                    'icon' => 'bx-x-circle',
                    'items' => [
                        ['text' => '📎 Dokumen Tidak Lengkap', 'template' => 'Dokumen pendukung tidak lengkap. Harap melengkapi dokumen yang diperlukan.', 'class' => 'bg-danger'],
                        ['text' => '⚠️ Data Tidak Sesuai', 'template' => 'Data mahasiswa tidak sesuai dengan sistem akademik. Silakan hubungi bagian akademik.', 'class' => 'bg-danger'],
                        ['text' => '💳 Tanggungan Administrasi', 'template' => 'Mahasiswa memiliki tanggungan administrasi. Harap diselesaikan terlebih dahulu.', 'class' => 'bg-danger'],
                        ['text' => '❓ Tujuan Tidak Jelas', 'template' => 'Tujuan pengajuan belum jelas. Mohon diperjelas maksud dan tujuan pengajuan surat.', 'class' => 'bg-danger'],
                        ['text' => '🚫 Status Cuti', 'template' => 'Surat tidak dapat diproses karena mahasiswa sedang cuti akademik.', 'class' => 'bg-danger'],
                    ]
                ],
                'siap_diambil' => [
                    'category' => 'Template Siap Diambil',
                    'icon' => 'bx-package',
                    'items' => [
                        ['text' => '✅ Siap Diambil', 'template' => 'Surat telah selesai diproses dan siap untuk diambil. Silakan konfirmasi pengambilan.', 'class' => 'bg-success'],
                        ['text' => '📥 Dapat Diunduh', 'template' => 'Surat telah ditandatangani dan dapat diunduh melalui sistem. Terima kasih atas kesabarannya.', 'class' => 'bg-success'],
                        ['text' => '🎉 Selesai Diproses', 'template' => 'Proses persetujuan selesai. Surat sudah tersedia dan dapat Anda akses kapan saja.', 'class' => 'bg-success'],
                    ]
                ],
            ],
            'dosen' => [
                'approve' => [
                    'category' => 'Template Persetujuan',
                    'icon' => 'bx-check-circle',
                    'items' => [
                        ['text' => '✅ Disetujui', 'template' => 'Surat telah disetujui dan dapat dilanjutkan ke proses berikutnya.', 'class' => 'bg-success'],
                        ['text' => '✔️ Verifikasi Selesai', 'template' => 'Data mahasiswa telah diverifikasi dan surat disetujui untuk proses selanjutnya.', 'class' => 'bg-success'],
                        ['text' => '📚 Mahasiswa Aktif', 'template' => 'Pengajuan disetujui. Mahasiswa masih aktif kuliah pada semester ini.', 'class' => 'bg-success'],
                    ]
                ],
                'reject' => [
                    'category' => 'Template Penolakan',
                    'icon' => 'bx-x-circle',
                    'items' => [
                        ['text' => '❌ Data Tidak Sesuai', 'template' => 'Mohon maaf, surat tidak dapat disetujui karena data tidak sesuai.', 'class' => 'bg-danger'],
                        ['text' => '📋 Persyaratan Belum Lengkap', 'template' => 'Pengajuan ditolak. Mahasiswa belum menyelesaikan persyaratan akademik.', 'class' => 'bg-danger'],
                        ['text' => 'ℹ️ Hubungi Koordinator', 'template' => 'Tidak dapat disetujui. Silakan hubungi koordinator untuk informasi lebih lanjut.', 'class' => 'bg-danger'],
                    ]
                ],
            ],
        ];

        return $templates[$this->userRole] ?? [];
    }
}