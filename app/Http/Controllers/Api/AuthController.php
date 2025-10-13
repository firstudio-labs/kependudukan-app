<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Models\Keperluan;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Log;
use App\Services\ApiAuthService;


class AuthController extends Controller
{
    protected $citizenService;
    protected $wilayahService;
    protected $apiAuthService;

    public function __construct(
        CitizenService $citizenService,
        WilayahService $wilayahService,
        ApiAuthService $apiAuthService
    ) {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
        $this->apiAuthService = $apiAuthService;
    }

    public function homepage()
    {

        $this->forceLogoutIfAuthenticated();

        $provinces = app(WilayahService::class)->getProvinces();

        $keperluanList = Keperluan::all();

        return response()->json([
            'success' => true,
            'data' => [
                'provinces' => $provinces,
                'keperluanList' => $keperluanList
            ]
        ]);
    }


    protected function forceLogoutIfAuthenticated()
    {
        $loggedOut = false;


        if (Auth::guard('web')->check()) {

            if (request()->user('web')->tokens) {
                request()->user('web')->tokens()->delete();
            }
            Auth::guard('web')->logout();
            $loggedOut = true;
        } else if (Auth::guard('penduduk')->check()) {

            if (request()->user('penduduk')->tokens) {
                request()->user('penduduk')->tokens()->delete();
            }
            Auth::guard('penduduk')->logout();
            $loggedOut = true;
        }

        if ($loggedOut) {
            return response()->json([
                'success' => true,
                'message' => 'Anda telah keluar dari sistem. Silakan login kembali.'
            ]);
        }

        return null;
    }

    public function showLoginForm()
    {
        $this->forceLogoutIfAuthenticated();
        
        return response()->json([
            'status' => true,
            'message' => 'Login form endpoint',
            'data' => [
                'endpoint' => '/api/login',
                'method' => 'POST',
                'required_fields' => [
                    'nik' => 'string (required)',
                    'password' => 'string (required)'
                ],
                'example' => [
                    'nik' => '1234567890123456',
                    'password' => 'password123'
                ]
            ]
        ]);
    }

    protected function isPublicPage($url)
    {

        $publicPages = ['/', '/home', '/login', '/register', ''];
        return in_array($url, $publicPages);
    }

