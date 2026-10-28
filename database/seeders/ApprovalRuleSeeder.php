<?php

namespace Database\Seeders;

use App\Models\ApprovalRule;
use Illuminate\Database\Seeder;

class ApprovalRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua aturan yang ada agar hanya menyisakan 3 skema nominal yang diminta
        ApprovalRule::query()->delete();

        $rules = [
            [
                'name' => 'Tier 1 (< Rp 1 Juta)',
                'department_id' => null,
                'min_amount' => 0,
                'max_amount' => 1000000,
                'approver_for_staff' => 'L3',
                'approver_for_l3' => 'L4',
                'approver_for_l4' => 'L5',
                'approver_role' => 'Manager Level 3',
                'is_active' => true,
            ],
            [
                'name' => 'Tier 2 (Rp 1 Juta - Rp 5 Juta)',
                'department_id' => null,
                'min_amount' => 1000000,
                'max_amount' => 5000000,
                'approver_for_staff' => 'L4',
                'approver_for_l3' => 'L4',
                'approver_for_l4' => 'L5',
                'approver_role' => 'Regional Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Tier 3 (Rp 5 Juta - Rp 9 Juta)',
                'department_id' => null,
                'min_amount' => 5000000,
                'max_amount' => 9000000,
                'approver_for_staff' => 'L5',
                'approver_for_l3' => 'L5',
                'approver_for_l4' => 'L5',
                'approver_role' => 'General Manager',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $ruleData) {
            ApprovalRule::create($ruleData);
        }
    }
}
