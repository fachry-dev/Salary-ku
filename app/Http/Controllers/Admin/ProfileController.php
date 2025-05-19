<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule; // Untuk validasi email unik

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request  // Seharusnya tipe request khusus jika Anda buat FormRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request) // Ganti Request dengan UpdateProfileInformationRequest jika Anda buat FormRequest
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $request->name;

        // Jika email diubah, kita bisa set email_verified_at ke null
        // agar pengguna perlu verifikasi ulang (jika Anda menggunakan fitur verifikasi email)
        if ($user->email !== $request->email) {
            $user->email_verified_at = null;
            $user->email = $request->email;
            // $user->sendEmailVerificationNotification(); // Kirim notifikasi verifikasi jika perlu
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
        // Atau bisa juga dengan pesan sukses yang lebih umum:
        // return redirect()->route('profile.edit')->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request // Seharusnya tipe request khusus jika Anda buat FormRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request) // Ganti Request dengan UpdatePasswordRequest jika Anda buat FormRequest
    {
        $user = $request->user();

        $validated = $request->validateWithBag('updatePassword', [ // Menggunakan error bag 'updatePassword'
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Optional: Logout user lain di device lain setelah ganti password
        // Auth::logoutOtherDevices($validated['password']);

        return back()->with('status', 'password-updated');
        // Atau bisa juga dengan pesan sukses yang lebih umum:
        // return back()->with('success', 'Password berhasil diperbarui.');
    }


    /**
     * Delete the user's account.
     * (Fungsionalitas ini opsional dan perlu penanganan hati-hati)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [ // Menggunakan error bag 'userDeletion'
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Sebelum menghapus, pastikan tidak ada constraint yang menghalangi
        // atau lakukan tindakan yang diperlukan (misalnya, nullify foreign keys jika di-set null on delete)
        // Pada kasus ini, jika user adalah admin atau karyawan, penghapusan user
        // mungkin akan cascade ke tabel karyawan jika ada onDelete('cascade') pada foreign key.

        Auth::logout(); // Logout pengguna saat ini

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
        // Atau bisa juga dengan pesan sukses yang lebih umum:
        // return redirect('/')->with('success', 'Akun Anda berhasil dihapus.');
    }
}