@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">User Management</a></li>
    <li class="breadcrumb-item active">User</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Pengguna</span>
                    <h3 class="mb-0 font-weight-bold">{{ $users->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Role Administrator</span>
                    <h3 class="mb-0 font-weight-bold">{{ $users->filter(fn($u) => $u->hasRole('Admin'))->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                    <i class="fas fa-sitemap fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Departemen</span>
                    <h3 class="mb-0 font-weight-bold">{{ $departments->count() }} Divisi</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users-cog mr-2"></i>Daftar Pengguna Aplikasi
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                <i class="fas fa-user-plus mr-1"></i> Tambah User
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('users.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="department_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="branch_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Cabang --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="role" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('role') || request('department_id') || request('branch_id'))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="50" class="text-center">#</th>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th width="140">Cabang</th>
                        <th width="150">Departemen</th>
                        <th width="140" class="text-center">Role / Hak Akses</th>
                        <th width="110" class="text-center">Terdaftar</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #4f46e5, #4338ca); font-size: 0.95rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="badge badge-info"><i class="fas fa-check mr-1"></i> Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">
                                <i class="fas fa-envelope mr-1 text-secondary"></i>{{ $user->email }}
                            </td>
                            <td>
                                @if($user->branch)
                                    <span class="badge badge-light border font-weight-bold text-dark">
                                        <i class="fas fa-building mr-1 text-primary"></i>{{ $user->branch->name }}
                                    </span>
                                @else
                                    <span class="text-muted small italic"><i class="fas fa-globe mr-1"></i>Pusat (HO)</span>
                                @endif
                            </td>
                            <td>
                                @if($user->department)
                                    <span class="badge badge-primary font-weight-bold">
                                        <i class="fas fa-sitemap mr-1"></i>{{ $user->department->name }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @forelse($user->roles as $role)
                                    <span class="badge badge-secondary">{{ $role->name }}</span>
                                @empty
                                    <span class="badge badge-light">Tidak ada role</span>
                                @endforelse
                            </td>
                            <td class="text-center text-muted small">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $user->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteUser({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users-slash fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data user</p>
                                    <small>Klik tombol "Tambah User" di atas untuk menambahkan akun baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $users->firstItem() }}</b> - <b>{{ $users->lastItem() }}</b> dari <b>{{ $users->total() }}</b> pengguna
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form User (Add & Edit) -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">
                    <i class="fas fa-user text-primary mr-2"></i>
                    <span class="title-text">Tambah Pengguna</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm" onsubmit="event.preventDefault(); submitUserForm();">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama lengkap user" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="user@perusahaan.com" required>
                    </div>

                    <div class="form-group">
                        <label for="department_id">Departemen / Divisi</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">-- Tanpa Departemen / Umum --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Departemen tempat user bertugas untuk menentukan hierarki approval.</small>
                    </div>

                    <div class="form-group">
                        <label for="branch_id">Penugasan Cabang</label>
                        <select class="form-control" id="branch_id" name="branch_id">
                            <option value="">-- Kantor Pusat (HO) / Global --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Cabang fisik tempat user bertugas.</small>
                    </div>

                    <div class="form-group">
                        <label for="role">Hak Akses / Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span id="passwordRequired" class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter">
                        <small id="passwordHelp" class="form-text text-muted" style="display: none;">
                            Kosongkan password jika tidak ingin mengubah password saat ini.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveUser">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const storeUrl = "{{ route('users.store') }}";
    const baseEditUrl = "{{ url('users') }}";

    function openCreateModal() {
        ModalCrud.openCreate('#userModal', '#userForm', 'Tambah Pengguna Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#passwordRequired').show();
                form.find('#passwordHelp').hide();
                form.find('#password').attr('required', true);
                form.find('#department_id').val('');
                form.find('#branch_id').val('');
                form.find('#role').val('');
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#userModal', '#userForm', `${baseEditUrl}/${id}`, 'Edit Pengguna', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#passwordRequired').hide();
                form.find('#passwordHelp').show();
                form.find('#password').removeAttr('required').val('');
                form.find('#department_id').val(data.department_id || '');
                form.find('#branch_id').val(data.branch_id || '');
                form.find('#role').val(data.role || '');
            }
        });
    }

    function submitUserForm() {
        ModalCrud.submitAjax('#userForm', '#userModal');
    }

    function deleteUser(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Pengguna ini akan dihapus secara permanen.');
    }
</script>
@endpush
