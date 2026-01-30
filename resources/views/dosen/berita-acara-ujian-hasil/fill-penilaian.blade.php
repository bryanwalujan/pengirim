@extends('layouts.admin.app')

@section('title', 'Isi Penilaian Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-9">
                <h4 class="fw-bold py-3 mb-4">
                    <span class="text-muted fw-light">Dosen / Berita Acara /</span> Isi Penilaian
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

        <div class="row">
            <!-- Info Panel -->
            <div class="col-lg-4 mb-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Informasi Mahasiswa</h5>
                        <p class="mb-1"><strong>Nama:</strong><br> {{ $beritaAcara->mahasiswa_name }}</p>
                        <p class="mb-1"><strong>NIM:</strong><br> {{ $beritaAcara->mahasiswa_nim }}</p>
                        <p class="mb-0"><strong>Judul:</strong><br> {{ $beritaAcara->judul_skripsi }}</p>
                    </div>
                </div>

                <!-- Nilai Mutu Card -->
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title text-white mb-2">Nilai Mutu (Skala 4.00)</h5>
                        <div class="display-3 fw-bold mb-0" id="displayNilaiMutu">0.00</div>
                        <div class="display-6 fw-bold" id="displayGrade">-</div>
                        <p class="text-white mt-2 small opacity-75">Rumus: (Total Bobot / 10) × 4</p>
                    </div>
                </div>

                <!-- Panduan Grade -->
                <div class="card">
                    <div class="card-header bg-label-info">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-info-circle me-1"></i> Panduan Nilai Huruf</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nilai Mutu</th>
                                    <th class="text-center">Huruf</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>3.60 - 4.00</td>
                                    <td class="text-center"><span class="badge bg-success">A</span></td>
                                </tr>
                                <tr>
                                    <td>3.00 - 3.59</td>
                                    <td class="text-center"><span class="badge bg-info">B</span></td>
                                </tr>
                                <tr>
                                    <td>2.00 - 2.99</td>
                                    <td class="text-center"><span class="badge bg-warning">C</span></td>
                                </tr>
                                <tr>
                                    <td>1.00 - 1.99</td>
                                    <td class="text-center"><span class="badge bg-danger">D</span></td>
                                </tr>
                                <tr>
                                    <td>0.00 - 0.99</td>
                                    <td class="text-center"><span class="badge bg-dark">E</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Penilaian -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Formulir Penilaian Ujian Hasil</h5>
                        <small class="text-muted">Berikan nilai 0-100 untuk setiap komponen. Total bobot = 10.</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dosen.berita-acara-ujian-hasil.penilaian.store', $beritaAcara) }}"
                            method="POST" id="formPenilaian">
                            @csrf

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered" id="tabelPenilaian">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th width="50%">Komponen</th>
                                            <th width="12%" class="text-center">Bobot</th>
                                            <th width="18%" class="text-center">Nilai (0-100)</th>
                                            <th width="15%" class="text-center">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- A. Kebaruan dan Signifikansi (1.5) -->
                                        <tr>
                                            <td class="text-center align-middle">A</td>
                                            <td class="align-middle">Kebaruan dan Signifikansi Penelitian</td>
                                            <td class="text-center align-middle fw-bold">1.5</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_kebaruan" id="nilai_kebaruan" min="0" max="100"
                                                    step="1" data-bobot="1.5"
                                                    value="{{ old('nilai_kebaruan', $existingPenilaian->nilai_kebaruan ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_kebaruan">-</td>
                                        </tr>

                                        <!-- B. Kesesuaian Judul dst (1.5) -->
                                        <tr>
                                            <td class="text-center align-middle">B</td>
                                            <td class="align-middle">Kesesuaian Judul, Masalah, Tujuan, Pembahasan,
                                                Kesimpulan, dan Saran</td>
                                            <td class="text-center align-middle fw-bold">1.5</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_kesesuaian" id="nilai_kesesuaian" min="0"
                                                    max="100" step="1" data-bobot="1.5"
                                                    value="{{ old('nilai_kesesuaian', $existingPenilaian->nilai_kesesuaian ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_kesesuaian">-</td>
                                        </tr>

                                        <!-- C. Metode Penelitian (1) -->
                                        <tr>
                                            <td class="text-center align-middle">C</td>
                                            <td class="align-middle">Metode Penelitian dan Pemecahan Masalah (Metode dan
                                                Algoritma)</td>
                                            <td class="text-center align-middle fw-bold">1</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_metode" id="nilai_metode" min="0" max="100"
                                                    step="1" data-bobot="1"
                                                    value="{{ old('nilai_metode', $existingPenilaian->nilai_metode ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_metode">-</td>
                                        </tr>

                                        <!-- D. Kajian Teori (1) -->
                                        <tr>
                                            <td class="text-center align-middle">D</td>
                                            <td class="align-middle">Kajian Teori</td>
                                            <td class="text-center align-middle fw-bold">1</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_kajian_teori" id="nilai_kajian_teori" min="0"
                                                    max="100" step="1" data-bobot="1"
                                                    value="{{ old('nilai_kajian_teori', $existingPenilaian->nilai_kajian_teori ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_kajian_teori">-
                                            </td>
                                        </tr>

                                        <!-- E. Hasil Penelitian (3) -->
                                        <tr class="table-warning">
                                            <td class="text-center align-middle">E</td>
                                            <td class="align-middle">
                                                Hasil Penelitian (Kesesuaian dengan Metode/Hasil)
                                                <span class="badge bg-warning text-dark ms-1">Bobot Tertinggi</span>
                                            </td>
                                            <td class="text-center align-middle fw-bold">3</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_hasil_penelitian" id="nilai_hasil_penelitian"
                                                    min="0" max="100" step="1" data-bobot="3"
                                                    value="{{ old('nilai_hasil_penelitian', $existingPenilaian->nilai_hasil_penelitian ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_hasil_penelitian">
                                                -</td>
                                        </tr>

                                        <!-- F. Referensi (1) -->
                                        <tr>
                                            <td class="text-center align-middle">F</td>
                                            <td class="align-middle">Referensi</td>
                                            <td class="text-center align-middle fw-bold">1</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_referensi" id="nilai_referensi" min="0"
                                                    max="100" step="1" data-bobot="1"
                                                    value="{{ old('nilai_referensi', $existingPenilaian->nilai_referensi ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_referensi">-</td>
                                        </tr>

                                        <!-- G. Tata Bahasa (1) -->
                                        <tr>
                                            <td class="text-center align-middle">G</td>
                                            <td class="align-middle">Tata Bahasa</td>
                                            <td class="text-center align-middle fw-bold">1</td>
                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center nilai-input"
                                                    name="nilai_tata_bahasa" id="nilai_tata_bahasa" min="0"
                                                    max="100" step="1" data-bobot="1"
                                                    value="{{ old('nilai_tata_bahasa', $existingPenilaian->nilai_tata_bahasa ?? 75) }}"
                                                    oninput="updateCalculation()" required>
                                            </td>
                                            <td class="text-center align-middle fw-bold" id="kontribusi_tata_bahasa">-
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-primary">
                                        <tr>
                                            <td colspan="2" class="fw-bold text-end">Total</td>
                                            <td class="text-center fw-bold">10</td>
                                            <td class="text-center fw-bold">-</td>
                                            <td class="text-center fw-bold" id="totalKontribusi">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Rumus Info -->
                            <div class="alert alert-light border mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bx bx-calculator fs-3 text-primary"></i>
                                    <div>
                                        <strong>Rumus Perhitungan:</strong><br>
                                        <code>Nilai Skripsi = (Total Kontribusi / 10) × 4</code>
                                        <span class="text-muted ms-2">→ Hasil: <strong
                                                id="rumusHasil">0.00</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan untuk mahasiswa...">{{ old('catatan', $existingPenilaian->catatan ?? '') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bx bx-save me-1"></i> Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Komponen bobot mapping
        const komponenBobot = {
            nilai_kebaruan: 1.5,
            nilai_kesesuaian: 1.5,
            nilai_metode: 1,
            nilai_kajian_teori: 1,
            nilai_hasil_penelitian: 3,
            nilai_referensi: 1,
            nilai_tata_bahasa: 1
        };

        function clampValue(input) {
            let nilai = parseInt(input.value);
            if (isNaN(nilai)) nilai = 0;
            if (nilai < 0) nilai = 0;
            if (nilai > 100) nilai = 100;
            input.value = nilai;
            return nilai;
        }

        function updateCalculation() {
            let totalKontribusi = 0;

            // Calculate each component's contribution
            Object.keys(komponenBobot).forEach(key => {
                const input = document.getElementById(key);
                // Clamp value BEFORE calculation
                const nilai = clampValue(input);
                const bobot = komponenBobot[key];

                // Contribution = (nilai / 100) * bobot
                const kontribusi = (nilai / 100) * bobot;
                totalKontribusi += kontribusi;

                // Update contribution display
                const kontribusiEl = document.getElementById('kontribusi_' + key.replace('nilai_', ''));
                if (kontribusiEl) {
                    kontribusiEl.innerText = kontribusi.toFixed(2);
                }
            });

            // Update total contribution
            document.getElementById('totalKontribusi').innerText = totalKontribusi.toFixed(2);

            // Calculate nilai mutu: (total / 10) * 4
            const nilaiMutu = (totalKontribusi / 10) * 4;
            document.getElementById('displayNilaiMutu').innerText = nilaiMutu.toFixed(2);
            document.getElementById('rumusHasil').innerText = nilaiMutu.toFixed(2);

            // Determine Grade based on 4.0 scale
            let grade = 'E';
            let gradeClass = 'bg-dark';

            if (nilaiMutu >= 3.60) {
                grade = 'A';
                gradeClass = 'text-success';
            } else if (nilaiMutu >= 3.00) {
                grade = 'B';
                gradeClass = 'text-info';
            } else if (nilaiMutu >= 2.00) {
                grade = 'C';
                gradeClass = 'text-warning';
            } else if (nilaiMutu >= 1.00) {
                grade = 'D';
                gradeClass = 'text-danger';
            } else {
                grade = 'E';
                gradeClass = 'text-dark';
            }

            const gradeEl = document.getElementById('displayGrade');
            gradeEl.innerText = grade;
            gradeEl.className = 'display-6 fw-bold ' + gradeClass;
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            // Add blur event to clamp values when leaving input
            document.querySelectorAll('.nilai-input').forEach(input => {
                input.addEventListener('blur', function() {
                    clampValue(this);
                    updateCalculation();
                });

                // Prevent typing beyond 3 digits
                input.addEventListener('keydown', function(e) {
                    // Allow: backspace, delete, tab, escape, enter, arrows
                    if ([8, 9, 13, 27, 46, 37, 38, 39, 40].includes(e.keyCode)) return;
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;
                    // Block if not a number
                    if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                // Immediate clamp on paste
                input.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        clampValue(this);
                        updateCalculation();
                    }, 10);
                });
            });

            updateCalculation();
        });

        // Prevent form submission if values are invalid
        document.getElementById('formPenilaian').addEventListener('submit', function(e) {
            const inputs = document.querySelectorAll('.nilai-input');
            let valid = true;

            inputs.forEach(input => {
                clampValue(input);
                const val = parseInt(input.value);
                if (isNaN(val) || val < 0 || val > 100) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Pastikan semua nilai berada dalam rentang 0-100');
            }
        });
    </script>
@endpush
