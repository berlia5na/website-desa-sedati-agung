<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Desa Sedati Agung')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if (request()->routeIs(['profil-desa', 'profil-bumdes', 'berita', 'agenda*']))
        <link href="https://fonts.googleapis.com/css2?family=Ancizar+Serif:wght@400;600;700;800&family=Epilogue:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
    @if (request()->routeIs('agenda*'))
        @vite('resources/css/agenda.css')
    @endif
    @if (request()->routeIs('profil-desa'))
        <link rel="stylesheet" href="{{ asset('css/profil-desa-lengkap.css') }}">
    @endif
</head>
<body>
    @include('components.landing.navbar')

    <main class="site-main">
        @yield('content')
    </main>
    @include('components.landing.footer')
</body>
</html>
