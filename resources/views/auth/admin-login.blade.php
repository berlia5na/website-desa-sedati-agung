<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Portal Admin | Desa Sedati Agung</title>
    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>
<body class="login-page">
    <main class="login-card">
        <section class="login-identity" aria-label="Identitas Desa Sedati Agung">
            <div class="login-identity__content">
                <span class="login-badge">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.9 8.7 7 10 4.1-1.3 7-5.4 7-10V6l-7-3Zm0 3.1 4.5 1.9v3c0 3.2-1.8 6.2-4.5 7.4-2.7-1.2-4.5-4.2-4.5-7.4V8l4.5-1.9Z"/></svg>
                    Akses Terautentikasi &amp; Terpusat
                </span>
                <h1>Portal Manajemen<br>Desa &amp; BUMDes</h1>
                <p>Masuk untuk mengelola administrasi kependudukan, pengaduan warga, agenda desa, berita, serta unit usaha peternakan, kios pasar, dan persewaan lapangan badminton.</p>
            </div>

            <div class="login-identity__footer">
                <span class="village-mark" aria-hidden="true">
                    <svg viewBox="0 0 48 48"><path d="M6 19h36L24 8 6 19Zm4 4h28m-24 0v15m8-15v15m8-15v15m8-15v15M7 41h34"/></svg>
                </span>
                <span class="login-identity__details">
                    <strong>Balai Desa Sedati Agung</strong>
                    <small>Kecamatan Sedati, Kab. Sidoarjo</small>
                </span>
                <span class="official-mark"><span aria-hidden="true">★</span> Resmi</span>
            </div>
        </section>

        <section class="login-form-panel" aria-labelledby="login-title">
            <div class="login-lock" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 10V7a5 5 0 0 1 10 0v3m-11 0h12v11H6V10Zm6 4v3"/></svg>
            </div>
            <div class="login-heading">
                <h2 id="login-title">Masuk ke Sistem</h2>
                <p>Silakan pilih peran dan masukkan kredensial resmi Anda.</p>
            </div>

            @if ($errors->any())
                <div class="login-alert" role="alert">
                    <span aria-hidden="true">!</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-field">
                    <label for="username">Nama Pengguna</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="admin_desa" autocomplete="username" required autofocus>
                    </div>
                </div>

                <div class="form-field">
                    <div class="field-heading">
                        <label for="password">Kata Sandi / Password</label>
                        <button class="help-toggle" type="button" aria-expanded="false" aria-controls="login-help">Lupa Kata Sandi? Hubungi Sekretaris Desa</button>
                    </div>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3m-11 0h12v11H6V10Zm6 4v3"/></svg>
                        <input id="password" name="password" type="password" placeholder="Masukkan kata sandi akun resmi" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                        </button>
                    </div>
                    <p class="help-message" id="login-help" hidden>Hubungi Sekretaris Desa melalui kanal komunikasi resmi untuk bantuan akses. Jangan pernah membagikan kata sandi Anda.</p>
                </div>

                <div class="login-options">
                    <label class="remember-option">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Ingat Saya di Perangkat Ini</span>
                    </label>
                    <span class="device-badge"><span aria-hidden="true">▣</span> Khusus Perangkat Kantor</span>
                </div>

                <button class="login-submit" type="submit">
                    Masuk ke Portal Admin
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m12-9h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
                </button>
            </form>

            <div class="security-note">
                <span class="security-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="m12 2 2.4 2.1 3.2-.2.9 3.1 2.7 1.8-1 3.1 1 3.1-2.7 1.8-.9 3.1-3.2-.2L12 22l-2.4-2.1-3.2.2-.9-3.1-2.7-1.8 1-3.1-1-3.1 2.7-1.8.9-3.1 3.2.2L12 2Zm-3 10 2 2 4-4"/></svg>
                </span>
                <span>Akses admin dilindungi autentikasi dan sesi Laravel.</span>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.querySelector('#password');
        const passwordToggle = document.querySelector('.password-toggle');
        const helpToggle = document.querySelector('.help-toggle');
        const helpMessage = document.querySelector('#login-help');

        passwordToggle.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(showPassword));
            passwordToggle.setAttribute('aria-label', showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });

        helpToggle.addEventListener('click', () => {
            const isExpanded = helpToggle.getAttribute('aria-expanded') === 'true';
            helpToggle.setAttribute('aria-expanded', String(! isExpanded));
            helpMessage.hidden = isExpanded;
        });
    </script>
</body>
</html>
