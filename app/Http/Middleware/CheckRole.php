<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        if (session('user.role') != $role) {
            return redirect('/login');
        }

        return $next($request);
    }
}
