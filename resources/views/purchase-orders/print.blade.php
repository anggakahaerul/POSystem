<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $po->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px 25px;
        }

        /* Header Section */
        .header {
            margin-bottom: 20px;
        }

        .header-logo {
            max-height: 55px;
            max-width: 260px;
            object-fit: contain;
        }

        .header-company {
            font-size: 14px;
            font-weight: bold;
            color: #d35400;
        }

        .header-tagline {
            font-size: 10px;
            color: #999;
            font-style: italic;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin: 18px 0 22px 0;
        }

        .title-section h1 {
            font-family: 'Helvetica Neue', Helvetica, 'Segoe UI', Arial, sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            display: inline-block;
            border-bottom: 2px solid #111827;
            padding-bottom: 3px;
            margin: 0;
        }

        .title-section .title-npwp {
            font-size: 10px;
            color: #4b5563;
            font-weight: 600;
            margin-top: 5px;
            letter-spacing: 0.5px;
        }

        /* Info Section - Two Columns */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .vendor-col {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }

        .po-col {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        /* Vendor Box */
        .vendor-box {
            border: 1px solid #ccc;
            padding: 12px;
            background: #fafafa;
        }

        .vendor-box .kepada {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }

        .vendor-box .vendor-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .vendor-box .vendor-address {
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .vendor-box .vendor-contact {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .vendor-box .contact-row {
            margin-bottom: 3px;
        }

        .vendor-box .contact-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 60px;
        }

        /* PO Info */
        .po-info-table {
            width: 100%;
        }

        .po-info-table td {
            padding: 3px 0;
        }

        .po-info-table .label {
            color: #888;
            text-align: right;
            padding-right: 10px;
            width: 80px;
        }

        .po-info-table .value {
            font-weight: 600;
            color: #222;
        }

        .po-info-table .po-number {
            font-size: 13px;
            font-weight: bold;
            color: #111;
        }

        .referensi-box {
            margin-top: 15px;
            text-align: center;
        }

        .referensi-box .ref-title {
            color: #888;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .referensi-box .ref-value {
            font-weight: bold;
            color: #222;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table .item-no {
            width: 30px;
        }

        .items-table .item-desc {
            font-weight: 500;
        }

        .items-table .qty-col {
            white-space: nowrap;
        }

        /* Bottom Section */
        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 15px;
        }

        .notes-col {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 20px;
        }

        .totals-col {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }

        /* Notes */
        .notes-box {
            font-size: 10px;
            line-height: 1.6;
        }

        .notes-box .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .notes-box ol {
            padding-left: 15px;
            margin: 0;
        }

        .notes-box li {
            margin-bottom: 4px;
        }

        .notes-box .delivery-info {
            margin-top: 5px;
            padding-left: 15px;
        }

        /* Totals */
        .totals-table {
            width: 100%;
        }

        .totals-table td {
            padding: 5px 0;
        }

        .totals-table .tot-label {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
            color: #555;
        }

        .totals-table .tot-value {
            text-align: right;
            font-weight: bold;
            width: 100px;
        }

        .totals-table .grand-total td {
            font-size: 14px;
            color: #333;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }

        /* Signature & Confirmation Section */
        .signature-section {
            margin-top: 30px;
        }

        .signatures-wrapper {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .signatures-wrapper td {
            border: none;
            padding: 0;
        }

        .sig-col-left {
            width: 55%;
            vertical-align: top;
        }

        .sig-col-right {
            width: 45%;
            vertical-align: bottom;
            text-align: right;
        }

        .sig-company {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
            color: #333;
        }

        .sig-table {
            width: 260px;
            border-collapse: collapse;
            border: 1px solid #333;
            text-align: center;
        }

        .sig-table th {
            border: 1px solid #333;
            padding: 6px;
            font-size: 11px;
            font-weight: bold;
            background: #f9f9f9;
        }

        .sig-table .sig-space {
            height: 72px;
            border: 1px solid #333;
            vertical-align: middle;
            text-align: center;
            padding: 3px 2px;
        }

        .sig-table .sig-image {
            max-height: 44px;
            max-width: 110px;
            display: block;
            margin: 0 auto;
        }

        .sig-timestamp {
            font-size: 7.5px;
            color: #0038a8;
            margin-top: 2px;
            line-height: 1.2;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .sig-table .sig-name {
            border: 1px solid #333;
            padding: 5px 4px;
            font-size: 10px;
            font-weight: bold;
            background: #f9f9f9;
            min-height: 18px;
        }

        .confirm-table {
            width: 220px;
            margin-left: auto;
            border-collapse: collapse;
            border: 1px solid #333;
            text-align: center;
        }

        .confirm-table th {
            border: 1px solid #333;
            padding: 6px;
            font-size: 11px;
            font-weight: bold;
            background: #f9f9f9;
        }

        .confirm-table .confirm-space {
            height: 93px;
            border: 1px solid #333;
            vertical-align: bottom;
            padding: 6px;
            text-align: center;
        }

        .confirm-hint {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #d35400;
            font-size: 10px;
        }

        .footer .branch-name {
            font-weight: bold;
            color: #d35400;
            display: inline;
        }

        .footer .branch-address {
            display: inline;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .container {
                padding: 10px 15px;
            }
        }

        /* Rejected Watermark */
        .watermark-rejected {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(220, 53, 69, 0.25);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 9999;
            letter-spacing: 10px;
        }

        /* Signature Image */
        .sig-image {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @php
                $defaultLogoFile = public_path('images/logo-pangan-lestari.png');
                $defaultLogoData = file_exists($defaultLogoFile) 
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($defaultLogoFile)) 
                    : asset('images/logo-pangan-lestari.png');
            @endphp

            @if ($po->branch && $po->branch->logo_base64)
                <img src="{{ $po->branch->logo_base64 }}" alt="Logo" class="header-logo">
            @elseif ($po->branch && $po->branch->logo)
                @php
                    $logoFile = storage_path('app/public/' . $po->branch->logo);
                    if (isset($isPdf) && $isPdf && file_exists($logoFile)) {
                        $logoType = pathinfo($logoFile, PATHINFO_EXTENSION);
                        $logoData = base64_encode(file_get_contents($logoFile));
                        $logoPath = 'data:image/' . $logoType . ';base64,' . $logoData;
                    } else {
                        $logoPath = asset('storage/' . $po->branch->logo);
                    }
                @endphp
                <img src="{{ $logoPath }}" alt="Logo" class="header-logo">
            @else
                <img src="{{ $defaultLogoData }}" alt="PT. PANGAN LESTARI" class="header-logo">
            @endif

        </div>

        <!-- Title -->
        <div class="title-section">
            <h1>{{ $po->status == 'draft' ? 'DRAFT ' : '' }}PURCHASE ORDER</h1>
            @if ($po->branch && $po->branch->npwp)
                <div class="title-npwp">NPWP: {{ $po->branch->npwp }}</div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <!-- Vendor Column -->
            <div class="vendor-col">
                <div class="vendor-box">
                    <div class="kepada">KEPADA:</div>
                    <div class="vendor-name">{{ $po->vendor->name ?? '-' }}</div>
                    <div class="vendor-address">
                        @if ($po->vendor->address)
                            {{ $po->vendor->address }}
                        @endif
                    </div>
                    <div class="vendor-contact">
                        @if ($po->vendor->phone)
                            <div class="contact-row">
                                <span class="contact-label">TELP/FAX</span>: {{ $po->vendor->phone }}
                                @if ($po->vendor->email)
                                    / {{ $po->vendor->email }}
                                @endif
                            </div>
                        @endif
                        @if ($po->vendor->contact_person)
                            <div class="contact-row">
                                <span class="contact-label">ATT</span>: {{ $po->vendor->contact_person }}
                                @if ($po->vendor->contact_phone)
                                    ({{ $po->vendor->contact_phone }})
                                @endif
                            </div>
                        @endif
                        @if ($po->vendor->npwp)
                            <div class="contact-row">
                                <span class="contact-label">NPWP</span>: {{ $po->vendor->npwp }}
                                @if ($po->vendor->npwp_type)
                                    <span style="font-size: 8pt; color: #555;">({{ $po->vendor->npwp_type }})</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PO Info Column -->
            <div class="po-col">
                <table class="po-info-table">
                    <tr>
                        <td class="label">PO :</td>
                        <td class="value po-number">{{ $po->po_number }}</td>
                    </tr>
                    @if ($po->purchaseRequest)
                        <tr>
                            <td class="label">NO. PR:</td>
                            <td class="value">{{ $po->purchaseRequest->pr_number }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="label">DATE:</td>
                        <td class="value">
                            {{ $po->required_date ? $po->required_date->format('d F Y') : $po->created_at->format('d F Y') }}
                        </td>
                    </tr>
                    @if ($po->payment_due_date)
                        <tr>
                            <td class="label">DUE DATE:</td>
                            <td class="value">{{ $po->payment_due_date->format('d F Y') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="label">TERM:</td>
                        <td class="value">{{ $po->paymentTerm->name ?? 'COD' }}</td>
                    </tr>
                    <tr>
                        <td class="label">REVISI:</td>
                        <td class="value">
                            @if ($po->revision_count > 0)
                                Revisi ke {{ $po->revision_count }}
                                @if ($po->revised_at)
                                    <span style="font-size: 8pt; font-weight: normal; color: #555;">({{ $po->revised_at->format('d/m/Y') }})</span>
                                @endif
                            @else
                                Revisi ke 0
                            @endif
                        </td>
                    </tr>
                </table>

                @if ($po->note)
                    <div class="referensi-box">
                        <div class="ref-title">REFERENSI:</div>
                        <div class="ref-value">{{ $po->note }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="item-no">NO</th>
                    <th>DESCRIPTION</th>
                    <th class="text-center" width="80">QTY</th>
                    <th class="text-right" width="80">HARGA</th>
                    <th class="text-right" width="80">DISC/UNIT</th>
                    <th class="text-right" width="90">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($po->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td class="item-desc">{{ $detail->product->name ?? '-' }}</td>
                        <td class="text-center qty-col">{{ number_format($detail->quantity, 0) }}
                            {{ $detail->product->unit ?? 'Pcs' }}</td>
                        <td class="text-right">{{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">
                            @if ($detail->discount_amount > 0)
                                {{ number_format($detail->discount_amount / $detail->quantity, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-right">
                            {{ number_format($detail->subtotal_after_discount ?: $detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <!-- Notes Column -->
            <div class="notes-col">
                <div class="notes-box">
                    <div class="notes-title">Catatan :</div>
                    <ol>
                        <li>Harap catatkan Nomor P/O ini di dalam Surat Jalan, Faktur dan kwitansi</li>
                        <li>Harap Copy Order Pembelian dilampirkan pada surat penagihan</li>
                        <li>Jika barang tidak sesuai dengan pesanan maka akan dikembalikan dengan menjadi beban penjual
                        </li>
                        <li><strong>Tgl pengiriman</strong> :
                            {{ $po->required_date ? $po->required_date->format('d/m/Y') : 'a.s.a.p' }}</li>
                        <li><strong>Lokasi Pengiriman</strong> : {{ $po->branch->name ?? '-' }}
                            @if ($po->branch && $po->branch->address)
                                <div class="delivery-info">{{ $po->branch->address }}</div>
                            @endif
                        </li>
                        <li>Mohon kirim konfirmasi PO setelah diterima</li>
                    </ol>
                </div>
            </div>

            <!-- Totals Column -->
            <div class="totals-col">
                <table class="totals-table">
                    <tr>
                        <td class="tot-label">SUBTOTAL</td>
                        <td class="tot-value">{{ number_format($po->subtotal ?? $po->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if ($po->discount_amount > 0)
                        <tr>
                            <td class="tot-label">Diskon</td>
                            <td class="tot-value">{{ number_format($po->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if ($po->ppn_amount > 0)
                        <tr>
                            <td class="tot-label">Tax</td>
                            <td class="tot-value">{{ $po->ppn_percentage }}%</td>
                        </tr>
                    @endif
                    @if ($po->pph_amount > 0)
                        <tr>
                            <td class="tot-label">PPh</td>
                            <td class="tot-value" style="color: #c00;">-{{ number_format($po->pph_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td class="tot-label">GRAND TOTAL</td>
                        <td class="tot-value">{{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Rejected Watermark -->
        @if ($po->status == 'rejected')
            <div class="watermark-rejected">REJECTED</div>
        @endif

        <!-- Signature & Confirmation Section -->
        <div class="signature-section">
            <table class="signatures-wrapper">
                <tr>
                    <td class="sig-col-left">
                        <div class="sig-company">{{ $po->branch->name ?? config('app.name') }}</div>
                        <table class="sig-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Dibuat</th>
                                    <th style="width: 50%;">Disetujui</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="sig-space">
                                        @if ($po->creator_signature)
                                            @if (\Illuminate\Support\Str::startsWith($po->creator_signature, 'data:image'))
                                                <img src="{{ $po->creator_signature }}" alt="TTD" class="sig-image">
                                            @else
                                                <img src="{{ asset('storage/' . $po->creator_signature) }}" alt="TTD" class="sig-image">
                                            @endif
                                            <div class="sig-timestamp">
                                                Ditandatangani Pada: {{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }} WIB
                                            </div>
                                        @endif
                                    </td>
                                    <td class="sig-space">
                                        @if ($po->approver_signature)
                                            @if (\Illuminate\Support\Str::endsWith(strtolower($po->approver_signature), '.pdf'))
                                                <div style="font-size: 8pt; color: #166534; font-weight: bold; border: 1px dashed #166534; padding: 4px; border-radius: 4px; text-align: center;">
                                                    Dokumen Disetujui<br>(PDF Terlampir)
                                                </div>
                                            @elseif (\Illuminate\Support\Str::startsWith($po->approver_signature, 'data:image'))
                                                <img src="{{ $po->approver_signature }}" alt="TTD" class="sig-image">
                                            @else
                                                <img src="{{ asset('storage/' . $po->approver_signature) }}" alt="TTD" class="sig-image">
                                            @endif
                                            <div class="sig-timestamp">
                                                Ditandatangani Pada: {{ $po->approved_at ? $po->approved_at->format('d/m/Y H:i') : '-' }} WIB
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="sig-name">{{ $po->creator->name ?? '' }}</td>
                                    <td class="sig-name">{{ $po->approver->name ?? '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="sig-col-right">
                        <table class="confirm-table">
                            <thead>
                                <tr>
                                    <th>Konfirmasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="confirm-space">
                                        <div class="confirm-hint">ttd,nama,tanggal diterima</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <span class="branch-name">{{ $po->branch->name ?? '' }}</span>
            @if ($po->branch && $po->branch->address)
                <span class="branch-address">: {{ $po->branch->address }}</span>
            @endif
        </div>
    </div>
</body>

</html>
