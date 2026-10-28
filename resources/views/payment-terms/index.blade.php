@extends('layouts.app')

@section('title', 'Daftar Term Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Term Pembayaran</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Total Termin</span>
                    <h3 class="mb-0 font-weight-bold">{{ $paymentTerms->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-money-check-alt fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Pembayaran Penuh (Full)</span>
                    <h3 class="mb-0 font-weight-bold">{{ $paymentTerms->where('payment_type', 'full')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white border-0 shadow-sm mb-0">
            <div class="card-body d-flex align-items-center p-3">
                <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-coins fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold text-uppercase">Bertahap / Cicilan (Partial)</span>
                    <h3 class="mb-0 font-weight-bold">{{ $paymentTerms->where('payment_type', 'partial')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card mr-2"></i>Daftar Master Term Pembayaran
        </h3>
        <div class="card-tools d-flex align-items-center">
            <a href="{{ route('master-data.export', 'payment-terms') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold" title="Export data term pembayaran ke Excel">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-outline-info btn-sm mr-2 font-weight-bold" onclick="openMasterImportModal('payment-terms', 'Term Pembayaran')">
                <i class="fas fa-file-upload mr-1"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openCreateModal()">
                <i class="fas fa-plus mr-1"></i> Tambah Termin
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <form action="{{ route('payment-terms.index') }}" method="GET" class="mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama termin pembayaran..." value="{{ request('search') }}">
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
                        <a href="{{ route('payment-terms.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th>Nama Termin</th>
                        <th width="150" class="text-center">Tipe Pembayaran</th>
                        <th width="120" class="text-center">Tempo (Hari)</th>
                        <th>Deskripsi</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTerms as $index => $term)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">
                                {{ $paymentTerms->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $term->name }}</div>
                            </td>
                            <td class="text-center">
                                @if($term->payment_type === 'full')
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Pembayaran Penuh</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-percentage mr-1"></i> Cicilan / Bertahap</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($term->payment_type === 'full')
                                    <span class="badge badge-primary font-weight-bold">{{ $term->days }} Hari</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $term->description ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($term->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" title="Edit" 
                                        onclick="openEditModal({{ $term->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" 
                                        onclick="deletePaymentTerm({{ $term->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 font-weight-bold">Belum ada data term pembayaran</p>
                                    <small>Klik tombol "Tambah Termin" di atas untuk menambahkan skema pembayaran baru.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($paymentTerms->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <b>{{ $paymentTerms->firstItem() }}</b> - <b>{{ $paymentTerms->lastItem() }}</b> dari <b>{{ $paymentTerms->total() }}</b> termin
                </div>
                <div>
                    {{ $paymentTerms->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Payment Term (Add & Edit) -->
<div class="modal fade" id="paymentTermModal" tabindex="-1" role="dialog" aria-labelledby="paymentTermModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentTermModalTitle">
                    <i class="fas fa-credit-card text-primary mr-2"></i>
                    <span class="title-text">Tambah Term Pembayaran</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentTermForm" onsubmit="event.preventDefault(); submitPaymentTermForm();">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Termin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Net 30 Hari / DP 50%" required>
                    </div>

                    <div class="form-group">
                        <label for="payment_type">Tipe Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control" id="payment_type" name="payment_type" onchange="toggleDaysField()" required>
                            <option value="full">Pembayaran Penuh (Full Payment)</option>
                            <option value="partial">Bertahap / Cicilan (Partial Payment)</option>
                        </select>
                    </div>

                    <div class="form-group" id="daysGroup">
                        <label for="days">Jangka Waktu Tempo (Hari)</label>
                        <input type="number" class="form-control" id="days" name="days" placeholder="0" min="0" value="30">
                        <small class="form-text text-muted">Isi 0 jika pembayaran tunai/hari itu juga (Cash On Delivery).</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi / Keterangan</label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Ketentuan mengenai termin pembayaran ini..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Status Termin Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSavePaymentTerm">
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
    const storeUrl = "{{ route('payment-terms.store') }}";
    const baseEditUrl = "{{ url('payment-terms') }}";

    function toggleDaysField() {
        const type = $('#payment_type').val();
        if (type === 'full') {
            $('#daysGroup').show();
        } else {
            $('#daysGroup').hide();
        }
    }

    function openCreateModal() {
        ModalCrud.openCreate('#paymentTermModal', '#paymentTermForm', 'Tambah Term Pembayaran', {
            actionUrl: storeUrl,
            beforeShow: function(form) {
                form.find('#payment_type').val('full');
                form.find('#days').val('30');
                form.find('#is_active').prop('checked', true);
                toggleDaysField();
            }
        });
    }

    function openEditModal(id) {
        ModalCrud.openEdit('#paymentTermModal', '#paymentTermForm', `${baseEditUrl}/${id}`, 'Edit Term Pembayaran', {
            updateUrl: `${baseEditUrl}/${id}`,
            afterPopulate: function(data, form) {
                form.find('#payment_type').val(data.payment_type || 'full');
                form.find('#is_active').prop('checked', Boolean(data.is_active));
                toggleDaysField();
            }
        });
    }

    function submitPaymentTermForm() {
        ModalCrud.submitAjax('#paymentTermForm', '#paymentTermModal');
    }

    function deletePaymentTerm(id) {
        ModalCrud.deleteAjax(`${baseEditUrl}/${id}`, 'Term pembayaran ini akan dihapus secara permanen.');
    }
</script>
@endpush
