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
        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_details', 'is_pph')) {
                $table->boolean('is_pph')->default(false)->after('subtotal_after_discount');
            }
            if (!Schema::hasColumn('purchase_order_details', 'pph_type')) {
                $table->string('pph_type', 50)->nullable()->after('is_pph');
            }
            if (!Schema::hasColumn('purchase_order_details', 'pph_percentage')) {
                $table->decimal('pph_percentage', 5, 2)->default(0)->after('pph_type');
            }
            if (!Schema::hasColumn('purchase_order_details', 'pph_amount')) {
                $table->decimal('pph_amount', 15, 2)->default(0)->after('pph_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn(['is_pph', 'pph_type', 'pph_percentage', 'pph_amount']);
        });
    }
};
