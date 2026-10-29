<?php

namespace Tests\Feature;

use App\Models\ProfilBumdes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilBumdesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_bumdes_photo_uploader_and_guests_are_redirected(): void
    {
        $this->get(route('admin.profil-bumdes'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.profil-bumdes'))
            ->assertOk()
            ->assertSee('Kelola Profil BUMDes')
            ->assertSee('Foto BUMDes')
            ->assertSee('legalitas[perdes_nomor]', false)
            ->assertSee('pengurus[0][nama]', false)
            ->assertSee('data-add-bumdes-manager', false)
            ->assertSee('data-add-bumdes-unit', false)
            ->assertSee('name="foto"', false)
            ->assertSee('action="' . route('admin.profil-bumdes.update') . '"', false);
    }

    public function test_admin_can_upload_and_replace_the_public_bumdes_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Sedati Agung']);
        $oldPhoto = UploadedFile::fake()->image('foto-lama.jpg')->store('profil-bumdes', 'public');
        $profil->update(['foto' => $oldPhoto]);

        $this->actingAs($admin)
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'foto' => UploadedFile::fake()->image('foto-baru.png'),
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHas('status');

        $profil->refresh();
        $this->assertNotSame($oldPhoto, $profil->foto);
        Storage::disk('public')->assertMissing($oldPhoto);
        Storage::disk('public')->assertExists($profil->foto);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee(\Illuminate\Support\Facades\Storage::url($profil->foto), false)
            ->assertDontSee('<span>SA</span>', false);
    }

    public function test_invalid_photo_upload_is_rejected_without_changing_the_saved_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Sedati Agung']);
        $oldPhoto = UploadedFile::fake()->image('foto-aktif.jpg')->store('profil-bumdes', 'public');
        $profil->update(['foto' => $oldPhoto]);

        $this->actingAs($admin)
            ->from(route('admin.profil-bumdes'))
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'foto' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHasErrors('foto');

        $this->assertSame($oldPhoto, $profil->fresh()->foto);
        Storage::disk('public')->assertExists($oldPhoto);
    }

    public function test_admin_can_update_profile_content_and_manage_unit_businesses(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create([
            'nama_bumdes' => 'Nama lama',
            'deskripsi' => 'Deskripsi lama',
        ]);
        $existingUnit = $profil->unitBumdes()->create([
            'nama_unit' => 'Unit yang diperbarui',
            'jenis' => 'Pertanian',
            'deskripsi' => 'Deskripsi awal',
            'status' => 'aktif',
        ]);
        $removedUnit = $profil->unitBumdes()->create([
            'nama_unit' => 'Unit yang dihapus',
            'jenis' => 'Layanan',
            'gambar' => UploadedFile::fake()->image('unit-lama.jpg')->store('unit-bumdes', 'public'),
            'status' => 'nonaktif',
        ]);
        $removedImage = $removedUnit->gambar;

        $this->actingAs($admin)
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Makmur',
                'deskripsi' => 'Deskripsi terbaru BUMDes.',
                'sejarah' => 'Sejarah yang tersimpan.',
                'visi' => 'Visi yang terbaru.',
                'misi' => "Misi satu.\nMisi dua.",
                'alamat' => 'Jalan Desa Sedati Agung',
                'telepon' => '81234567890',
                'email' => 'bumdes@example.test',
                'legalitas' => [
                    'perdes_nomor' => 'Perdes Nomor 4 Tahun 2025',
                    'perdes_keterangan' => 'Pendirian BUMDes',
                    'ahu_nomor' => 'AHU-12345',
                    'ahu_keterangan' => 'Terdaftar resmi',
                    'tanggal_berdiri' => '2025-01-15',
                    'nib' => '1234567890123',
                ],
                'pengurus' => [
                    ['nama' => 'Nama Penasihat', 'jabatan' => 'Penasihat', 'deskripsi' => 'Memberi arahan.'],
                    ['nama' => 'Nama Direktur', 'jabatan' => 'Direktur', 'deskripsi' => 'Memimpin BUMDes.'],
                    ['nama' => 'Nama Pengawas', 'jabatan' => 'Pengawas Unit', 'deskripsi' => 'Memantau unit usaha.'],
                    ['nama' => 'Nama Pengelola Kios', 'jabatan' => 'Pengelola Kios', 'deskripsi' => 'Mengelola kios.'],
                    ['nama' => 'Nama Bendahara', 'jabatan' => 'Bendahara', 'deskripsi' => 'Mengelola keuangan.'],
                ],
                'unit' => [
                    [
                        'id' => $existingUnit->id,
                        'nama_unit' => 'Unit Pertanian',
                        'jenis' => 'Pertanian',
                        'deskripsi' => 'Deskripsi terbaru.',
                        'harga' => '25000',
                        'telepon' => '0812-3456-7890',
                        'status' => 'aktif',
                    ],
                    [
                        'nama_unit' => 'Unit Baru',
                        'jenis' => 'Jasa',
                        'deskripsi' => 'Unit baru untuk warga.',
                        'telepon' => '+62 812-3456-7890',
                        'status' => 'aktif',
                        'gambar' => UploadedFile::fake()->image('unit-baru.jpg'),
                    ],
                    [
                        'id' => $removedUnit->id,
                        'hapus' => '1',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('profil_bumdes', [
            'id' => $profil->id,
            'nama_bumdes' => 'BUMDes Makmur',
            'deskripsi' => 'Deskripsi terbaru BUMDes.',
            'visi' => 'Visi yang terbaru.',
            'misi' => "Misi satu.\nMisi dua.",
            'alamat' => 'Jalan Desa Sedati Agung',
            'email' => 'bumdes@example.test',
        ]);
        $this->assertSame('Perdes Nomor 4 Tahun 2025', $profil->fresh()->legalitas['perdes_nomor']);
        $this->assertSame('1234567890123', $profil->fresh()->legalitas['nib']);
        $this->assertSame('Nama Direktur', $profil->fresh()->pengurus[1]['nama']);
        $this->assertDatabaseHas('unit_bumdes', [
            'id' => $existingUnit->id,
            'nama_unit' => 'Unit Pertanian',
            'harga' => 25000,
            'telepon' => '0812-3456-7890',
        ]);
        $this->assertDatabaseHas('unit_bumdes', ['nama_unit' => 'Unit Baru']);
        $this->assertDatabaseMissing('unit_bumdes', ['id' => $removedUnit->id]);
        Storage::disk('public')->assertMissing($removedImage);

        $newUnit = $profil->unitBumdes()->where('nama_unit', 'Unit Baru')->firstOrFail();
        Storage::disk('public')->assertExists($newUnit->gambar);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee('BUMDes Makmur')
            ->assertSee('Perdes Nomor 4 Tahun 2025')
            ->assertSee('AHU-12345')
            ->assertSee('Nama Direktur')
            ->assertSee('Unit Pertanian')
            ->assertSee('Unit Baru')
            ->assertSee('tel:+6281234567890', false)
            ->assertSee('Hubungi +62 812-3456-7890')
            ->assertDontSee('Unit yang dihapus');
    }

    public function test_unit_ids_from_another_bumdes_cannot_be_changed(): void
    {
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Pertama']);
        $profilLain = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Lain']);
        $unitLain = $profilLain->unitBumdes()->create([
            'nama_unit' => 'Unit milik profil lain',
            'jenis' => 'Jasa',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.profil-bumdes'))
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Pertama',
                'unit' => [
                    [
                        'id' => $unitLain->id,
                        'nama_unit' => 'Unit diubah',
                        'jenis' => 'Jasa',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHasErrors('unit');

        $this->assertSame('Unit milik profil lain', $unitLain->fresh()->nama_unit);
    }

    public function test_manager_cards_can_be_added_removed_and_saved_without_a_fixed_count(): void
    {
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Sedati Agung']);

        $this->actingAs($admin)
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'pengurus' => [
                    ['nama' => 'Ibu Siti', 'jabatan' => 'Direktur', 'deskripsi' => 'Mengelola BUMDes.'],
                    ['nama' => '', 'jabatan' => '', 'deskripsi' => ''],
                    ['nama' => 'Bapak Joko', 'jabatan' => 'Pengawas', 'deskripsi' => 'Memantau kegiatan.'],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHas('status');

        $this->assertSame([
            ['nama' => 'Ibu Siti', 'jabatan' => 'Direktur', 'deskripsi' => 'Mengelola BUMDes.', 'foto' => null],
            ['nama' => 'Bapak Joko', 'jabatan' => 'Pengawas', 'deskripsi' => 'Memantau kegiatan.', 'foto' => null],
        ], $profil->fresh()->pengurus);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee('Ibu Siti')
            ->assertSee('Bapak Joko')
            ->assertSee('Memantau kegiatan.');
    }

    public function test_admin_can_upload_and_replace_manager_photos_and_removed_photos_are_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $oldPhoto = UploadedFile::fake()->image('foto-lama.jpg')->store('pengurus-bumdes', 'public');
        $profil = ProfilBumdes::create([
            'nama_bumdes' => 'BUMDes Sedati Agung',
            'pengurus' => [
                ['nama' => 'Ibu Siti', 'jabatan' => 'Direktur', 'deskripsi' => '', 'foto' => $oldPhoto],
            ],
        ]);

        $this->actingAs($admin)
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'pengurus' => [
                    [
                        'nama' => 'Ibu Siti',
                        'jabatan' => 'Direktur',
                        'deskripsi' => 'Mengelola BUMDes.',
                        'foto_tersimpan' => $oldPhoto,
                        'foto' => UploadedFile::fake()->image('foto-baru.png'),
                    ],
                    [
                        'nama' => 'Bapak Joko',
                        'jabatan' => 'Pengawas',
                        'deskripsi' => 'Mengawasi unit usaha.',
                        'foto' => UploadedFile::fake()->image('foto-pengawas.jpg'),
                    ],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHas('status');

        $pengurus = $profil->fresh()->pengurus;
        $this->assertCount(2, $pengurus);
        $this->assertNotSame($oldPhoto, $pengurus[0]['foto']);
        Storage::disk('public')->assertMissing($oldPhoto);
        Storage::disk('public')->assertExists($pengurus[0]['foto']);
        Storage::disk('public')->assertExists($pengurus[1]['foto']);

        $this->get(route('profil-bumdes'))
            ->assertOk()
            ->assertSee(Storage::url($pengurus[0]['foto']), false)
            ->assertSee(Storage::url($pengurus[1]['foto']), false);

        $this->actingAs($admin)
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'pengurus' => [
                    [
                        'nama' => 'Ibu Siti',
                        'jabatan' => 'Direktur',
                        'deskripsi' => 'Mengelola BUMDes.',
                        'foto_tersimpan' => $pengurus[0]['foto'],
                    ],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHas('status');

        Storage::disk('public')->assertExists($pengurus[0]['foto']);
        Storage::disk('public')->assertMissing($pengurus[1]['foto']);
    }

    public function test_invalid_unit_contact_number_is_rejected(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->from(route('admin.profil-bumdes'))
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'unit' => [
                    [
                        'nama_unit' => 'Unit Pertanian',
                        'jenis' => 'Pertanian',
                        'telepon' => 'telepon bukan nomor',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHasErrors('unit.0.telepon');
    }

    public function test_manager_card_requires_name_and_position_together(): void
    {
        $admin = User::factory()->create();
        $profil = ProfilBumdes::create(['nama_bumdes' => 'BUMDes Sedati Agung']);

        $this->actingAs($admin)
            ->from(route('admin.profil-bumdes'))
            ->put(route('admin.profil-bumdes.update'), [
                'nama_bumdes' => 'BUMDes Sedati Agung',
                'pengurus' => [
                    ['nama' => 'Nama saja', 'jabatan' => '', 'deskripsi' => ''],
                ],
            ])
            ->assertRedirect(route('admin.profil-bumdes'))
            ->assertSessionHasErrors('pengurus');

        $this->assertNull($profil->fresh()->pengurus);
    }
}
