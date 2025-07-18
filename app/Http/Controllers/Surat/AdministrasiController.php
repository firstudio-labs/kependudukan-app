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
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        // Jika user adalah admin desa, filter berdasarkan village_id
        if (Auth::user()->role === 'admin desa') {
            $villageId = Auth::user()->villages_id; // field pada tabel users
            $query->where('village_id', $villageId); // field pada tabel administration
        }

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

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.administrasi.index', compact('administrations'));
        }

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


        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.administrasi.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.administrasi.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
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
            'signing' => 'nullable|string', // Ensure signing is nullable
        ]);

        try {
            Administration::create($request->all());
            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.administrasi.index')
                    ->with('success', 'Surat administrasi berhasil dibuat!');
            }

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

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.administrasi.edit', compact(
            'administration',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers' // Add this line to pass signers to the view
            ));
        }

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
            'signing' => 'nullable|string', // Ensure signing is nullable
        ]);

        try {
            $administration = Administration::findOrFail($id);
            $data = $request->all();

            // Set is_accepted field if it's provided in the form
            $data['is_accepted'] = $request->has('is_accepted') ? 1 : 0;

            $administration->update($data);

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.administrasi.index')
                    ->with('success', 'Surat administrasi berhasil diperbarui!');
            }

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

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.administrasi.index')
                    ->with('success', 'Surat administrasi berhasil dihapus!');
            }

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
        $villageId = $request->input('village_id');

        // Log minimal untuk debugging
        \Log::info('fetchAllCitizens called', [
            'search' => $search,
            'village_id' => $villageId
        ]);

        // Use cache for non-search queries to improve performance
        $cacheKey = "admin_citizens_all" . ($villageId ? "_village_{$villageId}" : "");

        // Only use cache for non-search queries
        if (!$search && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Ambil semua data citizen dari API (tanpa filter)
        $response = $search
            ? $this->citizenService->searchCitizens($search)
            : $this->citizenService->getAllCitizensWithHighLimit();

        // Extract citizens array from the response structure
        $citizens = [];
        $status = 'ERROR';

        if (isset($response['data']) && isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
            $citizens = $response['data']['citizens'];
            $status = 'OK';
        } elseif (isset($response['data']) && is_array($response['data'])) {
            $citizens = $response['data'];
            $status = 'OK';
        } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
            $citizens = $response['citizens'];
            $status = 'OK';
        }

        // Filter citizens by village_id if provided (optimized)
        if ($villageId && !empty($villageId)) {
            $originalCount = count($citizens);

            // Optimized filtering
            $filteredCitizens = array_filter($citizens, function($citizen) use ($villageId) {
                $citizenVillageId = $citizen['village_id'] ??
                                   $citizen['villages_id'] ??
                                   $citizen['sub_district_id'] ??
                                   null;
                return $citizenVillageId == $villageId;
            });

            // Fallback jika tidak ada data yang cocok
            if (empty($filteredCitizens)) {
                \Log::warning('No citizens found for village_id, using all citizens as fallback', [
                    'village_id' => $villageId,
                    'total_citizens' => $originalCount
                ]);
                $filteredCitizens = $citizens;
            } else {
                \Log::info('Filtered citizens by village_id', [
                    'villageId' => $villageId,
                    'originalCount' => $originalCount,
                    'filteredCount' => count($filteredCitizens)
                ]);
            }

            $citizens = $filteredCitizens;
        }

        // Optimized processing - hanya log sample untuk debugging
        $processedCitizens = [];
        $sampleLogged = false;

        foreach ($citizens as $citizen) {
            $processedCitizen = [
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
                'village_id' => $citizen['village_id'] ?? $citizen['villages_id'] ?? '',
                'family_status' => $citizen['family_status'] ?? '',
                'rf_id_tag' => $citizen['rf_id_tag'] ?? null,
            ];

            // Log sample citizen hanya sekali untuk debugging
            if (!$sampleLogged && (empty($processedCitizen['nik']) || empty($processedCitizen['full_name']))) {
                \Log::warning('Sample citizen with missing required fields', [
                    'original_citizen' => $citizen,
                    'processed_citizen' => $processedCitizen
                ]);
                $sampleLogged = true;
            }

            $processedCitizens[] = $processedCitizen;
        }

        // Prepare the response data
        $responseData = [
            'status' => $status,
            'count' => count($processedCitizens),
            'data' => array_values($processedCitizens), // Pastikan array
            'debug' => [
                'village_id_requested' => $villageId,
                'total_citizens' => count($processedCitizens),
                'filtered' => !empty($villageId)
            ]
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
                $penandatangan = Penandatangan::find($administration->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get user image based on matching district_id
            $districtLogo = null;
            if (!empty($administration->district_id)) {
                $userWithLogo = User::where('districts_id', $administration->district_id)
                    ->whereNotNull('image')
                    ->first();

                if ($userWithLogo && $userWithLogo->image) {
                    $districtLogo = $userWithLogo->image;
                }
            }

            // Log the logo information for debugging
            \Log::info('District logo for administration ID: ' . $id, [
                'district_id' => $administration->district_id,
                'logo_found' => !is_null($districtLogo),
                'logo_path' => $districtLogo
            ]);

            if (Auth::user()->role === 'admin desa') {
                return view('admin.desa.surat.administrasi.AdministrasiUmum', [
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
                    'signing_name' => $signing_name, // Pass the signing name to the view
                    'district_logo' => $districtLogo // Pass the district logo to the view
                ]);
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
                'signing_name' => $signing_name, // Pass the signing name to the view
                'district_logo' => $districtLogo // Pass the district logo to the view
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }

    // Tambahkan method untuk cache management
    private function getCacheKey($search, $villageId)
    {
        if ($search) {
            return "admin_citizens_search_" . md5($search);
        }

        if ($villageId) {
            return "admin_citizens_village_{$villageId}";
        }

        return "admin_citizens_all";
    }

    private function shouldUseCache($search)
    {
        // Hanya cache untuk non-search queries
        return !$search;
    }
}
