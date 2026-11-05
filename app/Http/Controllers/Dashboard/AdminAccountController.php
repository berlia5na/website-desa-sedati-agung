<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensurePrimaryAdmin($request);

        $admins = User::query()
            ->where('role', 'admin')
            ->withCount(['agenda', 'berita', 'galleryPhotos'])
            ->orderBy('name')
            ->get();

        return view('dashboard.admin-accounts', [
            'admins' => $admins,
            'adminCount' => $admins->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePrimaryAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'current_password' => ['required', 'string', 'current_password'],
        ]);

        unset($data['current_password'], $data['password_confirmation']);
        $data['role'] = 'admin';
        User::query()->create($data);

        return redirect()
            ->route('admin.admins')
            ->with('status', 'Akun admin baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensurePrimaryAdmin($request);
        abort_unless($user->role === 'admin', 404);
        abort_unless(! $user->is_primary_admin || $request->user()->is($user), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
            'current_password' => ['required', 'string', 'current_password'],
        ]);

        unset($data['current_password'], $data['password_confirmation']);
        if (blank($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.admins')
            ->with('status', 'Perubahan akun admin berhasil disimpan.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensurePrimaryAdmin($request);
        abort_unless($user->role === 'admin', 404);

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
        ]);

        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'account' => 'Akun yang sedang digunakan tidak dapat dihapus.',
            ]);
        }

        abort_unless(! $user->is_primary_admin, 403);

        DB::transaction(function () use ($user): void {
            $admins = User::query()
                ->where('role', 'admin')
                ->lockForUpdate()
                ->get(['id']);

            if ($admins->count() <= 1) {
                throw ValidationException::withMessages([
                    'account' => 'Admin terakhir tidak dapat dihapus.',
                ]);
            }

            $user->refresh();
            if ($user->berita()->exists() || $user->agenda()->exists() || $user->galleryPhotos()->exists()) {
                throw ValidationException::withMessages([
                    'account' => 'Akun ini masih tercatat sebagai pembuat berita, agenda, atau foto galeri sehingga tidak dapat dihapus.',
                ]);
            }

            $user->delete();
        });

        return redirect()
            ->route('admin.admins')
            ->with('status', 'Akun admin berhasil dihapus.');
    }

    private function ensurePrimaryAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_primary_admin, 403);
    }
}
