<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_publish_an_agenda_to_the_public_page(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.agenda.store'), $this->agendaData())
            ->assertRedirect(route('admin.agenda'))
            ->assertSessionHas('status', 'Agenda berhasil dipublikasikan di halaman Agenda Desa.');

        $agenda = Agenda::query()->firstOrFail();
        $this->assertSame($admin->id, $agenda->user_id);
        $this->assertSame('published', $agenda->status);

        $this->get(route('agenda'))
            ->assertOk()
            ->assertSee($agenda->judul)
            ->assertSee('Balai Desa Sedati Agung');
    }

    public function test_admin_can_save_a_draft_and_drafts_are_hidden_from_public(): void
    {
        $admin = User::factory()->create();
        $data = [...$this->agendaData(), 'status' => 'draft'];

        $this->actingAs($admin)
            ->post(route('admin.agenda.store'), $data)
            ->assertRedirect(route('admin.agenda'))
            ->assertSessionHas('status', 'Agenda berhasil disimpan sebagai draft.');

        $draft = Agenda::query()->firstOrFail();
        $this->assertSame('draft', $draft->status);

        $this->get(route('agenda'))
            ->assertOk()
            ->assertDontSee($draft->judul);
    }

    public function test_public_agenda_shows_only_upcoming_published_events_and_spotlights_the_nearest(): void
    {
        $admin = User::factory()->create();
        $nearest = Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Agenda Terdekat',
            'tanggal' => today()->addDays(2)->format('Y-m-d'),
            'user_id' => $admin->id,
        ]);
        Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Agenda Lebih Jauh',
            'tanggal' => today()->addDays(8)->format('Y-m-d'),
            'user_id' => $admin->id,
        ]);
        Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Agenda Sudah Lewat',
            'tanggal' => today()->subDays(2)->format('Y-m-d'),
            'user_id' => $admin->id,
        ]);
        Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Agenda Draft',
            'status' => 'draft',
            'user_id' => $admin->id,
        ]);

        $this->get(route('agenda'))
            ->assertOk()
            ->assertSee('Agenda Terdekat')
            ->assertSee('Agenda Lebih Jauh')
            ->assertDontSee('Agenda Sudah Lewat')
            ->assertDontSee('Agenda Draft')
            ->assertSee(route('agenda.detail', $nearest));
    }

    public function test_public_agenda_search_matches_title_location_and_resets_pagination_results(): void
    {
        $admin = User::factory()->create();
        $agenda = Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Posyandu Balita',
            'lokasi' => 'Balai RW Melati',
            'user_id' => $admin->id,
        ]);

        $this->get(route('agenda', ['q' => 'Balai RW']))
            ->assertOk()
            ->assertSee($agenda->judul)
            ->assertSee('Balai RW Melati');

        $this->get(route('agenda', ['q' => 'kegiatan-tidak-ada']))
            ->assertOk()
            ->assertSee('Agenda tidak ditemukan')
            ->assertDontSee('href="'.route('agenda.detail', $agenda).'">'.$agenda->judul.'</a>', false);
    }

    public function test_public_agenda_detail_is_available_only_for_published_agendas(): void
    {
        $admin = User::factory()->create();
        $published = Agenda::query()->create([
            ...$this->agendaData(),
            'user_id' => $admin->id,
        ]);
        $draft = Agenda::query()->create([
            ...$this->agendaData(),
            'judul' => 'Agenda Internal Draft',
            'status' => 'draft',
            'user_id' => $admin->id,
        ]);

        $this->get(route('agenda.detail', $published))
            ->assertOk()
            ->assertSee($published->judul)
            ->assertSee('Balai Desa Sedati Agung');

        $this->get(route('agenda.detail', $draft))
            ->assertNotFound();
    }

    public function test_admin_can_search_edit_update_and_delete_agendas(): void
    {
        $admin = User::factory()->create();
        $agenda = Agenda::query()->create([
            ...$this->agendaData(),
            'user_id' => $admin->id,
        ]);
        $this->actingAs($admin);

        $this->get(route('admin.agenda', ['q' => 'Siti PIC']))
            ->assertOk()
            ->assertSee($agenda->judul);

        $this->get(route('admin.agenda.edit', $agenda))
            ->assertOk()
            ->assertSee('value="'.$agenda->judul.'"', false);

        $this->put(route('admin.agenda.update', $agenda), [
            ...$this->agendaData(),
            'judul' => 'Musyawarah Desa Terbaru',
            'status' => 'draft',
        ])->assertRedirect(route('admin.agenda'));

        $this->assertDatabaseHas('agenda', [
            'id' => $agenda->id,
            'judul' => 'Musyawarah Desa Terbaru',
            'status' => 'draft',
        ]);

        $this->delete(route('admin.agenda.delete', $agenda))
            ->assertRedirect(route('admin.agenda'))
            ->assertSessionHas('status', 'Agenda berhasil dihapus.');

        $this->assertDatabaseMissing('agenda', ['id' => $agenda->id]);
    }

    public function test_admin_agenda_list_shows_six_cards_per_page(): void
    {
        $admin = User::factory()->create();

        foreach (range(1, 7) as $number) {
            Agenda::query()->create([
                ...$this->agendaData(),
                'judul' => "Agenda Uji {$number}",
                'user_id' => $admin->id,
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.agenda'))
            ->assertOk()
            ->assertSee('Agenda Uji 2')
            ->assertSee('Agenda Uji 7')
            ->assertDontSee('Agenda Uji 1');

        $this->assertSame(6, substr_count($response->getContent(), '<article class="agenda-card">'));
    }

    public function test_agenda_form_validates_required_fields_and_status(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.agenda.store'), ['judul' => 'Agenda tanpa detail'])
            ->assertSessionHasErrors([
                'badge_kategori',
                'deskripsi',
                'tanggal',
                'waktu_mulai',
                'kategori',
                'jenis_kegiatan',
                'jenis_undangan',
                'lokasi',
                'penyelenggara',
                'status',
            ]);

        $this->assertDatabaseCount('agenda', 0);
    }

    private function agendaData(): array
    {
        return [
            'badge_kategori' => 'Agenda Resmi Terverifikasi',
            'judul' => 'Musyawarah Desa',
            'deskripsi' => 'Musyawarah membahas rencana kegiatan desa.',
            'tanggal' => today()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'kategori' => 'Pemerintahan',
            'jenis_kegiatan' => 'Rapat / Musyawarah',
            'jenis_undangan' => 'Terbuka untuk umum',
            'lokasi' => 'Balai Desa Sedati Agung',
            'penyelenggara' => 'Pemerintah Desa',
            'pic' => 'Siti PIC',
            'status' => 'published',
        ];
    }
}
