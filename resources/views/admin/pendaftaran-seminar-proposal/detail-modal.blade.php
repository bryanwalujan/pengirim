<div class="row">
    <div class="col-md-6">
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">Nama Mahasiswa</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->user->name }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">NIM</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->user->nim }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">Angkatan</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->angkatan }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">IPK</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->ipk }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">Dosen Pembimbing</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->dosenPembimbing->name ?? 'Belum Dipilih' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">Judul Skripsi</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->judul_skripsi }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label fw-semibold">Tanggal Pengajuan</label>
            <div class="col-sm-8">
                <p class="form-control-plaintext">: {{ $pendaftaran->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<hr class="my-4">

<h6 class="fw-semibold mb-3">Dokumen Terlampir</h6>
<div class="list-group">
    <a href="{{ Storage::url($pendaftaran->file_transkrip_nilai) }}" target="_blank"
        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <span><i class="bx bx-file me-2"></i>Transkrip Nilai</span>
        <i class="bx bx-link-external"></i>
    </a>
    <a href="{{ Storage::url($pendaftaran->file_proposal_penelitian) }}" target="_blank"
        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <span><i class="bx bx-file me-2"></i>Proposal Penelitian</span>
        <i class="bx bx-link-external"></i>
    </a>
    <a href="{{ Storage::url($pendaftaran->file_surat_permohonan) }}" target="_blank"
        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <span><i class="bx bx-file me-2"></i>Surat Permohonan</span>
        <i class="bx bx-link-external"></i>
    </a>
</div>
