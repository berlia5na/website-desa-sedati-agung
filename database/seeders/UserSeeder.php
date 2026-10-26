<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    protected const PASSWORD_PLACEHOLDER = 'GANTI_PASSWORD_INI_SEBELUM_SEEDING';

    protected function adminUsername(): ?string
    {
        return env('ADMIN_USERNAME');
    }

    protected function adminPassword(): ?string
    {
        return env('ADMIN_PASSWORD');
    }

    public function run(): void
    {
        $username = $this->adminUsername();
        $password = $this->adminPassword();

        if (blank($username) || blank($password) || $password === static::PASSWORD_PLACEHOLDER) {
            throw new RuntimeException('UserSeeder requires ADMIN_USERNAME and ADMIN_PASSWORD to be set in .env.');
        }

        $admin = User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->first() ?? new User;

        $admin->fill([
            'name' => 'Admin Desa',
            'username' => $username,
            'password' => $password,
            'role' => 'admin',
        ])->save();
    }
}
