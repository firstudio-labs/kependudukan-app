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
        $villageCode = '';
        try {
            // Log untuk debugging
            Log::info('Attempting to get village name for ID: ' . $villageId, [
                'user_id' => $user->id,
                'villages_id' => $villageId,
                'districts_id' => $user->districts_id
            ]);

            // Get location names using wilayah service - Improved location data retrieval
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';
            $villageCode = null; // Initialize village code variable

            $wilayahService = app(\App\Services\WilayahService::class);

            // Get province data
            if (!empty($user->province_id)) {
                $provinces = $wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $user->province_id) {
                        $provinceName = $province['name'];

                        // Get province code for further queries
                        $provinceCode = $province['code'];

                        // Now get district data using province code
                        if (!empty($user->districts_id) && !empty($provinceCode)) {
                            $districts = $wilayahService->getKabupaten($provinceCode);
                            foreach ($districts as $district) {
                                if ($district['id'] == $user->districts_id) {
                                    $districtName = $district['name'];

                                    // Get district code for further queries
                                    $districtCode = $district['code'];

                                    // Now get subdistrict data using district code
                                    if (!empty($user->sub_districts_id) && !empty($districtCode)) {
                                        $subdistricts = $wilayahService->getKecamatan($districtCode);
                                        foreach ($subdistricts as $subdistrict) {
                                            if ($subdistrict['id'] == $user->sub_districts_id) {
                                                $subdistrictName = $subdistrict['name'];

                                                // Get subdistrict code for further queries
                                                $subdistrictCode = $subdistrict['code'];

                                                // Finally get village data using subdistrict code
                                                if (!empty($villageId) && !empty($subdistrictCode)) {
                                                    $villages = $wilayahService->getDesa($subdistrictCode);
                                                    foreach ($villages as $village) {
                                                        if ($village['id'] == $villageId) {
                                                            $villageName = $village['name'];
                                                            $villageCode = $village['code']; // Store the complete village code
                                                            break;
                                                        }
                                                    }
                                                }
                                                break;
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // If village name is still empty, try to search all districts
            if (empty($villageName)) {
                Log::info('Village name not found in hierarchy, trying alternative approach');

                // Get all districts from all provinces
                $provinces = $wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    $provinceCode = $province['code'];
                    $districts = $wilayahService->getKabupaten($provinceCode);

                    foreach ($districts as $district) {
                        $districtCode = $district['code'];
                        $subdistricts = $wilayahService->getKecamatan($districtCode);

                        foreach ($subdistricts as $subdistrict) {
                            $subdistrictCode = $subdistrict['code'];
                            $villages = $wilayahService->getDesa($subdistrictCode);

                            foreach ($villages as $village) {
                                if ($village['id'] == $villageId) {
                                    $villageName = $village['name'];
                                    $villageCode = $village['code'];
                                    $subdistrictName = $subdistrict['name'];
                                    $districtName = $district['name'];
                                    $provinceName = $province['name'];
                                    Log::info('Found village in broader search: ' . $villageName);
                                    break 4; // Break out of all loops
                                }
                            }
                        }
                    }
                }
            }

            // Log location data for debugging
            Log::info('Location data for user ID: ' . $user->id, [
                'province_id' => $user->province_id,
                'district_id' => $user->districts_id,
                'subdistrict_id' => $user->sub_districts_id,
                'village_id' => $villageId,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'village_code' => $villageCode // Log the village code
            ]);

            // Jika masih kosong, gunakan fallback
            if (empty($villageName)) {
                Log::warning('Could not find village name for ID: ' . $villageId);
                $villageName = 'Desa #' . $villageId;
            }
        } catch (\Exception $e) {
            Log::error('Error getting village name: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            $villageName = 'Desa #' . $villageId;
        }

        // Log untuk debug
        Log::info('Nama desa yang digunakan', [
            'village_id' => $villageId,
            'village_name' => $villageName,
            'village_code' => $villageCode
        ]);

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

        // Dapatkan data warga
        Log::info('Mengambil data warga berdasarkan nama desa', ['village_name' => $villageName]);

        // Gunakan metode yang tersedia di CitizenService
        $citizenData = $this->citizenService->getAllCitizensWithHighLimit();

        // Filter warga berdasarkan nama desa secara manual jika perlu
        $filteredCitizens = [];

        // Ekstrak array warga dari respons API dengan penanganan yang lebih baik
        if (isset($citizenData['data']['citizens']) && is_array($citizenData['data']['citizens'])) {
            $allCitizens = $citizenData['data']['citizens'];
            // Filter citizens by village name if we have it
            if (!empty($villageName) && $villageName !== 'Desa #' . $villageId) {
                foreach ($allCitizens as $citizen) {
                    if (isset($citizen['village_name']) &&
                        (strtolower($citizen['village_name']) === strtolower($villageName) ||
                         strpos(strtolower($citizen['village_name']), strtolower($villageName)) !== false)) {
                        $filteredCitizens[] = $citizen;
                    }
                }
                Log::info('Filtered citizens by village name', ['count' => count($filteredCitizens)]);
            } else {
                $filteredCitizens = $allCitizens;
            }
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
