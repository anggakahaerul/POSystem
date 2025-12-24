@extends('layouts.app')

@section('title', ($user ? 'Edit' : 'Tambah') . ' User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
    <li class="breadcrumb-item active">{{ $user ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $user ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $user ? 'Edit' : 'Tambah' }} User
                </h3>
            </div>
            <form action="{{ $user ? route('users.update', $user) : route('users.store') }}" method="POST">
                @csrf
                @if($user)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $user?->name) }}" 
                            placeholder="Masukkan nama lengkap"
                            required autofocus>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email', $user?->email) }}" 
                            placeholder="user@example.com"
                            required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ $user ? 'Password Baru' : 'Password' }} @if(!$user)<span class="text-danger">*</span>@endif</label>
                        <input type="password" name="password" id="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            placeholder="{{ $user ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                            {{ $user ? '' : 'required' }}>
                        @if($user)
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        @endif
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password @if(!$user)<span class="text-danger">*</span>@endif</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="form-control" 
                            placeholder="Ulangi password"
                            {{ $user ? '' : 'required' }}>
                    </div>

                    <div class="form-group">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" 
                                    {{ old('role', $user?->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
