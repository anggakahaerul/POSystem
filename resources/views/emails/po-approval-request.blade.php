<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permintaan Approval PO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 140px;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
        .btn:hover {
            background: #218838;
        }
        .footer {
            text-align: center;
            padding: 15px;
            color: #666;
            font-size: 12px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">🛒 Permintaan Approval Purchase Order</h2>
    </div>
    
    <div class="content">
        <p>Yth. Manager,</p>
        
        <p>Ada Purchase Order baru yang memerlukan persetujuan Anda:</p>
        
        <table class="info-table">
            <tr>
                <td>No. PO</td>
                <td><strong>{{ $po->po_number }}</strong></td>
            </tr>
            <tr>
                <td>Vendor</td>
                <td>{{ $po->vendor->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td>{{ $po->branch->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Diajukan Oleh</td>
                <td>{{ $po->creator->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ $po->created_at->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jumlah Item</td>
                <td>{{ $po->details->count() }} item</td>
            </tr>
            <tr>
                <td>Total</td>
                <td class="amount">{{ $po->formatted_total }}</td>
            </tr>
        </table>
        
        @if($po->note)
        <p><strong>Catatan:</strong><br>{{ $po->note }}</p>
        @endif
        
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $approvalUrl }}" class="btn">
                ✅ Lihat & Approve PO
            </a>
        </div>
        
        <p style="color: #666; font-size: 13px;">
            Klik tombol di atas untuk langsung masuk ke aplikasi dan melakukan approval.
            <br>File PDF PO terlampir di email ini.
        </p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem PO Invoice.<br>
        Harap tidak membalas email ini.</p>
    </div>
</body>
</html>
