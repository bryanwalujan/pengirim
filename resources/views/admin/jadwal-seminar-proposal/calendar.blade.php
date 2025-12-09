{{-- filepath: resources/views/admin/jadwal-seminar-proposal/calendar.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Kalender Jadwal Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2"></i>Kalender Jadwal Seminar Proposal
                </h4>
                <p class="text-muted mb-0">
                    {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-seminar-proposal.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-list-ul me-1"></i>Tampilan List
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-calendar fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">Total Jadwal</small>
                                <h5 class="mb-0">{{ $stats['total_jadwal'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-calendar-check fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">Hari Ini</small>
                                <h5 class="mb-0">{{ $stats['hari_ini'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="bx bx-calendar-week fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">Minggu Ini</small>
                                <h5 class="mb-0">{{ $stats['minggu_ini'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-calendar-star fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-muted d-block">Bulan Ini</small>
                                <h5 class="mb-0">{{ $stats['bulan_ini'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.jadwal-seminar-proposal.calendar') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-calendar me-1"></i>Bulan
                        </label>
                        <select name="bulan" class="form-select">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-time me-1"></i>Tahun
                        </label>
                        <select name="tahun" class="form-select">
                            @for ($i = now()->year - 2; $i <= now()->year + 2; $i++)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.jadwal-seminar-proposal.calendar') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 calendar-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center py-3 fw-semibold">Minggu</th>
                                <th class="text-center py-3 fw-semibold">Senin</th>
                                <th class="text-center py-3 fw-semibold">Selasa</th>
                                <th class="text-center py-3 fw-semibold">Rabu</th>
                                <th class="text-center py-3 fw-semibold">Kamis</th>
                                <th class="text-center py-3 fw-semibold">Jumat</th>
                                <th class="text-center py-3 fw-semibold">Sabtu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $firstDay = \Carbon\Carbon::create($tahun, $bulan, 1);
                                $lastDay = $firstDay->copy()->endOfMonth();
                                $startOfCalendar = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                                $endOfCalendar = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                                $currentDate = $startOfCalendar->copy();
                            @endphp

                            @while ($currentDate <= $endOfCalendar)
                                <tr>
                                    @for ($i = 0; $i < 7; $i++)
                                        @php
                                            $isCurrentMonth = $currentDate->month == $bulan;
                                            $dateKey = $currentDate->format('Y-m-d');
                                            $jadwalsOnDate = $jadwals->filter(function ($j) use ($dateKey) {
                                                return $j->tanggal->format('Y-m-d') === $dateKey;
                                            });
                                        @endphp
                                        <td class="calendar-day {{ !$isCurrentMonth ? 'bg-light text-muted' : '' }} {{ $currentDate->isToday() ? 'bg-primary bg-opacity-10' : '' }}"
                                            style="height: 120px; vertical-align: top; position: relative; min-width: 150px;">
                                            <div class="p-2">
                                                <div
                                                    class="fw-bold mb-2 {{ $currentDate->isToday() ? 'text-primary' : '' }}">
                                                    {{ $currentDate->format('d') }}
                                                    @if ($currentDate->isToday())
                                                        <span class="badge bg-primary badge-sm ms-1">Hari Ini</span>
                                                    @endif
                                                </div>

                                                @if ($jadwalsOnDate->isNotEmpty())
                                                    <div class="jadwal-list">
                                                        @foreach ($jadwalsOnDate as $jadwal)
                                                            <div class="jadwal-item mb-2 p-2 bg-primary bg-opacity-10 rounded-2 small cursor-pointer hover-shadow"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#detailModal{{ $jadwal->id }}">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="bx bx-time text-primary me-1"></i>
                                                                    <strong class="text-primary">
                                                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                                    </strong>
                                                                </div>
                                                                <div class="text-truncate fw-semibold"
                                                                    style="max-width: 140px;"
                                                                    title="{{ $jadwal->pendaftaranSeminarProposal->user->name }}">
                                                                    {{ $jadwal->pendaftaranSeminarProposal->user->name }}
                                                                </div>
                                                                <div class="text-muted small">
                                                                    <i
                                                                        class="bx bx-door-open me-1"></i>{{ Str::limit($jadwal->ruangan, 15) }}
                                                                </div>
                                                            </div>

                                                            {{-- Modal Detail --}}
                                                            <div class="modal fade" id="detailModal{{ $jadwal->id }}"
                                                                tabindex="-1">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-primary">
                                                                            <h5 class="modal-title text-white">
                                                                                <i
                                                                                    class="bx bx-info-circle me-2"></i>Detail
                                                                                Jadwal Seminar Proposal
                                                                            </h5>
                                                                            <button type="button"
                                                                                class="btn-close btn-close-white"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row g-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">Mahasiswa</label>
                                                                                    <p class="fw-bold mb-0">
                                                                                        {{ $jadwal->pendaftaranSeminarProposal->user->name }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">NIM</label>
                                                                                    <p class="fw-bold mb-0">
                                                                                        {{ $jadwal->pendaftaranSeminarProposal->user->nim }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">Judul
                                                                                        Skripsi</label>
                                                                                    <p class="mb-0">
                                                                                        {{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">Tanggal</label>
                                                                                    <p class="fw-bold mb-0">
                                                                                        {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">Waktu</label>
                                                                                    <p class="fw-bold mb-0">
                                                                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                                                        -
                                                                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                                                        WITA
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label text-muted small mb-1">Ruangan</label>
                                                                                    <p class="fw-bold mb-0">
                                                                                        {{ $jadwal->ruangan }}
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <a href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}"
                                                                                class="btn btn-primary">
                                                                                <i class="bx bx-show me-1"></i>Lihat Detail
                                                                                Lengkap
                                                                            </a>
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">
                                                                                <i class="bx bx-x me-1"></i>Tutup
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        @php
                                            $currentDate->addDay();
                                        @endphp
                                    @endfor
                                </tr>
                            @endwhile
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-bold me-3">
                        <i class="bx bx-info-circle me-1"></i>Keterangan:
                    </span>
                    <span class="badge bg-primary">Hari Ini</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary">Jadwal Seminar</span>
                    <small class="text-muted">
                        <i class="bx bx-mouse me-1"></i>Klik pada jadwal untuk melihat detail
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .calendar-table {
            border-collapse: collapse;
        }

        .calendar-table td {
            border: 1px solid #dee2e6;
            min-width: 150px;
        }

        .calendar-day {
            transition: background-color 0.2s;
        }

        .calendar-day:hover {
            background-color: rgba(105, 108, 255, 0.05) !important;
        }

        .jadwal-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid var(--bs-primary);
        }

        .jadwal-item:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.15);
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2) !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .badge-sm {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
        }
    </style>
@endpush
@endsection
