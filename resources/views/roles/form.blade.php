@extends('layouts.app')

@section('title', ($role ? 'Edit' : 'Tambah') . ' Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
    <li class="breadcrumb-item active">{{ $role ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<form action="{{ $role ? route('roles.update', $role) : route('roles.store') }}" method="POST">
    @csrf
    @if($role)
        @method('PUT')
    @endif
    
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-{{ $role ? 'edit' : 'plus' }} mr-2"></i>
                        {{ $role ? 'Edit' : 'Tambah' }} Role
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $role?->name) }}" 
                            placeholder="Contoh: Manager, Staff"
                            required {{ $role && $role->name === 'Admin' ? 'readonly' : '' }}>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-2"></i>Permissions</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-default" id="check-all">
                            <i class="fas fa-check-square mr-1"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-default" id="uncheck-all">
                            <i class="fas fa-square mr-1"></i> Hapus Semua
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permissions as $group => $groupPermissions)
                    <div class="permission-group mb-3">
                        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-2">
                            <i class="fas fa-folder mr-1"></i> {{ ucfirst($group) }}
                        </h6>
                        <div class="row">
                            @foreach($groupPermissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" 
                                        id="perm_{{ $permission->id }}" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}"
                                        {{ $role && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $('#check-all').click(function() {
        $('.permission-checkbox').prop('checked', true);
    });
    
    $('#uncheck-all').click(function() {
        $('.permission-checkbox').prop('checked', false);
    });
</script>
@endpush
