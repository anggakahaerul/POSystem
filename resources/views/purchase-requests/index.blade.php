@extends('layouts.app')

@section('title', 'Permintaan Pembelian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Permintaan Pembelian</li>
@endsection

@push('styles')
<style>
    .po-popover-trigger {
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .po-popover-trigger:hover {
        background-color: #007bff !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }
    .popover.po-list-popover {
        border: none !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        max-width: 330px !important;
        z-index: 1060 !important;
    }
    .po-item-row {
        transition: background-color 0.15s ease;
    }
    .po-item-row:hover {
        background-color: #f1f5f9 !important;
    }
</style>
@endpush

@section('content')
    <!-- Stat Counters -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-file-upload fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Total Request</span>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Menunggu PO</span>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm mb-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Sudah Jadi PO</span>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['processed'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold m-0 text-dark">
                <i class="fas fa-file-alt mr-2 text-primary"></i>Daftar Permintaan Pembelian
            </h3>
            <div class="card-tools">
                <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Upload Request Baru
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form action="{{ route('purchase-requests.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari No. PR atau judul..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu PO</option>
                            <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Sudah Jadi PO</option>
                        </select>
                    </div>
                    @if(request()->hasAny(['search', 'status']))
                        <div class="col-md-2">
                            <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 150px;">No. PR</th>
                            <th>Judul Request</th>
                            <th>Lampiran</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 160px;">Pemohon & Tanggal</th>
                            <th style="width: 240px;" class="text-right text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseRequests as $pr)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('purchase-requests.show', $pr->id) }}" class="text-primary">
                                        {{ $pr->pr_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $pr->title }}</div>
                                    @if($pr->description)
                                        <div class="text-muted small text-truncate" style="max-width: 320px;">
                                            {{ $pr->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($pr->attachment)
                                        <a href="{{ route('purchase-requests.download', $pr->id) }}" target="_blank"
                                            class="btn btn-xs {{ $pr->is_pdf ? 'btn-danger' : 'btn-info' }} px-2 py-1"
                                            title="Buka lampiran">
                                            @if($pr->is_pdf)
                                                <i class="fas fa-file-pdf mr-1"></i>PDF
                                            @else
                                                <i class="fas fa-image mr-1"></i>PNG
                                            @endif
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pr->status === 'processed')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i>Sudah Jadi PO
                                        </span>
                                        @php
                                            $poList = $pr->purchaseOrders;
                                            $poCount = $poList->count();
                                        @endphp

                                        @if($poCount === 1)
                                            @php $singlePo = $poList->first(); @endphp
                                            <div class="small mt-1">
                                                <a href="{{ route('purchase-orders.show', $singlePo->id) }}" class="text-primary font-weight-bold" title="Buka Detail PO">
                                                    <i class="fas fa-file-invoice mr-1 text-muted"></i>{{ $singlePo->po_number }}
                                                </a>
                                            </div>
                                        @elseif($poCount > 1)
                                            <div class="mt-1">
                                                <button type="button" 
                                                        class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0 font-weight-bold po-popover-trigger" 
                                                        data-target="#po-popover-{{ $pr->id }}"
                                                        title="Klik untuk melihat ke-{{ $poCount }} PO terkait">
                                                    <i class="fas fa-layer-group mr-1"></i>{{ $poCount }} PO Terkait <i class="fas fa-chevron-down ml-1 small text-muted"></i>
                                                </button>
                                            </div>

                                            <!-- Template Popover Card -->
                                            <div id="po-popover-{{ $pr->id }}" class="d-none">
                                                <div class="po-popover-card p-2" style="min-width: 290px; max-width: 320px;">
                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                        <span class="font-weight-bold text-dark small">
                                                            <i class="fas fa-file-invoice-dollar text-primary mr-1"></i> Daftar PO Terkait
                                                        </span>
                                                        <span class="badge badge-primary badge-pill">{{ $poCount }} PO</span>
                                                    </div>
                                                    <div class="po-popover-list" style="max-height: 220px; overflow-y: auto;">
                                                        @foreach($poList as $idx => $linkedPo)
                                                            <div class="p-2 mb-1 rounded bg-light border-left border-primary d-flex justify-content-between align-items-center po-item-row" style="border-left-width: 3px !important;">
                                                                <div>
                                                                    <a href="{{ route('purchase-orders.show', $linkedPo->id) }}" class="font-weight-bold text-dark text-sm" target="_blank" title="Buka Detail PO">
                                                                        #{{ $idx + 1 }} {{ $linkedPo->po_number }}
                                                                    </a>
                                                                    <div class="text-muted" style="font-size: 11px;">
                                                                        <i class="fas fa-store mr-1 text-muted"></i>{{ Str::limit($linkedPo->vendor->name ?? '-', 20) }}
                                                                    </div>
                                                                </div>
                                                                <div class="text-right pl-2">
                                                                    <span class="badge badge-{{ $linkedPo->status_badge }}" style="font-size: 10px;">
                                                                        {{ $linkedPo->status_label }}
                                                                    </span>
                                                                    <div class="font-weight-bold text-success mt-1" style="font-size: 11px;">
                                                                        Rp {{ number_format($linkedPo->grand_total ?: $linkedPo->total_amount, 0, ',', '.') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-2 pt-1 border-top text-center">
                                                        <a href="{{ route('purchase-requests.show', $pr->id) }}" class="small text-primary font-weight-bold">
                                                            Kelola di Detail PR <i class="fas fa-arrow-right ml-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($pr->purchaseOrder)
                                            <div class="small mt-1">
                                                <a href="{{ route('purchase-orders.show', $pr->purchaseOrder->id) }}" class="text-primary font-weight-bold">
                                                    <i class="fas fa-file-invoice mr-1 text-muted"></i>{{ $pr->purchaseOrder->po_number }}
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge badge-warning px-2 py-1">
                                            <i class="fas fa-clock mr-1"></i>Menunggu PO
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark">{{ $pr->creator->name ?? 'User' }}</div>
                                    <div class="text-muted small">{{ $pr->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="text-right text-nowrap">
                                    <div class="d-inline-flex align-items-center justify-content-end" style="gap: 6px;">
                                        @if($pr->status === 'pending')
                                            <a href="{{ route('purchase-orders.create', ['purchase_request_id' => $pr->id]) }}" 
                                               class="btn btn-sm btn-success text-nowrap font-weight-bold d-inline-flex align-items-center shadow-sm" 
                                               title="Buat PO Baru"
                                               style="height: 31px; font-size: 0.8rem; border-radius: 4px; padding: 0 10px;">
                                                <i class="fas fa-cart-plus mr-1"></i> Buat PO Baru
                                            </a>
                                        @endif
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('purchase-requests.show', $pr->id) }}" class="btn btn-default" title="Detail">
                                                <i class="fas fa-eye text-secondary"></i>
                                            </a>
                                            <a href="{{ route('purchase-requests.edit', $pr->id) }}" class="btn btn-default" title="Edit">
                                                <i class="fas fa-edit text-secondary"></i>
                                            </a>
                                            @if($pr->status === 'pending')
                                                <button type="button" class="btn btn-default text-danger" title="Hapus"
                                                    onclick="deleteRequest('{{ $pr->id }}', '{{ $pr->pr_number }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <form id="delete-form-{{ $pr->id }}" 
                                          action="{{ route('purchase-requests.destroy', $pr->id) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 text-muted"></i>
                                    <p class="mb-0">Belum ada data permintaan pembelian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($purchaseRequests->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $purchaseRequests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function deleteRequest(id, prNumber) {
        Swal.fire({
            title: 'Hapus Permintaan Pembelian?',
            text: 'Request ' + prNumber + ' akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    $(document).ready(function() {
        // Initialize Popover for PO list
        $('.po-popover-trigger').each(function() {
            var $trigger = $(this);
            var targetId = $trigger.data('target');
            var contentHtml = $(targetId).html();

            $trigger.popover({
                html: true,
                placement: 'bottom',
                trigger: 'manual',
                container: 'body',
                content: contentHtml,
                template: '<div class="popover po-list-popover shadow border-0" role="tooltip"><div class="arrow"></div><div class="popover-body p-0"></div></div>'
            });
        });

        // Toggle on click
        $(document).on('click', '.po-popover-trigger', function(e) {
            e.stopPropagation();
            var _this = this;
            $('.po-popover-trigger').not(_this).popover('hide');
            $(_this).popover('toggle');
        });

        // Close when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.popover').length) {
                $('.po-popover-trigger').popover('hide');
            }
        });
    });
</script>
@endpush
