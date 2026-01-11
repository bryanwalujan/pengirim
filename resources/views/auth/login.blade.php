<x-guest-layout title="Login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @section('title', 'Login')

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-group">
                <input type="text" id="email" name="email" :value="old('email', '')"
                    class="form-control form-control-lg border-gray-400 text-sm rounded-start"
                    placeholder="Email unima anda" aria-label="Email" required autofocus autocomplete="username">
                <span
                    class="input-group-text bg-light bg-gradient-orange text-white text-bold border-gray-400 rounded-end"
                    id="domain">@unima.ac.id</span>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <script>
            // Pastikan nilai yang dikirim hanya bagian nama pengguna
            document.querySelector('form').addEventListener('submit', function(e) {
                const emailInput = document.getElementById('email');
                if (emailInput.value && !emailInput.value.includes('@')) {
                    emailInput.value += '@unima.ac.id';
                }
            });
        </script>

        <!-- Password -->
        <div class="mb-1">
            <x-input-label for="password" :value="__('Password')" />
            <div class="password-wrapper">
                <input type="password" id="password" name="password"
                    class="form-control form-control-lg rounded border-gray-400 text-sm" placeholder="Password"
                    aria-label="Password" required autocomplete="current-password">
                <button type="button" class="password-toggle" id="togglePassword" tabindex="-1"
                    aria-label="Toggle password visibility">
                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="text-end">
                <a href="{{ route('password.request') }}" class="text-sm text-orange">
                    {{ __('Lupa password?') }}
                </a>
            </div>
        @endif

        <!-- Login Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-orange btn-lg w-100 mt-2 mb-3">Login</button>
        </div>

        <!-- Link kembali ke beranda untuk mobile -->
        <div class="text-center d-md-none">
            <a href="{{ route('user.home.index') }}" class="btn btn-outline-secondary w-100">
                <i class="fas fa-home me-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </form>

    {{-- Quick Login untuk Development (hanya tampil di local) --}}
    @if(config('app.env') === 'local')
        <div class="mt-4 quick-login-container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="mb-0 fw-bold text-secondary text-uppercase small ls-1">
                    <i class="fas fa-code me-2"></i>Developer Access
                </h6>
                <span class="badge bg-dark text-white rounded-pill px-3">LOCAL ONLY</span>
            </div>
            
            <div class="d-grid gap-2">
                {{-- Mahasiswa --}}
                <div class="dropdown">
                    <button type="button" 
                       class="btn btn-white w-100 text-start d-flex align-items-center p-3 border border-light-subtle shadow-sm hover-lift group quick-login-btn dropdown-toggle no-caret"
                       data-bs-toggle="dropdown" data-role="mahasiswa" aria-expanded="false">
                        <div class="rounded-3 bg-blue-subtle p-2 me-3 text-primary d-flex align-items-center justify-content-center icon-box">
                            <i class="fas fa-user-graduate fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark mb-0">Mahasiswa</div>
                            <div class="text-muted extra-small">Pilih akun Mahasiswa</div>
                        </div>
                        <div class="bg-light rounded-circle p-1 d-flex align-items-center justify-content-center arrow-icon">
                            <i class="fas fa-chevron-down text-muted extra-small"></i>
                        </div>
                    </button>
                    <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 py-2 mt-2 user-dropdown-menu" id="dropdown-mahasiswa">
                        <li class="px-3 py-2 text-center text-muted small loading-text">Memuat user...</li>
                    </ul>
                </div>

                {{-- Dosen --}}
                <div class="dropdown">
                    <button type="button" 
                       class="btn btn-white w-100 text-start d-flex align-items-center p-3 border border-light-subtle shadow-sm hover-lift group quick-login-btn dropdown-toggle no-caret"
                       data-bs-toggle="dropdown" data-role="dosen" aria-expanded="false">
                        <div class="rounded-3 bg-green-subtle p-2 me-3 text-success d-flex align-items-center justify-content-center icon-box">
                            <i class="fas fa-chalkboard-teacher fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark mb-0">Dosen</div>
                            <div class="text-muted extra-small">Pilih akun Dosen</div>
                        </div>
                        <div class="bg-light rounded-circle p-1 d-flex align-items-center justify-content-center arrow-icon">
                            <i class="fas fa-chevron-down text-muted extra-small"></i>
                        </div>
                    </button>
                    <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 py-2 mt-2 user-dropdown-menu" id="dropdown-dosen">
                        <li class="px-3 py-2 text-center text-muted small loading-text">Memuat user...</li>
                    </ul>
                </div>

                {{-- Staff --}}
                <div class="dropdown">
                    <button type="button" 
                       class="btn btn-white w-100 text-start d-flex align-items-center p-3 border border-light-subtle shadow-sm hover-lift group quick-login-btn dropdown-toggle no-caret"
                       data-bs-toggle="dropdown" data-role="staff" aria-expanded="false">
                        <div class="rounded-3 bg-purple-subtle p-2 me-3 text-purple d-flex align-items-center justify-content-center icon-box">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark mb-0">Staff</div>
                            <div class="text-muted extra-small">Pilih akun Staff</div>
                        </div>
                        <div class="bg-light rounded-circle p-1 d-flex align-items-center justify-content-center arrow-icon">
                            <i class="fas fa-chevron-down text-muted extra-small"></i>
                        </div>
                    </button>
                    <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 py-2 mt-2 user-dropdown-menu" id="dropdown-staff">
                        <li class="px-3 py-2 text-center text-muted small loading-text">Memuat user...</li>
                    </ul>
                </div>
            </div>
        </div>

        <style>
            .quick-login-container {
                background: linear-gradient(to bottom, #ffffff, #f8f9fa);
                border: 1px dashed #cbd5e1;
                border-radius: 16px;
                padding: 1.5rem;
                position: relative;
            }
            .ls-1 { letter-spacing: 0.5px; }
            .extra-small { font-size: 0.75rem; }
            
            /* Custom Colors */
            .bg-blue-subtle { background-color: #eff6ff !important; }
            .bg-green-subtle { background-color: #f0fdf4 !important; }
            .bg-purple-subtle { background-color: #f5f3ff !important; }
            .text-purple { color: #7c3aed !important; }
            
            .icon-box { width: 48px; height: 48px; }

            .btn-white {
                background-color: #ffffff;
                color: #1e293b;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                transition: all 0.3s ease;
            }
            
            .hover-lift {
                text-decoration: none;
            }
            .hover-lift:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                border-color: #cbd5e1 !important;
                z-index: 10;
            }
            
            /* Arrow Animation */
            .arrow-icon {
                width: 24px;
                height: 24px;
                transition: all 0.3s ease;
                opacity: 0.6;
            }
            .hover-lift:hover .arrow-icon {
                background-color: #1e293b !important;
                color: white !important;
                transform: translateX(4px);
                opacity: 1;
            }
            .hover-lift:hover .arrow-icon i {
                color: white !important;
            }

            .no-caret::after { display: none !important; }
            .user-dropdown-menu {
                max-height: 300px;
                overflow-y: auto;
            }
            .dropdown-item-user {
                transition: all 0.2s;
                cursor: pointer;
            }
            .dropdown-item-user:hover {
                background-color: #f8f9fa;
                transform: translateX(4px);
            }
        </style>
    @endif

    <!-- Informasi tambahan -->
    <div class="text-center mt-3">
        <p class="text-muted small">
            Belum memiliki akses?
            <a href="{{ route('user.home.index') }}" class="text-orange text-decoration-none">
                Kunjungi beranda untuk informasi lebih lanjut
            </a>
        </p>
    </div>

    {{-- Script untuk Toggle Password --}}
    @push('scripts')
        <script>
            // Password Toggle logic (dipisah agar rapi)
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('togglePasswordIcon');
                if (togglePassword && passwordInput && toggleIcon) {
                    togglePassword.addEventListener('click', function(e) {
                        e.preventDefault();
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        if (type === 'text') {
                            toggleIcon.classList.remove('bi-eye-slash');
                            toggleIcon.classList.add('bi-eye');
                        } else {
                            toggleIcon.classList.remove('bi-eye');
                            toggleIcon.classList.add('bi-eye-slash');
                        }
                    });
                }
            });

            // Quick Login Dropdown logic
            @if(config('app.env') === 'local')
            document.addEventListener('DOMContentLoaded', function() {
                const dropdownButtons = document.querySelectorAll('.quick-login-btn');
                
                dropdownButtons.forEach(button => {
                    button.addEventListener('show.bs.dropdown', function () {
                        const role = this.getAttribute('data-role');
                        const menu = document.getElementById(`dropdown-${role}`);
                        
                        // Hanya load jika belum ada data (atau jika ingin force reload hapus check ini)
                        if (menu.querySelectorAll('.dropdown-item-user').length > 0) return;

                        console.log('Loading users for:', role);
                        
                        fetch(`/dev/users/${role}`)
                            .then(response => response.json())
                            .then(users => {
                                if (users.length === 0) {
                                    menu.innerHTML = '<li><span class="dropdown-item-text text-center text-muted">Tidak ada user</span></li>';
                                    return;
                                }

                                let html = '';
                                users.forEach(user => {
                                    const iconClass = role === 'mahasiswa' ? 'fa-user-graduate text-primary' : 
                                                     role === 'dosen' ? 'fa-chalkboard-teacher text-success' : 
                                                     'fa-user-tie text-purple';
                                    
                                    html += `
                                        <li>
                                            <a class="dropdown-item dropdown-item-user d-flex align-items-center p-3" 
                                               href="javascript:void(0)" 
                                               onclick="window.quickLoginAsUser(${user.id}, '${role}')">
                                                <div class="me-3">
                                                    <i class="fas ${iconClass}"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small text-dark">${user.name}</div>
                                                    <div class="extra-small text-muted">${user.identifier}</div>
                                                </div>
                                            </a>
                                        </li>
                                    `;
                                });
                                menu.innerHTML = html;
                            })
                            .catch(error => {
                                menu.innerHTML = '<li><span class="dropdown-item-text text-danger text-center">Gagal memuat data</span></li>';
                                console.error('Error:', error);
                            });
                    });
                });
            });
            @endif
        </script>
    @endpush
</x-guest-layout>
