<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->unsignedSmallInteger('anggaran_tahun')->nullable();
            $table->unsignedBigInteger('anggaran_pendapatan')->nullable();
            $table->unsignedBigInteger('realisasi_pendapatan')->nullable();
            $table->unsignedBigInteger('anggaran_belanja')->nullable();
            $table->unsignedBigInteger('realisasi_belanja')->nullable();
            $table->unsignedBigInteger('anggaran_pembiayaan')->nullable();
            $table->unsignedBigInteger('realisasi_pembiayaan')->nullable();
            $table->text('anggaran_keterangan')->nullable();
            $table->string('anggaran_dokumen_url', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->dropColumn([
                'anggaran_tahun',
                'anggaran_pendapatan',
                'realisasi_pendapatan',
                'anggaran_belanja',
                'realisasi_belanja',
                'anggaran_pembiayaan',
                'realisasi_pembiayaan',
                'anggaran_keterangan',
                'anggaran_dokumen_url',
            ]);
        });
    }
};
