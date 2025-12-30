{{-- filepath: resources/views/admin/lembar-catatan-sempro/create.blade.php --}}
@extends('layouts.admin.app')

@section('title', $catatan ? 'Edit Lembar Catatan' : 'Isi Lembar Catatan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Lembar Catatan /</span>
                    {{ $catatan ? 'Edit' : 'Isi Baru' }}
                </h4>
                <p class="text-muted mb-0">{{ $mahasiswa->name }} ({{ $mahasiswa->nim }})</p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Info Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="mb-3"><strong>Informasi Proposal</strong></h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Judul</strong></td>
                                <td>: {{ $pendaftaran->judul_skripsi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pembimbing</strong></td>
                                <td>: {{ $pendaftaran->dosenPembimbing->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Ujian</strong></td>
                                <td>: {{ $jadwal->tanggal_ujian->translatedFormat('d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Catatan:</strong><br>
                            <small>
                                • Isi nilai dan catatan sesuai hasil ujian<br>
                                • Data dapat diedit sebelum ditandatangani<br>
                                • Auto-save ke localStorage
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form
            action="{{ $catatan ? route('admin.lembar-catatan-sempro.update', $catatan) : route('admin.lembar-catatan-sempro.store', $beritaAcara) }}"
            method="POST" id="form-lembar-catatan">
            @csrf
            @if ($catatan)
                @method('PUT')
            @endif

            <div class="row">
                {{-- Aspek Penilaian --}}
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-check-square me-2"></i>Aspek Penilaian</h5>
                        </div>
                        <div class="card-body">
                            {{-- Kebaruan Penelitian --}}
                            <div class="mb-3">
                                <label for="catatan_kebaruan" class="form-label fw-bold">
                                    <i class="bx bx-bulb me-1"></i>Kebaruan Penelitian
                                </label>
                                <textarea class="form-control @error('catatan_kebaruan') is-invalid @enderror" 
                                          id="catatan_kebaruan" name="catatan_kebaruan" 
                                          rows="4" 
                                          placeholder="Berikan catatan tentang kebaruan dan orisinalitas penelitian...">{{ old('catatan_kebaruan', $catatan->catatan_kebaruan ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_kebaruan">0</span>/5000</small>
                                <div class="form-text">Catatan tentang aspek kebaruan dan orisinalitas penelitian</div>
                                @error('catatan_kebaruan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Metode Penelitian --}}
                            <div class="mb-3">
                                <label for="catatan_metode" class="form-label fw-bold">
                                    <i class="bx bx-cog me-1"></i>Metode Penelitian
                                </label>
                                <textarea class="form-control @error('catatan_metode') is-invalid @enderror" 
                                          id="catatan_metode" name="catatan_metode" 
                                          rows="4" 
                                          placeholder="Berikan catatan tentang metode penelitian yang digunakan...">{{ old('catatan_metode', $catatan->catatan_metode ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_metode">0</span>/5000</small>
                                <div class="form-text">Catatan tentang metodologi yang digunakan</div>
                                @error('catatan_metode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ketersediaan Data/Software/Hardware --}}
                            <div class="mb-0">
                                <label for="catatan_ketersediaan_data" class="form-label fw-bold">
                                    <i class="bx bx-data me-1"></i>Ketersediaan Data/Software/Hardware
                                </label>
                                <textarea class="form-control @error('catatan_ketersediaan_data') is-invalid @enderror" 
                                          id="catatan_ketersediaan_data" name="catatan_ketersediaan_data" 
                                          rows="4" 
                                          placeholder="Berikan catatan tentang ketersediaan data, software, hardware yang diperlukan...">{{ old('catatan_ketersediaan_data', $catatan->catatan_ketersediaan_data ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_ketersediaan">0</span>/5000</small>
                                <div class="form-text">Catatan tentang ketersediaan dan kesiapan data, software, dan hardware</div>
                                @error('catatan_ketersediaan_data')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan Per Bab --}}
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Catatan Revisi Per Bab</h5>
                        </div>
                        <div class="card-body">
                            {{-- BAB I --}}
                            <div class="mb-3">
                                <label for="catatan_bab1" class="form-label">
                                    <strong>BAB I - Pendahuluan</strong>
                                    <small class="text-muted">(Latar belakang, rumusan masalah, tujuan, manfaat)</small>
                                </label>
                                <textarea class="form-control @error('catatan_bab1') is-invalid @enderror" id="catatan_bab1" name="catatan_bab1"
                                    rows="4" placeholder="Masukkan catatan revisi untuk BAB I...">{{ old('catatan_bab1', $catatan->catatan_bab1 ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_bab1">0</span>/5000</small>
                                @error('catatan_bab1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BAB II --}}
                            <div class="mb-3">
                                <label for="catatan_bab2" class="form-label">
                                    <strong>BAB II - Tinjauan Pustaka</strong>
                                    <small class="text-muted">(Landasan teori, penelitian terdahulu)</small>
                                </label>
                                <textarea class="form-control @error('catatan_bab2') is-invalid @enderror" id="catatan_bab2" name="catatan_bab2"
                                    rows="4" placeholder="Masukkan catatan revisi untuk BAB II...">{{ old('catatan_bab2', $catatan->catatan_bab2 ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_bab2">0</span>/5000</small>
                                @error('catatan_bab2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BAB III --}}
                            <div class="mb-3">
                                <label for="catatan_bab3" class="form-label">
                                    <strong>BAB III - Metodologi Penelitian</strong>
                                    <small class="text-muted">(Metode, desain, teknik analisis)</small>
                                </label>
                                <textarea class="form-control @error('catatan_bab3') is-invalid @enderror" id="catatan_bab3" name="catatan_bab3"
                                    rows="4" placeholder="Masukkan catatan revisi untuk BAB III...">{{ old('catatan_bab3', $catatan->catatan_bab3 ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_bab3">0</span>/5000</small>
                                @error('catatan_bab3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            {{-- Jadwal --}}
                            <div class="mb-3">
                                <label for="catatan_jadwal" class="form-label">
                                    <strong>Jadwal Penelitian</strong>
                                    <small class="text-muted">(Timeline, durasi per tahap)</small>
                                </label>
                                <textarea class="form-control @error('catatan_jadwal') is-invalid @enderror" id="catatan_jadwal"
                                    name="catatan_jadwal" rows="3" placeholder="Catatan terkait jadwal penelitian...">{{ old('catatan_jadwal', $catatan->catatan_jadwal ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_jadwal">0</span>/2000</small>
                                @error('catatan_jadwal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Referensi --}}
                            <div class="mb-3">
                                <label for="catatan_referensi" class="form-label">
                                    <strong>Daftar Pustaka/Referensi</strong>
                                    <small class="text-muted">(Kualitas, relevansi, kebaruan sumber)</small>
                                </label>
                                <textarea class="form-control @error('catatan_referensi') is-invalid @enderror" id="catatan_referensi"
                                    name="catatan_referensi" rows="3" placeholder="Catatan terkait referensi...">{{ old('catatan_referensi', $catatan->catatan_referensi ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_referensi">0</span>/2000</small>
                                @error('catatan_referensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan Umum --}}
                            <div class="mb-0">
                                <label for="catatan_umum" class="form-label">
                                    <strong>Catatan Umum</strong>
                                    <small class="text-muted">(Saran tambahan, komentar keseluruhan)</small>
                                </label>
                                <textarea class="form-control @error('catatan_umum') is-invalid @enderror" id="catatan_umum" name="catatan_umum"
                                    rows="3" placeholder="Catatan umum atau saran tambahan untuk mahasiswa">{{ old('catatan_umum', $catatan->catatan_umum ?? '') }}</textarea>
                                <small class="text-muted">Karakter: <span id="count_umum">0</span>/3000</small>
                                @error('catatan_umum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>
                                    {{ $catatan ? 'Update Lembar Catatan' : 'Simpan Lembar Catatan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formId = 'lembar-catatan-{{ $beritaAcara->id }}';

            // ========== CHARACTER COUNTER ==========
            const textareas = {
                'catatan_kebaruan': {
                    max: 5000,
                    counter: 'count_kebaruan'
                },
                'catatan_metode': {
                    max: 5000,
                    counter: 'count_metode'
                },
                'catatan_ketersediaan_data': {
                    max: 5000,
                    counter: 'count_ketersediaan'
                },
                'catatan_bab1': {
                    max: 5000,
                    counter: 'count_bab1'
                },
                'catatan_bab2': {
                    max: 5000,
                    counter: 'count_bab2'
                },
                'catatan_bab3': {
                    max: 5000,
                    counter: 'count_bab3'
                },
                'catatan_jadwal': {
                    max: 2000,
                    counter: 'count_jadwal'
                },
                'catatan_referensi': {
                    max: 2000,
                    counter: 'count_referensi'
                },
                'catatan_umum': {
                    max: 3000,
                    counter: 'count_umum'
                }
            };

            Object.keys(textareas).forEach(key => {
                const textarea = document.getElementById(key);
                const counter = document.getElementById(textareas[key].counter);
                const max = textareas[key].max;

                if (textarea && counter) {
                    // Initial count
                    counter.textContent = textarea.value.length;

                    // Update on input
                    textarea.addEventListener('input', function() {
                        const count = this.value.length;
                        counter.textContent = count;

                        if (count > max) {
                            counter.classList.add('text-danger');
                            counter.classList.remove('text-muted');
                        } else {
                            counter.classList.add('text-muted');
                            counter.classList.remove('text-danger');
                        }
                    });
                }
            });

            // ========== AUTO-SAVE TO LOCALSTORAGE ==========
            const formInputs = document.querySelectorAll('textarea, input[type="number"]');

            formInputs.forEach(input => {
                // Load from localStorage
                const savedValue = localStorage.getItem(`${formId}-${input.name}`);
                if (savedValue && !input.value) {
                    input.value = savedValue;

                    // Trigger events for counter and average
                    input.dispatchEvent(new Event('input'));
                }

                // Save on change
                input.addEventListener('input', function() {
                    localStorage.setItem(`${formId}-${input.name}`, this.value);
                });
            });

            // Clear localStorage on successful submit
            document.getElementById('form-lembar-catatan').addEventListener('submit', function() {
                formInputs.forEach(input => {
                    localStorage.removeItem(`${formId}-${input.name}`);
                });
            });

            // ========== CONFIRMATION BEFORE LEAVE ==========
            let formChanged = false;

            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });

            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            document.getElementById('form-lembar-catatan').addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
@endpush
