<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentBranch;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AppUserSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('Pangles1234');

        $deptHrga = Department::where('code', 'HRGA')->first();
        $deptFin  = Department::where('code', 'FIN')->first();
        $deptPur  = Department::where('code', 'PUR')->first();
        $deptMis  = Department::where('code', 'MIS')->first();
        $branchJkt = Branch::where('code', 'PLJKT')->first();

        // 1. User Approver (Email: anggakahaerul20@gmail.com)
        $approver = User::updateOrCreate(
            ['email' => 'anggakahaerul20@gmail.com'],
            [
                'name'          => 'Manager Approver',
                'password'      => $password,
                'department_id' => $deptHrga?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        $approver->syncRoles(['Manager', 'Regional Manager', 'General Manager']);

        // 2. User Tax (Email: anggakahaeruloffice@gmail.com) - Hanya review & koreksi pajak
        $taxUser = User::updateOrCreate(
            ['email' => 'anggakahaeruloffice@gmail.com'],
            [
                'name'          => 'Staff Tax',
                'password'      => $password,
                'department_id' => $deptFin?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        $taxUser->syncRoles(['Tax']);

        // 3. User HR Staff 1 (Staff pemohon level 1 - Bikin PO, PR, Lengkap)
        $hrStaff1 = User::updateOrCreate(
            ['email' => 'hr.staff1@pangles.com'],
            [
                'name'          => 'Staff HR 1',
                'password'      => $password,
                'department_id' => $deptHrga?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        $hrStaff1->syncRoles(['HR']);

        // 4. User HR Staff 2 (Staff pemohon level 1)
        $hrStaff2 = User::updateOrCreate(
            ['email' => 'hr.staff2@pangles.com'],
            [
                'name'          => 'Staff HR 2',
                'password'      => $password,
                'department_id' => $deptHrga?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        $hrStaff2->syncRoles(['HR']);

        // 5. User HR Manager (Pemohon Level 3 - Memiliki akses HR & posisi Level 3)
        $hrManagerL3 = User::updateOrCreate(
            ['email' => 'hr.manager@pangles.com'],
            [
                'name'          => 'Manager HR (L3)',
                'password'      => $password,
                'department_id' => $deptHrga?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        // Memiliki role HR (untuk fitur lengkap PR/PO) dan Manager (agar diakui sistem sebagai Level 3)
        $hrManagerL3->syncRoles(['HR', 'Manager']);

        // 6. Administrator (Email admin@pangles.com dan admin@example.com)
        $adminPangles = User::updateOrCreate(
            ['email' => 'admin@pangles.com'],
            [
                'name'          => 'Administrator',
                'password'      => $password,
                'department_id' => $deptMis?->id,
                'branch_id'     => $branchJkt?->id,
            ]
        );
        $adminPangles->syncRoles(['Admin']);

        $adminDefault = User::where('email', 'admin@example.com')->first();
        if ($adminDefault) {
            $adminDefault->update(['password' => $password]);
        }

        // Update password semua user eksisting agar seragam Pangles1234
        foreach (User::all() as $u) {
            $u->update(['password' => $password]);
        }

        // 7. Update Approver di Departemen HRGA dan Cabang agar menunjuk ke anggakahaerul20@gmail.com
        if ($deptHrga) {
            $deptHrga->update([
                'manager_l3_id' => $approver->id,
                'manager_l4_id' => $approver->id,
                'manager_l5_id' => $approver->id,
            ]);

            // Pastikan setiap cabang untuk departemen HRGA memiliki approver ini
            if ($branchJkt) {
                DepartmentBranch::updateOrCreate(
                    [
                        'department_id' => $deptHrga->id,
                        'branch_id'     => $branchJkt->id,
                    ],
                    [
                        'manager_l3_id' => $approver->id,
                        'manager_l4_id' => $approver->id,
                        'manager_l5_id' => $approver->id,
                    ]
                );
            }
        }

        // 8. Update Mail System Setting: masih menggunakan anggakahaerul30@gmail.com
        Setting::set('mail_username', 'anggakahaerul30@gmail.com', 'email');
        Setting::set('mail_from_address', 'anggakahaerul30@gmail.com', 'email');
        Setting::set('mail_from_name', 'PO Approval System', 'email');
        Setting::applyEmailConfig();
    }
}
