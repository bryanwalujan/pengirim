@extends('layouts.admin.app')

@section('title', 'Aktivitas Sistem')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            border: 1px solid rgba(67, 89, 113, 0.1);
        }

        .stats-card {
            transition: all 0.3s ease;
            border-radius: 0.375rem;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px 0 rgba(67, 89, 113, 0.15);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #5a67d8 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #71dd37 0%, #28a745 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffab00 0%, #fd7e14 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #03c3ec 0%, #0dcaf0 100%);
        }

        .filter-container {
            background: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1.25rem;
            border: 1px solid rgba(67, 89, 113, 0.1);
        }

        .table-container {
            background: #fff;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(90deg, #696cff 0%, #5a67d8 100%);
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: none;
        }

        .activity-badge {
            font-size: 0.6875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 0.25rem 0.625rem;
            border-radius: 0.25rem;
        }

        .activity-badge.diajukan {
            background-color: rgba(54, 162, 235, 0.16);
            color: #36a2eb;
        }

        .activity-badge.diproses {
            background-color: rgba(255, 206, 84, 0.16);
            color: #ffce54;
        }

        .activity-badge.disetujui,
        .activity-badge.disetujui_kaprodi {
            background-color: rgba(113, 221, 55, 0.16);
            color: #71dd37;
        }

        .activity-badge.ditolak {
            background-color: rgba(255, 99, 132, 0.16);
            color: #ff6384;
        }

        .activity-badge.siap_diambil {
            background-color: rgba(255, 171, 0, 0.16);
            color: #ffab00;
        }

        .activity-badge.diambil {
            background-color: rgba(67, 89, 113, 0.16);
            color: #435971;
        }

        .surat-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .trend-indicator {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        .trend-up {
            color: #71dd37;
        }

        .trend-down {
            color: #ff3e1d;
        }

        .trend-neutral {
            color: #8592a3;
        }


        .spinner-border-sm {
            width: 1.5rem;
            height: 1.5rem;
        }

        .filter-badge {
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
            border: 1px solid rgba(105, 108, 255, 0.2);
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-flex;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #8592a3;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8592a3;
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .filter-container {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold py-3 mb-1">
                    <span class="text-muted fw-light">Admin /</span> Aktivitas Sistem
                </h4>
                <p class="text-muted mb-0">Monitor dan analisis aktivitas sistem</p>
            </div>

        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-gradient-primary rounded">
                                    <i class='bx bx-list-ul'></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Aktivitas</span>
                        <h3 class="card-title mb-2" id="total-activities">{{ number_format($stats['total']) }}</h3>
                        <small class="text-success fw-semibold">
                            <i class='bx bx-up-arrow-alt'></i> Semua periode
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-gradient-success rounded">
                                    <i class='bx bx-calendar-plus'></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Hari Ini</span>
                        <h3 class="card-title mb-2" id="today-activities">{{ number_format($stats['today']) }}</h3>
                        <div class="trend-indicator">
                            @if ($stats['recent_trend']['direction'] === 'up')
                                <i class='bx bx-chevron-up trend-up'></i>
                                <small class="trend-up fw-semibold">
                                    +{{ $stats['recent_trend']['percentage'] }}% dari kemarin
                                </small>
                            @elseif($stats['recent_trend']['direction'] === 'down')
                                <i class='bx bx-chevron-down trend-down'></i>
                                <small class="trend-down fw-semibold">
                                    -{{ $stats['recent_trend']['percentage'] }}% dari kemarin
                                </small>
                            @else
                                <i class='bx bx-minus trend-neutral'></i>
                                <small class="trend-neutral fw-semibold">
                                    {{ $stats['recent_trend']['percentage'] }}% dari kemarin
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-gradient-warning rounded">
                                    <i class='bx bx-calendar-week'></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Minggu Ini</span>
                        <h3 class="card-title mb-2" id="week-activities">{{ number_format($stats['this_week']) }}</h3>
                        <small class="text-muted fw-semibold">
                            <i class='bx bx-calendar-week'></i> 7 hari terakhir
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-initial bg-gradient-info rounded">
                                    <i class='bx bx-calendar'></i>
                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Bulan Ini</span>
                        <h3 class="card-title mb-2" id="month-activities">{{ number_format($stats['this_month']) }}</h3>
                        @if ($stats['most_active_action'])
                            <small class="text-primary fw-semibold">
                                <i class='bx bx-trending-up'></i>
                                Terbanyak: {{ ucwords($stats['most_active_action']->aksi) }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class='bx bx-filter-alt me-2'></i>Filter Aktivitas
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleFilterCollapse()">
                        <i class='bx bx-chevron-down me-1' id="filter-toggle-icon"></i>
                        <span id="filter-toggle-text">Tampilkan Filter</span>
                    </button>
                </div>
            </div>
            <div id="filterCollapse">
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-medium">
                                <i class='bx bx-search me-1'></i>Pencarian Global
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class='bx bx-search'></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama, NIM, aksi, keterangan..." value="{{ request('search') }}"
                                    id="search-input">
                                @if (request('search'))
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class='bx bx-x'></i>
                                    </button>
                                @endif
                            </div>
                            <small class="text-muted">Pencarian otomatis saat mengetik</small>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-medium">
                                <i class='bx bx-file me-1'></i>Jenis Surat
                            </label>
                            <select class="form-select" name="surat_type" id="surat-type-filter">
                                <option value="all">Semua Jenis</option>
                                @foreach ($filterOptions['surat_types'] as $type)
                                    <option value="{{ $type['value'] }}"
                                        {{ request('surat_type') == $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-medium">
                                <i class='bx bx-task me-1'></i>Status Aksi
                            </label>
                            <select class="form-select" name="aksi" id="aksi-filter">
                                <option value="all">Semua Aksi</option>
                                @foreach ($filterOptions['actions'] as $action)
                                    <option value="{{ $action['value'] }}"
                                        {{ request('aksi') == $action['value'] ? 'selected' : '' }}>
                                        {{ $action['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-medium">
                                <i class='bx bx-calendar me-1'></i>Periode Waktu
                            </label>
                            <select class="form-select" name="date_range" id="date-range-filter">
                                <option value="">Pilih Periode</option>
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>
                                    Hari Ini
                                </option>
                                <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>
                                    Kemarin
                                </option>
                                <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>
                                    Minggu Ini
                                </option>
                                <option value="last_week" {{ request('date_range') == 'last_week' ? 'selected' : '' }}>
                                    Minggu Lalu
                                </option>
                                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>
                                    Bulan Ini
                                </option>
                                <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>
                                    Bulan Lalu
                                </option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6" id="custom-date-container" style="display: none;">
                            <label class="form-label fw-medium">
                                <i class='bx bx-calendar-range me-1'></i>Custom Date Range
                            </label>
                            <input type="text" class="form-control" name="custom_range" id="custom-date-range"
                                placeholder="Pilih rentang tanggal...">
                        </div>

                        <!-- Filter Action Buttons -->
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                        <i class='bx bx-filter me-1'></i>Terapkan Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearAllFilters()">
                                        <i class='bx bx-refresh me-1'></i>Reset Filter
                                    </button>
                                </div>

                                <!-- Quick Filter Buttons -->
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="quickFilter('today')">
                                        <i class='bx bx-calendar-plus me-1'></i>Hari Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm"
                                        onclick="quickFilter('this_week')">
                                        <i class='bx bx-calendar-week me-1'></i>Minggu Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                        onclick="quickFilter('this_month')">
                                        <i class='bx bx-calendar me-1'></i>Bulan Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Active Filters Display -->
                    <div id="active-filters" class="mt-4" style="display: none;">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <small class="text-muted fw-medium">Filter aktif:</small>
                            <div id="filter-badges" class="d-flex flex-wrap gap-1"></div>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearAllFilters()">
                                <i class='bx bx-x'></i> Hapus Semua
                            </button>
                        </div>
                    </div>

                    <!-- Filter Summary -->
                    <div id="filter-summary" class="mt-3 p-3 bg-light rounded" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Hasil filter:</small>
                                <span class="fw-bold ms-2" id="filter-result-count">0 aktivitas ditemukan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities Table -->
        <div class="card table-container">
            <div class="card-header table-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class='bx bx-list-ul me-2'></i>Daftar Aktivitas
                    </h5>
                </div>
            </div>

            <div class="card-body p-0 position-relative">

                <div id="activities-container">
                    @include('admin.activities.partials.activities-table', ['activities' => $activities])
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script>
            let searchTimeout;
            let currentFilters = {};
            let isRealTimeSearch = true;

            document.addEventListener('DOMContentLoaded', function() {
                initializeFilters();
                updateActiveFilters();
                updateFilterSummary();

                // Real-time search (optional)
                document.getElementById('search-input').addEventListener('input', function() {
                    if (isRealTimeSearch) {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            applyFilters();
                        }, 500);
                    }
                });

                // Auto refresh setiap 2 menit
                setInterval(refreshStats, 120000);
            });

            function initializeFilters() {
                const form = document.getElementById('filter-form');
                const formData = new FormData(form);

                for (let [key, value] of formData.entries()) {
                    if (value && value !== 'all' && value !== '') {
                        currentFilters[key] = value;
                    }
                }
            }


            function applyFilters() {
                showLoading();

                const form = document.getElementById('filter-form');
                const formData = new FormData(form);

                // Update current filters
                currentFilters = {};
                for (let [key, value] of formData.entries()) {
                    if (value && value !== 'all' && value !== '') {
                        currentFilters[key] = value;
                    }
                }

                // Build query string
                const params = new URLSearchParams(currentFilters);

                fetch(`${window.location.pathname}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('activities-container').innerHTML = data.html;

                        updateActiveFilters();
                        updateFilterSummary(data.total);
                        hideLoading();

                        // Update URL without page reload
                        const newUrl = `${window.location.pathname}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Show success message
                        showToast(`Filter berhasil diterapkan. ${data.total} aktivitas ditemukan.`, 'success');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        hideLoading();
                        showToast('Terjadi kesalahan saat menerapkan filter', 'error');
                    });
            }

            function quickFilter(period) {
                // Clear all filters first
                document.getElementById('filter-form').reset();
                currentFilters = {};

                // Set the period filter
                document.getElementById('date-range-filter').value = period;
                currentFilters.date_range = period;

                // Apply the filter
                applyFilters();
            }

            function clearSearch() {
                document.getElementById('search-input').value = '';
                delete currentFilters.search;
                applyFilters();
            }

            function updateActiveFilters() {
                const activeFiltersDiv = document.getElementById('active-filters');
                const filterBadges = document.getElementById('filter-badges');

                filterBadges.innerHTML = '';

                if (Object.keys(currentFilters).length === 0) {
                    activeFiltersDiv.style.display = 'none';
                    return;
                }

                activeFiltersDiv.style.display = 'block';

                Object.entries(currentFilters).forEach(([key, value]) => {
                    if (value && value !== 'all') {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-primary me-1 mb-1';
                        badge.style.cursor = 'pointer';
                        badge.innerHTML = `
                    ${getFilterLabel(key)}: ${getFilterValueLabel(key, value)}
                    <i class='bx bx-x ms-1' onclick="removeFilter('${key}')" title="Hapus filter ini"></i>
                `;
                        filterBadges.appendChild(badge);
                    }
                });
            }

            function updateFilterSummary(totalResults = null) {
                const summaryDiv = document.getElementById('filter-summary');
                const countElement = document.getElementById('filter-result-count');

                if (Object.keys(currentFilters).length === 0) {
                    summaryDiv.style.display = 'none';
                    return;
                }

                summaryDiv.style.display = 'block';

                if (totalResults !== null) {
                    countElement.textContent = `${totalResults.toLocaleString()} aktivitas ditemukan`;
                }
            }

            function getFilterLabel(key) {
                const labels = {
                    'search': 'Pencarian',
                    'surat_type': 'Jenis Surat',
                    'aksi': 'Aksi',
                    'date_range': 'Periode',
                    'date_from': 'Dari Tanggal',
                    'date_to': 'Sampai Tanggal'
                };
                return labels[key] || key;
            }

            function getFilterValueLabel(key, value) {
                if (key === 'surat_type') {
                    const select = document.getElementById('surat-type-filter');
                    const option = select.querySelector(`option[value="${value}"]`);
                    return option ? option.textContent : value;
                }

                if (key === 'aksi') {
                    const select = document.getElementById('aksi-filter');
                    const option = select.querySelector(`option[value="${value}"]`);
                    return option ? option.textContent : value;
                }

                if (key === 'date_range') {
                    const select = document.getElementById('date-range-filter');
                    const option = select.querySelector(`option[value="${value}"]`);
                    return option ? option.textContent : value;
                }

                return value;
            }

            function removeFilter(key) {
                delete currentFilters[key];

                // Reset form field
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'select-one') {
                        field.value = field.querySelector('option[value="all"]') ? 'all' : '';
                    } else {
                        field.value = '';
                    }
                }

                applyFilters();
            }

            function clearAllFilters() {
                currentFilters = {};
                document.getElementById('filter-form').reset();

                // Reset select elements to 'all'
                document.querySelectorAll('select[name]').forEach(select => {
                    const allOption = select.querySelector('option[value="all"]');
                    if (allOption) {
                        select.value = 'all';
                    } else {
                        select.value = '';
                    }
                });

                // Hide custom date range
                document.getElementById('custom-date-container').style.display = 'none';

                applyFilters();
            }

            // Ganti function toggleFilterCollapse() dengan ini:
            function toggleFilterCollapse() {
                const collapse = document.getElementById('filterCollapse');
                const icon = document.getElementById('filter-toggle-icon');
                const text = document.getElementById('filter-toggle-text');

                if (collapse.style.display === 'none') {
                    collapse.style.display = 'block';
                    icon.className = 'bx bx-chevron-up me-1';
                    text.textContent = 'Sembunyikan Filter';
                } else {
                    collapse.style.display = 'none';
                    icon.className = 'bx bx-chevron-down me-1';
                    text.textContent = 'Tampilkan Filter';
                }
            }

            function toggleRealTimeSearch() {
                isRealTimeSearch = !isRealTimeSearch;
                const button = event.target;

                if (isRealTimeSearch) {
                    button.innerHTML = '<i class="bx bx-pause me-1"></i>Matikan Auto Search';
                    button.className = 'btn btn-outline-warning btn-sm';
                    showToast('Pencarian otomatis diaktifkan', 'info');
                } else {
                    button.innerHTML = '<i class="bx bx-play me-1"></i>Aktifkan Auto Search';
                    button.className = 'btn btn-outline-success btn-sm';
                    showToast('Pencarian otomatis dimatikan', 'info');
                }
            }

            function saveCurrentFilter() {
                const filterName = prompt('Masukkan nama untuk menyimpan filter ini:');
                if (filterName) {
                    const savedFilters = JSON.parse(localStorage.getItem('savedFilters') || '{}');
                    savedFilters[filterName] = currentFilters;
                    localStorage.setItem('savedFilters', JSON.stringify(savedFilters));
                    showToast(`Filter "${filterName}" berhasil disimpan`, 'success');
                }
            }



            function showLoading() {
                const loadingHtml = `
            <div class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary me-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span>Memproses filter...</span>
            </div>
        `;
                document.getElementById('activities-container').innerHTML = loadingHtml;
            }

            function hideLoading() {
                // Loading will be replaced by actual content in applyFilters()
            }

            function refreshStats() {
                fetch(`${window.location.pathname}?type=stats`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => updateStats(data))
                    .catch(error => console.error('Error refreshing stats:', error));
            }

            function updateStats(stats) {
                document.getElementById('total-activities').textContent = stats.total.toLocaleString();
                document.getElementById('today-activities').textContent = stats.today.toLocaleString();
                document.getElementById('week-activities').textContent = stats.this_week.toLocaleString();
                document.getElementById('month-activities').textContent = stats.this_month.toLocaleString();
            }

            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
            <div class="alert-body">
                ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }
        </script>
    @endpush
@endsection
