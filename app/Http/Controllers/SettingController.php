<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    /**
     * Show email settings form
     */
    public function email()
    {
        $settings = Setting::getByGroup('email');

        return view('settings.email', compact('settings'));
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        // Save each setting
        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'email');
        }

        // Save notification toggles
        Setting::set('notify_submit', $request->boolean('notify_submit') ? '1' : '0', 'email');
        Setting::set('notify_approve', $request->boolean('notify_approve') ? '1' : '0', 'email');
        Setting::set('notify_reject', $request->boolean('notify_reject') ? '1' : '0', 'email');
        Setting::set('notify_tax_review', $request->boolean('notify_tax_review') ? '1' : '0', 'email');

        return back()->with('success', 'Pengaturan email berhasil disimpan.');
    }

    /**
     * Send test email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Apply email settings
            Setting::applyEmailConfig();

            Mail::raw('Ini adalah email test dari sistem PO Invoice. Jika Anda menerima email ini, konfigurasi SMTP sudah benar.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email - PO Invoice System');
            });

            return back()->with('success', 'Email test berhasil dikirim ke ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Show utilization & deployment tools
     */
    public function utilization()
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Administrator.');
        }

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Aktif' : 'Nonaktif',
            'app_url' => config('app.url'),
            'db_connection' => config('database.default'),
            'db_name' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
            'storage_symlink_exists' => file_exists(public_path('storage')),
            'storage_target' => is_link(public_path('storage')) ? @readlink(public_path('storage')) : (file_exists(public_path('storage')) ? 'Direktori Fisik' : 'Belum Dibuat'),
        ];

        $seeders = [
            'DatabaseSeeder' => 'Semua Data Awal (DatabaseSeeder)',
            'RoleSeeder' => 'Role & Permission (RoleSeeder)',
            'BranchSeeder' => 'Data Cabang (BranchSeeder)',
            'DepartmentSeeder' => 'Data Departemen (DepartmentSeeder)',
            'PaymentTermSeeder' => 'Termin Pembayaran (PaymentTermSeeder)',
            'AppUserSetupSeeder' => 'Setup User Sistem (AppUserSetupSeeder)',
        ];

        return view('settings.utilization', compact('systemInfo', 'seeders'));
    }

    /**
     * Run utilization deployment command
     */
    public function runUtilization(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Fitur ini hanya untuk Administrator.',
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:migrate,migrate_fresh,migrate_fresh_seed,seed,storage_link,storage_relink,optimize_clear,optimize',
            'class' => 'nullable|string',
        ]);

        @set_time_limit(300);

        $action = $request->input('action');
        $output = '';
        $message = '';

        try {
            switch ($action) {
                case 'migrate':
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Perintah `php artisan migrate` berhasil dijalankan.';
                    break;

                case 'migrate_fresh':
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Perintah `php artisan migrate:fresh` berhasil dijalankan.';
                    break;

                case 'migrate_fresh_seed':
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
                        '--seed' => true,
                        '--force' => true,
                    ]);
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Perintah `php artisan migrate:fresh --seed` berhasil dijalankan.';
                    break;

                case 'seed':
                    $params = ['--force' => true];
                    if ($request->filled('class')) {
                        $params['--class'] = $request->input('class');
                    }
                    \Illuminate\Support\Facades\Artisan::call('db:seed', $params);
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $className = $request->input('class', 'DatabaseSeeder');
                    $message = "Perintah `php artisan db:seed --class={$className}` berhasil dijalankan.";
                    break;

                case 'storage_link':
                    \Illuminate\Support\Facades\Artisan::call('storage:link');
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Perintah `php artisan storage:link` selesai.';
                    break;

                case 'storage_relink':
                    $publicStorage = public_path('storage');
                    if (is_link($publicStorage)) {
                        @unlink($publicStorage);
                    }
                    \Illuminate\Support\Facades\Artisan::call('storage:link');
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Symlink storage berhasil dibuat ulang.';
                    break;

                case 'optimize_clear':
                    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Cache sistem berhasil dibersihkan.';
                    break;

                case 'optimize':
                    \Illuminate\Support\Facades\Artisan::call('optimize');
                    $output = \Illuminate\Support\Facades\Artisan::output();
                    $message = 'Optimasi cache produksi berhasil dijalankan.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'output' => trim($output) ?: '(Perintah berhasil dieksekusi tanpa log keluaran teks)',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'storage_linked' => file_exists(public_path('storage')),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'output' => $e->getTraceAsString(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }
}

