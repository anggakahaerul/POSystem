<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterExportImportService
{
    /**
     * Get configuration for each master type
     */
    public function getConfig(string $type): ?array
    {
        $configs = [
            'products' => [
                'title' => 'Master Produk',
                'filename' => 'master_produk_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_produk.xlsx',
                'headers' => [
                    'Kode / SKU',
                    'Nama Produk',
                    'Kategori',
                    'Satuan',
                    'Harga Satuan (Rp)',
                    'Stok Saat Ini',
                    'Stok Minimum',
                    'Deskripsi',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['PRD-001', 'Kopi Kapal Api Spesial 65g', 'Minuman', 'pcs', 7500, 100, 20, 'Kopi bubuk kemasan sachet', 'Aktif'],
                    ['PRD-002', 'Minyak Goreng Sania 2L', 'Sembako', 'pouch', 34500, 50, 10, 'Minyak goreng kelapa sawit', 'Aktif'],
                ],
            ],
            'vendors' => [
                'title' => 'Master Vendor',
                'filename' => 'master_vendor_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_vendor.xlsx',
                'headers' => [
                    'Kode Vendor',
                    'Nama Vendor / Perusahaan',
                    'Contact Person (PIC)',
                    'No Telepon / WA',
                    'Email',
                    'Term of Payment',
                    'Tipe NPWP',
                    'NPWP Vendor',
                    'Informasi Rekening Bank',
                    'Alamat Kantor / Gudang',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['VND0001', 'PT. Mitra Sejahtera Bersama', 'Budi Santoso', '08123456789', 'budi@mitra.co.id', 'Net 30 Hari', 'Badan', '01.234.567.8-901.000', 'BCA 123456789 a.n PT Mitra', 'Jl. Rungkut Industri No. 12, Surabaya', 'Aktif'],
                    ['VND0002', 'CV. Sumber Pangan Makmur', 'Siti Rahma', '08219876543', 'order@sumberpangan.com', 'COD', 'Orang Pribadi', '', 'Mandiri 987654321 a.n Siti', 'Kawasan Pergudangan Margomulyo Indah B-5', 'Aktif'],
                ],
            ],
            'categories' => [
                'title' => 'Master Kategori',
                'filename' => 'master_kategori_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_kategori.xlsx',
                'headers' => [
                    'Nama Kategori',
                    'Asset Group (HRGA/MIS)',
                    'Deskripsi',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['Alat Tulis Kantor', 'HRGA', 'Perlengkapan alat tulis dan kertas kantor', 'Aktif'],
                    ['Hardware Komputer', 'MIS', 'Perangkat keras PC, printer, dan server', 'Aktif'],
                ],
            ],
            'branches' => [
                'title' => 'Master Cabang',
                'filename' => 'master_cabang_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_cabang.xlsx',
                'headers' => [
                    'Kode Cabang',
                    'Nama Cabang',
                    'Alamat',
                    'No Telepon',
                    'Email',
                    'NPWP',
                    'Catatan Footer',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['CBG-SBY', 'Cabang Surabaya', 'Jl. Darmo No. 45, Surabaya', '031-5678901', 'surabaya@panganlestari.co.id', '01.999.888.7-001.000', 'Terima kasih atas kerja sama Anda', 'Aktif'],
                    ['CBG-JKT', 'Cabang Jakarta Pusat', 'Jl. Hayam Wuruk No. 88, Jakarta', '021-3456789', 'jakarta@panganlestari.co.id', '01.999.888.7-002.000', '', 'Aktif'],
                ],
            ],
            'departments' => [
                'title' => 'Master Departemen',
                'filename' => 'master_departemen_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_departemen.xlsx',
                'headers' => [
                    'Kode Departemen',
                    'Nama Departemen',
                    'Deskripsi',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['DPT-PROC', 'Procurement & Purchasing', 'Pengadaan barang, vendor management, dan logistik', 'Aktif'],
                    ['DPT-FIN', 'Finance & Accounting', 'Keuangan, perpajakan, dan pencatatan pembayaran', 'Aktif'],
                ],
            ],
            'payment-terms' => [
                'title' => 'Master Term Pembayaran',
                'filename' => 'master_term_pembayaran_' . date('Ymd_His') . '.xlsx',
                'template_filename' => 'template_import_term_pembayaran.xlsx',
                'headers' => [
                    'Nama Term',
                    'Jumlah Hari (Jatuh Tempo)',
                    'Tipe (full/partial)',
                    'Deskripsi',
                    'Status (Aktif/Nonaktif)',
                ],
                'sample' => [
                    ['Cash on Delivery (COD)', 0, 'full', 'Pembayaran langsung saat barang tiba', 'Aktif'],
                    ['Net 30 Hari', 30, 'full', 'Jatuh tempo 30 hari kalender setelah invoice', 'Aktif'],
                    ['Termin 2 Tahap (50-50)', 60, 'partial', 'Uang muka 50% dan pelunasan 50%', 'Aktif'],
                ],
            ],
        ];

        return $configs[$type] ?? null;
    }

    /**
     * Generate and download template Excel
     */
    public function downloadTemplate(string $type): StreamedResponse
    {
        $config = $this->getConfig($type);
        if (!$config) {
            abort(404, 'Master type tidak ditemukan');
        }

        return $this->streamExcelDownload(
            $config['template_filename'],
            substr($config['title'], 0, 31),
            $config['headers'],
            $config['sample'] ?? []
        );
    }

    /**
     * Export all master data to Excel
     */
    public function exportData(string $type): StreamedResponse
    {
        $config = $this->getConfig($type);
        if (!$config) {
            abort(404, 'Master type tidak ditemukan');
        }

        $dataRows = $this->fetchDataRows($type);

        return $this->streamExcelDownload(
            $config['filename'],
            substr($config['title'], 0, 31),
            $config['headers'],
            $dataRows
        );
    }

    /**
     * Generate Excel binary (via PhpSpreadsheet if available, or pure-PHP native XLSX fallback)
     * and stream it safely to the browser without failing on missing ext-zip or restricted /tmp.
     */
    protected function streamExcelDownload(string $filename, string $sheetTitle, array $headers, array $dataRows): StreamedResponse
    {
        $xlsxBinary = null;

        if (class_exists(Spreadsheet::class) && class_exists(Xlsx::class) && class_exists(\ZipArchive::class)) {
            try {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($sheetTitle ?: 'Sheet1');

                $colCount = count($headers);
                for ($i = 0; $i < $colCount; $i++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->setCellValue("{$colLetter}1", $headers[$i]);
                }

                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $headerRange = "A1:{$lastCol}1";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                $rowIndex = 2;
                foreach ($dataRows as $row) {
                    $rowValues = array_values((array) $row);
                    for ($j = 0; $j < count($rowValues); $j++) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j + 1);
                        $sheet->setCellValue("{$colLetter}{$rowIndex}", $rowValues[$j]);
                    }
                    $rowIndex++;
                }

                for ($i = 1; $i <= $colCount; $i++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }

                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) {
                    @mkdir($tempDir, 0775, true);
                }
                if (!is_dir($tempDir) || !is_writable($tempDir)) {
                    $tempDir = sys_get_temp_dir();
                }

                $tempFile = rtrim($tempDir, '/\\') . DIRECTORY_SEPARATOR . 'xlsx_' . uniqid('', true) . '.xlsx';
                $writer = new Xlsx($spreadsheet);
                $writer->save($tempFile);

                if (is_file($tempFile) && filesize($tempFile) > 0) {
                    $xlsxBinary = file_get_contents($tempFile);
                }
                @unlink($tempFile);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('PhpSpreadsheet export fallback triggered: ' . $e->getMessage());
                $xlsxBinary = null;
            }
        }

        if (!$xlsxBinary) {
            $xlsxBinary = $this->buildNativeXlsxBinary($sheetTitle ?: 'Sheet1', $headers, $dataRows);
        }

        return response()->streamDownload(function () use ($xlsxBinary) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            echo $xlsxBinary;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Fetch formatted rows for export
     */
    protected function fetchDataRows(string $type): array
    {
        $rows = [];

        switch ($type) {
            case 'products':
                $items = Product::with('category')->orderBy('name')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->sku ?: '',
                        $item->name ?: '',
                        $item->category->name ?? '',
                        $item->unit ?: 'pcs',
                        (float)$item->price,
                        (int)$item->stock,
                        (int)$item->min_stock,
                        $item->description ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;

            case 'vendors':
                $items = Vendor::with('paymentTerm')->orderBy('name')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->code ?: '',
                        $item->name ?: '',
                        $item->contact_person ?: '',
                        $item->phone ?: '',
                        $item->email ?: '',
                        $item->paymentTerm->name ?? '',
                        $item->npwp_type ?: '',
                        $item->npwp ?: '',
                        $item->bank_info ?: '',
                        $item->address ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;

            case 'categories':
                $items = Category::orderBy('name')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->name ?: '',
                        $item->asset ?: 'HRGA',
                        $item->description ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;

            case 'branches':
                $items = Branch::orderBy('name')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->code ?: '',
                        $item->name ?: '',
                        $item->address ?: '',
                        $item->phone ?: '',
                        $item->email ?: '',
                        $item->npwp ?: '',
                        $item->footer_note ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;

            case 'departments':
                $items = Department::orderBy('name')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->code ?: '',
                        $item->name ?: '',
                        $item->description ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;

            case 'payment-terms':
                $items = PaymentTerm::orderBy('days')->get();
                foreach ($items as $item) {
                    $rows[] = [
                        $item->name ?: '',
                        (int)$item->days,
                        $item->payment_type ?: 'full',
                        $item->description ?: '',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ];
                }
                break;
        }

        return $rows;
    }

    /**
     * Import uploaded Excel or CSV file
     */
    public function importData(string $type, UploadedFile $file, bool $updateExisting = true): array
    {
        $config = $this->getConfig($type);
        if (!$config) {
            return ['success' => false, 'message' => 'Tipe master tidak valid.'];
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return [
                    'success' => false,
                    'message' => 'File Excel kosong atau tidak memiliki data baris untuk diimpor.',
                ];
            }

            // Remove header row
            array_shift($rows);

            $added = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $line = $index + 2; // Excel row numbering

                // Skip completely empty rows
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim($val) !== ''))) {
                    continue;
                }

                try {
                    $res = $this->processRowImport($type, $row, $updateExisting);
                    if ($res === 'added') {
                        $added++;
                    } elseif ($res === 'updated') {
                        $updated++;
                    } else {
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Baris {$line}: " . $e->getMessage();
                }
            }

            $totalProcessed = $added + $updated;

            return [
                'success' => true,
                'message' => "Proses import selesai. Berhasil: {$totalProcessed} (Baru: {$added}, Update: {$updated})" . ($failed > 0 ? ", Dilewati/Gagal: {$failed}." : '.'),
                'added' => $added,
                'updated' => $updated,
                'failed' => $failed,
                'errors' => array_slice($errors, 0, 10), // Return max 10 error lines for readability
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process a single row import
     */
    protected function processRowImport(string $type, array $row, bool $updateExisting): string
    {
        switch ($type) {
            case 'products':
                // Headers: [SKU, Name, Category, Unit, Price, Stock, Min Stock, Description, Status]
                $sku = trim((string)($row[0] ?? ''));
                $name = trim((string)($row[1] ?? ''));
                $categoryName = trim((string)($row[2] ?? ''));
                $unit = trim((string)($row[3] ?? 'pcs')) ?: 'pcs';
                $price = floatval(preg_replace('/[^\d.]/', '', (string)($row[4] ?? 0)));
                $stock = intval($row[5] ?? 0);
                $minStock = intval($row[6] ?? 0);
                $desc = trim((string)($row[7] ?? ''));
                $statusStr = strtolower(trim((string)($row[8] ?? 'aktif')));
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama produk wajib diisi.');
                }

                // Match or create category
                $categoryId = null;
                if (!empty($categoryName)) {
                    $cat = Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['asset' => 'HRGA', 'slug' => Str::slug($categoryName), 'is_active' => true]
                    );
                    $categoryId = $cat->id;
                }

                // If SKU is empty, auto-generate
                if (empty($sku)) {
                    $sku = 'PRD-' . strtoupper(Str::random(6));
                }

                $existing = Product::where('sku', $sku)->orWhere('name', $name)->first();

                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'name' => $name,
                        'sku' => $sku,
                        'category_id' => $categoryId ?? $existing->category_id,
                        'unit' => $unit,
                        'price' => $price,
                        'stock' => $stock,
                        'min_stock' => $minStock,
                        'description' => $desc ?: $existing->description,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                Product::create([
                    'sku' => $sku,
                    'name' => $name,
                    'category_id' => $categoryId,
                    'unit' => $unit,
                    'price' => $price,
                    'stock' => $stock,
                    'min_stock' => $minStock,
                    'description' => $desc,
                    'is_active' => $isActive,
                ]);
                return 'added';

            case 'vendors':
                // Headers: [Code, Name, Contact, Phone, Email, Payment Term, (optional Tipe NPWP), NPWP, Bank Info, Address, Status]
                $code = trim((string)($row[0] ?? ''));
                $name = trim((string)($row[1] ?? ''));
                $contact = trim((string)($row[2] ?? ''));
                $phone = trim((string)($row[3] ?? ''));
                $email = trim((string)($row[4] ?? ''));
                $paymentTermName = trim((string)($row[5] ?? ''));

                if (count($row) >= 11) {
                    $npwpType = trim((string)($row[6] ?? ''));
                    $npwp = trim((string)($row[7] ?? ''));
                    $bankInfo = trim((string)($row[8] ?? ''));
                    $address = trim((string)($row[9] ?? ''));
                    $statusStr = strtolower(trim((string)($row[10] ?? 'aktif')));
                } else {
                    $npwpType = null;
                    $npwp = trim((string)($row[6] ?? ''));
                    $bankInfo = trim((string)($row[7] ?? ''));
                    $address = trim((string)($row[8] ?? ''));
                    $statusStr = strtolower(trim((string)($row[9] ?? 'aktif')));
                }
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama vendor wajib diisi.');
                }

                // Match payment term
                $paymentTermId = null;
                if (!empty($paymentTermName)) {
                    $term = PaymentTerm::where('name', 'like', "%{$paymentTermName}%")->first();
                    if ($term) {
                        $paymentTermId = $term->id;
                    }
                }

                if (empty($code)) {
                    $code = Vendor::generateCode();
                }

                $existing = Vendor::where('code', $code)->orWhere('name', $name)->first();

                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'code' => $code,
                        'name' => $name,
                        'contact_person' => $contact ?: $existing->contact_person,
                        'phone' => $phone ?: $existing->phone,
                        'email' => $email ?: $existing->email,
                        'payment_term_id' => $paymentTermId ?? $existing->payment_term_id,
                        'npwp_type' => $npwpType ?: $existing->npwp_type,
                        'npwp' => $npwp ?: $existing->npwp,
                        'bank_info' => $bankInfo ?: $existing->bank_info,
                        'address' => $address ?: $existing->address,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                Vendor::create([
                    'code' => $code,
                    'name' => $name,
                    'contact_person' => $contact,
                    'phone' => $phone,
                    'email' => $email,
                    'payment_term_id' => $paymentTermId,
                    'npwp_type' => $npwpType,
                    'npwp' => $npwp,
                    'bank_info' => $bankInfo,
                    'address' => $address,
                    'is_active' => $isActive,
                ]);
                return 'added';

            case 'categories':
                // Headers: [Name, Asset, Description, Status]
                $name = trim((string)($row[0] ?? ''));
                $asset = strtoupper(trim((string)($row[1] ?? 'HRGA')));
                if (!in_array($asset, ['HRGA', 'MIS'])) {
                    $asset = 'HRGA';
                }
                $desc = trim((string)($row[2] ?? ''));
                $statusStr = strtolower(trim((string)($row[3] ?? 'aktif')));
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama kategori wajib diisi.');
                }

                $existing = Category::where('name', $name)->first();
                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'asset' => $asset,
                        'description' => $desc ?: $existing->description,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                Category::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'asset' => $asset,
                    'description' => $desc,
                    'is_active' => $isActive,
                ]);
                return 'added';

            case 'branches':
                // Headers: [Code, Name, Address, Phone, Email, NPWP, Footer Note, Status]
                $code = trim((string)($row[0] ?? ''));
                $name = trim((string)($row[1] ?? ''));
                $address = trim((string)($row[2] ?? ''));
                $phone = trim((string)($row[3] ?? ''));
                $email = trim((string)($row[4] ?? ''));
                $npwp = trim((string)($row[5] ?? ''));
                $footer = trim((string)($row[6] ?? ''));
                $statusStr = strtolower(trim((string)($row[7] ?? 'aktif')));
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama cabang wajib diisi.');
                }
                if (empty($code)) {
                    $code = 'CBG-' . strtoupper(Str::random(3));
                }

                $existing = Branch::where('code', $code)->orWhere('name', $name)->first();
                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'code' => $code,
                        'name' => $name,
                        'address' => $address ?: $existing->address,
                        'phone' => $phone ?: $existing->phone,
                        'email' => $email ?: $existing->email,
                        'npwp' => $npwp ?: $existing->npwp,
                        'footer_note' => $footer ?: $existing->footer_note,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                Branch::create([
                    'code' => $code,
                    'name' => $name,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                    'npwp' => $npwp,
                    'footer_note' => $footer,
                    'is_active' => $isActive,
                ]);
                return 'added';

            case 'departments':
                // Headers: [Code, Name, Description, Status]
                $code = trim((string)($row[0] ?? ''));
                $name = trim((string)($row[1] ?? ''));
                $desc = trim((string)($row[2] ?? ''));
                $statusStr = strtolower(trim((string)($row[3] ?? 'aktif')));
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama departemen wajib diisi.');
                }
                if (empty($code)) {
                    $code = 'DPT-' . strtoupper(Str::random(4));
                }

                $existing = Department::where('code', $code)->orWhere('name', $name)->first();
                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'code' => $code,
                        'name' => $name,
                        'description' => $desc ?: $existing->description,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                Department::create([
                    'code' => $code,
                    'name' => $name,
                    'description' => $desc,
                    'is_active' => $isActive,
                ]);
                return 'added';

            case 'payment-terms':
                // Headers: [Name, Days, Type, Description, Status]
                $name = trim((string)($row[0] ?? ''));
                $days = intval($row[1] ?? 0);
                $payType = strtolower(trim((string)($row[2] ?? 'full')));
                if (!in_array($payType, ['full', 'partial'])) {
                    $payType = 'full';
                }
                $desc = trim((string)($row[3] ?? ''));
                $statusStr = strtolower(trim((string)($row[4] ?? 'aktif')));
                $isActive = in_array($statusStr, ['1', 'aktif', 'active', 'ya', 'true']);

                if (empty($name)) {
                    throw new \Exception('Nama term pembayaran wajib diisi.');
                }

                $existing = PaymentTerm::where('name', $name)->first();
                if ($existing) {
                    if (!$updateExisting) {
                        return 'skipped';
                    }
                    $existing->update([
                        'days' => $days,
                        'payment_type' => $payType,
                        'description' => $desc ?: $existing->description,
                        'is_active' => $isActive,
                    ]);
                    return 'updated';
                }

                PaymentTerm::create([
                    'name' => $name,
                    'days' => $days,
                    'payment_type' => $payType,
                    'description' => $desc,
                    'is_active' => $isActive,
                ]);
                return 'added';

            default:
                throw new \Exception("Tipe master {$type} tidak dikenali.");
        }
    }
}
