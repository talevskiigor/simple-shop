<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NonProductionSafety
{
    public function handle(Request $request, Closure $next): Response
    {
        // Keep these controls in production as well as isolated environments.
        $blocked = $request->is('update', 'admin/register')
            || ($request->is('bank', 'bank/*') && !config('payments.enabled'));
        $response = $blocked
            ? response('Not Found', 404)
            : $next($request);
        if (!config('store.allow_indexing') || $request->is(
            'admin', 'admin/*', 'cart', 'cart/*', 'order', 'order/*',
            'bank', 'bank/*', 'payment', 'payment/*'
        )) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }
        $bank = config('payments.enabled') ? ' https://www.cpay.com.mk' : '';
        $response->headers->set('Content-Security-Policy', "form-action 'self'{$bank}; frame-src 'none'");

        return $response;
    }
}
