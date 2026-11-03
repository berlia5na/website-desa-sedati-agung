<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProfilBumdes;
use App\Models\UnitBumdes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class BumdesController extends Controller
{
    public function profilBumdes()
    {
        $profil = ProfilBumdes::query()->firstOrCreate([], [
            'nama_bumdes' => 'BUMDes Sedati Agung',
        ]);
        $profil->load('unitBumdes');

        return view('dashboard.profil-bumdes', compact('profil'));
    }

    public function updateProfilBumdes(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_bumdes' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'sejarah' => ['nullable', 'string', 'max:10000'],
            'visi' => ['nullable', 'string', 'max:5000'],
            'misi' => ['nullable', 'string', 'max:10000'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'telepon' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'legalitas' => ['nullable', 'array'],
            'legalitas.perdes_nomor' => ['nullable', 'string', 'max:255'],
            'legalitas.perdes_keterangan' => ['nullable', 'string', 'max:500'],
            'legalitas.ahu_nomor' => ['nullable', 'string', 'max:255'],
            'legalitas.ahu_keterangan' => ['nullable', 'string', 'max:500'],
            'legalitas.tanggal_berdiri' => ['nullable', 'date'],
            'legalitas.nib' => ['nullable', 'string', 'max:255'],
            'pengurus' => ['nullable', 'array', 'max:20'],
            'pengurus.*' => ['array'],
            'pengurus.*.nama' => ['nullable', 'string', 'max:255'],
            'pengurus.*.jabatan' => ['nullable', 'string', 'max:255'],
            'pengurus.*.deskripsi' => ['nullable', 'string', 'max:500'],
            'pengurus.*.foto' => ['nullable', 'image'],
            'pengurus.*.foto_tersimpan' => ['nullable', 'string', 'max:500'],
            'pengurus.*.hapus_foto' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image'],
            'hapus_foto' => ['nullable', 'boolean'],
            'unit' => ['nullable', 'array', 'max:20'],
            'unit.*' => ['array'],
            'unit.*.id' => ['nullable', 'integer', 'distinct'],
            'unit.*.hapus' => ['nullable', 'boolean'],
            'unit.*.hapus_gambar' => ['nullable', 'boolean'],
            'unit.*.nama_unit' => ['nullable', 'string', 'max:255'],
            'unit.*.jenis' => ['nullable', 'string', 'max:100'],
            'unit.*.deskripsi' => ['nullable', 'string', 'max:2000'],
            'unit.*.harga' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'unit.*.telepon' => ['nullable', 'string', 'max:40', 'regex:/^\+?[0-9\s().-]+$/'],
            'unit.*.gambar' => ['nullable', 'image'],
            'unit.*.status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        $profil = ProfilBumdes::query()->firstOrCreate([], [
            'nama_bumdes' => 'BUMDes Sedati Agung',
        ]);
        $units = $profil->unitBumdes()->get()->keyBy('id');
        $unitIds = collect($data['unit'] ?? [])
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id);

        if ($unitIds->diff($units->keys())->isNotEmpty()) {
            throw ValidationException::withMessages([
                'unit' => 'Unit usaha yang dipilih tidak sesuai dengan profil BUMDes ini.',
            ]);
        }

        $fotoPengurusLama = collect($profil->pengurus ?? [])
            ->pluck('foto')
            ->filter()
            ->unique()
            ->values();
        $pengurus = collect();

        foreach ($data['pengurus'] ?? [] as $index => $anggota) {
            $nama = trim($anggota['nama'] ?? '');
            $jabatan = trim($anggota['jabatan'] ?? '');
            $deskripsi = trim($anggota['deskripsi'] ?? '');
            $fotoTersimpan = $anggota['foto_tersimpan'] ?? null;
            $fotoBaru = $request->file("pengurus.{$index}.foto");
            $hapusFoto = filter_var($anggota['hapus_foto'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($fotoTersimpan && ! $fotoPengurusLama->contains($fotoTersimpan)) {
                throw ValidationException::withMessages([
                    'pengurus' => 'Foto pengurus yang dipilih tidak sesuai dengan data tersimpan.',
                ]);
            }

            if ($hapusFoto && ! $fotoBaru) {
                $fotoTersimpan = null;
            }

            if ($nama === '' && $jabatan === '' && $deskripsi === '' && ! $fotoTersimpan && ! $fotoBaru) {
                continue;
            }

            if ($nama === '' || $jabatan === '') {
                throw ValidationException::withMessages([
                    'pengurus' => 'Isi nama dan jabatan pengurus, atau hapus kartu yang tidak digunakan.',
                ]);
            }

            $pengurus->push([
                '_index' => $index,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'deskripsi' => $deskripsi,
                'foto' => $fotoTersimpan,
            ]);
        }

        foreach ($data['unit'] ?? [] as $index => $unitData) {
            if (filter_var($unitData['hapus'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            $hasContent = collect($unitData)
                ->except(['id', 'hapus', 'status'])
                ->contains(fn ($value) => is_string($value) && trim($value) !== '');
            $hasImage = $request->hasFile("unit.{$index}.gambar");

            if (! empty($unitData['id']) || $hasContent || $hasImage) {
                if (trim($unitData['nama_unit'] ?? '') === '' || trim($unitData['jenis'] ?? '') === '') {
                    throw ValidationException::withMessages([
                        "unit.{$index}.nama_unit" => 'Nama dan jenis unit usaha wajib diisi.',
                    ]);
                }
            }
        }

        $pengurus = $pengurus->map(function (array $anggota) use ($request): array {
            $fotoBaru = $request->file("pengurus.{$anggota['_index']}.foto");

            if ($fotoBaru) {
                $foto = $fotoBaru->store('pengurus-bumdes', 'public');

                if (! $foto) {
                    throw new RuntimeException('Foto pengurus gagal disimpan. Silakan coba unggah kembali.');
                }

                $anggota['foto'] = $foto;
            }

            unset($anggota['_index']);

            return $anggota;
        });

        $fotoLama = $profil->foto;
        $gambarLamaYangDihapus = [];

        DB::transaction(function () use ($data, $pengurus, $profil, $units, $request, &$gambarLamaYangDihapus): void {
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto')->store('profil-bumdes', 'public');

                if (! $foto) {
                    throw new RuntimeException('Foto BUMDes gagal disimpan. Silakan coba unggah kembali.');
                }

                $profil->foto = $foto;
            } elseif ($request->boolean('hapus_foto')) {
                $profil->foto = null;
            }

            $profil->fill([
                'nama_bumdes' => $data['nama_bumdes'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'sejarah' => $data['sejarah'] ?? null,
                'visi' => $data['visi'] ?? null,
                'misi' => $data['misi'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'legalitas' => $data['legalitas'] ?? null,
                'pengurus' => $pengurus->all(),
            ])->save();

            foreach ($data['unit'] ?? [] as $index => $unitData) {
                $unit = ! empty($unitData['id'])
                    ? $units->get((int) $unitData['id'])
                    : new UnitBumdes;

                if (filter_var($unitData['hapus'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                    if ($unit) {
                        if ($unit->gambar) {
                            $gambarLamaYangDihapus[] = $unit->gambar;
                        }

                        $unit->delete();
                    }

                    continue;
                }

                $hasContent = collect($unitData)
                    ->except(['id', 'hapus', 'status'])
                    ->contains(fn ($value) => is_string($value) && trim($value) !== '');
                $hasImage = $request->hasFile("unit.{$index}.gambar");

                if (empty($unitData['id']) && ! $hasContent && ! $hasImage) {
                    continue;
                }

                if ($hasImage) {
                    $gambar = $request->file("unit.{$index}.gambar")->store('unit-bumdes', 'public');

                    if (! $gambar) {
                        throw new RuntimeException('Gambar unit usaha gagal disimpan. Silakan coba unggah kembali.');
                    }

                    if ($unit->gambar) {
                        $gambarLamaYangDihapus[] = $unit->gambar;
                    }

                    $unit->gambar = $gambar;
                } elseif (filter_var($unitData['hapus_gambar'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                    if ($unit->gambar) {
                        $gambarLamaYangDihapus[] = $unit->gambar;
                    }

                    $unit->gambar = null;
                }

                $unit->fill([
                    'nama_unit' => trim($unitData['nama_unit']),
                    'jenis' => trim($unitData['jenis']),
                    'deskripsi' => $unitData['deskripsi'] ?? null,
                    'harga' => $unitData['harga'] ?? null,
                    'telepon' => $unitData['telepon'] ?? null,
                    'status' => $unitData['status'] ?? 'aktif',
                ]);
                $profil->unitBumdes()->save($unit);
            }
        });

        if ($fotoLama && ($request->hasFile('foto') || $request->boolean('hapus_foto')) && ! Storage::disk('public')->delete($fotoLama)) {
            throw new RuntimeException('Profil tersimpan, tetapi foto BUMDes lama gagal dihapus dari penyimpanan.');
        }

        $fotoPengurusTerpakai = $pengurus->pluck('foto')->filter()->unique();

        foreach ($fotoPengurusLama->diff($fotoPengurusTerpakai) as $fotoLamaPengurus) {
            if (! Storage::disk('public')->delete($fotoLamaPengurus)) {
                throw new RuntimeException('Profil tersimpan, tetapi foto pengurus lama gagal dihapus dari penyimpanan.');
            }
        }

        foreach (array_unique($gambarLamaYangDihapus) as $gambarLama) {
            if (! Storage::disk('public')->delete($gambarLama)) {
                throw new RuntimeException('Profil tersimpan, tetapi gambar unit usaha lama gagal dihapus dari penyimpanan.');
            }
        }

        return redirect()
            ->route('admin.profil-bumdes')
            ->with('status', 'Profil BUMDes berhasil diperbarui.');
    }
}
