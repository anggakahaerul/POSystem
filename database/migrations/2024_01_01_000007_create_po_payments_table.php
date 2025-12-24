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
        Schema::create('po_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->integer('payment_number')->default(1); // Urutan pembayaran
            $table->string('description'); // DP, Termin 1, Pelunasan
            $table->decimal('percentage', 5, 2)->default(0); // 50.00, 30.00, 20.00
            $table->decimal('amount', 15, 2)->default(0); // Nominal yang harus dibayar
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo
            $table->decimal('paid_amount', 15, 2)->default(0); // Nominal yang sudah dibayar
            $table->dateTime('paid_at')->nullable(); // Tanggal dibayar
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('note')->nullable(); // Catatan pembayaran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_payments');
    }
};
