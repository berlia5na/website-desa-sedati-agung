<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPublicVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_visits_are_counted_once_per_session_per_day(): void
    {
        $this->get(route('home'))->assertOk();

        $this->assertDatabaseCount('visitor_visits', 1);
        $this->assertDatabaseHas('visitor_visits', [
            'visit_date' => today()->toDateString(),
        ]);
    }

    public function test_repeated_public_requests_with_the_same_session_are_deduplicated(): void
    {
        $session = \Mockery::mock(Store::class);
        $session->shouldReceive('getId')->twice()->andReturn('same-session-id');
        $middleware = app(TrackPublicVisitor::class);
        $next = static fn () => new Response;

        foreach (range(1, 2) as $_) {
            $request = Request::create('/', 'GET');
            $request->setLaravelSession($session);
            $request->setUserResolver(static fn () => null);
            $middleware->handle($request, $next);
        }

        $this->assertDatabaseCount('visitor_visits', 1);
    }

    public function test_authenticated_visits_are_not_counted_and_admin_dashboard_shows_statistics(): void
    {
        DB::table('visitor_visits')->insert([
            'visitor_hash' => str_repeat('a', 64),
            'visit_date' => today()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk();

        $this->assertDatabaseCount('visitor_visits', 1);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Selamat datang, '.$admin->name)
            ->assertSee('Pengunjung unik')
            ->assertSee('Pengunjung hari ini')
            ->assertSee('<strong>1</strong>', false)
            ->assertSee('admin-notification-icon', false)
            ->assertSee('aria-controls="admin-notification-popover"', false)
            ->assertSee('Alamat IP tidak disimpan.');
    }
}
