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
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->string('approver_role'); // Manager, Regional Manager, General Manager
            $table->string('level_label')->nullable(); // Level 3, Level 4, GM
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add department_id to purchase_orders if not exists
        if (!Schema::hasColumn('purchase_orders', 'department_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('department_id')->nullable()->after('branch_id')->constrained('departments')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_rules');

        if (Schema::hasColumn('purchase_orders', 'department_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            });
        }
    }
};
