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
            <a href="{{ route('dosen.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-secondary mt-3">
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
            
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title text-white mb-2">Total Nilai & Grade</h5>
                    <div class="display-3 fw-bold mb-0 text-center" id="displayTotal">0.00</div>
                    <div class="display-6 fw-bold text-center" id="displayGrade">-</div>
                    <p class="text-white text-center mt-2 small opacity-75">*Dihitung otomatis rata-rata dari 5 kriteria</p>
                </div>
            </div>
        </div>

        <!-- Form Penilaian -->
        <div class="col-lg-8">
            <div class="card">
                <h5 class="card-header">Formulir Penilaian Ujian Hasil</h5>
                <div class="card-body">
                    <form action="{{ route('dosen.berita-acara-ujian-hasil.penilaian.store', $beritaAcara) }}" method="POST" id="formPenilaian">
                        @csrf
                        
                        <!-- Kriteria 1 -->
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>1. Kebaruan (Novelty)</span>
                                <span class="badge bg-label-primary" id="val_kebaruan">0</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" 
                                name="nilai_kebaruan" id="nilai_kebaruan" 
                                value="{{ old('nilai_kebaruan', $existingPenilaian->nilai_kebaruan ?? 75) }}"
                                oninput="updateCalculation()">
                            <div class="form-text">Relevansi dan orisinalitas temuan.</div>
                        </div>

                        <!-- Kriteria 2 -->
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>2. Metode</span>
                                <span class="badge bg-label-primary" id="val_metode">0</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" 
                                name="nilai_metode" id="nilai_metode" 
                                value="{{ old('nilai_metode', $existingPenilaian->nilai_metode ?? 75) }}"
                                oninput="updateCalculation()">
                            <div class="form-text">Ketepatan algoritma/metodologi yang digunakan.</div>
                        </div>

                        <!-- Kriteria 3 -->
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>3. Ketersediaan Data/Resource</span>
                                <span class="badge bg-label-primary" id="val_data">0</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" 
                                name="nilai_data_software" id="nilai_data_software" 
                                value="{{ old('nilai_data_software', $existingPenilaian->nilai_data_software ?? 75) }}"
                                oninput="updateCalculation()">
                            <div class="form-text">Validitas software, hardware, dan dataset.</div>
                        </div>

                        <!-- Kriteria 4 -->
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>4. Referensi</span>
                                <span class="badge bg-label-primary" id="val_referensi">0</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" 
                                name="nilai_referensi" id="nilai_referensi" 
                                value="{{ old('nilai_referensi', $existingPenilaian->nilai_referensi ?? 75) }}"
                                oninput="updateCalculation()">
                            <div class="form-text">Kualitas pustaka (minimal 5 tahun terakhir).</div>
                        </div>

                        <!-- Kriteria 5 -->
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>5. Penguasaan Materi</span>
                                <span class="badge bg-label-primary" id="val_penguasaan">0</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="100" step="1" 
                                name="nilai_penguasaan" id="nilai_penguasaan" 
                                value="{{ old('nilai_penguasaan', $existingPenilaian->nilai_penguasaan ?? 75) }}"
                                oninput="updateCalculation()">
                            <div class="form-text">Performa mahasiswa saat tanya jawab.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan Tambahan (Opsional)</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan untuk mahasiswa...">{{ old('catatan', $existingPenilaian->catatan ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
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
    function updateCalculation() {
        const kebaruan = parseInt(document.getElementById('nilai_kebaruan').value) || 0;
        const metode = parseInt(document.getElementById('nilai_metode').value) || 0;
        const data = parseInt(document.getElementById('nilai_data_software').value) || 0;
        const referensi = parseInt(document.getElementById('nilai_referensi').value) || 0;
        const penguasaan = parseInt(document.getElementById('nilai_penguasaan').value) || 0;

        // Update badges
        document.getElementById('val_kebaruan').innerText = kebaruan;
        document.getElementById('val_metode').innerText = metode;
        document.getElementById('val_data').innerText = data;
        document.getElementById('val_referensi').innerText = referensi;
        document.getElementById('val_penguasaan').innerText = penguasaan;

        // Calculate average
        const total = (kebaruan + metode + data + referensi + penguasaan) / 5;
        const formattedTotal = total.toFixed(2);
        
        document.getElementById('displayTotal').innerText = formattedTotal;

        // Determine Grade
        let grade = 'E';
        if (total >= 85) grade = 'A';
        else if (total >= 80) grade = 'A-';
        else if (total >= 75) grade = 'B+';
        else if (total >= 70) grade = 'B';
        else if (total >= 65) grade = 'B-';
        else if (total >= 60) grade = 'C+';
        else if (total >= 55) grade = 'C';
        else if (total >= 45) grade = 'D';
        
        // Simple 4-scale for general view, adjust based on academic rules if needed
        // Using standard mapping from Model
        if (total >= 85) grade = 'A';
        else if (total >= 75) grade = 'B';
        else if (total >= 65) grade = 'C';
        else if (total >= 55) grade = 'D';
        else grade = 'E';

        document.getElementById('displayGrade').innerText = grade;
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', updateCalculation);
</script>
@endpush
