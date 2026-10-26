<?php

namespace Tests\Feature;

use App\Models\StatistikDesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_the_desa_statistics(): void
    {
        StatistikDesa::query()->first()->update([
            'jumlah_penduduk' => 1234,
            'jumlah_usaha_bumdes' => 4,
            'jumlah_umkm' => 56,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Jumlah Penduduk')
            ->assertSee('1.234')
            ->assertSee('Usaha BUMDes')
            ->assertSee('Jumlah UMKM')
            ->assertSee('56');
    }

    public function test_admin_can_update_the_statistics_shown_on_the_home_page(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Jumlah penduduk')
            ->assertSee('Jumlah usaha BUMDes')
            ->assertSee('Jumlah UMKM');

        $this->put(route('admin.beranda.statistik.update'), [
            'jumlah_penduduk' => 4321,
            'jumlah_usaha_bumdes' => 5,
            'jumlah_umkm' => 67,
        ])->assertRedirect(route('admin.beranda'));

        $this->assertDatabaseHas('statistik_desa', [
            'jumlah_penduduk' => 4321,
            'jumlah_usaha_bumdes' => 5,
            'jumlah_umkm' => 67,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('4.321')
            ->assertSee('5')
            ->assertSee('67');
    }

    public function test_guests_cannot_update_statistics_and_invalid_values_are_rejected(): void
    {
        $this->put(route('admin.beranda.statistik.update'), [
            'jumlah_penduduk' => 10,
            'jumlah_usaha_bumdes' => 4,
            'jumlah_umkm' => 10,
        ])->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())
            ->put(route('admin.beranda.statistik.update'), [
                'jumlah_penduduk' => -1,
                'jumlah_usaha_bumdes' => 'banyak',
                'jumlah_umkm' => -2,
            ])
            ->assertSessionHasErrors([
                'jumlah_penduduk',
                'jumlah_usaha_bumdes',
                'jumlah_umkm',
            ]);
    }
}
