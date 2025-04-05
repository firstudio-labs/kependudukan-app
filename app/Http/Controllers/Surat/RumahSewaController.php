<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RumahSewa;
use App\Models\Penandatangan;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Log;
use Exception;

class RumahSewaController extends Controller
{
    protected $wilayahService;
    protected $citizenService;
    protected $jobService;

    /**
     * Create a new controller instance.
     *
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     * @param JobService $jobService
     */
    public function __construct(WilayahService $wilayahService, CitizenService $citizenService, JobService $jobService)
    {
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
        $this->jobService = $jobService;
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
                $q->where('full_name', 'like', "%{$search}%")
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
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        try {
            $signers = Penandatangan::all();
        } catch (Exception $e) {
            Log::error('Error fetching signers: ' . $e->getMessage());
            $signers = collect();
        }

        return view('superadmin.datamaster.surat.rumah-sewa.create', compact(
            'jobs',
            'provinces',
            'signers'
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
        // Log incoming request data for debugging
        Log::info('RumahSewa Store Request:', $request->all());

        // Validation rules that match the form fields exactly
        $validated = $request->validate([
            // Location fields
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',

            // Personal information fields
            'nik' => 'required|string',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'responsible_name' => 'required|string|max:255',

            // Rental property details
            'rental_address' => 'required|string',
            'street' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            'alley_number' => 'required|string|max:50',
            'rt' => 'required|string',
            'building_area' => 'required|string|max:50',
            'room_count' => 'required|integer|min:1',
            'rental_type' => 'required|string|max:255',
            'valid_until' => 'nullable|date',

            // Letter information fields
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            // Create new rental house record
            $rumahSewa = new RumahSewa();
            $rumahSewa->fill($validated);
            $rumahSewa->save();

            return redirect()->route('superadmin.surat.rumah-sewa.index')
                ->with('success', 'Izin rumah sewa berhasil dibuat!');
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create Rumah Sewa: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat izin rumah sewa: ' . $e->getMessage());
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

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all();

        return view('superadmin.datamaster.surat.rumah-sewa.edit', compact(
            'rumahSewa',
            'jobs',
            'provinces',
            'signers'
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
        // Same validation rules as store to ensure consistency
        $validated = $request->validate([
            // Location fields
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',

            // Personal information fields
            'nik' => 'required|string',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'responsible_name' => 'required|string|max:255',

            // Rental property details
            'rental_address' => 'required|string',
            'street' => 'required|string|max:255',
            'village_name' => 'required|string|max:255',
            'alley_number' => 'required|string|max:50',
            'rt' => 'required|string',
            'building_area' => 'required|string|max:50',
            'room_count' => 'required|integer|min:1',
            'rental_type' => 'required|string|max:255',
            'valid_until' => 'nullable|date',

            // Letter information fields
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            $rumahSewa = RumahSewa::findOrFail($id);
            $rumahSewa->update($validated);

            return redirect()->route('superadmin.surat.rumah-sewa.index')
                ->with('success', 'Izin rumah sewa berhasil diperbarui!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui izin rumah sewa: ' . $e->getMessage());
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
            $rtValue = $rumahSewa->rt;

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data
            if (!empty($rumahSewa->province_id)) {
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

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($rumahSewa->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($rumahSewa->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Return view directly instead of generating PDF
            return view('superadmin.datamaster.surat.rumah-sewa.IjinRumahSewa', compact(
                'rumahSewa',
                'provinceName',
                'districtName',
                'subdistrictName',
                'villageName',
                'validUntilDate',
                'rtValue',
                'signing_name'
            ));

        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh surat izin rumah sewa: ' . $e->getMessage());
        }
    }
}
