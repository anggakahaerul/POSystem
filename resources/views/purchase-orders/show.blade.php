@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">{{ $po->po_number }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- PO Info Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice mr-2"></i>
                        {{ $po->po_number }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $po->status_badge }} px-3 py-2">
                            {{ $po->status_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="140">No. PO</th>
                                    <td>: {{ $po->po_number }}</td>
                                </tr>
                                <tr>
                                    <th>Vendor</th>
                                    <td>: {{ $po->vendor->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Vendor</th>
                                    <td>: {{ $po->vendor->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal PO</th>
                                    <td>: {{ $po->required_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Term Pembayaran</th>
                                    <td>: {{ $po->paymentTerm->display_name ?? 'Tidak ditentukan' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="140">Dibuat Oleh</th>
                                    <td>: {{ $po->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status PO</th>
                                    <td>: <span class="badge badge-{{ $po->status_badge }}">{{ $po->status_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Bayar</th>
                                    <td>: <span
                                            class="badge badge-{{ $po->payment_status_badge ?? 'secondary' }}">{{ $po->payment_status_label ?? 'Belum Lunas' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Barang</th>
                                    <td>: <span
                                            class="badge badge-{{ $po->receipt_status_badge ?? 'secondary' }}">{{ $po->receipt_status_label ?? 'Belum Diterima' }}</span>
                                    </td>
                                </tr>
                                @if ($po->approved_by)
                                    <tr>
                                        <th>{{ $po->status == 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh</th>
                                        <td>: {{ $po->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl Approval</th>
                                        <td>: {{ $po->approved_at ? $po->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @endif
                                @if ($po->payment_due_date)
                                    <tr>
                                        <th>Jatuh Tempo</th>
                                        <td>: {{ $po->payment_due_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @if ($po->note)
                        <hr>
                        <p><strong>Catatan:</strong> {{ $po->note }}</p>
                    @endif
                    @if ($po->status == 'rejected' && $po->rejection_reason)
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle mr-2"></i>
                            <strong>Alasan Penolakan:</strong> {{ $po->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- PO Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Detail Item</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Produk</th>
                                    <th width="80" class="text-center">Qty</th>
                                    @if ($po->status == 'approved')
                                        <th width="100" class="text-center">Diterima</th>
                                    @endif
                                    <th width="150" class="text-right">Harga Satuan</th>
                                    <th width="150" class="text-right">Diskon</th>
                                    <th width="150" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($po->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->product->name ?? '-' }}</strong>
                                            <br><small class="text-muted">{{ $detail->product->sku ?? '' }}</small>
                                        </td>
                                        <td class="text-center">{{ $detail->quantity }} {{ $detail->product->unit ?? '' }}
                                        </td>
                                        @if ($po->status == 'approved')
                                            <td class="text-center">
                                                <span class="badge badge-{{ $detail->receipt_status_badge }}">
                                                    {{ $detail->receipt_status_label }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="text-right">{{ $detail->formatted_unit_price }}</td>
                                        <td class="text-right">{{ $detail->formatted_discount_amount }}</td>
                                        <td class="text-right">{{ $detail->formatted_subtotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="{{ $po->status == 'approved' ? 4 : 3 }}" class="text-right">Total:</th>
                                    <th colspan="2" class="text-right text-primary">{{ $po->formatted_total }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Schedule (if approved) -->
            @if ($po->status == 'approved')
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Jadwal Pembayaran</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($po->payments->count() > 0)
                            <table class="table table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Termin</th>
                                        <th class="text-center">%</th>
                                        <th class="text-right">Nominal</th>
                                        <th class="text-right">Terbayar</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($po->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->description }}</td>
                                            <td class="text-center">{{ $payment->percentage }}%</td>
                                            <td class="text-right">{{ $payment->formatted_amount }}</td>
                                            <td class="text-right">{{ $payment->formatted_paid_amount }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-{{ $payment->status_badge }}">{{ $payment->status_label }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($payment->status !== 'paid')
                                                    <button type="button" class="btn btn-xs btn-success btn-record-payment"
                                                        data-id="{{ $payment->id }}"
                                                        data-amount="{{ $payment->remaining_amount }}"
                                                        data-description="{{ $payment->description }}">
                                                        <i class="fas fa-plus"></i> Bayar
                                                    </button>
                                                @else
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-calendar-alt fa-2x mb-2 d-block"></i>
                                Belum ada jadwal pembayaran.
                                <div class="mt-2">
                                    <form action="{{ route('po-payments.quick', $po) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="type" value="full">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">100% (Penuh)</button>
                                    </form>
                                    <form action="{{ route('po-payments.quick', $po) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="type" value="dp50">
                                        <button type="submit" class="btn btn-sm btn-outline-info">DP 50% +
                                            Pelunasan</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Actions Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cogs mr-2"></i>Aksi</h3>
                </div>
                <div class="card-body">
                    @if ($po->canEdit())
                        <a href="{{ route('purchase-orders.edit', $po->id) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit mr-2"></i> Edit PO
                        </a>
                    @endif

                    @if ($po->canSubmit())
                        <button type="button" class="btn btn-info btn-block mt-2" data-toggle="modal"
                            data-target="#submitModal">
                            <i class="fas fa-paper-plane mr-2"></i> Submit untuk Approval
                        </button>
                    @endif

                    @if ($po->canApprove() && Auth::user()->canApprovePO())
                        <hr>
                        <h6 class="text-muted">Approval</h6>
                        <button type="button" class="btn btn-success btn-block mt-2" data-toggle="modal"
                            data-target="#approveModal">
                            <i class="fas fa-check mr-2"></i> Approve PO
                        </button>
                        <button type="button" class="btn btn-danger btn-block mt-2" data-toggle="modal"
                            data-target="#rejectModal">
                            <i class="fas fa-times mr-2"></i> Reject PO
                        </button>
                    @endif

                    @if (in_array($po->status, ['draft', 'approved']))
                        <a href="{{ route('purchase-orders.preview', $po->id) }}"
                            class="btn btn-outline-info btn-block mt-2" target="_blank">
                            <i class="fas fa-eye mr-2"></i> Preview PDF
                        </a>
                    @endif

                    @if ($po->status == 'approved')
                        <a href="{{ route('purchase-orders.print', $po->id) }}" class="btn btn-primary btn-block"
                            target="_blank">
                            <i class="fas fa-print mr-2"></i> Print PDF
                        </a>
                    @endif

                    @if ($po->canReceive())
                        <hr>
                        <h6 class="text-muted">Penerimaan Barang</h6>
                        <button type="button" class="btn btn-success btn-block mt-2" data-toggle="modal"
                            data-target="#receiveModal">
                            <i class="fas fa-box-open mr-2"></i> Terima Barang
                        </button>
                        <form action="{{ route('purchase-orders.receive', $po->id) }}" method="POST" class="mt-2"
                            id="form-receive-full">
                            @csrf
                            <input type="hidden" name="type" value="full">
                            <button type="button" class="btn btn-outline-success btn-block btn-receive-full">
                                <i class="fas fa-check-double mr-2"></i> Terima Semua (Lengkap)
                            </button>
                        </form>
                    @endif

                    <hr>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>

                    @if ($po->canEdit())
                        <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="mt-2"
                            id="delete-po-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger btn-block"
                                onclick="confirmDelete('delete-po-form')">
                                <i class="fas fa-trash mr-2"></i> Hapus PO
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Ringkasan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Total PO</td>
                            <td class="text-right"><strong>{{ $po->formatted_total }}</strong></td>
                        </tr>
                        <tr>
                            <td>Terbayar</td>
                            <td class="text-right text-success">Rp {{ number_format($po->total_paid ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Sisa</strong></td>
                            <td class="text-right text-danger"><strong>Rp
                                    {{ number_format($po->remaining_amount ?? $po->total_amount, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-2"></i>Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">
                        <div class="time-label">
                            <span class="bg-secondary">{{ $po->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i>
                                    {{ $po->created_at->format('H:i') }}</span>
                                <h3 class="timeline-header">PO Dibuat</h3>
                                <div class="timeline-body">
                                    Oleh {{ $po->creator->name ?? 'Unknown' }}
                                </div>
                            </div>
                        </div>
                        @if ($po->status != 'draft')
                            <div>
                                <i class="fas fa-paper-plane bg-info"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">PO Disubmit</h3>
                                    <div class="timeline-body">Menunggu approval</div>
                                </div>
                            </div>
                        @endif
                        @if ($po->approved_at)
                            <div>
                                <i
                                    class="fas fa-{{ $po->status == 'approved' ? 'check' : 'times' }} bg-{{ $po->status == 'approved' ? 'success' : 'danger' }}"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ $po->approved_at->format('H:i') }}</span>
                                    <h3 class="timeline-header">PO
                                        {{ $po->status == 'approved' ? 'Disetujui' : 'Ditolak' }}</h3>
                                    <div class="timeline-body">
                                        Oleh {{ $po->approver->name ?? 'Unknown' }}
                                        @if ($po->rejection_reason)
                                            <br><small class="text-danger">{{ $po->rejection_reason }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($po->received_at)
                            <div>
                                <i class="fas fa-box-open bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ $po->received_at->format('H:i') }}</span>
                                    <h3 class="timeline-header">Barang Diterima</h3>
                                    <div class="timeline-body">
                                        Oleh {{ $po->receiver->name ?? 'Unknown' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Signature Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('purchase-orders.submit', $po->id) }}" method="POST" id="form-submit">
                    @csrf
                    <input type="hidden" name="creator_signature" id="creator_signature_input">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="fas fa-signature mr-2"></i>Tanda Tangan Pembuat</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Silakan tanda tangan untuk submit PO <strong>{{ $po->po_number }}</strong>:</p>
                        
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#creator-draw-tab">
                                    <i class="fas fa-pen mr-1"></i> Gambar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#creator-upload-tab">
                                    <i class="fas fa-upload mr-1"></i> Upload
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Draw Tab -->
                            <div class="tab-pane fade show active" id="creator-draw-tab">
                                <div class="signature-pad-container" style="border: 2px solid #ccc; border-radius: 5px;">
                                    <canvas id="creator-signature-pad" style="width: 100%; height: 200px;"></canvas>
                                </div>
                                <div class="mt-2 text-right">
                                    <button type="button" class="btn btn-sm btn-secondary" id="clear-creator-signature">
                                        <i class="fas fa-eraser mr-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Upload Tab -->
                            <div class="tab-pane fade" id="creator-upload-tab">
                                <div class="form-group">
                                    <label>Upload Gambar Tanda Tangan</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="creator-signature-file" accept="image/*">
                                        <label class="custom-file-label" for="creator-signature-file">Pilih file...</label>
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, PNG, GIF. Max 2MB</small>
                                </div>
                                <div id="creator-upload-preview" class="text-center mt-3" style="display: none;">
                                    <img id="creator-upload-img" src="" alt="Preview" style="max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" id="clear-creator-upload">
                                            <i class="fas fa-times mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info" id="btn-submit-with-signature">
                            <i class="fas fa-paper-plane mr-1"></i> Submit PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Approve Signature Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('purchase-orders.approve', $po->id) }}" method="POST" id="form-approve">
                    @csrf
                    <input type="hidden" name="approver_signature" id="approver_signature_input">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-signature mr-2"></i>Tanda Tangan Approver</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Silakan tanda tangan untuk approve PO <strong>{{ $po->po_number }}</strong>:</p>
                        
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#approver-draw-tab">
                                    <i class="fas fa-pen mr-1"></i> Gambar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#approver-upload-tab">
                                    <i class="fas fa-upload mr-1"></i> Upload
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Draw Tab -->
                            <div class="tab-pane fade show active" id="approver-draw-tab">
                                <div class="signature-pad-container" style="border: 2px solid #ccc; border-radius: 5px;">
                                    <canvas id="approver-signature-pad" style="width: 100%; height: 200px;"></canvas>
                                </div>
                                <div class="mt-2 text-right">
                                    <button type="button" class="btn btn-sm btn-secondary" id="clear-approver-signature">
                                        <i class="fas fa-eraser mr-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Upload Tab -->
                            <div class="tab-pane fade" id="approver-upload-tab">
                                <div class="form-group">
                                    <label>Upload Gambar Tanda Tangan</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="approver-signature-file" accept="image/*">
                                        <label class="custom-file-label" for="approver-signature-file">Pilih file...</label>
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, PNG, GIF. Max 2MB</small>
                                </div>
                                <div id="approver-upload-preview" class="text-center mt-3" style="display: none;">
                                    <img id="approver-upload-img" src="" alt="Preview" style="max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" id="clear-approver-upload">
                                            <i class="fas fa-times mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn-approve-with-signature">
                            <i class="fas fa-check mr-1"></i> Approve PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('purchase-orders.reject', $po->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="fas fa-times-circle text-danger mr-2"></i>Tolak
                                        Purchase Order
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="rejection_reason">Alasan Penolakan <span
                                                class="text-danger">*</span></label>
                                        <textarea name="rejection_reason" id="rejection_reason"
                                            class="form-control @error('rejection_reason') is-invalid @enderror" rows="4" required
                                            placeholder="Jelaskan alasan penolakan PO ini (min. 10 karakter)">{{ old('rejection_reason') }}</textarea>
                                        @error('rejection_reason')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times mr-1"></i> Tolak PO
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Record Payment Modal -->
                <div class="modal fade" id="paymentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="form-record-payment" method="POST">
                                @csrf
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title"><i class="fas fa-money-bill-wave mr-2"></i>Catat Pembayaran
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong id="payment-description"></strong></p>
                                    <div class="form-group">
                                        <label>Jumlah Pembayaran <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="paid_amount" id="paid_amount"
                                                class="form-control money-format" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Catatan</label>
                                        <textarea name="note" class="form-control" rows="2" placeholder="Nomor bukti transfer, dll"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check mr-1"></i> Simpan Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Receive Items Modal -->
                @if ($po->status == 'approved')
                <div class="modal fade" id="receiveModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="{{ route('purchase-orders.receive', $po->id) }}" method="POST" id="form-receive-items">
                                @csrf
                                <input type="hidden" name="type" value="items">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title"><i class="fas fa-box-open mr-2"></i>Penerimaan Barang</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-1"></i> Masukkan jumlah barang yang diterima untuk setiap item. Isi <strong>0</strong> jika item belum diterima.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="select-all-items">
                                                            <label class="custom-control-label" for="select-all-items">Semua</label>
                                                        </div>
                                                    </th>
                                                    <th>Produk</th>
                                                    <th class="text-center" width="100">Qty Order</th>
                                                    <th class="text-center" width="100">Sudah Terima</th>
                                                    <th class="text-center" width="120">Terima Sekarang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($po->details as $detail)
                                                    @php
                                                        $remaining = $detail->remaining_receive_quantity;
                                                    @endphp
                                                    <tr class="{{ $detail->is_fully_received ? 'table-success' : '' }}">
                                                        <td class="text-center">
                                                            @if (!$detail->is_fully_received)
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input item-checkbox" 
                                                                        id="check-{{ $detail->id }}"
                                                                        data-id="{{ $detail->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    <label class="custom-control-label" for="check-{{ $detail->id }}"></label>
                                                                </div>
                                                            @else
                                                                <i class="fas fa-check text-success"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $detail->product->name ?? '-' }}</strong>
                                                            <br><small class="text-muted">{{ $detail->product->sku ?? '' }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $detail->quantity }} {{ $detail->product->unit ?? '' }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $detail->receipt_status_badge }}">
                                                                {{ $detail->received_quantity ?? 0 }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if (!$detail->is_fully_received)
                                                                <input type="number" 
                                                                    name="items[{{ $detail->id }}]" 
                                                                    class="form-control form-control-sm text-center item-qty"
                                                                    id="qty-{{ $detail->id }}"
                                                                    min="0" 
                                                                    max="{{ $remaining }}"
                                                                    value="0"
                                                                    style="width: 80px; margin: 0 auto;">
                                                            @else
                                                                <span class="text-success"><i class="fas fa-check"></i> Lengkap</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-box-open mr-1"></i> Simpan Penerimaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
                <script>
                    $(document).ready(function() {
                        // Submit PO with SweetAlert
                        $('.btn-submit-po').on('click', function() {
                            Swal.fire({
                                title: 'Submit PO?',
                                text: 'PO akan disubmit untuk approval dan tidak bisa diedit lagi.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#17a2b8',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Submit!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#form-submit').submit();
                                }
                            });
                        });

                        // Approve PO with SweetAlert
                        $('.btn-approve-po').on('click', function() {
                            Swal.fire({
                                title: 'Approve PO?',
                                text: 'PO akan disetujui dan bisa dicetak.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Approve!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#form-approve').submit();
                                }
                            });
                        });

                        // Receive full with SweetAlert
                        $('.btn-receive-full').on('click', function() {
                            Swal.fire({
                                title: 'Terima Barang?',
                                text: 'Konfirmasi bahwa semua barang sudah diterima lengkap.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Terima!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#form-receive-full').submit();
                                }
                            });
                        });

                        // Receive Items Modal - Select All Checkbox
                        $('#select-all-items').on('change', function() {
                            var isChecked = $(this).is(':checked');
                            $('.item-checkbox').each(function() {
                                $(this).prop('checked', isChecked);
                                var id = $(this).data('id');
                                var remaining = $(this).data('remaining');
                                var qtyInput = $('#qty-' + id);
                                if (isChecked) {
                                    qtyInput.val(remaining);
                                } else {
                                    qtyInput.val(0);
                                }
                            });
                        });

                        // Individual item checkbox - auto-fill quantity when checked
                        $('.item-checkbox').on('change', function() {
                            var id = $(this).data('id');
                            var remaining = $(this).data('remaining');
                            var qtyInput = $('#qty-' + id);
                            
                            if ($(this).is(':checked')) {
                                qtyInput.val(remaining);
                            } else {
                                qtyInput.val(0);
                            }

                            // Update select-all checkbox state
                            var allChecked = $('.item-checkbox:checked').length === $('.item-checkbox').length;
                            $('#select-all-items').prop('checked', allChecked);
                        });

                        // Update checkbox when quantity input changes
                        $('.item-qty').on('input', function() {
                            var id = $(this).attr('id').replace('qty-', '');
                            var checkbox = $('#check-' + id);
                            var value = parseInt($(this).val()) || 0;
                            
                            checkbox.prop('checked', value > 0);
                            
                            // Update select-all checkbox state
                            var allChecked = $('.item-checkbox:checked').length === $('.item-checkbox').length;
                            $('#select-all-items').prop('checked', allChecked);
                        });

                        // Record Payment Modal
                        $('.btn-record-payment').on('click', function() {
                            var id = $(this).data('id');
                            var amount = $(this).data('amount');
                            var description = $(this).data('description');

                            $('#payment-description').text(description);
                            $('#paid_amount').val(formatMoney(amount));
                            $('#form-record-payment').attr('action', '{{ url("po-payments") }}/' + id + '/record');
                            $('#paymentModal').modal('show');
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

                        // SignaturePad initialization
                        var creatorCanvas = document.getElementById('creator-signature-pad');
                        var approverCanvas = document.getElementById('approver-signature-pad');
                        var creatorSignaturePad, approverSignaturePad;

                        function resizeCanvas(canvas, signaturePad) {
                            var ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            canvas.getContext("2d").scale(ratio, ratio);
                            if (signaturePad) {
                                signaturePad.clear();
                            }
                        }

                        // Initialize creator signature pad when modal opens
                        $('#submitModal').on('shown.bs.modal', function() {
                            if (!creatorSignaturePad && creatorCanvas) {
                                resizeCanvas(creatorCanvas);
                                creatorSignaturePad = new SignaturePad(creatorCanvas, {
                                    backgroundColor: 'rgb(255, 255, 255)',
                                    penColor: 'rgb(0, 0, 0)'
                                });
                            }
                        });

                        // Initialize approver signature pad when modal opens
                        $('#approveModal').on('shown.bs.modal', function() {
                            if (!approverSignaturePad && approverCanvas) {
                                resizeCanvas(approverCanvas);
                                approverSignaturePad = new SignaturePad(approverCanvas, {
                                    backgroundColor: 'rgb(255, 255, 255)',
                                    penColor: 'rgb(0, 0, 0)'
                                });
                            }
                        });

                        // Clear canvas buttons
                        $('#clear-creator-signature').on('click', function() {
                            if (creatorSignaturePad) creatorSignaturePad.clear();
                        });

                        $('#clear-approver-signature').on('click', function() {
                            if (approverSignaturePad) approverSignaturePad.clear();
                        });

                        // File upload variables
                        var creatorUploadData = null;
                        var approverUploadData = null;

                        // Function to resize image
                        function resizeImage(file, maxWidth, maxHeight, callback) {
                            var reader = new FileReader();
                            reader.onload = function(event) {
                                var img = new Image();
                                img.onload = function() {
                                    var canvas = document.createElement('canvas');
                                    var width = img.width;
                                    var height = img.height;

                                    // Calculate new dimensions
                                    if (width > height) {
                                        if (width > maxWidth) {
                                            height = Math.round(height * maxWidth / width);
                                            width = maxWidth;
                                        }
                                    } else {
                                        if (height > maxHeight) {
                                            width = Math.round(width * maxHeight / height);
                                            height = maxHeight;
                                        }
                                    }

                                    canvas.width = width;
                                    canvas.height = height;
                                    var ctx = canvas.getContext('2d');
                                    ctx.fillStyle = '#FFFFFF';
                                    ctx.fillRect(0, 0, width, height);
                                    ctx.drawImage(img, 0, 0, width, height);
                                    
                                    // Convert to base64 PNG
                                    callback(canvas.toDataURL('image/png', 0.8));
                                };
                                img.src = event.target.result;
                            };
                            reader.readAsDataURL(file);
                        }

                        // Creator file upload with resize
                        $('#creator-signature-file').on('change', function(e) {
                            var file = e.target.files[0];
                            var input = $(this);
                            if (file) {
                                if (file.size > 2 * 1024 * 1024) {
                                    Swal.fire('Error', 'File terlalu besar. Max 2MB.', 'error');
                                    return;
                                }
                                resizeImage(file, 300, 150, function(resizedData) {
                                    creatorUploadData = resizedData;
                                    $('#creator-upload-img').attr('src', creatorUploadData);
                                    $('#creator-upload-preview').show();
                                });
                                input.next('.custom-file-label').text(file.name);
                            }
                        });

                        // Approver file upload with resize
                        $('#approver-signature-file').on('change', function(e) {
                            var file = e.target.files[0];
                            var input = $(this);
                            if (file) {
                                if (file.size > 2 * 1024 * 1024) {
                                    Swal.fire('Error', 'File terlalu besar. Max 2MB.', 'error');
                                    return;
                                }
                                resizeImage(file, 300, 150, function(resizedData) {
                                    approverUploadData = resizedData;
                                    $('#approver-upload-img').attr('src', approverUploadData);
                                    $('#approver-upload-preview').show();
                                });
                                input.next('.custom-file-label').text(file.name);
                            }
                        });

                        // Clear upload buttons
                        $('#clear-creator-upload').on('click', function() {
                            creatorUploadData = null;
                            $('#creator-signature-file').val('');
                            $('#creator-signature-file').next('.custom-file-label').text('Pilih file...');
                            $('#creator-upload-preview').hide();
                        });

                        $('#clear-approver-upload').on('click', function() {
                            approverUploadData = null;
                            $('#approver-signature-file').val('');
                            $('#approver-signature-file').next('.custom-file-label').text('Pilih file...');
                            $('#approver-upload-preview').hide();
                        });

                        // Submit form with signature (canvas or upload)
                        $('#form-submit').on('submit', function(e) {
                            var activeTab = $('#submitModal .tab-pane.active').attr('id');
                            var hasSignature = false;

                            if (activeTab === 'creator-draw-tab') {
                                if (creatorSignaturePad && !creatorSignaturePad.isEmpty()) {
                                    $('#creator_signature_input').val(creatorSignaturePad.toDataURL('image/png'));
                                    hasSignature = true;
                                }
                            } else if (activeTab === 'creator-upload-tab') {
                                if (creatorUploadData) {
                                    $('#creator_signature_input').val(creatorUploadData);
                                    hasSignature = true;
                                }
                            }

                            if (!hasSignature) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tanda Tangan Diperlukan',
                                    text: 'Silakan gambar atau upload tanda tangan terlebih dahulu.'
                                });
                                return false;
                            }
                        });

                        // Approve form with signature (canvas or upload)
                        $('#form-approve').on('submit', function(e) {
                            var activeTab = $('#approveModal .tab-pane.active').attr('id');
                            var hasSignature = false;

                            if (activeTab === 'approver-draw-tab') {
                                if (approverSignaturePad && !approverSignaturePad.isEmpty()) {
                                    $('#approver_signature_input').val(approverSignaturePad.toDataURL('image/png'));
                                    hasSignature = true;
                                }
                            } else if (activeTab === 'approver-upload-tab') {
                                if (approverUploadData) {
                                    $('#approver_signature_input').val(approverUploadData);
                                    hasSignature = true;
                                }
                            }

                            if (!hasSignature) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tanda Tangan Diperlukan',
                                    text: 'Silakan gambar atau upload tanda tangan terlebih dahulu.'
                                });
                                return false;
                            }
                        });
                    });
                </script>
                <!-- SignaturePad Library -->
                <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endpush
