@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">Buat PO</li>
@endsection

@section('content')
    <form action="{{ route('purchase-orders.store') }}" method="POST" id="po-form">
        @csrf

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
                                        value="{{ old('required_date') }}" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="note">Catatan</label>
                                    <textarea name="note" id="note" class="form-control" rows="1" placeholder="Catatan tambahan (opsional)">{{ old('note') }}</textarea>
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
                                        <th width="30%">Produk</th>
                                        <th width="10%">Qty</th>
                                        <th width="18%">Harga Satuan</th>
                                        <th width="18%">Diskon</th>
                                        <th width="18%">Subtotal</th>
                                        <th width="6%"></th>
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

                <!-- Document Discount & PPN -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-percent mr-2"></i>Diskon & PPN Dokumen</h3>
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

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-boxes mr-2"></i>Pilih Produk</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
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
                                    <th width="100">SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th width="80">Satuan</th>
                                    <th width="120" class="text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="products-body">
                                @foreach ($products as $product)
                                    <tr class="product-row" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}" data-sku="{{ $product->sku }}"
                                        data-unit="{{ $product->unit }}" data-price="{{ $product->price }}"
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
                                        <td class="text-right">{{ $product->formatted_price }}</td>
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
            let itemIndex = 0;

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
                        unit: row.data('unit'),
                        price: row.data('price')
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
                    $row.find('.item-price').val(formatMoney(product.price));

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

            // Document discount & PPN change
            $('#discount_type, #discount_value, #ppn_percentage').on('input change', function() {
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

                var grandTotal = afterDiscount + ppnAmount;

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
