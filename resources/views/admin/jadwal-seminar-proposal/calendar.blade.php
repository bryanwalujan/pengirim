@extends('layouts.admin.app')

@section('title', 'Kalender Seminar Proposal')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Kalender Seminar Proposal
                            </h4>
                            <p class="text-muted mb-0">
                                Tampilan kalender jadwal seminar proposal
                            </p>
                        </div>
                        <a href="{{ route('admin.jadwal-seminar-proposal.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Tampilan List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bulan/Tahun -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.jadwal-seminar-proposal.calendar') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                @for($i = now()->year - 2; $i <= now()->year + 2; $i++)
                                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.jadwal-seminar-proposal.calendar') }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F Y') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 calendar-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center py-3">Minggu</th>
                                    <th class="text-center py-3">Senin</th>
                                    <th class="text-center py-3">Selasa</th>
                                    <th class="text-center py-3">Rabu</th>
                                    <th class="text-center py-3">Kamis</th>
                                    <th class="text-center py-3">Jumat</th>
                                    <th class="text-center py-3">Sabtu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $firstDay = \Carbon\Carbon::create($tahun, $bulan, 1);
                                    $lastDay = $firstDay->copy()->endOfMonth();
                                    $startOfCalendar = $firstDay->copy()->startOfWeek();
                                    $endOfCalendar = $lastDay->copy()->endOfWeek();
                                    $currentDate = $startOfCalendar->copy();
                                @endphp

                                @while($currentDate <= $endOfCalendar)
                                    <tr>
                                        @for($i = 0; $i < 7; $i++)
                                            @php
                                                $isCurrentMonth = $currentDate->month == $bulan;
                                                $dateKey = $currentDate->format('Y-m-d');
                                                $jadwalsOnDate = $jadwals->filter(function($j) use ($dateKey) {
                                                    return $j->tanggal == $dateKey;
                                                });
                                            @endphp
                                            <td class="calendar-day {{ !$isCurrentMonth ? 'text-muted bg-light' : '' }}" 
                                                style="height: 120px; vertical-align: top; position: relative;">
                                                <div class="p-2">
                                                    <div class="fw-bold mb-2 {{ $currentDate->isToday() ? 'text-primary' : '' }}">
                                                        {{ $currentDate->format('d') }}
                                                        @if($currentDate->isToday())
                                                            <span class="badge bg-primary badge-sm">Hari Ini</span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($jadwalsOnDate->isNotEmpty())
                                                        <div class="jadwal-list">
                                                            @foreach($jadwalsOnDate as $jadwal)
                                                                <div class="jadwal-item mb-1 p-2 bg-primary bg-opacity-10 rounded small cursor-pointer"
                                                                     data-bs-toggle="modal" 
                                                                     data-bs-target="#detailModal{{ $jadwal->id }}">
                                                                    <div class="fw-bold text-primary">
                                                                        {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                                                    </div>
                                                                    <div class="text-truncate" style="max-width: 150px;">
                                                                        {{ $jadwal->pendaftaranSeminarProposal->user->name }}
                                                                    </div>
                                                                    <div class="text-muted">
                                                                        <i class="fas fa-door-open me-1"></i>{{ $jadwal->ruangan }}
                                                                    </div>
                                                                </div>

                                                                <!-- Modal Detail -->
                                                                <div class="modal fade" id="detailModal{{ $jadwal->id }}" tabindex="-1">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-primary text-white">
                                                                                <h5 class="modal-title">
                                                                                    <i class="fas fa-info-circle me-2"></i>Detail Jadwal Seminar Proposal
                                                                                </h5>
                                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row g-3">
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label text-muted small">Mahasiswa</label>
                                                                                        <p class="fw-bold">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label text-muted small">NIM</label>
                                                                                        <p class="fw-bold">{{ $jadwal->pendaftaranSeminarProposal->user->nim }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label class="form-label text-muted small">Judul Proposal</label>
                                                                                        <p>{{ $jadwal->pendaftaranSeminarProposal->judul_proposal }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-muted small">Tanggal</label>
                                                                                        <p class="fw-bold">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-muted small">Waktu</label>
                                                                                        <p class="fw-bold">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                                                                                    </div>
                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-muted small">Ruangan</label>
                                                                                        <p class="fw-bold">{{ $jadwal->ruangan }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <a href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}" 
                                                                                   class="btn btn-primary">
                                                                                    <i class="fas fa-eye me-2"></i>Lihat Detail Lengkap
                                                                                </a>
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold me-3">Keterangan:</span>
                        <span class="badge bg-primary">Hari Ini</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Jadwal Seminar</span>
                        <small class="text-muted">Klik pada jadwal untuk melihat detail</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        background-color: #f8f9fa !important;
    }
    .jadwal-item {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid var(--bs-primary);
    }
    .jadwal-item:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush
@endsection</div>