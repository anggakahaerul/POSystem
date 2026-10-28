<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $manager = User::where('email', 'manager@example.com')->first();
        $purchasing = User::where('email', 'purchasing@example.com')->first();
        $finance = User::where('email', 'finance@example.com')->first();

        $departments = [
            [
                'code' => 'PUR',
                'name' => 'Purchasing & Procurement',
                'description' => 'Pengadaan barang, pemilihan vendor, dan penerbitan PO.',
                'manager_l3_id' => $purchasing?->id ?? $manager?->id,
                'manager_l4_id' => $manager?->id,
                'manager_l5_id' => $admin?->id,
                'is_active' => true,
            ],
            [
                'code' => 'FIN',
                'name' => 'Finance & Accounting',
                'description' => 'Verifikasi pembayaran, termin tempo, dan pencatatan kas.',
                'manager_l3_id' => $finance?->id ?? $manager?->id,
                'manager_l4_id' => $manager?->id,
                'manager_l5_id' => $admin?->id,
                'is_active' => true,
            ],
            [
                'code' => 'HRGA',
                'name' => 'Human Resources & General Affairs',
                'description' => 'Pengelolaan SDM dan sarana operasional umum kantor.',
                'manager_l3_id' => $manager?->id,
                'manager_l4_id' => $manager?->id,
                'manager_l5_id' => $admin?->id,
                'is_active' => true,
            ],
            [
                'code' => 'MIS',
                'name' => 'Management Information System / IT',
                'description' => 'Infrastruktur teknologi, sistem aplikasi POS, dan hardware.',
                'manager_l3_id' => $admin?->id,
                'manager_l4_id' => $admin?->id,
                'manager_l5_id' => $admin?->id,
                'is_active' => true,
            ],
        ];

        foreach ($departments as $deptData) {
            Department::updateOrCreate(
                ['code' => $deptData['code']],
                $deptData
            );
        }
    }
}
