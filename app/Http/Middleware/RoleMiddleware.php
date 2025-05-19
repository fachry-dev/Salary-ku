<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            // Redirect berdasarkan role jika salah akses
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
            } elseif ($user->isKaryawan()) {
                return redirect()->route('employee.dashboard')->with('error', 'Akses ditolak.');
            }
            return redirect('/dashboard')->with('error', 'Akses ditolak.'); // fallback
        }
        return $next($request);
    }
}