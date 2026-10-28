<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('badge_color', 30)->default('info');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial data
        DB::table('asset_types')->insert([
            [
                'code' => 'HRGA',
                'name' => 'HRGA (General Affairs / Umum)',
                'description' => 'Aset operasional umum, perlengkapan kantor, dan fasilitas gedung',
                'badge_color' => 'info',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MIS',
                'name' => 'MIS (IT / Komputer / Sistem)',
                'description' => 'Aset teknologi informasi, komputer, jaringan, dan perangkat elektronik',
                'badge_color' => 'primary',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_types');
    }
};
