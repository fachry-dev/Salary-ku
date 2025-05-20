<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Periksa apakah pengguna sudah autentikasi
        if (!Auth::check()) {
            // Jika belum autentikasi, redirect ke login
            return redirect()->route('login');
        }

        // Dapatkan pengguna yang terautentikasi
        $user = Auth::user();

        // Periksa apakah pengguna memiliki peran yang diperlukan
        if ($user->role !== $role) {
            // Jika pengguna tidak memiliki peran yang diperlukan, catat kejadian ini
            \Log::warning('Percobaan akses peran tidak sah', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_role' => $role
            ]);

            // Tentukan ke mana mengarahkan berdasarkan peran pengguna sebenarnya
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman yang diminta.');
            } else if ($user->role === 'employee') {
                return redirect()->route('employee.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman yang diminta.');
            }

            // Jika peran tidak valid atau tidak dikenali, logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Anda tidak memiliki izin yang diperlukan. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
