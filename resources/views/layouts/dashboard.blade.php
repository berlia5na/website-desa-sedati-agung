<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Admin | Desa Sedati Agung')</title>
    @if (request()->routeIs('admin.profil-bumdes'))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
    @if (request()->routeIs('admin.profil-desa'))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:wght@600;700&display=swap" rel="stylesheet">
    @endif
    <script>
        try {
            if (localStorage.getItem('desa-admin-theme') === 'dark') {
                document.documentElement.dataset.adminTheme = 'dark';
            }
        } catch {
            document.documentElement.dataset.adminTheme = 'light';
        }
    </script>
    @vite(['resources/css/admin.css', 'resources/css/admin-beranda.css', 'resources/js/admin-beranda.js'])
    @if (request()->routeIs('admin.profil-bumdes'))
        @vite('resources/js/admin-profil-bumdes.js')
    @endif
    @if (request()->routeIs('admin.profil-desa'))
        <link rel="stylesheet" href="{{ asset('css/admin-profil-desa.css') }}">
        <script src="{{ asset('js/admin-profil-desa.js') }}" defer></script>
    @endif
</head>
<body @class(['admin-body', 'admin-profil-desa-page' => request()->routeIs('admin.profil-desa')])>
    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="admin-brand__logo" aria-hidden="true">SA</span>
                <span>
                    <strong>Desa Sedati Agung</strong>
                    <small>Portal Administrasi</small>
                </span>
            </a>

            <div class="admin-sidebar__label">MENU UTAMA</div>
            <nav class="admin-nav" aria-label="Navigasi portal admin">
                <a class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" data-search-label="dashboard">
                    <span class="admin-nav__icon" aria-hidden="true">⌂</span><span>Dashboard</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.beranda*') ? 'is-active' : '' }}" href="{{ route('admin.beranda') }}" data-search-label="kelola beranda landing page">
                    <span class="admin-nav__icon" aria-hidden="true">▧</span><span>Kelola Beranda</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.profil-desa') ? 'is-active' : '' }}" href="{{ route('admin.profil-desa') }}" data-search-label="profil desa">
                    <span class="admin-nav__icon" aria-hidden="true">⌖</span><span>Profil Desa</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.berita*') ? 'is-active' : '' }}" href="{{ route('admin.berita') }}" data-search-label="berita kegiatan">
                    <span class="admin-nav__icon" aria-hidden="true">▤</span><span>Berita &amp; Kegiatan</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.profil-bumdes') ? 'is-active' : '' }}" href="{{ route('admin.profil-bumdes') }}" data-search-label="profil bumdes">
                    <span class="admin-nav__icon" aria-hidden="true">▥</span><span>Profil BUMDes</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.agenda') ? 'is-active' : '' }}" href="{{ route('admin.agenda') }}" data-search-label="agenda desa">
                    <span class="admin-nav__icon" aria-hidden="true">▦</span><span>Agenda Desa</span>
                </a>
                <a class="admin-nav__link {{ request()->routeIs('admin.kontak') ? 'is-active' : '' }}" href="{{ route('admin.kontak') }}" data-search-label="kotak masuk kontak">
                    <span class="admin-nav__icon" aria-hidden="true">✉</span><span>Kotak Masuk &amp; Kontak</span>
                </a>
            </nav>

            <div class="admin-sidebar__bottom">
                <a class="admin-nav__link admin-nav__link--website" href="{{ route('home') }}">
                    <span class="admin-nav__icon" aria-hidden="true">↗</span><span>Kembali ke Tampilan Web</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="admin-logout" type="submit"><span aria-hidden="true">⇥</span> Keluar dari Portal</button>
                </form>
                <div class="admin-sidebar__copyright">© {{ now()->year }} Pemerintah Desa<br>Sedati Agung</div>
            </div>
        </aside>

        <div class="admin-workspace">
            <header class="admin-topbar">
                <button class="admin-menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="admin-sidebar" aria-expanded="false" data-sidebar-toggle>☰</button>
                <label class="admin-search">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" placeholder="Cari menu..." aria-label="Cari menu portal" data-menu-search>
                    <kbd>⌘ K</kbd>
                </label>
                <div class="admin-topbar__right">
                    <button class="admin-theme-toggle" type="button" aria-label="Aktifkan mode gelap" aria-pressed="false" data-theme-toggle>
                        <span class="admin-theme-toggle__icon" aria-hidden="true">☾</span>
                        <span class="admin-theme-toggle__label">Mode gelap</span>
                    </button>
                    <div class="admin-notifications">
                        <button class="admin-icon-button" type="button" aria-label="Notifikasi" aria-expanded="false" data-notification-toggle>♧</button>
                        <div class="admin-notification-popover" hidden data-notification-popover>
                            Belum ada notifikasi baru.
                        </div>
                    </div>
                    <span class="admin-topbar__divider" aria-hidden="true"></span>
                    <div class="admin-user">
                        <span class="admin-user__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="admin-user__details">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>Administrator</small>
                        </span>
                        <span class="admin-user__chevron" aria-hidden="true">⌄</span>
                    </div>
                </div>
            </header>

            <main class="admin-main">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
