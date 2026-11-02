@extends('layouts.dashboard')

@section('title', 'Kalender & Agenda Kegiatan | Desa Sedati Agung')

@section('content')
    @php($formAgenda = $editingAgenda ?? new \App\Models\Agenda())
    @php($isEditing = isset($editingAgenda))
    @php($categoryOptions = ['Pemerintahan', 'Sosial Kemasyarakatan', 'Kesehatan', 'Ekonomi & BUMDes', 'Pendidikan', 'Keagamaan', 'Lainnya'])
    @php($activityOptions = ['Rapat / Musyawarah', 'Pelayanan masyarakat', 'Penyaluran bantuan', 'Kegiatan rutin', 'Program kerja', 'Lainnya'])
    @php($invitationOptions = ['Terbuka untuk umum', 'Undangan terbatas', 'Pendaftaran diperlukan'])
    <div class="agenda-admin">
        <header class="agenda-admin__heading">
            <div>
                <span class="agenda-admin__eyebrow">MANAJEMEN PUBLIKASI</span>
                <h1>Kalender &amp; Agenda Kegiatan</h1>
                <p>Kelola informasi jadwal kegiatan dan agenda resmi Desa Sedati Agung.</p>
            </div>
            <a class="agenda-admin__public-link" href="{{ route('agenda') }}" target="_blank" rel="noopener">
                <span aria-hidden="true">↗</span> Lihat halaman publik
            </a>
        </header>

        @if (session('status'))
            <div class="agenda-admin__notice" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="agenda-admin__error-summary" role="alert">
                Periksa kembali isian yang ditandai. Semua kolom wajib harus dilengkapi.
            </div>
        @endif

        <form class="agenda-admin__panel agenda-admin__form" method="POST"
            action="{{ $isEditing ? route('admin.agenda.update', $formAgenda) : route('admin.agenda.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div class="agenda-admin__panel-heading">
                <span class="agenda-admin__panel-icon" aria-hidden="true">▦</span>
                <div>
                    <h2>{{ $isEditing ? 'Ubah informasi agenda' : 'Buat agenda baru' }}</h2>
                    <p>Lengkapi detail kegiatan sebelum disimpan sebagai draft atau dipublikasikan.</p>
                </div>
                <span class="agenda-admin__step">{{ $isEditing ? 'EDIT AGENDA' : 'AGENDA RESMI' }}</span>
            </div>

            <section class="agenda-admin__section" aria-labelledby="agenda-main-info">
                <div class="agenda-admin__section-heading">
                    <span class="agenda-admin__section-number">01</span>
                    <div><h3 id="agenda-main-info">Informasi utama</h3><p>Judul dan informasi yang dilihat warga.</p></div>
                </div>
                <div class="agenda-admin__fields">
                    <div class="agenda-admin__field">
                        <label for="badge_kategori">Label agenda <span aria-hidden="true">*</span></label>
                        <input id="badge_kategori" name="badge_kategori" value="{{ old('badge_kategori', $formAgenda->badge_kategori ?: 'Agenda Resmi Terverifikasi') }}" maxlength="80" required>
                        @error('badge_kategori')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="judul">Judul agenda <span aria-hidden="true">*</span></label>
                        <input id="judul" name="judul" value="{{ old('judul', $formAgenda->judul) }}" maxlength="255" placeholder="Contoh: Musyawarah Desa" required>
                        @error('judul')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field agenda-admin__field--full">
                        <label for="deskripsi">Deskripsi kegiatan <span aria-hidden="true">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="5000" placeholder="Jelaskan tujuan, rangkaian, atau informasi penting kegiatan..." required>{{ old('deskripsi', $formAgenda->deskripsi) }}</textarea>
                        @error('deskripsi')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </section>

            <section class="agenda-admin__section" aria-labelledby="agenda-event-info">
                <div class="agenda-admin__section-heading">
                    <span class="agenda-admin__section-number agenda-admin__section-number--blue">02</span>
                    <div><h3 id="agenda-event-info">Informasi kegiatan</h3><p>Jadwal, kategori, dan sifat kegiatan.</p></div>
                </div>
                <div class="agenda-admin__fields agenda-admin__fields--three">
                    <div class="agenda-admin__field">
                        <label for="tanggal">Tanggal kegiatan <span aria-hidden="true">*</span></label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $formAgenda->tanggal?->format('Y-m-d')) }}" required>
                        @error('tanggal')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="waktu_mulai">Waktu mulai <span aria-hidden="true">*</span></label>
                        <input id="waktu_mulai" name="waktu_mulai" type="time" value="{{ old('waktu_mulai', $formAgenda->waktu_mulai ? substr($formAgenda->waktu_mulai, 0, 5) : '') }}" required>
                        @error('waktu_mulai')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="kategori">Kategori kegiatan <span aria-hidden="true">*</span></label>
                        <select id="kategori" name="kategori" required>
                            <option value="">Pilih kategori</option>
                            @if ($formAgenda->kategori && ! in_array($formAgenda->kategori, $categoryOptions, true))
                                <option value="{{ $formAgenda->kategori }}" @selected(old('kategori', $formAgenda->kategori) === $formAgenda->kategori)>{{ $formAgenda->kategori }}</option>
                            @endif
                            @foreach ($categoryOptions as $kategori)
                                <option value="{{ $kategori }}" @selected(old('kategori', $formAgenda->kategori) === $kategori)>{{ $kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="jenis_kegiatan">Jenis kegiatan <span aria-hidden="true">*</span></label>
                        <select id="jenis_kegiatan" name="jenis_kegiatan" required>
                            <option value="">Pilih jenis</option>
                            @if ($formAgenda->jenis_kegiatan && ! in_array($formAgenda->jenis_kegiatan, $activityOptions, true))
                                <option value="{{ $formAgenda->jenis_kegiatan }}" @selected(old('jenis_kegiatan', $formAgenda->jenis_kegiatan) === $formAgenda->jenis_kegiatan)>{{ $formAgenda->jenis_kegiatan }}</option>
                            @endif
                            @foreach ($activityOptions as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_kegiatan', $formAgenda->jenis_kegiatan) === $jenis)>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_kegiatan')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="jenis_undangan">Status undangan <span aria-hidden="true">*</span></label>
                        <select id="jenis_undangan" name="jenis_undangan" required>
                            <option value="">Pilih status undangan</option>
                            @if ($formAgenda->jenis_undangan && ! in_array($formAgenda->jenis_undangan, $invitationOptions, true))
                                <option value="{{ $formAgenda->jenis_undangan }}" @selected(old('jenis_undangan', $formAgenda->jenis_undangan) === $formAgenda->jenis_undangan)>{{ $formAgenda->jenis_undangan }}</option>
                            @endif
                            @foreach ($invitationOptions as $undangan)
                                <option value="{{ $undangan }}" @selected(old('jenis_undangan', $formAgenda->jenis_undangan) === $undangan)>{{ $undangan }}</option>
                            @endforeach
                        </select>
                        @error('jenis_undangan')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </section>

            <section class="agenda-admin__section" aria-labelledby="agenda-place-info">
                <div class="agenda-admin__section-heading">
                    <span class="agenda-admin__section-number agenda-admin__section-number--green">03</span>
                    <div><h3 id="agenda-place-info">Lokasi dan penyelenggara</h3><p>Informasi tempat kegiatan serta narahubung.</p></div>
                </div>
                <div class="agenda-admin__fields agenda-admin__fields--three">
                    <div class="agenda-admin__field">
                        <label for="lokasi">Lokasi kegiatan <span aria-hidden="true">*</span></label>
                        <input id="lokasi" name="lokasi" value="{{ old('lokasi', $formAgenda->lokasi) }}" maxlength="255" placeholder="Contoh: Balai Desa Sedati Agung" required>
                        @error('lokasi')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="penyelenggara">Penyelenggara <span aria-hidden="true">*</span></label>
                        <input id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', $formAgenda->penyelenggara) }}" maxlength="255" placeholder="Nama instansi atau kelompok" required>
                        @error('penyelenggara')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="agenda-admin__field">
                        <label for="pic">PIC / narahubung <span class="agenda-admin__optional">(opsional)</span></label>
                        <input id="pic" name="pic" value="{{ old('pic', $formAgenda->pic) }}" maxlength="255" placeholder="Nama penanggung jawab">
                        @error('pic')<small class="agenda-admin__field-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </section>

            <div class="agenda-admin__form-actions">
                <a class="agenda-admin__button agenda-admin__button--quiet" href="{{ route('admin.agenda') }}">Batal</a>
                <button class="agenda-admin__button agenda-admin__button--draft" type="submit" name="status" value="draft">Simpan sebagai Draft</button>
                <button class="agenda-admin__button agenda-admin__button--primary" type="submit" name="status" value="published">
                    {{ $isEditing ? 'Simpan & Publikasikan' : 'Simpan & Publikasikan ke Halaman Agenda Desa' }}
                </button>
            </div>
        </form>

        <section class="agenda-admin__list-section" aria-labelledby="agenda-list-heading">
            <div class="agenda-admin__list-heading">
                <div>
                    <span class="agenda-admin__eyebrow">JADWAL KEGIATAN</span>
                    <h2 id="agenda-list-heading">Daftar agenda</h2>
                    <p>Kelola agenda yang tersimpan dan pantau status publikasinya.</p>
                </div>
                <form class="agenda-admin__search" method="GET" action="{{ route('admin.agenda') }}">
                    <label class="sr-only" for="agenda-search">Cari agenda atau PIC</label>
                    <span aria-hidden="true">⌕</span>
                    <input id="agenda-search" type="search" name="q" value="{{ $keyword }}" placeholder="Cari agenda atau PIC...">
                    <button type="submit">Cari</button>
                </form>
            </div>

            @if ($agendas->isNotEmpty())
                <div class="agenda-admin__grid">
                    @foreach ($agendas as $agenda)
                        <article class="agenda-card">
                            <div class="agenda-card__topline">
                                <span class="agenda-card__date">
                                    <span aria-hidden="true">▦</span>
                                    {{ $agenda->tanggal?->translatedFormat('d M Y') ?? 'Tanggal menyusul' }}
                                </span>
                                <span @class(['agenda-card__status', 'agenda-card__status--draft' => $agenda->status === 'draft'])>
                                    {{ $agenda->status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                                </span>
                            </div>
                            <span class="agenda-card__badge">{{ $agenda->badge_kategori }}</span>
                            <h3>{{ $agenda->judul }}</h3>
                            <p class="agenda-card__description">{{ \Illuminate\Support\Str::limit($agenda->deskripsi, 140) }}</p>
                            <dl class="agenda-card__details">
                                <div><dt>Kategori</dt><dd>{{ $agenda->kategori }} · {{ $agenda->jenis_kegiatan ?: 'Jenis belum diatur' }}</dd></div>
                                <div><dt>Waktu</dt><dd>{{ $agenda->waktu_mulai ? substr($agenda->waktu_mulai, 0, 5).' WIB' : 'Waktu menyusul' }}</dd></div>
                                <div><dt>Lokasi</dt><dd>{{ $agenda->lokasi ?: 'Lokasi belum diatur' }}</dd></div>
                                <div><dt>Penyelenggara / PIC</dt><dd>{{ $agenda->penyelenggara ?: '-' }}@if ($agenda->pic) · {{ $agenda->pic }}@endif</dd></div>
                            </dl>
                            <div class="agenda-card__actions">
                                @if ($agenda->status === 'published')
                                    <a class="agenda-card__action" href="{{ route('agenda.detail', $agenda->id) }}" target="_blank" rel="noopener">Lihat detail <span aria-hidden="true">↗</span></a>
                                @else
                                    <a class="agenda-card__action" href="{{ route('admin.agenda.edit', $agenda) }}">Lihat detail</a>
                                @endif
                                <a class="agenda-card__action agenda-card__action--edit" href="{{ route('admin.agenda.edit', $agenda) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.agenda.delete', $agenda) }}" data-confirm="Hapus agenda “{{ $agenda->judul }}”? Tindakan ini tidak dapat dibatalkan.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="agenda-card__action agenda-card__action--delete" type="submit">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="agenda-admin__pagination">{{ $agendas->links() }}</div>
            @else
                <div class="agenda-admin__empty">
                    <span aria-hidden="true">▦</span>
                    <h3>{{ $keyword !== '' ? 'Agenda tidak ditemukan' : 'Belum ada agenda' }}</h3>
                    <p>{{ $keyword !== '' ? 'Coba kata kunci judul, nama PIC, atau penyelenggara lainnya.' : 'Agenda yang dibuat akan tampil di sini setelah disimpan.' }}</p>
                </div>
            @endif
        </section>
    </div>
@endsection
