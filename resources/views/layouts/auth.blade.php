<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Purchase & Asset Management') }} | @yield('title', 'Login')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/dist/css/adminlte.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-sub: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --brand-accent: #f97316;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: var(--font-main);
            color: var(--slate-800);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Modern Mesh & Ambient Background */
        .modern-auth-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.09) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(249, 115, 22, 0.07) 0px, transparent 45%),
                radial-gradient(at 50% 100%, rgba(79, 70, 229, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
            padding: 24px 16px;
        }

        /* Subtle decorative background shapes */
        .modern-auth-body::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -100px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .modern-auth-body::after {
            content: "";
            position: absolute;
            bottom: -150px;
            left: -120px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.12) 0%, rgba(79, 70, 229, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            margin: auto;
        }

        /* Modern Card - Zero Glitch */
        .auth-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 20px;
            box-shadow:
                0 20px 40px -15px rgba(15, 23, 42, 0.07),
                0 1px 3px 0 rgba(15, 23, 42, 0.04);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card-body {
            padding: 2.25rem 2.25rem;
        }

        @media (max-width: 576px) {
            .auth-card-body {
                padding: 1.75rem 1.5rem;
            }
        }

        /* Zero-Glitch Modern Input Wrapper */
        .form-group-modern {
            margin-bottom: 1.35rem;
        }

        .form-label-modern {
            display: block;
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 0.45rem;
            letter-spacing: -0.01em;
        }

        .modern-input-wrapper {
            display: flex;
            align-items: center;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .modern-input-wrapper:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }

        .modern-input-wrapper:focus-within {
            border-color: var(--brand-primary);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }

        .modern-input-wrapper.has-error {
            border-color: #ef4444 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }

        .modern-input-wrapper .input-icon-lead {
            padding-left: 14px;
            padding-right: 4px;
            color: var(--slate-400);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .modern-input-wrapper:focus-within .input-icon-lead {
            color: var(--brand-primary);
        }

        .modern-input-wrapper.has-error .input-icon-lead {
            color: #ef4444;
        }

        .modern-input {
            flex: 1;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 12px 14px 12px 8px;
            font-size: 0.93rem;
            font-weight: 500;
            color: var(--slate-900);
            font-family: inherit;
            line-height: 1.5;
            width: 100%;
        }

        .modern-input::placeholder {
            color: var(--slate-400);
            font-weight: 400;
            font-size: 0.88rem;
        }

        .btn-toggle-password {
            background: transparent;
            border: none;
            outline: none !important;
            color: var(--slate-400);
            padding: 0 14px;
            height: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            transition: color 0.2s ease, transform 0.15s ease;
        }

        .btn-toggle-password:hover {
            color: var(--brand-primary);
        }

        .btn-toggle-password:active {
            transform: scale(0.92);
        }

        .field-error-msg {
            display: flex;
            align-items: center;
            margin-top: 0.45rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: #ef4444;
            animation: fadeInError 0.25s ease;
        }

        @keyframes fadeInError {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern Custom Checkbox */
        .modern-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            user-select: none;
            cursor: pointer;
        }

        .modern-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkbox-custom-mark {
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--slate-300);
            border-radius: 5px;
            background-color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .checkbox-custom-mark i {
            color: #ffffff;
            font-size: 10px;
            display: none;
        }

        .modern-checkbox input[type="checkbox"]:checked~.checkbox-custom-mark {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .modern-checkbox input[type="checkbox"]:checked~.checkbox-custom-mark i {
            display: block;
        }

        .modern-checkbox:hover .checkbox-custom-mark {
            border-color: var(--brand-primary);
        }

        .modern-checkbox .checkbox-label-text {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--slate-600);
        }

        /* Modern Primary Button */
        .btn-modern-auth {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #4338ca 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 13px 20px;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.35);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .btn-modern-auth:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            color: #ffffff;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 20px 0 rgba(79, 70, 229, 0.45);
        }

        .btn-modern-auth:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px 0 rgba(79, 70, 229, 0.3);
        }

        .btn-modern-auth:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Modern Alerts */
        .modern-auth-alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.86rem;
            margin-bottom: 1.35rem;
            display: flex;
            align-items: flex-start;
        }

        .modern-auth-alert i {
            color: #ef4444;
            font-size: 1rem;
            margin-top: 2px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        /* Modern Security & Footer Badges */
        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--slate-200);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.76rem;
            color: var(--slate-600);
            font-weight: 500;
            backdrop-filter: blur(4px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 0.75rem;
        }

        .security-badge i {
            color: #10b981;
            margin-right: 6px;
        }

        .copyright-text {
            font-size: 0.78rem;
            color: var(--slate-500);
            font-family: var(--font-sub);
        }

        /* Fix legacy Bootstrap input-groups if used on other auth views */
        .input-group>.form-control:not(:last-child) {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .input-group>.input-group-append>.input-group-text {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
    </style>

    @stack('styles')
</head>

<body class="modern-auth-body @yield('body-class', '')">
    <div class="auth-container @yield('container-class', '')">
        @hasSection('auth-card')
            @yield('auth-card')
        @else
            <div class="auth-card">
                <div class="auth-card-body">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                            <span class="h3 font-weight-bold text-dark">{{ config('app.name', 'Purchase & Asset Management') }}</span>
                        </a>
                    </div>
                    @yield('content')
                </div>
            </div>
        @endif

        @hasSection('auth-footer')
            @yield('auth-footer')
        @else
            <div class="auth-footer">
                <!-- <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Sistem Terenkripsi &amp; Terproteksi SSL</span>
                    </div> -->
                <p class="copyright-text mb-0">
                    &copy; {{ date('Y') }} EDP Division. Seluruh hak cipta dilindungi.
                </p>
            </div>
        @endif
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#4f46e5'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444'
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>