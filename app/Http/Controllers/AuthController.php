<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('homepage');
    }
    /**
     * Menampilkan form login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $user = Auth::user();
            return $this->redirectBasedOnRole($user->role);
        }

        return back()->with('error', 'NIK atau password salah.');
    }

    /**
     * Menampilkan form registrasi.
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Proses registrasi.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|unique:users|regex:/^[0-9]+$/', // Hanya angka
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|regex:/^[0-9]+$/', // Hanya angka
        ], [
            'nik.regex' => 'NIK harus berupa angka.',
            'no_hp.regex' => 'Nomor HP harus berupa angka.'
        ]);

        try {
            User::create([
                'nik' => $request->nik,
                'password' => Hash::make($request->password), // Hash password
                'no_hp' => $request->no_hp,
                'role' => 'user', // Default role untuk registrasi
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses logout.
     */
    public function logout()
    {
        Auth::logout(); // Logout user
        return redirect('/login'); // Redirect ke halaman login
    }

    /**
     * Redirect berdasarkan role.
     */
    protected function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'superadmin':
                return redirect()->intended('/superadmin/index');
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'operator':
                return redirect()->intended('/operator/dashboard');
            case 'user':
                return redirect()->intended('/user/index');
            default:
                return redirect()->intended('/');
        }
    }
}
