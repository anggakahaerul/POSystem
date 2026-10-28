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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('revision_count')->default(0)->after('status');
            $table->text('revision_reason')->nullable()->after('rejection_reason');
            $table->foreignId('revised_by')->nullable()->after('received_by')->constrained('users')->nullOnDelete();
            $table->timestamp('revised_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['revised_by']);
            $table->dropColumn(['revision_count', 'revision_reason', 'revised_by', 'revised_at']);
        });
    }
};
