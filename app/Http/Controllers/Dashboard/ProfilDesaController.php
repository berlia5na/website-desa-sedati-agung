<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDesa;
use App\Models\ProfilDesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ProfilDesaController extends Controller
{
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
                    : new PerangkatDesa;

                return $anggota;
            });
        } else {
            $perangkat->push(new PerangkatDesa);
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
            'foto' => ['nullable', 'image'],
            'hapus_foto' => ['nullable', 'boolean'],
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
            'foto_kantor' => ['nullable', 'image'],
            'perangkat' => ['nullable', 'array'],
            'perangkat.*' => ['array'],
            'perangkat.*.id' => ['nullable', 'integer', 'distinct', 'exists:perangkat_desas,id'],
            'perangkat.*.hapus' => ['nullable', 'boolean'],
            'perangkat.*.hapus_foto' => ['nullable', 'boolean'],
            'perangkat.*.nama' => ['nullable', 'string', 'max:255'],
            'perangkat.*.jabatan' => ['nullable', 'string', 'max:255'],
            'perangkat.*.deskripsi' => ['nullable', 'string', 'max:255'],
            'perangkat.*.foto' => ['nullable', 'image'],
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

        $profil = ProfilDesa::query()->first() ?? new ProfilDesa;
        $fotoProfil = $request->file('foto')
            ? $this->storeProfilImage($request, 'foto')
            : ($request->boolean('hapus_foto') ? null : $profil->foto);
        $fotoKantor = $request->file('foto_kantor')
            ? $this->storeProfilImage($request, 'foto_kantor')
            : $profil->foto_kantor;
        $anggotaTersimpan = PerangkatDesa::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
        $fotoLamaYangDihapus = [];

        DB::transaction(function () use ($data, $profil, $fotoProfil, $fotoKantor, $anggotaTersimpan, $request, &$fotoLamaYangDihapus): void {
            if ($profil->foto && $profil->foto !== $fotoProfil) {
                $fotoLamaYangDihapus[] = $profil->foto;
            }
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
                        $anggotaDihapus = $anggotaTersimpan->firstWhere('id', (int) $anggotaData['id']);
                        if ($anggotaDihapus?->foto) {
                            $fotoLamaYangDihapus[] = $anggotaDihapus->foto;
                        }
                        $anggotaDihapus?->delete();
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
                    ? $anggotaTersimpan->firstWhere('id', (int) $anggotaData['id']) ?? new PerangkatDesa
                    : new PerangkatDesa;
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
                    $fotoBaru = $this->storeProfilImage($request, $fotoPerangkatKey);
                    if ($anggota->foto && $anggota->foto !== $fotoBaru) {
                        $fotoLamaYangDihapus[] = $anggota->foto;
                    }
                    $anggota->foto = $fotoBaru;
                } elseif (filter_var($anggotaData['hapus_foto'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                    if ($anggota->foto) {
                        $fotoLamaYangDihapus[] = $anggota->foto;
                    }
                    $anggota->foto = null;
                }

                $anggota->save();
            }
        });

        $this->deleteStoredProfileImages($fotoLamaYangDihapus);

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

        return 'storage/'.$path;
    }

    private function deleteStoredProfileImages(array $paths): void
    {
        foreach (array_unique($paths) as $path) {
            if (! str_starts_with($path, 'storage/')) {
                continue;
            }

            if (! Storage::disk('public')->delete(Str::after($path, 'storage/'))) {
                throw new RuntimeException('Profil tersimpan, tetapi foto lama gagal dihapus dari penyimpanan.');
            }
        }
    }
}
