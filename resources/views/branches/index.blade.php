@extends('layouts.app')

@section('title', 'Daftar Cabang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Cabang</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Cabang / Unit</span>
                    <h3 class="mb-0 font-weight-bold">{{ $branches->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Cabang Operasional Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $branches->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-store-alt mr-2"></i>Daftar Master Cabang
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('master-data.export', 'branches') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data cabang ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('branches', 'Cabang')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Cabang
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <form action="{{ route('branches.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama atau kode cabang..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="80" class="text-center">Logo</th>
                        <th width="120">Kode</th>
                        <th>Nama Cabang</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $index => $branch)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $branches->firstItem() + $index }}
                            </td>
                            <td class="text-center">
                                @if($branch->logo_base64)
                                    <img src="{{ $branch->logo_base64 }}" alt="Logo" class="rounded shadow-xs border" style="width: 36px; height: 36px; object-fit: contain;">
                                @else
                                    <div class="rounded bg-light border d-inline-flex align-items-center justify-content-center text-muted" style="width: 36px; height: 36px;">
                                        <i class="fas fa-building text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary font-monospace">{{ $branch->code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $branch->name }}</div>
                                @if($branch->npwp)
                                    <small class="text-muted"><i class="fas fa-id-card mr-1"></i>NPWP: {{ $branch->npwp }}</small>
                                @endif
                            </td>
                            <td>
                                @if($branch->phone)
                                    <small class="d-block text-dark"><i class="fas fa-phone mr-1 text-muted"></i>{{ $branch->phone }}</small>
                                @endif
                                @if($branch->email)
                                    <small class="d-block text-muted"><i class="fas fa-envelope mr-1 text-muted"></i>{{ $branch->email }}</small>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ Str::limit($branch->address ?? '-', 35) }}
                            </td>
                            <td class="text-center">
                                @if($branch->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $branch->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteBranch({{ $branch->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-building fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data cabang</p>
                                    <small>Klik tombol "Tambah Cabang" di atas untuk menambahkan cabang baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($branches->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $branches->firstItem() }}</b> - <b>{{ $branches->lastItem() }}</b> dari <b>{{ $branches->total() }}</b> cabang
                </div>
                <div>
                    {{ $branches->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Cabang (Add & Edit) -->
<div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="branchModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="branchModalTitle">
                    <i class="fas fa-building text-primary mr-2"></i>
                    <span class="title-text">Tambah Cabang</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="branchForm" onsubmit="event.preventDefault(); submitBranchForm();">
                @csrf
                <input type="hidden" id="logo_base64" name="logo_base64">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Kode Cabang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="HO / JKT" maxlength="20" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nama Cabang / Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Kantor Pusat Jakarta" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">No. Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="021-xxxxxxx">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="branch@example.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="npwp">NPWP Cabang</label>
                                <input type="text" class="form-control" id="npwp" name="npwp" placeholder="00.000.000.0-000.000">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat Lengkap</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Alamat jalan, kota, kodepos..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="footer_note">Catatan Footer PO Cabang</label>
                        <textarea class="form-control" id="footer_note" name="footer_note" rows="2" placeholder="Catatan syarat ketentuan yang tercetak di bawah PO cabang ini..."></textarea>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="form-group mb-0">
                                <label>Logo Cabang (Opsional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_input" accept="image/png,image/jpeg,image/jpg" onchange="previewLogo(this)">
                                    <label class="custom-file-label" for="logo_input">Pilih file logo (PNG, JPG)...</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div id="logo_preview_container" class="mt-2">
                                <img id="logo_preview" src="" alt="Preview Logo" class="img-thumbnail" style="max-height: 50px; display: none;">
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Cabang Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveBranch">
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
    const storeUrl = "{{ route('branches.store') }}";
    const baseEditUrl = "{{ url('branches') }}";

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo_base64').val(e.target.result);
                $('#logo_preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
            $(input).next('.custom-file-label').html(input.files[0].name);
        }
    }

    function openCreateModal() {
        ModalCrud.openCreate('#branchModal', '#branchForm', 'Tambah Cabang Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#logo_base64').val('');
                form.find('#logo_preview').attr('src', '').hide();
                form.find('.custom-file-label').html('Pilih file logo (PNG, JPG)...');
                form.find('#is_active').prop('checked', true);
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#branchModal', '#branchForm', `${baseEditUrl}/${id}`, 'Edit Cabang', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#is_active').prop('checked', Boolean(data.is_active));
                if (data.logo_base64) {
                    form.find('#logo_base64').val(data.logo_base64);
                    form.find('#logo_preview').attr('src', data.logo_base64).show();
                } else {
                    form.find('#logo_base64').val('');
                    form.find('#logo_preview').attr('src', '').hide();
                }
            }
        });
    }

    function submitBranchForm() {
        ModalCrud.submitAjax('#branchForm', '#branchModal');
    }

    function deleteBranch(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Cabang ini akan dihapus secara permanen.');
    }
</script>
@endpush
