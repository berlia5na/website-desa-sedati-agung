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

    <button class="site-nav-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="site-nav" data-site-nav-toggle>
        <span></span><span></span><span></span>
    </button>
    <a class="site-header__admin" href="{{ route('admin.dashboard') }}"><span class="site-header__admin-icon" aria-hidden="true"></span>Portal Admin</a>
</header>
