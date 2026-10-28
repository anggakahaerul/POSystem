<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $purchasing = Role::firstOrCreate(['name' => 'Purchasing']);
        $finance = Role::firstOrCreate(['name' => 'Finance']);
        $tax = Role::firstOrCreate(['name' => 'Tax']);
        $hr = Role::firstOrCreate(['name' => 'HR']);
        $gudang = Role::firstOrCreate(['name' => 'Gudang']);

        // Complete list of permissions across all application modules
        $permissions = [
            // Purchase Orders
            'view purchase-orders',
            'create purchase-orders',
            'edit purchase-orders',
            'delete purchase-orders',
            'submit purchase-orders',
            'approve purchase-orders',
            'reject purchase-orders',
            'print purchase-orders',

            // Purchase Requests
            'view purchase-requests',
            'create purchase-requests',
            'edit purchase-requests',
            'delete purchase-requests',

            // Tax Review
            'view tax',
            'review tax',

            // Goods Receipts
            'view goods-receipts',
            'receive goods-receipts',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Vendors
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Asset Types
            'view asset-types',
            'create asset-types',
            'edit asset-types',
            'delete asset-types',

            // Branches
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',

            // Departments
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',

            // Payment Terms
            'view payment-terms',
            'create payment-terms',
            'edit payment-terms',
            'delete payment-terms',

            // Approval Rules
            'view approval-rules',
            'create approval-rules',
            'edit approval-rules',
            'delete approval-rules',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // Manager permissions
        $managerPermissions = [
            'view users',
            'view categories',
            'view products',
            'view vendors',
            'view branches',
            'view departments',
            'view payment-terms',
            'view approval-rules',
            'view purchase-requests',
            'create purchase-requests',
            'edit purchase-requests',
            'view purchase-orders',
            'approve purchase-orders',
            'reject purchase-orders',
            'print purchase-orders',
            'view goods-receipts',
        ];
        $manager->syncPermissions($managerPermissions);

        $regionalManager = Role::firstOrCreate(['name' => 'Regional Manager']);
        $regionalManager->syncPermissions($managerPermissions);

        $generalManager = Role::firstOrCreate(['name' => 'General Manager']);
        $generalManager->syncPermissions($managerPermissions);

        // Purchasing permissions
        $purchasing->syncPermissions([
            'view categories',
            'view asset-types',
            'view products', 'create products', 'edit products',
            'view vendors', 'create vendors', 'edit vendors',
            'view branches',
            'view departments',
            'view payment-terms',
            'view purchase-requests', 'create purchase-requests', 'edit purchase-requests', 'delete purchase-requests',
            'view purchase-orders', 'create purchase-orders', 'edit purchase-orders', 'delete purchase-orders', 'submit purchase-orders', 'print purchase-orders',
            'view goods-receipts',
        ]);

        // Finance permissions (Hanya view PO/PR/Master Data, print PO, dan review pajak)
        $finance->syncPermissions([
            'view categories',
            'view products',
            'view vendors',
            'view branches',
            'view departments',
            'view payment-terms',
            'view purchase-orders', 'print purchase-orders',
            'view purchase-requests',
            'view tax', 'review tax',
            'view goods-receipts',
        ]);

        // Tax permissions (Hanya review, koreksi, dan verifikasi/approve pajak saja)
        $tax->syncPermissions([
            'view tax',
            'review tax',
        ]);

        // HR permissions (Lengkap: Buat PO, PR, Kelola Master Data, Users, dll)
        $hr->syncPermissions([
            'view purchase-orders', 'create purchase-orders', 'edit purchase-orders', 'delete purchase-orders', 'submit purchase-orders', 'print purchase-orders',
            'view purchase-requests', 'create purchase-requests', 'edit purchase-requests', 'delete purchase-requests',
            'view goods-receipts', 'receive goods-receipts',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view asset-types', 'create asset-types', 'edit asset-types', 'delete asset-types',
            'view products', 'create products', 'edit products', 'delete products',
            'view vendors', 'create vendors', 'edit vendors', 'delete vendors',
            'view branches', 'create branches', 'edit branches', 'delete branches',
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view payment-terms', 'create payment-terms', 'edit payment-terms', 'delete payment-terms',
            'view approval-rules', 'create approval-rules', 'edit approval-rules', 'delete approval-rules',
            'view users', 'create users', 'edit users', 'delete users',
        ]);

        // Gudang permissions
        $gudang->syncPermissions([
            'view products',
            'view categories',
            'view purchase-orders',
            'view goods-receipts', 'receive goods-receipts',
        ]);

        $this->command->info('Roles and Permissions created/updated successfully!');
    }
}
