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
}
