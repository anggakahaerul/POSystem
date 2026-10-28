@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">Edit {{ $po->po_number }}</li>
@endsection

@section('content')
    @if ($po->revision_count > 0 && $po->revision_reason)
        <div class="alert alert-warning border shadow-sm mb-3 p-3" style="border-radius: 8px;">
            <div class="d-flex align-items-start">
                <i class="fas fa-undo-alt fa-2x mr-3 text-warning mt-1"></i>
                <div>
                    <strong class="d-block text-dark font-weight-bold" style="font-size: 1rem;">
                        Mode Revisi PO (Revisi ke-{{ $po->revision_count }})
                    </strong>
                    <div class="text-dark mt-1" style="font-size: 0.9rem;">
                        Alasan Revisi: <em>&ldquo;{{ $po->revision_reason }}&rdquo;</em>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        Ditarik untuk revisi oleh <strong>{{ $po->revisedBy->name ?? 'User' }}</strong> pada {{ $po->revised_at ? $po->revised_at->format('d/m/Y H:i') : '-' }} WIB.
                        Silakan sesuaikan data item, harga, atau syarat di bawah, lalu klik <strong>Simpan Perubahan</strong> untuk mengajukan approval kembali.
                    </small>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('purchase-orders.update', $po->id) }}" method="POST" id="po-form">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <!-- PO Header -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Edit PO: {{ $po->po_number }}</h3>
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
                                                {{ old('branch_id', $po->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                                {{ old('vendor_id', $po->vendor_id) == $vendor->id ? 'selected' : '' }}>
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
                                                {{ old('payment_term_id', $po->payment_term_id) == $term->id ? 'selected' : '' }}>
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
                                        value="{{ old('required_date', $po->required_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="note">Catatan</label>
                                    <textarea name="note" id="note" class="form-control" rows="1" placeholder="Catatan tambahan (opsional)">{{ old('note', $po->note) }}</textarea>
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
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
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
                                @forelse($po->details as $index => $detail)
                                    <tr class="item-row">
                                        <td>
                                            <input type="hidden" name="products[{{ $index }}][product_id]"
                                                class="product-id" value="{{ $detail->product_id }}">
                                            <strong class="product-name">{{ $detail->product->name ?? '-' }}</strong>
                                            <br><small
                                                class="text-muted product-sku">{{ $detail->product->sku ?? '' }}</small>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="products[{{ $index }}][quantity]"
                                                    class="form-control item-qty" min="1"
                                                    value="{{ $detail->quantity }}" required>
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text item-unit">{{ $detail->product->unit ?? 'pcs' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" name="products[{{ $index }}][unit_price]"
                                                    class="form-control item-price money-format"
                                                    value="{{ number_format($detail->unit_price, 0, ',', '.') }}"
                                                    required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <select name="products[{{ $index }}][discount_type]"
                                                    class="form-control item-discount-type" style="max-width: 60px;">
                                                <option value="">-</option>
                                                <option value="percentage"
                                                    {{ $detail->discount_type == 'percentage' ? 'selected' : '' }}>%
                                                </option>
                                                <option value="fixed"
                                                    {{ $detail->discount_type == 'fixed' ? 'selected' : '' }}>Rp
                                                </option>
                                            </select>
                                            <input type="text"
                                                name="products[{{ $index }}][discount_value]"
                                                class="form-control item-discount-value money-format"
                                                value="{{ number_format($detail->discount_value, 0, ',', '.') }}">
                                        </div>
                                    </td>
                                    <td class="item-subtotal font-weight-bold">Rp
                                        {{ number_format($detail->subtotal_after_discount ?: $detail->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-item"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row">
                                    <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                            Belum ada produk.
                                        </td>
                                    </tr>
                                @endforelse
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
                                            {{ old('discount_type', $po->discount_type) == 'percentage' ? 'selected' : '' }}>
                                            Persentase (%)</option>
                                        <option value="fixed"
                                            {{ old('discount_type', $po->discount_type) == 'fixed' ? 'selected' : '' }}>
                                            Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nilai Diskon</label>
                                    <div class="input-group">
                                        <input type="text" name="discount_value" id="discount_value"
                                            class="form-control money-format"
                                            value="{{ old('discount_value', number_format($po->discount_value, 0, ',', '.')) }}">
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text discount-type-label">{{ $po->discount_type == 'fixed' ? 'Rp' : '%' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PPN (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="ppn_percentage" id="ppn_percentage"
                                            class="form-control"
                                            value="{{ old('ppn_percentage', $po->ppn_percentage ?? 11) }}" min="0"
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
                                            {{ old('is_pph', $po->is_pph) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_pph" style="cursor: pointer;">
                                            <i class="fas fa-hand-holding-usd text-warning mr-1"></i> Kenakan PPh
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Potongan pajak penghasilan dari vendor (Withholding Tax)</small>
                                </div>
                                <div class="col-md-8">
                                    <div id="pph-config-row" class="row align-items-center" style="{{ old('is_pph', $po->is_pph) ? '' : 'display: none;' }}">
                                        <div class="col-md-7">
                                            <label class="small text-muted mb-1 font-weight-bold">Jenis PPh:</label>
                                            <select name="pph_type" id="pph_type" class="form-control form-control-sm">
                                                <option value="pph23" data-rate="2" {{ old('pph_type', $po->pph_type ?? 'pph23') == 'pph23' ? 'selected' : '' }}>PPh 23 (2.0%) - Jasa / Sewa Alat</option>
                                                <option value="pph22_15" data-rate="1.5" {{ old('pph_type', $po->pph_type) == 'pph22_15' ? 'selected' : '' }}>PPh 22 (1.5%) - Barang / Pengadaan</option>
                                                <option value="pph22_05" data-rate="0.5" {{ old('pph_type', $po->pph_type) == 'pph22_05' ? 'selected' : '' }}>PPh 22 (0.5%) - PP 23 UMKM</option>
                                                <option value="pph4_2" data-rate="10" {{ old('pph_type', $po->pph_type) == 'pph4_2' ? 'selected' : '' }}>PPh 4(2) (10.0%) - Sewa Tanah & Bangunan</option>
                                                <option value="pph21_25" data-rate="2.5" {{ old('pph_type', $po->pph_type) == 'pph21_25' ? 'selected' : '' }}>PPh 21 (2.5%) - Upah / Tenaga Ahli</option>
                                                <option value="pph21_50" data-rate="5" {{ old('pph_type', $po->pph_type) == 'pph21_50' ? 'selected' : '' }}>PPh 21 (5.0%) - Jasa Orang Pribadi</option>
                                                <option value="custom" data-rate="" {{ old('pph_type', $po->pph_type) == 'custom' ? 'selected' : '' }}>PPh (%)</option>
                                                <option value="custom_nominal" data-rate="" {{ old('pph_type', $po->pph_type) == 'custom_nominal' ? 'selected' : '' }}>PPh Nominal (Rp)</option>
                                            </select>
                                        </div>
                                        @php
                                            $isNominalMode = old('pph_type', $po->pph_type) == 'custom_nominal';
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
                                                    class="form-control" value="{{ old('pph_percentage', (float)($po->pph_percentage ?? 2)) }}" min="0"
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
                                                    class="form-control money-format" value="{{ old('pph_amount', number_format((float)($po->pph_amount ?? 0), 0, '', '.')) }}" placeholder="0" oninput="calculateGrandTotal()">
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
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-secondary btn-block">
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
                                <td class="text-right"><strong id="item-count">{{ $po->details->count() }}</strong> item
                                </td>
                            </tr>
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-right"><strong id="subtotal-display">Rp
                                        {{ number_format($po->subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr id="discount-row" style="{{ $po->discount_amount > 0 ? '' : 'display: none;' }}">
                                <td>Diskon:</td>
                                <td class="text-right text-danger">- <strong id="discount-display">Rp
                                        {{ number_format($po->discount_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td>PPN (<span id="ppn-pct">{{ $po->ppn_percentage ?? 11 }}</span>%):</td>
                                <td class="text-right"><strong id="ppn-display">Rp
                                        {{ number_format($po->ppn_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr id="pph-row" style="{{ ($po->is_pph && $po->pph_amount > 0) || $po->details->where('is_pph', true)->count() > 0 ? '' : 'display: none;' }}">
                                <td id="pph-label-text">PPh:</td>
                                <td class="text-right text-danger">- <strong id="pph-display">Rp
                                        {{ number_format($po->pph_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-right text-primary"><strong id="grand-total">Rp
                                        {{ number_format($po->total_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-boxes text-primary mr-2"></i>Pilih Produk</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Search & Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="modal-search" class="form-control"
                                    placeholder="Cari nama atau SKU produk...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default" id="btn-search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="modal-category" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach (\App\Models\Category::active()->orderBy('name')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-right">
                            <span class="badge badge-primary p-2" id="selected-count">0 dipilih</span>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm" id="products-table">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="check-all" title="Pilih Semua">
                                    </th>
                                    <th width="120">SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th width="100">Satuan</th>
                                </tr>
                            </thead>
                            <tbody id="products-body">
                                @foreach ($products as $product)
                                    <tr class="product-row" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}" data-sku="{{ $product->sku }}"
                                        data-unit="{{ $product->unit }}"
                                        data-category="{{ $product->category_id }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                        </td>
                                        <td><code>{{ $product->sku }}</code></td>
                                        <td>{{ $product->name }}</td>
                                        <td><span
                                                class="badge badge-secondary">{{ $product->category->name ?? '-' }}</span>
                                        </td>
                                        <td>{{ $product->unit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-add-selected">
                        <i class="fas fa-plus mr-1"></i> Tambahkan Produk Terpilih
                    </button>
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
            let itemIndex = {{ $po->details->count() }};

            // Search in modal
            $('#modal-search').on('keyup', filterProducts);
            $('#modal-category').on('change', filterProducts);
            $('#btn-search').on('click', filterProducts);

            function filterProducts() {
                var search = $('#modal-search').val().toLowerCase();
                var category = $('#modal-category').val();

                $('.product-row').each(function() {
                    var name = $(this).data('name').toLowerCase();
                    var sku = $(this).data('sku').toLowerCase();
                    var cat = $(this).data('category').toString();

                    var matchSearch = name.includes(search) || sku.includes(search);
                    var matchCategory = !category || cat === category;

                    $(this).toggle(matchSearch && matchCategory);
                });
            }

            // Check all
            $('#check-all').on('change', function() {
                var checked = $(this).prop('checked');
                $('.product-row:visible .product-checkbox').prop('checked', checked);
                updateSelectedCount();
            });

            // Update selected count
            $(document).on('change', '.product-checkbox', updateSelectedCount);

            function updateSelectedCount() {
                var count = $('.product-checkbox:checked').length;
                $('#selected-count').text(count + ' dipilih');
            }

            // Add selected products
            $('#btn-add-selected').on('click', function() {
                var selectedProducts = [];
                $('.product-checkbox:checked').each(function() {
                    var row = $(this).closest('.product-row');
                    selectedProducts.push({
                        id: row.data('id'),
                        name: row.data('name'),
                        sku: row.data('sku'),
                        unit: row.data('unit')
                    });
                });

                if (selectedProducts.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Silakan pilih minimal 1 produk.'
                    });
                    return;
                }

                // Add each product
                selectedProducts.forEach(function(product) {
                    addProductRow(product);
                });

                // Reset checkboxes and close modal
                $('.product-checkbox').prop('checked', false);
                $('#check-all').prop('checked', false);
                updateSelectedCount();
                $('#productModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: selectedProducts.length + ' produk berhasil ditambahkan.'
                });
            });

            // Add product row
            function addProductRow(product) {
                // Hide empty row
                $('#empty-row').hide();

                // Check if product already exists
                var exists = false;
                $('.product-id').each(function() {
                    if ($(this).val() == product.id) {
                        exists = true;
                        var row = $(this).closest('.item-row');
                        var qtyInput = row.find('.item-qty');
                        qtyInput.val(parseInt(qtyInput.val()) + 1);
                        calculateRowSubtotal(row);
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

            // Calculate on load
            calculateGrandTotal();

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

            // Reset modal on open
            $('#productModal').on('show.bs.modal', function() {
                $('.product-checkbox').prop('checked', false);
                $('#check-all').prop('checked', false);
                $('#modal-search').val('');
                $('#modal-category').val('');
                filterProducts();
                updateSelectedCount();
            });
        });
    </script>
@endpush
