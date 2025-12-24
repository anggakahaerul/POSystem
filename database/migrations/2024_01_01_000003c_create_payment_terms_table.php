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
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // COD, Net 7, Net 30, DP 50%, etc.
            $table->integer('days')->default(0); // 0 for COD, 7, 14, 30, 60
            $table->enum('payment_type', ['full', 'partial'])->default('full');
            $table->json('installments')->nullable(); // For partial: [{percentage, days, description}]
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};
