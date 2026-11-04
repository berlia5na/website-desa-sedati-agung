<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\HalamanBeranda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomePageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_home_page_editor_and_see_its_sections(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Kelola Halaman Beranda')
            ->assertSee('Mode gelap')
            ->assertSee('data-theme-toggle', false)
            ->assertSee('Hero &amp; Banner Utama', false)
            ->assertSee('Statistik Utama Desa')
            ->assertSee('Sambutan Kepala Desa')
            ->assertSee('Hapus sambutan dari beranda')
            ->assertSee('name="sambutan_tampil" value="1" checked', false)
            ->assertSee('Widget &amp; Banner Layanan', false)
            ->assertSee('Live Visual Mockup Preview')
            ->assertSee('Batal Perubahan')
            ->assertSee('Simpan &amp; Publikasikan ke Web', false);
    }

    public function test_admin_can_publish_home_page_content_statistics_and_selected_news_and_agenda(): void
    {
        $admin = User::factory()->create();
        $berita = Berita::create([
            'user_id' => $admin->id,
            'judul' => 'Berita Pilihan',
            'slug' => 'berita-pilihan',
            'isi' => 'Isi berita pilihan',
            'status' => 'published',
        ]);
        $agenda = Agenda::create([
            'user_id' => $admin->id,
            'judul' => 'Agenda Pilihan',
            'tanggal' => today()->addDay(),
            'status' => 'published',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.beranda.update'), [
                'judul' => 'Maju Bersama Sedati Agung',
                'deskripsi' => 'Informasi resmi desa untuk masyarakat.',
                'hero_button_label' => 'Lihat Profil Desa',
                'hero_button_url' => '/profil-desa',
                'jumlah_penduduk' => 4321,
                'jumlah_usaha_bumdes' => 4,
                'jumlah_umkm' => 56,
                'sambutan_nama' => 'Kepala Desa',
                'sambutan_jabatan' => 'Kepala Desa Sedati Agung',
                'sambutan_teks' => 'Sambutan resmi untuk warga.',
                'sambutan_tampil' => '1',
                'sorotan' => ["berita:{$berita->id}", "agenda:{$agenda->id}"],
                'widget_judul' => 'Layanan Warga',
                'widget_teks' => 'Informasi layanan masyarakat.',
                'widget_tombol' => 'Hubungi Kami',
                'widget_tautan' => '/kontak',
                'widget_tampil' => '1',
            ])
            ->assertRedirect(route('admin.beranda'))
            ->assertSessionHas('status');

        $halaman = HalamanBeranda::query()->firstOrFail();
        $this->assertSame('published', $halaman->status);
        $this->assertSame($admin->id, $halaman->published_by);
        $this->assertSame(["berita:{$berita->id}", "agenda:{$agenda->id}"], $halaman->sorotan);
        $this->assertDatabaseHas('statistik_desa', [
            'jumlah_penduduk' => 4321,
            'jumlah_usaha_bumdes' => 4,
            'jumlah_umkm' => 56,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Maju Bersama Sedati Agung')
            ->assertSee('Berita Pilihan')
            ->assertSee('Agenda Pilihan')
            ->assertSee('Sambutan resmi untuk warga.')
            ->assertSee('Layanan Warga')
            ->assertSee('4.321')
            ->assertDontSee('Berita Terkini');
    }

    public function test_admin_can_publish_budget_transparency_for_the_public_home_page(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Transparansi Anggaran')
            ->assertSee('name="anggaran_pendapatan"', false)
            ->assertSee('name="realisasi_belanja"', false);

        $this->put(route('admin.beranda.update'), [
            'jumlah_usaha_bumdes' => 4,
            'anggaran_tahun' => 2026,
            'anggaran_pendapatan' => 1500000000,
            'realisasi_pendapatan' => 1200000000,
            'anggaran_belanja' => 1400000000,
            'realisasi_belanja' => 950000000,
            'anggaran_pembiayaan' => 100000000,
            'realisasi_pembiayaan' => 50000000,
            'anggaran_keterangan' => 'Realisasi sampai dengan semester pertama.',
            'anggaran_dokumen_url' => 'https://example.test/apbdes-2026.pdf',
        ])
            ->assertRedirect(route('admin.beranda'));

        $this->assertDatabaseHas('halaman_beranda', [
            'anggaran_tahun' => 2026,
            'anggaran_pendapatan' => 1500000000,
            'realisasi_belanja' => 950000000,
            'anggaran_keterangan' => 'Realisasi sampai dengan semester pertama.',
            'anggaran_dokumen_url' => 'https://example.test/apbdes-2026.pdf',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Transparansi Anggaran Desa')
            ->assertSee('Tahun Anggaran 2026')
            ->assertSee('Rp 1.500.000.000')
            ->assertSee('Rp 950.000.000')
            ->assertSee('Realisasi sampai dengan semester pertama.')
            ->assertSee('https://example.test/apbdes-2026.pdf');
    }

    public function test_budget_document_link_only_accepts_local_paths_or_http_urls(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('admin.beranda'))
            ->put(route('admin.beranda.update'), [
                'jumlah_usaha_bumdes' => 4,
                'anggaran_dokumen_url' => 'javascript:alert(1)',
            ])
            ->assertRedirect(route('admin.beranda'))
            ->assertSessionHasErrors('anggaran_dokumen_url');
    }

    public function test_admin_can_upload_banner_and_welcome_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.beranda.update'), [
                'judul' => 'Beranda Desa',
                'jumlah_usaha_bumdes' => 4,
                'hero_image' => UploadedFile::fake()->image('banner.jpg'),
                'sambutan_foto' => UploadedFile::fake()->image('kepala-desa.jpg'),
            ])
            ->assertRedirect(route('admin.beranda'));

        $halaman = HalamanBeranda::query()->firstOrFail();
        Storage::disk('public')->assertExists($halaman->gambar);
        Storage::disk('public')->assertExists($halaman->sambutan_foto);
    }

    public function test_greeting_is_visible_by_default_on_the_public_home_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Sambutan Kepala Desa')
            ->assertSee('Selamat datang di website resmi Desa Sedati Agung');
    }

    public function test_admin_can_delete_the_greeting_and_its_uploaded_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $photo = UploadedFile::fake()->image('sambutan.jpg')->store('halaman-beranda', 'public');
        $halaman = HalamanBeranda::create([
            'judul' => 'Beranda Desa',
            'status' => 'published',
            'sambutan_nama' => 'Nama Kepala Desa',
            'sambutan_jabatan' => 'Kepala Desa',
            'sambutan_teks' => 'Teks sambutan untuk dihapus.',
            'sambutan_foto' => $photo,
            'sambutan_tampil' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.beranda.sambutan.delete'))
            ->assertRedirect(route('admin.beranda'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('halaman_beranda', [
            'id' => $halaman->id,
            'sambutan_nama' => null,
            'sambutan_jabatan' => null,
            'sambutan_teks' => null,
            'sambutan_foto' => null,
            'sambutan_tampil' => false,
        ]);
        Storage::disk('public')->assertMissing($photo);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Sambutan Kepala Desa')
            ->assertDontSee('Teks sambutan untuk dihapus.');
    }

    public function test_admin_can_remove_homepage_photos_without_deleting_the_greeting_text(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('halaman-beranda/banner.jpg', 'banner-image');
        Storage::disk('public')->put('halaman-beranda/sambutan.jpg', 'welcome-image');
        $admin = User::factory()->create();
        HalamanBeranda::create([
            'judul' => 'Beranda Desa',
            'status' => 'published',
            'gambar' => 'halaman-beranda/banner.jpg',
            'sambutan_nama' => 'Nama Kepala Desa',
            'sambutan_jabatan' => 'Kepala Desa',
            'sambutan_teks' => 'Sambutan yang tetap dipertahankan.',
            'sambutan_foto' => 'halaman-beranda/sambutan.jpg',
            'sambutan_tampil' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('name="hapus_gambar_hero"', false)
            ->assertSee('name="hapus_foto_sambutan"', false);

        $this->actingAs($admin)
            ->put(route('admin.beranda.update'), [
                'jumlah_usaha_bumdes' => 4,
                'sambutan_nama' => 'Nama Kepala Desa',
                'sambutan_jabatan' => 'Kepala Desa',
                'sambutan_teks' => 'Sambutan yang tetap dipertahankan.',
                'sambutan_tampil' => '1',
                'hapus_gambar_hero' => '1',
                'hapus_foto_sambutan' => '1',
            ])
            ->assertRedirect(route('admin.beranda'));

        $halaman = HalamanBeranda::query()->firstOrFail();
        $this->assertNull($halaman->gambar);
        $this->assertNull($halaman->sambutan_foto);
        $this->assertSame('Sambutan yang tetap dipertahankan.', $halaman->sambutan_teks);
        Storage::disk('public')->assertMissing('halaman-beranda/banner.jpg');
        Storage::disk('public')->assertMissing('halaman-beranda/sambutan.jpg');
    }

    public function test_guests_cannot_access_or_publish_home_page_content(): void
    {
        $this->get(route('admin.beranda'))
            ->assertRedirect(route('admin.login'));

        $this->put(route('admin.beranda.update'), [
            'jumlah_usaha_bumdes' => 4,
        ])->assertRedirect(route('admin.login'));
    }

    public function test_invalid_spotlight_and_unsafe_links_are_rejected(): void
    {
        $admin = User::factory()->create();
        $draft = Berita::create([
            'user_id' => $admin->id,
            'judul' => 'Berita Draft',
            'slug' => 'berita-draft',
            'isi' => 'Belum dipublikasikan',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.beranda'))
            ->put(route('admin.beranda.update'), [
                'jumlah_usaha_bumdes' => 4,
                'sorotan' => ["berita:{$draft->id}"],
            ])
            ->assertRedirect(route('admin.beranda'))
            ->assertSessionHasErrors('sorotan');

        $this->put(route('admin.beranda.update'), [
            'jumlah_usaha_bumdes' => 4,
            'hero_button_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('hero_button_url');

        $this->assertDatabaseMissing('halaman_beranda', [
            'status' => 'published',
        ]);
    }
}
