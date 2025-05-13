<footer id="footer" class="footer">
    <div class="container footer-top">
        <div class="row gy-3">
            <!-- Bagian Tentang E-Services -->
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="{{ route('user.home.index') }}" class="logo d-flex align-items-center">
                    <span class="sitename me-2">E-Services</span>
                    <img src="{{ asset('/img/logo-unima.png') }}" alt="E-Services Logo"
                        style="height: 45px; width: auto;">
                </a>
                <div class="footer-contact">
                    <p>Program Studi Teknik Informatika</p>
                    <p>Fakultas Teknik, Universitas Negeri Manado</p>
                    <p>Tondano, Sulawesi Utara, Indonesia</p>
                    <p class="mt-3"><strong>Phone:</strong> <span>+62 431 123456</span></p>
                    <p><strong>Email:</strong> <span>informatika@unima.ac.id</span></p>
                </div>
                <div class="social-links d-flex mt-4">
                    <a href="https://twitter.com/unima_official"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://facebook.com/unima.official"><i class="bi bi-facebook"></i></a>
                    <a href="https://instagram.com/unima_official"><i class="bi bi-instagram"></i></a>
                    <a href="https://linkedin.com/company/universitas-negeri-manado"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <!-- Bagian Tautan Layanan Utama -->
            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Layanan Mahasiswa</h4>
                <ul>
                    <li><a href="{{ route('user.surat-aktif-kuliah.index') }}">Surat Aktif Kuliah</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan Akademik</a></li>
                    <li><a href="{{ route('academic-calendar.view', ['filename' => 'latest.pdf']) }}">Kalender
                            Akademik</a></li>
                </ul>
            </div>

            <!-- Bagian Tautan Informasi -->
            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Informasi</h4>
                <ul>
                    <li><a href="#">Tentang E-Services</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Kontak Kami</a></li>
                </ul>
            </div>

            <!-- Bagian Tautan Eksternal -->
            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Tautan Eksternal</h4>
                <ul>
                    <li><a href="https://unima.ac.id">Universitas Negeri Manado</a></li>
                    <li><a href="https://ft.unima.ac.id">Fakultas Teknik</a></li>
                    <li><a href="https://informatika.ft.unima.ac.id">Prodi Teknik Informatika</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>© <span>2025</span> <strong class="px-1 sitename">E-Services Teknik Informatika</strong> <span>All Rights
                Reserved</span></p>
        <div class="credits">
            Developed by <a href="https://informatika.ft.unima.ac.id">Program Studi Teknik Informatika, Universitas
                Negeri Manado</a>
        </div>
    </div>
</footer>
