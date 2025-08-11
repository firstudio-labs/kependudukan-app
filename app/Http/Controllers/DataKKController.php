<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KK;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class DataKKController extends Controller
{
    protected $citizenService;
    protected $wilayahService;
    protected $jobService;

    public function __construct(
        CitizenService $citizenService,
        WilayahService $wilayahService,
        JobService $jobService
    ) {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
        $this->jobService = $jobService;
    }

    public function index(Request $request)
    {
        // Debug admin village info
        if (Auth::user()->role === 'admin desa') {
            Log::info('Admin Desa User Details:', [
                'id' => Auth::user()->id,
                'username' => Auth::user()->username,
                'villages_id' => Auth::user()->villages_id ?? 'Not set',
                'role' => Auth::user()->role
            ]);
        }

        // If export parameter exists, call the export function directly
        if ($request->has('export')) {
            return $this->export();
        }

        $page = $request->input('page', 1);
        $search = $request->input('search');
        $villagesId = null;

        // Get villages_id filter for admin desa
        if (Auth::user()->role === 'admin desa' && Auth::user()->villages_id) {
            $villagesId = Auth::user()->villages_id;
            Log::info('Admin desa has villages_id: ' . $villagesId);
        }

        // Get data based on user role and search parameters
        if (Auth::user()->role === 'admin desa' && $villagesId) {
            if ($search) {
                // Use the service method that supports search with village filtering
                $kk = $this->citizenService->getCitizensByVillageId($villagesId, $page, 10, $search);
            } else {
                // Direct call to get citizens by village ID with pagination
                $kk = $this->citizenService->getCitizensByVillageId($villagesId, $page);

                // Verify village ID filtering
                Log::info('Direct KK village query results', [
                    'village_id' => $villagesId,
                    'has_data' => isset($kk['data']['citizens']),
                    'count' => isset($kk['data']['citizens']) ? count($kk['data']['citizens']) : 0,
                    'pagination' => isset($kk['data']['pagination']) ? $kk['data']['pagination'] : 'none'
                ]);
            }
        } else {
            // For superadmin or other roles, show all data with consistent search
            if ($search) {
                // Gunakan method baru yang melakukan filtering lokal seperti admin desa
                $kk = $this->citizenService->getAllCitizensWithSearch($page, 10, $search);
            } else {
                $kk = $this->citizenService->getAllCitizensWithSearch($page, 10);
            }
        }

        // Prepare pagination data
        $paginationData = [];
        if (isset($kk['data']['pagination'])) {
            $paginationData = [
                'current_page' => $kk['data']['pagination']['current_page'],
                'total_page' => $kk['data']['pagination']['total_page'],
                'base_url' => Auth::user()->role === 'admin desa'
                    ? route('admin.desa.datakk.index') . '?'
                    : route('superadmin.datakk.index') . '?',
                'search' => $search
            ];
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

                        // Tambahkan NIK kepala keluarga jika tersedia
                        foreach ($familyMembers['data'] as $member) {
                            if (isset($member['family_status']) && strtoupper($member['family_status']) === 'KEPALA KELUARGA') {
                                $kk['data']['citizens'][$index]['nik'] = $member['nik'] ?? null;
                                break;
                            }
                        }
                    } else {
                        $kk['data']['citizens'][$index]['jml_anggota_kk'] = 0;
                    }
                } else {
                    $kk['data']['citizens'][$index]['jml_anggota_kk'] = 0;
                }
            }
        }

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.datakk.index', compact('kk', 'search', 'paginationData'));
        }

        return view('superadmin.datakk.index', compact('kk', 'search', 'paginationData'));
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

        if (Auth::user()->role === 'admin') {
            return view('admin.desa.datakk.create', compact(
                'provinces',
                'districts',
                'subDistricts',
                'villages',
                'jobs'
            ));
        }

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
            'rf_id_tag' => $request->rf_id_tag,
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

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.desa.datakk.index')->with('success', 'Data KK berhasil disimpan');
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

        if (Auth::user()->role === 'admin') {
            return view('admin.desa.datakk.update', compact(
                'kk',
                'provinces',
                'districts',
                'subDistricts',
                'villages'
            ));
        }

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
            'religion' => 'required|integer|in:1,2,3,4,5,6,7',
            'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
            'marital_certificate' => 'required|in:1,2',
            'marital_certificate_no' => 'nullable|string',
            'marriage_date' => 'nullable|date',
            'divorce_certificate' => 'nullable|integer|in:1,2',
            'divorce_certificate_no' => 'nullable|string',
            'divorce_certificate_date' => 'nullable|date',
            'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
            'mental_disorders' => 'required|integer|in:1,2',
            'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
            'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
            'job_type_id' => 'required|integer',
            'nik_mother' => 'nullable|string|size:16',
            'mother' => 'nullable|string|max:255',
            'nik_father' => 'nullable|string|size:16',
            'father' => 'nullable|string|max:255',
            'coordinate' => 'nullable|string|max:255',
            // New fields
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'hamlet' => 'nullable|string|max:100',
            'foreign_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'foreign_postal_code' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
            'rf_id_tag' => 'nullable|string',
        ]);

        $kk = KK::findOrFail($id);
        $kk->fill($validatedData);
        $kk->save();

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.desa.datakk.index')->with('success', 'Data KK berhasil diperbarui!');
        }

        return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $kk = KK::findOrFail($id);
            $kk->delete();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.desa.datakk.index')->with('success', 'Data KK berhasil dihapus!');
            }

            return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil dihapus!');
        } catch (\Exception $e) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.desa.datakk.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
            }

            return redirect()->route('superadmin.datakk.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function fetchAllCitizens(Request $request)
    {
        // Get search parameters
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);
        $villagesId = null;

        // Check if the user is admin desa and has a villages_id
        if (Auth::user()->role === 'admin desa' && Auth::user()->villages_id) {
            $villagesId = Auth::user()->villages_id;
            Log::info('Admin desa fetching citizens with villages_id: ' . $villagesId);
        }

        // Use cache for non-search queries to improve performance
        $cacheKey = "citizens_all_{$page}_{$limit}" . ($villagesId ? "_village_{$villagesId}" : "");

        // Only use cache for non-search queries
        if (!$search && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get citizens from API without filtering
        $response = $search
            ? $this->citizenService->searchCitizens($search)
            : $this->citizenService->getAllCitizensWithHighLimit();

        // Normalize village IDs in the response
        $response = $this->citizenService->normalizeVillageIdFields($response);

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

        // Apply filtering for admin desa
        if (Auth::user()->role === 'admin desa' && $villagesId && !empty($citizens)) {
            $originalCount = count($citizens);

            // Filter by villages_id
            $citizens = array_filter($citizens, function($citizen) use ($villagesId) {
                // Now we can consistently use villages_id
                return isset($citizen['villages_id']) && $citizen['villages_id'] == $villagesId;
            });

            // Re-index array
            $citizens = array_values($citizens);
            $filteredCount = count($citizens);

            Log::info('Filtered citizens for admin desa in fetchAllCitizens:', [
                'villages_id' => $villagesId,
                'original_count' => $originalCount,
                'filtered_count' => $filteredCount
            ]);
        }

        // Filter only needed fields to reduce response size
        $citizens = array_map(function ($citizen) {
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
                'villages_id' => $citizen['villages_id'] ?? $citizen['village_id'] ?? '',
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

        // Log if admin desa is accessing family members data
        if (Auth::user()->role === 'admin desa' && Auth::user()->villages_id) {
            Log::info('Admin desa accessing family members for KK: ' . $kk, [
                'villages_id' => Auth::user()->villages_id
            ]);
        }

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
                'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
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
                'rf_id_tag' => 'nullable|integer',
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
                if (Auth::user()->role === 'admin') {
                    return redirect()
                        ->route('admin.desa.datakk.create')
                        ->with('success', 'Anggota keluarga berhasil ditambahkan!');
                }

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
            // Get family members data from JSON
            $familyMembersJson = $request->input('family_members_json');
            $familyMembers = json_decode($familyMembersJson, true);

            // Debug log to see what's coming in
            Log::info('Multiple Family Members Store - Request data:', [
                'kk' => $request->input('kk'),
                'family_members_count' => count($familyMembers),
            ]);

            // Validate that we have family members to process
            if (empty($familyMembers) || !is_array($familyMembers)) {
                return back()
                    ->withInput()
                    ->with('error', 'Tidak ada data anggota keluarga untuk disimpan');
            }

            // Initialize counters
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Process each family member
            foreach ($familyMembers as $index => $memberData) {
                $validator = Validator::make($memberData, [
                    'nik' => 'required|size:16',
                    'kk' => 'required|size:16',
                    'full_name' => 'required|string|max:255',
                    'gender' => 'required|integer|in:1,2',
                    'birth_date' => 'required|date',
                    'age' => 'required|integer',
                    'birth_place' => 'required|string|max:255',
                    'address' => 'required|string',
                    'province_id' => 'required',
                    'district_id' => 'required',
                    'sub_district_id' => 'required',
                    'village_id' => 'required',
                    'rt' => 'required|string|max:3',
                    'rw' => 'required|string|max:3',
                    'postal_code' => 'nullable|string|max:5',
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
                    'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
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
                    'rf_id_tag' => 'nullable|integer',
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

                // Convert NIK and KK to integers for API compatibility
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
                if (Auth::user()->role === 'admin') {
                    return redirect()
                        ->route('admin.desa.datakk.create')
                        ->with('success', "Berhasil menambahkan {$successCount} anggota keluarga!");
                }

                return redirect()
                    ->route('superadmin.datakk.create')
                    ->with('success', "Berhasil menambahkan {$successCount} anggota keluarga!");
            } else if ($successCount > 0 && $errorCount > 0) {
                if (Auth::user()->role === 'admin') {
                    return redirect()
                        ->route('admin.desa.datakk.create')
                        ->with('warning', "Berhasil menambahkan {$successCount} anggota keluarga, tetapi {$errorCount} gagal: " . implode('; ', $errors));
                }

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

        $nullableStringFields = [
            'birth_certificate_no',
            'marital_certificate_no',
            'divorce_certificate_no',
            'nik_mother',
            'nik_father',
            'coordinate',
            'telephone',
            'email',
            'hamlet',
            'foreign_address',
            'city',
            'state',
            'country',
            'foreign_postal_code',
            'status'
        ];
        foreach ($nullableStringFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : $data[$field];
        }

        $nullableDateFields = ['marriage_date', 'divorce_certificate_date'];
        foreach ($nullableDateFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : date('Y-m-d', strtotime($data[$field]));
        }

        $integerFields = [
            'gender',
            'age',
            'province_id',
            'district_id',
            'sub_district_id',
            'village_id',
            'citizen_status',
            'birth_certificate',
            'blood_type',
            'religion',
            'family_status',
            'mental_disorders',
            'disabilities',
            'education_status',
            'job_type_id'
        ];
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

    /**
     * Export citizen data to Excel
     */
    public function export()
    {
        try {
            $villagesId = null;

            // Get villages_id filter for admin desa
            if (Auth::user()->role === 'admin desa' && Auth::user()->villages_id) {
                $villagesId = Auth::user()->villages_id;
                Log::info('Filtering export data for admin desa with villages_id: ' . $villagesId);
            }

            // Ambil semua data dari service tanpa filter
            $response = $this->citizenService->getAllCitizensWithHighLimit();
            $exportData = [];

            // Header untuk Excel
            $exportData[] = [
                'NIK',
                'Nomor KK',
                'Nama Lengkap',
                'Jenis Kelamin',
                'Tanggal Lahir',
                'Tempat Lahir',
                'Usia',
                'Alamat',
                'RT',
                'RW',
                'Provinsi',
                'Kabupaten',
                'Kecamatan',
                'Desa',
                'Kode Pos',
                'Status Kewarganegaraan',
                'Agama',
                'Golongan Darah',
                'Status Dalam Keluarga',
                'Nama Ayah',
                'Nama Ibu',
                'NIK Ayah',
                'NIK Ibu',
            ];

            // Periksa response struktur data
            $citizens = [];
            if (isset($response['data']) && isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
                $citizens = $response['data']['citizens'];
            } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
                $citizens = $response['citizens'];
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $citizens = $response['data'];
            }

            // Filter by villages_id if admin desa
            if (Auth::user()->role === 'admin desa' && $villagesId && !empty($citizens)) {
                $originalCount = count($citizens);

                $citizens = array_filter($citizens, function($citizen) use ($villagesId) {
                    $citizenVillageId = null;

                    if (isset($citizen['villages_id'])) {
                        $citizenVillageId = $citizen['villages_id'];
                    } else if (isset($citizen['village_id'])) {
                        $citizenVillageId = $citizen['village_id'];
                    }

                    return $citizenVillageId == $villagesId;
                });

                $citizens = array_values($citizens);
                $filteredCount = count($citizens);

                Log::info('Filtered export data:', [
                    'villages_id' => $villagesId,
                    'original_count' => $originalCount,
                    'filtered_count' => $filteredCount,
                    'filter_type' => 'village_id filter'
                ]);
            }

            if (empty($citizens)) {
                return redirect()->route('admin.desa.datakk.index')
                    ->with('error', 'Tidak ada data yang bisa diekspor atau format data tidak sesuai');
            }

            foreach ($citizens as $citizen) {
                $exportData[] = [
                    $citizen['nik'] ?? '',
                    $citizen['kk'] ?? '',
                    $citizen['full_name'] ?? '',
                    $this->formatGender($citizen['gender'] ?? ''),
                    $citizen['birth_date'] ?? '',
                    $citizen['birth_place'] ?? '',
                    $citizen['age'] ?? '',
                    $citizen['address'] ?? '',
                    $citizen['rt'] ?? '',
                    $citizen['rw'] ?? '',
                    $citizen['province_id'] ?? '',
                    $citizen['district_id'] ?? '',
                    $citizen['sub_district_id'] ?? '',
                    $citizen['village_id'] ?? '',
                    $citizen['postal_code'] ?? '',
                    $this->formatCitizenStatus($citizen['citizen_status'] ?? ''),
                    $this->formatReligion($citizen['religion'] ?? ''),
                    $this->formatBloodType($citizen['blood_type'] ?? ''),
                    $this->formatFamilyStatus($citizen['family_status'] ?? ''),
                    $citizen['father'] ?? '',
                    $citizen['mother'] ?? '',
                    $citizen['nik_father'] ?? '',
                    $citizen['nik_mother'] ?? '',
                ];
            }

            $filename = 'data_kk_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new \App\Exports\CitizensExport($exportData), $filename);
        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            return redirect()->route('admin.desa.datakk.index')
                ->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    /**
     * Format gender value for export
     */
    private function formatGender($gender)
    {
        if ($gender == 1) return 'Laki-laki';
        if ($gender == 2) return 'Perempuan';
        return $gender;
    }

    /**
     * Format citizen status value for export
     */
    private function formatCitizenStatus($status)
    {
        if ($status == 1) return 'WNI';
        if ($status == 2) return 'WNA';
        return $status;
    }

    /**
     * Format religion value for export
     */
    private function formatReligion($religion)
    {
        $religions = [
            1 => 'Islam',
            2 => 'Kristen',
            3 => 'Katolik',
            4 => 'Hindu',
            5 => 'Buddha',
            6 => 'Konghucu',
            7 => 'Lainnya'
        ];
        return $religions[$religion] ?? $religion;
    }

    /**
     * Format blood type value for export
     */
    private function formatBloodType($bloodType)
    {
        $bloodTypes = [
            1 => 'A',
            2 => 'B',
            3 => 'AB',
            4 => 'O',
            5 => 'A+',
            6 => 'A-',
            7 => 'B+',
            8 => 'B-',
            9 => 'AB+',
            10 => 'AB-',
            11 => 'O+',
            12 => 'O-',
            13 => 'Tidak Tahu'
        ];
        return $bloodTypes[$bloodType] ?? $bloodType;
    }

    /**
     * Format family status value for export
     */
    private function formatFamilyStatus($status)
    {
        $statuses = [
            1 => 'ANAK',
            2 => 'KEPALA KELUARGA',
            3 => 'ISTRI',
            4 => 'ORANG TUA',
            5 => 'MERTUA',
            6 => 'CUCU',
            7 => 'FAMILI LAIN'
        ];
        return $statuses[$status] ?? $status;
    }

    public function editByKK($kk)
    {
        // Cari KK berdasarkan nomor KK dari API
        $citizenData = $this->citizenService->getCitizenByKK($kk);

        if (!$citizenData || !isset($citizenData['data'])) {
            return redirect()->route('admin.desa.datakk.index')
                ->with('error', 'Data KK tidak ditemukan');
        }

        $kkData = $citizenData['data'];

        // Get provinces data with caching
        $provinces = Cache::remember('provinces', 3600, function () {
            return $this->wilayahService->getProvinces();
        });

        // Get location data for pre-selecting dropdowns
        $districts = $this->wilayahService->getKabupaten($kkData['province_id'] ?? null);
        $subDistricts = $this->wilayahService->getKecamatan($kkData['district_id'] ?? null);
        $villages = $this->wilayahService->getDesa($kkData['sub_district_id'] ?? null);

        // Get jobs data
        $jobs = $this->jobService->getAllJobs();

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.datakk.update', compact(
                'kkData',
                'provinces',
                'districts',
                'subDistricts',
                'villages',
                'jobs'
            ));
        }

        return view('superadmin.datakk.update', compact(
            'kkData',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'jobs'
        ));
    }

    public function updateByKK(Request $request, $kk)
    {
        // Validasi input
        $validatedData = $request->validate([
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
            'religion' => 'required|integer|in:1,2,3,4,5,6,7',
            'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
            'marital_certificate' => 'required|in:1,2',
            'marital_certificate_no' => 'nullable|string',
            'marriage_date' => 'nullable|date',
            'divorce_certificate' => 'nullable|integer|in:1,2',
            'divorce_certificate_no' => 'nullable|string',
            'divorce_certificate_date' => 'nullable|date',
            'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
            'mental_disorders' => 'required|integer|in:1,2',
            'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
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
            'rf_id_tag' => 'nullable|string',
        ]);

        try {
            // Update data melalui API
            $response = Http::withHeaders([
                'X-API-Key' => config('services.kependudukan.key'),
            ])->put(config('services.kependudukan.url') . "/api/citizens/{$kk}", $validatedData);

            if ($response->successful()) {
                if (Auth::user()->role === 'admin desa') {
                    return redirect()->route('admin.desa.datakk.index')
                        ->with('success', 'Data KK berhasil diperbarui!');
                }
                return redirect()->route('superadmin.datakk.index')
                    ->with('success', 'Data KK berhasil diperbarui!');
            } else {
                throw new \Exception('Gagal memperbarui data: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error updating KK data: ' . $e->getMessage());

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.datakk.index')
                    ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
            return redirect()->route('superadmin.datakk.index')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}
