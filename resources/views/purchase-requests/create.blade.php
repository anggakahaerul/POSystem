@extends('layouts.app')

@section('title', 'Buat Permintaan Pembelian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Permintaan Pembelian</a></li>
    <li class="breadcrumb-item active">Upload Request</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-weight-bold m-0 text-dark">
                    <i class="fas fa-file-upload mr-2 text-primary"></i>Upload Permintaan Pembelian
                </h3>
            </div>

            <form action="{{ route('purchase-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <!-- Judul Request -->
                    <div class="form-group mb-3">
                        <label for="title" class="font-weight-bold">Judul Request <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" 
                            class="form-control form-control-lg @error('title') is-invalid @enderror" 
                            placeholder="Contoh: Pengadaan Laptop Kantor / ATK Bulanan" 
                            value="{{ old('title') }}" required autofocus>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Deskripsi Request -->
                    <div class="form-group mb-3">
                        <label for="description" class="font-weight-bold">Deskripsi Request</label>
                        <textarea name="description" id="description" rows="3" 
                            class="form-control @error('description') is-invalid @enderror" 
                            placeholder="Keterangan singkat request...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Attachment (PDF / PNG) -->
                    <div class="form-group mb-3">
                        <label for="attachment" class="font-weight-bold">Attachment (PDF / PNG) <small class="text-muted font-weight-normal">(Opsional)</small></label>
                        <div class="custom-file">
                            <input type="file" name="attachment" id="attachment" 
                                class="custom-file-input @error('attachment') is-invalid @enderror" 
                                accept=".pdf,.png,.jpg,.jpeg" onchange="previewFile(this)">
                            <label class="custom-file-label" id="file-label" for="attachment">Pilih file PDF atau PNG...</label>
                            @error('attachment')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Preview Container -->
                        <div id="file-preview-container" class="mt-3 p-3 bg-light rounded text-center" style="display: none;">
                            <img id="img-preview" src="#" alt="Preview" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 220px; display: none;">
                            <div id="pdf-preview" style="display: none;">
                                <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
                            </div>
                            <div id="file-info" class="font-weight-bold text-dark small"></div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between py-3">
                    <a href="{{ route('purchase-requests.index') }}" class="btn btn-default px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewFile(input) {
        const file = input.files[0];
        const label = document.getElementById('file-label');
        const container = document.getElementById('file-preview-container');
        const img = document.getElementById('img-preview');
        const pdf = document.getElementById('pdf-preview');
        const info = document.getElementById('file-info');

        if (file) {
            label.textContent = file.name;
            info.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            container.style.display = 'block';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'inline-block';
                    pdf.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                img.style.display = 'none';
                pdf.style.display = 'block';
            } else {
                img.style.display = 'none';
                pdf.style.display = 'none';
            }
        } else {
            label.textContent = 'Pilih file PDF atau PNG...';
            container.style.display = 'none';
        }
    }
</script>
@endpush
