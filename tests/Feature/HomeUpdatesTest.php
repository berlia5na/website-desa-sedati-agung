<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\StatistikDesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_three_latest_published_news_and_the_nearest_upcoming_agenda(): void
    {
        $user = User::factory()->create();
        StatistikDesa::query()->firstOrCreate([], ['jumlah_usaha_bumdes' => 4]);

        foreach (range(1, 4) as $number) {
            Berita::create([
                'user_id' => $user->id,
                'judul' => "Berita {$number}",
                'slug' => "berita-{$number}",
                'isi' => "Isi berita {$number}",
                'status' => 'published',
            ]);

            Berita::query()
                ->where('slug', "berita-{$number}")
                ->update(['created_at' => now()->subDays(5 - $number)]);
        }

        Berita::create([
            'user_id' => $user->id,
            'judul' => 'Berita Draft',
            'slug' => 'berita-draft',
            'isi' => 'Berita yang belum dipublikasikan',
            'status' => 'draft',
        ]);

        Agenda::create([
            'user_id' => $user->id,
            'judul' => 'Agenda Terdekat',
            'tanggal' => today()->addDays(2),
            'waktu_mulai' => '09:00:00',
            'lokasi' => 'Balai Desa',
            'status' => 'published',
        ]);
        Agenda::create([
            'user_id' => $user->id,
            'judul' => 'Agenda Berikutnya',
            'tanggal' => today()->addDays(3),
            'status' => 'published',
        ]);
        Agenda::create([
            'user_id' => $user->id,
            'judul' => 'Agenda Lampau',
            'tanggal' => today()->subDay(),
            'status' => 'published',
        ]);
        Agenda::create([
            'user_id' => $user->id,
            'judul' => 'Agenda Draft',
            'tanggal' => today()->addDay(),
            'status' => 'draft',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Berita Terkini')
            ->assertSee('Berita 4')
            ->assertSee('Berita 3')
            ->assertSee('Berita 2')
            ->assertDontSee('Berita 1')
            ->assertDontSee('Berita Draft')
            ->assertSee('Agenda Terdekat')
            ->assertSee('09:00')
            ->assertSee('Balai Desa')
            ->assertDontSee('Agenda Berikutnya')
            ->assertDontSee('Agenda Lampau')
            ->assertDontSee('Agenda Draft')
            ->assertSee(route('berita'))
            ->assertSee(route('agenda'));
    }

    public function test_home_page_shows_empty_states_when_no_news_or_upcoming_agenda_exist(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Belum ada berita terbaru')
            ->assertSee('Belum ada agenda mendatang');
    }
}
