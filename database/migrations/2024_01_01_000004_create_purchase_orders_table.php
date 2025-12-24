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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('payment_term_id')->nullable()->constrained('payment_terms')->nullOnDelete();
            
            // Amounts
            $table->decimal('subtotal', 15, 2)->default(0); // Before discount/ppn
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('ppn_percentage', 5, 2)->default(11);
            $table->decimal('ppn_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('receipt_status', ['not_received', 'partial', 'received'])->default('not_received');
            
            // Users
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Dates
            $table->date('required_date')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('received_at')->nullable();
            
            // Notes
            $table->text('note')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('po_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('receipt_status');
            $table->index(['vendor_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
