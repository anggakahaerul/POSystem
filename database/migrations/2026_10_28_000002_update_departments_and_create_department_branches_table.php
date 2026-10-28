<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
            if (Schema::hasColumn('departments', 'general_manager_id')) {
                $table->dropForeign(['general_manager_id']);
                $table->dropColumn('general_manager_id');
            }

            $table->foreignId('manager_l3_id')->nullable()->after('description')->constrained('users')->nullOnDelete();
            $table->foreignId('manager_l4_id')->nullable()->after('manager_l3_id')->constrained('users')->nullOnDelete();
            $table->foreignId('manager_l5_id')->nullable()->after('manager_l4_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('department_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('manager_l3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_l4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_l5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['department_id', 'branch_id'], 'dept_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_branches');

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_l3_id']);
            $table->dropForeign(['manager_l4_id']);
            $table->dropForeign(['manager_l5_id']);
            $table->dropColumn(['manager_l3_id', 'manager_l4_id', 'manager_l5_id']);

            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('general_manager_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
