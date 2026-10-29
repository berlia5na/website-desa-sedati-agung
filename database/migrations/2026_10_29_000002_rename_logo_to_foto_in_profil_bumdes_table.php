<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_bumdes', function (Blueprint $table) {
            $table->renameColumn('logo', 'foto');
        });
    }

    public function down(): void
    {
        Schema::table('profil_bumdes', function (Blueprint $table) {
            $table->renameColumn('foto', 'logo');
        });
    }
};
