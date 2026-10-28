@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Kategori</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-folder fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Kategori</span>
                    <h3 class="mb-0 font-weight-bold">{{ $categories->total() }}</h3>
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
                    <span class="text-muted small font-weight-bold text-uppercase">Kategori Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $categories->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Relasi Produk</span>
                    <h3 class="mb-0 font-weight-bold">{{ $categories->sum('products_count') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-layer-group mr-2"></i>Daftar Master Kategori
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('asset-types.index') }}" class="btn btn-outline-primary btn-sm mr-2 font-weight-bold" title="Kelola master tipe asset">
                <i class="fas fa-tags mr-1"></i> Kelola Tipe Asset
            </a>
            <a href="{{ route('master-data.export', 'categories') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data kategori ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('categories', 'Kategori')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('categories.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama atau deskripsi kategori..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="asset" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Tipe Asset --</option>
                        @foreach($assetTypes as $at)
                            <option value="{{ $at->code }}" {{ request('asset') == $at->code ? 'selected' : '' }}>
                                {{ $at->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('status') || request('asset'))
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="60" class="text-center">#</th>
                        <th>Nama Kategori</th>
                        <th width="140" class="text-center">Tipe Asset</th>
                        <th>Deskripsi</th>
                        <th width="140" class="text-center">Jumlah Produk</th>
                        <th width="120" class="text-center">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $categories->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $category->name }}</div>
                                <small class="text-muted">{{ $category->slug }}</small>
                            </td>
                            <td class="text-center">
                                @if($category->asset)
                                    @php
                                        $atObj = $assetTypes->firstWhere('code', $category->asset);
                                        $badgeColor = $atObj ? $atObj->badge_color : 'info';
                                    @endphp
                                    <span class="badge badge-{{ $badgeColor }} px-2 py-1">
                                        <i class="fas fa-tag mr-1"></i> {{ $category->asset }}
                                    </span>
                                @else
                                    <span class="text-muted small"><em>-</em></span>
                                @endif
                            </td>
                            <td class="text-muted">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary px-3 py-1 font-weight-bold">
                                    {{ $category->products_count }} item
                                </span>
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $category->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteCategory({{ $category->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data kategori</p>
                                    <small>Klik tombol "Tambah Kategori" di atas untuk menambahkan kategori baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $categories->firstItem() }}</b> - <b>{{ $categories->lastItem() }}</b> dari <b>{{ $categories->total() }}</b> kategori
                </div>
                <div>
                    {{ $categories->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Kategori (Add & Edit) -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">
                    <i class="fas fa-folder text-primary mr-2"></i>
                    <span class="title-text">Tambah Kategori</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="categoryForm" onsubmit="event.preventDefault(); submitCategoryForm();">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Alat Tulis Kantor" required>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="asset" class="mb-0">Tipe Asset</label>
                            <a href="{{ route('asset-types.index') }}" target="_blank" class="small text-primary font-weight-bold" title="Buka master tipe asset di tab baru untuk tambah/edit tipe baru">
                                <i class="fas fa-plus-circle mr-1"></i>Kelola / Tambah Baru
                            </a>
                        </div>
                        <select class="form-control" id="asset" name="asset">
                            <option value="">-- Pilih Tipe Asset (Opsional) --</option>
                            @foreach($assetTypes as $at)
                                <option value="{{ $at->code }}">{{ $at->name }} ({{ $at->code }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih divisi pengelola aset untuk kategori ini.</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi singkat mengenai kategori..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Kategori Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveCategory">
                        <i class="fas fa-save mr-1"></i> Simpan
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
    const storeUrl = "{{ route('categories.store') }}";
    const baseEditUrl = "{{ url('categories') }}";

    function openCreateModal() {
        ModalCrud.openCreate('#categoryModal', '#categoryForm', 'Tambah Kategori Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#is_active').prop('checked', true);
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#categoryModal', '#categoryForm', `${baseEditUrl}/${id}`, 'Edit Kategori', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#is_active').prop('checked', Boolean(data.is_active));
            }
        });
    }

    function submitCategoryForm() {
        ModalCrud.submitAjax('#categoryForm', '#categoryModal');
    }

    function deleteCategory(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Kategori ini akan dihapus secara permanen.');
    }
</script>
@endpush
