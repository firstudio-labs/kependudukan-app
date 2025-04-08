<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Add search functionality if needed
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('no_hp', 'like', "%$search%");
            });
        }

        $users = $query->paginate(10);
        return view('superadmin.datamaster.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $provinces = $this->wilayahService->getProvinces();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.users.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Log received data for debugging
        Log::info('User creation request received', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'nik' => 'required|unique:users,nik',
            'username' => 'nullable|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'province_id' => 'nullable|numeric',
            'districts_id' => 'nullable|numeric',
            'sub_districts_id' => 'nullable|numeric',
            'villages_id' => 'nullable|numeric',
            'role' => 'required|in:superadmin,admin desa,admin kabupaten,operator',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/users', $imageName);
            $validated['image'] = 'users/' . $imageName;
        }

        // Log the validated data before saving
        Log::info('User data after validation', [
            'validated_data' => $validated
        ]);

        User::create($validated);

        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $provinces = $this->wilayahService->getProvinces();

        // Initialize empty arrays
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // Find the province code from provinces list
        $provinceCode = null;
        if ($user->province_id) {
            foreach ($provinces as $province) {
                if ((int)$province['id'] === (int)$user->province_id) {
                    $provinceCode = $province['code'];
                    break;
                }
            }
        }

        // If province code is found, load districts
        if ($provinceCode) {
            $districts = $this->wilayahService->getKabupaten($provinceCode);

            // Find district code if district ID exists
            $districtCode = null;
            if ($user->districts_id && !empty($districts)) {
                foreach ($districts as $district) {
                    if ((int)$district['id'] === (int)$user->districts_id) {
                        $districtCode = $district['code'];
                        break;
                    }
                }

                // If district code is found, load sub-districts
                if ($districtCode) {
                    $subDistricts = $this->wilayahService->getKecamatan($districtCode);

                    // Find sub-district code if sub-district ID exists
                    $subDistrictCode = null;
                    if ($user->sub_districts_id && !empty($subDistricts)) {
                        foreach ($subDistricts as $subDistrict) {
                            if ((int)$subDistrict['id'] === (int)$user->sub_districts_id) {
                                $subDistrictCode = $subDistrict['code'];
                                break;
                            }
                        }

                        // If sub-district code is found, load villages
                        if ($subDistrictCode) {
                            $villages = $this->wilayahService->getDesa($subDistrictCode);
                        }
                    }
                }
            }
        }

        // Log the data being sent to the view for debugging
        Log::info('Location data for user edit', [
            'user_id' => $user->id,
            'province_id' => $user->province_id,
            'districts_id' => $user->districts_id,
            'sub_districts_id' => $user->sub_districts_id,
            'villages_id' => $user->villages_id,
            'district_count' => count($districts),
            'subdistrict_count' => count($subDistricts),
            'village_count' => count($villages)
        ]);

        return view('superadmin.datamaster.users.edit', compact(
            'user',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Enhanced logging to diagnose form submission
        Log::info('User update request received', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'location_fields' => [
                'province_id' => $request->province_id,
                'districts_id' => $request->districts_id,
                'sub_districts_id' => $request->sub_districts_id,
                'villages_id' => $request->villages_id,
            ],
            'current_values' => [
                'province_id' => $user->province_id,
                'districts_id' => $user->districts_id,
                'sub_districts_id' => $user->sub_districts_id,
                'villages_id' => $user->villages_id,
            ]
        ]);

        $validated = $request->validate([
            'nik' => ['required', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'province_id' => 'nullable|numeric',
            'districts_id' => 'nullable|numeric',
            'sub_districts_id' => 'nullable|numeric',
            'villages_id' => 'nullable|numeric',
            'role' => 'required|in:superadmin,admin desa,admin kabupaten,operator',
            'status' => 'required|in:active,inactive',
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                Storage::delete('public/' . $user->image);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/users', $imageName);
            $validated['image'] = 'users/' . $imageName;
        }

        // Log the validated data before saving
        Log::info('User data after validation for update', [
            'user_id' => $user->id,
            'validated_data' => $validated
        ]);

        // Store original location values for logging
        $originalLocation = [
            'province_id' => $user->province_id,
            'districts_id' => $user->districts_id,
            'sub_districts_id' => $user->sub_districts_id,
            'villages_id' => $user->villages_id,
        ];

        $user->update($validated);

        // Log the location changes
        Log::info('User location update', [
            'user_id' => $user->id,
            'original' => $originalLocation,
            'updated' => [
                'province_id' => $user->province_id,
                'districts_id' => $user->districts_id,
                'sub_districts_id' => $user->sub_districts_id,
                'villages_id' => $user->villages_id,
            ]
        ]);

        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Delete user image if exists
        if ($user->image) {
            Storage::delete('public/' . $user->image);
        }

        $user->delete();
        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Pengguna berhasil dihapus');
    }

    /**
     * Get provinces for location dropdowns
     */
    public function getProvinces()
    {
        $provinces = $this->wilayahService->getProvinces();
        return response()->json($provinces);
    }

    /**
     * Get districts for a province
     */
    public function getDistricts($provinceCode)
    {
        try {
            Log::info('UsersController getDistricts called', [
                'province_code' => $provinceCode
            ]);

            $districts = $this->wilayahService->getKabupaten($provinceCode);

            Log::info('Districts API response', [
                'province_code' => $provinceCode,
                'count' => count($districts),
                'sample' => !empty($districts) ? $districts[0] : null,
                'full_data' => $districts
            ]);

            return response()->json($districts);
        } catch (\Exception $e) {
            Log::error('Error in getDistricts method', [
                'province_code' => $provinceCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get subdistricts for a district
     */
    public function getSubDistricts($districtCode)
    {
        try {
            Log::info('UsersController getSubDistricts called', [
                'district_code' => $districtCode
            ]);

            $subDistricts = $this->wilayahService->getKecamatan($districtCode);

            Log::info('SubDistricts API response', [
                'district_code' => $districtCode,
                'count' => count($subDistricts),
                'sample' => !empty($subDistricts) ? $subDistricts[0] : null,
                'full_data' => $subDistricts
            ]);

            return response()->json($subDistricts);
        } catch (\Exception $e) {
            Log::error('Error in getSubDistricts method', [
                'district_code' => $districtCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get villages for a subdistrict
     */
    public function getVillages($subDistrictCode)
    {
        try {
            Log::info('UsersController getVillages called', [
                'subdistrict_code' => $subDistrictCode
            ]);

            $villages = $this->wilayahService->getDesa($subDistrictCode);

            Log::info('Villages API response', [
                'subdistrict_code' => $subDistrictCode,
                'count' => count($villages),
                'sample' => !empty($villages) ? $villages[0] : null,
                'full_data' => $villages
            ]);

            return response()->json($villages);
        } catch (\Exception $e) {
            Log::error('Error in getVillages method', [
                'subdistrict_code' => $subDistrictCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
