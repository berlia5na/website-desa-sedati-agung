@extends('layouts.landing')

@section('title', 'Berita Kegiatan | Desa Sedati Agung')

@section('content')
    @php
        $selectedCategory = request()->input('kategori', 'semua');
        $categoryQuery = request()->except(['kategori', 'page']);
        $searchQuery = request()->input('q');
    @endphp

    <div class="news-page">
        <section class="news-page__header">
            <div class="news-page__intro">
                <span class="news-badge">
                    <span class="news-badge__icon" aria-hidden="true">✦</span>
                    {{ $newsPageSettings?->badge ?? 'Portal Warta Desa' }}
                </span>
                <h1>{{ $newsPageSettings?->title ?? 'Kabar & Publikasi Terkini' }}</h1>
                <p>
                    {{ $newsPageSettings?->description ?? 'Dapatkan informasi terbaru seputar berita, kegiatan, dan berbagai aktivitas Desa Sedati melalui publikasi terkini yang kami sajikan untuk masyarakat.' }}
                </p>
            </div>

            <form class="news-search" method="GET" action="{{ route('berita') }}">
                @foreach (request()->except(['q', 'page']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="news-search__field">
                    <span class="news-search__icon" aria-hidden="true">⌕</span>
                    <input type="search" name="q" value="{{ old('q', $searchQuery) }}" placeholder="Cari berita, agenda, atau kegiatan..." aria-label="Cari berita">
                    <button type="submit">Cari</button>
                </div>
            </form>
        </section>

        <div class="news-filter-bar">
            <div class="news-filter-bar__list" aria-label="Filter berita">
                <a href="{{ route('berita', $categoryQuery) }}" class="news-filter-pill {{ $selectedCategory === 'semua' ? 'is-active' : '' }}">Semua Berita</a>
                @foreach ($categories as $category)
                    @php $categoryParams = array_merge($categoryQuery, ['kategori' => $category]); @endphp
                    <a href="{{ route('berita', $categoryParams) }}" class="news-filter-pill {{ $selectedCategory === $category ? 'is-active' : '' }}">{{ $category }}</a>
                @endforeach
            </div>
            <span class="news-result-count">Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel</span>
        </div>

        @if ($featured)
            <article class="news-featured">
                <div class="news-featured__media">
                    <a class="news-featured__image-link" href="{{ route('berita.detail', $featured->slug) }}" aria-label="Baca berita: {{ $featured->judul }}">
                        @if ($featured->gambar)
                            <img src="{{ $featured->gambar_url }}" alt="">
                        @else
                            <span class="news-featured__placeholder" aria-hidden="true"></span>
                        @endif
                    </a>
                    <span class="news-featured__badge"><span aria-hidden="true">✦</span> Kabar Utama</span>
                </div>

                <div class="news-featured__body">
                    <div class="news-meta">
                        <span>{{ $featured->created_at ? $featured->created_at->translatedFormat('d M Y') : 'Baru' }}</span>
                        <span>•</span>
                        <span>{{ $featured->author_name ?: 'Pemerintah Desa' }}</span>
                        <span>•</span>
                        <span>{{ $featured->read_time ?? 4 }} menit baca</span>
                    </div>

                    <h2>{{ $featured->judul }}</h2>
                    <p>{{ $featured->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featured->isi), 180) }}</p>

                    <div class="news-featured__footer">
                        <div class="news-author">
                            <span class="news-author__avatar">{{ strtoupper(substr($featured->author_name ?: 'DSA', 0, 1)) }}</span>
                            <div>
                                <strong>{{ $featured->author_name ?: 'Pemerintah Desa' }}</strong>
                                <small>{{ $featured->created_at ? $featured->created_at->translatedFormat('d M Y') : 'Hari ini' }}</small>
                            </div>
                        </div>

                        <a class="news-button" href="{{ route('berita.detail', $featured->slug) }}">
                            Baca Selengkapnya
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </article>
        @endif

        <section class="news-section" aria-label="Artikel dan liputan terkini">
            <div class="news-section__header">
                <div>
                    <h2>Artikel &amp; Liputan Terkini</h2>
                    <p>Berita desa, program, dan kegiatan masyarakat yang perlu Anda tahu.</p>
                </div>

                <form class="news-sort" method="GET" action="{{ route('berita') }}">
                    @foreach (request()->except(['urut', 'page']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="urut">Urutkan:</label>
                    <select id="urut" name="urut" onchange="this.form.submit()">
                        <option value="terbaru" @selected(($sort ?? 'terbaru') === 'terbaru')>Terbaru</option>
                        <option value="terlama" @selected(($sort ?? 'terbaru') === 'terlama')>Terlama</option>
                        <option value="terpopuler" @selected(($sort ?? 'terbaru') === 'terpopuler')>Terpopuler</option>
                    </select>
                </form>
            </div>

            <div class="news-grid">
                @forelse ($articles as $article)
                    <article class="news-card">
                        <div class="news-card__image">
                            <a class="news-card__image-link" href="{{ route('berita.detail', $article->slug) }}" aria-label="Baca berita: {{ $article->judul }}">
                                @if ($article->gambar)
                                    <img src="{{ $article->gambar_url }}" alt="">
                                @else
                                    <span class="news-card__placeholder" aria-hidden="true"></span>
                                @endif
                            </a>
                            <span class="news-card__badge" style="background: {{ ($article->kategori ?? 'UMKM Binaan') === 'UMKM Binaan' ? '#2D6197' : '#8D4B00' }};">{{ $article->kategori ?: 'Berita' }}</span>
                        </div>

                        <div class="news-card__body">
                            <div class="news-meta news-meta--small">
                                <span>{{ $article->created_at ? $article->created_at->translatedFormat('d M Y') : 'Baru' }}</span>
                                <span>•</span>
                                <span>{{ $article->author_name ?: 'Pemerintah Desa' }}</span>
                                <span>•</span>
                                <span>{{ $article->read_time ?? 4 }}m</span>
                            </div>

                            <h3><a href="{{ route('berita.detail', $article->slug) }}">{{ $article->judul }}</a></h3>
                            <p>{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->isi), 120) }}</p>

                            <div class="news-card__footer">
                                <span>{{ $article->author_name ?: 'Pemerintah Desa' }}</span>
                                <a href="{{ route('berita.detail', $article->slug) }}">
                                    Baca Selengkapnya
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="news-empty-state">
                        Belum ada berita yang sesuai dengan filter yang Anda pilih.
                    </div>
                @endforelse
            </div>

            @if ($articles->hasPages())
                <div class="news-pagination">
                    {{ $articles->links() }}
                </div>
            @endif
        </section>

        <section class="gallery-section" aria-label="Dokumentasi kegiatan terkini">
            <div class="gallery-section__header">
                <div>
                    <span class="gallery-kicker">
                        <span aria-hidden="true">◌</span>
                        Dokumentasi
                    </span>
                    <h2>Galeri Kegiatan Terkini</h2>
                    <p>Jejak kegiatan masyarakat dan program desa dalam satu ruang visual.</p>
                </div>
                <a href="{{ route('galeri') }}" class="gallery-link">
                    Lihat Semua Galeri
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="gallery-grid">
                @foreach ($galleryPhotos as $photo)
                    <a class="gallery-item" href="{{ route('galeri') }}">
                        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy">
                        <span class="gallery-item__overlay"><span>{{ $photo->title }}</span></span>
                    </a>
                @endforeach
                @forelse ($galleryItems as $galleryItem)
                    <a class="gallery-item" href="{{ route('berita.detail', $galleryItem->slug) }}">
                        <img src="{{ $galleryItem->gambar_url }}" alt="{{ $galleryItem->judul }}">
                        <span class="gallery-item__overlay"><span>{{ $galleryItem->judul }}</span></span>
                    </a>
                @empty
                    @if ($galleryPhotos->isEmpty())
                    <p class="gallery-empty">Foto kegiatan yang diunggah bersama berita terpublikasi akan tampil di galeri.</p>
                    @endif
                @endforelse
            </div>
        </section>
    </div>
@endsection
