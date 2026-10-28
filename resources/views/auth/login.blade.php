@extends('layouts.auth')

@section('title', 'Masuk ke Akun')

@section('auth-card')
    <div class="auth-card">
        <div class="auth-card-body">
            <!-- Brand & App Identity -->
            <div class="text-center mb-4">
                <div class="mb-3 d-flex justify-content-center align-items-center">
                    <img src="{{ asset('images/logo-pangan-lestari.png') }}" 
                         alt="PT. Pangan Lestari" 
                         class="img-fluid" 
                         style="max-height: 48px; object-fit: contain;"
                         onerror="this.style.display='none'; document.getElementById('textBrandFallback').classList.remove('d-none');">
                    <div id="textBrandFallback" class="d-none">
                        <span class="h3 font-weight-bold text-dark">
                            <span class="text-primary font-weight-bolder">PO</span> System
                        </span>
                    </div>
                </div>

                <div class="mb-2">
                    <span class="badge" style="background-color: #eef2ff; color: #4f46e5; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; letter-spacing: 0.02em;">
                        Purchase Order &amp; Invoice Portal
                    </span>
                </div>

                <h1 class="h4 font-weight-bold text-slate-900 mb-1" style="font-size: 1.35rem; letter-spacing: -0.02em;">
                    Selamat Datang
                </h1>
                <p class="text-muted small mb-0" style="font-size: 0.85rem;">
                    Silakan masuk untuk mengakses sistem operasional
                </p>
            </div>

            <!-- Session Status / Alert -->
            @if (session('status'))
                <div class="modern-auth-alert mb-3" style="background-color: #ecfeff; border-color: #a5f3fc; color: #0e7490;">
                    <i class="fas fa-info-circle text-info"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="modern-auth-alert" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Gagal Masuk:</strong> {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="post" id="loginForm" autocomplete="on">
                @csrf

                <!-- Email Input -->
                <div class="form-group-modern">
                    <label for="email" class="form-label-modern">
                        Alamat Email <span class="text-danger">*</span>
                    </label>
                    <div class="modern-input-wrapper @error('email') has-error @enderror">
                        <span class="input-icon-lead">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="modern-input" 
                               placeholder="nama@panganlestari.com"
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="email">
                    </div>
                    @error('email')
                        <div class="field-error-msg">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group-modern">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label-modern mb-0">
                            Kata Sandi <span class="text-danger">*</span>
                        </label>
                    </div>
                    <div class="modern-input-wrapper @error('password') has-error @enderror">
                        <span class="input-icon-lead">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="modern-input" 
                               placeholder="Masukkan kata sandi akun"
                               required 
                               autocomplete="current-password">
                        <button type="button" 
                                class="btn-toggle-password" 
                                id="togglePasswordBtn" 
                                aria-label="Lihat kata sandi" 
                                title="Tampilkan kata sandi"
                                tabindex="-1">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error-msg">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-4 pt-1">
                    <label class="modern-checkbox" for="remember">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkbox-custom-mark">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="checkbox-label-text">Ingat Saya</span>
                    </label>

                    <span class="text-muted small" style="font-size: 0.78rem;">
                        <i class="fas fa-shield-alt text-muted mr-1"></i> Sesi Aman
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-modern-auth" id="btnSubmitLogin">
                    <span id="btnSubmitText">
                        Masuk ke Akun <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                    <span id="btnSubmitLoading" class="d-none">
                        <i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses Masuk...
                    </span>
                </button>
            </form>

            <!-- Support & IT Contact -->
            <div class="mt-4 pt-3 border-top text-center" style="border-color: #f1f5f9 !important;">
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">
                    Mengalami kendala akun? 
                    <span class="text-slate-700 font-weight-medium">Hubungi Administrator IT</span>
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Password Visibility
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                    toggleBtn.setAttribute('title', 'Sembunyikan kata sandi');
                    toggleBtn.setAttribute('aria-label', 'Sembunyikan kata sandi');
                } else {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                    toggleBtn.setAttribute('title', 'Tampilkan kata sandi');
                    toggleBtn.setAttribute('aria-label', 'Tampilkan kata sandi');
                }
            });
        }

        // Form Submit Loading Feedback
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('btnSubmitLogin');
        const submitText = document.getElementById('btnSubmitText');
        const submitLoading = document.getElementById('btnSubmitLoading');

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', function () {
                // Prevent duplicate click
                submitBtn.disabled = true;
                if (submitText && submitLoading) {
                    submitText.classList.add('d-none');
                    submitLoading.classList.remove('d-none');
                }
            });
        }
    });
</script>
@endpush
