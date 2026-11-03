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
            $table->string('telepon')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('kontak_masuk')
            ->whereNull('telepon')
            ->update(['telepon' => '']);

        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->string('telepon')->nullable(false)->change();
        });
    }
};
