@extends('layouts.app')

@section('title', 'Master Tipe Asset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Tipe Asset</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-tags fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Tipe Asset</span>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['total'] }}</h3>
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
                    <span class="text-muted small font-weight-bold text-uppercase">Tipe Asset Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['active'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-layer-group fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Kategori Terhubung</span>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['linked_categories'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags mr-2"></i>Daftar Master Tipe Asset
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm mr-2 font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kategori
            </a>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Tipe Asset
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('asset-types.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari kode, nama, atau deskripsi tipe asset..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ $status == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $status == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if($search || $status !== null && $status !== '')
                        <a href="{{ route('asset-types.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="120">Kode</th>
                        <th>Nama Tipe Asset</th>
                        <th>Deskripsi</th>
                        <th width="140" class="text-center">Jumlah Kategori</th>
                        <th width="120" class="text-center">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assetTypes as $index => $type)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $assetTypes->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $type->badge_color ?? 'info' }} px-2 py-1 font-monospace" style="font-size: 0.82rem;">
                                    {{ $type->code }}
                                </span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $type->name }}</div>
                            </td>
                            <td class="text-muted">
                                {{ $type->description ?? '-' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('categories.index', ['asset' => $type->code]) }}" class="badge badge-secondary px-3 py-1 font-weight-bold" title="Lihat kategori dengan tipe ini">
                                    {{ $type->categories_count }} kategori
                                </a>
                            </td>
                            <td class="text-center">
                                @if($type->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $type->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteAssetType({{ $type->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-tags fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data tipe asset</p>
                                    <small>Klik tombol "Tambah Tipe Asset" di atas untuk menambahkan tipe asset baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assetTypes->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $assetTypes->firstItem() }}</b> - <b>{{ $assetTypes->lastItem() }}</b> dari <b>{{ $assetTypes->total() }}</b> tipe asset
                </div>
                <div>
                    {{ $assetTypes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Tipe Asset (Add & Edit) -->
<div class="modal fade" id="assetTypeModal" tabindex="-1" role="dialog" aria-labelledby="assetTypeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assetTypeModalTitle">
                    <i class="fas fa-tags text-primary mr-2"></i>
                    <span class="title-text">Tambah Tipe Asset</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assetTypeForm" onsubmit="event.preventDefault(); submitAssetTypeForm();">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="code">Kode Tipe Asset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-weight-bold" id="code" name="code" placeholder="Contoh: HRGA, MIS, OPS, IT" style="text-transform: uppercase;" required>
                        <small class="form-text text-muted">Kode singkat unik (misal: HRGA, MIS, LOGISTIK, KENDARAAN).</small>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Lengkap Tipe Asset <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: HRGA (General Affairs / Umum)" required>
                    </div>

                    <div class="form-group">
                        <label for="badge_color">Warna Label / Badge</label>
                        <select class="form-control" id="badge_color" name="badge_color">
                            <option value="info">Cyan / Info (Default HRGA)</option>
                            <option value="primary">Biru / Primary (Default MIS)</option>
                            <option value="success">Hijau / Success</option>
                            <option value="warning">Kuning / Warning</option>
                            <option value="danger">Merah / Danger</option>
                            <option value="dark">Hitam / Dark</option>
                            <option value="secondary">Abu-abu / Secondary</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Keterangan singkat mengenai tipe asset ini..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Tipe Asset Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveAssetType">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const storeUrl = "{{ route('asset-types.store') }}";
    const baseEditUrl = "{{ url('asset-types') }}";

    function openCreateModal() {
        ModalCrud.openCreate('#assetTypeModal', '#assetTypeForm', 'Tambah Tipe Asset Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#is_active').prop('checked', true);
                form.find('#badge_color').val('info');
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#assetTypeModal', '#assetTypeForm', `${baseEditUrl}/${id}`, 'Edit Tipe Asset', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#is_active').prop('checked', Boolean(data.is_active));
            }
        });
    }

    function submitAssetTypeForm() {
        ModalCrud.submitAjax('#assetTypeForm', '#assetTypeModal');
    }

    function deleteAssetType(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Tipe asset ini akan dihapus.');
    }
</script>
@endpush
