<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelahiran;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class KelahiranController extends Controller
{
    protected $wilayahService;
    protected $citizenService;
    protected $jobService;

    /**
     * Create a new controller instance.
     *
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     * @param JobService $jobService
     */
    public function __construct(WilayahService $wilayahService, CitizenService $citizenService, JobService $jobService)
    {
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
        $this->jobService = $jobService;
    }

    /**
     * Display a listing of the birth certificates
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Kelahiran::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('child_name', 'like', "%{$search}%")
                  ->orWhere('child_birth_place', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $kelahiranList = $query->paginate(10);

        return view('superadmin.datamaster.surat.kelahiran.index', compact('kelahiranList'));
    }

    /**
     * Show the form for creating a new birth certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get regions data from service
        $provinces = $this->wilayahService->getProvinces();

        // Get job types
        $jobTypes = $this->jobService->getAllJobs();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.kelahiran.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'jobTypes'
        ));
    }

    /**
     * Store a newly created birth certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate after try-catch to see all errors
            $validated = $request->validate([
                'province_id' => 'required|numeric',
                'district_id' => 'required|numeric',
                'subdistrict_id' => 'required|numeric',
                'village_id' => 'required|numeric',
                'letter_number' => 'nullable|string',
                'father_nik' => 'nullable|numeric',
                'father_full_name' => 'nullable|string',
                'father_birth_place' => 'nullable|string',
                'father_birth_date' => 'nullable|date',
                'father_job' => 'nullable|numeric',
                'father_religion' => 'nullable|numeric',
                'father_address' => 'nullable|string',
                'mother_nik' => 'nullable|numeric',
                'mother_full_name' => 'nullable|string',
                'mother_birth_place' => 'nullable|string',
                'mother_birth_date' => 'nullable|date',
                'mother_job' => 'nullable|numeric',
                'mother_religion' => 'nullable|numeric',
                'mother_address' => 'nullable|string',
                'child_name' => 'required|string|max:255',
                'child_gender' => 'required|numeric',
                'child_birth_date' => 'required|date',
                'child_birth_place' => 'required|string|max:255',
                'child_religion' => 'required|numeric',
                'child_address' => 'required|string',
                'child_order' => 'required|integer',
                'signing' => 'nullable|string|max:255',
            ]);

            // Get all request data
            $data = $request->all();

            // Log all data for debugging
            Log::info('Attempting to create kelahiran with data:', $data);

            // Ensure child_gender is stored correctly
            $data['child_gender'] = $request->child_gender;

            // Create the kelahiran record
            $kelahiran = Kelahiran::create($data);

            // Log success
            Log::info('Successfully created kelahiran with ID: ' . $kelahiran->id);

            return redirect()->route('superadmin.surat.kelahiran.index')
                ->with('success', 'Surat keterangan kelahiran berhasil dibuat!');
        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Error creating birth certificate: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));

            // Return back with error message and input data
            return back()->withInput()->with('error', 'Gagal membuat surat keterangan kelahiran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified birth certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $kelahiran = Kelahiran::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($kelahiran->province_id)) {
                $province = $this->wilayahService->getProvinceById($kelahiran->province_id);
                if ($province) {
                    $kelahiran->province_name = $province['name'];
                }
            }

            if (!empty($kelahiran->district_id)) {
                $district = $this->wilayahService->getDistrictById($kelahiran->district_id);
                if ($district) {
                    $kelahiran->district_name = $district['name'];
                }
            }

            if (!empty($kelahiran->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($kelahiran->subdistrict_id);
                if ($subdistrict) {
                    $kelahiran->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($kelahiran->village_id)) {
                $village = $this->wilayahService->getVillageById($kelahiran->village_id);
                if ($village) {
                    $kelahiran->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'kelahiran' => $kelahiran
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified birth certificate.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kelahiran = Kelahiran::findOrFail($id);

        // Get provinces data from service
        $provinces = $this->wilayahService->getProvinces();

        // Get job types
        $jobTypes = $this->jobService->getAllJobs();

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.kelahiran.edit', compact(
            'kelahiran',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'jobTypes'
        ));
    }

    /**
     * Update the specified birth certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'father_nik' => 'nullable|numeric',
            'father_full_name' => 'nullable|string',
            'father_birth_place' => 'nullable|string',
            'father_birth_date' => 'nullable|date',
            'father_job' => 'nullable|numeric',
            'father_religion' => 'nullable|numeric',
            'father_address' => 'nullable|string',
            'mother_nik' => 'nullable|numeric',
            'mother_full_name' => 'nullable|string',
            'mother_birth_place' => 'nullable|string',
            'mother_birth_date' => 'nullable|date',
            'mother_job' => 'nullable|numeric',
            'mother_religion' => 'nullable|numeric',
            'mother_address' => 'nullable|string',
            'child_name' => 'required|string|max:255',
            'child_gender' => 'required|numeric',
            'child_birth_date' => 'required|date',
            'child_birth_place' => 'required|string|max:255',
            'child_religion' => 'required|numeric',
            'child_address' => 'required|string',
            'child_order' => 'required|integer',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            // Ensure child_gender is stored as numeric value
            $data = $request->all();

            // Make sure gender is stored as the numeric value
            $data['child_gender'] = $request->child_gender; // This should already be "1" or "2"

            $kelahiran = Kelahiran::findOrFail($id);
            $kelahiran->update($data);

            return redirect()->route('superadmin.surat.kelahiran.index')
                ->with('success', 'Surat keterangan kelahiran berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log the detailed error
            Log::error('Error updating birth certificate: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));

            return back()->with('error', 'Gagal memperbarui surat keterangan kelahiran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified birth certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kelahiran = Kelahiran::findOrFail($id);
            $kelahiran->delete();

            return redirect()->route('superadmin.surat.kelahiran.index')
                ->with('success', 'Surat keterangan kelahiran berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat keterangan kelahiran: ' . $e->getMessage());
        }
    }

    /**
     * Export the birth certificate as PDF.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($id)
    {
        try {
            $kelahiran = Kelahiran::findOrFail($id);

            // Get location names
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data - fetch all provinces and filter
            if (!empty($kelahiran->province_id)) {
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $kelahiran->province_id) {
                        $provinceName = $province['name'];
                        $provinceCode = $province['code'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($kelahiran->district_id) && !empty($provinceCode)) {
                $districts = $this->wilayahService->getKabupaten($provinceCode);
                foreach ($districts as $district) {
                    if ($district['id'] == $kelahiran->district_id) {
                        $districtName = $district['name'];
                        $districtCode = $district['code'];
                        break;
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($kelahiran->subdistrict_id) && !empty($districtCode)) {
                $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                foreach ($subdistricts as $subdistrict) {
                    if ($subdistrict['id'] == $kelahiran->subdistrict_id) {
                        $subdistrictName = $subdistrict['name'];
                        $subdistrictCode = $subdistrict['code'];
                        break;
                    }
                }
            }

            // Get village/desa data
            if (!empty($kelahiran->village_id) && !empty($subdistrictCode)) {
                $villages = $this->wilayahService->getDesa($subdistrictCode);
                foreach ($villages as $village) {
                    if ($village['id'] == $kelahiran->village_id) {
                        $villageName = $village['name'];
                        break;
                    }
                }
            }

            // Store location names in the kelahiran object
            $kelahiran->province_name = $provinceName;
            $kelahiran->district_name = $districtName;
            $kelahiran->subdistrict_name = $subdistrictName;
            $kelahiran->village_name = $villageName;

            // Log location data to help with debugging
            \Log::info('Location data for kelahiran ID: ' . $id, [
                'province_id' => $kelahiran->province_id,
                'district_id' => $kelahiran->district_id,
                'subdistrict_id' => $kelahiran->subdistrict_id,
                'village_id' => $kelahiran->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName
            ]);

            // Get job names
            if ($kelahiran->father_job) {
                $fatherJob = $this->jobService->getJobById($kelahiran->father_job);
                if ($fatherJob) {
                    $kelahiran->father_job_name = $fatherJob['name'];
                }
            }

            if ($kelahiran->mother_job) {
                $motherJob = $this->jobService->getJobById($kelahiran->mother_job);
                if ($motherJob) {
                    $kelahiran->mother_job_name = $motherJob['name'];
                }
            }

            // Convert religion codes to names
            $religions = [
                1 => 'Islam',
                2 => 'Kristen',
                3 => 'Katholik',
                4 => 'Hindu',
                5 => 'Buddha',
                6 => 'Kong Hu Cu',
                7 => 'Lainnya'
            ];

            $kelahiran->father_religion_name = isset($religions[$kelahiran->father_religion]) ? $religions[$kelahiran->father_religion] : '';
            $kelahiran->mother_religion_name = isset($religions[$kelahiran->mother_religion]) ? $religions[$kelahiran->mother_religion] : '';
            $kelahiran->child_religion_name = isset($religions[$kelahiran->child_religion]) ? $religions[$kelahiran->child_religion] : '';

            // Convert gender code to name
            $kelahiran->child_gender_name = $kelahiran->child_gender == 1 ? 'Laki-laki' : 'Perempuan';

            // Format dates for better display
            $kelahiran->formatted_father_birth_date = $kelahiran->father_birth_date ? date('d-m-Y', strtotime($kelahiran->father_birth_date)) : '';
            $kelahiran->formatted_mother_birth_date = $kelahiran->mother_birth_date ? date('d-m-Y', strtotime($kelahiran->mother_birth_date)) : '';
            $kelahiran->formatted_child_birth_date = $kelahiran->child_birth_date ? date('d-m-Y', strtotime($kelahiran->child_birth_date)) : '';

            // Current date for the letter - use Indonesian month names
            $bulanIndonesia = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            $tanggal = date('d');
            $bulan = $bulanIndonesia[(int)date('m')];
            $tahun = date('Y');
            $currentDate = "$tanggal $bulan $tahun";

            return view('superadmin.datamaster.surat.kelahiran.Kelahiran', compact('kelahiran', 'currentDate'));
        } catch (\Exception $e) {
            Log::error('Error generating birth certificate PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
