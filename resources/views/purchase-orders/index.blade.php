@extends('layouts.app')

@section('title', 'Daftar Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Purchase Order</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice mr-2"></i>Daftar Purchase Order
            </h3>
            <div class="card-tools">
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Buat PO Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('purchase-orders.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari No. PO / Vendor..."
                                value="{{ request('search') }}">
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
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="vendor_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Vendor --</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"
                            placeholder="Dari" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                            placeholder="Sampai" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-1">
                        @if (request('search') || request('status') || request('vendor_id') || request('date_from') || request('date_to'))
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary" title="Reset Filter">
                                <i class="fas fa-times"></i>
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
                            <th width="140">No. PO</th>
                            <th>Vendor</th>
                            <th width="130" class="text-right">Total</th>
                            <th width="120" class="text-center">Status PO</th>
                            <th width="120" class="text-center">Status Barang</th>
                            <th width="120" class="text-center">Status Pembayaran</th>
                            <th width="130">Dibuat Oleh</th>
                            <th width="100" class="text-center">Tanggal</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $index => $po)
                            <tr>
                                <td>{{ $purchaseOrders->firstItem() + $index }}</td>
                                <td><code>{{ $po->po_number }}</code></td>
                                <td>
                                    <strong>{{ $po->vendor->name ?? '-' }}</strong>
                                </td>
                                <td class="text-right">{{ $po->formatted_total }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $po->status_badge }}">{{ $po->status_label }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-{{ $po->receipt_status_badge }}">{{ $po->receipt_status_label }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-{{ $po->payment_status_badge }}">{{ $po->payment_status_label }}</span>
                                </td>
                                <td>{{ $po->creator->name ?? '-' }}</td>
                                <td class="text-center">{{ $po->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-info btn-sm"
                                            title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($po->canEdit())
                                            <a href="{{ route('purchase-orders.edit', $po) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST"
                                                class="d-inline" id="delete-form-{{ $po->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="confirmDelete('delete-form-{{ $po->id }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($po->status == 'approved')
                                            <a href="{{ route('purchase-orders.print', $po) }}"
                                                class="btn btn-success btn-sm" title="Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                                    Belum ada data Purchase Order
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $purchaseOrders->firstItem() ?? 0 }} - {{ $purchaseOrders->lastItem() ?? 0 }} dari
                    {{ $purchaseOrders->total() }} data
                </div>
                <div>
                    {{ $purchaseOrders->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ \App\Models\PurchaseOrder::where('status', 'draft')->count() }}</h3>
                    <p>Draft</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <a href="{{ route('purchase-orders.index', ['status' => 'draft']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\PurchaseOrder::where('status', 'pending')->count() }}</h3>
                    <p>Pending Approval</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
                <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\PurchaseOrder::where('status', 'approved')->count() }}</h3>
                    <p>Approved</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
                <a href="{{ route('purchase-orders.index', ['status' => 'approved']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\PurchaseOrder::where('status', 'rejected')->count() }}</h3>
                    <p>Rejected</p>
                </div>
                <div class="icon"><i class="fas fa-times"></i></div>
                <a href="{{ route('purchase-orders.index', ['status' => 'rejected']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
