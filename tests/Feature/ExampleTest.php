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
}
