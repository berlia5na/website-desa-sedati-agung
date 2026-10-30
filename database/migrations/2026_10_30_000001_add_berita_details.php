<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('slug');
            $table->string('badge_kategori')->nullable()->after('kategori');
            $table->text('excerpt')->nullable()->after('isi');
            $table->string('author_name')->nullable()->after('excerpt');
            $table->integer('read_time')->nullable()->after('author_name');
            $table->boolean('is_featured')->default(false)->after('read_time');
        });
    }

    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'badge_kategori', 'excerpt', 'author_name', 'read_time', 'is_featured']);
        });
    }
};
