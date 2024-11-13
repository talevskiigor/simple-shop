<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SharedVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('cartCount', 0);
        View::share('categories', Category::all());
        View::share('pages', Page::all());

        return $next($request);
    }
}
