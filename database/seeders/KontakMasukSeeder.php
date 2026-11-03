<?php

namespace Database\Seeders;

use App\Models\KontakMasuk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KontakMasukSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            ['Wulan Puspitasari', 'wulan@example.test', '081234567801', 'Informasi layanan desa', 'Mohon informasi jadwal pelayanan surat keterangan domisili dan berkas yang perlu dibawa.', 'baru', 1],
            ['Budi Santoso', 'budi@example.test', '081234567802', 'Administrasi kependudukan', 'Apakah pengajuan surat pengantar pindah bisa diwakilkan oleh anggota keluarga?', 'baru', 2],
            ['Rina Kartika', 'rina@example.test', '081234567803', 'Pengaduan atau aspirasi', 'Lampu penerangan di jalan menuju balai RW 03 sudah padam beberapa hari. Mohon dapat diperiksa.', 'dibaca', 3],
            ['Ahmad Fauzi', 'ahmad@example.test', null, 'Informasi layanan desa', 'Saya ingin mengetahui jadwal pelayanan kantor desa selama bulan ini.', 'dibalas', 4],
            ['Siti Aminah', 'siti@example.test', '081234567805', 'Kerja sama dan lainnya', 'Kami dari kelompok PKK ingin mengajukan jadwal penggunaan balai desa untuk pelatihan warga.', 'baru', 5],
            ['Dimas Pratama', 'dimas@example.test', '081234567806', 'Administrasi kependudukan', 'Mohon petunjuk untuk mengurus surat keterangan usaha bagi usaha rumahan.', 'arsip', 6],
            ['Nur Aisyah', 'aisyah@example.test', '081234567807', 'Informasi layanan desa', 'Apakah kantor desa melayani legalisasi dokumen pada hari Jumat?', 'dibaca', 7],
            ['Hendra Wijaya', 'hendra@example.test', '081234567808', 'Pengaduan atau aspirasi', 'Saluran drainase di sekitar RT 05 tersumbat setelah hujan deras kemarin.', 'baru', 8],
            ['Dewi Lestari', 'dewi@example.test', '081234567809', 'Kerja sama dan lainnya', 'Kami bermaksud mengundang perwakilan desa untuk kegiatan sosialisasi kesehatan lingkungan.', 'dibalas', 9],
            ['Rizky Maulana', 'rizky@example.test', '081234567810', 'Administrasi kependudukan', 'Apakah ada formulir yang perlu diisi sebelum mengajukan surat keterangan tidak mampu?', 'baru', 10],
            ['Lina Marlina', 'lina@example.test', '081234567811', 'Informasi layanan desa', 'Saya ingin menyampaikan perubahan data nomor telepon pada data keluarga.', 'dibaca', 11],
            ['Agus Setiawan', 'agus@example.test', '081234567812', 'Pengaduan atau aspirasi', 'Mohon informasi tindak lanjut usulan perbaikan jalan lingkungan yang disampaikan bulan lalu.', 'baru', 12],
        ];

        foreach ($examples as [$nama, $email, $telepon, $subjek, $pesan, $status, $daysAgo]) {
            $createdAt = Carbon::now()->subDays($daysAgo);

            KontakMasuk::query()->firstOrCreate(
                ['email' => $email, 'subjek' => $subjek],
                [
                    'nama' => $nama,
                    'telepon' => $telepon,
                    'pesan' => $pesan,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }
    }
}
