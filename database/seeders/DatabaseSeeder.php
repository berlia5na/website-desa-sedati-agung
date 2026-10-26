<?php

namespace Database\Seeders;

use App\Models\ProfilDesa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ProfilDesa::firstOrCreate([], [
            'nama_desa' => 'Desa Sedati Agung',
        ]);

        $this->call(UserSeeder::class);
    }
}
