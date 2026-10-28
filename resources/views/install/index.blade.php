<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Web Installer | PO System PT. Pangan Lestari</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE / Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <style>
        :root {
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

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(249, 115, 22, 0.07) 0px, transparent 45%),
                radial-gradient(at 50% 100%, rgba(79, 70, 229, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--slate-800);
            padding: 30px 15px;
        }

        .installer-container {
            max-width: 820px;
            margin: 0 auto;
        }

        .installer-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .installer-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 2rem 2.5rem 1.5rem;
            text-align: center;
        }

        .installer-body {
            padding: 2rem 2.5rem;
        }

        @media (max-width: 576px) {
            .installer-header, .installer-body {
                padding: 1.5rem 1.25rem;
            }
        }

        /* Wizard Step Bar */
        .step-progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-progress-bar::before {
            content: "";
            position: absolute;
            top: 18px;
            left: 30px;
            right: 30px;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: #64748b;
            transition: all 0.25s ease;
            margin-bottom: 6px;
        }

        .step-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
        }

        .step-item.active .step-circle {
            border-color: var(--brand-primary);
            background: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }

        .step-item.active .step-label {
            color: var(--brand-primary);
        }

        .step-item.completed .step-circle {
            border-color: #10b981;
            background: #10b981;
            color: #ffffff;
        }

        .step-item.completed .step-label {
            color: #10b981;
        }

        /* Check Table */
        .check-table {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .check-table table {
            margin-bottom: 0;
        }

        .check-table th {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #475569;
        }

        .check-table td {
            font-size: 0.88rem;
            vertical-align: middle;
        }

        /* Terminal Window */
        .terminal-box {
            background-color: #0b1120;
            border: 1px solid #1e293b;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .terminal-box-header {
            background-color: #1e293b;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .terminal-box-body {
            padding: 1rem;
            max-height: 250px;
            overflow-y: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.82rem;
            color: #38bdf8;
            white-space: pre-wrap;
        }

        .btn-modern {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 22px;
            transition: all 0.2s ease;
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #4338ca 100%);
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-brand:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            color: #ffffff;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="installer-container">
        <div class="installer-card">
            <!-- Header -->
            <div class="installer-header">
                <img src="{{ asset('images/logo-pangan-lestari.png') }}" 
                     alt="PT. Pangan Lestari" 
                     style="max-height: 48px; object-fit: contain;" 
                     class="mb-3"
                     onerror="this.style.display='none'; document.getElementById('brandTextFallback').classList.remove('d-none');">
                <div id="brandTextFallback" class="d-none mb-3">
                    <h3 class="font-weight-bold text-dark mb-0"><span class="text-primary font-weight-bolder">PO</span> System</h3>
                </div>

                <div class="mb-2">
                    <span class="badge" style="background-color: #eef2ff; color: #4f46e5; font-size: 0.76rem; font-weight: 600; padding: 4px 12px; border-radius: 9999px;">
                        Web Installation Wizard
                    </span>
                </div>
                <h4 class="font-weight-bold mb-1 text-slate-900" style="font-size: 1.4rem;">Setup Awal Aplikasi</h4>
                <p class="text-muted small mb-0">Konfigurasi database, migrasi tabel, dan setup akun awal tanpa perlu akses shell SSH.</p>
            </div>

            <!-- Body -->
            <div class="installer-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible mb-4" style="border-radius: 10px;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($isInstalled)
                    <!-- Already Installed Notice -->
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <span class="badge badge-pill badge-success p-3" style="font-size: 2.2rem; border-radius: 50%;">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-success mb-2">Aplikasi Sudah Terpasang</h4>
                        <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                            Sistem PO &amp; Invoicing PT. Pangan Lestari sudah berhasil diinstalasi dan siap digunakan. Kunci instalasi aktif untuk menjaga keamanan aplikasi.
                        </p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('login') }}" class="btn btn-brand btn-modern mr-2">
                                <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Halaman Login
                            </a>
                            <form action="{{ route('install.unlock') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membuka kunci dan mengonfigurasi ulang aplikasi?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-modern">
                                    <i class="fas fa-lock-open mr-1"></i> Buka Kunci Instalasi Ulang
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Step Progress Bar -->
                    <div class="step-progress-bar">
                        <div class="step-item active" id="indicatorStep1">
                            <span class="step-circle">1</span>
                            <span class="step-label">Cek Sistem</span>
                        </div>
                        <div class="step-item" id="indicatorStep2">
                            <span class="step-circle">2</span>
                            <span class="step-label">Basis Data</span>
                        </div>
                        <div class="step-item" id="indicatorStep3">
                            <span class="step-circle">3</span>
                            <span class="step-label">Eksekusi</span>
                        </div>
                        <div class="step-item" id="indicatorStep4">
                            <span class="step-circle">4</span>
                            <span class="step-label">Selesai</span>
                        </div>
                    </div>

                    <!-- STEP 1: Requirements & Permissions -->
                    <div class="wizard-step" id="step1">
                        <h5 class="font-weight-bold text-slate-800 mb-2">
                            <i class="fas fa-tasks text-primary mr-2"></i> 1. Verifikasi Persyaratan Server &amp; Izin Folder
                        </h5>
                        <p class="text-muted small mb-3">
                            Server hosting harus memenuhi spesifikasi minimum PHP dan memiliki izin tulis (*write permission*) pada folder storage &amp; cache.
                        </p>

                        <!-- Extensions Check -->
                        <div class="check-table">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Persyaratan PHP</th>
                                        <th>Nilai Server</th>
                                        <th class="text-center" style="width: 100px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requirements as $req)
                                        <tr>
                                            <td class="font-weight-500">{{ $req['name'] }}</td>
                                            <td class="text-muted small">{{ $req['current'] }}</td>
                                            <td class="text-center">
                                                @if($req['status'])
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Pass</span>
                                                @else
                                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Fail</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Permissions Check -->
                        <div class="check-table">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Direktori / File</th>
                                        <th>Lokasi Path</th>
                                        <th class="text-center" style="width: 100px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $perm)
                                        <tr>
                                            <td class="font-weight-500">{{ $perm['name'] }}</td>
                                            <td class="text-muted small text-truncate" style="max-width: 250px;" title="{{ $perm['path'] }}">{{ $perm['path'] }}</td>
                                            <td class="text-center">
                                                @if($perm['status'])
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Writable</span>
                                                @else
                                                    <span class="badge badge-danger px-2 py-1" title="Ubah permission folder ke 775 atau 755"><i class="fas fa-times"></i> Denied</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <span class="text-muted small">
                                @if($allRequirementsPassed && $allPermissionsPassed)
                                    <i class="fas fa-check-circle text-success mr-1"></i> Semua persyaratan server terpenuhi.
                                @else
                                    <i class="fas fa-exclamation-triangle text-danger mr-1"></i> Harap penuhi persyaratan yang berlabel <b>Fail/Denied</b> sebelum lanjut.
                                @endif
                            </span>
                            <button type="button" class="btn btn-brand btn-modern" id="btnNextToStep2" {{ ($allRequirementsPassed && $allPermissionsPassed) ? '' : 'disabled' }}>
                                Lanjut ke Pengaturan Basis Data <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Database Configuration -->
                    <div class="wizard-step d-none" id="step2">
                        <h5 class="font-weight-bold text-slate-800 mb-2">
                            <i class="fas fa-database text-primary mr-2"></i> 2. Konfigurasi Basis Data &amp; URL Subdomain
                        </h5>
                        <p class="text-muted small mb-3">
                            Masukkan parameter koneksi MySQL yang telah Anda buat di cPanel Jagoan Hosting (menu <b>MySQL Databases</b>).
                        </p>

                        <form id="dbConfigForm">
                            <div class="form-group mb-3">
                                <label class="font-weight-600 small text-slate-700">App URL (Alamat Subdomain)</label>
                                <input type="url" name="app_url" id="app_url" class="form-control" value="{{ $currentConfig['app_url'] }}" placeholder="https://posystem.pldev.id" required>
                                <small class="text-muted">Gunakan awalan https:// sesuai domain di cPanel.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-600 small text-slate-700">Database Host</label>
                                        <input type="text" name="db_host" id="db_host" class="form-control" value="{{ $currentConfig['db_host'] }}" placeholder="127.0.0.1 atau localhost" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-600 small text-slate-700">Database Port</label>
                                        <input type="number" name="db_port" id="db_port" class="form-control" value="{{ $currentConfig['db_port'] }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-600 small text-slate-700">Nama Database</label>
                                <input type="text" name="db_database" id="db_database" class="form-control" value="{{ $currentConfig['db_database'] }}" placeholder="contoh: username_invoices" required>
                                <small class="text-muted">Di cPanel, nama database biasanya memiliki prefix username cPanel (misal: <code>pldev_posystem</code>).</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-600 small text-slate-700">Database Username</label>
                                        <input type="text" name="db_username" id="db_username" class="form-control" value="{{ $currentConfig['db_username'] }}" placeholder="contoh: username_dbuser" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-600 small text-slate-700">Database Password</label>
                                        <input type="password" name="db_password" id="db_password" class="form-control" value="{{ $currentConfig['db_password'] }}" placeholder="Kata sandi user database">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <button type="button" class="btn btn-outline-info btn-sm mr-2" id="btnTestDb">
                                    <i class="fas fa-plug mr-1"></i> Uji Koneksi Database
                                </button>
                                <span id="testDbStatus" class="small"></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <button type="button" class="btn btn-light btn-modern" id="btnBackToStep1">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </button>
                                <button type="button" class="btn btn-brand btn-modern" id="btnSaveConfig">
                                    Simpan &amp; Lanjut ke Eksekusi <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- STEP 3: Execution (Migrate & Seed) -->
                    <div class="wizard-step d-none" id="step3">
                        <h5 class="font-weight-bold text-slate-800 mb-2">
                            <i class="fas fa-cogs text-primary mr-2"></i> 3. Eksekusi Instalasi Otomatis
                        </h5>
                        <p class="text-muted small mb-3">
                            Sistem akan mengeksekusi pembuatan tabel, pengisian akun administrator default, symlink storage, dan optimasi berkas.
                        </p>

                        <!-- Step Checklist -->
                        <div class="card border mb-3" style="border-radius: 12px;">
                            <div class="list-group list-group-flush" id="executionList">
                                <div class="list-group-item d-flex justify-content-between align-items-center" id="itemKey">
                                    <span><i class="fas fa-key text-muted mr-2"></i> 1. Inisialisasi App Encryption Key</span>
                                    <span class="badge badge-secondary" id="badgeKey">Menunggu</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center" id="itemMigrate">
                                    <span><i class="fas fa-table text-muted mr-2"></i> 2. Migrasi Tabel Database (<code>php artisan migrate</code>)</span>
                                    <span class="badge badge-secondary" id="badgeMigrate">Menunggu</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center" id="itemSeed">
                                    <span><i class="fas fa-seedling text-muted mr-2"></i> 3. Seeder Akun Admin &amp; Master Data (<code>db:seed</code>)</span>
                                    <span class="badge badge-secondary" id="badgeSeed">Menunggu</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center" id="itemStorage">
                                    <span><i class="fas fa-link text-muted mr-2"></i> 4. Buat Symlink Storage &amp; Bersihkan Cache</span>
                                    <span class="badge badge-secondary" id="badgeStorage">Menunggu</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terminal Output Window -->
                        <div class="terminal-box">
                            <div class="terminal-box-header">
                                <span class="badge badge-danger p-1" style="border-radius: 50%;"></span>
                                <span class="badge badge-warning p-1" style="border-radius: 50%;"></span>
                                <span class="badge badge-success p-1" style="border-radius: 50%;"></span>
                                <span class="text-white small ml-2 font-monospace">Console Output</span>
                            </div>
                            <div class="terminal-box-body" id="installConsole">
