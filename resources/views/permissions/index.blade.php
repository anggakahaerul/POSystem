@extends('layouts.app')

@section('title', 'Daftar Permission')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Permission</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-key mr-2"></i>Daftar Permission
        </h3>
        <div class="card-tools">
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Permission
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('permissions.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari permission..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="group" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Modul --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                {{ ucfirst($group) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    @if(request('search') || request('group'))
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
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
                        <th>Nama Permission</th>
                        <th width="120">Modul</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $index => $permission)
                    <tr>
                        <td>{{ $permissions->firstItem() + $index }}</td>
                        <td>
                            <code>{{ $permission->name }}</code>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ explode(' ', $permission->name)[1] ?? 'other' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline" id="delete-form-{{ $permission->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="confirmDelete('delete-form-{{ $permission->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-key fa-3x mb-3 d-block"></i>
                            Belum ada data permission
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $permissions->firstItem() ?? 0 }} - {{ $permissions->lastItem() ?? 0 }} dari {{ $permissions->total() }} data
            </div>
            <div>
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
