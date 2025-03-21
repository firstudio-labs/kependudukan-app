<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RumahSewa;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class RumahSewaController extends Controller
{
    protected $wilayahService;
    protected $citizenService;

    /**
     * Create a new controller instance.
     *
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     */
    public function __construct(WilayahService $wilayahService, CitizenService $citizenService)
    {
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
    }

    /**
     * Display a listing of the rental house permits
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = RumahSewa::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%") // Changed from organizer_name
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('responsible_name', 'like', "%{$search}%")
                  ->orWhere('rental_address', 'like', "%{$search}%")
                  ->orWhere('rental_type', 'like', "%{$search}%");
            });
        }

        $rumahSewaList = $query->paginate(10);

        return view('superadmin.datamaster.surat.rumah-sewa.index', compact('rumahSewaList'));
    }

    /**
     * Show the form for creating a new rental house permit.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get regions data from service
        $provinces = $this->wilayahService->getProvinces();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.rumah-sewa.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Store a newly created rental house permit in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'province_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'district_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'subdistrict_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'village_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'letter_number' => 'nullable|string', // Changed from integer to string to match string column
            'nik' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'responsible_name' => 'required|string|max:255',
            'rental_address' => 'required|string',
            'street' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            'alley_number' => 'required|string|max:50',
            'rt' => 'required|string', // RT field as string to support values like "001", "002", etc.
            'building_area' => 'required|string|max:50',
            'room_count' => 'required|integer', // Matches integer
            'rental_type' => 'required|string|max:255',
            'valid_until' => 'nullable|date', // Matches date
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            // Map form fields to database fields if necessary
            $formData = $request->all();

            // Store the mapped data
            RumahSewa::create($formData);

            return redirect()->route('superadmin.surat.rumah-sewa.index')
                ->with('success', 'Izin rumah sewa berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat izin rumah sewa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rental house permit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $rumahSewa = RumahSewa::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($rumahSewa->province_id)) {
                $province = $this->wilayahService->getProvinceById($rumahSewa->province_id);
                if ($province) {
                    $rumahSewa->province_name = $province['name'];
                }
            }

            if (!empty($rumahSewa->district_id)) {
                $district = $this->wilayahService->getDistrictById($rumahSewa->district_id);
                if ($district) {
                    $rumahSewa->district_name = $district['name'];
                }
            }

            if (!empty($rumahSewa->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($rumahSewa->subdistrict_id);
                if ($subdistrict) {
                    $rumahSewa->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($rumahSewa->village_id)) {
                $village = $this->wilayahService->getVillageById($rumahSewa->village_id);
                if ($village) {
                    $rumahSewa->village_name_admin = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'rumahSewa' => $rumahSewa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified rental house permit.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rumahSewa = RumahSewa::findOrFail($id);

        // Get provinces data from service
        $provinces = $this->wilayahService->getProvinces();

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // If we have province_id, try to get districts


        return view('superadmin.datamaster.surat.rumah-sewa.edit', compact(
            'rumahSewa',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Update the specified rental house permit in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'province_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'district_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'subdistrict_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'village_id' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'letter_number' => 'nullable|string', // Changed from integer to string to match string column
            'nik' => 'required|numeric', // Changed from string to numeric to match bigInteger
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'responsible_name' => 'required|string|max:255',
            'rental_address' => 'required|string',
            'street' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            'alley_number' => 'required|string|max:50',
            'rt' => 'required|string', // RT field as string to support values like "001", "002", etc.
            'building_area' => 'required|string|max:50',
            'room_count' => 'required|integer', // Matches integer
            'rental_type' => 'required|string|max:255',
            'valid_until' => 'nullable|date', // Matches date
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            $rumahSewa = RumahSewa::findOrFail($id);

            // Map form fields to database fields if necessary
            $formData = $request->all();

            // Update with the mapped data
            $rumahSewa->update($formData);

            return redirect()->route('superadmin.surat.rumah-sewa.index')
                ->with('success', 'Izin rumah sewa berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui izin rumah sewa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified rental house permit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rumahSewa = RumahSewa::findOrFail($id);
            $rumahSewa->delete();

            return redirect()->route('superadmin.surat.rumah-sewa.index')
                ->with('success', 'Izin rumah sewa berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus izin rumah sewa: ' . $e->getMessage());
        }
    }

    /**
     * Export rental house permit to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($id)
    {
        try {
            $rumahSewa = RumahSewa::findOrFail($id);

            // Ensure RT is displayed exactly as stored in the database
            // This avoids any automatic type conversion
            $rtValue = $rumahSewa->rt;

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data
            if (!empty($rumahSewa->province_id)) {
                // Get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $rumahSewa->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($rumahSewa->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $rumahSewa->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $rumahSewa->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($rumahSewa->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $rumahSewa->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $rumahSewa->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($rumahSewa->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $rumahSewa->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $rumahSewa->village_id) {
                            $villageName = $village['name'];
                            break;
                        }
                    }
                }
            }

            // Format date
            $validUntilDate = $rumahSewa->valid_until ? \Carbon\Carbon::parse($rumahSewa->valid_until)->locale('id')->isoFormat('D MMMM Y') : '-';

            // Return view directly instead of generating PDF
            return view('superadmin.datamaster.surat.rumah-sewa.IjinRumahSewa', compact(
                'rumahSewa',
                'provinceName',
                'districtName',
                'subdistrictName',
                'villageName',
                'validUntilDate',
                'rtValue'  // Add the explicit RT value
            ));

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh surat izin rumah sewa: ' . $e->getMessage());
        }
    }
}
