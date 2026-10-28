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

        // 1. Buat / pastikan permissions yang dibutuhkan ada
        $permissions = [
            'view tax',
            'review tax',
            'view purchase-orders',
            'view vendors',
        ];

        $permModels = [];
        foreach ($permissions as $permName) {
            $permModels[$permName] = Permission::firstOrCreate(['name' => $permName]);
        }

        // 2. Berikan ke Role Admin jika role sudah ada
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo([$permModels['view tax'], $permModels['review tax']]);
        }

        // 3. Buat Role khusus Tax
        $taxRole = Role::firstOrCreate(['name' => 'Tax']);
        $taxRole->syncPermissions([
            'view tax',
            'review tax',
            'view purchase-orders',
            'view vendors',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
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
