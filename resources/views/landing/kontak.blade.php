@extends('layouts.landing')

@section('title', 'Kontak | Desa Sedati Agung')

@section('content')
    <div class="contact-page">
        <!-- Hero dan breadcrumb -->
        <section class="contact-hero" aria-labelledby="contact-hero-title">
            <div class="contact-container">
                <span class="contact-eyebrow">Kami siap membantu</span>
                <h1 id="contact-hero-title">Hubungi Kami</h1>
                <p class="contact-hero__copy">Ada pertanyaan atau membutuhkan informasi layanan desa? Sampaikan kepada kami, dan tim Pemerintah Desa Sedati Agung akan dengan senang hati membantu.</p>
                <nav class="contact-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Beranda</a>
                    <span aria-hidden="true">›</span>
                    <span aria-current="page">Kontak</span>
                </nav>
            </div>
        </section>

        <!-- Pilihan kontak utama -->
        <section class="contact-cards-section" aria-label="Informasi kontak utama">
            <div class="contact-container contact-cards">
                <article class="contact-card">
                    <span class="contact-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z"/></svg>
                    </span>
                    <h3>Telepon / WhatsApp</h3>
                    <p>Senin–Jumat, pukul 08.00–15.00 WIB</p>
                    <a class="contact-text-link" href="tel:+6281234567890">+62 812-3456-7890 →</a>
                </article>
                <article class="contact-card">
                    <span class="contact-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    </span>
                    <h3>Email</h3>
                    <p>Kirim pertanyaan atau permohonan informasi.</p>
                    <a class="contact-text-link" href="mailto:info@sedatiagung.desa.id">info@sedatiagung.desa.id →</a>
                </article>
                <article class="contact-card">
                    <span class="contact-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span>
                    <h3>Alamat Kantor</h3>
                    <p>Datang langsung ke kantor desa pada jam layanan.</p>
                    <a class="contact-text-link" href="https://maps.google.com/?q=Kantor+Desa+Sedati+Agung+Sidoarjo" target="_blank" rel="noopener noreferrer">Lihat di Google Maps ↗</a>
                </article>
            </div>
        </section>

        <!-- Formulir kontak dan informasi pendukung -->
        <section class="contact-form-section" id="form-kontak" aria-labelledby="contact-form-heading">
            <div class="contact-container">
                <div class="contact-section-heading">
                    <span class="contact-eyebrow">Sampaikan kepada kami</span>
                    <h2>Mulai Percakapan</h2>
                    <p>Isi formulir berikut dengan informasi yang benar. Kami akan membantu mengarahkan pertanyaan Anda.</p>
                </div>
                <div class="contact-layout">
                    <form class="contact-form" data-contact-form method="POST" action="{{ route('kontak.store') }}" novalidate>
                        @csrf
                        <div class="contact-honeypot" aria-hidden="true">
                            <label for="contact-website">Jangan isi kolom ini</label>
                            <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <h2 id="contact-form-heading">Kirim Pesan</h2>
                        <p class="contact-form__intro">Kolom bertanda <span class="contact-required">*</span> wajib diisi.</p>
                        @if (session('contact_status'))
                            <p class="contact-form__status" data-state="success" role="status">{{ session('contact_status') }}</p>
                        @endif
                        @if ($errors->any())
                            <p class="contact-form__status" data-state="error" role="alert">Pesan belum terkirim. Periksa kembali isian yang ditandai.</p>
                        @endif
                        <div class="contact-form__grid">
                            <div class="contact-field">
                                <label for="contact-name">Nama Lengkap <span class="contact-required">*</span></label>
                                <input id="contact-name" name="nama" type="text" autocomplete="name" placeholder="Nama Anda" value="{{ old('nama') }}" maxlength="150" required @if ($errors->has('nama')) aria-invalid="true" @endif aria-describedby="contact-name-error">
                                <p class="contact-field__error" id="contact-name-error">@error('nama'){{ $message }}@enderror</p>
                            </div>
                            <div class="contact-field">
                                <label for="contact-email">Email <span class="contact-required">*</span></label>
                                <input id="contact-email" name="email" type="email" autocomplete="email" placeholder="nama@email.com" value="{{ old('email') }}" maxlength="254" required @if ($errors->has('email')) aria-invalid="true" @endif aria-describedby="contact-email-error">
                                <p class="contact-field__error" id="contact-email-error">@error('email'){{ $message }}@enderror</p>
                            </div>
                            <div class="contact-field">
                                <label for="contact-phone">Nomor Telepon <span>(opsional)</span></label>
                                <input id="contact-phone" name="telepon" type="tel" autocomplete="tel" placeholder="Contoh: 0812 3456 7890" value="{{ old('telepon') }}" maxlength="30" @if ($errors->has('telepon')) aria-invalid="true" @endif aria-describedby="contact-phone-error">
                                <p class="contact-field__error" id="contact-phone-error">@error('telepon'){{ $message }}@enderror</p>
                            </div>
                            <div class="contact-field">
                                <label for="contact-subject">Subjek <span class="contact-required">*</span></label>
                                <select id="contact-subject" name="subjek" required @if ($errors->has('subjek')) aria-invalid="true" @endif aria-describedby="contact-subject-error">
                                    <option value="">Pilih topik pesan</option>
                                    <option @selected(old('subjek') === 'Informasi layanan desa')>Informasi layanan desa</option>
                                    <option @selected(old('subjek') === 'Administrasi kependudukan')>Administrasi kependudukan</option>
                                    <option @selected(old('subjek') === 'Pengaduan atau aspirasi')>Pengaduan atau aspirasi</option>
                                    <option @selected(old('subjek') === 'Kerja sama dan lainnya')>Kerja sama dan lainnya</option>
                                </select>
                                <p class="contact-field__error" id="contact-subject-error">@error('subjek'){{ $message }}@enderror</p>
                            </div>
                            <div class="contact-field contact-field--full">
                                <label for="contact-message">Pesan <span class="contact-required">*</span></label>
                                <textarea id="contact-message" name="pesan" minlength="10" maxlength="5000" placeholder="Ceritakan kebutuhan atau pertanyaan Anda (minimal 10 karakter)." required @if ($errors->has('pesan')) aria-invalid="true" @endif aria-describedby="contact-message-error">{{ old('pesan') }}</textarea>
                                <p class="contact-field__error" id="contact-message-error">@error('pesan'){{ $message }}@enderror</p>
                            </div>
                            <div class="contact-field contact-field--full">
                                <div class="contact-form__actions">
                                    <button class="contact-button contact-form__submit" type="submit">
                                        <span class="contact-form__spinner" aria-hidden="true"></span>
                                        <span data-contact-submit-label>Kirim Pesan</span>
                                        <span data-contact-submit-arrow aria-hidden="true">→</span>
                                    </button>
                                    <p class="contact-form__note">Pesan akan dikirim langsung ke Pemerintah Desa Sedati Agung.</p>
                                </div>
                                <p class="contact-form__status" data-contact-status role="status" aria-live="polite"></p>
                            </div>
                        </div>
                    </form>

                    <aside class="contact-side" aria-label="Informasi tambahan">
                        <section class="contact-side-card">
                            <h3>Jam Operasional</h3>
                            <dl class="contact-hours">
                                <div><dt>Senin–Kamis</dt><dd>08.00–15.00</dd></div>
                                <div><dt>Jumat</dt><dd>08.00–11.30</dd></div>
                                <div><dt>Sabtu–Minggu</dt><dd>Tutup</dd></div>
                            </dl>
                            <p class="contact-hours-note">Waktu Indonesia Barat (WIB). Jam layanan dapat berubah pada hari libur nasional.</p>
                        </section>
                        <section class="contact-side-card">
                            <h3>Ikuti Kanal Kami</h3>
                            <div class="contact-socials">
                                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Instagram ↗</a>
                                <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook ↗</a>
                                <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer">YouTube ↗</a>
                            </div>
                        </section>
                        <section class="contact-side-card contact-response">
                            <span class="contact-response__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </span>
                            <div><h3>Waktu Respons</h3><p>Kami berupaya membalas dalam 1×24 jam kerja.</p></div>
                        </section>
                    </aside>
                </div>
            </div>
        </section>

        <!-- Peta lokasi kantor desa -->
        <section class="contact-map-section" aria-labelledby="contact-map-heading">
            <div class="contact-container">
                <div class="contact-section-heading contact-map-heading">
                    <span class="contact-eyebrow">Kunjungi kami</span>
                    <h2 id="contact-map-heading">Lokasi Kantor Desa</h2>
                    <p>Kami menyambut Anda untuk mendapatkan pelayanan secara langsung di kantor desa.</p>
                </div>
                <div class="contact-map">
                    <iframe title="Peta lokasi Kantor Desa Sedati Agung" src="https://maps.google.com/maps?q=Kantor%20Desa%20Sedati%20Agung%2C%20Sidoarjo&t=&z=15&ie=UTF8&iwloc=&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
                <div class="contact-address-row">
                    <div class="contact-address">
                        <span class="contact-address__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        </span>
                        <div><strong>Kantor Pemerintah Desa Sedati Agung</strong><p>Jl. Raya Raden Agung No. 01, Kec. Sedati, Kabupaten Sidoarjo, Jawa Timur 61253</p></div>
                    </div>
                    <a class="contact-button" href="https://maps.google.com/?q=Jl.+Raya+Raden+Agung+No.+01,+Sedati,+Sidoarjo" target="_blank" rel="noopener noreferrer">Petunjuk Arah ↗</a>
                </div>
            </div>
        </section>

        <!-- FAQ yang dapat dioperasikan dengan keyboard -->
        <section class="contact-faq-section" aria-labelledby="contact-faq-heading">
            <div class="contact-container">
                <div class="contact-section-heading">
                    <span class="contact-eyebrow">Pertanyaan umum</span>
                    <h2 id="contact-faq-heading">Ada yang Bisa Kami Bantu?</h2>
                    <p>Jawaban singkat untuk pertanyaan yang sering disampaikan warga.</p>
                </div>
                <div class="contact-faq-list">
                    <details class="contact-faq-item">
                        <summary>Kapan kantor desa melayani warga?</summary>
                        <p>Pelayanan tersedia Senin–Kamis pukul 08.00–15.00 WIB dan Jumat pukul 08.00–11.30 WIB. Kantor tutup pada Sabtu, Minggu, dan hari libur nasional.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Bagaimana cara menanyakan persyaratan layanan?</summary>
                        <p>Hubungi kami melalui telepon atau WhatsApp pada jam operasional, atau kirim pesan melalui formulir di halaman ini. Sebutkan layanan yang Anda perlukan agar kami dapat memberi arahan yang sesuai.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Apakah saya perlu membuat janji sebelum datang?</summary>
                        <p>Untuk layanan umum Anda dapat datang pada jam operasional. Untuk keperluan yang membutuhkan petugas atau berkas tertentu, silakan hubungi kantor terlebih dahulu untuk memastikan jadwal dan persyaratannya.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Berapa lama pesan saya akan dibalas?</summary>
                        <p>Kami berupaya menanggapi pesan dalam 1×24 jam kerja. Pesan yang dikirim di luar jam operasional akan diproses pada hari kerja berikutnya.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Bagaimana menyampaikan pengaduan atau aspirasi?</summary>
                        <p>Pilih subjek “Pengaduan atau aspirasi” pada formulir dan sampaikan informasi secara jelas. Anda juga dapat menyampaikan langsung ke kantor desa pada jam pelayanan.</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- Ajakan untuk menghubungi melalui WhatsApp -->
        <section class="contact-container" aria-label="Hubungi melalui WhatsApp">
            <div class="contact-cta">
                <div><h2>Perlu jawaban lebih cepat?</h2><p>Hubungi tim kami melalui WhatsApp pada jam operasional.</p></div>
                <a class="contact-button contact-button--light" href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer">Hubungi via WhatsApp ↗</a>
            </div>
        </section>
    </div>
@endsection
