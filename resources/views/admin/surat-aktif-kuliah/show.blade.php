@extends('layouts.admin.app')

@section('title', 'Detail Surat Aktif Kuliah')

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
                        'disetujui', 'siap_diambil', 'sudah_diambil' => 'success',
                        'ditolak' => 'danger',
                        default => 'warning',
                    };
                @endphp
                <div class="alert alert-{{ $alertClass }} mb-4">
                    <h6 class="alert-heading mb-1">Status:
                        <strong>{{ str_replace('_', ' ', ucfirst($surat->status ?? 'Diajukan')) }}</strong>
                    </h6>
                    @if ($surat->status()->first()?->catatan_admin)
                        <p class="mb-0"><strong>Catatan Admin:</strong> {{ $surat->status()->first()->catatan_admin }}</p>
                    @endif
                </div>

                <!-- Informasi Surat -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Informasi Surat</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control" value="{{ $surat->nomor_surat ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '-' }}" readonly>
                        </div>
                        @if ($surat->penandatangan)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penandatangan</label>
                                <input type="text" class="form-control" value="{{ $surat->penandatangan->name }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" value="{{ $surat->jabatan_penandatangan }}"
                                    readonly>
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

                <!-- File Surat -->
                @if ($surat->file_surat_path)
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">File Surat</h5>
                        <div class="d-flex gap-2">
                            @if ($surat->draft_path && in_array($surat->status, ['diajukan', 'diproses']))
                                <a href="{{ Storage::url($surat->draft_path) }}" target="_blank" class="btn btn-info">
                                    <i class="bx bx-show me-1"></i> Lihat Draft
                                </a>
                            @endif

                            @if ($surat->file_surat_path)
                                <a href="{{ Storage::url($surat->file_surat_path) }}" target="_blank"
                                    class="btn btn-primary">
                                    <i class="bx bx-show me-1"></i> Lihat Surat Final
                                </a>
                                <a href="{{ route('admin.surat-aktif-kuliah.download', $surat->id) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-download me-1"></i> Unduh Surat Final
                                </a>
                            @endif
                        </div>

                        @if ($surat->draft_path && auth()->user()->hasRole('dosen'))
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Ini adalah versi draft surat. Silakan review sebelum memberikan persetujuan.
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Update Form -->
        @if (!in_array($surat->status, ['sudah_diambil', 'ditolak']))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if (auth()->user()->hasRole('dosen'))
                            Persetujuan Surat
                        @else
                            Update Status Pengajuan
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if (auth()->user()->hasRole('dosen'))
                        <!-- Form khusus untuk dosen -->
                        <form action="{{ route('admin.surat-aktif-kuliah.approve', $surat->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="penandatangan_id" class="form-label">Penandatangan <span
                                            class="text-danger">*</span></label>
                                    <select name="penandatangan_id" id="penandatangan_id" class="form-select" required>
                                        <option value="">Pilih Penandatangan</option>
                                        @foreach ($penandatangans as $penandatangan)
                                            <option value="{{ $penandatangan->id }}"
                                                {{ $surat->penandatangan_id == $penandatangan->id ? 'selected' : '' }}>
                                                {{ $penandatangan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="jabatan_penandatangan" class="form-label">Jabatan Penandatangan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="jabatan_penandatangan" id="jabatan_penandatangan"
                                        class="form-control" value="{{ $surat->jabatan_penandatangan }}" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="catatan_admin" class="form-label">Catatan</label>
                                    <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" name="action" value="approve" class="btn btn-success me-2">
                                        <i class="bx bx-check me-1"></i> Setujui
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                                        <i class="bx bx-x me-1"></i> Tolak
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Form untuk staff/admin -->
                        <form action="{{ route('admin.surat-aktif-kuliah.update-status', $surat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status Pengajuan <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select" required>
                                        @php
                                            $nextStatuses = [
                                                'diajukan' => ['diproses', 'ditolak'],
                                                'diproses' => ['disetujui', 'ditolak'],
                                                'disetujui' => ['siap_diambil'],
                                                'siap_diambil' => ['sudah_diambil'],
                                            ];
                                            $currentStatus = $surat->status;
                                        @endphp

                                        @foreach ($nextStatuses[$currentStatus] ?? [] as $nextStatus)
                                            <option value="{{ $nextStatus }}">
                                                {{ ucfirst(str_replace('_', ' ', $nextStatus)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if (in_array($surat->status, ['diproses', 'disetujui']))
                                    <!-- Form penandatangan hanya muncul untuk status tertentu -->
                                    <div class="col-md-6 mb-3">
                                        <label for="penandatangan_id" class="form-label">Penandatangan <span
                                                class="text-danger">*</span></label>
                                        <select name="penandatangan_id" id="penandatangan_id" class="form-select"
                                            required>
                                            <option value="">Pilih Penandatangan</option>
                                            @foreach ($penandatangans as $penandatangan)
                                                <option value="{{ $penandatangan->id }}"
                                                    {{ $surat->penandatangan_id == $penandatangan->id ? 'selected' : '' }}>
                                                    {{ $penandatangan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="jabatan_penandatangan" class="form-label">Jabatan Penandatangan <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="jabatan_penandatangan" id="jabatan_penandatangan"
                                            class="form-control" value="{{ $surat->jabatan_penandatangan }}"
                                            placeholder="Masukkan jabatan penandatangan" required>
                                    </div>
                                @endif

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
                    @endif
                </div>
            </div>
        @endif

        <!-- Timeline -->
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
                                        'disetujui', 'siap_diambil', 'sudah_diambil' => 'text-success',
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
            const statusSelect = document.getElementById('status');
            const penandatanganSelect = document.getElementById('penandatangan_id');
            const jabatanInput = document.getElementById('jabatan_penandatangan');
            const penandatanganRequired = document.getElementById('penandatangan_required');
            const jabatanRequired = document.getElementById('jabatan_required');

            function toggleRequiredFields() {
                if (!statusSelect) return; // Skip if not staff form

                const requiresPenandatangan = ['disetujui', 'siap_diambil'].includes(statusSelect.value);
                if (penandatanganSelect) penandatanganSelect.required = requiresPenandatangan;
                if (jabatanInput) jabatanInput.required = requiresPenandatangan;
                if (penandatanganRequired) penandatanganRequired.style.display = requiresPenandatangan ? 'inline' :
                    'none';
                if (jabatanRequired) jabatanRequired.style.display = requiresPenandatangan ? 'inline' : 'none';
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', toggleRequiredFields);
                toggleRequiredFields(); // Panggil saat load
            }
        });
    </script>
@endpush
