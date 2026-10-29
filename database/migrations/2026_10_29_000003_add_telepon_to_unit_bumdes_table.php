<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_bumdes', function (Blueprint $table) {
            $table->string('telepon', 40)->nullable()->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('unit_bumdes', function (Blueprint $table) {
            $table->dropColumn('telepon');
        });
    }
};
