<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\User;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

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

        // Try to auth as a user (admin, superadmin, operator) first
        if (Auth::guard('web')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $user = Auth::guard('web')->user();
            return $this->redirectBasedOnRole($user->role);
        }

        // If not found in users table, try with penduduk guard
        if (Auth::guard('penduduk')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $penduduk = Auth::guard('penduduk')->user();
            return redirect()->intended('/');
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
            'nik' => 'required|string|unique:penduduk|regex:/^[0-9]+$/', // Changed table to penduduk
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|regex:/^[0-9]+$/', // Hanya angka
        ], [
            'nik.regex' => 'NIK harus berupa angka.',
            'no_hp.regex' => 'Nomor HP harus berupa angka.'
        ]);

        try {
            // Keep the original NIK as string for database
            $nikString = $request->nik;

            // Convert NIK to integer only for the API request
            $nik = (int) $nikString;

            Log::info('Attempting to register user with NIK: ' . $nikString);

            // Get citizen data by NIK
            $citizenData = $this->citizenService->getCitizenByNIK($nik);

            // Log the API response for debugging
            Log::info('API Response for NIK lookup:', ['response' => $citizenData]);

            // Check if the citizen exists with more detailed error handling
            if (!$citizenData) {
                Log::error('API returned null for NIK: ' . $nikString);
                return redirect()->back()->withErrors(['nik' => 'Pastikan NIK Kepala Keluarga.'])->withInput();
            }

            // Check response structure more carefully - adjusted for actual API structure
            if (!isset($citizenData['data']) || !is_array($citizenData['data'])) {
                Log::error('Invalid API response structure for NIK: ' . $nikString, ['response' => $citizenData]);
                return redirect()->back()->withErrors(['nik' => 'Format data tidak valid.'])->withInput();
            }

            // The citizen data is directly in the 'data' field, not in 'data.citizen'
            $citizen = $citizenData['data'];

            // Check if the citizen has family_status field
            if (!isset($citizen['family_status'])) {
                Log::error('Missing family_status field for NIK: ' . $nikString);
                return redirect()->back()->withErrors(['nik' => 'Status keluarga tidak ditemukan.'])->withInput();
            }

            if ($citizen['family_status'] !== 'KEPALA KELUARGA') {
                Log::info('Registration rejected - Not a family head. Status: ' . $citizen['family_status']);
                return redirect()->back()->withErrors(['nik' => 'Hanya Kepala Keluarga yang dapat mendaftar.'])->withInput();
            }

            // If all checks pass, create the penduduk - store NIK as string
            Penduduk::create([
                'nik' => $nikString, // Use the original string NIK
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
            ]);

            Log::info('Penduduk registered successfully with NIK: ' . $nikString);
            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        // Check which guard the user is authenticated with
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } else if (Auth::guard('penduduk')->check()) {
            Auth::guard('penduduk')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
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
                return redirect()->intended('/admin/index');
            case 'operator':
                return redirect()->intended('/operator/index');
            default:
                return redirect()->intended('/');
        }
    }
}
