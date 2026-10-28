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
            $table->string('tax_reviewer_email', 100)->nullable()->after('tax_notes');
            $table->timestamp('tax_review_requested_at')->nullable()->after('tax_reviewer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['tax_reviewer_email', 'tax_review_requested_at']);
        });
    }
};
