<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\KontakMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KontakMasukController extends Controller
{
    private const STATUSES = [
        'baru' => 'Belum Dibaca',
        'dibaca' => 'Sudah Dibaca',
        'dibalas' => 'Sudah Dibalas',
        'arsip' => 'Arsip',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:semua,baru,dibaca,dibalas,arsip'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'urut' => ['nullable', 'in:terbaru,terlama'],
            'pesan' => ['nullable', 'integer', 'exists:kontak_masuk,id'],
        ]);

        $statusCounts = KontakMasuk::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $query = KontakMasuk::query()
            ->when(filled($filters['q'] ?? null), function ($builder) use ($filters) {
                $keyword = trim($filters['q']);
                $builder->where(function ($search) use ($keyword) {
                    $search->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('subjek', 'like', "%{$keyword}%")
                        ->orWhere('pesan', 'like', "%{$keyword}%");
                });
            })
            ->when(($filters['status'] ?? 'semua') !== 'semua', fn ($builder) => $builder->where('status', $filters['status']))
            ->when(filled($filters['tanggal_dari'] ?? null), fn ($builder) => $builder->whereDate('created_at', '>=', $filters['tanggal_dari']))
            ->when(filled($filters['tanggal_sampai'] ?? null), fn ($builder) => $builder->whereDate('created_at', '<=', $filters['tanggal_sampai']));

        if (($filters['urut'] ?? 'terbaru') === 'terlama') {
            $query->oldest('created_at')->orderBy('id');
        } else {
            $query->latest('created_at')->orderByDesc('id');
        }

        $messages = $query->paginate(10)->withQueryString();
        $selectedMessage = isset($filters['pesan'])
            ? KontakMasuk::query()->find($filters['pesan'])
            : null;

        return view('dashboard.kontak', [
            'messages' => $messages,
            'filters' => $filters,
            'selectedMessage' => $selectedMessage,
            'statusCounts' => $statusCounts,
            'totalMessages' => KontakMasuk::query()->count(),
            'statusLabels' => self::STATUSES,
        ]);
    }

    public function open(Request $request, KontakMasuk $kontakMasuk): RedirectResponse
    {
        if ($kontakMasuk->status === 'baru') {
            $kontakMasuk->update(['status' => 'dibaca']);
        }

        return $this->redirectToInbox($request, $kontakMasuk->id)
            ->with('status', 'Pesan dibuka dan ditandai sudah dibaca.');
    }

    public function updateStatus(Request $request, KontakMasuk $kontakMasuk): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,dibaca,dibalas,arsip'],
        ]);
        $kontakMasuk->update(['status' => $validated['status']]);

        return $this->redirectToInbox(
            $request,
            $request->boolean('kembali_ke_detail') ? $kontakMasuk->id : null
        )->with('status', 'Status pesan berhasil diperbarui.');
    }

    public function updateNote(Request $request, KontakMasuk $kontakMasuk): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:5000'],
        ]);
        $kontakMasuk->update(['catatan_admin' => $validated['catatan_admin']]);

        return $this->redirectToInbox($request, $kontakMasuk->id)
            ->with('status', 'Catatan internal berhasil disimpan.');
    }

    public function destroy(Request $request, KontakMasuk $kontakMasuk): RedirectResponse
    {
        $kontakMasuk->delete();

        return $this->redirectToInbox($request)
            ->with('status', 'Pesan berhasil dihapus permanen.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'aksi' => ['required', 'in:baca,arsip'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:kontak_masuk,id'],
        ]);
        $status = $validated['aksi'] === 'baca' ? 'dibaca' : 'arsip';

        KontakMasuk::query()->whereIn('id', $validated['ids'])->update(['status' => $status]);

        return $this->redirectToInbox($request)
            ->with('status', 'Aksi massal berhasil diterapkan pada pesan terpilih.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:kontak_masuk,id'],
        ]);

        KontakMasuk::query()->whereIn('id', $validated['ids'])->delete();

        return $this->redirectToInbox($request)
            ->with('status', 'Pesan terpilih berhasil dihapus permanen.');
    }

    private function redirectToInbox(Request $request, ?int $messageId = null): RedirectResponse
    {
        $query = array_intersect_key(
            $request->query(),
            array_flip(['q', 'status', 'tanggal_dari', 'tanggal_sampai', 'urut', 'page'])
        );
        if ($messageId !== null) {
            $query['pesan'] = $messageId;
        }

        return redirect()->route('admin.kontak', $query);
    }
}
