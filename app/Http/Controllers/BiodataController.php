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
        Log::info('Incoming biodata request:', $request->all());
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
        'birth_certificate' => '|integer|in:1,2',
        'birth_certificate_no' => 'nullable|string',
        'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
        'religion' => 'required|integer|in:1,2,3,4,5,6,7',
        'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
        'marital_certificate_no' => 'nullable|string',
        'marriage_date' => 'nullable|date',
        'divorce_certificate' => 'nullable|integer|in:1,2',
        'divorce_certificate_no' => 'nullable|string',
        'divorce_certificate_date' => 'nullable|date',
        'family_status' => 'required|integer|in:1,2,3,4,5,6,7,8',
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
    Log::info('Validated data:', $validatedData);

    $response = $this->citizenService->createCitizen($validatedData);

    Log::info('API Response:', $response);

    if ($response['status'] === 'CREATED') {
        return redirect()
            ->route('superadmin.biodata.index')
            ->with('success', $response['message']);
    }

    Log::error('Failed to create biodata:', $response);
    return back()
        ->withInput()
        ->with('error', $response['message'] ?? 'Gagal menyimpan data');
        
} catch (\Exception $e) {
    Log::error('Exception in store method:', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
}

}

    public function edit($id)
    {
        $response = $this->citizenService->getCitizenByNIK($id);

        if ($response && $response['status'] === 'OK') {
            $biodata = (object) $response['data'];
            return view('superadmin.biodata.update', compact('biodata'));
        }

        return redirect()
            ->route('superadmin.biodata.index')
            ->with('error', 'Gagal mengambil data biodata: ' . ($response['message'] ?? 'Unknown error'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validateCitizenData($request, false);
        $response = $this->citizenService->updateCitizen($id, $validatedData);

        if ($response['status'] === 'OK') {
            return redirect()
                ->route('superadmin.biodata.index')
                ->with('success', 'Biodata berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui biodata: ' . $response['message']);
    }

    public function destroy($id)
    {
        $response = $this->citizenService->deleteCitizen($id);

        if ($response['status'] === 'OK') {
            return redirect()
                ->route('superadmin.biodata.index')
                ->with('success', 'Biodata berhasil dihapus!');
        }

        return back()->with('error', 'Gagal menghapus biodata: ' . $response['message']);
    }

    private function getProvinces()
    {
        try {
            $response = Http::get('https://api.desaverse.id/wilayah/provinsi');
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
    public function getCities($provinceCode)
    {
        $cities = $this->wilayahService->getKabupaten($provinceCode);
        return response()->json($cities);
    }

    public function getDistricts($cityCode)
    {
        $districts = $this->wilayahService->getKecamatan($cityCode);
        return response()->json($districts);
    }

    public function getVillages($districtCode)
    {
        $villages = $this->wilayahService->getDesa($districtCode);
        return response()->json($villages);
    }
}
