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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('attachment');
            $table->string('attachment_name')->nullable();
            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('pr_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
