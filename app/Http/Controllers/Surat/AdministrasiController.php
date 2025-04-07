<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Administration;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Penandatangan;

class AdministrasiController extends Controller
{
    protected $jobService;
    protected $wilayahService;
    protected $citizenService;

    /**
     * Create a new controller instance.
     *
     * @param JobService $jobService
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     */
    public function __construct(JobService $jobService, WilayahService $wilayahService, CitizenService $citizenService)
    {
        $this->jobService = $jobService;
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
    }

    /**
     * Display a listing of the administrations
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Administration::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('statement_content', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $administrations = $query->paginate(10);

        return view('superadmin.datamaster.surat.administrasi.index', compact('administrations'));
    }

    /**
     * Show the form for creating a new administration letter
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all(); // Fetch all signers from the penandatanganan table


        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];


        return view('superadmin.datamaster.surat.administrasi.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers',

        ));
    }

    /**
     * Store a newly created administration letter in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric',
            'job_type_id' => 'required|numeric', // Changed from job_id to match the database column
            'religion' => 'required|string',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'letter_date' => 'required|date',
            'statement_content' => 'required|string',
            'purpose' => 'required|string',
            'province_id' => 'required|string',
            'district_id' => 'required|string',
            'subdistrict_id' => 'required|string',
            'village_id' => 'required|string',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            Administration::create($request->all());
            return redirect()->route('superadmin.surat.administrasi.index')
                ->with('success', 'Surat administrasi berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat administrasi: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified administration letter
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Find administration record by ID
        $administration = Administration::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all(); // Add this line to fetch signers

        // For debugging - get location names
        $provinceName = '';
        $districtName = '';
        $subdistrictName = '';
        $villageName = '';


        // Log location data for debugging
        \Log::info('Location data for edit - administration ID: ' . $id, [
            'province_id' => $administration->province_id,
            'district_id' => $administration->district_id,
            'subdistrict_id' => $administration->subdistrict_id,
            'village_id' => $administration->village_id,
            'province_name' => $provinceName,
            'district_name' => $districtName,
            'subdistrict_name' => $subdistrictName,
            'village_name' => $villageName
        ]);

        // Initialize empty arrays for district, sub-district, and village data
        // These will be populated via AJAX in the view
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // Debug information to verify data
        \Log::info("Editing administration record", [
            'id' => $id,
            'administration' => $administration->toArray(),
            'job_type_id' => $administration->job_type_id
        ]);

        return view('superadmin.datamaster.surat.administrasi.edit', compact(
            'administration',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers' // Add this line to pass signers to the view
        ));
    }

    /**
     * Update the specified administration letter in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Add debugging to see what data is being received
        \Log::info("Updating administration record", [
            'id' => $id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'nik' => 'required|numeric',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric',
            'job_type_id' => 'required|numeric',
            'religion' => 'required|numeric',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'letter_date' => 'required|date',
            'statement_content' => 'required|string',
            'purpose' => 'required|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            $administration = Administration::findOrFail($id);
            $administration->update($request->all());

            return redirect()->route('superadmin.surat.administrasi.index')
                ->with('success', 'Surat administrasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat administrasi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified administration from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $administration = Administration::findOrFail($id);
            $administration->delete();

            return redirect()->route('superadmin.surat.administrasi.index')
                ->with('success', 'Surat administrasi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat administrasi: ' . $e->getMessage());
        }
    }

    /**
     * Fetch all citizens for the administration form
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchAllCitizens(Request $request)
    {
        // Get search parameters
        $search = $request->input('search');

        // Use cache for non-search queries to improve performance
        $cacheKey = "admin_citizens_all";

        // Only use cache for non-search queries
        if (!$search && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Use getAllCitizensWithHighLimit to get all citizens without pagination limits
        $response = $search
            ? $this->citizenService->searchCitizens($search)
            : $this->citizenService->getAllCitizensWithHighLimit();

        // Log the response to see what we're receiving
        \Log::info('Citizens API response', [
            'structure' => json_encode(array_keys($response)),
            'hasData' => isset($response['data']),
            'count' => isset($response['data']) && is_array($response['data']) ? count($response['data']) : 'N/A'
        ]);

        // Extract citizens array from the response structure
        $citizens = [];
        $status = 'ERROR';

        if (isset($response['data']) && isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
            // Format from API: { data: { citizens: [...] } }
            $citizens = $response['data']['citizens'];
            $status = 'OK';
        } elseif (isset($response['data']) && is_array($response['data'])) {
            // Format directly: { data: [...] }
            $citizens = $response['data'];
            $status = 'OK';
        } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
            // Format: { citizens: [...] }
            $citizens = $response['citizens'];
            $status = 'OK';
        }

        // Log the number of citizens extracted
        \Log::info('Extracted citizens', ['count' => count($citizens)]);

        // Filter and include all fields needed for the administration form
        $citizens = array_map(function($citizen) {
            return [
                'nik' => $citizen['nik'] ?? null,
                'kk' => $citizen['kk'] ?? null,
                'full_name' => $citizen['full_name'] ?? '',
                'gender' => $citizen['gender'] ?? '',
                'birth_place' => $citizen['birth_place'] ?? '',
                'birth_date' => $citizen['birth_date'] ?? '',
                'address' => $citizen['address'] ?? '',
                'religion' => $citizen['religion'] ?? '',
                'citizen_status' => $citizen['citizen_status'] ?? '',
                'job_type_id' => $citizen['job_type_id'] ?? null,
                'rt' => $citizen['rt'] ?? '',
                'rw' => $citizen['rw'] ?? '',
                'postal_code' => $citizen['postal_code'] ?? '',
                'province_id' => $citizen['province_id'] ?? '',
                'district_id' => $citizen['district_id'] ?? '',
                'sub_district_id' => $citizen['sub_district_id'] ?? '',
                'village_id' => $citizen['village_id'] ?? '',
                'family_status' => $citizen['family_status'] ?? '',
            ];
        }, $citizens);

        // Prepare the response data
        $responseData = [
            'status' => $status,
            'count' => count($citizens),
            'data' => $citizens
        ];

        // Cache non-search results for 15 minutes
        if (!$search) {
            Cache::put($cacheKey, $responseData, now()->addMinutes(15));
        }

        return response()->json($responseData);
    }

    /**
     * Generate PDF for the specified administration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $administration = Administration::findOrFail($id);

            // Get job name from job service
            $jobName = '';
            if (!empty($administration->job_type_id)) {
                $job = $this->jobService->getJobById($administration->job_type_id);
                if ($job) {
                    $jobName = $job['name'];
                }
            }

            // Get location names using wilayah service - Improved location data retrieval
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';
            $villageCode = null; // Initialize village code variable

            // Get province data
            if (!empty($administration->province_id)) {
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $administration->province_id) {
                        $provinceName = $province['name'];

                        // Get province code for further queries
                        $provinceCode = $province['code'];

                        // Now get district data using province code
                        if (!empty($administration->district_id) && !empty($provinceCode)) {
                            $districts = $this->wilayahService->getKabupaten($provinceCode);
                            foreach ($districts as $district) {
                                if ($district['id'] == $administration->district_id) {
                                    $districtName = $district['name'];

                                    // Get district code for further queries
                                    $districtCode = $district['code'];

                                    // Now get subdistrict data using district code
                                    if (!empty($administration->subdistrict_id) && !empty($districtCode)) {
                                        $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                                        foreach ($subdistricts as $subdistrict) {
                                            if ($subdistrict['id'] == $administration->subdistrict_id) {
                                                $subdistrictName = $subdistrict['name'];

                                                // Get subdistrict code for further queries
                                                $subdistrictCode = $subdistrict['code'];

                                                // Finally get village data using subdistrict code
                                                if (!empty($administration->village_id) && !empty($subdistrictCode)) {
                                                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                                                    foreach ($villages as $village) {
                                                        if ($village['id'] == $administration->village_id) {
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

            // Log location data for debugging
            \Log::info('Location data for administration ID: ' . $id, [
                'province_id' => $administration->province_id,
                'district_id' => $administration->district_id,
                'subdistrict_id' => $administration->subdistrict_id,
                'village_id' => $administration->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'village_code' => $villageCode // Log the village code
            ]);

            // Format gender
            $gender = $administration->gender == 1 ? 'Laki-Laki' : 'Perempuan';

            // Format religion
            $religions = [
                1 => 'Islam',
                2 => 'Kristen',
                3 => 'Katholik',
                4 => 'Hindu',
                5 => 'Buddha',
                6 => 'Kong Hu Cu',
                7 => 'Lainnya'
            ];
            $religion = $religions[$administration->religion] ?? '';

            // Format citizenship
            $citizenship = $administration->citizen_status == 1 ? 'WNA' : 'WNI';

            // Format date for display
            $birthDate = \Carbon\Carbon::parse($administration->birth_date)->format('d-m-Y');
            $letterDate = \Carbon\Carbon::parse($administration->letter_date)->format('d-m-Y');

            // Don't modify the RT value, leave it exactly as stored in the database
            $rt = $administration->rt;

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($administration->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($administration->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            return view('superadmin.datamaster.surat.administrasi.AdministrasiUmum', [
                'administration' => $administration,
                'job_name' => $jobName,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'village_code' => $villageCode, // Pass the complete village code
                'gender' => $gender,
                'religion' => $religion,
                'citizenship' => $citizenship,
                'formatted_birth_date' => $birthDate,
                'formatted_letter_date' => $letterDate,
                'rt' => $rt,
                'signing_name' => $signing_name // Pass the signing name to the view
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }
}
