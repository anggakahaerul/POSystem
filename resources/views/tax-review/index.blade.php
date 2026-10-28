@extends('layouts.app')

@section('title', 'Review Pajak PO')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active">Review Pajak</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stat Cards (Minimalist) -->
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-2 mr-3 bg-light text-primary">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Total PO</div>
                        <h4 class="mb-0 font-weight-bold">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-2 mr-3 bg-light text-warning">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Menunggu Review</div>
                        <h4 class="mb-0 font-weight-bold text-warning">{{ $stats['pending'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-2 mr-3 bg-light text-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Terverifikasi</div>
                        <h4 class="mb-0 font-weight-bold text-success">{{ $stats['verified'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-2 mr-3 bg-light text-info">
                        <i class="fas fa-edit fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Terkoreksi</div>
                        <h4 class="mb-0 font-weight-bold text-info">{{ $stats['revised'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <!-- Filter Tabs -->
                <div class="btn-group btn-group-sm mb-2 mb-md-0" role="group">
                    <a href="{{ route('tax-reviews.index', ['status' => 'all', 'search' => $search]) }}" 
                       class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
                    <a href="{{ route('tax-reviews.index', ['status' => 'pending', 'search' => $search]) }}" 
                       class="btn {{ $status === 'pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary' }}">
                       Menunggu
                       @if($stats['pending'] > 0)
                           <span class="badge badge-light ml-1">{{ $stats['pending'] }}</span>
                       @endif
                    </a>
                    <a href="{{ route('tax-reviews.index', ['status' => 'verified', 'search' => $search]) }}" 
                       class="btn {{ $status === 'verified' ? 'btn-success' : 'btn-outline-secondary' }}">Terverifikasi</a>
                    <a href="{{ route('tax-reviews.index', ['status' => 'revised', 'search' => $search]) }}" 
                       class="btn {{ $status === 'revised' ? 'btn-info' : 'btn-outline-secondary' }}">Terkoreksi</a>
                </div>

                <!-- Search Form -->
                <form action="{{ route('tax-reviews.index') }}" method="GET" class="form-inline">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari No. PO / Vendor..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                            @if($search)
                                <a href="{{ route('tax-reviews.index', ['status' => $status]) }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="bg-light">
                    <tr>
                        <th width="140">No. PO</th>
                        <th>Vendor & NPWP</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center">PPN</th>
                        <th class="text-center">PPh</th>
                        <th class="text-right">Total Akhir</th>
                        <th class="text-center" width="130">Status Pajak</th>
                        <th class="text-center" width="110">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr id="row-po-{{ $po->id }}">
                            <td>
                                <a href="{{ route('purchase-orders.show', $po->id) }}" class="font-weight-bold text-dark font-monospace">
                                    {{ $po->po_number }}
                                </a>
                                <small class="text-muted d-block">{{ $po->created_at->format('d/m/Y') }}</small>
                                @if($po->tax_review_requested_at)
                                    <small class="text-info d-block" title="Permohonan review dikirim ke {{ $po->tax_reviewer_email }} pada {{ $po->tax_review_requested_at->format('d/m/Y H:i') }}">
                                        <i class="fas fa-paper-plane mr-1"></i>Email {{ $po->tax_review_requested_at->format('d/m') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $po->vendor->name ?? '-' }}</div>
                                <div class="d-flex align-items-center flex-wrap mt-1">
                                    @if($po->vendor?->npwp)
                                        <small class="text-muted mr-2"><i class="fas fa-id-card mr-1"></i>{{ $po->vendor->npwp }}</small>
                                    @else
                                        <small class="text-danger mr-2"><i class="fas fa-exclamation-triangle mr-1"></i>Tanpa NPWP</small>
                                    @endif

                                    @if($po->vendor?->npwp_type)
                                        <span class="badge badge-light border text-info" style="font-size: 0.72rem;">{{ $po->vendor->npwp_type }}</span>
                                    @else
                                        <span class="badge badge-light border text-warning" style="font-size: 0.72rem;">Belum diset</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right font-weight-bold text-muted">
                                Rp {{ number_format($po->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($po->ppn_percentage > 0)
                                    <span class="badge badge-light border text-dark">{{ (float)$po->ppn_percentage }}%</span>
                                    <br><small class="text-muted">+Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</small>
                                @else
                                    <span class="text-muted small">0%</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($po->pph_amount > 0)
                                    <span class="badge badge-light border text-danger">{{ $po->pph_label }}</span>
                                    <br><small class="text-danger font-weight-bold">-Rp {{ number_format($po->pph_amount, 0, ',', '.') }}</small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-right font-weight-bold text-dark">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center" id="status-badge-{{ $po->id }}">
                                <span class="badge badge-{{ $po->tax_review_badge }} px-2 py-1">
                                    {{ $po->tax_review_label }}
                                </span>
                                @if($po->taxReviewer)
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">oleh {{ $po->taxReviewer->name }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('review tax')
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openReviewModal({{ $po->id }})" title="Review & Koreksi Pajak">
                                        <i class="fas fa-tasks mr-1"></i> Review
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="openReviewModal({{ $po->id }})" title="Lihat Detail Pajak">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x mb-2 text-secondary" style="opacity: 0.3;"></i>
                                <div class="font-weight-bold">Tidak ada data review pajak</div>
                                <small>Semua PO telah terverifikasi atau tidak ada data yang cocok dengan filter.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchaseOrders->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">Total {{ $purchaseOrders->total() }} PO</small>
                <div>{{ $purchaseOrders->links() }}</div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Review & Koreksi Pajak (Minimalist) -->
<div class="modal fade" id="taxReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <form id="taxReviewForm" onsubmit="event.preventDefault(); submitTaxReview();" class="modal-content border-0 shadow">
            @csrf
            <input type="hidden" id="mPoId" name="po_id">
            <input type="hidden" id="mActionType" name="action_type" value="verify">

            <div class="modal-header bg-light py-2">
                <h6 class="modal-title font-weight-bold" id="modalTitle">
                    <i class="fas fa-balance-scale text-primary mr-1"></i> Review Pajak PO <span id="mPoNumber" class="text-primary font-monospace"></span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body py-3" style="overflow-y: auto; max-height: calc(100vh - 190px);">
                    <!-- Section 1: Validasi Vendor & NPWP -->
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold small text-uppercase text-secondary">
                                    <i class="fas fa-building mr-1"></i> Data Vendor & NPWP
                                </span>
                                <span id="mVendorName" class="font-weight-bold text-dark"></span>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold mb-1" for="mNpwpType">Tipe NPWP</label>
                                    <select class="form-control form-control-sm" id="mNpwpType" name="npwp_type">
                                        <option value="">-- Pilih Tipe NPWP --</option>
                                        <option value="Orang Pribadi">NPWP Orang Pribadi</option>
                                        <option value="Badan">NPWP Badan</option>
                                        <option value="Instansi Pemerintah">NPWP Instansi Pemerintah</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bold mb-1" for="mNpwp">Nomor NPWP</label>
                                    <input type="text" class="form-control form-control-sm" id="mNpwp" name="npwp" placeholder="00.000.000.0-000.000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Item PO (Minimalist) -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold small text-uppercase text-secondary">
                                <i class="fas fa-boxes mr-1"></i> Detail Item PO
                            </span>
                            <span class="badge badge-light border text-muted" id="mItemCount">0 Item</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Item / Produk</th>
                                        <th width="70" class="text-center text-nowrap">Qty</th>
                                        <th width="140" class="text-right text-nowrap">Harga</th>
                                        <th width="140" class="text-right text-nowrap">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="mItemsBody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Section 3: Koreksi Pajak (PPN & PPh) -->
                    <div class="card border mb-3">
                        <div class="card-body p-3">
                            <div class="font-weight-bold small text-uppercase text-secondary mb-3">
                                <i class="fas fa-calculator mr-1"></i> Pengaturan Pajak (PPN & PPh)
                            </div>
                            
                            <div class="row">
                                <!-- PPN -->
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="small font-weight-bold mb-1" for="mPpnPct">PPN</label>
                                    <select class="form-control form-control-sm" id="mPpnPct" name="ppn_percentage" onchange="calculateLiveTotal()">
                                        <option value="0">0% (Bebas PPN)</option>
                                        <option value="11">11% (Standar)</option>
                                        <option value="12">12%</option>
                                    </select>
                                </div>

                                <!-- PPh Toggle & Type -->
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small font-weight-bold mb-0">PPh (Pajak Penghasilan)</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="mIsPph" name="is_pph" value="1" onchange="togglePphSection()">
                                            <label class="custom-control-label small font-weight-bold" for="mIsPph">Aktifkan PPh</label>
                                        </div>
                                    </div>
                                    <div id="mPphOptions" style="display: none;">
                                        <select class="form-control form-control-sm" id="mPphType" name="pph_type" onchange="onPphTypeChange()">
                                            <option value="pph23" data-rate="2">PPh 23 (2.0%) - Jasa / Sewa</option>
                                            <option value="pph22_15" data-rate="1.5">PPh 22 (1.5%) - Barang / Pengadaan</option>
                                            <option value="pph22_05" data-rate="0.5">PPh 22 (0.5%) - PP 23 UMKM</option>
                                            <option value="pph4_2" data-rate="10">PPh 4(2) (10.0%) - Sewa Bangunan</option>
                                            <option value="pph21_25" data-rate="2.5">PPh 21 (2.5%) - Tenaga Ahli</option>
                                            <option value="pph21_50" data-rate="5">PPh 21 (5.0%) - Jasa Orang Pribadi</option>
                                            <option value="custom" data-rate="custom">PPh (%)</option>
                                            <option value="custom_nominal" data-rate="custom_nominal">PPh Nominal (Rp)</option>
                                        </select>

                                        <div id="mCustomPphWrap" class="mt-2" style="display: none;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="small font-weight-bold text-muted">Metode PPh:</span>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-primary btn-xs active" id="lblModeRate" style="padding: 2px 8px; font-size: 11px;">
                                                        <input type="radio" name="custom_mode_radio" id="modeRate" value="rate" checked onchange="switchCustomMode('rate')">
                                                        <i class="fas fa-percent mr-1"></i>Persen (%)
                                                    </label>
                                                    <label class="btn btn-outline-primary btn-xs" id="lblModeNominal" style="padding: 2px 8px; font-size: 11px;">
                                                        <input type="radio" name="custom_mode_radio" id="modeNominal" value="nominal" onchange="switchCustomMode('nominal')">
                                                        <i class="fas fa-coins mr-1"></i>Nominal (Rp)
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Input Mode Persentase (%) -->
                                            <div id="mRateInputWrap">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white small font-weight-bold">Tarif PPh:</span>
                                                    </div>
                                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="mCustomPphRate" placeholder="Contoh: 1.75" oninput="onCustomRateInput()">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-white">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1 font-italic" id="mCustomRateNote"></small>
                                            </div>

                                            <!-- Input Mode Nominal (Rp) -->
                                            <div id="mNominalInputWrap" style="display: none;">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white small font-weight-bold">Nominal PPh:</span>
                                                        <span class="input-group-text bg-light font-weight-bold">Rp</span>
                                                    </div>
                                                    <input type="number" step="1" min="0" class="form-control" id="mCustomPphNominal" placeholder="Contoh: 150000" oninput="onCustomNominalInput()">
                                                </div>
                                                <small class="text-muted d-block mt-1 font-italic" id="mCustomNominalNote"></small>
                                            </div>
                                        </div>

                                        <input type="hidden" id="mPphPct" name="pph_percentage" value="2">
                                        <input type="hidden" id="mPphAmt" name="pph_amount" value="0">
                                    </div>
                                    <div id="mPphPlaceholder" class="text-muted small py-1">Tidak dikenakan potongan PPh</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Live Summary -->
                    <div class="p-3 rounded mb-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="text-muted small">Subtotal</div>
                                <div class="font-weight-bold text-dark" id="calcSubtotal">Rp 0</div>
                            </div>
                            <div class="col-3">
                                <div class="text-muted small">PPN (+)</div>
                                <div class="font-weight-bold text-primary" id="calcPpn">Rp 0</div>
                            </div>
                            <div class="col-3">
                                <div class="text-muted small">PPh (-)</div>
                                <div class="font-weight-bold text-danger" id="calcPph">Rp 0</div>
                            </div>
                            <div class="col-3">
                                <div class="text-muted small">Total PO</div>
                                <div class="font-weight-bold text-success" id="calcTotal">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Catatan Reviewer (Opsional) -->
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold mb-1" for="mTaxNotes">Catatan Review Pajak (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" id="mTaxNotes" name="tax_notes" placeholder="Misal: NPWP diperbarui, PPh disesuaikan ke PPh 21">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <div>
                        @can('review tax')
                            <button type="button" class="btn btn-sm btn-info mr-1" onclick="submitReviewAction('revise')" id="btnRevise">
                                <i class="fas fa-edit mr-1"></i> Simpan Koreksi
                            </button>
                            <button type="button" class="btn btn-sm btn-success" onclick="submitReviewAction('verify')" id="btnVerify">
                                <i class="fas fa-check-circle mr-1"></i> Verifikasi Sesuai
                            </button>
                        @endcan
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activePo = null;
    const canReviewTax = @json(auth()->user()->can('review tax'));

    function openReviewModal(id) {
        $('#mPoId').val(id);
        
        // Fetch details
        $.ajax({
            url: `{{ url('tax-reviews') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    activePo = res.data;
                    populateModal(activePo);
                    $('#taxReviewModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Gagal mengambil data PO.');
            }
        });
    }

    function populateModal(po) {
        $('#mPoNumber').text(po.po_number);
        $('#mVendorName').text(po.vendor.name);
        $('#mNpwpType').val(po.vendor.npwp_type || '');
        $('#mNpwp').val(po.vendor.npwp || '');

        $('#mPpnPct').val(po.ppn_percentage || 0);

        // Render Items Table
        const itemsBody = $('#mItemsBody');
        itemsBody.empty();
        if (po.items && po.items.length > 0) {
            $('#mItemCount').text(`${po.items.length} Item`);
            po.items.forEach(function(item) {
                itemsBody.append(`
                    <tr>
                        <td class="font-weight-bold text-dark">
                            ${item.product_name}
                        </td>
                        <td class="text-center font-weight-bold text-nowrap">${item.quantity}</td>
                        <td class="text-right text-muted text-nowrap">${formatRupiah(item.unit_price)}</td>
                        <td class="text-right font-weight-bold text-dark text-nowrap">${formatRupiah(item.subtotal)}</td>
                    </tr>
                `);
            });
        } else {
            $('#mItemCount').text('0 Item');
            itemsBody.append('<tr><td colspan="4" class="text-center text-muted py-2">Tidak ada item</td></tr>');
        }

        const isPph = Boolean(po.is_pph);
        $('#mIsPph').prop('checked', isPph);
        if (isPph) {
            if (po.pph_type === 'custom_nominal') {
                $('#mPphType').val('custom_nominal');
                $('#mCustomPphNominal').val(Math.round(po.pph_amount || 0));
                $('#mPphAmt').val(po.pph_amount || 0);
                $('#mCustomPphWrap').show();
                switchCustomMode('nominal');
            } else {
                const matchingOption = $('#mPphType').find(`option[value="${po.pph_type}"]`);
                if (matchingOption.length && po.pph_type !== 'custom') {
                    $('#mPphType').val(po.pph_type);
                    $('#mCustomPphWrap').hide();
                    $('#mPphPct').val(po.pph_percentage || matchingOption.data('rate') || 0);
                    $('#mPphAmt').val(0);
                } else {
                    $('#mPphType').val('custom');
                    $('#mCustomPphRate').val(po.pph_percentage || 0);
                    $('#mCustomPphWrap').show();
                    switchCustomMode('rate');
                    $('#mPphPct').val(po.pph_percentage || 0);
                    $('#mPphAmt').val(0);
                }
            }
        }
        $('#mTaxNotes').val(po.tax_notes || '');

        if (!canReviewTax) {
            $('#taxReviewForm input, #taxReviewForm select').prop('disabled', true);
        } else {
            $('#taxReviewForm input, #taxReviewForm select').prop('disabled', false);
        }

        togglePphSection();
        calculateLiveTotal();
    }

    let currentCustomMode = 'rate'; // 'rate' or 'nominal'

    function switchCustomMode(mode) {
        currentCustomMode = mode;
        if (mode === 'nominal') {
            $('#lblModeRate').removeClass('active');
            $('#lblModeNominal').addClass('active');
            $('#mRateInputWrap').hide();
            $('#mNominalInputWrap').show();
            $('#mPphType').val('custom_nominal');
            onCustomNominalInput();
        } else {
            $('#lblModeNominal').removeClass('active');
            $('#lblModeRate').addClass('active');
            $('#mNominalInputWrap').hide();
            $('#mRateInputWrap').show();
            $('#mPphType').val('custom');
            onCustomRateInput();
        }
    }

    function togglePphSection() {
        const isPph = $('#mIsPph').is(':checked');
        if (isPph) {
            $('#mPphOptions').show();
            $('#mPphPlaceholder').hide();
            onPphTypeChange();
        } else {
            $('#mPphOptions').hide();
            $('#mPphPlaceholder').show();
            $('#mCustomPphWrap').hide();
        }
        calculateLiveTotal();
    }

    function onPphTypeChange() {
        const selected = $('#mPphType').find(':selected');
        const val = selected.val();
        const rate = selected.data('rate');

        if (val === 'custom_nominal') {
            $('#mCustomPphWrap').show();
            switchCustomMode('nominal');
        } else if (val === 'custom' || rate === 'custom') {
            $('#mCustomPphWrap').show();
            switchCustomMode('rate');
        } else {
            $('#mCustomPphWrap').hide();
            $('#mPphPct').val(rate || 0);
            $('#mPphAmt').val(0);
            currentCustomMode = 'rate';
        }
        calculateLiveTotal();
    }

    function onCustomRateInput() {
        const rate = parseFloat($('#mCustomPphRate').val()) || 0;
        $('#mPphPct').val(rate);
        $('#mPphAmt').val(0);

        if (activePo) {
            const subtotal = activePo.subtotal - activePo.discount_amount;
            const equivalentAmt = (subtotal * rate) / 100;
            $('#mCustomRateNote').text(`≈ Potongan: ${formatRupiah(equivalentAmt)}`);
        }
        calculateLiveTotal();
    }

    function onCustomNominalInput() {
        const nominal = parseFloat($('#mCustomPphNominal').val()) || 0;
        $('#mPphAmt').val(nominal);

        if (activePo) {
            const subtotal = activePo.subtotal - activePo.discount_amount;
            const equivalentPct = subtotal > 0 ? ((nominal / subtotal) * 100).toFixed(2) : 0;
            $('#mPphPct').val(equivalentPct);
            $('#mCustomNominalNote').text(`≈ Setara tarif: ${equivalentPct}% dari Subtotal`);
        }
        calculateLiveTotal();
    }

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function calculateLiveTotal() {
        if (!activePo) return;

        const subtotal = activePo.subtotal - activePo.discount_amount;
        const ppnPct = parseFloat($('#mPpnPct').val()) || 0;
        const isPph = $('#mIsPph').is(':checked');
        const pphType = $('#mPphType').val();

        let pphAmt = 0;
        let pphPct = 0;

        if (isPph) {
            if (pphType === 'custom_nominal') {
                pphAmt = parseFloat($('#mPphAmt').val()) || 0;
                pphPct = subtotal > 0 ? ((pphAmt / subtotal) * 100).toFixed(2) : 0;
            } else {
                pphPct = parseFloat($('#mPphPct').val()) || 0;
                pphAmt = (subtotal * pphPct) / 100;
            }
        }

        const ppnAmt = (subtotal * ppnPct) / 100;
        const total = subtotal + ppnAmt - pphAmt;

        $('#calcSubtotal').text(formatRupiah(subtotal));
        $('#calcPpn').text('+' + formatRupiah(ppnAmt) + ` (${ppnPct}%)`);
        if (isPph) {
            if (pphType === 'custom_nominal') {
                $('#calcPph').text('-' + formatRupiah(pphAmt) + ` (Rp Nominal)`);
            } else {
                $('#calcPph').text('-' + formatRupiah(pphAmt) + ` (${pphPct}%)`);
            }
        } else {
            $('#calcPph').text('Rp 0');
        }
        $('#calcTotal').text(formatRupiah(total));
    }

    function submitReviewAction(actionType) {
        $('#mActionType').val(actionType);
        submitTaxReview();
    }

    function submitTaxReview() {
        const poId = $('#mPoId').val();
        const actionType = $('#mActionType').val();

        const data = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            action_type: actionType,
            npwp_type: $('#mNpwpType').val(),
            npwp: $('#mNpwp').val(),
            ppn_percentage: $('#mPpnPct').val(),
            is_pph: $('#mIsPph').is(':checked') ? 1 : 0,
            pph_type: $('#mPphType').val(),
            pph_percentage: $('#mPphPct').val(),
            pph_amount: $('#mPphAmt').val() || 0,
            tax_notes: $('#mTaxNotes').val(),
        };

        const btnSave = actionType === 'verify' ? $('#btnVerify') : $('#btnRevise');
        btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: `{{ url('tax-reviews') }}/${poId}`,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                btnSave.prop('disabled', false);
                if (actionType === 'verify') {
                    btnSave.html('<i class="fas fa-check-circle mr-1"></i> Verifikasi Sesuai');
                } else {
                    btnSave.html('<i class="fas fa-edit mr-1"></i> Simpan Koreksi');
                }

                if (res.success) {
                    $('#taxReviewModal').modal('hide');
                    toastr.success(res.message);
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    toastr.error(res.message || 'Terjadi kesalahan.');
                }
            },
            error: function(xhr) {
                btnSave.prop('disabled', false);
                if (actionType === 'verify') {
                    btnSave.html('<i class="fas fa-check-circle mr-1"></i> Verifikasi Sesuai');
                } else {
                    btnSave.html('<i class="fas fa-edit mr-1"></i> Simpan Koreksi');
                }
                const msg = xhr.responseJSON?.message || 'Gagal menyimpan review.';
                toastr.error(msg);
            }
        });
    }
</script>
@endpush
