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
                <table class="table table-bordered table-striped table-hover table-valign-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="45" class="text-center">#</th>
                            <th width="125">No. PO</th>
                            <th>Vendor</th>
                            <th width="130" class="text-left">Total</th>
                            <th width="260" class="text-center">Roadmap PO</th>
                            <th width="135" class="text-center">Status PO</th>
                            <th width="135" class="text-center">Status Barang</th>
                            <th width="135" class="text-center">Status Pembayaran</th>
                            <th width="115">Dibuat Oleh</th>
                            <th width="90" class="text-center">Tanggal</th>
                            <th width="130" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $index => $po)
                            <tr>
                                <td class="text-center">{{ $purchaseOrders->firstItem() + $index }}</td>
                                <td><code>{{ $po->po_number }}</code></td>
                                <td>
                                    <strong>{{ $po->vendor->name ?? '-' }}</strong>
                                </td>
                                <td class="text-left font-weight-bold text-nowrap">{{ $po->formatted_total }}</td>
                                <!-- Roadmap PO Column (Visual 6 Tahapan) -->
                                <td class="text-center py-2" style="min-width: 250px; max-width: 280px;">
                                    @php
                                        $roadmap = $po->roadmap;
                                    @endphp
                                    <div class="table-roadmap-container">
                                        <!-- Connecting Line Track -->
                                        <div class="table-roadmap-track">
                                            <div class="table-roadmap-track-fill {{ $roadmap['progress'] == 100 ? 'bg-success' : ($roadmap['current_badge_class'] == 'danger' ? 'bg-danger' : 'bg-primary') }}" 
                                                 style="width: {{ $roadmap['progress'] }}%;"></div>
                                        </div>

                                        <!-- 6 Steps Nodes -->
                                        <div class="table-roadmap-steps">
                                            @foreach ($roadmap['steps'] as $st)
                                                @php
                                                    $isCompleted = ($st['status'] == 'completed');
                                                    $isCurrent = str_contains($st['status'], 'current');
                                                    $isRejected = ($st['status'] == 'rejected');
                                                @endphp
                                                <div class="table-roadmap-step {{ $st['status'] }}" title="{{ $st['step'] }}. {{ $st['name'] }}: {{ $st['badge'] }}">
                                                    <div class="table-roadmap-node {{ $isCompleted ? 'node-done' : ($isCurrent ? 'node-current' : ($isRejected ? 'node-rejected' : 'node-pending')) }}">
                                                        @if ($isCompleted)
                                                            <i class="fas fa-check"></i>
                                                        @elseif ($isCurrent)
                                                            @if ($st['step'] == 3 && $po->status == 'pending')
                                                                <i class="fas fa-clock"></i>
                                                            @elseif ($st['step'] == 4)
                                                                <i class="fas fa-box-open"></i>
                                                            @elseif ($st['step'] == 5)
                                                                <i class="fas fa-hourglass-half"></i>
                                                            @else
                                                                <i class="fas fa-dot-circle"></i>
                                                            @endif
                                                        @elseif ($isRejected)
                                                            <i class="fas fa-times"></i>
                                                        @else
                                                            {{ $st['step'] }}
                                                        @endif
                                                    </div>
                                                    <span class="table-roadmap-label">{{ $st['name'] == 'Persetujuan' ? 'Approve' : ($st['name'] == 'Penerimaan Barang' ? 'Barang' : ($st['name'] == 'PO Dibuat' ? 'Draft' : ($st['name'] == 'PO Disubmit' ? 'Submit' : ($st['name'] == 'Pembayaran' ? 'Bayar' : 'Selesai')))) }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Current Position Subtitle -->
                                        <div class="table-roadmap-pos mt-1">
                                            <span class="badge badge-pill badge-{{ $roadmap['current_badge_class'] }}">
                                                {{ $roadmap['current_label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status PO -->
                                <td class="text-center">
                                    <span class="badge badge-{{ $po->status_badge }} status-badge">
                                        <i class="{{ $po->status_icon }}"></i>{{ $po->status_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $totalQty = $po->details->sum('quantity');
                                        $receivedQty = $po->details->sum('received_quantity');
                                        $receivedPercent = $totalQty > 0 ? round(($receivedQty / $totalQty) * 100) : 0;
                                        
                                        $receivedItems = $po->details->filter(function($d) {
                                            return ($d->received_quantity ?? 0) > 0;
                                        });
                                        
                                        $unreceivedItems = $po->details->filter(function($d) {
                                            return ($d->quantity - ($d->received_quantity ?? 0)) > 0;
                                        });
                                    @endphp

                                    <span class="badge badge-{{ $po->receipt_status_badge }} status-badge receipt-popover-trigger"
                                        data-target="#receipt-popover-{{ $po->id }}"
                                        title="Arahkan kursor untuk melihat rincian barang">
                                        <i class="{{ $po->receipt_status_icon }}"></i>{{ $po->receipt_status_label }}
                                    </span>

                                    <!-- Popover Template Content -->
                                    <div id="receipt-popover-{{ $po->id }}" class="d-none">
                                        <div class="receipt-popover-content text-left">
                                            <!-- Summary Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                <strong class="text-dark" style="font-size: 0.8rem;">
                                                    <i class="fas fa-boxes text-primary mr-1"></i> {{ $po->po_number }}
                                                </strong>
                                                <span class="badge badge-{{ $po->receipt_status_badge }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                                    {{ $po->receipt_status_label }}
                                                </span>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem; margin-bottom: 2px;">
                                                    <span>Diterima: <strong>{{ $receivedQty }}</strong> / {{ $totalQty }} unit</span>
                                                    <span class="font-weight-bold text-dark">{{ $receivedPercent }}%</span>
                                                </div>
                                                <div class="progress" style="height: 5px; border-radius: 3px; background-color: #e2e8f0;">
                                                    <div class="progress-bar {{ $receivedPercent == 100 ? 'bg-success' : ($receivedPercent > 0 ? 'bg-info' : 'bg-secondary') }}"
                                                        role="progressbar" style="width: {{ $receivedPercent }}%;"></div>
                                                </div>
                                            </div>

                                            <!-- Section: Sudah Diterima -->
                                            @if ($receivedItems->count() > 0)
                                                <div class="mb-2">
                                                    <div class="text-success font-weight-bold mb-1" style="font-size: 0.73rem;">
                                                        <i class="fas fa-check-circle mr-1"></i> SUDAH DITERIMA:
                                                    </div>
                                                    <div class="bg-light rounded p-1 border">
                                                        @foreach ($receivedItems as $item)
                                                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom-dashed" style="font-size: 0.73rem;">
                                                                <span class="text-truncate mr-2" style="max-width: 170px;" title="{{ $item->product->name ?? 'Produk' }}">
                                                                    <i class="fas fa-check text-success mr-1" style="font-size: 0.65rem;"></i>
                                                                    <strong>{{ $item->product->name ?? 'Produk' }}</strong>
                                                                </span>
                                                                <span class="text-success font-weight-bold text-nowrap">
                                                                    {{ $item->received_quantity }} {{ $item->product->unit ?? '' }}
                                                                    @if ($item->is_fully_received)
                                                                        <small class="badge badge-success px-1" style="font-size: 0.6rem;">Lengkap</small>
                                                                    @else
                                                                        <small class="badge badge-info px-1" style="font-size: 0.6rem;">{{ $item->received_quantity }}/{{ $item->quantity }}</small>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Section: Belum Diterima / Sisa -->
                                            @if ($unreceivedItems->count() > 0)
                                                <div class="mb-1">
                                                    <div class="text-secondary font-weight-bold mb-1" style="font-size: 0.73rem;">
                                                        <i class="fas fa-clock mr-1 text-warning"></i> BELUM DITERIMA:
                                                    </div>
                                                    <div class="bg-light rounded p-1 border">
                                                        @foreach ($unreceivedItems as $item)
                                                            @php
                                                                $remaining = $item->quantity - ($item->received_quantity ?? 0);
                                                            @endphp
                                                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom-dashed" style="font-size: 0.73rem;">
                                                                <span class="text-truncate mr-2" style="max-width: 170px;" title="{{ $item->product->name ?? 'Produk' }}">
                                                                    <i class="fas fa-box text-muted mr-1" style="font-size: 0.65rem;"></i>
                                                                    {{ $item->product->name ?? 'Produk' }}
                                                                </span>
                                                                <span class="text-danger font-weight-bold text-nowrap">
                                                                    {{ $remaining }} {{ $item->product->unit ?? '' }}
                                                                    <small class="badge badge-secondary px-1" style="font-size: 0.6rem;">Kurang</small>
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($po->details->isEmpty())
                                                <div class="text-muted text-center py-2" style="font-size: 0.73rem;">
                                                    <em>Tidak ada item terdaftar</em>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $po->payment_status_badge }} status-badge">
                                        <i class="{{ $po->payment_status_icon }}"></i>{{ $po->payment_status_label }}
                                    </span>
                                </td>
                                <td>{{ $po->creator->name ?? '-' }}</td>
                                <td class="text-center text-nowrap">{{ $po->created_at->format('d/m/Y') }}</td>
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
                                <td colspan="11" class="text-center text-muted py-4">
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

@push('styles')
<style>
    /* Table row vertical alignment */
    .table-valign-middle th,
    .table-valign-middle td {
        vertical-align: middle !important;
    }

    /* Uniform status pill badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 132px;
        padding: 0.32rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        border-radius: 50rem;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: transform 0.15s ease;
    }

    .status-badge i {
        font-size: 0.82rem;
        margin-right: 0.35rem;
    }

    /* Receipt hoverable badge */
    .receipt-popover-trigger {
        cursor: pointer;
    }

    /* Receipt hoverable badge */
    .receipt-popover-trigger {
        cursor: pointer;
    }

    .receipt-popover-trigger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        filter: brightness(1.05);
    }

    /* Popover container & typography */
    .popover.receipt-popover-card {
        min-width: 290px;
        max-width: 350px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.18), 0 6px 12px -4px rgba(0, 0, 0, 0.08);
        z-index: 1060;
    }

    .popover.receipt-popover-card .popover-body {
        padding: 11px 13px;
    }

    .border-bottom-dashed:not(:last-child) {
        border-bottom: 1px dashed #e2e8f0;
    }

    /* ============================================================
       VISUAL ROADMAP PO (TABLE INLINE STEPPER)
       ============================================================ */
    .table-roadmap-container {
        position: relative;
        padding: 4px 2px;
        width: 100%;
        max-width: 270px;
        margin: 0 auto;
    }

    /* Track line behind the circles */
    .table-roadmap-track {
        position: absolute;
        top: 14px;
        left: 14px;
        right: 14px;
        height: 3px;
        background-color: #e2e8f0;
        border-radius: 2px;
        z-index: 1;
    }

    .table-roadmap-track-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    /* 6 Steps Flex Row */
    .table-roadmap-steps {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        z-index: 2;
    }

    .table-roadmap-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    /* Circular Node */
    .table-roadmap-node {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.62rem;
        font-weight: 700;
        margin-bottom: 3px;
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    /* Node State: Done / Completed (Green) */
    .table-roadmap-node.node-done {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: 2px solid #d1fae5;
    }

    /* Node State: Current / Active (Pulsing / Vibrant) */
    .table-roadmap-node.node-current {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border: 2px solid #bae6fd;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.25);
    }
    .table-roadmap-step.status-warning .table-roadmap-node.node-current {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-color: #fef3c7;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
    }
    .table-roadmap-step.status-info .table-roadmap-node.node-current {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        border-color: #cffafe;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.25);
    }

    /* Node State: Rejected (Red) */
    .table-roadmap-node.node-rejected {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: 2px solid #fee2e2;
    }

    /* Node State: Pending / Incomplete (Greyscale) */
    .table-roadmap-node.node-pending {
        background-color: #f8fafc;
        color: #94a3b8;
        border: 1.5px dashed #cbd5e1;
    }

    /* Tiny label under each node */
    .table-roadmap-label {
        font-size: 0.6rem;
        line-height: 1.1;
        color: #64748b;
        white-space: nowrap;
    }

    .table-roadmap-step.step-completed .table-roadmap-label {
        color: #065f46;
        font-weight: 600;
    }

    .table-roadmap-step.node-current .table-roadmap-label,
    .table-roadmap-step[class*="status-"] .table-roadmap-label {
        color: #0369a1;
        font-weight: 700;
    }

    .table-roadmap-step.step-greyscale .table-roadmap-label {
        color: #94a3b8;
    }

    /* Position Pill Badge */
    .table-roadmap-pos .badge {
        font-size: 0.68rem;
        padding: 0.22rem 0.55rem;
        font-weight: 600;
        letter-spacing: 0.2px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Receipt popover initialization
    $('.receipt-popover-trigger').each(function() {
        var $trigger = $(this);
        var targetId = $trigger.data('target');
        
        $trigger.popover({
            html: true,
            trigger: 'manual',
            placement: 'top',
            container: 'body',
            boundary: 'window',
            content: function() {
                return $(targetId).html();
            },
            template: '<div class="popover receipt-popover-card shadow-lg" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
        }).on('mouseenter', function() {
            var _this = this;
            $('.receipt-popover-trigger').not(_this).popover('hide');
            $(_this).popover('show');

            $('.popover').on('mouseleave', function() {
                $(_this).popover('hide');
            });
        }).on('mouseleave', function() {
            var _this = this;
            setTimeout(function() {
                if (!$('.popover:hover').length) {
                    $(_this).popover('hide');
                }
            }, 180);
        });
    });
});
</script>
@endpush
