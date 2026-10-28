<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PO Ditolak - PT. Pangan Lestari</title>
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
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 24px 28px;
            text-align: left;
            border-bottom: 3px solid #ef4444;
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
        .status-banner {
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
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
            width: 165px;
            color: #64748b;
            background-color: #f8fafc;
        }
        .info-table td.value {
            color: #1e293b;
            font-weight: 500;
        }
        .total-amount {
            font-size: 17px;
            font-weight: 700;
            color: #0f766e;
        }
        .reason-box {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 20px 0;
        }
        .action-box {
            text-align: center;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 22px 20px;
            margin: 24px 0;
        }
        .btn-action {
            display: inline-block;
            padding: 13px 32px;
            background-color: #dc2626;
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
            <div class="header-subtitle">Pemberitahuan Penolakan Purchase Order</div>
        </div>
        
        <div class="content">
            <p class="greeting">Halo {{ $po->creator->name ?? 'Pembuat PO' }},</p>
            
            <div class="status-banner">
                <strong>Purchase Order Ditolak</strong>
                <div style="margin-top: 4px; font-size: 13px;">
                    Pengajuan PO nomor <strong>{{ $po->po_number }}</strong> tidak disetujui / ditolak oleh atasan.
                </div>
            </div>

            <div class="reason-box">
                <strong style="color: #9a3412; font-size: 13px;">Alasan Penolakan:</strong>
                <div style="color: #7c2d12; font-size: 14px; margin-top: 6px; font-style: italic;">
                    "{{ $po->rejection_reason ?? 'Tidak ada keterangan penolakan.' }}"
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Nomor PO</td>
                    <td class="value"><strong style="color: #0284c7; font-size: 15px;">{{ $po->po_number }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Pemasok / Vendor</td>
                    <td class="value"><strong>{{ $po->vendor->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Cabang Pemesan</td>
                    <td class="value">{{ $po->branch->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Ditolak Oleh</td>
                    <td class="value">
                        <span style="color: #dc2626; font-weight: 700;">{{ $po->approver->name ?? $po->approver_email ?? 'Manager' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Total Nilai PO</td>
                    <td class="value total-amount">{{ $po->formatted_total }}</td>
                </tr>
            </table>

            <div class="action-box">
                <a href="{{ $poUrl }}" class="btn-action">
                    Buka Detail PO di Sistem
                </a>
                <div class="notice">
                    Silakan tinjau kembali alasan penolakan dan koordinasikan dengan atasan terkait revisi PO yang diperlukan.
                </div>
            </div>
        </div>

        <div class="footer">
            Email otomatis dari Sistem Procurement & Purchase Order PT. Pangan Lestari.<br>
            Harap jangan membalas langsung ke alamat email ini.
        </div>
    </div>
</body>
</html>
