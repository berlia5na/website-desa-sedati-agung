<?php

namespace Tests\Feature;

use App\Models\KontakMasuk;
use App\Models\User;
use Database\Seeders\KontakMasukSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_form_stores_a_new_message_for_the_admin_inbox(): void
    {
        $this->post(route('kontak.store'), $this->contactData())
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('contact_status');

        $this->assertDatabaseHas('kontak_masuk', [
            'nama' => 'Nabila Warga',
            'email' => 'nabila@example.test',
            'telepon' => '081234567890',
            'subjek' => 'Informasi layanan desa',
            'status' => 'baru',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.kontak'))
            ->assertOk()
            ->assertSee('Nabila Warga')
            ->assertSee('Belum Dibaca');
    }

    public function test_honeypot_submission_is_acknowledged_without_storing_a_message(): void
    {
        $this->post(route('kontak.store'), [...$this->contactData(), 'website' => 'https://spam.example'])
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('contact_status');

        $this->assertDatabaseCount('kontak_masuk', 0);
    }

    public function test_public_contact_form_validates_fields_and_message_length(): void
    {
        $this->from(route('kontak'))
            ->post(route('kontak.store'), [
                ...$this->contactData(),
                'email' => 'bukan-email',
                'pesan' => 'Pendek',
            ])
            ->assertRedirect(route('kontak'))
            ->assertSessionHasErrors(['email', 'pesan']);

        $this->assertDatabaseCount('kontak_masuk', 0);
    }

    public function test_public_contact_form_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('kontak.store'), [
                ...$this->contactData(),
                'email' => "nabila{$attempt}@example.test",
            ])->assertRedirect(route('kontak'));
        }

        $this->post(route('kontak.store'), $this->contactData())->assertTooManyRequests();
        $this->assertDatabaseCount('kontak_masuk', 3);
    }

    public function test_inbox_is_restricted_to_authenticated_admins(): void
    {
        $this->get(route('admin.kontak'))->assertRedirect(route('admin.login'));

        $editor = User::factory()->create(['role' => 'editor']);
        $this->actingAs($editor)->get(route('admin.kontak'))->assertForbidden();
    }

    public function test_admin_can_search_filter_and_paginate_messages(): void
    {
        $admin = User::factory()->create();
        $matched = $this->createMessage(['nama' => 'Wulan Puspitasari', 'status' => 'baru']);
        $this->createMessage(['nama' => 'Budi Santoso', 'status' => 'dibaca']);
        for ($index = 0; $index < 10; $index++) {
            $this->createMessage(['nama' => "Pesan Tambahan {$index}", 'status' => 'arsip']);
        }

        $this->actingAs($admin)
            ->get(route('admin.kontak', ['q' => 'Wulan', 'status' => 'baru']))
            ->assertOk()
            ->assertSee('Wulan Puspitasari')
            ->assertDontSee('Budi Santoso')
            ->assertSee('Menampilkan 1-1 dari 1 pesan');

        $this->get(route('admin.kontak', ['status' => 'arsip']))
            ->assertOk()
            ->assertSee('Pesan Tambahan 0')
            ->assertSee('Menampilkan 1-10 dari 10 pesan');

        $this->assertSame('baru', $matched->fresh()->status);
    }

    public function test_opening_a_message_marks_it_read_and_escapes_its_contents(): void
    {
        $admin = User::factory()->create();
        $message = $this->createMessage([
            'nama' => '<script>alert(1)</script>',
            'pesan' => '<script>alert(1)</script>',
            'status' => 'baru',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kontak.open', $message))
            ->assertRedirect(route('admin.kontak', ['pesan' => $message->id]))
            ->assertSessionHas('status');

        $this->assertSame('dibaca', $message->fresh()->status);

        $this->get(route('admin.kontak', ['pesan' => $message->id]))
            ->assertOk()
            ->assertSee('data-message-dialog data-open="true"', false)
            ->assertSee('Detail pesan', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_admin_can_save_internal_notes_change_status_and_delete_a_message(): void
    {
        $admin = User::factory()->create();
        $message = $this->createMessage();

        $this->actingAs($admin)
            ->put(route('admin.kontak.note', $message), ['catatan_admin' => 'Perlu ditindaklanjuti.'])
            ->assertRedirect(route('admin.kontak', ['pesan' => $message->id]));
        $this->assertSame('Perlu ditindaklanjuti.', $message->fresh()->catatan_admin);

        $this->post(route('admin.kontak.status', $message), ['status' => 'dibalas'])
            ->assertRedirect(route('admin.kontak'));
        $this->assertSame('dibalas', $message->fresh()->status);

        $this->delete(route('admin.kontak.delete', $message))
            ->assertRedirect(route('admin.kontak'));
        $this->assertDatabaseMissing('kontak_masuk', ['id' => $message->id]);
    }

    public function test_admin_can_apply_bulk_actions_and_delete_selected_messages(): void
    {
        $admin = User::factory()->create();
        $first = $this->createMessage(['email' => 'first@example.test']);
        $second = $this->createMessage(['email' => 'second@example.test']);

        $this->actingAs($admin)
            ->post(route('admin.kontak.bulk'), ['aksi' => 'baca', 'ids' => [$first->id, $second->id]])
            ->assertRedirect(route('admin.kontak'));
        $this->assertSame('dibaca', $first->fresh()->status);
        $this->assertSame('dibaca', $second->fresh()->status);

        $this->delete(route('admin.kontak.bulk-delete'), ['ids' => [$first->id, $second->id]])
            ->assertRedirect(route('admin.kontak'));
        $this->assertDatabaseCount('kontak_masuk', 0);
    }

    public function test_contact_seeder_adds_at_least_twelve_example_messages_idempotently(): void
    {
        $seeder = new KontakMasukSeeder;
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('kontak_masuk', 12);
        $this->assertDatabaseHas('kontak_masuk', ['status' => 'baru']);
        $this->assertDatabaseHas('kontak_masuk', ['status' => 'dibaca']);
        $this->assertDatabaseHas('kontak_masuk', ['status' => 'dibalas']);
        $this->assertDatabaseHas('kontak_masuk', ['status' => 'arsip']);
    }

    private function contactData(): array
    {
        return [
            'nama' => 'Nabila Warga',
            'email' => 'nabila@example.test',
            'telepon' => '081234567890',
            'subjek' => 'Informasi layanan desa',
            'pesan' => 'Mohon informasi persyaratan layanan surat pengantar.',
        ];
    }

    private function createMessage(array $overrides = []): KontakMasuk
    {
        static $counter = 0;
        $counter++;

        return KontakMasuk::query()->create([
            'nama' => 'Pengirim Contoh',
            'email' => "pengirim{$counter}@example.test",
            'telepon' => '081234567890',
            'subjek' => 'Informasi layanan desa',
            'pesan' => 'Mohon informasi lebih lanjut mengenai layanan desa.',
            'status' => 'baru',
            ...$overrides,
        ]);
    }
}