    protected function checkUnauthorizedAccess($role, $currentPath)
    {
        $currentPath = '/' . $currentPath;


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
                return true;
        }
    }

    protected function isSuperadminArea($path)
    {
        return strpos($path, '/superadmin') === 0;
    }

    protected function isAdminDesaArea($path)
    {
        return strpos($path, '/admin-desa') === 0;
    }

    protected function isAdminKabupatenArea($path)
    {
        return strpos($path, '/admin-kabupaten') === 0;
    }

    protected function isOperatorArea($path)
    {
        return strpos($path, '/operator') === 0;
    }

    protected function isGuestArea($path)
    {
        return strpos($path, '/guest') === 0 || $this->isPublicPage($path);
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Try to authenticate with the remote API first
            $apiResponse = $this->apiAuthService->login($request->nik, $request->password);

            if ($apiResponse && isset($apiResponse['status']) && $apiResponse['status'] === true) {
                // API login successful - you can handle returning an API token here if needed
                Log::info('User authenticated via API', [
                    'nik' => $request->nik,
                    'api_response' => $apiResponse
                ]);

                // Depending on your implementation, you might want to create/update a local user
                // and log them in, or just return the API token

                return response()->json([
                    'status' => true,
                    'message' => 'API login successful',
                    'data' => $apiResponse['data'] ?? []
                ], 200);
            }

            // If API auth fails, try local authentication (your existing logic)
            if (Auth::guard('web')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
                $user = Auth::guard('web')->user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil',
                    'data' => [
                        'user' => $user,
                        'role' => $user->role,
                        'access_token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ], 200);
            }

            if (Auth::guard('penduduk')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
                $penduduk = Auth::guard('penduduk')->user();
                $token = $penduduk->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil',
                    'data' => [
                        'user' => $penduduk,
                        'role' => 'penduduk',
                        'access_token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'NIK atau password salah.'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login khusus admin (users table) menggunakan NIK + password
     */
    public function adminLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Auth::guard('web')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIK atau password salah.'
                ], 401);
            }

            $user = Auth::guard('web')->user();

            // Batasi hanya role admin/operator (bukan penduduk)
            $allowedRoles = ['superadmin', 'admin desa', 'admin kabupaten', 'operator'];
            if (!$user->role || !in_array(strtolower($user->role), $allowedRoles)) {
                // Logout segera jika role tidak diizinkan
                Auth::guard('web')->logout();
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak memiliki akses admin.'
                ], 403);
            }

            $token = $user->createToken('admin_auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login admin berhasil',
                'data' => [
                    'user' => $user,
                    'role' => $user->role,
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Admin login error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function showRegistrationForm()
    {
        return view('register');
    }



    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|unique:penduduk|regex:/^[0-9]+$/',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|regex:/^[0-9]+$/',
        ], [
            'nik.regex' => 'NIK harus berupa angka.',
            'no_hp.regex' => 'Nomor HP harus berupa angka.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // First try registering with the remote API
            $apiResponse = $this->apiAuthService->register([
                'nik' => $request->nik,
                'password' => $request->password,
                'no_hp' => $request->no_hp,
            ]);

            if ($apiResponse && isset($apiResponse['status']) && $apiResponse['status'] === true) {
                // API registration successful
                Log::info('User registered via API', [
                    'nik' => $request->nik,
                    'api_response' => $apiResponse
                ]);

                // You might want to create a local user here as well

                return response()->json([
                    'status' => true,
                    'message' => 'API registration successful',
                    'data' => $apiResponse['data'] ?? []
                ], 201);
            }

            // If API registration fails or is not implemented, continue with local registration

            // Keep the original NIK as string for database
            $nikString = $request->nik;

            // Convert NIK to integer only for the API request
            $nik = (int) $nikString;

            Log::info('Attempting to register user with NIK: ' . $nikString);

            // Get citizen data by NIK
            $citizenData = $this->citizenService->getCitizenByNIK($nik);

            // Rest of your existing registration logic...
            // (The remaining code is unchanged)

            // Log the API response for debugging
            Log::info('API Response for NIK lookup:', ['response' => $citizenData]);


            // Check if the citizen exists with more detailed error handling
            if (!$citizenData) {
                Log::error('API returned null for NIK: ' . $nikString);
                return response()->json([
                    'status' => false,
                    'message' => 'Pastikan NIK Kepala Keluarga.'
                ], 400);
            }


            // Check response structure more carefully - adjusted for actual API structure
            if (!isset($citizenData['data']) || !is_array($citizenData['data'])) {
                Log::error('Invalid API response structure for NIK: ' . $nikString, ['response' => $citizenData]);
                return response()->json([
                    'status' => false,
                    'message' => 'Format data tidak valid.'
                ], 400);
            }

            // The citizen data is directly in the 'data' field, not in 'data.citizen'
            $citizen = $citizenData['data'];

            // Check if the citizen has family_status field
            if (!isset($citizen['family_status'])) {
                Log::error('Missing family_status field for NIK: ' . $nikString);
                return response()->json([
                    'status' => false,
                    'message' => 'Status keluarga tidak ditemukan.'
                ], 400);
            }

            if ($citizen['family_status'] !== 'KEPALA KELUARGA') {
                Log::info('Registration rejected - Not a family head. Status: ' . $citizen['family_status']);
                return response()->json([
                    'status' => false,
                    'message' => 'Hanya Kepala Keluarga yang dapat mendaftar.'
                ], 400);
            }


            // If all checks pass, create the penduduk - store NIK as string
            $penduduk = Penduduk::create([
                'nik' => $nikString, // Use the original string NIK
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
            ]);


            $token = $penduduk->createToken('auth_token')->plainTextToken;

            Log::info('Penduduk registered successfully with NIK: ' . $nikString);

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil!',
                'data' => [
                    'user' => $penduduk,
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } else if (Auth::guard('penduduk')->check()) {
            Auth::guard('penduduk')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function redirectBasedOnRole(Request $request, $role)
    {
        // Get the intended path from request
        $intended = $request->query('intended', '');

        switch ($role) {
            case 'superadmin':

                if ($intended && !$this->isSuperadminArea($intended)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses ditolak. Area ini tidak dapat diakses oleh Superadmin.'
                    ], 403);
                }
                return response()->json([
                    'status' => true,
                    'redirect' => '/superadmin/index'
                ]);

            case 'admin desa':

                if ($intended && !$this->isAdminDesaArea($intended)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses ditolak. Silahkan login dengan akun yang sesuai.'
                    ], 403);
                }
                return response()->json([
                    'status' => true,
                    'redirect' => '/admin/desa/index'
                ]);

            case 'admin kabupaten':

                if ($intended && !$this->isAdminKabupatenArea($intended)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses ditolak. Silahkan login dengan akun yang sesuai.'
                    ], 403);
                }
                return response()->json([
                    'status' => true,
                    'redirect' => '/admin/kabupaten/index'
                ]);

            case 'operator':

                if ($intended && !$this->isOperatorArea($intended)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses ditolak. Silahkan login dengan akun yang sesuai.'
                    ], 403);
                }
                return response()->json([
                    'status' => true,
                    'redirect' => '/operator/index'
                ]);

            case 'guest':

                if ($intended && !$this->isGuestArea($intended)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses ditolak. Silahkan login dengan akun yang sesuai.'
                    ], 403);
                }
                return response()->json([
                    'status' => true,
                    'redirect' => '/guest/index'
                ]);

            default:
                return response()->json([
                    'status' => true,
                    'redirect' => '/'
                ]);
        }
    }
}
