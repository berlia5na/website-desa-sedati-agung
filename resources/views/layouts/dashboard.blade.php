<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Desa Sedati Agung')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        a { color: #0f766e; text-decoration: none; }
        .sidebar { width: 240px; background: #111827; color: white; min-height: 100vh; position: fixed; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; margin-bottom: 12px; }
        .content { margin-left: 280px; padding: 32px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); }
        h1, h2 { margin-top: 0; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Desa</h2>
        <a href="{{ url('/admin') }}">Dashboard</a>
        <a href="{{ url('/admin/beranda') }}">Beranda</a>
        <a href="{{ url('/admin/profil-desa') }}">Profil Desa</a>
        <a href="{{ url('/admin/berita') }}">Berita</a>
        <a href="{{ url('/admin/profil-bumdes') }}">Profil BUMDES</a>
        <a href="{{ url('/admin/agenda') }}">Agenda</a>
        <a href="{{ url('/admin/kontak') }}">Kontak</a>
        <a href="{{ url('/') }}">Kembali ke Website</a>
    </aside>

    <div class="content">
        @yield('content')
    </div>
</body>
</html>
