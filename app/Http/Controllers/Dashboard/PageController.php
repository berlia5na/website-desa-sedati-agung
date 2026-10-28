<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\HalamanBeranda;
use App\Models\PerangkatDesa;
use App\Models\ProfilDesa;
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
        $profil = ProfilDesa::query()->first() ?? new ProfilDesa(['nama_desa' => '']);
        $perangkat = PerangkatDesa::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        if (old('perangkat')) {
            $perangkat = collect(old('perangkat'))->map(function (array $input) use ($perangkat) {
                $anggota = ! empty($input['id'])
                    ? $perangkat->firstWhere('id', (int) $input['id']) ?? new PerangkatDesa(['id' => $input['id']])
                    : new PerangkatDesa();

                return $anggota;
            });
        } else {
            $perangkat->push(new PerangkatDesa());
        }

        $lokasiPeta = trim($profil->lokasi_peta ?: $profil->alamat ?: 'Desa Sedati Agung, Kecamatan Sedati, Kabupaten Sidoarjo');

        return view('dashboard.profil-desa', compact('profil', 'perangkat', 'lokasiPeta'));
    }

    public function updateProfilDesa(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_desa' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:255'],
            'judul' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'kutipan' => ['nullable', 'string', 'max:5000'],
            'nama_kades' => ['nullable', 'string', 'max:255'],
            'jabatan_kades' => ['nullable', 'string', 'max:255'],
            'caption_hero' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'visi_label' => ['nullable', 'string', 'max:255'],
            'visi' => ['nullable', 'string', 'max:5000'],
            'visi_catatan' => ['nullable', 'string', 'max:5000'],
            'misi' => ['nullable', 'string', 'max:10000'],
            'sejarah' => ['nullable', 'string', 'max:20000'],
            'sejarah_sorotan' => ['nullable', 'string', 'max:5000'],
            'periode_data' => ['nullable', 'string', 'max:255'],
            'penduduk' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'luas_wilayah' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'jumlah_dusun' => ['nullable', 'integer', 'min:0', 'max:255'],
            'lahan_tani' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'alamat' => ['nullable', 'string', 'max:5000'],
            'lokasi_peta' => ['nullable', 'string', 'max:255'],
            'jam_layanan' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'foto_kantor' => ['nullable', 'image', 'max:5120'],
            'perangkat' => ['nullable', 'array'],
            'perangkat.*' => ['array'],
            'perangkat.*.id' => ['nullable', 'integer', 'distinct', 'exists:perangkat_desas,id'],
            'perangkat.*.hapus' => ['nullable', 'boolean'],
            'perangkat.*.nama' => ['nullable', 'string', 'max:255'],
            'perangkat.*.jabatan' => ['nullable', 'string', 'max:255'],
            'perangkat.*.deskripsi' => ['nullable', 'string', 'max:255'],
            'perangkat.*.foto' => ['nullable', 'image', 'max:5120'],
        ]);

        foreach ($data['perangkat'] ?? [] as $index => $anggota) {
            $memilikiIsi = collect($anggota)
                ->except(['id', 'hapus'])
                ->contains(fn ($value) => is_string($value) && trim($value) !== '');
            $memilikiFoto = $request->hasFile("perangkat.{$index}.foto");
            $akanDihapus = filter_var($anggota['hapus'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $akanDihapus && ($memilikiIsi || $memilikiFoto) && trim($anggota['nama'] ?? '') === '') {
                throw ValidationException::withMessages([
                    "perangkat.{$index}.nama" => 'Nama perangkat desa wajib diisi jika data lainnya diisi.',
                ]);
            }
        }

        $profil = ProfilDesa::query()->first() ?? new ProfilDesa();
        $fotoProfil = $request->file('foto')
            ? $this->storeProfilImage($request, 'foto')
            : $profil->foto;
        $fotoKantor = $request->file('foto_kantor')
            ? $this->storeProfilImage($request, 'foto_kantor')
            : $profil->foto_kantor;
        $anggotaTersimpan = PerangkatDesa::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        DB::transaction(function () use ($data, $profil, $fotoProfil, $fotoKantor, $anggotaTersimpan, $request): void {
            $profil->fill([
                'nama_desa' => $data['nama_desa'],
                'subtitle' => $data['subtitle'] ?? null,
                'badge' => $data['badge'] ?? null,
                'judul' => $data['judul'] ?? null,
                'deskripsi' => $data['deskripsi'] ?? null,
                'kutipan' => $data['kutipan'] ?? null,
                'nama_kades' => $data['nama_kades'] ?? null,
                'jabatan_kades' => $data['jabatan_kades'] ?? null,
                'caption_hero' => $data['caption_hero'] ?? null,
                'foto' => $fotoProfil,
                'visi_label' => $data['visi_label'] ?? null,
                'visi' => $data['visi'] ?? null,
                'visi_catatan' => $data['visi_catatan'] ?? null,
                'misi' => $data['misi'] ?? null,
                'sejarah' => $data['sejarah'] ?? null,
                'sejarah_sorotan' => $data['sejarah_sorotan'] ?? null,
                'periode_data' => $data['periode_data'] ?? null,
                'penduduk' => $data['penduduk'] ?? null,
                'luas_wilayah' => $data['luas_wilayah'] ?? null,
                'jumlah_dusun' => $data['jumlah_dusun'] ?? null,
                'lahan_tani' => $data['lahan_tani'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'lokasi_peta' => $data['lokasi_peta'] ?? null,
                'jam_layanan' => $data['jam_layanan'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'foto_kantor' => $fotoKantor,
            ]);
            $profil->save();

            foreach ($data['perangkat'] ?? [] as $index => $anggotaData) {
                if (filter_var($anggotaData['hapus'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                    if (! empty($anggotaData['id'])) {
                        $anggotaTersimpan->firstWhere('id', (int) $anggotaData['id'])?->delete();
                    }

                    continue;
                }

                $nama = trim($anggotaData['nama'] ?? '');
                $fotoPerangkatKey = "perangkat.{$index}.foto";
                $memilikiFoto = $request->hasFile($fotoPerangkatKey);

                if ($nama === '') {
                    continue;
                }

                $anggota = ! empty($anggotaData['id'])
                    ? $anggotaTersimpan->firstWhere('id', (int) $anggotaData['id']) ?? new PerangkatDesa()
                    : new PerangkatDesa();
                if (! $anggota->exists) {
                    $anggota->urutan = min((int) PerangkatDesa::query()->max('urutan') + 1, 255);
                }
                $anggota->fill([
                    'nama' => $nama,
                    'jabatan' => $anggotaData['jabatan'] ?? '',
                    'deskripsi' => $anggotaData['deskripsi'] ?? null,
                    'foto' => $anggota->foto,
                ]);

                if ($memilikiFoto) {
                    $anggota->foto = $this->storeProfilImage($request, $fotoPerangkatKey);
                }

                $anggota->save();
            }
        });

        return redirect()
            ->route('admin.profil-desa')
            ->with('status', 'Profil Desa berhasil diperbarui.');
    }

    private function storeProfilImage(Request $request, string $key): string
    {
        $path = $request->file($key)->store('profil-desa', 'public');

        if (! $path) {
            throw new RuntimeException('Foto gagal disimpan. Silakan coba unggah kembali.');
        }

        return 'storage/' . $path;
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
