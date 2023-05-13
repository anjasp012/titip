<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        if ($request->segment(1) == 'admin') {
            if (Auth::guard('admin')->check()) {
                if (Auth::guard('admin')->user()->status == 0) {
                    Auth::guard('admin')->logout();
                    return redirect('admin/auth/login')->with('result', [
                        'alert'   => 'danger', 
                        'title'   => 'Gagal', 
                        'message' => 'Akun dinonaktifkan.'
                    ]);
                }
                return redirect('admin');
            }
        } else {
            if (Auth::check()) {
                if (Auth::user()->status == 0) {
                    Auth::logout();
                    return redirect('auth/login')->with('result', [
                        'alert'   => 'danger', 
                        'title'   => 'Gagal', 
                        'message' => 'Akun dinonaktifkan.'
                    ]);
                }
                return redirect('/');
            }
        } 
        return $next($request);
    }
}
