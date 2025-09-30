<?php

return [
    /*
     |---------------------------------------------------------------
     | Status final yang dianggap "selesai"
     |---------------------------------------------------------------
     | Hanya ketika status masuk daftar ini mahasiswa boleh ajukan
     | surat baru. Silakan sesuaikan (misal: tambah 'disetujui' jika
     | mau langsung boleh setelah disetujui).
     */
    'final_statuses' => [
        'sudah_diambil',
        'selesai',
        'ditolak',
    ],

    /*
     |---------------------------------------------------------------
     | Apakah wajib file_surat_path sudah ada?
     |---------------------------------------------------------------
     | Jika true: walau status final, tetap tunggu file surat terbuat.
     */
    'require_generated_file' => false,

    /*
     |---------------------------------------------------------------
     | Daftar model surat yang dibatasi
     |---------------------------------------------------------------
     */
    'models' => [
        \App\Models\SuratAktifKuliah::class,
        \App\Models\SuratCutiAkademik::class,
        \App\Models\SuratIjinSurvey::class,
        \App\Models\SuratPindah::class,
    ],

    /*
     | Map nama manusiawi untuk pesan
     */
    'model_labels' => [
        \App\Models\SuratAktifKuliah::class => 'Surat Aktif Kuliah',
        \App\Models\SuratCutiAkademik::class => 'Surat Cuti Akademik',
        \App\Models\SuratIjinSurvey::class => 'Surat Ijin Survey',
        \App\Models\SuratPindah::class => 'Surat Pindah',
    ],
];