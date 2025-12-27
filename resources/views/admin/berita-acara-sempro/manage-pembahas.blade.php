{{-- filepath: resources/views/admin/berita-acara-sempro/manage-pembahas.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Kelola Pembahas - Berita Acara')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $pendaftaran->dosenPembimbing;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Kelola Pembahas
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-edit me-1"></i>
                    Ganti dosen pembahas jika diperlukan (Ketua Pembahas tidak dapat diganti)
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-warning alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bx bx-info-circle me-2"></i>Informasi Penting
            </h6>
            <ul class="mb-0">
                <li><strong class="text-primary">Ketua Pembahas (Pembimbing)</strong> bersifat <strong>TETAP</strong> dan
                    tidak dapat diganti</li>
                <li>Anda hanya dapat mengganti <strong>Anggota Pembahas 1, 2, dan 3</strong></li>
                <li><strong class="text-danger">PERHATIAN:</strong> Dosen yang sudah memberikan persetujuan (TTD) tidak
                    dapat diganti</li>
                <li>Dosen yang diganti akan kehilangan akses dan dosen baru akan mendapat notifikasi</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        {{-- Info Mahasiswa & Jadwal --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-info-circle me-2"></i>Informasi Ujian Seminar Proposal
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150" class="text-muted">Nama Mahasiswa</td>
                                <td>: <strong>{{ $mahasiswa->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td>: {{ $mahasiswa->nim }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pembimbing</td>
                                <td>: <strong>{{ $pembimbing->name }}</strong> (Ketua Pembahas)</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150" class="text-muted">Tanggal Ujian</td>
                                <td>: {{ $jadwal->tanggal_ujian->translatedFormat('l, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Waktu</td>
                                <td>: {{ $jadwal->waktu_mulai->format('H:i') }} -
                                    {{ $jadwal->waktu_selesai->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ruangan</td>
                                <td>: {{ $jadwal->ruangan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Kelola Pembahas --}}
        <form action="{{ route('admin.berita-acara-sempro.update-pembahas', $beritaAcara) }}" method="POST"
            id="formManagePembahas">
            @csrf

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-group me-2"></i>Susunan Dewan Pembahas
                    </h5>
                    <span class="badge bg-label-info">
                        Total: {{ $currentPenguji->count() }} Dosen
                    </span>
                </div>
                <div class="card-body">
                    {{-- ✅ INFO BOX: Ketua Pembahas (Read-Only) --}}
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-lock-alt fs-3 me-3 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-2">
                                    <i class="bx bx-star me-1"></i>Ketua Pembahas (Locked)
                                </h6>
                                <p class="mb-0">
                                    <strong>{{ $pembimbing->name }}</strong> sebagai <strong>Ketua Pembahas</strong>
                                    merupakan <strong>Dosen Pembimbing</strong> mahasiswa dan bersifat <strong
                                        class="text-primary">TETAP</strong>.
                                    <br>
                                    <small class="text-muted">Tidak dapat diganti melalui form ini.</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="25%">Posisi</th>
                                    <th width="50%">Nama Dosen</th>
                                    <th width="20%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rowNumber = 1;

                                    // ✅ PERBAIKI: Gunakan nilai DB yang sebenarnya (Ketua Penguji, bukan Ketua Pembahas)
                                    $ketuaPembahasData = $currentPenguji->firstWhere('pivot.posisi', 'Ketua Penguji');

                                    // ✅ Anggota sudah di-filter dari controller

                                @endphp

                                {{-- ✅ ROW 1: KETUA PEMBAHAS (Read-Only / Locked) --}}
                                @if ($ketuaPembahasData)
                                    <tr class="table-secondary">
                                        <td class="text-center fw-semibold">{{ $rowNumber++ }}</td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="bx bx-star me-1"></i>Ketua Pembahas
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-success rounded me-3">
                                                    <span class="avatar-initial fw-bold text-white">
                                                        {{ strtoupper(substr($ketuaPembahasData->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $ketuaPembahasData->name }}</div>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-id-card me-1"></i>NIP:
                                                        {{ $ketuaPembahasData->nip ?? '-' }}
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-envelope me-1"></i>{{ $ketuaPembahasData->email }}
                                                    </small>
                                                    <span class="badge bg-label-secondary badge-sm mt-2">
                                                        <i class="bx bx-lock-alt me-1"></i>Pembimbing - Tidak dapat diganti
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="bx bx-lock me-1"></i>Locked
                                            </span>
                                        </td>
                                    </tr>
                                @endif

                                {{-- ✅ ROWS 2-4: ANGGOTA PEMBAHAS (Editable jika belum TTD) --}}
                                @php
                                    $assignedAnggotaIds = $anggotaPenguji->pluck('id')->toArray();
                                    $excludedDosenIds = array_merge(
                                        $assignedAnggotaIds,
                                        [$pendaftaran->dosen_pembimbing_id],
                                        $ketuaPembahasData ? [$ketuaPembahasData->id] : [],
                                    );
                                @endphp

                                @forelse ($anggotaPenguji as $index => $dosen)
                                    @php
                                        $hasSigned = in_array($dosen->id, $signedDosenIds);
                                        $rowId = "pembahas_{$index}";

                                        $filteredDosen = $availableDosen->filter(function ($availDosen) use (
                                            $dosen,
                                            $excludedDosenIds,
                                        ) {
                                            if ($availDosen->id === $dosen->id) {
                                                return true;
                                            }
                                            return !in_array($availDosen->id, $excludedDosenIds);
                                        });

                                        // ✅ Label tampilan: Ganti "Penguji" → "Pembahas"
                                        $posisiDisplay = str_replace('Penguji', 'Pembahas', $dosen->pivot->posisi);
                                    @endphp

                                    <tr id="{{ $rowId }}" class="{{ $hasSigned ? 'table-success' : '' }}">
                                        <td class="text-center fw-semibold">{{ $rowNumber++ }}</td>

                                        {{-- Posisi (hidden input - TETAP kirim nilai DB asli) --}}
                                        <td>
                                            <input type="hidden" name="pembahas[{{ $index }}][posisi]"
                                                value="{{ $dosen->pivot->posisi }}">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-user me-1"></i>{{ $posisiDisplay }}
                                            </span>
                                        </td>

                                        {{-- Nama Dosen (Locked jika sudah TTD, Dropdown jika belum) --}}
                                        <td>
                                            @if ($hasSigned)
                                                {{-- ✅ SUDAH TTD - LOCKED --}}
                                                <input type="hidden" name="pembahas[{{ $index }}][dosen_id]"
                                                    value="{{ $dosen->id }}">
                                                <input type="hidden" name="pembahas[{{ $index }}][posisi]"
                                                    value="{{ $dosen->pivot->posisi }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-success rounded me-3">
                                                        <span class="avatar-initial fw-bold text-white">
                                                            {{ strtoupper(substr($dosen->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $dosen->name }}</div>
                                                        <small class="text-muted d-block">
                                                            <i class="bx bx-id-card me-1"></i>NIP:
                                                            {{ $dosen->nip ?? '-' }}
                                                        </small>
                                                        <small class="text-muted d-block">
                                                            <i class="bx bx-envelope me-1"></i>{{ $dosen->email }}
                                                        </small>
                                                        <span class="badge bg-success badge-sm mt-2">
                                                            <i class="bx bx-lock-alt me-1"></i>Sudah TTD - Tidak dapat
                                                            diganti
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- ✅ BELUM TTD - DAPAT DIGANTI --}}
                                                <input type="hidden" name="pembahas[{{ $index }}][posisi]"
                                                    value="{{ $dosen->pivot->posisi }}"> {{-- KIRIM NILAI ASLI DARI DB --}}

                                                <div>
                                                    <select name="pembahas[{{ $index }}][dosen_id]"
                                                        class="form-select select-dosen" data-index="{{ $index }}"
                                                        data-posisi="{{ str_replace('Penguji', 'Pembahas', $dosen->pivot->posisi) }}"
                                                        data-original-value="{{ $dosen->id }}"
                                                        data-original-name="{{ $dosen->name }}" required>
                                                        <option value="">-- Pilih Dosen --</option>
                                                        @php
                                                            // ✅ PERBAIKI: Filter dosen yang tersedia
                                                            $availableForThisPosition = $availableDosen->filter(
                                                                function ($availDosen) use ($excludedDosenIds, $dosen) {
                                                                    // Jika ini dosen saat ini, selalu tampilkan
                                                                    if ($availDosen->id === $dosen->id) {
                                                                        return true;
                                                                    }
                                                                    // Jika dosen sudah digunakan di posisi lain atau adalah pembimbing, jangan tampilkan
                                                                    return !in_array(
                                                                        $availDosen->id,
                                                                        $excludedDosenIds,
                                                                    );
                                                                },
                                                            );
                                                        @endphp

                                                        @foreach ($availableForThisPosition as $availDosen)
                                                            <option value="{{ $availDosen->id }}"
                                                                data-name="{{ $availDosen->name }}"
                                                                data-nip="{{ $availDosen->nip ?? '-' }}"
                                                                {{ $availDosen->id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $availDosen->name }} ({{ $availDosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @error("pembahas.{$index}.dosen_id")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror

                                                    {{-- Current info box --}}
                                                    <div class="mt-2 p-2 bg-light rounded"
                                                        id="current-info-{{ $index }}">
                                                        <small class="text-muted">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            <strong>Saat ini:</strong> {{ $dosen->name }}
                                                            <span class="text-primary">(NIP:
                                                                {{ $dosen->nip ?? '-' }})</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Status TTD --}}
                                        <td class="text-center">
                                            @if ($hasSigned)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bx bx-check-circle me-1"></i>Sudah TTD
                                                </span>
                                            @else
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="bx bx-time me-1"></i>Belum TTD
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bx bx-info-circle fs-2 mb-3 d-block"></i>
                                            <p class="mb-0">Tidak ada anggota pembahas yang ditugaskan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}"
                            class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formManagePembahas');
            const selectDosens = document.querySelectorAll('.select-dosen');
            const btnSubmit = document.getElementById('btnSubmit');

            // ✅ Store original values untuk detect changes
            selectDosens.forEach(select => {
                select.dataset.originalValue = select.value;
                select.dataset.originalName = select.options[select.selectedIndex].text;
            });

            // ✅ Update info box saat pilihan berubah
            selectDosens.forEach(select => {
                select.addEventListener('change', function() {
                    const index = this.dataset.index;
                    const infoBox = document.getElementById(`current-info-${index}`);
                    const selectedOption = this.options[this.selectedIndex];

                    if (infoBox && selectedOption.value) {
                        const dosenName = selectedOption.dataset.name;
                        const dosenNip = selectedOption.dataset.nip;

                        infoBox.innerHTML = `
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                <strong>Dipilih:</strong> ${dosenName}
                                <span class="text-primary">(NIP: ${dosenNip})</span>
                            </small>
                        `;
                        infoBox.classList.remove('bg-light');
                        infoBox.classList.add('bg-success', 'bg-opacity-10', 'border',
                            'border-success');
                    }
                });
            });

            // ✅ Validasi duplikasi dosen saat memilih
            selectDosens.forEach(select => {
                select.addEventListener('change', function() {
                    const selectedValues = Array.from(selectDosens)
                        .map(s => s.value)
                        .filter(v => v !== '');

                    const duplicates = selectedValues.filter((item, index) =>
                        selectedValues.indexOf(item) !== index
                    );

                    if (duplicates.length > 0) {
                        const dosenName = this.options[this.selectedIndex].dataset.name;
                        const posisi = this.dataset.posisi;

                        Swal.fire({
                            icon: 'error',
                            title: 'Dosen Duplikat!',
                            html: `
                                <p class="mb-0">
                                    <strong>${dosenName}</strong> sudah ditugaskan di posisi lain.
                                    <br><br>
                                    Silakan pilih dosen yang berbeda untuk <strong>${posisi}</strong>.
                                </p>
                            `,
                            confirmButtonColor: '#d33',
                            confirmButtonText: '<i class="bx bx-check me-1"></i> OK',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        });

                        this.value = this.dataset.originalValue || '';

                        const index = this.dataset.index;
                        const infoBox = document.getElementById(`current-info-${index}`);
                        if (infoBox) {
                            const originalNip = this.querySelector(
                                    `option[value="${this.dataset.originalValue}"]`)?.dataset.nip ||
                                '-';
                            infoBox.innerHTML = `
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    <strong>Saat ini:</strong> ${this.dataset.originalName}
                                    <span class="text-primary">(NIP: ${originalNip})</span>
                                </small>
                            `;
                            infoBox.classList.remove('bg-success', 'bg-opacity-10', 'border',
                                'border-success');
                            infoBox.classList.add('bg-light');
                        }
                    }
                });
            });

            // ✅ Submit confirmation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let hasChanges = false;
                let changes = [];

                selectDosens.forEach(select => {
                    const originalValue = select.dataset.originalValue;
                    const currentValue = select.value;

                    if (currentValue !== originalValue && currentValue !== '') {
                        hasChanges = true;
                        const posisi = select.dataset.posisi;
                        const oldDosenName = select.dataset.originalName;
                        const newDosenName = select.options[select.selectedIndex].dataset.name;
                        const newDosenNip = select.options[select.selectedIndex].dataset.nip;

                        changes.push({
                            posisi: posisi,
                            oldDosen: oldDosenName,
                            newDosen: newDosenName,
                            newNip: newDosenNip
                        });
                    }
                });

                if (!hasChanges) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Perubahan',
                        text: 'Anda belum melakukan perubahan pada daftar pembahas.',
                        confirmButtonColor: '#696cff',
                        confirmButtonText: '<i class="bx bx-check me-1"></i> OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                let changeList = '';
                changes.forEach((change, index) => {
                    changeList += `
                        <div class="mb-3 p-3 border rounded ${index % 2 === 0 ? 'bg-light' : ''}">
                            <div class="fw-bold text-primary mb-2">
                                <i class="bx bx-user me-1"></i>${change.posisi}
                            </div>
                            <div class="text-start">
                                <div class="mb-1">
                                    <small class="text-muted">Dosen Lama:</small>
                                    <div class="text-decoration-line-through text-danger">
                                        ${change.oldDosen}
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Dosen Baru:</small>
                                    <div class="fw-semibold text-success">
                                        ${change.newDosen}
                                        <small class="text-muted">(NIP: ${change.newNip})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                Swal.fire({
                    title: 'Konfirmasi Perubahan Pembahas',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">Anda akan mengganti <strong>${changes.length} dosen pembahas</strong> berikut:</p>
                            ${changeList}
                            <div class="alert alert-warning mb-0 mt-3">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-info-circle fs-4 me-2 mt-1"></i>
                                    <div>
                                        <strong>Perhatian:</strong>
                                        <ul class="mb-0 mt-2 small">
                                            <li>Dosen yang diganti akan <strong>kehilangan akses</strong> ke Berita Acara ini</li>
                                            <li>Dosen baru akan mendapat <strong>notifikasi email</strong></li>
                                            <li><strong>Ketua Pembahas (Pembimbing)</strong> tetap tidak berubah</li>
                                            <li>Perubahan bersifat <strong>permanen</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: '<i class="bx bx-check me-1"></i> Ya, Simpan Perubahan',
                    cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-outline-secondary',
                        popup: 'swal-wide'
                    },
                    buttonsStyling: false,
                    width: '700px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                        Swal.fire({
                            title: 'Menyimpan Perubahan...',
                            html: `
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mb-0">Mohon tunggu sebentar</p>
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                });
            });
        });
    </script>

    <style>
        .swal-wide {
            max-width: 700px !important;
        }

        .select-dosen {
            border: 2px solid #d9dee3;
            transition: all 0.3s ease;
        }

        .select-dosen:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
        }

        .select-dosen option {
            padding: 8px;
        }
    </style>
@endpush
