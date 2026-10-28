@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Primary KPI Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small font-weight-bold text-uppercase">Total Purchase Orders</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold text-dark mb-2">{{ $stats['total_po'] ?? 0 }}</h2>
                <div class="d-flex align-items-center text-muted small">
                    <a href="{{ route('purchase-orders.index') }}" class="text-primary font-weight-bold">
                        Lihat Semua Transaksi <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small font-weight-bold text-uppercase">Menunggu Approval</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold text-warning mb-2">{{ $stats['pending_po'] ?? 0 }}</h2>
                <div class="d-flex align-items-center small">
                    <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}" class="text-warning font-weight-bold">
                        Perlu Tindakan <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small font-weight-bold text-uppercase">PO Disetujui</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold text-success mb-2">{{ $stats['approved_po'] ?? 0 }}</h2>
                <div class="d-flex align-items-center small">
                    <a href="{{ route('purchase-orders.index', ['status' => 'approved']) }}" class="text-success font-weight-bold">
                        Siap Proses Penerimaan <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small font-weight-bold text-uppercase">PO Ditolak / Dibatalkan</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold text-danger mb-2">{{ $stats['rejected_po'] ?? 0 }}</h2>
                <div class="d-flex align-items-center small">
                    <a href="{{ route('purchase-orders.index', ['status' => 'rejected']) }}" class="text-danger font-weight-bold">
                        Lihat Riwayat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats & Master Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-muted small font-weight-bold text-uppercase">Item Produk</span>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $stats['total_products'] ?? 0 }} SKU</h4>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Buka</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-truck fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-muted small font-weight-bold text-uppercase">Mitra Vendor</span>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $stats['total_vendors'] ?? 0 }} Rekanan</h4>
                </div>
                <a href="{{ route('vendors.index') }}" class="btn btn-sm btn-outline-success">Buka</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                    <i class="fas fa-folder fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-muted small font-weight-bold text-uppercase">Kategori Barang</span>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $stats['total_categories'] ?? 0 }} Kategori</h4>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-info">Buka</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent PO Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Purchase Order Terbaru
                </h3>
                <div class="card-tools">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Vendor</th>
                                <th>Total Nilai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPOs ?? [] as $po)
                            <tr>
                                <td>
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="font-weight-bold text-primary font-monospace">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $po->vendor->name ?? '-' }}</div>
                                </td>
                                <td class="font-weight-bold text-dark">
                                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $po->status_badge }} px-3 py-1 font-weight-bold">
                                        <i class="{{ $po->status_icon }} mr-1"></i>{{ $po->status_label }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $po->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-secondary btn-sm" title="Buka Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-invoice fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-0 font-weight-bold">Belum ada riwayat transaksi Purchase Order</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Pending Approval Widget -->
    <div class="col-lg-4">
        <!-- Quick Actions Panel -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2 text-warning"></i>Aksi Cepat
                </h3>
            </div>
            <div class="card-body">
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-block mb-3 py-2 text-left d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-plus-circle mr-2"></i> Buat PO Baru</span>
                    <i class="fas fa-chevron-right small"></i>
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-block mb-3 py-2 text-left d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-box mr-2 text-info"></i> Kelola Master Produk</span>
                    <i class="fas fa-chevron-right small"></i>
                </a>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary btn-block mb-3 py-2 text-left d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-truck mr-2 text-success"></i> Kelola Master Vendor</span>
                    <i class="fas fa-chevron-right small"></i>
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-block py-2 text-left d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-folder mr-2 text-primary"></i> Kelola Master Kategori</span>
                    <i class="fas fa-chevron-right small"></i>
                </a>
            </div>
        </div>

        <!-- Pending Approval Widget -->
        @if(isset($pendingPOs) && $pendingPOs->count() > 0)
        <div class="card border-warning">
            <div class="card-header bg-warning-light">
                <h3 class="card-title text-warning font-weight-bold">
                    <i class="fas fa-bell mr-2"></i>Menunggu Persetujuan ({{ $pendingPOs->count() }})
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pendingPOs->take(5) as $po)
                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="list-group-item list-group-item-action p-3">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark">{{ $po->po_number }}</span>
                            <span class="badge badge-warning">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <small class="text-muted d-block">{{ $po->vendor->name ?? '-' }}</small>
                        <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ $po->created_at->diffForHumans() }}</small>
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="card-footer text-center bg-white">
                <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}" class="text-warning font-weight-bold small">
                    Buka Halaman Approval <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
