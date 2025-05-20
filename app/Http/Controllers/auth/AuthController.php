<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba autentikasi dengan fitur "remember me"
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Berhasil autentikasi
            $request->session()->regenerate();

            // Hapus error sesi sebelumnya
            $request->session()->forget('auth.failed');

            // Dapatkan user yang sudah autentikasi
            $user = Auth::user();

            // Baris debug - bisa dihapus di production
            \Log::info('User berhasil login', ['id' => $user->id, 'email' => $user->email, 'role' => $user->role]);

            // Redirect berdasarkan peran
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else if ($user->role === 'employee') {
                return redirect()->route('employee.dashboard');
            } else {
                // Peran tidak valid - logout dan redirect dengan pesan
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Peran pengguna tidak valid. Silakan hubungi administrator.');
            }
        }

        // Autentikasi gagal
        \Log::warning('Percobaan login gagal', ['email' => $request->email]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
                
            ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
