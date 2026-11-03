<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->renameColumn('no_telepon', 'telepon');
        });

        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nama');
            $table->text('catatan_admin')->nullable()->after('pesan');
        });

        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->string('status')->default('baru')->change();
        });

        DB::table('kontak_masuk')
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'baru']);

        DB::table('kontak_masuk')
            ->where('status', 'sudah_dibaca')
            ->update(['status' => 'dibaca']);
    }

    public function down(): void
    {
        DB::table('kontak_masuk')
            ->whereIn('status', ['baru', 'dibalas', 'arsip'])
            ->update(['status' => 'belum_dibaca']);

        DB::table('kontak_masuk')
            ->where('status', 'dibaca')
            ->update(['status' => 'sudah_dibaca']);

        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->enum('status', ['belum_dibaca', 'sudah_dibaca'])->default('belum_dibaca')->change();
        });

        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->dropColumn(['email', 'catatan_admin']);
            $table->renameColumn('telepon', 'no_telepon');
        });
    }
};
