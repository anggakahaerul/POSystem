@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_po'] ?? 0 }}</h3>
                <p>Total Purchase Order</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending_po'] ?? 0 }}</h3>
                <p>PO Menunggu Approval</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['approved_po'] ?? 0 }}</h3>
                <p>PO Disetujui</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('purchase-orders.index', ['status' => 'approved']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['rejected_po'] ?? 0 }}</h3>
                <p>PO Ditolak</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('purchase-orders.index', ['status' => 'rejected']) }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Master Data Stats -->
    <div class="col-lg-4 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Produk</span>
                <span class="info-box-number">{{ $stats['total_products'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-tags"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Kategori</span>
                <span class="info-box-number">{{ $stats['total_categories'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Vendor</span>
                <span class="info-box-number">{{ $stats['total_vendors'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent PO -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Purchase Order Terbaru</h3>
                <div class="card-tools">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-tool btn-sm">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>No. PO</th>
                            <th>Vendor</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPOs ?? [] as $po)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-orders.show', $po->id) }}">
                                    {{ $po->po_number }}
                                </a>
                            </td>
                            <td>{{ $po->vendor->name ?? '-' }}</td>
                            <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $po->status_badge }} px-2 py-1">
                                    <i class="{{ $po->status_icon }} mr-1"></i>{{ $po->status_label }}
                                </span>
                            </td>
                            <td>{{ $po->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions / Pending Approval -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-plus mr-2"></i> Buat PO Baru
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-box mr-2"></i> Tambah Produk
                </a>
                <a href="{{ route('vendors.create') }}" class="btn btn-secondary btn-block mb-2">
                    <i class="fas fa-truck mr-2"></i> Tambah Vendor
                </a>
            </div>
        </div>

        @if(isset($pendingPOs) && $pendingPOs->count() > 0)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Menunggu Approval
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($pendingPOs->take(5) as $po)
                    <li class="item">
                        <div class="product-info">
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="product-title">
                                {{ $po->po_number }}
                                <span class="badge badge-warning float-right">
                                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                </span>
                            </a>
                            <span class="product-description">
                                {{ $po->vendor->name ?? '-' }} - {{ $po->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
