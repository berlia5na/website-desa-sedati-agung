@extends('layouts.landing')

@section('title', 'Profil BUMDES | Desa Sedati Agung')

@section('content')
    @php
        $unitUsaha = $profilBumdes?->unitBumdes ?? collect();
        $misi = $profilBumdes?->misi
            ? collect(preg_split('/\r\n|\r|\n/', trim($profilBumdes->misi)))->filter()
            : collect([
                'Mengembangkan potensi usaha lokal secara produktif dan berkelanjutan.',
                'Membuka ruang tumbuh bagi usaha warga dan pelaku UMKM desa.',
                'Menghadirkan layanan usaha yang bermanfaat dan mudah dijangkau masyarakat.',
                'Mengelola usaha secara profesional, transparan, dan bertanggung jawab.',
            ]);
        $visi = $profilBumdes?->visi ?: 'Menempatkan BUMDes sebagai penggerak perekonomian lokal yang adil, berakar pada kearifan desa, dan memberi manfaat bagi masyarakat.';
        $legalitas = $profilBumdes?->legalitas ?? [];
        $pengurus = collect($profilBumdes?->pengurus ?? [])
            ->filter(fn (array $anggota) => filled($anggota['nama'] ?? null) && filled($anggota['jabatan'] ?? null))
            ->values();
    @endphp

    <div class="bumdes-page">
        <section class="bumdes-hero" aria-labelledby="bumdes-title">
            <div class="bumdes-hero__content">
                <span class="bumdes-eyebrow"><span aria-hidden="true">✦</span> Badan Usaha Milik Desa</span>
                <h1 id="bumdes-title">{{ $profilBumdes?->nama_bumdes ?: 'BUMDes Sedati Agung' }}</h1>
                <p class="bumdes-hero__location">Desa Sedati Agung <span aria-hidden="true">·</span> Kabupaten Sidoarjo</p>
                <p class="bumdes-hero__description">
                    {{ $profilBumdes?->deskripsi ?: 'Membangun kemandirian ekonomi desa melalui pengelolaan potensi lokal dan usaha yang memberi manfaat bagi masyarakat Desa Sedati Agung.' }}
                </p>
                <div class="bumdes-hero__actions">
                    <a class="bumdes-button bumdes-button--primary" href="#unit-usaha">Jelajahi unit usaha <span aria-hidden="true">→</span></a>
                    <a class="bumdes-button bumdes-button--outline" href="#tentang-bumdes">Tentang BUMDes</a>
                </div>
                <div class="bumdes-hero__stats" aria-label="Ringkasan BUMDes">
                    <div>
                        <strong>{{ number_format($unitUsaha->count(), 0, ',', '.') }}</strong>
                        <span>Unit usaha aktif</span>
                    </div>
                    <div>
                        <strong>Desa</strong>
                        <span>Berbasis potensi lokal</span>
                    </div>
                    <div>
                        <strong>Bersama</strong>
                        <span>Tumbuh untuk warga</span>
                    </div>
                </div>
            </div>

            <div class="bumdes-hero__art">
                @if ($profilBumdes?->foto)
                    <figure class="bumdes-photo-card">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($profilBumdes->foto) }}" alt="Foto {{ $profilBumdes->nama_bumdes }}">
                        <figcaption>
                            <span class="bumdes-photo-card__badge">BUMDes Sedati Agung</span>
                            <strong>{{ $profilBumdes->nama_bumdes }}</strong>
                            <span>Bergerak bersama masyarakat desa</span>
                        </figcaption>
                    </figure>
                @else
                    <div class="bumdes-photo-card bumdes-photo-card--empty">
                        <span class="bumdes-photo-card__icon" aria-hidden="true">⌂</span>
                        <strong>Foto BUMDes</strong>
                        <span>Foto lembaga akan ditampilkan di sini.</span>
                    </div>
                @endif
            </div>
        </section>

        <section class="bumdes-section bumdes-legal" id="tentang-bumdes" aria-labelledby="bumdes-legal-title">
            <div class="bumdes-section__heading bumdes-section__heading--split">
                <div>
                    <span class="bumdes-section__eyebrow">MENGENAL LEMBAGA</span>
                    <h2 id="bumdes-legal-title">Latar Belakang &amp; Legalitas</h2>
                </div>
                <p>
                    {{ $profilBumdes?->sejarah ?: 'BUMDes menjadi wadah pengelolaan usaha desa untuk mengembangkan potensi lokal dan memperluas manfaat ekonomi bagi masyarakat.' }}
                </p>
            </div>
            <div class="bumdes-legal__grid">
                <article class="bumdes-info-card">
                    <span class="bumdes-info-card__icon" aria-hidden="true">▤</span>
                    <span class="bumdes-info-card__label">LANDASAN</span>
                    <h3>Peraturan Desa</h3>
                    <p>{{ $legalitas['perdes_nomor'] ?? 'Data Peraturan Desa belum ditambahkan.' }}</p>
                    <span class="bumdes-info-card__note">{{ $legalitas['perdes_keterangan'] ?? 'Nomor dan tahun regulasi desa' }}</span>
                </article>
                <article class="bumdes-info-card bumdes-info-card--blue">
                    <span class="bumdes-info-card__icon" aria-hidden="true">▣</span>
                    <span class="bumdes-info-card__label">BADAN HUKUM</span>
                    <h3>SK Kemenkumham</h3>
                    <p>{{ $legalitas['ahu_nomor'] ?? 'Data registrasi AHU belum ditambahkan.' }}</p>
                    <span class="bumdes-info-card__note">{{ $legalitas['ahu_keterangan'] ?? 'Nomor registrasi AHU' }}</span>
                </article>
                <article class="bumdes-info-card bumdes-info-card--green">
                    <span class="bumdes-info-card__icon" aria-hidden="true">↗</span>
                    <span class="bumdes-info-card__label">PENDIRIAN</span>
                    <h3>NIB &amp; Pendirian</h3>
                    <p>NIB: {{ $legalitas['nib'] ?? 'Belum ditambahkan' }}</p>
                    <span class="bumdes-info-card__note">Tanggal berdiri: {{ $legalitas['tanggal_berdiri'] ?? 'Belum ditambahkan' }}</span>
                </article>
            </div>
        </section>

        <section class="bumdes-section bumdes-vision" aria-labelledby="bumdes-vision-title">
            <div class="bumdes-section__heading bumdes-section__heading--center">
                <span class="bumdes-section__eyebrow">ARAH &amp; KOMITMEN</span>
                <h2 id="bumdes-vision-title">Visi dan Misi BUMDes</h2>
                <p>Menjadi penggerak usaha desa yang tumbuh bersama masyarakat.</p>
            </div>
            <div class="bumdes-vision__layout">
                <article class="bumdes-vision__statement">
                    <span class="bumdes-vision__quote" aria-hidden="true">“</span>
                    <span class="bumdes-section__eyebrow">VISI KAMI</span>
                    <h3>Berdaya dari potensi desa</h3>
                    <p>{{ $visi }}</p>
                    <span class="bumdes-vision__decoration" aria-hidden="true">SA</span>
                </article>
                <div class="bumdes-mission-grid">
                    @foreach ($misi as $index => $item)
                        <article class="bumdes-mission-card">
                            <span class="bumdes-mission-card__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <span class="bumdes-section__eyebrow">MISI {{ $index + 1 }}</span>
                                <p>{{ $item }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bumdes-section bumdes-management" aria-labelledby="bumdes-management-title">
            <div class="bumdes-section__heading bumdes-section__heading--split">
                <div>
                    <span class="bumdes-section__eyebrow">TATA KELOLA AMANAH</span>
                    <h2 id="bumdes-management-title">Struktur Pengelola</h2>
                </div>
                <span class="bumdes-status"><span aria-hidden="true"></span> Pengelolaan profesional</span>
            </div>
            @if ($pengurus->isNotEmpty())
                <div class="bumdes-management__grid">
                    @foreach ($pengurus as $anggota)
                        <article class="bumdes-role-card">
                            @if (filled($anggota['foto'] ?? null))
                                <img class="bumdes-role-card__photo" src="{{ \Illuminate\Support\Facades\Storage::url($anggota['foto']) }}" alt="Foto {{ $anggota['nama'] }}">
                            @else
                                <span class="bumdes-role-card__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($anggota['nama'], 0, 1)) }}</span>
                            @endif
                            <h3>{{ $anggota['nama'] }}</h3>
                            <span class="bumdes-role-card__position">{{ $anggota['jabatan'] }}</span>
                            @if (filled($anggota['deskripsi'] ?? null))
                                <p>{{ $anggota['deskripsi'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bumdes-management__empty" role="status">
                    <span aria-hidden="true">♙</span>
                    <p>Informasi pengurus sedang disiapkan.</p>
                </div>
            @endif
        </section>

        <section class="bumdes-section bumdes-units" id="unit-usaha" aria-labelledby="bumdes-units-title">
            <div class="bumdes-section__heading bumdes-section__heading--split">
                <div>
                    <span class="bumdes-section__eyebrow">POTENSI EKONOMI LOKAL</span>
                    <h2 id="bumdes-units-title">Unit Usaha BUMDes</h2>
                </div>
                <p>Kenali usaha yang dikelola untuk mendukung ekonomi dan pelayanan masyarakat desa.</p>
            </div>

            @if ($unitUsaha->isNotEmpty())
                <div class="bumdes-units__grid">
                    @foreach ($unitUsaha as $unit)
                        <article class="bumdes-unit-card">
                            <div class="bumdes-unit-card__image">
                                @if ($unit->gambar)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($unit->gambar) }}" alt="{{ $unit->nama_unit }}">
                                @else
                                    <span class="bumdes-unit-card__placeholder" aria-hidden="true">✦</span>
                                @endif
                                <span class="bumdes-unit-card__category">{{ $unit->jenis }}</span>
                            </div>
                            <div class="bumdes-unit-card__body">
                                <span class="bumdes-unit-card__status"><span aria-hidden="true"></span> Aktif</span>
                                <h3>{{ $unit->nama_unit }}</h3>
                                <p>{{ $unit->deskripsi ?: 'Informasi mengenai unit usaha ini akan segera diperbarui.' }}</p>
                                @if ($unit->harga !== null)
                                    <span class="bumdes-unit-card__price">Mulai Rp{{ number_format($unit->harga, 0, ',', '.') }}</span>
                                @endif
                                @if ($unit->telepon)
                                    @php($nomorTelepon = preg_replace('/[^\d+]/', '', $unit->telepon))
                                    <a class="bumdes-unit-card__contact" href="tel:{{ $nomorTelepon }}">Hubungi {{ $unit->telepon }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bumdes-units__empty">
                    <span aria-hidden="true">⌂</span>
                    <h3>Unit usaha sedang disiapkan</h3>
                    <p>Informasi unit usaha BUMDes Sedati Agung akan ditampilkan di halaman ini setelah tersedia.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
