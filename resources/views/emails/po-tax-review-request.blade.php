<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permohonan Review Pajak PO - PT. Pangan Lestari</title>
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
            border-bottom: 3px solid #f59e0b;
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
        .badge-tax {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #fde68a;
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
        .btn-review {
            display: inline-block;
            padding: 13px 32px;
            background-color: #0284c7;
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
            <div class="header-subtitle">Permohonan Review Pajak Purchase Order (PPh)</div>
        </div>
        
        <div class="content">
            <p class="greeting">Yth. Tim Pajak / Tax Reviewer,</p>
            
            <p style="font-size: 14px; color: #334155; margin-bottom: 16px;">
                Terdapat Purchase Order yang mengenakan pajak penghasilan (PPh) dan membutuhkan peninjauan serta verifikasi dari Tim Pajak sebelum diajukan untuk persetujuan (approval) atasan:
            </p>

            <table class="info-table">
                <tr>
                    <td class="label">Nomor PO</td>
                    <td class="value">
                        <strong style="color: #0284c7; font-size: 15px;">{{ $po->po_number }}</strong>
                        <span class="badge-tax" style="margin-left: 6px;">Butuh Review Pajak</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Pemasok / Vendor</td>
                    <td class="value">
                        <strong>{{ $po->vendor->name ?? '-' }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">NPWP & Tipe NPWP</td>
                    <td class="value">
                        {{ $po->vendor->npwp ? $po->vendor->npwp : '(Belum terisi)' }}
                        @if(!empty($po->vendor->npwp_type))
                            <span style="color: #64748b; font-size: 12px;">({{ $po->vendor->npwp_type }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Pembuat Dokumen</td>
                    <td class="value">{{ $po->creator->name ?? '-' }} ({{ $po->creator->email ?? '-' }})</td>
                </tr>
                <tr>
                    <td class="label">Cabang Pemesan</td>
                    <td class="value">{{ $po->branch->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Subtotal Dokumen</td>
                    <td class="value">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
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
                    <td class="label">PPh Diajukan</td>
                    <td class="value" style="color: #dc2626; font-weight: 600;">
                        {{ $po->pph_label }} (-Rp {{ number_format($po->pph_amount, 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Total Nilai PO</td>
                    <td class="value total-amount">{{ $po->formatted_total }}</td>
                </tr>
            </table>

            @if(!empty($notes))
                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px;">
                    <strong style="font-size: 13px; color: #92400e;">Pesan / Catatan dari Pemohon:</strong>
                    <div style="font-size: 13px; color: #78350f; margin-top: 4px;">{{ $notes }}</div>
                </div>
            @endif

            <div class="action-box">
                <a href="{{ $reviewUrl }}" class="btn-review">
                    Buka & Review Pajak PO
                </a>
                <div class="notice">
                    Dokumen cetak PO dalam format <strong>PDF</strong> telah dilampirkan bersama email ini.<br>
                    Silakan klik tombol di atas untuk membuka formulir verifikasi/koreksi pajak pada sistem.
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
