<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BiodataController extends Controller
{
    protected $citizenService;
    protected $jobService;
    protected $wilayahService;

    public function __construct(
        CitizenService $citizenService,
        JobService $jobService,
        WilayahService $wilayahService
    ) {
        $this->citizenService = $citizenService;
        $this->jobService = $jobService;
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $citizens = $search
            ? $this->citizenService->searchCitizens($search)
            : $this->citizenService->getAllCitizens($page);

        return view('superadmin.biodata.index', compact('citizens', 'search'));
    }

    public function create()
    {
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        return view('superadmin.biodata.create', compact('provinces', 'jobs'));
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
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
                'mother' => 'required|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'required|string|max:255',
                'coordinate' => 'nullable|string|max:255',
            ]);

            // Batch process nullable fields
            $this->processNullableFields($validatedData);

            // Convert NIK and KK to integers
            $validatedData['nik'] = (int) $validatedData['nik'];
            $validatedData['kk'] = (int) $validatedData['kk'];
            $validatedData['religion'] = (int) $validatedData['religion'];

            $response = $this->citizenService->createCitizen($validatedData);

            if ($response['status'] === 'CREATED') {
                return redirect()
                    ->route('superadmin.biodata.index')
                    ->with('success', $response['message']);
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal menyimpan data');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Add this new helper method
    private function processNullableFields(&$data)
    {
        // Handle nullable integer fields
        $nullableIntegerFields = ['marital_status', 'marital_certificate', 'divorce_certificate', 'postal_code'];
        foreach ($nullableIntegerFields as $field) {
            $data[$field] = empty($data[$field]) ? 0 : (int) $data[$field];
        }

        // Handle nullable string fields
        $nullableStringFields = ['birth_certificate_no', 'marital_certificate_no', 'divorce_certificate_no',
                               'nik_mother', 'nik_father', 'coordinate'];
        foreach ($nullableStringFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : $data[$field];
        }

        // Handle nullable date fields
        $nullableDateFields = ['marriage_date', 'divorce_certificate_date'];
        foreach ($nullableDateFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : date('Y-m-d', strtotime($data[$field]));
        }

        // Format integer fields
        $integerFields = ['gender', 'age', 'province_id', 'district_id', 'sub_district_id',
                         'village_id', 'citizen_status', 'birth_certificate', 'blood_type',
                         'religion', 'family_status', 'mental_disorders', 'disabilities',
                         'education_status', 'job_type_id'];
        foreach ($integerFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }

        // Format date fields
        $dateFields = ['birth_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
    }

    public function edit($nik)
    {
        $citizen = $this->citizenService->getCitizenByNIK($nik);
        if (!$citizen || !isset($citizen['data'])) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Get location data for pre-selecting dropdowns
        $districts = $this->wilayahService->getKabupaten($citizen['data']['province_id']);
        $subDistricts = $this->wilayahService->getKecamatan($citizen['data']['district_id']);
        $villages = $this->wilayahService->getDesa($citizen['data']['sub_district_id']);

        return view('superadmin.biodata.update', compact(
            'citizen',
            'provinces',
            'jobs',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    public function update(Request $request, $nik)
    {
        try {
            $page = $request->input('current_page', 1);

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
                'disabilities' => 'required|integer|in:1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'required|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'required|string|max:255',
                'coordinate' => 'nullable|string|max:255',
            ]);

            // Process nullable fields
            $this->processNullableFields($validatedData);
            $nik = (int) $nik;

            // Convert KK to integer
            $validatedData['kk'] = (int) $validatedData['kk'];

            $response = $this->citizenService->updateCitizen($nik, $validatedData);

            if ($response['status'] === 'OK') {
                return redirect()
                    ->route('superadmin.biodata.index', ['page' => $page])
                    ->with('success', 'Biodata berhasil diperbarui!');
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal memperbarui data');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function destroy($id, Request $request)
    {
        $page = $request->query('page', 1);
    $response = $this->citizenService->deleteCitizen($id);

    if ($response['status'] === 'OK') {
        return redirect()
            ->route('superadmin.biodata.index', ['page' => $page])
            ->with('success', 'Biodata berhasil dihapus!');
    }

    return redirect()
        ->route('superadmin.biodata.index', ['page' => $page])
        ->with('error', 'Gagal menghapus biodata: ' . $response['message']);
    }

    private function getProvinces()
    {
        try {
            $baseUrl = config('services.kependudukan.url');
            $apiKey = config('services.kependudukan.key');

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->get("{$baseUrl}/api/provinces");

            if ($response->successful()) {
                $provinces = $response->json();
                // Transform the data to match the expected format
                return collect($provinces)->map(function ($province) {
                    return [
                        'id' => $province['code'], // Use code instead of id
                        'name' => $province['name']
                    ];
                })->all();
            }
            Log::error('Province API request failed: ' . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            return [];
        }
    }

    private function getJobs()
    {
        try {
            $response = Http::get('https://api.desaverse.id/jobs');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Jobs API request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching jobs: ' . $e->getMessage());
            return [];
        }
    }



    private function formatDates(array $data)
    {
        $dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];

        foreach ($dateFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }

        return $data;
    }

    // Add these new methods for location data
    /**
     * Get cities/districts for a province
     */
public function getCities($provinceCode)
{
    try {
        Log::info('BiodataController->getCities called with province code: ' . $provinceCode);

        // Use the WilayahService to get data from the external API
        $cities = $this->wilayahService->getKabupaten($provinceCode);

        // Return the data as JSON
        return response()->json($cities);
    } catch (\Exception $e) {
        Log::error('Error in getCities: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch cities: ' . $e->getMessage()], 500);
    }

}

/**
 * Get sub-districts for a city/district
 *
 * @param string $cityCode
 * @return \Illuminate\Http\JsonResponse
 */
public function getDistricts($cityCode)
{
    try {
        Log::info('BiodataController->getDistricts called with city code: ' . $cityCode);

        // Use the WilayahService to get data from the external API
        $districts = $this->wilayahService->getKecamatan($cityCode);

        // Return the data as JSON
        return response()->json($districts);
    } catch (\Exception $e) {
        Log::error('Error in getDistricts: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch districts: ' . $e->getMessage()], 500);
    }
}

/**
 * Get villages for a sub-district
 *
 * @param string $districtCode
 * @return \Illuminate\Http\JsonResponse
 */
public function getVillages($districtCode)
{
    try {
        Log::info('BiodataController->getVillages called with district code: ' . $districtCode);

        // Use the WilayahService to get data from the external API
        $villages = $this->wilayahService->getDesa($districtCode);

        // Return the data as JSON
        return response()->json($villages);
    } catch (\Exception $e) {
        Log::error('Error in getVillages: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch villages: ' . $e->getMessage()], 500);
    }
}
}
