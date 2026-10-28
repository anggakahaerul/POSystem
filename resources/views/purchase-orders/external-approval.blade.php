<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Purchase Order {{ $po->po_number }} - PT. Pangan Lestari</title>

    <!-- Google Font: Inter / Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .portal-header {
            background-color: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .logo-img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .main-container {
            max-width: 1020px;
            margin: 30px auto 60px;
            padding: 0 16px;
        }

        .portal-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .portal-card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .portal-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            letter-spacing: -0.2px;
        }

        .table-items th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 14px;
        }

        .table-items td {
            font-size: 0.88rem;
            vertical-align: middle;
            padding: 12px 14px;
            border-color: #f1f5f9;
        }

        .signature-box {
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            background-color: #ffffff;
            position: relative;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
        }

        .btn-corporate-approve {
            background-color: #15803d;
            border-color: #15803d;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 24px;
            font-size: 0.95rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-corporate-approve:hover {
            background-color: #166534;
            border-color: #166534;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(21, 128, 61, 0.3);
        }

        .btn-corporate-reject {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 24px;
            font-size: 0.95rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-corporate-reject:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
        }

        .meta-label {
            font-size: 0.78rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .meta-value {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1e293b;
        }

        .portal-footer {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            padding: 24px 0 40px;
        }
    </style>
</head>
<body>

    <!-- Corporate Header -->
    <header class="portal-header">
        <div class="container-fluid" style="max-width: 1020px;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/logo-pangan-lestari.png') }}" alt="PT. Pangan Lestari" class="logo-img mr-3" onerror="this.src='{{ asset('storage/logos/logo-pangan-lestari.png') }}'">
                    <div>
                        <div class="font-weight-bold text-dark" style="font-size: 1.05rem; letter-spacing: -0.3px;">PT. PANGAN LESTARI</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Sistem Otorisasi Dokumen Pengadaan & Purchasing</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="badge badge-pending">
                        <i class="fas fa-clock mr-1"></i> Menunggu Persetujuan
                    </span>
                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                        Tautan Satu Kali Pakai
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="main-container">

        <!-- Top Notification Banner -->
        <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e0f2fe; border-left: 4px solid #0284c7 !important; border-radius: 6px;">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle text-info mt-1 mr-3" style="font-size: 1.25rem;"></i>
                <div style="font-size: 0.88rem; color: #0369a1;">
                    <strong>Permohonan Persetujuan Purchase Order:</strong><br>
                    Dokumen ini dikirimkan kepada <strong>{{ $po->approver_email }}</strong> untuk ditinjau dan diberikan keputusan persetujuan. Anda dapat memeriksa rincian dokumen di bawah ini atau mengunduh versi cetak resmi (PDF).
                </div>
            </div>
        </div>

        <!-- Document Overview Card -->
        <div class="portal-card">
            <div class="portal-card-header">
                <h2 class="portal-card-title">
                    <i class="fas fa-file-invoice text-secondary mr-2"></i>
                    PURCHASE ORDER: {{ $po->po_number }}
                </h2>
                <a href="{{ route('po.external-preview-pdf', $token) }}" target="_blank" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 6px;">
                    <i class="fas fa-file-pdf text-danger mr-1"></i> Buka PDF Dokumen Resmi
                </a>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="meta-label">Tanggal Pengajuan</div>
                        <div class="meta-value">{{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="meta-label">Cabang / Unit</div>
                        <div class="meta-value">{{ $po->branch->name ?? 'Kantor Pusat' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="meta-label">Pemohon (Staff PO)</div>
                        <div class="meta-value">{{ $po->creator->name ?? 'Staff Purchasing' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="meta-label">Term of Payment</div>
                        <div class="meta-value">{{ $po->paymentTerm->name ?? 'Cash / Tunai' }}</div>
                    </div>
                </div>

                <hr class="my-2" style="border-color: #f1f5f9;">

                <div class="row mt-3">
                    <div class="col-md-6 mb-2">
                        <div class="meta-label">Pemasok / Vendor</div>
                        <div class="meta-value" style="font-size: 1rem; color: #0f172a;">{{ $po->vendor->name ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 0.82rem; margin-top: 2px;">
                            {{ $po->vendor->address ?? '' }}
                            @if(!empty($po->vendor->phone)) &bull; Telp: {{ $po->vendor->phone }} @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="meta-label">Catatan Pembuat PO</div>
                        <div class="text-dark" style="font-size: 0.88rem; font-style: italic;">
                            {{ $po->note ?: 'Tidak ada catatan khusus.' }}
                        </div>
                    </div>
                </div>

                @if(!empty($po->creator_signature))
                    <div class="mt-3 p-3 rounded bg-light border d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if(\Illuminate\Support\Str::startsWith($po->creator_signature, 'data:image'))
                                <img src="{{ $po->creator_signature }}" alt="TTD Pembuat" style="max-height: 48px; max-width: 130px; background: #fff; padding: 2px; border: 1px solid #cbd5e1; border-radius: 4px;" class="mr-3">
                            @else
                                <img src="{{ asset('storage/' . $po->creator_signature) }}" alt="TTD Pembuat" style="max-height: 48px; max-width: 130px; background: #fff; padding: 2px; border: 1px solid #cbd5e1; border-radius: 4px;" class="mr-3">
                            @endif
                            <div>
                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">{{ $po->creator->name ?? 'Pembuat Dokumen' }}</div>
                                <div style="font-size: 0.72rem; color: #0038a8; font-weight: 600;">
                                    Ditandatangani Pada: {{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }} WIB
                                </div>
                            </div>
                        </div>
                        <span class="badge badge-success px-2 py-1 mt-2 mt-md-0" style="font-size: 0.72rem;">
                            <i class="fas fa-check-circle mr-1"></i>TTD Pembuat Tervalidasi
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items Detail Card -->
        <div class="portal-card">
            <div class="portal-card-header">
                <h3 class="portal-card-title">
                    <i class="fas fa-boxes text-secondary mr-2"></i>
                    Rincian Barang / Jasa
                </h3>
                <span class="text-muted" style="font-size: 0.82rem;">{{ $po->details->count() }} Item Dipesan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-items mb-0">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Deskripsi Produk</th>
                            <th width="90" class="text-center">Qty</th>
                            <th width="150" class="text-right">Harga Satuan</th>
                            <th width="120" class="text-right">Diskon</th>
                            <th width="160" class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($po->details as $index => $detail)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $detail->product->name ?? '-' }}</div>
                                    @if(!empty($detail->product->sku))
                                        <small class="text-muted">Kode: {{ $detail->product->sku }}</small>
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->product->unit ?? 'pcs' }}
                                </td>
                                <td class="text-right text-muted">
                                    Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-muted">
                                    @if($detail->discount_amount > 0)
                                        <span class="text-danger">-Rp {{ number_format($detail->discount_amount, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    Rp {{ number_format($detail->subtotal_after_discount ?? ($detail->subtotal - $detail->discount_amount), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                        <tr>
                            <td colspan="5" class="text-right font-weight-normal text-muted py-2">Subtotal:</td>
                            <td class="text-right font-weight-normal text-dark py-2">Rp {{ number_format($po->subtotal ?? $po->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($po->discount_amount > 0)
                            <tr>
                                <td colspan="5" class="text-right font-weight-normal text-danger py-1">Diskon PO:</td>
                                <td class="text-right font-weight-normal text-danger py-1">-Rp {{ number_format($po->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($po->ppn_amount > 0)
                            <tr>
                                <td colspan="5" class="text-right font-weight-normal text-muted py-1">PPN ({{ (float)$po->ppn_percentage }}%):</td>
                                <td class="text-right font-weight-normal text-dark py-1">Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($po->pph_amount > 0)
                            <tr>
                                <td colspan="5" class="text-right font-weight-normal text-danger py-1">PPh:</td>
                                <td class="text-right font-weight-normal text-danger py-1">-Rp {{ number_format($po->pph_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr style="border-top: 2px solid #cbd5e1;">
                            <td colspan="5" class="text-right font-weight-bold py-3" style="font-size: 1rem; color: #0f172a;">TOTAL PEMESANAN:</td>
                            <td class="text-right font-weight-bold py-3 text-primary" style="font-size: 1.15rem;">
                                {{ $po->formatted_total }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Approval / Rejection Action Area -->
        <div class="row mt-4">
            <!-- Panel Setujui (Approve) -->
            <div class="col-lg-7 mb-4">
                <div class="portal-card h-100" style="border-top: 4px solid #15803d;">
                    <div class="portal-card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="portal-card-title text-success mb-0">
                            <i class="fas fa-check-circle mr-2"></i>
                            Persetujuan Dokumen
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('po.external-approve', $token) }}" method="POST" id="form-approve-po" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="approver_signature" id="approver_signature_input">

                            <!-- Tab Pilihan: TTD atau Upload -->
                            <ul class="nav nav-pills mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active py-1 px-3 font-weight-bold" data-toggle="pill" href="#tab-draw-sig" style="font-size: 0.85rem; border-radius: 6px;">
                                        <i class="fas fa-pen mr-1"></i> Gambar TTD
                                    </a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="nav-link py-1 px-3 font-weight-bold" data-toggle="pill" href="#tab-upload-sig" style="font-size: 0.85rem; border-radius: 6px;">
                                        <i class="fas fa-file-upload mr-1"></i> Upload PDF / PNG
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mb-3">
                                <!-- Tab 1: Gambar TTD -->
                                <div class="tab-pane fade show active" id="tab-draw-sig">
                                    <div class="d-flex justify-content-end mb-1">
                                        <button type="button" class="btn btn-xs btn-link text-secondary p-0 font-weight-bold" id="btn-clear-sig">
                                            <i class="fas fa-eraser mr-1"></i> Bersihkan
                                        </button>
                                    </div>
                                    <div class="signature-box" style="height: 140px;">
                                        <canvas id="approver-sig-canvas" style="width: 100%; height: 100%; display: block;"></canvas>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-primary font-weight-bold" style="font-size: 0.72rem;">
                                            <i class="fas fa-pen-nib mr-1"></i> Tinta: Biru
                                        </small>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            Ditandatangani Pada: {{ now()->format('d/m/Y H:i') }} WIB
                                        </small>
                                    </div>
                                </div>

                                <!-- Tab 2: Upload PDF / PNG -->
                                <div class="tab-pane fade" id="tab-upload-sig">
                                    <div class="form-group mb-2">
                                        <div class="custom-file">
                                            <input type="file" name="approver_file" class="custom-file-input" id="approver-file-input" accept=".pdf,.png,.jpg,.jpeg">
                                            <label class="custom-file-label text-truncate" for="approver-file-input" id="approver-file-label">Pilih file PDF atau PNG/JPG...</label>
                                        </div>
                                        <small class="text-muted">Mendukung format PDF, PNG, JPG (Maks. 5MB)</small>
                                    </div>

                                    <!-- Preview Container -->
                                    <div id="file-preview-container" class="mt-2 text-center" style="display: none;">
                                        <!-- Image Preview -->
                                        <div id="file-img-preview-box" style="display: none;">
                                            <img id="file-img-preview" src="" alt="Preview" style="max-height: 110px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px; background: #fff;" class="img-fluid mb-2">
                                        </div>
                                        <!-- PDF Preview Badge -->
                                        <div id="file-pdf-preview-box" style="display: none;" class="alert alert-light border py-2 px-3 mb-2 text-left d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center text-truncate mr-2">
                                                <i class="fas fa-file-pdf text-danger fa-2x mr-2"></i>
                                                <span id="file-pdf-name" class="font-weight-bold text-truncate text-dark" style="font-size: 0.85rem;"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-xs btn-outline-danger" id="btn-clear-file">
                                                <i class="fas fa-times mr-1"></i> Hapus File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-corporate-approve btn-block mt-3" id="btn-submit-approve">
                                <i class="fas fa-check mr-2"></i> Setujui Purchase Order Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel Tolak (Reject) -->
            <div class="col-lg-5 mb-4">
                <div class="portal-card h-100" style="border-top: 4px solid #dc2626;">
                    <div class="portal-card-header bg-white">
                        <h4 class="portal-card-title text-danger mb-0">
                            <i class="fas fa-times-circle mr-2"></i>
                            Tolak Purchase Order
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('po.external-reject', $token) }}" method="POST" id="form-reject-po">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="meta-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" style="font-size: 0.85rem; border-radius: 6px;" placeholder="Tuliskan catatan alasan penolakan..." required minlength="5"></textarea>
                            </div>

                            <button type="submit" class="btn btn-corporate-reject btn-block" id="btn-submit-reject">
                                <i class="fas fa-times mr-2"></i> Tolak PO Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Corporate Footer -->
        <footer class="portal-footer">
            <div>PT. PANGAN LESTARI &bull; Purchasing & Inventory Replenishment Portal</div>
            <div class="mt-1" style="font-size: 0.75rem;">Dokumen ini bersifat rahasia dan sah secara internal. Tanggal Akses: {{ now()->format('d M Y') }}</div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var canvas = document.getElementById('approver-sig-canvas');
            var signaturePad = null;

            function initCanvas() {
                if (!canvas) return;
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 56, 168)'
                });
            }

            initCanvas();
            window.addEventListener('resize', function() {
                if (canvas && signaturePad) {
                    var data = signaturePad.toData();
                    initCanvas();
                    signaturePad.fromData(data);
                }
            });

            // Re-init canvas when switching to draw tab
            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#tab-draw-sig') {
                    if (canvas && signaturePad) {
                        var data = signaturePad.toData();
                        initCanvas();
                        if (data && data.length) signaturePad.fromData(data);
                    }
                }
            });

            $('#btn-clear-sig').on('click', function(e) {
                e.preventDefault();
                if (signaturePad) signaturePad.clear();
            });

            // File upload handling (PDF / PNG / JPG)
            $('#approver-file-input').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Ukuran file maksimal adalah 5MB.'
                    });
                    $(this).val('');
                    $('#approver-file-label').text('Pilih file PDF atau PNG/JPG...');
                    return;
                }

                var fileName = file.name;
                var ext = fileName.split('.').pop().toLowerCase();
                $('#approver-file-label').text(fileName);
                $('#file-preview-container').show();

                if (ext === 'pdf') {
                    $('#file-img-preview-box').hide();
                    $('#file-pdf-name').text(fileName + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
                    $('#file-pdf-preview-box').show();
                } else if (['png', 'jpg', 'jpeg'].indexOf(ext) !== -1) {
                    var reader = new FileReader();
                    reader.onload = function(ev) {
                        $('#file-img-preview').attr('src', ev.target.result);
                        $('#file-img-preview-box').show();
                        $('#file-pdf-preview-box').hide();
                    };
                    reader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Didukung',
                        text: 'Hanya format PDF, PNG, atau JPG yang diperbolehkan.'
                    });
                    $(this).val('');
                    $('#approver-file-label').text('Pilih file PDF atau PNG/JPG...');
                    $('#file-preview-container').hide();
                }
            });

            $('#btn-clear-file').on('click', function(e) {
                e.preventDefault();
                $('#approver-file-input').val('');
                $('#approver-file-label').text('Pilih file PDF atau PNG/JPG...');
                $('#file-preview-container').hide();
                $('#file-img-preview-box').hide();
                $('#file-pdf-preview-box').hide();
                $('#file-img-preview').attr('src', '');
                $('#file-pdf-name').text('');
            });

            // Approve Form Submission
            $('#form-approve-po').on('submit', function(e) {
                // If draw tab is active and canvas has content, capture signature
                var activeTab = $('.nav-pills .nav-link.active').attr('href');
                if (activeTab === '#tab-draw-sig' && signaturePad && !signaturePad.isEmpty()) {
                    $('#approver_signature_input').val(signaturePad.toDataURL('image/png'));
                }

                $('#btn-submit-approve')
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Persetujuan...');
            });

            // Reject Form Submission
            $('#form-reject-po').on('submit', function(e) {
                var reason = ($('#rejection_reason').val() || '').trim();
                if (reason.length < 5) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Alasan Wajib Diisi',
                        text: 'Mohon tuliskan alasan penolakan minimal 5 karakter.'
                    });
                    return false;
                }

                $('#btn-submit-reject')
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Penolakan...');
            });
        });
    </script>
</body>
</html>
