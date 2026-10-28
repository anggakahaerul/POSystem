@extends('layouts.app')

@section('title', ($product ? 'Edit' : 'Tambah') . ' Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
    <li class="breadcrumb-item active">{{ $product ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $product ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $product ? 'Edit' : 'Tambah' }} Produk
                </h3>
            </div>
            <form action="{{ $product ? route('products.update', $product) : route('products.store') }}" method="POST">
                @csrf
                @if($product)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" 
                                    class="form-control @error('sku') is-invalid @enderror" 
                                    value="{{ old('sku', $product?->sku ?? $suggestedSku) }}" 
                                    placeholder="Kode SKU produk"
                                    required>
                                @error('sku')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $product?->name) }}" 
                            placeholder="Masukkan nama produk"
                            required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="unit" id="unit" 
                                    class="form-control @error('unit') is-invalid @enderror" 
                                    value="{{ old('unit', $product?->unit ?? 'pcs') }}" 
                                    placeholder="pcs, kg, liter"
                                    required>
                                @error('unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" 
                                    class="form-control @error('stock') is-invalid @enderror" 
                                    value="{{ old('stock', $product?->stock ?? 0) }}" 
                                    min="0"
                                    required>
                                @error('stock')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="min_stock">Minimum Stok</label>
                        <input type="number" name="min_stock" id="min_stock" 
                            class="form-control @error('min_stock') is-invalid @enderror" 
                            value="{{ old('min_stock', $product?->min_stock ?? 0) }}" 
                            min="0">
                        <small class="text-muted">Alert jika stok di bawah nilai ini</small>
                        @error('min_stock')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="3" placeholder="Deskripsi produk (opsional)">{{ old('description', $product?->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Produk Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Money format
    $(document).on('input', '.money-format', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    });
</script>
@endpush
