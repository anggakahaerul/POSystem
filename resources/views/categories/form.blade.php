@extends('layouts.app')

@section('title', ($category ? 'Edit' : 'Tambah') . ' Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active">{{ $category ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $category ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $category ? 'Edit' : 'Tambah' }} Kategori
                </h3>
            </div>
            <form action="{{ $category ? route('categories.update', $category) : route('categories.store') }}" method="POST">
                @csrf
                @if($category)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $category?->name) }}" 
                            placeholder="Masukkan nama kategori"
                            required autofocus>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="3" placeholder="Deskripsi kategori (opsional)">{{ old('description', $category?->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Kategori Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
