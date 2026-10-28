@extends('layouts.app')

@section('title', 'Utilitas Sistem & Deployment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.email') }}">Pengaturan</a></li>
    <li class="breadcrumb-item active">Utilitas Deploy</li>
@endsection

@push('styles')
<style>
    .status-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 1.15rem 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .status-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .util-action-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        margin-bottom: 1.5rem;
        background: #ffffff;
        overflow: hidden;
    }
    .util-action-card .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1.25rem;
    }
    .terminal-window {
        background-color: #0f172a;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #1e293b;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .terminal-header {
        background-color: #1e293b;
        padding: 0.6rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #334155;
    }
    .terminal-dots {
        display: flex;
        gap: 6px;
    }
    .terminal-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
    }
    .dot-red { background-color: #ef4444; }
    .dot-yellow { background-color: #f59e0b; }
    .dot-green { background-color: #10b981; }
    .terminal-body {
        padding: 1.25rem;
        max-height: 420px;
        min-height: 220px;
        overflow-y: auto;
        font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        font-size: 0.88rem;
        line-height: 1.6;
        color: #38bdf8;
        background-color: #0b1120;
    }
    .terminal-body::-webkit-scrollbar {
        width: 8px;
    }
    .terminal-body::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }
    .terminal-line-prompt {
        color: #10b981;
    }
    .terminal-line-cmd {
        color: #f1f5f9;
        font-weight: 600;
    }
    .terminal-line-time {
        color: #64748b;
        font-size: 0.78rem;
    }
    .terminal-line-output {
        color: #cbd5e1;
        white-space: pre-wrap;
        word-break: break-all;
        margin-bottom: 0.75rem;
    }
    .terminal-line-success {
        color: #34d399;
        font-weight: 500;
    }
    .terminal-line-error {
        color: #f87171;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Section Header & Alert Notice -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534; border-radius: 10px;">
                <i class="fas fa-server fa-2x mr-3 text-success"></i>
                <div>
                    <h6 class="font-weight-bold mb-1">Pusat Utilitas Deployment &amp; Pemeliharaan Sistem</h6>
                    <span class="small">
                        Halaman ini memfasilitasi eksekusi perintah penting Artisan Laravel (seperti migrasi database, seeding, symlink storage, dan cache) langsung melalui web browser tanpa memerlukan akses SSH/Terminal cPanel.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="status-card d-flex align-items-center">
                <div class="status-icon-box bg-primary-subtle text-primary mr-3" style="background: #eef2ff; color: #4f46e5;">
                    <i class="fab fa-php"></i>
                </div>
                <div>
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.72rem;">Lingkungan Server</small>
                    <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">PHP {{ $systemInfo['php_version'] }}</span>
                    <small class="d-block text-muted">Laravel {{ $systemInfo['laravel_version'] }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="status-card d-flex align-items-center">
                <div class="status-icon-box mr-3" style="background: #ecfeff; color: #0891b2;">
                    <i class="fas fa-cogs"></i>
                </div>
                <div>
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.72rem;">Environment / Mode</small>
                    <span class="badge {{ $systemInfo['app_env'] === 'production' ? 'badge-success' : 'badge-warning' }}">
                        {{ strtoupper($systemInfo['app_env']) }}
                    </span>
                    <small class="d-block text-muted">Debug: {{ $systemInfo['app_debug'] }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="status-card d-flex align-items-center">
                <div class="status-icon-box mr-3" style="background: #fdf4ff; color: #c026d3;">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.72rem;">Basis Data Terhubung</small>
                    <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $systemInfo['db_name'] }}</span>
                    <small class="d-block text-muted">Driver: {{ strtoupper($systemInfo['db_connection']) }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="status-card d-flex align-items-center">
                <div class="status-icon-box mr-3" style="background: #f0fdf4; color: #16a34a;">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 0.72rem;">Storage Symlink</small>
                    <span id="storageBadge" class="badge {{ $systemInfo['storage_symlink_exists'] ? 'badge-success' : 'badge-warning' }}">
                        {{ $systemInfo['storage_symlink_exists'] ? 'Tersambung' : 'Belum Terhubung' }}
                    </span>
                    <small class="d-block text-muted text-truncate" style="max-width: 140px;" title="{{ $systemInfo['storage_target'] }}">
                        {{ $systemInfo['storage_target'] }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Operations Grid -->
    <div class="row">
        <!-- Action Cards Column -->
        <div class="col-lg-6">
            <!-- 1. Database Migration -->
            <div class="util-action-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-800 mb-0">
                        <i class="fas fa-database text-primary mr-2"></i> 1. Database Migration
                    </h6>
                    <span class="badge badge-light text-muted">php artisan migrate</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Mengeksekusi berkas migration baru yang belum pernah dijalankan di database. Aman dijalankan kapan saja karena tidak menghapus data lama.
                    </p>
                    <button type="button" class="btn btn-primary btn-sm px-3 btn-action" data-action="migrate">
                        <i class="fas fa-play mr-1"></i> Jalankan Migrate
                    </button>
                </div>
            </div>

            <!-- 2. Database Seeder -->
            <div class="util-action-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-800 mb-0">
                        <i class="fas fa-seedling text-success mr-2"></i> 2. Database Seeder
                    </h6>
                    <span class="badge badge-light text-muted">php artisan db:seed</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Mengisi data awal atau data referensi sistem (User Administrator default, Role, Permissions, Master Cabang, dsb).
                    </p>
                    <div class="form-row align-items-center">
                        <div class="col-md-7 mb-2 mb-md-0">
                            <select id="seederClassSelect" class="form-control form-control-sm">
                                @foreach($seeders as $class => $name)
                                    <option value="{{ $class }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="btn btn-success btn-sm btn-block btn-action" id="btnRunSeed" data-action="seed">
                                <i class="fas fa-play mr-1"></i> Jalankan Seeder
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Storage Symlink -->
            <div class="util-action-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-800 mb-0">
                        <i class="fas fa-link text-info mr-2"></i> 3. Storage Symlink
                    </h6>
                    <span class="badge badge-light text-muted">php artisan storage:link</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Menghubungkan folder <code>storage/app/public</code> ke <code>public/storage</code>. Sangat wajib agar logo perusahaan, bukti approval, dan berkas lampiran PO dapat diakses dari browser.
                    </p>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm px-3 btn-action" data-action="storage_link">
                            <i class="fas fa-link mr-1"></i> Buat Symlink
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 btn-action" data-action="storage_relink" title="Hapus symlink lama dan buat ulang">
                            <i class="fas fa-redo-alt mr-1"></i> Re-link
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4. Cache & Optimization -->
            <div class="util-action-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-800 mb-0">
                        <i class="fas fa-broom text-warning mr-2"></i> 4. Cache &amp; Optimasi Deploy
                    </h6>
                    <span class="badge badge-light text-muted">optimize:clear</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Membersihkan cache route, config, dan views yang menumpuk setelah upload file baru ke hosting, atau membuat cache kompilasi untuk mempercepat respon aplikasi di server produksi.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-warning btn-sm px-3 mr-2 mb-2 btn-action" data-action="optimize_clear">
                            <i class="fas fa-trash-alt mr-1"></i> Bersihkan Cache (optimize:clear)
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3 mb-2 btn-action" data-action="optimize">
                            <i class="fas fa-bolt mr-1"></i> Optimasi Produksi (optimize)
                        </button>
                    </div>
                </div>
            </div>

            <!-- 5. Danger Zone: Migrate Fresh -->
            <div class="util-action-card border-danger">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i> 5. Zona Bahaya: Reset Database (Migrate Fresh)
                    </h6>
                    <span class="badge badge-light text-danger font-weight-bold">DANGER</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-3 p-2 small">
                        <strong>PERINGATAN KERAS:</strong> Perintah ini akan <u>MENGHAPUS SEMUA TABEL</u> beserta seluruh data transaksi PO, Vendor, dan User di database, lalu membuat ulang skema tabel dari awal!
                    </div>
                    
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="checkWithSeed" checked>
                        <label class="custom-control-label text-dark font-weight-semibold" for="checkWithSeed">
                            Sekaligus jalankan seeder setelah reset (<code>--seed</code>)
                        </label>
                        <small class="d-block text-muted">Akan langsung mengisi kembali akun Administrator default &amp; master data awal.</small>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm px-3" id="btnMigrateFresh">
                        <i class="fas fa-trash mr-1"></i> Reset Total Database (Migrate Fresh)
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Terminal Console Column -->
        <div class="col-lg-6">
            <div class="terminal-window sticky-top" style="top: 20px;">
                <div class="terminal-header">
                    <div class="d-flex align-items-center">
                        <div class="terminal-dots mr-3">
                            <span class="terminal-dot dot-red"></span>
                            <span class="terminal-dot dot-yellow"></span>
                            <span class="terminal-dot dot-green"></span>
                        </div>
                        <span class="text-slate-200 small font-weight-bold font-monospace">
                            <i class="fas fa-terminal mr-1 text-primary"></i> Artisan Execution Console
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-dark btn-xs text-slate-300 py-0 px-2 mr-1" id="btnCopyLog" title="Salin output ke clipboard">
                            <i class="fas fa-copy"></i> Salin
                        </button>
                        <button type="button" class="btn btn-dark btn-xs text-slate-300 py-0 px-2" id="btnClearLog" title="Bersihkan tampilan log">
                            <i class="fas fa-eraser"></i> Bersihkan
                        </button>
                    </div>
                </div>

                <div class="terminal-body" id="terminalOutput">
                    <div class="terminal-entry">
                        <span class="terminal-line-time">[{{ date('Y-m-d H:i:s') }}]</span>
                        <span class="terminal-line-prompt">posystem@jagoanhosting:~$</span>
                        <span class="terminal-line-cmd">system --ready</span>
                        <div class="terminal-line-output text-muted">
Konsol siap. Klik salah satu tombol aksi di sebelah kiri untuk menjalankan perintah deployment Artisan.
Output eksekusi akan ditampilkan di sini secara real-time.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const terminal = $('#terminalOutput');

        function appendLog(command, output, isSuccess, time) {
            const timeStr = time || new Date().toISOString().replace('T', ' ').substring(0, 19);
            const statusClass = isSuccess ? 'terminal-line-success' : 'terminal-line-error';
            const statusIcon = isSuccess ? '✔' : '✖';

            const logHtml = `
                <div class="terminal-entry mt-3 border-top border-secondary pt-2">
                    <div>
                        <span class="terminal-line-time">[${timeStr}]</span>
                        <span class="terminal-line-prompt">posystem@server:~$</span>
                        <span class="terminal-line-cmd">php artisan ${escapeHtml(command)}</span>
                    </div>
                    <div class="terminal-line-output ${statusClass} mt-1">${statusIcon} ${escapeHtml(output)}</div>
                </div>
            `;

            terminal.append(logHtml);
            terminal.scrollTop(terminal[0].scrollHeight);
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function executeArtisan(action, payload, buttonElement, commandLabel) {
            const originalHtml = buttonElement.html();
            buttonElement.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

            // Append initial running message to terminal
            const runTime = new Date().toISOString().replace('T', ' ').substring(0, 19);
            terminal.append(`
                <div class="terminal-entry mt-2 text-warning">
                    <span class="terminal-line-time">[${runTime}]</span>
                    <span><i class="fas fa-circle-notch fa-spin mr-1"></i> Menjalankan: <code>php artisan ${escapeHtml(commandLabel || action)}</code> ...</span>
                </div>
            `);
            terminal.scrollTop(terminal[0].scrollHeight);

            const data = Object.assign({
                action: action,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, payload || {});

            $.ajax({
                url: "{{ route('settings.utilization.run') }}",
                method: "POST",
                data: data,
                success: function (res) {
                    buttonElement.prop('disabled', false).html(originalHtml);
                    appendLog(commandLabel || action, res.output || res.message, res.success, res.timestamp);

                    // Update storage badge if action is storage_link or storage_relink
                    if (res.storage_linked !== undefined) {
                        const badge = $('#storageBadge');
                        if (res.storage_linked) {
                            badge.removeClass('badge-warning').addClass('badge-success').text('Tersambung');
                        } else {
                            badge.removeClass('badge-success').addClass('badge-warning').text('Belum Terhubung');
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 3500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                },
                error: function (xhr) {
                    buttonElement.prop('disabled', false).html(originalHtml);
                    const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    const errorOutput = (xhr.responseJSON && xhr.responseJSON.output) ? xhr.responseJSON.output : xhr.responseText;
                    
                    appendLog(commandLabel || action, errorMsg + "\n" + errorOutput, false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengeksekusi!',
                        text: errorMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        // Generic Button Actions
        $('.btn-action').on('click', function () {
            const btn = $(this);
            const action = btn.data('action');
            let payload = {};
            let cmdLabel = action;

            if (action === 'seed') {
                const selectedClass = $('#seederClassSelect').val();
                payload.class = selectedClass;
                cmdLabel = 'db:seed --class=' + selectedClass;
            } else if (action === 'migrate') {
                cmdLabel = 'migrate --force';
            } else if (action === 'storage_link') {
                cmdLabel = 'storage:link';
            } else if (action === 'storage_relink') {
                cmdLabel = 'storage:link (re-link)';
            } else if (action === 'optimize_clear') {
                cmdLabel = 'optimize:clear';
            } else if (action === 'optimize') {
                cmdLabel = 'optimize';
            }

            executeArtisan(action, payload, btn, cmdLabel);
        });

        // Danger Action: Migrate Fresh
        $('#btnMigrateFresh').on('click', function () {
            const btn = $(this);
            const withSeed = $('#checkWithSeed').is(':checked');
            const action = withSeed ? 'migrate_fresh_seed' : 'migrate_fresh';
            const cmdLabel = withSeed ? 'migrate:fresh --seed --force' : 'migrate:fresh --force';

            Swal.fire({
                title: 'Konfirmasi Reset Total Database',
                html: `
                    <div class="text-left">
                        <p class="text-danger font-weight-bold mb-2">
                            <i class="fas fa-radiation-alt mr-1"></i> PERINGATAN KERAS:
                        </p>
                        <p class="mb-2">Semua data tabel di database <strong>akan dihapus permanen</strong> dan dibuat ulang.</p>
                        <p class="text-muted small">Ketik kata <strong>RESET</strong> di bawah ini untuk mengonfirmasi tindakan:</p>
                    </div>
                `,
                input: 'text',
                inputPlaceholder: 'Ketik RESET di sini',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Reset Sekarang',
                cancelButtonText: 'Batalkan',
                preConfirm: (value) => {
                    if (value !== 'RESET') {
                        Swal.showValidationMessage('Anda harus mengetik "RESET" dengan huruf kapital!');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeArtisan(action, {}, btn, cmdLabel);
                }
            });
        });

        // Copy Terminal Log
        $('#btnCopyLog').on('click', function () {
            const textToCopy = terminal.text().trim();
            if (!textToCopy) return;

            navigator.clipboard.writeText(textToCopy).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: 'Seluruh log terminal berhasil disalin ke clipboard.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(function () {
                const dummy = $('<textarea>').val(textToCopy).appendTo('body').select();
                document.execCommand('copy');
                dummy.remove();
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });

        // Clear Terminal Log
        $('#btnClearLog').on('click', function () {
            const clearTime = new Date().toISOString().replace('T', ' ').substring(0, 19);
            terminal.html(`
                <div class="terminal-entry">
                    <span class="terminal-line-time">[${clearTime}]</span>
                    <span class="terminal-line-prompt">posystem@server:~$</span>
                    <span class="terminal-line-cmd">clear</span>
                    <div class="terminal-line-output text-muted">Log konsol dibersihkan. Siap untuk eksekusi perintah berikutnya.</div>
                </div>
            `);
        });
    });
</script>
@endpush
