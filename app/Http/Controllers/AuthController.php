<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_data' => 'required',
            'password' => 'required',
        ], [
            'login_data.required' => 'NIS atau Email wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ]);

        $loginType = filter_var($request->login_data, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login_data,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 1. CEK ROLE ADMIN: Gunakan intended hanya untuk admin
            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            }

            // 2. CEK ROLE SISWA: Paksa ke home (Jangan pakai intended)
            // Ini untuk mencegah siswa balik ke halaman admin yang mereka coba buka tadi
            return redirect('/')->with('success', 'Selamat datang, ' . $user->name);
        }

        return back()->withErrors([
            'login_data' => 'Data login (NIS/Email/Password) tidak sesuai dengan rekaman kami.',
        ])->withInput($request->only('login_data'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}