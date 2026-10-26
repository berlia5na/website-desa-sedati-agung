<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->string('hero_button_label')->nullable();
            $table->string('hero_button_url')->nullable();
            $table->string('sambutan_nama')->nullable();
            $table->string('sambutan_jabatan')->nullable();
            $table->longText('sambutan_teks')->nullable();
            $table->string('sambutan_foto')->nullable();
            $table->boolean('sambutan_tampil')->default(true);
            $table->json('sorotan')->nullable();
            $table->string('widget_judul')->nullable();
            $table->text('widget_teks')->nullable();
            $table->string('widget_tombol')->nullable();
            $table->string('widget_tautan')->nullable();
            $table->boolean('widget_tampil')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('halaman_beranda', function (Blueprint $table) {
            $table->dropConstrainedForeignId('published_by');
            $table->dropColumn([
                'hero_button_label',
                'hero_button_url',
                'sambutan_nama',
                'sambutan_jabatan',
                'sambutan_teks',
                'sambutan_foto',
                'sambutan_tampil',
                'sorotan',
                'widget_judul',
                'widget_teks',
                'widget_tombol',
                'widget_tautan',
                'widget_tampil',
                'published_at',
            ]);
        });
    }
};
