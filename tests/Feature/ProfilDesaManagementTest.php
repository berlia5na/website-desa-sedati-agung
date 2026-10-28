<?php

namespace Tests\Feature;

use App\Models\PerangkatDesa;
use App\Models\ProfilDesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilDesaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_profile_editor_and_guest_access_is_protected(): void
    {
        $this->get(route('admin.profil-desa'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.profil-desa'))
            ->assertOk()
            ->assertSee('BAGIAN PEMBUKA WEBSITE')
            ->assertSee('Visi &amp; Misi Desa', false)
            ->assertSee('Sejarah Singkat', false)
            ->assertSee('Data Umum &amp; Demografi Desa', false)
            ->assertSee('Struktur Perangkat Desa')
            ->assertSee('data-add-official', false)
            ->assertSee('Kantor Balai Desa &amp; Kontak Layanan', false)
            ->assertSee('EDITOR HALAMAN WEBSITE')
            ->assertSee('admin-profil-desa.css')
            ->assertSee('name="nama_desa"', false)
            ->assertDontSee('placeholder pengelolaan profil desa');

        $this->get(route('admin.beranda'))
            ->assertOk()
            ->assertDontSee('admin-profil-desa.css');
    }

    public function test_admin_can_save_profile_content_and_existing_official_records_are_retained(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $profil = ProfilDesa::create([
            'nama_desa' => 'Desa Sedati Agung',
            'visi' => 'Visi yang telah tersimpan',
        ]);
        $existingOfficial = PerangkatDesa::create([
            'nama' => 'Nama lama',
            'jabatan' => 'Sekretaris Desa',
            'deskripsi' => 'Data perangkat yang sudah ada.',
            'urutan' => 1,
        ]);
        $additionalOfficial = PerangkatDesa::create([
            'nama' => 'Perangkat di luar empat kartu',
            'jabatan' => 'Staf',
            'urutan' => 5,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.profil-desa.update'), [
                'nama_desa' => 'Desa Sedati Agung',
                'subtitle' => 'Kecamatan Sedati, Kabupaten Sidoarjo',
                'badge' => 'Profil resmi desa',
                'judul' => 'Profil Desa Sedati Agung',
                'deskripsi' => 'Narasi resmi yang dimasukkan admin.',
                'visi' => 'Visi desa terbaru.',
                'misi' => "Misi pertama.\nMisi kedua.",
                'penduduk' => 12345,
                'luas_wilayah' => 2.5,
                'jumlah_dusun' => 4,
                'lahan_tani' => 68,
                'alamat' => 'Alamat kantor resmi.',
                'lokasi_peta' => 'Lokasi kantor yang sudah diverifikasi',
                'email' => 'desa@example.test',
                'foto' => UploadedFile::fake()->image('profil.jpg'),
                'foto_kantor' => UploadedFile::fake()->image('kantor.jpg'),
                'perangkat' => [
                    [
                        'id' => $existingOfficial->id,
                        'nama' => 'Nama perangkat diperbarui',
                        'jabatan' => 'Sekretaris Desa',
                        'deskripsi' => 'Uraian tugas terbaru.',
                    ],
                    [
                        'nama' => 'Perangkat baru',
                        'jabatan' => 'Kaur Pelayanan',
                        'deskripsi' => 'Melayani administrasi warga.',
                        'foto' => UploadedFile::fake()->image('perangkat-baru.jpg'),
                    ],
                    ['nama' => 'Anggota ketiga', 'jabatan' => 'Kaur Keuangan'],
                    ['nama' => 'Anggota keempat', 'jabatan' => 'Kasi Pemerintahan'],
                    ['nama' => 'Anggota kelima', 'jabatan' => 'Kasi Kesejahteraan'],
                ],
            ])
            ->assertRedirect(route('admin.profil-desa'))
            ->assertSessionHas('status');

        $profil->refresh();
        $existingOfficial->refresh();

        $this->assertSame('Visi desa terbaru.', $profil->visi);
        $this->assertSame('Narasi resmi yang dimasukkan admin.', $profil->deskripsi);
        $this->assertSame(12345, $profil->penduduk);
        $this->assertSame('Lokasi kantor yang sudah diverifikasi', $profil->lokasi_peta);
        $this->assertSame('Nama perangkat diperbarui', $existingOfficial->nama);
        $newOfficial = PerangkatDesa::query()->where('nama', 'Perangkat baru')->firstOrFail();
        $this->assertDatabaseHas('perangkat_desas', ['nama' => 'Anggota kelima']);
        $this->assertDatabaseHas('perangkat_desas', ['id' => $additionalOfficial->id]);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $newOfficial->foto));
        Storage::disk('public')->assertExists(str_replace('storage/', '', $profil->foto));
        Storage::disk('public')->assertExists(str_replace('storage/', '', $profil->foto_kantor));

        $this->get(route('profil-desa'))
            ->assertOk()
            ->assertSee('desa@example.test')
            ->assertSee('Kecamatan Sedati, Kabupaten Sidoarjo')
            ->assertSee('Peta lokasi Desa Sedati Agung')
            ->assertSee('Lokasi+kantor+yang+sudah+diverifikasi')
            ->assertSee('stat__icon--blue" aria-hidden="true"><svg', false)
            ->assertDontSee('👥');
    }

    public function test_admin_can_remove_a_selected_official_without_removing_other_records(): void
    {
        $admin = User::factory()->create();
        ProfilDesa::create(['nama_desa' => 'Desa Sedati Agung']);
        $toDelete = PerangkatDesa::create([
            'nama' => 'Perangkat yang dihapus',
            'jabatan' => 'Staf',
            'urutan' => 1,
        ]);
        $toKeep = PerangkatDesa::create([
            'nama' => 'Perangkat yang dipertahankan',
            'jabatan' => 'Sekretaris Desa',
            'urutan' => 2,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.profil-desa.update'), [
                'nama_desa' => 'Desa Sedati Agung',
                'perangkat' => [
                    ['id' => $toDelete->id, 'hapus' => '1'],
                    [
                        'id' => $toKeep->id,
                        'hapus' => '0',
                        'nama' => 'Perangkat yang dipertahankan',
                        'jabatan' => 'Sekretaris Desa',
                    ],
                    ['nama' => ''],
                ],
            ])
            ->assertRedirect(route('admin.profil-desa'));

        $this->assertDatabaseMissing('perangkat_desas', ['id' => $toDelete->id]);
        $this->assertDatabaseHas('perangkat_desas', ['id' => $toKeep->id]);
    }

    public function test_profile_validation_rejects_an_official_photo_without_a_name(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create())
            ->from(route('admin.profil-desa'))
            ->put(route('admin.profil-desa.update'), [
                'nama_desa' => 'Desa Sedati Agung',
                'perangkat' => [
                    ['foto' => UploadedFile::fake()->image('perangkat.jpg')],
                ],
            ])
            ->assertRedirect(route('admin.profil-desa'))
            ->assertSessionHasErrors('perangkat.0.nama');

        $this->assertDatabaseCount('profil_desa', 0);
    }
}
