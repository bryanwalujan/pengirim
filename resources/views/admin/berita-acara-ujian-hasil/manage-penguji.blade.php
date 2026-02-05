@extends('layouts.admin.app')

@section('title', 'Kelola Penguji - Berita Acara Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $mahasiswa = $pendaftaran->user;
            $pembimbing1 = $pendaftaran->dosenPembimbing1;
            $pembimbing2 = $pendaftaran->dosenPembimbing2;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Kelola Penguji
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-edit me-1"></i>
                    Ganti dosen penguji jika diperlukan (Ketua Penguji dan Pembimbing tidak dapat diganti)
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-warning alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bx bx-info-circle me-2"></i>Informasi Penting
            </h6>
            <ul class="mb-0">
                <li><strong class="text-primary">Ketua Penguji</strong> dan <strong class="text-primary">Pembimbing (PS1/PS2)</strong> bersifat <strong>TETAP</strong> dan tidak dapat diganti pada halaman ini.</li>
                <li>Anda hanya dapat mengganti <strong>Anggota Penguji (Penguji 1, Penguji 2, dst)</strong>.</li>
                <li><strong class="text-danger">PERHATIAN:</strong> Dosen yang sudah memberikan persetujuan (TTD) tidak dapat diganti.</li>
                <li>Dosen yang diganti akan kehilangan akses dan dosen baru akan mendapat notifikasi.</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        {{-- Info Mahasiswa & Jadwal --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-info-circle me-2"></i>Informasi Ujian Hasil
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
                                <td class="text-muted">Pembimbing 1</td>
                                <td>: <strong>{{ $pembimbing1->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pembimbing 2</td>
                                <td>: <strong>{{ $pembimbing2->name ?? '-' }}</strong></td>
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

        {{-- Form Kelola Penguji --}}
        <form action="{{ route('admin.berita-acara-ujian-hasil.update-penguji', $beritaAcara) }}" method="POST"
            id="formManagePenguji">
            @csrf

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-group me-2"></i>Susunan Dewan Penguji
                    </h5>
                    <span class="badge bg-label-info">
                        Total: {{ $currentPenguji->count() }} Dosen
                    </span>
                </div>
                <div class="card-body">
                    {{-- ✅ INFO BOX --}}
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-lock-alt fs-3 me-3 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-2">
                                    <i class="bx bx-star me-1"></i>Ketua Penguji & Pembimbing (Locked)
                                </h6>
                                <p class="mb-0">
                                    Posisi <strong>Ketua Penguji</strong> dan <strong>Pembimbing Skripsi (PS1/PS2)</strong> ditetapkan saat penjadwalan dan bersifat <strong class="text-primary">TETAP</strong> di halaman ini.
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
                                @endphp

                                {{-- ✅ ROW 1: KETUA PENGUJI (Read-Only / Locked) --}}
                                @if ($ketuaPengujiData)
                                    <tr class="table-secondary">
                                        <td class="text-center fw-semibold">{{ $rowNumber++ }}</td>
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="bx bx-star me-1"></i>Ketua Penguji
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-dark rounded me-3">
                                                    <span class="avatar-initial fw-bold text-white">
                                                        {{ strtoupper(substr($ketuaPengujiData->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $ketuaPengujiData->name }}</div>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-id-card me-1"></i>NIP:
                                                        {{ $ketuaPengujiData->nip ?? '-' }}
                                                    </small>
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

                                {{-- ✅ ROWS 2+: ANGGOTA PENGUJI (Editable kecuali PS1/PS2 atau sudah TTD) --}}
                                @php
                                    $assignedIds = $currentPenguji->pluck('id')->toArray();
                                @endphp

                                @forelse ($anggotaPenguji as $index => $dosen)
                                    @php
                                        $hasSigned = in_array($dosen->id, $signedDosenIds);
                                        $rowId = "penguji_{$index}";
                                        $posisi = $dosen->pivot->posisi;
                                        
                                        // Check logic: Is PS1 or PS2?
                                        $isPembimbing = str_contains($posisi, '(PS1)') || str_contains($posisi, '(PS2)');
                                        $isLocked = $hasSigned || $isPembimbing;

                                        // Filter available dosen
                                        $filteredDosen = $availableDosen->filter(function ($availDosen) use ($dosen, $assignedIds) {
                                            if ($availDosen->id === $dosen->id) return true;
                                            return !in_array($availDosen->id, $assignedIds);
                                        });
                                    @endphp

                                    <tr id="{{ $rowId }}" class="{{ $hasSigned ? 'table-success' : ($isPembimbing ? 'table-secondary' : '') }}">
                                        <td class="text-center fw-semibold">{{ $rowNumber++ }}</td>

                                        {{-- Posisi --}}
                                        <td>
                                            <input type="hidden" name="penguji[{{ $index }}][posisi]" value="{{ $posisi }}">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-user me-1"></i>{{ $posisi }}
                                            </span>
                                        </td>

                                        {{-- Nama Dosen --}}
                                        <td>
                                            @if ($isLocked)
                                                {{-- ✅ LOCKED --}}
                                                <input type="hidden" name="penguji[{{ $index }}][dosen_id]" value="{{ $dosen->id }}">
                                                
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial {{ $isPembimbing ? 'bg-secondary' : 'bg-success' }} rounded fw-bold text-white">
                                                            {{ strtoupper(substr($dosen->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $dosen->name }}</div>
                                                        <small class="text-muted d-block">
                                                            <i class="bx bx-id-card me-1"></i>NIP:
                                                            {{ $dosen->nip ?? '-' }}
                                                        </small>
                                                        
                                                        @if($hasSigned)
                                                            <span class="badge bg-success badge-sm mt-2">
                                                                <i class="bx bx-check-double me-1"></i>Sudah TTD
                                                            </span>
                                                        @elseif($isPembimbing)
                                                            <span class="badge bg-secondary badge-sm mt-2">
                                                                <i class="bx bx-lock-alt me-1"></i>Pembimbing (Tetap)
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                {{-- ✅ EDITABLE --}}
                                                <div>
                                                    <select name="penguji[{{ $index }}][dosen_id]"
                                                        class="form-select select-dosen" data-index="{{ $index }}"
                                                        data-posisi="{{ $posisi }}"
                                                        data-original-value="{{ $dosen->id }}"
                                                        data-original-name="{{ $dosen->name }}" required>
                                                        <option value="">-- Pilih Dosen --</option>
                                                        @foreach ($filteredDosen as $availDosen)
                                                            <option value="{{ $availDosen->id }}"
                                                                data-name="{{ $availDosen->name }}"
                                                                data-nip="{{ $availDosen->nip ?? '-' }}"
                                                                {{ $availDosen->id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $availDosen->name }} ({{ $availDosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    {{-- Current info box --}}
                                                    <div class="mt-2 p-2 bg-light rounded" id="current-info-{{ $index }}">
                                                        <small class="text-muted">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            <strong>Saat ini:</strong> {{ $dosen->name }}
                                                            <span class="text-primary">(NIP: {{ $dosen->nip ?? '-' }})</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="text-center">
                                            @if ($isLocked)
                                                <span class="badge bg-secondary px-3 py-2">
                                                    <i class="bx bx-lock me-1"></i>Locked
                                                </span>
                                            @else
                                                <span class="badge bg-label-primary px-3 py-2">
                                                    <i class="bx bx-edit-alt me-1"></i>Dapat Diganti
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bx bx-info-circle fs-2 mb-3 d-block"></i>
                                            <p class="mb-0">Tidak ada anggota penguji yang ditugaskan</p>
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
                        <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}"
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
            const form = document.getElementById('formManagePenguji');
            const selectDosens = document.querySelectorAll('.select-dosen');
            const btnSubmit = document.getElementById('btnSubmit');

            // ✅ Store original values
            selectDosens.forEach(select => {
                select.dataset.originalValue = select.value;
                select.dataset.originalName = select.options[select.selectedIndex].text;
            });

            // ✅ Update info box
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
                        infoBox.classList.add('bg-success', 'bg-opacity-10', 'border', 'border-success');
                    }
                });
            });

            // ✅ Duplicate Validation
            selectDosens.forEach(select => {
                select.addEventListener('change', function() {
                    const selectedValues = Array.from(selectDosens)
                        .map(s => s.value)
                        .filter(v => v !== '');
                    
                    // Note: We should technically also check against LOCKED IDs (like Ketua, PS1, PS2)
                    // but they are not in the selectValues array because they are hidden inputs.
                    // For now, checking against other selects is good.

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
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });

                        this.value = this.dataset.originalValue || '';
                        // Reset info box...
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
                        text: 'Anda belum melakukan perubahan pada daftar penguji.',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' }
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
                    title: 'Konfirmasi Perubahan Penguji',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">Anda akan mengganti <strong>${changes.length} dosen penguji</strong>:</p>
                            ${changeList}
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Perubahan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <style>
        .select-dosen {
            border: 2px solid #d9dee3;
            transition: all 0.3s ease;
        }
        .select-dosen:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
        }
    </style>
@endpush
