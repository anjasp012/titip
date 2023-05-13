<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Verification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->segment(2) <> 'verification') {
            if (Auth::check() == true AND Auth::user()->is_verified == '0') {
                return redirect('auth/verification');
            }
        }
        return $next($request);
    }
}
