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
        Schema::create('purchase_order_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->unsignedInteger('revision_number');
            $table->text('reason');
            $table->foreignId('revised_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('previous_status')->nullable();
            $table->decimal('previous_total_amount', 15, 2)->nullable();
            $table->timestamps();

            $table->index(['purchase_order_id', 'revision_number']);
        });

        // Backfill data untuk PO yang sudah memiliki catatan revisi
        $existingPOs = \Illuminate\Support\Facades\DB::table('purchase_orders')
            ->where('revision_count', '>', 0)
            ->whereNotNull('revision_reason')
            ->get();

        foreach ($existingPOs as $po) {
            \Illuminate\Support\Facades\DB::table('purchase_order_revisions')->insert([
                'purchase_order_id' => $po->id,
                'revision_number' => $po->revision_count,
                'reason' => $po->revision_reason,
                'revised_by' => $po->revised_by,
                'previous_status' => 'pending',
                'previous_total_amount' => $po->total_amount,
                'created_at' => $po->revised_at ?? now(),
                'updated_at' => $po->revised_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_revisions');
    }
};
