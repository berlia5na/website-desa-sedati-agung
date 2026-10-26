<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\HalamanBeranda;
use App\Models\StatistikDesa;

class PageController extends Controller
{
    public function home()
    {
        $halamanBeranda = HalamanBeranda::query()
            ->where('status', 'published')
            ->latest('updated_at')
            ->first();
        $statistik = StatistikDesa::query()->firstOrCreate([], [
            'jumlah_usaha_bumdes' => 4,
        ]);
        $beritaTerkini = Berita::query()
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get(['judul', 'slug', 'isi', 'created_at']);
        $agendaTerdekat = Agenda::query()
            ->where('status', 'published')
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->first(['judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'lokasi']);
        $sorotanTerkini = collect($halamanBeranda?->sorotan ?? [])
            ->map(function (string $item) {
                [$jenis, $id] = explode(':', $item, 2);
                $model = $jenis === 'berita' ? Berita::class : Agenda::class;
                $pilihan = $model::query()
                    ->whereKey($id)
                    ->where('status', 'published')
                    ->first();

                return $pilihan?->setAttribute('jenis_sorotan', $jenis);
            })
            ->filter()
            ->values();
        $sambutanTampil = ! $halamanBeranda || $halamanBeranda->sambutan_tampil;

        return view('landing.home', compact(
            'halamanBeranda',
            'statistik',
            'beritaTerkini',
            'agendaTerdekat',
            'sorotanTerkini',
            'sambutanTampil'
        ));
    }

    public function profilDesa()
    {
        return view('landing.profil-desa');
    }

    public function profilBumdes()
    {
        return view('landing.profil-bumdes');
    }

    public function berita()
    {
        return view('landing.berita');
    }

    public function agenda()
    {
        return view('landing.agenda');
    }

    public function kontak()
    {
        return view('landing.kontak');
    }
}
