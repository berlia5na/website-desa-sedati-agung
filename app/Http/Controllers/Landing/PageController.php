<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\GalleryPhoto;
use App\Models\HalamanBeranda;
use App\Models\NewsPageSetting;
use App\Models\ProfilBumdes;
use App\Models\StatistikDesa;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $profilBumdes = ProfilBumdes::query()
            ->with(['unitBumdes' => fn ($query) => $query->where('status', 'aktif')->latest()])
            ->first();

        return view('landing.profil-bumdes', compact('profilBumdes'));
    }

    public function berita(Request $request)
    {
        $query = Berita::query()
            ->where('status', 'published')
            ->when($request->filled('q'), function ($builder) use ($request) {
                $keyword = trim($request->input('q'));
                $builder->where(function ($where) use ($keyword) {
                    $where->where('judul', 'like', "%{$keyword}%")
                        ->orWhere('excerpt', 'like', "%{$keyword}%")
                        ->orWhere('kategori', 'like', "%{$keyword}%")
                        ->orWhere('isi', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('kategori') && $request->input('kategori') !== 'semua', function ($builder) use ($request) {
                $builder->where('kategori', $request->input('kategori'));
            });

        $sort = $request->input('urut', 'terbaru');
        if ($sort === 'terlama') {
            $query->oldest('created_at');
        } elseif ($sort === 'terpopuler') {
            $query->orderByDesc('is_featured')->orderByDesc('created_at');
        } else {
            $query->latest('created_at');
        }

        $featured = (clone $query)
            ->where('is_featured', true)
            ->first() ?? (clone $query)->first();

        if ($featured) {
            $articles = $query->whereKeyNot($featured->getKey())->paginate(6)->appends($request->query());
        } else {
            $articles = $query->paginate(6)->appends($request->query());
        }

        $categories = Berita::query()
            ->where('status', 'published')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori')
            ->sort();
        $galleryItems = Berita::query()
            ->where('status', 'published')
            ->whereNotNull('gambar')
            ->latest('created_at')
            ->take(4)
            ->get();
        $galleryPhotos = GalleryPhoto::query()->latest()->take(4)->get();
        $newsPageSettings = NewsPageSetting::query()->first();

        return view('landing.berita', compact('featured', 'articles', 'categories', 'sort', 'galleryItems', 'galleryPhotos', 'newsPageSettings'));
    }

    public function beritaDetail(string $slug): View
    {
        $berita = Berita::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $beritaTerkait = Berita::query()
            ->where('status', 'published')
            ->whereKeyNot($berita->getKey())
            ->when($berita->kategori, fn ($query) => $query->where('kategori', $berita->kategori))
            ->latest('created_at')
            ->take(3)
            ->get();

        if ($beritaTerkait->isEmpty()) {
            $beritaTerkait = Berita::query()
                ->where('status', 'published')
                ->whereKeyNot($berita->getKey())
                ->latest('created_at')
                ->take(3)
                ->get();
        }

        return view('landing.berita-detail', compact('berita', 'beritaTerkait'));
    }

    public function agenda()
    {
        return view('landing.agenda');
    }

    public function galeri()
    {
        $galleryPhotos = GalleryPhoto::query()
            ->latest()
            ->paginate(12);
        $galleryItems = Berita::query()
            ->where('status', 'published')
            ->whereNotNull('gambar')
            ->latest('created_at')
            ->take(12)
            ->get();

        return view('landing.galeri', compact('galleryItems', 'galleryPhotos'));
    }

    public function kontak()
    {
        return view('landing.kontak');
    }
}
