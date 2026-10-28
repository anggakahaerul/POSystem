<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Definition of system modules and their standard actions.
     */
    public static function getModuleDefinitions(): array
    {
        return [
            'purchase-orders' => [
                'name' => 'Purchase Order (PO)',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'primary',
                'actions' => [
                    'view' => ['label' => 'Lihat Dokumen PO', 'desc' => 'Melihat daftar, filter, dan rincian dokumen PO', 'type' => 'view'],
                    'create' => ['label' => 'Buat Dokumen PO', 'desc' => 'Membuat dokumen PO baru dari draft atau PR', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Dokumen PO', 'desc' => 'Mengubah rincian dan item dokumen PO berstatus draft', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Dokumen PO', 'desc' => 'Menghapus dokumen PO draft yang belum disubmit', 'type' => 'delete'],
                    'submit' => ['label' => 'Ajukan Approval PO', 'desc' => 'Mengirim permohonan persetujuan PO ke atasan', 'type' => 'special'],
                    'approve' => ['label' => 'Setujui (Approve) PO', 'desc' => 'Menyetujui dan menandatangani dokumen PO (Manager)', 'type' => 'approval'],
                    'reject' => ['label' => 'Tolak (Reject) PO', 'desc' => 'Menolak pengajuan PO dengan alasan penolakan', 'type' => 'approval'],
                    'print' => ['label' => 'Cetak / Unduh PDF PO', 'desc' => 'Mencetak dan mengunduh berkas cetak PO resmi', 'type' => 'special'],
                ]
            ],
            'tax' => [
                'name' => 'Review Pajak (PPh & PPN)',
                'icon' => 'fas fa-balance-scale',
                'color' => 'warning',
                'actions' => [
                    'view' => ['label' => 'Lihat Antrean Pajak', 'desc' => 'Melihat daftar PO yang membutuhkan peninjauan pajak', 'type' => 'view'],
                    'review' => ['label' => 'Koreksi & Verifikasi Pajak', 'desc' => 'Menyesuaikan tarif PPh/PPN, NPWP, dan verifikasi', 'type' => 'special'],
                ]
            ],
            'purchase-requests' => [
                'name' => 'Permintaan Pembelian (PR)',
                'icon' => 'fas fa-clipboard-list',
                'color' => 'info',
                'actions' => [
                    'view' => ['label' => 'Lihat Permintaan Pembelian', 'desc' => 'Melihat daftar dan rincian permohonan pembelian', 'type' => 'view'],
                    'create' => ['label' => 'Buat Permintaan Pembelian', 'desc' => 'Membuat permintaan pembelian barang/jasa baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Permintaan Pembelian', 'desc' => 'Mengubah permohonan pembelian status pending', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Permintaan Pembelian', 'desc' => 'Menghapus permohonan pembelian', 'type' => 'delete'],
                ]
            ],
            'goods-receipts' => [
                'name' => 'Penerimaan Barang (Goods Receipt)',
                'icon' => 'fas fa-truck-loading',
                'color' => 'success',
                'actions' => [
                    'view' => ['label' => 'Lihat Riwayat Penerimaan', 'desc' => 'Melihat log dan daftar barang yang sudah diterima', 'type' => 'view'],
                    'receive' => ['label' => 'Catat Penerimaan Barang', 'desc' => 'Memproses penerimaan parsial atau lengkap PO', 'type' => 'create'],
                ]
            ],
            'products' => [
                'name' => 'Katalog Produk / Barang',
                'icon' => 'fas fa-box-open',
                'color' => 'success',
                'actions' => [
                    'view' => ['label' => 'Lihat Produk', 'desc' => 'Melihat daftar produk, SKU, dan harga satuan', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Produk', 'desc' => 'Mendaftarkan master produk dan spesifikasi baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Produk', 'desc' => 'Mengubah informasi nama, satuan, atau harga produk', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Produk', 'desc' => 'Menghapus produk dari database sistem', 'type' => 'delete'],
                ]
            ],
            'vendors' => [
                'name' => 'Pemasok / Vendor',
                'icon' => 'fas fa-building',
                'color' => 'secondary',
                'actions' => [
                    'view' => ['label' => 'Lihat Vendor', 'desc' => 'Melihat direktori dan profil pemasok rekanan', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Vendor', 'desc' => 'Mendaftarkan rekanan vendor baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Vendor', 'desc' => 'Mengubah data kontak, alamat, dan NPWP vendor', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Vendor', 'desc' => 'Menghapus vendor dari sistem', 'type' => 'delete'],
                ]
            ],
            'categories' => [
                'name' => 'Kategori Produk',
                'icon' => 'fas fa-tags',
                'color' => 'dark',
                'actions' => [
                    'view' => ['label' => 'Lihat Kategori', 'desc' => 'Melihat pengelompokan kategori barang', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Kategori', 'desc' => 'Membuat kelompok kategori baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Kategori', 'desc' => 'Mengubah nama kategori barang', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Kategori', 'desc' => 'Menghapus kelompok kategori', 'type' => 'delete'],
                ]
            ],
            'asset-types' => [
                'name' => 'Tipe Aset',
                'icon' => 'fas fa-layer-group',
                'color' => 'indigo',
                'actions' => [
                    'view' => ['label' => 'Lihat Tipe Aset', 'desc' => 'Melihat master klasifikasi tipe aset', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Tipe Aset', 'desc' => 'Menambahkan tipe aset baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Tipe Aset', 'desc' => 'Mengubah informasi tipe aset', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Tipe Aset', 'desc' => 'Menghapus tipe aset', 'type' => 'delete'],
                ]
            ],
            'branches' => [
                'name' => 'Cabang Perusahaan',
                'icon' => 'fas fa-store-alt',
                'color' => 'teal',
                'actions' => [
                    'view' => ['label' => 'Lihat Cabang', 'desc' => 'Melihat daftar kantor cabang dan logo', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Cabang', 'desc' => 'Mendaftarkan cabang baru perusahaan', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Cabang', 'desc' => 'Mengubah alamat, NPWP, dan kop cabang', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Cabang', 'desc' => 'Menonaktifkan cabang', 'type' => 'delete'],
                ]
            ],
            'departments' => [
                'name' => 'Departemen / Divisi',
                'icon' => 'fas fa-sitemap',
                'color' => 'cyan',
                'actions' => [
                    'view' => ['label' => 'Lihat Departemen', 'desc' => 'Melihat struktur divisi/departemen kerja', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Departemen', 'desc' => 'Mendaftarkan divisi/departemen baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Departemen', 'desc' => 'Mengubah data kode dan nama departemen', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Departemen', 'desc' => 'Menghapus departemen', 'type' => 'delete'],
                ]
            ],
            'payment-terms' => [
                'name' => 'Termin Pembayaran',
                'icon' => 'fas fa-credit-card',
                'color' => 'maroon',
                'actions' => [
                    'view' => ['label' => 'Lihat Termin', 'desc' => 'Melihat daftar syarat tempo pembayaran', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Termin', 'desc' => 'Membuat aturan termin pembayaran baru (TOP)', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Termin', 'desc' => 'Mengubah jumlah hari tempo pembayaran', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Termin', 'desc' => 'Menghapus termin pembayaran', 'type' => 'delete'],
                ]
            ],
            'approval-rules' => [
                'name' => 'Aturan Approval (Plafond)',
                'icon' => 'fas fa-sliders-h',
                'color' => 'olive',
                'actions' => [
                    'view' => ['label' => 'Lihat Aturan', 'desc' => 'Melihat ambang batas nominal persetujuan', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Aturan', 'desc' => 'Menambah aturan rentang nominal approval', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Aturan', 'desc' => 'Mengubah rentang nominal dan approver', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Aturan', 'desc' => 'Menghapus aturan batas approval', 'type' => 'delete'],
                ]
            ],
            'users' => [
                'name' => 'Manajemen Pengguna (User)',
                'icon' => 'fas fa-users',
                'color' => 'purple',
                'actions' => [
                    'view' => ['label' => 'Lihat Pengguna', 'desc' => 'Melihat akun pengguna sistem', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Pengguna', 'desc' => 'Mendaftarkan akun staf baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Pengguna', 'desc' => 'Mengubah profil, password, dan penugasan role', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Pengguna', 'desc' => 'Menghapus / menonaktifkan akun user', 'type' => 'delete'],
                ]
            ],
            'roles' => [
                'name' => 'Manajemen Role',
                'icon' => 'fas fa-user-tag',
                'color' => 'navy',
                'actions' => [
                    'view' => ['label' => 'Lihat Role', 'desc' => 'Melihat daftar peran jabatan dan hak akses', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Role', 'desc' => 'Membuat peran jabatan baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Role', 'desc' => 'Mengatur perizinan dan wewenang role', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Role', 'desc' => 'Menghapus peran jabatan', 'type' => 'delete'],
                ]
            ],
            'permissions' => [
                'name' => 'Manajemen Permission',
                'icon' => 'fas fa-key',
                'color' => 'dark',
                'actions' => [
                    'view' => ['label' => 'Lihat Permission', 'desc' => 'Melihat hak akses teknis aplikasi', 'type' => 'view'],
                    'create' => ['label' => 'Tambah Permission', 'desc' => 'Mendaftarkan hak akses baru', 'type' => 'create'],
                    'edit' => ['label' => 'Edit Permission', 'desc' => 'Mengubah izin akses', 'type' => 'edit'],
                    'delete' => ['label' => 'Hapus Permission', 'desc' => 'Menghapus izin akses', 'type' => 'delete'],
                ]
            ],
        ];
    }

    /**
     * Parse permission name into friendly human metadata.
     */
    public static function parsePermission(string $name): array
    {
        $modules = self::getModuleDefinitions();
        $parts = explode(' ', trim($name));
        $action = strtolower($parts[0] ?? 'custom');
        $moduleKey = isset($parts[1]) ? strtolower($parts[1]) : '';

        // If matched defined module
        if ($moduleKey && isset($modules[$moduleKey])) {
            $modDef = $modules[$moduleKey];
            $actDef = $modDef['actions'][$action] ?? null;

            return [
                'module_key' => $moduleKey,
                'module_name' => $modDef['name'],
                'module_icon' => $modDef['icon'],
                'module_color' => $modDef['color'],
                'action_key' => $action,
                'action_label' => $actDef['label'] ?? ucfirst($action) . ' ' . $modDef['name'],
                'action_desc' => $actDef['desc'] ?? 'Akses ' . $action . ' pada modul ' . $modDef['name'],
                'action_type' => $actDef['type'] ?? 'special',
            ];
        }

        // Special fallback
        return [
            'module_key' => $moduleKey ?: 'custom',
            'module_name' => $moduleKey ? ucfirst($moduleKey) : 'Kustom / Umum',
            'module_icon' => 'fas fa-shield-alt',
            'module_color' => 'secondary',
            'action_key' => $action,
            'action_label' => ucfirst($name),
            'action_desc' => 'Hak akses khusus pada sistem',
            'action_type' => 'special',
        ];
    }

    /**
     * Display a listing of permissions with pagination.
     */
    public function index(Request $request)
    {
        $query = Permission::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('module')) {
            $query->where('name', 'like', "% {$request->module}%");
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $permissions = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Attach parsed metadata to each permission
        $permissions->getCollection()->transform(function($p) {
            $p->meta = self::parsePermission($p->name);
            return $p;
        });

        $roles = Role::orderBy('name')->get();
        $moduleDefs = self::getModuleDefinitions();

        return view('permissions.index', [
            'permissions' => $permissions,
            'roles' => $roles,
            'moduleDefs' => $moduleDefs,
            'moduleName' => 'Permission',
            'moduleRoute' => 'permissions',
        ]);
    }

    /**
     * Store newly created permission(s).
     * Supports both Module+Action batch mode and custom string.
     */
    public function store(Request $request)
    {
        $mode = $request->input('mode', 'module');

        if ($mode === 'module' && $request->filled('module') && $request->module !== '__custom__') {
            $validated = $request->validate([
                'module' => 'required|string',
                'actions' => 'required|array|min:1',
                'actions.*' => 'string',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
            ], [
                'module.required' => 'Pilih modul sistem terlebih dahulu.',
                'actions.required' => 'Pilih minimal satu aksi hak akses.',
                'actions.min' => 'Pilih minimal satu aksi hak akses.',
            ]);

            $module = $validated['module'];
            $actions = $validated['actions'];
            $roleIds = $request->input('roles', []);
            $roles = !empty($roleIds) ? Role::whereIn('id', $roleIds)->get() : collect();

            $createdCount = 0;
            $existingCount = 0;

            foreach ($actions as $action) {
                $permName = trim($action . ' ' . $module);
                $permission = Permission::firstOrCreate(['name' => $permName]);

                if ($permission->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $existingCount++;
                }

                // Assign to selected roles
                if ($roles->isNotEmpty()) {
                    foreach ($roles as $role) {
                        if (!$role->hasPermissionTo($permission->name)) {
                            $role->givePermissionTo($permission);
                        }
                    }
                }
            }

            $message = "Berhasil memproses hak akses: {$createdCount} permission baru dibuat";
            if ($existingCount > 0) {
                $message .= ", {$existingCount} permission sudah ada diperbarui";
            }
            if ($roles->isNotEmpty()) {
                $message .= " dan dihubungkan ke role terpilih.";
            } else {
                $message .= ".";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('permissions.index')->with('success', $message);
        }

        // Custom single name mode
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah ada dalam sistem.',
        ]);

        $permission = Permission::create(['name' => strtolower(trim($validated['name']))]);

        if (!empty($validated['roles'])) {
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $permission->syncRoles($roles);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil ditambahkan.',
                'data' => $permission,
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil ditambahkan.');
    }

    /**
     * Get single permission data for edit modal (JSON).
     */
    public function show(Permission $permission): JsonResponse
    {
        $permission->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'meta' => self::parsePermission($permission->name),
                'role_ids' => $permission->roles->pluck('id')->toArray(),
            ],
        ]);
    }

    /**
     * Show form for editing (supports AJAX modal).
     */
    public function edit(Permission $permission, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->show($permission);
        }

        return view('permissions.form', [
            'permission' => $permission,
            'roles' => Role::orderBy('name')->get(),
            'moduleDefs' => self::getModuleDefinitions(),
        ]);
    }

    /**
     * Update the permission & sync roles.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah ada.',
        ]);

        $permission->update(['name' => strtolower(trim($validated['name']))]);

        // Sync roles if provided
        if ($request->has('roles')) {
            $roleIds = (array)$request->input('roles', []);
            $roles = Role::whereIn('id', $roleIds)->get();
            $permission->syncRoles($roles);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil diperbarui.',
                'data' => $permission,
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    /**
     * Remove the permission.
     */
    public function destroy(Permission $permission, Request $request)
    {
        if ($permission->roles()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission ini masih aktif digunakan oleh ' . $permission->roles()->count() . ' role. Lepaskan role terlebih dahulu sebelum menghapus.',
                ], 422);
            }
            return redirect()->route('permissions.index')
                ->with('error', 'Permission masih digunakan oleh role.');
        }

        $permission->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil dihapus dari sistem.',
            ]);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
