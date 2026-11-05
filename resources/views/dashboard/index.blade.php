@extends('layouts.dashboard')

@section('title', 'Dashboard | Desa Sedati Agung')

@section('content')
    @php($maxDailyVisitors = max(1, (int) $visitorDays->max('total')))

    <div class="dashboard-page">
        <header class="dashboard-heading">
            <div>
                <p class="dashboard-eyebrow">RINGKASAN PORTAL</p>
                <h1>Selamat datang, {{ auth()->user()->name }}</h1>
                <p>Pantau aktivitas website dan kelola informasi Desa Sedati Agung dari satu tempat.</p>
            </div>
            <a class="dashboard-site-link" href="{{ route('home') }}" target="_blank" rel="noopener">
                Lihat Website <span aria-hidden="true">↗</span>
            </a>
        </header>

        <section class="dashboard-stats" aria-label="Ringkasan statistik">
            <article class="dashboard-stat dashboard-stat--visitors">
                <span class="dashboard-stat__icon" aria-hidden="true">◉</span>
                <span class="dashboard-stat__label">Pengunjung unik</span>
                <strong>{{ number_format($totalVisitors, 0, ',', '.') }}</strong>
                <small>Akumulasi sejak pencatatan aktif</small>
            </article>
            <article class="dashboard-stat dashboard-stat--today">
                <span class="dashboard-stat__icon" aria-hidden="true">↗</span>
                <span class="dashboard-stat__label">Pengunjung hari ini</span>
                <strong>{{ number_format($todayVisitors, 0, ',', '.') }}</strong>
                <small>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</small>
            </article>
            <article class="dashboard-stat dashboard-stat--news">
                <span class="dashboard-stat__icon" aria-hidden="true">▤</span>
                <span class="dashboard-stat__label">Berita dipublikasikan</span>
                <strong>{{ number_format($publishedNewsCount, 0, ',', '.') }}</strong>
                <small>Berita yang tampil di website</small>
            </article>
            <article class="dashboard-stat dashboard-stat--agenda">
                <span class="dashboard-stat__icon" aria-hidden="true">▦</span>
                <span class="dashboard-stat__label">Agenda mendatang</span>
                <strong>{{ number_format($upcomingAgendaCount, 0, ',', '.') }}</strong>
                <small>Agenda aktif mulai hari ini</small>
            </article>
        </section>

        <div class="dashboard-columns">
            <section class="dashboard-panel dashboard-visitor-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <p class="dashboard-eyebrow">ANALITIK WEBSITE</p>
                        <h2>Pengunjung 7 hari terakhir</h2>
                    </div>
                    <span class="dashboard-total">{{ number_format($weekVisitors, 0, ',', '.') }} <small>kunjungan</small></span>
                </div>
                <div class="dashboard-chart" role="img" aria-label="Grafik jumlah pengunjung unik per hari selama tujuh hari terakhir">
                    @foreach ($visitorDays as $day)
                        <div class="dashboard-chart__day" aria-label="{{ $day['label'] }}: {{ $day['total'] }} pengunjung">
                            <strong>{{ $day['total'] }}</strong>
                            <div class="dashboard-chart__track">
                                <span style="height: {{ $day['total'] > 0 ? max(8, (int) round($day['total'] / $maxDailyVisitors * 100)) : 3 }}%"></span>
                            </div>
                            <small>{{ $day['label'] }}</small>
                        </div>
                    @endforeach
                </div>
                <p class="dashboard-privacy-note">Pengunjung dihitung satu kali per sesi per hari. Alamat IP tidak disimpan.</p>
            </section>

            <section class="dashboard-panel dashboard-shortcuts">
                <div class="dashboard-panel__heading">
                    <div>
                        <p class="dashboard-eyebrow">AKSES CEPAT</p>
                        <h2>Kelola website</h2>
                    </div>
                </div>
                <a class="dashboard-shortcut" href="{{ route('admin.beranda') }}">
                    <span class="dashboard-shortcut__icon" aria-hidden="true">▧</span>
                    <span><strong>Kelola beranda</strong><small>Atur tampilan utama website</small></span>
                    <span class="dashboard-shortcut__arrow" aria-hidden="true">→</span>
                </a>
                <a class="dashboard-shortcut" href="{{ route('admin.berita') }}">
                    <span class="dashboard-shortcut__icon" aria-hidden="true">▤</span>
                    <span><strong>Berita &amp; kegiatan</strong><small>{{ $publishedNewsCount }} berita sudah tayang</small></span>
                    <span class="dashboard-shortcut__arrow" aria-hidden="true">→</span>
                </a>
                <a class="dashboard-shortcut" href="{{ route('admin.agenda') }}">
                    <span class="dashboard-shortcut__icon" aria-hidden="true">▦</span>
                    <span><strong>Agenda desa</strong><small>{{ $upcomingAgendaCount }} agenda akan datang</small></span>
                    <span class="dashboard-shortcut__arrow" aria-hidden="true">→</span>
                </a>
                <a class="dashboard-shortcut" href="{{ route('admin.kontak') }}">
                    <span class="dashboard-shortcut__icon" aria-hidden="true">✉</span>
                    <span><strong>Kotak masuk</strong><small>{{ $newContactCount }} pesan belum ditinjau</small></span>
                    <span class="dashboard-shortcut__arrow" aria-hidden="true">→</span>
                </a>
            </section>
        </div>

        <section class="dashboard-panel dashboard-latest">
            <div class="dashboard-panel__heading">
                <div>
                    <p class="dashboard-eyebrow">KONTEN TERBARU</p>
                    <h2>Berita terakhir diperbarui</h2>
                </div>
                <a href="{{ route('admin.berita') }}">Lihat semua <span aria-hidden="true">→</span></a>
            </div>
            @forelse ($latestNews as $news)
                <a class="dashboard-news-row" href="{{ route('admin.berita.edit', $news) }}">
                    <span class="dashboard-news-row__title">{{ $news->judul }}</span>
                    <span class="dashboard-news-status dashboard-news-status--{{ $news->status }}">{{ $news->status === 'published' ? 'Terbit' : 'Draft' }}</span>
                    <time datetime="{{ $news->updated_at->toDateString() }}">{{ $news->updated_at->locale('id')->translatedFormat('d M Y') }}</time>
                    <span class="dashboard-shortcut__arrow" aria-hidden="true">→</span>
                </a>
            @empty
                <div class="dashboard-empty">
                    <span aria-hidden="true">▤</span>
                    <strong>Belum ada berita</strong>
                    <p>Berita yang dibuat akan muncul di sini untuk memudahkan pemantauan.</p>
                    <a href="{{ route('admin.berita.create') }}">Tulis berita pertama</a>
                </div>
            @endforelse
        </section>
    </div>
@endsection
