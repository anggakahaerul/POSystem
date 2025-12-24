<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'code' => 'PLJKT',
                'name' => 'Jakarta',
                'address' => 'Jl. Jatiraya Blok J1 No. 10B Komp.Newton Techno Park, Lippo Cikarang, Bekasi 17530',
                'phone' => '021-89906002',
                'email' => 'jakarta@panganlestari.com',
            ],
            [
                'code' => 'PLPLSDA',
                'name' => 'Sidoarjo',
                'address' => 'Jl. Jenggolo II/17, Sidoarjo 61219',
                'phone' => '031-8963032',
                'email' => 'sidoarjo@panganlestari.com',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['code' => $branch['code']],
                $branch
            );
        }
    }
}
