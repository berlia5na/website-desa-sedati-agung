<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    protected $table = 'profil_desa';

    protected $fillable = [
        'nama_desa',
        'deskripsi',
        'sejarah',
        'visi',
        'misi',
        'alamat',
        'telepon',
        'email',
        'website',
        'foto',
        'subtitle',
        'badge',
        'judul',
        'kutipan',
        'nama_kades',
        'jabatan_kades',
        'caption_hero',
        'visi_label',
        'visi_catatan',
        'sejarah_sorotan',
        'periode_data',
        'penduduk',
        'luas_wilayah',
        'jumlah_dusun',
        'lahan_tani',
        'jam_layanan',
        'foto_kantor',
        'lokasi_peta',
    ];
}
