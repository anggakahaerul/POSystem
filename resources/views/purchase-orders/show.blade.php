@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">{{ $po->po_number }}</li>
@endsection

@section('content')
    @php
        // --- STEP 1: PO Dibuat ---
        $step1Class = 'step-completed';

        // --- STEP 2: PO Disubmit ---
        $step2Done = ($po->status != 'draft');
        $step2Class = $step2Done ? 'step-completed' : 'step-greyscale';

        // --- STEP 3: Persetujuan (Approval) ---
        $step3Done = ($po->status == 'approved');
        $step3Rejected = ($po->status == 'rejected');
        $step3Pending = ($po->status == 'pending');

        if ($step3Done) {
            $step3Class = 'step-completed';
        } elseif ($step3Rejected) {
            $step3Class = 'step-rejected';
        } elseif ($step3Pending) {
            $step3Class = 'step-current status-warning';
        } else {
            $step3Class = 'step-greyscale';
        }

        // --- STEP 4: Pembayaran ---
        $totalPaid = $po->total_paid ?? 0;
        $step4Done = ($po->payment_status == 'paid');
        $step4Partial = ($po->payment_status == 'partial');

        if ($step4Done) {
            $step4Class = 'step-completed';
        } elseif ($step4Partial) {
            $step4Class = 'step-current status-info';
        } elseif ($po->status == 'approved') {
            $step4Class = 'step-current status-pending';
        } else {
            $step4Class = 'step-greyscale';
        }

        // --- STEP 5: Penerimaan Barang ---
        $totalQtyOrdered = $po->details->sum('quantity');
        $totalQtyReceived = $po->details->sum('received_quantity');
        $receiptPercent = $totalQtyOrdered > 0 ? round(($totalQtyReceived / $totalQtyOrdered) * 100) : 0;

        $step5Done = ($po->status == 'approved' && $po->receipt_status == 'received');
        $step5Partial = ($po->status == 'approved' && $po->receipt_status == 'partial');
        $step5Waiting = ($po->status == 'approved' && $po->receipt_status == 'not_received');

        if ($step5Done) {
            $step5Class = 'step-completed';
        } elseif ($step5Partial) {
            $step5Class = 'step-current status-info';
        } elseif ($step5Waiting) {
            $step5Class = 'step-current status-pending';
        } else {
            $step5Class = 'step-greyscale';
        }

        // --- STEP 6: PO Selesai ---
        $step6Done = ($po->status == 'approved' && $po->receipt_status == 'received' && $po->payment_status == 'paid');
        $step6Class = $step6Done ? 'step-completed' : 'step-greyscale';

        // Progress bar percentage calculation for 6 steps
        if ($step3Rejected) {
            $progressWidth = 40;
            $progressTrackClass = 'bg-danger';
        } elseif ($step6Done) {
            $progressWidth = 100;
            $progressTrackClass = 'bg-success';
        } elseif ($step4Done && $step5Done) {
            $progressWidth = 85;
            $progressTrackClass = 'bg-success';
        } elseif ($step4Done || $step5Done) {
            $progressWidth = 72;
            $progressTrackClass = 'bg-primary';
        } elseif ($step4Partial || $step5Partial) {
            $progressWidth = 60;
            $progressTrackClass = 'bg-info';
        } elseif ($step3Done) {
            $progressWidth = 45;
            $progressTrackClass = 'bg-primary';
        } elseif ($step3Pending) {
            $progressWidth = 35;
            $progressTrackClass = 'bg-warning';
        } elseif ($step2Done) {
            $progressWidth = 20;
            $progressTrackClass = 'bg-primary';
        } else {
            $progressWidth = 8;
            $progressTrackClass = 'bg-secondary';
        }
    @endphp

    <!-- Top Roadmap Stepper Card (6 Steps) -->
    <div class="card shadow-sm border-0 mb-4 roadmap-card">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold text-dark mb-1">
                <i class="fas fa-route text-primary mr-2"></i>Roadmap & Progress PO
            </h5>
            <div class="card-tools d-flex align-items-center mb-1">
                <span class="badge badge-{{ $po->status_badge }} px-3 py-1 mr-2" style="font-size: 0.8rem;">
                    <i class="{{ $po->status_icon }} mr-1"></i>Status PO: {{ $po->status_label }}
                </span>
                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.8rem;">
                    <code>{{ $po->po_number }}</code>
                </span>
            </div>
        </div>

        <div class="card-body pt-3 pb-4 px-3">
            <div class="roadmap-stepper">
                <!-- Progress Line Track -->
                <div class="roadmap-progress-track">
                    <div class="roadmap-progress-fill {{ $progressTrackClass }}" style="width: {{ $progressWidth }}%;"></div>
                </div>

                <!-- 6 Steps Container -->
                <div class="roadmap-steps">
                    <!-- Step 1: PO Dibuat -->
                    <div class="roadmap-step {{ $step1Class }}">
                        <div class="roadmap-node">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 1</span>
                            <h6 class="roadmap-title">PO Dibuat</h6>
                            <span class="badge badge-success roadmap-badge">
                                <i class="fas fa-check-circle mr-1"></i>Draft Selesai
                            </span>
                            <div class="roadmap-meta">
                                <span class="roadmap-actor" title="{{ $po->creator->name ?? 'User' }}">
                                    <i class="fas fa-user mr-1 text-muted"></i>{{ Str::limit($po->creator->name ?? 'User', 14) }}
                                </span>
                                <span class="roadmap-time">
                                    <i class="far fa-clock mr-1"></i>{{ $po->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: PO Disubmit -->
                    <div class="roadmap-step {{ $step2Class }}">
                        <div class="roadmap-node">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 2</span>
                            <h6 class="roadmap-title">PO Disubmit</h6>
                            @if ($step2Done)
                                <span class="badge badge-success roadmap-badge">
                                    <i class="fas fa-check mr-1"></i>Telah Disubmit
                                </span>
                                <div class="roadmap-meta">
                                    <span class="roadmap-actor" title="{{ $po->creator->name ?? 'User' }}">
                                        <i class="fas fa-paper-plane mr-1 text-muted"></i>{{ Str::limit($po->creator->name ?? 'User', 14) }}
                                    </span>
                                    <span class="roadmap-time">
                                        <i class="far fa-clock mr-1"></i>{{ $po->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @else
                                <span class="badge badge-secondary roadmap-badge">Belum Disubmit</span>
                                <div class="roadmap-meta">
                                    <span class="roadmap-actor text-muted">Menunggu Submit</span>
                                    <span class="roadmap-time text-muted">-</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3: Persetujuan (Menunggu / Disetujui / Ditolak) -->
                    <div class="roadmap-step {{ $step3Class }}">
                        <div class="roadmap-node">
                            @if ($step3Done)
                                <i class="fas fa-check-circle"></i>
                            @elseif ($step3Rejected)
                                <i class="fas fa-times-circle"></i>
                            @elseif ($step3Pending)
                                <i class="fas fa-clock"></i>
                            @else
                                <i class="fas fa-user-shield"></i>
                            @endif
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 3</span>
                            <h6 class="roadmap-title">Persetujuan</h6>
                            @if ($step3Done)
                                <span class="badge badge-success roadmap-badge">
                                    <i class="fas fa-check mr-1"></i>Disetujui
                                </span>
                            @elseif ($step3Rejected)
                                <span class="badge badge-danger roadmap-badge">
                                    <i class="fas fa-times mr-1"></i>Ditolak
                                </span>
                            @elseif ($step3Pending)
                                <span class="badge badge-warning roadmap-badge text-dark font-weight-bold">
                                    <i class="fas fa-hourglass-half mr-1"></i>Menunggu Approval
                                </span>
                            @else
                                <span class="badge badge-secondary roadmap-badge">Belum Diajukan</span>
                            @endif

                            <div class="roadmap-meta">
                                @if ($step3Done || $step3Rejected)
                                    <span class="roadmap-actor" title="{{ $po->approver->name ?? '-' }}">
                                        <i class="fas fa-user-shield mr-1 text-muted"></i>{{ Str::limit($po->approver->name ?? '-', 14) }}
                                    </span>
                                    <span class="roadmap-time">
                                        <i class="far fa-clock mr-1"></i>{{ $po->approved_at ? $po->approved_at->format('d/m/Y H:i') : '-' }}
                                    </span>
                                    @if ($step3Rejected && $po->rejection_reason)
                                        <span class="text-danger d-block mt-1 font-weight-bold" style="font-size: 0.7rem;" title="{{ $po->rejection_reason }}">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ Str::limit($po->rejection_reason, 18) }}
                                        </span>
                                    @endif
                                @elseif ($step3Pending)
                                    <span class="roadmap-actor text-dark font-weight-bold">
                                        <i class="fas fa-user-clock mr-1 text-warning"></i>Menunggu Manajer
                                    </span>
                                    <span class="roadmap-time text-muted">Dalam Antrean</span>
                                @else
                                    <span class="roadmap-actor text-muted">-</span>
                                    <span class="roadmap-time text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Pembayaran -->
                    <div class="roadmap-step {{ $step4Class }}">
                        <div class="roadmap-node">
                            @if ($step4Done)
                                <i class="fas fa-money-check-alt"></i>
                            @elseif ($step4Partial)
                                <i class="fas fa-hourglass-half"></i>
                            @else
                                <i class="fas fa-wallet"></i>
                            @endif
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 4</span>
                            <h6 class="roadmap-title">Pembayaran</h6>
                            @if ($step4Done)
                                <span class="badge badge-success roadmap-badge">
                                    <i class="fas fa-check-circle mr-1"></i>Lunas
                                </span>
                            @elseif ($step4Partial)
                                <span class="badge badge-info roadmap-badge">
                                    <i class="fas fa-coins mr-1"></i>Sebagian
                                </span>
                            @else
                                <span class="badge badge-secondary roadmap-badge">Belum Lunas</span>
                            @endif
                        </div>
                    </div>

                    <!-- Step 5: Penerimaan Barang -->
                    <div class="roadmap-step {{ $step5Class }}">
                        <div class="roadmap-node">
                            @if ($step5Done)
                                <i class="fas fa-check-double"></i>
                            @elseif ($step5Partial)
                                <i class="fas fa-box-open"></i>
                            @else
                                <i class="fas fa-truck-loading"></i>
                            @endif
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 5</span>
                            <h6 class="roadmap-title">Penerimaan</h6>
                            @if ($step5Done)
                                <span class="badge badge-success roadmap-badge">
                                    <i class="fas fa-check-double mr-1"></i>Diterima Lengkap
                                </span>
                            @elseif ($step5Partial)
                                <span class="badge badge-info roadmap-badge">
                                    <i class="fas fa-box-open mr-1"></i>Sebagian
                                </span>
                            @elseif ($step5Waiting)
                                <span class="badge badge-secondary roadmap-badge">
                                    <i class="fas fa-box mr-1"></i>Belum Diterima
                                </span>
                            @else
                                <span class="badge badge-secondary roadmap-badge">Belum Diterima</span>
                            @endif

                            <!-- Histori Penerimaan Hover Popover Trigger -->
                            <div class="receipt-hover-container mt-1">
                                <span class="receipt-hover-trigger" tabindex="0">
                                    <i class="fas fa-list-alt mr-1"></i>
                                    @if ($step5Done)
                                        Lengkap ({{ $totalQtyReceived }}/{{ $totalQtyOrdered }}) <i class="fas fa-chevron-down ml-1"></i>
                                    @elseif ($step5Partial)
                                        Histori ({{ $totalQtyReceived }}/{{ $totalQtyOrdered }}) <i class="fas fa-chevron-down ml-1"></i>
                                    @else
                                        Rincian (0/{{ $totalQtyOrdered }}) <i class="fas fa-chevron-down ml-1"></i>
                                    @endif
                                </span>

                                <!-- Dropdown Popover Card for Receipt History -->
                                <div class="receipt-popover-card shadow-lg">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <strong class="text-dark" style="font-size: 0.82rem;">
                                            <i class="fas fa-clipboard-check text-primary mr-1"></i>Histori Penerimaan Barang
                                        </strong>
                                        <span class="badge badge-{{ $po->receipt_status_badge }} px-2">
                                            {{ $po->receipt_status_label }}
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.74rem;">
                                            <span>Total Fisik Diterima:</span>
                                            <span class="font-weight-bold text-dark">{{ $totalQtyReceived }} / {{ $totalQtyOrdered }} unit ({{ $receiptPercent }}%)</span>
                                        </div>
                                        <div class="progress mt-1" style="height: 6px; border-radius: 4px;">
                                            <div class="progress-bar bg-{{ $po->receipt_status == 'received' ? 'success' : ($po->receipt_status == 'partial' ? 'info' : 'secondary') }}"
                                                style="width: {{ $receiptPercent }}%;"></div>
                                        </div>
                                    </div>

                                    @if ($po->received_by || $po->received_at)
                                        <div class="bg-light p-2 rounded mb-2 border" style="font-size: 0.73rem;">
                                            @if ($po->received_by)
                                                <div><i class="fas fa-user-check text-muted mr-1"></i>Penerima: <strong>{{ $po->receiver->name ?? '-' }}</strong></div>
                                            @endif
                                            @if ($po->received_at)
                                                <div><i class="far fa-clock text-muted mr-1"></i>Penerimaan Terakhir: <strong>{{ $po->received_at->format('d M Y H:i') }}</strong></div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="receipt-items-list" style="max-height: 190px; overflow-y: auto;">
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.74rem;">
                                            <thead>
                                                <tr class="text-muted border-bottom" style="font-size: 0.7rem;">
                                                    <th>Produk</th>
                                                    <th class="text-center">Qty Diterima</th>
                                                    <th class="text-right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($po->details as $detail)
                                                    <tr class="border-bottom">
                                                        <td>
                                                            <strong>{{ $detail->product->name ?? '-' }}</strong>
                                                            @if ($detail->received_at)
                                                                <br><small class="text-muted"><i class="far fa-clock"></i> {{ $detail->received_at->format('d/m H:i') }}</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="font-weight-bold {{ ($detail->received_quantity ?? 0) > 0 ? 'text-primary' : 'text-muted' }}">
                                                                {{ $detail->received_quantity ?? 0 }}
                                                            </span> / {{ $detail->quantity }}
                                                        </td>
                                                        <td class="text-right align-middle">
                                                            <span class="badge badge-{{ $detail->receipt_status_badge }}">
                                                                {{ $detail->receipt_status_label }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: PO Selesai -->
                    <div class="roadmap-step {{ $step6Class }}">
                        <div class="roadmap-node">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <div class="roadmap-content">
                            <span class="roadmap-step-num">Langkah 6</span>
                            <h6 class="roadmap-title">PO Selesai</h6>
                            @if ($step6Done)
                                <span class="badge badge-success roadmap-badge">
                                    <i class="fas fa-check-double mr-1"></i>Tuntas Penuh
                                </span>
                            @else
                                <span class="badge badge-secondary roadmap-badge">Dalam Proses</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Detail Content Row -->
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
                            <i class="{{ $po->status_icon }} mr-1"></i>{{ $po->status_label }}
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ml-2 font-weight-bold shadow-sm" data-toggle="modal" data-target="#revisionHistoryModal" style="border-radius: 6px;" title="Lihat riwayat revisi dokumen">
                            <i class="fas fa-history mr-1 text-warning"></i> Riwayat Revisi 
                            @if ($po->revision_count > 0)
                                <span class="badge badge-warning ml-1">{{ $po->revision_count }}</span>
                            @else
                                <span class="badge badge-light border ml-1">0</span>
                            @endif
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($po->revision_count > 0)
                        <div class="alert alert-warning border mb-3 p-3" style="border-radius: 8px;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-start mb-2 mb-md-0">
                                    <i class="fas fa-history fa-2x mr-3 text-warning mt-1"></i>
                                    <div>
                                        <strong class="text-dark d-block mb-1">
                                            Dokumen Telah Direvisi (Revisi ke-{{ $po->revision_count }})
                                        </strong>
                                        @if ($po->revision_reason)
                                            <div class="text-dark" style="font-size: 0.9rem;">
                                                &ldquo;{{ $po->revision_reason }}&rdquo;
                                            </div>
                                        @endif
                                        <small class="text-muted mt-1 d-block">
                                            Revisi terakhir oleh <strong>{{ $po->revisedBy->name ?? 'User' }}</strong> pada {{ $po->revised_at ? $po->revised_at->format('d/m/Y H:i') : '-' }} WIB
                                        </small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-warning shadow-sm font-weight-bold px-3" data-toggle="modal" data-target="#revisionHistoryModal">
                                    <i class="fas fa-list-ul mr-1"></i> Lihat Riwayat Revisi ({{ $po->revision_count }})
                                </button>
                            </div>
                        </div>
                    @endif
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
                                @if ($po->purchaseRequest)
                                <tr>
                                    <th>Permintaan Pembelian</th>
                                    <td>: 
                                        <a href="{{ route('purchase-requests.show', $po->purchaseRequest->id) }}" class="font-weight-bold text-primary">
                                            <i class="fas fa-file-invoice mr-1"></i>{{ $po->purchaseRequest->pr_number }}
                                        </a>
                                        <span class="text-muted ml-1">({{ Str::limit($po->purchaseRequest->title, 25) }})</span>
                                        @if ($po->purchaseRequest->attachment)
                                            <a href="{{ route('purchase-requests.download', $po->purchaseRequest->id) }}" target="_blank" class="badge badge-info ml-1" title="Lihat Lampiran">
                                                <i class="fas fa-paperclip mr-1"></i>Lampiran
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="140">Dibuat Oleh</th>
                                    <td>: {{ $po->creator->name ?? '-' }}
                                        @if ($po->creator_signature)
                                            <div class="mt-1">
                                                @if (\Illuminate\Support\Str::startsWith($po->creator_signature, 'data:image'))
                                                    <img src="{{ $po->creator_signature }}" alt="TTD Pembuat" style="max-height: 45px; max-width: 130px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; background: #fff;" class="d-block">
                                                @else
                                                    <a href="{{ asset('storage/' . $po->creator_signature) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $po->creator_signature) }}" alt="TTD Pembuat" style="max-height: 45px; max-width: 130px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; background: #fff;" class="d-block">
                                                    </a>
                                                @endif
                                                <small class="d-block text-primary font-weight-bold" style="font-size: 0.68rem; margin-top: 2px;">
                                                    Ditandatangani Pada: {{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }} WIB
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status PO</th>
                                    <td>: <span class="badge badge-{{ $po->status_badge }}"><i class="{{ $po->status_icon }} mr-1"></i>{{ $po->status_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Bayar</th>
                                    <td>: <span
                                            class="badge badge-{{ $po->payment_status_badge ?? 'secondary' }}"><i class="{{ $po->payment_status_icon }} mr-1"></i>{{ $po->payment_status_label ?? 'Belum Lunas' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Barang</th>
                                    <td>: <span
                                            class="badge badge-{{ $po->receipt_status_badge ?? 'secondary' }}"><i class="{{ $po->receipt_status_icon }} mr-1"></i>{{ $po->receipt_status_label ?? 'Belum Diterima' }}</span>
                                    </td>
                                </tr>
                                @if ($po->is_pph)
                                <tr>
                                    <th>Review Pajak (PPh)</th>
                                    <td>: <span class="badge badge-{{ $po->tax_review_badge }}">{{ $po->tax_review_label }}</span>
                                        @if($po->taxReviewer)
                                            <small class="text-muted">({{ $po->taxReviewer->name }})</small>
                                        @endif
                                        <a href="{{ route('tax-reviews.index', ['search' => $po->po_number]) }}" class="btn btn-xs btn-outline-primary ml-1" title="Buka Review Pajak">
                                            <i class="fas fa-balance-scale"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                @if ($po->approved_by || $po->approver_signature)
                                    <tr>
                                        <th>{{ $po->status == 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh</th>
                                        <td>: {{ $po->approver->name ?? ($po->approver_email ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl Approval</th>
                                        <td>: {{ $po->approved_at ? $po->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @if ($po->approver_signature)
                                        <tr>
                                            <th>Bukti / TTD Approval</th>
                                            <td>:
                                                @if (\Illuminate\Support\Str::endsWith(strtolower($po->approver_signature), '.pdf'))
                                                    <a href="{{ asset('storage/' . $po->approver_signature) }}" target="_blank" class="btn btn-xs btn-outline-danger font-weight-bold">
                                                        <i class="fas fa-file-pdf mr-1"></i> Buka Dokumen PDF
                                                    </a>
                                                @elseif (\Illuminate\Support\Str::startsWith($po->approver_signature, 'data:image'))
                                                    <img src="{{ $po->approver_signature }}" alt="TTD" style="max-height: 45px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; background: #fff;" class="d-block">
                                                @else
                                                    <a href="{{ asset('storage/' . $po->approver_signature) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $po->approver_signature) }}" alt="TTD" style="max-height: 45px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; background: #fff;" class="d-block">
                                                    </a>
                                                @endif
                                                <small class="d-block text-primary font-weight-bold" style="font-size: 0.68rem; margin-top: 2px;">
                                                    Ditandatangani Pada: {{ $po->approved_at ? $po->approved_at->format('d/m/Y H:i') : '-' }} WIB
                                                </small>
                                            </td>
                                        </tr>
                                    @endif
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
                @php
                    $hasItemPph = $po->details->where('is_pph', true)->count() > 0;
                    $tableColspan = ($po->status == 'approved' ? 6 : 5) + ($hasItemPph ? 1 : 0);
                @endphp
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
                                    @if ($hasItemPph)
                                        <th width="130" class="text-right">PPh</th>
                                    @endif
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
                                        @if ($hasItemPph)
                                            <td class="text-right">
                                                @if ($detail->is_pph && $detail->pph_amount > 0)
                                                    <span class="badge badge-warning">{{ $detail->pph_label }}</span>
                                                    <br><small class="text-danger font-weight-bold">-Rp {{ number_format($detail->pph_amount, 0, ',', '.') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-right">{{ $detail->formatted_subtotal }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="{{ $tableColspan }}" class="text-right text-muted font-weight-normal border-top">Subtotal:</th>
                                    <th class="text-right font-weight-normal border-top">Rp {{ number_format($po->subtotal ?? $po->total_amount, 0, ',', '.') }}</th>
                                </tr>
                                @if ($po->discount_amount > 0)
                                    <tr>
                                        <th colspan="{{ $tableColspan }}" class="text-right text-danger font-weight-normal">Diskon:</th>
                                        <th class="text-right text-danger font-weight-normal">- Rp {{ number_format($po->discount_amount, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                @if ($po->ppn_amount > 0)
                                    <tr>
                                        <th colspan="{{ $tableColspan }}" class="text-right text-muted font-weight-normal">PPN ({{ (float)$po->ppn_percentage }}%):</th>
                                        <th class="text-right font-weight-normal">Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                @if ($po->pph_amount > 0)
                                    <tr>
                                        <th colspan="{{ $tableColspan }}" class="text-right text-danger font-weight-normal">PPh:</th>
                                        <th class="text-right text-danger font-weight-normal">- Rp {{ number_format($po->pph_amount, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                                <tr class="bg-light">
                                    <th colspan="{{ $tableColspan }}" class="text-right font-weight-bold">Total PO:</th>
                                    <th class="text-right text-primary font-weight-bold" style="font-size: 1.05rem;">{{ $po->formatted_total }}</th>
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
                                        <th class="text-center">Bukti</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($po->payments as $payment)
                                        <tr>
                                            <td>
                                                <div>{{ $payment->description }}</div>
                                                @if ($payment->note)
                                                    <small class="text-muted"><i class="fas fa-sticky-note mr-1"></i>{{ $payment->note }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $payment->percentage }}%</td>
                                            <td class="text-right">{{ $payment->formatted_amount }}</td>
                                            <td class="text-right">{{ $payment->formatted_paid_amount }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-{{ $payment->status_badge }}">{{ $payment->status_label }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($payment->proof_file)
                                                    @php
                                                        $ext = strtolower(pathinfo($payment->proof_file, PATHINFO_EXTENSION));
                                                        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
                                                    @endphp
                                                    <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank"
                                                        class="btn btn-xs btn-outline-info" title="Lihat Bukti Pembayaran">
                                                        <i class="fas {{ $isImage ? 'fa-image' : 'fa-file-pdf' }} mr-1"></i>
                                                        Bukti
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                    @if ($po->canEdit() && Auth::user()->can('edit purchase-orders'))
                        <a href="{{ route('purchase-orders.edit', $po->id) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit mr-2"></i> Edit PO
                        </a>
                    @endif

                    @if ($po->canRevise())
                        <button type="button" class="btn btn-warning btn-block font-weight-bold shadow-sm mt-2" data-toggle="modal" data-target="#revisionModal">
                            <i class="fas fa-undo-alt mr-2"></i> Revisi PO
                        </button>
                    @endif

                    @if ($po->status === \App\Models\PurchaseOrder::STATUS_DRAFT && $po->details->count() > 0)
                        @if ($po->is_pph)
                            @if ($po->isTaxReviewed())
                                <div class="alert alert-success mt-2 mb-2 p-3 shadow-sm border-0" style="background-color: #28a745 !important; color: #ffffff !important; border-radius: 6px;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-check-circle fa-2x mr-2 mt-1 text-white"></i>
                                        <div>
                                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.95rem;">
                                                Review Tim Tax Selesai
                                            </div>
                                            <div class="text-white" style="font-size: 0.85rem; line-height: 1.45; opacity: 0.95;">
                                                Diverifikasi oleh <strong>{{ $po->taxReviewer?->name ?? 'Tim Tax' }}</strong> ({{ $po->tax_reviewed_at?->format('d/m/Y H:i') }}). Silakan ajukan approval atasan.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @can('submit purchase-orders')
                                <button type="button" class="btn btn-info btn-block mt-2 font-weight-bold shadow-sm" data-toggle="modal" data-target="#submitModal">
                                    <i class="fas fa-paper-plane mr-2"></i> Submit untuk Approval
                                </button>
                                @endcan
                            @else
                                <div class="alert alert-warning mt-2 mb-2 p-2 border-0 shadow-sm" style="border-radius: 6px; font-size: 0.83rem; background-color: #fff3cd; color: #856404;">
                                    @if ($po->tax_review_requested_at)
                                        <i class="fas fa-clock mr-1 text-warning"></i> Permohonan review telah dikirim ke <strong>{{ $po->tax_reviewer_email }}</strong> ({{ $po->tax_review_requested_at->format('d/m/Y H:i') }}). Menunggu verifikasi tim tax.
                                    @else
                                        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> <strong>Review Pajak Diperlukan:</strong> PO mengenakan PPh dan harus direview Tim Tax sebelum diajukan approval atasan.
                                    @endif
                                </div>

                                @if (Auth::user()->can('submit purchase-orders') || Auth::user()->can('edit purchase-orders'))
                                <button type="button" class="btn btn-warning btn-block font-weight-bold shadow-sm" data-toggle="modal" data-target="#taxReviewEmailModal">
                                    <i class="fas fa-balance-scale mr-2"></i> {{ $po->tax_review_requested_at ? 'Kirim Ulang Email ke Tim Tax' : 'Ajukan Review ke Tim Tax' }}
                                </button>

                                <button type="button" class="btn btn-secondary btn-block mt-2 font-weight-bold" disabled title="Review tim tax diperlukan terlebih dahulu sebelum dapat diajukan ke approval atasan">
                                    <i class="fas fa-lock mr-2"></i> Submit untuk Approval (Terkunci)
                                </button>
                                @endif
                            @endif
                        @else
                            @can('submit purchase-orders')
                            <button type="button" class="btn btn-info btn-block mt-2 font-weight-bold shadow-sm" data-toggle="modal" data-target="#submitModal">
                                <i class="fas fa-paper-plane mr-2"></i> Submit untuk Approval
                            </button>
                            @endcan
                        @endif
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

                    <button type="button" class="btn btn-outline-secondary btn-block font-weight-bold shadow-sm mt-2" data-toggle="modal" data-target="#revisionHistoryModal">
                        <i class="fas fa-history mr-2 text-warning"></i> Riwayat Revisi {{ $po->revision_count > 0 ? "({$po->revision_count})" : '' }}
                    </button>

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
                            <td>Subtotal</td>
                            <td class="text-right">Rp {{ number_format($po->subtotal ?? $po->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if ($po->discount_amount > 0)
                            <tr>
                                <td>Diskon</td>
                                <td class="text-right text-danger">- Rp {{ number_format($po->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if ($po->ppn_amount > 0)
                            <tr>
                                <td>PPN ({{ (float)$po->ppn_percentage }}%)</td>
                                <td class="text-right">Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if ($po->pph_amount > 0)
                            <tr>
                                <td>PPh</td>
                                <td class="text-right text-danger">- Rp {{ number_format($po->pph_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong>Total PO</strong></td>
                            <td class="text-right"><strong>{{ $po->formatted_total }}</strong></td>
                        </tr>
                        <tr>
                            <td>Terbayar</td>
                            <td class="text-right text-success">Rp {{ number_format($po->total_paid ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Sisa</strong></td>
                            <td class="text-right text-danger"><strong>Rp {{ number_format($po->remaining_amount ?? $po->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Tax Review Request Modal -->
    <div class="modal fade" id="taxReviewEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <form action="{{ route('purchase-orders.send-tax-review', $po->id) }}" method="POST" id="form-tax-review-email">
                    @csrf
                    <div class="modal-header text-white px-4 py-3" style="background-color: #0f172a; border-bottom: 3px solid #f59e0b;">
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                <i class="fas fa-envelope-open-text mr-2 text-warning"></i>Kirim Review ke Pajak via Email
                            </h5>
                            <small class="text-white-50">PO No: <strong>{{ $po->po_number }}</strong> &bull; Total: <strong>{{ $po->formatted_total }}</strong></small>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.85;">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="alert alert-light border py-2 px-3 mb-3" style="border-radius: 8px; font-size: 0.85rem; background-color: #f8fafc;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-primary fa-lg mr-2"></i>
                                <div>
                                    Email akan dikirimkan ke Tim Pajak berisi ringkasan tarif PPh & PPN, serta lampiran <strong>PDF Purchase Order</strong> otomatis.
                                </div>
                            </div>
                        </div>

                        <!-- Select Tax Reviewer -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark d-flex justify-content-between align-items-center mb-1">
                                <span><i class="fas fa-user-shield text-primary mr-1"></i> Pilih Petugas Pajak / Reviewer <span class="text-danger">*</span></span>
                                <small class="text-muted font-weight-normal">Dari akun Tim Tax / Admin</small>
                            </label>
                            <select class="form-control form-control-lg border-primary-subtle" id="select_tax_reviewer" style="font-size: 0.95rem; border-radius: 8px;">
                                <option value="" {{ empty($po->tax_reviewer_email) ? 'selected' : '' }} disabled>-- Pilih Petugas Pajak Penerima --</option>
                                @if(isset($taxReviewers) && $taxReviewers->count() > 0)
                                    @foreach($taxReviewers as $tr)
                                        <option value="{{ $tr->email }}" data-name="{{ $tr->name }}" {{ ($po->tax_reviewer_email === $tr->email) ? 'selected' : '' }}>
                                            {{ $tr->name }} &mdash; {{ $tr->email }}
                                        </option>
                                    @endforeach
                                @endif
                                <option value="__manual__">Tulis Alamat Email Manual Lainnya...</option>
                            </select>
                            <input type="hidden" name="reviewer_email" id="tax_reviewer_email_input" value="{{ $po->tax_reviewer_email ?? '' }}">
                        </div>

                        <!-- Manual Email Input -->
                        <div class="form-group mb-3" id="manual_tax_email_wrapper" style="display: none;">
                            <label class="small font-weight-bold text-dark mb-1">
                                <i class="fas fa-envelope text-primary mr-1"></i> Masukkan Alamat Email Tim Pajak: <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="manual_tax_email_field" class="form-control" placeholder="contoh: tax@panganlestari.co.id">
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="alert alert-warning py-2 px-3 mb-3 border-warning" style="border-radius: 8px;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm_tax_reviewer_email_verified">
                                <label class="custom-control-label font-weight-bold text-dark mb-0" for="confirm_tax_reviewer_email_verified" style="font-size: 0.86rem; cursor: pointer;">
                                    Saya telah memeriksa & mengonfirmasi bahwa alamat email tim tax tujuan sudah benar.
                                </label>
                            </div>
                        </div>

                        <!-- Optional Notes -->
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-dark mb-1" for="tax_review_notes">
                                <i class="fas fa-comment-alt text-secondary mr-1"></i> Catatan untuk Tim Pajak (Opsional)
                            </label>
                            <textarea name="notes" id="tax_review_notes" class="form-control" rows="3" placeholder="Misal: Mohon pengecekan tarif PPh 23 untuk jasa vendor, NPWP telah dilampirkan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btn-submit-tax-review-email">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Submit Signature & Approver Email Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <form action="{{ route('purchase-orders.submit', $po->id) }}" method="POST" id="form-submit">
                    @csrf
                    <input type="hidden" name="creator_signature" id="creator_signature_input">
                    <div class="modal-header text-white px-4 py-3" style="background-color: #1e293b;">
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                <i class="fas fa-paper-plane mr-2 text-warning"></i>Submit Purchase Order & Permohonan Approval
                            </h5>
                            <small class="text-white-50">PO No: <strong>{{ $po->po_number }}</strong> &bull; Total: <strong>{{ $po->formatted_total }}</strong></small>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.85;">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <!-- Step 1: Select Approver -->
                        <div class="form-group mb-3">
                            @if(isset($matchingRule) && $matchingRule)
                                <div class="alert alert-info py-2 px-3 mb-3 border-info" style="border-radius: 8px; font-size: 0.88rem; background-color: #f0f7ff;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-sitemap text-primary mr-2 mt-1" style="font-size: 1.1rem;"></i>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold text-dark mb-1">
                                                Matriks Aturan Approval: <span class="badge badge-primary">{{ $matchingRule->name }}</span>
                                            </div>
                                            <div class="text-secondary small mb-1">
                                                <i class="fas fa-info-circle mr-1"></i> {{ $recommendedReason }}
                                            </div>
                                            @if(isset($recommendedApprover) && $recommendedApprover)
                                                <div class="text-success small font-weight-bold">
                                                    <i class="fas fa-check-circle mr-1"></i> Rekomendasi Terpilih: {{ $recommendedApprover->name }} &lt;{{ $recommendedApprover->email }}&gt;
                                                </div>
                                            @elseif(isset($requiredApproverLevel) && in_array(strtoupper($requiredApproverLevel), ['L3', 'L4', 'L5']))
                                                <div class="text-warning small font-weight-bold">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada akun user yang ditugaskan sebagai {{ \App\Models\ApprovalRule::formatLevelLabel($requiredApproverLevel) }} untuk cabang ini di Master Departemen. Anda dapat memilih dari daftar atasan di bawah.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <label class="font-weight-bold text-dark d-flex justify-content-between align-items-center mb-1">
                                <span><i class="fas fa-user-check text-primary mr-1"></i> Pilih Manager / Atasan Approver <span class="text-danger">*</span></span>
                                <small class="text-muted font-weight-normal">Dari daftar akun atasan di sistem</small>
                            </label>
                            <select class="form-control form-control-lg border-primary-subtle" id="select_approver_user" style="font-size: 0.95rem; border-radius: 8px;">
                                <option value="" {{ !isset($recommendedApprover) || !$recommendedApprover ? 'selected' : '' }} disabled>-- Pilih Atasan / Manager Penerima --</option>
                                @if(isset($approvers) && $approvers->count() > 0)
                                    @foreach($approvers as $approver)
                                        @php
                                            $isRecommended = (isset($recommendedApprover) && $recommendedApprover && $approver->id === $recommendedApprover->id);
                                        @endphp
                                        <option value="{{ $approver->email }}" data-name="{{ $approver->name }}" {{ $isRecommended ? 'selected' : '' }}>
                                            {{ $approver->name }} &mdash; {{ $approver->email }} {{ $isRecommended ? '★ [REKOMENDASI SISTEM]' : '' }}
                                        </option>
                                    @endforeach
                                @endif
                                <option value="__manual__">Tulis Alamat Email Manual Lainnya...</option>
                            </select>
                            <input type="hidden" name="approver_email" id="approver_email_input" value="{{ (isset($recommendedApprover) && $recommendedApprover) ? $recommendedApprover->email : '' }}">
                        </div>

                        <!-- Manual Email Input (hanya muncul jika opsi Tulis Manual dipilih) -->
                        <div class="form-group mb-3" id="manual_email_wrapper" style="display: none;">
                            <label class="small font-weight-bold text-dark mb-1">
                                <i class="fas fa-envelope text-primary mr-1"></i> Masukkan Alamat Email Tujuan: <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="manual_email_field" class="form-control" placeholder="contoh: manager.purchasing@panganlestari.co.id">
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="alert alert-warning py-2 px-3 mb-3 border-warning" style="border-radius: 8px;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm_email_verified">
                                <label class="custom-control-label font-weight-bold text-dark mb-0" for="confirm_email_verified" style="font-size: 0.86rem; cursor: pointer;">
                                    Saya telah memeriksa & mengonfirmasi bahwa alamat email tujuan sudah benar untuk menerima tautan persetujuan PO.
                                </label>
                            </div>
                        </div>

                        <!-- Step 3: Creator Signature -->
                        <div class="card border shadow-none mb-0" style="border-radius: 10px;">
                            <div class="card-header bg-light py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                        <i class="fas fa-pen-nib text-secondary mr-1"></i> Tanda Tangan Pembuat (Opsional)
                                    </span>
                                    <!-- Tabs -->
                                    <ul class="nav nav-pills" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active py-1 px-3" data-toggle="tab" href="#creator-draw-tab" style="font-size: 0.78rem;">
                                                <i class="fas fa-pen mr-1"></i> Gambar
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link py-1 px-3" data-toggle="tab" href="#creator-upload-tab" style="font-size: 0.78rem;">
                                                <i class="fas fa-upload mr-1"></i> Upload
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="tab-content">
                                    <!-- Draw Tab -->
                                    <div class="tab-pane fade show active" id="creator-draw-tab">
                                        <div class="signature-pad-container" style="border: 1px dashed #cbd5e1; border-radius: 6px; background: #fafafa;">
                                            <canvas id="creator-signature-pad" style="width: 100%; height: 160px; touch-action: none; cursor: crosshair;"></canvas>
                                        </div>
                                        <div class="mt-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-primary font-weight-bold d-block" style="font-size: 0.74rem;">
                                                    <i class="fas fa-pen-nib mr-1"></i> Tinta Tanda Tangan: Biru
                                                </small>
                                                <small class="text-muted" style="font-size: 0.72rem;">
                                                    Ditandatangani Pada: {{ now()->format('d/m/Y H:i') }} WIB
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" id="clear-creator-signature">
                                                <i class="fas fa-eraser mr-1"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Upload Tab -->
                                    <div class="tab-pane fade" id="creator-upload-tab">
                                        <div class="form-group mb-2">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="creator-signature-file" accept="image/*">
                                                <label class="custom-file-label" for="creator-signature-file">Pilih file tanda tangan...</label>
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG. Max 2MB</small>
                                        </div>
                                        <div id="creator-upload-preview" class="text-center mt-2" style="display: none;">
                                            <img id="creator-upload-img" src="" alt="Preview" style="max-height: 120px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                            <div class="mt-1">
                                                <button type="button" class="btn btn-xs btn-danger" id="clear-creator-upload">
                                                    <i class="fas fa-times mr-1"></i> Hapus File
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btn-submit-with-signature" disabled>
                            <i class="fas fa-paper-plane mr-2"></i> Konfirmasi & Submit PO
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
                                    <canvas id="approver-signature-pad" style="width: 100%; height: 200px; touch-action: none; cursor: crosshair;"></canvas>
                                </div>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-primary font-weight-bold d-block" style="font-size: 0.74rem;">
                                            <i class="fas fa-pen-nib mr-1"></i> Tinta Tanda Tangan: Biru
                                        </small>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            Ditandatangani Pada: {{ now()->format('d/m/Y H:i') }} WIB
                                        </small>
                                    </div>
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
                            <form id="form-record-payment" method="POST" enctype="multipart/form-data">
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
                                        <textarea name="note" id="payment-note" class="form-control" rows="2" placeholder="Nomor bukti transfer, dll"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Upload Bukti Pembayaran <small class="text-muted">(PDF, PNG, JPG - Maks 5MB)</small></label>
                                        <div class="custom-file">
                                            <input type="file" name="proof_file" id="proof_file" class="custom-file-input" accept=".pdf,.png,.jpg,.jpeg">
                                            <label class="custom-file-label" for="proof_file">Pilih file bukti...</label>
                                        </div>
                                        <div id="proof-preview-container" class="mt-2 text-center" style="display: none;">
                                            <img id="proof-img-preview" src="" alt="Preview Bukti" style="max-height: 120px; border-radius: 4px; border: 1px solid #ced4da;" class="img-fluid mb-1">
                                            <div id="proof-pdf-preview" style="display: none;" class="alert alert-light border py-2 mb-1">
                                                <i class="fas fa-file-pdf text-danger fa-2x mr-2 align-middle"></i>
                                                <span id="proof-file-name" class="font-weight-bold"></span>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-xs btn-outline-danger" id="btn-clear-proof">
                                                    <i class="fas fa-times mr-1"></i> Hapus File
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted mt-1">Opsional: Lampirkan bukti transfer atau struk pembayaran.</small>
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

                <!-- Modal Revisi PO -->
                <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog" aria-labelledby="revisionModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-warning text-dark py-3">
                                <h5 class="modal-title font-weight-bold" id="revisionModalTitle">
                                    <i class="fas fa-undo-alt mr-2"></i>Tarik PO untuk Revisi
                                </h5>
                                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('purchase-orders.revise', $po->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="alert alert-light border mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-invoice text-primary fa-2x mr-3"></i>
                                            <div>
                                                <span class="d-block text-muted small">Nomor Dokumen:</span>
                                                <strong class="text-dark" style="font-size: 15px;">{{ $po->po_number }}</strong>
                                                <span class="badge badge-secondary ml-1">Revisi ke-{{ ($po->revision_count ?? 0) + 1 }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="callout callout-warning bg-light p-3 mb-3 small" style="border-left-color: #ffc107;">
                                        <strong>Perhatian:</strong>
                                        <ul class="pl-3 mb-0 mt-1 text-muted">
                                            <li>Status PO akan dikembalikan ke <strong>Draft</strong>.</li>
                                            @if ($po->status === 'approved')
                                                <li>Status persetujuan sebelumnya akan <strong>direset</strong> dan memerlukan approval ulang setelah revisi selesai.</li>
                                            @else
                                                <li>Tautan permohonan persetujuan (approval) email sebelumnya akan <strong>dibatalkan otomatis</strong>.</li>
                                            @endif
                                            <li>Setelah mengklik tombol di bawah, Anda akan langsung diarahkan ke halaman formulir edit PO.</li>
                                        </ul>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="showRevReasonInput" class="font-weight-bold text-dark">
                            Alasan Revisi <span class="text-danger">*</span>
                        </label>
                                        <textarea name="reason" id="showRevReasonInput" class="form-control" rows="3" 
                                            placeholder="Contoh: Perubahan kuantitas item kabel dari 10 ke 15 pcs sesuai memo vendor..." required></textarea>
                                        <small class="form-text text-muted">Catatan ini akan tersimpan dalam riwayat revisi dokumen PO.</small>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2">
                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold px-4">
                                        <i class="fas fa-arrow-right mr-1"></i> Proses & Buka Form Edit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Riwayat Revisi PO (Timeline Popup) -->
                <div class="modal fade" id="revisionHistoryModal" tabindex="-1" role="dialog" aria-labelledby="revisionHistoryModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                            <div class="modal-header text-white px-4 py-3" style="background-color: #1e293b;">
                                <div>
                                    <h5 class="modal-title font-weight-bold mb-0" id="revisionHistoryModalTitle">
                                        <i class="fas fa-history text-warning mr-2"></i>Riwayat Revisi Dokumen PO
                                    </h5>
                                    <small class="text-white-50">
                                        No. PO: <strong>{{ $po->po_number }}</strong> &bull; Total Tercatat: <strong>{{ $po->revision_count }} kali revisi</strong>
                                    </small>
                                </div>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4" style="background-color: #f8fafc;">
                                @php
                                    $revList = $po->revisions ?? collect();
                                @endphp

                                @if($revList->count() > 0)
                                    <div class="timeline mb-0">
                                        @foreach($revList as $rev)
                                            <div class="time-label">
                                                <span class="bg-warning text-dark px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 6px;">
                                                    <i class="fas fa-calendar-alt mr-1"></i> {{ $rev->created_at->format('d F Y') }}
                                                </span>
                                            </div>
                                            <div>
                                                <i class="fas fa-undo-alt bg-primary text-white"></i>
                                                <div class="timeline-item shadow-sm border" style="border-radius: 8px;">
                                                    <span class="time text-muted small">
                                                        <i class="fas fa-clock mr-1"></i>{{ $rev->created_at->format('H:i') }} WIB
                                                    </span>
                                                    <h3 class="timeline-header font-weight-bold d-flex justify-content-between align-items-center flex-wrap" style="font-size: 0.95rem;">
                                                        <span>
                                                            <span class="badge badge-primary px-2 py-1 mr-1">Revisi ke-{{ $rev->revision_number }}</span>
                                                            Ditarik oleh <strong>{{ $rev->user->name ?? ($rev->revisedBy->name ?? 'User') }}</strong>
                                                        </span>
                                                        @if($rev->previous_status)
                                                            <small class="badge badge-light border text-muted mt-1 mt-md-0">
                                                                Status sebelum ditarik: <strong>{{ $rev->previous_status_label }}</strong>
                                                            </small>
                                                        @endif
                                                    </h3>
                                                    <div class="timeline-body py-3">
                                                        <div class="p-3 rounded" style="background-color: #f1f5f9; border-left: 4px solid #ffc107;">
                                                            <div class="small font-weight-bold text-muted mb-1">
                                                                <i class="fas fa-comment-dots text-warning mr-1"></i> Alasan / Catatan Revisi:
                                                            </div>
                                                            <div class="text-dark font-italic" style="font-size: 0.92rem; line-height: 1.5;">
                                                                &ldquo;{{ $rev->reason }}&rdquo;
                                                            </div>
                                                        </div>
                                                        @if($rev->previous_total_amount)
                                                            <div class="mt-2 small text-muted">
                                                                <i class="fas fa-receipt mr-1 text-secondary"></i> Total nominal sebelum revisi: <strong>{{ $rev->formatted_previous_total }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div>
                                            <i class="fas fa-check-circle bg-success text-white" title="Dokumen Awal Dibuat"></i>
                                            <div class="timeline-item shadow-sm border bg-white" style="border-radius: 8px;">
                                                <span class="time text-muted small">
                                                    <i class="fas fa-clock mr-1"></i>{{ $po->created_at->format('H:i') }} WIB
                                                </span>
                                                <h3 class="timeline-header font-weight-bold" style="font-size: 0.9rem;">
                                                    <span class="badge badge-secondary px-2 py-1 mr-1">Versi Awal</span>
                                                    Purchase Order pertama kali dibuat oleh <strong>{{ $po->creator->name ?? 'User' }}</strong>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($po->revision_count > 0 && $po->revision_reason)
                                    <!-- Fallback single record -->
                                    <div class="timeline mb-0">
                                        <div class="time-label">
                                            <span class="bg-warning text-dark px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 6px;">
                                                <i class="fas fa-calendar-alt mr-1"></i> {{ $po->revised_at ? $po->revised_at->format('d F Y') : '-' }}
                                            </span>
                                        </div>
                                        <div>
                                            <i class="fas fa-undo-alt bg-primary text-white"></i>
                                            <div class="timeline-item shadow-sm border" style="border-radius: 8px;">
                                                <span class="time text-muted small">
                                                    <i class="fas fa-clock mr-1"></i>{{ $po->revised_at ? $po->revised_at->format('H:i') . ' WIB' : '' }}
                                                </span>
                                                <h3 class="timeline-header font-weight-bold" style="font-size: 0.95rem;">
                                                    <span class="badge badge-primary px-2 py-1 mr-1">Revisi ke-{{ $po->revision_count }}</span>
                                                    Ditarik oleh <strong>{{ $po->revisedBy->name ?? 'User' }}</strong>
                                                </h3>
                                                <div class="timeline-body py-3">
                                                    <div class="p-3 rounded" style="background-color: #f1f5f9; border-left: 4px solid #ffc107;">
                                                        <div class="small font-weight-bold text-muted mb-1">
                                                            <i class="fas fa-comment-dots text-warning mr-1"></i> Alasan / Catatan Revisi:
                                                        </div>
                                                        <div class="text-dark font-italic" style="font-size: 0.92rem; line-height: 1.5;">
                                                            &ldquo;{{ $po->revision_reason }}&rdquo;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                        <h6 class="font-weight-bold">Belum Ada Riwayat Revisi</h6>
                                        <p class="small mb-0">Dokumen PO ini belum pernah ditarik untuk proses revisi.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer bg-light px-4 py-2">
                                <button type="button" class="btn btn-secondary btn-sm px-4 font-weight-bold" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SignaturePad Library (Local asset with CDN fallback) -->
    <script src="{{ asset('js/signature_pad.umd.min.js') }}"></script>
    <script>
        if (typeof SignaturePad === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"><\/script>');
        }
    </script>
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
                            $('#payment-note').val('');
                            // Reset file input & preview
                            $('#proof_file').val('');
                            $('#proof_file').next('.custom-file-label').text('Pilih file bukti...');
                            $('#proof-preview-container').hide();
                            $('#proof-img-preview').hide().attr('src', '');
                            $('#proof-pdf-preview').hide();
                            $('#proof-file-name').text('');

                            $('#form-record-payment').attr('action', '{{ url("po-payments") }}/' + id + '/record');
                            $('#paymentModal').modal('show');
                        });

                        // File input change preview
                        $('#proof_file').on('change', function(e) {
                            var file = e.target.files[0];
                            if (!file) {
                                $('#proof_file').next('.custom-file-label').text('Pilih file bukti...');
                                $('#proof-preview-container').hide();
                                return;
                            }

                            $('#proof_file').next('.custom-file-label').text(file.name);
                            $('#proof-preview-container').show();

                            if (file.type && file.type.match('image.*')) {
                                var reader = new FileReader();
                                reader.onload = function(re) {
                                    $('#proof-img-preview').attr('src', re.target.result).show();
                                    $('#proof-pdf-preview').hide();
                                };
                                reader.readAsDataURL(file);
                            } else {
                                $('#proof-img-preview').hide().attr('src', '');
                                $('#proof-pdf-preview').show();
                                var sizeText = file.size ? ' (' + (file.size / 1024).toFixed(1) + ' KB)' : '';
                                $('#proof-file-name').text(file.name + sizeText);
                            }
                        });

                        // Clear proof file
                        $('#btn-clear-proof').on('click', function() {
                            $('#proof_file').val('');
                            $('#proof_file').next('.custom-file-label').text('Pilih file bukti...');
                            $('#proof-preview-container').hide();
                            $('#proof-img-preview').hide().attr('src', '');
                            $('#proof-pdf-preview').hide();
                            $('#proof-file-name').text('');
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

                        // SignaturePad initialization & Fallback Drawing Engine
                        var creatorCanvas = document.getElementById('creator-signature-pad');
                        var approverCanvas = document.getElementById('approver-signature-pad');
                        var creatorSignaturePad = null;
                        var approverSignaturePad = null;

                        function createFallbackSignaturePad(canvas, ratio) {
                            var ctx = canvas.getContext('2d');
                            var isDrawing = false;
                            var hasDrawn = false;

                            ctx.fillStyle = '#ffffff';
                            ctx.fillRect(0, 0, canvas.width / ratio, canvas.height / ratio);
                            ctx.strokeStyle = '#0038a8';
                            ctx.lineWidth = 2.5;
                            ctx.lineCap = 'round';
                            ctx.lineJoin = 'round';

                            function getPos(e) {
                                var rect = canvas.getBoundingClientRect();
                                var clientX = e.clientX;
                                var clientY = e.clientY;
                                if (e.touches && e.touches.length > 0) {
                                    clientX = e.touches[0].clientX;
                                    clientY = e.touches[0].clientY;
                                }
                                return {
                                    x: clientX - rect.left,
                                    y: clientY - rect.top
                                };
                            }

                            function startDraw(e) {
                                e.preventDefault();
                                isDrawing = true;
                                hasDrawn = true;
                                var pos = getPos(e);
                                ctx.beginPath();
                                ctx.moveTo(pos.x, pos.y);
                            }

                            function moveDraw(e) {
                                if (!isDrawing) return;
                                e.preventDefault();
                                var pos = getPos(e);
                                ctx.lineTo(pos.x, pos.y);
                                ctx.stroke();
                            }

                            function stopDraw(e) {
                                if (isDrawing) {
                                    isDrawing = false;
                                }
                            }

                            canvas.onmousedown = startDraw;
                            canvas.onmousemove = moveDraw;
                            window.addEventListener('mouseup', stopDraw);

                            canvas.ontouchstart = startDraw;
                            canvas.ontouchmove = moveDraw;
                            window.addEventListener('touchend', stopDraw);

                            return {
                                clear: function() {
                                    hasDrawn = false;
                                    ctx.fillStyle = '#ffffff';
                                    ctx.fillRect(0, 0, canvas.width / ratio, canvas.height / ratio);
                                },
                                isEmpty: function() {
                                    return !hasDrawn;
                                },
                                toDataURL: function(type) {
                                    return canvas.toDataURL(type || 'image/png');
                                }
                            };
                        }

                        function initPad(canvasId) {
                            var canvas = document.getElementById(canvasId);
                            if (!canvas) return null;

                            var container = canvas.parentElement;
                            var width = container ? (container.clientWidth || 550) : 550;
                            var height = parseInt(canvas.style.height) || 160;
                            var ratio = Math.max(window.devicePixelRatio || 1, 1);

                            canvas.width = width * ratio;
                            canvas.height = height * ratio;
                            canvas.style.width = width + 'px';
                            canvas.style.height = height + 'px';

                            var ctx = canvas.getContext('2d');
                            ctx.scale(ratio, ratio);

                            if (typeof SignaturePad !== 'undefined') {
                                try {
                                    var pad = new SignaturePad(canvas, {
                                        backgroundColor: 'rgb(255, 255, 255)',
                                        penColor: 'rgb(0, 56, 168)',
                                        minWidth: 1.5,
                                        maxWidth: 3.5
                                    });
                                    pad.clear();
                                    return pad;
                                } catch (err) {
                                    console.warn('SignaturePad initialization failed, using native canvas fallback', err);
                                    return createFallbackSignaturePad(canvas, ratio);
                                }
                            } else {
                                return createFallbackSignaturePad(canvas, ratio);
                            }
                        }

                        // Initialize creator signature pad when submitModal opens or draw tab is activated
                        $('#submitModal').on('shown.bs.modal', function() {
                            setTimeout(function() {
                                creatorSignaturePad = initPad('creator-signature-pad');
                            }, 50);
                        });

                        $('a[href="#creator-draw-tab"]').on('shown.bs.tab', function() {
                            if (!creatorSignaturePad || creatorSignaturePad.isEmpty()) {
                                setTimeout(function() {
                                    creatorSignaturePad = initPad('creator-signature-pad');
                                }, 50);
                            }
                        });

                        // Initialize approver signature pad when approveModal opens or draw tab is activated
                        $('#approveModal').on('shown.bs.modal', function() {
                            setTimeout(function() {
                                approverSignaturePad = initPad('approver-signature-pad');
                            }, 50);
                        });

                        $('a[href="#approver-draw-tab"]').on('shown.bs.tab', function() {
                            if (!approverSignaturePad || approverSignaturePad.isEmpty()) {
                                setTimeout(function() {
                                    approverSignaturePad = initPad('approver-signature-pad');
                                }, 50);
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

                        // --- APPROVER EMAIL & SELECTION LOGIC ---
                        function isValidEmail(email) {
                            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            return re.test(String(email).toLowerCase());
                        }

                        function updateSubmitButtonState() {
                            var email = ($('#approver_email_input').val() || '').trim();
                            var isConfirmed = $('#confirm_email_verified').length ? $('#confirm_email_verified').is(':checked') : true;
                            var isValid = isValidEmail(email) && isConfirmed;

                            $('#btn-submit-with-signature').prop('disabled', !isValid);
                        }

                        // Initialize approver email if already selected by recommendation
                        var initialApproverVal = $('#select_approver_user').val();
                        if (initialApproverVal && initialApproverVal !== '__manual__') {
                            $('#approver_email_input').val(initialApproverVal);
                        }
                        updateSubmitButtonState();

                        // When manager is selected from dropdown
                        $('#select_approver_user').on('change', function() {
                            var val = $(this).val();

                            if (val === '__manual__') {
                                $('#manual_email_wrapper').slideDown(200);
                                $('#manual_email_field').val('').focus();
                                $('#approver_email_input').val('');
                            } else if (val) {
                                $('#manual_email_wrapper').slideUp(200);
                                $('#approver_email_input').val(val);
                            } else {
                                $('#manual_email_wrapper').slideUp(200);
                                $('#approver_email_input').val('');
                            }

                            // Reset checkbox so user confirms explicitly
                            $('#confirm_email_verified').prop('checked', false);
                            updateSubmitButtonState();
                        });

                        // When typing in manual email input
                        $('#manual_email_field').on('input', function() {
                            $('#approver_email_input').val($(this).val().trim());
                            updateSubmitButtonState();
                        });

                        // When confirmation checkbox is toggled
                        $('#confirm_email_verified').on('change', function() {
                            var email = ($('#approver_email_input').val() || '').trim();
                            if ($(this).is(':checked') && (!email || !isValidEmail(email))) {
                                $(this).prop('checked', false);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Email Belum Ditentukan',
                                    text: 'Silakan pilih atasan dari daftar atau isi alamat email tujuan terlebih dahulu.'
                                });
                                return;
                            }
                            updateSubmitButtonState();
                        });

                        // Submit form handler
                        $('#form-submit').on('submit', function(e) {
                            var email = ($('#approver_email_input').val() || '').trim();
                            if (!email || !isValidEmail(email)) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Alamat Email Diperlukan',
                                    text: 'Pastikan alamat email tujuan sudah diisi dan berformat valid.'
                                });
                                return false;
                            }

                            if (!$('#confirm_email_verified').is(':checked')) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Konfirmasi Diperlukan',
                                    text: 'Centang kotak konfirmasi alamat email sebelum mengirim permohonan persetujuan.'
                                });
                                return false;
                            }

                            // Capture signature if drawn or uploaded
                            var activeTab = $('#submitModal .tab-pane.active').attr('id');
                            if (activeTab === 'creator-draw-tab') {
                                if (creatorSignaturePad && !creatorSignaturePad.isEmpty()) {
                                    $('#creator_signature_input').val(creatorSignaturePad.toDataURL('image/png'));
                                }
                            } else if (activeTab === 'creator-upload-tab') {
                                if (creatorUploadData) {
                                    $('#creator_signature_input').val(creatorUploadData);
                                }
                            }

                            // Show loading state on button
                            $('#btn-submit-with-signature')
                                .prop('disabled', true)
                                .html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim PO...');
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

                        // --- TAX REVIEW EMAIL SELECTION LOGIC ---
                        function updateTaxReviewSubmitButton() {
                            var email = ($('#tax_reviewer_email_input').val() || '').trim();
                            var isConfirmed = $('#confirm_tax_reviewer_email_verified').length ? $('#confirm_tax_reviewer_email_verified').is(':checked') : true;
                            var isValid = isValidEmail(email) && isConfirmed;
                            $('#btn-submit-tax-review-email').prop('disabled', !isValid);
                        }

                        // Run on load in case a reviewer was already selected
                        updateTaxReviewSubmitButton();

                        $('#select_tax_reviewer').on('change', function() {
                            var val = $(this).val();
                            if (val === '__manual__') {
                                $('#manual_tax_email_wrapper').slideDown(200);
                                $('#manual_tax_email_field').val('').focus();
                                $('#tax_reviewer_email_input').val('');
                            } else if (val) {
                                $('#manual_tax_email_wrapper').slideUp(200);
                                $('#tax_reviewer_email_input').val(val);
                            } else {
                                $('#manual_tax_email_wrapper').slideUp(200);
                                $('#tax_reviewer_email_input').val('');
                            }
                            $('#confirm_tax_reviewer_email_verified').prop('checked', false);
                            updateTaxReviewSubmitButton();
                        });

                        $('#manual_tax_email_field').on('input', function() {
                            $('#tax_reviewer_email_input').val($(this).val().trim());
                            updateTaxReviewSubmitButton();
                        });

                        $('#confirm_tax_reviewer_email_verified').on('change', function() {
                            var email = ($('#tax_reviewer_email_input').val() || '').trim();
                            if ($(this).is(':checked') && (!email || !isValidEmail(email))) {
                                $(this).prop('checked', false);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Email Belum Ditentukan',
                                    text: 'Silakan pilih petugas pajak dari daftar atau ketik alamat email terlebih dahulu.'
                                });
                                return;
                            }
                            updateTaxReviewSubmitButton();
                        });

                        var taxReviewConfirmed = false;
                        $('#form-tax-review-email').on('submit', function(e) {
                            var email = ($('#tax_reviewer_email_input').val() || '').trim();
                            if (!email || !isValidEmail(email)) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Email Belum Ditentukan',
                                    text: 'Silakan pilih petugas pajak atau isi alamat email yang valid.'
                                });
                                return false;
                            }

                            if (!$('#confirm_tax_reviewer_email_verified').is(':checked')) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Konfirmasi Diperlukan',
                                    text: 'Centang kotak konfirmasi alamat email sebelum mengirim permohonan ke Tim Tax.'
                                });
                                return false;
                            }

                            if (!taxReviewConfirmed) {
                                e.preventDefault();
                                var selectedName = $('#select_tax_reviewer option:selected').data('name');
                                var targetInfo = selectedName ? selectedName + ' &bull; ' + email : email;

                                Swal.fire({
                                    title: 'Konfirmasi Kirim Review Tax',
                                    html: '<div class="text-left" style="font-size: 0.95rem;">' +
                                          '<p class="mb-2">Permohonan review pajak dan lampiran <strong>PDF PO</strong> akan dikirimkan ke:</p>' +
                                          '<div class="alert alert-info py-2 px-3 mb-2 font-weight-bold" style="border-radius: 8px;">' +
                                          '<i class="fas fa-envelope mr-2"></i> ' + targetInfo +
                                          '</div>' +
                                          '<p class="small text-muted mb-0"><i class="fas fa-info-circle mr-1 text-primary"></i> Pastikan alamat email di atas sudah benar agar permohonan tidak salah kirim.</p>' +
                                          '</div>',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#007bff',
                                    cancelButtonColor: '#6c757d',
                                    confirmButtonText: '<i class="fas fa-paper-plane mr-1"></i> Ya, Kirim Sekarang',
                                    cancelButtonText: 'Periksa Kembali'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        taxReviewConfirmed = true;
                                        $('#btn-submit-tax-review-email')
                                            .prop('disabled', true)
                                            .html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim Permohonan...');
                                        $('#form-tax-review-email').submit();
                                    }
                                });
                                return false;
                            }
                        });
                    });
                </script>
@endpush

@push('styles')
<style>
    /* ============================================================
       ROADMAP STEPPER STYLES
       ============================================================ */
    .roadmap-card {
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: visible !important;
    }

    .roadmap-card .card-body {
        overflow: visible !important;
    }

    .roadmap-stepper {
        position: relative;
        padding: 10px 10px 0 10px;
        overflow: visible !important;
    }

    /* Connecting Progress Track */
    .roadmap-progress-track {
        position: absolute;
        top: 35px;
        left: 8%;
        right: 8%;
        height: 4px;
        background-color: #e2e8f0;
        z-index: 1;
        border-radius: 4px;
    }

    .roadmap-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }

    .roadmap-progress-fill.bg-success {
        background: linear-gradient(90deg, #10b981, #059669) !important;
    }

    .roadmap-progress-fill.bg-primary {
        background: linear-gradient(90deg, #3b82f6, #1d4ed8) !important;
    }

    .roadmap-progress-fill.bg-info {
        background: linear-gradient(90deg, #06b6d4, #0284c7) !important;
    }

    .roadmap-progress-fill.bg-warning {
        background: linear-gradient(90deg, #f59e0b, #d97706) !important;
    }

    .roadmap-progress-fill.bg-danger {
        background: linear-gradient(90deg, #ef4444, #b91c1c) !important;
    }

    /* Steps flex row */
    .roadmap-steps {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        overflow: visible !important;
    }

    .roadmap-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0 6px;
        position: relative;
        transition: all 0.3s ease;
    }

    /* Node Circle */
    .roadmap-node {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 10px;
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 3;
    }

    /* Step content typography */
    .roadmap-content {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .roadmap-step-num {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .roadmap-title {
        font-size: 0.88rem;
        font-weight: 700;
        margin-bottom: 4px;
        color: #1e293b;
        line-height: 1.3;
    }

    .roadmap-badge {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 50rem;
        margin-bottom: 6px;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .roadmap-meta {
        font-size: 0.75rem;
        line-height: 1.35;
        color: #475569;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .roadmap-actor {
        font-weight: 600;
        color: #334155;
        margin-bottom: 2px;
    }

    .roadmap-time {
        font-size: 0.72rem;
        color: #64748b;
    }

    /* ============================================================
       STATE 1: COMPLETED (Active, Full Color, Checked)
       ============================================================ */
    .roadmap-step.step-completed .roadmap-node {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: 3px solid #d1fae5;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }

    .roadmap-step.step-completed .roadmap-step-num {
        color: #10b981;
    }

    .roadmap-step.step-completed .roadmap-title {
        color: #064e3b;
    }

    /* ============================================================
       STATE 2: CURRENT / IN PROGRESS (Pulse, Vibrant Color)
       ============================================================ */
    .roadmap-step.step-current.status-warning .roadmap-node {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        border: 3px solid #fef3c7;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
        animation: pulse-ring-warning 2s infinite;
    }

    .roadmap-step.step-current.status-info .roadmap-node {
        background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
        color: #ffffff;
        border: 3px solid #cffafe;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.25);
        animation: pulse-ring-info 2s infinite;
    }

    .roadmap-step.step-current.status-pending .roadmap-node {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #ffffff;
        border: 3px solid #dbeafe;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
    }

    .roadmap-step.step-current .roadmap-step-num {
        color: #2563eb;
    }

    @keyframes pulse-ring-warning {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
        70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    @keyframes pulse-ring-info {
        0% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.5); }
        70% { box-shadow: 0 0 0 10px rgba(6, 182, 212, 0); }
        100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0); }
    }

    /* ============================================================
       STATE 3: REJECTED (Danger Color)
       ============================================================ */
    .roadmap-step.step-rejected .roadmap-node {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: 3px solid #fee2e2;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }

    .roadmap-step.step-rejected .roadmap-step-num {
        color: #ef4444;
    }

    .roadmap-step.step-rejected .roadmap-title {
        color: #991b1b;
    }

    /* ============================================================
       STATE 4: GREYSCALE / INCOMPLETE (Muted, Greyscale)
       ============================================================ */
    .roadmap-step.step-greyscale {
        filter: grayscale(100%);
        opacity: 0.55;
    }

    .roadmap-step.step-greyscale .roadmap-node {
        background-color: #f8fafc;
        color: #94a3b8;
        border: 2px dashed #cbd5e1;
        box-shadow: none;
    }

    .roadmap-step.step-greyscale .roadmap-title {
        color: #64748b;
    }

    .roadmap-step.step-greyscale .roadmap-meta {
        color: #94a3b8;
    }

    /* ============================================================
       RECEIPT HISTORY HOVER TRIGGER & POPOVER CARD
       ============================================================ */
    .receipt-hover-container {
        position: relative;
        display: inline-block;
        z-index: 1050;
    }

    .receipt-hover-trigger {
        cursor: pointer;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .receipt-hover-trigger:hover,
    .receipt-hover-trigger:focus {
        background-color: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        box-shadow: 0 3px 10px rgba(22, 163, 74, 0.25);
        outline: none;
    }

    .receipt-popover-card {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        min-width: 320px;
        max-width: 380px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.18), 0 5px 15px rgba(0, 0, 0, 0.08);
        padding: 14px;
        z-index: 1060;
        text-align: left;
        filter: none !important; /* Ensure it stays fully colored even if parent step is greyscaled */
        opacity: 1 !important;
        pointer-events: auto;
    }

    /* Triangle indicator */
    .receipt-popover-card::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -7px;
        border-width: 7px;
        border-style: solid;
        border-color: transparent transparent #ffffff transparent;
    }

    .receipt-hover-container:hover .receipt-popover-card,
    .receipt-hover-container:focus-within .receipt-popover-card {
        display: block;
        animation: fadeInPopover 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeInPopover {
        from {
            opacity: 0;
            transform: translate(-50%, 6px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }

    /* Node Circle */
    .roadmap-node {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 3;
    }

    /* Responsive adjustments for 6 steps */
    @media (max-width: 1199.98px) {
        .roadmap-step {
            padding: 0 3px;
        }
        .roadmap-title {
            font-size: 0.82rem;
        }
        .roadmap-badge {
            font-size: 0.68rem;
            padding: 0.2rem 0.45rem;
        }
        .roadmap-meta {
            font-size: 0.71rem;
        }
    }

    @media (max-width: 991.98px) {
        .roadmap-progress-track {
            display: none;
        }
        .roadmap-steps {
            flex-wrap: wrap;
            gap: 12px;
        }
        .roadmap-step {
            flex: 0 0 31%;
            margin-bottom: 10px;
        }
        .receipt-popover-card {
            left: 0;
            transform: none;
        }
        .receipt-popover-card::before {
            left: 20px;
            margin-left: 0;
        }
    }

    @media (max-width: 767.98px) {
        .roadmap-step {
            flex: 0 0 48%;
        }
    }

    @media (max-width: 575.98px) {
        .roadmap-step {
            flex: 0 0 100%;
            flex-direction: row;
            text-align: left;
            align-items: flex-start;
            gap: 12px;
        }
        .roadmap-content {
            align-items: flex-start;
        }
        .roadmap-meta {
            align-items: flex-start;
        }
        .roadmap-node {
            margin-bottom: 0;
            flex-shrink: 0;
        }
    }
</style>
@endpush

