@if ($activities->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class='bx bx-search-alt-2'></i>
        </div>
        <h5 class="mb-2">Tidak Ada Data Ditemukan</h5>
        <p class="text-muted mb-3">
            Tidak ada aktivitas yang sesuai dengan filter yang Anda pilih.
        </p>
        <button class="btn btn-outline-primary" onclick="clearAllFilters()">
            <i class='bx bx-refresh me-1'></i>Reset Filter
        </button>
    </div>
@else
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Mahasiswa</th>
                    <th>Jenis Surat</th>
                    <th>Keterangan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach ($activities as $activity)
                    <tr>
                        <td>
                            @php
                                $badgeClass = match ($activity->aksi) {
                                    'diajukan' => 'diajukan',
                                    'diproses' => 'diproses',
                                    'disetujui', 'disetujui_kaprodi' => 'disetujui',
                                    'ditolak' => 'ditolak',
                                    'siap_diambil' => 'siap_diambil',
                                    'diambil' => 'diambil',
                                    default => 'diproses',
                                };
                            @endphp
                            <span class="activity-badge {{ $badgeClass }}">
                                {{ str_replace('_', ' ', ucwords($activity->aksi)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    @php
                                        $initials = '';
                                        if ($activity->mahasiswa) {
                                            $nameParts = explode(' ', $activity->mahasiswa->name);
                                            $initials =
                                                count($nameParts) >= 2
                                                    ? strtoupper(
                                                        substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1),
                                                    )
                                                    : strtoupper(substr($activity->mahasiswa->name, 0, 2));
                                        }
                                    @endphp
                                    <span class="avatar-initial rounded bg-label-primary">
                                        {{ $initials ?: 'NA' }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        {{ $activity->mahasiswa->name ?? 'Data tidak tersedia' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $activity->mahasiswa->nim ?? 'NIM tidak tersedia' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $suratInfo = match ($activity->surat_type) {
                                    'App\\Models\\SuratAktifKuliah' => [
                                        'icon' => 'bx-user-check',
                                        'text' => 'Surat Aktif Kuliah',
                                        'class' => 'primary',
                                    ],
                                    'App\\Models\\SuratIjinSurvey' => [
                                        'icon' => 'bx-search-alt',
                                        'text' => 'Surat Ijin Survey',
                                        'class' => 'info',
                                    ],
                                    'App\\Models\\SuratCutiAkademik' => [
                                        'icon' => 'bx-pause',
                                        'text' => 'Surat Cuti Akademik',
                                        'class' => 'warning',
                                    ],
                                    'App\\Models\\SuratPindah' => [
                                        'icon' => 'bx-transfer',
                                        'text' => 'Surat Pindah',
                                        'class' => 'secondary',
                                    ],
                                    default => [
                                        'icon' => 'bx-file',
                                        'text' => class_basename($activity->surat_type),
                                        'class' => 'secondary',
                                    ],
                                };
                            @endphp
                            <span class="surat-badge badge bg-label-{{ $suratInfo['class'] }}">
                                <i class='bx {{ $suratInfo['icon'] }} me-1'></i>
                                {{ $suratInfo['text'] }}
                            </span>
                        </td>
                        <td>
                            @if ($activity->keterangan)
                                <span title="{{ $activity->keterangan }}">
                                    {{ Str::limit($activity->keterangan, 50) }}
                                </span>
                            @else
                                <span class="text-muted fst-italic">Tidak ada keterangan</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <span class="fw-medium">{{ $activity->created_at->diffForHumans() }}</span>
                                <br>
                                <small class="text-muted">
                                    {{ $activity->created_at->format('d M Y, H:i') }}
                                </small>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginasi --}}
    @if ($activities->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $activities->links() }}
        </div>
    @endif
@endif
