<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackPublicVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->user()) {
            $sessionId = $request->session()->getId();

            DB::table('visitor_visits')->insertOrIgnore([
                'visitor_hash' => hash_hmac('sha256', $sessionId, (string) config('app.key')),
                'visit_date' => today()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $next($request);
    }
}
