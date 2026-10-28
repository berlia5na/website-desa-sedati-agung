@extends('layouts.landing')

@section('title', 'Profil Desa | Desa Sedati Agung')

@section('content')
{{-- HERO --}}
    <section class="container">
        <div class="hero">
            <div class="hero__text">
                <span class="badge">{{ $profil->badge }}</span>
                <h1>{{ $profil->judul }}</h1>
                <p>{{ $profil->deskripsi }}</p>
                <div class="quote">
                    <p>“{{ $profil->kutipan }}”</p>
                    <strong>{{ $profil->nama_kades }}</strong>
                    <small>{{ $profil->jabatan_kades }}</small>
                </div>
            </div>
            <figure class="hero__img">
                <img src="{{ asset(($profil->foto ?: 'images/hero-desa.jpg')) }}" alt="{{ $profil->caption_hero }}" onerror="this.style.display='none'">
                <figcaption>
                    <span>{{ $profil->caption_hero }}</span>
                    <small>{{ $profil->subtitle }}</small>
                </figcaption>
            </figure>
        </div>
    </section>

    {{-- VISI & MISI --}}
    <section class="container section section--center">
        <h2 class="title">Visi &amp; Misi Desa</h2>
        <p class="sub">Landasan arah pembangunan pelayanan angga Pemerintah Desa Sedati Agung.</p>

        <div class="grid-2 vm">
            <article class="card">
                <div class="card__icon card__icon--blue">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/></svg>
                </div>
                <span class="eyebrow eyebrow--blue">{{ $profil->visi_label }}</span>
                <h3 class="visi">“{{ $profil->visi }}”</h3>
                <p class="muted">{{ $profil->visi_catatan }}</p>
            </article>

            <article class="card">
                <div class="card__icon card__icon--orange">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM8 9h8M8 13h8M8 17h5"/></svg>
                </div>
                <span class="eyebrow eyebrow--orange">MISI STRATEGIS</span>
                <ul class="misi">
                    @foreach (array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', (string) $profil->misi))) as $item)
                        <li><i></i><span>{{ $item }}</span></li>
                    @endforeach
                </ul>
            </article>
        </div>
    </section>

    {{-- SEJARAH --}}
    <section class="container section">
        <div class="sejarah">
            <div>
                <span class="eyebrow eyebrow--orange">JEJAK LANGKAH &amp; PERKEMBANGAN</span>
                <h2 class="title title--left">Sejarah Singkat<br>{{ $profil->nama_desa }}</h2>
                <p class="muted">Perjalanan panjang desa pesisir dari masa ke masa, sudah menjadi pusat kehidupan warga dan kemajuan.</p>
                <div class="highlight">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#c2670a" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                    <span>{{ $profil->sejarah_sorotan }}</span>
                </div>
            </div>
            <div class="sejarah__text">
                @foreach (preg_split("/\n{2,}/", $profil->sejarah) as $par)
                    <p>{{ $par }}</p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- DATA UMUM --}}
    <section class="container section">
        <div class="head-row">
            <div>
                <span class="eyebrow eyebrow--orange">STATISTIK &amp; WILAYAH</span>
                <h2 class="title title--left">Data Umum &amp; Demografi Desa</h2>
            </div>
            <span class="pill">{{ $profil->periode_data }}</span>
        </div>

        <div class="stats">
            <div class="stat">
                <div class="stat__top"><span class="stat__icon stat__icon--blue" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.8a3.5 3.5 0 0 1 0 6.5M18 14a5.6 5.6 0 0 1 3.5 5"/></svg></span><small>PENDUDUK</small></div>
                <strong>{{ number_format($profil->penduduk, 0, ',', '.') }}</strong>
                <span>Total Penduduk</span>
                <em>Jumlah jiwa terdaftar tahun ini</em>
            </div>
            <div class="stat">
                <div class="stat__top"><span class="stat__icon stat__icon--orange" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15M15 6v15"/></svg></span><small>WILAYAH</small></div>
                <strong>{{ number_format($profil->luas_wilayah, 2, ',', '.') }} km²</strong>
                <span>Luas Wilayah</span>
                <em>Tertuju seluruhnya perkotaan atas pesisir</em>
            </div>
            <div class="stat">
                <div class="stat__top"><span class="stat__icon stat__icon--blue" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/><path d="M9 9h.01M15 9h.01"/></svg></span><small>Administrasi</small></div>
                <strong>{{ $profil->jumlah_dusun }} Dusun</strong>
                <span>Wilayah Administrasi</span>
                <em>Dusun terpisah dan terkait</em>
            </div>
            <div class="stat">
                <div class="stat__top"><span class="stat__icon stat__icon--orange" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-9M12 12c-5 0-8-3-8-8 5 0 8 3 8 8ZM12 16c0-5 3-8 8-8 0 5-3 8-8 8Z"/></svg></span><small>Aset Desa</small></div>
                <strong>{{ $profil->lahan_tani }} Ha</strong>
                <span>Lahan Tani &amp; Tambak</span>
                <em>Struktur desa dilengkapi tambak</em>
            </div>
        </div>
    </section>

    {{-- STRUKTUR PERANGKAT --}}
    <section class="container section section--center">
        <span class="eyebrow eyebrow--orange">AKAR PEMERINTAHAN</span>
        <h2 class="title">Struktur Perangkat Desa</h2>
        <p class="sub">Pelayan terpercaya yang siap memberikan kebutuhan administrasi dan kebijakan desa.</p>

        <div class="team">
            @forelse ($perangkat as $p)
                <article class="member">
                    <div class="member__photo">
                        @if ($p->foto)
                            <img src="{{ asset($p->foto) }}" alt="{{ $p->nama }}">
                        @endif
                    </div>
                    <div class="member__body">
                        <h4>{{ $p->nama }}</h4>
                        <span>{{ $p->jabatan }}</span>
                        <p>{{ $p->deskripsi }}</p>
                    </div>
                </article>
            @empty
                @foreach ([
                    ['nama' => 'Nama Kepala Desa', 'jabatan' => 'Kepala Desa', 'deskripsi' => 'Penanggung jawab pemerintahan desa.'],
                    ['nama' => 'Nama Sekretaris Desa', 'jabatan' => 'Sekretaris Desa', 'deskripsi' => 'Koordinator administrasi dan pelayanan.'],
                    ['nama' => 'Nama Kepala Urusan', 'jabatan' => 'Kepala Urusan', 'deskripsi' => 'Pengelolaan administrasi dan pembangunan.'],
                    ['nama' => 'Nama Kepala Seksi', 'jabatan' => 'Kepala Seksi', 'deskripsi' => 'Pelayanan masyarakat dan pemberdayaan.'],
                ] as $contoh)
                    <article class="member">
                        <div class="member__photo"></div>
                        <div class="member__body">
                            <h4>{{ $contoh['nama'] }}</h4>
                            <span>{{ $contoh['jabatan'] }}</span>
                            <p>{{ $contoh['deskripsi'] }}</p>
                        </div>
                    </article>
                @endforeach
            @endforelse
        </div>
    </section>

    {{-- KANTOR & KONTAK --}}
    <section class="container section" id="kontak">
        <div class="kantor">
            <div>
                <span class="eyebrow eyebrow--orange">LAYANAN MASYARAKAT</span>
                <h2 class="title title--left">Kantor Balai Desa &amp; Kontak Layanan</h2>
                <p class="muted">Silakan kunjungi kantor kami jam kerja atau hubungi kami melalui salah satu kanal berikut.</p>

                <div class="info">
                    <span class="info__icon">📍</span>
                    <div><strong>Kantor Balai Desa</strong><p>{{ $profil->alamat }}</p></div>
                </div>
                <div class="info">
                    <span class="info__icon">🕒</span>
                    <div><strong>Jam Operasional Pelayanan</strong><p>{{ $profil->jam_layanan }}</p></div>
                </div>
                <div class="info info--wa">
                    <span class="info__icon info__icon--orange">💬</span>
                    <div><strong>WhatsApp &amp; Pengaduan Warga</strong><b>{{ $profil->telepon }}</b><p>Layanan administrasi pengaduan di bagian umum.</p></div>
                </div>
                @if ($profil->email)
                    <div class="info">
                        <span class="info__icon">✉</span>
                        <div><strong>Email Resmi</strong><p>{{ $profil->email }}</p></div>
                    </div>
                @endif
                @if ($profil->website)
                    <div class="info">
                        <span class="info__icon">↗</span>
                        <div><strong>Website</strong><p>{{ $profil->website }}</p></div>
                    </div>
                @endif
            </div>

            <figure class="kantor__map">
                <iframe
                    src="https://maps.google.com/maps?q={{ urlencode($lokasiPeta) }}&amp;output=embed"
                    title="Peta lokasi {{ $profil->nama_desa }}"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
                <figcaption>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($lokasiPeta) }}" target="_blank" rel="noopener">
                        Buka lokasi di Google Maps <span aria-hidden="true">↗</span>
                    </a>
                </figcaption>
            </figure>
        </div>
    </section>

@endsection
