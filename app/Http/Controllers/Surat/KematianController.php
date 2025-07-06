<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kematian;
use App\Models\Penandatangan;
use App\Models\User;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KematianController extends Controller
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
     * Display a listing of the death certificates
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Kematian::query();

        // Jika user adalah admin desa, filter berdasarkan village_id
        if (\Auth::user()->role === 'admin desa') {
            $villageId = \Auth::user()->villages_id;
            $query->where('village_id', $villageId);
        }

        // Add search functionality jika ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        $kematianList = $query->paginate(10);

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.kematian.index', compact('kematianList'));
        }

        return view('superadmin.datamaster.surat.kematian.index', compact('kematianList'));
    }

    /**
     * Show the form for creating a new death certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all(); // Fetch signers

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.kematian.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.kematian.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created death certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'nik' => 'required|numeric',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric',
            'job_type_id' => 'required|numeric',
            'religion' => 'required|numeric',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'info' => 'required|string|max:255',
            'rt' => 'nullable|string',
            'rt_letter_date' => 'nullable|date',
            'death_cause' => 'required|string|max:255',
            'death_place' => 'required|string|max:255',
            'reporter_name' => 'required|string|max:255',
            'reporter_relation' => 'required|string',
            'death_date' => 'required|date',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            Kematian::create($request->all());
            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.kematian.index')
                    ->with('success', 'Surat keterangan kematian berhasil dibuat!');
            }

            return redirect()->route('superadmin.surat.kematian.index')
                ->with('success', 'Surat keterangan kematian berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat keterangan kematian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified death certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $kematian = Kematian::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($kematian->province_id)) {
                $province = $this->wilayahService->getProvinceById($kematian->province_id);
                if ($province) {
                    $kematian->province_name = $province['name'];
                }
            }

            if (!empty($kematian->district_id)) {
                $district = $this->wilayahService->getDistrictById($kematian->district_id);
                if ($district) {
                    $kematian->district_name = $district['name'];
                }
            }

            if (!empty($kematian->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($kematian->subdistrict_id);
                if ($subdistrict) {
                    $kematian->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($kematian->village_id)) {
                $village = $this->wilayahService->getVillageById($kematian->village_id);
                if ($village) {
                    $kematian->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'kematian' => $kematian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified death certificate.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kematian = Kematian::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.kematian.edit', compact(
            'kematian',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.kematian.edit', compact(
            'kematian',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified death certificate in storage.
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
            'nik' => 'required|numeric',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|numeric',
            'job_type_id' => 'required|numeric',
            'religion' => 'required|numeric',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'info' => 'required|string|max:255',
            'rt' => 'nullable|string',
            'rt_letter_date' => 'nullable|date',
            'death_cause' => 'required|string|max:255',
            'death_place' => 'required|string|max:255',
            'reporter_name' => 'required|string|max:255',
            'reporter_relation' => 'required|string',
            'death_date' => 'required|date',
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            $kematian = Kematian::findOrFail($id);
            $data = $request->all();

            // Set is_accepted field if it's provided in the form
            $data['is_accepted'] = $request->has('is_accepted') ? 1 : 0;

            $kematian->update($data);

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.kematian.index')
                    ->with('success', 'Surat keterangan kematian berhasil diperbarui!');
            }

            return redirect()->route('superadmin.surat.kematian.index')
                ->with('success', 'Surat keterangan kematian berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat keterangan kematian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified death certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kematian = Kematian::findOrFail($id);
            $kematian->delete();

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.kematian.index')
                    ->with('success', 'Surat keterangan kematian berhasil dihapus!');
            }

            return redirect()->route('superadmin.surat.kematian.index')
                ->with('success', 'Surat keterangan kematian berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat keterangan kematian: ' . $e->getMessage());
        }
    }

    /**
     * Export death certificate to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($id)
    {
        try {
            $kematian = Kematian::findOrFail($id);

            // Get additional data needed for the PDF
            // Get job name from job service
            $jobName = '';
            if (!empty($kematian->job_type_id)) {
                $job = $this->jobService->getJobById($kematian->job_type_id);
                if ($job) {
                    $jobName = $job['name'];
                }
            }

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';
            $villageCode = '';

            // Get province data
            if (!empty($kematian->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $kematian->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($kematian->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $kematian->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $kematian->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($kematian->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $kematian->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $kematian->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($kematian->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $kematian->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $kematian->village_id) {
                            $villageName = $village['name'];
                            $villageCode = $village['code']; // Store the complete village code
                            break;
                        }
                    }
                }
            }

            // Log location data to help with debugging
            \Log::info('Location data for kematian ID: ' . $id, [
                'province_id' => $kematian->province_id,
                'district_id' => $kematian->district_id,
                'subdistrict_id' => $kematian->subdistrict_id,
                'village_id' => $kematian->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName
            ]);

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

            $religionName = $religionMap[$kematian->religion] ?? '';

            // Get gender name
            $genderMap = [
                '1' => 'Laki-laki',
                '2' => 'Perempuan'
            ];

            $genderName = $genderMap[$kematian->gender] ?? '';

            // Get citizen status name
            $citizenStatusMap = [
                '1' => 'WNA',
                '2' => 'WNI'
            ];

            $citizenStatusName = $citizenStatusMap[$kematian->citizen_status] ?? '';

            // Format date
            $birthDate = \Carbon\Carbon::parse($kematian->birth_date)->format('d F Y');
            $deathDate = \Carbon\Carbon::parse($kematian->death_date)->format('d F Y');
            $rtLetterDate = $kematian->rt_letter_date ? \Carbon\Carbon::parse($kematian->rt_letter_date)->format('d F Y') : null;

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($kematian->signing)) {
                $penandatangan = Penandatangan::find($kematian->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get user image based on matching district_id
            $districtLogo = null;
            if (!empty($kematian->district_id)) {
                $userWithLogo = User::where('districts_id', $kematian->district_id)
                    ->whereNotNull('image')
                    ->first();

                if ($userWithLogo && $userWithLogo->image) {
                    $districtLogo = $userWithLogo->image;
                }
            }

            // Log the logo information for debugging
            \Log::info('District logo for Kematian ID: ' . $id, [
                'district_id' => $kematian->district_id,
                'logo_found' => !is_null($districtLogo),
                'logo_path' => $districtLogo
            ]);

            if (Auth::user()->role === 'admin desa') {
                return view('admin.desa.surat.kematian.kematian', [
                    'kematian' => $kematian,
                    'provinceName' => $provinceName,
                    'districtName' => $districtName,
                    'subdistrictName' => $subdistrictName,
                    'villageName' => $villageName,
                    'villageCode' => $villageCode, // Add the village code
                    'jobName' => $jobName,
                    'religionName' => $religionName,
                    'genderName' => $genderName,
                    'citizenStatusName' => $citizenStatusName,
                    'birthDate' => $birthDate,
                    'deathDate' => $deathDate,
                    'rtLetterDate' => $rtLetterDate,
                    'signing_name' => $signing_name, // Pass the signing name to the view
                    'district_logo' => $districtLogo // Add this line
                ]);
            }

            // Return view directly instead of generating PDF
            return view('superadmin.datamaster.surat.kematian.Kematian', [
                'kematian' => $kematian,
                'provinceName' => $provinceName,
                'districtName' => $districtName,
                'subdistrictName' => $subdistrictName,
                'villageName' => $villageName,
                'villageCode' => $villageCode, // Add the village code
                'jobName' => $jobName,
                'religionName' => $religionName,
                'genderName' => $genderName,
                'citizenStatusName' => $citizenStatusName,
                'birthDate' => $birthDate,
                'deathDate' => $deathDate,
                'rtLetterDate' => $rtLetterDate,
                'signing_name' => $signing_name, // Pass the signing name to the view
                'district_logo' => $districtLogo // Add this line
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh surat keterangan kematian: ' . $e->getMessage());
        }
    }
}
