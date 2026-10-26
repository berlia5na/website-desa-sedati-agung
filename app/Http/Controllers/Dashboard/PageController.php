<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\HalamanBeranda;
use App\Models\StatistikDesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PageController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function beranda()
    {
        $halaman = HalamanBeranda::query()
            ->with('publisher:id,name')
            ->firstOrCreate([], ['judul' => '', 'status' => 'draft', 'sambutan_tampil' => true]);
        $statistik = StatistikDesa::query()->firstOrCreate([], [
            'jumlah_usaha_bumdes' => 4,
        ]);
        $beritaTersedia = Berita::query()
            ->where('status', 'published')
            ->latest()
            ->get(['id', 'judul', 'created_at']);
        $agendaTersedia = Agenda::query()
            ->where('status', 'published')
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get(['id', 'judul', 'tanggal']);

        return view('dashboard.beranda', compact('halaman', 'statistik', 'beritaTersedia', 'agendaTersedia'));
    }

    public function updateBeranda(Request $request): RedirectResponse
    {
        $urlRule = ['nullable', 'string', 'max:255', 'regex:~^(?:/(?!/)|https?://)[^\s]*$~i'];
        $data = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'hero_button_label' => ['nullable', 'string', 'max:80'],
            'hero_button_url' => $urlRule,
            'hero_image' => ['nullable', 'image', 'max:5120'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_usaha_bumdes' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_umkm' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'sambutan_nama' => ['nullable', 'string', 'max:255'],
            'sambutan_jabatan' => ['nullable', 'string', 'max:255'],
            'sambutan_teks' => ['nullable', 'string', 'max:10000'],
            'sambutan_foto' => ['nullable', 'image', 'max:5120'],
            'sorotan' => ['nullable', 'array', 'max:3'],
            'sorotan.*' => ['string', 'regex:/^(berita|agenda):[1-9][0-9]*$/'],
            'widget_judul' => ['nullable', 'string', 'max:255'],
            'widget_teks' => ['nullable', 'string', 'max:2000'],
            'widget_tombol' => ['nullable', 'string', 'max:80'],
            'widget_tautan' => $urlRule,
        ]);

        $sorotan = $data['sorotan'] ?? [];
        foreach ($sorotan as $item) {
            [$jenis, $id] = explode(':', $item, 2);
            $model = $jenis === 'berita' ? Berita::class : Agenda::class;

            if (! $model::query()->whereKey($id)->where('status', 'published')->exists()) {
                throw ValidationException::withMessages([
                    'sorotan' => 'Pilih berita atau agenda yang sudah dipublikasikan.',
                ]);
            }
        }

        $halaman = HalamanBeranda::query()->firstOrCreate([], [
            'judul' => '',
            'status' => 'draft',
            'sambutan_tampil' => true,
        ]);
        $heroImage = $request->file('hero_image')
            ? $this->storePublicImage($request, 'hero_image')
            : $halaman->gambar;
        $welcomePhoto = $request->file('sambutan_foto')
            ? $this->storePublicImage($request, 'sambutan_foto')
            : $halaman->sambutan_foto;

        DB::transaction(function () use ($data, $halaman, $heroImage, $welcomePhoto, $request, $sorotan): void {
            $halaman->fill([
                'judul' => $data['judul'] ?? '',
                'deskripsi' => $data['deskripsi'] ?? null,
                'gambar' => $heroImage,
                'hero_button_label' => $data['hero_button_label'] ?? null,
                'hero_button_url' => $data['hero_button_url'] ?? null,
                'sambutan_nama' => $data['sambutan_nama'] ?? null,
                'sambutan_jabatan' => $data['sambutan_jabatan'] ?? null,
                'sambutan_teks' => $data['sambutan_teks'] ?? null,
                'sambutan_foto' => $welcomePhoto,
                'sambutan_tampil' => $request->boolean('sambutan_tampil'),
                'sorotan' => $sorotan,
                'widget_judul' => $data['widget_judul'] ?? null,
                'widget_teks' => $data['widget_teks'] ?? null,
                'widget_tombol' => $data['widget_tombol'] ?? null,
                'widget_tautan' => $data['widget_tautan'] ?? null,
                'widget_tampil' => $request->boolean('widget_tampil'),
                'status' => 'published',
                'published_at' => now(),
                'published_by' => $request->user()->id,
            ]);
            $halaman->save();

            StatistikDesa::query()->firstOrCreate([], [
                'jumlah_usaha_bumdes' => 4,
            ])->update([
                'jumlah_penduduk' => $data['jumlah_penduduk'] ?? null,
                'jumlah_usaha_bumdes' => $data['jumlah_usaha_bumdes'],
                'jumlah_umkm' => $data['jumlah_umkm'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.beranda')
            ->with('status', 'Halaman beranda berhasil dipublikasikan.');
    }

    private function storePublicImage(Request $request, string $key): string
    {
        $path = $request->file($key)->store('halaman-beranda', 'public');

        if (! $path) {
            throw new RuntimeException('Gambar gagal disimpan. Silakan coba unggah kembali.');
        }

        return $path;
    }

    public function deleteSambutan(Request $request): RedirectResponse
    {
        $halaman = HalamanBeranda::query()->firstOrCreate([], [
            'judul' => '',
            'status' => 'draft',
            'sambutan_tampil' => true,
        ]);
        $foto = $halaman->sambutan_foto;

        DB::transaction(function () use ($halaman, $request): void {
            $halaman->fill([
                'sambutan_nama' => null,
                'sambutan_jabatan' => null,
                'sambutan_teks' => null,
                'sambutan_foto' => null,
                'sambutan_tampil' => false,
                'published_at' => now(),
                'published_by' => $request->user()->id,
            ]);
            $halaman->save();
        });

        if ($foto && ! Storage::disk('public')->delete($foto)) {
            throw new RuntimeException('Data sambutan terhapus, tetapi foto gagal dihapus dari penyimpanan.');
        }

        return redirect()
            ->route('admin.beranda')
            ->with('status', 'Sambutan kepala desa berhasil dihapus dari halaman beranda.');
    }

    public function updateStatistik(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_usaha_bumdes' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_umkm' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
        ]);

        $statistik = StatistikDesa::query()->firstOrCreate([], [
            'jumlah_usaha_bumdes' => 4,
        ]);
        $statistik->update($data);

        return redirect()
            ->route('admin.beranda')
            ->with('status', 'Statistik beranda berhasil diperbarui.');
    }

    public function profilDesa()
    {
        return view('dashboard.profil-desa');
    }

    public function berita()
    {
        return view('dashboard.berita');
    }

    public function profilBumdes()
    {
        return view('dashboard.profil-bumdes');
    }

    public function agenda()
    {
        return view('dashboard.agenda');
    }

    public function kontak()
    {
        return view('dashboard.kontak');
    }
}
