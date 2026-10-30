<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_remain_available_without_authentication(): void
    {
        $paths = [
            '/',
            '/profil-desa',
            '/profil-bumdes',
            '/berita',
            '/galeri',
            '/agenda',
            '/kontak',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_admin_pages_require_authentication(): void
    {
        foreach (['/admin', '/admin/beranda', '/admin/profil-desa', '/admin/berita', '/admin/profil-bumdes', '/admin/agenda', '/admin/kontak'] as $path) {
            $this->get($path)->assertRedirect(route('admin.login'));
        }
    }

    public function test_gallery_is_reached_from_news_instead_of_the_main_navigation(): void
    {
        $this->get(route('berita'))
            ->assertOk()
            ->assertSee('Lihat Semua Galeri')
            ->assertSee(route('galeri'))
            ->assertSee('href="'.route('kontak').'">Kontak</a>', false)
            ->assertDontSee('href="'.route('galeri').'">Galeri</a>', false);
    }
}
