<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Status Persetujuan PO' }} - PT. Pangan Lestari</title>

    <!-- Google Font: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .status-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.05);
            max-width: 560px;
            width: 100%;
            overflow: hidden;
            text-align: center;
        }

        .status-header {
            padding: 30px 24px 20px;
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 18px;
        }

        .icon-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .icon-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .icon-warning {
            background-color: #fef3c7;
            color: #b45309;
        }

        .icon-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .status-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .status-body {
            padding: 0 32px 30px;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .po-badge-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 18px 0;
            text-align: left;
        }

        .logo-img {
            max-height: 40px;
            width: auto;
            margin-bottom: 20px;
        }

        .footer-note {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 24px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="status-card">
        <div class="status-header">
            <img src="{{ asset('images/logo-pangan-lestari.png') }}" alt="PT. Pangan Lestari" class="logo-img" onerror="this.src='{{ asset('storage/logos/logo-pangan-lestari.png') }}'">
            
            @if(in_array($type, ['success_approved']))
                <div class="icon-circle icon-success">
                    <i class="fas fa-check"></i>
                </div>
            @elseif(in_array($type, ['success_rejected']))
                <div class="icon-circle icon-danger">
                    <i class="fas fa-times"></i>
                </div>
            @elseif(in_array($type, ['expired', 'already_processed']))
                <div class="icon-circle icon-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            @else
                <div class="icon-circle icon-info">
                    <i class="fas fa-info"></i>
                </div>
            @endif

            <h1 class="status-title">{{ $title }}</h1>
        </div>

        <div class="status-body">
            <p class="mb-0">{{ $message }}</p>

            @if(isset($po) && $po)
                <div class="po-badge-info">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.82rem;">Nomor PO:</span>
                        <strong class="text-dark">{{ $po->po_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.82rem;">Vendor:</span>
                        <span class="font-weight-bold text-dark">{{ $po->vendor->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.82rem;">Total Nominal:</span>
                        <span class="font-weight-bold text-primary">{{ $po->formatted_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 0.82rem;">Status Terkini:</span>
                        <span class="badge badge-{{ $po->status === 'approved' ? 'success' : ($po->status === 'rejected' ? 'danger' : 'secondary') }}">
                            {{ strtoupper($po->status) }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <button type="button" onclick="window.close()" class="btn btn-outline-secondary font-weight-bold px-4 py-2" style="border-radius: 6px; font-size: 0.88rem;">
                    <i class="fas fa-times-circle mr-1"></i> Tutup Jendela Ini
                </button>
            </div>
        </div>
    </div>

    <div class="footer-note">
        PT. PANGAN LESTARI &bull; Dokumen Resmi & Otorisasi Pengadaan
    </div>

</body>
</html>