Siap memulai instalasi. Klik tombol di bawah untuk menjalankan seluruh proses secara otomatis.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                            <button type="button" class="btn btn-light btn-modern" id="btnBackToStep2">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-success btn-modern px-4" id="btnStartInstall">
                                <i class="fas fa-rocket mr-1"></i> Mulai Instalasi Otomatis
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: Completed -->
                    <div class="wizard-step d-none text-center py-3" id="step4">
                        <div class="mb-3">
                            <span class="badge badge-pill badge-success p-3" style="font-size: 2.2rem; border-radius: 50%;">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-success mb-2">Instalasi Berhasil Diselesaikan!</h4>
                        <p class="text-muted mb-4" style="max-width: 520px; margin: 0 auto;">
                            Database telah termigrasi, data awal berhasil diisi, dan tautan storage telah aktif. Anda dapat langsung masuk dengan kredensial default berikut:
                        </p>

                        <div class="card border mb-4 text-left p-3" style="max-width: 420px; margin: 0 auto; border-radius: 12px; background: #f8fafc;">
                            <h6 class="font-weight-bold text-slate-800 mb-2 border-bottom pb-2">
                                <i class="fas fa-user-shield text-primary mr-1"></i> Akun Administrator Default
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Email:</span>
                                <span class="font-weight-bold font-monospace text-dark">admin@example.com</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Password:</span>
                                <span class="font-weight-bold font-monospace text-dark">Pangles1234</span>
                            </div>
                            <div class="mt-2 text-danger small font-italic">
                                * Disarankan untuk segera mengubah kata sandi setelah berhasil login!
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="btn btn-brand btn-modern px-4 py-2">
                            <i class="fas fa-sign-in-alt mr-2"></i> Buka Halaman Login Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-3 text-muted small">
            &copy; {{ date('Y') }} PT. Pangan Lestari &bull; Automated Deployment Assistant
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/AdminLTE3') }}/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function () {
            const token = $('meta[name="csrf-token"]').attr('content');
            const consoleBox = $('#installConsole');

            function log(msg) {
                consoleBox.append("\n" + msg);
                consoleBox.scrollTop(consoleBox[0].scrollHeight);
            }

            function switchStep(fromStep, toStep) {
                $(`#step${fromStep}`).addClass('d-none');
                $(`#step${toStep}`).removeClass('d-none');
                $(`#indicatorStep${fromStep}`).removeClass('active').addClass('completed');
                $(`#indicatorStep${toStep}`).addClass('active');
            }

            // Step 1 -> Step 2
            $('#btnNextToStep2').on('click', function () {
                switchStep(1, 2);
            });

            // Step 2 -> Step 1
            $('#btnBackToStep1').on('click', function () {
                $(`#step2`).addClass('d-none');
                $(`#step1`).removeClass('d-none');
                $('#indicatorStep2').removeClass('active');
                $('#indicatorStep1').removeClass('completed').addClass('active');
            });

            // Step 3 -> Step 2
            $('#btnBackToStep2').on('click', function () {
                $(`#step3`).addClass('d-none');
                $(`#step2`).removeClass('d-none');
                $('#indicatorStep3').removeClass('active');
                $('#indicatorStep2').removeClass('completed').addClass('active');
            });

            // Test Database Connection
            $('#btnTestDb').on('click', function () {
                const btn = $(this);
                const statusSpan = $('#testDbStatus');

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menguji...');
                statusSpan.html('');

                $.ajax({
                    url: "{{ route('install.check-db') }}",
                    method: "POST",
                    data: {
                        _token: token,
                        db_host: $('#db_host').val(),
                        db_port: $('#db_port').val(),
                        db_database: $('#db_database').val(),
                        db_username: $('#db_username').val(),
                        db_password: $('#db_password').val(),
                    },
                    success: function (res) {
                        btn.prop('disabled', false).html('<i class="fas fa-plug mr-1"></i> Uji Koneksi Database');
                        statusSpan.html('<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> ' + res.message + '</span>');
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-plug mr-1"></i> Uji Koneksi Database');
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Koneksi gagal.';
                        statusSpan.html('<span class="text-danger font-weight-bold"><i class="fas fa-times-circle mr-1"></i> ' + msg + '</span>');
                    }
                });
            });

            // Save Config & Proceed to Step 3
            $('#btnSaveConfig').on('click', function () {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan .env...');

                $.ajax({
                    url: "{{ route('install.save-config') }}",
                    method: "POST",
                    data: {
                        _token: token,
                        app_url: $('#app_url').val(),
                        db_host: $('#db_host').val(),
                        db_port: $('#db_port').val(),
                        db_database: $('#db_database').val(),
                        db_username: $('#db_username').val(),
                        db_password: $('#db_password').val(),
                    },
                    success: function () {
                        btn.prop('disabled', false).html('Simpan & Lanjut ke Eksekusi <i class="fas fa-arrow-right ml-1"></i>');
                        switchStep(2, 3);
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('Simpan & Lanjut ke Eksekusi <i class="fas fa-arrow-right ml-1"></i>');
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan konfigurasi .env.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan!',
                            text: msg
                        });
                    }
                });
            });

            // Execute Sequential Install Steps
            $('#btnStartInstall').on('click', function () {
                const startBtn = $(this);
                startBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menginstalasi...');
                $('#btnBackToStep2').prop('disabled', true);

                consoleBox.html('=== MEMULAI INSTALASI APLIKASI PO SYSTEM ===\n');

                function runStepAjax(stepName, badgeId, nextStepCallback) {
                    $(`#badge${badgeId}`).removeClass('badge-secondary').addClass('badge-warning').html('<i class="fas fa-spinner fa-spin"></i> Proses');
                    log(`> Menjalankan langkah: ${stepName}...`);

                    $.ajax({
                        url: "{{ route('install.run-step') }}",
                        method: "POST",
                        data: {
                            _token: token,
                            step: stepName
                        },
                        success: function (res) {
                            $(`#badge${badgeId}`).removeClass('badge-warning').addClass('badge-success').text('Selesai');
                            log(res.output);
                            log(`✔ ${res.message}\n`);
                            if (nextStepCallback) {
                                nextStepCallback();
                            }
                        },
                        error: function (xhr) {
                            $(`#badge${badgeId}`).removeClass('badge-warning').addClass('badge-danger').text('Gagal');
                            const err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error pada proses.';
                            const detail = (xhr.responseJSON && xhr.responseJSON.output) ? xhr.responseJSON.output : xhr.responseText;
                            log(`✖ Gagal: ${err}\n${detail}`);
                            startBtn.prop('disabled', false).html('<i class="fas fa-redo mr-1"></i> Coba Lagi');
                            $('#btnBackToStep2').prop('disabled', false);

                            Swal.fire({
                                icon: 'error',
                                title: 'Instalasi Terhenti!',
                                text: err
                            });
                        }
                    });
                }

                // Step sequence: key -> migrate -> seed -> storage_link -> finish
                runStepAjax('key', 'Key', function () {
                    runStepAjax('migrate', 'Migrate', function () {
                        runStepAjax('seed', 'Seed', function () {
                            runStepAjax('storage_link', 'Storage', function () {
                                runStepAjax('finish', 'Storage', function () {
                                    log('=== SELURUH PROSES INSTALASI SELESAI ===');
                                    setTimeout(function () {
                                        switchStep(3, 4);
                                    }, 1200);
                                });
                            });
                        });
                    });
                });
            });
        });
    </script>
</body>

</html>
