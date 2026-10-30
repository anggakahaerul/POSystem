<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Tampilkan halaman Daftar Barang Inventaris (Terintegrasi Opsi A dari Penerimaan Barang PO)
     */
    public function index(Request $request)
    {
        Asset::ensureTablesExist();

        // One-time auto-reset untuk mereset barcode yang sebelumnya sudah ter-generate agar mulai ulang dari tombol Generate QR
        if (!\Illuminate\Support\Facades\Cache::has('assets_qr_reset_v2') || $request->boolean('reset_qr')) {
            \App\Models\AssetHistory::where('aksi', 'Generate QR Code')->delete();
            foreach (Asset::all() as $a) {
                $a->is_qr_generated = false;
                $a->sequence_number = 0;
                $a->asset_code = 'PENDING-' . $a->id;
                $a->save();
            }
            \Illuminate\Support\Facades\Cache::forever('assets_qr_reset_v2', true);
        }

        // Opsi A: Otomatis sinkronkan semua barang diterima dari PO yang belum masuk ke tabel assets
        $newlySyncedCount = Asset::syncAllUnregisteredReceivedPoItems();

        // Jika belum ada PO yang diterima sama sekali di database, seed contoh awal agar tampilan siap diuji
        if (Asset::count() === 0) {
            $this->seedInitialSampleAssets();
        }

        $assets = Asset::with(['histories', 'repairs', 'purchaseOrder'])
            ->orderBy('id', 'asc')
            ->get();

        $assetDatabase = [];
        foreach ($assets as $asset) {
            $assetDatabase[$asset->asset_code] = $asset->toFrontendArray();
        }

        $stats = [
            'total' => $assets->count(),
            'digunakan' => $assets->where('status', 'Digunakan')->count(),
            'tersedia' => $assets->where('status', 'Tersedia')->count(),
            'maintenance_rusak' => $assets->whereIn('status', ['Maintenance', 'Rusak'])->count(),
        ];

        // Daftar pilihan Cabang, Kategori, Divisi (Gabungan Master Data DB + Data Aset)
        $dbBranches = Branch::active()->orderBy('name')->pluck('name')->toArray();
        $defaultBranches = ['Branch Sidoarjo', 'Cabang Surabaya', 'Branch Jakarta'];
        $cabangList = array_values(array_unique(array_merge(
            $dbBranches,
            $defaultBranches,
            $assets->pluck('cabang')->filter()->toArray()
        )));

        $dbCategories = Category::active()->orderBy('name')->pluck('name')->toArray();
        $defaultCategories = ['Elektronik & IT', 'Scanner & Printer', 'Komputer', 'Furniture & Kantor', 'Kendaraan', 'Mesin & Peralatan', 'ATK'];
        $kategoriList = array_values(array_unique(array_merge(
            $dbCategories,
            $defaultCategories,
            $assets->pluck('kategori')->filter()->toArray()
        )));

        $dbAssetTypes = \App\Models\AssetType::active()->orderBy('code')->pluck('code')->toArray();
        $dbDepartments = Department::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        $defaultDivisi = ['HRGA', 'MIS'];
        $divisiList = array_values(array_unique(array_merge(
            $dbAssetTypes,
            $defaultDivisi,
            $dbDepartments,
            $assets->pluck('divisi')->filter()->toArray()
        )));

        return view('assets.index', compact(
            'assets',
            'assetDatabase',
            'stats',
            'cabangList',
            'kategoriList',
            'divisiList',
            'newlySyncedCount'
        ));
    }

    /**
     * Update data / spesifikasi barang (dari Modal Edit Barang maupun Modal Scan QR Update/Repair)
     */
    public function update(Request $request, string $code)
    {
        Asset::ensureTablesExist();

        $asset = Asset::where('asset_code', $code)->orWhere('id', $code)->firstOrFail();
        $oldCode = $asset->asset_code;
        $oldCabang = $asset->cabang;
        $oldKategori = $asset->kategori;
        $mode = $request->input('mode', 'edit'); // 'edit', 'update', 'repair'

        $asset->nama_barang = $request->input('nama', $asset->nama_barang);
        $asset->cabang = $request->input('cabang', $asset->cabang);
        $asset->kategori = $request->input('kategori', $asset->kategori);
        $asset->divisi = $request->input('divisi', $asset->divisi);
        $asset->user_holder = $request->input('user', $asset->user_holder);
        $asset->status = $request->input('status', $asset->status);
        $asset->deskripsi = $request->input('deskripsi', $asset->deskripsi);

        // Jika update dari Modal Edit Spesifikasi Lengkap
        if ($mode === 'edit') {
            $asset->merk = $request->input('merk') ?: '-';
            $asset->model = $request->input('model') ?: '-';
            $asset->processor = $request->input('processor') ?: '-';
            $asset->generasi = $request->input('generasi') ?: '-';
            $asset->ram = $request->input('ram') ?: '-';
            $asset->harddisk = $request->input('harddisk') ?: '-';
            $asset->serial_number = $request->input('serial') ?: '-';
            $asset->os = $request->input('os') ?: '-';
            $asset->warna = $request->input('warna') ?: '-';

            if ($request->filled('harga')) {
                $rawHarga = preg_replace('/[^0-9]/', '', (string) $request->input('harga'));
                if ($rawHarga !== '') {
                    $asset->harga = (float) $rawHarga;
                }
            }

            $asset->supplier = $request->input('supplier') ?: '-';

            if ($request->filled('tanggal_raw')) {
                try {
                    $asset->tanggal_terima = \Carbon\Carbon::parse($request->input('tanggal_raw'));
                } catch (\Throwable $e) {
                }
            }
        }

        // Jika QR sudah pernah di-generate dan Kategori atau Cabang berubah, hitung sequence baru untuk kombinasi (Kategori, Cabang) baru
        if ($asset->is_qr_generated) {
            $oldSuffix = Asset::formatCategoryForBarcode($oldKategori) . Asset::formatBranchForBarcode($oldCabang);
            $newSuffix = Asset::formatCategoryForBarcode($asset->kategori) . Asset::formatBranchForBarcode($asset->cabang);

            if ($oldSuffix !== $newSuffix) {
                $newSeq = Asset::nextSequenceNumberForGroup($asset->kategori, $asset->cabang, $asset->id);
                $asset->sequence_number = $newSeq;
                $asset->asset_code = Asset::generateAssetCode(
                    $newSeq,
                    $asset->kategori,
                    $asset->cabang,
                    $asset->id
                );
            }
        }

        $asset->save();

        $picName = auth()->user()?->name ?? ($mode === 'edit' ? 'Admin Inventaris' : 'Petugas Scanner');

        if ($mode === 'repair') {
            $asset->repairs()->create([
                'waktu' => now(),
                'deskripsi' => $asset->deskripsi ?: 'Perbaikan / pengecekan rutin unit aset.',
            ]);

            $asset->histories()->create([
                'waktu' => now(),
                'pic' => $picName,
                'cabang' => $asset->cabang,
                'user_holder' => $asset->user_holder,
                'aksi' => 'Repair / Maintenance',
                'deskripsi' => $asset->deskripsi ?: 'Pencatatan perbaikan (Repair) via Scan QR.',
            ]);
        } else {
            $aksiLabel = $mode === 'update' ? 'Update via Scan QR' : 'Update Data & Spesifikasi';
            $asset->histories()->create([
                'waktu' => now(),
                'pic' => $picName,
                'cabang' => $asset->cabang,
                'user_holder' => $asset->user_holder,
                'aksi' => $aksiLabel,
                'deskripsi' => $asset->deskripsi ?: 'Pembaruan data pemegang & spesifikasi barang.',
            ]);
        }

        $asset->load(['histories', 'repairs', 'purchaseOrder']);

        return response()->json([
            'success' => true,
            'old_code' => $oldCode,
            'item' => $asset->toFrontendArray(),
        ]);
    }

    /**
     * Generate QR Code saat tombol "Generate QR" diklik oleh user
     */
    public function generateQr(Request $request, string $code)
    {
        Asset::ensureTablesExist();

        $asset = Asset::where('asset_code', $code)->orWhere('id', $code)->firstOrFail();
        $oldCode = $asset->asset_code;

        $seq = Asset::nextSequenceNumberForGroup($asset->kategori, $asset->cabang, $asset->id);
        $asset->sequence_number = $seq;
        $asset->asset_code = Asset::generateAssetCode(
            $seq,
            $asset->kategori,
            $asset->cabang,
            $asset->id
        );
        $asset->is_qr_generated = true;
        $asset->save();

        $picName = auth()->user()?->name ?? 'Admin Inventaris';
        $asset->histories()->create([
            'waktu' => now(),
            'pic' => $picName,
            'cabang' => $asset->cabang,
            'user_holder' => $asset->user_holder,
            'aksi' => 'Generate QR Code',
            'deskripsi' => "QR Code {$asset->asset_code} berhasil di-generate oleh {$picName}.",
        ]);

        $asset->load(['histories', 'repairs', 'purchaseOrder']);

        return response()->json([
            'success' => true,
            'old_code' => $oldCode,
            'item' => $asset->toFrontendArray(),
        ]);
    }

    /**
     * Reset 1 QR Code kembali menjadi belum di-generate
     */
    public function resetQr(Request $request, string $code)
    {
        Asset::ensureTablesExist();

        $asset = Asset::where('asset_code', $code)->orWhere('id', $code)->firstOrFail();
        $oldCode = $asset->asset_code;

        $asset->histories()->where('aksi', 'Generate QR Code')->delete();
        $asset->is_qr_generated = false;
        $asset->sequence_number = 0;
        $asset->asset_code = 'PENDING-' . $asset->id;
        $asset->save();

        $asset->load(['histories', 'repairs', 'purchaseOrder']);

        return response()->json([
            'success' => true,
            'old_code' => $oldCode,
            'item' => $asset->toFrontendArray(),
        ]);
    }

    /**
     * Reset semua QR Code agar seluruh barang kembali ke tombol Generate QR dan sequence mulai dari 001
     */
    public function resetAllQr()
    {
        Asset::ensureTablesExist();

        \App\Models\AssetHistory::where('aksi', 'Generate QR Code')->delete();
        foreach (Asset::all() as $asset) {
            $asset->is_qr_generated = false;
            $asset->sequence_number = 0;
            $asset->asset_code = 'PENDING-' . $asset->id;
            $asset->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh QR Code berhasil di-reset.',
        ]);
    }

    /**
     * Hapus unit barang dari inventaris
     */
    public function destroy(string $code)
    {
        Asset::ensureTablesExist();

        $asset = Asset::where('asset_code', $code)->firstOrFail();
        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => "Data barang {$code} berhasil dihapus.",
        ]);
    }

    /**
     * Seed data contoh awal jika tabel assets masih kosong dan belum ada PO diterima
     */
    protected function seedInitialSampleAssets(): void
    {
        $samples = [
            [
                'sequence_number' => 1,
                'cabang' => 'Branch Sidoarjo',
                'kategori' => 'Elektronik & IT',
                'divisi' => 'IT & System (MIS)',
                'user_holder' => 'Budi Santoso',
                'nama_barang' => 'Apple MacBook Pro 14" M2 Pro',
                'status' => 'Digunakan',
                'merk' => 'Apple',
                'processor' => 'Apple M2 Pro (10-Core CPU, 16-Core GPU)',
                'model' => 'MacBook Pro 14-inch (MPHE3ID/A)',
                'generasi' => '2023 (M2 Series)',
                'ram' => '16 GB Unified Memory',
                'harddisk' => '512 GB NVMe SSD',
                'serial_number' => 'C02XG124J058',
                'os' => 'macOS Sonoma 14.5',
                'warna' => 'Space Grey',
                'harga' => 31499000,
                'supplier' => 'PT. Digimap Nusantara (Official Authorized)',
                'tanggal_terima' => '2026-01-12',
                'deskripsi' => 'Chip M2 Pro 10-core, RAM 16GB, SSD 512GB Space Grey. Pembelian PO-2026-004',
                'histories' => [
                    ['waktu' => '2026-01-12 09:30:00', 'pic' => 'Admin Gudang (GA)', 'cabang' => 'Branch Sidoarjo', 'user_holder' => 'Pool Gudang / Standby', 'aksi' => 'Barang Diterima (PO)', 'deskripsi' => 'Unit otomatis masuk dari Penerimaan PO-2026-004 dengan status Tersedia.'],
                    ['waktu' => '2026-01-14 13:15:00', 'pic' => 'Hendra Wijaya (IT)', 'cabang' => 'Branch Sidoarjo', 'user_holder' => 'Budi Santoso', 'aksi' => 'Serah Terima / Alokasi', 'deskripsi' => 'Instalasi software standar kerja dan serah terima ke PIC Budi Santoso.'],
                ],
                'repairs' => [
                    ['waktu' => '2026-06-10 10:00:00', 'deskripsi' => 'Pembersihan kipas internal (deep cleaning) & kalibrasi baterai rutin 6 bulanan.'],
                ],
            ],
            [
                'sequence_number' => 2,
                'cabang' => 'Cabang Surabaya',
                'kategori' => 'Furniture & Kantor',
                'divisi' => 'HRGA & Operasional',
                'user_holder' => 'Pool Gudang / Standby',
                'nama_barang' => 'Kursi Kerja Ergonomis Mesh Office',
                'status' => 'Tersedia',
                'merk' => 'Indachi',
                'processor' => '-',
                'model' => 'ErgoPlus V2 High-Back',
                'generasi' => '2025 Series',
                'ram' => '-',
                'harddisk' => '-',
                'serial_number' => 'IND-ERG-88210',
                'os' => '-',
                'warna' => 'Hitam (Matte Black)',
                'harga' => 2850000,
                'supplier' => 'PT. Mitra Kantor Sejahtera',
                'tanggal_terima' => '2026-02-05',
                'deskripsi' => 'Material breathable mesh, lumbar support adjustable, warna hitam. Kondisi 100% baru dari PO.',
                'histories' => [
                    ['waktu' => '2026-02-05 11:00:00', 'pic' => 'Staf GA', 'cabang' => 'Cabang Surabaya', 'user_holder' => 'Pool Gudang / Standby', 'aksi' => 'Barang Diterima (PO)', 'deskripsi' => 'Penerimaan kursi kerja dari PO-2026-019 otomatis masuk inventaris.'],
                ],
                'repairs' => [],
            ],
            [
                'sequence_number' => 3,
                'cabang' => 'Branch Sidoarjo',
                'kategori' => 'Kendaraan',
                'divisi' => 'Operasional Logistik',
                'user_holder' => 'Agus Priyono',
                'nama_barang' => 'Toyota Avanza 1.5 G MT',
                'status' => 'Digunakan',
                'merk' => 'Toyota',
                'processor' => '1.496 cc 4-Silinder Dual VVT-i',
                'model' => 'All New Avanza 1.5 G M/T',
                'generasi' => 'Gen-3 (2024)',
                'ram' => '-',
                'harddisk' => '-',
                'serial_number' => 'MHKM5EA3J0K119283 (No. Pol: W 2145 PGL)',
                'os' => 'Manual 5-Speed',
                'warna' => 'Silver Metallic',
                'harga' => 258700000,
                'supplier' => 'PT. Astra International Tbk - Auto2000',
                'tanggal_terima' => '2024-10-20',
                'deskripsi' => 'Mobil operasional antar cabang Sidoarjo dan Surabaya. Pajak STNK s/d Okt 2027.',
                'histories' => [
                    ['waktu' => '2024-10-20 10:00:00', 'pic' => 'Supervisor GA', 'cabang' => 'Branch Sidoarjo', 'user_holder' => 'Pool Transport', 'aksi' => 'Barang Diterima (PO)', 'deskripsi' => 'Unit kendaraan operasional diterima dari Auto2000.'],
                    ['waktu' => '2024-11-02 08:30:00', 'pic' => 'Kepala Logistik', 'cabang' => 'Branch Sidoarjo', 'user_holder' => 'Agus Priyono', 'aksi' => 'Mutasi & Alokasi', 'deskripsi' => 'Penugasan kendaraan operasional untuk Branch Sidoarjo.'],
                ],
                'repairs' => [
                    ['waktu' => '2026-04-15 09:00:00', 'deskripsi' => 'Service berkala 20.000 KM, ganti oli mesin, filter udara, dan spooring balancing di Auto2000 Sidoarjo.'],
                ],
            ],
            [
                'sequence_number' => 4,
                'cabang' => 'Cabang Surabaya',
                'kategori' => 'Scanner & Printer',
                'divisi' => 'Finance & Accounting',
                'user_holder' => 'Dewi Lestari',
                'nama_barang' => 'Scanner & Printer Epson EcoTank L3210',
                'status' => 'Maintenance',
                'merk' => 'Epson',
                'processor' => '-',
                'model' => 'EcoTank L3210 (Print/Scan/Copy)',
                'generasi' => 'EcoTank Gen-2',
                'ram' => '-',
                'harddisk' => '-',
                'serial_number' => 'EPS-88192-SUB',
                'os' => 'Windows / macOS Driver Compatible',
                'warna' => 'Hitam',
                'harga' => 2450000,
                'supplier' => 'CV. Surya Komputer Surabaya',
                'tanggal_terima' => '2025-03-18',
                'deskripsi' => 'Kerusakan head print warna merah bergaris. Sedang dalam perbaikan service center.',
                'histories' => [
                    ['waktu' => '2025-03-18 14:00:00', 'pic' => 'IT Support Surabaya', 'cabang' => 'Cabang Surabaya', 'user_holder' => 'Dewi Lestari', 'aksi' => 'Barang Diterima (PO)', 'deskripsi' => 'Pemasangan unit Scanner & Printer untuk kebutuhan Finance Surabaya.'],
                    ['waktu' => '2026-10-25 11:20:00', 'pic' => 'IT Support Surabaya', 'cabang' => 'Cabang Surabaya', 'user_holder' => 'Dewi Lestari', 'aksi' => 'Status Maintenance', 'deskripsi' => 'Unit dibawa ke Epson Authorized Service Center Surabaya.'],
                ],
                'repairs' => [
                    ['waktu' => '2026-10-25 11:20:00', 'deskripsi' => 'Penggantian printhead magenta & flushing saluran tinta di Epson Service Center Surabaya.'],
                ],
            ],
        ];

        foreach ($samples as $s) {
            $histories = $s['histories'];
            $repairs = $s['repairs'];
            unset($s['histories'], $s['repairs']);

            $s['asset_code'] = Asset::generateAssetCode($s['sequence_number'], $s['kategori'], $s['cabang']);
            $asset = Asset::create($s);

            foreach ($histories as $h) {
                $asset->histories()->create($h);
            }
            foreach ($repairs as $r) {
                $asset->repairs()->create($r);
            }
        }
    }
}

