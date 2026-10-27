<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Desa Sedati Agung')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (request()->routeIs('profil-desa'))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:wght@600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/profil-desa-lengkap.css') }}">
    @endif
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="Desa Sedati Agung, beranda">
            <span class="site-brand__mark" aria-hidden="true">SA</span>
            <span class="site-brand__text">
                <strong>Desa Sedati Agung</strong>
                <small>Kabupaten Sidoarjo</small>
            </span>
        </a>

        <nav class="site-nav" aria-label="Navigasi utama">
            <a @class(['is-active' => request()->routeIs('home')]) href="{{ route('home') }}">Beranda</a>
            <a @class(['is-active' => request()->routeIs('profil-desa')]) href="{{ route('profil-desa') }}">Profil Desa</a>
            <a @class(['is-active' => request()->routeIs('profil-bumdes')]) href="{{ route('profil-bumdes') }}">Profil BUMDES</a>
            <a @class(['is-active' => request()->routeIs('berita')]) href="{{ route('berita') }}">Berita</a>
            <a @class(['is-active' => request()->routeIs('agenda')]) href="{{ route('agenda') }}">Agenda</a>
            <a @class(['is-active' => request()->routeIs('kontak')]) href="{{ route('kontak') }}">Kontak</a>
        </nav>

        <a class="site-header__admin" href="{{ route('admin.dashboard') }}">Portal Admin <span aria-hidden="true">↗</span></a>
    </header>

    <main class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer__inner">
            <a class="site-brand site-brand--footer" href="{{ route('home') }}">
                <span class="site-brand__mark" aria-hidden="true">SA</span>
                <span class="site-brand__text">
                    <strong>Desa Sedati Agung</strong>
                    <small>Kabupaten Sidoarjo</small>
                </span>
            </a>
            <p>Informasi dan layanan resmi Desa Sedati Agung.</p>
            <span>© {{ now()->year }} Pemerintah Desa Sedati Agung</span>
        </div>
    </footer>
</body>
</html>
