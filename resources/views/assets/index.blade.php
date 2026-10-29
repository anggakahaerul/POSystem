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
                        <h3 class="mb-0 font-weight-bold" id="statTotal">128</h3>
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
                        <h3 class="mb-0 font-weight-bold text-success" id="statDigunakan">94</h3>
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
                        <h3 class="mb-0 font-weight-bold text-info" id="statTersedia">26</h3>
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
                        <h3 class="mb-0 font-weight-bold text-warning" id="statMaintenance">8</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-dolly-flatbed text-primary mr-2"></i>Daftar Barang &amp; Aset Perusahaan
                    </h3>
                    <small class="text-muted d-block mt-1">Kelola data inventaris, tracking QR code, status penggunaan, dan PIC divisi</small>
                </div>
                <div class="d-flex flex-wrap align-items-center mt-2 mt-sm-0" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="openScanQrModal()">
                        <i class="fas fa-qrcode mr-1"></i> Scan QR
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="alert('Fitur Export Data Excel (Mockup)')">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
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
                        <input type="text" id="searchInput" class="form-control border-left-0" placeholder="Cari nama, QR, serial number...">
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
                        <option value="IT &amp; System">IT &amp; System</option>
                        <option value="Finance &amp; Tax">Finance &amp; Tax</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Operasional Logistik">Operasional Logistik</option>
                        <option value="Marketing &amp; Sales">Marketing &amp; Sales</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <select id="filterKategori" class="form-control form-control-sm">
                        <option value="">Semua Kategori</option>
                        <option value="Elektronik &amp; IT">Elektronik &amp; IT</option>
                        <option value="Kendaraan">Kendaraan</option>
                        <option value="Furniture &amp; Kantor">Furniture &amp; Kantor</option>
                        <option value="Mesin &amp; Peralatan">Mesin &amp; Peralatan</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <select id="filterCabang" class="form-control form-control-sm">
                        <option value="">Semua Cabang</option>
                        <option value="Head Office (Jakarta)">Head Office (Jakarta)</option>
                        <option value="Cabang Surabaya">Cabang Surabaya</option>
                        <option value="Cabang Bandung">Cabang Bandung</option>
                        <option value="DC Cikarang">DC Cikarang</option>
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
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="alert('Fitur Mutasi / Update Status Massal')">
                        <i class="fas fa-exchange-alt mr-1"></i> Update Status / Mutasi
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="alert('Fitur Hapus Massal')">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
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
                            <th class="text-center" style="width: 100px;">QR</th>
                            <th style="min-width: 200px;">Nama Barang</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th style="min-width: 150px;">User</th>
                            <th style="min-width: 140px;">Divisi</th>
                            <th style="min-width: 140px;">Kategori</th>
                            <th style="min-width: 150px;">Cabang</th>
                            <th style="min-width: 220px;">Deskripsi</th>
                            <th class="text-center" style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.88rem;">
                        <!-- Row 1 -->
                        <tr data-status="Digunakan" data-divisi="IT &amp; System" data-kategori="Elektronik &amp; IT" data-cabang="Head Office (Jakarta)">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_1" value="AST-2026-001">
                                    <label class="custom-control-label" for="check_1"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-001', 'Apple MacBook Pro 14 M2 Pro', 'Budi Santoso', 'IT &amp; System')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-001
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Apple MacBook Pro 14" M2 Pro</strong>
                                <small class="text-muted"><i class="fas fa-barcode mr-1"></i>S/N: C02XG124J058 | Brand: Apple</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-success px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-check-circle mr-1"></i>Digunakan
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-primary text-white">BS</div>
                                    <div>
                                        <strong class="d-block text-dark">Budi Santoso</strong>
                                        <small class="text-muted">budi.s@panganlestari.com</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">IT &amp; System</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Elektronik &amp; IT</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-building text-secondary mr-1"></i>Head Office (Jakarta)
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Chip M2 Pro 10-core, RAM 16GB, SSD 512GB Space Grey. Pembelian PO-2026-004">
                                    Chip M2 Pro 10-core, RAM 16GB, SSD 512GB Space Grey. Pembelian PO-2026-004
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-001')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-001')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-001', 'Apple MacBook Pro 14 M2 Pro', 'Budi Santoso', 'IT &amp; System')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-001')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-001')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2 -->
                        <tr data-status="Tersedia" data-divisi="Human Resources" data-kategori="Furniture &amp; Kantor" data-cabang="Head Office (Jakarta)">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_2" value="AST-2026-002">
                                    <label class="custom-control-label" for="check_2"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-002', 'Kursi Kerja Ergonomis Mesh', 'Pool HR Standby', 'Human Resources')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-002
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Kursi Kerja Ergonomis Mesh Office</strong>
                                <small class="text-muted"><i class="fas fa-tag mr-1"></i>Model: ErgoPlus V2 | Brand: Indachi</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-info px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-box mr-1"></i>Tersedia
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-secondary text-white">HR</div>
                                    <div>
                                        <strong class="d-block text-dark">Pool Standby GA</strong>
                                        <small class="text-muted">Siap dialokasikan</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">Human Resources</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Furniture &amp; Kantor</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-building text-secondary mr-1"></i>Head Office (Jakarta)
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Material breathable mesh, lumbar support adjustable, warna hitam. Kondisi 100% baru.">
                                    Material breathable mesh, lumbar support adjustable, warna hitam. Kondisi 100% baru.
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-002')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-002')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-002', 'Kursi Kerja Ergonomis Mesh', 'Pool HR Standby', 'Human Resources')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-002')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-002')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3 -->
                        <tr data-status="Digunakan" data-divisi="Operasional Logistik" data-kategori="Kendaraan" data-cabang="DC Cikarang">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_3" value="AST-2026-003">
                                    <label class="custom-control-label" for="check_3"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-003', 'Toyota Avanza 1.5 G MT Operasional', 'Agus Priyono', 'Operasional Logistik')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-003
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Toyota Avanza 1.5 G MT</strong>
                                <small class="text-muted"><i class="fas fa-car mr-1"></i>No. Polisi: B 2145 PGL | Th: 2024</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-success px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-check-circle mr-1"></i>Digunakan
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-success text-white">AP</div>
                                    <div>
                                        <strong class="d-block text-dark">Agus Priyono</strong>
                                        <small class="text-muted">Driver Operasional</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">Operasional Logistik</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Kendaraan</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-warehouse text-secondary mr-1"></i>DC Cikarang
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Mobil operasional antar cabang dan gudang logistik. Pajak STNK s/d Okt 2027. Service berkala rutin.">
                                    Mobil operasional antar cabang dan gudang logistik. Pajak STNK s/d Okt 2027. Service berkala rutin.
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-003')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-003')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-003', 'Toyota Avanza 1.5 G MT', 'Agus Priyono', 'Operasional Logistik')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-003')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-003')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 4 -->
                        <tr data-status="Maintenance" data-divisi="Finance &amp; Tax" data-kategori="Elektronik &amp; IT" data-cabang="Cabang Surabaya">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_4" value="AST-2026-004">
                                    <label class="custom-control-label" for="check_4"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-004', 'Printer Epson EcoTank L3210 All-in-One', 'Dewi Lestari', 'Finance &amp; Tax')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-004
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Printer Epson EcoTank L3210 All-in-One</strong>
                                <small class="text-muted"><i class="fas fa-barcode mr-1"></i>S/N: EPS-88192-SUB | Brand: Epson</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-warning text-dark px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-wrench mr-1"></i>Maintenance
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-info text-white">DL</div>
                                    <div>
                                        <strong class="d-block text-dark">Dewi Lestari</strong>
                                        <small class="text-muted">Staff Finance</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">Finance &amp; Tax</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Elektronik &amp; IT</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-building text-secondary mr-1"></i>Cabang Surabaya
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Kerusakan head print warna merah bergaris. Sedang dalam perbaikan service center resmi Epson Surabaya.">
                                    Kerusakan head print warna merah bergaris. Sedang dalam perbaikan service center.
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-004')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-004')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-004', 'Printer Epson EcoTank L3210', 'Dewi Lestari', 'Finance &amp; Tax')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-004')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-004')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 5 -->
                        <tr data-status="Dipinjam" data-divisi="Marketing &amp; Sales" data-kategori="Elektronik &amp; IT" data-cabang="Cabang Bandung">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_5" value="AST-2026-005">
                                    <label class="custom-control-label" for="check_5"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-005', 'Projector Epson EB-E01 3300 Lumens', 'Rian Hidayat', 'Marketing &amp; Sales')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-005
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Projector Epson EB-E01 3300 Lumens</strong>
                                <small class="text-muted"><i class="fas fa-barcode mr-1"></i>S/N: PROJ-BDG-2024 | Brand: Epson</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-primary px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-hand-holding mr-1"></i>Dipinjam
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-purple text-white" style="background-color: #8b5cf6;">RH</div>
                                    <div>
                                        <strong class="d-block text-dark">Rian Hidayat</strong>
                                        <small class="text-muted">Expo Event Client</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">Marketing &amp; Sales</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Elektronik &amp; IT</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-building text-secondary mr-1"></i>Cabang Bandung
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Peminjaman operasional demo produk food service di pameran Bandung Expo s/d 30 Okt 2026.">
                                    Peminjaman operasional demo produk food service di pameran Bandung Expo s/d 30 Okt.
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-005')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-005')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-005', 'Projector Epson EB-E01', 'Rian Hidayat', 'Marketing &amp; Sales')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-005')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-005')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 6 -->
                        <tr data-status="Rusak" data-divisi="Operasional Logistik" data-kategori="Mesin &amp; Peralatan" data-cabang="DC Cikarang">
                            <td class="text-center align-middle">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input item-checkbox" id="check_6" value="AST-2026-006">
                                    <label class="custom-control-label" for="check_6"></label>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <a href="javascript:void(0)" class="qr-badge-preview" onclick="openQrModal('AST-2026-006', 'Hand Pallet Truck 3 Ton', 'Gudang DC', 'Operasional Logistik')" title="Klik untuk lihat &amp; cetak QR">
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-family: monospace; font-size: 0.78rem;">
                                        <i class="fas fa-qrcode text-primary mr-1"></i>AST-006
                                    </span>
                                </a>
                            </td>
                            <td class="align-middle">
                                <strong class="text-dark d-block">Hand Pallet Truck Hydraulic 3.0 Ton</strong>
                                <small class="text-muted"><i class="fas fa-hashtag mr-1"></i>Model: OP-PL-3000 | Brand: Krisbow</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-danger px-2 py-1 font-weight-normal" style="font-size: 0.78rem;">
                                    <i class="fas fa-times-circle mr-1"></i>Rusak
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-sm mr-2 bg-danger text-white">GD</div>
                                    <div>
                                        <strong class="d-block text-dark">Pool Gudang DC</strong>
                                        <small class="text-muted">Unloading Bay</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1">Operasional Logistik</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-dark font-weight-500">Mesin &amp; Peralatan</span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-warehouse text-secondary mr-1"></i>DC Cikarang
                            </td>
                            <td class="align-middle text-muted small" style="max-width: 250px;">
                                <div class="text-truncate" title="Seal oli hidrolik jebol dan roda nilon retak. Menunggu persetujuan scrap / penggantian unit baru.">
                                    Seal oli hidrolik jebol dan roda retak. Menunggu persetujuan scrap unit baru.
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="action-btn-group">
                                    <button type="button" class="btn-action btn-action-info" title="Detail & Riwayat Barang" onclick="openDetailModal('AST-2026-006')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-pdf" title="Download PDF Spesifikasi & Riwayat" onclick="downloadAssetPdf('AST-2026-006')">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-006', 'Hand Pallet Truck 3 Ton', 'Gudang DC', 'Operasional Logistik')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="openEditAssetModal('AST-2026-006')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-danger" title="Hapus Barang" onclick="confirmDeleteAsset('AST-2026-006')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="card-footer bg-white py-3 border-top d-flex flex-wrap justify-content-between align-items-center">
            <div class="text-muted small mb-2 mb-md-0">
                Menampilkan <span class="font-weight-bold">1 - 6</span> dari <span class="font-weight-bold">128</span> total barang terdaftar
            </div>
            <div>
                <ul class="pagination pagination-sm m-0">
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Preview & Cetak Label QR -->
<div class="modal fade" id="modalQrPreview" tabindex="-1" role="dialog" aria-labelledby="modalQrTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalQrTitle">
                    <i class="fas fa-qrcode mr-2"></i>Label Asset QR Code
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <!-- Label Card Printable Mockup -->
                <div class="border rounded p-3 bg-white shadow-sm d-inline-block text-center w-100" id="printableQrLabel" style="border: 2px dashed #cbd5e1 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <strong class="text-primary text-uppercase font-weight-bold" style="font-size: 0.78rem; letter-spacing: 0.05em;">
                            PT. Pangan Lestari
                        </strong>
                        <span class="badge badge-dark" style="font-size: 0.68rem;">FIXED ASSET</span>
                    </div>

                    <!-- SVG QR Code Visual Demonstration -->
                    <div class="my-3 d-flex justify-content-center">
                        <div class="p-2 border rounded bg-white shadow-xs">
                            <svg width="150" height="150" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="100" height="100" fill="white"/>
                                <!-- QR Mock Pattern -->
                                <rect x="5" y="5" width="30" height="30" fill="black"/>
                                <rect x="9" y="9" width="22" height="22" fill="white"/>
                                <rect x="13" y="13" width="14" height="14" fill="black"/>

                                <rect x="65" y="5" width="30" height="30" fill="black"/>
                                <rect x="69" y="9" width="22" height="22" fill="white"/>
                                <rect x="73" y="13" width="14" height="14" fill="black"/>

                                <rect x="5" y="65" width="30" height="30" fill="black"/>
                                <rect x="9" y="69" width="22" height="22" fill="white"/>
                                <rect x="13" y="73" width="14" height="14" fill="black"/>

                                <rect x="42" y="8" width="6" height="6" fill="black"/>
                                <rect x="52" y="8" width="6" height="6" fill="black"/>
                                <rect x="42" y="20" width="6" height="6" fill="black"/>
                                <rect x="52" y="26" width="6" height="6" fill="black"/>
                                <rect x="8" y="42" width="6" height="6" fill="black"/>
                                <rect x="20" y="42" width="6" height="6" fill="black"/>
                                <rect x="30" y="42" width="6" height="6" fill="black"/>
                                <rect x="42" y="42" width="16" height="16" fill="black"/>
                                <rect x="65" y="42" width="6" height="6" fill="black"/>
                                <rect x="75" y="42" width="12" height="6" fill="black"/>
                                <rect x="42" y="65" width="6" height="12" fill="black"/>
                                <rect x="52" y="72" width="6" height="6" fill="black"/>
                                <rect x="65" y="65" width="16" height="6" fill="black"/>
                                <rect x="85" y="65" width="6" height="16" fill="black"/>
                                <rect x="65" y="77" width="8" height="8" fill="black"/>
                                <rect x="78" y="86" width="12" height="6" fill="black"/>
                                <rect x="42" y="86" width="16" height="6" fill="black"/>
                            </svg>
                        </div>
                    </div>

                    <h5 class="font-weight-bold text-dark mb-1" id="modalQrCodeText">AST-2026-001</h5>
                    <div class="text-truncate font-weight-bold text-slate-800 small" id="modalQrItemName">Nama Barang</div>
                    <div class="text-muted small mt-1">
                        PIC: <span id="modalQrUserName">-</span> (<span id="modalQrDivisi">-</span>)
                    </div>
                </div>

                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle mr-1 text-info"></i> Label ini dapat ditempelkan pada fisik unit barang/aset untuk keperluan audit &amp; stock opname.
                </div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak Label Ini
                </button>
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
                            <span class="badge badge-primary font-monospace" id="detQr">AST-2026-001</span>
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

                <!-- 2. Riwayat Produk -->
                <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom">
                    <i class="fas fa-history text-info mr-1"></i> 2. Riwayat Produk
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered table-striped mb-0" style="font-size: 0.82rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th style="width: 125px;">Waktu</th>
                                <th>Yang Bersangkutan</th>
                                <th>Cabang</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody id="detHistoryTableBody"></tbody>
                    </table>
                </div>

                <!-- 3. Detail Repair -->
                <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom">
                    <i class="fas fa-tools text-warning mr-1"></i> 3. Detail Repair
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped mb-0" style="font-size: 0.82rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No.</th>
                                <th style="width: 140px;">Waktu</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody id="detRepairTableBody"></tbody>
                    </table>
                </div>
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
                        <small class="text-muted">Perbarui informasi pemegang, lokasi cabang, dan spesifikasi teknis barang</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditBarang" onsubmit="saveEditAssetModal(event)">
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Kode QR / Aset</label>
                            <input type="text" id="editAssetCode" class="form-control form-control-sm font-monospace font-weight-bold" readonly style="background-color: #f8fafc;">
                        </div>

                        <div class="col-md-8 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="editAssetNama" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Cabang <span class="text-danger">*</span></label>
                            <select id="editAssetCabang" class="form-control form-control-sm" required>
                                <option value="Head Office (Jakarta)">Head Office (Jakarta)</option>
                                <option value="Cabang Surabaya">Cabang Surabaya</option>
                                <option value="Cabang Bandung">Cabang Bandung</option>
                                <option value="DC Cikarang">DC Cikarang</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Kategori <span class="text-danger">*</span></label>
                            <select id="editAssetKategori" class="form-control form-control-sm" required>
                                <option value="Elektronik & IT">Elektronik &amp; IT</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Furniture & Kantor">Furniture &amp; Kantor</option>
                                <option value="Mesin & Peralatan">Mesin &amp; Peralatan</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold small text-muted mb-1">Divisi <span class="text-danger">*</span></label>
                            <select id="editAssetDivisi" class="form-control form-control-sm" required>
                                <option value="IT & System">IT &amp; System</option>
                                <option value="Finance & Tax">Finance &amp; Tax</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Operasional Logistik">Operasional Logistik</option>
                                <option value="Marketing & Sales">Marketing &amp; Sales</option>
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
                            <input type="text" id="editAssetMerk" class="form-control form-control-sm" placeholder="Contoh: Apple / Dell">
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
                            <input type="text" id="editAssetGenerasi" class="form-control form-control-sm" placeholder="Contoh: Gen 13 / 2023">
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
                            <input type="text" id="editAssetTanggal" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 mb-1">
                            <label class="font-weight-bold small text-muted mb-1">Deskripsi</label>
                            <textarea id="editAssetDeskripsi" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light border btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold text-dark px-3">
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
                            Aplikasi membutuhkan izin kamera untuk memindai QR Code atau Barcode pada label barang.
                        </p>
                        <button type="button" class="btn btn-primary btn-sm font-weight-bold px-4 py-2 shadow-xs" id="btnRequestCamera" onclick="startCameraScanner()">
                            <i class="fas fa-video mr-1"></i> Izinkan &amp; Buka Kamera
                        </button>
                        <div id="cameraErrorMsg" class="text-danger small mt-2 d-none"></div>
                    </div>

                    <!-- Box Live Kamera (Muncul setelah kamera diizinkan) -->
                    <div id="cameraViewportWrapper" class="d-none">
                        <div class="scanner-viewport-box position-relative rounded overflow-hidden bg-dark mb-2" style="height: 250px;">
                            <video id="qrVideoElement" class="w-100 h-100" style="object-fit: cover;" playsinline muted></video>
                            <div id="html5QrReader" class="w-100 h-100"></div>
                            <!-- Frame Garis Fokus Scan -->
                            <div class="scanner-overlay-frame">
                                <div class="scanner-laser-line"></div>
                            </div>
                            <div class="position-absolute w-100 text-center" style="bottom: 10px; left: 0;">
                                <span class="badge badge-dark px-3 py-1" style="opacity: 0.8; font-weight: 500;">
                                    <i class="fas fa-crosshairs mr-1"></i> Posisikan QR / Barcode di dalam kotak
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Manual / Quick Simulasi Scan (Memudahkan Testing Tanpa Print QR) -->
                    <div class="mt-3 pt-3 border-top">
                        <label class="small font-weight-bold text-muted mb-1 d-block">
                            Atau masukkan kode / pilih simulasi hasil scan:
                        </label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="manualBarcodeInput" class="form-control" placeholder="Ketik kode QR (misal: AST-2026-001)...">
                            <div class="input-group-append">
                                <button class="btn btn-dark font-weight-bold" type="button" onclick="handleScannedCode($('#manualBarcodeInput').val())">
                                    Cek Data
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap" style="gap: 5px;">
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="handleScannedCode('AST-2026-001')">AST-001 (MacBook)</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="handleScannedCode('AST-2026-002')">AST-002 (Kursi)</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="handleScannedCode('AST-2026-003')">AST-003 (Avanza)</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="handleScannedCode('AST-2026-004')">AST-004 (Printer)</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="handleScannedCode('AST-2026-006')">AST-006 (Pallet)</button>
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
                                <small class="text-primary font-monospace" id="scanValCode">AST-2026-001</small>
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
                        <span class="badge badge-light border font-monospace" id="scanFormCodeBadge">AST-2026-001</span>
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
                                    <select id="inputScanCabang" class="form-control form-control-sm" required>
                                        <option value="Head Office (Jakarta)">Head Office (Jakarta)</option>
                                        <option value="Cabang Surabaya">Cabang Surabaya</option>
                                        <option value="Cabang Bandung">Cabang Bandung</option>
                                        <option value="DC Cikarang">DC Cikarang</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Kategori</label>
                                    <select id="inputScanKategori" class="form-control form-control-sm" required>
                                        <option value="Elektronik & IT">Elektronik &amp; IT</option>
                                        <option value="Kendaraan">Kendaraan</option>
                                        <option value="Furniture & Kantor">Furniture &amp; Kantor</option>
                                        <option value="Mesin & Peralatan">Mesin &amp; Peralatan</option>
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
                                        <option value="IT & System">IT &amp; System</option>
                                        <option value="Finance & Tax">Finance &amp; Tax</option>
                                        <option value="Human Resources">Human Resources</option>
                                        <option value="Operasional Logistik">Operasional Logistik</option>
                                        <option value="Marketing & Sales">Marketing &amp; Sales</option>
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
<!-- html2pdf.js untuk Download PDF langsung di sisi client -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Data Master Barang Aset Lengkap (Spesifikasi 19 Field + Riwayat Produk + Detail Repair)
const assetDatabase = {
    'AST-2026-001': {
        code: 'AST-2026-001',
        shortCode: 'AST-001',
        cabang: 'Head Office (Jakarta)',
        kategori: 'Elektronik & IT',
        divisi: 'IT & System',
        user: 'Budi Santoso',
        nama: 'Apple MacBook Pro 14" M2 Pro',
        status: 'Digunakan',
        merk: 'Apple',
        processor: 'Apple M2 Pro (10-Core CPU, 16-Core GPU)',
        model: 'MacBook Pro 14-inch (MPHE3ID/A)',
        generasi: '2023 (M2 Series)',
        ram: '16 GB Unified Memory',
        harddisk: '512 GB NVMe SSD',
        serial: 'C02XG124J058',
        os: 'macOS Sonoma 14.5',
        warna: 'Space Grey',
        harga: 'Rp 31.499.000',
        supplier: 'PT. Digimap Nusantara (Official Authorized)',
        tanggal: '12 Jan 2026',
        deskripsi: 'Chip M2 Pro 10-core, RAM 16GB, SSD 512GB Space Grey. Pembelian PO-2026-004',
        history: [
            { waktu: '12 Jan 2026 09:30', pic: 'Admin Gudang (GA)', cabang: 'Head Office (Jakarta)', user: 'Pool IT', aksi: 'Barang Diterima (PO)', deskripsi: 'Unit baru diterima dari PO-2026-004 dalam kondisi tersegel.' },
            { waktu: '14 Jan 2026 13:15', pic: 'Hendra Wijaya (IT)', cabang: 'Head Office (Jakarta)', user: 'Budi Santoso', aksi: 'Serah Terima / Alokasi', deskripsi: 'Instalasi software standar kerja dan serah terima ke PIC Budi Santoso.' }
        ],
        repairs: [
            { waktu: '10 Jun 2026 10:00', deskripsi: 'Pembersihan kipas internal (deep cleaning) & kalibrasi baterai rutin 6 bulanan.' }
        ]
    },
    'AST-2026-002': {
        code: 'AST-2026-002',
        shortCode: 'AST-002',
        cabang: 'Head Office (Jakarta)',
        kategori: 'Furniture & Kantor',
        divisi: 'Human Resources',
        user: 'Pool Standby GA',
        nama: 'Kursi Kerja Ergonomis Mesh Office',
        status: 'Tersedia',
        merk: 'Indachi',
        processor: '-',
        model: 'ErgoPlus V2 High-Back',
        generasi: '2025 Series',
        ram: '-',
        harddisk: '-',
        serial: 'IND-ERG-88210',
        os: '-',
        warna: 'Hitam (Matte Black)',
        harga: 'Rp 2.850.000',
        supplier: 'PT. Mitra Kantor Sejahtera',
        tanggal: '05 Feb 2026',
        deskripsi: 'Material breathable mesh, lumbar support adjustable, warna hitam. Kondisi 100% baru.',
        history: [
            { waktu: '05 Feb 2026 11:00', pic: 'Staf GA', cabang: 'Head Office (Jakarta)', user: 'Pool Standby GA', aksi: 'Barang Diterima (PO)', deskripsi: 'Penerimaan kursi kerja dari PO-2026-019, disimpan di ruang penyimpanan GA.' }
        ],
        repairs: []
    },
    'AST-2026-003': {
        code: 'AST-2026-003',
        shortCode: 'AST-003',
        cabang: 'DC Cikarang',
        kategori: 'Kendaraan',
        divisi: 'Operasional Logistik',
        user: 'Agus Priyono',
        nama: 'Toyota Avanza 1.5 G MT',
        status: 'Digunakan',
        merk: 'Toyota',
        processor: '1.496 cc 4-Silinder Dual VVT-i',
        model: 'All New Avanza 1.5 G M/T',
        generasi: 'Gen-3 (2024)',
        ram: '-',
        harddisk: '-',
        serial: 'MHKM5EA3J0K119283 (No. Pol: B 2145 PGL)',
        os: 'Manual 5-Speed',
        warna: 'Silver Metallic',
        harga: 'Rp 258.700.000',
        supplier: 'PT. Astra International Tbk - Auto2000',
        tanggal: '20 Okt 2024',
        deskripsi: 'Mobil operasional antar cabang dan gudang logistik. Pajak STNK s/d Okt 2027. Service berkala rutin.',
        history: [
            { waktu: '20 Okt 2024 10:00', pic: 'Supervisor GA', cabang: 'Head Office (Jakarta)', user: 'Pool Transport', aksi: 'Registrasi Aset', deskripsi: 'Unit kendaraan operasional diterima dari Auto2000.' },
            { waktu: '02 Nov 2024 08:30', pic: 'Kepala Logistik', cabang: 'DC Cikarang', user: 'Agus Priyono', aksi: 'Mutasi & Alokasi', deskripsi: 'Penugasan kendaraan operasional untuk DC Cikarang.' }
        ],
        repairs: [
            { waktu: '15 Apr 2026 09:00', deskripsi: 'Service berkala 20.000 KM, ganti oli mesin, filter udara, dan spooring balancing di Auto2000 Cikarang.' }
        ]
    },
    'AST-2026-004': {
        code: 'AST-2026-004',
        shortCode: 'AST-004',
        cabang: 'Cabang Surabaya',
        kategori: 'Elektronik & IT',
        divisi: 'Finance & Tax',
        user: 'Dewi Lestari',
        nama: 'Printer Epson EcoTank L3210 All-in-One',
        status: 'Maintenance',
        merk: 'Epson',
        processor: '-',
        model: 'EcoTank L3210 (Print/Scan/Copy)',
        generasi: 'EcoTank Gen-2',
        ram: '-',
        harddisk: '-',
        serial: 'EPS-88192-SUB',
        os: 'Windows / macOS Driver Compatible',
        warna: 'Hitam',
        harga: 'Rp 2.450.000',
        supplier: 'CV. Surya Komputer Surabaya',
        tanggal: '18 Mar 2025',
        deskripsi: 'Kerusakan head print warna merah bergaris. Sedang dalam perbaikan service center.',
        history: [
            { waktu: '18 Mar 2025 14:00', pic: 'IT Support Surabaya', cabang: 'Cabang Surabaya', user: 'Dewi Lestari', aksi: 'Serah Terima Aset', deskripsi: 'Pemasangan printer untuk kebutuhan cetak faktur pajak Finance.' },
            { waktu: '25 Okt 2026 11:20', pic: 'IT Support Surabaya', cabang: 'Cabang Surabaya', user: 'Dewi Lestari', aksi: 'Status Maintenance', deskripsi: 'Unit dibawa ke Epson Authorized Service Center Surabaya.' }
        ],
        repairs: [
            { waktu: '25 Okt 2026 11:20', deskripsi: 'Penggantian printhead magenta & flushing saluran tinta di Epson Service Center Surabaya.' }
        ]
    },
    'AST-2026-005': {
        code: 'AST-2026-005',
        shortCode: 'AST-005',
        cabang: 'Cabang Bandung',
        kategori: 'Elektronik & IT',
        divisi: 'Marketing & Sales',
        user: 'Rian Hidayat',
        nama: 'Projector Epson EB-E01 3300 Lumens',
        status: 'Dipinjam',
        merk: 'Epson',
        processor: '3LCD Technology',
        model: 'EB-E01 XGA',
        generasi: '2024',
        ram: '-',
        harddisk: '-',
        serial: 'PROJ-BDG-2024',
        os: '-',
        warna: 'Putih',
        harga: 'Rp 5.650.000',
        supplier: 'PT. Bhinneka Mentari Dimensi',
        tanggal: '11 Mei 2025',
        deskripsi: 'Peminjaman operasional demo produk food service di pameran Bandung Expo s/d 30 Okt.',
        history: [
            { waktu: '11 Mei 2025 09:00', pic: 'GA Bandung', cabang: 'Cabang Bandung', user: 'Pool Ruang Rapat', aksi: 'Barang Diterima (PO)', deskripsi: 'Ditempatkan di Ruang Meeting Utama Cabang Bandung.' },
            { waktu: '26 Okt 2026 08:00', pic: 'GA Bandung', cabang: 'Cabang Bandung', user: 'Rian Hidayat', aksi: 'Peminjaman Sementara', deskripsi: 'Dipinjam tim Marketing untuk event pameran Bandung Expo.' }
        ],
        repairs: []
    },
    'AST-2026-006': {
        code: 'AST-2026-006',
        shortCode: 'AST-006',
        cabang: 'DC Cikarang',
        kategori: 'Mesin & Peralatan',
        divisi: 'Operasional Logistik',
        user: 'Pool Gudang DC',
        nama: 'Hand Pallet Truck Hydraulic 3.0 Ton',
        status: 'Rusak',
        merk: 'Krisbow',
        processor: 'Hydraulic Pump Manual',
        model: 'OP-PL-3000 Heavy Duty',
        generasi: '2023',
        ram: '-',
        harddisk: '-',
        serial: 'KRB-HPT-30091',
        os: '-',
        warna: 'Kuning / Hitam',
        harga: 'Rp 4.900.000',
        supplier: 'PT. Kawan Lama Sejahtera',
        tanggal: '09 Agu 2023',
        deskripsi: 'Seal oli hidrolik jebol dan roda retak. Menunggu persetujuan scrap unit baru.',
        history: [
            { waktu: '09 Agu 2023 10:00', pic: 'Supervisor Warehouse', cabang: 'DC Cikarang', user: 'Pool Gudang DC', aksi: 'Barang Diterima (PO)', deskripsi: 'Digunakan di area Unloading Bay 2 DC Cikarang.' },
            { waktu: '21 Okt 2026 16:10', pic: 'Supervisor Warehouse', cabang: 'DC Cikarang', user: 'Pool Gudang DC', aksi: 'Laporan Kerusakan', deskripsi: 'Hidrolik bocor berat, tidak dapat mengangkat beban palet.' }
        ],
        repairs: [
            { waktu: '14 Des 2024 13:00', deskripsi: 'Penggantian bearing roda depan & penambahan oli hidrolik.' },
            { waktu: '21 Okt 2026 16:10', deskripsi: 'Inspeksi teknisi: Seal utama pecah dan as pompa aus, direkomendasikan pengajuan unit pengganti.' }
        ]
    }
};

