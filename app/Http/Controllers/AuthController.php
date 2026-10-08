<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $key = 'admin-login:' . $request->ip() . '|' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withErrors([
                    'email' => 'Terlalu banyak percobaan login. Coba lagi dalam ' . max(1, (int) ceil($seconds / 60)) . ' menit.',
                ])
                ->withInput()
                ->setStatusCode(429);
        }

        if (Auth::attempt(['email' => $email, 'password' => $credentials['password']])) {
            if (Auth::user()->isAdmin()) {
                RateLimiter::clear($key);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.index'))
                    ->with('ok', 'Selamat datang di dashboard admin.');
            }

            Auth::logout();
        }

        RateLimiter::hit($key, 60);

        return back()
            ->withErrors(['email' => 'Email atau password salah, atau akun bukan admin.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
