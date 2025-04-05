<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domisili;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DomisiliController extends Controller
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
     * Display a listing of domicile certificates
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Domisili::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('domicile_address', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $domisiliList = $query->paginate(10);

        return view('superadmin.datamaster.surat.domisili.index', compact('domisiliList'));
    }

    /**
     * Show the form for creating a new domicile certificate
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

        return view('superadmin.datamaster.surat.domisili.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created domicile certificate in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Domisili store request', $request->all());

        // Manual validation to provide better error messages
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:16', // Changed to string to match existing implementation
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',  // Accept numeric value
            'job_type_id' => 'required',  // Accept numeric value
            'religion' => 'required',  // Accept numeric value
            'citizen_status' => 'required',  // Accept numeric value
            'address' => 'required|string',
            'rt' => 'required|string',  // Ensure RT is validated as string for longText field
            'letter_date' => 'required|date',
            'domicile_address' => 'required|string',
            'purpose' => 'required|string',
            'province_id' => 'required',  // Accept numeric value
            'district_id' => 'required',  // Accept numeric value
            'subdistrict_id' => 'required',  // Accept numeric value
            'village_id' => 'required',  // Accept numeric value
            'letter_number' => 'nullable|string',  // Changed to string to match schema
            'signing' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Formulir memiliki kesalahan. Silakan periksa kembali.');
        }

        try {
            DB::beginTransaction();

            // Prepare data without type casting to maintain original values
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
                'rt' => $request->rt, // Store RT as-is without conversion
                'letter_date' => $request->letter_date,
                'domicile_address' => $request->domicile_address,
                'purpose' => $request->purpose,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
                'letter_number' => $request->letter_number,
                'signing' => $request->signing,
            ];

            // Debug what's being saved
            Log::info('Saving domisili data', ['data' => $data]);

            $domisili = Domisili::create($data);

            DB::commit();

            Log::info('Domisili created successfully', ['id' => $domisili->id]);

            return redirect()->route('superadmin.surat.domisili.index')
                ->with('success', 'Surat keterangan domisili berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create domisili: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat surat keterangan domisili: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified domicile certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $domisili = Domisili::findOrFail($id);

            // Get job name from job service
            if (!empty($domisili->job_type_id)) {  // Changed from job_id to job_type_id
                $job = $this->jobService->getJobById($domisili->job_type_id);  // Changed from job_id to job_type_id
                if ($job) {
                    $domisili->job_name = $job['name'];
                }
            }

            // Get location names using wilayah service
            if (!empty($domisili->province_id)) {
                $province = $this->wilayahService->getProvinceById($domisili->province_id);
                if ($province) {
                    $domisili->province_name = $province['name'];
                }
            }

            if (!empty($domisili->district_id)) {
                $district = $this->wilayahService->getDistrictById($domisili->district_id);
                if ($district) {
                    $domisili->district_name = $district['name'];
                }
            }

            if (!empty($domisili->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($domisili->subdistrict_id);
                if ($subdistrict) {
                    $domisili->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($domisili->village_id)) {
                $village = $this->wilayahService->getVillageById($domisili->village_id);
                if ($village) {
                    $domisili->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'domisili' => $domisili
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified domicile certificate
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $domisili = Domisili::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.domisili.edit', compact(
            'domisili',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified domicile certificate in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request for debugging
        Log::info('Domisili update request', ['id' => $id, 'data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:16', // Changed to string to match existing implementation
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',  // Accept numeric value
            'job_type_id' => 'required',  // Accept numeric value
            'religion' => 'required',  // Accept numeric value
            'citizen_status' => 'required',  // Accept numeric value
            'address' => 'required|string',
            'rt' => 'required|string',  // Ensure RT is validated as string for longText field
            'letter_date' => 'required|date',
            'domicile_address' => 'required|string',
            'purpose' => 'required|string',
            'province_id' => 'required',  // Accept numeric value
            'district_id' => 'required',  // Accept numeric value
            'subdistrict_id' => 'required',  // Accept numeric value
            'village_id' => 'required',  // Accept numeric value
            'letter_number' => 'nullable|string',  // Changed to string to match schema
            'signing' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Update validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Formulir memiliki kesalahan. Silakan periksa kembali.');
        }

        try {
            DB::beginTransaction();

            $domisili = Domisili::findOrFail($id);

            // Prepare data without type casting to maintain original values
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
                'rt' => $request->rt, // Store RT as-is without conversion
                'letter_date' => $request->letter_date,
                'domicile_address' => $request->domicile_address,
                'purpose' => $request->purpose,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
                'letter_number' => $request->letter_number,
                'signing' => $request->signing,
            ];

            Log::info('Updating domisili data', ['id' => $id, 'data' => $data]);

            $domisili->update($data);

            DB::commit();

            Log::info('Domisili updated successfully', ['id' => $id]);

            return redirect()->route('superadmin.surat.domisili.index')
                ->with('success', 'Surat keterangan domisili berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update domisili: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui surat keterangan domisili: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified domicile certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $domisili = Domisili::findOrFail($id);
            $domisili->delete();

            return redirect()->route('superadmin.surat.domisili.index')
                ->with('success', 'Surat keterangan domisili berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat keterangan domisili: ' . $e->getMessage());
        }
    }

    /**
     * Generate a PDF file for the specified domicile certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $domisili = Domisili::findOrFail($id);

            // Get job name from job service
            $jobName = '';
            if (!empty($domisili->job_type_id)) {
                $job = $this->jobService->getJobById($domisili->job_type_id);
                if ($job) {
                    $jobName = $job['name'];
                }
            }

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data
            if (!empty($domisili->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $domisili->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($domisili->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $domisili->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $domisili->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($domisili->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $domisili->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $domisili->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($domisili->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $domisili->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $domisili->village_id) {
                            $villageName = $village['name'];
                            break;
                        }
                    }
                }
            }

            // Log location data to help with debugging
            \Log::info('Location data for Domisili ID: ' . $id, [
                'province_id' => $domisili->province_id,
                'district_id' => $domisili->district_id,
                'subdistrict_id' => $domisili->subdistrict_id,
                'village_id' => $domisili->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName
            ]);

            // Format gender
            $gender = $domisili->gender == 1 ? 'Laki-Laki' : 'Perempuan';

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
            $religion = $religions[$domisili->religion] ?? '';

            // Format citizenship
            $citizenship = $domisili->citizen_status == 1 ? 'WNA' : 'WNI';

            // Format date for display
            $birthDate = \Carbon\Carbon::parse($domisili->birth_date)->format('d-m-Y');
            $letterDate = \Carbon\Carbon::parse($domisili->letter_date)->format('d-m-Y');

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($domisili->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($domisili->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            return view('superadmin.datamaster.surat.domisili.Domisili', [
                'domisili' => $domisili,
                'job_name' => $jobName,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'gender' => $gender,
                'religion' => $religion,
                'citizenship' => $citizenship,
                'formatted_birth_date' => $birthDate,
                'formatted_letter_date' => $letterDate,
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
