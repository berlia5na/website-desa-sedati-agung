@extends('layouts.dashboard')

@section('title', 'Kelola Beranda | Portal Admin Desa Sedati Agung')

@section('content')
    @php
        $kelengkapan = collect([
            'Judul hero' => filled($halaman->judul),
            'Subjudul hero' => filled($halaman->deskripsi),
            'Statistik desa' => $statistik->jumlah_penduduk !== null && $statistik->jumlah_umkm !== null,
            'Sambutan pimpinan' => filled($halaman->sambutan_nama) && filled($halaman->sambutan_teks),
            'Sorotan berita/agenda' => filled($halaman->sorotan),
            'Widget layanan' => ! $halaman->widget_tampil || (filled($halaman->widget_judul) && filled($halaman->widget_teks)),
        ]);
    @endphp

    <form id="delete-sambutan-form" method="POST" action="{{ route('admin.beranda.sambutan.delete') }}">
        @csrf
        @method('DELETE')
    </form>

    <form id="home-editor-form" class="home-editor" method="POST" action="{{ route('admin.beranda.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="home-editor__heading">
            <div>
                <div class="admin-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span>Kelola Beranda</span></div>
                <h1>Kelola Halaman Beranda <span>(Landing Page)</span></h1>
                <p>Atur konten utama yang tampil pada website Desa Sedati Agung.</p>
            </div>
            <div class="home-editor__actions">
                <button class="admin-button admin-button--secondary" type="reset">Batal Perubahan</button>
                <button class="admin-button admin-button--primary" type="submit">Simpan &amp; Publikasikan ke Web <span aria-hidden="true">↗</span></button>
            </div>
        </div>

        @if (session('status'))
            <p class="admin-alert admin-alert--success" role="status"><span aria-hidden="true">✓</span>{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error" role="alert">
                <strong>Periksa kembali isian berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="home-editor__layout">
            <div class="home-editor__main">
                <div class="admin-tabs" role="tablist" aria-label="Bagian pengaturan halaman beranda">
                    <button class="admin-tab is-active" id="tab-hero" type="button" role="tab" aria-selected="true" aria-controls="panel-hero" data-admin-tab="hero">Hero &amp; Banner Utama</button>
                    <button class="admin-tab" id="tab-statistics" type="button" role="tab" aria-selected="false" aria-controls="panel-statistics" data-admin-tab="statistics" tabindex="-1">Statistik Utama Desa</button>
                    <button class="admin-tab" id="tab-greeting" type="button" role="tab" aria-selected="false" aria-controls="panel-greeting" data-admin-tab="greeting" tabindex="-1">Sambutan Kepala Desa &amp; BUMDes</button>
                    <button class="admin-tab" id="tab-widgets" type="button" role="tab" aria-selected="false" aria-controls="panel-widgets" data-admin-tab="widgets" tabindex="-1">Widget &amp; Banner Layanan</button>
                </div>

                <section class="editor-panel" id="panel-hero" role="tabpanel" aria-labelledby="tab-hero" data-admin-panel="hero">
                    <div class="editor-panel__heading">
                        <span class="editor-panel__icon" aria-hidden="true">▧</span>
                        <div><h2>Hero &amp; Banner Utama</h2><p>Pesan pertama yang dilihat pengunjung saat membuka website desa.</p></div>
                    </div>

                    <div class="editor-fields">
                        <div class="editor-field editor-field--full">
                            <label for="judul">Judul utama <span class="field-required">* tampil di banner</span></label>
                            <input id="judul" name="judul" type="text" maxlength="255" value="{{ old('judul', $halaman->judul) }}" placeholder="Contoh: Membangun Desa Sedati Agung" data-preview-source="hero-title" data-preview-default="Judul utama halaman beranda">
                            @error('judul') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field editor-field--full">
                            <label for="deskripsi">Subjudul</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="2000" placeholder="Tulis deskripsi singkat yang mendukung judul utama." data-preview-source="hero-subtitle" data-preview-default="Tambahkan subjudul singkat tentang Desa Sedati Agung.">{{ old('deskripsi', $halaman->deskripsi) }}</textarea>
                            @error('deskripsi') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field editor-field--full">
                            <label for="hero_image">Gambar banner</label>
                            <label class="upload-area" for="hero_image">
                                <span class="upload-area__icon" aria-hidden="true">↑</span>
                                <span><strong>Pilih gambar banner</strong><small>Format JPG, PNG, atau WebP · Maksimal 5 MB</small></span>
                                <span class="upload-area__button">Pilih file</span>
                            </label>
                            <input class="visually-hidden" id="hero_image" name="hero_image" type="file" accept="image/*">
                            @if ($halaman->gambar)
                                <span class="editor-field__hint">Gambar aktif: {{ basename($halaman->gambar) }}. Unggah file baru untuk menggantinya.</span>
                            @endif
                            @error('hero_image') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field">
                            <label for="hero_button_label">Label tombol utama</label>
                            <input id="hero_button_label" name="hero_button_label" type="text" maxlength="80" value="{{ old('hero_button_label', $halaman->hero_button_label) }}" placeholder="Jelajahi Profil Desa" data-preview-source="hero-button" data-preview-default="Jelajahi Profil Desa">
                            @error('hero_button_label') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field">
                            <label for="hero_button_url">Tautan tujuan tombol</label>
                            <input id="hero_button_url" name="hero_button_url" type="text" maxlength="255" value="{{ old('hero_button_url', $halaman->hero_button_url) }}" placeholder="/profil-desa">
                            <span class="editor-field__hint">Gunakan URL internal, misalnya /profil-desa, atau tautan HTTPS.</span>
                            @error('hero_button_url') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <section class="editor-panel" id="panel-statistics" role="tabpanel" aria-labelledby="tab-statistics" data-admin-panel="statistics" hidden>
                    <div class="editor-panel__heading">
                        <span class="editor-panel__icon" aria-hidden="true">▦</span>
                        <div><h2>Statistik Utama Desa</h2><p>Perbarui angka yang tampil pada kartu statistik beranda.</p></div>
                    </div>

                    <div class="statistic-edit-grid">
                        <div class="statistic-edit-card">
                            <span class="statistic-edit-card__icon" aria-hidden="true">♙</span>
                            <label for="jumlah_penduduk">Jumlah penduduk</label>
                            <div class="statistic-edit-card__input"><input id="jumlah_penduduk" name="jumlah_penduduk" type="number" min="0" max="1000000000" value="{{ old('jumlah_penduduk', $statistik->jumlah_penduduk) }}" placeholder="0"><span>jiwa</span></div>
                            @error('jumlah_penduduk') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="statistic-edit-card">
                            <span class="statistic-edit-card__icon" aria-hidden="true">▥</span>
                            <label for="jumlah_usaha_bumdes">Jumlah usaha BUMDes</label>
                            <div class="statistic-edit-card__input"><input id="jumlah_usaha_bumdes" name="jumlah_usaha_bumdes" type="number" min="0" max="1000000000" value="{{ old('jumlah_usaha_bumdes', $statistik->jumlah_usaha_bumdes) }}" placeholder="0" required><span>unit</span></div>
                            @error('jumlah_usaha_bumdes') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="statistic-edit-card">
                            <span class="statistic-edit-card__icon" aria-hidden="true">▣</span>
                            <label for="jumlah_umkm">Jumlah UMKM</label>
                            <div class="statistic-edit-card__input"><input id="jumlah_umkm" name="jumlah_umkm" type="number" min="0" max="1000000000" value="{{ old('jumlah_umkm', $statistik->jumlah_umkm) }}" placeholder="0"><span>UMKM</span></div>
                            @error('jumlah_umkm') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <p class="editor-note"><span aria-hidden="true">i</span> Angka akan tersimpan ke data statistik desa dan tampil di halaman beranda setelah dipublikasikan.</p>
                </section>

                <section class="editor-panel" id="panel-greeting" role="tabpanel" aria-labelledby="tab-greeting" data-admin-panel="greeting" hidden>
                    <div class="editor-panel__heading">
                        <span class="editor-panel__icon" aria-hidden="true">❝</span>
                        <div><h2>Sambutan Kepala Desa &amp; BUMDes</h2><p>Perkenalkan pimpinan dan sampaikan pesan kepada warga.</p></div>
                    </div>

                    <div class="editor-fields">
                        <div class="editor-field editor-field--full">
                            <label for="sambutan_foto">Foto kepala desa</label>
                            <label class="upload-area" for="sambutan_foto">
                                <span class="upload-area__icon" aria-hidden="true">↑</span>
                                <span><strong>Unggah foto kepala desa</strong><small>Format JPG, PNG, atau WebP · Maksimal 5 MB</small></span>
                                <span class="upload-area__button">Pilih file</span>
                            </label>
                            <input class="visually-hidden" id="sambutan_foto" name="sambutan_foto" type="file" accept="image/*">
                            @if ($halaman->sambutan_foto)
                                <span class="editor-field__hint">Foto aktif: {{ basename($halaman->sambutan_foto) }}. Unggah file baru untuk menggantinya.</span>
                            @endif
                            @error('sambutan_foto') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field">
                            <label for="sambutan_nama">Nama kepala desa</label>
                            <input id="sambutan_nama" name="sambutan_nama" type="text" maxlength="255" value="{{ old('sambutan_nama', $halaman->sambutan_nama) }}" placeholder="Nama sesuai data resmi" data-preview-source="welcome-name" data-preview-default="Nama kepala desa">
                            @error('sambutan_nama') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field">
                            <label for="sambutan_jabatan">Jabatan</label>
                            <input id="sambutan_jabatan" name="sambutan_jabatan" type="text" maxlength="255" value="{{ old('sambutan_jabatan', $halaman->sambutan_jabatan) }}" placeholder="Kepala Desa Sedati Agung" data-preview-source="welcome-position" data-preview-default="Jabatan">
                            @error('sambutan_jabatan') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field editor-field--full">
                            <label for="sambutan_teks">Teks sambutan</label>
                            <textarea id="sambutan_teks" name="sambutan_teks" rows="6" maxlength="10000" placeholder="Tuliskan sambutan kepala desa untuk warga." data-preview-source="welcome-text" data-preview-default="Teks sambutan kepala desa akan tampil di sini.">{{ old('sambutan_teks', $halaman->sambutan_teks) }}</textarea>
                            @error('sambutan_teks') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <label class="admin-switch">
                        <input type="checkbox" name="sambutan_tampil" value="1" @checked(old('sambutan_tampil', $halaman->sambutan_tampil)) data-preview-toggle="welcome">
                        <span class="admin-switch__track" aria-hidden="true"></span>
                        <span><strong>Tampilkan sambutan di halaman beranda</strong><small>Jika dinonaktifkan, bagian sambutan tidak ditampilkan kepada pengunjung.</small></span>
                    </label>
                    <button class="admin-button admin-button--danger admin-greeting-delete" type="submit" form="delete-sambutan-form" data-delete-sambutan>Hapus sambutan dari beranda</button>
                </section>

                <section class="editor-panel" id="panel-widgets" role="tabpanel" aria-labelledby="tab-widgets" data-admin-panel="widgets" hidden>
                    <div class="editor-panel__heading">
                        <span class="editor-panel__icon" aria-hidden="true">▤</span>
                        <div><h2>Widget &amp; Banner Layanan</h2><p>Atur ajakan layanan desa dan konten pilihan yang ingin disorot.</p></div>
                    </div>

                    <div class="editor-subsection">
                        <h3>Banner layanan</h3>
                        <p>Bagian ini hanya tampil jika diaktifkan dan dipublikasikan.</p>
                    </div>
                    <div class="editor-fields">
                        <div class="editor-field">
                            <label for="widget_judul">Judul banner layanan</label>
                            <input id="widget_judul" name="widget_judul" type="text" maxlength="255" value="{{ old('widget_judul', $halaman->widget_judul) }}" placeholder="Judul layanan">
                            @error('widget_judul') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field">
                            <label for="widget_tombol">Label tombol</label>
                            <input id="widget_tombol" name="widget_tombol" type="text" maxlength="80" value="{{ old('widget_tombol', $halaman->widget_tombol) }}" placeholder="Lihat layanan">
                            @error('widget_tombol') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field editor-field--full">
                            <label for="widget_teks">Deskripsi banner</label>
                            <textarea id="widget_teks" name="widget_teks" rows="3" maxlength="2000" placeholder="Informasi singkat tentang layanan desa.">{{ old('widget_teks', $halaman->widget_teks) }}</textarea>
                            @error('widget_teks') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="editor-field editor-field--full">
                            <label for="widget_tautan">Tautan tujuan</label>
                            <input id="widget_tautan" name="widget_tautan" type="text" maxlength="255" value="{{ old('widget_tautan', $halaman->widget_tautan) }}" placeholder="/kontak">
                            @error('widget_tautan') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <label class="admin-switch">
                        <input type="checkbox" name="widget_tampil" value="1" @checked(old('widget_tampil', $halaman->widget_tampil)) data-preview-toggle="widget">
                        <span class="admin-switch__track" aria-hidden="true"></span>
                        <span><strong>Tampilkan banner layanan di halaman beranda</strong><small>Pastikan konten banner sudah diisi sebelum diaktifkan.</small></span>
                    </label>

                    <div class="editor-subsection editor-subsection--separated">
                        <h3>Sorotan Berita &amp; Agenda</h3>
                        <p>Pilih hingga tiga berita atau agenda yang sudah dipublikasikan untuk ditampilkan di beranda.</p>
                    </div>
                    <div class="editor-field">
                        <label for="sorotan">Pilih konten sorotan <span class="field-required">Maks. 3 pilihan</span></label>
                        <select id="sorotan" name="sorotan[]" multiple size="7" aria-describedby="sorotan-hint">
                            @if ($beritaTersedia->isNotEmpty())
                                <optgroup label="Berita desa">
                                    @foreach ($beritaTersedia as $berita)
                                        <option value="berita:{{ $berita->id }}" @selected(in_array('berita:'.$berita->id, old('sorotan', $halaman->sorotan ?? []), true))>{{ $berita->judul }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if ($agendaTersedia->isNotEmpty())
                                <optgroup label="Agenda mendatang">
                                    @foreach ($agendaTersedia as $agenda)
                                        <option value="agenda:{{ $agenda->id }}" @selected(in_array('agenda:'.$agenda->id, old('sorotan', $halaman->sorotan ?? []), true))>{{ $agenda->tanggal?->format('d/m/Y') }} — {{ $agenda->judul }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <span class="editor-field__hint" id="sorotan-hint">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih beberapa item.</span>
                        <span class="form-error" data-spotlight-error hidden>Pilih maksimal tiga konten.</span>
                        @error('sorotan') <span class="form-error">{{ $message }}</span> @enderror
                        @if ($beritaTersedia->isEmpty() && $agendaTersedia->isEmpty())
                            <span class="editor-field__hint">Belum ada berita atau agenda terpublikasi yang dapat dipilih.</span>
                        @endif
                    </div>
                </section>

                <div class="home-editor__footer">
                    <span><span class="publication-dot {{ $halaman->status === 'published' ? 'is-published' : '' }}"></span>{{ $halaman->status === 'published' ? 'Perubahan akan memperbarui halaman yang sudah terpublikasi.' : 'Konten belum pernah dipublikasikan.' }}</span>
                    <button class="admin-button admin-button--primary" type="submit">Simpan &amp; Publikasikan ke Web <span aria-hidden="true">↗</span></button>
                </div>
            </div>

            <aside class="home-editor__aside">
                <section class="preview-card">
                    <div class="preview-card__heading">
                        <div><span class="preview-live-dot"></span><strong>Live Visual Mockup Preview</strong></div>
                        <span class="preview-card__external" aria-hidden="true">↗</span>
                    </div>
                    <div class="preview-browser">
                        <div class="preview-browser__chrome"><i></i><i></i><i></i><span>sedatiagung.desa.id</span></div>
                        <div class="preview-site">
                            <div class="preview-site__nav"><b>SA</b><span>DESA SEDATI AGUNG</span><span>BERANDA　 PROFIL　 BERITA</span></div>
                            <div class="preview-site__hero">
                                <img src="{{ $halaman->gambar ? \Illuminate\Support\Facades\Storage::url($halaman->gambar) : '' }}" alt="" data-preview-image data-saved-image="{{ $halaman->gambar ? \Illuminate\Support\Facades\Storage::url($halaman->gambar) : '' }}" @if (! $halaman->gambar) hidden @endif>
                                <span>WEBSITE RESMI DESA SEDATI AGUNG</span>
                                <strong data-preview="hero-title">{{ $halaman->judul ?: 'Judul utama halaman beranda' }}</strong>
                                <small data-preview="hero-subtitle">{{ $halaman->deskripsi ?: 'Tambahkan subjudul singkat tentang Desa Sedati Agung.' }}</small>
                                <i data-preview="hero-button">{{ $halaman->hero_button_label ?: 'Jelajahi Profil Desa' }}</i>
                            </div>
                            <div class="preview-site__stats">
                                <span><b data-preview-stat="jumlah_penduduk">{{ $statistik->jumlah_penduduk !== null ? number_format($statistik->jumlah_penduduk, 0, ',', '.') : '—' }}</b><small>PENDUDUK</small></span>
                                <span><b data-preview-stat="jumlah_usaha_bumdes">{{ number_format($statistik->jumlah_usaha_bumdes, 0, ',', '.') }}</b><small>UNIT BUMDES</small></span>
                                <span><b data-preview-stat="jumlah_umkm">{{ $statistik->jumlah_umkm !== null ? number_format($statistik->jumlah_umkm, 0, ',', '.') : '—' }}</b><small>UMKM</small></span>
                            </div>
                            <div class="preview-site__welcome" data-preview-visibility="welcome" @if (! $halaman->sambutan_tampil) hidden @endif>
                                <span class="preview-site__welcome-photo">
                                    <img src="{{ $halaman->sambutan_foto ? \Illuminate\Support\Facades\Storage::url($halaman->sambutan_foto) : '' }}" alt="" data-preview-welcome-photo data-saved-image="{{ $halaman->sambutan_foto ? \Illuminate\Support\Facades\Storage::url($halaman->sambutan_foto) : '' }}" @if (! $halaman->sambutan_foto) hidden @endif>
                                    <b @if ($halaman->sambutan_foto) hidden @endif>SA</b>
                                </span>
                                <span><b data-preview="welcome-name">{{ $halaman->sambutan_nama ?: 'Nama kepala desa' }}</b><small data-preview="welcome-position">{{ $halaman->sambutan_jabatan ?: 'Jabatan' }}</small><small data-preview="welcome-text">{{ $halaman->sambutan_teks ?: 'Teks sambutan kepala desa akan tampil di sini.' }}</small></span>
                            </div>
                            <div class="preview-site__spotlights" data-preview-spotlights>
                                @forelse (($halaman->sorotan ?? []) as $item)
                                    @php
                                        [$jenis, $id] = explode(':', $item, 2);
                                        $judulSorotan = $jenis === 'berita'
                                            ? $beritaTersedia->firstWhere('id', $id)?->judul
                                            : $agendaTersedia->firstWhere('id', $id)?->judul;
                                    @endphp
                                    @if ($judulSorotan)
                                        <span class="preview-spotlight">{{ $judulSorotan }}</span>
                                    @endif
                                @empty
                                    <span class="preview-empty">Belum ada sorotan dipilih</span>
                                @endforelse
                            </div>
                            <div class="preview-site__widget" data-preview-visibility="widget" @if (! $halaman->widget_tampil) hidden @endif>
                                <b data-preview="widget-title">{{ $halaman->widget_judul ?: 'Banner layanan desa' }}</b>
                                <small data-preview="widget-text">{{ $halaman->widget_teks ?: 'Deskripsi widget akan tampil di sini.' }}</small>
                            </div>
                        </div>
                    </div>
                    <p class="preview-caption">Pratinjau mockup mengikuti perubahan form sebelum dipublikasikan.</p>
                </section>

                <section class="completion-card">
                    <div class="aside-section__heading"><h2>Kelengkapan Konten</h2><span>{{ $kelengkapan->filter()->count() }}/{{ $kelengkapan->count() }}</span></div>
                    <div class="completion-meter"><span style="width: {{ round($kelengkapan->filter()->count() / max($kelengkapan->count(), 1) * 100) }}%"></span></div>
                    <ul class="completion-list">
                        @foreach ($kelengkapan as $label => $lengkap)
                            <li><span class="completion-check {{ $lengkap ? 'is-complete' : '' }}" aria-hidden="true">{{ $lengkap ? '✓' : '·' }}</span>{{ $label }}</li>
                        @endforeach
                    </ul>
                </section>

                <section class="history-card">
                    <div class="aside-section__heading"><h2>Riwayat Publikasi</h2><span aria-hidden="true">◷</span></div>
                    @if ($halaman->published_at)
                        <div class="history-entry">
                            <span class="history-entry__icon" aria-hidden="true">↗</span>
                            <span><strong>Halaman dipublikasikan</strong><small>{{ $halaman->published_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</small><small>Oleh {{ $halaman->publisher?->name ?? 'Administrator' }}</small></span>
                        </div>
                    @else
                        <p class="history-empty">Belum ada riwayat publikasi. Publikasikan perubahan untuk mencatat aktivitas pertama.</p>
                    @endif
                </section>
            </aside>
        </div>
    </form>
@endsection
