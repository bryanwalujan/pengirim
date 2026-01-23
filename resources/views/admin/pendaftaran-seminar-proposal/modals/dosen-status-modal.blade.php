{{-- Modal: Status Dosen Pembahas --}}
<div class="modal fade" id="modalDosenStatus" tabindex="-1" aria-labelledby="modalDosenStatusLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-label-info">
                <h5 class="modal-title" id="modalDosenStatusLabel">
                    <i class="bx bx-list-ul me-2"></i>Status Beban Dosen Pembahas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Info Banner --}}
                <div class="alert alert-info d-flex align-items-start mb-3">
                    <i class="bx bx-info-circle fs-4 me-2"></i>
                    <div>
                        <strong>Informasi:</strong> Tabel ini menampilkan beban kerja semua dosen sebagai pembahas seminar proposal. 
                        Gunakan informasi ini untuk membantu menentukan pembahas yang seimbang.
                    </div>
                </div>

                {{-- Filter Tabs --}}
                <ul class="nav nav-pills mb-3" id="dosenStatusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="pill"
                            data-bs-target="#all-dosen" type="button" role="tab">
                            <i class="bx bx-user me-1"></i>Semua Dosen
                            <span class="badge bg-label-dark ms-1">{{ count($pembahasStatistics) }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="available-tab" data-bs-toggle="pill"
                            data-bs-target="#available-dosen" type="button" role="tab">
                            <i class="bx bx-user-plus me-1"></i>Beban Rendah
                            <span class="badge bg-label-success ms-1" id="availableCount">0</span>
                        </button>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content" id="dosenStatusTabContent">
                    {{-- All Dosen Tab --}}
                    <div class="tab-pane fade show active" id="all-dosen" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tableAllDosen">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="50%">Nama Dosen</th>
                                        <th width="20%" class="text-center">Total Beban</th>
                                        <th width="25%" class="text-center">Status Beban</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembahasStatistics as $stat)
                                        @php
                                            $maxBeban = collect($pembahasStatistics)->max('total_beban');
                                            $minBeban = collect($pembahasStatistics)->min('total_beban');
                                            $totalBeban = $stat['total_beban'];

                                            // Determine beban status
                                            if ($totalBeban == 0) {
                                                $badgeClass = 'bg-label-secondary';
                                                $statusText = 'Kosong';
                                                $iconClass = 'bx-minus-circle';
                                            } elseif ($totalBeban == $minBeban) {
                                                $badgeClass = 'bg-label-success';
                                                $statusText = 'Rendah';
                                                $iconClass = 'bx-chevron-down';
                                            } elseif ($totalBeban == $maxBeban) {
                                                $badgeClass = 'bg-label-danger';
                                                $statusText = 'Tinggi';
                                                $iconClass = 'bx-chevron-up';
                                            } else {
                                                $badgeClass = 'bg-label-warning';
                                                $statusText = 'Sedang';
                                                $iconClass = 'bx-chevron-right';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-label-dark">
                                                            {{ strtoupper(substr($stat['dosen']->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $stat['dosen']->name }}</h6>
                                                        <small class="text-muted">{{ $stat['dosen']->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <h5 class="mb-0">
                                                    <span class="badge bg-label-dark">{{ $totalBeban }}</span>
                                                </h5>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $badgeClass }}">
                                                    <i class="bx {{ $iconClass }} me-1"></i>{{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Available/Low Workload Dosen Tab --}}
                    <div class="tab-pane fade" id="available-dosen" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tableAvailableDosen">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="55%">Nama Dosen</th>
                                        <th width="20%" class="text-center">Total Beban</th>
                                        <th width="20%" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $availableNo = 1;
                                        $minBeban = collect($pembahasStatistics)->min('total_beban');
                                    @endphp
                                    @foreach ($pembahasStatistics as $stat)
                                        @php
                                            $totalBeban = $stat['total_beban'];
                                            // Only show dosen with minimum workload
                                            $isLowWorkload = ($totalBeban == $minBeban);
                                        @endphp
                                        @if ($isLowWorkload)
                                            <tr class="table-success">
                                                <td>{{ $availableNo++ }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <span class="avatar-initial rounded-circle bg-label-success">
                                                                {{ strtoupper(substr($stat['dosen']->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $stat['dosen']->name }}</h6>
                                                            <small class="text-muted">{{ $stat['dosen']->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <h5 class="mb-0">
                                                        <span class="badge bg-label-dark">{{ $totalBeban }}</span>
                                                    </h5>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-success">
                                                        <i class="bx bx-chevron-down me-1"></i>Rendah
                                                    </span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.pendaftaran-seminar-proposal.export-status-dosen') }}" 
                   class="btn btn-success me-2">
                    <i class="bx bxs-file-export me-1"></i>Export ke Excel
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTables for modal
            if (typeof $.fn.DataTable !== 'undefined') {
                $('#tableAllDosen, #tableAvailableDosen').DataTable({
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                    },
                    "order": [
                        [2, "asc"]
                    ] // Sort by beban (ascending - lowest first)
                });
            }

            // Update badge count for low workload tab
            function updateModalBadgeCounts() {
                const availableCount = $('#tableAvailableDosen tbody tr').length;
                $('#availableCount').text(availableCount);
            }

            // Call on page load
            updateModalBadgeCounts();
        });
    </script>
@endpush
