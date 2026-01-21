@extends('layouts.admin.app')

@section('title', 'Tentukan Penguji Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}">Pendaftaran Ujian Hasil</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tentukan Penguji</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">Penentuan Tim Penguji</h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user-check me-1"></i>
                    Tentukan tim penguji untuk ujian hasil skripsi
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Context Information --}}
            <div class="col-xl-4 col-lg-5 mb-4">
                {{-- Student Profile Card --}}
                <div class="card mb-4 hover-shadow">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-user fs-1"></i>
                                </span>
                            </div>
                            <h5 class="mb-1">{{ $pendaftaranUjianHasil->user->name }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-label-primary">{{ $pendaftaranUjianHasil->user->nim }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <i class="bx bx-calendar-alt text-primary fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ $pendaftaranUjianHasil->angkatan }}</h6>
                                <small class="text-muted">Angkatan</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <i class="bx bx-medal text-success fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ number_format($pendaftaranUjianHasil->ipk, 2) }}</h6>
                                <small class="text-muted">IPK</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thesis Info Card --}}
                <div class="card mb-4 hover-shadow">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-book-open me-1"></i>Informasi Skripsi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Judul Skripsi</label>
                            <p class="mb-0 fw-medium">{{ $pendaftaranUjianHasil->judul_skripsi }}</p>
                        </div>
                    </div>
                </div>

                {{-- Supervisors Card --}}
                <div class="card mb-4 hover-shadow">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-group me-1"></i>Dosen Pembimbing
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="bx bx-user-pin me-1"></i>Pembimbing 1
                            </label>
                            <div class="d-flex align-items-center p-2 bg-lighter rounded">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($pendaftaranUjianHasil->dosenPembimbing1->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $pendaftaranUjianHasil->dosenPembimbing1->name }}</p>
                                    <small class="text-muted">{{ $pendaftaranUjianHasil->dosenPembimbing1->nip ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted small mb-1">
                                <i class="bx bx-user-pin me-1"></i>Pembimbing 2
                            </label>
                            <div class="d-flex align-items-center p-2 bg-lighter rounded">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">
                                        {{ strtoupper(substr($pendaftaranUjianHasil->dosenPembimbing2->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $pendaftaranUjianHasil->dosenPembimbing2->name }}</p>
                                    <small class="text-muted">{{ $pendaftaranUjianHasil->dosenPembimbing2->nip ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pembahas from BA Card --}}
                @if (count($pengujiFromBA) > 0)
                    <div class="card hover-shadow">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="card-title mb-0 text-success">
                                <i class="bx bx-check-circle me-1"></i>Referensi dari Berita Acara
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                <i class="bx bx-info-circle me-1"></i>
                                Dosen pembahas dari Seminar Proposal dapat dijadikan referensi penguji:
                            </p>
                            <div class="list-group list-group-flush">
                                @foreach ($pengujiFromBA as $index => $penguji)
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-success">
                                                    {{ $index + 1 }}
                                                </span>
                                            </div>
                                            <span class="fw-medium">{{ $penguji['name'] }}</span>
                                        </div>
                                        <span class="badge bg-success">Pembahas {{ $index + 1 }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Assignment Form --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card hover-shadow">
                    <div class="card-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i class="bx bx-user-check me-2"></i>Form Penentuan Penguji
                                </h5>
                                <p class="text-muted small mb-0">Pilih dosen untuk menjadi tim penguji ujian hasil</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pendaftaran-ujian-hasil.store-penguji', $pendaftaranUjianHasil) }}" method="POST" id="assignPengujiForm">
                            @csrf

                            {{-- Info Alert --}}
                            <div class="alert alert-info bg-info bg-opacity-10 border-0 mb-4">
                                <div class="d-flex">
                                    <i class="bx bx-info-circle fs-4 me-2 text-info"></i>
                                    <div>
                                        <strong class="text-info">Ketentuan Penguji:</strong>
                                        <ul class="mb-0 mt-2 ps-3">
                                            <li>Penguji 1, 2, dan 3 <strong>wajib</strong> diisi</li>
                                            <li>Penguji Tambahan bersifat <strong>opsional</strong></li>
                                            <li>Setiap penguji harus <strong>berbeda</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Penguji Selection Grid --}}
                            <div class="row g-4">
                                {{-- Penguji 1 --}}
                                <div class="col-12">
                                    <div class="penguji-card" data-penguji="1">
                                        <div class="penguji-header">
                                            <div class="d-flex align-items-center">
                                                <div class="penguji-number">1</div>
                                                <div>
                                                    <h6 class="mb-0">Penguji 1</h6>
                                                    <small class="text-muted">Ketua Tim Penguji</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-danger">Wajib</span>
                                        </div>
                                        <div class="penguji-body">
                                            <select class="form-select penguji-select @error('penguji_1_id') is-invalid @enderror" 
                                                    id="penguji_1_id" name="penguji_1_id" required>
                                                <option value="">-- Pilih Penguji 1 --</option>
                                                @foreach ($availableDosen as $dosen)
                                                    <option value="{{ $dosen->id }}" 
                                                        data-name="{{ $dosen->name }}"
                                                        data-nip="{{ $dosen->nip ?? '-' }}"
                                                        {{ old('penguji_1_id', $currentPenguji['Penguji 1'] ?? (isset($pengujiFromBA[0]) ? $pengujiFromBA[0]['id'] : '')) == $dosen->id ? 'selected' : '' }}>
                                                        {{ $dosen->name }} {{ $dosen->nip ? '(' . $dosen->nip . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('penguji_1_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="selected-info mt-2" style="display: none;">
                                                <div class="d-flex align-items-center p-2 bg-primary bg-opacity-10 rounded">
                                                    <i class="bx bx-check-circle text-primary me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Terpilih:</small>
                                                        <span class="fw-semibold selected-name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Penguji 2 --}}
                                <div class="col-12">
                                    <div class="penguji-card" data-penguji="2">
                                        <div class="penguji-header">
                                            <div class="d-flex align-items-center">
                                                <div class="penguji-number">2</div>
                                                <div>
                                                    <h6 class="mb-0">Penguji 2</h6>
                                                    <small class="text-muted">Anggota Tim Penguji</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-danger">Wajib</span>
                                        </div>
                                        <div class="penguji-body">
                                            <select class="form-select penguji-select @error('penguji_2_id') is-invalid @enderror" 
                                                    id="penguji_2_id" name="penguji_2_id" required>
                                                <option value="">-- Pilih Penguji 2 --</option>
                                                @foreach ($availableDosen as $dosen)
                                                    <option value="{{ $dosen->id }}"
                                                        data-name="{{ $dosen->name }}"
                                                        data-nip="{{ $dosen->nip ?? '-' }}"
                                                        {{ old('penguji_2_id', $currentPenguji['Penguji 2'] ?? (isset($pengujiFromBA[1]) ? $pengujiFromBA[1]['id'] : '')) == $dosen->id ? 'selected' : '' }}>
                                                        {{ $dosen->name }} {{ $dosen->nip ? '(' . $dosen->nip . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('penguji_2_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="selected-info mt-2" style="display: none;">
                                                <div class="d-flex align-items-center p-2 bg-primary bg-opacity-10 rounded">
                                                    <i class="bx bx-check-circle text-primary me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Terpilih:</small>
                                                        <span class="fw-semibold selected-name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Penguji 3 --}}
                                <div class="col-12">
                                    <div class="penguji-card" data-penguji="3">
                                        <div class="penguji-header">
                                            <div class="d-flex align-items-center">
                                                <div class="penguji-number">3</div>
                                                <div>
                                                    <h6 class="mb-0">Penguji 3</h6>
                                                    <small class="text-muted">Anggota Tim Penguji</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-danger">Wajib</span>
                                        </div>
                                        <div class="penguji-body">
                                            <select class="form-select penguji-select @error('penguji_3_id') is-invalid @enderror" 
                                                    id="penguji_3_id" name="penguji_3_id" required>
                                                <option value="">-- Pilih Penguji 3 --</option>
                                                @foreach ($availableDosen as $dosen)
                                                    <option value="{{ $dosen->id }}"
                                                        data-name="{{ $dosen->name }}"
                                                        data-nip="{{ $dosen->nip ?? '-' }}"
                                                        {{ old('penguji_3_id', $currentPenguji['Penguji 3'] ?? (isset($pengujiFromBA[2]) ? $pengujiFromBA[2]['id'] : '')) == $dosen->id ? 'selected' : '' }}>
                                                        {{ $dosen->name }} {{ $dosen->nip ? '(' . $dosen->nip . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('penguji_3_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="selected-info mt-2" style="display: none;">
                                                <div class="d-flex align-items-center p-2 bg-primary bg-opacity-10 rounded">
                                                    <i class="bx bx-check-circle text-primary me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Terpilih:</small>
                                                        <span class="fw-semibold selected-name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Divider --}}
                                <div class="col-12">
                                    <div class="divider">
                                        <div class="divider-text">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Penguji Tambahan --}}
                                <div class="col-12">
                                    <div class="penguji-card optional" data-penguji="tambahan">
                                        <div class="penguji-header">
                                            <div class="d-flex align-items-center">
                                                <div class="penguji-number optional">+</div>
                                                <div>
                                                    <h6 class="mb-0">Penguji Tambahan</h6>
                                                    <small class="text-muted">Opsional - Jika diperlukan</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-secondary">Opsional</span>
                                        </div>
                                        <div class="penguji-body">
                                            <select class="form-select penguji-select @error('penguji_tambahan_id') is-invalid @enderror" 
                                                    id="penguji_tambahan_id" name="penguji_tambahan_id">
                                                <option value="">-- Tidak Ada Penguji Tambahan --</option>
                                                @foreach ($availableDosen as $dosen)
                                                    <option value="{{ $dosen->id }}"
                                                        data-name="{{ $dosen->name }}"
                                                        data-nip="{{ $dosen->nip ?? '-' }}"
                                                        {{ old('penguji_tambahan_id', $currentPenguji['Penguji Tambahan'] ?? '') == $dosen->id ? 'selected' : '' }}>
                                                        {{ $dosen->name }} {{ $dosen->nip ? '(' . $dosen->nip . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('penguji_tambahan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="selected-info mt-2" style="display: none;">
                                                <div class="d-flex align-items-center p-2 bg-secondary bg-opacity-10 rounded">
                                                    <i class="bx bx-check-circle text-secondary me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Terpilih:</small>
                                                        <span class="fw-semibold selected-name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                                <a href="{{ route('admin.pendaftaran-ujian-hasil.show', $pendaftaranUjianHasil) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bx bx-check me-1"></i> Simpan Tim Penguji
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Statistics Table Card --}}
                <div class="card mt-4 hover-shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-list-ul me-1"></i>Daftar Beban Dosen Penguji
                        </h5>
                        <button type="button" class="btn btn-sm btn-label-primary" id="btnRefreshStats">
                            <i class="bx bx-refresh"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tableStatistics">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="60%">Nama Dosen</th>
                                    <th width="20%" class="text-center">Total Beban</th>
                                    <th width="15%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengujiStatistics as $stat)
                                    @php
                                        $maxBeban = collect($pengujiStatistics)->max('total_beban');
                                        $minBeban = collect($pengujiStatistics)->min('total_beban');
                                        $totalBeban = $stat['total_beban'];

                                        if ($totalBeban == 0) {
                                            $badgeClass = 'bg-label-secondary';
                                            $statusText = 'Kosong';
                                            $iconClass = 'bx-minus-circle';
                                        } elseif ($totalBeban == $minBeban) {
                                            $badgeClass = 'bg-label-success';
                                            $statusText = 'Rendah';
                                            $iconClass = 'bx-chevron-down';
                                        } elseif ($totalBeban == $maxBeban) {
                                            $badgeClass = 'bg-label-danger';
                                            $statusText = 'Tinggi';
                                            $iconClass = 'bx-chevron-up';
                                        } else {
                                            $badgeClass = 'bg-label-warning';
                                            $statusText = 'Sedang';
                                            $iconClass = 'bx-chevron-right';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-dark">
                                                        {{ strtoupper(substr($stat['dosen']->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $stat['dosen']->name }}</h6>
                                                    <small class="text-muted">{{ $stat['dosen']->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <h5 class="mb-0">
                                                <span class="badge bg-label-dark">{{ $totalBeban }}</span>
                                            </h5>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }}">
                                                <i class="bx {{ $iconClass }} me-1"></i>{{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.penguji-select');
            
            // Handle select change events
            selects.forEach(select => {
                // Show initial selection if exists
                updateSelectedInfo(select);
                
                select.addEventListener('change', function() {
                    // Update visual feedback
                    updateSelectedInfo(this);
                    
                    // Validate duplicates
                    validateDuplicates();
                });
            });

            function updateSelectedInfo(select) {
                const card = select.closest('.penguji-card');
                const selectedInfo = card.querySelector('.selected-info');
                const selectedName = card.querySelector('.selected-name');
                
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const name = selectedOption.getAttribute('data-name');
                    const nip = selectedOption.getAttribute('data-nip');
                    
                    selectedName.textContent = name + (nip !== '-' ? ' (' + nip + ')' : '');
                    selectedInfo.style.display = 'block';
                    
                    // Add success animation
                    card.classList.add('selected');
                    setTimeout(() => card.classList.remove('selected'), 300);
                } else {
                    selectedInfo.style.display = 'none';
                }
            }

            function validateDuplicates() {
                const selectedValues = [];
                const selectsArray = Array.from(selects);
                
                selectsArray.forEach(s => {
                    if (s.value) selectedValues.push(s.value);
                });
                
                // Check for duplicates
                const hasDuplicates = selectedValues.length !== new Set(selectedValues).size;
                
                if (hasDuplicates) {
                    // Show error notification
                    Swal.fire({
                        icon: 'error',
                        title: 'Penguji Duplikat!',
                        text: 'Setiap penguji harus berbeda. Silakan pilih dosen yang berbeda.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#696cff'
                    });
                    
                    // Find and reset the duplicate
                    const lastChanged = event.target;
                    if (lastChanged) {
                        lastChanged.value = '';
                        updateSelectedInfo(lastChanged);
                    }
                }
            }

            // Form submission validation
            const form = document.getElementById('assignPengujiForm');
            form.addEventListener('submit', function(e) {
                const penguji1 = document.getElementById('penguji_1_id').value;
                const penguji2 = document.getElementById('penguji_2_id').value;
                const penguji3 = document.getElementById('penguji_3_id').value;
                
                if (!penguji1 || !penguji2 || !penguji3) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Penguji 1, 2, dan 3 wajib diisi!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#696cff'
                    });
                    return false;
                }
            });

            // Auto-dismiss alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // DataTable initialization for statistics table
            if (typeof $.fn.DataTable !== 'undefined' && $('#tableStatistics').length) {
                $('#tableStatistics').DataTable({
                    "order": [[2, "asc"]], // Sort by beban ascending (lowest first)
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                    },
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 0 // No column
                    }]
                });
            }

            // Refresh stats button
            $('#btnRefreshStats').on('click', function() {
                const $btn = $(this);
                $btn.html('<span class="spinner-border spinner-border-sm"></span>');
                setTimeout(() => location.reload(), 500);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Card Hover Effects */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.16);
            transform: translateY(-2px);
        }

        /* Penguji Card Styles */
        .penguji-card {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1.25rem;
            background: #fff;
            transition: all 0.3s ease;
        }

        .penguji-card:hover {
            border-color: #696cff;
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.1);
        }

        .penguji-card.selected {
            border-color: #71dd37;
            background: rgba(113, 221, 55, 0.05);
        }

        .penguji-card.optional {
            border-style: dashed;
            background: #f8f9fa;
        }

        .penguji-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .penguji-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #696cff 0%, #9395ff 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.125rem;
            margin-right: 0.75rem;
            box-shadow: 0 2px 6px rgba(105, 108, 255, 0.3);
        }

        .penguji-number.optional {
            background: linear-gradient(135deg, #8592a3 0%, #a8b1bb 100%);
            box-shadow: 0 2px 6px rgba(133, 146, 163, 0.3);
        }

        .penguji-body {
            padding-top: 0.5rem;
        }

        .penguji-select {
            border: 2px solid #e9ecef;
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .penguji-select:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
        }

        .selected-info {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Divider Styles */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px dashed #d9dee3;
        }

        .divider-text {
            padding: 0 1rem;
            color: #8592a3;
            font-size: 1.25rem;
        }

        /* Background Lighter */
        .bg-lighter {
            background-color: rgba(67, 89, 113, 0.04);
        }

        /* Avatar Styles */
        .avatar-xl {
            width: 5rem;
            height: 5rem;
        }

        .avatar-xl .avatar-initial {
            font-size: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .penguji-card {
                padding: 1rem;
            }

            .penguji-number {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .penguji-header h6 {
                font-size: 0.9rem;
            }
        }
    </style>
@endpush
