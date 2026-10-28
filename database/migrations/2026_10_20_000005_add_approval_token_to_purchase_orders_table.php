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
            $table->string('approval_token', 80)->nullable()->unique()->after('status');
            $table->string('approver_email')->nullable()->after('approval_token');
            $table->timestamp('approval_token_expires_at')->nullable()->after('approver_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['approval_token', 'approver_email', 'approval_token_expires_at']);
        });
    }
};
