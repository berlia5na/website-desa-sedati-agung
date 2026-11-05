<footer class="site-footer">
    <div class="site-footer__inner">
        <a class="site-brand site-brand--footer" href="{{ route('home') }}" aria-label="Desa Sedati Agung, beranda">
            <img class="site-brand__logo" src="{{ asset('assets/image/logo-sidoarjo.svg') }}" alt="Logo Desa Sedati Agung">
            <span class="site-brand__text">
                <strong>Desa Sedati Agung</strong>
                <small>Kabupaten Sidoarjo</small>
            </span>
        </a>
        <p>Informasi dan layanan resmi Desa Sedati Agung.</p>
        <span>© {{ now()->year }} Pemerintah Desa Sedati Agung</span>
    </div>
</footer>
