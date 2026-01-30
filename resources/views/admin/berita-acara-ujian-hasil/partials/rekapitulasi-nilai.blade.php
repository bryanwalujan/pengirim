{{-- Recapitulation of Scores and Corrections --}}
@props(['jadwal', 'isDosen', 'user'])

<div class="card mb-4 shadow-sm border-0 overflow-hidden">
    <div class="card-header border-bottom p-4">
        <h5 class="mb-0 fw-bold"><i class="bx bx-bar-chart-alt-2 me-2 text-warning"></i>Rekapitulasi Nilai & Koreksi</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 fw-bold py-3" width="5%">No</th>
                    <th class="fw-bold py-3">Dosen Penguji</th>
                    <th class="fw-bold py-3 text-center" width="15%">Nilai Akhir</th>
                    <th class="fw-bold py-3 text-center" width="15%">Status Koreksi</th>
                    <th class="pe-4 fw-bold py-3 text-center" width="25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($jadwal)
                    @foreach ($jadwal->dosenPenguji as $index => $penguji)
                        @php
                            $penilaian = $beritaAcara->penilaians->where('dosen_id', $penguji->id)->first();
                            $koreksi = $beritaAcara->lembarKoreksis->where('dosen_id', $penguji->id)->first();
                            $isPembimbingUser = str_contains($penguji->pivot->posisi, 'PS1') ||
                                str_contains($penguji->pivot->posisi, 'PS2') ||
                                str_contains($penguji->pivot->posisi, 'Pembimbing');
                            $isCurrentDosen = $penguji->id === $user->id;
                            $isKetuaPenguji = $penguji->pivot->posisi === 'Ketua Penguji';
                            $canEditPenilaian = $isCurrentDosen && $beritaAcara->isMenungguTtdPenguji() && !$isKetuaPenguji;
                        @endphp
                        <tr class="{{ $isCurrentDosen ? 'table-warning' : '' }}">
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle {{ $isCurrentDosen ? 'bg-warning' : 'bg-label-primary' }}">
                                            {{ strtoupper(substr($penguji->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $penguji->name }}</div>
                                        <small class="text-muted">{{ $penguji->pivot->posisi }}</small>
                                        @if ($isCurrentDosen)
                                            <span class="badge bg-warning ms-1" style="font-size: 9px;">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($penilaian)
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold text-warning fs-5">{{ number_format($penilaian->nilai_mutu ?? 0, 2) }}</span>
                                        <span class="badge bg-label-warning px-2 rounded" style="font-size: 10px;">{{ $penilaian->grade_letter }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Belum diinput</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($isPembimbingUser)
                                    @if ($koreksi)
                                        <span class="badge bg-label-success p-2 rounded-2">
                                            <i class="bx bx-check-double"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary p-2 rounded-2">
                                            <i class="bx bx-minus"></i> Belum
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    {{-- Tombol Lihat Detail Penilaian --}}
                                    @if ($penilaian)
                                        <button class="btn btn-sm btn-outline-info"
                                            onclick="showDetailModal({{ $penguji->id }}, '{{ addslashes($penguji->name) }}', '{{ $penguji->pivot->posisi }}')"
                                            data-bs-toggle="tooltip" title="Lihat Detail Penilaian">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    @endif

                                    {{-- Tombol Edit/Isi Penilaian untuk Dosen yang login --}}
                                    @if ($canEditPenilaian)
                                        <a href="{{ route('dosen.berita-acara-ujian-hasil.penilaian', $beritaAcara) }}"
                                            class="btn btn-sm {{ $penilaian ? 'btn-outline-warning' : 'btn-primary' }}"
                                            data-bs-toggle="tooltip" title="{{ $penilaian ? 'Edit Penilaian' : 'Isi Penilaian' }}">
                                            <i class="bx {{ $penilaian ? 'bx-edit' : 'bx-plus' }}"></i>
                                            {{ $penilaian ? 'Edit' : 'Isi' }}
                                        </a>
                                    @endif

                                    {{-- Tombol Edit/Isi Lembar Koreksi untuk PS1/PS2 --}}
                                    @if ($isPembimbingUser && $isCurrentDosen && $beritaAcara->isMenungguTtdPenguji())
                                        <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}"
                                            class="btn btn-sm {{ $koreksi ? 'btn-outline-secondary' : 'btn-secondary' }}"
                                            data-bs-toggle="tooltip" title="{{ $koreksi ? 'Edit Koreksi' : 'Isi Koreksi' }}">
                                            <i class="bx {{ $koreksi ? 'bx-edit' : 'bx-plus' }}"></i>
                                            Koreksi
                                        </a>
                                    @endif

                                    {{-- Jika tidak ada aksi --}}
                                    @if (!$penilaian && !$canEditPenilaian && !($isPembimbingUser && $isCurrentDosen && $beritaAcara->isMenungguTtdPenguji()))
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