let activeVideoStream = null;
let barcodeDetectionInterval = null;
let currentScannedAssetCode = null;

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
            return '<span class="badge badge-success px-2 py-1 font-weight-normal"><i class="fas fa-check-circle mr-1"></i>Digunakan</span>';
        case 'Tersedia':
            return '<span class="badge badge-info px-2 py-1 font-weight-normal"><i class="fas fa-box mr-1"></i>Tersedia</span>';
        case 'Maintenance':
            return '<span class="badge badge-warning text-dark px-2 py-1 font-weight-normal"><i class="fas fa-wrench mr-1"></i>Maintenance</span>';
        case 'Rusak':
            return '<span class="badge badge-danger px-2 py-1 font-weight-normal"><i class="fas fa-times-circle mr-1"></i>Rusak</span>';
        case 'Dipinjam':
            return '<span class="badge badge-primary px-2 py-1 font-weight-normal"><i class="fas fa-hand-holding mr-1"></i>Dipinjam</span>';
        default:
            return '<span class="badge badge-secondary px-2 py-1">' + status + '</span>';
    }
}

function getInitials(name) {
    if (!name) return 'NA';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
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

// Meminta Izin Kamera Browser & Menyalakan Stream
async function startCameraScanner() {
    const errorEl = $('#cameraErrorMsg');
    errorEl.addClass('d-none').text('');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        errorEl.removeClass('d-none').text('Browser ini tidak mendukung akses kamera langsung (pastikan menggunakan HTTPS atau localhost). Silakan gunakan input kode di bawah.');
        return;
    }

    try {
        $('#btnRequestCamera').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Meminta Izin Kamera...');
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            audio: false
        });

        activeVideoStream = stream;
        const videoEl = document.getElementById('qrVideoElement');
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
            }, 500);
        }
    } catch (err) {
        $('#btnRequestCamera').prop('disabled', false).html('<i class="fas fa-video mr-1"></i> Izinkan & Buka Kamera');
        errorEl.removeClass('d-none').text('Akses kamera ditolak atau perangkat kamera tidak ditemukan. Anda tetap bisa mencoba simulasi scan di bawah.');
    }
}

