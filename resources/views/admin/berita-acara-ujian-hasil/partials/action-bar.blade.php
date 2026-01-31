{{-- Status & Quick Action Bar with all action buttons --}}
@props(['isDosen', 'isStaff', 'isPenguji', 'isPembimbing', 'isKetua', 'myKoreksi', 'user'])

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <small class="text-uppercase text-muted fw-bold d-block mb-1">Status Saat Ini</small>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        {!! $beritaAcara->status_badge !!}
                    </div>
                    <div class="text-muted small">
                        <i class="bx bx-info-circle me-1 text-warning"></i>
                        {{ $beritaAcara->workflow_message }}
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end flex-wrap gap-2">
                {{-- TOMBOL UNTUK PENGUJI --}}
                @if ($isPenguji)
                    @if ($beritaAcara->canBeSignedByPenguji($user->id))
                        <a href="{{ route('admin.berita-acara-ujian-hasil.approve-penguji', $beritaAcara) }}"
                            class="btn btn-primary fw-bold shadow-sm">
                            <i class="bx bx-check-circle me-1"></i>Berikan Persetujuan
                        </a>
                    @elseif ($beritaAcara->hasSignedByPenguji($user->id))
                        <span class="badge bg-label-success p-2 px-3 fs-6">
                            <i class="bx bx-check-circle me-1"></i> Sudah Disetujui
                        </span>
                    @endif
                @endif

                {{-- TOMBOL LEMBAR KOREKSI (PS1/PS2 - OPSIONAL) --}}
                @if ($isPembimbing)
                    <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}"
                        class="btn btn-outline-info fw-bold">
                        <i class="bx bx-edit me-1"></i>{{ $myKoreksi ? 'Ubah Lembar Koreksi' : 'Isi Lembar Koreksi' }}
                        <span class="badge bg-label-info ms-2">Opsional</span>
                    </a>
                @endif

                {{-- TOMBOL UNTUK KETUA PENGUJI - DEPRECATED: Workflow baru tidak memerlukan persetujuan Ketua Penguji --}}
                {{-- Step langsung dari Penguji -> Sekretaris Panitia -> Ketua Panitia --}}

                {{-- STATUS SELESAI BADGE --}}
                @if ($beritaAcara->isSelesai())
                    <span class="badge bg-label-success p-2 px-3 fs-6">
                        <i class="bx bx-check-circle me-1"></i> Berkas Telah Selesai
                    </span>
                @endif

                {{-- TOMBOL SEKRETARIS PANITIA (KORPRODI) - Untuk Korprodi langsung --}}
                @if ($beritaAcara->isMenungguTtdPanitiaSekretaris())
                    @if ($user->canSignAsPanitiaSekretaris())
                        <a href="{{ route('admin.berita-acara-ujian-hasil.sign-panitia-sekretaris', $beritaAcara) }}"
                            class="btn btn-info fw-bold shadow-sm">
                            <i class="bx bx-pen me-1"></i>TTD Sekretaris Panitia
                        </a>
                    @elseif ($isStaff)
                        {{-- Staff Override untuk Sekretaris --}}
                        <a href="{{ route('admin.berita-acara-ujian-hasil.sign-panitia-sekretaris', $beritaAcara) }}"
                            class="btn btn-dark fw-bold shadow-sm">
                            <i class="bx bx-user-check me-1"></i>Override Sekretaris Panitia
                        </a>
                    @endif
                @endif

                {{-- TOMBOL KETUA PANITIA (DEKAN) - Untuk Dekan langsung --}}
                @if ($beritaAcara->isMenungguTtdPanitiaKetua())
                    @if ($user->canSignAsPanitiaKetua())
                        <a href="{{ route('admin.berita-acara-ujian-hasil.sign-panitia-ketua', $beritaAcara) }}"
                            class="btn btn-warning fw-bold shadow-sm text-white">
                            <i class="bx bx-pen me-1"></i>TTD Ketua Panitia
                        </a>
                    @elseif ($isStaff)
                        {{-- Staff Override untuk Ketua Panitia --}}
                        <a href="{{ route('admin.berita-acara-ujian-hasil.sign-panitia-ketua', $beritaAcara) }}"
                            class="btn btn-dark fw-bold shadow-sm">
                            <i class="bx bx-user-check me-1"></i>Override Ketua Panitia
                        </a>
                    @endif
                @endif

                {{-- TOMBOL STAFF/ADMIN --}}
                @if ($isStaff)
                    @if ($beritaAcara->isSelesai() && !$beritaAcara->file_path)
                        <form action="{{ route('admin.berita-acara-ujian-hasil.generate-pdf', $beritaAcara) }}"
                            method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info fw-bold shadow-sm">
                                <i class="bx bxs-file-pdf me-1"></i>Generate PDF
                            </button>
                        </form>
                    @endif

                    @can('delete', $beritaAcara)
                        <button type="button" class="btn btn-outline-danger fw-bold"
                            onclick="deleteBeritaAcara({{ $beritaAcara->id }}, '{{ addslashes($mahasiswa->name) }}', '{{ $beritaAcara->status }}')">
                            <i class="bx bx-trash me-1"></i>Hapus
                        </button>
                    @endcan
                @endif

                {{-- TOMBOL DOWNLOAD PDF --}}
                @if ($beritaAcara->file_path)
                    <a href="{{ route('admin.berita-acara-ujian-hasil.view-pdf', $beritaAcara) }}"
                        class="btn btn-secondary fw-bold" target="_blank">
                        <i class="bx bx-show me-1"></i>Lihat PDF
                    </a>
                    <a href="{{ route('admin.berita-acara-ujian-hasil.download-pdf', $beritaAcara) }}"
                        class="btn btn-primary fw-bold shadow-sm">
                        <i class="bx bx-download me-1"></i>Download
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
