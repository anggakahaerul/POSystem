@extends('layouts.app')

@section('title', 'Daftar Barang Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active">Barang Masuk</li>
@endsection

@push('styles')
<style>
    .stat-card-clean {
        border-radius: 10px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-card-clean:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.08) !important;
    }
    .icon-box {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.15rem;
    }
    .table-goods thead th {
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        vertical-align: middle;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    .table-goods tbody td {
        vertical-align: middle;
        padding: 12px 14px;
        font-size: 0.9rem;
    }
    .table-goods tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-soft-success {
        background-color: #e8f8f0;
        color: #198754;
        border: 1px solid #c3e6cb;
    }
    .badge-soft-info {
        background-color: #e7f4fa;
        color: #0c82ba;
        border: 1px solid #bce1f1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Stat Cards Ringkas -->
    <div class="row mb-3">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm stat-card-clean mb-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-box bg-light text-primary mr-3 shadow-none">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Unit Diterima</div>
                        <h4 class="mb-0 font-weight-bold text-dark">{{ number_format($stats['total_received_qty'], 0, ',', '.') }} <span class="small font-weight-normal text-muted" style="font-size: 0.8rem;">Unit</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm stat-card-clean mb-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-box bg-light text-success mr-3 shadow-none">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Diterima Lengkap</div>
                        <h4 class="mb-0 font-weight-bold text-success">{{ number_format($stats['full_received_count'], 0, ',', '.') }} <span class="small font-weight-normal text-muted" style="font-size: 0.8rem;">Item</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm stat-card-clean mb-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="icon-box bg-light text-info mr-3 shadow-none">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Diterima Sebagian</div>
                        <h4 class="mb-0 font-weight-bold text-info">{{ number_format($stats['partial_received_count'], 0, ',', '.') }} <span class="small font-weight-normal text-muted" style="font-size: 0.8rem;">Item</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-3 border-bottom bg-light" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <form method="GET" action="{{ route('goods-receipts.index') }}">
                <!-- Baris 1: Pencarian dan Filter Dropdown Utama -->
                <div class="row align-items-center">
                    <!-- Search Input -->
                    <div class="col-12 col-md-5 col-lg-4 mb-2 mb-lg-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control border-left-0" 
                                   placeholder="Cari barang, SKU, No. PO, vendor..." 
                                   value="{{ $search }}">
                        </div>
                    </div>

                    <!-- Filter Cabang -->
                    <div class="col-6 col-md-3 col-lg-3 mb-2 mb-lg-0">
                        <select name="branch_id" class="form-control">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Kategori Asset -->
                    <div class="col-6 col-md-4 col-lg-2 mb-2 mb-lg-0">
                        <select name="asset" class="form-control">
                            <option value="">Semua Asset</option>
                            @foreach($assetTypes as $at)
                                <option value="{{ $at->code }}" {{ $asset == $at->code ? 'selected' : '' }}>
                                    {{ $at->code }} ({{ $at->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="col-12 col-md-4 col-lg-3 mb-2 mb-lg-0">
                        <select name="receipt_type" class="form-control">
                            <option value="all" {{ $receiptType === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="full" {{ $receiptType === 'full' ? 'selected' : '' }}>Diterima Lengkap</option>
                            <option value="partial" {{ $receiptType === 'partial' ? 'selected' : '' }}>Diterima Sebagian</option>
                        </select>
                    </div>
                </div>

                <!-- Baris 2: Filter Rentang Tanggal & Tombol Aksi -->
                <div class="row align-items-center mt-2 pt-2 border-top">
                    <!-- Rentang Tanggal -->
                    <div class="col-12 col-md-7 col-lg-6 mb-2 mb-md-0 d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="text-muted small font-weight-bold text-nowrap">
                            <i class="far fa-calendar-alt text-primary mr-1"></i> Periode Terima:
                        </span>
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <input type="date" name="date_from" class="form-control form-control-sm" title="Tanggal Dari" value="{{ $dateFrom }}" style="width: 155px;">
                            <span class="text-muted small font-weight-bold">s/d</span>
                            <input type="date" name="date_to" class="form-control form-control-sm" title="Tanggal Sampai" value="{{ $dateTo }}" style="width: 155px;">
                        </div>
                    </div>

                    <!-- Tombol Aksi (Filter, Reset, Export) -->
                    <div class="col-12 col-md-5 col-lg-6 d-flex justify-content-md-end align-items-center" style="gap: 8px;">
                        <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold shadow-none" title="Terapkan Filter">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @php
                            $hasActiveFilter = $search || $branchId || $asset || $receiptType !== 'all' || 
                                (request()->has('date_from') && ($dateFrom !== $defaultDateFrom || $dateTo !== $defaultDateTo));
                        @endphp
                        @if($hasActiveFilter)
                            <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary btn-sm px-2" title="Kembalikan ke Default">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </a>
                        @endif
                        <a href="{{ route('goods-receipts.export', request()->query()) }}" class="btn btn-success btn-sm px-3 font-weight-bold shadow-none" title="Download Excel / CSV">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Barang Masuk -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-goods table-hover mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th style="width: 130px;">Tanggal Terima</th>
                            <th>Nama Barang & SKU</th>
                            <th style="width: 110px;" class="text-center">Asset</th>
                            <th style="width: 145px;">Qty Diterima</th>
                            <th style="width: 175px;">Kode Inventaris (Sync)</th>
                            <th style="width: 155px;">No. PO</th>
                            <th style="width: 150px;">No. PR</th>
                            <th style="width: 160px;">Vendor</th>
                            <th style="width: 125px;">Cabang / Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $receiveDate = $item->received_at ?? $item->purchaseOrder->received_at;
                                $isFull = ($item->received_quantity >= $item->quantity);
                            @endphp
                            <tr>
                                <!-- Tanggal Terima -->
                                <td>
                                    @if($receiveDate)
                                        <div class="font-weight-bold text-dark">
                                            <i class="far fa-calendar-alt text-muted mr-1"></i>{{ $receiveDate->format('d/m/Y') }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            {{ $receiveDate->format('H:i') }} WIB
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Nama Barang & SKU -->
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        {{ $item->product->name ?? 'Produk Dihapus' }}
                                    </div>
                                    <div class="text-muted small">
                                        <span class="badge badge-light border text-muted px-2 py-0" style="font-size: 0.72rem; font-family: monospace;">
                                            SKU: {{ $item->product->sku ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Asset -->
                                <td class="text-center">
                                    @php
                                        $assetCode = $item->product->category->asset ?? null;
                                        $categoryName = $item->product->category->name ?? null;
                                        $assetObj = $assetCode ? $assetTypes->firstWhere('code', $assetCode) : null;
                                        $badgeColor = $assetObj->badge_color ?? ($assetCode === 'MIS' ? 'primary' : ($assetCode === 'HRGA' ? 'info' : 'secondary'));
                                    @endphp
                                    @if($assetCode)
                                        <span class="badge badge-{{ $badgeColor }} px-2 py-1 font-weight-bold" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                                            {{ $assetCode }}
                                        </span>
                                        @if($categoryName)
                                            <div class="text-muted small text-truncate mt-1" style="font-size: 0.72rem; max-width: 110px;" title="{{ $categoryName }}">
                                                {{ $categoryName }}
                                            </div>
                                        @endif
                                    @else
                                        @if($categoryName)
                                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.72rem;">{{ $categoryName }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Qty Diterima -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-bold text-dark mr-2" style="font-size: 1rem;">
                                            {{ number_format($item->received_quantity, 0, ',', '.') }}
                                        </span>
                                        <span class="text-muted small">
                                            / {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->product->unit ?? 'Pcs' }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        @if($isFull)
                                            <span class="badge badge-soft-success px-2 py-0 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-check-circle mr-1"></i>Lengkap
                                            </span>
                                        @else
                                            <span class="badge badge-soft-info px-2 py-0 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-clock mr-1"></i>Sebagian
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Kode Inventaris (Auto-Sync Opsi A) -->
                                <td>
                                    @if($item->assets && $item->assets->count() > 0)
                                        @php
                                            $firstCode = $item->assets->first()->asset_code;
                                            $lastCode = $item->assets->last()->asset_code;
                                            $countAssets = $item->assets->count();
                                        @endphp
                                        <a href="{{ route('assets.index', ['search' => $item->purchaseOrder->po_number ?? $firstCode]) }}"
                                           class="badge badge-light border text-primary font-weight-bold px-2 py-1 d-inline-block"
                                           style="font-family: monospace; font-size: 0.76rem;"
                                           title="Klik untuk lihat & lengkapi spesifikasi di Daftar Barang Inventaris">
                                            <i class="fas fa-qrcode mr-1"></i>
                                            @if($countAssets === 1)
                                                {{ $firstCode }}
                                            @else
                                                {{ $firstCode }} ({{ $countAssets }} Unit)
                                            @endif
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- No. PO -->
                                <td>
                                    @if($item->purchaseOrder)
                                        <a href="{{ route('purchase-orders.show', $item->purchaseOrder->id) }}" 
                                           class="font-weight-bold text-primary" 
                                           title="Buka Detail PO" 
                                           target="_blank">
                                            <i class="fas fa-file-invoice mr-1 text-muted"></i>{{ $item->purchaseOrder->po_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- No. PR -->
                                <td>
                                    @if($item->purchaseOrder && $item->purchaseOrder->purchaseRequest)
                                        <a href="{{ route('purchase-requests.show', $item->purchaseOrder->purchaseRequest->id) }}" 
                                           class="font-weight-bold text-secondary" 
                                           title="Buka Detail PR" 
                                           target="_blank">
                                            <i class="fas fa-file-alt mr-1 text-muted"></i>{{ $item->purchaseOrder->purchaseRequest->pr_number }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Vendor -->
                                <td>
                                    <div class="text-dark font-weight-500" title="{{ $item->purchaseOrder->vendor->name ?? '-' }}">
                                        <i class="fas fa-store text-muted mr-1 small"></i>{{ Str::limit($item->purchaseOrder->vendor->name ?? '-', 20) }}
                                    </div>
                                </td>

                                <!-- Cabang -->
                                <td>
                                    <div class="text-secondary small font-weight-bold">
                                        <i class="fas fa-map-marker-alt text-danger mr-1 small"></i>{{ $item->purchaseOrder->branch->name ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 text-muted" style="opacity: 0.4;"></i>
                                    <p class="mb-0 font-weight-bold text-secondary">Belum ada riwayat barang masuk.</p>
                                    <small class="text-muted">Barang yang sudah diterima dari PO akan otomatis tampil di daftar ini.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Pagination -->
        @if ($items->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2 px-3 border-top">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong> - <strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> barang masuk
                </div>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
