@extends('layouts.dashboard')

@section('title', 'Edit Profil Desa | Desa Sedati Agung')

@section('content')
    <div class="village-profile">
        <div class="village-profile__toolbar">
            <div>
                <span class="village-profile__eyebrow">EDITOR HALAMAN WEBSITE</span>
                <h1>Edit Profil Desa</h1>
                <p>Perubahan di sini akan ditampilkan pada halaman Profil Desa di website.</p>
            </div>
            <a class="village-profile__preview-link" href="{{ route('profil-desa') }}" target="_blank" rel="noopener">
                Lihat halaman website <span aria-hidden="true">↗</span>
            </a>
        </div>

        @if (session('status'))
            <div class="village-profile__status" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="village-profile__errors" role="alert">
                <strong>Periksa kembali data yang ditandai.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="village-profile__form" method="POST" action="{{ route('admin.profil-desa.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="site-editor-hero" aria-label="Bagian pembuka halaman Profil Desa">
                <div class="site-editor-hero__content">
                    <span class="site-editor-badge">BAGIAN PEMBUKA WEBSITE</span>
                    <div class="village-profile__fields">
                        <div class="village-profile__field">
                            <label for="nama_desa">Nama desa</label>
                            <input id="nama_desa" name="nama_desa" type="text" maxlength="255" required value="{{ old('nama_desa', $profil->nama_desa) }}">
                            @error('nama_desa') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="village-profile__field">
                            <label for="badge">Label profil</label>
                            <input id="badge" name="badge" type="text" maxlength="255" value="{{ old('badge', $profil->badge) }}">
                            @error('badge') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="village-profile__field">
                            <label for="judul">Judul utama</label>
                            <input id="judul" name="judul" type="text" maxlength="255" value="{{ old('judul', $profil->judul) }}">
                            @error('judul') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="village-profile__field">
                            <label for="deskripsi">Narasi pembuka</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="10000">{{ old('deskripsi', $profil->deskripsi) }}</textarea>
                            @error('deskripsi') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="site-editor-quote">
                            <div class="village-profile__field">
                                <label for="kutipan">Pesan kepala desa</label>
                                <textarea id="kutipan" name="kutipan" rows="3" maxlength="5000">{{ old('kutipan', $profil->kutipan) }}</textarea>
                                @error('kutipan') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="village-profile__fields village-profile__fields--two">
                                <div class="village-profile__field">
                                    <label for="nama_kades">Nama kepala desa</label>
                                    <input id="nama_kades" name="nama_kades" type="text" maxlength="255" value="{{ old('nama_kades', $profil->nama_kades) }}">
                                    @error('nama_kades') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="village-profile__field">
                                    <label for="jabatan_kades">Jabatan / periode</label>
                                    <input id="jabatan_kades" name="jabatan_kades" type="text" maxlength="255" value="{{ old('jabatan_kades', $profil->jabatan_kades) }}">
                                    @error('jabatan_kades') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="site-editor-image">
                    <span class="site-editor-image__label">FOTO UTAMA</span>
                    @if ($profil->foto)
                        <img class="site-editor-image__preview" src="{{ asset($profil->foto) }}" alt="Foto utama Profil Desa">
                    @else
                        <div class="site-editor-image__placeholder">Foto utama akan tampil di sini</div>
                    @endif
                    <div class="village-profile__field">
                        <label for="foto">Unggah foto utama</label>
                        <input id="foto" name="foto" type="file" accept="image/*">
                        @error('foto') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="village-profile__field">
                        <label for="caption_hero">Keterangan foto</label>
                        <input id="caption_hero" name="caption_hero" type="text" maxlength="255" value="{{ old('caption_hero', $profil->caption_hero) }}">
                        @error('caption_hero') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="village-profile__field">
                        <label for="subtitle">Wilayah administratif</label>
                        <input id="subtitle" name="subtitle" type="text" maxlength="255" value="{{ old('subtitle', $profil->subtitle) }}">
                        @error('subtitle') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="site-editor-section">
                <header class="site-editor-section__heading">
                    <span class="village-profile__eyebrow">ARAH PEMBANGUNAN</span>
                    <h2>Visi &amp; Misi Desa</h2>
                    <p>Kelola visi dan misi yang ditampilkan pada halaman website.</p>
                </header>
                <div class="site-editor-vision">
                    <article class="site-editor-card site-editor-card--vision">
                        <div class="village-profile__field">
                            <label for="visi_label">Label visi</label>
                            <input id="visi_label" name="visi_label" type="text" maxlength="255" value="{{ old('visi_label', $profil->visi_label) }}">
                            @error('visi_label') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="village-profile__field">
                            <label for="visi">Pernyataan visi</label>
                            <textarea id="visi" name="visi" rows="4" maxlength="5000">{{ old('visi', $profil->visi) }}</textarea>
                            @error('visi') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="village-profile__field">
                            <label for="visi_catatan">Catatan visi</label>
                            <textarea id="visi_catatan" name="visi_catatan" rows="2" maxlength="5000">{{ old('visi_catatan', $profil->visi_catatan) }}</textarea>
                            @error('visi_catatan') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                    </article>
                    <article class="site-editor-card site-editor-card--mission">
                        <div class="village-profile__field">
                            <label for="misi">Misi strategis <span class="village-profile__hint">Satu poin per baris.</span></label>
                            <textarea id="misi" name="misi" rows="9" maxlength="10000">{{ old('misi', $profil->misi) }}</textarea>
                            @error('misi') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                    </article>
                </div>
            </section>

            <section class="site-editor-section">
                <div class="site-editor-history">
                    <div class="site-editor-history__intro">
                        <span class="village-profile__eyebrow">JEJAK LANGKAH &amp; PERKEMBANGAN</span>
                        <h2>Sejarah Singkat<br>Desa Sedati Agung</h2>
                        <p>Bagian ini menyampaikan asal-usul dan perjalanan desa kepada pengunjung website.</p>
                        <div class="village-profile__field">
                            <label for="sejarah_sorotan">Sorotan sejarah</label>
                            <textarea id="sejarah_sorotan" name="sejarah_sorotan" rows="4" maxlength="5000">{{ old('sejarah_sorotan', $profil->sejarah_sorotan) }}</textarea>
                            @error('sejarah_sorotan') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="village-profile__field">
                        <label for="sejarah">Narasi sejarah <span class="village-profile__hint">Pisahkan paragraf dengan satu baris kosong.</span></label>
                        <textarea id="sejarah" name="sejarah" rows="13" maxlength="20000">{{ old('sejarah', $profil->sejarah) }}</textarea>
                        @error('sejarah') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="site-editor-section">
                <header class="site-editor-section__heading site-editor-section__heading--row">
                    <div>
                        <span class="village-profile__eyebrow">STATISTIK &amp; WILAYAH</span>
                        <h2>Data Umum &amp; Demografi Desa</h2>
                    </div>
                    <div class="village-profile__field site-editor-period">
                        <label for="periode_data">Periode data</label>
                        <input id="periode_data" name="periode_data" type="text" maxlength="255" value="{{ old('periode_data', $profil->periode_data) }}">
                        @error('periode_data') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </div>
                </header>
                <div class="site-editor-stats">
                    <article class="site-editor-stat">
                        <span aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.8a3.5 3.5 0 0 1 0 6.5M18 14a5.6 5.6 0 0 1 3.5 5"/></svg></span>
                        <label for="penduduk">Jumlah penduduk</label>
                        <input id="penduduk" name="penduduk" type="number" min="0" step="1" value="{{ old('penduduk', $profil->penduduk) }}">
                        <small>Jiwa</small>
                        @error('penduduk') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </article>
                    <article class="site-editor-stat">
                        <span aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15M15 6v15"/></svg></span>
                        <label for="luas_wilayah">Luas wilayah</label>
                        <input id="luas_wilayah" name="luas_wilayah" type="number" min="0" max="9999.99" step="0.01" value="{{ old('luas_wilayah', $profil->luas_wilayah) }}">
                        <small>km²</small>
                        @error('luas_wilayah') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </article>
                    <article class="site-editor-stat">
                        <span aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/><path d="M9 9h.01M15 9h.01"/></svg></span>
                        <label for="jumlah_dusun">Wilayah administratif</label>
                        <input id="jumlah_dusun" name="jumlah_dusun" type="number" min="0" max="255" step="1" value="{{ old('jumlah_dusun', $profil->jumlah_dusun) }}">
                        <small>Dusun</small>
                        @error('jumlah_dusun') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </article>
                    <article class="site-editor-stat">
                        <span aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-9M12 12c-5 0-8-3-8-8 5 0 8 3 8 8ZM12 16c0-5 3-8 8-8 0 5-3 8-8 8Z"/></svg></span>
                        <label for="lahan_tani">Lahan tani &amp; tambak</label>
                        <input id="lahan_tani" name="lahan_tani" type="number" min="0" step="1" value="{{ old('lahan_tani', $profil->lahan_tani) }}">
                        <small>Hektare</small>
                        @error('lahan_tani') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                    </article>
                </div>
            </section>

            <section class="site-editor-section">
                <header class="site-editor-section__heading">
                    <span class="village-profile__eyebrow">AKAR PEMERINTAHAN</span>
                    <h2>Struktur Perangkat Desa</h2>
                    <p>Tambah, ubah, atau keluarkan perangkat desa. Tidak dibatasi empat anggota.</p>
                </header>
                <div class="site-editor-team" data-official-list data-next-index="{{ $perangkat->keys()->max() + 1 }}">
                    @foreach ($perangkat as $index => $anggota)
                        <fieldset class="site-editor-member" data-official-card>
                            <input type="hidden" name="perangkat[{{ $index }}][id]" value="{{ old("perangkat.{$index}.id", $anggota->id) }}">
                            <input type="hidden" name="perangkat[{{ $index }}][hapus]" value="{{ old("perangkat.{$index}.hapus", 0) }}" data-official-delete-value>
                            <legend>Perangkat Desa {{ $loop->iteration }}</legend>
                            <button class="site-editor-member__remove" type="button" data-remove-official aria-label="Tandai perangkat untuk dihapus">Hapus</button>
                            <div class="site-editor-member__fields">
                                <div class="village-profile__field">
                                    <label for="perangkat-{{ $index }}-nama">Nama</label>
                                    <input id="perangkat-{{ $index }}-nama" name="perangkat[{{ $index }}][nama]" type="text" maxlength="255" value="{{ old("perangkat.{$index}.nama", $anggota->nama) }}">
                                    @error("perangkat.{$index}.nama") <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="village-profile__field">
                                    <label for="perangkat-{{ $index }}-jabatan">Jabatan</label>
                                    <input id="perangkat-{{ $index }}-jabatan" name="perangkat[{{ $index }}][jabatan]" type="text" maxlength="255" value="{{ old("perangkat.{$index}.jabatan", $anggota->jabatan) }}">
                                    @error("perangkat.{$index}.jabatan") <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="village-profile__field">
                                    <label for="perangkat-{{ $index }}-deskripsi">Ringkasan tugas</label>
                                    <input id="perangkat-{{ $index }}-deskripsi" name="perangkat[{{ $index }}][deskripsi]" type="text" maxlength="255" value="{{ old("perangkat.{$index}.deskripsi", $anggota->deskripsi) }}">
                                    @error("perangkat.{$index}.deskripsi") <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="village-profile__field site-editor-member__photo">
                                    <label for="perangkat-{{ $index }}-foto">Foto</label>
                                    <input id="perangkat-{{ $index }}-foto" name="perangkat[{{ $index }}][foto]" type="file" accept="image/*">
                                    @if ($anggota->foto)
                                        <span class="village-profile__current-file"><a href="{{ asset($anggota->foto) }}" target="_blank" rel="noopener">Foto tersimpan</a></span>
                                    @endif
                                    @error("perangkat.{$index}.foto") <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <p class="site-editor-member__deleted" data-official-deleted-notice hidden>Anggota ini akan dihapus setelah perubahan disimpan.</p>
                        </fieldset>
                    @endforeach
                </div>
                <button class="site-editor-add-member" type="button" data-add-official>＋ Tambah perangkat desa</button>

                <template id="official-member-template">
                    <fieldset class="site-editor-member" data-official-card>
                        <input type="hidden" name="perangkat[__INDEX__][id]" value="">
                        <input type="hidden" name="perangkat[__INDEX__][hapus]" value="0" data-official-delete-value>
                        <legend>Perangkat Desa baru</legend>
                        <button class="site-editor-member__remove" type="button" data-remove-official aria-label="Hapus form perangkat desa">Hapus</button>
                        <div class="site-editor-member__fields">
                            <div class="village-profile__field">
                                <label for="perangkat-__INDEX__-nama">Nama</label>
                                <input id="perangkat-__INDEX__-nama" name="perangkat[__INDEX__][nama]" type="text" maxlength="255">
                            </div>
                            <div class="village-profile__field">
                                <label for="perangkat-__INDEX__-jabatan">Jabatan</label>
                                <input id="perangkat-__INDEX__-jabatan" name="perangkat[__INDEX__][jabatan]" type="text" maxlength="255">
                            </div>
                            <div class="village-profile__field">
                                <label for="perangkat-__INDEX__-deskripsi">Ringkasan tugas</label>
                                <input id="perangkat-__INDEX__-deskripsi" name="perangkat[__INDEX__][deskripsi]" type="text" maxlength="255">
                            </div>
                            <div class="village-profile__field site-editor-member__photo">
                                <label for="perangkat-__INDEX__-foto">Foto</label>
                                <input id="perangkat-__INDEX__-foto" name="perangkat[__INDEX__][foto]" type="file" accept="image/*">
                            </div>
                        </div>
                    </fieldset>
                </template>
            </section>

            <section class="site-editor-section">
                <div class="site-editor-contact">
                    <div class="site-editor-contact__details">
                        <header class="site-editor-section__heading">
                            <span class="village-profile__eyebrow">LAYANAN MASYARAKAT</span>
                            <h2>Kantor Balai Desa &amp; Kontak Layanan</h2>
                            <p>Kontak resmi yang ditampilkan kepada pengunjung website.</p>
                        </header>
                        <div class="village-profile__fields">
                            <div class="village-profile__field">
                                <label for="alamat">Alamat kantor desa</label>
                                <textarea id="alamat" name="alamat" rows="3" maxlength="5000">{{ old('alamat', $profil->alamat) }}</textarea>
                                @error('alamat') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="village-profile__field">
                                <label for="jam_layanan">Jam operasional pelayanan</label>
                                <textarea id="jam_layanan" name="jam_layanan" rows="3" maxlength="255">{{ old('jam_layanan', $profil->jam_layanan) }}</textarea>
                                @error('jam_layanan') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="village-profile__fields village-profile__fields--two">
                                <div class="village-profile__field">
                                    <label for="telepon">Nomor telepon / WhatsApp</label>
                                    <input id="telepon" name="telepon" type="text" maxlength="255" value="{{ old('telepon', $profil->telepon) }}">
                                    @error('telepon') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="village-profile__field">
                                    <label for="email">Email resmi</label>
                                    <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $profil->email) }}">
                                    @error('email') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="village-profile__field">
                                <label for="website">Website</label>
                                <input id="website" name="website" type="text" maxlength="255" value="{{ old('website', $profil->website) }}">
                                @error('website') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="site-editor-contact__image">
                        <div class="village-profile__field">
                            <label for="lokasi_peta">Lokasi pada Google Maps</label>
                            <input id="lokasi_peta" name="lokasi_peta" type="text" maxlength="255" value="{{ old('lokasi_peta', $lokasiPeta) }}" data-map-location data-map-default="{{ $lokasiPeta }}">
                            <span class="village-profile__hint">Masukkan nama tempat atau alamat yang ingin ditunjukkan pada peta.</span>
                            @error('lokasi_peta') <span class="village-profile__field-error">{{ $message }}</span> @enderror
                        </div>
                        <iframe
                            class="site-editor-map"
                            src="https://maps.google.com/maps?q={{ urlencode($lokasiPeta) }}&amp;output=embed"
                            title="Pratinjau lokasi pada Google Maps"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            data-map-preview
                        ></iframe>
                        <a class="site-editor-map__link" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($lokasiPeta) }}" target="_blank" rel="noopener" data-map-link>
                            Buka lokasi di Google Maps <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>
            </section>

            <div class="village-profile__actions">
                <span>Data profil disimpan ke database dan ditampilkan pada website.</span>
                <button type="submit"><span aria-hidden="true">✓</span> Simpan &amp; publikasikan ke website</button>
            </div>
        </form>
    </div>
@endsection
