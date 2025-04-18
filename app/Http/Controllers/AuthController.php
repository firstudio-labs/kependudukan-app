<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\User;
use App\Models\Keperluan;
use App\Services\CitizenService;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $citizenService;
    protected $wilayahService;

    public function __construct(CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
    }

    /**
     * Force logout if user navigates to the homepage or other public pages
     */
    public function homepage()
    {
        // Always force logout when accessing homepage
        $this->forceLogoutIfAuthenticated();

        // Get provinces for the pelayanan component
        $provinces = app(WilayahService::class)->getProvinces();

        // Get keperluan list for the form dropdown
        $keperluanList = Keperluan::all();

        return view('homepage', compact('provinces', 'keperluanList'));
    }



    /**
     * Helper method to force logout any authenticated user
     */
    protected function forceLogoutIfAuthenticated()
    {
        if (Auth::guard('web')->check() || Auth::guard('penduduk')->check()) {
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
            } else if (Auth::guard('penduduk')->check()) {
                Auth::guard('penduduk')->logout();
            }

            session()->invalidate();
            session()->regenerateToken();
            session()->flash('info', 'Anda telah keluar dari sistem. Silakan login kembali.');
        }
    }

    public function showLoginForm()
    {
        // Always force logout when accessing login page
        $this->forceLogoutIfAuthenticated();

        // Always show the login form (not authenticated anymore)
        return view('login');
    }

    /**
     * Check if the URL is the homepage, login, or register page
     */
    protected function isPublicPage($url)
    {
        // Consider these routes as public pages that should always be accessible
        $publicPages = ['/', '/home', '/login', '/register', ''];
        return in_array($url, $publicPages);
    }

    /**
     * Check if a user is trying to access an area they're not authorized for
     */
    protected function checkUnauthorizedAccess($role, $currentPath)
    {
        $currentPath = '/' . $currentPath; // Add leading slash for consistency

        // Public pages always cause logout when directly accessed
        if ($this->isPublicPage($currentPath)) {
            return true;
        }

        switch ($role) {
            case 'superadmin':
                return !$this->isSuperadminArea($currentPath);

            case 'admin desa':
                return !$this->isAdminDesaArea($currentPath);

            case 'admin kabupaten':
                return !$this->isAdminKabupatenArea($currentPath);

            case 'operator':
                return !$this->isOperatorArea($currentPath);

            case 'guest':
                return !$this->isGuestArea($currentPath);

            default:
                return true; // Unauthorized for unknown roles
        }
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

        // Get the intended URL from the session
        $intended = session()->get('url.intended', '');

        // Try to auth as a user (admin, superadmin, operator) first
        if (Auth::guard('web')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $user = Auth::guard('web')->user();

            // Check if a superadmin is trying to access non-superadmin areas
            if ($user->role === 'superadmin' && $intended && !$this->isSuperadminArea($intended)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Akses ditolak. Area ini tidak dapat diakses oleh Superadmin.');
            }

            return $this->redirectBasedOnRole($user->role);
        }

        // If not found in users table, try with penduduk guard
        if (Auth::guard('penduduk')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $penduduk = Auth::guard('penduduk')->user();

            // Check if trying to access restricted areas
            if ($intended && !$this->isUserArea($intended)) {
                Auth::guard('penduduk')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Akses ditolak. Silahkan login dengan akun yang sesuai.');
            }

            return redirect()->intended('/user/index');
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
        // Get the intended URL from the session
        $intended = session()->get('url.intended', '');

        switch ($role) {
            case 'superadmin':
                // If superadmin is trying to access non-superadmin areas, force logout
                if ($intended && !$this->isSuperadminArea($intended)) {
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Akses ditolak. Area ini tidak dapat diakses oleh Superadmin.');
                }
                return redirect()->intended('/superadmin/index');

            case 'admin desa':
                // If admin desa tries to access non-admin areas, block access
                if ($intended && !$this->isAdminDesaArea($intended)) {
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Akses ditolak. Silahkan login dengan akun yang sesuai.');
                }
                return redirect()->intended('/admin/desa/index');

            case 'admin kabupaten':
                // If admin kabupaten tries to access non-admin areas, block access
                if ($intended && !$this->isAdminKabupatenArea($intended)) {
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Akses ditolak. Silahkan login dengan akun yang sesuai.');
                }
                return redirect()->intended('/admin/kabupaten/index');

            case 'operator':
                // If operator tries to access non-operator areas, block access
                if ($intended && !$this->isOperatorArea($intended)) {
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Akses ditolak. Silahkan login dengan akun yang sesuai.');
                }
                return redirect()->intended('/operator/index');

            case 'guest':
                // If guest tries to access non-guest areas, block access
                if ($intended && !$this->isGuestArea($intended)) {
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Akses ditolak. Silahkan login dengan akun yang sesuai.');
                }
                return redirect()->intended('/guest/index');

            default:
                return redirect()->intended('/');
        }
    }

    /**
     * Check if the URL is in the superadmin area.
     */
    protected function isSuperadminArea($url)
    {
        return strpos($url, '/superadmin/') !== false || $url === '/superadmin';
    }

    /**
     * Check if the URL is in the admin desa area.
     */
    protected function isAdminDesaArea($url)
    {
        return strpos($url, '/admin/desa/') !== false || $url === '/admin/desa';
    }

    /**
     * Check if the URL is in the admin kabupaten area.
     */
    protected function isAdminKabupatenArea($url)
    {
        return strpos($url, '/admin/kabupaten/') !== false || $url === '/admin/kabupaten';
    }

    /**
     * Check if the URL is in the operator area.
     */
    protected function isOperatorArea($url)
    {
        return strpos($url, '/operator/') !== false || $url === '/operator';
    }

    /**
     * Check if the URL is in the user area.
     */
    protected function isUserArea($url)
    {
        return strpos($url, '/user/') !== false || $url === '/user';
    }

    /**
     * Check if the URL is in the guest area.
     */
    protected function isGuestArea($url)
    {
        return strpos($url, '/guest/') !== false || $url === '/guest';
    }
}
