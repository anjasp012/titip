<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class API {
    public function handle(Request $request, Closure $next) {
        $check_user = User::where([['api_key', $request['api_key']]])->first();
        if ($check_user == false) {
            return response()->json([
                'status'  => false,
                'data'    => [
                    'message' => 'Pengguna tidak ditemukan.'
                ]
            ], 403, [], JSON_PRETTY_PRINT);
        }
        $request->attributes->add(['user' => $check_user]);
        return $next($request);
    }
}
