<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->string('jenis_kegiatan')->nullable()->after('kategori');
            $table->string('jenis_undangan')->nullable()->after('jenis_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn(['jenis_kegiatan', 'jenis_undangan']);
        });
    }
};
