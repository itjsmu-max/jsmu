<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthGuard
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('uid')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
