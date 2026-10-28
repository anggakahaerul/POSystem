@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">Buat PO</li>
@endsection

@push('styles')
<style>
    /* Modal Two-Column Layout & ERP Styling */
    #productModal .modal-content {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    #productModal .modal-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .catalog-column {
        background-color: #ffffff;
    }
    .selected-column {
        background-color: #f8fafc;
        border-left: 1px solid #e2e8f0;
    }
    .product-row {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .product-row:hover {
        background-color: #f1f5f9;
    }
    .product-row.is-selected {
        background-color: #ebf5ff !important;
    }
    .product-row.is-selected td:first-child {
        border-left: 3px solid #007bff;
    }
    .catalog-scroll-container,
    .selected-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
    .catalog-scroll-container::-webkit-scrollbar,
    .selected-scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    .catalog-scroll-container::-webkit-scrollbar-track,
    .selected-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .catalog-scroll-container::-webkit-scrollbar-thumb,
    .selected-scroll-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    .selected-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        border-left: 3px solid #007bff !important;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }
    .selected-item-card:hover {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .cursor-pointer {
        cursor: pointer;
    }
    @media (max-width: 991.98px) {
        .selected-column {
            border-left: none !important;
            border-top: 2px solid #e2e8f0;
        }
    }
</style>
@endpush

@section('content')
    <form action="{{ route('purchase-orders.store') }}" method="POST" id="po-form">
        @csrf

        @if(isset($purchaseRequest))
            <input type="hidden" name="purchase_request_id" value="{{ $purchaseRequest->id }}">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-3 py-2 px-3" style="border-radius: 8px;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-invoice text-info mr-2 fa-lg"></i>
                    <div>
                        <strong>Permintaan Pembelian: {{ $purchaseRequest->pr_number }}</strong> &mdash; {{ $purchaseRequest->title }}
                    </div>
                </div>
                @if($purchaseRequest->attachment)
                    <a href="{{ route('purchase-requests.download', $purchaseRequest->id) }}" target="_blank" class="btn btn-sm btn-outline-info bg-white">
                        <i class="fas fa-paperclip mr-1"></i>Lihat Lampiran
                    </a>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- PO Header -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Informasi PO</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="branch_id">Cabang <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id"
                                        class="form-control select2 @error('branch_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_id">Vendor <span class="text-danger">*</span></label>
                                    <select name="vendor_id" id="vendor_id"
                                        class="form-control select2 @error('vendor_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"
                                                data-payment-term="{{ $vendor->payment_term_id }}"
                                                {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->code }} - {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_term_id">Term Pembayaran</label>
                                    <select name="payment_term_id" id="payment_term_id" class="form-control select2">
                                        <option value="">-- Pilih Term --</option>
                                        @foreach (\App\Models\PaymentTerm::active()->orderBy('days')->get() as $term)
                                            <option value="{{ $term->id }}"
                                                {{ old('payment_term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="required_date">Tanggal PO</label>
                                    <input type="date" name="required_date" id="required_date"
                                        class="form-control @error('required_date') is-invalid @enderror"
                                        value="{{ old('required_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="note">Catatan</label>
                                    <textarea name="note" id="note" class="form-control" rows="1" placeholder="Catatan tambahan (opsional)">{{ old('note', isset($purchaseRequest) ? '[' . $purchaseRequest->pr_number . '] ' . $purchaseRequest->title : '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PO Items -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Item Produk</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-open-product-modal" data-toggle="modal"
                                data-target="#productModal">
                                <i class="fas fa-plus mr-1"></i> Tambah Produk
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="items-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="min-width: 170px;">Produk</th>
                                        <th style="width: 105px; min-width: 105px;">Qty</th>
                                        <th style="width: 155px; min-width: 155px;">Harga Satuan</th>
                                        <th style="width: 135px; min-width: 135px;">Diskon</th>
                                        <th style="width: 130px; min-width: 130px;" class="text-right">Subtotal</th>
                                        <th style="width: 45px; min-width: 45px;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    <tr id="empty-row">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                            Belum ada produk. Klik <strong>Tambah Produk</strong> untuk memilih.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Document Discount & PPN & PPh -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-percent mr-2"></i>Diskon & Pajak Dokumen</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipe Diskon</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="">Tidak Ada</option>
                                        <option value="percentage"
                                            {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)
                                        </option>
                                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>
                                            Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nilai Diskon</label>
                                    <div class="input-group">
                                        <input type="text" name="discount_value" id="discount_value"
                                            class="form-control money-format" value="{{ old('discount_value', 0) }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text discount-type-label">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PPN (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="ppn_percentage" id="ppn_percentage"
                                            class="form-control" value="{{ old('ppn_percentage', 11) }}" min="0"
                                            max="100" step="0.1">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PPh Section -->
                        <div class="border-top pt-3 mt-2">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_pph" name="is_pph" value="1"
                                            {{ old('is_pph') ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_pph" style="cursor: pointer;">
                                            <i class="fas fa-hand-holding-usd text-warning mr-1"></i> Kenakan PPh
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Potongan pajak penghasilan dari vendor (Withholding Tax)</small>
                                </div>
                                <div class="col-md-8">
                                    <div id="pph-config-row" class="row align-items-center" style="{{ old('is_pph') ? '' : 'display: none;' }}">
                                        <div class="col-md-7">
                                            <label class="small text-muted mb-1 font-weight-bold">Jenis PPh:</label>
                                            <select name="pph_type" id="pph_type" class="form-control form-control-sm">
                                                <option value="pph23" data-rate="2" {{ old('pph_type', 'pph23') == 'pph23' ? 'selected' : '' }}>PPh 23 (2.0%) - Jasa / Sewa Alat</option>
                                                <option value="pph22_15" data-rate="1.5" {{ old('pph_type') == 'pph22_15' ? 'selected' : '' }}>PPh 22 (1.5%) - Barang / Pengadaan</option>
                                                <option value="pph22_05" data-rate="0.5" {{ old('pph_type') == 'pph22_05' ? 'selected' : '' }}>PPh 22 (0.5%) - PP 23 UMKM</option>
                                                <option value="pph4_2" data-rate="10" {{ old('pph_type') == 'pph4_2' ? 'selected' : '' }}>PPh 4(2) (10.0%) - Sewa Tanah & Bangunan</option>
                                                <option value="pph21_25" data-rate="2.5" {{ old('pph_type') == 'pph21_25' ? 'selected' : '' }}>PPh 21 (2.5%) - Upah / Tenaga Ahli</option>
                                                <option value="pph21_50" data-rate="5" {{ old('pph_type') == 'pph21_50' ? 'selected' : '' }}>PPh 21 (5.0%) - Jasa Orang Pribadi</option>
                                                <option value="custom" data-rate="" {{ old('pph_type') == 'custom' ? 'selected' : '' }}>PPh (%)</option>
                                                <option value="custom_nominal" data-rate="" {{ old('pph_type') == 'custom_nominal' ? 'selected' : '' }}>PPh Nominal (Rp)</option>
                                            </select>
                                        </div>
                                        @php
                                            $isNominalMode = old('pph_type') == 'custom_nominal';
                                        @endphp
                                        <div class="col-md-5">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="small text-muted mb-0 font-weight-bold" id="lbl_pph_input">{{ $isNominalMode ? 'Nominal PPh (Rp):' : 'Tarif PPh (%):' }}</label>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Mode PPh">
                                                    <button type="button" class="btn btn-xs {{ $isNominalMode ? 'btn-outline-secondary' : 'btn-primary' }} px-2 py-0" id="btn_pph_mode_pct" style="font-size: 11px; border-top-right-radius: 0; border-bottom-right-radius: 0;" title="Input dalam Persen (%)">% Persen</button>
                                                    <button type="button" class="btn btn-xs {{ $isNominalMode ? 'btn-primary' : 'btn-outline-secondary' }} px-2 py-0" id="btn_pph_mode_amt" style="font-size: 11px; border-top-left-radius: 0; border-bottom-left-radius: 0;" title="Input dalam Nominal Rupiah (Rp)">Rp Nominal</button>
                                                </div>
                                            </div>
                                            <div class="input-group input-group-sm" id="wrap_pph_pct" style="{{ $isNominalMode ? 'display: none;' : '' }}">
                                                <input type="number" name="pph_percentage" id="pph_percentage"
                                                    class="form-control" value="{{ old('pph_percentage', 2) }}" min="0"
                                                    max="100" step="0.1" oninput="calculateGrandTotal()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="input-group input-group-sm" id="wrap_pph_amt" style="{{ $isNominalMode ? '' : 'display: none;' }}">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" name="pph_amount" id="pph_amount"
                                                    class="form-control money-format" value="{{ old('pph_amount', 0) }}" placeholder="0" oninput="calculateGrandTotal()">
                                            </div>
                                            <small class="text-muted d-block mt-1" id="pph_live_hint" style="font-size: 11px;"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Actions -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-save mr-2"></i>Aksi</h3>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save mr-2"></i> Simpan sebagai Draft
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Ringkasan</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>Jumlah Item:</td>
                                <td class="text-right"><strong id="item-count">0</strong> item</td>
                            </tr>
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-right"><strong id="subtotal-display">Rp 0</strong></td>
                            </tr>
                            <tr id="discount-row" style="display: none;">
                                <td>Diskon:</td>
                                <td class="text-right text-danger">- <strong id="discount-display">Rp 0</strong></td>
                            </tr>
                            <tr>
                                <td>PPN (<span id="ppn-pct">11</span>%):</td>
                                <td class="text-right"><strong id="ppn-display">Rp 0</strong></td>
                            </tr>
                            <tr id="pph-row" style="display: none;">
                                <td id="pph-label-text">PPh:</td>
                                <td class="text-right text-danger">- <strong id="pph-display">Rp 0</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-right text-primary"><strong id="grand-total">Rp 0</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Product Selection Modal (Redesigned ERP Two-Column Layout) -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content border-0">
                <!-- Modal Header -->
                <div class="modal-header py-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0" id="productModalLabel">
                            <i class="fas fa-boxes text-primary mr-2"></i>Pilih Produk untuk Purchase Order
                        </h5>
                        <small class="text-muted">Cari produk, centang untuk memilih, dan atur jumlah langsung sebelum menambahkan ke PO</small>
                    </div>
                    <button type="button" class="close text-muted" id="btn-modal-close-top" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body (Two-Column ERP Layout) -->
                <div class="modal-body p-0">
                    <div class="row no-gutters">
                        <!-- Left Column: Product Catalog (~65%) -->
                        <div class="col-lg-7 col-xl-8 p-3 d-flex flex-column catalog-column">
                            <!-- Search & Category Filter -->
                            <div class="row mb-2">
                                <div class="col-md-7 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" id="modal-search" class="form-control border-left-0 border-right-0"
                                            placeholder="Cari nama atau SKU produk..." autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary border-left-0 bg-white" 
                                                id="btn-clear-search" style="display: none;" title="Hapus pencarian">
                                                <i class="fas fa-times text-muted"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <select id="modal-category" class="form-control form-control-sm">
                                        <option value="">Semua Kategori</option>
                                        @foreach (\App\Models\Category::active()->orderBy('name')->get() as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Subheader: Select All & Counter -->
                            <div class="d-flex justify-content-between align-items-center py-1 px-2 border-bottom bg-light mb-2 rounded">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="check-all">
                                    <label class="custom-control-label text-muted small cursor-pointer" for="check-all" id="label-check-all">
                                        Pilih Semua Hasil ({{ count($products) }})
                                    </label>
                                </div>
                                <span class="small text-muted" id="catalog-count-text">
                                    Menampilkan <strong id="catalog-visible-count">{{ count($products) }}</strong> produk
                                </span>
                            </div>

                            <!-- Scrollable Products Catalog -->
                            <div class="catalog-scroll-container flex-grow-1" style="max-height: 430px; min-height: 280px; overflow-y: auto;">
                                <table class="table table-hover table-sm border-bottom mb-0" id="products-table">
                                    <thead class="bg-light text-muted sticky-top">
                                        <tr>
                                            <th width="35" class="text-center align-middle"></th>
                                            <th class="align-middle">Produk & SKU</th>
                                            <th width="140" class="align-middle">Kategori</th>
                                            <th width="170" class="text-center align-middle">Jumlah (Qty)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-body">
                                        @foreach ($products as $product)
                                            <tr class="product-row" 
                                                id="product-row-{{ $product->id }}"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}" 
                                                data-sku="{{ $product->sku }}"
                                                data-unit="{{ $product->unit }}" 
                                                data-category="{{ $product->category_id }}"
                                                data-category-name="{{ $product->category->name ?? '-' }}"
                                                data-stock="{{ $product->stock }}">
                                                <td class="text-center align-middle" style="width: 35px;">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input product-checkbox" 
                                                            id="chk-prod-{{ $product->id }}" value="{{ $product->id }}">
                                                        <label class="custom-control-label cursor-pointer" for="chk-prod-{{ $product->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark product-title">{{ $product->name }}</div>
                                                    <div class="small text-muted d-flex align-items-center flex-wrap">
                                                        <code class="mr-2">{{ $product->sku }}</code>
                                                        <span class="mr-2">Stok: {{ $product->stock }} {{ $product->unit }}</span>
                                                        <span class="badge badge-info po-already-badge" id="po-badge-{{ $product->id }}" style="display: none;">
                                                            <i class="fas fa-check-circle mr-1"></i>Di PO: <span class="po-badge-qty">0</span>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary font-weight-normal">{{ $product->category->name ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle text-center" style="width: 170px;">
                                                    <div class="catalog-qty-group input-group input-group-sm" id="catalog-qty-box-{{ $product->id }}" style="display: none; width: 145px; margin: 0 auto;">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-outline-secondary btn-catalog-qty-minus" data-id="{{ $product->id }}">
                                                                <i class="fas fa-minus fa-xs"></i>
                                                            </button>
                                                        </div>
                                                        <input type="number" class="form-control text-center catalog-qty-input px-1" 
                                                            id="catalog-qty-{{ $product->id }}" data-id="{{ $product->id }}" 
                                                            min="1" step="1" value="1">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary btn-catalog-qty-plus" data-id="{{ $product->id }}">
                                                                <i class="fas fa-plus fa-xs"></i>
                                                            </button>
                                                            <span class="input-group-text py-0 px-1 small text-muted">{{ $product->unit }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="catalog-qty-placeholder text-muted small" id="catalog-qty-ph-{{ $product->id }}">
                                                        <span class="text-black-50"><i class="far fa-square mr-1"></i>Centang untuk pilih</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Empty State for Catalog -->
                                <div id="catalog-empty-state" class="text-center py-5 text-muted" style="display: none;">
                                    <i class="fas fa-search fa-3x mb-3 text-secondary"></i>
                                    <h6 class="font-weight-bold text-dark">Produk Tidak Ditemukan</h6>
                                    <p class="small text-muted mb-3">Tidak ditemukan produk yang cocok dengan pencarian atau filter yang dipilih.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-reset-filters">
                                        <i class="fas fa-redo mr-1"></i> Reset Pencarian & Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Selected Products Panel (~35%) -->
                        <div class="col-lg-5 col-xl-4 p-3 selected-column d-flex flex-column">
                            <!-- Panel Header -->
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <i class="fas fa-shopping-cart text-primary mr-1"></i>
                                    <strong class="text-dark">Produk Dipilih</strong>
                                    <span class="badge badge-primary ml-1 font-weight-bold" id="selected-badge-count">0</span>
                                </div>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 font-weight-normal" id="btn-clear-all-selected" style="display: none;">
                                    <i class="fas fa-trash-alt mr-1"></i>Hapus Semua
                                </button>
                            </div>

                            <!-- Scrollable Selected Items Container -->
                            <div class="selected-scroll-container flex-grow-1" id="selected-items-container" style="max-height: 430px; min-height: 280px; overflow-y: auto;">
                                <!-- Empty State -->
                                <div id="selected-empty-state" class="text-center py-5 px-3 text-muted">
                                    <div class="mb-3 text-black-50">
                                        <i class="fas fa-clipboard-list fa-3x"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark">Belum Ada Produk Dipilih</h6>
                                    <p class="small text-muted mb-0">
                                        Centang produk pada katalog di sebelah kiri dan tentukan jumlah yang ingin dipesan.
                                    </p>
                                </div>

                                <!-- Selected Items Cards List -->
                                <div id="selected-items-list" style="display: none;">
                                    <!-- Rendered dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-white border-top py-2 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted" id="footer-count-text">
                            <strong class="text-primary h6 mb-0 mr-1" id="footer-selected-count">0</strong> produk dipilih
                        </span>
                        <small class="text-danger ml-2 d-none" id="footer-qty-error">
                            <i class="fas fa-exclamation-circle mr-1"></i>Jumlah produk harus berupa angka minimal 1
                        </small>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-secondary mr-2" id="btn-modal-cancel">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary font-weight-bold" id="btn-add-selected" disabled>
                            <i class="fas fa-plus mr-1"></i> <span id="btn-add-text">Tambahkan ke PO</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Row Template -->
    <template id="item-row-template">
        <tr class="item-row">
            <td>
                <input type="hidden" name="products[{index}][product_id]" class="product-id">
                <strong class="product-name">-</strong>
                <br><small class="text-muted product-sku"></small>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="products[{index}][quantity]" class="form-control item-qty"
                        min="1" value="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text item-unit">pcs</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" name="products[{index}][unit_price]"
                        class="form-control item-price money-format" required>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <select name="products[{index}][discount_type]" class="form-control item-discount-type"
                        style="max-width: 60px;">
                        <option value="">-</option>
                        <option value="percentage">%</option>
                        <option value="fixed">Rp</option>
                    </select>
                    <input type="text" name="products[{index}][discount_value]"
                        class="form-control item-discount-value money-format" value="0">
                </div>
            </td>
            <td class="item-subtotal font-weight-bold">Rp 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-item" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let itemIndex = 0;

            // Auto-select vendor's default Term of Payment
            function applyVendorDefaultPaymentTerm() {
                let selectedOption = $('#vendor_id').find('option:selected');
                let termId = selectedOption.attr('data-payment-term') || selectedOption.data('payment-term');
                if (termId) {
                    $('#payment_term_id').val(termId).trigger('change');
                } else if ($('#vendor_id').val()) {
                    $('#payment_term_id').val('').trigger('change');
                }
            }

            $('#vendor_id').on('change select2:select', function() {
                applyVendorDefaultPaymentTerm();
            });

            // State for Modal Product Selection
            let modalSelectedProducts = new Map(); // key: productId (number), value: { id, name, sku, unit, price, formatted_price, quantity }
            let searchDebounceTimer = null;
            let lastFocusedTrigger = null;

            // Track trigger button for accessibility focus restoration
            $('#btn-open-product-modal').on('click', function() {
                lastFocusedTrigger = this;
            });

            // Sync with existing PO items (marks items already in PO)
            function syncExistingPoItems() {
                $('.po-already-badge').hide();
                $('.product-row').removeClass('is-in-po');

                $('.item-row').each(function() {
                    let prodId = parseInt($(this).find('.product-id').val());
                    let qty = parseInt($(this).find('.item-qty').val()) || 0;
                    if (prodId) {
                        let row = $(`#product-row-${prodId}`);
                        if (row.length) {
                            row.addClass('is-in-po');
                            let badge = $(`#po-badge-${prodId}`);
                            badge.find('.po-badge-qty').text(qty);
                            badge.show();
                        }
                    }
                });
            }

            // Filter Catalog Products
            function filterProducts() {
                let search = ($('#modal-search').val() || '').trim().toLowerCase();
                let category = $('#modal-category').val();
                let visibleCount = 0;
                let visibleCheckedCount = 0;

                $('.product-row').each(function() {
                    let name = ($(this).data('name') || '').toString().toLowerCase();
                    let sku = ($(this).data('sku') || '').toString().toLowerCase();
                    let cat = ($(this).data('category') || '').toString();
                    let prodId = parseInt($(this).data('id'));

                    let matchSearch = !search || name.includes(search) || sku.includes(search);
                    let matchCategory = !category || cat === category;
                    let isVisible = matchSearch && matchCategory;

                    $(this).toggle(isVisible);

                    if (isVisible) {
                        visibleCount++;
                        if (modalSelectedProducts.has(prodId)) {
                            visibleCheckedCount++;
                        }
                    }
                });

                // Update Catalog UI & Counters
                $('#catalog-visible-count').text(visibleCount);
                $('#label-check-all').text(`Pilih Semua Hasil (${visibleCount})`);

                if (visibleCount === 0) {
                    $('#products-table').hide();
                    $('#catalog-empty-state').show();
                    $('#check-all').prop('checked', false).prop('indeterminate', false).prop('disabled', true);
                } else {
                    $('#products-table').show();
                    $('#catalog-empty-state').hide();
                    $('#check-all').prop('disabled', false);

                    if (visibleCheckedCount === 0) {
                        $('#check-all').prop('checked', false).prop('indeterminate', false);
                    } else if (visibleCheckedCount === visibleCount) {
                        $('#check-all').prop('checked', true).prop('indeterminate', false);
                    } else {
                        $('#check-all').prop('checked', false).prop('indeterminate', true);
                    }
                }
            }

            // Search input with debounce and clear button
            $('#modal-search').on('input', function() {
                let val = $(this).val();
                $('#btn-clear-search').toggle(val.length > 0);
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(filterProducts, 200);
            });

            // Enter key on search: if 1 visible and unchecked, select it
            $('#modal-search').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let visibleRows = $('.product-row:visible');
                    if (visibleRows.length === 1) {
                        let row = visibleRows.first();
                        let chk = row.find('.product-checkbox');
                        if (!chk.prop('checked')) {
                            chk.prop('checked', true).trigger('change');
                            row.find('.catalog-qty-input').focus().select();
                        }
                    }
                }
            });

            // Clear search button
            $('#btn-clear-search').on('click', function() {
                $('#modal-search').val('');
                $(this).hide();
                filterProducts();
                $('#modal-search').focus();
            });

            // Category filter change
            $('#modal-category').on('change', function() {
                filterProducts();
            });

            // Reset filters button in empty state
            $('#btn-reset-filters').on('click', function() {
                $('#modal-search').val('');
                $('#btn-clear-search').hide();
                $('#modal-category').val('');
                filterProducts();
                $('#modal-search').focus();
            });

            // Catalog Row Click: toggle selection when clicking row (except on controls)
            $(document).on('click', '.product-row', function(e) {
                if ($(e.target).closest('.custom-control, .catalog-qty-group, button, input').length > 0) {
                    return;
                }
                let chk = $(this).find('.product-checkbox');
                chk.prop('checked', !chk.prop('checked')).trigger('change');
            });

            // Checkbox change handler for individual product
            $(document).on('change', '.product-checkbox', function() {
                let prodId = parseInt($(this).val());
                let row = $(`#product-row-${prodId}`);
                let isChecked = $(this).prop('checked');

                if (isChecked) {
                    let existingData = modalSelectedProducts.get(prodId);
                    let qty = existingData ? existingData.quantity : 1;

                    modalSelectedProducts.set(prodId, {
                        id: prodId,
                        name: row.data('name'),
                        sku: row.data('sku'),
                        unit: row.data('unit'),
                        quantity: qty
                    });

                    row.addClass('is-selected');
                    $(`#catalog-qty-ph-${prodId}`).hide();
                    $(`#catalog-qty-box-${prodId}`).show();
                    $(`#catalog-qty-${prodId}`).val(qty).removeClass('is-invalid');
                } else {
                    modalSelectedProducts.delete(prodId);
                    row.removeClass('is-selected');
                    $(`#catalog-qty-box-${prodId}`).hide();
                    $(`#catalog-qty-ph-${prodId}`).show();
                }

                updateSelectedPanel();
                updateCheckAllState();
            });

            // Check All Visible Products
            $('#check-all').on('change', function() {
                let isChecked = $(this).prop('checked');
                $('.product-row:visible').each(function() {
                    let chk = $(this).find('.product-checkbox');
                    let prodId = parseInt($(this).data('id'));

                    if (isChecked) {
                        chk.prop('checked', true);
                        if (!modalSelectedProducts.has(prodId)) {
                            modalSelectedProducts.set(prodId, {
                                id: prodId,
                                name: $(this).data('name'),
                                sku: $(this).data('sku'),
                                unit: $(this).data('unit'),
                                quantity: 1
                            });
                        }
                        $(this).addClass('is-selected');
                        $(`#catalog-qty-ph-${prodId}`).hide();
                        $(`#catalog-qty-box-${prodId}`).show();
                        let currentQty = modalSelectedProducts.get(prodId).quantity;
                        $(`#catalog-qty-${prodId}`).val(currentQty).removeClass('is-invalid');
                    } else {
                        chk.prop('checked', false);
                        modalSelectedProducts.delete(prodId);
                        $(this).removeClass('is-selected');
                        $(`#catalog-qty-box-${prodId}`).hide();
                        $(`#catalog-qty-ph-${prodId}`).show();
                    }
                });

                updateSelectedPanel();
            });

            function updateCheckAllState() {
                let visibleRows = $('.product-row:visible');
                let visibleCount = visibleRows.length;
                let checkedVisible = visibleRows.find('.product-checkbox:checked').length;

                if (visibleCount === 0) {
                    $('#check-all').prop('checked', false).prop('indeterminate', false);
                } else if (checkedVisible === visibleCount) {
                    $('#check-all').prop('checked', true).prop('indeterminate', false);
                } else if (checkedVisible > 0) {
                    $('#check-all').prop('checked', false).prop('indeterminate', true);
                } else {
                    $('#check-all').prop('checked', false).prop('indeterminate', false);
                }
            }

            // Synchronize Quantity Changes between Catalog & Panel
            function setProductQuantity(prodId, qty) {
                let parsed = parseInt(qty);
                let item = modalSelectedProducts.get(prodId);
                if (!item) return;

                if (isNaN(parsed) || parsed < 1) {
                    item.quantity = 0; // mark invalid
                    $(`#catalog-qty-${prodId}`).addClass('is-invalid');
                    $(`.panel-qty-input[data-id="${prodId}"]`).addClass('is-invalid');
                } else {
                    item.quantity = parsed;
                    $(`#catalog-qty-${prodId}`).removeClass('is-invalid').val(parsed);
                    $(`.panel-qty-input[data-id="${prodId}"]`).removeClass('is-invalid').val(parsed);
                }

                validateSelectionState();
            }

            // Catalog Quantity Input Handlers
            $(document).on('input', '.catalog-qty-input', function() {
                let prodId = parseInt($(this).data('id'));
                setProductQuantity(prodId, $(this).val());
            });

            $(document).on('click', '.btn-catalog-qty-plus', function() {
                let prodId = parseInt($(this).data('id'));
                let input = $(`#catalog-qty-${prodId}`);
                let current = parseInt(input.val()) || 0;
                setProductQuantity(prodId, current + 1);
            });

            $(document).on('click', '.btn-catalog-qty-minus', function() {
                let prodId = parseInt($(this).data('id'));
                let input = $(`#catalog-qty-${prodId}`);
                let current = parseInt(input.val()) || 1;
                if (current > 1) {
                    setProductQuantity(prodId, current - 1);
                }
            });

            // Panel Quantity Input Handlers
            $(document).on('input', '.panel-qty-input', function() {
                let prodId = parseInt($(this).data('id'));
                setProductQuantity(prodId, $(this).val());
            });

            $(document).on('click', '.btn-panel-qty-plus', function() {
                let prodId = parseInt($(this).data('id'));
                let input = $(`.panel-qty-input[data-id="${prodId}"]`);
                let current = parseInt(input.val()) || 0;
                setProductQuantity(prodId, current + 1);
            });

            $(document).on('click', '.btn-panel-qty-minus', function() {
                let prodId = parseInt($(this).data('id'));
                let input = $(`.panel-qty-input[data-id="${prodId}"]`);
                let current = parseInt(input.val()) || 1;
                if (current > 1) {
                    setProductQuantity(prodId, current - 1);
                }
            });

            // Remove Individual Item from Panel
            $(document).on('click', '.btn-remove-selected', function() {
                let prodId = parseInt($(this).data('id'));
                modalSelectedProducts.delete(prodId);

                // Uncheck in catalog
                let chk = $(`#chk-prod-${prodId}`);
                if (chk.length) {
                    chk.prop('checked', false);
                    let row = $(`#product-row-${prodId}`);
                    row.removeClass('is-selected');
                    $(`#catalog-qty-box-${prodId}`).hide();
                    $(`#catalog-qty-ph-${prodId}`).show();
                }

                updateSelectedPanel();
                updateCheckAllState();
            });

            // Clear All Selected
            $('#btn-clear-all-selected').on('click', function() {
                modalSelectedProducts.clear();
                $('.product-checkbox').prop('checked', false);
                $('.product-row').removeClass('is-selected');
                $('.catalog-qty-group').hide();
                $('.catalog-qty-placeholder').show();
                updateSelectedPanel();
                updateCheckAllState();
            });

            // Update Right Side Selected Panel UI
            function updateSelectedPanel() {
                let count = modalSelectedProducts.size;
                $('#selected-badge-count').text(count);
                $('#footer-selected-count').text(count);

                if (count === 0) {
                    $('#selected-empty-state').show();
                    $('#selected-items-list').hide().empty();
                    $('#btn-clear-all-selected').hide();
                } else {
                    $('#selected-empty-state').hide();
                    $('#btn-clear-all-selected').show();

                    let html = '';
                    modalSelectedProducts.forEach(function(item) {
                        html += `
                            <div class="selected-item-card p-2 mb-2" id="selected-card-${item.id}">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="pr-2 text-truncate" style="max-width: 82%;">
                                        <div class="font-weight-bold text-dark text-truncate small" title="${escapeHtml(item.name)}">
                                            ${escapeHtml(item.name)}
                                        </div>
                                        <div class="text-muted small">
                                            <code>${escapeHtml(item.sku)}</code>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-selected" data-id="${item.id}" title="Hapus produk ini">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 pt-1 border-top">
                                    <span class="small text-muted font-weight-bold">Jumlah:</span>
                                    <div class="input-group input-group-sm" style="width: 140px;">
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-outline-secondary btn-panel-qty-minus" data-id="${item.id}">
                                                <i class="fas fa-minus fa-xs"></i>
                                            </button>
                                        </div>
                                        <input type="number" class="form-control text-center panel-qty-input px-1 ${item.quantity < 1 ? 'is-invalid' : ''}" 
                                               data-id="${item.id}" min="1" step="1" value="${item.quantity || 1}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-panel-qty-plus" data-id="${item.id}">
                                                <i class="fas fa-plus fa-xs"></i>
                                            </button>
                                            <span class="input-group-text py-0 px-1 small text-muted">${escapeHtml(item.unit)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $('#selected-items-list').html(html).show();
                }

                validateSelectionState();
            }

            function escapeHtml(text) {
                if (!text) return '';
                return $('<div>').text(text).html();
            }

            // Validate if all selected items have valid quantities
            function validateSelectionState() {
                let count = modalSelectedProducts.size;
                let hasInvalidQty = false;

                modalSelectedProducts.forEach(function(item) {
                    if (isNaN(item.quantity) || item.quantity < 1) {
                        hasInvalidQty = true;
                    }
                });

                if (hasInvalidQty) {
                    $('#footer-qty-error').removeClass('d-none');
                    $('#btn-add-selected').prop('disabled', true);
                } else {
                    $('#footer-qty-error').addClass('d-none');
                    if (count > 0) {
                        $('#btn-add-selected').prop('disabled', false);
                        $('#btn-add-text').text(`Tambahkan ${count} Produk ke PO`);
                    } else {
                        $('#btn-add-selected').prop('disabled', true);
                        $('#btn-add-text').text('Tambahkan ke PO');
                    }
                }
            }

            // Submit Selected Products to PO Form
            $('#btn-add-selected').on('click', function() {
                if (modalSelectedProducts.size === 0) return;

                let hasInvalid = false;
                modalSelectedProducts.forEach(function(item) {
                    if (isNaN(item.quantity) || item.quantity < 1) {
                        hasInvalid = true;
                    }
                });

                if (hasInvalid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jumlah Tidak Valid',
                        text: 'Pastikan seluruh produk memiliki jumlah minimal 1.'
                    });
                    return;
                }

                let addedCount = 0;
                modalSelectedProducts.forEach(function(item) {
                    addProductRow(item, item.quantity);
                    addedCount++;
                });

                // Clear modal selections after adding to PO
                modalSelectedProducts.clear();
                $('.product-checkbox').prop('checked', false);
                $('.product-row').removeClass('is-selected');
                $('.catalog-qty-group').hide();
                $('.catalog-qty-placeholder').show();
                updateSelectedPanel();

                // Close modal
                $('#productModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `${addedCount} produk berhasil ditambahkan ke Purchase Order.`,
                    timer: 1800,
                    showConfirmButton: false
                });

                // Restore focus to Tambah Produk button
                if (lastFocusedTrigger) {
                    $(lastFocusedTrigger).focus();
                }
            });

            // Add product row
            function addProductRow(product, quantity) {
                quantity = parseInt(quantity) || 1;
                // Hide empty row
                $('#empty-row').hide();

                // Check if product already exists
                var exists = false;
                $('.product-id').each(function() {
                    if ($(this).val() == product.id) {
                        exists = true;
                        var row = $(this).closest('.item-row');
                        var qtyInput = row.find('.item-qty');
                        var currentQty = parseInt(qtyInput.val()) || 0;
                        qtyInput.val(currentQty + quantity);
                        calculateRowSubtotal(row);

                        // Visual highlight for updated row
                        row.addClass('table-warning');
                        setTimeout(function() {
                            row.removeClass('table-warning');
                        }, 1500);
                        return false;
                    }
                });

                if (!exists) {
                    var template = $('#item-row-template').html().replace(/{index}/g, itemIndex);
                    var $row = $(template);

                    $row.find('.product-id').val(product.id);
                    $row.find('.product-name').text(product.name);
                    $row.find('.product-sku').text(product.sku);
                    $row.find('.item-unit').text(product.unit);
                    $row.find('.item-price').val('').attr('placeholder', '0');
                    $row.find('.item-qty').val(quantity);

                    $('#items-body').append($row);
                    calculateRowSubtotal($row);
                    itemIndex++;
                }

                calculateGrandTotal();
            }

            // Remove item
            $(document).on('click', '.btn-remove-item', function() {
                $(this).closest('.item-row').remove();
                calculateGrandTotal();

                if ($('.item-row').length === 0) {
                    $('#empty-row').show();
                }
            });

            // Quantity, Price, Discount change
            $(document).on('input', '.item-qty, .item-price, .item-discount-value', function() {
                var row = $(this).closest('.item-row');
                calculateRowSubtotal(row);
                calculateGrandTotal();
            });

            $(document).on('change', '.item-discount-type', function() {
                var row = $(this).closest('.item-row');
                calculateRowSubtotal(row);
                calculateGrandTotal();
            });

            // Document discount, PPN & PPh change
            $('#discount_type, #discount_value, #ppn_percentage, #pph_percentage, #pph_amount').on('input change', function() {
                calculateGrandTotal();
            });

            // Toggle Document PPh
            $('#is_pph').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#pph-config-row').slideDown(200);
                    syncPphRate();
                } else {
                    $('#pph-config-row').slideUp(200);
                }
                calculateGrandTotal();
            });

            // Mode PPh Switcher Buttons (% Persen vs Rp Nominal)
            $('#btn_pph_mode_pct').on('click', function(e) {
                e.preventDefault();
                setPphInputMode('percentage');
            });

            $('#btn_pph_mode_amt').on('click', function(e) {
                e.preventDefault();
                setPphInputMode('nominal');
            });

            function setPphInputMode(mode) {
                if (mode === 'nominal') {
                    $('#btn_pph_mode_amt').removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('#btn_pph_mode_pct').removeClass('btn-primary').addClass('btn-outline-secondary');
                    $('#lbl_pph_input').text('Nominal PPh (Rp):');
                    $('#wrap_pph_pct').hide();
                    $('#wrap_pph_amt').show();
                    $('#pph_type').val('custom_nominal');
                    $('#pph_amount').focus();
                } else {
                    $('#btn_pph_mode_pct').removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('#btn_pph_mode_amt').removeClass('btn-primary').addClass('btn-outline-secondary');
                    $('#lbl_pph_input').text('Tarif PPh (%):');
                    $('#wrap_pph_amt').hide();
                    $('#wrap_pph_pct').show();
                    if ($('#pph_type').val() === 'custom_nominal') {
                        $('#pph_type').val('custom');
                    }
                    $('#pph_percentage').focus();
                }
                calculateGrandTotal();
            }

            function syncPphRate() {
                var selected = $('#pph_type option:selected');
                var val = selected.val();
                var rate = selected.data('rate');
                if (val === 'custom_nominal') {
                    $('#btn_pph_mode_amt').removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('#btn_pph_mode_pct').removeClass('btn-primary').addClass('btn-outline-secondary');
                    $('#lbl_pph_input').text('Nominal PPh (Rp):');
                    $('#wrap_pph_pct').hide();
                    $('#wrap_pph_amt').show();
                } else {
                    $('#btn_pph_mode_pct').removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('#btn_pph_mode_amt').removeClass('btn-primary').addClass('btn-outline-secondary');
                    $('#lbl_pph_input').text('Tarif PPh (%):');
                    $('#wrap_pph_amt').hide();
                    $('#wrap_pph_pct').show();
                    if (rate !== undefined && rate !== '') {
                        $('#pph_percentage').val(rate);
                    }
                }
            }

            $('#pph_type').on('change', function() {
                syncPphRate();
                calculateGrandTotal();
            });

            // Update discount label
            $('#discount_type').on('change', function() {
                var type = $(this).val();
                if (type === 'percentage') {
                    $('.discount-type-label').text('%');
                } else if (type === 'fixed') {
                    $('.discount-type-label').text('Rp');
                } else {
                    $('.discount-type-label').text('%');
                }
            });

            // Money format
            $(document).on('input', '.money-format', function() {
                var value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatMoney(value));
            });

            function formatMoney(value) {
                // Parse to integer first (removes decimals like .00)
                var num = parseInt(parseFloat(value)) || 0;
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseMoney(value) {
                return parseInt(value.toString().replace(/\./g, '')) || 0;
            }

            function calculateRowSubtotal(row) {
                var qty = parseInt(row.find('.item-qty').val()) || 0;
                var price = parseMoney(row.find('.item-price').val());
                var subtotal = qty * price;

                // Item discount
                var discountType = row.find('.item-discount-type').val();
                var discountValue = parseMoney(row.find('.item-discount-value').val());
                var discountAmount = 0;

                if (discountType === 'percentage' && discountValue > 0) {
                    discountAmount = (subtotal * discountValue) / 100;
                } else if (discountType === 'fixed' && discountValue > 0) {
                    discountAmount = discountValue;
                }

                var subtotalAfterDiscount = subtotal - discountAmount;
                row.find('.item-subtotal').text('Rp ' + formatMoney(subtotalAfterDiscount));
            }

            function calculateGrandTotal() {
                var subtotal = 0;
                var itemCount = 0;

                $('.item-row').each(function() {
                    var qty = parseInt($(this).find('.item-qty').val()) || 0;
                    var price = parseMoney($(this).find('.item-price').val());
                    var rowSubtotal = qty * price;

                    // Item discount
                    var discountType = $(this).find('.item-discount-type').val();
                    var discountValue = parseMoney($(this).find('.item-discount-value').val());

                    if (discountType === 'percentage' && discountValue > 0) {
                        rowSubtotal -= (rowSubtotal * discountValue) / 100;
                    } else if (discountType === 'fixed' && discountValue > 0) {
                        rowSubtotal -= discountValue;
                    }

                    subtotal += rowSubtotal;
                    itemCount++;
                });

                // Document discount
                var docDiscountType = $('#discount_type').val();
                var docDiscountValue = parseMoney($('#discount_value').val());
                var discountAmount = 0;

                if (docDiscountType === 'percentage' && docDiscountValue > 0) {
                    discountAmount = (subtotal * docDiscountValue) / 100;
                } else if (docDiscountType === 'fixed' && docDiscountValue > 0) {
                    discountAmount = docDiscountValue;
                }

                var afterDiscount = subtotal - discountAmount;

                // PPN
                var ppnPct = parseFloat($('#ppn_percentage').val()) || 0;
                var ppnAmount = (afterDiscount * ppnPct) / 100;

                // Document PPh
                var isDocPph = $('#is_pph').is(':checked');
                var pphType = $('#pph_type').val();
                var isNominalMode = (pphType === 'custom_nominal') || $('#wrap_pph_amt').is(':visible');
                var docPphAmount = 0;
                var docPphPct = 0;
                if (isDocPph) {
                    if (isNominalMode) {
                        docPphAmount = parseMoney($('#pph_amount').val());
                        docPphPct = afterDiscount > 0 ? ((docPphAmount / afterDiscount) * 100).toFixed(2) : 0;
                        $('#pph_live_hint').html(docPphAmount > 0 ? '<span class="text-info"><i class="fas fa-calculator mr-1"></i>Setara <strong>' + docPphPct + '%</strong> dari DPP</span>' : '');
                    } else {
                        docPphPct = parseFloat($('#pph_percentage').val()) || 0;
                        docPphAmount = (docPphPct > 0) ? (afterDiscount * docPphPct) / 100 : 0;
                        $('#pph_live_hint').html(docPphAmount > 0 ? '<span class="text-info"><i class="fas fa-calculator mr-1"></i>Estimasi: <strong>Rp ' + formatMoney(Math.round(docPphAmount)) + '</strong></span>' : '');
                    }
                } else {
                    $('#pph_live_hint').html('');
                }

                var grandTotal = afterDiscount + ppnAmount - docPphAmount;

                // Update display
                $('#subtotal-display').text('Rp ' + formatMoney(subtotal));
                $('#item-count').text(itemCount);
                $('#ppn-pct').text(ppnPct);
                $('#ppn-display').text('Rp ' + formatMoney(Math.round(ppnAmount)));
                $('#grand-total').text('Rp ' + formatMoney(Math.round(grandTotal)));

                if (discountAmount > 0) {
                    $('#discount-row').show();
                    $('#discount-display').text('Rp ' + formatMoney(Math.round(discountAmount)));
                } else {
                    $('#discount-row').hide();
                }

                if (docPphAmount > 0) {
                    $('#pph-row').show();
                    $('#pph-label-text').html('PPh:');
                    $('#pph-display').text('Rp ' + formatMoney(Math.round(docPphAmount)));
                } else {
                    $('#pph-row').hide();
                }
            }

            // Form validation
            $('#po-form').on('submit', function(e) {
                if ($('.item-row').length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Minimal harus ada 1 item produk!'
                    });
                    return false;
                }

                if (!$('#branch_id').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Silakan pilih cabang!'
                    });
                    return false;
                }
            });

            // Handle Modal Close Confirmation if Unsaved Selections Exist
            function handleModalCloseAttempt() {
                if (modalSelectedProducts.size > 0) {
                    Swal.fire({
                        title: 'Tutup Pilihan Produk?',
                        text: `Ada ${modalSelectedProducts.size} produk yang telah Anda pilih. Pilihan Anda tetap tersimpan saat modal dibuka kembali.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Tutup Modal',
                        cancelButtonText: 'Lanjut Memilih'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#productModal').modal('hide');
                            if (lastFocusedTrigger) {
                                $(lastFocusedTrigger).focus();
                            }
                        }
                    });
                } else {
                    $('#productModal').modal('hide');
                    if (lastFocusedTrigger) {
                        $(lastFocusedTrigger).focus();
                    }
                }
            }

            $('#btn-modal-close-top, #btn-modal-cancel').on('click', function(e) {
                e.preventDefault();
                handleModalCloseAttempt();
            });

            // Keyboard Escape handling when modal is open
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#productModal').hasClass('show')) {
                    e.preventDefault();
                    handleModalCloseAttempt();
                }
            });

            // Modal Lifecycle Events
            $('#productModal').on('show.bs.modal', function() {
                syncExistingPoItems();
                filterProducts();
                updateSelectedPanel();
            });

            $('#productModal').on('shown.bs.modal', function() {
                $('#modal-search').focus();
            });

            $('#productModal').on('hidden.bs.modal', function() {
                if (lastFocusedTrigger) {
                    $(lastFocusedTrigger).focus();
                }
            });
        });
    </script>
@endpush
