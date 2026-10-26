<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistik_desa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jumlah_penduduk')->nullable();
            $table->unsignedInteger('jumlah_usaha_bumdes')->default(4);
            $table->unsignedInteger('jumlah_umkm')->nullable();
            $table->timestamps();
        });

        DB::table('statistik_desa')->insert([
            'jumlah_usaha_bumdes' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('statistik_desa');
    }
};
