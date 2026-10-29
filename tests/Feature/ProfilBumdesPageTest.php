<?php

namespace Tests\Feature;

use App\Models\ProfilBumdes;
use App\Models\UnitBumdes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilBumdesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_bumdes_page_shows_its_sections_and_uses_the_shared_footer(): void
    {
        $response = $this->get(route('profil-bumdes'));

        $response->assertOk()
            ->assertSee('BUMDes Sedati Agung')
            ->assertSee('Latar Belakang &amp; Legalitas', false)
            ->assertSee('Visi dan Misi BUMDes')
            ->assertSee('Struktur Pengelola')
            ->assertSee('Unit Usaha BUMDes')
            ->assertSee('Unit usaha sedang disiapkan')
            ->assertSee('site-footer', false);
    }

    public function test_bumdes_page_shows_saved_profile_missions_and_only_active_units(): void
    {
        $profil = ProfilBumdes::create([
            'nama_bumdes' => 'BUMDes Makmur',
            'deskripsi' => 'Deskripsi profil usaha desa.',
            'visi' => 'Visi yang disimpan.',
            'misi' => "Misi satu.\nMisi dua.",
        ]);

        UnitBumdes::create([
            'bumdes_id' => $profil->id,
            'nama_unit' => 'Unit Pertanian',
            'jenis' => 'Pertanian',
            'deskripsi' => 'Mengembangkan hasil tani warga.',
            'telepon' => '0812-3456-7890',
            'status' => 'aktif',
        ]);
        UnitBumdes::create([
            'bumdes_id' => $profil->id,
            'nama_unit' => 'Unit Nonaktif',
            'jenis' => 'Layanan',
            'status' => 'nonaktif',
        ]);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee('BUMDes Makmur')
            ->assertSee('Deskripsi profil usaha desa.')
            ->assertSee('Visi yang disimpan.')
            ->assertSee('Misi satu.')
            ->assertSee('Misi dua.')
            ->assertSee('Unit Pertanian')
            ->assertSee('tel:081234567890', false)
            ->assertDontSee('Unit Nonaktif')
            ->assertSee('>1</strong>', false)
            ->assertSee('Unit usaha aktif');
    }

    public function test_bumdes_page_shows_profile_photo_and_manager_cards(): void
    {
        Storage::fake('public');
        $photo = UploadedFile::fake()->image('foto-bumdes.jpg')->store('profil-bumdes', 'public');
        $profil = ProfilBumdes::create([
            'nama_bumdes' => 'BUMDes Makmur',
            'foto' => $photo,
            'pengurus' => [
                ['nama' => 'Ibu Siti', 'jabatan' => 'Direktur', 'deskripsi' => 'Mengelola unit usaha.', 'foto' => null],
            ],
        ]);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee(Storage::url($profil->foto), false)
            ->assertSee('Foto BUMDes Makmur')
            ->assertSee('Ibu Siti')
            ->assertSee('Direktur')
            ->assertSee('Mengelola unit usaha.');
    }
}
