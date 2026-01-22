{{-- Modal TTD Kajur - UNTUK KAJUR DAN STAFF --}}
@if ($pendaftaranUjianHasil->suratUsulanSkripsi && $pendaftaranUjianHasil->suratUsulanSkripsi->status === 'menunggu_ttd_kajur' && 
    (auth()->user()->isKetuaJurusan() || auth()->user()->hasRole('staff')))

    @php
        $isKajur = auth()->user()->isKetuaJurusan();
        $isStaff = auth()->user()->hasRole('staff') && !$isKajur;
        
        // Get default Kajur untuk staff
        $defaultKajur = null;
        if ($isStaff) {
            $defaultKajur = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'dosen');
            })->where(function ($query) {
                $query->whereRaw('LOWER(jabatan) LIKE ?', ['%ketua jurusan%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kajur%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%pimpinan jurusan%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kepala jurusan%']);
            })->first();
        }
    @endphp

    <div class="modal fade" id="ttdKajurModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header {{ $isStaff ? 'bg-warning' : 'bg-primary' }} text-white">
                    <h5 class="modal-title text-white">
                        <i class="bx {{ $isStaff ? 'bx-shield-alt' : 'bx-pen' }} me-2"></i>
                        {{ $isStaff ? 'Staff Override - TTD Kajur' : 'Tanda Tangan Kajur' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-ujian-hasil.ttd-kajur', $pendaftaranUjianHasil) }}" method="POST" id="formTtdKajur">
                    @csrf
                    <div class="modal-body">
                        {{-- Info untuk Kajur --}}
                        @if ($isKajur)
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Informasi:</strong><br>
                                Anda akan menandatangani surat ini sebagai <strong>Ketua Jurusan</strong>.
                            </div>
                        @endif

                        {{-- Info untuk Staff Override --}}
                        @if ($isStaff)
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-shield-alt fs-4 me-2 text-warning"></i>
                                    <div>
                                        <strong class="d-block mb-1">⚠️ STAFF OVERRIDE MODE</strong>
                                        <small>
                                            Anda akan menandatangani surat ini <strong>atas nama Ketua Jurusan</strong>.
                                            <br><br>
                                            <strong>Penandatangan:</strong><br>
                                            @if ($defaultKajur)
                                                <span class="badge bg-primary">{{ $defaultKajur->name }}</span>
                                                <br><small class="text-muted">{{ $defaultKajur->jabatan }}</small>
                                            @else
                                                <span class="badge bg-danger">Tidak Ditemukan!</span>
                                                <br><small class="text-danger">Silakan tambahkan dosen dengan jabatan Ketua Jurusan</small>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger mb-3">
                                <i class="bx bx-error-circle me-2"></i>
                                <strong>Perhatian:</strong>
                                <ul class="mb-0 mt-2 small">
                                    <li>Tindakan ini akan tercatat dalam sistem audit</li>
                                    <li>Staff ID: <strong>{{ auth()->user()->id }}</strong> ({{ auth()->user()->name }})</li>
                                    <li>Waktu override akan dicatat</li>
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Status Persetujuan:</strong><br>
                            Kaprodi telah menandatangani surat pada:
                            <strong>{{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at->format('d M Y H:i') }} WITA</strong>
                            @if ($pendaftaranUjianHasil->suratUsulanSkripsi->override_info && isset($pendaftaranUjianHasil->suratUsulanSkripsi->override_info['kaprodi']))
                                <br><small class="text-muted">
                                    (Override oleh: {{ $pendaftaranUjianHasil->suratUsulanSkripsi->override_info['kaprodi']['override_by_name'] ?? '-' }})
                                </small>
                            @endif
                        </div>

                        <div class="alert alert-secondary mb-3">
                            <i class="bx bx-shield-quarter me-2"></i>
                            <strong>Tanda Tangan Terakhir:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ini adalah tanda tangan final untuk surat ini</li>
                                <li>QR Code akan di-generate untuk verifikasi publik</li>
                                <li>Mahasiswa dapat langsung mengunduh surat</li>
                                <li>Proses tidak dapat dibatalkan setelah TTD</li>
                            </ul>
                        </div>

                        <!-- Surat Info -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Surat</label>
                            <input type="text" class="form-control" value="{{ $pendaftaranUjianHasil->suratUsulanSkripsi->nomor_surat }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $pendaftaranUjianHasil->user->name }} ({{ $pendaftaranUjianHasil->user->nim }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Skripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ strip_tags($pendaftaranUjianHasil->judul_skripsi) }}</textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <i class="bx bx-check-circle me-2"></i>
                            Dengan menandatangani, proses persetujuan <strong>SELESAI</strong>.
                            Surat resmi dapat diunduh oleh mahasiswa.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn {{ $isStaff ? 'btn-warning' : 'btn-primary' }}"
                            @if ($isStaff && !$defaultKajur) disabled @endif>
                            <i class="bx {{ $isStaff ? 'bx-shield-alt' : 'bx-pen' }} me-1"></i> 
                            {{ $isStaff ? 'Override & Selesaikan' : 'Ya, Tanda Tangani & Selesaikan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
