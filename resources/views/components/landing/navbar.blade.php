@php
    $menuItems = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Profil Desa', 'route' => 'profil-desa', 'active' => 'profil-desa'],
        ['label' => 'Profil BUMDES', 'route' => 'profil-bumdes', 'active' => 'profil-bumdes'],
        ['label' => 'Berita Kegiatan', 'route' => 'berita', 'active' => 'berita*'],
        ['label' => 'Agenda Desa', 'route' => 'agenda', 'active' => 'agenda*'],
        ['label' => 'Kontak', 'route' => 'kontak', 'active' => 'kontak'],
    ];
@endphp

<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}" aria-label="Desa Sedati Agung, beranda">
        <img class="site-brand__logo" src="{{ asset('assets/image/logo-sidoarjo.svg') }}" alt="Logo Desa Sedati Agung">
        <span class="site-brand__text">
            <strong>Desa Sedati Agung</strong>
            <small>Kabupaten Sidoarjo</small>
        </span>
    </a>

    <nav class="site-nav" id="site-nav" aria-label="Navigasi utama">
        @foreach ($menuItems as $item)
            @php($active = request()->routeIs($item['active']))
            <a @class(['is-active' => $active]) @if ($active) aria-current="page" @endif href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>

    <button class="site-theme-toggle" type="button" aria-label="Aktifkan mode gelap" aria-pressed="false" title="Aktifkan mode gelap" data-theme-toggle>
        <svg data-theme-icon="dark" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.2 15.2A8.5 8.5 0 0 1 8.8 3.8 8.5 8.5 0 1 0 20.2 15.2Z"></path>
        </svg>
        <svg data-theme-icon="light" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" hidden>
            <circle cx="12" cy="12" r="4"></circle>
            <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
        </svg>
    </button>
    <button class="site-nav-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="site-nav" data-site-nav-toggle>
        <span></span><span></span><span></span>
    </button>
    <a class="site-header__admin" href="{{ route('admin.dashboard') }}"><span class="site-header__admin-icon" aria-hidden="true"></span>Portal Admin</a>
</header>
