<?php

namespace App\Http\Controllers;

use App\Models\PerangkatDesa;
use App\Models\ProfilDesa;

class ProfilDesaLengkapController extends Controller
{
    public function index()
    {
        // Ambil profil desa dari database jika tersedia.
        $profil = ProfilDesa::first();

        // Jika belum ada data, siapkan nilai sementara untuk pratinjau.
        if (!$profil) {
            $profil = new ProfilDesa([
                'nama_desa' => 'Desa Sedati Agung',
                'subtitle' => 'Profil Desa',
                'badge' => 'PROFIL DESA',
                'judul' => 'Profil Desa Sedati Agung',
                'deskripsi' => 'Informasi lengkap mengenai Desa Sedati Agung.',
                'kutipan' => null,
                'nama_kades' => null,
                'jabatan_kades' => null,
                'caption_hero' => null,
                'visi' => null,
                'visi_label' => 'VISI',
                'visi_catatan' => null,
                'misi' => null,
                'sejarah' => null,
                'sejarah_sorotan' => null,
                'periode_data' => null,
                'penduduk' => null,
                'luas_wilayah' => null,
                'jumlah_dusun' => null,
                'lahan_tani' => null,
                'alamat' => null,
                'jam_layanan' => null,
                'telepon' => null,
                'foto' => null,
                'foto_kantor' => null,
            ]);
        }

        $perangkat = PerangkatDesa::orderBy('urutan')->get();

        return view('profil-desa-lengkap', compact('profil', 'perangkat'));
    }
}
