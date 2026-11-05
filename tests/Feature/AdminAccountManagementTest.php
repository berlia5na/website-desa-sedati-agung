<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_page_is_restricted_to_authenticated_admins(): void
    {
        $this->get(route('admin.admins'))->assertRedirect(route('admin.login'));

        $editor = User::factory()->create(['role' => 'editor']);
        $this->actingAs($editor)->get(route('admin.admins'))->assertForbidden();

        $admin = $this->createPrimaryAdmin();
        $this->actingAs($admin)
            ->get(route('admin.admins'))
            ->assertOk()
            ->assertSee('Kelola Admin')
            ->assertSee('Kata sandi Anda untuk konfirmasi')
            ->assertSee('Admin utama')
            ->assertSee($admin->username);

        $secondary = User::factory()->create();
        $this->actingAs($secondary)
            ->get(route('admin.admins'))
            ->assertForbidden()
            ->assertDontSee('Kelola Admin');
    }

    public function test_admin_can_create_another_admin_after_confirming_own_password(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);

        $this->actingAs($admin)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Baru',
                'username' => 'admin_baru',
                'password' => 'password-admin-baru',
                'password_confirmation' => 'password-admin-baru',
                'current_password' => 'my-current-password',
            ])
            ->assertRedirect(route('admin.admins'))
            ->assertSessionHas('status');

        $newAdmin = User::query()->where('username', 'admin_baru')->firstOrFail();
        $this->assertSame('admin', $newAdmin->role);
        $this->assertTrue(Hash::check('password-admin-baru', $newAdmin->password));
    }

    public function test_admin_must_confirm_own_password_before_creating_an_account(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);

        $this->actingAs($admin)
            ->from(route('admin.admins'))
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Baru',
                'username' => 'admin_baru',
                'password' => 'password-admin-baru',
                'password_confirmation' => 'password-admin-baru',
                'current_password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_admin_can_change_username_and_password_without_exposing_password_hash(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);
        $account = User::factory()->create([
            'name' => 'Admin Lama',
            'username' => 'admin_lama',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.admins.update', $account), [
                'name' => 'Admin Diperbarui',
                'username' => 'admin_baru',
                'password' => 'another-new-password',
                'password_confirmation' => 'another-new-password',
                'current_password' => 'my-current-password',
            ])
            ->assertRedirect(route('admin.admins'))
            ->assertSessionHas('status');

        $this->assertSame('Admin Diperbarui', $account->fresh()->name);
        $this->assertSame('admin_baru', $account->fresh()->username);
        $this->assertTrue(Hash::check('another-new-password', $account->fresh()->password));
    }

    public function test_admin_cannot_create_account_with_wrong_confirmation_password_or_short_password(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);
        $payload = [
            'name' => 'Admin Baru',
            'username' => 'admin_baru',
            'password' => 'short',
            'password_confirmation' => 'different',
            'current_password' => 'my-current-password',
        ];

        $this->actingAs($admin)
            ->from(route('admin.admins'))
            ->post(route('admin.admins.store'), $payload)
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_admin_can_delete_another_admin_but_not_themselves_or_last_admin(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);

        $this->actingAs($admin)
            ->delete(route('admin.admins.delete', $admin), [
                'current_password' => 'my-current-password',
            ])
            ->assertSessionHasErrors('account');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $otherAdmin = User::factory()->create();
        $this->delete(route('admin.admins.delete', $otherAdmin), [
            'current_password' => 'my-current-password',
        ])->assertRedirect(route('admin.admins'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);
    }

    public function test_admin_accounts_with_authored_content_cannot_be_deleted(): void
    {
        $admin = $this->createPrimaryAdmin(['password' => 'my-current-password']);
        $account = User::factory()->create();
        $now = now();

        DB::table('berita')->insert([
            'user_id' => $account->id,
            'judul' => 'Berita Admin',
            'slug' => 'berita-admin',
            'isi' => 'Isi berita untuk pengujian.',
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.admins.delete', $account), [
                'current_password' => 'my-current-password',
            ])
            ->assertSessionHasErrors('account');

        $this->assertDatabaseHas('users', ['id' => $account->id]);
    }

    public function test_secondary_admin_cannot_create_or_modify_admin_accounts(): void
    {
        $primary = $this->createPrimaryAdmin(['password' => 'primary-password']);
        $secondary = User::factory()->create(['password' => 'secondary-password']);

        $this->actingAs($secondary)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Tidak Sah',
                'username' => 'unauthorized_admin',
                'password' => 'password-admin-baru',
                'password_confirmation' => 'password-admin-baru',
                'current_password' => 'secondary-password',
            ])
            ->assertForbidden();

        $this->put(route('admin.admins.update', $primary), [
            'name' => 'Nama Diubah',
            'username' => 'primary_changed',
            'current_password' => 'secondary-password',
        ])->assertForbidden();

        $this->delete(route('admin.admins.delete', $primary), [
            'current_password' => 'secondary-password',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['username' => 'unauthorized_admin']);
        $this->assertSame($primary->name, $primary->fresh()->name);
        $this->assertSame($primary->username, $primary->fresh()->username);
        $this->assertDatabaseHas('users', ['id' => $primary->id]);
    }

    private function createPrimaryAdmin(array $attributes = []): User
    {
        $admin = User::factory()->create($attributes);
        $admin->is_primary_admin = true;
        $admin->save();

        return $admin;
    }
}
