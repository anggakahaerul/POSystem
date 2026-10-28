@extends('layouts.app')

@section('title', 'Pengaturan Email')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengaturan Email</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Konfigurasi SMTP</h3>
            </div>
            <form action="{{ route('settings.email.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mail Driver</label>
                                <select name="mail_mailer" class="form-control">
                                    <option value="smtp" {{ ($settings['mail_mailer'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Encryption</label>
                                <select name="mail_encryption" class="form-control">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($settings['mail_encryption'] ?? '') === 'null' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>SMTP Host <span class="text-danger">*</span></label>
                                <input type="text" name="mail_host" class="form-control" 
                                    value="{{ $settings['mail_host'] ?? '' }}" 
                                    placeholder="smtp.gmail.com" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Port <span class="text-danger">*</span></label>
                                <input type="number" name="mail_port" class="form-control" 
                                    value="{{ $settings['mail_port'] ?? '587' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="mail_username" class="form-control" 
                                    value="{{ $settings['mail_username'] ?? '' }}" 
                                    placeholder="your-email@gmail.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="mail_password" class="form-control" 
                                    value="{{ $settings['mail_password'] ?? '' }}" 
                                    placeholder="App Password">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fas fa-user mr-2"></i>Pengirim Email</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Pengirim <span class="text-danger">*</span></label>
                                <input type="email" name="mail_from_address" class="form-control" 
                                    value="{{ $settings['mail_from_address'] ?? '' }}" 
                                    placeholder="noreply@company.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Pengirim <span class="text-danger">*</span></label>
                                <input type="text" name="mail_from_name" class="form-control" 
                                    value="{{ $settings['mail_from_name'] ?? config('app.name') }}" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fas fa-bell mr-2"></i>Notifikasi Email</h5>
                    <p class="text-muted small">Aktifkan/nonaktifkan notifikasi email otomatis</p>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="notify_submit" 
                                    name="notify_submit" value="1"
                                    {{ ($settings['notify_submit'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="notify_submit">
                                    <strong>Submit Approval</strong><br>
                                    <small class="text-muted">Kirim ke Manager saat PO diajukan</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="notify_tax_review" 
                                    name="notify_tax_review" value="1"
                                    {{ ($settings['notify_tax_review'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="notify_tax_review">
                                    <strong>Review Pajak (PPh)</strong><br>
                                    <small class="text-muted">Kirim email permohonan & hasil review pajak</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="notify_approve" 
                                    name="notify_approve" value="1"
                                    {{ ($settings['notify_approve'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="notify_approve">
                                    <strong>PO Disetujui</strong><br>
                                    <small class="text-muted">Kirim ke pembuat PO saat disetujui</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="notify_reject" 
                                    name="notify_reject" value="1"
                                    {{ ($settings['notify_reject'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="notify_reject">
                                    <strong>PO Ditolak</strong><br>
                                    <small class="text-muted">Kirim ke pembuat PO saat ditolak</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Test Email -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane mr-2"></i>Test Email</h3>
            </div>
            <form action="{{ route('settings.email.test') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Kirim Test Email Ke:</label>
                        <input type="email" name="test_email" class="form-control" 
                            placeholder="test@example.com" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Test Email
                    </button>
                </div>
            </form>
        </div>

        <!-- Help -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Bantuan</h3>
            </div>
            <div class="card-body">
                <h6>Gmail SMTP:</h6>
                <ul class="small pl-3">
                    <li>Host: smtp.gmail.com</li>
                    <li>Port: 587 (TLS) / 465 (SSL)</li>
                    <li>Gunakan App Password</li>
                </ul>
                <h6>Outlook SMTP:</h6>
                <ul class="small pl-3">
                    <li>Host: smtp.office365.com</li>
                    <li>Port: 587</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