// Matikan Kamera saat Modal Ditutup atau Selesai Scan
function stopCameraScanner() {
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
    }
    $('#cameraViewportWrapper').addClass('d-none');
    $('#cameraPermissionPrompt').removeClass('d-none');
}

// Proses Kode QR / Barcode yang Di-scan
function handleScannedCode(rawCode) {
    if (!rawCode || !rawCode.trim()) return;
    let normalized = rawCode.trim().toUpperCase();

    if (normalized.match(/^AST-\d{3}$/)) {
        normalized = normalized.replace('AST-', 'AST-2026-');
    }

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

// Sinkronisasi perubahan data barang ke baris Tabel Utama
function syncAssetRowInTable(code, item) {
    const checkbox = $(`.item-checkbox[value="${code}"]`);
    if (checkbox.length) {
        const row = checkbox.closest('tr');
        row.attr('data-status', item.status).data('status', item.status);
        row.attr('data-divisi', item.divisi).data('divisi', item.divisi);
        row.attr('data-kategori', item.kategori).data('kategori', item.kategori);
        row.attr('data-cabang', item.cabang).data('cabang', item.cabang);

        row.find('td:eq(2) strong').text(item.nama);
        row.find('td:eq(3)').html(getStatusBadgeHtml(item.status));
        row.find('td:eq(4) .user-avatar-sm').text(getInitials(item.user));
        row.find('td:eq(4) strong').text(item.user);
        row.find('td:eq(5) span').text(item.divisi);
        row.find('td:eq(6) span').text(item.kategori);
        row.find('td:eq(7)').html('<i class="fas fa-building text-secondary mr-1"></i>' + item.cabang);
        row.find('td:eq(8) div').attr('title', item.deskripsi).text(item.deskripsi);
    }
}

// Buka Modal Edit Barang dari Tombol Action Tabel
function openEditAssetModal(code) {
    const item = assetDatabase[code];
    if (!item) return;

    $('#editAssetCode').val(item.code);
    $('#editAssetNama').val(item.nama);
    $('#editAssetStatus').val(item.status);
    $('#editAssetUser').val(item.user);
    $('#editAssetDivisi').val(item.divisi);
    $('#editAssetKategori').val(item.kategori);
    $('#editAssetCabang').val(item.cabang);
    $('#editAssetMerk').val(item.merk || '');
    $('#editAssetModel').val(item.model || '');
    $('#editAssetProcessor').val(item.processor || '');
    $('#editAssetGenerasi').val(item.generasi || '');
    $('#editAssetRam').val(item.ram || '');
    $('#editAssetHarddisk').val(item.harddisk || '');
    $('#editAssetSerial').val(item.serial || '');
    $('#editAssetOs').val(item.os || '');
    $('#editAssetWarna').val(item.warna || '');
    $('#editAssetHarga').val(item.harga || '');
    $('#editAssetSupplier').val(item.supplier || '');
    $('#editAssetTanggal').val(item.tanggal || '');
    $('#editAssetDeskripsi').val(item.deskripsi || '');

    $('#modalEditBarang').modal('show');
}

// Simpan Perubahan dari Modal Edit Barang & Catat ke Riwayat Produk
function saveEditAssetModal(e) {
    e.preventDefault();
    const code = $('#editAssetCode').val();
    if (!code || !assetDatabase[code]) return;

    const item = assetDatabase[code];
    item.nama = $('#editAssetNama').val();
    item.status = $('#editAssetStatus').val();
    item.user = $('#editAssetUser').val();
    item.divisi = $('#editAssetDivisi').val();
    item.kategori = $('#editAssetKategori').val();
    item.cabang = $('#editAssetCabang').val();
    item.merk = $('#editAssetMerk').val() || '-';
    item.model = $('#editAssetModel').val() || '-';
    item.processor = $('#editAssetProcessor').val() || '-';
    item.generasi = $('#editAssetGenerasi').val() || '-';
    item.ram = $('#editAssetRam').val() || '-';
    item.harddisk = $('#editAssetHarddisk').val() || '-';
    item.serial = $('#editAssetSerial').val() || '-';
    item.os = $('#editAssetOs').val() || '-';
    item.warna = $('#editAssetWarna').val() || '-';
    item.harga = $('#editAssetHarga').val() || '-';
    item.supplier = $('#editAssetSupplier').val() || '-';
    item.tanggal = $('#editAssetTanggal').val() || '-';
    item.deskripsi = $('#editAssetDeskripsi').val();

    item.history.push({
        waktu: getCurrentTimestampStr(),
        pic: 'Admin Inventaris',
        cabang: item.cabang,
        user: item.user,
        aksi: 'Update Data Barang',
        deskripsi: item.deskripsi || 'Pembaruan data & spesifikasi barang.'
    });

    syncAssetRowInTable(code, item);
    $('#modalEditBarang').modal('hide');

    const msg = 'Data & spesifikasi barang ' + item.code + ' berhasil diperbarui.';
    if (typeof toastr !== 'undefined') {
        toastr.success(msg);
    } else {
        alert(msg);
    }
}

// Simpan Perubahan dari Form Update Data / Repair Data (Modal Scan QR)
function saveScanQuickAction(e) {
    e.preventDefault();
    if (!currentScannedAssetCode || !assetDatabase[currentScannedAssetCode]) return;

    const item = assetDatabase[currentScannedAssetCode];
    const mode = $('#scanActionMode').val();

    item.nama = $('#inputScanNama').val();
    item.cabang = $('#inputScanCabang').val();
    item.kategori = $('#inputScanKategori').val();
    item.status = $('#inputScanStatus').val();
    item.divisi = $('#inputScanDivisi').val();
    item.user = $('#inputScanUser').val();
    item.deskripsi = $('#inputScanDeskripsi').val();

    const waktuSkrg = getCurrentTimestampStr();
    if (mode === 'repair') {
        item.repairs.push({
            waktu: waktuSkrg,
            deskripsi: item.deskripsi || 'Perbaikan / pengecekan rutin unit aset.'
        });
        item.history.push({
            waktu: waktuSkrg,
            pic: 'Petugas Scanner',
            cabang: item.cabang,
            user: item.user,
            aksi: 'Repair / Maintenance',
            deskripsi: item.deskripsi
        });
    } else {
        item.history.push({
            waktu: waktuSkrg,
            pic: 'Petugas Scanner',
            cabang: item.cabang,
            user: item.user,
            aksi: 'Update via Scan QR',
            deskripsi: item.deskripsi
        });
    }

    syncAssetRowInTable(currentScannedAssetCode, item);
    renderScanResult(item);

    const msg = mode === 'repair'
        ? 'Data perbaikan (Repair) untuk ' + item.code + ' berhasil disimpan.'
        : 'Data barang ' + item.code + ' berhasil diperbarui.';

    if (typeof toastr !== 'undefined') {
        toastr.success(msg);
    } else {
        alert(msg);
    }
}

// Buka Modal Detail Lengkap (Spesifikasi + Riwayat Produk + Detail Repair)
function openDetailModal(code) {
    const item = assetDatabase[code];
    if (!item) return;

    $('#detQr').text(item.code);
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

    // Render Riwayat Produk
    const historyBody = $('#detHistoryTableBody');
    historyBody.empty();
    if (item.history && item.history.length > 0) {
        item.history.forEach((h, idx) => {
            historyBody.append(`
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td>${h.waktu}</td>
                    <td>${h.pic}</td>
                    <td>${h.cabang}</td>
                    <td class="font-weight-bold">${h.user}</td>
                    <td><span class="badge badge-light border">${h.aksi}</span></td>
                    <td>${h.deskripsi}</td>
                </tr>
            `);
        });
    } else {
        historyBody.append('<tr><td colspan="7" class="text-center text-muted">Belum ada riwayat produk.</td></tr>');
    }

    // Render Detail Repair
    const repairBody = $('#detRepairTableBody');
    repairBody.empty();
    if (item.repairs && item.repairs.length > 0) {
        item.repairs.forEach((r, idx) => {
            repairBody.append(`
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td>${r.waktu}</td>
                    <td>${r.deskripsi}</td>
                </tr>
            `);
        });
    } else {
        repairBody.append('<tr><td colspan="3" class="text-center text-muted">Belum ada riwayat perbaikan (repair).</td></tr>');
    }

    $('#btnModalDownloadPdf').attr('onclick', `downloadAssetPdf('${item.code}')`);
    $('#modalDetailBarang').modal('show');
}

// Download Data Spesifikasi, Riwayat Produk & Detail Repair ke PDF
function downloadAssetPdf(code) {
    const item = assetDatabase[code];
    if (!item) return;

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
        <div style="font-family: Arial, Helvetica, sans-serif; color: #1e293b; padding: 16px; font-size: 12px; line-height: 1.45;">
            <!-- Header Dokumen -->
            <div style="border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;">PT. PANGAN LESTARI</div>
                    <div style="font-size: 13px; font-weight: 600; color: #4f46e5; margin-top: 2px;">DOKUMEN SPESIFIKASI &amp; RIWAYAT ASET INVENTARIS</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 12px; font-weight: 700; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 4px; display: inline-block;">
                        KODE ASET: ${item.code}
                    </div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Dicetak: ${getCurrentTimestampStr()}</div>
                </div>
            </div>

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
        const searchVal = $('#searchInput').val().toLowerCase();
        const statusVal = $('#filterStatus').val();
        const divisiVal = $('#filterDivisi').val();
        const kategoriVal = $('#filterKategori').val();
        const cabangVal = $('#filterCabang').val();

        $('#assetsTable tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const rowStatus = $(this).data('status');
            const rowDivisi = $(this).data('divisi');
            const rowKategori = $(this).data('kategori');
            const rowCabang = $(this).data('cabang');

            const matchSearch = !searchVal || rowText.indexOf(searchVal) > -1;
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchDivisi = !divisiVal || rowDivisi === divisiVal;
            const matchKategori = !kategoriVal || rowKategori === kategoriVal;
            const matchCabang = !cabangVal || rowCabang === cabangVal;

            if (matchSearch && matchStatus && matchDivisi && matchKategori && matchCabang) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).find('.item-checkbox').prop('checked', false);
            }
        });

        // Update select all state
        const totalVisible = $('.item-checkbox:visible').length;
        const totalChecked = $('.item-checkbox:visible:checked').length;
        $('#selectAllCheckbox').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
        updateBatchBar();
    }

    $('#searchInput').on('keyup', applyFilters);
    $('#filterStatus, #filterDivisi, #filterKategori, #filterCabang').on('change', applyFilters);

    $('#btnResetFilter').on('click', function() {
        $('#searchInput').val('');
        $('#filterStatus, #filterDivisi, #filterKategori, #filterCabang').val('');
        applyFilters();
    });
});

// Modal QR Preview & Print Trigger
function openQrModal(code, name, user, divisi) {
    $('#modalQrCodeText').text(code);
    $('#modalQrItemName').text(name);
    $('#modalQrUserName').text(user);
    $('#modalQrDivisi').text(divisi);
    $('#modalQrPreview').modal('show');
}

function confirmDeleteAsset(code) {
    if (confirm('Apakah Anda yakin ingin menghapus data barang ' + code + '? (Mockup)')) {
        alert('Barang ' + code + ' berhasil dihapus dari simulasi.');
    }
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
