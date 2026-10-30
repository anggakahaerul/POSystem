<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence_number',
        'asset_code',
        'is_qr_generated',
        'purchase_order_id',
        'purchase_order_detail_id',
        'product_id',
        'branch_id',
        'cabang',
        'kategori',
        'divisi',
        'user_holder',
        'nama_barang',
        'status',
        'merk',
        'processor',
        'model',
        'generasi',
        'ram',
        'harddisk',
        'serial_number',
        'os',
        'warna',
        'harga',
        'supplier',
        'tanggal_terima',
        'deskripsi',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'is_qr_generated' => 'boolean',
        'harga' => 'decimal:2',
        'tanggal_terima' => 'date',
    ];

    public function histories()
    {
        return $this->hasMany(AssetHistory::class)->orderBy('waktu', 'asc')->orderBy('id', 'asc');
    }

    public function repairs()
    {
        return $this->hasMany(AssetRepair::class)->orderBy('waktu', 'asc')->orderBy('id', 'asc');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Pastikan tabel assets, asset_histories, dan asset_repairs sudah tersedia di database.
     */
    public static function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('assets')) {
                Schema::create('assets', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedInteger('sequence_number')->index();
                    $table->string('asset_code', 60)->unique();
                    $table->boolean('is_qr_generated')->default(false);
                    $table->unsignedBigInteger('purchase_order_id')->nullable()->index();
                    $table->unsignedBigInteger('purchase_order_detail_id')->nullable()->index();
                    $table->unsignedBigInteger('product_id')->nullable()->index();
                    $table->unsignedBigInteger('branch_id')->nullable()->index();

                    $table->string('cabang', 120)->default('Sidoarjo');
                    $table->string('kategori', 120)->default('Elektronik');
                    $table->string('divisi', 120)->default('HRGA');
                    $table->string('user_holder', 150)->default('Pool Gudang / GA');
                    $table->string('nama_barang', 255);
                    $table->string('status', 50)->default('Tersedia');

                    $table->string('merk', 120)->nullable()->default('-');
                    $table->string('processor', 150)->nullable()->default('-');
                    $table->string('model', 150)->nullable()->default('-');
                    $table->string('generasi', 100)->nullable()->default('-');
                    $table->string('ram', 100)->nullable()->default('-');
                    $table->string('harddisk', 120)->nullable()->default('-');
                    $table->string('serial_number', 150)->nullable()->default('-');
                    $table->string('os', 120)->nullable()->default('-');
                    $table->string('warna', 100)->nullable()->default('-');
                    $table->decimal('harga', 15, 2)->default(0);
                    $table->string('supplier', 200)->nullable()->default('-');
                    $table->date('tanggal_terima')->nullable();
                    $table->text('deskripsi')->nullable();

                    $table->timestamps();
                });
            } elseif (!Schema::hasColumn('assets', 'is_qr_generated')) {
                Schema::table('assets', function (Blueprint $table) {
                    $table->boolean('is_qr_generated')->default(false)->after('asset_code');
                });
            }

            if (!Schema::hasTable('asset_histories')) {
                Schema::create('asset_histories', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                    $table->dateTime('waktu');
                    $table->string('pic', 150)->default('Sistem');
                    $table->string('cabang', 120)->default('-');
                    $table->string('user_holder', 150)->default('-');
                    $table->string('aksi', 120);
                    $table->text('deskripsi')->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('asset_repairs')) {
                Schema::create('asset_repairs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                    $table->dateTime('waktu');
                    $table->text('deskripsi');
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to ensure asset tables exist: ' . $e->getMessage());
        }
    }

    /**
     * Format nama cabang menjadi nama penuh KAPITAL tanpa singkatan
     * Contoh: "Branch Sidoarjo" -> "SIDOARJO", "Cabang Surabaya" -> "SURABAYA", "Head Office (Jakarta)" -> "JAKARTA"
     */
    public static function formatBranchForBarcode(?string $cabang): string
    {
        if (empty($cabang)) {
            return 'SIDOARJO';
        }

        // Hilangkan kata awalan umum seperti Branch, Cabang, Head Office, Kantor Pusat, DC
        $cleaned = preg_replace('/\b(head\s*office|kantor\s*pusat|branch|cabang|dc)\b/i', '', $cabang);
        $cleaned = preg_replace('/[^a-zA-Z]/', '', $cleaned ?? '');
        $upper = strtoupper($cleaned);

        if (empty($upper)) {
            $upper = strtoupper(preg_replace('/[^a-zA-Z]/', '', $cabang));
        }

        return $upper ?: 'SIDOARJO';
    }

    /**
     * Format 3 huruf awal kategori dalam KAPITAL
     * Contoh: "Scanner & Printer" -> "SCA", "Elektronik" -> "ELE"
     */
    public static function formatCategoryForBarcode(?string $kategori): string
    {
        $cleanKat = preg_replace('/[^a-zA-Z]/', '', $kategori ?? 'AST');
        $prefix = strtoupper(substr($cleanKat, 0, 3));
        return str_pad($prefix ?: 'AST', 3, 'X');
    }

    /**
     * Generate Kode Barcode Aset: No (3 digit) + 3 Huruf Kategori Awal + NAMA CABANG KAPITAL PENUH
     * Contoh: 001SCASURABAYA, 002ELESIDOARJO
     */
    public static function generateAssetCode(int $sequenceNumber, ?string $kategori, ?string $cabang, ?int $ignoreAssetId = null): string
    {
        $numStr = str_pad((string) max(1, $sequenceNumber), 3, '0', STR_PAD_LEFT);
        $katPrefix = self::formatCategoryForBarcode($kategori);
        $branchFull = self::formatBranchForBarcode($cabang);

        $baseCode = "{$numStr}{$katPrefix}{$branchFull}";

        // Pastikan unik jika ada duplikasi urutan
        $candidate = $baseCode;
        $seq = $sequenceNumber;
        while (
            self::where('asset_code', $candidate)
                ->when($ignoreAssetId, fn($q) => $q->where('id', '!=', $ignoreAssetId))
                ->exists()
        ) {
            $seq++;
            $numStr = str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            $candidate = "{$numStr}{$katPrefix}{$branchFull}";
        }

        return $candidate;
    }

    /**
     * Ambil nomor urut (sequence_number) berikutnya khusus untuk kombinasi (3 Huruf Kategori + NAMA CABANG KAPITAL PENUH)
     * Hanya menghitung aset yang sudah di-generate QR Code-nya (is_qr_generated = true).
     */
    public static function nextSequenceNumberForGroup(?string $kategori, ?string $cabang, ?int $ignoreAssetId = null): int
    {
        $katPrefix = self::formatCategoryForBarcode($kategori);
        $branchFull = self::formatBranchForBarcode($cabang);
        $suffix = "{$katPrefix}{$branchFull}";

        $existingCodes = self::where('is_qr_generated', true)
            ->where('asset_code', 'like', "%{$suffix}")
            ->when($ignoreAssetId, fn($q) => $q->where('id', '!=', $ignoreAssetId))
            ->pluck('asset_code');

        $maxSeq = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/^(\d+)' . preg_quote($suffix, '/') . '$/i', (string) $code, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
        }

        return $maxSeq + 1;
    }

    /**
     * Ambil nomor urut (sequence_number) berikutnya
     */
    public static function nextSequenceNumber(): int
    {
        $max = (int) self::where('is_qr_generated', true)->max('sequence_number');
        return $max + 1;
    }

    /**
     * OPSI A: Auto-Sync unit barang yang diterima dari PurchaseOrderDetail ke tabel Assets
     */
    public static function syncFromPurchaseOrderDetail(PurchaseOrderDetail $detail, int $qtyToCreate, ?int $receiverId = null): array
    {
        if ($qtyToCreate <= 0) {
            return [];
        }

        self::ensureTablesExist();

        $detail->loadMissing([
            'product.category',
            'purchaseOrder.branch',
            'purchaseOrder.vendor',
            'purchaseOrder.department',
            'purchaseOrder.creator.department',
        ]);

        $po = $detail->purchaseOrder;
        $product = $detail->product;
        $category = $product?->category;
        $branch = $po?->branch;
        $vendor = $po?->vendor;

        $cabangName = $branch?->name ?? 'Branch Sidoarjo';
        $kategoriName = $category?->name ?? 'Elektronik & IT';

        // Utamakan Tipe Asset dari Kategori Produk pada PO (contoh: HRGA atau MIS), bukan departemen user pembuat PO
        $divisiName = !empty($category?->asset)
            ? $category->asset
            : ($po?->department?->name ?? $po?->creator?->department?->name ?? 'HRGA');

        $receiverUser = $receiverId ? User::find($receiverId) : auth()->user();
        $picName = $receiverUser?->name ?? 'Admin Gudang (Auto-Sync PO)';

        $poNumber = $po?->po_number ?? '-';
        $supplierName = $vendor?->name ?? '-';
        $receivedDate = $detail->received_at ?? $po?->received_at ?? now();

        $createdAssets = [];

        for ($i = 0; $i < $qtyToCreate; $i++) {
            $tempCode = 'PENDING-' . strtoupper(uniqid());

            $descParts = [];
            if (!empty($detail->notes)) {
                $descParts[] = $detail->notes;
            }
            if (!empty($product?->description)) {
                $descParts[] = $product->description;
            }
            $cleanDesc = !empty($descParts) ? implode(' — ', $descParts) : '-';

            $asset = self::create([
                'sequence_number' => 0,
                'asset_code' => $tempCode,
                'is_qr_generated' => false,
                'purchase_order_id' => $po?->id,
                'purchase_order_detail_id' => $detail->id,
                'product_id' => $product?->id,
                'branch_id' => $branch?->id,
                'cabang' => $cabangName,
                'kategori' => $kategoriName,
                'divisi' => $divisiName,
                'user_holder' => 'Pool Gudang / Standby',
                'nama_barang' => $product?->name ?? 'Barang Inventaris PO',
                'status' => 'Tersedia',
                'merk' => '-',
                'processor' => '-',
                'model' => $product?->sku ?? '-',
                'generasi' => '-',
                'ram' => '-',
                'harddisk' => '-',
                'serial_number' => '-',
                'os' => '-',
                'warna' => '-',
                'harga' => $detail->unit_price ?? $product?->price ?? 0,
                'supplier' => $supplierName,
                'tanggal_terima' => $receivedDate,
                'deskripsi' => $cleanDesc,
            ]);

            $asset->asset_code = 'PENDING-' . $asset->id;
            $asset->save();

            $asset->histories()->create([
                'waktu' => $receivedDate,
                'pic' => $picName,
                'cabang' => $cabangName,
                'user_holder' => 'Pool Gudang / Standby',
                'aksi' => 'Barang Diterima (PO)',
                'deskripsi' => "Auto-Sync penerimaan barang dari PO {$poNumber}. Status awal: Tersedia.",
            ]);

            $createdAssets[] = $asset;
        }

        return $createdAssets;
    }

    /**
     * Sinkronisasi otomatis seluruh item PO yang sudah berstatus diterima (received_quantity > 0)
     * namun belum tercatat jumlah unitnya di tabel assets.
     */
    public static function syncAllUnregisteredReceivedPoItems(): int
    {
        self::ensureTablesExist();

        $receivedDetails = PurchaseOrderDetail::with([
            'product.category',
            'purchaseOrder.branch',
            'purchaseOrder.vendor',
            'purchaseOrder.department',
            'purchaseOrder.creator.department',
        ])
            ->where('received_quantity', '>', 0)
            ->whereHas('purchaseOrder', function ($q) {
                $q->where('status', PurchaseOrder::STATUS_APPROVED);
            })
            ->orderBy('id', 'asc')
            ->get();

        $totalSynced = 0;

        foreach ($receivedDetails as $detail) {
            $existingCount = self::where('purchase_order_detail_id', $detail->id)->count();
            $missingQty = (int) $detail->received_quantity - $existingCount;

            if ($missingQty > 0) {
                $created = self::syncFromPurchaseOrderDetail(
                    $detail,
                    $missingQty,
                    $detail->purchaseOrder?->received_by
                );
                $totalSynced += count($created);
            }
        }

        // Bersihkan deskripsi lama yang sebelumnya menggabungkan teks "Unit diterima otomatis dari PO ..."
        $legacyAssets = self::where('deskripsi', 'like', 'Unit diterima otomatis dari PO%')->get();
        foreach ($legacyAssets as $legacy) {
            $cleaned = preg_replace('/^Unit diterima otomatis dari PO [^\s—]+(\s*—\s*SKU:\s*[^\s—]+)?(\s*—\s*)?/i', '', (string) $legacy->deskripsi);
            $legacy->deskripsi = trim((string) $cleaned) !== '' ? trim((string) $cleaned) : '-';
            $legacy->save();
        }

        // Pastikan aset yang belum di-generate QR tidak memakan nomor urut sequence
        $unGeneratedAssets = self::where('is_qr_generated', false)
            ->where('asset_code', 'not like', 'PENDING-%')
            ->get();
        foreach ($unGeneratedAssets as $unGen) {
            $unGen->sequence_number = 0;
            $unGen->asset_code = 'PENDING-' . $unGen->id;
            $unGen->save();
        }

        // Sinkronkan juga kolom divisi & kategori pada aset PO yang sudah ada agar sesuai dengan Tipe Asset Kategori Produk (HRGA / MIS)
        $poAssets = self::with('product.category')->whereNotNull('product_id')->get();
        foreach ($poAssets as $poAsset) {
            $cat = $poAsset->product?->category;
            if ($cat && !empty($cat->asset) && $poAsset->divisi !== $cat->asset) {
                $poAsset->divisi = $cat->asset;
                if (!empty($cat->name)) {
                    $poAsset->kategori = $cat->name;
                }
                $poAsset->save();
            }
        }

        return $totalSynced;
    }

    /**
     * Format data untuk dikonsumsi oleh JavaScript Frontend (Modal Detail, Edit, Scan QR, & PDF)
     */
    public function toFrontendArray(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $tglStr = '-';
        if ($this->tanggal_terima) {
            $d = $this->tanggal_terima->format('d');
            $m = $months[(int) $this->tanggal_terima->format('n') - 1] ?? $this->tanggal_terima->format('M');
            $y = $this->tanggal_terima->format('Y');
            $tglStr = "{$d} {$m} {$y}";
        }

        return [
            'id' => $this->id,
            'noUrut' => $this->sequence_number,
            'code' => $this->asset_code,
            'isQrGenerated' => (bool) $this->is_qr_generated,
            'poId' => $this->purchase_order_id,
            'poNumber' => $this->purchaseOrder?->po_number,
            'poUrl' => $this->purchase_order_id ? route('purchase-orders.show', $this->purchase_order_id) : null,
            'cabang' => $this->cabang,
            'kategori' => $this->kategori,
            'divisi' => $this->divisi,
            'user' => $this->user_holder,
            'nama' => $this->nama_barang,
            'status' => $this->status,
            'merk' => $this->merk ?: '-',
            'processor' => $this->processor ?: '-',
            'model' => $this->model ?: '-',
            'generasi' => $this->generasi ?: '-',
            'ram' => $this->ram ?: '-',
            'harddisk' => $this->harddisk ?: '-',
            'serial' => $this->serial_number ?: '-',
            'os' => $this->os ?: '-',
            'warna' => $this->warna ?: '-',
            'harga' => 'Rp ' . number_format((float) $this->harga, 0, ',', '.'),
            'hargaRaw' => (float) $this->harga,
            'supplier' => $this->supplier ?: '-',
            'tanggal' => $tglStr,
            'tanggalRaw' => $this->tanggal_terima ? $this->tanggal_terima->format('Y-m-d') : '',
            'deskripsi' => $this->deskripsi ?: '-',
            'history' => $this->histories->map(function ($h) use ($months) {
                $w = $h->waktu;
                $wStr = $w
                    ? $w->format('d') . ' ' . ($months[(int) $w->format('n') - 1] ?? $w->format('M')) . ' ' . $w->format('Y H:i')
                    : '-';
                return [
                    'waktu' => $wStr,
                    'pic' => $h->pic,
                    'cabang' => $h->cabang,
                    'user' => $h->user_holder,
                    'aksi' => $h->aksi,
                    'deskripsi' => $h->deskripsi ?: '-',
                ];
            })->values()->all(),
            'repairs' => $this->repairs->map(function ($r) use ($months) {
                $w = $r->waktu;
                $wStr = $w
                    ? $w->format('d') . ' ' . ($months[(int) $w->format('n') - 1] ?? $w->format('M')) . ' ' . $w->format('Y H:i')
                    : '-';
                return [
                    'waktu' => $wStr,
                    'deskripsi' => $r->deskripsi ?: '-',
                ];
            })->values()->all(),
        ];
    }
}

