<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_available_and_authenticated_admin_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create();

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Masuk ke Sistem')
            ->assertSee('Khusus Perangkat Kantor');

        $this->actingAs($admin)
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_log_in_with_username_and_remember_me(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin_desa',
            'password' => 'secure-password',
        ]);

        $this->post(route('admin.login.submit'), [
            'username' => 'admin_desa',
            'password' => 'secure-password',
            'remember' => '1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->remember_token);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_invalid_password_and_email_login_are_rejected(): void
    {
        User::factory()->create([
            'username' => 'admin_desa',
            'password' => 'secure-password',
        ]);

        $this->post(route('admin.login.submit'), [
            'username' => 'admin_desa',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.test',
            'password' => 'secure-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_login_without_remember_me_does_not_create_remember_token(): void
    {
        $admin = User::factory()->create([
            'password' => 'secure-password',
        ]);

        $this->post(route('admin.login.submit'), [
            'username' => $admin->username,
            'password' => 'secure-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNull($admin->fresh()->remember_token);
    }

    public function test_non_admin_user_cannot_log_in_or_open_admin_pages(): void
    {
        $user = User::factory()->create([
            'role' => 'editor',
            'password' => 'secure-password',
        ]);

        $this->post(route('admin.login.submit'), [
            'username' => $user->username,
            'password' => 'secure-password',
        ])->assertSessionHasErrors('username');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_logout_invalidates_admin_session_and_public_pages_stay_public(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $this->from(route('admin.dashboard'))
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('home'))->assertOk();
    }

    public function test_login_throttles_repeated_invalid_attempts(): void
    {
        User::factory()->create([
            'username' => 'admin_desa',
            'password' => 'secure-password',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.submit'), [
                'username' => 'admin_desa',
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('admin.login.submit'), [
            'username' => 'admin_desa',
            'password' => 'secure-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_user_seeder_creates_or_updates_credentials_from_its_values(): void
    {
        $existingAdmin = User::factory()->create([
            'username' => 'old_admin',
            'password' => 'old-password',
        ]);

        $seeder = new class extends UserSeeder
        {
            protected function adminUsername(): ?string
            {
                return 'admin_baru';
            }

            protected function adminPassword(): ?string
            {
                return 'new-secure-password';
            }
        };
        $seeder->run();

        $updatedAdmin = $existingAdmin->fresh();
        $this->assertSame('admin_baru', $updatedAdmin->username);
        $this->assertTrue(Hash::check('new-secure-password', $updatedAdmin->password));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_seeder_rejects_unchanged_password_placeholder(): void
    {
        $seeder = new class extends UserSeeder
        {
            protected function adminUsername(): ?string
            {
                return 'admin_desa';
            }

            protected function adminPassword(): ?string
            {
                return 'GANTI_PASSWORD_INI_SEBELUM_SEEDING';
            }
        };

        try {
            $seeder->run();
            $this->fail('User seeder must reject the example password.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('UserSeeder', $exception->getMessage());
        }
    }

    public function test_default_database_seeder_runs_user_seeder(): void
    {
        putenv('ADMIN_USERNAME=admin_uji');
        putenv('ADMIN_PASSWORD=password-uji-yang-aman');
        try {
            $this->seed();
        } finally {
            putenv('ADMIN_USERNAME');
            putenv('ADMIN_PASSWORD');
        }

        $this->assertDatabaseHas('users', [
            'name' => 'Admin Desa',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('profil_desa', [
            'nama_desa' => 'Desa Sedati Agung',
        ]);
    }
}
