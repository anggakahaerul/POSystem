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
            $table->boolean('is_pph')->default(false)->after('ppn_amount');
            $table->string('pph_type', 50)->nullable()->after('is_pph');
            $table->decimal('pph_percentage', 5, 2)->default(0)->after('pph_type');
            $table->decimal('pph_amount', 15, 2)->default(0)->after('pph_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['is_pph', 'pph_type', 'pph_percentage', 'pph_amount']);
        });
    }
};
