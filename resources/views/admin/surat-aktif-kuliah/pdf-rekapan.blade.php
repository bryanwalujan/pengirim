@extends('layouts.admin.app')

@section('title', 'Rekapan PDF Surat Aktif Kuliah')

@push('styles')
    <style>
        .statistics-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .filter-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .file-size {
            font-family: monospace;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-bulk-action {
            margin-bottom: 1rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-file-blank me-2"></i>
                Rekapan PDF Surat Aktif Kuliah
            </h4>
        </div>

        <!-- Statistics Cards -->
        <div class="statistics-card">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($statistics['total_files']) }}</div>
                        <div class="stat-label">Total File PDF</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($statistics['total_size'] / 1024 / 1024, 1) }}MB</div>
                        <div class="stat-label">Total Ukuran</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value">{{ $statistics['status_counts']['sudah_diambil'] ?? 0 }}</div>
                        <div class="stat-label">Sudah Diambil</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-value">{{ $statistics['status_counts']['siap_diambil'] ?? 0 }}</div>
                        <div class="stat-label">Siap Diambil</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="filter-card">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="form-select">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahunAjaranOptions as $tahun)
                                <option value="{{ $tahun }}" {{ $tahun_ajaran === $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">Semua</option>
                            <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" class="form-control" value="{{ $tanggal_dari }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" class="form-control" value="{{ $tanggal_sampai }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="NIM, Nama, atau Nomor Surat"
                            value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('admin.surat-aktif-kuliah.pdf-rekapan') }}"
                            class="btn btn-outline-secondary w-100">
                            <i class="bx bx-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-bulk-action" id="downloadSelected">
                    <i class="bx bx-download"></i> Download Terpilih
                </button>
                <button type="button" class="btn btn-warning btn-bulk-action" onclick="selectAll()">
                    <i class="bx bx-check-square"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-outline-secondary btn-bulk-action" onclick="deselectAll()">
                    <i class="bx bx-square"></i> Batal Pilih
                </button>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-cog"></i> Kelola PDF
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                            <i class="bx bx-trash me-2"></i>Cleanup File Lama
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="refreshStatistics()">
                            <i class="bx bx-refresh me-2"></i>Refresh Statistik
                        </a></li>
                </ul>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                            </th>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Nomor Surat</th>
                            <th>Tahun/Semester</th>
                            <th>Status</th>
                            <th>File Info</th>
                            <th>Tanggal</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $index => $surat)
                            <tr id="row-{{ $surat->id }}">
                                <td>
                                    <input type="checkbox" class="select-item" value="{{ $surat->id }}">
                                </td>
                                <td>{{ ($surats->currentPage() - 1) * $surats->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $surat->mahasiswa->name }}</strong><br>
                                        <small class="text-muted">{{ $surat->mahasiswa->nim }}</small>
                                    </div>
                                </td>
                                <td>
                                    {{ $surat->nomor_surat ?? '-' }}<br>
                                    <small class="text-muted">
                                        {{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d/m/Y') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $surat->tahun_ajaran }}<br>
                                    <small class="text-muted">{{ ucfirst($surat->semester) }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($surat->status->status ?? 'unknown') {
                                            'diajukan' => 'warning',
                                            'diproses' => 'info',
                                            'disetujui_kaprodi' => 'primary',
                                            'disetujui_pimpinan' => 'primary',
                                            'disetujui' => 'success',
                                            'siap_diambil' => 'secondary',
                                            'sudah_diambil' => 'success',
                                            default => 'light',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }} status-badge">
                                        {{ str_replace('_', ' ', ucwords($surat->status->status ?? 'unknown')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="file-info-cell" data-surat-id="{{ $surat->id }}">
                                        @if ($surat->file_surat_path && Storage::disk('public')->exists($surat->file_surat_path))
                                            <span class="text-success">✓ File OK</span><br>
                                            <span
                                                class="file-size">{{ number_format(Storage::disk('public')->size($surat->file_surat_path) / 1024, 1) }}
                                                KB</span>
                                        @else
                                            <span class="text-danger">✗ File Hilang</span><br>
                                            <small class="text-muted">-</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small>
                                        Dibuat: {{ $surat->created_at->format('d/m/Y H:i') }}<br>
                                        Update: {{ $surat->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if ($surat->file_surat_path && Storage::disk('public')->exists($surat->file_surat_path))
                                            <a href="{{ route('admin.surat-aktif-kuliah.download', $surat->id) }}"
                                                class="btn btn-sm btn-success" title="Download" target="_blank">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            <a href="{{ Storage::url($surat->file_surat_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary" title="Preview">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-sm btn-info" onclick="showFileInfo({{ $surat->id }})"
                                            title="Info">
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="regeneratePdf({{ $surat->id }})" title="Regenerate">
                                            <i class="bx bx-refresh"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bx bx-search" style="font-size: 3rem; color: #ccc;"></i><br>
                                    <strong>Tidak ada data ditemukan</strong><br>
                                    <small class="text-muted">Coba ubah filter pencarian Anda</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($surats->hasPages())
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="text-muted">
                        Menampilkan {{ $surats->firstItem() }}-{{ $surats->lastItem() }} dari {{ $surats->total() }} data
                    </div>
                    {{ $surats->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Cleanup Modal -->
    <div class="modal fade" id="cleanupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cleanup File PDF Lama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.surat-aktif-kuliah.cleanup-pdfs') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hapus file yang lebih lama dari (hari):</label>
                            <input type="number" name="older_than_days" class="form-control" value="90"
                                min="30">
                            <small class="text-muted">Minimal 30 hari untuk keamanan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kecualikan status:</label>
                            <div class="form-check">
                                <input type="checkbox" name="exclude_status[]" value="sudah_diambil"
                                    class="form-check-input" checked>
                                <label class="form-check-label">Sudah Diambil</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="exclude_status[]" value="siap_diambil"
                                    class="form-check-input" checked>
                                <label class="form-check-label">Siap Diambil</label>
                            </div>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bx bx-error-circle me-2"></i>
                            <strong>Peringatan:</strong> Aksi ini tidak dapat dibatalkan. Pastikan Anda sudah membuat backup
                            terlebih dahulu.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus File Lama</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Info Modal -->
    <div class="modal fade" id="fileInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi File PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="fileInfoContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Select All Functionality
        function selectAll() {
            document.querySelectorAll('.select-item').forEach(item => item.checked = true);
            updateSelectAllCheckbox();
        }

        function deselectAll() {
            document.querySelectorAll('.select-item').forEach(item => item.checked = false);
            updateSelectAllCheckbox();
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            document.querySelectorAll('.select-item').forEach(item => {
                item.checked = selectAllCheckbox.checked;
            });
        }

        function updateSelectAllCheckbox() {
            const checkboxes = document.querySelectorAll('.select-item');
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            selectAllCheckbox.checked = checkedCount === checkboxes.length;
        }

        // Listen for individual checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('select-item')) {
                updateSelectAllCheckbox();
            }
        });

        // Download Selected
        document.getElementById('downloadSelected').addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);

            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu file untuk diunduh', 'warning');
                return;
            }

            if (selectedIds.length > 50) {
                Swal.fire('Peringatan', 'Maksimal 50 file dapat diunduh sekaligus', 'warning');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.surat-aktif-kuliah.download-multiple') }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add selected IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            Swal.fire({
                title: 'Memproses...',
                text: `Sedang mempersiapkan ${selectedIds.length} file untuk diunduh`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Close loading after 3 seconds
            setTimeout(() => {
                Swal.close();
            }, 3000);
        });

        // Show File Info
        function showFileInfo(suratId) {
            fetch(`{{ url('admin/surat-aktif-kuliah/pdf-info') }}/${suratId}`)
                .then(response => response.json())
                .then(data => {
                    const content = `
                <div class="row">
                    <div class="col-sm-4"><strong>ID Surat:</strong></div>
                    <div class="col-sm-8">${data.id}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Mahasiswa:</strong></div>
                    <div class="col-sm-8">${data.mahasiswa} (${data.nim})</div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Nomor Surat:</strong></div>
                    <div class="col-sm-8">${data.nomor_surat || '-'}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Status:</strong></div>
                    <div class="col-sm-8"><span class="badge bg-info">${data.status}</span></div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>File Tersedia:</strong></div>
                    <div class="col-sm-8">
                        ${data.file_exists ? 
                            '<span class="text-success">✓ Ya</span>' : 
                            '<span class="text-danger">✗ Tidak</span>'
                        }
                    </div>
                </div>
                ${data.file_exists ? `
                                <div class="row mt-2">
                                    <div class="col-sm-4"><strong>Ukuran File:</strong></div>
                                    <div class="col-sm-8">${data.file_size_formatted}</div>
                                </div>
                            ` : ''}
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Path File:</strong></div>
                    <div class="col-sm-8"><code>${data.file_path || 'Tidak ada'}</code></div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Dibuat:</strong></div>
                    <div class="col-sm-8">${new Date(data.created_at).toLocaleString('id-ID')}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-4"><strong>Diupdate:</strong></div>
                    <div class="col-sm-8">${new Date(data.updated_at).toLocaleString('id-ID')}</div>
                </div>
            `;

                    document.getElementById('fileInfoContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('fileInfoModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat informasi file', 'error');
                });
        }

        // Regenerate PDF
        function regeneratePdf(suratId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin generate ulang PDF? File lama akan digantikan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Generate',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang generate ulang PDF',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('admin/surat-aktif-kuliah/regenerate-pdf') }}/${suratId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire('Berhasil', data.message, 'success');
                                // Refresh file info in the row
                                location.reload();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            console.error('Error:', error);
                            Swal.fire('Error', 'Terjadi kesalahan saat generate PDF', 'error');
                        });
                }
            });
        }

        // Refresh Statistics
        function refreshStatistics() {
            location.reload();
        }

        // Auto-submit filter form on change
        document.querySelectorAll('#filterForm select').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
@endpush
