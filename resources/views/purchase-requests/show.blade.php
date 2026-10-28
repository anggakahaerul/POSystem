@extends('layouts.app')

@section('title', 'Detail Permintaan Pembelian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-requests.index') }}">Permintaan Pembelian</a></li>
    <li class="breadcrumb-item active">{{ $purchaseRequest->pr_number }}</li>
@endsection

@push('styles')
<style>
    .po-card-item {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-color: #e2e8f0 !important;
    }
    .po-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
        border-color: #cbd5e1 !important;
    }
    .hover-primary:hover {
        color: #007bff !important;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Left Column: PR Details -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold m-0 text-dark">
                    <i class="fas fa-file-invoice mr-2 text-primary"></i>{{ $purchaseRequest->pr_number }}
                </h3>
                <span class="badge badge-{{ $purchaseRequest->status_badge }} px-2 py-1" id="pr-status-badge">
                    {{ $purchaseRequest->status_label }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="120" class="text-muted">Judul</th>
                        <td class="font-weight-bold text-dark">: {{ $purchaseRequest->title }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Pemohon</th>
                        <td>: {{ $purchaseRequest->creator->name ?? 'User' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal</th>
                        <td>: {{ $purchaseRequest->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>: 
                            <span class="badge badge-{{ $purchaseRequest->status_badge }}" id="pr-status-badge-table">
                                {{ $purchaseRequest->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Total PO</th>
                        <td>: 
                            <span class="badge badge-info font-weight-bold px-2" id="left-po-count-badge">
                                {{ $purchaseRequest->purchaseOrders->count() }} PO
                            </span>
                        </td>
                    </tr>
                    @if($purchaseRequest->attachment)
                        <tr>
                            <th class="text-muted">Lampiran</th>
                            <td>: 
                                <a href="{{ route('purchase-requests.download', $purchaseRequest->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" title="Unduh File Lampiran">
                                    <i class="fas fa-paperclip mr-1"></i> {{ $purchaseRequest->attachment_name ?: 'Download Lampiran' }}
                                </a>
                            </td>
                        </tr>
                    @endif
                </table>

                <hr>

                <div class="form-group mb-0">
                    <label class="text-muted small font-weight-bold text-uppercase">Deskripsi Request</label>
                    <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap; min-height: 80px;">{{ $purchaseRequest->description ?: '-' }}</div>
                </div>
            </div>

            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between">
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('purchase-requests.edit', $purchaseRequest->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit mr-1"></i> Edit Request
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: List PO (Replaces Lampiran File) -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h3 class="card-title font-weight-bold m-0 text-dark">
                        <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Daftar Purchase Order
                    </h3>
                    <span class="badge badge-primary badge-pill ml-2 px-2 py-1" id="header-po-count">
                        {{ $purchaseRequest->purchaseOrders->count() }} PO
                    </span>
                </div>
                <!-- Menu Tambah PO di Pojok -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle font-weight-bold shadow-sm" type="button" id="dropdownAddPO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus mr-1"></i> Tambah PO
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="dropdownAddPO">
                        <a class="dropdown-item py-2" href="javascript:void(0)" data-toggle="modal" data-target="#modalAttachPO">
                            <i class="fas fa-link text-primary mr-2"></i> Hubungkan PO yang Ada
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item py-2" href="{{ route('purchase-orders.create', ['purchase_request_id' => $purchaseRequest->id]) }}">
                            <i class="fas fa-plus-circle text-success mr-2"></i> Buat PO Baru untuk PR Ini
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 bg-light" style="min-height: 380px;">
                <!-- Empty State -->
                <div id="po-empty-state" class="text-center py-5 {{ $purchaseRequest->purchaseOrders->isEmpty() ? '' : 'd-none' }}">
                    <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-muted" style="opacity: 0.35;"></i>
                    <h5 class="text-muted font-weight-normal mb-2">Belum ada Purchase Order yang terhubung</h5>
                    <p class="text-muted small mb-3">Klik tombol <strong>Tambah PO</strong> di pojok kanan atas untuk menghubungkan atau membuat PO baru.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalAttachPO">
                        <i class="fas fa-plus mr-1"></i> Hubungkan PO Sekarang
                    </button>
                </div>

                <!-- Cards Container -->
                <div id="po-cards-container" class="d-flex flex-column" style="gap: 12px;">
                    @foreach($purchaseRequest->purchaseOrders as $index => $po)
                        <div class="card border mb-0 po-card-item shadow-sm" data-po-id="{{ $po->id }}" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary font-weight-bold px-2 py-1 po-seq-badge">PO #{{ $loop->iteration }}</span>
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="font-weight-bold text-dark ml-2 hover-primary" target="_blank" title="Buka Detail PO">
                                        <i class="fas fa-file-invoice-dollar text-success mr-1"></i>{{ $po->po_number }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $po->status_badge }} mr-2">{{ $po->status_label }}</span>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-danger btn-remove-po" 
                                            data-id="{{ $po->id }}" 
                                            data-po-number="{{ $po->po_number }}"
                                            data-url="{{ route('purchase-requests.detach-po', [$purchaseRequest->id, $po->id]) }}" 
                                            title="Hapus / Lepaskan dari PR ini">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-2 px-3 bg-light">
                                <div class="row text-sm">
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="far fa-calendar-alt mr-1"></i>Tanggal:</span>
                                        <span class="font-weight-bold text-dark ml-1">{{ $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d M Y') : $po->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-store mr-1"></i>Vendor:</span>
                                        <span class="font-weight-bold text-dark ml-1">{{ $po->vendor->name ?? '-' }}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-money-bill-wave mr-1"></i>Total PO:</span>
                                        <span class="font-weight-bold text-success ml-1">Rp {{ number_format($po->grand_total ?: $po->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-building mr-1"></i>Cabang:</span>
                                        <span class="font-weight-bold text-dark ml-1">{{ $po->branch->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white py-1 px-3 d-flex justify-content-end align-items-center">
                                <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-xs btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt mr-1"></i> Buka PO
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hubungkan PO -->
<div class="modal fade" id="modalAttachPO" tabindex="-1" role="dialog" aria-labelledby="modalAttachPOLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form id="formAttachPO" action="{{ route('purchase-requests.attach-po', $purchaseRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAttachPOLabel">
                        <i class="fas fa-link mr-2"></i>Hubungkan Purchase Order
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="select_purchase_order_id" class="font-weight-bold text-dark">Pilih Purchase Order</label>
                        <select name="purchase_order_id" id="select_purchase_order_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Pilih PO yang akan dihubungkan --</option>
                            @foreach($availablePurchaseOrders as $availPo)
                                <option value="{{ $availPo->id }}" 
                                        data-po-number="{{ $availPo->po_number }}"
                                        data-vendor="{{ $availPo->vendor->name ?? '-' }}"
                                        data-date="{{ $availPo->po_date ? \Carbon\Carbon::parse($availPo->po_date)->format('d M Y') : $availPo->created_at->format('d M Y') }}"
                                        data-total="Rp {{ number_format($availPo->grand_total ?: $availPo->total_amount, 0, ',', '.') }}"
                                        data-status="{{ $availPo->status_label }}"
                                        data-status-badge="{{ $availPo->status_badge }}"
                                        data-branch="{{ $availPo->branch->name ?? '-' }}"
                                        data-show-url="{{ route('purchase-orders.show', $availPo->id) }}">
                                    {{ $availPo->po_number }} &mdash; {{ $availPo->vendor->name ?? '-' }} (Rp {{ number_format($availPo->grand_total ?: $availPo->total_amount, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Menampilkan daftar PO aktif yang belum terkait dengan PR ini.</small>
                    </div>

                    <!-- PO Info Preview Box -->
                    <div id="po-selection-preview" class="p-3 bg-light rounded border d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bold text-dark" id="preview-po-number">-</span>
                            <span class="badge" id="preview-po-status">-</span>
                        </div>
                        <div class="small">
                            <div class="mb-1"><span class="text-muted">Vendor:</span> <strong id="preview-po-vendor">-</strong></div>
                            <div class="mb-1"><span class="text-muted">Tanggal:</span> <strong id="preview-po-date">-</strong></div>
                            <div class="mb-1"><span class="text-muted">Total Nilai:</span> <strong class="text-success" id="preview-po-total">-</strong></div>
                            <div><span class="text-muted">Cabang:</span> <strong id="preview-po-branch">-</strong></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('purchase-orders.create', ['purchase_request_id' => $purchaseRequest->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Buat PO Baru Saja
                    </a>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnSubmitAttachPO">
                            <i class="fas fa-check mr-1"></i> Hubungkan ke PR
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 in modal
    $('#select_purchase_order_id').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modalAttachPO'),
        placeholder: '-- Pilih PO yang akan dihubungkan --',
        allowClear: true
    });

    // Preview selected PO info
    $('#select_purchase_order_id').on('change', function() {
        const selected = $(this).find(':selected');
        const poId = selected.val();
        if (poId) {
            $('#preview-po-number').text(selected.data('po-number'));
            $('#preview-po-vendor').text(selected.data('vendor'));
            $('#preview-po-date').text(selected.data('date'));
            $('#preview-po-total').text(selected.data('total'));
            $('#preview-po-branch').text(selected.data('branch'));
            $('#preview-po-status')
                .removeClass()
                .addClass('badge badge-' + selected.data('status-badge'))
                .text(selected.data('status'));
            $('#po-selection-preview').removeClass('d-none');
        } else {
            $('#po-selection-preview').addClass('d-none');
        }
    });

    // Reindex PO Cards function
    // Strict contiguous sequence: PO #1, PO #2, PO #3...
    // When a card is removed, the remaining cards adapt and when a new card is added, it continues cleanly without jumping numbers.
    window.reindexPoCards = function() {
        const cards = document.querySelectorAll('#po-cards-container .po-card-item');
        const countBadge = document.getElementById('header-po-count');
        const leftCountBadge = document.getElementById('left-po-count-badge');
        const emptyState = document.getElementById('po-empty-state');
        
        const total = cards.length;
        if (countBadge) countBadge.textContent = total + ' PO';
        if (leftCountBadge) leftCountBadge.textContent = total + ' PO';
        
        if (total === 0) {
            if (emptyState) emptyState.classList.remove('d-none');
        } else {
            if (emptyState) emptyState.classList.add('d-none');
        }
        
        cards.forEach((card, index) => {
            const seqBadge = card.querySelector('.po-seq-badge');
            if (seqBadge) {
                seqBadge.textContent = `PO #${index + 1}`;
            }
        });
    };

    // Attach PO AJAX Submit
    $('#formAttachPO').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitAttachPO');
        const poId = $('#select_purchase_order_id').val();

        if (!poId) {
            toastr.warning('Silakan pilih Purchase Order terlebih dahulu.');
            return;
        }

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menghubungkan...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Hubungkan ke PR');
                if (res.success) {
                    $('#modalAttachPO').modal('hide');
                    
                    // Remove selected option from dropdown so it can't be chosen again
                    $(`#select_purchase_order_id option[value="${res.po.id}"]`).remove();
                    $('#select_purchase_order_id').val('').trigger('change');
                    $('#po-selection-preview').addClass('d-none');

                    // Create dynamic card element
                    const newCard = $(`
                        <div class="card border mb-0 po-card-item shadow-sm" data-po-id="${res.po.id}" style="border-radius: 8px; display: none;">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary font-weight-bold px-2 py-1 po-seq-badge">PO #0</span>
                                    <a href="${res.po.show_url}" class="font-weight-bold text-dark ml-2 hover-primary" target="_blank" title="Buka Detail PO">
                                        <i class="fas fa-file-invoice-dollar text-success mr-1"></i>${res.po.po_number}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-${res.po.status_badge} mr-2">${res.po.status_label}</span>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-danger btn-remove-po" 
                                            data-id="${res.po.id}" 
                                            data-po-number="${res.po.po_number}"
                                            data-url="${res.po.detach_url}" 
                                            title="Hapus / Lepaskan dari PR ini">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-2 px-3 bg-light">
                                <div class="row text-sm">
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="far fa-calendar-alt mr-1"></i>Tanggal:</span>
                                        <span class="font-weight-bold text-dark ml-1">${res.po.po_date}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-store mr-1"></i>Vendor:</span>
                                        <span class="font-weight-bold text-dark ml-1">${res.po.vendor_name}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-money-bill-wave mr-1"></i>Total PO:</span>
                                        <span class="font-weight-bold text-success ml-1">${res.po.total_amount}</span>
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted"><i class="fas fa-building mr-1"></i>Cabang:</span>
                                        <span class="font-weight-bold text-dark ml-1">${res.po.branch_name}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white py-1 px-3 d-flex justify-content-end align-items-center">
                                <a href="${res.po.show_url}" class="btn btn-xs btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt mr-1"></i> Buka PO
                                </a>
                            </div>
                        </div>
                    `);

                    $('#po-cards-container').append(newCard);
                    newCard.slideDown(200, function() {
                        reindexPoCards();
                    });

                    // Update PR status badge
                    if (res.pr_status_badge && res.pr_status_label) {
                        $('#pr-status-badge')
                            .removeClass()
                            .addClass('badge badge-' + res.pr_status_badge + ' px-2 py-1')
                            .text(res.pr_status_label);
                        $('#pr-status-badge-table')
                            .removeClass()
                            .addClass('badge badge-' + res.pr_status_badge)
                            .text(res.pr_status_label);
                    }

                    toastr.success(res.message);
                } else {
                    toastr.error(res.message || 'Gagal menghubungkan PO.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Hubungkan ke PR');
                toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            }
        });
    });

    // Remove / Detach PO button
    $(document).on('click', '.btn-remove-po', function(e) {
        e.preventDefault();
        const btn = $(this);
        const card = btn.closest('.po-card-item');
        const poNumber = btn.data('po-number');
        const url = btn.data('url');

        Swal.fire({
            title: 'Lepaskan PO dari PR?',
            html: `Apakah Anda yakin ingin melepaskan PO <strong>${poNumber}</strong> dari Permintaan Pembelian ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Lepaskan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            card.slideUp(250, function() {
                                $(this).remove();
                                reindexPoCards();
                            });

                            // Update PR status badge if updated
                            if (res.pr_status_badge && res.pr_status_label) {
                                $('#pr-status-badge')
                                    .removeClass()
                                    .addClass('badge badge-' + res.pr_status_badge + ' px-2 py-1')
                                    .text(res.pr_status_label);
                                $('#pr-status-badge-table')
                                    .removeClass()
                                    .addClass('badge badge-' + res.pr_status_badge)
                                    .text(res.pr_status_label);
                            }

                            toastr.success(res.message);
                        } else {
                            btn.prop('disabled', false);
                            toastr.error(res.message || 'Gagal melepaskan PO.');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                    }
                });
            }
        });
    });
});
</script>
@endpush
