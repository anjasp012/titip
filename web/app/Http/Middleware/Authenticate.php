<?php

namespace App\Http\Middleware;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request) {
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
        } elseif (Auth::check()) {
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
}
