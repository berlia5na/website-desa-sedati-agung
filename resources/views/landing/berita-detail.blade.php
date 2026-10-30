@extends('layouts.landing')

@section('title', $berita->judul . ' | Berita Desa Sedati Agung')

@section('content')
    <article class="news-detail">
        <a class="news-detail__back" href="{{ route('berita') }}">← Kembali ke semua berita</a>

        <header class="news-detail__header">
            <span class="news-detail__category">{{ $berita->kategori ?: 'Berita Desa' }}</span>
            <h1>{{ $berita->judul }}</h1>
            <div class="news-meta">
                <span>{{ $berita->created_at?->translatedFormat('d F Y') ?: 'Baru' }}</span>
                <span>•</span>
                <span>{{ $berita->author_name ?: 'Pemerintah Desa Sedati Agung' }}</span>
                <span>•</span>
                <span>{{ $berita->read_time ?? 4 }} menit baca</span>
            </div>
        </header>

        @if ($berita->gambar_url)
            <figure class="news-detail__image">
                <img src="{{ $berita->gambar_url }}" alt="Dokumentasi: {{ $berita->judul }}">
            </figure>
        @endif

        @if ($berita->excerpt)
            <p class="news-detail__excerpt">{{ $berita->excerpt }}</p>
        @endif

        <div class="news-detail__content">{{ $berita->isi }}</div>

        <footer class="news-detail__footer">
            <span>Diterbitkan oleh <strong>{{ $berita->author_name ?: 'Pemerintah Desa Sedati Agung' }}</strong></span>
            <a href="{{ route('berita') }}">Jelajahi berita lainnya <span aria-hidden="true">→</span></a>
        </footer>
    </article>

    @if ($beritaTerkait->isNotEmpty())
        <section class="news-related">
            <div class="news-related__heading">
                <h2>Berita lainnya</h2>
                <a href="{{ route('berita') }}">Semua berita <span aria-hidden="true">→</span></a>
            </div>
            <div class="news-related__grid">
                @foreach ($beritaTerkait as $item)
                    <a class="news-related-card" href="{{ route('berita.detail', $item->slug) }}">
                        @if ($item->gambar_url)<img src="{{ $item->gambar_url }}" alt="">@endif
                        <span>{{ $item->kategori ?: 'Berita Desa' }}</span>
                        <h3>{{ $item->judul }}</h3>
                        <small>{{ $item->created_at?->translatedFormat('d M Y') }}</small>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
