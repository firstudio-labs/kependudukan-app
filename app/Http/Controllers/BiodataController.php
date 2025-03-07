<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
            $validatedData = $this->validateCitizenData($request);
            $validatedData = $this->formatDates($validatedData);

            Log::info('Validated data:', $validatedData);

            $response = $this->citizenService->createCitizen($validatedData);

            if (isset($response['status']) && $response['status'] === 'CREATED') {
                return redirect()
                    ->route('superadmin.biodata.index')
                    ->with('success', 'Data biodata berhasil ditambahkan');
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . ($response['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
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

    private function validateCitizenData(Request $request, $isCreate = true)
    {
        $rules = [
            'kk' => 'required|numeric|digits:16',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-Laki,Perempuan',
            'birth_date' => 'required|date',
            'age' => 'required|numeric',
            'birth_place' => 'required|string',
            'address' => 'nullable|string',
            'province_id' => 'nullable|numeric',
            'district_id' => 'nullable|numeric',
            'sub_district_id' => 'nullable|numeric',
            'village_id' => 'nullable|numeric',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'postal_code' => 'nullable|numeric',
            'citizen_status' => 'nullable|in:WNI,WNA',
            'birth_certificate' => 'nullable|in:Ada,Tidak Ada',
            'birth_certificate_no' => 'nullable|string',
            'blood_type' => 'nullable|in:A,B,AB,O,A+,A-,B+,B-,AB+,AB-,O+,O-,Tidak Tahu',
            'religion' => 'nullable|in:Islam,Kristen,Katholik,Hindu,Buddha,Kong Hu Cu,Lainya....',
            'marital_status' => 'nullable|in:Belum Kawin,Kawin Tercatat,Kawin Belum Tercatat,Cerai Hidup Tercatat,Cerai Hidup Belum Tercatat,Cerai Mati',
            'marital_certificate' => 'nullable|in:Ada,Tidak Ada',
            'marital_certificate_no' => 'nullable|string',
            'marriage_date' => 'nullable|date',
            'divorce_certificate' => 'nullable|in:Ada,Tidak Ada',
            'divorce_certificate_no' => 'nullable|string',
            'divorce_certificate_date' => 'nullable|date',
            'family_status' => 'required|in:KEPALA KELUARGA,ISTRI,ANAK,MERTUA,ORANG TUA,CUCU,FAMILI LAIN,LAINNYA',
            'mental_disorders' => 'nullable|in:Ada,Tidak Ada',
            'disabilities' => 'nullable|string',
            'education_status' => 'nullable|string',
            'job_type_id' => 'required|numeric',
            'nik_mother' => 'nullable|string|max:255',
            'mother' => 'nullable|string|max:255',
            'nik_father' => 'nullable|string|max:255',
            'father' => 'nullable|string|max:255',
            'coordinate' => 'nullable|string',
        ];

        if ($isCreate) {
            $rules['nik'] = 'required|numeric|digits:16';
        }

        return $request->validate($rules);
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
