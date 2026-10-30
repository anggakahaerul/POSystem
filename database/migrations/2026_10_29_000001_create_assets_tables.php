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
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sequence_number')->index();
                $table->string('asset_code', 60)->unique();
                $table->unsignedBigInteger('purchase_order_id')->nullable()->index();
                $table->unsignedBigInteger('purchase_order_detail_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('branch_id')->nullable()->index();

                $table->string('cabang', 120)->default('Sidoarjo');
                $table->string('kategori', 120)->default('Elektronik');
                $table->string('divisi', 120)->default('HRGA');
                $table->string('user_holder', 150)->default('Pool Gudang / GA');
                $table->string('nama_barang', 255);
                $table->string('status', 50)->default('Tersedia');

                // Spesifikasi Teknis Lengkap (19 Field)
                $table->string('merk', 120)->nullable()->default('-');
                $table->string('processor', 150)->nullable()->default('-');
                $table->string('model', 150)->nullable()->default('-');
                $table->string('generasi', 100)->nullable()->default('-');
                $table->string('ram', 100)->nullable()->default('-');
                $table->string('harddisk', 120)->nullable()->default('-');
                $table->string('serial_number', 150)->nullable()->default('-');
                $table->string('os', 120)->nullable()->default('-');
                $table->string('warna', 100)->nullable()->default('-');
                $table->decimal('harga', 15, 2)->default(0);
                $table->string('supplier', 200)->nullable()->default('-');
                $table->date('tanggal_terima')->nullable();
                $table->text('deskripsi')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_histories')) {
            Schema::create('asset_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->dateTime('waktu');
                $table->string('pic', 150)->default('Sistem');
                $table->string('cabang', 120)->default('-');
                $table->string('user_holder', 150)->default('-');
                $table->string('aksi', 120);
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asset_repairs')) {
            Schema::create('asset_repairs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->dateTime('waktu');
                $table->text('deskripsi');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_repairs');
        Schema::dropIfExists('asset_histories');
        Schema::dropIfExists('assets');
    }
};

