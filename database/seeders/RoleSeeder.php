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

        // Create Roles (use firstOrCreate to avoid duplicates)
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $purchasing = Role::firstOrCreate(['name' => 'Purchasing']);
        $finance = Role::firstOrCreate(['name' => 'Finance']);

        // Create Permissions (use firstOrCreate to avoid duplicates)
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Category
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Product
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Vendor
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',
            
            // Purchase Order
            'view purchase-orders',
            'create purchase-orders',
            'edit purchase-orders',
            'delete purchase-orders',
            'submit purchase-orders',
            'approve purchase-orders',
            'reject purchase-orders',
            'print purchase-orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles (syncPermissions will update without duplicating)
        $admin->syncPermissions(Permission::all());

        $manager->syncPermissions([
            'view users',
            'view categories', 'create categories', 'edit categories',
            'view products', 'create products', 'edit products',
            'view vendors', 'create vendors', 'edit vendors',
            'view purchase-orders', 'approve purchase-orders', 'reject purchase-orders', 'print purchase-orders',
        ]);

        $purchasing->syncPermissions([
            'view categories',
            'view products',
            'view vendors',
            'view purchase-orders', 'create purchase-orders', 'edit purchase-orders', 'delete purchase-orders', 'submit purchase-orders',
        ]);

        $finance->syncPermissions([
            'view categories',
            'view products',
            'view vendors',
            'view purchase-orders', 'print purchase-orders',
        ]);

        $this->command->info('Roles and Permissions created/updated successfully!');
    }
}
