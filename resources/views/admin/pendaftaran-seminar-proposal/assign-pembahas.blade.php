{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/assign-pembahas.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Penentuan Pembahas')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}">Pendaftaran Seminar Proposal</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal) }}">Detail</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Penentuan Pembahas</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Penentuan Dosen Pembahas</h4>
                <p class="text-muted mb-0">Pilih 3 dosen pembahas untuk seminar proposal</p>
            </div>
            <a href="{{ route('admin.pendaftaran-seminar-proposal.show', $pendaftaranSeminarProposal) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Messages --}}
        @if ($hasSurat && !$isSigned)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bx bx-info-circle fs-4 me-2"></i>
                <div>
                    <h6 class="alert-heading mb-1">Perhatian!</h6>
                    <div class="alert-text">Surat usulan sudah digenerate. Mengubah pembahas akan menghapus surat dan perlu
                        generate ulang.</div>
                </div>
            </div>
        @endif

        @if ($isSigned)
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div>
                    <h6 class="alert-heading mb-1">Tidak Dapat Diubah!</h6>
                    <div class="alert-text">Pembahas tidak dapat diubah karena surat sudah ditandatangani.</div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Info & Stats --}}
            <div class="col-xl-4 col-lg-5 mb-4">
                {{-- Mahasiswa Info Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($pendaftaranSeminarProposal->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $pendaftaranSeminarProposal->user->name }}</h5>
                                <small class="text-muted">{{ $pendaftaranSeminarProposal->user->nim }}</small>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Angkatan</span>
                            <span class="badge bg-label-primary">{{ $pendaftaranSeminarProposal->angkatan }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">IPK</span>
                            <span
                                class="badge bg-label-success">{{ number_format($pendaftaranSeminarProposal->ipk, 2) }}</span>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Judul Skripsi</small>
                            <p class="mb-0 small fw-medium">{{ $pendaftaranSeminarProposal->judul_skripsi }}</p>
                        </div>

                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">Dosen Pembimbing</small>
                            <div class="d-flex align-items-center">
                                <i class="bx bx-user-pin text-primary fs-5 me-2"></i>
                                <span class="fw-medium">{{ $pendaftaranSeminarProposal->dosenPembimbing->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guidelines Card - REPLACED STATISTICS --}}
                <div class="card mb-4 border-primary">
                    <div class="card-header mb-3 bg-label-primary">
                        <h6 class="card-title mb-0 text-primary">
                            <i class="bx bx-info-circle me-1"></i>Panduan Pemilihan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <span
                                        class="badge badge-center rounded-pill bg-label-primary w-px-30 h-px-30 me-2">1</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Pilih 3 Dosen Berbeda</h6>
                                    <p class="small text-muted mb-0">Pastikan semua pembahas adalah dosen yang berbeda</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <span
                                        class="badge badge-center rounded-pill bg-label-success w-px-30 h-px-30 me-2">2</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Perhatikan Beban Kerja</h6>
                                    <p class="small text-muted mb-0">Pilih dosen dengan beban kerja yang seimbang</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <span
                                        class="badge badge-center rounded-pill bg-label-info w-px-30 h-px-30 me-2">3</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Gunakan Rekomendasi</h6>
                                    <p class="small text-muted mb-0">Sistem akan merekomendasikan dosen dengan beban
                                        terendah</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <span
                                        class="badge badge-center rounded-pill bg-label-warning w-px-30 h-px-30 me-2">4</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Tidak Boleh Pembimbing</h6>
                                    <p class="small text-muted mb-0">Dosen pembimbing tidak dapat menjadi pembahas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Selection Summary - NEW --}}
                <div class="card" id="selectionSummary" style="display: none;">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-check-square me-1"></i>Pembahas Terpilih
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2" id="summary-pembahas-1" style="display: none;">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">1</span>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Pembahas 1</small>
                                    <strong class="pembahas-name"></strong>
                                </div>
                                <span class="badge bg-label-primary pembahas-beban"></span>
                            </div>
                        </div>

                        <div class="mb-2" id="summary-pembahas-2" style="display: none;">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">2</span>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Pembahas 2</small>
                                    <strong class="pembahas-name"></strong>
                                </div>
                                <span class="badge bg-label-info pembahas-beban"></span>
                            </div>
                        </div>

                        <div class="mb-0" id="summary-pembahas-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">3</span>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Pembahas 3</small>
                                    <strong class="pembahas-name"></strong>
                                </div>
                                <span class="badge bg-label-success pembahas-beban"></span>
                            </div>
                        </div>

                        <hr class="my-3" id="summary-divider" style="display: none;">

                        <div class="d-flex justify-content-between align-items-center" id="summary-total"
                            style="display: none;">
                            <span class="text-muted">Total Beban Gabungan</span>
                            <h5 class="mb-0">
                                <span class="badge bg-label-dark" id="total-beban-selected">0</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Form --}}
            <div class="col-xl-8 col-lg-7">
                <form
                    action="{{ route('admin.pendaftaran-seminar-proposal.store-pembahas', $pendaftaranSeminarProposal) }}"
                    method="POST" id="assignPembahasForm">
                    @csrf

                    {{-- Auto Recommendation Card --}}
                    <div class="card mb-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bx bx-bulb text-primary me-1"></i>Rekomendasi Otomatis
                                    </h6>
                                    <small class="text-muted">Pilih 3 dosen dengan beban paling rendah secara
                                        otomatis</small>
                                </div>
                                <button type="button" class="btn btn-primary" id="btnAutoAssign">
                                    <i class="bx bx-sun me-1"></i> Terapkan
                                </button>
                            </div>

                            <div id="autoSuggestionResult" class="mt-3" style="display: none;">
                                <div class="alert alert-success d-flex align-items-start mb-0">
                                    <i class="bx bx-check-circle fs-4 me-2"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-2">Rekomendasi Diterapkan:</h6>
                                        <ul class="mb-0 ps-3" id="suggestionList"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pembahas Selection Cards --}}
                    @foreach ([1 => 'primary', 2 => 'info', 3 => 'success'] as $posisi => $color)
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bx bx-user-check me-1 text-{{ $color }}"></i>
                                    <span class="text-{{ $color }}">Pembahas {{ $posisi }}</span>
                                </h6>
                                @if ($currentPembahas[$posisi])
                                    <span class="badge bg-label-{{ $color }}">
                                        <i class="bx bx-check-circle me-1"></i>Sudah Ditentukan
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <select name="pembahas_{{ $posisi }}_id" id="pembahas_{{ $posisi }}_id"
                                    class="form-select select2 @error('pembahas_' . $posisi . '_id') is-invalid @enderror"
                                    required {{ $isSigned ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Dosen Pembahas {{ $posisi }} --</option>
                                    @foreach ($dosenList as $dosen)
                                        @php
                                            $stat = $pembahasStatistics[$dosen->id] ?? null;
                                            $totalBeban = $stat ? $stat['total_beban'] : 0;
                                            $isSelected =
                                                old(
                                                    'pembahas_' . $posisi . '_id',
                                                    $currentPembahas[$posisi]?->dosen_id,
                                                ) == $dosen->id;
                                        @endphp
                                        <option value="{{ $dosen->id }}" data-beban="{{ $totalBeban }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $dosen->name }} - Beban: {{ $totalBeban }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pembahas_' . $posisi . '_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Selected Dosen Info --}}
                                <div class="mt-3 p-3 bg-lighter rounded" id="info-pembahas-{{ $posisi }}"
                                    style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 dosen-name"></h6>
                                            <small class="text-muted">
                                                Total Beban: <span
                                                    class="badge bg-label-{{ $color }} total-beban"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Submit Card --}}
                    @if (!$isSigned)
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $hasSurat ? 'Update Penentuan Pembahas' : 'Konfirmasi Penentuan Pembahas' }}
                                        </h6>
                                        <small class="text-muted">
                                            @if ($hasSurat)
                                                Surat akan dihapus dan perlu digenerate ulang
                                            @else
                                                Pastikan semua pembahas sudah dipilih dengan benar
                                            @endif
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>
                                        {{ $hasSurat ? 'Update' : 'Simpan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>

                {{-- Statistics Table Card --}}
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-list-ul me-1"></i>Daftar Beban Dosen
                        </h5>
                        <button type="button" class="btn btn-sm btn-label-primary" id="btnRefreshStats">
                            <i class="bx bx-refresh"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tableStatistics">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="60%">Nama Dosen</th>
                                    <th width="20%" class="text-center">Total Beban</th>
                                    <th width="15%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembahasStatistics as $stat)
                                    @php
                                        $maxBeban = collect($pembahasStatistics)->max('total_beban');
                                        $minBeban = collect($pembahasStatistics)->min('total_beban');
                                        $totalBeban = $stat['total_beban'];

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
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownAutoWidth: true
            });

            // Update selection summary
            function updateSelectionSummary() {
                let selectedCount = 0;
                let totalBeban = 0;

                for (let i = 1; i <= 3; i++) {
                    const $select = $(`#pembahas_${i}_id`);
                    const selectedOption = $select.find(':selected');
                    const $summary = $(`#summary-pembahas-${i}`);

                    if (selectedOption.val()) {
                        const dosenName = selectedOption.text().split(' - ')[0];
                        const beban = parseInt(selectedOption.data('beban')) || 0;

                        $summary.find('.pembahas-name').text(dosenName);
                        $summary.find('.pembahas-beban').text(`Beban: ${beban}`);
                        $summary.fadeIn();

                        selectedCount++;
                        totalBeban += beban;
                    } else {
                        $summary.fadeOut();
                    }
                }

                // Show/hide summary card
                if (selectedCount > 0) {
                    $('#selectionSummary').fadeIn();

                    if (selectedCount === 3) {
                        $('#summary-divider, #summary-total').fadeIn();
                        $('#total-beban-selected').text(totalBeban);
                    } else {
                        $('#summary-divider, #summary-total').fadeOut();
                    }
                } else {
                    $('#selectionSummary').fadeOut();
                }
            }

            // Update dosen info when selected
            function updateDosenInfo(selectId, infoId, posisi) {
                const $select = $(`#${selectId}`);
                const $info = $(`#${infoId}`);
                const selectedOption = $select.find(':selected');

                if (selectedOption.val()) {
                    const dosenName = selectedOption.text().split(' - ')[0];
                    const totalBeban = selectedOption.data('beban');

                    $info.find('.dosen-name').text(dosenName);
                    $info.find('.total-beban').text(totalBeban);
                    $info.slideDown(300);
                } else {
                    $info.slideUp(300);
                }

                // Update summary
                updateSelectionSummary();
            }

            // Bind events
            for (let i = 1; i <= 3; i++) {
                $(`#pembahas_${i}_id`).on('change', function() {
                    updateDosenInfo(`pembahas_${i}_id`, `info-pembahas-${i}`, i);
                });
            }

            // Auto assign recommendation
            $('#btnAutoAssign').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

                const dosenData = [];
                $('#pembahas_1_id option').each(function() {
                    if ($(this).val()) {
                        dosenData.push({
                            id: $(this).val(),
                            name: $(this).text().split(' - ')[0],
                            beban: parseInt($(this).data('beban')) || 0
                        });
                    }
                });

                dosenData.sort((a, b) => a.beban - b.beban);
                const recommended = dosenData.slice(0, 3);

                if (recommended.length >= 3) {
                    $('#pembahas_1_id').val(recommended[0].id).trigger('change');
                    $('#pembahas_2_id').val(recommended[1].id).trigger('change');
                    $('#pembahas_3_id').val(recommended[2].id).trigger('change');

                    const $list = $('#suggestionList');
                    $list.empty();
                    recommended.forEach((dosen, idx) => {
                        $list.append(`
                        <li class="mb-1">
                            <strong>Pembahas ${idx + 1}:</strong> ${dosen.name} 
                            <span class="badge bg-success">Beban: ${dosen.beban}</span>
                        </li>
                    `);
                    });
                    $('#autoSuggestionResult').slideDown(400);

                    // Highlight selection summary
                    $('#selectionSummary').addClass('border-success');
                    setTimeout(() => {
                        $('#selectionSummary').removeClass('border-success');
                    }, 3000);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Rekomendasi otomatis telah diterapkan',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak cukup dosen untuk rekomendasi'
                    });
                }

                $btn.prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i> Terapkan');
            });

            // Form validation
            $('#assignPembahasForm').on('submit', function(e) {
                e.preventDefault();

                const pembahas1 = $('#pembahas_1_id').val();
                const pembahas2 = $('#pembahas_2_id').val();
                const pembahas3 = $('#pembahas_3_id').val();

                if (!pembahas1 || !pembahas2 || !pembahas3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Semua pembahas harus dipilih!'
                    });
                    return false;
                }

                if (pembahas1 === pembahas2 || pembahas1 === pembahas3 || pembahas2 === pembahas3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Tidak boleh memilih dosen yang sama!'
                    });
                    return false;
                }

                const pembahas1Name = $('#pembahas_1_id option:selected').text().split(' - ')[0];
                const pembahas2Name = $('#pembahas_2_id option:selected').text().split(' - ')[0];
                const pembahas3Name = $('#pembahas_3_id option:selected').text().split(' - ')[0];

                Swal.fire({
                    title: 'Konfirmasi Penentuan Pembahas',
                    html: `
                    <div class="text-start">
                        <p class="mb-3"><strong>Pembahas yang dipilih:</strong></p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="badge bg-label-primary me-2">1</span>
                                ${pembahas1Name}
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-label-info me-2">2</span>
                                ${pembahas2Name}
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-label-success me-2">3</span>
                                ${pembahas3Name}
                            </li>
                        </ul>
                        @if ($hasSurat && !$isSigned)
                            <div class="alert alert-warning mt-3 mb-0">
                                <small><i class="bx bx-info-circle me-1"></i>Surat akan dihapus dan perlu digenerate ulang!</small>
                            </div>
                        @endif
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bx bx-save me-1"></i> Ya, Simpan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            html: '<div class="spinner-border text-primary"></div>',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        this.submit();
                    }
                });
            });

            // Real-time duplicate validation
            $('select[name^="pembahas"]').on('change', function() {
                const values = [];
                $('select[name^="pembahas"]').each(function() {
                    const val = $(this).val();
                    if (val) values.push(val);
                });

                const hasDuplicates = values.length !== new Set(values).size;

                $('select[name^="pembahas"]').removeClass('is-invalid');
                $('.invalid-feedback-duplicate').remove();

                if (hasDuplicates) {
                    $('select[name^="pembahas"]').each(function() {
                        const val = $(this).val();
                        if (val && values.filter(v => v === val).length > 1) {
                            $(this).addClass('is-invalid');
                            if (!$(this).next('.invalid-feedback-duplicate').length) {
                                $(this).after(`
                                <div class="invalid-feedback invalid-feedback-duplicate d-block">
                                    <i class="bx bx-error me-1"></i>Dosen ini sudah dipilih!
                                </div>
                            `);
                            }
                        }
                    });
                }
            });

            // Refresh stats
            $('#btnRefreshStats').on('click', function() {
                const $btn = $(this);
                $btn.html('<span class="spinner-border spinner-border-sm"></span>');
                setTimeout(() => location.reload(), 500);
            });

            // DataTable initialization
            if (typeof $.fn.DataTable !== 'undefined') {
                $('#tableStatistics').DataTable({
                    "order": [
                        [2, "asc"]
                    ],
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                    },
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    }]
                });
            }

            // Trigger initial display
            $('#pembahas_1_id').trigger('change');
            $('#pembahas_2_id').trigger('change');
            $('#pembahas_3_id').trigger('change');
        });
    </script>
@endpush

@push('styles')
    <style>
        .bg-lighter {
            background-color: rgba(67, 89, 113, 0.04);
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 40px;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
        }

        .avatar-lg {
            width: 3.5rem;
            height: 3.5rem;
        }

        .table-hover tbody tr {
            transition: all 0.2s;
        }

        .card {
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            border: none;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.16);
        }

        .card.border-success {
            border: 2px solid #71dd37 !important;
            transition: all 0.3s;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            padding: 0.4em 0.65em;
            font-weight: 500;
        }

        .badge-center {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .w-px-30 {
            width: 30px !important;
        }

        .h-px-30 {
            height: 30px !important;
        }

        .invalid-feedback-duplicate {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Smooth fade animations */
        #selectionSummary,
        #summary-pembahas-1,
        #summary-pembahas-2,
        #summary-pembahas-3,
        #summary-divider,
        #summary-total {
            transition: all 0.3s ease-in-out;
        }

        /* Guidelines card styling */
        .card.border-primary {
            border-width: 1px !important;
        }

        .bg-label-primary {
            background-color: rgba(105, 108, 255, 0.12) !important;
        }
    </style>
@endpush
