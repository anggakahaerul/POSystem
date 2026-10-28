@extends('layouts.app')

@section('title', 'Daftar Departemen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Departemen</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-sitemap fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Departemen</span>
                    <h3 class="mb-0 font-weight-bold">{{ $departments->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-double fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Departemen Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $departments->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Anggota/User</span>
                    <h3 class="mb-0 font-weight-bold">{{ $departments->sum('users_count') }} Karyawan</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sitemap mr-2"></i>Daftar Departemen & Hierarki Manager per Cabang
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('master-data.export', 'departments') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data departemen ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('departments', 'Departemen')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Departemen
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('departments.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari kode, nama, atau deskripsi departemen..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Modern Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th width="100">Kode</th>
                        <th width="220">Nama Departemen</th>
                        <th>Manager Level 3, 4, 5 per Cabang</th>
                        <th width="90" class="text-center">Anggota</th>
                        <th width="90" class="text-center">Status</th>
                        <th width="90" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $index => $department)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $departments->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge badge-primary font-monospace">{{ $department->code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $department->name }}</div>
                                @if($department->description)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $department->description }}</small>
                                @endif
                            </td>
                            <!-- Manager per Cabang -->
                            <td>
                                @if($department->departmentBranches->count() > 0)
                                    <div class="row row-cols-1 row-cols-md-2 g-2">
                                        @foreach($department->departmentBranches as $deptBranch)
                                            <div class="col mb-2">
                                                <div class="p-2 rounded border bg-light" style="font-size: 0.82rem;">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <strong class="text-dark">
                                                            <i class="fas fa-building text-primary mr-1"></i>{{ $deptBranch->branch->name }}
                                                        </strong>
                                                        <span class="badge badge-secondary font-monospace" style="font-size: 0.68rem;">{{ $deptBranch->branch->code }}</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap" style="gap: 4px;">
                                                        <span class="badge badge-white border text-dark" title="Manager Level 3">
                                                            <span class="badge badge-primary mr-1">L3</span>{{ $deptBranch->managerL3?->name ?? '-' }}
                                                        </span>
                                                        <span class="badge badge-white border text-dark" title="Manager Level 4">
                                                            <span class="badge badge-info mr-1">L4</span>{{ $deptBranch->managerL4?->name ?? '-' }}
                                                        </span>
                                                        <span class="badge badge-white border text-dark" title="Manager Level 5">
                                                            <span class="badge badge-success mr-1">L5</span>{{ $deptBranch->managerL5?->name ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small italic">
                                        <i class="fas fa-exclamation-circle text-warning mr-1"></i>Belum ada manager cabang yang ditentukan
                                    </span>
                                @endif
                            </td>
                            <!-- Anggota -->
                            <td class="text-center">
                                <span class="badge badge-secondary px-2 py-1 font-weight-bold">
                                    {{ $department->users_count }} user
                                </span>
                            </td>
                            <!-- Status -->
                            <td class="text-center">
                                @if($department->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit Departemen & Manager Cabang" 
                                        onclick="openEditModal({{ $department->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteDepartment({{ $department->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-sitemap fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                    <h6 class="font-weight-bold">Belum Ada Departemen</h6>
                                    <p class="small mb-3">Klik tombol "Tambah Departemen" untuk membuat data departemen baru.</p>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                                        <i class="fas fa-plus mr-1"></i> Tambah Departemen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($departments->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $departments->firstItem() }}</b> - <b>{{ $departments->lastItem() }}</b> dari <b>{{ $departments->total() }}</b> departemen
                </div>
                <div>
                    {{ $departments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Departemen (Add & Edit) -->
<div class="modal fade" id="departmentModal" tabindex="-1" role="dialog" aria-labelledby="departmentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title font-weight-bold" id="departmentModalTitle">
                    <i class="fas fa-sitemap text-warning mr-2"></i>
                    <span class="title-text">Tambah Departemen</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="departmentForm" onsubmit="event.preventDefault(); submitDepartmentForm();">
                @csrf
                <div class="modal-body p-4">
                    <!-- Bagian Informasi Departemen -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="code" class="font-weight-bold">Kode Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="Contoh: PUR / FIN / MIS" maxlength="30" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="font-weight-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Purchasing & Procurement" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold d-block">Status</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label font-weight-bold text-dark" for="is_active">Aktif</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="description" class="font-weight-bold">Deskripsi / Ruang Lingkup</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Penjelasan singkat ruang lingkup departemen...">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Pengaturan Manager Level 3, 4, 5 per Cabang -->
                    <div class="card border mb-0 shadow-none mt-2" style="border-radius: 8px;">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-building text-primary mr-2"></i>Pengaturan Manager Level 3, 4, 5 per Cabang
                                </h6>
                                <small class="text-muted">Tentukan Manager Approval (Level 3, Level 4, dan Level 5) untuk setiap cabang pada departemen ini.</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th width="200">Cabang</th>
                                            <th><span class="badge badge-primary mr-1">L3</span> Manager Level 3</th>
                                            <th><span class="badge badge-info mr-1">L4</span> Manager Level 4</th>
                                            <th><span class="badge badge-success mr-1">L5</span> Manager Level 5</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($branches as $branch)
                                            <tr>
                                                <td class="align-middle bg-light-subtle">
                                                    <input type="hidden" name="branches[{{ $branch->id }}][branch_id]" value="{{ $branch->id }}">
                                                    <div class="font-weight-bold text-dark">
                                                        <i class="fas fa-building text-secondary mr-1"></i>{{ $branch->name }}
                                                    </div>
                                                    <small class="text-muted font-monospace">{{ $branch->code }}</small>
                                                </td>
                                                <!-- L3 Cabang -->
                                                <td>
                                                    <select class="form-control form-control-sm branch-manager-select" 
                                                            name="branches[{{ $branch->id }}][manager_l3_id]" 
                                                            id="branch_{{ $branch->id }}_l3">
                                                        <option value="">-- Pilih Manager L3 --</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">
                                                                {{ $user->name }} ({{ $user->branch?->name ?? 'HO' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <!-- L4 Cabang -->
                                                <td>
                                                    <select class="form-control form-control-sm branch-manager-select" 
                                                            name="branches[{{ $branch->id }}][manager_l4_id]" 
                                                            id="branch_{{ $branch->id }}_l4">
                                                        <option value="">-- Pilih Manager L4 --</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">
                                                                {{ $user->name }} ({{ $user->branch?->name ?? 'HO' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <!-- L5 Cabang -->
                                                <td>
                                                    <select class="form-control form-control-sm branch-manager-select" 
                                                            name="branches[{{ $branch->id }}][manager_l5_id]" 
                                                            id="branch_{{ $branch->id }}_l5">
                                                        <option value="">-- Pilih Manager L5 --</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">
                                                                {{ $user->name }} ({{ $user->branch?->name ?? 'HO' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btnSaveDepartment">
                        <i class="fas fa-save mr-1"></i> Simpan Data Departemen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.master-import-modal')
@endsection

@push('scripts')
<script>
    const storeUrl = "{{ route('departments.store') }}";
    const baseEditUrl = "{{ url('departments') }}";

    function resetDepartmentForm() {
        $('#departmentForm')[0].reset();
        $('.branch-manager-select').val('');
        $('#is_active').prop('checked', true);
    }

    function openCreateModal() {
        ModalCrud.openCreate('#departmentModal', '#departmentForm', 'Tambah Departemen Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                resetDepartmentForm();
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#departmentModal', '#departmentForm', `${baseEditUrl}/${id}`, 'Edit Departemen & Manager Cabang', {
            updateUrl: `${baseEditUrl}/${id}`,
            beforeShow: function() {
                resetDepartmentForm();
            },
            afterPopulate: function(data, form) {
                form.find('#code').val(data.code || '');
                form.find('#name').val(data.name || '');
                form.find('#description').val(data.description || '');
                form.find('#is_active').prop('checked', Boolean(data.is_active));

                // Populate Branch-specific managers if any
                if (data.department_branches && data.department_branches.length > 0) {
                    data.department_branches.forEach(function(db) {
                        $('#branch_' + db.branch_id + '_l3').val(db.manager_l3_id || '');
                        $('#branch_' + db.branch_id + '_l4').val(db.manager_l4_id || '');
                        $('#branch_' + db.branch_id + '_l5').val(db.manager_l5_id || '');
                    });
                }
            }
        });
    }

    function submitDepartmentForm() {
        ModalCrud.submitAjax('#departmentForm', '#departmentModal');
    }

    function deleteDepartment(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Departemen ini akan dihapus secara permanen.');
    }
</script>
@endpush
