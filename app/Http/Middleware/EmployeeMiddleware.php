<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isEmployee()) {
            return $next($request);
        }
        return redirect('/login')->with('error', 'Akses ditolak. Anda bukan Karyawan.');
    }
}