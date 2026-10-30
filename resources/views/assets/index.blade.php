@extends('layouts.app')

@section('title', 'Daftar Barang (Asset Management)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Asset Management</a></li>
    <li class="breadcrumb-item active">Daftar Barang</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Stat Cards Overview -->
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="card bg-white border-0 shadow-sm mb-0 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Total Barang Aset</span>
                        <h3 class="mb-0 font-weight-bold" id="statTotal">{{ $stats['total'] ?? count($assets ?? []) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="card bg-white border-0 shadow-sm mb-0 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Sedang Digunakan</span>
                        <h3 class="mb-0 font-weight-bold text-success" id="statDigunakan">{{ $stats['digunakan'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="card bg-white border-0 shadow-sm mb-0 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Tersedia (Ready)</span>
                        <h3 class="mb-0 font-weight-bold text-info" id="statTersedia">{{ $stats['tersedia'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 mb-2">
            <div class="card bg-white border-0 shadow-sm mb-0 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle p-3 mr-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Maintenance / Rusak</span>
                        <h3 class="mb-0 font-weight-bold text-warning" id="statMaintenance">{{ $stats['maintenance_rusak'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 px-3 border-bottom asset-card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center w-100" style="gap: 12px;">
                <!-- 3 Tab Pemisah: Aset GA, Aset MIS, Other -->
                <div class="asset-group-tabs" id="assetGroupTabs" role="tablist">
                    <button type="button" class="asset-tab-btn active" data-tab-key="GA" onclick="switchAssetTab('GA')">
                        <span>Aset GA</span>
                        <span class="asset-tab-count" id="tabCountGA">0</span>
                    </button>
                    <button type="button" class="asset-tab-btn" data-tab-key="MIS" onclick="switchAssetTab('MIS')">
                        <span>Aset MIS</span>
                        <span class="asset-tab-count" id="tabCountMIS">0</span>
                    </button>
                    <button type="button" class="asset-tab-btn" data-tab-key="OTHER" onclick="switchAssetTab('OTHER')">
                        <span>Other</span>
                        <span class="asset-tab-count" id="tabCountOTHER">0</span>
                    </button>
                </div>

                <div class="d-flex flex-wrap align-items-center ml-auto" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold px-3" onclick="openScanQrModal()">
                        <i class="fas fa-qrcode mr-1"></i> Scan QR
                    </button>
                    <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-info btn-sm font-weight-bold px-3" title="Lihat Riwayat Barang Masuk PO">
                        <i class="fas fa-truck-loading mr-1"></i> Barang Masuk PO
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card-body bg-light border-bottom p-3">
            <div class="row align-items-center" style="row-gap: 10px;">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="searchInput" class="form-control border-left-0" placeholder="Cari nama, kode QR, PO, serial number..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select id="filterStatus" class="form-control form-control-sm">
                        <option value="">Semua Status</option>
                        <option value="Digunakan">Digunakan</option>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Dipinjam">Dipinjam</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select id="filterDivisi" class="form-control form-control-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisiList ?? [] as $div)
                            <option value="{{ $div }}">{{ $div }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <select id="filterKategori" class="form-control form-control-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList ?? [] as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <select id="filterCabang" class="form-control form-control-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangList ?? [] as $cab)
                            <option value="{{ $cab }}">{{ $cab }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-1 col-12 text-right">
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btnResetFilter" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>

            <!-- Batch Action Toolbar (Muncul saat Select All / Checkbox dipilih) -->
            <div id="batchActionBar" class="alert alert-primary d-none align-items-center justify-content-between p-2 mt-3 mb-0" style="background-color: #e0e7ff; border-color: #c7d2fe; color: #3730a3; border-radius: 6px;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-square fa-lg mr-2"></i>
                    <span class="font-weight-bold mr-2"><span id="selectedCount">0</span> barang dipilih</span>
                </div>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-sm btn-primary" onclick="bulkPrintQR()">
                        <i class="fas fa-print mr-1"></i> Cetak Label QR Terpilih
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="assetsTable">
                    <thead class="thead-light">
                        <tr class="text-nowrap" style="font-size: 0.84rem; text-transform: uppercase; letter-spacing: 0.03em;">
                            <th class="text-center" style="width: 45px;">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input" id="selectAllCheckbox">
                                    <label class="custom-control-label" for="selectAllCheckbox" title="Select All"></label>
                                </div>
                            </th>
                            <th class="text-center" style="width: 140px;">QR Code</th>
                            <th style="min-width: 160px;">No. PO</th>
                            <th style="min-width: 200px;">Nama Barang</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th style="min-width: 150px;">User</th>
                            <th style="min-width: 140px;">Divisi</th>
                            <th style="min-width: 140px;">Kategori</th>
                            <th style="min-width: 150px;">Cabang</th>
                            <th style="min-width: 220px;">Deskripsi</th>
                            <th class="text-center" style="width: 155px;">Action</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        @forelse($assets as $idx => $asset)
                            @php
                                $code = $asset->asset_code;
                                $words = explode(' ', trim($asset->user_holder ?: 'NA'));
                                $initials = count($words) > 1
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($words[0], 0, 2));
                                $divUpper = strtoupper(trim($asset->divisi ?? ''));
                                if (str_contains($divUpper, 'GA') || str_contains($divUpper, 'HR')) {
                                    $tabGroup = 'GA';
                                } elseif (str_contains($divUpper, 'MIS') || str_contains($divUpper, 'IT')) {
                                    $tabGroup = 'MIS';
                                } else {
                                    $tabGroup = 'OTHER';
                                }
                            @endphp
                            <tr data-asset-tab="{{ $tabGroup }}" data-status="{{ $asset->status }}" data-divisi="{{ $asset->divisi }}" data-kategori="{{ $asset->kategori }}" data-cabang="{{ $asset->cabang }}">
                                <td class="text-center align-middle">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" class="custom-control-input item-checkbox" id="check_{{ $asset->id }}" value="{{ $code }}">
                                        <label class="custom-control-label" for="check_{{ $asset->id }}"></label>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @if($asset->is_qr_generated)
                                        <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('{{ $code }}', '{{ addslashes($asset->nama_barang) }}', '{{ addslashes($asset->user_holder) }}', '{{ addslashes($asset->divisi) }}')" title="Klik untuk lihat &amp; cetak QR">
                                            <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                                <i class="fas fa-qrcode text-primary mr-1"></i><span class="qr-code-text">{{ $code }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-xs btn-primary font-weight-bold px-2 py-1 shadow-xs" onclick="generateQrForAsset('{{ $code }}')" title="Klik untuk Generate QR Code">
                                            <i class="fas fa-qrcode mr-1"></i> Generate QR
                                        </button>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap">
                                    @if($asset->purchaseOrder && $asset->purchase_order_id)
                                        <a href="{{ route('purchase-orders.show', $asset->purchase_order_id) }}" class="font-weight-bold text-primary" title="Lihat Detail PO {{ $asset->purchaseOrder->po_number }}">
                                            <i class="fas fa-file-invoice mr-1"></i>{{ $asset->purchaseOrder->po_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <strong class="text-dark d-block">{{ $asset->nama_barang }}</strong>
                                </td>
                                <td class="text-center align-middle status-cell">
                                    @if($asset->status === 'Digunakan')
                                        <span class="badge badge-success px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-check-circle mr-1"></i>Digunakan</span>
                                    @elseif($asset->status === 'Tersedia')
                                        <span class="badge badge-info px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-box mr-1"></i>Tersedia</span>
                                    @elseif($asset->status === 'Maintenance')
                                        <span class="badge badge-warning text-dark px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-wrench mr-1"></i>Maintenance</span>
                                    @elseif($asset->status === 'Rusak')
                                        <span class="badge badge-danger px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-times-circle mr-1"></i>Rusak</span>
                                    @elseif($asset->status === 'Dipinjam')
                                        <span class="badge badge-primary px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-hand-holding mr-1"></i>Dipinjam</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">{{ $asset->status }}</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-sm mr-2 bg-primary text-white">{{ $initials }}</div>
                                        <div>
                                            <strong class="d-block text-dark">{{ $asset->user_holder }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-secondary px-2 py-1">{{ $asset->divisi }}</span>
                                </td>
                                <td class="align-middle">
                                    <span class="text-dark font-weight-500">{{ $asset->kategori }}</span>
                                </td>
                                <td class="align-middle">
                                    <i class="fas fa-building text-secondary mr-1"></i>{{ $asset->cabang }}
                                </td>
                                <td class="align-middle">
                                    <div class="text-muted small text-truncate" style="max-width: 240px;" title="{{ $asset->deskripsi }}">
                                        {{ $asset->deskripsi ?: '-' }}
                                    </div>
                                </td>
                                <td class="text-center align-middle text-nowrap">
                                    <div class="action-btn-group">
                                        <button type="button" class="btn-action btn-action-info" title="Detail Spesifikasi &amp; Riwayat" onclick="openDetailModal('{{ $code }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi &amp; Riwayat" onclick="downloadAssetPdf('{{ $code }}')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        @if($asset->is_qr_generated)
                                            <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('{{ $code }}', '{{ addslashes($asset->nama_barang) }}', '{{ addslashes($asset->user_holder) }}', '{{ addslashes($asset->divisi) }}')">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn-action btn-action-success" title="Generate QR Code" onclick="generateQrForAsset('{{ $code }}')">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('{{ $code }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('{{ $code }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-2 text-muted" style="opacity: 0.4;"></i>
                                    <p class="mb-0 font-weight-bold text-secondary">Belum ada data barang inventaris.</p>
                                    <small class="text-muted">Saat barang pada Purchase Order (PO) diterima, unit barang akan otomatis masuk ke daftar ini.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer -->
        <div class="card-footer bg-white py-3 border-top d-flex flex-wrap justify-content-between align-items-center">
            <div class="text-muted small mb-2 mb-md-0">
                Menampilkan <span class="font-weight-bold" id="visibleRowCount">{{ count($assets ?? []) }}</span> dari <span class="font-weight-bold" id="totalRowCount">{{ count($assets ?? []) }}</span> total unit barang terdaftar
            </div>
        </div>
    </div>
</div>

<!-- Modal: Preview & Cetak Label QR Code Asli -->
<div class="modal fade" id="modalQrPreview" tabindex="-1" role="dialog" aria-labelledby="modalQrTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalQrTitle">
                    <i class="fas fa-qrcode mr-2"></i>Label QR Code Aset
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <!-- Label Sticker Printable Asli (Sesuai Format: Teks Kiri + QR Kanan) -->
                <div class="bg-white d-inline-block w-100 p-3 rounded border" id="printableQrLabel" style="max-width: 360px;">
                    <div class="d-flex align-items-center justify-content-center" style="gap: 18px;">
                        <div class="text-center font-weight-bold text-dark" style="font-family: Arial, Helvetica, sans-serif; font-size: 1.05rem; line-height: 1.35; color: #000000 !important; min-width: 120px;">
                            <div>PT Pangan</div>
                            <div>Lestari</div>
                            <div>Cabang</div>
                            <div class="text-uppercase" id="modalQrBranchText">JAKARTA</div>
                            <div class="text-uppercase" id="modalQrItemName">KOMPUTER</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center bg-white p-1" id="realQrCodeContainer"></div>
                    </div>
                </div>

                <!-- Info Kode di luar area cetak stiker -->
                <div class="mt-3">
                    <span class="badge badge-light border font-monospace text-dark px-2 py-1" style="font-size: 0.85rem;">
                        Kode QR: <strong id="modalQrCodeText">001KOMJAKARTA</strong>
                    </span>
                    <span class="d-none" id="modalQrUserName"></span>
                    <span class="d-none" id="modalQrDivisi"></span>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between flex-wrap">
                <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" onclick="resetCurrentQrCode()" title="Reset QR Code barang ini">
                    <i class="fas fa-undo mr-1"></i> Reset QR
                </button>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="testScanCurrentLabel()">
                        <i class="fas fa-camera mr-1"></i> Tes Scan
                    </button>
                    <button type="button" class="btn btn-danger btn-sm font-weight-bold" onclick="downloadQrLabelPdf()">
                        <i class="fas fa-file-pdf mr-1"></i> PDF Label
                    </button>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detail Barang, Spesifikasi Lengkap, Riwayat Produk & Detail Repair -->
<div class="modal fade" id="modalDetailBarang" tabindex="-1" role="dialog" aria-labelledby="modalDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom py-3 px-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <span class="badge badge-primary font-monospace" id="detQr">001ELESIDOARJO</span>
                            <span id="detStatus"></span>
                        </div>
                        <h6 class="modal-title font-weight-bold text-dark mb-0 mt-1" id="detNama">Nama Barang</h6>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                <!-- 1. Spesifikasi Lengkap -->
                <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom">
                    <i class="fas fa-microchip text-primary mr-1"></i> 1. Spesifikasi Barang
                </h6>
                <div class="row mb-4" style="font-size: 0.85rem;">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width: 135px;">Cabang</td><td class="font-weight-bold text-dark" id="detCabang">-</td></tr>
                            <tr><td class="text-muted">Kategori</td><td class="font-weight-500 text-dark" id="detKategori">-</td></tr>
                            <tr><td class="text-muted">Divisi</td><td class="font-weight-500 text-dark" id="detDivisi">-</td></tr>
                            <tr><td class="text-muted">User</td><td class="font-weight-bold text-dark" id="detUser">-</td></tr>
                            <tr><td class="text-muted">Nama Barang</td><td class="font-weight-500 text-dark" id="detNamaSpec">-</td></tr>
                            <tr><td class="text-muted">Status</td><td id="detStatusSpec">-</td></tr>
                            <tr><td class="text-muted">Merk</td><td class="font-weight-500 text-dark" id="detMerk">-</td></tr>
                            <tr><td class="text-muted">Processor</td><td class="font-weight-500 text-dark" id="detProcessor">-</td></tr>
                            <tr><td class="text-muted">Model</td><td class="font-weight-500 text-dark" id="detModel">-</td></tr>
                            <tr><td class="text-muted">Generasi</td><td class="font-weight-500 text-dark" id="detGenerasi">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted" style="width: 135px;">RAM</td><td class="font-weight-500 text-dark" id="detRam">-</td></tr>
                            <tr><td class="text-muted">Harddisk / Storage</td><td class="font-weight-500 text-dark" id="detHarddisk">-</td></tr>
                            <tr><td class="text-muted">Serial Number</td><td class="font-monospace text-dark" id="detSerial">-</td></tr>
                            <tr><td class="text-muted">Sistem Operasi</td><td class="font-weight-500 text-dark" id="detOs">-</td></tr>
                            <tr><td class="text-muted">Warna</td><td class="font-weight-500 text-dark" id="detWarna">-</td></tr>
                            <tr><td class="text-muted">Harga Perolehan</td><td class="font-weight-bold text-success" id="detHarga">-</td></tr>
                            <tr><td class="text-muted">Supplier</td><td class="font-weight-500 text-dark" id="detSupplier">-</td></tr>
                            <tr><td class="text-muted">Tanggal Terima</td><td class="font-weight-500 text-dark" id="detTanggal">-</td></tr>
                            <tr><td class="text-muted">Deskripsi</td><td class="text-muted" id="detDeskripsi">-</td></tr>
                        </table>
                    </div>
                </div>

                <!-- 2. Riwayat Produk (Simple Collapsible Per Bulan) -->
                <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom">
                    <i class="fas fa-history text-info mr-1"></i> 2. Riwayat Produk
                </h6>
                <div id="detHistoryRoadmapContainer" class="mb-4"></div>

                <!-- 3. Detail Repair -->
                <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom">
                    <i class="fas fa-tools text-warning mr-1"></i> 3. Detail Repair
                </h6>
                <div id="detRepairRoadmapContainer"></div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between">
                <button type="button" class="btn btn-light border btn-sm font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger btn-sm font-weight-bold px-3" id="btnModalDownloadPdf">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF Spesifikasi &amp; Riwayat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Barang -->
<div class="modal fade" id="modalEditBarang" tabindex="-1" role="dialog" aria-labelledby="modalEditTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom py-3 px-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 34px; height: 34px; background: #fff7ed; color: #ea580c;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-weight-bold text-dark mb-0" id="modalEditTitle">Edit Data &amp; Spesifikasi Barang</h6>
                        <small class="text-muted">Lengkapi atau perbarui informasi pemegang, lokasi cabang, dan spesifikasi teknis barang</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditBarang" onsubmit="saveEditAssetModal(event)">
                <input type="hidden" id="editAssetOriginalCode">
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Kode QR (No+3Kat+CABANG)</label>
                            <input type="text" id="editAssetCode" class="form-control form-control-sm font-monospace font-weight-bold text-primary" readonly style="background-color: #f8fafc;">
                        </div>

                        <div class="col-md-8 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="editAssetNama" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Cabang <span class="text-danger">*</span></label>
                            <select id="editAssetCabang" class="form-control form-control-sm" onchange="previewUpdatedEditCode()" required>
                                @foreach($cabangList ?? [] as $cab)
                                    <option value="{{ $cab }}">{{ $cab }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Kategori <span class="text-danger">*</span></label>
                            <select id="editAssetKategori" class="form-control form-control-sm" onchange="previewUpdatedEditCode()" required>
                                @foreach($kategoriList ?? [] as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Divisi <span class="text-danger">*</span></label>
                            <select id="editAssetDivisi" class="form-control form-control-sm" required>
                                @foreach($divisiList ?? [] as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">User / PIC <span class="text-danger">*</span></label>
                            <input type="text" id="editAssetUser" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Status <span class="text-danger">*</span></label>
                            <select id="editAssetStatus" class="form-control form-control-sm" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Digunakan">Digunakan</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Dipinjam">Dipinjam</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Merk</label>
                            <input type="text" id="editAssetMerk" class="form-control form-control-sm" placeholder="Contoh: Apple / Epson / Dell">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Model</label>
                            <input type="text" id="editAssetModel" class="form-control form-control-sm" placeholder="Contoh: MacBook Pro 14">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Processor</label>
                            <input type="text" id="editAssetProcessor" class="form-control form-control-sm" placeholder="Contoh: Apple M2 Pro / Core i7">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Generasi</label>
                            <input type="text" id="editAssetGenerasi" class="form-control form-control-sm" placeholder="Contoh: Gen 13 / 2024">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">RAM</label>
                            <input type="text" id="editAssetRam" class="form-control form-control-sm" placeholder="Contoh: 16 GB">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Harddisk / Storage</label>
                            <input type="text" id="editAssetHarddisk" class="form-control form-control-sm" placeholder="Contoh: SSD 512 GB">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Serial Number</label>
                            <input type="text" id="editAssetSerial" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Sistem Operasi</label>
                            <input type="text" id="editAssetOs" class="form-control form-control-sm" placeholder="Contoh: macOS / Windows 11 Pro">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Warna</label>
                            <input type="text" id="editAssetWarna" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Harga</label>
                            <input type="text" id="editAssetHarga" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Supplier</label>
                            <input type="text" id="editAssetSupplier" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Tanggal Terima</label>
                            <input type="text" id="editAssetTanggal" class="form-control form-control-sm" readonly style="background-color: #f8fafc;">
                        </div>

                        <div class="col-12 mb-1">
                            <label class="font-weight-bold small text-muted mb-1">Deskripsi</label>
                            <textarea id="editAssetDeskripsi" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light border btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold text-dark px-3" id="btnSaveEditAsset">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Scan QR / Barcode (Simple, Modern & Lightweight) -->
<div class="modal fade" id="modalScanQr" tabindex="-1" role="dialog" aria-labelledby="modalScanQrTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <!-- Header Minimalis -->
            <div class="modal-header bg-white border-bottom py-3 px-4 align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 34px; height: 34px; background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-weight-bold text-dark mb-0" id="modalScanQrTitle">Scan QR / Barcode Aset</h6>
                        <small class="text-muted" id="modalScanSubtitle">Arahkan kamera ke label barcode barang</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="stopCameraScanner()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <!-- STEP 1: Izin Kamera & Viewfinder Scanner -->
                <div id="scanStepCamera">
                    <!-- Box Izin Kamera (Muncul sebelum kamera diizinkan) -->
                    <div id="cameraPermissionPrompt" class="text-center py-4 px-3 rounded border bg-light">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: #e0e7ff; color: #4f46e5; font-size: 1.5rem;">
                                <i class="fas fa-camera"></i>
                            </span>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Izinkan Akses Kamera</h6>
                        <p class="text-muted small mb-3">
                            Aplikasi membutuhkan izin kamera untuk memindai QR Code atau Barcode (<code>No+3Kat+CABANG</code>) pada label barang.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center" style="gap: 8px;">
                            <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3 py-2 shadow-xs" id="btnRequestCamera" onclick="startCameraScanner()">
                                <i class="fas fa-video mr-1"></i> Izinkan &amp; Buka Kamera
                            </button>
                            <label class="btn btn-outline-primary btn-sm font-weight-bold px-3 py-2 mb-0" style="cursor: pointer;">
                                <i class="fas fa-image mr-1"></i> Scan dari Foto/Gambar
                                <input type="file" id="qrImageFileInput" accept="image/*" class="d-none" onchange="scanFromUploadedImage(this)">
                            </label>
                        </div>
                        <div id="cameraErrorMsg" class="text-danger small mt-2 d-none"></div>
                    </div>

                    <!-- Box Live Kamera (Muncul setelah kamera diizinkan) -->
                    <div id="cameraViewportWrapper" class="d-none">
                        <div class="scanner-viewport-box position-relative rounded overflow-hidden bg-dark mb-2" style="min-height: 250px;">
                            <div id="html5QrReader" class="w-100"></div>
                            <video id="qrVideoElement" class="w-100 h-100 d-none" style="object-fit: cover; max-height: 250px;" playsinline muted></video>
                        </div>
                        <div class="text-center">
                            <small class="text-muted"><i class="fas fa-crosshairs text-primary mr-1"></i> Arahkan QR Code atau Barcode Garis ke tengah kamera</small>
                        </div>
                    </div>

                    <!-- Input Manual / Quick Simulasi Scan -->
                    <div class="mt-3 pt-3 border-top">
                        <label class="small font-weight-bold text-muted mb-1 d-block">
                            Atau masukkan kode (No+3Kat+NAMACABANG) / pilih simulasi hasil scan:
                        </label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="manualBarcodeInput" class="form-control font-monospace" placeholder="Ketik kode QR (misal: 001ELESIDOARJO / 004SCASURABAYA)...">
                            <div class="input-group-append">
                                <button class="btn btn-dark font-weight-bold" type="button" onclick="handleScannedCode($('#manualBarcodeInput').val())">
                                    Cek Data
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap" style="gap: 5px;" id="quickSimButtons">
                            @foreach(($assets ?? collect())->take(5) as $simAsset)
                                <button type="button" class="btn btn-xs btn-outline-secondary font-monospace" onclick="handleScannedCode('{{ $simAsset->asset_code }}')">
                                    {{ $simAsset->asset_code }} ({{ Str::limit($simAsset->nama_barang, 14) }})
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Hasil Scan Detail Barang & Pemegang Terakhir -->
                <div id="scanStepResult" class="d-none">
                    <!-- Highlight Terakhir Ada di Siapa (Current Holder) -->
                    <div class="p-3 mb-3 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase font-weight-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                                <i class="fas fa-user-clock text-primary mr-1"></i> Posisi / Pemegang Saat Ini
                            </span>
                            <span id="scanResStatusBadge"></span>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="user-avatar-sm mr-2 bg-primary text-white" id="scanResAvatar" style="width: 38px; height: 38px; font-size: 0.85rem;">BS</div>
                            <div>
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;" id="scanResHolderUser">-</div>
                                <div class="text-muted small">
                                    <span id="scanResHolderDivisi">-</span> &bull; <span id="scanResHolderCabang">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Detail Barang (Sesuai Urutan Request) -->
                    <div class="border rounded px-3 py-2 bg-white mb-3">
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">Cabang</span>
                            <span class="scan-detail-val" id="scanValCabang">-</span>
                        </div>
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">Kategori</span>
                            <span class="scan-detail-val" id="scanValKategori">-</span>
                        </div>
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">Nama Barang</span>
                            <div class="text-right">
                                <span class="scan-detail-val d-block" id="scanValNama">-</span>
                                <small class="text-primary font-monospace" id="scanValCode">001ELESIDOARJO</small>
                            </div>
                        </div>
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">Status</span>
                            <span class="scan-detail-val" id="scanValStatus">-</span>
                        </div>
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">Divisi</span>
                            <span class="scan-detail-val" id="scanValDivisi">-</span>
                        </div>
                        <div class="scan-detail-row">
                            <span class="scan-detail-label">User</span>
                            <span class="scan-detail-val font-weight-bold" id="scanValUser">-</span>
                        </div>
                        <div class="scan-detail-row border-bottom-0">
                            <span class="scan-detail-label">Deskripsi</span>
                            <span class="scan-detail-val text-muted small" id="scanValDeskripsi" style="max-width: 65%;">-</span>
                        </div>
                    </div>

                    <!-- Action Buttons: Scan Ulang, Download PDF, Repair Data, Update Data -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-1" style="gap: 8px;">
                        <div class="d-flex" style="gap: 6px;">
                            <button type="button" class="btn btn-light btn-sm border font-weight-bold" onclick="resetToScannerView()">
                                <i class="fas fa-redo mr-1"></i> Scan Ulang
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" onclick="downloadAssetPdf(currentScannedAssetCode)">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </button>
                        </div>
                        <div class="d-flex" style="gap: 6px;">
                            <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark" onclick="openScanActionForm('repair')">
                                <i class="fas fa-tools mr-1"></i> Repair Data
                            </button>
                            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openScanActionForm('update')">
                                <i class="fas fa-edit mr-1"></i> Update Data
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Form Cepat Update Data / Repair Data (Di dalam Modal yang Sama) -->
                <div id="scanStepActionForm" class="d-none">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0" id="scanFormTitle">Update Data Barang</h6>
                        <span class="badge badge-light border font-monospace text-primary" id="scanFormCodeBadge">001ELESIDOARJO</span>
                    </div>

                    <form id="formScanQuickAction" onsubmit="saveScanQuickAction(event)">
                        <input type="hidden" id="scanActionMode" value="update">

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted mb-1">Nama Barang</label>
                            <input type="text" id="inputScanNama" class="form-control form-control-sm" required>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Cabang</label>
                                    <select id="inputScanCabang" class="form-control form-control-sm" onchange="previewUpdatedScanCode()" required>
                                        @foreach($cabangList ?? [] as $cab)
                                            <option value="{{ $cab }}">{{ $cab }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Kategori</label>
                                    <select id="inputScanKategori" class="form-control form-control-sm" onchange="previewUpdatedScanCode()" required>
                                        @foreach($kategoriList ?? [] as $kat)
                                            <option value="{{ $kat }}">{{ $kat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Status</label>
                                    <select id="inputScanStatus" class="form-control form-control-sm" required>
                                        <option value="Digunakan">Digunakan</option>
                                        <option value="Tersedia">Tersedia</option>
                                        <option value="Maintenance">Maintenance (Repair)</option>
                                        <option value="Rusak">Rusak</option>
                                        <option value="Dipinjam">Dipinjam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Divisi</label>
                                    <select id="inputScanDivisi" class="form-control form-control-sm" required>
                                        @foreach($divisiList ?? [] as $div)
                                            <option value="{{ $div }}">{{ $div }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold text-muted mb-1">User / Pemegang Terakhir</label>
                            <input type="text" id="inputScanUser" class="form-control form-control-sm" placeholder="Nama pemegang atau PIC..." required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted mb-1" id="labelScanDeskripsi">Deskripsi / Catatan</label>
                            <textarea id="inputScanDeskripsi" class="form-control form-control-sm" rows="2" placeholder="Keterangan kondisi atau catatan perbaikan..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between pt-2 border-top">
                            <button type="button" class="btn btn-light btn-sm border" onclick="backToScanResult()">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3" id="btnSubmitScanAction">
                                <i class="fas fa-check mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling Action Buttons Identik dengan Design System PO */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        vertical-align: middle;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        min-width: 32px;
        max-width: 32px;
        min-height: 32px;
        max-height: 32px;
        padding: 0 !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        font-size: 0.85rem !important;
        line-height: 1 !important;
        border: none !important;
        outline: none !important;
        text-decoration: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        color: #ffffff !important;
    }

    .btn-action i {
        display: inline-block;
        font-size: 0.85rem;
        line-height: 1;
        margin: 0;
        padding: 0;
        color: #ffffff !important;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        filter: brightness(1.08);
        color: #ffffff !important;
        text-decoration: none !important;
    }

    .btn-action:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .btn-action-info { background-color: #17a2b8; }
    .btn-action-pdf { background-color: #e11d48; }
    .btn-action-warning { background-color: #e67e22; }
    .btn-action-danger { background-color: #dc3545; }
    .btn-action-success { background-color: #10b981; }

    /* Avatar Small */
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .qr-badge-preview {
        text-decoration: none;
        transition: transform 0.15s ease;
        display: inline-block;
    }

    .qr-badge-preview:hover {
        transform: scale(1.05);
        text-decoration: none;
    }

    /* Segmented 3-Tab Bar (Aset GA, Aset MIS, Other) */
    .asset-card-header::after {
        display: none !important;
    }

    .asset-group-tabs {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        gap: 4px;
    }

    .asset-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border: none;
        border-radius: 7px;
        background: transparent;
        color: #475569;
        font-size: 0.86rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none !important;
    }

    .asset-tab-btn:hover {
        color: #1e293b;
        background: rgba(255, 255, 255, 0.6);
    }

    .asset-tab-btn.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    .asset-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 6px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 700;
        background: #e2e8f0;
        color: #334155;
    }

    .asset-tab-btn.active .asset-tab-count {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff;
    }

    /* Scanner Modal Minimalist & Lightweight Styles */
    .scanner-overlay-frame {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 170px;
        height: 170px;
        transform: translate(-50%, -50%);
        border: 2px solid rgba(255, 255, 255, 0.75);
        border-radius: 12px;
        box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.45);
        pointer-events: none;
        overflow: hidden;
    }

    .scanner-laser-line {
        width: 100%;
        height: 2px;
        background: #4f46e5;
        box-shadow: 0 0 8px #4f46e5;
        position: absolute;
        top: 0;
        animation: scanLaserMove 2s infinite ease-in-out;
    }

    @keyframes scanLaserMove {
        0%, 100% { top: 8%; }
        50% { top: 92%; }
    }

    .scan-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 7px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }

    .scan-detail-label {
        color: #64748b;
        font-weight: 500;
        min-width: 105px;
    }

    .scan-detail-val {
        color: #1e293b;
        font-weight: 500;
        text-align: right;
    }

    /* Simple Monthly Collapsible Timeline Styles */
    .roadmap-month-group {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #ffffff;
        overflow: hidden;
    }

    .roadmap-month-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        cursor: pointer;
        user-select: none;
        font-size: 1rem;
    }

    .roadmap-month-header:hover {
        background: #f1f5f9;
    }

    .roadmap-chevron {
        transition: transform 0.2s ease;
        color: #64748b;
        font-size: 0.9rem;
    }

    .roadmap-month-group.is-open .roadmap-chevron {
        transform: rotate(180deg);
    }

    .roadmap-timeline-track {
        padding: 14px 18px 6px 26px;
        border-top: 1px solid #f1f5f9;
    }

    .roadmap-step-item {
        position: relative;
        padding: 0 0 16px 20px;
        border-left: 2px solid #cbd5e1;
        font-size: 0.96rem;
        line-height: 1.5;
    }

    .roadmap-step-item:last-child {
        border-left-color: transparent;
        padding-bottom: 8px;
    }

    .roadmap-step-dot {
        position: absolute;
        left: -7px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #3b82f6;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 1px #cbd5e1;
    }

    /* Print Label Media Query */
    @media print {
        body * {
            visibility: hidden;
        }
        #printableQrLabel, #printableQrLabel * {
            visibility: visible;
        }
        #printableQrLabel {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
@php
    // Encode Logo Kop Surat PT. Pangan Lestari ke Base64 agar langsung ter-render tajam saat Download PDF / Print
    $kopLogoPath = public_path('images/logo-pangan-lestari.png');
    $kopLogoBase64 = file_exists($kopLogoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($kopLogoPath))
        : asset('images/logo-pangan-lestari.png');
@endphp
<!-- Library Generator QR Code (2D), Scanner Kamera Lintas Browser, dan PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
const KOP_SURAT_LOGO = @json($kopLogoBase64);
const CSRF_TOKEN = '{{ csrf_token() }}';
const ASSET_UPDATE_URL_BASE = '{{ url("/asset-management") }}';

/**
 * Format nama cabang menjadi NAMA CABANG KAPITAL PENUH (bukan disingkat SBY/SDA)
 * Contoh: "Branch Sidoarjo" -> "SIDOARJO", "Cabang Surabaya" -> "SURABAYA"
 */
function formatBranchFullUppercase(cabang) {
    if (!cabang) return 'SIDOARJO';
    let cleaned = cabang.replace(/\b(head\s*office|kantor\s*pusat|branch|cabang|dc)\b/gi, '');
    cleaned = cleaned.replace(/[^a-zA-Z]/g, '').toUpperCase();
    if (!cleaned) {
        cleaned = cabang.replace(/[^a-zA-Z]/g, '').toUpperCase();
    }
    return cleaned || 'SIDOARJO';
}

/**
 * Generator Kode Aset Otomatis (Opsi A Sync PO -> Inventaris)
 * Format: No (3 digit) + 3 Huruf Kategori Awal + NAMA CABANG KAPITAL PENUH
 * Contoh: No 4 + Kategori "Scanner & Printer" (SCA) + Cabang Surabaya (SURABAYA) => 004SCASURABAYA
 */
function generateAssetCode(noUrut, kategori, cabang) {
    const numStr = String(noUrut || 1).padStart(3, '0');
    const cleanKat = (kategori || 'AST').replace(/[^a-zA-Z]/g, '').toUpperCase();
    const katPrefix = (cleanKat.substring(0, 3) || 'AST').padEnd(3, 'X');
    const branchFull = formatBranchFullUppercase(cabang);
    return `${numStr}${katPrefix}${branchFull}`;
}

// Data Master Barang Aset dari Database (Hasil Auto-Sync Penerimaan Barang PO)
const assetDatabase = @json($assetDatabase ?? []);

let activeVideoStream = null;
let barcodeDetectionInterval = null;
let html5QrScannerInstance = null;
let currentScannedAssetCode = null;
let activeAssetTab = 'GA';

function resolveAssetTabGroup(divisi) {
    const d = String(divisi || '').toUpperCase().trim();
    if (d.includes('GA') || d.includes('HR')) return 'GA';
    if (d.includes('MIS') || d.includes('IT')) return 'MIS';
    return 'OTHER';
}

function switchAssetTab(tabKey) {
    activeAssetTab = tabKey;
    $('#assetGroupTabs .asset-tab-btn').removeClass('active');
    $(`#assetGroupTabs .asset-tab-btn[data-tab-key="${tabKey}"]`).addClass('active');
    refreshStatCards();
    if (typeof window.triggerAssetFilter === 'function') {
        window.triggerAssetFilter();
    }
}

function getCurrentTimestampStr() {
    const now = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const d = String(now.getDate()).padStart(2, '0');
    const m = months[now.getMonth()];
    const y = now.getFullYear();
    const hh = String(now.getHours()).padStart(2, '0');
    const mm = String(now.getMinutes()).padStart(2, '0');
    return `${d} ${m} ${y} ${hh}:${mm}`;
}

function getStatusBadgeHtml(status) {
    switch (status) {
        case 'Digunakan':
            return '<span class="badge badge-success px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-check-circle mr-1"></i>Digunakan</span>';
        case 'Tersedia':
            return '<span class="badge badge-info px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-box mr-1"></i>Tersedia</span>';
        case 'Maintenance':
            return '<span class="badge badge-warning text-dark px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-wrench mr-1"></i>Maintenance</span>';
        case 'Rusak':
            return '<span class="badge badge-danger px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-times-circle mr-1"></i>Rusak</span>';
        case 'Dipinjam':
            return '<span class="badge badge-primary px-2 py-1 font-weight-normal" style="font-size: 0.78rem;"><i class="fas fa-hand-holding mr-1"></i>Dipinjam</span>';
        default:
            return '<span class="badge badge-secondary px-2 py-1" style="font-size: 0.78rem;">' + status + '</span>';
    }
}

function getInitials(name) {
    if (!name) return 'NA';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
}

function refreshStatCards() {
    const allItems = Object.values(assetDatabase);
    const countGA = allItems.filter(i => resolveAssetTabGroup(i.divisi) === 'GA').length;
    const countMIS = allItems.filter(i => resolveAssetTabGroup(i.divisi) === 'MIS').length;
    const countOTHER = allItems.filter(i => resolveAssetTabGroup(i.divisi) === 'OTHER').length;

    $('#tabCountGA').text(countGA);
    $('#tabCountMIS').text(countMIS);
    $('#tabCountOTHER').text(countOTHER);

    const items = allItems.filter(i => resolveAssetTabGroup(i.divisi) === activeAssetTab);
    $('#statTotal, #totalRowCount').text(items.length);
    $('#statDigunakan').text(items.filter(i => i.status === 'Digunakan').length);
    $('#statTersedia').text(items.filter(i => i.status === 'Tersedia').length);
    $('#statMaintenance').text(items.filter(i => i.status === 'Maintenance' || i.status === 'Rusak').length);
}

// Buka Modal Scan QR
function openScanQrModal() {
    resetToScannerView();
    $('#modalScanQr').modal('show');
}

// Reset kembali ke tampilan Scanner Kamera
function resetToScannerView() {
    currentScannedAssetCode = null;
    $('#manualBarcodeInput').val('');
    $('#scanStepResult, #scanStepActionForm').addClass('d-none');
    $('#scanStepCamera').removeClass('d-none');
    $('#modalScanSubtitle').text('Arahkan kamera ke label barcode barang');
}

// Meminta Izin Kamera Browser & Menyalakan Stream Scanner (Mendukung QR Code 2D & Barcode Garis 1D di Semua Browser)
async function startCameraScanner() {
    const errorEl = $('#cameraErrorMsg');
    errorEl.addClass('d-none').text('');

    $('#btnRequestCamera').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Meminta Izin Kamera...');

    // Gunakan Html5Qrcode (mendukung QR Code + Barcode Code-128 di Chrome, Edge, Firefox, Safari, Android & iOS)
    if (typeof Html5Qrcode !== 'undefined') {
        try {
            $('#cameraPermissionPrompt').addClass('d-none');
            $('#cameraViewportWrapper').removeClass('d-none');

            if (html5QrScannerInstance) {
                try { await html5QrScannerInstance.stop(); } catch (e) {}
                html5QrScannerInstance = null;
            }

            html5QrScannerInstance = new Html5Qrcode('html5QrReader');
            await html5QrScannerInstance.start(
                { facingMode: 'environment' },
                {
                    fps: 12,
                    qrbox: { width: 230, height: 150 },
                    aspectRatio: 1.333
                },
                (decodedText) => {
                    if (decodedText) {
                        handleScannedCode(decodedText);
                    }
                },
                () => {}
            );

            $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');
            return;
        } catch (err) {
            $('#cameraViewportWrapper').addClass('d-none');
            $('#cameraPermissionPrompt').removeClass('d-none');
            $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');
            errorEl.removeClass('d-none').text('Akses kamera ditolak atau tidak ditemukan. Anda juga bisa klik "Scan dari Foto/Gambar" atau gunakan tombol simulasi di bawah.');
            return;
        }
    }

    // Fallback Native Stream jika CDN Html5Qrcode belum termuat
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');
        errorEl.removeClass('d-none').text('Browser ini tidak mendukung akses kamera langsung. Silakan gunakan tombol Scan dari Foto/Gambar atau input kode di bawah.');
        return;
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            audio: false
        });

        activeVideoStream = stream;
        const videoEl = document.getElementById('qrVideoElement');
        videoEl.classList.remove('d-none');
        videoEl.srcObject = stream;
        await videoEl.play();

        $('#cameraPermissionPrompt').addClass('d-none');
        $('#cameraViewportWrapper').removeClass('d-none');
        $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');

        if ('BarcodeDetector' in window) {
            const detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'code_39', 'ean_13'] });
            barcodeDetectionInterval = setInterval(async () => {
                if (videoEl.readyState === videoEl.HAVE_ENOUGH_DATA) {
                    try {
                        const barcodes = await detector.detect(videoEl);
                        if (barcodes.length > 0 && barcodes[0].rawValue) {
                            handleScannedCode(barcodes[0].rawValue);
                        }
                    } catch (e) {}
                }
            }, 400);
        }
    } catch (err) {
        $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');
        errorEl.removeClass('d-none').text('Akses kamera ditolak atau perangkat kamera tidak ditemukan.');
    }
}

// Scan QR / Barcode dari File Gambar / Screenshot Label
async function scanFromUploadedImage(inputEl) {
    if (!inputEl.files || !inputEl.files[0]) return;
    const file = inputEl.files[0];
    const errorEl = $('#cameraErrorMsg');
    errorEl.addClass('d-none').text('');

    if (typeof Html5Qrcode !== 'undefined') {
        try {
            const fileScanner = new Html5Qrcode('html5QrReader');
            const decodedText = await fileScanner.scanFile(file, false);
            inputEl.value = '';
            handleScannedCode(decodedText);
            return;
        } catch (err) {
            inputEl.value = '';
            errorEl.removeClass('d-none').text('QR / Barcode tidak terbaca pada gambar tersebut. Pastikan gambar jelas dan tidak terpotong.');
            return;
        }
    }
}

// Matikan Kamera saat Modal Ditutup atau Selesai Scan
function stopCameraScanner() {
    if (html5QrScannerInstance) {
        try {
            html5QrScannerInstance.stop().then(() => {
                html5QrScannerInstance.clear();
                html5QrScannerInstance = null;
            }).catch(() => {
                html5QrScannerInstance = null;
            });
        } catch (e) {
            html5QrScannerInstance = null;
        }
    }
    if (barcodeDetectionInterval) {
        clearInterval(barcodeDetectionInterval);
        barcodeDetectionInterval = null;
    }
    if (activeVideoStream) {
        activeVideoStream.getTracks().forEach(track => track.stop());
        activeVideoStream = null;
    }
    const videoEl = document.getElementById('qrVideoElement');
    if (videoEl) {
        videoEl.srcObject = null;
        videoEl.classList.add('d-none');
    }
    $('#cameraViewportWrapper').addClass('d-none');
    $('#cameraPermissionPrompt').removeClass('d-none');
}

// Proses Kode QR / Barcode yang Di-scan
function handleScannedCode(rawCode) {
    if (!rawCode || !rawCode.trim()) return;
    let normalized = rawCode.trim().toUpperCase();

    const item = assetDatabase[normalized];
    if (!item) {
        if (typeof toastr !== 'undefined') {
            toastr.warning('Data barang dengan kode "' + rawCode + '" tidak ditemukan.');
        } else {
            alert('Data barang dengan kode "' + rawCode + '" tidak ditemukan.');
        }
        return;
    }

    stopCameraScanner();
    currentScannedAssetCode = normalized;
    renderScanResult(item);
}

// Tampilkan Detail Hasil Scan & Terakhir Ada di Siapa
function renderScanResult(item) {
    $('#scanStepCamera, #scanStepActionForm').addClass('d-none');
    $('#scanStepResult').removeClass('d-none');
    $('#modalScanSubtitle').text('Hasil pemindaian data barang & posisi terakhir');

    $('#scanResAvatar').text(getInitials(item.user));
    $('#scanResHolderUser').text(item.user);
    $('#scanResHolderDivisi').text(item.divisi);
    $('#scanResHolderCabang').text(item.cabang);
    $('#scanResStatusBadge').html(getStatusBadgeHtml(item.status));

    $('#scanValCabang').text(item.cabang);
    $('#scanValKategori').text(item.kategori);
    $('#scanValNama').text(item.nama);
    $('#scanValCode').text(item.code);
    $('#scanValStatus').html(getStatusBadgeHtml(item.status));
    $('#scanValDivisi').text(item.divisi);
    $('#scanValUser').text(item.user);
    $('#scanValDeskripsi').text(item.deskripsi || '-');
}

// Preview otomatis kode baru saat Kategori atau Cabang diubah di Modal Scan QR
function previewUpdatedScanCode() {
    if (!currentScannedAssetCode || !assetDatabase[currentScannedAssetCode]) return;
    const item = assetDatabase[currentScannedAssetCode];
    const newCode = generateAssetCode(item.noUrut, $('#inputScanKategori').val(), $('#inputScanCabang').val());
    $('#scanFormCodeBadge').text(newCode);
}

// Preview otomatis kode baru saat Kategori atau Cabang diubah di Modal Edit Barang
function previewUpdatedEditCode() {
    const origCode = $('#editAssetOriginalCode').val();
    if (!origCode || !assetDatabase[origCode]) return;
    const item = assetDatabase[origCode];
    const newCode = generateAssetCode(item.noUrut, $('#editAssetKategori').val(), $('#editAssetCabang').val());
    $('#editAssetCode').val(newCode);
}

// Buka Form Update Data / Repair Data di dalam Modal Scan QR
function openScanActionForm(mode) {
    if (!currentScannedAssetCode || !assetDatabase[currentScannedAssetCode]) return;
    const item = assetDatabase[currentScannedAssetCode];

    $('#scanActionMode').val(mode);
    $('#scanFormCodeBadge').text(item.code);
    $('#inputScanNama').val(item.nama);
    $('#inputScanCabang').val(item.cabang);
    $('#inputScanKategori').val(item.kategori);
    $('#inputScanDivisi').val(item.divisi);
    $('#inputScanUser').val(item.user);
    $('#inputScanDeskripsi').val(item.deskripsi);

    if (mode === 'repair') {
        $('#scanFormTitle').html('<i class="fas fa-tools text-warning mr-1"></i> Repair Data Barang');
        $('#inputScanStatus').val('Maintenance');
        $('#labelScanDeskripsi').text('Catatan Kerusakan / Perbaikan (Repair)');
        $('#btnSubmitScanAction').removeClass('btn-primary').addClass('btn-warning text-dark').html('<i class="fas fa-tools mr-1"></i> Simpan Status Repair');
    } else {
        $('#scanFormTitle').html('<i class="fas fa-edit text-primary mr-1"></i> Update Data Barang');
        $('#inputScanStatus').val(item.status);
        $('#labelScanDeskripsi').text('Deskripsi / Spesifikasi');
        $('#btnSubmitScanAction').removeClass('btn-warning text-dark').addClass('btn-primary').html('<i class="fas fa-check mr-1"></i> Simpan Update');
    }

    $('#scanStepResult').addClass('d-none');
    $('#scanStepActionForm').removeClass('d-none');
}

function backToScanResult() {
    if (!currentScannedAssetCode || !assetDatabase[currentScannedAssetCode]) return;
    renderScanResult(assetDatabase[currentScannedAssetCode]);
}

// Sinkronisasi perubahan data barang & kode QR ke baris Tabel Utama
function syncAssetRowInTable(oldCode, item) {
    const checkbox = $(`.item-checkbox[value="${oldCode}"]`);
    const newCode = item.code;

    if (oldCode !== newCode) {
        assetDatabase[newCode] = item;
        delete assetDatabase[oldCode];
    } else {
        assetDatabase[newCode] = item;
    }

    if (checkbox.length) {
        const row = checkbox.closest('tr');
        checkbox.val(newCode);

        const newTabGroup = resolveAssetTabGroup(item.divisi);
        row.attr('data-asset-tab', newTabGroup).data('asset-tab', newTabGroup);
        row.attr('data-status', item.status).data('status', item.status);
        row.attr('data-divisi', item.divisi).data('divisi', item.divisi);
        row.attr('data-kategori', item.kategori).data('kategori', item.kategori);
        row.attr('data-cabang', item.cabang).data('cabang', item.cabang);

        const safeNama = (item.nama || '').replace(/'/g, "\\'");
        const safeUser = (item.user || '').replace(/'/g, "\\'");
        const safeDivisi = (item.divisi || '').replace(/'/g, "\\'");

        if (item.isQrGenerated) {
            row.find('td:eq(1)').html(`
                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('${newCode}', '${safeNama}', '${safeUser}', '${safeDivisi}')" title="Klik untuk lihat & cetak QR">
                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                        <i class="fas fa-qrcode text-primary mr-1"></i><span class="qr-code-text">${newCode}</span>
                    </span>
                </a>
            `);
        } else {
            row.find('td:eq(1)').html(`
                <button type="button" class="btn btn-xs btn-primary font-weight-bold px-2 py-1 shadow-xs" onclick="generateQrForAsset('${newCode}')" title="Klik untuk Generate QR Code">
                    <i class="fas fa-qrcode mr-1"></i> Generate QR
                </button>
            `);
        }

        row.find('td:eq(3) strong').text(item.nama);

        row.find('td:eq(4)').html(getStatusBadgeHtml(item.status));
        row.find('td:eq(5) .user-avatar-sm').text(getInitials(item.user));
        row.find('td:eq(5) strong').text(item.user);
        row.find('td:eq(6) span').text(item.divisi);
        row.find('td:eq(7) span').text(item.kategori);
        row.find('td:eq(8)').html('<i class="fas fa-building text-secondary mr-1"></i>' + item.cabang);
        row.find('td:eq(9) div').attr('title', item.deskripsi).text(item.deskripsi);

        const actionBtns = row.find('td:eq(10) button');
        $(actionBtns[0]).attr('onclick', `openDetailModal('${newCode}')`);
        $(actionBtns[1]).attr('onclick', `downloadAssetPdf('${newCode}')`);
        if (item.isQrGenerated) {
            $(actionBtns[2]).attr('title', 'Cetak Label QR').attr('onclick', `openQrModal('${newCode}', '${safeNama}', '${safeUser}', '${safeDivisi}')`);
        } else {
            $(actionBtns[2]).attr('title', 'Generate QR Code').attr('onclick', `generateQrForAsset('${newCode}')`);
        }
        $(actionBtns[3]).attr('onclick', `openEditAssetModal('${newCode}')`);
        $(actionBtns[4]).attr('onclick', `confirmDeleteAsset('${newCode}')`);
    }

    refreshStatCards();
}

// Generate QR Code saat tombol "Generate QR" diklik oleh user
function generateQrForAsset(code) {
    $.ajax({
        url: `${ASSET_UPDATE_URL_BASE}/${encodeURIComponent(code)}/generate-qr`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        success: function (res) {
            if (res && res.success && res.item) {
                const updatedItem = res.item;
                syncAssetRowInTable(res.old_code || code, updatedItem);

                if (typeof toastr !== 'undefined') {
                    toastr.success(`QR Code ${updatedItem.code} berhasil di-generate!`);
                }

                // Tampilkan modal preview QR Code yang baru di-generate
                openQrModal(updatedItem.code, updatedItem.nama, updatedItem.user, updatedItem.divisi);
            }
        },
        error: function () {
            if (typeof toastr !== 'undefined') {
                toastr.error('Gagal melakukan generate QR Code.');
            } else {
                alert('Gagal melakukan generate QR Code.');
            }
        }
    });
}

// Buka Modal Edit Barang dari Tombol Action Tabel
function openEditAssetModal(code) {
    const item = assetDatabase[code];
    if (!item) return;

    $('#editAssetOriginalCode').val(item.code);
    $('#editAssetCode').val(item.isQrGenerated ? item.code : 'Belum di-generate (Klik tombol Generate QR)');
    $('#editAssetNama').val(item.nama);
    $('#editAssetStatus').val(item.status);
    $('#editAssetUser').val(item.user);
    $('#editAssetDivisi').val(item.divisi);
    $('#editAssetKategori').val(item.kategori);
    $('#editAssetCabang').val(item.cabang);
    $('#editAssetMerk').val(item.merk && item.merk !== '-' ? item.merk : '');
    $('#editAssetModel').val(item.model && item.model !== '-' ? item.model : '');
    $('#editAssetProcessor').val(item.processor && item.processor !== '-' ? item.processor : '');
    $('#editAssetGenerasi').val(item.generasi && item.generasi !== '-' ? item.generasi : '');
    $('#editAssetRam').val(item.ram && item.ram !== '-' ? item.ram : '');
    $('#editAssetHarddisk').val(item.harddisk && item.harddisk !== '-' ? item.harddisk : '');
    $('#editAssetSerial').val(item.serial && item.serial !== '-' ? item.serial : '');
    $('#editAssetOs').val(item.os && item.os !== '-' ? item.os : '');
    $('#editAssetWarna').val(item.warna && item.warna !== '-' ? item.warna : '');
    $('#editAssetHarga').val(item.harga || '');
    $('#editAssetSupplier').val(item.supplier && item.supplier !== '-' ? item.supplier : '');
    $('#editAssetTanggal').val(item.tanggal || '');
    $('#editAssetDeskripsi').val(item.deskripsi && item.deskripsi !== '-' ? item.deskripsi : '');

    $('#modalEditBarang').modal('show');
}

// Preview perubahan Kode QR saat Cabang atau Kategori diubah pada Modal Edit Barang
function previewUpdatedEditCode() {
    const origCode = $('#editAssetOriginalCode').val();
    const item = assetDatabase[origCode];
    if (!item || !item.isQrGenerated) {
        $('#editAssetCode').val('Belum di-generate (Klik tombol Generate QR)');
        return;
    }

    const newKat = $('#editAssetKategori').val() || item.kategori;
    const newCab = $('#editAssetCabang').val() || item.cabang;

    const oldCleanKat = (item.kategori || 'AST').replace(/[^a-zA-Z]/g, '').toUpperCase();
    const oldSuffix = ((oldCleanKat.substring(0, 3) || 'AST').padEnd(3, 'X')) + formatBranchFullUppercase(item.cabang);

    const newCleanKat = (newKat || 'AST').replace(/[^a-zA-Z]/g, '').toUpperCase();
    const newSuffix = ((newCleanKat.substring(0, 3) || 'AST').padEnd(3, 'X')) + formatBranchFullUppercase(newCab);

    if (oldSuffix === newSuffix) {
        $('#editAssetCode').val(item.code);
        return;
    }

    // Hitung nomor urut (sequence) berikutnya untuk kombinasi Kategori + Cabang yang baru
    let maxSeq = 0;
    Object.values(assetDatabase).forEach(a => {
        if (a.isQrGenerated && a.id !== item.id && a.code && a.code.endsWith(newSuffix)) {
            const numPart = parseInt(a.code.substring(0, a.code.length - newSuffix.length), 10);
            if (!isNaN(numPart) && numPart > maxSeq) {
                maxSeq = numPart;
            }
        }
    });

    const nextSeqStr = String(maxSeq + 1).padStart(3, '0');
    $('#editAssetCode').val(`${nextSeqStr}${newSuffix}`);
}

// Simpan Perubahan dari Modal Edit Barang ke Database & Catat ke Riwayat Produk
function saveEditAssetModal(e) {
    e.preventDefault();
    const origCode = $('#editAssetOriginalCode').val();
    if (!origCode || !assetDatabase[origCode]) return;

    const payload = {
        _token: CSRF_TOKEN,
        _method: 'PUT',
        mode: 'edit',
        nama: $('#editAssetNama').val(),
        status: $('#editAssetStatus').val(),
        user: $('#editAssetUser').val(),
        divisi: $('#editAssetDivisi').val(),
        kategori: $('#editAssetKategori').val(),
        cabang: $('#editAssetCabang').val(),
        merk: $('#editAssetMerk').val() || '-',
        model: $('#editAssetModel').val() || '-',
        processor: $('#editAssetProcessor').val() || '-',
        generasi: $('#editAssetGenerasi').val() || '-',
        ram: $('#editAssetRam').val() || '-',
        harddisk: $('#editAssetHarddisk').val() || '-',
        serial: $('#editAssetSerial').val() || '-',
        os: $('#editAssetOs').val() || '-',
        warna: $('#editAssetWarna').val() || '-',
        harga: $('#editAssetHarga').val() || '0',
        supplier: $('#editAssetSupplier').val() || '-',
        deskripsi: $('#editAssetDeskripsi').val()
    };

    const btn = $('#btnSaveEditAsset');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.ajax({
        url: `${ASSET_UPDATE_URL_BASE}/${encodeURIComponent(origCode)}`,
        method: 'POST',
        data: payload,
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
            if (res && res.success && res.item) {
                syncAssetRowInTable(origCode, res.item);
                $('#modalEditBarang').modal('hide');
                const msg = 'Data & spesifikasi barang ' + res.item.code + ' berhasil disimpan ke database.';
                if (typeof toastr !== 'undefined') toastr.success(msg);
                else alert(msg);
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
            alert('Gagal menyimpan perubahan ke server.');
        }
    });
}

// Simpan Perubahan dari Form Update Data / Repair Data (Modal Scan QR) ke Database
function saveScanQuickAction(e) {
    e.preventDefault();
    if (!currentScannedAssetCode || !assetDatabase[currentScannedAssetCode]) return;

    const oldCode = currentScannedAssetCode;
    const mode = $('#scanActionMode').val();

    const payload = {
        _token: CSRF_TOKEN,
        _method: 'PUT',
        mode: mode,
        nama: $('#inputScanNama').val(),
        cabang: $('#inputScanCabang').val(),
        kategori: $('#inputScanKategori').val(),
        status: $('#inputScanStatus').val(),
        divisi: $('#inputScanDivisi').val(),
        user: $('#inputScanUser').val(),
        deskripsi: $('#inputScanDeskripsi').val()
    };

    const btn = $('#btnSubmitScanAction');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.ajax({
        url: `${ASSET_UPDATE_URL_BASE}/${encodeURIComponent(oldCode)}`,
        method: 'POST',
        data: payload,
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Simpan Perubahan');
            if (res && res.success && res.item) {
                syncAssetRowInTable(oldCode, res.item);
                currentScannedAssetCode = res.item.code;
                renderScanResult(res.item);

                const msg = mode === 'repair'
                    ? 'Data perbaikan (Repair) untuk ' + res.item.code + ' berhasil disimpan.'
                    : 'Data barang ' + res.item.code + ' berhasil diperbarui.';

                if (typeof toastr !== 'undefined') toastr.success(msg);
                else alert(msg);
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Simpan Perubahan');
            alert('Gagal menyimpan perubahan ke server.');
        }
    });
}

// Buka Modal Detail Lengkap (Spesifikasi + Riwayat Produk + Detail Repair)
function openDetailModal(code) {
    const item = assetDatabase[code];
    if (!item) return;

    $('#detQr').text(item.isQrGenerated ? item.code : 'Belum Generate QR');
    $('#detNama').text(item.nama);
    $('#detStatus').html(getStatusBadgeHtml(item.status));

    $('#detCabang').text(item.cabang || '-');
    $('#detKategori').text(item.kategori || '-');
    $('#detDivisi').text(item.divisi || '-');
    $('#detUser').text(item.user || '-');
    $('#detNamaSpec').text(item.nama || '-');
    $('#detStatusSpec').html(getStatusBadgeHtml(item.status));
    $('#detMerk').text(item.merk || '-');
    $('#detProcessor').text(item.processor || '-');
    $('#detModel').text(item.model || '-');
    $('#detGenerasi').text(item.generasi || '-');
    $('#detRam').text(item.ram || '-');
    $('#detHarddisk').text(item.harddisk || '-');
    $('#detSerial').text(item.serial || '-');
    $('#detOs').text(item.os || '-');
    $('#detWarna').text(item.warna || '-');
    $('#detHarga').text(item.harga || '-');
    $('#detSupplier').text(item.supplier || '-');
    $('#detTanggal').text(item.tanggal || '-');
    $('#detDeskripsi').text(item.deskripsi || '-');

    // Render Riwayat Produk (Interactive Monthly Roadmap & Collapsible Timeline)
    renderHistoryRoadmap(item.history || []);

    // Render Detail Repair (Visual Timeline Cards)
    renderRepairRoadmap(item.repairs || []);

    $('#btnModalDownloadPdf').attr('onclick', `downloadAssetPdf('${item.code}')`);
    $('#modalDetailBarang').modal('show');
}

// Helper: Ekstrak Bulan & Tahun dari string waktu (misal: "29 Okt 2026 14:10" -> { key: "2026-10", label: "Oktober 2026", shortLabel: "Okt 2026" })
function parseMonthFromWaktu(waktuStr) {
    const monthMap = {
        'jan': { num: '01', full: 'Januari', short: 'Jan' },
        'feb': { num: '02', full: 'Februari', short: 'Feb' },
        'mar': { num: '03', full: 'Maret', short: 'Mar' },
        'apr': { num: '04', full: 'April', short: 'Apr' },
        'mei': { num: '05', full: 'Mei', short: 'Mei' },
        'may': { num: '05', full: 'Mei', short: 'Mei' },
        'jun': { num: '06', full: 'Juni', short: 'Jun' },
        'jul': { num: '07', full: 'Juli', short: 'Jul' },
        'agu': { num: '08', full: 'Agustus', short: 'Agu' },
        'aug': { num: '08', full: 'Agustus', short: 'Agu' },
        'sep': { num: '09', full: 'September', short: 'Sep' },
        'okt': { num: '10', full: 'Oktober', short: 'Okt' },
        'oct': { num: '10', full: 'Oktober', short: 'Okt' },
        'nov': { num: '11', full: 'November', short: 'Nov' },
        'des': { num: '12', full: 'Desember', short: 'Des' },
        'dec': { num: '12', full: 'Desember', short: 'Des' }
    };

    if (!waktuStr || waktuStr === '-') {
        return { key: '0000-00', label: 'Waktu Tidak Diketahui', shortLabel: 'Lainnya' };
    }

    const parts = String(waktuStr).trim().split(/\s+/);
    if (parts.length >= 3) {
        const mKey = parts[1].toLowerCase().substring(0, 3);
        const year = parts[2];
        if (monthMap[mKey] && /^\d{4}$/.test(year)) {
            return {
                key: `${year}-${monthMap[mKey].num}`,
                label: `${monthMap[mKey].full} ${year}`,
                shortLabel: `${monthMap[mKey].short} ${year}`
            };
        }
    }

    return { key: '9999-99', label: 'Riwayat Aktivitas', shortLabel: 'Riwayat' };
}

// Helper: Warna & Ikon berdasarkan jenis Aksi pada Roadmap
function getRoadmapActionTheme(aksi) {
    const lower = String(aksi || '').toLowerCase();
    if (lower.includes('diterima') || lower.includes('registrasi') || lower.includes('po')) {
        return { bg: '#10b981', badgeBg: '#dcfce7', badgeColor: '#15803d', icon: 'fa-box-open' };
    }
    if (lower.includes('repair') || lower.includes('perbaikan') || lower.includes('maintenance') || lower.includes('rusak')) {
        return { bg: '#f59e0b', badgeBg: '#fef3c7', badgeColor: '#b45309', icon: 'fa-tools' };
    }
    if (lower.includes('edit') || lower.includes('spesifikasi')) {
        return { bg: '#6366f1', badgeBg: '#e0e7ff', badgeColor: '#4338ca', icon: 'fa-sliders-h' };
    }
    return { bg: '#2563eb', badgeBg: '#dbeafe', badgeColor: '#1d4ed8', icon: 'fa-exchange-alt' };
}

// Render Riwayat Produk Minimalis (Rangkuman Per Bulan Collapsible + Garis Timeline Sederhana)
function renderHistoryRoadmap(historyList) {
    const containerEl = $('#detHistoryRoadmapContainer');
    containerEl.empty();

    const totalCount = Array.isArray(historyList) ? historyList.length : 0;
    if (totalCount === 0) {
        containerEl.html(`<div class="text-muted py-2" style="font-size: 0.95rem;">Belum ada riwayat produk.</div>`);
        return;
    }

    // Kelompokkan per bulan
    const monthGroupsMap = {};
    historyList.forEach(h => {
        const mInfo = parseMonthFromWaktu(h.waktu);
        if (!monthGroupsMap[mInfo.key]) {
            monthGroupsMap[mInfo.key] = {
                key: mInfo.key,
                label: mInfo.label,
                items: []
            };
        }
        monthGroupsMap[mInfo.key].items.push(h);
    });

    const sortedGroups = Object.values(monthGroupsMap).sort((a, b) => b.key.localeCompare(a.key));

    sortedGroups.forEach((group, groupIdx) => {
        const isOpenDefault = groupIdx === 0;
        const itemsInMonth = [...group.items].reverse();

        const stepsHtml = itemsInMonth.map(h => {
            const theme = getRoadmapActionTheme(h.aksi);
            return `
                <div class="roadmap-step-item">
                    <div class="roadmap-step-dot" style="background-color: ${theme.bg};"></div>
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <strong class="text-dark" style="font-size: 1rem;">${h.aksi}</strong>
                        <span class="text-muted" style="font-size: 0.9rem;">${h.waktu}</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.93rem;">
                        ${h.cabang || '-'} &bull; User: <strong class="text-dark">${h.user || '-'}</strong> &bull; PIC: <strong class="text-dark">${h.pic || '-'}</strong>
                    </div>
                    ${h.deskripsi && h.deskripsi !== '-' ? `<div class="text-dark mt-1" style="font-size: 0.94rem;">${h.deskripsi}</div>` : ''}
                </div>
            `;
        }).join('');

        containerEl.append(`
            <div class="roadmap-month-group ${isOpenDefault ? 'is-open' : ''}" data-month-group="${group.key}">
                <div class="roadmap-month-header" onclick="toggleRoadmapMonth('${group.key}')">
                    <strong class="text-dark" style="font-size: 1.02rem;">${group.label}</strong>
                    <div class="d-flex align-items-center">
                        <span class="text-muted mr-2" style="font-size: 0.92rem;">${group.items.length} riwayat</span>
                        <i class="fas fa-chevron-down roadmap-chevron"></i>
                    </div>
                </div>
                <div class="roadmap-month-body" style="${isOpenDefault ? '' : 'display: none;'}">
                    <div class="roadmap-timeline-track">
                        ${stepsHtml}
                    </div>
                </div>
            </div>
        `);
    });
}

// Buka/tutup (collapse) 1 grup bulan pada Roadmap
function toggleRoadmapMonth(monthKey) {
    const groupEl = $(`.roadmap-month-group[data-month-group="${monthKey}"]`);
    if (!groupEl.length) return;
    const bodyEl = groupEl.find('.roadmap-month-body');
    if (groupEl.hasClass('is-open')) {
        groupEl.removeClass('is-open');
        bodyEl.slideUp(150);
    } else {
        groupEl.addClass('is-open');
        bodyEl.slideDown(150);
    }
}

// Render Detail Repair Minimalis
function renderRepairRoadmap(repairsList) {
    const containerEl = $('#detRepairRoadmapContainer');
    containerEl.empty();

    const totalRepair = Array.isArray(repairsList) ? repairsList.length : 0;
    if (totalRepair === 0) {
        containerEl.html(`<div class="text-muted py-1" style="font-size: 0.95rem;">Belum ada riwayat perbaikan (repair).</div>`);
        return;
    }

    const itemsHtml = repairsList.map(r => `
        <div class="roadmap-step-item">
            <div class="roadmap-step-dot" style="background-color: #f59e0b;"></div>
            <div class="d-flex justify-content-between align-items-baseline mb-1">
                <strong class="text-dark" style="font-size: 1rem;">Perbaikan / Repair</strong>
                <span class="text-muted" style="font-size: 0.9rem;">${r.waktu}</span>
            </div>
            <div class="text-dark mt-1" style="font-size: 0.94rem;">${r.deskripsi || '-'}</div>
        </div>
    `).join('');

    containerEl.html(`<div class="pl-2 pt-1">${itemsHtml}</div>`);
}

// Helper: Generate Base64 Data URL dari 2D QR Code agar 100% muncul tajam di dalam PDF (html2pdf)
function generateQrDataUrl(text, size = 140) {
    if (!text || typeof QRCode === 'undefined') return '';
    const tempDiv = document.createElement('div');
    new QRCode(tempDiv, {
        text: String(text),
        width: size,
        height: size,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
    const canvas = tempDiv.querySelector('canvas');
    if (canvas && typeof canvas.toDataURL === 'function') {
        return canvas.toDataURL('image/png');
    }
    const img = tempDiv.querySelector('img');
    return img ? img.src : '';
}

// Download Data Spesifikasi, Riwayat Produk & Detail Repair ke PDF (Dilengkapi Kop Surat & Label QR PT. Pangan Lestari)
function downloadAssetPdf(code) {
    const item = assetDatabase[code];
    if (!item) return;

    const branchUpper = formatBranchFullUppercase(item.cabang || '');
    const itemShortLabel = (item.nama || item.kategori || 'ASET').toUpperCase();
    const effectiveQrCode = item.isQrGenerated
        ? item.code
        : generateAssetCode(item.noUrut || 1, item.kategori, item.cabang);
    const qrDataUrl = generateQrDataUrl(effectiveQrCode, 140);

    const qrStickerBlockHtml = qrDataUrl
        ? `
            <div style="margin-bottom: 18px; display: flex; justify-content: flex-start;">
                <div style="display: inline-flex; align-items: center; gap: 18px; padding: 12px 18px; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff;">
                    <div style="text-align: center; font-family: Arial, Helvetica, sans-serif; font-weight: 700; font-size: 13px; line-height: 1.35; color: #000000; min-width: 115px;">
                        <div>PT Pangan</div>
                        <div>Lestari</div>
                        <div>Cabang</div>
                        <div style="text-transform: uppercase;">${branchUpper}</div>
                        <div style="text-transform: uppercase;">${itemShortLabel}</div>
                    </div>
                    <div>
                        <img src="${qrDataUrl}" alt="QR ${effectiveQrCode}" style="width: 115px; height: 115px; display: block;" />
                    </div>
                </div>
            </div>
        `
        : '';

    const historyRows = (item.history && item.history.length > 0)
        ? item.history.map((h, i) => `
            <tr>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;">${i + 1}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${h.waktu}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${h.pic}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${h.cabang}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1; font-weight: 600;">${h.user}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${h.aksi}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${h.deskripsi}</td>
            </tr>
        `).join('')
        : `<tr><td colspan="7" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; color: #64748b;">Belum ada riwayat produk</td></tr>`;

    const repairRows = (item.repairs && item.repairs.length > 0)
        ? item.repairs.map((r, i) => `
            <tr>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;">${i + 1}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${r.waktu}</td>
                <td style="padding: 6px 8px; border: 1px solid #cbd5e1;">${r.deskripsi}</td>
            </tr>
        `).join('')
        : `<tr><td colspan="3" style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; color: #64748b;">Belum ada data repair</td></tr>`;

    const pdfHtml = `
        <div style="font-family: Arial, Helvetica, sans-serif; color: #1e293b; padding: 14px 18px; font-size: 12px; line-height: 1.45; background: #ffffff;">
            <!-- Kop Surat Resmi PT. Pangan Lestari -->
            <div style="border-bottom: 3px double #0f172a; padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <img src="${KOP_SURAT_LOGO}" alt="PT. Pangan Lestari" style="height: 56px; width: auto; object-fit: contain; display: block;" />
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a; letter-spacing: 0.03em; text-transform: uppercase;">
                        Kartu Spesifikasi &amp; Riwayat Aset
                    </div>
                    <div style="margin-top: 4px;">
                        <span style="font-size: 12px; font-weight: 700; font-family: monospace; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 3px 10px; border-radius: 4px; color: #1e293b;">
                            KODE: ${item.isQrGenerated ? item.code : 'BELUM GENERATE QR'}
                        </span>
                    </div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Dicetak: ${getCurrentTimestampStr()}</div>
                </div>
            </div>

            ${qrStickerBlockHtml}

            <!-- 1. Download Spesifikasi -->
            <div style="font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">
                1. Spesifikasi Barang
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 11.5px;">
                <tbody>
                    <tr>
                        <td style="width: 22%; padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Cabang</td>
                        <td style="width: 28%; padding: 5px 8px; border: 1px solid #cbd5e1;">${item.cabang || '-'}</td>
                        <td style="width: 22%; padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">RAM</td>
                        <td style="width: 28%; padding: 5px 8px; border: 1px solid #cbd5e1;">${item.ram || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Kategori</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.kategori || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Harddisk</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.harddisk || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Divisi</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.divisi || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Serial</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-family: monospace;">${item.serial || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">User</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: 700;">${item.user || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Sistem Operasi</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.os || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Nama Barang</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: 600;">${item.nama || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Warna</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.warna || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Status</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: 600;">${item.status || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Harga</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; font-weight: 600;">${item.harga || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Merk</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.merk || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Supplier</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.supplier || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Processor</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.processor || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Tanggal</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.tanggal || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Model</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.model || '-'}</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Generasi</td>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.generasi || '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 600;">Deskripsi</td>
                        <td colspan="3" style="padding: 5px 8px; border: 1px solid #cbd5e1;">${item.deskripsi || '-'}</td>
                    </tr>
                </tbody>
            </table>

            <!-- 2. Riwayat Produk -->
            <div style="font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">
                2. Riwayat Produk
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 11px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="width: 32px; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;">No</th>
                        <th style="width: 115px; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Waktu</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Yang Bersangkutan</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Cabang</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">User</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Aksi</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    ${historyRows}
                </tbody>
            </table>

            <!-- 3. Detail Repair -->
            <div style="font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #0f172a; text-transform: uppercase;">
                3. Detail Repair
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="width: 32px; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: center;">No.</th>
                        <th style="width: 130px; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Waktu</th>
                        <th style="padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    ${repairRows}
                </tbody>
            </table>
        </div>
    `;

    const filename = `Spesifikasi_Aset_${item.code}.pdf`;

    if (typeof html2pdf !== 'undefined') {
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = pdfHtml;
        const opt = {
            margin:       10,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(tempContainer).save();
    } else {
        const printWin = window.open('', '_blank', 'width=900,height=700');
        printWin.document.write(`<html><head><title>${filename}</title></head><body>${pdfHtml}<script>window.onload=function(){window.print();}<\/script></body></html>`);
        printWin.document.close();
    }
}

$(document).ready(function() {
    // Pastikan kamera selalu dimatikan saat modal ditutup
    $('#modalScanQr').on('hidden.bs.modal', function () {
        stopCameraScanner();
    });

    // 1. Select All Checkbox Handler
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.item-checkbox:visible').prop('checked', isChecked);
        updateBatchBar();
    });

    // 2. Individual Checkbox Change
    $(document).on('change', '.item-checkbox', function() {
        const totalVisible = $('.item-checkbox:visible').length;
        const totalChecked = $('.item-checkbox:visible:checked').length;
        $('#selectAllCheckbox').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
        updateBatchBar();
    });

    function updateBatchBar() {
        const count = $('.item-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#batchActionBar').removeClass('d-none').addClass('d-flex');
        } else {
            $('#batchActionBar').addClass('d-none').removeClass('d-flex');
        }
    }

    // 3. Client-side Realtime Filtering
    function applyFilters() {
        const searchVal = ($('#searchInput').val() || '').toLowerCase();
        const statusVal = $('#filterStatus').val();
        const divisiVal = $('#filterDivisi').val();
        const kategoriVal = $('#filterKategori').val();
        const cabangVal = $('#filterCabang').val();

        let visibleCount = 0;

        $('#assetsTable tbody tr').each(function() {
            if ($(this).find('td').length <= 1) return;
            const rowText = $(this).text().toLowerCase();
            const rowTab = $(this).attr('data-asset-tab') || 'GA';
            const rowStatus = $(this).attr('data-status');
            const rowDivisi = $(this).attr('data-divisi');
            const rowKategori = $(this).attr('data-kategori');
            const rowCabang = $(this).attr('data-cabang');

            const matchTab = !activeAssetTab || rowTab === activeAssetTab;
            const matchSearch = !searchVal || rowText.indexOf(searchVal) > -1;
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchDivisi = !divisiVal || rowDivisi === divisiVal;
            const matchKategori = !kategoriVal || rowKategori === kategoriVal;
            const matchCabang = !cabangVal || rowCabang === cabangVal;

            if (matchTab && matchSearch && matchStatus && matchDivisi && matchKategori && matchCabang) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
                $(this).find('.item-checkbox').prop('checked', false);
            }
        });

        $('#visibleRowCount').text(visibleCount);

        const totalVisible = $('.item-checkbox:visible').length;
        const totalChecked = $('.item-checkbox:visible:checked').length;
        $('#selectAllCheckbox').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
        updateBatchBar();
    }

    window.triggerAssetFilter = applyFilters;

    $('#searchInput').on('keyup', applyFilters);
    $('#filterStatus, #filterDivisi, #filterKategori, #filterCabang').on('change', applyFilters);

    $('#btnResetFilter').on('click', function() {
        $('#searchInput').val('');
        $('#filterStatus, #filterDivisi, #filterKategori, #filterCabang').val('');
        applyFilters();
    });

    // Pilih tab awal yang memiliki data (default GA, jika kosong cek MIS / OTHER)
    const allItemsInit = Object.values(assetDatabase);
    const hasGA = allItemsInit.some(i => resolveAssetTabGroup(i.divisi) === 'GA');
    const hasMIS = allItemsInit.some(i => resolveAssetTabGroup(i.divisi) === 'MIS');
    const hasOTHER = allItemsInit.some(i => resolveAssetTabGroup(i.divisi) === 'OTHER');

    if (!hasGA && hasMIS) {
        switchAssetTab('MIS');
    } else if (!hasGA && !hasMIS && hasOTHER) {
        switchAssetTab('OTHER');
    } else {
        switchAssetTab('GA');
    }
});

// Modal QR Code Asli Preview + Print Trigger
function openQrModal(code, name, user, divisi) {
    const item = assetDatabase[code] || {};
    const branchUpper = formatBranchFullUppercase(item.cabang || '');
    const itemLabel = (name || item.nama || item.kategori || 'ASET').toUpperCase();

    $('#modalQrCodeText').text(code);
    $('#modalQrBranchText').text(branchUpper);
    $('#modalQrItemName').text(itemLabel);
    $('#modalQrUserName').text(user || item.user || '-');
    $('#modalQrDivisi').text(divisi || item.divisi || '-');

    // Render Real 2D QR Code (Mengandung teks kode persis seperti 001ELESIDOARJO)
    const qrContainer = document.getElementById('realQrCodeContainer');
    if (qrContainer) {
        qrContainer.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: code,
                width: 145,
                height: 145,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    }

    $('#modalQrPreview').modal('show');
}

// Download PDF Label QR sesuai desain stiker (Teks PT Pangan Lestari Cabang di kiri + QR di kanan)
function downloadQrLabelPdf() {
    const code = $('#modalQrCodeText').text().trim();
    const branchText = $('#modalQrBranchText').text().trim() || 'JAKARTA';
    const itemNameText = $('#modalQrItemName').text().trim() || 'ASET';
    const qrDataUrl = generateQrDataUrl(code, 160);

    const stickerHtml = `
        <div style="background: #ffffff; padding: 14px 18px; display: flex; align-items: center; justify-content: center; gap: 20px; font-family: Arial, Helvetica, sans-serif; width: 340px;">
            <div style="text-align: center; font-weight: 700; font-size: 15px; line-height: 1.35; color: #000000; min-width: 120px;">
                <div>PT Pangan</div>
                <div>Lestari</div>
                <div>Cabang</div>
                <div style="text-transform: uppercase;">${branchText}</div>
                <div style="text-transform: uppercase;">${itemNameText}</div>
            </div>
            <div>
                <img src="${qrDataUrl}" alt="${code}" style="width: 135px; height: 135px; display: block;" />
            </div>
        </div>
    `;

    const filename = `Label_QR_${code}.pdf`;
    if (typeof html2pdf !== 'undefined') {
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = stickerHtml;
        const opt = {
            margin:       4,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.99 },
            html2canvas:  { scale: 3, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: [95, 55], orientation: 'landscape' }
        };
        html2pdf().set(opt).from(tempContainer).save();
    } else {
        window.print();
    }
}

// Reset QR Code kembali menjadi tombol Generate QR
function resetCurrentQrCode() {
    const code = $('#modalQrCodeText').text().trim();
    if (!code) return;

    $.ajax({
        url: `${ASSET_UPDATE_URL_BASE}/${encodeURIComponent(code)}/reset-qr`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        success: function (res) {
            if (res && res.success && res.item) {
                $('#modalQrPreview').modal('hide');
                syncAssetRowInTable(res.old_code || code, res.item);
                if (typeof toastr !== 'undefined') {
                    toastr.info('QR Code berhasil di-reset kembali ke tombol Generate QR.');
                }
            }
        },
        error: function () {
            alert('Gagal mereset QR Code.');
        }
    });
}

// Langsung Tes Scan Kode dari Modal Label QR
function testScanCurrentLabel() {
    const code = $('#modalQrCodeText').text().trim();
    $('#modalQrPreview').modal('hide');
    setTimeout(() => {
        $('#modalScanQr').modal('show');
        handleScannedCode(code);
    }, 300);
}

function confirmDeleteAsset(code) {
    if (!confirm('Apakah Anda yakin ingin menghapus data barang ' + code + '?')) return;

    $.ajax({
        url: `${ASSET_UPDATE_URL_BASE}/${encodeURIComponent(code)}`,
        method: 'POST',
        data: {
            _token: CSRF_TOKEN,
            _method: 'DELETE'
        },
        success: function(res) {
            if (res && res.success) {
                const checkbox = $(`.item-checkbox[value="${code}"]`);
                if (checkbox.length) {
                    checkbox.closest('tr').remove();
                }
                delete assetDatabase[code];
                refreshStatCards();
                $('#visibleRowCount').text($('.item-checkbox:visible').length);
                if (typeof toastr !== 'undefined') toastr.success(res.message || 'Data barang berhasil dihapus.');
                else alert(res.message || 'Data barang berhasil dihapus.');
            }
        },
        error: function() {
            alert('Gagal menghapus data barang dari server.');
        }
    });
}

function bulkPrintQR() {
    const selected = [];
    $('.item-checkbox:checked').each(function() {
        selected.push($(this).val());
    });
    alert('Mencetak label QR untuk ' + selected.length + ' barang terpilih:\n' + selected.join(', '));
}
</script>
@endpush
