<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SKCK;
use App\Models\Penandatangan;
use App\Models\User;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SKCKController extends Controller
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
     * Display a listing of the SKCK
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = SKCK::query();

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

        $skckList = $query->paginate(10);

        if (\Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.skck.index', compact('skckList'));
        }

        return view('superadmin.datamaster.surat.skck.index', compact('skckList'));
    }

    /**
     * Show the form for creating a new SKCK
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
            $provinces = $this->wilayahService->getProvinces();
            $jobs = $this->jobService->getAllJobs();
            $signers = Penandatangan::all();
            $districts = [];
            $subDistricts = [];
            $villages = [];
            $villageId = Auth::user()->village_id; // Add village ID

            return view('admin.desa.surat.skck.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers',
            'villageId' // Pass village ID to the view
            ));
        }

        return view('superadmin.datamaster.surat.skck.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created SKCK in storage
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
            'purpose' => 'required|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            SKCK::create($request->all());
            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.skck.index')
                    ->with('success', 'Surat SKCK berhasil dibuat!');
            }

            return redirect()->route('superadmin.surat.skck.index')
                ->with('success', 'Surat SKCK berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat SKCK: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified SKCK.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $skck = SKCK::findOrFail($id);

            // Get job name from job service
            if (!empty($skck->job_type_id)) {
                $job = $this->jobService->getJobById($skck->job_type_id);
                if ($job) {
                    $skck->job_name = $job['name'];
                }
            }

            // Get location names using wilayah service
            if (!empty($skck->province_id)) {
                $province = $this->wilayahService->getProvinceById($skck->province_id);
                if ($province) {
                    $skck->province_name = $province['name'];
                }
            }

            if (!empty($skck->district_id)) {
                $district = $this->wilayahService->getDistrictById($skck->district_id);
                if ($district) {
                    $skck->district_name = $district['name'];
                }
            }

            if (!empty($skck->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($skck->subdistrict_id);
                if ($subdistrict) {
                    $skck->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($skck->village_id)) {
                $village = $this->wilayahService->getVillageById($skck->village_id);
                if ($village) {
                    $skck->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'skck' => $skck
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified SKCK
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $skck = SKCK::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = Penandatangan::all(); // Fetch signers

        // Initialize arrays for district, sub-district, and village data
        // These will be populated via AJAX in the view (same as AdministrasiController)
        $districts = [];
        $subDistricts = [];
        $villages = [];

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.skck.edit', compact(
            'skck',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.skck.edit', compact(
            'skck',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified SKCK in storage
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
            'purpose' => 'required|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            $skck = SKCK::findOrFail($id);
            $data = $request->all();

            // Set is_accepted field if it's provided in the form
            $data['is_accepted'] = $request->has('is_accepted') ? 1 : 0;

            $skck->update($data);

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.skck.index')
                    ->with('success', 'Surat SKCK berhasil diperbarui!');
            }

            return redirect()->route('superadmin.surat.skck.index')
                ->with('success', 'Surat SKCK berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat SKCK: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified SKCK from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $skck = SKCK::findOrFail($id);
            $skck->delete();

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.skck.index')
                    ->with('success', 'Surat SKCK berhasil dihapus!');
            }

            return redirect()->route('superadmin.surat.skck.index')
                ->with('success', 'Surat SKCK berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat SKCK: ' . $e->getMessage());
        }
    }

    /**
     * Generate a PDF file for the specified SKCK.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $skck = SKCK::findOrFail($id);

            // Get job name from job service
            $jobName = '';
            if (!empty($skck->job_type_id)) {
                $job = $this->jobService->getJobById($skck->job_type_id);
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
            if (!empty($skck->province_id)) {
                // Since the service doesn't have getProvinceById, we'll get all provinces and filter
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $skck->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($skck->district_id) && !empty($provinceName)) {
                // First try with province code if available
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $skck->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $skck->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($skck->subdistrict_id) && !empty($districtName)) {
                // Get district code first
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $skck->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $skck->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($skck->village_id) && !empty($subdistrictName)) {
                // Get subdistrict code first
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $skck->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $skck->village_id) {
                            $villageName = $village['name'];
                            $villageCode = $village['code']; // Store the complete village code
                            break;
                        }
                    }
                }
            }

            // Log location data to help with debugging
            \Log::info('Location data for SKCK ID: ' . $id, [
                'province_id' => $skck->province_id,
                'district_id' => $skck->district_id,
                'subdistrict_id' => $skck->subdistrict_id,
                'village_id' => $skck->village_id,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName
            ]);

            // Format gender
            $gender = $skck->gender == 1 ? 'Laki-Laki' : 'Perempuan';

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
            $religion = $religions[$skck->religion] ?? '';

            // Format citizenship
            $citizenship = $skck->citizen_status == 1 ? 'WNA' : 'WNI';

            // Format date for display
            $birthDate = \Carbon\Carbon::parse($skck->birth_date)->format('d-m-Y');
            $letterDate = \Carbon\Carbon::parse($skck->letter_date)->format('d-m-Y');

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($skck->signing)) {
                // Cari berdasarkan keterangan atau judul, bukan ID
                $penandatangan = Penandatangan::where('keterangan', $skck->signing)
                    ->orWhere('judul', $skck->signing)
                    ->first();

                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                } else {
                    // Fallback: gunakan langsung nilai dari field signing
                    $signing_name = $skck->signing;
                }
            }

            // Debug: Log signing information
            \Log::info('Signing data for SKCK ID: ' . $id, [
                'skck_signing' => $skck->signing,
                'signing_name_found' => !is_null($signing_name),
                'signing_name' => $signing_name,
                'penandatangan_search_result' => $skck->signing ? Penandatangan::where('keterangan', $skck->signing)->orWhere('judul', $skck->signing)->first() : null
            ]);

            // Get user image based on matching district_id
            $district_logo = null;
            if (!empty($skck->district_id)) {
                $userWithLogo = User::where('districts_id', $skck->district_id)
                    ->whereNotNull('image')
                    ->first();

                if ($userWithLogo && $userWithLogo->image) {
                    $district_logo = $userWithLogo->image;
                }
            }

            // Get kepala desa data based on matching village_id
            $kepala_desa_name = null;
            $kepala_desa_signature = null;
            if (!empty($skck->village_id)) {
                // Find admin desa user for this village
                $adminDesaUser = User::where('villages_id', $skck->village_id)
                    ->where('role', 'admin desa')
                    ->first();

                if ($adminDesaUser) {
                    // Get kepala desa data from the kepala_desa table
                    $kepalaDesa = \App\Models\KepalaDesa::where('user_id', $adminDesaUser->id)->first();

                    if ($kepalaDesa) {
                        $kepala_desa_name = $kepalaDesa->nama;
                        $kepala_desa_signature = $kepalaDesa->tanda_tangan;
                    }
                }
            }

            // Log the kepala desa information for debugging
            \Log::info('Kepala desa data for SKCK ID: ' . $id, [
                'village_id' => $skck->village_id,
                'kepala_desa_name_found' => !is_null($kepala_desa_name),
                'kepala_desa_name' => $kepala_desa_name,
                'signature_found' => !is_null($kepala_desa_signature),
                'signature_path' => $kepala_desa_signature
            ]);

            // Log the logo information for debugging
            \Log::info('District logo for SKCK ID: ' . $id, [
                'district_id' => $skck->district_id,
                'logo_found' => !is_null($district_logo),
                'logo_path' => $district_logo
            ]);

            if (Auth::user()->role === 'admin desa') {
                return view('admin.desa.surat.skck.SKCK', [
                    'skck' => $skck,
                    'job_name' => $jobName,
                    'province_name' => $provinceName,
                    'district_name' => $districtName,
                    'subdistrict_name' => $subdistrictName,
                    'village_name' => $villageName,
                    'villageCode' => $villageCode,
                    'gender' => $gender,
                    'religion' => $religion,
                    'citizenship' => $citizenship,
                    'formatted_birth_date' => $birthDate,
                    'formatted_letter_date' => $letterDate,
                    'signing_name' => $signing_name,
                    'district_logo' => $district_logo,
                    'kepala_desa_name' => $kepala_desa_name,
                    'kepala_desa_signature' => $kepala_desa_signature
                ]);
            }

            return view('superadmin.datamaster.surat.skck.SKCK', [
                'skck' => $skck,
                'job_name' => $jobName,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'villageCode' => $villageCode,
                'gender' => $gender,
                'religion' => $religion,
                'citizenship' => $citizenship,
                'formatted_birth_date' => $birthDate,
                'formatted_letter_date' => $letterDate,
                'signing_name' => $signing_name,
                'district_logo' => $district_logo,
                'kepala_desa_name' => $kepala_desa_name,
                'kepala_desa_signature' => $kepala_desa_signature
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
