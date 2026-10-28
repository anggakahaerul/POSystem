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
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="alert('Fitur Scanner QR / Barcode akan aktif saat kamera scanner dihubungkan.')">
                        <i class="fas fa-qrcode mr-1"></i> Scan QR
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="alert('Fitur Export Data Excel (Mockup)')">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalTambahBarang">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-001', 'Apple MacBook Pro 14 M2 Pro', 'Digunakan', 'Budi Santoso', 'IT &amp; System', 'Elektronik &amp; IT', 'Head Office (Jakarta)', 'Chip M2 Pro 10-core, RAM 16GB, SSD 512GB Space Grey.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-001', 'Apple MacBook Pro 14 M2 Pro', 'Budi Santoso', 'IT &amp; System')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-002', 'Kursi Kerja Ergonomis Mesh', 'Tersedia', 'Pool Standby GA', 'Human Resources', 'Furniture &amp; Kantor', 'Head Office (Jakarta)', 'Material breathable mesh, lumbar support adjustable.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-002', 'Kursi Kerja Ergonomis Mesh', 'Pool HR Standby', 'Human Resources')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-003', 'Toyota Avanza 1.5 G MT', 'Digunakan', 'Agus Priyono', 'Operasional Logistik', 'Kendaraan', 'DC Cikarang', 'Mobil operasional antar cabang.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-003', 'Toyota Avanza 1.5 G MT', 'Agus Priyono', 'Operasional Logistik')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-004', 'Printer Epson L3210', 'Maintenance', 'Dewi Lestari', 'Finance &amp; Tax', 'Elektronik &amp; IT', 'Cabang Surabaya', 'Perbaikan head print di Epson.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-004', 'Printer Epson EcoTank L3210', 'Dewi Lestari', 'Finance &amp; Tax')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-005', 'Projector Epson EB-E01', 'Dipinjam', 'Rian Hidayat', 'Marketing &amp; Sales', 'Elektronik &amp; IT', 'Cabang Bandung', 'Demo produk Bandung Expo.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-005', 'Projector Epson EB-E01', 'Rian Hidayat', 'Marketing &amp; Sales')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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
                                    <button type="button" class="btn-action btn-action-info" title="Detail Barang" onclick="openDetailModal('AST-2026-006', 'Hand Pallet Truck 3 Ton', 'Rusak', 'Pool Gudang DC', 'Operasional Logistik', 'Mesin &amp; Peralatan', 'DC Cikarang', 'Seal oli hidrolik jebol.')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-success" title="Cetak Label QR" onclick="openQrModal('AST-2026-006', 'Hand Pallet Truck 3 Ton', 'Gudang DC', 'Operasional Logistik')">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-warning" title="Edit Barang" onclick="alert('Form Edit Barang (Mockup)')">
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

<!-- Modal: Detail Barang -->
<div class="modal fade" id="modalDetailBarang" tabindex="-1" role="dialog" aria-labelledby="modalDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalDetailTitle">
                    <i class="fas fa-info-circle mr-2"></i>Rincian Barang &amp; Aset
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="rounded-circle p-3 mr-3 bg-light text-primary border">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <div>
                        <span class="badge badge-primary" id="detQr">AST-2026-001</span>
                        <h5 class="font-weight-bold text-dark mb-0 mt-1" id="detNama">Nama Barang</h5>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="text-muted text-nowrap" style="width: 130px;">Status:</th>
                        <td id="detStatus"><span class="badge badge-success">Digunakan</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">User / PIC:</th>
                        <td class="font-weight-bold text-dark" id="detUser">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Divisi:</th>
                        <td id="detDivisi">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Kategori:</th>
                        <td id="detKategori">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Cabang / Lokasi:</th>
                        <td id="detCabang">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Deskripsi:</th>
                        <td class="text-muted" id="detDeskripsi">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Barang (Design Mockup Form) -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" role="dialog" aria-labelledby="modalTambahTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalTambahTitle">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Barang Baru (Asset Management)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form onsubmit="event.preventDefault(); alert('Desain form tersimpan (Simulasi Mockup)'); $('#modalTambahBarang').modal('hide');">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Kode QR / Barcode Aset <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="AST-2026-007" readonly style="background-color: #f8fafc; font-weight: bold;">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light text-muted small"><i class="fas fa-magic mr-1"></i> Auto</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Contoh: Laptop Dell Latitude 5420" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">Status Barang <span class="text-danger">*</span></label>
                            <select class="form-control" required>
                                <option value="Tersedia">Tersedia (Ready di Pool)</option>
                                <option value="Digunakan" selected>Digunakan</option>
                                <option value="Maintenance">Dalam Perawatan (Maintenance)</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Dipinjam">Dipinjam Sementara</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">User / PIC Penanggung Jawab</label>
                            <input type="text" class="form-control" placeholder="Nama karyawan / PIC">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">Divisi / Departemen <span class="text-danger">*</span></label>
                            <select class="form-control" required>
                                <option value="">Pilih Divisi...</option>
                                <option value="IT & System">IT &amp; System</option>
                                <option value="Finance & Tax">Finance &amp; Tax</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Operasional Logistik">Operasional Logistik</option>
                                <option value="Marketing & Sales">Marketing &amp; Sales</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Kategori Barang <span class="text-danger">*</span></label>
                            <select class="form-control" required>
                                <option value="">Pilih Kategori...</option>
                                <option value="Elektronik & IT">Elektronik &amp; IT</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Furniture & Kantor">Furniture &amp; Kantor</option>
                                <option value="Mesin & Peralatan">Mesin &amp; Peralatan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Cabang / Lokasi Fisik <span class="text-danger">*</span></label>
                            <select class="form-control" required>
                                <option value="">Pilih Cabang...</option>
                                <option value="Head Office (Jakarta)">Head Office (Jakarta)</option>
                                <option value="Cabang Surabaya">Cabang Surabaya</option>
                                <option value="Cabang Bandung">Cabang Bandung</option>
                                <option value="DC Cikarang">DC Cikarang</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small">Deskripsi / Spesifikasi / Catatan Kondisi</label>
                            <textarea class="form-control" rows="3" placeholder="Tuliskan spesifikasi, nomor seri, kelengkapan, atau catatan kondisi fisik barang..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">
                        <i class="fas fa-save mr-1"></i> Simpan Barang
                    </button>
                </div>
            </form>
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
<script>
$(document).ready(function() {
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

// Modal Detail Trigger
function openDetailModal(code, name, status, user, divisi, kategori, cabang, deskripsi) {
    $('#detQr').text(code);
    $('#detNama').text(name);
    $('#detStatus').html('<span class="badge badge-success">' + status + '</span>');
    $('#detUser').text(user);
    $('#detDivisi').text(divisi);
    $('#detKategori').text(kategori);
    $('#detCabang').text(cabang);
    $('#detDeskripsi').text(deskripsi);
    $('#modalDetailBarang').modal('show');
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
