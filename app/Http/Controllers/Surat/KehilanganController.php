<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kehilangan;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class KehilanganController extends Controller
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
     * Display a listing of the loss reports
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Kehilangan::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('lost_items', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $kehilangans = $query->paginate(10);

        return view('superadmin.datamaster.surat.kehilangan.index', compact('kehilangans'));
    }

    /**
     * Show the form for creating a new loss report
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

        return view('superadmin.datamaster.surat.kehilangan.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created loss report in storage
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
            'job_type_id' => 'required|numeric',
            'religion' => 'required|numeric',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'letter_date' => 'required|date',
            'lost_items' => 'required|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string', // Changed from integer to string
            'signing' => 'nullable|string',
        ]);

        try {
            Kehilangan::create($request->all());
            return redirect()->route('superadmin.surat.kehilangan.index')
                ->with('success', 'Surat kehilangan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat kehilangan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified loss report
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kehilangan = Kehilangan::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.kehilangan.edit', compact(
            'kehilangan',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified loss report in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
            'lost_items' => 'required|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string', // Changed from integer to string
            'signing' => 'nullable|string',
        ]);

        try {
            $kehilangan = Kehilangan::findOrFail($id);
            $kehilangan->update($request->all());

            return redirect()->route('superadmin.surat.kehilangan.index')
                ->with('success', 'Surat kehilangan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat kehilangan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified loss report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kehilangan = Kehilangan::findOrFail($id);
            $kehilangan->delete();

            return redirect()->route('superadmin.surat.kehilangan.index')
                ->with('success', 'Surat kehilangan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat kehilangan: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for the specified loss report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $kehilangan = Kehilangan::findOrFail($id);

            // Get additional data needed for the PDF
            // Get job name from job service
            $jobName = '';
            if (!empty($kehilangan->job_type_id)) {
                $job = $this->jobService->getJobById($kehilangan->job_type_id);
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
            if (!empty($kehilangan->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $kehilangan->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($kehilangan->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $kehilangan->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $kehilangan->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($kehilangan->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $kehilangan->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $kehilangan->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($kehilangan->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $kehilangan->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $kehilangan->village_id) {
                            $villageName = $village['name'];
                            break;
                        }
                    }
                }
            }

            // Log location data to help with debugging
            \Log::info('Location data for kehilangan ID: ' . $id, [
                'province_id' => $kehilangan->province_id,
                'district_id' => $kehilangan->district_id,
                'subdistrict_id' => $kehilangan->subdistrict_id,
                'village_id' => $kehilangan->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName
            ]);

            // Convert gender numeric to text
            $gender = $kehilangan->gender == 1 ? 'Laki-Laki' : 'Perempuan';

            // Convert religion numeric to text
            $religions = [
                '1' => 'Islam',
                '2' => 'Kristen',
                '3' => 'Katholik',
                '4' => 'Hindu',
                '5' => 'Buddha',
                '6' => 'Kong Hu Cu',
                '7' => 'Lainnya'
            ];
            $religion = $religions[$kehilangan->religion] ?? '';

            // Convert citizenship numeric to text
            $citizenship = $kehilangan->citizen_status == 1 ? 'WNA' : 'WNI';

            // Format date
            $birthDate = \Carbon\Carbon::parse($kehilangan->birth_date)->format('d-m-Y');
            $letterDate = \Carbon\Carbon::parse($kehilangan->letter_date)->format('d-m-Y');

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($kehilangan->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($kehilangan->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            return view('superadmin.datamaster.surat.kehilangan.Kehilangan', [
                'kehilangan' => $kehilangan,
                'provinceName' => $provinceName,
                'districtName' => $districtName,
                'subdistrictName' => $subdistrictName,
                'villageName' => $villageName,
                'jobName' => $jobName,
                'gender' => $gender,
                'religion' => $religion,
                'citizenship' => $citizenship,
                'birthDate' => $birthDate,
                'letterDate' => $letterDate,
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
