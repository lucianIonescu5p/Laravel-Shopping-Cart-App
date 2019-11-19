<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // redirect back to the login page if admin is not authenticated
        if ($request->session()->exists('auth') && !session('auth')) {
            return redirect('/login?unauthorized');
        } else {
            return $next($request);
        }
    }
}
