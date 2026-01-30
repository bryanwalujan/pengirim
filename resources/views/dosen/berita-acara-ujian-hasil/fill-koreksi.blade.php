@extends('layouts.admin.app')

@section('title', 'Isi Lembar Koreksi Skripsi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-9">
                <h4 class="fw-bold py-3 mb-4">
                    <span class="text-muted fw-light">Dosen / Berita Acara /</span> Lembar Koreksi
                </h4>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-secondary mt-3">
                    <i class="bx bx-arrow-back me-1"></i> Batal
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="bx bx-info-circle me-1"></i>
            Sebagai Dosen Pembimbing ({{ $pengujiInfo->pivot->posisi }}), Anda dapat mengisi detail koreksi/perbaikan
            skripsi untuk mahasiswa. <strong>Pengisian lembar koreksi bersifat opsional.</strong>
        </div>

        <div class="card">
            <h5 class="card-header">Formulir Lembar Koreksi (Opsional)</h5>
            <div class="card-body">
                <form action="{{ route('dosen.berita-acara-ujian-hasil.koreksi.store', $beritaAcara) }}" method="POST"
                    id="formKoreksi">
                    @csrf

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="tableKoreksi">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Halaman</th>
                                    <th width="70%">Uraian Koreksi / Catatan Perbaikan</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="koreksiBody">
                                {{-- Rows will be populated by JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <button type="button" class="btn btn-outline-primary" onclick="addRow()">
                            <i class="bx bx-plus me-1"></i> Tambah Baris
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bx bx-save me-1"></i> Simpan Lembar Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let rowCount = 0;
        const existingData = @json($existingKoreksi ? $existingKoreksi->koreksi_collection : []);

        function addRow(data = null) {
            rowCount++;
            const tbody = document.getElementById('koreksiBody');
            const tr = document.createElement('tr');
            tr.id = `row_${rowCount}`;

            const halamanVal = data ? data.halaman : '';
            const catatanVal = data ? data.catatan : '';

            tr.innerHTML = `
            <td class="text-center align-middle row-number"></td>
            <td>
                <input type="text" class="form-control" name="koreksi[${rowCount}][halaman]" 
                       placeholder="Contoh: 12, BAB I" value="${halamanVal}">
            </td>
            <td>
                <textarea class="form-control" name="koreksi[${rowCount}][catatan]" 
                          rows="2" placeholder="Tuliskan koreksi (opsional)...">${catatanVal}</textarea>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-icon btn-label-danger" onclick="removeRow(${rowCount})">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(tr);
            renumberRows();
        }

        function removeRow(id) {
            const row = document.getElementById(`row_${id}`);
            if (row) row.remove();
            renumberRows();
        }

        function renumberRows() {
            const rows = document.querySelectorAll('#koreksiBody tr');
            rows.forEach((row, index) => {
                row.querySelector('.row-number').innerText = index + 1;
            });

            // Validation: If no rows, warn user or add empty row
            if (rows.length === 0) {
                // Optional: addRow() if want to enforce at least one
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (existingData.length > 0) {
                existingData.forEach(item => addRow(item));
            } else {
                // Add one empty row by default
                addRow();
            }
        });
    </script>
@endpush
