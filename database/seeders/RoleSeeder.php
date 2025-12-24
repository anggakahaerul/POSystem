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
        $admin = Role::create(['name' => 'Admin']);
        $manager = Role::create(['name' => 'Manager']);
        $purchasing = Role::create(['name' => 'Purchasing']);
        $finance = Role::create(['name' => 'Finance']);

        // Create Permissions
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
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'view users',
            'view categories', 'create categories', 'edit categories',
            'view products', 'create products', 'edit products',
            'view vendors', 'create vendors', 'edit vendors',
            'view purchase-orders', 'approve purchase-orders', 'reject purchase-orders', 'print purchase-orders',
        ]);

        $purchasing->givePermissionTo([
            'view categories',
            'view products',
            'view vendors',
            'view purchase-orders', 'create purchase-orders', 'edit purchase-orders', 'delete purchase-orders', 'submit purchase-orders',
        ]);

        $finance->givePermissionTo([
            'view categories',
            'view products',
            'view vendors',
            'view purchase-orders', 'print purchase-orders',
        ]);

        $this->command->info('Roles and Permissions created successfully!');
    }
}
