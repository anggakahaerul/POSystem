@extends('layouts.app')

@section('title', 'Manajemen Hak Akses (Permissions)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User Management</a></li>
    <li class="breadcrumb-item active">Permission</li>
@endsection

@section('content')
<!-- Metric Cards -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-3 mb-md-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-key fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Hak Akses</span>
                    <h3 class="mb-0 font-weight-bold">{{ $permissions->total() }}</h3>
                    <small class="text-muted">Terdaftar dalam sistem</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-3 mb-md-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-cubes fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Modul Fitur</span>
                    <h3 class="mb-0 font-weight-bold">{{ count($moduleDefs) }} Modul</h3>
                    <small class="text-muted">Struktur modul aplikasi</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-user-tag fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Role Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $roles->count() }} Role</h3>
                    <small class="text-muted">Peran pemegang izin</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-shield-alt mr-2 text-primary"></i>Daftar Hak Akses Sistem (Permissions)
        </h3>
        <div class="card-tools ml-auto">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Hak Akses Baru
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('permissions.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama atau kode permission..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="module" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Modul --</option>
                        @foreach($moduleDefs as $key => $m)
                            <option value="{{ $key }}" {{ request('module') == $key ? 'selected' : '' }}>
                                {{ $m['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="role" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                                Role {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2 flex-grow-1">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('module') || request('role'))
                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Modern Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th width="320">Hak Akses & Deskripsi</th>
                        <th width="200">Modul</th>
                        <th width="120" class="text-center">Tipe Aksi</th>
                        <th>Digunakan Oleh Role</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $index => $permission)
                        @php
                            $meta = $permission->meta;
                            $actionType = $meta['action_type'] ?? 'special';
                            $badgeClass = match($actionType) {
                                'view' => 'badge-success',
                                'create' => 'badge-info',
                                'edit' => 'badge-warning text-dark',
                                'delete' => 'badge-danger',
                                'approval' => 'badge-purple text-white',
                                default => 'badge-primary',
                            };
                            $badgeIcon = match($actionType) {
                                'view' => 'fa-eye',
                                'create' => 'fa-plus',
                                'edit' => 'fa-pen',
                                'delete' => 'fa-trash',
                                'approval' => 'fa-signature',
                                default => 'fa-bolt',
                            };
                        @endphp
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $permissions->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                    {{ $meta['action_label'] }}
                                </div>
                                <div class="mt-1">
                                    <code class="text-primary font-weight-bold" style="font-size: 0.8rem; background: #eef2ff; padding: 2px 6px; border-radius: 4px;">
                                        {{ $permission->name }}
                                    </code>
                                </div>
                                <small class="text-muted d-block mt-1" style="line-height: 1.3;">
                                    {{ $meta['action_desc'] }}
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-light border px-2 py-1 font-weight-bold text-dark d-inline-flex align-items-center">
                                    <i class="{{ $meta['module_icon'] }} mr-1 text-{{ $meta['module_color'] }}"></i>
                                    {{ $meta['module_name'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} px-2 py-1" style="{{ $actionType === 'approval' ? 'background-color: #7c3aed;' : '' }}">
                                    <i class="fas {{ $badgeIcon }} mr-1"></i>
                                    {{ ucfirst($actionType) }}
                                </span>
                            </td>
                            <td>
                                @if($permission->roles->count() > 0)
                                    <div class="d-flex flex-wrap gap-1" style="gap: 4px;">
                                        @foreach($permission->roles as $role)
                                            <span class="badge badge-light border border-secondary-subtle px-2 py-1" style="font-size: 0.76rem;">
                                                <i class="fas fa-user-tag text-secondary mr-1"></i>{{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge badge-light text-muted border px-2 py-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-minus-circle mr-1 text-secondary"></i>Belum Dipakai
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Edit Permission & Role" 
                                        onclick="openEditModal({{ $permission->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Hapus Permission" 
                                        onclick="deletePermission({{ $permission->id }}, '{{ $permission->name }}', {{ $permission->roles->count() }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-key fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <h6 class="font-weight-bold">Tidak ada data permission</h6>
                                    <small>Gunakan filter lain atau klik tombol "Tambah Hak Akses Baru" di atas.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($permissions->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $permissions->firstItem() }}</b> - <b>{{ $permissions->lastItem() }}</b> dari <b>{{ $permissions->total() }}</b> permission
                </div>
                <div>
                    {{ $permissions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL TAMBAH PERMISSION (MODUL + AKSI CHECKLIST)                      -->
<!-- ===================================================================== -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white px-4 py-3" style="background-color: #0f172a !important; border-bottom: 3px solid #3b82f6;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0">
                        <i class="fas fa-shield-alt text-primary mr-2"></i>Tambah Hak Akses (Permission)
                    </h5>
                    <small class="text-white-50">Tentukan modul dan aksi yang diizinkan dalam sistem</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="createPermissionForm" onsubmit="event.preventDefault(); submitCreatePermission();">
                @csrf
                <input type="hidden" name="mode" id="createMode" value="module">

                <div class="modal-body px-4 py-3">
                    <!-- Step 1: Select Module -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-folder-open text-primary mr-1"></i> 1. Pilih Modul Sistem <span class="text-danger">*</span>
                        </label>
                        <select class="form-control form-control-lg border-primary-subtle" id="create_module_select" name="module" onchange="onModuleChange(this.value)" style="border-radius: 8px;">
                            <option value="" selected disabled>-- Pilih Modul Aplikasi --</option>
                            @foreach($moduleDefs as $modKey => $mDef)
                                <option value="{{ $modKey }}" data-icon="{{ $mDef['icon'] }}" data-color="{{ $mDef['color'] }}">
                                    {{ $mDef['name'] }} ({{ $modKey }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 2: Check Actions (Dynamic based on selected module) -->
                    <div class="form-group mb-3" id="actions_wrapper" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-tasks text-success mr-1"></i> 2. Centang Aksi yang Ingin Dibuat <span class="text-danger">*</span>
                            </label>
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary mr-1" onclick="selectAllActions(true)">
                                    <i class="fas fa-check-double mr-1"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="selectAllActions(false)">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <div class="border rounded p-3 bg-light" id="actions_container" style="max-height: 260px; overflow-y: auto;">
                            <!-- Dynamic Action Checkboxes Injected Here -->
                        </div>
                    </div>

                    <!-- Step 3: Direct Role Assignment -->
                    <div class="card border mb-0" style="border-radius: 8px;">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-dark small text-uppercase">
                                <i class="fas fa-user-tag text-warning mr-1"></i> 3. Berikan Hak Ini Langsung ke Role (Opsional)
                            </span>
                            <small class="text-muted">Bisa diatur nanti di menu Role</small>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @foreach($roles as $r)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input create-role-cb" 
                                                   id="create_role_{{ $r->id }}" name="roles[]" value="{{ $r->id }}">
                                            <label class="custom-control-label font-weight-normal text-dark" for="create_role_{{ $r->id }}" style="cursor: pointer;">
                                                {{ $r->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btnSaveCreatePermission">
                        <i class="fas fa-save mr-1"></i> Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL EDIT PERMISSION (RENAME & MANAGE ROLES DIRECTLY)                -->
<!-- ===================================================================== -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white px-4 py-3" style="background-color: #0f172a !important; border-bottom: 3px solid #f59e0b;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0">
                        <i class="fas fa-edit text-warning mr-2"></i>Edit Hak Akses & Penugasan Role
                    </h5>
                    <small class="text-white-50" id="edit_perm_subtitle">Perbarui nama atau role pemilik hak ini</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editPermissionForm" onsubmit="event.preventDefault(); submitEditPermission();">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_permission_id" name="permission_id">

                <div class="modal-body px-4 py-3">
                    <div class="alert alert-light border p-3 mb-3" style="border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 mr-3 bg-light border text-primary">
                                <i class="fas fa-info-circle fa-lg"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark" id="edit_friendly_label"></span>
                                <small class="text-muted" id="edit_friendly_desc"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1" for="edit_name">
                            Kode Nama Permission <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <small class="text-muted">Mengubah kode nama permission dapat memengaruhi pengecekan di kode sistem.</small>
                    </div>

                    <!-- Manage Roles Directly -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-1 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-tag text-warning mr-1"></i> Penugasan Role Pemilik Hak Ini</span>
                            <small class="text-muted font-weight-normal">Centang role yang berhak</small>
                        </label>
                        <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            <div class="row">
                                @foreach($roles as $r)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input edit-role-cb" 
                                                   id="edit_role_{{ $r->id }}" name="roles[]" value="{{ $r->id }}">
                                            <label class="custom-control-label font-weight-normal text-dark" for="edit_role_{{ $r->id }}" style="cursor: pointer;">
                                                {{ $r->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btnSaveEditPermission">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const moduleDefinitions = @json($moduleDefs);
    const storeUrl = "{{ route('permissions.store') }}";
    const basePermissionUrl = "{{ url('permissions') }}";

    function openCreateModal() {
        $('#createPermissionForm')[0].reset();
        $('#create_module_select').val('');
        $('#actions_wrapper').hide();
        $('#actions_container').empty();
        $('.create-role-cb').prop('checked', false);
        $('#createPermissionModal').modal('show');
    }

    function onModuleChange(moduleKey) {
        const modDef = moduleDefinitions[moduleKey];
        if (!modDef || !modDef.actions) {
            $('#actions_wrapper').hide();
            return;
        }

        const container = $('#actions_container');
        container.empty();

        let html = '<div class="row">';
        for (const [actionKey, act] of Object.entries(modDef.actions)) {
            const permCode = `${actionKey} ${moduleKey}`;
            const badgeIcon = act.type === 'view' ? 'fa-eye text-success' :
                             act.type === 'create' ? 'fa-plus text-info' :
                             act.type === 'edit' ? 'fa-pen text-warning' :
                             act.type === 'delete' ? 'fa-trash text-danger' :
                             act.type === 'approval' ? 'fa-signature text-purple' : 'fa-bolt text-primary';

            html += `
                <div class="col-md-6 mb-2">
                    <div class="border rounded p-2 bg-white h-100 shadow-none">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input create-action-cb" 
                                   id="act_${actionKey}" name="actions[]" value="${actionKey}" checked>
                            <label class="custom-control-label font-weight-bold text-dark d-block" for="act_${actionKey}" style="cursor: pointer; font-size: 0.88rem;">
                                <i class="fas ${badgeIcon} mr-1"></i> ${act.label}
                            </label>
                            <code class="small text-muted d-block mt-0 font-monospace">${permCode}</code>
                            <small class="text-secondary d-block mt-1" style="font-size: 0.75rem; line-height: 1.2;">${act.desc}</small>
                        </div>
                    </div>
                </div>
            `;
        }
        html += '</div>';

        container.html(html);
        $('#actions_wrapper').slideDown(200);
    }

    function selectAllActions(checked) {
        $('.create-action-cb').prop('checked', checked);
    }

    function submitCreatePermission() {
        const btn = $('#btnSaveCreatePermission');
        const module = $('#create_module_select').val();

        if (!module) {
            toastr.warning('Silakan pilih modul sistem terlebih dahulu.');
            return;
        }
        if ($('.create-action-cb:checked').length === 0) {
            toastr.warning('Pilih minimal satu aksi yang ingin dibuat.');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            data: $('#createPermissionForm').serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Hak Akses');
                if (res.success) {
                    $('#createPermissionModal').modal('hide');
                    toastr.success(res.message);
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    toastr.error(res.message || 'Gagal menyimpan permission.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Hak Akses');
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
                toastr.error(msg);
            }
        });
    }

    function openEditModal(id) {
        $.ajax({
            url: `${basePermissionUrl}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    $('#edit_permission_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_friendly_label').text(data.meta.action_label);
                    $('#edit_friendly_desc').text(`${data.meta.module_name} — ${data.meta.action_desc}`);
                    $('#edit_perm_subtitle').text(`ID: #${data.id} • ${data.name}`);

                    // Pre-check roles
                    $('.edit-role-cb').prop('checked', false);
                    if (data.role_ids && data.role_ids.length > 0) {
                        data.role_ids.forEach(function(roleId) {
                            $(`#edit_role_${roleId}`).prop('checked', true);
                        });
                    }

                    $('#editPermissionModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Gagal mengambil data permission.');
            }
        });
    }

    function submitEditPermission() {
        const id = $('#edit_permission_id').val();
        const btn = $('#btnSaveEditPermission');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: `${basePermissionUrl}/${id}`,
            type: 'POST',
            data: $('#editPermissionForm').serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
                if (res.success) {
                    $('#editPermissionModal').modal('hide');
                    toastr.success(res.message);
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    toastr.error(res.message || 'Gagal memperbarui permission.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui.';
                toastr.error(msg);
            }
        });
    }

    function deletePermission(id, name, roleCount) {
        if (roleCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Permission Sedang Dipakai',
                text: `Permission "${name}" masih aktif digunakan oleh ${roleCount} role. Buka tombol Edit dan hilangkan centang role terlebih dahulu jika ingin menghapusnya.`,
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Permission?',
            text: `Permission "${name}" akan dihapus permanen dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${basePermissionUrl}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.reload(), 500);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Gagal menghapus permission.');
                    }
                });
            }
        });
    }
</script>
@endpush
