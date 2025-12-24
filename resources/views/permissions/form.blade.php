@extends('layouts.app')

@section('title', ($permission ? 'Edit' : 'Tambah') . ' Permission')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permission</a></li>
    <li class="breadcrumb-item active">{{ $permission ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $permission ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $permission ? 'Edit' : 'Tambah' }} Permission
                </h3>
            </div>
            <form action="{{ $permission ? route('permissions.update', $permission) : route('permissions.store') }}" method="POST">
                @csrf
                @if($permission)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $permission?->name) }}" 
                            placeholder="Contoh: view users, create products"
                            required>
                        <small class="text-muted">Format: [action] [module], contoh: view users, create products, delete purchase-orders</small>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
