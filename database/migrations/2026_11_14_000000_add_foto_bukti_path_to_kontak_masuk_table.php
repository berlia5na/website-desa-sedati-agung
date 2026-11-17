<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->string('foto_bukti_path')->nullable()->after('pesan');
        });
    }

    public function down(): void
    {
        Schema::table('kontak_masuk', function (Blueprint $table) {
            $table->dropColumn('foto_bukti_path');
        });
    }
};
