@extends('layouts.app')

@section('title', 'Daftar Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Vendor</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-building mr-2"></i>Daftar Vendor
        </h3>
        <div class="card-tools">
            <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Vendor
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('vendors.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama/kode/email/telepon vendor..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    @if(request('search') || request('status'))
                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
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
                        <th width="100">Kode</th>
                        <th>Nama Vendor</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th width="100" class="text-center">Jumlah PO</th>
                        <th width="80" class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $index => $vendor)
                    <tr>
                        <td>{{ $vendors->firstItem() + $index }}</td>
                        <td><code>{{ $vendor->code }}</code></td>
                        <td>
                            <strong>{{ $vendor->name }}</strong>
                            @if($vendor->contact_person)
                                <br><small class="text-muted">CP: {{ $vendor->contact_person }}</small>
                            @endif
                        </td>
                        <td>{{ $vendor->phone ?: '-' }}</td>
                        <td>{{ $vendor->email ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $vendor->purchase_orders_count }} PO</span>
                        </td>
                        <td class="text-center">
                            @if($vendor->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline" id="delete-form-{{ $vendor->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="confirmDelete('delete-form-{{ $vendor->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-building fa-3x mb-3 d-block"></i>
                            Belum ada data vendor
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $vendors->firstItem() ?? 0 }} - {{ $vendors->lastItem() ?? 0 }} dari {{ $vendors->total() }} data
            </div>
            <div>
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
