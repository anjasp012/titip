<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Maintenance {
    public function handle(Request $request, Closure $next) {
        if (website_config('main')->is_website_under_maintenance) {
            return response()->view('maintenance');
        }
        return $next($request);
    }
}
