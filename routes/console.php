<?php

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:reset-login', function () {
    $username = trim((string) $this->ask('Nama pengguna admin'));

    if ($username === '' || mb_strlen($username) > 255) {
        $this->error('Nama pengguna wajib diisi dan maksimal 255 karakter.');

        return Command::FAILURE;
    }

    $admin = User::query()->where('role', 'admin')->orderBy('id')->first();
    $usernameInUse = User::query()
        ->where('username', $username)
        ->when($admin, fn ($query) => $query->whereKeyNot($admin->getKey()))
        ->exists();

    if ($usernameInUse) {
        $this->error('Nama pengguna tersebut sudah digunakan akun lain.');

        return Command::FAILURE;
    }

    $password = $this->secret('Kata sandi baru (minimal 12 karakter)');

    if (! is_string($password) || mb_strlen($password) < 12) {
        $this->error('Kata sandi harus memiliki minimal 12 karakter.');

        return Command::FAILURE;
    }

    $passwordConfirmation = $this->secret('Ulangi kata sandi baru');

    if (! is_string($passwordConfirmation) || ! hash_equals($password, $passwordConfirmation)) {
        $this->error('Konfirmasi kata sandi tidak cocok.');

        return self::FAILURE;
    }

    DB::transaction(function () use ($admin, $username, $password): void {
        $admin ??= new User;
        $admin->fill([
            'name' => $admin->name ?: 'Admin Desa',
            'username' => $username,
            'password' => $password,
            'role' => 'admin',
        ])->save();
    });

    $this->info('Kredensial admin berhasil diperbarui. Silakan masuk menggunakan nama pengguna baru.');

    return Command::SUCCESS;
})->purpose('Atur nama pengguna dan kata sandi admin dengan prompt aman');
