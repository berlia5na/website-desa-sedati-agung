<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\GalleryPhoto;
use App\Models\NewsPageSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BeritaController extends Controller
{
    public function berita()
    {
        $berita = Berita::query()->latest('updated_at')->paginate(10);
        $galleryPhotos = GalleryPhoto::query()->latest()->get();
        $newsPageSettings = NewsPageSetting::query()->first() ?? new NewsPageSetting([
            'badge' => 'Portal Warta Desa',
            'title' => 'Kabar & Publikasi Terkini',
            'description' => 'Dapatkan informasi terbaru seputar berita, kegiatan, dan berbagai aktivitas Desa Sedati melalui publikasi terkini yang kami sajikan untuk masyarakat.',
        ]);

        return view('dashboard.berita', compact('berita', 'galleryPhotos', 'newsPageSettings'));
    }

    public function updateBeritaHeader(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'badge' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        NewsPageSetting::query()->updateOrCreate(['id' => 1], $data);

        return redirect()
            ->route('admin.berita')
            ->with('status', 'Header halaman berita berhasil diperbarui.');
    }

    public function storeGalleryPhoto(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);
        $imagePath = $request->file('image')->store('galeri-kegiatan', 'public');

        if (! $imagePath) {
            throw new RuntimeException('Foto galeri gagal disimpan. Silakan coba unggah kembali.');
        }

        GalleryPhoto::query()->create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'caption' => $data['caption'] ?? null,
            'image_path' => $imagePath,
        ]);

        return redirect()
            ->route('admin.berita')
            ->with('status', 'Foto berhasil ditambahkan ke galeri publik.');
    }

    public function deleteGalleryPhoto(GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $imagePath = $galleryPhoto->image_path;
        $galleryPhoto->delete();

        if (! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Foto dihapus dari daftar, tetapi file gagal dihapus dari penyimpanan.');
        }

        return redirect()
            ->route('admin.berita')
            ->with('status', 'Foto berhasil dihapus dari galeri.');
    }

    public function createBerita()
    {
        return view('dashboard.berita-form', ['berita' => new Berita]);
    }

    public function storeBerita(Request $request): RedirectResponse
    {
        $data = $this->validateBerita($request);
        $imagePath = $request->file('gambar')?->store('berita', 'public');

        if ($request->hasFile('gambar') && ! $imagePath) {
            throw new RuntimeException('Gambar berita gagal disimpan. Silakan coba unggah kembali.');
        }

        $slug = $this->uniqueBeritaSlug($data['judul']);
        $berita = Berita::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'slug' => $slug,
            'gambar' => $imagePath,
            'author_name' => $request->user()->name,
            'read_time' => max(1, (int) ceil(str_word_count(strip_tags($data['isi'])) / 200)),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()
            ->route('admin.berita')
            ->with('status', $berita->status === 'published'
                ? 'Berita berhasil dipublikasikan dan sudah tampil di website.'
                : 'Draft berita berhasil disimpan.');
    }

    public function editBerita(Berita $berita)
    {
        return view('dashboard.berita-form', compact('berita'));
    }

    public function updateBerita(Request $request, Berita $berita): RedirectResponse
    {
        $data = $this->validateBerita($request);
        $oldImage = $berita->gambar;
        $imagePath = $request->file('gambar')?->store('berita', 'public');

        if ($request->hasFile('gambar') && ! $imagePath) {
            throw new RuntimeException('Gambar berita gagal disimpan. Silakan coba unggah kembali.');
        }

        $berita->fill([
            ...$data,
            'gambar' => $imagePath ?: ($request->boolean('hapus_gambar') ? null : $oldImage),
            'author_name' => $request->user()->name,
            'read_time' => max(1, (int) ceil(str_word_count(strip_tags($data['isi'])) / 200)),
            'is_featured' => $request->boolean('is_featured'),
        ])->save();

        if (($imagePath || $request->boolean('hapus_gambar')) && $oldImage && ! str_starts_with($oldImage, 'http') && ! Storage::disk('public')->delete($oldImage)) {
            throw new RuntimeException('Berita tersimpan, tetapi gambar lama gagal dihapus dari penyimpanan.');
        }

        return redirect()
            ->route('admin.berita')
            ->with('status', $berita->status === 'published'
                ? 'Berita berhasil diperbarui dan dipublikasikan.'
                : 'Draft berita berhasil diperbarui.');
    }

    public function deleteBerita(Berita $berita): RedirectResponse
    {
        $image = $berita->gambar;
        $berita->delete();

        if ($image && ! str_starts_with($image, 'http') && ! Storage::disk('public')->delete($image)) {
            throw new RuntimeException('Berita terhapus, tetapi gambar gagal dihapus dari penyimpanan.');
        }

        return redirect()
            ->route('admin.berita')
            ->with('status', 'Berita berhasil dihapus.');
    }

    private function validateBerita(Request $request): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'isi' => ['required', 'string', 'max:50000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'hapus_gambar' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
        ]);

        unset($data['hapus_gambar']);

        return $data;
    }

    private function uniqueBeritaSlug(string $judul): string
    {
        $baseSlug = Str::slug($judul) ?: 'berita';
        $slug = $baseSlug;
        $suffix = 2;

        while (Berita::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}
