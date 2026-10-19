<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('sesi')->nullable();
            $table->string('kategori')->nullable();
            $table->string('badge_kategori')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('pic')->nullable();
            $table->string('gambar')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_prioritas')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
