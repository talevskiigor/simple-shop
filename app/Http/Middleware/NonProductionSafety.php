<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NonProductionSafety
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('store.sandbox')) {
            return $next($request);
        }

        // The legacy importer and callbacks mutate data without adequate verification.
        $response = $request->is('update', 'bank', 'bank/*', 'admin/register')
            ? response('Not Found', 404)
            : $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $response->headers->set('Content-Security-Policy', "form-action 'self'; frame-src 'none'");

        return $response;
    }
}
