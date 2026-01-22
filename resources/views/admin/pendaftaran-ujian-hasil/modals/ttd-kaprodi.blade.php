{{-- Modal TTD Kaprodi - UNTUK KAPRODI DAN STAFF --}}
@if (
    $pendaftaranUjianHasil->suratUsulanSkripsi &&
        $pendaftaranUjianHasil->suratUsulanSkripsi->status === 'menunggu_ttd_kaprodi' &&
        (auth()->user()->isKoordinatorProdi() || auth()->user()->hasRole('staff')))

    @php
        $isKaprodi = auth()->user()->isKoordinatorProdi();
        $isStaff = auth()->user()->hasRole('staff') && !$isKaprodi;

        // Get default Kaprodi untuk staff
        $defaultKaprodi = null;
        if ($isStaff) {
            $defaultKaprodi = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'dosen');
            })
                ->where(function ($query) {
                    $query
                        ->whereRaw('LOWER(jabatan) LIKE ?', ['%koordinator%'])
                        ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kaprodi%'])
                        ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%korprodi%']);
                })
                ->first();
        }
    @endphp

    <div class="modal fade" id="ttdKaprodiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header {{ $isStaff ? 'bg-warning' : 'bg-success' }} text-white py-3">
                    <h5 class="modal-title text-white">
                        <i class="bx {{ $isStaff ? 'bx-shield-alt' : 'bx-pen' }} me-2"></i>
                        {{ $isStaff ? 'Staff Override - TTD Kaprodi' : 'Tanda Tangan Korprodi' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-ujian-hasil.ttd-kaprodi', $pendaftaranUjianHasil) }}"
                    method="POST" id="formTtdKaprodi">
                    @csrf
                    <div class="modal-body">
                        {{-- Info untuk Kaprodi --}}
                        @if ($isKaprodi)
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Informasi:</strong><br>
                                Anda akan menandatangani surat ini sebagai <strong>Koordinator Program Studi</strong>.
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
                                            Anda akan menandatangani surat ini <strong>atas nama Koordinator Program
                                                Studi</strong>.
                                            <br><br>
                                            <strong>Penandatangan:</strong><br>
                                            @if ($defaultKaprodi)
                                                <span class="badge bg-primary">{{ $defaultKaprodi->name }}</span>
                                                <br><small class="text-muted">{{ $defaultKaprodi->jabatan }}</small>
                                            @else
                                                <span class="badge bg-danger">Tidak Ditemukan!</span>
                                                <br><small class="text-danger">Silakan tambahkan dosen dengan jabatan
                                                    Koordinator Program Studi</small>
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
                                    <li>Staff ID: <strong>{{ auth()->user()->id }}</strong> ({{ auth()->user()->name }})
                                    </li>
                                    <li>Waktu override akan dicatat</li>
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-secondary mb-3">
                            <i class="bx bx-shield-quarter me-2"></i>
                            <strong>Keamanan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>QR Code akan di-generate untuk verifikasi</li>
                                <li>Tanda tangan digital tidak dapat dibatalkan</li>
                                <li>Dokumen akan tersimpan secara permanen</li>
                            </ul>
                        </div>

                        <!-- Surat Info -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaranUjianHasil->suratUsulanSkripsi->nomor_surat }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Mahasiswa</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaranUjianHasil->user->name }} ({{ $pendaftaranUjianHasil->user->nim }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Skripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ strip_tags($pendaftaranUjianHasil->judul_skripsi) }}</textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <i class="bx bx-check-circle me-2"></i>
                            Setelah TTD, surat akan diteruskan ke <strong>Kajur</strong> untuk persetujuan final.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn {{ $isStaff ? 'btn-warning' : 'btn-success' }}"
                            @if ($isStaff && !$defaultKaprodi) disabled @endif>
                            <i class="bx {{ $isStaff ? 'bx-shield-alt' : 'bx-pen' }} me-1"></i>
                            {{ $isStaff ? 'Override & Tanda Tangani' : 'Ya, Tanda Tangani' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
