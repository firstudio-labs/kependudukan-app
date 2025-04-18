<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KK;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DataKKController extends Controller
{
    protected $citizenService;
    protected $wilayahService;
    protected $jobService;

    public function __construct(
        CitizenService $citizenService,
        WilayahService $wilayahService,
        JobService $jobService
    )
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
        $this->jobService = $jobService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');

        if ($search) {
            $kk = $this->citizenService->searchCitizens($search);
            // If search returns null or error, fallback to getting all citizens
            if (!$kk || isset($kk['status']) && $kk['status'] === 'ERROR') {
                $kk = $this->citizenService->getAllCitizens($page);
                session()->flash('warning', 'Search failed, showing all results instead');
            }
        } else {
            $kk = $this->citizenService->getAllCitizens($page);
        }

        // Get family member counts for each KK
        if (isset($kk['data']['citizens']) && is_array($kk['data']['citizens'])) {
            foreach ($kk['data']['citizens'] as $index => $citizen) {
                if (isset($citizen['kk']) && !empty($citizen['kk'])) {
                    // Get family members for this KK
                    $familyMembers = $this->citizenService->getFamilyMembersByKK($citizen['kk']);

                    // Set the count if family members were found
                    if ($familyMembers && isset($familyMembers['data']) && is_array($familyMembers['data'])) {
                        $kk['data']['citizens'][$index]['jml_anggota_kk'] = count($familyMembers['data']);
                    } else {
                        $kk['data']['citizens'][$index]['jml_anggota_kk'] = 0;
                    }
                } else {
                    $kk['data']['citizens'][$index]['jml_anggota_kk'] = 0;
                }
            }
        }

        return view('superadmin.datakk.index', compact('kk', 'search'));
    }

    public function create()
    {
        // Get provinces data with caching
        $provinces = $this->wilayahService->getProvinces();

        // Get jobs data
        $jobs = $this->jobService->getAllJobs();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datakk.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'jobs'
        ));
    }

    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'kk' => 'required|string',
            'full_name' => 'required|string',
            'address' => 'required|string',
            // Add other validation rules as needed
        ]);

        // Create KK record
        $kk = KK::create([
            'kk' => $request->kk,
            'full_name' => $request->full_name,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'jml_anggota_kk' => $request->jml_anggota_kk,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'sub_district_id' => $request->sub_district_id,
            'village_id' => $request->village_id,
            'dusun' => $request->dusun,
            'alamat_luar_negeri' => $request->alamat_luar_negeri,
            'kota' => $request->kota,
            'negara_bagian' => $request->negara_bagian,
            'negara' => $request->negara,
            'kode_pos_luar_negeri' => $request->kode_pos_luar_negeri,
        ]);

        // If you have family members in the request, create them
        if ($request->has('family_members')) {
            foreach ($request->family_members as $member) {
                if (!empty($member['full_name']) && !empty($member['family_status'])) {
                    $kk->familyMembers()->create([
                        'full_name' => $member['full_name'],
                        'family_status' => $member['family_status'],
                    ]);
                }
            }
        }

        return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil disimpan');
    }

    public function edit($id)
    {
        $kk = KK::findOrFail($id);

        // Get provinces data with caching
        $provinces = Cache::remember('provinces', 3600, function () {
            return $this->wilayahService->getProvinces();
        });

        // Log the province data for debugging
        Log::info('Provinces data for KK edit', [
            'count' => count($provinces),
            'kk_province_id' => $kk->province_id
        ]);

        // Get location data for pre-selecting dropdowns
        $districts = $this->wilayahService->getKabupaten($kk->province_id);
        $subDistricts = $this->wilayahService->getKecamatan($kk->district_id);
        $villages = $this->wilayahService->getDesa($kk->sub_district_id);

        // Log the fetched location data to ensure it's being retrieved correctly
        Log::info('Location data for KK edit', [
            'districts_count' => count($districts),
            'sub_districts_count' => count($subDistricts),
            'villages_count' => count($villages),
        ]);

        return view('superadmin.datakk.update', compact(
            'kk',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    public function update(Request $request, $id)
    {
        // Validasi input tanpa kk dan full_name
        $validatedData = $request->validate([
            'address' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'jml_anggota_kk' => 'required|integer',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'province_id' => 'required|string|max:50',
            'district_id' => 'required|string|max:50',
            'sub_district_id' => 'required|string|max:50',
            'village_id' => 'required|string|max:50',
            'dusun' => 'nullable|string|max:50',
            'alamat_luar_negeri' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'negara_bagian' => 'nullable|string|max:50',
            'negara' => 'nullable|string|max:50',
            'kode_pos_luar_negeri' => 'nullable|string|max:10',
        ]);

        $kk = KK::findOrFail($id);
        $kk->fill($validatedData);
        $kk->save();

        return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $kk = KK::findOrFail($id);
            $kk->delete();

            return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.datakk.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function fetchAllCitizens(Request $request)
    {
        // Get search parameters
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);

        // Use cache for non-search queries to improve performance
        $cacheKey = "citizens_all_{$page}_{$limit}";

        // Only use cache for non-search queries
        if (!$search && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get citizens with parameters and transform to expected format
        $response = $search
            ? $this->citizenService->searchCitizens($search, $page, $limit)
            : $this->citizenService->getAllCitizensWithHighLimit($page, $limit);

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
        }

        // Filter only needed fields to reduce response size
        $citizens = array_map(function($citizen) {
            return [
                'kk' => $citizen['kk'] ?? '',
                'full_name' => $citizen['full_name'] ?? '',
                'address' => $citizen['address'] ?? '',
                'postal_code' => $citizen['postal_code'] ?? '',
                'rt' => $citizen['rt'] ?? '',
                'rw' => $citizen['rw'] ?? '',
                'telepon' => $citizen['telepon'] ?? '',
                'email' => $citizen['email'] ?? '',
                'province_id' => $citizen['province_id'] ?? '',
                'district_id' => $citizen['district_id'] ?? '',
                'sub_district_id' => $citizen['sub_district_id'] ?? '',
                'village_id' => $citizen['village_id'] ?? '',
                // Map dusun field to hamlet for consistency
                'hamlet' => $citizen['dusun'] ?? $citizen['hamlet'] ?? '',
                'family_status' => $citizen['family_status'] ?? '',

                // Add foreign address fields with consistent naming
                'foreign_address' => $citizen['foreign_address'] ?? $citizen['alamat_luar_negeri'] ?? '',
                'city' => $citizen['city'] ?? $citizen['kota'] ?? '',
                'state' => $citizen['state'] ?? $citizen['negara_bagian'] ?? '',
                'country' => $citizen['country'] ?? $citizen['negara'] ?? '',
                'foreign_postal_code' => $citizen['foreign_postal_code'] ?? $citizen['kode_pos_luar_negeri'] ?? '',
            ];
        }, $citizens);

        // Prepare the response
        $responseData = [
            'status' => $status,
            'count' => count($citizens),
            'data' => $citizens
        ];

        // Cache non-search results for 5 minutes
        if (!$search) {
            Cache::put($cacheKey, $responseData, now()->addMinutes(5));
        }

        return response()->json($responseData);
    }

    public function getFamilyMembers(Request $request)
    {
        $kk = $request->input('kk');

        // Use a short cache for family members (1 minute)
        $cacheKey = "family_members_{$kk}";

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get family members
        $familyMembers = $this->citizenService->getFamilyMembersByKK($kk);

        if ($familyMembers && isset($familyMembers['data']) && is_array($familyMembers['data'])) {
            // Sort family members to put Kepala Keluarga first
            $sortedMembers = collect($familyMembers['data'])->sortBy(function ($member) {
                $order = [
                    'KEPALA KELUARGA' => 1,
                    'ISTRI' => 2,
                    'ANAK' => 3
                ];
                return $order[$member['family_status']] ?? 999;
            })->values()->all();

            // Prepare response
            $responseData = [
                'status' => 'OK',
                'count' => count($sortedMembers),
                'data' => $sortedMembers
            ];

            // Cache the result for 1 minute
            Cache::put($cacheKey, $responseData, now()->addMinute());

            return response()->json($responseData);
        }

        return response()->json([
            'status' => 'ERROR',
            'count' => 0,
            'message' => 'Gagal mengambil data anggota keluarga'
        ]);
    }

    public function getFamilyMembersByKK($kk_id)
    {
        try {
            $kk = KK::findOrFail($kk_id);

            // Cache family members for better performance
            $cacheKey = "kk_family_members_{$kk_id}";

            return Cache::remember($cacheKey, 300, function () use ($kk) {
                $familyMembers = $kk->familyMembers()->get();

                return response()->json([
                    'status' => 'OK',
                    'count' => $familyMembers->count(),
                    'data' => $familyMembers
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal mengambil data anggota keluarga'
            ], 500);
        }
    }

    // Add these methods for location data
    /**
     * Get provinces with both ID and code for mapping purposes
     */
    public function getProvinces()
    {
        $provinces = $this->wilayahService->getProvinces();

        // Log the data to verify it contains both ID and code
        Log::info('Provinces data', [
            'count' => count($provinces),
            'sample' => !empty($provinces) ? $provinces[0] : 'No data'
        ]);

        return response()->json(['data' => $provinces]);
    }

    /**
     * Get districts with both ID and code for mapping purposes
     */
    public function getDistricts($provinceCode)
    {
        $districts = $this->wilayahService->getKabupaten($provinceCode);

        // Log the data to verify it contains both ID and code
        Log::info('Districts data', [
            'for_province' => $provinceCode,
            'count' => count($districts),
            'sample' => !empty($districts) ? $districts[0] : 'No data'
        ]);

        return response()->json($districts);
    }

    public function getSubDistricts($districtCode)
    {
        $subDistricts = $this->wilayahService->getKecamatan($districtCode);
        return response()->json($subDistricts);
    }

    public function getVillages($subDistrictCode)
    {
        $villages = $this->wilayahService->getDesa($subDistrictCode);
        return response()->json($villages);
    }

    /**
     * Store a new family member for an existing KK
     */
    public function storeFamilyMember(Request $request)
    {
        try {
            // Debug log to see what's coming in
            Log::info('Family Member Store - Request data:', [
                'kk' => $request->input('kk'),
                'request_has_kk' => $request->has('kk'),
                'all_data' => $request->all(),
            ]);

            $validator = Validator::make($request->all(), [
                'nik' => 'required|size:16',
                'kk' => 'required|size:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|integer|in:1,2',
                'birth_date' => 'required|date',
                'age' => 'required|integer',
                'birth_place' => 'required|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'required|string|max:3',
                'rw' => 'required|string|max:3',
                'postal_code' => 'nullable|digits:5',
                'citizen_status' => 'required|integer|in:1,2',
                'birth_certificate' => 'integer|in:1,2',
                'birth_certificate_no' => 'nullable|string',
                'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                'religion' => 'required|in:1,2,3,4,5,6,7',
                'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                'marital_certificate' => 'required|in:1,2',
                'marital_certificate_no' => 'nullable|string',
                'marriage_date' => 'nullable|date',
                'divorce_certificate' => 'nullable|integer|in:1,2',
                'divorce_certificate_no' => 'nullable|string',
                'divorce_certificate_date' => 'nullable|date',
                'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                'mental_disorders' => 'required|integer|in:1,2',
                'disabilities' => 'required|integer|in:1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255', // Changed from 'required' to 'nullable'
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255', // Changed from 'required' to 'nullable'
                'coordinate' => 'nullable|string|max:255',
                // New fields for foreign address
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
            ]);

            // Pemeriksaan apakah validasi gagal
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
            }

            // Ambil data yang sudah divalidasi
            $validatedData = $validator->validated();

            // Process nullable fields
            $this->processNullableFields($validatedData);

            // Convert NIK and KK to integers
            $validatedData['nik'] = (int) $validatedData['nik'];
            $validatedData['kk'] = (int) $validatedData['kk'];
            $validatedData['religion'] = (int) $validatedData['religion'];

            // Call the citizen service to create the new family member
            $response = $this->citizenService->createCitizen($validatedData);

            if ($response['status'] === 'CREATED') {
                return redirect()
                    ->route('superadmin.datakk.create')
                    ->with('success', 'Anggota keluarga berhasil ditambahkan!');
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal menyimpan data anggota keluarga');

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan anggota keluarga: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store multiple family members for an existing KK
     */
    public function storeFamilyMembers(Request $request)
    {
        try {
            // Debug log to see what's coming in
            Log::info('Multiple Family Members Store - Request data:', [
                'kk' => $request->input('kk'),
                'family_members_count' => $request->has('family_members') ? count($request->input('family_members')) : 0,
            ]);

            // Validate that we have family members to process
            if (!$request->has('family_members') || !is_array($request->input('family_members')) || count($request->input('family_members')) === 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Tidak ada data anggota keluarga untuk disimpan');
            }

            // Initialize counters
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Process each family member
            foreach ($request->input('family_members') as $index => $memberData) {
                $validator = Validator::make($memberData, [
                    'nik' => 'required|size:16',
                    'kk' => 'required|size:16',
                    'full_name' => 'required|string|max:255',
                    'gender' => 'required|integer|in:1,2',
                    'birth_date' => 'required|date',
                    'age' => 'required|integer',
                    'birth_place' => 'required|string|max:255',
                    'address' => 'required|string',
                    'province_id' => 'required|integer',
                    'district_id' => 'required|integer',
                    'sub_district_id' => 'required|integer',
                    'village_id' => 'required|integer',
                    'rt' => 'required|string|max:3',
                    'rw' => 'required|string|max:3',
                    'postal_code' => 'nullable|digits:5',
                    'citizen_status' => 'required|integer|in:1,2',
                    'birth_certificate' => 'integer|in:1,2',
                    'birth_certificate_no' => 'nullable|string',
                    'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                    'religion' => 'required|in:1,2,3,4,5,6,7',
                    'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                    'marital_certificate' => 'required|in:1,2',
                    'marital_certificate_no' => 'nullable|string',
                    'marriage_date' => 'nullable|date',
                    'divorce_certificate' => 'nullable|integer|in:1,2',
                    'divorce_certificate_no' => 'nullable|string',
                    'divorce_certificate_date' => 'nullable|date',
                    'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                    'mental_disorders' => 'required|integer|in:1,2',
                    'disabilities' => 'required|integer|in:1,2,3,4,5,6',
                    'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                    'job_type_id' => 'required|integer',
                    'nik_mother' => 'nullable|string|size:16',
                    'mother' => 'nullable|string|max:255',
                    'nik_father' => 'nullable|string|size:16',
                    'father' => 'nullable|string|max:255',
                    'coordinate' => 'nullable|string|max:255',
                    'telephone' => 'nullable|string|max:20',
                    'email' => 'nullable|email|max:255',
                    'hamlet' => 'nullable|string|max:100',
                    'foreign_address' => 'nullable|string',
                    'city' => 'nullable|string|max:100',
                    'state' => 'nullable|string|max:100',
                    'country' => 'nullable|string|max:100',
                    'foreign_postal_code' => 'nullable|string|max:20',
                    'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                ]);

                if ($validator->fails()) {
                    $errorCount++;
                    $errors[] = "Anggota #{$index}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Get validated data
                $validatedData = $validator->validated();

                // Process nullable fields
                $this->processNullableFields($validatedData);

                // Convert NIK and KK to integers
                $validatedData['nik'] = (int) $validatedData['nik'];
                $validatedData['kk'] = (int) $validatedData['kk'];
                $validatedData['religion'] = (int) $validatedData['religion'];

                // Call the citizen service to create the new family member
                $response = $this->citizenService->createCitizen($validatedData);

                if ($response['status'] === 'CREATED') {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Anggota #{$index} ({$validatedData['full_name']}): " . ($response['message'] ?? 'Gagal menyimpan data');
                }
            }

            // Create response message based on results
            if ($successCount > 0 && $errorCount === 0) {
                return redirect()
                    ->route('superadmin.datakk.create')
                    ->with('success', "Berhasil menambahkan {$successCount} anggota keluarga!");
            } else if ($successCount > 0 && $errorCount > 0) {
                return redirect()
                    ->route('superadmin.datakk.create')
                    ->with('warning', "Berhasil menambahkan {$successCount} anggota keluarga, tetapi {$errorCount} gagal: " . implode('; ', $errors));
            } else {
                return back()
                    ->withInput()
                    ->with('error', "Gagal menambahkan anggota keluarga: " . implode('; ', $errors));
            }

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan anggota keluarga: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process nullable fields to ensure they have appropriate default values
     */
    private function processNullableFields(&$data)
    {
        $nullableIntegerFields = ['marital_status', 'marital_certificate', 'divorce_certificate', 'postal_code'];
        foreach ($nullableIntegerFields as $field) {
            $data[$field] = empty($data[$field]) ? 0 : (int) $data[$field];
        }

        $nullableStringFields = ['birth_certificate_no', 'marital_certificate_no', 'divorce_certificate_no',
                               'nik_mother', 'nik_father', 'coordinate', 'telephone', 'email', 'hamlet',
                               'foreign_address', 'city', 'state', 'country', 'foreign_postal_code', 'status'];
        foreach ($nullableStringFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : $data[$field];
        }

        $nullableDateFields = ['marriage_date', 'divorce_certificate_date'];
        foreach ($nullableDateFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : date('Y-m-d', strtotime($data[$field]));
        }

        $integerFields = ['gender', 'age', 'province_id', 'district_id', 'sub_district_id',
                         'village_id', 'citizen_status', 'birth_certificate', 'blood_type',
                         'religion', 'family_status', 'mental_disorders', 'disabilities',
                         'education_status', 'job_type_id'];
        foreach ($integerFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }

        $dateFields = ['birth_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
    }
}

