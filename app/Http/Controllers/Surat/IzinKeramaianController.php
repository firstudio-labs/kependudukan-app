<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IzinKeramaian;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use App\Services\JobService;


class IzinKeramaianController extends Controller
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
     * Display a listing of the entertainment permits
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = IzinKeramaian::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('place', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhere('entertainment', 'like', "%{$search}%");
            });
        }

        $keramaianList = $query->paginate(10);

        return view('superadmin.datamaster.surat.keramaian.index', compact('keramaianList'));
    }

    /**
     * Show the form for creating a new entertainment permit.
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

        return view('superadmin.datamaster.surat.keramaian.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created entertainment permit in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'province_id' => 'required|numeric', // Changed from string to numeric
            'district_id' => 'required|numeric', // Changed from string to numeric
            'subdistrict_id' => 'required|numeric', // Changed from string to numeric
            'village_id' => 'required|numeric', // Changed from string to numeric
            'letter_number' => 'nullable|string', // Changed from integer to string
            'nik' => 'required|numeric', // Changed from string to numeric
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric', // Changed from string to numeric
            'job_type_id' => 'required|numeric', // Changed from string to numeric
            'religion' => 'required|numeric', // Changed from string to numeric
            'citizen_status' => 'required|numeric', // Changed from string to numeric
            'address' => 'required|string',
            'day' => 'required|string|max:20',
            'time' => 'required',
            'event_date' => 'required|date',
            'place' => 'required|string|max:255',
            'entertainment' => 'required|string|max:255',
            'event' => 'required|string|max:255',
            'invitation' => 'required|string|max:255',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            IzinKeramaian::create($request->all());
            return redirect()->route('superadmin.surat.keramaian.index')
                ->with('success', 'Surat izin keramaian berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat izin keramaian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified entertainment permit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $keramaian = IzinKeramaian::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($keramaian->province_id)) {
                $province = $this->wilayahService->getProvinceById($keramaian->province_id);
                if ($province) {
                    $keramaian->province_name = $province['name'];
                }
            }

            if (!empty($keramaian->district_id)) {
                $district = $this->wilayahService->getDistrictById($keramaian->district_id);
                if ($district) {
                    $keramaian->district_name = $district['name'];
                }
            }

            if (!empty($keramaian->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($keramaian->subdistrict_id);
                if ($subdistrict) {
                    $keramaian->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($keramaian->village_id)) {
                $village = $this->wilayahService->getVillageById($keramaian->village_id);
                if ($village) {
                    $keramaian->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'keramaian' => $keramaian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified entertainment permit.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $keramaian = IzinKeramaian::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.keramaian.edit', compact(
            'keramaian',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified entertainment permit in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'province_id' => 'required|numeric', // Changed from string to numeric
            'district_id' => 'required|numeric', // Changed from string to numeric
            'subdistrict_id' => 'required|numeric', // Changed from string to numeric
            'village_id' => 'required|numeric', // Changed from string to numeric
            'letter_number' => 'nullable|string', // Changed from integer to string
            'nik' => 'required|numeric', // Changed from string to numeric
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric', // Changed from string to numeric
            'job_type_id' => 'required|numeric', // Changed from string to numeric
            'religion' => 'required|numeric', // Changed from string to numeric
            'citizen_status' => 'required|numeric', // Changed from string to numeric
            'address' => 'required|string',
            'day' => 'required|string|max:20',
            'time' => 'required',
            'event_date' => 'required|date',
            'place' => 'required|string|max:255',
            'entertainment' => 'required|string|max:255',
            'event' => 'required|string|max:255',
            'invitation' => 'required|string|max:255',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            $keramaian = IzinKeramaian::findOrFail($id);
            $keramaian->update($request->all());

            return redirect()->route('superadmin.surat.keramaian.index')
                ->with('success', 'Surat izin keramaian berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat izin keramaian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified entertainment permit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $keramaian = IzinKeramaian::findOrFail($id);
            $keramaian->delete();

            return redirect()->route('superadmin.surat.keramaian.index')
                ->with('success', 'Surat izin keramaian berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat izin keramaian: ' . $e->getMessage());
        }
    }

    /**
     * Export entertainment permit to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($id)
    {
        try {
            $keramaian = IzinKeramaian::findOrFail($id);

            // Get additional data needed for the PDF
            // Get job name from job service
            $jobName = '';
            if (!empty($keramaian->job_type_id)) {
                $job = $this->jobService->getJobById($keramaian->job_type_id);
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
            if (!empty($keramaian->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $keramaian->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($keramaian->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $keramaian->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $keramaian->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($keramaian->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $keramaian->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $keramaian->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($keramaian->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $keramaian->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $keramaian->village_id) {
                            $villageName = $village['name'];
                            break;
                        }
                    }
                }
            }

            // Get religion name
            $religionMap = [
                '1' => 'Islam',
                '2' => 'Kristen',
                '3' => 'Katholik',
                '4' => 'Hindu',
                '5' => 'Buddha',
                '6' => 'Kong Hu Cu',
                '7' => 'Lainnya'
            ];
            $religionName = $religionMap[$keramaian->religion] ?? '';

            // Get gender name
            $genderMap = [
                '1' => 'Laki-laki',
                '2' => 'Perempuan'
            ];
            $genderName = $genderMap[$keramaian->gender] ?? '';

            // Get citizen status name
            $citizenStatusMap = [
                '1' => 'WNA',
                '2' => 'WNI'
            ];
            $citizenStatusName = $citizenStatusMap[$keramaian->citizen_status] ?? '';

            // Format dates
            $birthDate = \Carbon\Carbon::parse($keramaian->birth_date)->locale('id')->isoFormat('D MMMM Y');
            $eventDate = \Carbon\Carbon::parse($keramaian->event_date)->locale('id')->isoFormat('D MMMM Y');

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($keramaian->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($keramaian->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Return view directly instead of generating PDF
            return view('superadmin.datamaster.surat.keramaian.IjinKeramaian', compact(
                'keramaian',
                'provinceName',
                'districtName',
                'subdistrictName',
                'villageName',
                'jobName',
                'religionName',
                'genderName',
                'citizenStatusName',
                'birthDate',
                'eventDate',
                'signing_name'
            ));

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh surat izin keramaian: ' . $e->getMessage());
        }
    }
}
