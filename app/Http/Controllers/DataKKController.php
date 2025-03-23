<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KK;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Cache;

class DataKKController extends Controller
{
    protected $citizenService;
    protected $wilayahService;

    public function __construct(CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
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

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datakk.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages'
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
                'dusun' => $citizen['dusun'] ?? '',
                'family_status' => $citizen['family_status'] ?? '',
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
}

