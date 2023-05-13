<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie as CookieHelper;

class Cookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($this->check() == true) {
            return $next($request);
        }
        return $next($request);
    }
    private function check() {
        if (Auth::check() == true) return false;
        if (CookieHelper::has('user') == false) return false;
        $check_data = UserCookie::where('value', CookieHelper::get('user'))->first();
        if ($check_data == false) return false;
        $check_user = User::findOrFail($check_data->user_id);
        $login = Auth::loginUsingId($check_user->id);
        if ($login == true) {
            UserLoginLog::create([
                'user_id' => $check_data->user_id,
                'ip_address' => request()->ip()
            ]);
			Session::put('information_popup', true);
            return session()->flash('result', [
                'alert'   => 'success', 
                'title'   => 'Berhasil', 
                'message' => 'Selamat datang '.$check_user->full_name.'.'
            ]);
            return true;
        }
        return false;
    }
}
