<div class="row">
    <div class="col-md-6">
        <p><strong>Nama Mahasiswa</strong><br>: {{ $pendaftaranUjianHasil->nama }}</p>
        <p><strong>NIM</strong><br>: {{ $pendaftaranUjianHasil->nim }}</p>
        <p><strong>Angkatan</strong><br>: {{ $pendaftaranUjianHasil->angkatan }}</p>
        <p><strong>IPK</strong><br>: {{ $pendaftaranUjianHasil->ipk }}</p>
        <p><strong>Dosen PA</strong><br>: {{ $pendaftaranUjianHasil->dosenPa->name ?? 'Belum Dipilih' }}</p>
        <p><strong>Dosen Pembimbing SKripsi 1</strong><br>:
            {{ $pendaftaranUjianHasil->dosenPembimbing1->name ?? 'Belum Dipilih' }}</p>
        <p><strong>Dosen Pembimbing Skripsi 2</strong><br>:
            {{ $pendaftaranUjianHasil->dosenPembimbing2->name ?? 'Belum Dipilih' }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Judul Skripsi</strong><br>: {{ $pendaftaranUjianHasil->judul_skripsi }}</p>
        <p><strong>Tanggal Pengajuan</strong><br>: {{ $pendaftaranUjianHasil->created_at->format('d M Y, H:i') }}</p>
    </div>
</div>

<hr>

<h5 class="mb-3">Dokumen Terlampir</h5>
<div class="list-group">
    <a href="{{ Storage::url($pendaftaranUjianHasil->transkrip_nilai) }}" class="list-group-item list-group-item-action"
        target="_blank">
        <i class="bx bx-file me-2"></i>Transkrip Nilai
    </a>
    <a href="{{ Storage::url($pendaftaranUjianHasil->file_skripsi) }}" class="list-group-item list-group-item-action"
        target="_blank">
        <i class="bx bx-file me-2"></i>File Skripsi
    </a>
    <a href="{{ Storage::url($pendaftaranUjianHasil->komisi_hasil) }}" class="list-group-item list-group-item-action"
        target="_blank">
        <i class="bx bx-file me-2"></i>Surat Komisi Hasil
    </a>
    <a href="{{ Storage::url($pendaftaranUjianHasil->surat_permohonan_hasil) }}"
        class="list-group-item list-group-item-action" target="_blank">
        <i class="bx bx-file me-2"></i>Surat Permohonan Hasil
    </a>
</div>
