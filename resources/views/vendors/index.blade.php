@extends('layouts.app')

@section('title', 'Daftar Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Vendor</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-truck fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Vendor Mitra</span>
                    <h3 class="mb-0 font-weight-bold">{{ $vendors->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-user-check fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Vendor Aktif</span>
                    <h3 class="mb-0 font-weight-bold">{{ $vendors->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                    <i class="fas fa-file-invoice fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Purchase Orders</span>
                    <h3 class="mb-0 font-weight-bold">{{ $vendors->sum('purchase_orders_count') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-handshake mr-2"></i>Daftar Mitra Vendor
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('master-data.export', 'vendors') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data vendor ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('vendors', 'Vendor')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Vendor
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form action="{{ route('vendors.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama, kode, email, atau telepon vendor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="120">Kode</th>
                        <th>Nama Vendor</th>
                        <th>Kontak & Person</th>
                        <th>Alamat</th>
                        <th width="100" class="text-center">Total PO</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="130" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $index => $vendor)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $vendors->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge badge-secondary font-monospace">{{ $vendor->code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $vendor->name }}</div>
                                @if($vendor->paymentTerm)
                                    <span class="badge badge-light border text-primary mt-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-file-invoice-dollar mr-1"></i>TOP: {{ $vendor->paymentTerm->name }}
                                    </span>
                                @endif
                                @if($vendor->npwp || $vendor->npwp_type)
                                    <div class="mt-1">
                                        @if($vendor->npwp)
                                            <small class="text-muted"><i class="fas fa-id-card mr-1"></i>{{ $vendor->npwp }}</small>
                                        @endif
                                        @if($vendor->npwp_type)
                                            <span class="badge badge-light border text-info ml-1" style="font-size: 0.72rem;">{{ $vendor->npwp_type }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($vendor->contact_person)
                                    <div class="font-weight-bold small text-dark"><i class="fas fa-user mr-1 text-muted"></i>{{ $vendor->contact_person }}</div>
                                @endif
                                @if($vendor->phone)
                                    <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i>{{ $vendor->phone }}</small>
                                @endif
                                @if($vendor->email)
                                    <small class="text-muted d-block"><i class="fas fa-envelope mr-1"></i>{{ $vendor->email }}</small>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ Str::limit($vendor->address ?? '-', 40) }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $vendor->purchase_orders_count }} PO</span>
                            </td>
                            <td class="text-center">
                                @if($vendor->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $vendor->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deleteVendor({{ $vendor->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-truck fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data vendor</p>
                                    <small>Klik tombol "Tambah Vendor" di atas untuk menambahkan mitra baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vendors->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $vendors->firstItem() }}</b> - <b>{{ $vendors->lastItem() }}</b> dari <b>{{ $vendors->total() }}</b> vendor
                </div>
                <div>
                    {{ $vendors->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Vendor (Add & Edit) -->
<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorModalTitle">
                    <i class="fas fa-truck text-primary mr-2"></i>
                    <span class="title-text">Tambah Vendor</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vendorForm" onsubmit="event.preventDefault(); submitVendorForm();">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Kode Vendor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" placeholder="VND-0001" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary" onclick="generateCode()" title="Generate Kode">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nama Vendor / Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="PT. Mitra Sejahtera Bersama" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="contact_person">Contact Person (PIC)</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Nama PIC">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">No. Telepon / WA</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="0812xxxxxxxx">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="vendor@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_term_id">Term of Payment</label>
                                <select class="form-control" id="payment_term_id" name="payment_term_id">
                                    <option value="">-- Pilih Term of Payment --</option>
                                    @if(isset($paymentTerms))
                                        @foreach($paymentTerms as $term)
                                            <option value="{{ $term->id }}">{{ $term->display_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bank_info">Informasi Rekening Bank</label>
                                <input type="text" class="form-control" id="bank_info" name="bank_info" placeholder="BCA 123456789 a.n PT Mitra">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="npwp_type">Tipe NPWP</label>
                                <select class="form-control" id="npwp_type" name="npwp_type">
                                    <option value="">-- Pilih Tipe NPWP --</option>
                                    <option value="Orang Pribadi">NPWP Orang Pribadi</option>
                                    <option value="Badan">NPWP Badan</option>
                                    <option value="Instansi Pemerintah">NPWP Instansi Pemerintah</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="npwp">Nomor NPWP Vendor</label>
                                <input type="text" class="form-control" id="npwp" name="npwp" placeholder="00.000.000.0-000.000">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat Kantor / Gudang</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Alamat lengkap vendor..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Vendor Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveVendor">
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
    const storeUrl = "{{ route('vendors.store') }}";
    const baseEditUrl = "{{ url('vendors') }}";
    const defaultCode = "{{ $suggestedCode }}";

    function generateCode() {
        $('#code').val(defaultCode);
    }

    function openCreateModal() {
        ModalCrud.openCreate('#vendorModal', '#vendorForm', 'Tambah Vendor Baru', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#code').val(defaultCode);
                form.find('#payment_term_id').val('');
                form.find('#npwp_type').val('');
                form.find('#is_active').prop('checked', true);
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#vendorModal', '#vendorForm', `${baseEditUrl}/${id}`, 'Edit Vendor', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#payment_term_id').val(data.payment_term_id || '');
                form.find('#npwp_type').val(data.npwp_type || '');
                form.find('#is_active').prop('checked', Boolean(data.is_active));
            }
        });
    }

    function submitVendorForm() {
        ModalCrud.submitAjax('#vendorForm', '#vendorModal');
    }

    function deleteVendor(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Vendor ini akan dihapus secara permanen.');
    }
</script>
@endpush
