<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_desa', function (Blueprint $table) {
            $table->string('subtitle')->nullable();
            $table->string('badge')->nullable();
            $table->string('judul')->nullable();
            $table->text('kutipan')->nullable();
            $table->string('nama_kades')->nullable();
            $table->string('jabatan_kades')->nullable();
            $table->string('caption_hero')->nullable();
            $table->string('visi_label')->nullable();
            $table->text('visi_catatan')->nullable();
            $table->text('sejarah_sorotan')->nullable();
            $table->string('periode_data')->nullable();
            $table->unsignedInteger('penduduk')->nullable();
            $table->decimal('luas_wilayah', 6, 2)->nullable();
            $table->unsignedTinyInteger('jumlah_dusun')->nullable();
            $table->unsignedInteger('lahan_tani')->nullable();
            $table->string('jam_layanan')->nullable();
            $table->string('foto_kantor')->nullable();
        });

        if (!Schema::hasTable('perangkat_desas')) {
            Schema::create('perangkat_desas', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('jabatan');
                $table->string('deskripsi')->nullable();
                $table->string('foto')->nullable();
                $table->unsignedTinyInteger('urutan')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('profil_desa', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'badge',
                'judul',
                'kutipan',
                'nama_kades',
                'jabatan_kades',
                'caption_hero',
                'visi_label',
                'visi_catatan',
                'sejarah_sorotan',
                'periode_data',
                'penduduk',
                'luas_wilayah',
                'jumlah_dusun',
                'lahan_tani',
                'jam_layanan',
                'foto_kantor',
            ]);
        });

        // Tabel perangkat_desas sengaja tidak dihapus saat rollback.
    }
};
