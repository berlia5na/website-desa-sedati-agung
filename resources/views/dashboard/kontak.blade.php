@extends('layouts.dashboard')

@section('title', 'Kotak Masuk | Portal Admin Desa Sedati Agung')

@section('content')
    @php
        $statusNames = $statusLabels;
        $routeQuery = request()->query();
        $whatsappNumber = '';
        if ($selectedMessage?->telepon) {
            $whatsappNumber = preg_replace('/\D+/', '', $selectedMessage->telepon);
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '62'.substr($whatsappNumber, 1);
            }
        }
    @endphp

    <div class="inbox-page">
        <!-- Judul dan breadcrumb halaman -->
        <header class="inbox-heading">
            <div class="inbox-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Kotak Masuk</span>
            </div>
            <h1>Kotak Masuk</h1>
            <p>Pesan dari pengunjung website</p>
        </header>

        @if (session('status'))
            <div class="inbox-toast inbox-toast--success" role="status" data-toast>{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="inbox-toast inbox-toast--error" role="alert">
                Tindakan belum berhasil. Periksa kembali pilihan pesan lalu coba lagi.
            </div>
        @endif

        <!-- Statistik pesan -->
        <section class="inbox-stats" aria-label="Ringkasan pesan">
            <article class="inbox-stat">
                <span class="inbox-stat__icon inbox-stat__icon--blue" aria-hidden="true">✉</span>
                <div><strong>{{ number_format($totalMessages) }}</strong><span>Total Pesan</span></div>
            </article>
            <article class="inbox-stat">
                <span class="inbox-stat__icon inbox-stat__icon--orange" aria-hidden="true">●</span>
                <div><strong class="inbox-stat__number--unread">{{ number_format($statusCounts->get('baru', 0)) }}</strong><span>Belum Dibaca</span></div>
            </article>
            <article class="inbox-stat">
                <span class="inbox-stat__icon inbox-stat__icon--green" aria-hidden="true">✓</span>
                <div><strong>{{ number_format($statusCounts->get('dibaca', 0)) }}</strong><span>Sudah Dibaca</span></div>
            </article>
            <article class="inbox-stat">
                <span class="inbox-stat__icon inbox-stat__icon--purple" aria-hidden="true">↩</span>
                <div><strong>{{ number_format($statusCounts->get('dibalas', 0)) }}</strong><span>Sudah Dibalas</span></div>
            </article>
        </section>

        <!-- Pencarian dan filter, diproses di server -->
        <section class="inbox-panel" aria-label="Pencarian dan filter pesan">
            <form class="inbox-filters" method="GET" action="{{ route('admin.kontak') }}">
                <div class="inbox-search">
                    <label class="sr-only" for="inbox-search">Cari nama, email, subjek, atau isi pesan</label>
                    <span aria-hidden="true">⌕</span>
                    <input id="inbox-search" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, email, subjek, isi pesan...">
                </div>
                <div class="inbox-filter-row">
                    <div class="inbox-filter">
                        <label for="inbox-status">Status</label>
                        <select id="inbox-status" name="status">
                            <option value="semua" @selected(($filters['status'] ?? 'semua') === 'semua')>Semua</option>
                            <option value="baru" @selected(($filters['status'] ?? '') === 'baru')>Belum Dibaca</option>
                            <option value="dibaca" @selected(($filters['status'] ?? '') === 'dibaca')>Sudah Dibaca</option>
                            <option value="dibalas" @selected(($filters['status'] ?? '') === 'dibalas')>Dibalas</option>
                            <option value="arsip" @selected(($filters['status'] ?? '') === 'arsip')>Arsip</option>
                        </select>
                    </div>
                    <div class="inbox-filter">
                        <label for="inbox-date-from">Tanggal dari</label>
                        <input id="inbox-date-from" type="date" name="tanggal_dari" value="{{ $filters['tanggal_dari'] ?? '' }}">
                    </div>
                    <div class="inbox-filter">
                        <label for="inbox-date-to">Tanggal sampai</label>
                        <input id="inbox-date-to" type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}">
                    </div>
                    <div class="inbox-filter">
                        <label for="inbox-order">Urutan</label>
                        <select id="inbox-order" name="urut">
                            <option value="terbaru" @selected(($filters['urut'] ?? 'terbaru') === 'terbaru')>Terbaru</option>
                            <option value="terlama" @selected(($filters['urut'] ?? '') === 'terlama')>Terlama</option>
                        </select>
                    </div>
                    <button class="inbox-button inbox-button--primary" type="submit">Terapkan</button>
                    <a class="inbox-button inbox-button--plain" href="{{ route('admin.kontak') }}">Reset</a>
                </div>
            </form>

            <!-- Aksi massal dibatasi ke pesan yang dicentang -->
            <form id="inbox-bulk-form" method="POST" action="{{ route('admin.kontak.bulk', $routeQuery) }}" data-delete-action="{{ route('admin.kontak.bulk-delete', $routeQuery) }}">
                @csrf
                <input type="hidden" name="_method" value="DELETE" data-bulk-method disabled>
            </form>
            <div class="inbox-bulk">
                <span class="inbox-selection" data-selection-count>0 pesan dipilih</span>
                <div class="inbox-bulk__actions">
                    <button class="inbox-button inbox-button--plain" type="submit" form="inbox-bulk-form" name="aksi" value="baca" data-bulk-submit disabled>Tandai Dibaca</button>
                    <button class="inbox-button inbox-button--plain" type="submit" form="inbox-bulk-form" name="aksi" value="arsip" data-bulk-submit disabled>Arsipkan</button>
                    <button class="inbox-button inbox-button--danger" type="button" data-bulk-delete disabled>Hapus</button>
                </div>
            </div>
        </section>

        <!-- Daftar pesan: tabel desktop menjadi kartu pada layar kecil -->
        <section class="inbox-list-panel" aria-label="Daftar pesan masuk">
            @if ($messages->isEmpty())
                <div class="inbox-empty">
                    <span class="inbox-empty__icon" aria-hidden="true">✉</span>
                    <h2>{{ filled($filters['q'] ?? null) || ($filters['status'] ?? 'semua') !== 'semua' || filled($filters['tanggal_dari'] ?? null) || filled($filters['tanggal_sampai'] ?? null) ? 'Pesan tidak ditemukan' : 'Belum ada pesan masuk' }}</h2>
                    <p>{{ filled($filters['q'] ?? null) || ($filters['status'] ?? 'semua') !== 'semua' || filled($filters['tanggal_dari'] ?? null) || filled($filters['tanggal_sampai'] ?? null) ? 'Coba ubah kata kunci atau filter pencarian.' : 'Pesan yang dikirim melalui halaman Kontak akan tampil di sini.' }}</p>
                </div>
            @else
                <div class="inbox-table-wrap">
                    <table class="inbox-table">
                        <thead>
                            <tr>
                                <th class="inbox-check-cell"><input type="checkbox" aria-label="Pilih semua pesan di halaman" data-select-all></th>
                                <th scope="col">Pengirim</th>
                                <th scope="col">Subjek</th>
                                <th scope="col">Cuplikan Pesan</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $message)
                                @php($isUnread = $message->status === 'baru')
                                <tr @class(['is-unread' => $isUnread])>
                                    <td class="inbox-check-cell" data-label="Pilih">
                                        <input class="inbox-row-checkbox" form="inbox-bulk-form" type="checkbox" name="ids[]" value="{{ $message->id }}" aria-label="Pilih pesan dari {{ $message->nama }}">
                                    </td>
                                    <td data-label="Pengirim">
                                        <div class="inbox-sender">
                                            @if ($isUnread)<span class="inbox-unread-dot" aria-label="Belum dibaca"></span>@endif
                                            <span><strong>{{ $message->nama }}</strong><small>{{ $message->email }}</small></span>
                                        </div>
                                    </td>
                                    <td data-label="Subjek"><span class="inbox-subject">{{ $message->subjek }}</span></td>
                                    <td data-label="Cuplikan Pesan"><span class="inbox-excerpt">{{ \Illuminate\Support\Str::limit($message->pesan, 80) }}</span></td>
                                    <td data-label="Tanggal"><time datetime="{{ $message->created_at?->toIso8601String() }}">{{ $message->created_at?->locale('id')->translatedFormat('j M Y, H.i') }}</time></td>
                                    <td data-label="Status"><span class="inbox-badge inbox-badge--{{ $message->status }}">{{ $statusNames[$message->status] ?? $message->status }}</span></td>
                                    <td data-label="Aksi" class="inbox-actions-cell">
                                        <form id="inbox-open-{{ $message->id }}" method="POST" action="{{ route('admin.kontak.open', array_merge(['kontakMasuk' => $message->id], $routeQuery)) }}">
                                            @csrf
                                            <button class="inbox-row-action inbox-row-action--view" type="submit">Lihat</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.kontak.status', array_merge(['kontakMasuk' => $message->id], $routeQuery)) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $isUnread ? 'dibaca' : 'baru' }}">
                                            <button class="inbox-row-action" type="submit" aria-label="{{ $isUnread ? 'Tandai sudah dibaca' : 'Tandai belum dibaca' }}">{{ $isUnread ? 'Tandai Dibaca' : 'Belum Dibaca' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.kontak.status', array_merge(['kontakMasuk' => $message->id], $routeQuery)) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="arsip">
                                            <button class="inbox-row-action" type="submit">Arsipkan</button>
                                        </form>
                                        <form id="inbox-delete-{{ $message->id }}" method="POST" action="{{ route('admin.kontak.delete', array_merge(['kontakMasuk' => $message->id], $routeQuery)) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inbox-row-action inbox-row-action--delete" type="button" data-delete-trigger data-delete-form="inbox-delete-{{ $message->id }}" data-delete-name="{{ $message->nama }}">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="inbox-pagination">
                    <p>Menampilkan {{ $messages->firstItem() ?? 0 }}-{{ $messages->lastItem() ?? 0 }} dari {{ number_format($messages->total()) }} pesan</p>
                    {{ $messages->links() }}
                </div>
            @endif
        </section>
    </div>

    <!-- Dialog detail pesan -->
    @if ($selectedMessage)
        <dialog class="inbox-dialog inbox-detail-dialog" data-message-dialog aria-labelledby="message-detail-title">
            <div class="inbox-dialog__header">
                <div><span class="inbox-eyebrow">Detail pesan</span><h2 id="message-detail-title">{{ $selectedMessage->subjek }}</h2></div>
                <form method="dialog"><button class="inbox-dialog__close" type="submit" aria-label="Tutup detail pesan">×</button></form>
            </div>
            <div class="inbox-detail-meta">
                <div><span>Nama</span><strong>{{ $selectedMessage->nama }}</strong></div>
                <div><span>Email</span>
                    @if ($selectedMessage->email)
                        <a href="mailto:{{ $selectedMessage->email }}">{{ $selectedMessage->email }}</a>
                    @else
                        <strong>Tidak dicantumkan</strong>
                    @endif
                </div>
                <div><span>Nomor telepon</span><strong>{{ $selectedMessage->telepon ?: 'Tidak dicantumkan' }}</strong></div>
                <div><span>Waktu kirim</span><strong>{{ $selectedMessage->created_at?->locale('id')->translatedFormat('j M Y, H.i') }}</strong></div>
            </div>
            <section class="inbox-message-body" aria-label="Isi pesan">
                <h3>Isi Pesan</h3>
                <p>{!! nl2br(e($selectedMessage->pesan)) !!}</p>
            </section>
            <form class="inbox-note-form" method="POST" action="{{ route('admin.kontak.note', array_merge(['kontakMasuk' => $selectedMessage->id], $routeQuery)) }}">
                @csrf
                @method('PUT')
                <label for="internal-note">Catatan internal <span>Hanya terlihat oleh admin</span></label>
                <textarea id="internal-note" name="catatan_admin" rows="3" maxlength="5000" placeholder="Tambahkan catatan untuk admin...">{{ $selectedMessage->catatan_admin }}</textarea>
                <button class="inbox-button inbox-button--plain" type="submit">Simpan Catatan</button>
            </form>
            <div class="inbox-detail-actions">
                @if ($selectedMessage->email)
                    <a class="inbox-button inbox-button--primary" href="mailto:{{ $selectedMessage->email }}?subject={{ rawurlencode('Re: '.$selectedMessage->subjek) }}">Balas via Email</a>
                @endif
                @if ($whatsappNumber !== '')
                    <a class="inbox-button inbox-button--whatsapp" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer">Hubungi via WhatsApp</a>
                @endif
                @foreach (['dibalas' => 'Tandai Sudah Dibalas', 'arsip' => 'Arsipkan'] as $status => $label)
                    <form method="POST" action="{{ route('admin.kontak.status', array_merge(['kontakMasuk' => $selectedMessage->id], $routeQuery)) }}">
                        @csrf
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="hidden" name="kembali_ke_detail" value="1">
                        <button class="inbox-button inbox-button--plain" type="submit">{{ $label }}</button>
                    </form>
                @endforeach
                <button class="inbox-button inbox-button--danger" type="button" data-delete-trigger data-delete-form="inbox-detail-delete" data-delete-name="{{ $selectedMessage->nama }}">Hapus</button>
                <form id="inbox-detail-delete" method="POST" action="{{ route('admin.kontak.delete', array_merge(['kontakMasuk' => $selectedMessage->id], $routeQuery)) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </dialog>
    @endif

    <!-- Konfirmasi hapus; tombol Escape menutup dialog secara native -->
    <dialog class="inbox-dialog inbox-confirm-dialog" data-delete-dialog aria-labelledby="delete-dialog-title">
        <form method="dialog">
            <span class="inbox-confirm-icon" aria-hidden="true">!</span>
            <h2 id="delete-dialog-title">Hapus pesan?</h2>
            <p data-delete-copy>Pesan ini akan dihapus permanen.</p>
            <div class="inbox-confirm-actions">
                <button class="inbox-button inbox-button--plain" value="cancel">Batal</button>
                <button class="inbox-button inbox-button--danger" type="button" data-confirm-delete>Hapus Permanen</button>
            </div>
        </form>
    </dialog>
@endsection
