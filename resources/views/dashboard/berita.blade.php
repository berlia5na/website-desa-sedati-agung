@extends('layouts.dashboard')

@section('title', 'Berita & Kegiatan | Portal Admin Desa Sedati Agung')

@section('content')
    <section class="news-admin">
        <div class="news-admin__heading">
            <div>
                <div class="admin-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><span>Berita &amp; Kegiatan</span></div>
                <h1>Berita &amp; Kegiatan</h1>
                <p>Bagikan dokumentasi dan informasi kegiatan desa. Berita berstatus publik akan langsung tampil di website.</p>
            </div>
            <div class="news-admin__actions">
                <a class="admin-button admin-button--secondary" href="{{ route('berita') }}" target="_blank" rel="noopener">Lihat halaman publik <span aria-hidden="true">↗</span></a>
                <a class="admin-button admin-button--primary" href="{{ route('admin.berita.create') }}"><span aria-hidden="true">＋</span> Tulis berita</a>
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

        <section class="news-admin__panel">
            <div class="news-admin__section-heading">
                <div>
                    <h2>Header halaman publik</h2>
                    <p>Ubah label, judul, dan pengantar yang tampil di bagian atas halaman Berita Kegiatan.</p>
                </div>
            </div>
            <form class="news-admin__header-form" method="POST" action="{{ route('admin.berita.header.update') }}">
                @csrf
                @method('PUT')
                <label>
                    <span>Label kecil</span>
                    <input type="text" name="badge" maxlength="80" value="{{ old('badge', $newsPageSettings->badge) }}" required>
                </label>
                <label>
                    <span>Judul halaman</span>
                    <input type="text" name="title" maxlength="255" value="{{ old('title', $newsPageSettings->title) }}" required>
                </label>
                <label class="news-admin__header-description">
                    <span>Deskripsi</span>
                    <textarea name="description" rows="3" maxlength="1000" required>{{ old('description', $newsPageSettings->description) }}</textarea>
                </label>
                <button class="admin-button admin-button--primary" type="submit">Simpan header</button>
            </form>
        </section>

        <section class="news-admin__panel">
            <div class="news-admin__section-heading">
                <div>
                    <h2>Galeri kegiatan</h2>
                    <p>Foto yang ditambahkan akan langsung tampil di halaman publik Galeri.</p>
                </div>
                <a class="admin-button admin-button--secondary" href="{{ route('galeri') }}" target="_blank" rel="noopener">Lihat galeri <span aria-hidden="true">↗</span></a>
            </div>
            <div class="news-admin__gallery-grid">
                <form class="news-admin-gallery-add-card" method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data">
                    @csrf
                    <span class="news-admin-gallery-add-card__icon" aria-hidden="true">＋</span>
                    <strong>Tambah foto kegiatan</strong>
                    <small>Tambahkan dokumentasi baru ke galeri publik.</small>
                    <label>
                        <span>Nama kegiatan</span>
                        <input type="text" name="title" maxlength="255" value="{{ old('title') }}" placeholder="Contoh: Kerja bakti lingkungan" required>
                    </label>
                    <label>
                        <span>Keterangan <small>(opsional)</small></span>
                        <input type="text" name="caption" maxlength="500" value="{{ old('caption') }}" placeholder="Lokasi atau tanggal">
                    </label>
                    <label class="news-admin-gallery-add-card__file">
                        <span>Pilih foto <small>JPG, PNG, WebP · maks. 5 MB</small></span>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                    </label>
                    <button class="admin-button admin-button--primary" type="submit">Tambah ke galeri</button>
                </form>

                @foreach ($galleryPhotos as $photo)
                    <article class="news-admin-gallery-card">
                        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}">
                        <div class="news-admin-gallery-card__details">
                            <strong>{{ $photo->title }}</strong>
                            @if ($photo->caption)<small>{{ $photo->caption }}</small>@endif
                        </div>
                        <form method="POST" action="{{ route('admin.galeri.delete', $photo) }}" onsubmit="return confirm('Hapus foto ini dari galeri?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus foto</button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="news-admin__summary">
            <strong>{{ $berita->total() }}</strong>
            <span>berita dan kegiatan tersimpan</span>
        </div>

        @if ($berita->isEmpty())
            <div class="news-admin__empty">
                <span aria-hidden="true">▤</span>
                <h2>Belum ada berita kegiatan</h2>
                <p>Mulai dengan menambahkan dokumentasi kegiatan, pengumuman, atau kabar terbaru desa.</p>
                <a class="admin-button admin-button--primary" href="{{ route('admin.berita.create') }}">Buat berita pertama</a>
            </div>
        @else
            <div class="news-admin__list">
                @foreach ($berita as $item)
                    <article class="news-admin-card">
                        @if ($item->gambar_url)
                            <img class="news-admin-card__image" src="{{ $item->gambar_url }}" alt="">
                        @else
                            <div class="news-admin-card__placeholder" aria-hidden="true">▤</div>
                        @endif
                        <div class="news-admin-card__content">
                            <div class="news-admin-card__meta">
                                <span class="news-admin__status news-admin__status--{{ $item->status }}">{{ $item->status === 'published' ? 'Tayang' : 'Draft' }}</span>
                                <span>{{ $item->kategori ?: 'Tanpa kategori' }}</span>
                                <span>{{ $item->updated_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <h2>{{ $item->judul }}</h2>
                            <p>{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->isi), 140) }}</p>
                        </div>
                        <div class="news-admin-card__actions">
                            @if ($item->status === 'published')
                                <a href="{{ route('berita.detail', $item->slug) }}" target="_blank" rel="noopener">Lihat <span aria-hidden="true">↗</span></a>
                            @endif
                            <a href="{{ route('admin.berita.edit', $item) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.berita.delete', $item) }}" onsubmit="return confirm('Hapus berita ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="news-admin__pagination">{{ $berita->links() }}</div>
        @endif
    </section>
@endsection
