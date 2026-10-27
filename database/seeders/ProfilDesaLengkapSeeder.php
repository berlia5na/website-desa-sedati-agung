<?php

namespace Database\Seeders;

use App\Models\PerangkatDesa;
use App\Models\ProfilDesa;
use Illuminate\Database\Seeder;

class ProfilDesaLengkapSeeder extends Seeder
{
    public function run(): void
    {
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

        // Pakai baris profil_desa yang sudah ada; kalau belum ada, buat baru.
        // forceFill dipakai supaya $fillable di model Anda tidak perlu diubah.
        $profil = ProfilDesa::first() ?? new ProfilDesa();

        $profil->forceFill([
            'nama_desa' => 'Desa Sedati Agung',
            'subtitle' => 'Kecamatan Sedati, Kabupaten Sidoarjo',
            'badge' => 'Pemerintah Desa Terbuka & Terpercaya',
            'judul' => 'Profil Resmi Pemerintah Desa Sedati Agung',
            'deskripsi' => 'Mengenal Sedati Agung lebih dekat: sejarah, potensi ekonomi agrobisnis dan pertanian, serta kemajuan yang terus kami wujudkan untuk masyarakat.',
            'kutipan' => 'Mari bersama membangun Desa Sedati Agung yang lebih maju, sejahtera, dan berdaya saing.',
            'nama_kades' => 'Nama Kepala Desa',
            'jabatan_kades' => 'Kepala Desa Sedati Agung 2024 - 2030',
            'caption_hero' => 'Hamparan Tambak dan Sawah Sedati Agung',
            'visi_label' => 'VISI 2024-2030',
            'visi' => 'Terwujudnya Desa Sedati Agung yang Berakhlak, Mandiri Secara Ekonomi, Maju dalam Agrobisnis, dan Tanggap Melayani.',
            'visi_catatan' => 'Dirumuskan bersama BPD dan tokoh masyarakat dalam Musyawarah Desa Perencanaan Desa.',
            // misi disimpan satu poin per baris
            'misi' => implode("\n", [
                'Meningkatkan kehidupan beragama dan akhlak yang berkeseimbangan dalam pembinaan, dan menjunjung kebudayaan lokal.',
                'Memperkuat perekonomian warga melalui pengembangan BUMDes, usaha produksi tani, dan kemudahan akses pasar.',
                'Mewujudkan pembangunan sarana prasarana infrastruktur, penyediaan air bersih, dan layanan kesehatan yang merata.',
                'Menghadirkan layanan publik dengan tata kelola transparan, akuntabel, dan responsif terhadap warga.',
            ]),
            'sejarah' => $lorem . "\n\n" . $lorem,
            'sejarah_sorotan' => 'Wilayah desa ini sejak 1920-an sebagai kawasan agraris dan pesisir yang produktif.',
            'periode_data' => 'Pembaruan Data Terkini Tahun 2024',
            'penduduk' => 6428,
            'luas_wilayah' => 1.42,
            'jumlah_dusun' => 4,
            'lahan_tani' => 68,
            'alamat' => 'Jl. Raya Raden Agung No. 01, Kec. Sedati, Kabupaten Sidoarjo, Jawa Timur 61253',
            'jam_layanan' => 'Senin - Kamis: 08.00 - 15.00 WIB, Jumat: 08.00 - 11.30 WIB, Sabtu: Tutup',
            'telepon' => '+62 812-3456-7890 (Humas Pusat Tutur)',
        ])->save();

        PerangkatDesa::query()->delete();

        $data = [
            ['Nama', 'Kepala Desa', 'Penanggung Jawab Kebijakan & Tata Kelola Desa'],
            ['Nama', 'Sekretaris Desa', 'Koordinator Administrasi & Keuangan Pemerintahan'],
            ['Nama', 'Kaur Keuangan & Kaur Perencanaan', 'Perencanaan & Pengelolaan Anggaran'],
            ['Nama', 'Kaur Pelayanan & Kaur Umum', 'Pelayanan Warga & Pengelolaan Aset Desa'],
        ];

        foreach ($data as $i => [$nama, $jabatan, $deskripsi]) {
            PerangkatDesa::create([
                'nama' => $nama,
                'jabatan' => $jabatan,
                'deskripsi' => $deskripsi,
                'urutan' => $i + 1,
            ]);
        }
    }
}
