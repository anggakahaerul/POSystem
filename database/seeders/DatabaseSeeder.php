<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles and Permissions first
        $this->call(RoleSeeder::class);

        // Create Payment Terms
        $this->call(PaymentTermSeeder::class);

        // Create Branches
        $this->call(BranchSeeder::class);

        // Create Users (use firstOrCreate to avoid duplicates)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager Approval',
                'password' => Hash::make('password'),
            ]
        );
        if (!$manager->hasRole('Manager')) {
            $manager->assignRole('Manager');
        }

        $purchasing = User::firstOrCreate(
            ['email' => 'purchasing@example.com'],
            [
                'name' => 'Staff Purchasing',
                'password' => Hash::make('password'),
            ]
        );
        if (!$purchasing->hasRole('Purchasing')) {
            $purchasing->assignRole('Purchasing');
        }

        $finance = User::firstOrCreate(
            ['email' => 'finance@example.com'],
            [
                'name' => 'Staff Finance',
                'password' => Hash::make('password'),
            ]
        );
        if (!$finance->hasRole('Finance')) {
            $finance->assignRole('Finance');
        }

        // Create Categories (use firstOrCreate to avoid duplicates)
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'asset' => 'MIS', 'description' => 'Peralatan elektronik', 'is_active' => true],
            ['name' => 'ATK', 'slug' => 'atk', 'asset' => 'HRGA', 'description' => 'Alat Tulis Kantor', 'is_active' => true],
            ['name' => 'Furniture', 'slug' => 'furniture', 'asset' => 'HRGA', 'description' => 'Perabotan kantor', 'is_active' => true],
            ['name' => 'Komputer', 'slug' => 'komputer', 'asset' => 'MIS', 'description' => 'Perangkat komputer', 'is_active' => true],
            ['name' => 'Bahan Baku', 'slug' => 'bahan-baku', 'asset' => null, 'description' => 'Bahan baku produksi', 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // Create Products (use firstOrCreate to avoid duplicates)
        $products = [
            ['category_id' => 1, 'name' => 'Laptop ASUS VivoBook', 'sku' => 'ELK0001', 'unit' => 'unit', 'price' => 8500000, 'stock' => 10, 'min_stock' => 3, 'is_active' => true],
            ['category_id' => 1, 'name' => 'Monitor LED 24 inch', 'sku' => 'ELK0002', 'unit' => 'unit', 'price' => 2500000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Kertas HVS A4 70gr', 'sku' => 'ATK0001', 'unit' => 'rim', 'price' => 50000, 'stock' => 100, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Pulpen Pilot G-2', 'sku' => 'ATK0002', 'unit' => 'lusin', 'price' => 48000, 'stock' => 50, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Stapler HD-10', 'sku' => 'ATK0003', 'unit' => 'pcs', 'price' => 25000, 'stock' => 30, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Meja Kantor 120x60', 'sku' => 'FRN0001', 'unit' => 'unit', 'price' => 1500000, 'stock' => 5, 'min_stock' => 2, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Kursi Ergonomis', 'sku' => 'FRN0002', 'unit' => 'unit', 'price' => 2000000, 'stock' => 8, 'min_stock' => 3, 'is_active' => true],
            ['category_id' => 4, 'name' => 'Mouse Wireless Logitech', 'sku' => 'KMP0001', 'unit' => 'pcs', 'price' => 350000, 'stock' => 25, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 4, 'name' => 'Keyboard Mechanical', 'sku' => 'KMP0002', 'unit' => 'pcs', 'price' => 850000, 'stock' => 15, 'min_stock' => 3, 'is_active' => true],
            ['category_id' => 5, 'name' => 'Tinta Printer HP 680', 'sku' => 'BBK0001', 'unit' => 'pcs', 'price' => 180000, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],
        ];

        foreach ($products as $prd) {
            Product::firstOrCreate(
                ['sku' => $prd['sku']],
                $prd
            );
        }

        // Create Vendors (use firstOrCreate to avoid duplicates)
        $vendors = [
            ['code' => 'VND0001', 'name' => 'PT. Elektronik Jaya', 'email' => 'sales@elektronikjaya.com', 'phone' => '021-5551234', 'address' => 'Jl. Industri No. 123, Jakarta', 'is_active' => true],
            ['code' => 'VND0002', 'name' => 'CV. ATK Sejahtera', 'email' => 'order@atksejahtera.co.id', 'phone' => '021-5554567', 'address' => 'Jl. Perkantoran No. 45, Bekasi', 'is_active' => true],
            ['code' => 'VND0003', 'name' => 'PT. Furniture Modern', 'email' => 'info@furnituremodern.com', 'phone' => '021-5557890', 'address' => 'Jl. Mebel Raya No. 78, Tangerang', 'is_active' => true],
            ['code' => 'VND0004', 'name' => 'Toko Komputer Maju', 'email' => 'sales@komputermaju.com', 'phone' => '021-5552345', 'address' => 'Jl. IT Center No. 12, Jakarta', 'is_active' => true],
            ['code' => 'VND0005', 'name' => 'PT. Supply Chain Indo', 'email' => 'procurement@supplyindo.co.id', 'phone' => '021-5556789', 'address' => 'Jl. Logistik No. 99, Cikarang', 'is_active' => true],
        ];

        foreach ($vendors as $vnd) {
            Vendor::firstOrCreate(
                ['code' => $vnd['code']],
                $vnd
            );
        }

        $this->command->info('');
        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Manager: manager@example.com / password');
        $this->command->info('Purchasing: purchasing@example.com / password');
        $this->command->info('Finance: finance@example.com / password');
    }
}
