<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->boolean('sambutan_tampil')->default(true)->change();
        });

        DB::table('halaman_beranda')->update(['sambutan_tampil' => true]);
    }

    public function down(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->boolean('sambutan_tampil')->default(false)->change();
        });
    }
};
