@extends('layouts.app')

@section('title', ($branch ? 'Edit' : 'Tambah') . ' Cabang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Cabang</a></li>
    <li class="breadcrumb-item active">{{ $branch ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $branch ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $branch ? 'Edit' : 'Tambah' }} Cabang
                </h3>
            </div>
            <form action="{{ $branch ? route('branches.update', $branch) : route('branches.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($branch)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Kode Cabang <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" 
                                    class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                    value="{{ old('code', $branch?->code) }}" 
                                    placeholder="PLJKT"
                                    maxlength="20"
                                    required>
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Untuk format PO</small>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nama Cabang <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $branch?->name) }}" 
                                    placeholder="PT. Nama Perusahaan"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <textarea name="address" id="address" 
                            class="form-control @error('address') is-invalid @enderror" 
                            rows="2" placeholder="Alamat lengkap cabang">{{ old('address', $branch?->address) }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="text" name="phone" id="phone" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    value="{{ old('phone', $branch?->phone) }}" 
                                    placeholder="021-1234567">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email', $branch?->email) }}" 
                                    placeholder="cabang@example.com">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fas fa-file-pdf mr-2"></i>Pengaturan Cetak PDF</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="npwp">NPWP</label>
                                <input type="text" name="npwp" id="npwp" 
                                    class="form-control @error('npwp') is-invalid @enderror" 
                                    value="{{ old('npwp', $branch?->npwp) }}" 
                                    placeholder="01.234.567.8-901.000">
                                @error('npwp')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Ditampilkan di header PDF</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo">Logo Perusahaan</label>
                                <div class="custom-file">
                                    <input type="file" name="logo" id="logo" 
                                        class="custom-file-input @error('logo') is-invalid @enderror"
                                        accept="image/*">
                                    <label class="custom-file-label" for="logo">
                                        {{ $branch?->logo ? basename($branch->logo) : 'Pilih file...' }}
                                    </label>
                                </div>
                                @error('logo')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Ukuran max: 1MB (PNG/JPG)</small>
                                <div class="mt-2" id="logo-preview-container">
                                    @if($branch?->logo)
                                        <p class="mb-1 text-muted small">Logo saat ini:</p>
                                        <img src="{{ asset('storage/' . $branch->logo) }}" alt="Logo" id="current-logo" style="max-height: 60px; border: 1px solid #ddd; padding: 5px;">
                                    @endif
                                    <div id="new-logo-preview" style="display: none;">
                                        <p class="mb-1 text-success small">Preview logo baru:</p>
                                        <img id="preview-img" src="" alt="Preview" style="max-height: 60px; border: 2px solid #28a745; padding: 5px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="footer_note">Catatan Footer</label>
                        <textarea name="footer_note" id="footer_note" 
                            class="form-control @error('footer_note') is-invalid @enderror" 
                            rows="2" placeholder="Catatan yang ditampilkan di bagian bawah PDF">{{ old('footer_note', $branch?->footer_note) }}</textarea>
                        @error('footer_note')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $branch?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Format Nomor PO</h3>
            </div>
            <div class="card-body">
                <p>Kode cabang akan digunakan dalam format nomor PO:</p>
                <code class="d-block bg-light p-3 mb-3">
                    {sequence}/GA/{kode_cabang}/{bulan_romawi}/{tahun}
                </code>
                <p><strong>Contoh:</strong></p>
                <ul>
                    <li><code>009/GA/PLJKT/I/25</code> - Jakarta, Januari 2025</li>
                    <li><code>015/GA/PLPLSDA/XII/25</code> - Sidoarjo, Desember 2025</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Logo preview on file select
    $('#logo').on('change', function() {
        var file = this.files[0];
        if (file) {
            // Update file input label
            $(this).next('.custom-file-label').text(file.name);
            
            // Check file size
            if (file.size > 1024 * 1024) {
                Swal.fire({icon: 'warning', title: 'File terlalu besar!', text: 'Ukuran maksimal 1MB'});
                return;
            }
            
            // Preview image
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#new-logo-preview').show();
                $('#current-logo').css('opacity', '0.5');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
