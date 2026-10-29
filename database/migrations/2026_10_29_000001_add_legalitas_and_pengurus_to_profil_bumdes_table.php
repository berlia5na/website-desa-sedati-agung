<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_bumdes', function (Blueprint $table) {
            $table->json('legalitas')->nullable();
            $table->json('pengurus')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profil_bumdes', function (Blueprint $table) {
            $table->dropColumn(['legalitas', 'pengurus']);
        });
    }
};
