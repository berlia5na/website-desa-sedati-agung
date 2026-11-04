<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\KontakMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();
        $weekStart = $today->copy()->subDays(6)->toDateString();
        $todayDate = $today->toDateString();
        $dailyVisitorCounts = DB::table('visitor_visits')
            ->whereBetween('visit_date', [$weekStart, $todayDate])
            ->select('visit_date')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('visit_date')
            ->pluck('total', 'visit_date');
        $visitorDays = collect(range(6, 0))->map(function (int $daysAgo) use ($dailyVisitorCounts) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'date' => $date->toDateString(),
                'total' => (int) $dailyVisitorCounts->get($date->toDateString(), 0),
            ];
        });

        return view('dashboard.index', [
            'totalVisitors' => DB::table('visitor_visits')->distinct()->count('visitor_hash'),
            'todayVisitors' => (int) $dailyVisitorCounts->get($todayDate, 0),
            'weekVisitors' => (int) $dailyVisitorCounts->sum(),
            'visitorDays' => $visitorDays,
            'publishedNewsCount' => Berita::query()->where('status', 'published')->count(),
            'upcomingAgendaCount' => Agenda::query()
                ->where('status', 'published')
                ->whereDate('tanggal', '>=', $todayDate)
                ->count(),
            'newContactCount' => KontakMasuk::query()->where('status', 'baru')->count(),
            'latestNews' => Berita::query()
                ->latest('updated_at')
                ->take(5)
                ->get(['id', 'judul', 'slug', 'status', 'updated_at']),
        ]);
    }

    public function agenda(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $agendas = Agenda::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($search) use ($keyword) {
                    $search->where('judul', 'like', "%{$keyword}%")
                        ->orWhere('pic', 'like', "%{$keyword}%")
                        ->orWhere('penyelenggara', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_mulai')
            ->orderByDesc('id')
            ->paginate(6)
            ->withQueryString();

        return view('dashboard.agenda', compact('agendas', 'keyword'));
    }

    public function storeAgenda(Request $request): RedirectResponse
    {
        $data = $this->validateAgenda($request);
        $agenda = Agenda::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'is_prioritas' => false,
        ]);

        return redirect()
            ->route('admin.agenda')
            ->with('status', $agenda->status === 'published'
                ? 'Agenda berhasil dipublikasikan di halaman Agenda Desa.'
                : 'Agenda berhasil disimpan sebagai draft.');
    }

    public function editAgenda(Agenda $agenda): View
    {
        return view('dashboard.agenda', [
            'agendas' => Agenda::query()
                ->orderByDesc('tanggal')
                ->orderByDesc('waktu_mulai')
                ->orderByDesc('id')
                ->paginate(6),
            'keyword' => '',
            'editingAgenda' => $agenda,
        ]);
    }

    public function updateAgenda(Request $request, Agenda $agenda): RedirectResponse
    {
        $agenda->fill($this->validateAgenda($request))->save();

        return redirect()
            ->route('admin.agenda')
            ->with('status', $agenda->status === 'published'
                ? 'Perubahan agenda berhasil disimpan dan dipublikasikan.'
                : 'Perubahan agenda berhasil disimpan sebagai draft.');
    }

    public function deleteAgenda(Agenda $agenda): RedirectResponse
    {
        $agenda->delete();

        return redirect()
            ->route('admin.agenda')
            ->with('status', 'Agenda berhasil dihapus.');
    }

    public function kontak()
    {
        return view('dashboard.kontak');
    }

    private function validateAgenda(Request $request): array
    {
        return $request->validate([
            'badge_kategori' => ['required', 'string', 'max:80'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'kategori' => ['required', 'string', 'max:100'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'jenis_undangan' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'pic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
        ]);
    }
}
