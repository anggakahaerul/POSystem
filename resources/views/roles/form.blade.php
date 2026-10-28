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
        <!-- Left: Role Details & Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-{{ $role ? 'edit' : 'plus' }} text-primary mr-2"></i>
                        {{ $role ? 'Edit Role' : 'Tambah Role Baru' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name" class="font-weight-bold text-dark">
                            Nama Role / Peran Jabatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $role?->name) }}" 
                            placeholder="Contoh: Staff Purchasing, Reviewer Pajak"
                            required {{ $role && $role->name === 'Admin' ? 'readonly' : '' }}>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        @if($role && $role->name === 'Admin')
                            <small class="form-text text-muted">Role Admin adalah peran sistem utama dan namanya tidak dapat diubah.</small>
                        @endif
                    </div>

                    <div class="p-3 bg-light rounded border mb-2">
                        <div class="small font-weight-bold text-muted text-uppercase mb-1">Status Hak Akses</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold text-dark" style="font-size: 15px;">
                                <span id="selectedCountNumber">0</span> / {{ $totalPermissionsCount }}
                            </span>
                            <span class="badge badge-primary px-2 py-1" id="selectedPercentBadge">0%</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 d-flex justify-content-between">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm px-3">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Role
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Right: Structured Permissions (15 Modules) -->
        <div class="col-lg-8">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                        <div>
                            <h3 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-key text-info mr-2"></i>Daftar Hak Akses Sistem ({{ count($structuredModules) }} Modul)
                            </h3>
                        </div>
                        <div class="card-tools d-flex align-items-center" style="gap: 6px;">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllGlobal(true)">
                                <i class="fas fa-check-square mr-1"></i> Centang Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllGlobal(false)">
                                <i class="fas fa-square mr-1"></i> Kosongkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="p-3 bg-light border-bottom">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="permFilterInput" class="form-control" 
                               placeholder="Filter modul atau izin akses..." onkeyup="filterPermissions()">
                    </div>
                </div>

                <div class="card-body p-3" style="max-height: 65vh; overflow-y: auto;">
                    <div class="row" id="modulesGrid">
                        @foreach($structuredModules as $modKey => $module)
                            <div class="col-md-6 mb-3 module-card-item" data-module-key="{{ $modKey }}" data-module-name="{{ strtolower($module['name']) }}">
                                <div class="card h-100 border shadow-none mb-0">
                                    <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                                {{ $module['name'] }}
                                            </span>
                                            <span class="badge badge-light border text-muted ml-1" style="font-size: 10px;">
                                                {{ count($module['permissions']) }} izin
                                            </span>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input module-check-all" 
                                                   id="mod_all_{{ $modKey }}" 
                                                   onchange="toggleModulePermissions('{{ $modKey }}', this.checked)">
                                            <label class="custom-control-label small font-weight-normal text-muted" for="mod_all_{{ $modKey }}">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        @foreach($module['permissions'] as $perm)
                                            @php
                                                $isChecked = $role ? $role->hasPermissionTo($perm['name']) : false;
                                            @endphp
                                            <div class="perm-row p-2 rounded mb-1 border-bottom-light"
                                                 data-perm-id="{{ $perm['id'] }}" 
                                                 data-perm-name="{{ $perm['name'] }}"
                                                 data-perm-label="{{ strtolower($perm['action_label']) }}"
                                                 data-perm-action="{{ $perm['action_key'] }}">
                                                <div class="custom-control custom-checkbox d-flex align-items-start">
                                                    <input type="checkbox" class="custom-control-input perm-checkbox" 
                                                           id="perm_{{ $perm['id'] }}" 
                                                           name="permissions[]" 
                                                           value="{{ $perm['id'] }}"
                                                           data-module="{{ $modKey }}"
                                                           data-name="{{ $perm['name'] }}"
                                                           data-action="{{ $perm['action_key'] }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           onchange="onPermCheckChange('{{ $modKey }}')">
                                                    <label class="custom-control-label font-weight-bold text-dark d-block pl-1" for="perm_{{ $perm['id'] }}" style="font-size: 12px; cursor: pointer;">
                                                        {{ $perm['action_label'] }}
                                                        <span class="badge badge-light border text-muted ml-1 font-weight-normal" style="font-size: 10px; font-family: monospace;">
                                                            {{ $perm['name'] }}
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="text-muted pl-4" style="font-size: 11px; margin-top: -2px;">
                                                    {{ $perm['action_desc'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .perm-row {
        transition: background-color 0.15s ease-in-out;
    }
    .perm-row:hover {
        background-color: #f1f5f9;
    }
    .border-bottom-light {
        border-bottom: 1px solid #f1f5f9;
    }
    .border-bottom-light:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    const totalPermissions = {{ $totalPermissionsCount }};



    function updateSelectedCounter() {
        const checkedCount = $('.perm-checkbox:checked').length;
        $('#selectedCountNumber').text(checkedCount);

        const percent = totalPermissions > 0 ? Math.round((checkedCount / totalPermissions) * 100) : 0;
        $('#selectedPercentBadge').text(`${percent}%`);

        $('.module-card-item').each(function() {
            const modKey = $(this).data('module-key');
            const totalInMod = $(this).find('.perm-checkbox').length;
            const checkedInMod = $(this).find('.perm-checkbox:checked').length;
            $(`#mod_all_${modKey}`).prop('checked', totalInMod > 0 && totalInMod === checkedInMod);
        });
    }

    function onPermCheckChange(modKey) {
        updateSelectedCounter();
    }

    function toggleModulePermissions(modKey, isChecked) {
        $(`.perm-checkbox[data-module="${modKey}"]`).prop('checked', isChecked);
        updateSelectedCounter();
    }

    function checkAllGlobal(isChecked) {
        $('.perm-checkbox').prop('checked', isChecked);
        $('.module-check-all').prop('checked', isChecked);
        updateSelectedCounter();
    }



    function filterPermissions() {
        const query = $('#permFilterInput').val().toLowerCase().trim();

        if (!query) {
            $('.module-card-item').show();
            $('.perm-row').show();
            return;
        }

        $('.module-card-item').each(function() {
            const modName = $(this).data('module-name') || '';
            const modKey = $(this).data('module-key') || '';
            let hasVisibleChild = false;

            if (modName.includes(query) || modKey.includes(query)) {
                $(this).show();
                $(this).find('.perm-row').show();
                return;
            }

            $(this).find('.perm-row').each(function() {
                const label = $(this).data('perm-label') || '';
                const code = $(this).data('perm-name') || '';

                if (label.includes(query) || code.includes(query)) {
                    $(this).show();
                    hasVisibleChild = true;
                } else {
                    $(this).hide();
                }
            });

            if (hasVisibleChild) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $(document).ready(function() {
        updateSelectedCounter();
    });
</script>
@endpush
