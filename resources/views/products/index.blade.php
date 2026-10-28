@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Produk</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Item Produk</span>
                    <h3 class="mb-0 font-weight-bold">{{ $products->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-double fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Produk Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $products->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Stok Minimum / Habis</span>
                    <h3 class="mb-0 font-weight-bold">{{ $products->filter(fn($p) => $p->stock <= $p->min_stock)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box-open mr-2"></i>Daftar Master Produk
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm mr-2 font-weight-bold" title="Kelola Master Kategori">
                <i class="fas fa-layer-group mr-1"></i> Kategori
            </a>
            <a href="{{ route('master-data.export', 'products') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data produk ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('products', 'Produk')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Produk
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('products.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama, SKU, atau deskripsi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="category_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="asset" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tipe Asset --</option>
                        @foreach($assetTypes as $at)
                            <option value="{{ $at->code }}" {{ request('asset') == $at->code ? 'selected' : '' }}>
                                {{ $at->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('category_id') || request('asset') || request('status'))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Modern Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th width="120">SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori & Tipe Asset</th>
                        <th width="90" class="text-center">Satuan</th>
                        <th width="100" class="text-center">Stok</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge badge-secondary font-monospace">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $product->name }}</div>
                                @if($product->description)
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $product->description }}</small>
                                @endif
                            </td>
                            <td>
                                <div><span class="badge badge-light border text-dark font-weight-bold">{{ $product->category->name ?? '-' }}</span></div>
                                @if($product->category && $product->category->asset)
                                    @php
                                        $atObj = $assetTypes->firstWhere('code', $product->category->asset);
                                        $badgeColor = $atObj ? $atObj->badge_color : 'info';
                                    @endphp
                                    <div class="mt-1">
                                        <span class="badge badge-{{ $badgeColor }}" style="font-size: 0.72rem;">
                                            <i class="fas fa-tag mr-1"></i> {{ $product->category->asset }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $product->unit }}</span>
                            </td>
                            <td class="text-center">
                                @if($product->stock <= $product->min_stock)
                                    <span class="badge badge-danger font-weight-bold" title="Di bawah stok minimum ({{ $product->min_stock }})">
                                        <i class="fas fa-exclamation mr-1"></i> {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="badge badge-success font-weight-bold">
                                        {{ $product->stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $product->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteProduct({{ $product->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data produk</p>
                                    <small>Klik tombol "Tambah Produk" di atas untuk menambahkan item baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $products->firstItem() }}</b> - <b>{{ $products->lastItem() }}</b> dari <b>{{ $products->total() }}</b> produk
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Produk (Add & Edit) -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">
                    <i class="fas fa-box text-primary mr-2"></i>
                    <span class="title-text">Tambah Produk</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm" onsubmit="event.preventDefault(); submitProductForm();">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="category_id" class="mb-0">Kategori & Tipe Asset <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-xs btn-outline-primary px-2 py-0 font-weight-bold" onclick="openQuickCategoryModal()" title="Tambah kategori & tipe asset baru langsung">
                                        <i class="fas fa-plus mr-1"></i>+ Kategori Baru
                                    </button>
                                </div>
                                <select class="form-control select2" id="category_id" name="category_id" required style="width: 100%;">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-asset="{{ $category->asset ?? '' }}">
                                            {{ $category->name }} {{ $category->asset ? '(' . $category->asset . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU / Kode Barang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="sku" name="sku" placeholder="Contoh: PRD-0001" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" onclick="generateSku()" title="Generate SKU">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Kertas HVS A4 80gr" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Satuan (Unit) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit" name="unit" placeholder="Pcs / Box / Rim" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stock">Stok Awal</label>
                                <input type="number" class="form-control" id="stock" name="stock" placeholder="0" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="min_stock">Min. Stok</label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" placeholder="0" min="0" value="5">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi / Spesifikasi</label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Detail spesifikasi barang..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Produk Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveProduct">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sub-Modal Quick Create Kategori & Tipe Asset -->
<div class="modal fade" id="quickCategoryModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 8px;">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-folder-plus text-primary mr-1"></i> Tambah Kategori & Tipe Asset Baru
                </h6>
                <button type="button" class="close" onclick="$('#quickCategoryModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCategoryForm" onsubmit="event.preventDefault(); submitQuickCategory();">
                @csrf
                <div class="modal-body p-3">
                    <div class="form-group mb-3">
                        <label for="quick_category_name" class="small font-weight-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="quick_category_name" name="name" placeholder="Contoh: Laptop & Komputer, ATK, Mebel" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="quick_category_asset" class="small font-weight-bold">Tipe Asset</label>
                        <select class="form-control form-control-sm" id="quick_category_asset" name="asset">
                            <option value="">-- Pilih Tipe Asset (Opsional) --</option>
                            @foreach($assetTypes as $at)
                                <option value="{{ $at->code }}">{{ $at->name }} ({{ $at->code }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Divisi/departemen penanggung jawab aset untuk kategori ini.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="quick_category_desc" class="small font-weight-bold">Deskripsi Singkat</label>
                        <input type="text" class="form-control form-control-sm" id="quick_category_desc" name="description" placeholder="Keterangan kategori (opsional)">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="$('#quickCategoryModal').modal('hide')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnSaveQuickCategory">
                        <i class="fas fa-check mr-1"></i> Simpan & Gunakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.master-import-modal')
@endsection

@push('scripts')
<script>
    const storeUrl = "{{ route('products.store') }}";
    const baseEditUrl = "{{ url('products') }}";
    const defaultSku = "{{ $suggestedSku }}";

    function generateSku() {
        $('#sku').val(defaultSku);
    }

    function openCreateModal() {
        ModalCrud.openCreate('#productModal', '#productForm', 'Tambah Produk Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#sku').val(defaultSku);
                form.find('#stock').val('0');
                form.find('#min_stock').val('5');
                form.find('#is_active').prop('checked', true);
                form.find('#category_id').val('').trigger('change');
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#productModal', '#productForm', `${baseEditUrl}/${id}`, 'Edit Produk', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#category_id').val(data.category_id).trigger('change');
                form.find('#is_active').prop('checked', Boolean(data.is_active));
            }
        });
    }

    function submitProductForm() {
        ModalCrud.submitAjax('#productForm', '#productModal');
    }

    function deleteProduct(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Produk ini akan dihapus secara permanen.');
    }

    function openQuickCategoryModal() {
        $('#quickCategoryForm')[0].reset();
        $('#quickCategoryModal').modal('show');
    }

    function submitQuickCategory() {
        const form = $('#quickCategoryForm');
        const btn = $('#btnSaveQuickCategory');
        const originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('categories.store') }}",
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success && res.data) {
                    const newCat = res.data;
                    const assetText = newCat.asset ? ` (${newCat.asset})` : '';
                    const newOption = new Option(`${newCat.name}${assetText}`, newCat.id, true, true);
                    
                    $('#category_id').append(newOption).trigger('change');
                    $('#quickCategoryModal').modal('hide');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `Kategori "${newCat.name}" berhasil dibuat dan langsung dipilih!`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                const errors = xhr.responseJSON?.errors;
                let errorMsg = xhr.responseJSON?.message || 'Gagal menambahkan kategori.';
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('<br>');
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Gagal', html: errorMsg });
                } else {
                    alert(errorMsg);
                }
            }
        });
    }
</script>
@endpush
