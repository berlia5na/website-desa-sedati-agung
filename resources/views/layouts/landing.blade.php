<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Desa Sedati Agung')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fa; color: #1f2937; }
        a { color: #0f766e; text-decoration: none; }
        nav { background: #0f172a; color: white; padding: 16px 32px; }
        nav ul { list-style: none; display: flex; gap: 18px; margin: 0; padding: 0; flex-wrap: wrap; }
        nav a { color: #e2e8f0; }
        main { max-width: 1100px; margin: 32px auto; padding: 0 20px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); }
        h1, h2 { margin-top: 0; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ url('/') }}">Beranda</a></li>
            <li><a href="{{ url('/profil-desa') }}">Profil Desa</a></li>
            <li><a href="{{ url('/profil-bumdes') }}">Profil BUMDES</a></li>
            <li><a href="{{ url('/berita') }}">Berita</a></li>
            <li><a href="{{ url('/agenda') }}">Agenda</a></li>
            <li><a href="{{ url('/kontak') }}">Kontak</a></li>
            <li><a href="{{ url('/admin') }}">Admin</a></li>
        </ul>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
