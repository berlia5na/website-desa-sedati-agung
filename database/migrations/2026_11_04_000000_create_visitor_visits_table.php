<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->char('visitor_hash', 64);
            $table->date('visit_date');
            $table->timestamps();
            $table->unique(['visitor_hash', 'visit_date']);
            $table->index('visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
