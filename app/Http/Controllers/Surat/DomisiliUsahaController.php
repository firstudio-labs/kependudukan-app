<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomisiliUsaha;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class DomisiliUsahaController extends Controller
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
     * Display a listing of the business domicile certificates.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DomisiliUsaha::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%")
                  ->orWhere('business_address', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $domisiliUsahaList = $query->paginate(10);

        return view('superadmin.datamaster.surat.domisili-usaha.index', compact('domisiliUsahaList'));
    }

    /**
     * Show the form for creating a new business domicile certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.domisili-usaha.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created business domicile certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Update validation to ensure data types match the table structure
        $request->validate([
            'nik' => 'required|string|max:16',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',  // Keep as is to accept numeric value (1 or 2)
            'job_type_id' => 'required',  // Keep as is to accept numeric value
            'religion' => 'required',  // Keep as is to accept numeric value
            'citizen_status' => 'required',  // Keep as is to accept numeric value
            'address' => 'required|string',
            'rt' => 'required|string',  // Use string for RT
            // Removed business_name validation
            'business_address' => 'required|string',
            'business_type' => 'required|string|max:255',
            'business_year' => 'required|string|max:4', // Add validation for business_year
            'letter_date' => 'required|date',
            'purpose' => 'required|string',  // Added purpose field
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',
        ]);

        try {
            // Create domisili usaha with proper data types, including business_year
            $data = [
                'nik' => $request->nik,
                'full_name' => $request->full_name,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'job_type_id' => $request->job_type_id,
                'religion' => $request->religion,
                'citizen_status' => $request->citizen_status,
                'address' => $request->address,
                'rt' => $request->rt,
                // Removed business_name from data array
                'business_address' => $request->business_address,
                'business_type' => $request->business_type,
                'business_year' => $request->business_year,
                'letter_date' => $request->letter_date,
                'purpose' => $request->purpose,
                'letter_number' => $request->letter_number,
                'signing' => $request->signing,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
            ];

            // Log what we're trying to save for debugging
            \Log::info('Attempting to save domisili usaha data', $data);

            DomisiliUsaha::create($data);

            return redirect()->route('superadmin.surat.domisili-usaha.index')
                ->with('success', 'Surat keterangan domisili usaha berhasil dibuat!');
        } catch (\Exception $e) {
            \Log::error('Failed to create domisili usaha: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat surat keterangan domisili usaha: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified business domicile certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $domisiliUsaha = DomisiliUsaha::findOrFail($id);

            // Get job name from job service
            if (!empty($domisiliUsaha->job_type_id)) {
                $job = $this->jobService->getJobById($domisiliUsaha->job_type_id);
                if ($job) {
                    $domisiliUsaha->job_name = $job['name'];
                }
            }

            // Get location names using wilayah service
            if (!empty($domisiliUsaha->province_id)) {
                $province = $this->wilayahService->getProvinceById($domisiliUsaha->province_id);
                if ($province) {
                    $domisiliUsaha->province_name = $province['name'];
                }
            }

            if (!empty($domisiliUsaha->district_id)) {
                $district = $this->wilayahService->getDistrictById($domisiliUsaha->district_id);
                if ($district) {
                    $domisiliUsaha->district_name = $district['name'];
                }
            }

            if (!empty($domisiliUsaha->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($domisiliUsaha->subdistrict_id);
                if ($subdistrict) {
                    $domisiliUsaha->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($domisiliUsaha->village_id)) {
                $village = $this->wilayahService->getVillageById($domisiliUsaha->village_id);
                if ($village) {
                    $domisiliUsaha->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'domisiliUsaha' => $domisiliUsaha
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified business domicile certificate.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $domisiliUsaha = DomisiliUsaha::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.domisili-usaha.edit', compact(
            'domisiliUsaha',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified business domicile certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update validation to ensure data types match the table structure
        $request->validate([
            'nik' => 'required|string|max:16',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',  // Keep as is to accept numeric value (1 or 2)
            'job_type_id' => 'required',  // Keep as is to accept numeric value
            'religion' => 'required',  // Keep as is to accept numeric value
            'citizen_status' => 'required',  // Keep as is to accept numeric value
            'address' => 'required|string',
            'rt' => 'required|string',  // Use string for RT
            // Removed business_name validation
            'business_address' => 'required|string',
            'business_type' => 'required|string|max:255',
            'business_year' => 'required|string|max:4', // Add validation for business_year
            'letter_date' => 'required|date',
            'purpose' => 'required|string',  // Added purpose field
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',
        ]);

        try {
            $domisiliUsaha = DomisiliUsaha::findOrFail($id);

            // Prepare data with proper types, including business_year
            $data = [
                'nik' => $request->nik,
                'full_name' => $request->full_name,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'job_type_id' => $request->job_type_id,
                'religion' => $request->religion,
                'citizen_status' => $request->citizen_status,
                'address' => $request->address,
                'rt' => $request->rt,
                // Removed business_name from data array
                'business_address' => $request->business_address,
                'business_type' => $request->business_type,
                'business_year' => $request->business_year,
                'letter_date' => $request->letter_date,
                'purpose' => $request->purpose,
                'letter_number' => $request->letter_number,
                'signing' => $request->signing,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
            ];

            $domisiliUsaha->update($data);

            return redirect()->route('superadmin.surat.domisili-usaha.index')
                ->with('success', 'Surat keterangan domisili usaha berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui surat keterangan domisili usaha: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified business domicile certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $domisiliUsaha = DomisiliUsaha::findOrFail($id);
            $domisiliUsaha->delete();

            return redirect()->route('superadmin.surat.domisili-usaha.index')
                ->with('success', 'Surat keterangan domisili usaha berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat keterangan domisili usaha: ' . $e->getMessage());
        }
    }

    /**
     * Generate a PDF file for the specified business domicile certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $domisiliUsaha = DomisiliUsaha::findOrFail($id);

            // Get job name from job service
            $jobName = '';
            if (!empty($domisiliUsaha->job_type_id)) {
                $job = $this->jobService->getJobById($domisiliUsaha->job_type_id);
                if ($job) {
                    $jobName = $job['name'];
                }
            }

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';
            $villageCode = null; // Initialize village code variable

            // Get province data
            if (!empty($domisiliUsaha->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $domisiliUsaha->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($domisiliUsaha->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $domisiliUsaha->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $domisiliUsaha->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($domisiliUsaha->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $domisiliUsaha->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $domisiliUsaha->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($domisiliUsaha->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $domisiliUsaha->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $domisiliUsaha->village_id) {
                            $villageName = $village['name'];
                            $villageCode = $village['code']; // Store the complete village code
                            break;
                        }
                    }
                }
            }

            // Log location data to help with debugging
            \Log::info('Location data for Domisili Usaha ID: ' . $id, [
                'province_id' => $domisiliUsaha->province_id,
                'district_id' => $domisiliUsaha->district_id,
                'subdistrict_id' => $domisiliUsaha->subdistrict_id,
                'village_id' => $domisiliUsaha->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'village_code' => $villageCode
            ]);

            // Format gender
            $gender = $domisiliUsaha->gender == 1 ? 'Laki-Laki' : 'Perempuan';

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
            $religion = $religions[$domisiliUsaha->religion] ?? '';

            // Format citizenship
            $citizenship = $domisiliUsaha->citizen_status == 1 ? 'WNA' : 'WNI';

            // Format date for display
            $birthDate = \Carbon\Carbon::parse($domisiliUsaha->birth_date)->format('d-m-Y');
            $letterDate = \Carbon\Carbon::parse($domisiliUsaha->letter_date)->format('d-m-Y');

            // Don't modify the RT value, leave it exactly as stored in the database
            // so if it's stored as '001', it will appear as '001' in the PDF

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($domisiliUsaha->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($domisiliUsaha->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get user image based on matching district_id
            $districtLogo = null;
            if (!empty($domisiliUsaha->district_id)) {
                $userWithLogo = \App\Models\User::where('districts_id', $domisiliUsaha->district_id)
                    ->whereNotNull('image')
                    ->first();

                if ($userWithLogo && $userWithLogo->image) {
                    $districtLogo = $userWithLogo->image;
                }
            }

            // Log the logo information for debugging
            \Log::info('District logo for DomisiliUsaha ID: ' . $id, [
                'district_id' => $domisiliUsaha->district_id,
                'logo_found' => !is_null($districtLogo),
                'logo_path' => $districtLogo
            ]);

            return view('superadmin.datamaster.surat.domisili-usaha.DomisiliUsaha', [
                'domisiliUsaha' => $domisiliUsaha,
                'job_name' => $jobName,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'villageCode' => $villageCode, // Add the village code
                'gender' => $gender,
                'religion' => $religion,
                'citizenship' => $citizenship,
                'formatted_birth_date' => $birthDate,
                'formatted_letter_date' => $letterDate,
                'signing_name' => $signing_name,
                'district_logo' => $districtLogo // Add this line
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
