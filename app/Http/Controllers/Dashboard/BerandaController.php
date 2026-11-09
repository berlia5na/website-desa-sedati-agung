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

class BerandaController extends Controller
{
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
            'hero_image' => ['nullable', 'image'],
            'hapus_gambar_hero' => ['nullable', 'boolean'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_usaha_bumdes' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'jumlah_umkm' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'sambutan_nama' => ['nullable', 'string', 'max:255'],
            'sambutan_jabatan' => ['nullable', 'string', 'max:255'],
            'sambutan_teks' => ['nullable', 'string', 'max:10000'],
            'sambutan_foto' => ['nullable', 'image'],
            'hapus_foto_sambutan' => ['nullable', 'boolean'],
            'sorotan' => ['nullable', 'array', 'max:3'],
            'sorotan.*' => ['string', 'regex:/^(berita|agenda):[1-9][0-9]*$/'],
            'widget_judul' => ['nullable', 'string', 'max:255'],
            'widget_teks' => ['nullable', 'string', 'max:2000'],
            'widget_tombol' => ['nullable', 'string', 'max:80'],
            'widget_tautan' => $urlRule,
            'anggaran_tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'anggaran_pendapatan' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'realisasi_pendapatan' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'anggaran_belanja' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'realisasi_belanja' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'anggaran_pembiayaan' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'realisasi_pembiayaan' => ['nullable', 'integer', 'min:0', 'max:999999999999999999'],
            'anggaran_keterangan' => ['nullable', 'string', 'max:2000'],
            'anggaran_dokumen_url' => $urlRule,
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
        $gambarHeroLama = $halaman->gambar;
        $fotoSambutanLama = $halaman->sambutan_foto;
        $heroImage = $request->hasFile('hero_image')
            ? $this->storePublicImage($request, 'hero_image')
            : ($request->boolean('hapus_gambar_hero') ? null : $halaman->gambar);
        $welcomePhoto = $request->hasFile('sambutan_foto')
            ? $this->storePublicImage($request, 'sambutan_foto')
            : ($request->boolean('hapus_foto_sambutan') ? null : $halaman->sambutan_foto);

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
                'anggaran_tahun' => $data['anggaran_tahun'] ?? null,
                'anggaran_pendapatan' => $data['anggaran_pendapatan'] ?? null,
                'realisasi_pendapatan' => $data['realisasi_pendapatan'] ?? null,
                'anggaran_belanja' => $data['anggaran_belanja'] ?? null,
                'realisasi_belanja' => $data['realisasi_belanja'] ?? null,
                'anggaran_pembiayaan' => $data['anggaran_pembiayaan'] ?? null,
                'realisasi_pembiayaan' => $data['realisasi_pembiayaan'] ?? null,
                'anggaran_keterangan' => $data['anggaran_keterangan'] ?? null,
                'anggaran_dokumen_url' => $data['anggaran_dokumen_url'] ?? null,
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

        $this->deletePublicImages([
            $gambarHeroLama !== $heroImage ? $gambarHeroLama : null,
            $fotoSambutanLama !== $welcomePhoto ? $fotoSambutanLama : null,
        ], 'Beranda tersimpan, tetapi foto lama gagal dihapus dari penyimpanan.');

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

    private function deletePublicImages(array $paths, string $errorMessage): void
    {
        foreach (array_unique(array_filter($paths)) as $path) {
            if (! Storage::disk('public')->delete($path)) {
                throw new RuntimeException($errorMessage);
            }
        }
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
}
