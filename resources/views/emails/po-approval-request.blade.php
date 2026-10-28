<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permintaan Persetujuan Purchase Order - PT. Pangan Lestari</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        .header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 24px 28px;
            text-align: left;
        }
        .header-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.2px;
        }
        .header-subtitle {
            font-size: 13px;
            color: #cbd5e1;
            margin-top: 4px;
        }
        .content {
            padding: 28px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .info-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td.label {
            font-weight: 600;
            width: 150px;
            color: #64748b;
            background-color: #f8fafc;
        }
        .info-table td.value {
            color: #1e293b;
            font-weight: 500;
        }
        .total-amount {
            font-size: 18px;
            font-weight: 700;
            color: #0f766e;
        }
        .action-box {
            text-align: center;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 22px 20px;
            margin: 24px 0;
        }
        .btn-approve {
            display: inline-block;
            padding: 13px 32px;
            background-color: #15803d;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.2px;
        }
        .notice {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
            margin-top: 14px;
        }
        .footer {
            text-align: center;
            padding: 18px 24px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">PT. PANGAN LESTARI</div>
            <div class="header-subtitle">Permohonan Persetujuan Purchase Order</div>
        </div>
        
        <div class="content">
            <p class="greeting">Yth. Bapak/Ibu Manager & Atasan,</p>
            
            <p style="font-size: 14px; color: #334155; margin-bottom: 16px;">
                Terdapat pengajuan Purchase Order baru yang menunggu peninjauan dan persetujuan dari Anda:
            </p>
            
            <table class="info-table">
                <tr>
                    <td class="label">Nomor PO</td>
                    <td class="value"><strong>{{ $po->po_number }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Pemasok / Vendor</td>
                    <td class="value">{{ $po->vendor->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Cabang Pemesan</td>
                    <td class="value">{{ $po->branch->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Pembuat Dokumen</td>
                    <td class="value">{{ $po->creator->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pengajuan</td>
                    <td class="value">{{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Item</td>
                    <td class="value">{{ $po->details->count() }} item</td>
                </tr>
                <tr>
                    <td class="label">Total Nilai PO</td>
                    <td class="value total-amount">{{ $po->formatted_total }}</td>
                </tr>
            </table>
            
            @if(!empty($po->note))
                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px;">
                    <strong style="font-size: 13px; color: #92400e;">Catatan Pengaju:</strong>
                    <div style="font-size: 13px; color: #78350f; margin-top: 4px;">{{ $po->note }}</div>
                </div>
            @endif

            @if(!empty($po->creator_signature))
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #475569; margin-bottom: 8px; letter-spacing: 0.5px;">
                        Tanda Tangan Pengaju / Pembuat PO:
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 135px; vertical-align: middle; padding: 0;">
                                @if(\Illuminate\Support\Str::startsWith($po->creator_signature, 'data:image'))
                                    <img src="{{ $po->creator_signature }}" alt="Tanda Tangan" style="max-height: 52px; max-width: 130px; display: block;">
                                @else
                                    <img src="{{ asset('storage/' . $po->creator_signature) }}" alt="Tanda Tangan" style="max-height: 52px; max-width: 130px; display: block;">
                                @endif
                            </td>
                            <td style="vertical-align: middle; padding-left: 14px;">
                                <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $po->creator->name ?? 'Staff Purchasing' }}</div>
                                <div style="font-size: 11px; color: #0038a8; margin-top: 3px; font-weight: 600;">
                                    Ditandatangani Pada: {{ $po->created_at ? $po->created_at->format('d/m/Y H:i') : '-' }} WIB
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
            
            <div class="action-box">
                <a href="{{ $approvalUrl }}" class="btn-approve">
                    Tinjau & Setujui Purchase Order
                </a>
                <div class="notice">
                    <strong>Pemberitahuan:</strong> Anda dapat langsung menyetujui atau menolak dokumen melalui tautan di atas tanpa perlu login ke sistem.
                    <br>Tautan persetujuan ini aman dan berlaku selama 72 jam.
                </div>
            </div>
            
            <p style="color: #64748b; font-size: 13px; margin-bottom: 0;">
                Lampiran dokumen resmi PDF Purchase Order telah disertakan dalam email ini untuk arsip Anda.
            </p>
        </div>
        
        <div class="footer">
            Email ini dibuat secara otomatis oleh Sistem Pembelian & Persediaan PT. Pangan Lestari.<br>
            Harap tidak membalas email ini secara langsung.
        </div>
    </div>
</body>
</html>
