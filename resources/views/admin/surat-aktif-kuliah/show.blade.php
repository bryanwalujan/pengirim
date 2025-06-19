@extends('layouts.admin.app')

@section('title', 'Detail Surat Aktif Kuliah')

@push('styles')
    <style>
        .alert-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: rgb(255, 255, 255);
        }

        .alert-icon i {
            font-size: 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-2">
            <span class="text-muted">Detail Pengajuan Surat Aktif Kuliah</span>
        </h4>

        <div class="card mb-4">
            <div class="card-body">
                <!-- Status -->
                @php
                    $alertClass = match ($surat->status ?? 'diajukan') {
                        'diproses' => 'info',
                        'disetujui_kaprodi' => 'success',
                        'disetujui_pimpinan' => 'success',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'siap_diambil' => 'primary',
                        'sudah_diambil' => 'secondary',
                        default => 'warning',
                    };

                    $alertIcon = match ($surat->status ?? 'diajukan') {
                        'diproses' => 'bx bx-loader',
                        'disetujui_kaprodi' => 'bx bx-check',
                        'disetujui_pimpinan' => 'bx bx-check',
                        'disetujui' => 'bx bx-check',
                        'ditolak' => 'bx bx-error-circle',
                        'siap_diambil' => 'bx bx-package',
                        'sudah_diambil' => 'bx bx-check-circle',
                        default => 'bx bx-time',
                    };
                @endphp

                <div class="alert alert-{{ $alertClass }} alert-dismissible mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <span class="alert-icon rounded me-3">
                            <i class="icon-base {{ $alertIcon }}"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1 d-flex align-items-center gap-2">
                                Status:
                                <strong class="text-capitalize">
                                    {{ str_replace('_', ' ', $surat->status ?? 'Diajukan') }}
                                </strong>
                            </h6>
                            @if ($surat->status()->first()?->catatan_admin)
                                <hr class="my-2">
                                <p class="mb-0">
                                    <strong>Catatan Admin:</strong>
                                    {{ $surat->status()->first()->catatan_admin }}
                                </p>
                            @endif
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Informasi Surat -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Informasi Surat</h5>
                    <div class="row">
                        @if ($surat->status === 'diajukan' && auth()->user()->hasRole('staff'))
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" class="form-control"
                                    value="{{ $surat->nomor_surat ?? 'Akan digenerate otomatis' }}" readonly>
                                <small class="text-muted">
                                    Nomor surat akan digenerate saat memproses pengajuan
                                </small>
                            </div>
                        @else
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" class="form-control" value="{{ $surat->nomor_surat ?? '-' }}"
                                    readonly>
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '-' }}" readonly>
                        </div>
                        @if ($surat->penandatangan)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penandatangan (Pimpinan)</label>
                                <input type="text" class="form-control" value="{{ $surat->penandatangan->name }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Pimpinan</label>
                                <input type="text" class="form-control" value="{{ $surat->jabatan_penandatangan }}"
                                    readonly>
                            </div>
                        @endif
                        @if ($surat->penandatanganKaprodi)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penandatangan (Korprodi)</label>
                                <input type="text" class="form-control" value="{{ $surat->penandatanganKaprodi->name }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan Korprodi</label>
                                <input type="text" class="form-control"
                                    value="{{ $surat->jabatan_penandatangan_kaprodi }}" readonly>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Mahasiswa -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Informasi Mahasiswa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $surat->mahasiswa->name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" value="{{ $surat->mahasiswa->nim }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" value="{{ $surat->tahun_ajaran }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester</label>
                            <input type="text" class="form-control" value="{{ ucfirst($surat->semester) }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Detail Pengajuan -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Detail Pengajuan</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Tujuan Pengajuan</label>
                            <textarea class="form-control" rows="3" readonly>{{ $surat->tujuan_pengajuan }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan Tambahan</label>
                            <textarea class="form-control" rows="2" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                        </div>
                    </div>
                </div>

                @if ($surat->dokumenPendukung->count() > 0)
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Dokumen Pendukung</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($surat->dokumenPendukung as $dokumen)
                                        <tr>
                                            <td>{{ $dokumen->nama_asli }}</td>
                                            <td>{{ round($dokumen->size / (1024 * 1024), 2) }} MB</td>
                                            <td>
                                                <a href="{{ Storage::url($dokumen->path) }}" target="_blank"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bx bx-show"></i> Lihat
                                                </a>
                                                <a href="{{ route('admin.surat-aktif-kuliah.download-pendukung', $dokumen->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="bx bx-download"></i> Unduh
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($surat->file_surat_path)
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            @if ($surat->status === 'diproses')
                                Preview Surat
                            @elseif ($surat->status === 'disetujui_kaprodi')
                                Surat dengan Persetujuan Korprodi
                            @elseif (in_array($surat->status, ['disetujui_pimpinan', 'disetujui', 'siap_diambil', 'sudah_diambil']))
                                Surat Final
                            @else
                                File Surat
                            @endif
                        </h5>

                        <div
                            class="alert @if ($surat->status === 'diproses') alert-info @elseif ($surat->status === 'disetujui_kaprodi') alert-success @else alert-success @endif mb-3">
                            <div class="d-flex align-items-center">
                                <i
                                    class="bx @if ($surat->status === 'diproses') bx-info-circle @elseif ($surat->status === 'disetujui_kaprodi') bx-check-circle @else bx-check-circle @endif me-2"></i>
                                <div>
                                    @if ($surat->status === 'diproses')
                                        Ini adalah preview surat sebelum disetujui. Dokumen belum memiliki tanda tangan dan
                                        QR code verifikasi.
                                    @elseif ($surat->status === 'disetujui_kaprodi')
                                        Ini adalah surat dengan tanda tangan Korprodi. Menunggu persetujuan Pimpinan.
                                    @else
                                        Ini adalah surat final yang telah disetujui dan memiliki tanda tangan lengkap.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ Storage::url($surat->file_surat_path) }}" target="_blank"
                                class="btn btn-primary">
                                <i class="bx bx-show me-1"></i>
                                @if ($surat->status === 'diproses')
                                    Lihat Preview
                                @elseif ($surat->status === 'disetujui_kaprodi')
                                    Lihat Surat Korprodi
                                @else
                                    Lihat Surat Final
                                @endif
                            </a>
                            <a href="{{ route('admin.surat-aktif-kuliah.download', $surat->id) }}"
                                class="btn btn-success">
                                <i class="bx bx-download me-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if (auth()->user()->hasRole('staff') && in_array($surat->status, ['diajukan', 'disetujui']))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if ($surat->status === 'diajukan')
                            Proses Pengajuan
                        @else
                            Persiapkan Pengambilan
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.surat-aktif-kuliah.update-status', $surat->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($surat->status === 'diajukan')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nomor_surat" class="form-label">Nomor Surat</label>
                                    <input type="text" name="nomor_surat" class="form-control"
                                        value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                                        placeholder="Contoh: 0001 atau 0001/UN41.2/TI/{{ date('Y') }}">
                                    @error('nomor_surat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Nomor terakhir digunakan: <span
                                            class="fw-bold">{{ $lastNomorSurat ?? 'Belum ada' }}</span>
                                        <br>Nomor berikutnya yang akan digunakan: <span
                                            class="fw-bold text-primary">{{ $nextNomorSurat }}</span>
                                        <br>Kosongkan input di form proses untuk menggunakan nomor di atas, atau masukkan
                                        nomor manual (contoh: 0001 atau 0001/UN41.2/TI/{{ date('Y') }})
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status Pengajuan <span
                                        class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    @php
                                        $nextStatuses = [
                                            'diajukan' => ['diproses', 'ditolak'],
                                            'disetujui' => ['siap_diambil'],
                                        ];
                                        $currentStatus = $surat->status;
                                    @endphp

                                    @foreach ($nextStatuses[$currentStatus] ?? [] as $nextStatus)
                                        <option value="{{ $nextStatus }}">
                                            {{ ucfirst(str_replace('_', ' ', $nextStatus)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="catatan_admin" class="form-label">Catatan <span
                                        class="text-danger">*</span></label>
                                <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3" required
                                    placeholder="Masukkan catatan untuk mahasiswa">{{ $surat->status()->first()?->catatan_admin }}</textarea>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if (auth()->user()->hasRole('dosen'))
            @php
                $isKaprodi = str_contains(strtolower(auth()->user()->jabatan), 'koordinator program studi');
                $isPimpinan =
                    str_contains(strtolower(auth()->user()->jabatan), 'pimpinan jurusan') ||
                    str_contains(strtolower(auth()->user()->jabatan), 'ptik');
            @endphp

            @if (($surat->status === 'diproses' && $isKaprodi) || ($surat->status === 'disetujui_kaprodi' && $isPimpinan))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            @if ($surat->status === 'diproses')
                                Persetujuan Korprodi
                            @else
                                Persetujuan Pimpinan
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.surat-aktif-kuliah.approve', $surat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="status"
                                value="{{ $surat->status === 'diproses' ? 'disetujui_kaprodi' : 'disetujui' }}">

                            @if ($surat->status === 'diproses')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penandatangan (Korprodi)</label>
                                        <select name="penandatangan_kaprodi_id" class="form-select" required>
                                            <option value="{{ auth()->user()->id }}" selected>
                                                {{ auth()->user()->name }} (Anda)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan Korprodi</label>
                                        <input type="text" name="jabatan_penandatangan_kaprodi" class="form-control"
                                            value="{{ auth()->user()->jabatan ?? 'Koordinator Program Studi' }}" required>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penandatangan (Pimpinan)</label>
                                        <select name="penandatangan_id" class="form-select" required>
                                            <option value="{{ auth()->user()->id }}" selected>
                                                {{ auth()->user()->name }} (Anda)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan Pimpinan</label>
                                        <input type="text" name="jabatan_penandatangan" class="form-control"
                                            value="{{ auth()->user()->jabatan ?? 'Pimpinan Jurusan PTIK' }}" required>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="catatan_admin" class="form-label">Catatan Persetujuan</label>
                                <textarea name="catatan_admin" class="form-control" rows="3" required>{{ $surat->status()->first()?->catatan_admin }}</textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" name="action" value="approve" class="btn btn-success me-2">
                                    <i class="bx bx-check me-1"></i> Setujui
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">
                                    <i class="bx bx-x me-1"></i> Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('dosen'))
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle me-2"></i>
                    @if ($surat->status === 'diproses')
                        Hanya Koordinator Program Studi yang dapat menyetujui surat pada tahap ini
                    @elseif($surat->status === 'disetujui_kaprodi')
                        Hanya Pimpinan Jurusan PTIK yang dapat menyetujui surat pada tahap ini
                    @endif
                </div>
            @endif
        @endif

        @if (auth()->user()->hasRole('staff') && in_array($surat->status, ['diajukan', 'ditolak']))
            <div class="card mb-4">
                <div class="card-body">
                    <form id="delete-form-{{ $surat->id }}"
                        action="{{ route('admin.surat-aktif-kuliah.destroy', $surat->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <p>Anda akan menghapus pengajuan ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                        <button type="submit" class="btn btn-danger delete-btn delete-btn-card"
                            data-form-id="delete-form-{{ $surat->id }}">
                            <i class="bx bx-trash me-1"></i> Hapus Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Status</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse ($surat->trackings->sortByDesc('created_at') as $tracking)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                @php
                                    $timelineClass = match ($tracking->aksi) {
                                        'disetujui',
                                        'disetujui_kaprodi',
                                        'disetujui_pimpinan',
                                        'siap_diambil',
                                        'sudah_diambil'
                                            => 'text-success',
                                        'ditolak' => 'text-danger',
                                        default => 'text-primary',
                                    };
                                @endphp
                                <i class="bx bx-circle {{ $timelineClass }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-capitalize">{{ str_replace('_', ' ', $tracking->aksi) }}</h6>
                                <p class="text-muted small mb-1">
                                    {{ $tracking->created_at->format('d F Y H:i') }}
                                </p>
                                @if ($tracking->keterangan)
                                    <p class="mb-0">{{ $tracking->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actionSelect = document.querySelector('[name="action"]');
            const penandatanganPimpinan = document.getElementById('penandatangan_id');
            const jabatanPimpinan = document.getElementById('jabatan_penandatangan');
            const penandatanganKaprodi = document.getElementById('penandatangan_kaprodi_id');
            const jabatanKaprodi = document.getElementById('jabatan_penandatangan_kaprodi');

            if (actionSelect) {
                function toggleDosenFields() {
                    const isApprove = actionSelect.value === 'approve';
                    const fields =
                        @if ($surat->status === 'diproses')
                            [penandatanganKaprodi, jabatanKaprodi]
                        @else
                            [penandatanganPimpinan, jabatanPimpinan]
                        @endif ;

                    fields.forEach(field => {
                        field.closest('.col-md-6').style.display = isApprove ? 'block' : 'none';
                        field.required = isApprove;
                    });
                }

                actionSelect.addEventListener('change', toggleDosenFields);
                toggleDosenFields();
            }

            // SweetAlert for Delete Confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const formId = this.getAttribute('data-form-id');
                    const form = document.getElementById(formId);

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
