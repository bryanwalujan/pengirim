<div class="modal-body" x-data="peminjamanProyektorDetail()">
    <div class="row">
        <div class="col-12">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th width="200px">Nama Peminjam</th>
                        <td>{{ $peminjaman->user->name }}</td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td>{{ $peminjaman->user->nim }}</td>
                    </tr>
                    @if ($peminjaman->user->email)
                        <tr>
                            <th>Email</th>
                            <td>{{ $peminjaman->user->email }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Kode Proyektor</th>
                        <td>
                            <span class="badge bg-info">
                                <i class='bx bx-video me-1'></i>{{ $peminjaman->proyektor_code }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Keperluan</th>
                        <td style="vertical-align: top">{{ $peminjaman->keperluan }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pinjam</th>
                        <td>
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('l, d M Y H:i') }}
                            WIB
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Kembali</th>
                        <td>
                            @if ($peminjaman->tanggal_kembali)
                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('l, d M Y H:i') }}
                                WIB
                            @else
                                <span class="badge bg-label-secondary">Belum dikembalikan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if ($peminjaman->status == 'dipinjam')
                                <span class="badge bg-label-warning">
                                    <i class="bx bx-time-five me-1"></i> Sedang Dipinjam
                                </span>
                            @else
                                <span class="badge bg-label-success">
                                    <i class="bx bx-check-circle me-1"></i> Dikembalikan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if ($peminjaman->status == 'dipinjam')
                        <tr>
                            <th>Durasi Pinjam</th>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $peminjaman->tanggal_pinjam->diffForHumans(null, true) }}
                                </span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            @php
                $user = Auth::user();
                $canManage = $user->hasRole(['admin', 'staff']);
            @endphp

            @if ($canManage && $peminjaman->status == 'dipinjam')
                {{-- Override Return Section --}}
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-warning mb-3">
                            <i class="bx bx-check-shield me-1"></i> Override Pengembalian
                        </h6>

                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan fitur ini jika mahasiswa lupa mengembalikan proyektor</li>
                                <li>Proyektor akan otomatis tersedia kembali setelah override</li>
                                <li>Tindakan ini akan tercatat dalam sistem</li>
                            </ul>
                        </div>


                        <div class="d-grid">
                            <button type="button" class="btn btn-warning"
                                @click="handleOverrideReturn({{ $peminjaman->id }}, '{{ $peminjaman->user->name }}', '{{ $peminjaman->proyektor_code }}')">
                                <i class="bx bx-check-circle me-1"></i> Override Kembalikan Proyektor
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($canManage)
                {{-- Delete Section --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-trash me-1"></i> Zona Berbahaya
                        </h6>

                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Menghapus data peminjaman akan <strong>menghapus semua riwayat</strong></li>
                                <li>Tindakan ini <strong>tidak dapat dibatalkan</strong></li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-danger"
                                @click="handleDelete({{ $peminjaman->id }}, '{{ $peminjaman->user->name }}', '{{ $peminjaman->user->nim }}', '{{ $peminjaman->proyektor_code }}', '{{ $peminjaman->status }}')">
                                <i class="bx bx-trash me-1"></i> Hapus Data Peminjaman
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .swal-high-zindex {
        z-index: 99999 !important;
    }

    .swal2-container {
        z-index: 99999 !important;
    }

    .swal-popup-custom {
        font-size: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #detailModal.modal {
        z-index: 9999;
    }

    .modal-backdrop {
        z-index: 9998;
    }

    .card.border-danger,
    .card.border-warning {
        border-width: 2px;
    }

    .card.border-warning {
        box-shadow: 0 0 0 0.1rem rgba(255, 193, 7, 0.25);
    }
</style>
