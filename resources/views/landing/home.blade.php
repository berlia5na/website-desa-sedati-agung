@extends('layouts.landing')

@section('title', 'Beranda | Desa Sedati Agung')

@section('content')
    <section class="home-hero">
        @if ($halamanBeranda?->gambar)
            <img class="home-hero__banner" src="{{ \Illuminate\Support\Facades\Storage::url($halamanBeranda->gambar) }}" alt="" aria-hidden="true">
        @endif
        <div class="home-hero__content">
            <span class="eyebrow eyebrow--light"><span></span> Website Resmi Desa Sedati Agung</span>
            <h1>{{ $halamanBeranda?->judul ?: 'Membangun Kemandirian Ekonomi Agrikultur Desa Sedati Agung' }}</h1>
            <p>{{ $halamanBeranda?->deskripsi ?: 'Mengenal potensi desa, kegiatan masyarakat, dan unit usaha BUMDes Sedati Agung dalam satu ruang informasi.' }}</p>
            <div class="home-hero__actions">
                <a class="button button--gold" href="{{ $halamanBeranda?->hero_button_url ?: route('profil-desa') }}">{{ $halamanBeranda?->hero_button_label ?: 'Jelajahi Profil Desa' }} <span aria-hidden="true">→</span></a>
                <a class="button button--outline" href="{{ route('profil-bumdes') }}">Kenali BUMDes</a>
            </div>
        </div>
        <div class="home-hero__note">
            <span class="home-hero__note-icon" aria-hidden="true">✦</span>
            <span><strong>Desa Sedati Agung</strong><small>Kabupaten Sidoarjo, Jawa Timur</small></span>
        </div>
        <a class="home-hero__scroll" href="#sambutan"><span aria-hidden="true">↓</span> Jelajahi halaman</a>
    </section>

    <section class="home-statistics" aria-label="Statistik Desa Sedati Agung">
        <article class="home-statistics__item">
            <span class="home-statistics__label">Jumlah Penduduk</span>
            <strong>{{ $statistik->jumlah_penduduk !== null ? number_format($statistik->jumlah_penduduk, 0, ',', '.') : '—' }}</strong>
            <span class="home-statistics__unit">{{ $statistik->jumlah_penduduk !== null ? 'jiwa' : 'Belum diperbarui' }}</span>
        </article>
        <article class="home-statistics__item">
            <span class="home-statistics__label">Usaha BUMDes</span>
            <strong>{{ number_format($statistik->jumlah_usaha_bumdes, 0, ',', '.') }}</strong>
            <span class="home-statistics__unit">unit usaha</span>
        </article>
        <article class="home-statistics__item">
            <span class="home-statistics__label">Jumlah UMKM</span>
            <strong>{{ $statistik->jumlah_umkm !== null ? number_format($statistik->jumlah_umkm, 0, ',', '.') : '—' }}</strong>
            <span class="home-statistics__unit">{{ $statistik->jumlah_umkm !== null ? 'UMKM' : 'Belum diperbarui' }}</span>
        </article>
    </section>

    @if ($sambutanTampil)
        <section class="welcome-section section-wrap" id="sambutan">
            <div class="welcome-section__portrait">
                @if ($halamanBeranda?->sambutan_foto)
                    <img class="welcome-section__photo" src="{{ \Illuminate\Support\Facades\Storage::url($halamanBeranda->sambutan_foto) }}" alt="Foto {{ $halamanBeranda->sambutan_nama ?: 'kepala desa' }}">
                @else
                    <div class="portrait-placeholder" aria-hidden="true">
                        <span>SA</span>
                        <small>SEDATI AGUNG</small>
                    </div>
                @endif
                <div class="welcome-section__caption">
                    <span class="caption-mark" aria-hidden="true">“</span>
                    <div><strong>{{ $halamanBeranda?->sambutan_nama ?: 'Sambutan Pemimpin Desa & BUMDes' }}</strong><small>{{ $halamanBeranda?->sambutan_jabatan ?: 'Desa Sedati Agung' }}</small></div>
                </div>
            </div>

            <div class="welcome-section__body">
                <span class="eyebrow"><span></span> Sambutan Kepala Desa &amp; BUMDes</span>
                <h2>Sambutan Kepala Desa</h2>
                @if ($halamanBeranda?->sambutan_teks)
                    <p>{{ $halamanBeranda->sambutan_teks }}</p>
                @else
                    <p>Selamat datang di website resmi Desa Sedati Agung. Kami berkomitmen menghadirkan informasi desa yang terbuka, memperkuat potensi ekonomi lokal, dan mendorong pelayanan yang semakin dekat dengan masyarakat.</p>
                    <p>Melalui semangat gotong royong, pemerintah desa bersama masyarakat dan BUMDes terus mengembangkan potensi agrikultur serta unit usaha desa untuk kesejahteraan bersama.</p>
                @endif
                <a class="text-link" href="{{ route('profil-desa') }}">Selengkapnya tentang desa <span aria-hidden="true">→</span></a>

                <div class="transparency-card">
                    <div class="transparency-card__heading">
                        <span class="transparency-card__icon" aria-hidden="true">▤</span>
                        <strong>Transparansi Anggaran Desa</strong>
                        <span class="status-pill">Informasi publik</span>
                    </div>
                    <p>Rincian anggaran dan realisasi dapat ditampilkan di sini setelah data resmi tersedia.</p>
                    <span class="transparency-card__note">Informasi publik Desa Sedati Agung</span>
                </div>
            </div>
        </section>
    @endif

    @if ($sorotanTerkini->isNotEmpty())
        <section class="home-updates">
            <div class="section-wrap">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow"><span></span> Pilihan pengelola</span>
                        <h2>Sorotan Berita &amp; Agenda</h2>
                    </div>
                    <a class="text-link" href="{{ route('berita') }}">Jelajahi kabar desa <span aria-hidden="true">→</span></a>
                </div>

                <div class="home-news__grid">
                    @foreach ($sorotanTerkini as $sorotan)
                        @php($jenisBerita = $sorotan->jenis_sorotan === 'berita')
                        <a class="home-news-card" href="{{ route($jenisBerita ? 'berita.detail' : 'agenda', $jenisBerita ? $sorotan->slug : []) }}">
                            <span class="home-news-card__date">{{ $jenisBerita ? 'Berita desa' : 'Agenda desa' }}</span>
                            <h3>{{ $sorotan->judul }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($jenisBerita ? $sorotan->isi : $sorotan->deskripsi), 130) }}</p>
                            <span class="home-news-card__link">{{ $jenisBerita ? 'Baca berita' : 'Lihat agenda' }} <span aria-hidden="true">→</span></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <section class="home-updates">
        <div class="section-wrap">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><span></span> Kabar desa</span>
                    <h2>Berita Terkini</h2>
                </div>
                <a class="text-link" href="{{ route('berita') }}">Lihat semua berita <span aria-hidden="true">→</span></a>
            </div>

            <div class="home-news__grid">
                @forelse ($beritaTerkini as $berita)
                    <a class="home-news-card" href="{{ route('berita.detail', $berita->slug) }}">
                        <span class="home-news-card__date">{{ $berita->created_at->format('d/m/Y') }}</span>
                        <h3>{{ $berita->judul }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($berita->isi), 130) }}</p>
                        <span class="home-news-card__link">Baca berita <span aria-hidden="true">→</span></span>
                    </a>
                @empty
                    <p class="home-updates__empty">Belum ada berita terbaru. Silakan kunjungi kembali halaman berita desa.</p>
                @endforelse
            </div>
        </div>
        </section>

        <section class="home-agenda">
        <div class="section-wrap">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><span></span> Catat tanggalnya</span>
                    <h2>Agenda Terdekat</h2>
                </div>
                <a class="text-link" href="{{ route('agenda') }}">Lihat semua agenda <span aria-hidden="true">→</span></a>
            </div>

            @if ($agendaTerdekat)
                <a class="home-agenda-card" href="{{ route('agenda') }}">
                    <span class="home-agenda-card__date">
                        <strong>{{ $agendaTerdekat->tanggal->format('d') }}</strong>
                        <span>{{ $agendaTerdekat->tanggal->translatedFormat('M Y') }}</span>
                    </span>
                    <span class="home-agenda-card__details">
                        <strong>{{ $agendaTerdekat->judul }}</strong>
                        <span>
                            {{ $agendaTerdekat->waktu_mulai ? substr($agendaTerdekat->waktu_mulai, 0, 5) : 'Waktu menyusul' }}
                            @if ($agendaTerdekat->waktu_selesai)
                                – {{ substr($agendaTerdekat->waktu_selesai, 0, 5) }}
                            @endif
                            @if ($agendaTerdekat->lokasi)
                                <span aria-hidden="true"> · </span>{{ $agendaTerdekat->lokasi }}
                            @endif
                        </span>
                        @if ($agendaTerdekat->deskripsi)
                            <span>{{ \Illuminate\Support\Str::limit($agendaTerdekat->deskripsi, 150) }}</span>
                        @endif
                    </span>
                    <span class="home-agenda-card__arrow" aria-hidden="true">→</span>
                </a>
            @else
                <p class="home-updates__empty">Belum ada agenda mendatang. Silakan kunjungi kembali halaman agenda desa.</p>
            @endif
        </div>
        </section>
    @endif

    @if ($halamanBeranda?->widget_tampil)
        <section class="home-service-banner">
            <div class="section-wrap home-service-banner__inner">
                <div>
                    <span class="eyebrow eyebrow--light"><span></span> Layanan desa</span>
                    <h2>{{ $halamanBeranda->widget_judul }}</h2>
                    <p>{{ $halamanBeranda->widget_teks }}</p>
                </div>
                @if ($halamanBeranda->widget_tombol && $halamanBeranda->widget_tautan)
                    <a class="button button--gold" href="{{ $halamanBeranda->widget_tautan }}">{{ $halamanBeranda->widget_tombol }} <span aria-hidden="true">→</span></a>
                @endif
            </div>
        </section>
    @endif

    <section class="home-links">
        <div class="section-wrap">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><span></span> Temukan informasi</span>
                    <h2>Jelajahi Desa Sedati Agung</h2>
                </div>
                <p>Informasi desa, kabar terbaru, dan kegiatan masyarakat dalam satu tempat.</p>
            </div>
            <div class="home-links__grid">
                <a class="home-link-card" href="{{ route('profil-desa') }}">
                    <span class="home-link-card__number">01</span>
                    <span class="home-link-card__icon" aria-hidden="true">⌂</span>
                    <strong>Profil Desa</strong>
                    <span>Kenali sejarah, visi, dan potensi desa.</span>
                    <span class="home-link-card__arrow" aria-hidden="true">↗</span>
                </a>
                <a class="home-link-card" href="{{ route('profil-bumdes') }}">
                    <span class="home-link-card__number">02</span>
                    <span class="home-link-card__icon" aria-hidden="true">▥</span>
                    <strong>BUMDes</strong>
                    <span>Jelajahi unit usaha dan potensi ekonomi desa.</span>
                    <span class="home-link-card__arrow" aria-hidden="true">↗</span>
                </a>
                <a class="home-link-card" href="{{ route('berita') }}">
                    <span class="home-link-card__number">03</span>
                    <span class="home-link-card__icon" aria-hidden="true">▤</span>
                    <strong>Berita &amp; Kegiatan</strong>
                    <span>Ikuti kabar dan kegiatan terbaru desa.</span>
                    <span class="home-link-card__arrow" aria-hidden="true">↗</span>
                </a>
                <a class="home-link-card" href="{{ route('agenda') }}">
                    <span class="home-link-card__number">04</span>
                    <span class="home-link-card__icon" aria-hidden="true">▦</span>
                    <strong>Agenda Desa</strong>
                    <span>Lihat agenda dan kegiatan masyarakat.</span>
                    <span class="home-link-card__arrow" aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </section>
@endsection
