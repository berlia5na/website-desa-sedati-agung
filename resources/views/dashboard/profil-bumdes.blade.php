@extends('layouts.dashboard')

@section('title', 'Kelola Profil BUMDes | Portal Admin Desa Sedati Agung')

@section('content')
    @php
        $unitRows = old('unit', $profil->unitBumdes->map(fn ($unit) => [
            'id' => $unit->id,
            'nama_unit' => $unit->nama_unit,
            'jenis' => $unit->jenis,
            'deskripsi' => $unit->deskripsi,
            'harga' => $unit->harga,
            'telepon' => $unit->telepon,
            'status' => $unit->status,
            'gambar_tersimpan' => $unit->gambar,
        ])->all());
        $unitRows = array_merge($unitRows, array_fill(0, min(2, max(0, 20 - count($unitRows))), []));
        $legalitas = old('legalitas', $profil->legalitas ?? []);
        $pengurus = collect(old('pengurus', $profil->pengurus ?? []))
            ->filter(fn ($anggota) => is_array($anggota) && (filled($anggota['nama'] ?? null) || filled($anggota['jabatan'] ?? null) || filled($anggota['deskripsi'] ?? null) || filled($anggota['foto_tersimpan'] ?? $anggota['foto'] ?? null)))
            ->map(fn (array $anggota) => [
                ...$anggota,
                'foto_tersimpan' => $anggota['foto_tersimpan'] ?? $anggota['foto'] ?? null,
            ])
            ->all();
        $pengurus = $pengurus ?: [[]];
        $nextManagerIndex = max(array_keys($pengurus)) + 1;
    @endphp

    <div class="bumdes-admin">
        <div class="admin-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span>Profil BUMDes</span></div>
        <div class="bumdes-admin__heading">
            <div>
                <h1>Kelola Profil BUMDes {{ $profil->nama_bumdes }}</h1>
                <p>Perbarui informasi utama, visi dan misi, serta daftar unit usaha yang tampil di halaman publik.</p>
            </div>
            <a class="admin-button admin-button--secondary" href="{{ route('profil-bumdes') }}" target="_blank" rel="noopener">Lihat halaman publik <span aria-hidden="true">↗</span></a>
        </div>

        @if (session('status'))
            <p class="admin-alert admin-alert--success" role="status"><span aria-hidden="true">✓</span>{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error" role="alert">
                <strong>Periksa kembali data berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="bumdes-editor" method="POST" action="{{ route('admin.profil-bumdes.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="bumdes-editor__panel" aria-labelledby="bumdes-hero-editor-title">
                <div class="bumdes-editor__panel-heading">
                    <span class="bumdes-editor__panel-icon" aria-hidden="true">✦</span>
                    <div>
                        <h2 id="bumdes-hero-editor-title">Header &amp; identitas BUMDes</h2>
                        <p>Informasi utama yang tampil pada bagian pembuka halaman profil BUMDes.</p>
                    </div>
                    <span class="bumdes-editor__step">BAGIAN 01</span>
                </div>

                <div class="bumdes-editor__hero-grid">
                    <div class="bumdes-editor__fields">
                        <div class="bumdes-editor__field">
                            <label for="nama_bumdes">Nama BUMDes <span>*</span></label>
                            <input id="nama_bumdes" name="nama_bumdes" type="text" maxlength="255" required value="{{ old('nama_bumdes', $profil->nama_bumdes) }}">
                            @error('nama_bumdes') <small class="form-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="bumdes-editor__field">
                            <label for="telepon">Nomor kontak WhatsApp resmi</label>
                            <div class="bumdes-editor__phone">
                                <span>+62</span>
                                <input id="telepon" name="telepon" type="tel" maxlength="40" value="{{ old('telepon', $profil->telepon) }}" placeholder="812-3456-7890">
                            </div>
                            @error('telepon') <small class="form-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="bumdes-editor__field">
                            <label for="deskripsi">Deskripsi singkat</label>
                            <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="2000" placeholder="Ceritakan peran dan fokus BUMDes untuk masyarakat.">{{ old('deskripsi', $profil->deskripsi) }}</textarea>
                            <small class="bumdes-editor__hint">Ringkasan ini ditampilkan di bagian hero halaman publik.</small>
                            @error('deskripsi') <small class="form-error">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <aside class="bumdes-editor__visual">
                        <label class="bumdes-editor__label" for="foto">Foto BUMDes</label>
                        <div class="bumdes-editor__photo-preview" data-bumdes-photo-preview>
                            @if ($profil->foto)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($profil->foto) }}" alt="Foto {{ $profil->nama_bumdes }}">
                            @else
                                <span aria-hidden="true">Foto BUMDes belum diunggah</span>
                            @endif
                        </div>
                        <input id="foto" name="foto" type="file" accept="image/*">
                        <small class="bumdes-editor__hint">Pilih foto kegiatan atau suasana BUMDes. JPG, PNG, atau WebP; maksimal 5 MB.</small>
                        @error('foto') <small class="form-error">{{ $message }}</small> @enderror
                        <div class="bumdes-editor__metrics">
                            <strong>Ringkasan unit usaha</strong>
                            <div><span>Unit aktif</span><b>{{ $profil->unitBumdes->where('status', 'aktif')->count() }}</b></div>
                            <div><span>Total unit</span><b>{{ $profil->unitBumdes->count() }}</b></div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="bumdes-editor__panel" aria-labelledby="bumdes-background-title">
                <div class="bumdes-editor__panel-heading">
                    <span class="bumdes-editor__panel-icon bumdes-editor__panel-icon--blue" aria-hidden="true">▤</span>
                    <div>
                        <h2 id="bumdes-background-title">Latar belakang &amp; kontak</h2>
                        <p>Sejarah ringkas, alamat, dan email resmi yang ditampilkan sebagai informasi BUMDes.</p>
                    </div>
                    <span class="bumdes-editor__step">BAGIAN 02</span>
                </div>
                <div class="bumdes-editor__fields bumdes-editor__fields--two">
                    <div class="bumdes-editor__field bumdes-editor__field--full">
                        <label for="sejarah">Latar belakang / sejarah BUMDes</label>
                        <textarea id="sejarah" name="sejarah" rows="5" maxlength="10000" placeholder="Tuliskan sejarah dan latar belakang berdirinya BUMDes.">{{ old('sejarah', $profil->sejarah) }}</textarea>
                        @error('sejarah') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" maxlength="1000" placeholder="Alamat kantor atau unit layanan BUMDes.">{{ old('alamat', $profil->alamat) }}</textarea>
                        @error('alamat') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="email">Email resmi</label>
                        <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $profil->email) }}" placeholder="bumdes@sedatiagung.desa.id">
                        @error('email') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="bumdes-editor__subheading">
                    <strong>Data legalitas</strong>
                    <span>Isi berdasarkan dokumen resmi BUMDes.</span>
                </div>
                <div class="bumdes-editor__fields bumdes-editor__fields--two">
                    <div class="bumdes-editor__field">
                        <label for="legalitas-perdes-nomor">Nomor &amp; tahun Peraturan Desa</label>
                        <input id="legalitas-perdes-nomor" name="legalitas[perdes_nomor]" type="text" maxlength="255" value="{{ old('legalitas.perdes_nomor', $legalitas['perdes_nomor'] ?? '') }}">
                        @error('legalitas.perdes_nomor') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="legalitas-perdes-keterangan">Keterangan Peraturan Desa</label>
                        <input id="legalitas-perdes-keterangan" name="legalitas[perdes_keterangan]" type="text" maxlength="500" value="{{ old('legalitas.perdes_keterangan', $legalitas['perdes_keterangan'] ?? '') }}">
                        @error('legalitas.perdes_keterangan') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="legalitas-ahu-nomor">Nomor registrasi AHU</label>
                        <input id="legalitas-ahu-nomor" name="legalitas[ahu_nomor]" type="text" maxlength="255" value="{{ old('legalitas.ahu_nomor', $legalitas['ahu_nomor'] ?? '') }}">
                        @error('legalitas.ahu_nomor') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="legalitas-ahu-keterangan">Keterangan registrasi AHU</label>
                        <input id="legalitas-ahu-keterangan" name="legalitas[ahu_keterangan]" type="text" maxlength="500" value="{{ old('legalitas.ahu_keterangan', $legalitas['ahu_keterangan'] ?? '') }}">
                        @error('legalitas.ahu_keterangan') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="legalitas-tanggal-berdiri">Tanggal berdiri</label>
                        <input id="legalitas-tanggal-berdiri" name="legalitas[tanggal_berdiri]" type="date" value="{{ old('legalitas.tanggal_berdiri', $legalitas['tanggal_berdiri'] ?? '') }}">
                        @error('legalitas.tanggal_berdiri') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field">
                        <label for="legalitas-nib">Nomor Induk Berusaha (NIB)</label>
                        <input id="legalitas-nib" name="legalitas[nib]" type="text" maxlength="255" value="{{ old('legalitas.nib', $legalitas['nib'] ?? '') }}">
                        @error('legalitas.nib') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                </div>
            </section>

            <section class="bumdes-editor__panel" aria-labelledby="bumdes-vision-title">
                <div class="bumdes-editor__panel-heading">
                    <span class="bumdes-editor__panel-icon" aria-hidden="true">◎</span>
                    <div>
                        <h2 id="bumdes-vision-title">Visi &amp; misi</h2>
                        <p>Misi ditampilkan satu per baris pada halaman profil publik.</p>
                    </div>
                    <span class="bumdes-editor__step">BAGIAN 03</span>
                </div>
                <div class="bumdes-editor__fields">
                    <div class="bumdes-editor__field bumdes-editor__field--full">
                        <label for="visi">Visi BUMDes</label>
                        <textarea id="visi" name="visi" rows="3" maxlength="5000" placeholder="Tuliskan visi BUMDes.">{{ old('visi', $profil->visi) }}</textarea>
                        @error('visi') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <div class="bumdes-editor__field bumdes-editor__field--full">
                        <label for="misi">Daftar misi</label>
                        <textarea id="misi" name="misi" rows="5" maxlength="10000" placeholder="Satu misi pada setiap baris.">{{ old('misi', $profil->misi) }}</textarea>
                        <small class="bumdes-editor__hint">Setiap baris akan menjadi satu kartu misi.</small>
                        @error('misi') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                </div>
            </section>

            <section class="bumdes-editor__panel" aria-labelledby="bumdes-management-title">
                <div class="bumdes-editor__panel-heading">
                    <span class="bumdes-editor__panel-icon bumdes-editor__panel-icon--blue" aria-hidden="true">♙</span>
                    <div>
                        <h2 id="bumdes-management-title">Pengurus BUMDes</h2>
                        <p>Isi nama dan jabatan pengurus. Tekan tombol tambah untuk membuat kartu baru.</p>
                    </div>
                    <span class="bumdes-editor__step">BAGIAN 04</span>
                </div>
                <p class="bumdes-editor__accessible-help" id="pengurus-help">Kartu bisa diisi satu per satu. Nama dan jabatan wajib diisi bersama. Maksimal 20 pengurus.</p>
                <div class="bumdes-editor__managers" data-bumdes-managers data-next-index="{{ $nextManagerIndex }}" aria-describedby="pengurus-help" aria-live="polite">
                    @foreach ($pengurus as $index => $anggota)
                        @include('dashboard.partials.bumdes-manager-fields', ['anggota' => $anggota, 'index' => $index])
                    @endforeach
                </div>
                <button class="bumdes-editor__add bumdes-editor__add--manager" type="button" data-add-bumdes-manager @disabled(count($pengurus) >= 20)>+ Tambah pengurus</button>
                @error('pengurus') <small class="form-error">{{ $message }}</small> @enderror
                @foreach ($pengurus as $index => $anggota)
                    @error("pengurus.$index.nama") <small class="form-error">{{ $message }}</small> @enderror
                    @error("pengurus.$index.jabatan") <small class="form-error">{{ $message }}</small> @enderror
                @endforeach
            </section>

            <section class="bumdes-editor__panel" aria-labelledby="bumdes-units-title">
                <div class="bumdes-editor__panel-heading">
                    <span class="bumdes-editor__panel-icon bumdes-editor__panel-icon--green" aria-hidden="true">⌂</span>
                    <div>
                        <h2 id="bumdes-units-title">Unit usaha</h2>
                        <p>Kelola unit yang muncul pada katalog halaman profil. Maksimal 20 unit.</p>
                    </div>
                    <span class="bumdes-editor__step">BAGIAN 05</span>
                    <button class="bumdes-editor__add" type="button" data-add-bumdes-unit @disabled(count($unitRows) >= 20)>+ Tambah unit</button>
                </div>

                <div class="bumdes-editor__units" data-bumdes-units data-next-index="{{ count($unitRows) }}">
                    @foreach ($unitRows as $index => $unit)
                        @include('dashboard.partials.bumdes-unit-fields', ['unit' => $unit, 'index' => $index])
                    @endforeach
                </div>
                @error('unit') <small class="form-error">{{ $message }}</small> @enderror
            </section>

            <div class="bumdes-editor__footer">
                <span>Perubahan akan langsung ditampilkan di halaman publik.</span>
                <button class="admin-button admin-button--primary" type="submit">Simpan Profil BUMDes <span aria-hidden="true">↗</span></button>
            </div>
        </form>

        <template id="bumdes-unit-template">
            @include('dashboard.partials.bumdes-unit-fields', ['unit' => [], 'index' => '__INDEX__'])
        </template>
        <template id="bumdes-manager-template">
            @include('dashboard.partials.bumdes-manager-fields', ['anggota' => [], 'index' => '__INDEX__'])
        </template>
    </div>
@endsection
