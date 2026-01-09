{{-- filepath: resources/views/admin/jadwal-seminar-proposal/ganti-dosen.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Kelola Dosen Penguji')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Jadwal Sempro /</span> Kelola Penguji
                </h4>
                <p class="text-muted mb-0">{{ $mahasiswa->name }} ({{ $mahasiswa->nim }})</p>
            </div>
            <a href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Info Ujian --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Informasi Ujian</strong></h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>Tanggal</strong></td>
                                <td>: {{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA</td>
                            </tr>
                            <tr>
                                <td><strong>Ruangan</strong></td>
                                <td>: {{ $jadwal->ruangan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Judul Proposal</strong></h6>
                        <p class="mb-0">{{ $pendaftaran->judul_skripsi }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Penguji List --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Daftar Dosen Penguji Saat Ini</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Posisi</th>
                                <th>Nama Dosen</th>
                                <th>NIP</th>
                                <th>Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwal->dosenPenguji as $index => $dosen)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $dosen->pivot->posisi }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                                    {{ strtoupper(substr($dosen->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $dosen->name }}</strong>
                                                @if ($dosen->pivot->dosen_pengganti_id)
                                                    <br><small class="text-muted">
                                                        <i class="bx bx-transfer me-1"></i>Menggantikan
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $dosen->nip ?? '-' }}</td>
                                    <td>
                                        @if ($dosen->pivot->status_kehadiran === 'Hadir')
                                            <span class="badge bg-success">
                                                <i class="bx bx-check me-1"></i>Hadir
                                            </span>
                                        @elseif($dosen->pivot->status_kehadiran === 'Berhalangan')
                                            <span class="badge bg-danger">
                                                <i class="bx bx-x me-1"></i>Berhalangan
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-time me-1"></i>Belum Konfirmasi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#gantiDosenModal" data-posisi="{{ $dosen->pivot->posisi }}"
                                            data-dosen-id="{{ $dosen->id }}" data-dosen-name="{{ $dosen->name }}">
                                            <i class="bx bx-user-plus me-1"></i> Ganti
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Belum ada dosen penguji
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($jadwal->dosenPenguji->count() > 0)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Catatan:</strong>
                        <ul class="mb-0 ps-3">
                            <li>Penggantian dosen akan mencatat dosen lama sebagai "Berhalangan"</li>
                            <li>Dosen pengganti akan otomatis mendapat status "Hadir"</li>
                            <li>Undangan email tidak akan dikirim otomatis, kirim manual jika diperlukan</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Update Kehadiran Bulk --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status Kehadiran (Bulk)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.jadwal-seminar-proposal.update-kehadiran', $jadwal) }}" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Dosen</th>
                                    <th>Posisi</th>
                                    <th width="300">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal->dosenPenguji as $dosen)
                                    <tr>
                                        <td>{{ $dosen->name }}</td>
                                        <td>
                                            <span class="badge bg-label-primary">{{ $dosen->pivot->posisi }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check"
                                                    name="kehadiran[{{ $dosen->id }}]" id="hadir_{{ $dosen->id }}"
                                                    value="Hadir"
                                                    {{ $dosen->pivot->status_kehadiran === 'Hadir' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="hadir_{{ $dosen->id }}">
                                                    <i class="bx bx-check me-1"></i> Hadir
                                                </label>

                                                <input type="radio" class="btn-check"
                                                    name="kehadiran[{{ $dosen->id }}]" id="belum_{{ $dosen->id }}"
                                                    value="Belum Dikonfirmasi"
                                                    {{ $dosen->pivot->status_kehadiran === 'Belum Dikonfirmasi' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary" for="belum_{{ $dosen->id }}">
                                                    <i class="bx bx-time me-1"></i> Belum Konfirmasi
                                                </label>

                                                <input type="radio" class="btn-check"
                                                    name="kehadiran[{{ $dosen->id }}]"
                                                    id="berhalangan_{{ $dosen->id }}" value="Berhalangan"
                                                    {{ $dosen->pivot->status_kehadiran === 'Berhalangan' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger"
                                                    for="berhalangan_{{ $dosen->id }}">
                                                    <i class="bx bx-x me-1"></i> Berhalangan
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update Status Kehadiran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Ganti Dosen --}}
    <div class="modal fade" id="gantiDosenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal-seminar-proposal.ganti-dosen', $jadwal) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ganti Dosen Penguji</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="posisi" id="modal_posisi">

                        <div class="alert alert-warning">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Perhatian:</strong> Dosen <span id="modal_dosen_name" class="fw-bold"></span>
                            akan diganti dan statusnya menjadi "Berhalangan".
                        </div>

                        <div class="mb-3">
                            <label for="dosen_pengganti_id" class="form-label required">
                                Pilih Dosen Pengganti
                            </label>
                            <select class="form-select @error('dosen_pengganti_id') is-invalid @enderror"
                                id="dosen_pengganti_id" name="dosen_pengganti_id" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach ($availableDosen as $dosen)
                                    <option value="{{ $dosen->id }}">
                                        {{ $dosen->name }} ({{ $dosen->nip ?? 'Tanpa NIP' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pengganti_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">
                                Keterangan <small class="text-muted">(Opsional)</small>
                            </label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                rows="3" placeholder="Alasan penggantian dosen..." maxlength="500">{{ old('keterangan') }}</textarea>
                            <small class="text-muted">Maksimal 500 karakter</small>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Penggantian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gantiDosenModal = document.getElementById('gantiDosenModal');

            if (gantiDosenModal) {
                gantiDosenModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const posisi = button.getAttribute('data-posisi');
                    const dosenId = button.getAttribute('data-dosen-id');
                    const dosenName = button.getAttribute('data-dosen-name');

                    // Set data ke modal
                    document.getElementById('modal_posisi').value = posisi;
                    document.getElementById('modal_dosen_name').textContent = dosenName;

                    // Filter out current dosen from dropdown
                    const select = document.getElementById('dosen_pengganti_id');
                    Array.from(select.options).forEach(option => {
                        if (option.value == dosenId) {
                            option.disabled = true;
                            option.textContent += ' (Saat ini bertugas)';
                        }
                    });
                });

                // Reset on modal hide
                gantiDosenModal.addEventListener('hidden.bs.modal', function() {
                    const select = document.getElementById('dosen_pengganti_id');
                    select.value = '';
                    document.getElementById('keterangan').value = '';

                    // Re-enable all options
                    Array.from(select.options).forEach(option => {
                        option.disabled = false;
                        option.textContent = option.textContent.replace(' (Saat ini bertugas)', '');
                    });
                });
            }
        });
    </script>
@endpush
