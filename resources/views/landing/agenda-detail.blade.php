@extends('layouts.landing')

@section('title', $agenda->judul.' | Agenda Desa Sedati Agung')

@section('content')
    <div class="public-agenda public-agenda--detail">
        <a class="public-agenda__back-link" href="{{ route('agenda') }}">← Kembali ke Agenda Desa</a>
        <article class="public-agenda__detail-card">
            <span class="public-agenda__hero-badge"><span aria-hidden="true">▦</span> {{ $agenda->badge_kategori ?: $agenda->kategori ?: 'AGENDA DESA SEDATI AGUNG' }}</span>
            <div class="public-agenda__detail-date">
                <span aria-hidden="true">◷</span>
                {{ $agenda->tanggal?->translatedFormat('l, d F Y') ?? 'Tanggal menyusul' }}
                @if ($agenda->waktu_mulai)
                    · {{ substr($agenda->waktu_mulai, 0, 5) }} WIB
                    @if ($agenda->waktu_selesai)
                        – {{ substr($agenda->waktu_selesai, 0, 5) }}
                    @endif
                @endif
            </div>
            <h1>{{ $agenda->judul }}</h1>
            <p class="public-agenda__detail-description">{{ $agenda->deskripsi }}</p>
            <dl class="public-agenda__detail-info">
                <div><dt>Kategori</dt><dd>{{ $agenda->kategori ?: 'Agenda Desa' }}</dd></div>
                @if ($agenda->jenis_kegiatan)<div><dt>Jenis kegiatan</dt><dd>{{ $agenda->jenis_kegiatan }}</dd></div>@endif
                @if ($agenda->jenis_undangan)<div><dt>Undangan</dt><dd>{{ $agenda->jenis_undangan }}</dd></div>@endif
                <div><dt>Lokasi</dt><dd>{{ $agenda->lokasi ?: 'Informasi lokasi menyusul' }}</dd></div>
                <div><dt>Penyelenggara</dt><dd>{{ $agenda->penyelenggara ?: 'Pemerintah Desa Sedati Agung' }}</dd></div>
                @if ($agenda->pic)<div><dt>Narahubung</dt><dd>{{ $agenda->pic }}</dd></div>@endif
            </dl>
        </article>

        @if ($agendasTerkait->isNotEmpty())
            <section class="public-agenda__related">
                <div class="public-agenda__section-heading">
                    <span aria-hidden="true"></span>
                    <div><h2>Agenda mendatang lainnya</h2><p>Jadwal kegiatan lain yang mungkin ingin Anda ikuti.</p></div>
                </div>
                <div class="public-agenda__related-grid">
                    @foreach ($agendasTerkait as $agendaTerkait)
                        <a class="public-agenda__related-card" href="{{ route('agenda.detail', $agendaTerkait->id) }}">
                            <span>{{ $agendaTerkait->tanggal?->translatedFormat('d M Y') }}</span>
                            <strong>{{ $agendaTerkait->judul }}</strong>
                            <small>{{ $agendaTerkait->lokasi ?: 'Lokasi menyusul' }} · Lihat detail →</small>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
