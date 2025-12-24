@extends('layouts.app')

@section('title', 'Cabang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Cabang</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-building mr-2"></i>Daftar Cabang
        </h3>
        <div class="card-tools">
            <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Cabang
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search -->
        <form method="GET" class="mb-3">
            <div class="input-group" style="max-width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kode/nama..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="50">#</th>
                        <th width="120">Kode</th>
                        <th>Nama Cabang</th>
                        <th>Alamat</th>
                        <th width="120">Telepon</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $index => $branch)
                    <tr>
                        <td>{{ $branches->firstItem() + $index }}</td>
                        <td><code class="font-weight-bold">{{ $branch->code }}</code></td>
                        <td>{{ $branch->name }}</td>
                        <td>{{ Str::limit($branch->address, 50) ?: '-' }}</td>
                        <td>{{ $branch->phone ?: '-' }}</td>
                        <td class="text-center">
                            @if($branch->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline" id="delete-form-{{ $branch->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-form-{{ $branch->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-building fa-3x mb-3 d-block"></i>
                            Belum ada data cabang
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $branches->links() }}</div>
    </div>
</div>
@endsection
