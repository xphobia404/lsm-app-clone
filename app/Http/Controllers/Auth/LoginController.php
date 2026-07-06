<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek user terlebih dahulu (1 query) untuk bedakan "nonaktif" vs "salah password"
        $user = User::where('username', $request->username)->first();

        if ($user && ! $user->is_active) {
            return back()->withErrors([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi Admin.',
            ])->withInput($request->only('username'));
        }

        $credentials = [
            'username'  => $request->username,
            'password'  => $request->password,
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            auth()->user()->touchLastLogin();
            return $this->redirectBasedOnRole();
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function redirectBasedOnRole()
    {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
}
