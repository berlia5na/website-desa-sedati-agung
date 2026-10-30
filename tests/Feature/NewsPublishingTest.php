<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_news_can_be_opened_from_its_own_detail_page(): void
    {
        $author = User::factory()->create();
        $article = Berita::query()->create([
            'user_id' => $author->id,
            'judul' => 'Gotong Royong Warga',
            'slug' => 'gotong-royong-warga',
            'kategori' => 'Acara Desa',
            'excerpt' => 'Warga bersama membersihkan lingkungan desa.',
            'isi' => "Kegiatan dilaksanakan pada hari Minggu.\nWarga dari tiga dusun ikut serta.",
            'status' => 'published',
        ]);

        $this->get(route('berita'))
            ->assertOk()
            ->assertSee(route('berita.detail', $article->slug));

        $this->get(route('berita.detail', $article->slug))
            ->assertOk()
            ->assertSee($article->judul)
            ->assertSee('Warga dari tiga dusun ikut serta.')
            ->assertSee('Kembali ke semua berita');
    }

    public function test_draft_news_is_not_available_on_public_detail_pages(): void
    {
        $author = User::factory()->create();
        $article = Berita::query()->create([
            'user_id' => $author->id,
            'judul' => 'Draft Kegiatan',
            'slug' => 'draft-kegiatan',
            'isi' => 'Konten yang belum dipublikasikan.',
            'status' => 'draft',
        ]);

        $this->get(route('berita.detail', $article->slug))->assertNotFound();
    }

    public function test_gallery_page_shows_only_photos_from_published_news_and_news_page_links_to_it(): void
    {
        $author = User::factory()->create();
        $published = Berita::query()->create([
            'user_id' => $author->id,
            'judul' => 'Dokumentasi Kerja Bakti',
            'slug' => 'dokumentasi-kerja-bakti',
            'kategori' => 'Acara Desa',
            'isi' => 'Warga melaksanakan kerja bakti.',
            'gambar' => 'https://example.test/kerja-bakti.jpg',
            'status' => 'published',
        ]);
        Berita::query()->create([
            'user_id' => $author->id,
            'judul' => 'Draft Dokumentasi',
            'slug' => 'draft-dokumentasi',
            'isi' => 'Dokumentasi draft tidak untuk publik.',
            'gambar' => 'https://example.test/draft.jpg',
            'status' => 'draft',
        ]);

        $this->get(route('galeri'))
            ->assertOk()
            ->assertSee($published->judul)
            ->assertSee($published->gambar)
            ->assertDontSee('Draft Dokumentasi');

        $this->get(route('berita'))
            ->assertOk()
            ->assertSee(route('galeri'))
            ->assertDontSee('https://images.unsplash.com/');
    }

    public function test_admin_can_create_and_publish_a_news_article(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Desa']);

        $this->actingAs($admin)
            ->get(route('admin.berita.create'))
            ->assertOk()
            ->assertSee('Tulis Berita Kegiatan');

        $this->actingAs($admin)
            ->post(route('admin.berita.store'), [
                'judul' => 'Pelatihan UMKM Desa',
                'kategori' => 'UMKM Binaan',
                'excerpt' => 'Pelatihan untuk pelaku usaha desa.',
                'isi' => 'Pelatihan berlangsung di balai desa dan diikuti warga.',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.berita'))
            ->assertSessionHas('status');

        $article = Berita::query()->where('judul', 'Pelatihan UMKM Desa')->firstOrFail();
        $this->assertSame('pelatihan-umkm-desa', $article->slug);
        $this->assertSame($admin->id, $article->user_id);
        $this->assertSame('published', $article->status);

        $this->get(route('berita.detail', $article->slug))
            ->assertOk()
            ->assertSee($article->isi);

        $this->get(route('admin.berita'))
            ->assertOk()
            ->assertSee('Pelatihan UMKM Desa')
            ->assertSee('Lihat halaman publik')
            ->assertSee('Header halaman publik')
            ->assertSee('Galeri kegiatan')
            ->assertSee('Tambah foto');
    }

    public function test_admin_can_edit_and_remove_a_news_article(): void
    {
        $admin = User::factory()->create();
        $article = Berita::query()->create([
            'user_id' => $admin->id,
            'judul' => 'Berita Lama',
            'slug' => 'berita-lama',
            'kategori' => 'Acara Desa',
            'isi' => 'Isi awal berita.',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.berita.edit', $article))
            ->assertOk()
            ->assertSee('Ubah Berita Kegiatan');

        $this->put(route('admin.berita.update', $article), [
            'judul' => 'Berita Diperbarui',
            'kategori' => 'Acara Desa',
            'isi' => 'Isi berita yang sudah diperbarui.',
            'status' => 'published',
        ])->assertRedirect(route('admin.berita'));

        $this->assertDatabaseHas('berita', [
            'id' => $article->id,
            'judul' => 'Berita Diperbarui',
            'slug' => 'berita-lama',
            'status' => 'published',
        ]);

        $this->delete(route('admin.berita.delete', $article))
            ->assertRedirect(route('admin.berita'));

        $this->assertDatabaseMissing('berita', ['id' => $article->id]);
    }

    public function test_admin_can_update_the_public_news_header(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.berita.header.update'), [
                'badge' => 'Kabar Warga',
                'title' => 'Kegiatan Terbaru Desa',
                'description' => 'Berita dan kegiatan terbaru untuk warga Sedati Agung.',
            ])
            ->assertRedirect(route('admin.berita'));

        $this->assertDatabaseHas('news_page_settings', [
            'badge' => 'Kabar Warga',
            'title' => 'Kegiatan Terbaru Desa',
        ]);

        $this->get(route('berita'))
            ->assertOk()
            ->assertSee('Kabar Warga')
            ->assertSee('Kegiatan Terbaru Desa')
            ->assertSee('Berita dan kegiatan terbaru untuk warga Sedati Agung.');
    }

    public function test_gallery_photos_are_displayed_publicly_and_can_be_removed_by_admin(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('galeri-kegiatan/kerja-bakti.jpg', 'image-content');
        $admin = User::factory()->create();
        $photo = GalleryPhoto::query()->create([
            'user_id' => $admin->id,
            'title' => 'Kerja Bakti Warga',
            'caption' => 'Balai desa',
            'image_path' => 'galeri-kegiatan/kerja-bakti.jpg',
        ]);

        $this->get(route('galeri'))
            ->assertOk()
            ->assertSee('Kerja Bakti Warga')
            ->assertSee('/storage/galeri-kegiatan/kerja-bakti.jpg');

        $this->actingAs($admin)
            ->delete(route('admin.galeri.delete', $photo))
            ->assertRedirect(route('admin.berita'));

        $this->assertDatabaseMissing('gallery_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('galeri-kegiatan/kerja-bakti.jpg');
    }
}
