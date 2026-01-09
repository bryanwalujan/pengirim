{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/peminjaman-proyektor/proyektor-management.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Kelola Proyektor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.peminjaman-proyektor.index') }}">Peminjaman Proyektor</a>
                </li>
                <li class="breadcrumb-item active">Kelola Proyektor</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Peminjaman Proyektor /</span> Kelola Proyektor
            </h4>
            <a href="{{ route('admin.peminjaman-proyektor.index') }}" class="btn btn-label-secondary">
                <i class='bx bx-arrow-back me-1'></i> Kembali
            </a>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error me-2'></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error me-2'></i>
                <strong>Terjadi kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Form --}}
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header bg-label-primary">
                        <h5 class="card-title mb-0">
                            <i class='bx bx-edit me-2'></i>Kelola Daftar Proyektor
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.peminjaman-proyektor.update-proyektor-list') }}" method="POST"
                            id="proyektorForm">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info mb-4">
                                <i class='bx bx-info-circle me-2'></i>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0 mt-2 small">
                                    <li>Masukkan kode proyektor (contoh: PROY-001, LCD-A1)</li>
                                    <li>Kode hanya boleh berisi huruf, angka, dan tanda hubung (-)</li>
                                    <li>Klik "Tambah Proyektor" untuk menambah input baru</li>
                                    <li>Minimal harus ada 1 proyektor</li>
                                </ul>
                            </div>

                            <div id="proyektor-container">
                                @if (old('proyektor_codes'))
                                    @foreach (old('proyektor_codes') as $index => $code)
                                        <div class="proyektor-item mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class='bx bx-video'></i>
                                                </span>
                                                <input type="text" name="proyektor_codes[]"
                                                    class="form-control @error('proyektor_codes.' . $index) is-invalid @enderror"
                                                    placeholder="Contoh: PROY-001" value="{{ $code }}" required>
                                                @if ($loop->first)
                                                    <button type="button" class="btn btn-primary" onclick="addProyektor()">
                                                        <i class='bx bx-plus'></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="removeProyektor(this)">
                                                        <i class='bx bx-minus'></i>
                                                    </button>
                                                @endif
                                            </div>
                                            @error('proyektor_codes.' . $index)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                @else
                                    @forelse ($proyektorList as $index => $code)
                                        <div class="proyektor-item mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class='bx bx-video'></i>
                                                </span>
                                                <input type="text" name="proyektor_codes[]" class="form-control"
                                                    placeholder="Contoh: PROY-001" value="{{ $code }}" required>
                                                @if ($loop->first)
                                                    <button type="button" class="btn btn-primary" onclick="addProyektor()">
                                                        <i class='bx bx-plus'></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="removeProyektor(this)">
                                                        <i class='bx bx-minus'></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="proyektor-item mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class='bx bx-video'></i>
                                                </span>
                                                <input type="text" name="proyektor_codes[]" class="form-control"
                                                    placeholder="Contoh: PROY-001" required>
                                                <button type="button" class="btn btn-primary" onclick="addProyektor()">
                                                    <i class='bx bx-plus'></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforelse
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save me-1'></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-label-secondary" onclick="resetForm()">
                                    <i class='bx bx-reset me-1'></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: List --}}
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header bg-label-info d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class='bx bx-list-ul me-2'></i>Daftar Proyektor Terdaftar
                        </h5>
                        <span class="badge bg-primary">{{ count($proyektorList) }} Proyektor</span>
                    </div>
                    <div class="card-body">
                        @if (count($proyektorList) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="10%">No</th>
                                            <th>Kode Proyektor</th>
                                            <th>Total Peminjaman</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proyektorList as $index => $code)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar flex-shrink-0 me-3">
                                                            <span
                                                                class="avatar-initial rounded bg-label-{{ isset($stats[$code]) && is_array($stats[$code]) && $stats[$code]['sedang_dipinjam'] ? 'warning' : 'success' }}">
                                                                <i class='bx bx-video'></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $code }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-primary">
                                                        {{ isset($stats[$code]) && is_array($stats[$code]) ? $stats[$code]['total_peminjaman'] : 0 }}
                                                        kali
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (isset($stats[$code]) && is_array($stats[$code]) && $stats[$code]['sedang_dipinjam'])
                                                        <span class="badge bg-warning">
                                                            <i class='bx bx-time-five'></i> Sedang Dipinjam
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            <i class='bx bx-check-circle'></i> Tersedia
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class='bx bx-video bx-lg text-muted mb-3' style="font-size: 4rem;"></i>
                                <h5 class="text-muted">Belum Ada Proyektor Terdaftar</h5>
                                <p class="text-muted">Tambahkan proyektor menggunakan form di sebelah kiri</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class='bx bx-video'></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Proyektor</small>
                                        <h5 class="mb-0">{{ count($proyektorList) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class='bx bx-check-circle'></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Tersedia</small>
                                        <h5 class="mb-0 text-success">
                                            {{ collect($stats)->filter(function ($s) {
                                                    return is_array($s) && isset($s['sedang_dipinjam']) && !$s['sedang_dipinjam'];
                                                })->count() }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class='bx bx-time-five'></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Dipinjam</small>
                                        <h5 class="mb-0 text-warning">
                                            {{ collect($stats)->filter(function ($s) {
                                                    return is_array($s) && isset($s['sedang_dipinjam']) && $s['sedang_dipinjam'];
                                                })->count() }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .proyektor-item {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table td {
            vertical-align: middle;
        }

        .card-header.bg-label-primary {
            background-color: rgba(105, 108, 255, 0.12) !important;
        }

        .card-header.bg-label-info {
            background-color: rgba(3, 195, 236, 0.12) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Add new proyektor input
        function addProyektor() {
            const container = document.getElementById('proyektor-container');
            const newItem = document.createElement('div');
            newItem.className = 'proyektor-item mb-3';
            newItem.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text">
                        <i class='bx bx-video'></i>
                    </span>
                    <input type="text" name="proyektor_codes[]" class="form-control" 
                           placeholder="Contoh: PROY-001" required>
                    <button type="button" class="btn btn-danger" onclick="removeProyektor(this)">
                        <i class='bx bx-minus'></i>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
        }

        // Remove proyektor input
        function removeProyektor(button) {
            const items = document.querySelectorAll('.proyektor-item');
            if (items.length > 1) {
                button.closest('.proyektor-item').remove();
            } else {
                alert('Minimal harus ada 1 proyektor!');
            }
        }

        // Reset form
        function resetForm() {
            if (confirm('Yakin ingin mereset form? Semua perubahan yang belum disimpan akan hilang.')) {
                window.location.reload();
            }
        }

        // Auto dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-info)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Form validation
        document.getElementById('proyektorForm').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[name="proyektor_codes[]"]');
            const values = Array.from(inputs).map(input => input.value.trim().toUpperCase());

            // Check for duplicates
            const duplicates = values.filter((item, index) => values.indexOf(item) !== index);
            if (duplicates.length > 0) {
                e.preventDefault();
                alert('Terdapat kode proyektor yang duplikat: ' + duplicates.join(', '));
                return false;
            }

            // Check for empty values
            const hasEmpty = values.some(value => value === '');
            if (hasEmpty) {
                e.preventDefault();
                alert('Semua kode proyektor harus diisi!');
                return false;
            }

            // Confirm submission
            if (!confirm('Simpan perubahan daftar proyektor?')) {
                e.preventDefault();
                return false;
            }
        });

        // Auto uppercase input
        document.addEventListener('input', function(e) {
            if (e.target.name === 'proyektor_codes[]') {
                e.target.value = e.target.value.toUpperCase();
            }
        });
    </script>
@endpush
