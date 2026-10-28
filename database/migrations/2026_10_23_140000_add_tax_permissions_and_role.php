<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Permissions khusus Tax
        $viewTax = Permission::firstOrCreate(['name' => 'view tax']);
        $reviewTax = Permission::firstOrCreate(['name' => 'review tax']);

        // 2. Berikan ke Role Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo([$viewTax, $reviewTax]);
        }

        // 3. Buat Role khusus Tax
        $taxRole = Role::firstOrCreate(['name' => 'Tax']);
        $taxRole->syncPermissions([
            'view tax',
            'review tax',
            'view purchase-orders',
            'view vendors',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', ['view tax', 'review tax'])->delete();

        $taxRole = Role::where('name', 'Tax')->first();
        if ($taxRole) {
            $taxRole->delete();
        }
    }
};
