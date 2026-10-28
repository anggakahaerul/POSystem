@extends('layouts.app')

@section('title', ($permission ? 'Edit' : 'Tambah') . ' Hak Akses (Permission)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permission</a></li>
    <li class="breadcrumb-item active">{{ $permission ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-white py-3">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-{{ $permission ? 'edit' : 'plus' }} mr-2 text-primary"></i>
                    {{ $permission ? 'Edit' : 'Tambah' }} Hak Akses (Permission)
                </h3>
            </div>
            <form action="{{ $permission ? route('permissions.update', $permission) : route('permissions.store') }}" method="POST">
                @csrf
                @if($permission)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    @if($permission)
                        @php
                            $meta = \App\Http\Controllers\PermissionController::parsePermission($permission->name);
                        @endphp
                        <div class="alert alert-light border p-3 mb-3">
                            <span class="d-block font-weight-bold text-dark">{{ $meta['action_label'] }}</span>
                            <small class="text-muted">{{ $meta['module_name'] }} &bull; {{ $meta['action_desc'] }}</small>
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <label for="name" class="font-weight-bold">Nama Kode Permission <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $permission?->name) }}" 
                            placeholder="Contoh: view purchase-orders, create products"
                            required>
                        <small class="text-muted">Format standar: <code>[aksi] [nama-modul]</code>, contoh: <code>view purchase-orders</code></small>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Roles -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold d-block mb-2">Penugasan Role Pemilik Hak Ini</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                                   {{ (isset($permission) && $permission->hasRole($role->name)) ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-normal text-dark" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between py-3">
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
