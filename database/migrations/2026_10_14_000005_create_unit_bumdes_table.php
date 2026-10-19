<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_bumdes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bumdes_id')->constrained('profil_bumdes')->cascadeOnDelete();
            $table->string('nama_unit');
            $table->string('jenis');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->string('gambar')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_bumdes');
    }
};
