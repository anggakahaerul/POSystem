@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">User Management</a></li>
    <li class="breadcrumb-item active">Role</li>
@endsection

@section('content')
<!-- KPI Metric Cards -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded p-3 mr-3" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5;">
                    <i class="fas fa-user-tag fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Master Roles</span>
                    <h3 class="mb-0 font-weight-bold">{{ $roles->total() }} Role</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded p-3 mr-3" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Cakupan Sistem</span>
                    <h3 class="mb-0 font-weight-bold">{{ count($structuredModules) }} Modul <small class="text-muted font-weight-normal" style="font-size: 14px;">({{ $totalPermissionsCount }} Hak Akses)</small></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-shield-alt text-primary mr-2"></i>Daftar Hak Akses & Peran Jabatan (Role)
        </h3>
        <div class="card-tools ml-auto">
            <button type="button" class="btn btn-primary btn-sm px-3 font-weight-bold shadow-sm" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Role Baru
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <form action="{{ route('roles.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama role..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2 px-3">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Roles Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center text-muted font-weight-bold">#</th>
                        <th width="220">Nama Role</th>
                        <th>Cakupan Hak Akses Modul</th>
                        <th width="150" class="text-center">Total Izin</th>
                        <th width="140" class="text-center">Pengguna</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                        @php
                            $rolePermCount = $role->permissions_count;
                            $isFull = ($rolePermCount >= $totalPermissionsCount);
                            
                            $moduleMap = [];
                            foreach ($role->permissions as $p) {
                                $parts = explode(' ', $p->name);
                                $mKey = $parts[1] ?? 'other';
                                $moduleMap[$mKey] = ($moduleMap[$mKey] ?? 0) + 1;
                            }
                        @endphp
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $roles->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $role->name }}</div>
                                @if($role->name === 'Admin')
                                    <span class="badge badge-primary px-2 py-0" style="font-size: 11px;">Peran Sistem Utama</span>
                                @endif
                            </td>
                            <td>
                                @if($isFull)
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-check-circle mr-1"></i> Akses Penuh ({{ count($structuredModules) }} Modul)
                                    </span>
                                @elseif(empty($moduleMap))
                                    <span class="badge badge-light border text-muted">Belum ada hak akses</span>
                                @else
                                    <div class="d-flex flex-wrap" style="gap: 4px;">
                                        @php $shown = 0; @endphp
                                        @foreach($structuredModules as $modKey => $modInfo)
                                            @if(isset($moduleMap[$modKey]))
                                                @php $shown++; @endphp
                                                @if($shown <= 4)
                                                    <span class="badge badge-light border text-dark" title="{{ $modInfo['name'] }}: {{ $moduleMap[$modKey] }} hak akses">
                                                        {{ $modInfo['name'] }} ({{ $moduleMap[$modKey] }})
                                                    </span>
                                                @endif
                                            @endif
                                        @endforeach
                                        @if(count($moduleMap) > 4)
                                            <span class="badge badge-secondary" title="Serta {{ count($moduleMap) - 4 }} modul lainnya">
                                                +{{ count($moduleMap) - 4 }} modul lainnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $isFull ? 'badge-primary' : 'badge-light border text-dark' }} font-weight-bold px-2 py-1">
                                    {{ $role->permissions_count }} / {{ $totalPermissionsCount }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border font-weight-bold px-2 py-1">
                                    {{ $role->users_count }} pengguna
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Edit Hak Akses" 
                                        onclick="openEditModal({{ $role->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($role->name !== 'Admin')
                                        <button type="button" class="btn btn-outline-danger btn-sm" title="Hapus Role" 
                                            onclick="deleteRole({{ $role->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-shield-alt fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data role</p>
                                    <small>Klik tombol "Tambah Role Baru" untuk mulai mendefinisikan peran jabatan.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $roles->firstItem() }}</b> - <b>{{ $roles->lastItem() }}</b> dari <b>{{ $roles->total() }}</b> role
                </div>
                <div>
                    {{ $roles->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Role (Add & Edit) -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3 border-bottom">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="roleModalTitle">
                        <i class="fas fa-user-tag text-primary mr-2"></i>
                        <span class="title-text">Tambah Role Baru</span>
                    </h5>
                    <small class="text-muted">Atur nama peran jabatan dan pilih izin akses sistem yang diberikan</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="roleForm" onsubmit="event.preventDefault(); submitRoleForm();">
                @csrf
                <div class="modal-body p-4">
                    <!-- Role Name -->
                    <div class="form-group mb-4">
                        <label for="name" class="font-weight-bold text-dark">
                            Nama Role / Peran Jabatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" 
                               placeholder="Contoh: Staff Purchasing, Manager Operasional, Reviewer Pajak" required>
                        <small class="form-text text-muted">Gunakan nama jabatan yang jelas dan mudah diidentifikasi.</small>
                    </div>

                    <!-- Filter Bar & Counter with Centang Semua / Kosongkan -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="permFilterInput" class="form-control" 
                                       placeholder="Ketik untuk filter modul atau nama hak akses..." onkeyup="filterPermissions()">
                            </div>
                        </div>
                        <div class="col-md-7 d-flex justify-content-md-end align-items-center flex-wrap" style="gap: 8px;">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="checkAllGlobal(true)">
                                <i class="fas fa-check-square mr-1"></i> Centang Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAllGlobal(false)">
                                <i class="fas fa-square mr-1"></i> Kosongkan
                            </button>
                            <span class="badge badge-light border text-dark font-weight-bold px-3 py-2" style="font-size: 13px;" id="selectedCounterBadge">
                                Terpilih: <span id="selectedCountNumber">0</span> dari {{ $totalPermissionsCount }} hak akses
                            </span>
                        </div>
                    </div>

                    <!-- Permissions Grid (15 Modules) -->
                    <div class="permissions-container" style="max-height: 52vh; overflow-y: auto; padding-right: 4px;">
                        <div class="row" id="modulesGrid">
                            @foreach($structuredModules as $modKey => $module)
                                <div class="col-lg-6 mb-3 module-card-item" data-module-key="{{ $modKey }}" data-module-name="{{ strtolower($module['name']) }}">
                                    <div class="card h-100 border shadow-none mb-0">
                                        <!-- Module Card Header -->
                                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="font-weight-bold text-dark" style="font-size: 14px;">
                                                    {{ $module['name'] }}
                                                </span>
                                                <span class="badge badge-light border text-muted ml-1" style="font-size: 11px;">
                                                    {{ count($module['permissions']) }} izin
                                                </span>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input module-check-all" 
                                                       id="mod_all_{{ $modKey }}" data-target-module="{{ $modKey }}" 
                                                       onchange="toggleModulePermissions('{{ $modKey }}', this.checked)">
                                                <label class="custom-control-label small font-weight-normal text-muted" for="mod_all_{{ $modKey }}">
                                                    Pilih Semua
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Module Card Body (Actions List) -->
                                        <div class="card-body p-2">
                                            @foreach($module['permissions'] as $perm)
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
                                                               onchange="onPermCheckChange('{{ $modKey }}')">
                                                        <label class="custom-control-label font-weight-bold text-dark d-block pl-1" for="perm_{{ $perm['id'] }}" style="font-size: 13px; cursor: pointer;">
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

                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="btnSaveRole">
                        <i class="fas fa-save mr-1"></i> Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    const storeUrl = "{{ route('roles.store') }}";
    const baseEditUrl = "{{ url('roles') }}";
    const totalPermissions = {{ $totalPermissionsCount }};



    function updateSelectedCounter() {
        const checkedCount = $('.perm-checkbox:checked').length;
        $('#selectedCountNumber').text(checkedCount);

        const badge = $('#selectedCounterBadge');
        if (checkedCount === totalPermissions) {
            badge.removeClass('badge-light text-dark').addClass('badge-success text-white');
        } else if (checkedCount > 0) {
            badge.removeClass('badge-success text-white').addClass('badge-light text-dark');
        } else {
            badge.removeClass('badge-success text-white').addClass('badge-light text-dark');
        }

        // Sync module header checkboxes
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

            // If module name itself matches, show entire module
            if (modName.includes(query) || modKey.includes(query)) {
                $(this).show();
                $(this).find('.perm-row').show();
                return;
            }

            // Otherwise filter individual actions
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

    function openCreateModal() {
        $('#permFilterInput').val('');
        $('.module-card-item').show();
        $('.perm-row').show();

        ModalCrud.openCreate('#roleModal', '#roleForm', 'Tambah Role Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#name').val('').prop('readonly', false);
                form.find('.perm-checkbox').prop('checked', false);
                form.find('.module-check-all').prop('checked', false);
                updateSelectedCounter();
            }
        });
    }

    function openEditModal(id) {
        $('#permFilterInput').val('');
        $('.module-card-item').show();
        $('.perm-row').show();

        ModalCrud.openEdit('#roleModal', '#roleForm', `${baseEditUrl}/${id}`, 'Edit Role & Hak Akses', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('.perm-checkbox').prop('checked', false);
                form.find('.module-check-all').prop('checked', false);

                // Prevent renaming Admin
                if (data.name === 'Admin') {
                    form.find('#name').prop('readonly', true);
                } else {
                    form.find('#name').prop('readonly', false);
                }

                if (data.permission_ids && Array.isArray(data.permission_ids)) {
                    data.permission_ids.forEach(function(permId) {
                        $(`#perm_${permId}`).prop('checked', true);
                    });
                }

                updateSelectedCounter();
            }
        });
    }

    function submitRoleForm() {
        ModalCrud.submitAjax('#roleForm', '#roleModal');
    }

    function deleteRole(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Role ini akan dihapus secara permanen.');
    }
</script>
@endpush
