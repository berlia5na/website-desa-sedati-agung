@extends('layouts.landing')

@section('title', 'Agenda Desa | Desa Sedati Agung')

@section('content')
    <div class="public-agenda">
        <section class="public-agenda__hero">
            <span class="public-agenda__hero-badge"><span aria-hidden="true">▦</span> AGENDA DESA SEDATI AGUNG</span>
            <h1>Kalender &amp; Agenda Kegiatan</h1>
            <p>Pusat informasi jadwal musyawarah desa, program kerja BUMDes Makmur Sejahtera, layanan terpadu posyandu, penyaluran bantuan, serta jadwal kegiatan sosial kemasyarakatan Desa Sedati Agung.</p>
            <div class="public-agenda__hero-note">
                <span aria-hidden="true">✓</span>
                <span>Informasi agenda resmi yang diperbarui langsung oleh Pemerintah Desa.</span>
            </div>
        </section>

        <section class="public-agenda__spotlight-section" aria-labelledby="agenda-spotlight-heading">
            <div class="public-agenda__section-heading public-agenda__section-heading--brown">
                <span aria-hidden="true"></span>
                <div>
                    <h2 id="agenda-spotlight-heading">Spotlight Agenda Terdekat</h2>
                    <p>Jadwal kegiatan resmi yang paling dekat dengan hari ini.</p>
                </div>
            </div>

            @if ($spotlight)
                <article class="public-agenda__spotlight">
                    <div class="public-agenda__spotlight-date" aria-label="{{ $spotlight->tanggal->translatedFormat('d F Y') }}">
                        <span>{{ $spotlight->tanggal->translatedFormat('M Y') }}</span>
                        <strong>{{ $spotlight->tanggal->format('d') }}</strong>
                        <small>{{ $spotlight->tanggal->translatedFormat('l') }}</small>
                    </div>
                    <div class="public-agenda__spotlight-content">
                        <div class="public-agenda__badges">
                            <span class="public-agenda__badge">{{ $spotlight->kategori ?: 'Agenda Desa' }}</span>
                            @if ($spotlight->jenis_undangan)
                                <span class="public-agenda__badge public-agenda__badge--status">{{ $spotlight->jenis_undangan }}</span>
                            @endif
                        </div>
                        <h3>{{ $spotlight->judul }}</h3>
                        <p class="public-agenda__spotlight-description">{{ \Illuminate\Support\Str::limit($spotlight->deskripsi, 360) }}</p>
                        <dl class="public-agenda__spotlight-details">
                            <div>
                                <dt><span aria-hidden="true">◷</span> Waktu</dt>
                                <dd>{{ $spotlight->waktu_mulai ? substr($spotlight->waktu_mulai, 0, 5).' WIB' : 'Waktu menyusul' }}@if ($spotlight->waktu_selesai) – {{ substr($spotlight->waktu_selesai, 0, 5) }}@endif</dd>
                            </div>
                            <div>
                                <dt><span aria-hidden="true">⌖</span> Lokasi</dt>
                                <dd>{{ $spotlight->lokasi ?: 'Informasi lokasi menyusul' }}</dd>
                            </div>
                            <div>
                                <dt><span aria-hidden="true">◇</span> Penyelenggara</dt>
                                <dd>{{ $spotlight->penyelenggara ?: 'Pemerintah Desa Sedati Agung' }}</dd>
                            </div>
                        </dl>
                        <a class="public-agenda__detail-link" href="{{ route('agenda.detail', $spotlight->id) }}">
                            Lihat detail agenda <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @else
                <div class="public-agenda__empty public-agenda__empty--spotlight">
                    <span aria-hidden="true">◷</span>
                    <div>
                        <h3>Belum ada agenda terdekat</h3>
                        <p>Jadwal kegiatan mendatang akan ditampilkan di sini setelah diterbitkan oleh Pemerintah Desa.</p>
                    </div>
                </div>
            @endif
        </section>

        <section class="public-agenda__list-section" aria-labelledby="agenda-list-heading">
            <div class="public-agenda__list-heading">
                <div class="public-agenda__section-heading">
                    <span aria-hidden="true"></span>
                    <div>
                        <h2 id="agenda-list-heading">Timeline &amp; Agenda Mendatang</h2>
                        <p>Jelajahi jadwal kegiatan yang akan berlangsung di desa.</p>
                    </div>
                </div>
                <form class="public-agenda__search" method="GET" action="{{ route('agenda') }}">
                    <label class="sr-only" for="public-agenda-search">Cari agenda desa</label>
                    <span aria-hidden="true">⌕</span>
                    <input id="public-agenda-search" type="search" name="q" value="{{ $keyword }}" maxlength="100" placeholder="Cari agenda, lokasi, atau kategori...">
                    <button type="submit">Cari</button>
                    @if ($keyword !== '')
                        <a href="{{ route('agenda') }}" aria-label="Hapus pencarian">×</a>
                    @endif
                </form>
            </div>

            @if ($agendas->isNotEmpty())
                <div class="public-agenda__grid">
                    @foreach ($agendas as $agenda)
                        <article class="public-agenda__card" id="agenda-{{ $agenda->id }}">
                            <div class="public-agenda__card-top">
                                <div class="public-agenda__date-tile" aria-label="{{ $agenda->tanggal->translatedFormat('d F Y') }}">
                                    <span>{{ $agenda->tanggal->translatedFormat('M') }}</span>
                                    <strong>{{ $agenda->tanggal->format('d') }}</strong>
                                </div>
                                <span class="public-agenda__badge public-agenda__badge--status">{{ $agenda->jenis_undangan ?: 'Agenda resmi' }}</span>
                            </div>
                            <span class="public-agenda__category">{{ $agenda->badge_kategori ?: $agenda->kategori ?: 'Agenda Desa' }}</span>
                            <h3><a href="{{ route('agenda.detail', $agenda->id) }}">{{ $agenda->judul }}</a></h3>
                            <p class="public-agenda__description">{{ \Illuminate\Support\Str::limit($agenda->deskripsi, 145) }}</p>
                            <dl class="public-agenda__card-details">
                                <div>
                                    <dt><span aria-hidden="true">◷</span></dt>
                                    <dd>{{ $agenda->waktu_mulai ? substr($agenda->waktu_mulai, 0, 5).' WIB' : 'Waktu menyusul' }} · {{ $agenda->jenis_kegiatan ?: $agenda->kategori }}</dd>
                                </div>
                                <div>
                                    <dt><span aria-hidden="true">⌖</span></dt>
                                    <dd>{{ $agenda->lokasi ?: 'Lokasi menyusul' }}</dd>
                                </div>
                            </dl>
                            <a class="public-agenda__card-link" href="{{ route('agenda.detail', $agenda->id) }}">
                                Lihat detail <span aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="public-agenda__pagination">{{ $agendas->links() }}</div>
            @else
                <div class="public-agenda__empty">
                    <span aria-hidden="true">⌕</span>
                    <h3>{{ $keyword !== '' ? 'Agenda tidak ditemukan' : 'Belum ada agenda mendatang' }}</h3>
                    <p>{{ $keyword !== '' ? 'Coba kata kunci lain, atau hapus pencarian untuk melihat semua jadwal.' : 'Agenda desa yang akan datang akan ditampilkan di sini setelah dipublikasikan.' }}</p>
                    @if ($keyword !== '')
                        <a class="public-agenda__detail-link" href="{{ route('agenda') }}">Tampilkan semua agenda</a>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
