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
        Schema::table('approval_rules', function (Blueprint $table) {
            $table->string('approver_role', 50)->nullable()->change();
            $table->string('approver_for_staff', 50)->default('L3')->after('approver_role');
            $table->string('approver_for_l3', 50)->default('L4')->after('approver_for_staff');
            $table->string('approver_for_l4', 50)->default('L5')->after('approver_for_l3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_rules', function (Blueprint $table) {
            $table->dropColumn(['approver_for_staff', 'approver_for_l3', 'approver_for_l4']);
        });
    }
};
