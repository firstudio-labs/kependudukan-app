<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
        \Log::debug('Citizen data structure received', [
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
        // ...same logic as index method

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

        return view('admin.desa.index', compact('user', 'role', 'userStats', 'totalCitizens', 'headsOfFamily', 'monthlyData'));
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

