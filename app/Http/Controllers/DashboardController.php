<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Village;
use App\Services\CitizenService;

class DashboardController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index()
    {
        $user = Auth::user();
        $role = ucfirst($user->role); // Capitalize first letter of role

        // Get user statistics
        $userStats = [
            'superadmin' => User::where('role', 'superadmin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'operator' => User::where('role', 'operator')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        // Get monthly registration data by role from database
        $monthlyRegistrationsByRole = User::getMonthlyRegistrationsByRole();

        // Format data for chart
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $monthlyData = [
            'labels' => $months,
            'superadmin' => array_fill(0, 12, 0),
            'admin' => array_fill(0, 12, 0),
            'operator' => array_fill(0, 12, 0),
            'user' => array_fill(0, 12, 0),
        ];

        // Fill in actual registration counts by role
        foreach ($monthlyRegistrationsByRole as $registration) {
            // Month index is 1-based in database but 0-based in our array
            if (isset($monthlyData[$registration->role])) {
                $monthlyData[$registration->role][$registration->month - 1] = $registration->count;
            }
        }

        // Get citizen data with better error handling
        $citizenData = $this->citizenService->getAllCitizensWithHighLimit();

        // Debug the structure we received
        \Illuminate\Support\Facades\Log::debug('Citizen data structure received', [
            'data_exists' => isset($citizenData['data']),
            'citizens_exists_in_data' => isset($citizenData['data']['citizens']),
            'citizens_exists_at_root' => isset($citizenData['citizens']),
        ]);

        // More robust handling of different possible structures
        $citizens = [];
        $totalCitizens = 0;

        if (isset($citizenData['data']['citizens']) && is_array($citizenData['data']['citizens'])) {
            $citizens = $citizenData['data']['citizens'];
            $totalCitizens = count($citizens);
        } elseif (isset($citizenData['citizens']) && is_array($citizenData['citizens'])) {
            $citizens = $citizenData['citizens'];
            $totalCitizens = count($citizens);
        } elseif (isset($citizenData['data']) && is_array($citizenData['data'])) {
            $citizens = $citizenData['data'];
            $totalCitizens = count($citizens);
        }

        // Count heads of family with improved robustness
        $headsOfFamily = 0;
        foreach ($citizens as $citizen) {
            if (isset($citizen['family_status']) && strtoupper($citizen['family_status']) === 'KEPALA KELUARGA') {
                $headsOfFamily++;
            }
        }

        switch ($user->role) {
            case 'superadmin':
                return view('superadmin.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
            case 'admin desa':
                return view('admin.desa.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
            case 'admin kabupaten':
                return view('admin.kabupaten.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
            case 'operator':
                return view('operator.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
            default:
                return view('user.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
        }
    }

    /**
     * Display admin desa dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function indexDesa()
    {
        $user = Auth::user();
        $role = ucfirst($user->role);
        $villageId = $user->villages_id; // Ambil village_id dari user yang login

        // Dapatkan nama desa dari service dengan pendekatan yang lebih sederhana
        $villageName = '';
        try {
            // Gunakan accessor di model User untuk mendapatkan nama desa
            
            // $villageName = $user->getVillageNameAttribute();

            // Jika masih menampilkan ID, coba dapatkan dari WilayahService langsung
            if (strpos($villageName, 'Desa #') === 0) {
                $villageData = app(\App\Services\WilayahService::class)->getVillageById($villageId);

                if ($villageData && isset($villageData['name'])) {
                    $villageName = $villageData['name'];
                } elseif ($villageData && isset($villageData['data']['name'])) {
                    $villageName = $villageData['data']['name'];
                }
            }

            // Jika masih kosong atau masih berisi ID dan user memiliki district id,
            // coba ambil semua desa dan cari yang sesuai
            if ((empty($villageName) || strpos($villageName, 'Desa #') === 0) && $user->districts_id) {
                // Dapatkan daftar desa dari semua kecamatan di kabupaten ini
                $wilayahService = app(\App\Services\WilayahService::class);
                $kecamatanList = $wilayahService->getKecamatan($user->districts_id);

                // Periksa setiap kecamatan untuk mencari desa
                foreach ($kecamatanList as $kecamatan) {
                    $desaList = $wilayahService->getDesa($kecamatan['code']);

                    // Cari desa dengan ID yang sesuai
                    foreach ($desaList as $desa) {
                        if ($desa['id'] == $villageId) {
                            $villageName = $desa['name'];
                            break 2; // Keluar dari kedua loop jika desa ditemukan
                        }
                    }
                }
            }

            // Jika masih kosong, gunakan fallback
            if (empty($villageName)) {
                $villageName = 'Desa #' . $villageId;
            }
        } catch (\Exception $e) {
            Log::error('Error getting village name: ' . $e->getMessage());
            $villageName = 'Desa #' . $villageId;
        }

        // Log untuk debug
        Log::info('Nama desa yang digunakan', ['village_id' => $villageId, 'village_name' => $villageName]);

        // Dapatkan statistik pengguna berdasarkan desa
        $userStats = [
            'superadmin' => User::where('role', 'superadmin')->count(),
            'admin' => User::where('role', 'admin desa')->where('villages_id', $villageId)->count(),
            'operator' => User::where('role', 'operator')->where('villages_id', $villageId)->count(),
            'user' => User::where('role', 'user')->where('villages_id', $villageId)->count(),
        ];

        // Dapatkan data registrasi bulanan berdasarkan desa
        $monthlyRegistrationsByRole = User::selectRaw('MONTH(created_at) as month, role, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->where('villages_id', $villageId)
            ->groupBy('month', 'role')
            ->orderBy('month')
            ->get();

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $monthlyData = [
            'labels' => $months,
            'superadmin' => array_fill(0, 12, 0),
            'admin' => array_fill(0, 12, 0),
            'operator' => array_fill(0, 12, 0),
            'user' => array_fill(0, 12, 0),
        ];

        // Isi data registrasi bulanan
        foreach ($monthlyRegistrationsByRole as $registration) {
            if (isset($monthlyData[$registration->role])) {
                $monthlyData[$registration->role][$registration->month - 1] = $registration->count;
            }
        }

        // Log untuk debug
        Log::info('Mengambil data warga berdasarkan nama desa', ['village_name' => $villageName]);

        // Dapatkan data warga dari API berdasarkan nama desa
        $citizenData = $this->citizenService->getCitizensByVillageName($villageName);

        // Ekstrak array warga dari respons API dengan penanganan yang lebih baik
        $filteredCitizens = [];
        if (isset($citizenData['data']['citizens']) && is_array($citizenData['data']['citizens'])) {
            $filteredCitizens = $citizenData['data']['citizens'];
        } elseif (isset($citizenData['citizens']) && is_array($citizenData['citizens'])) {
            $filteredCitizens = $citizenData['citizens'];
        } elseif (isset($citizenData['data']) && is_array($citizenData['data'])) {
            $filteredCitizens = $citizenData['data'];
        }

        // Hitung total warga
        $totalCitizens = count($filteredCitizens);

        // Hitung kepala keluarga dan statistik lainnya
        $headsOfFamily = 0;
        $maleCount = 0;
        $femaleCount = 0;
        $ageStats = [
            '0-17' => 0,
            '18-30' => 0,
            '31-45' => 0,
            '46-60' => 0,
            '61+' => 0
        ];

        foreach ($filteredCitizens as $citizen) {
            // Hitung kepala keluarga
            if (
                isset($citizen['family_status']) &&
                (strtoupper($citizen['family_status']) === 'KEPALA KELUARGA' ||
                    $citizen['family_status'] == 2)
            ) {
                $headsOfFamily++;
            }

            // Hitung berdasarkan jenis kelamin
            if (isset($citizen['gender'])) {
                $gender = strtolower($citizen['gender']);
                if ($gender === 'laki-laki' || $gender === 'l') {
                    $maleCount++;
                } elseif ($gender === 'perempuan' || $gender === 'p') {
                    $femaleCount++;
                }
            }

            // Hitung berdasarkan usia jika ada tanggal lahir
            if (isset($citizen['birth_date'])) {
                $birthDate = strtotime($citizen['birth_date']);
                if ($birthDate) {
                    $age = floor((time() - $birthDate) / 31556926); // Umur dalam tahun

                    if ($age <= 17) {
                        $ageStats['0-17']++;
                    } elseif ($age <= 30) {
                        $ageStats['18-30']++;
                    } elseif ($age <= 45) {
                        $ageStats['31-45']++;
                    } elseif ($age <= 60) {
                        $ageStats['46-60']++;
                    } else {
                        $ageStats['61+']++;
                    }
                }
            }
        }

        // Data statistik gender
        $genderStats = [
            'male' => $maleCount,
            'female' => $femaleCount
        ];

        return view('admin.desa.index', compact(
            'user',
            'role',
            'userStats',
            'totalCitizens',
            'headsOfFamily',
            'monthlyData',
            'genderStats',
            'ageStats',
            'villageName'
        ));
    }

    /**
     * Display admin kabupaten dashboard
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function indexKabupaten()
    {
        $user = Auth::user();
        $role = ucfirst($user->role);

        // Get statistics and data similar to index method
        $userStats = [
            'superadmin' => User::where('role', 'superadmin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'operator' => User::where('role', 'operator')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        $citizenData = $this->citizenService->getAllCitizensWithHighLimit();

        // Handle citizen data similar to the index method
        $citizens = [];
        $totalCitizens = 0;
        $headsOfFamily = 0;

        // Process citizen data and calculate statistics
        if (isset($citizenData['data']['citizens']) && is_array($citizenData['data']['citizens'])) {
            $citizens = $citizenData['data']['citizens'];
            $totalCitizens = count($citizens);
        } elseif (isset($citizenData['citizens']) && is_array($citizenData['citizens'])) {
            $citizens = $citizenData['citizens'];
            $totalCitizens = count($citizens);
        } elseif (isset($citizenData['data']) && is_array($citizenData['data'])) {
            $citizens = $citizenData['data'];
            $totalCitizens = count($citizens);
        }

        // Count heads of family
        foreach ($citizens as $citizen) {
            if (isset($citizen['family_status']) && strtoupper($citizen['family_status']) === 'KEPALA KELUARGA') {
                $headsOfFamily++;
            }
        }

        // Monthly data similar to index method
        $monthlyRegistrationsByRole = User::getMonthlyRegistrationsByRole();
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $monthlyData = [
            'labels' => $months,
            'superadmin' => array_fill(0, 12, 0),
            'admin' => array_fill(0, 12, 0),
            'operator' => array_fill(0, 12, 0),
            'user' => array_fill(0, 12, 0),
        ];

        // Fill in actual registration counts by role
        foreach ($monthlyRegistrationsByRole as $registration) {
            if (isset($monthlyData[$registration->role])) {
                $monthlyData[$registration->role][$registration->month - 1] = $registration->count;
            }
        }

        return view('admin.kabupaten.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
    }
}
