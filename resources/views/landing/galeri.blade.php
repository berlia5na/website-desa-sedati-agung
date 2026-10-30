@extends('layouts.landing')

@section('title', 'Galeri Kegiatan | Desa Sedati Agung')

@section('content')
    <div class="gallery-page">
        <header class="gallery-page__header">
            <span class="news-badge"><span class="news-badge__icon" aria-hidden="true">◌</span> Dokumentasi Desa</span>
            <h1>Galeri Kegiatan</h1>
            <p>Foto kegiatan dan aktivitas masyarakat Desa Sedati Agung. Pengelola dapat menambahkan foto galeri secara langsung melalui Portal Admin.</p>
        </header>

        @if ($galleryPhotos->isNotEmpty() || $galleryItems->isNotEmpty())
            @if ($galleryPhotos->isNotEmpty())
                <section class="gallery-page__section" aria-label="Foto galeri kegiatan">
                    <h2>Dokumentasi Kegiatan</h2>
                    <div class="gallery-page__grid">
                        @foreach ($galleryPhotos as $photo)
                        <article class="gallery-page-card">
                            <img src="{{ $photo->image_url }}" alt="Dokumentasi kegiatan: {{ $photo->title }}" loading="lazy">
                            <span class="gallery-page-card__caption">
                                <small>{{ $photo->caption ?: 'Dokumentasi Desa' }}</small>
                                <strong>{{ $photo->title }}</strong>
                            </span>
                        </article>
                        @endforeach
                    </div>
                    @if ($galleryPhotos->hasPages())
                        <div class="news-pagination">{{ $galleryPhotos->links() }}</div>
                    @endif
                </section>
            @endif

            @if ($galleryItems->isNotEmpty())
                <section class="gallery-page__section" aria-label="Foto dari berita kegiatan">
                    <h2>Foto dari Berita Kegiatan</h2>
                    <div class="gallery-page__grid">
                        @foreach ($galleryItems as $item)
                            <a class="gallery-page-card" href="{{ route('berita.detail', $item->slug) }}">
                                <img src="{{ $item->gambar_url }}" alt="Dokumentasi kegiatan: {{ $item->judul }}" loading="lazy">
                                <span class="gallery-page-card__caption">
                                    <small>{{ $item->kategori ?: 'Kegiatan Desa' }}@if ($item->created_at) · {{ $item->created_at->translatedFormat('d M Y') }}@endif</small>
                                    <strong>{{ $item->judul }}</strong>
                                    <span class="gallery-page-card__link">Lihat berita <span aria-hidden="true">→</span></span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @else
            <div class="gallery-page__empty">
                <span aria-hidden="true">▧</span>
                <h2>Belum ada foto kegiatan</h2>
                <p>Foto kegiatan desa akan muncul di sini setelah pengelola mengunggah dan memublikasikan berita melalui fitur <strong>Berita &amp; Kegiatan</strong> di Portal Admin.</p>
                <a class="news-button" href="{{ route('berita') }}">Lihat berita kegiatan <span aria-hidden="true">→</span></a>
            </div>
        @endif
    </div>
@endsection
