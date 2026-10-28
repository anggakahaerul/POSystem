<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Review Pajak Selesai - PT. Pangan Lestari</title>
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
            border-bottom: 3px solid {{ $actionType === 'verify' ? '#10b981' : '#3b82f6' }};
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
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .status-banner.verify {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .status-banner.revise {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
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
            <div class="header-subtitle">Hasil Review Pajak Purchase Order (PPh)</div>
        </div>
        
        <div class="content">
            <p class="greeting">Halo {{ $po->creator->name ?? 'Pembuat PO' }},</p>
            
            <div class="status-banner {{ $actionType === 'verify' ? 'verify' : 'revise' }}">
                @if($actionType === 'verify')
                    <div>
                        <strong>Review Pajak Selesai & Terverifikasi:</strong> PO <strong>{{ $po->po_number }}</strong> telah diverifikasi sesuai oleh Tim Pajak. Anda kini dapat mengajukan PO ini untuk Approval atasan.
                    </div>
                @else
                    <div>
                        <strong>Review Pajak Telah Dikoreksi:</strong> Pengaturan pajak pada PO <strong>{{ $po->po_number }}</strong> telah disesuaikan/dikoreksi oleh Tim Pajak. Anda kini dapat melanjutkan pengajuan Approval.
                    </div>
                @endif
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Nomor PO</td>
                    <td class="value">
                        <strong style="color: #0284c7; font-size: 15px;">{{ $po->po_number }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">Pemasok / Vendor</td>
                    <td class="value"><strong>{{ $po->vendor->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Status Review Pajak</td>
                    <td class="value">
                        @if($po->tax_review_status === 'verified')
                            <span style="color: #059669; font-weight: 700;">Verifikasi Sesuai</span>
                        @else
                            <span style="color: #2563eb; font-weight: 700;">Telah Dikoreksi</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Ditinjau Oleh</td>
                    <td class="value">{{ $po->taxReviewer->name ?? 'Tim Pajak' }} ({{ $po->tax_reviewed_at ? $po->tax_reviewed_at->format('d/m/Y H:i') : '-' }})</td>
                </tr>
                <tr>
                    <td class="label">PPN</td>
                    <td class="value">
                        @if($po->ppn_percentage > 0)
                            {{ (float)$po->ppn_percentage }}% (+Rp {{ number_format($po->ppn_amount, 0, ',', '.') }})
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">PPh Akhir</td>
                    <td class="value" style="color: #dc2626; font-weight: 600;">
                        {{ $po->pph_label }} (-Rp {{ number_format($po->pph_amount, 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Total Nilai PO Akhir</td>
                    <td class="value total-amount">{{ $po->formatted_total }}</td>
                </tr>
                @if(!empty($po->tax_notes))
                    <tr>
                        <td class="label">Catatan Tim Pajak</td>
                        <td class="value" style="color: #b45309; font-style: italic;">
                            {{ $po->tax_notes }}
                        </td>
                    </tr>
                @endif
            </table>

            <div class="action-box">
                <a href="{{ $poUrl }}" class="btn-action">
                    Buka Detail PO & Lanjutkan Submit
                </a>
                <div class="notice">
                    Dokumen revisi PO dalam format <strong>PDF</strong> telah dilampirkan bersama email ini.<br>
                    Silakan klik tombol di atas untuk membuka dokumen PO dan mengajukan tanda tangan/approval ke atasan.
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
