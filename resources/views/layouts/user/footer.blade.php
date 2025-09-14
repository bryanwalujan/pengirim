<footer id="footer" class="footer">
    <div class="container footer-top">
        <div class="row gy-4">
            <!-- Bagian Tentang E-Services -->
            <div class="col-lg-4 col-md-6 footer-about">
                <div class="footer-brand">
                    <a href="{{ route('user.home.index') }}" class="logo d-flex align-items-center mb-3">
                        <img src="{{ asset('/img/logo-unima.png') }}" alt="E-Services Logo" class="logo-img">
                        <span class="sitename">E-Services</span>
                    </a>
                    <p class="footer-description">
                        Platform digital terpadu untuk layanan akademik Teknik Informatika Universitas Negeri Manado.
                        Memberikan kemudahan akses layanan administrasi akademik secara online.
                    </p>
                </div>

                <div class="footer-contact mb-4">
                    <div class="contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <strong>Alamat:</strong>
                            <a href="https://maps.app.goo.gl/zAKXtUMxn6YBs8qq9" target="_blank" class="contact-link">
                                Jl. Unima, Tataaran Satu, Kec. Tondano Sel., Kabupaten Minahasa, Sulawesi Utara
                            </a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-whatsapp-fill"></i>
                        <div>
                            <strong>Whatsapp:</strong>
                            <span>+62 898-5029-407</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <strong>Email:</strong>
                            <span>informatika@unima.ac.id</span>
                        </div>
                    </div>
                </div>

                <div class="social-links">
                    <h5 class="social-title">Ikuti Kami</h5>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/profile.php?id=100046755076902" target="_blank"
                            class="social-link facebook" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/informatikaftunima/" target="_blank"
                            class="social-link instagram" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@informatikaftunima6429" target="_blank"
                            class="social-link youtube" title="Youtube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bagian Layanan Mahasiswa -->
            <div class="col-lg-3 col-md-4 col-6 footer-links">
                <h4><i class="bi bi-mortarboard me-2"></i>Layanan Mahasiswa</h4>
                <ul>
                    <li><a href="{{ route('user.surat-aktif-kuliah.index') }}"><i class="bi bi-chevron-right"></i>Surat
                            Aktif Kuliah</a></li>
                    <li><a href="{{ route('user.surat-ijin-survey.index') }}"><i class="bi bi-chevron-right"></i>Surat
                            Ijin Survey</a></li>
                    <li><a href="{{ route('user.surat-cuti-akademik.index') }}"><i class="bi bi-chevron-right"></i>Surat
                            Cuti Akademik</a></li>
                    <li><a href="{{ route('user.surat-pindah.index') }}"><i class="bi bi-chevron-right"></i>Surat
                            Pindah</a></li>
                    <li><a href="{{ route('user.services.index') }}"><i class="bi bi-chevron-right"></i>Layanan
                            Akademik</a></li>
                    <li><a href="{{ url('/#academic-calendar') }}"><i class="bi bi-chevron-right"></i>Kalender
                            Akademik</a></li>
                </ul>
            </div>

            <!-- Bagian Informasi -->
            <div class="col-lg-2 col-md-4 col-6 footer-links">
                <h4><i class="bi bi-info-circle me-2"></i>Informasi</h4>
                <ul>
                    <li><a href="{{ url('/#about') }}"><i class="bi bi-chevron-right"></i>Tentang
                            E-Services</a></li>
                    <li><a href="{{ url('/#faq') }}"><i class="bi bi-chevron-right"></i>FAQ</a></li>
                    <li><a href="#"><i class="bi bi-chevron-right"></i>Panduan
                            Penggunaan</a></li>
                </ul>
            </div>

            <!-- Bagian Tautan Eksternal -->
            <div class="col-lg-3 col-md-4 col-6 footer-links">
                <h4><i class="bi bi-link-45deg me-2"></i>Tautan Eksternal</h4>
                <ul>
                    <li><a href="https://unima.ac.id" target="_blank"><i class="bi bi-chevron-right"></i>Universitas
                            Negeri Manado</a></li>
                    <li><a href="https://ft.unima.ac.id" target="_blank"><i class="bi bi-chevron-right"></i>Fakultas
                            Teknik</a></li>
                    <li><a href="https://ti.unima.ac.id" target="_blank"><i class="bi bi-chevron-right"></i>Prodi Teknik
                            Informatika</a></li>
                    <li><a href="https://si.unima.ac.id/gtakademik_portal/" target="_blank"><i
                                class="bi bi-chevron-right"></i>Portal Akademik</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="copyright-text">
                        © <span id="currentYear">{{ date('Y') }}</span>
                        <strong class="sitename">E-Services Teknik Informatika</strong>
                        <span>All Rights Reserved</span>
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-credits">
                        <span>Developed by</span>
                        <a href="https://github.com/patrickrompas20" target="_blank" class="developer-link">Trick20</a>
                        <span>|</span>
                        <a href="https://ti.unima.ac.id" target="_blank" class="institution-link">
                            Program Studi Teknik Informatika, Universitas Negeri Manado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
