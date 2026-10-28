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
            $table->string('tax_review_status', 20)->default('pending')->after('status');
            $table->foreignId('tax_reviewed_by')->nullable()->after('tax_review_status')->constrained('users')->nullOnDelete();
            $table->timestamp('tax_reviewed_at')->nullable()->after('tax_reviewed_by');
            $table->string('tax_notes', 255)->nullable()->after('tax_reviewed_at');

            $table->index('tax_review_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['tax_reviewed_by']);
            $table->dropColumn(['tax_review_status', 'tax_reviewed_by', 'tax_reviewed_at', 'tax_notes']);
        });
    }
};
