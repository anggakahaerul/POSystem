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
            max-height: 60px;
            max-width: 200px;
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
            margin: 25px 0;
        }

        .title-section h1 {
            font-size: 26px;
            font-weight: bold;
            color: #b8860b;
            letter-spacing: 3px;
            text-transform: uppercase;
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
            font-weight: bold;
            color: #d35400;
        }

        .po-info-table .po-number {
            font-size: 13px;
            color: #333;
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
            color: #d35400;
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

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
        }

        .sig-company {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sig-boxes {
            display: table;
            width: 300px;
        }

        .sig-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .sig-box .sig-title {
            /* border: 1px solid #333; */
            border-bottom: none;
            padding: 8px;
            font-weight: bold;
            background: #f9f9f9;
        }

        .sig-box .sig-space {
            /* border-left: 1px solid #333; */
            /* border-right: 1px solid #333; */
            height: 70px;
        }

        .sig-box .sig-name {
            /* border: 1px solid #333; */
            padding: 8px;
            font-weight: bold;
            text-decoration: underline;
            min-height: 20px;
            background: #f9f9f9;
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
            @if ($po->branch && $po->branch->logo)
                @php
                    $logoPath =
                        isset($isPdf) && $isPdf
                            ? public_path('storage/' . $po->branch->logo)
                            : asset('storage/' . $po->branch->logo);
                @endphp
                <img src="{{ $logoPath }}" alt="Logo" class="header-logo">
            @else
                <div class="header-company">{{ $po->branch->name ?? config('app.name') }}</div>
                @if ($po->branch && $po->branch->npwp)
                    <div class="header-tagline">NPWP: {{ $po->branch->npwp }}</div>
                @endif
            @endif
        </div>

        <!-- Title -->
        <div class="title-section">
            <h1>{{ $po->status == 'draft' ? 'DRAFT ' : '' }}PURCHASE ORDER</h1>
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

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="sig-company">{{ $po->branch->name ?? config('app.name') }}</div>
            <div class="sig-boxes">
                <div class="sig-box">
                    <div class="sig-title">Dibuat</div>
                    <div class="sig-space">
                        @if ($po->creator_signature)
                            <img src="{{ $po->creator_signature }}" alt="TTD" class="sig-image">
                        @endif
                    </div>
                    <div class="sig-name">{{ $po->creator->name ?? '' }}</div>
                </div>
                <div class="sig-box">
                    <div class="sig-title">Disetujui</div>
                    <div class="sig-space">
                        @if ($po->approver_signature)
                            <img src="{{ $po->approver_signature }}" alt="TTD" class="sig-image">
                        @endif
                    </div>
                    <div class="sig-name">{{ $po->approver->name ?? '' }}</div>
                </div>
            </div>
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
