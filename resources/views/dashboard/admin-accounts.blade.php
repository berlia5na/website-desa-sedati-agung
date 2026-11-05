@extends('layouts.dashboard')

@section('title', 'Kelola Admin | Portal Admin Desa Sedati Agung')

@section('content')
    <div class="admin-accounts">
        <header class="admin-accounts__heading">
            <div>
                <div class="admin-accounts__breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Kelola Admin</span>
                </div>
                <h1>Kelola Admin</h1>
                <p>Hanya admin utama yang dapat menambah dan mengelola akun administrator.</p>
            </div>
            <div class="admin-accounts__total">
                <strong>{{ number_format($adminCount) }}</strong>
                <span>Akun admin</span>
            </div>
        </header>

        @if (session('status'))
            <div class="admin-accounts__notice admin-accounts__notice--success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->has('account'))
            <div class="admin-accounts__notice admin-accounts__notice--error" role="alert">{{ $errors->first('account') }}</div>
        @endif

        <section class="admin-accounts__panel" aria-labelledby="admin-create-title">
            <div class="admin-accounts__panel-heading">
                <span class="admin-accounts__panel-icon" aria-hidden="true">＋</span>
                <div>
                    <h2 id="admin-create-title">Tambah admin baru</h2>
                    <p>Kata sandi harus terdiri dari minimal 12 karakter.</p>
                </div>
            </div>
            <form class="admin-accounts__form" method="POST" action="{{ route('admin.admins.store') }}">
                @csrf
                <input type="hidden" name="form_context" value="create">
                <div class="admin-accounts__fields">
                    <label class="admin-accounts__field">
                        <span>Nama lengkap</span>
                        <input type="text" name="name" value="{{ old('form_context') === 'create' ? old('name') : '' }}" autocomplete="name" required>
                        @if (old('form_context') === 'create') @error('name') <small>{{ $message }}</small> @enderror @endif
                    </label>
                    <label class="admin-accounts__field">
                        <span>Username</span>
                        <input type="text" name="username" value="{{ old('form_context') === 'create' ? old('username') : '' }}" autocomplete="username" required>
                        @if (old('form_context') === 'create') @error('username') <small>{{ $message }}</small> @enderror @endif
                    </label>
                    <label class="admin-accounts__field">
                        <span>Kata sandi</span>
                        <input type="password" name="password" autocomplete="new-password" minlength="12" required>
                        @if (old('form_context') === 'create') @error('password') <small>{{ $message }}</small> @enderror @endif
                    </label>
                    <label class="admin-accounts__field">
                        <span>Ulangi kata sandi</span>
                        <input type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
                    </label>
                    <label class="admin-accounts__field admin-accounts__field--verify">
                        <span>Kata sandi Anda untuk konfirmasi</span>
                        <input type="password" name="current_password" autocomplete="current-password" required>
                        @if (old('form_context') === 'create') @error('current_password') <small>{{ $message }}</small> @enderror @endif
                    </label>
                </div>
                <div class="admin-accounts__actions">
                    <button class="admin-accounts__button admin-accounts__button--primary" type="submit">Tambahkan admin</button>
                </div>
            </form>
        </section>

        <section class="admin-accounts__list" aria-labelledby="admin-list-title">
            <div class="admin-accounts__list-heading">
                <div>
                    <h2 id="admin-list-title">Daftar akun</h2>
                    <p>Perubahan username dan kata sandi memerlukan kata sandi Anda saat ini.</p>
                </div>
            </div>

            @foreach ($admins as $admin)
                @php
                    $isCurrentAdmin = auth()->user()->is($admin);
                    $hasContent = $admin->berita_count + $admin->agenda_count + $admin->gallery_photos_count > 0;
                    $canDelete = !$isCurrentAdmin && $adminCount > 1 && !$hasContent;
                @endphp
                <article class="admin-accounts__card">
                    <div class="admin-accounts__card-heading">
                        <span class="admin-accounts__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($admin->name, 0, 1)) }}</span>
                        <div>
                            <h3>{{ $admin->name }}</h3>
                            <p>{{ $admin->is_primary_admin ? 'Admin utama' : 'Administrator' }}{{ $isCurrentAdmin ? ' · Akun yang sedang digunakan' : '' }}</p>
                        </div>
                        @if ($admin->is_primary_admin)
                            <span class="admin-accounts__badge">Admin utama</span>
                        @elseif ($isCurrentAdmin)
                            <span class="admin-accounts__badge">Anda</span>
                        @endif
                    </div>

                    <form class="admin-accounts__form" method="POST" action="{{ route('admin.admins.update', $admin) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit-{{ $admin->id }}">
                        <div class="admin-accounts__fields">
                            <label class="admin-accounts__field">
                                <span>Nama lengkap</span>
                                <input type="text" name="name" value="{{ old('form_context') === 'edit-'.$admin->id ? old('name', $admin->name) : $admin->name }}" autocomplete="name" required>
                                @if (old('form_context') === 'edit-'.$admin->id) @error('name') <small>{{ $message }}</small> @enderror @endif
                            </label>
                            <label class="admin-accounts__field">
                                <span>Username</span>
                                <input type="text" name="username" value="{{ old('form_context') === 'edit-'.$admin->id ? old('username', $admin->username) : $admin->username }}" autocomplete="username" required>
                                @if (old('form_context') === 'edit-'.$admin->id) @error('username') <small>{{ $message }}</small> @enderror @endif
                            </label>
                            <label class="admin-accounts__field">
                                <span>Kata sandi baru <em>Opsional</em></span>
                                <input type="password" name="password" autocomplete="new-password" minlength="12">
                                @if (old('form_context') === 'edit-'.$admin->id) @error('password') <small>{{ $message }}</small> @enderror @endif
                            </label>
                            <label class="admin-accounts__field">
                                <span>Ulangi kata sandi baru</span>
                                <input type="password" name="password_confirmation" autocomplete="new-password" minlength="12">
                            </label>
                            <label class="admin-accounts__field admin-accounts__field--verify">
                                <span>Kata sandi Anda untuk konfirmasi</span>
                                <input type="password" name="current_password" autocomplete="current-password" required>
                                @if (old('form_context') === 'edit-'.$admin->id) @error('current_password') <small>{{ $message }}</small> @enderror @endif
                            </label>
                        </div>
                        <div class="admin-accounts__card-footer">
                            <p class="admin-accounts__content-note">
                                @if ($hasContent)
                                    Akun ini memiliki konten yang tercatat atas namanya dan tidak dapat dihapus.
                                @else
                                    {{ number_format($admin->agenda_count + $admin->berita_count + $admin->gallery_photos_count) }} konten terkait
                                @endif
                            </p>
                            <button class="admin-accounts__button admin-accounts__button--primary" type="submit">Simpan perubahan</button>
                        </div>
                    </form>

                    @if ($canDelete)
                        <form class="admin-accounts__delete-form" method="POST" action="{{ route('admin.admins.delete', $admin) }}" onsubmit="return confirm('Hapus akun admin? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="form_context" value="delete-{{ $admin->id }}">
                            <label class="admin-accounts__field admin-accounts__field--verify">
                                <span>Kata sandi Anda untuk konfirmasi penghapusan</span>
                                <input type="password" name="current_password" autocomplete="current-password" required>
                                @if (old('form_context') === 'delete-'.$admin->id) @error('current_password') <small>{{ $message }}</small> @enderror @endif
                            </label>
                            <button class="admin-accounts__button admin-accounts__button--danger" type="submit">Hapus akun</button>
                        </form>
                    @elseif ($isCurrentAdmin)
                        <p class="admin-accounts__content-note">Akun yang sedang digunakan tidak dapat dihapus dari sini.</p>
                    @elseif ($adminCount <= 1)
                        <p class="admin-accounts__content-note">Admin terakhir tidak dapat dihapus.</p>
                    @endif
                </article>
            @endforeach
        </section>
    </div>
@endsection
