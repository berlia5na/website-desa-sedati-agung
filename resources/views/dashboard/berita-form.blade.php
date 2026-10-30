@extends('layouts.dashboard')

@php($isEditing = $berita->exists)

@section('title', ($isEditing ? 'Ubah Berita' : 'Tulis Berita') . ' | Portal Admin Desa Sedati Agung')

@section('content')
    <form class="news-editor" method="POST" action="{{ $isEditing ? route('admin.berita.update', $berita) : route('admin.berita.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <div class="news-editor__heading">
            <div>
                <div class="admin-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a><span aria-hidden="true">/</span><a href="{{ route('admin.berita') }}">Berita &amp; Kegiatan</a><span aria-hidden="true">/</span><span>{{ $isEditing ? 'Ubah berita' : 'Tulis berita' }}</span></div>
                <h1>{{ $isEditing ? 'Ubah Berita Kegiatan' : 'Tulis Berita Kegiatan' }}</h1>
                <p>Isi informasi utama, tambahkan foto kegiatan, lalu pilih simpan sebagai draft atau publikasikan.</p>
            </div>
            <a class="admin-button admin-button--secondary" href="{{ route('admin.berita') }}">← Kembali ke daftar</a>
        </div>

        @if ($errors->any())
            <div class="admin-alert admin-alert--error" role="alert">
                <strong>Periksa kembali isian berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="news-editor__layout">
            <section class="news-editor__main">
                <div class="news-editor__panel">
                    <div class="news-editor__field">
                        <label for="judul">Judul berita <span aria-hidden="true">*</span></label>
                        <input id="judul" name="judul" type="text" maxlength="255" value="{{ old('judul', $berita->judul) }}" placeholder="Contoh: Gotong Royong Warga Bersihkan Saluran Irigasi" required autofocus>
                        @error('judul') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="news-editor__field">
                        <label for="kategori">Kategori <span aria-hidden="true">*</span></label>
                        <input id="kategori" name="kategori" type="text" list="kategori-berita" maxlength="100" value="{{ old('kategori', $berita->kategori) }}" placeholder="Pilih atau ketik kategori" required>
                        <datalist id="kategori-berita">
                            <option value="Acara Desa">
                            <option value="UMKM Binaan">
                            <option value="Pertanian & Peternakan">
                            <option value="Pemerintahan">
                            <option value="Layanan Publik">
                        </datalist>
                        @error('kategori') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="news-editor__field">
                        <label for="excerpt">Ringkasan <span class="news-editor__optional">opsional</span></label>
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="500" placeholder="Ringkasan singkat yang akan terlihat di kartu berita (maks. 500 karakter).">{{ old('excerpt', $berita->excerpt) }}</textarea>
                        @error('excerpt') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="news-editor__field">
                        <label for="isi">Isi berita <span aria-hidden="true">*</span></label>
                        <textarea id="isi" name="isi" rows="14" placeholder="Ceritakan kegiatan: apa yang dilakukan, kapan dan di mana, siapa yang terlibat, serta manfaatnya bagi warga." required>{{ old('isi', $berita->isi) }}</textarea>
                        <small class="news-editor__hint">Tulis dengan paragraf yang singkat agar nyaman dibaca di ponsel.</small>
                        @error('isi') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <aside class="news-editor__aside">
                <section class="news-editor__panel">
                    <h2>Foto kegiatan</h2>
                    <p class="news-editor__hint">Gunakan foto yang jelas dan relevan. JPG, PNG, atau WebP, maksimal 5 MB.</p>
                    @if ($berita->gambar_url)
                        <img class="news-editor__preview" src="{{ $berita->gambar_url }}" alt="Foto berita saat ini">
                    @endif
                    <label class="news-editor__upload" for="gambar"><span aria-hidden="true">↑</span><span>Pilih foto dari perangkat</span></label>
                    <input class="news-editor__file" id="gambar" name="gambar" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    @error('gambar') <span class="form-error">{{ $message }}</span> @enderror
                </section>

                <section class="news-editor__panel">
                    <h2>Publikasi</h2>
                    <label class="news-editor__field-label" for="status">Status berita</label>
                    <select id="status" name="status">
                        <option value="draft" @selected(old('status', $berita->status ?: 'draft') === 'draft')>Simpan sebagai draft</option>
                        <option value="published" @selected(old('status', $berita->status) === 'published')>Publikasikan sekarang</option>
                    </select>
                    @error('status') <span class="form-error">{{ $message }}</span> @enderror

                    <label class="news-editor__featured">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $berita->is_featured))>
                        <span><strong>Jadikan berita utama</strong><small>Berita ini akan menjadi sorotan di halaman Berita Kegiatan.</small></span>
                    </label>
                </section>
            </aside>
        </div>

        <div class="news-editor__footer">
            <a class="admin-button admin-button--secondary" href="{{ route('admin.berita') }}">Batal</a>
            <button class="admin-button admin-button--primary" type="submit">{{ $isEditing ? 'Simpan perubahan' : 'Simpan berita' }} <span aria-hidden="true">✓</span></button>
        </div>
    </form>
@endsection
