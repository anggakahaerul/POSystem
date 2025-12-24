@extends('layouts.app')

@section('title', 'Daftar Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Role</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-shield mr-2"></i>Daftar Role
        </h3>
        <div class="card-tools">
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Role
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search -->
        <form action="{{ route('roles.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama role..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    @if(request('search'))
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Role</th>
                        <th width="150" class="text-center">Jumlah Permission</th>
                        <th width="150" class="text-center">Jumlah User</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                    <tr>
                        <td>{{ $roles->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $role->name }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $role->permissions_count }} Permissions</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $role->users->count() }} Users</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($role->name !== 'Admin')
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" id="delete-form-{{ $role->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="confirmDelete('delete-form-{{ $role->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-user-shield fa-3x mb-3 d-block"></i>
                            Belum ada data role
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $roles->firstItem() ?? 0 }} - {{ $roles->lastItem() ?? 0 }} dari {{ $roles->total() }} data
            </div>
            <div>
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
