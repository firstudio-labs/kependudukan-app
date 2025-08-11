<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AhliWaris;
use App\Models\Penandatangan;
use App\Models\User;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\KepalaDesa;

class AhliWarisController extends Controller
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
     * Display a listing of the Heir certificates
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AhliWaris::query();

        // Jika user adalah admin desa, filter berdasarkan village_id
        if (\Auth::user()->role === 'admin desa') {
            $villageId = \Auth::user()->villages_id;
            $query->where('village_id', $villageId);
        }

        // Add search functionality jika ada
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('heir_name', 'like', "%{$search}%")
                  ->orWhere('deceased_name', 'like', "%{$search}%");
            });
        }

        $ahliWarisList = $query->paginate(10);

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.ahli-waris.index', compact('ahliWarisList'));
        }

        return view('superadmin.datamaster.surat.ahli-waris.index', compact('ahliWarisList'));
    }

    /**
     * Show the form for creating a new heir certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Fetch signers
        try {
            $signers = Penandatangan::all();
            Log::info('Signers fetched successfully', ['count' => $signers->count()]);
        } catch (\Exception $e) {
            Log::error('Error fetching signers', ['error' => $e->getMessage()]);
            $signers = collect(); // Empty collection as fallback
        }

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.ahli-waris.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.ahli-waris.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created heir certificate in storage.
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
            'nik' => 'required|array',
            'full_name' => 'required|array',
            'birth_place' => 'required|array',
            'birth_date' => 'required|array',
            'gender' => 'required|array',
            'religion' => 'required|array',
            'address' => 'required|array',
            'family_status' => 'required|array',
            'heir_name' => 'required|string|max:255',
            'deceased_name' => 'required|string|max:255',
            'death_place' => 'required|string|max:255',
            'death_date' => 'required|date',
            'death_certificate_number' => 'nullable|integer',
            'death_certificate_date' => 'nullable|date',
            'inheritance_letter_date' => 'nullable|date',
            'inheritance_type' => 'required|string|max:255',
            'signing' => 'nullable|string',
        ]);

        try {
            AhliWaris::create($request->all());
            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.ahli-waris.index')
                    ->with('success', 'Surat keterangan ahli waris berhasil dibuat!');
            }

            return redirect()->route('superadmin.surat.ahli-waris.index')
                ->with('success', 'Surat keterangan ahli waris berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat keterangan ahli waris: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified heir certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $ahliWaris = AhliWaris::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($ahliWaris->province_id)) {
                $province = $this->wilayahService->getProvinceById($ahliWaris->province_id);
                if ($province) {
                    $ahliWaris->province_name = $province['name'];
                }
            }

            if (!empty($ahliWaris->district_id)) {
                $district = $this->wilayahService->getDistrictById($ahliWaris->district_id);
                if ($district) {
                    $ahliWaris->district_name = $district['name'];
                }
            }

            if (!empty($ahliWaris->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($ahliWaris->subdistrict_id);
                if ($subdistrict) {
                    $ahliWaris->subdistrict_name = $subdistrict['name'];
                }
            }

            if (!empty($ahliWaris->village_id)) {
                $village = $this->wilayahService->getVillageById($ahliWaris->village_id);
                if ($village) {
                    $ahliWaris->village_name = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'ahliWaris' => $ahliWaris
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified heir certificate.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ahliWaris = AhliWaris::findOrFail($id);

        // Get jobs and provinces data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Fetch signers
        try {
            $signers = Penandatangan::all();
            Log::info('Signers fetched successfully for edit', ['count' => $signers->count()]);
        } catch (\Exception $e) {
            Log::error('Error fetching signers for edit', ['error' => $e->getMessage()]);
            $signers = collect(); // Empty collection as fallback
        }

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        if (Auth::user()->role === 'admin desa') {
            return view('admin.desa.surat.ahli-waris.edit', compact(
            'ahliWaris',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
            ));
        }

        return view('superadmin.datamaster.surat.ahli-waris.edit', compact(
            'ahliWaris',
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Update the specified heir certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'province_id' => 'required|string',
            'district_id' => 'required|string',
            'subdistrict_id' => 'required|string',
            'village_id' => 'required|string',
            'letter_number' => 'nullable|string',
            'nik' => 'required|array',
            'full_name' => 'required|array',
            'birth_place' => 'required|array',
            'birth_date' => 'required|array',
            'gender' => 'required|array',
            'religion' => 'required|array',
            'address' => 'required|array',
            'family_status' => 'required|array',
            'heir_name' => 'required|string|max:255',
            'deceased_name' => 'required|string|max:255',
            'death_place' => 'required|string|max:255',
            'death_date' => 'required|date',
            'death_certificate_number' => 'nullable|integer',
            'death_certificate_date' => 'nullable|date',
            'inheritance_letter_date' => 'nullable|date',
            'inheritance_type' => 'required|string|max:255',
            'signing' => 'nullable|string',
        ]);

        try {
            $ahliWaris = AhliWaris::findOrFail($id);
            $data = $request->all();

            // Set is_accepted field if it's provided in the form
            $data['is_accepted'] = $request->has('is_accepted') ? 1 : 0;

            $ahliWaris->update($data);

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.ahli-waris.index')
                    ->with('success', 'Surat keterangan ahli waris berhasil diperbarui!');
            }

            return redirect()->route('superadmin.surat.ahli-waris.index')
                ->with('success', 'Surat keterangan ahli waris berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat keterangan ahli waris: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified heir certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ahliWaris = AhliWaris::findOrFail($id);
            $ahliWaris->delete();

            if (Auth::user()->role === 'admin desa') {
                return redirect()->route('admin.desa.surat.ahli-waris.index')
                    ->with('success', 'Surat keterangan ahli waris berhasil dihapus!');
            }

            return redirect()->route('superadmin.surat.ahli-waris.index')
                ->with('success', 'Surat keterangan ahli waris berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat keterangan ahli waris: ' . $e->getMessage());
        }
    }

    /**
     * Generate a PDF file for the specified heir certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $ahliWaris = AhliWaris::findOrFail($id);

            // Ensure address is properly converted to string if it's an array
            $addressString = is_array($ahliWaris->address) ? implode(', ', $ahliWaris->address) : ($ahliWaris->address ?? '-');

            // Get heirs data - ensure it's normalized
            $heirs = [];
            if (!empty($ahliWaris->heirs) && is_string($ahliWaris->heirs)) {
                $heirs = json_decode($ahliWaris->heirs, true);
            } elseif (is_array($ahliWaris->heirs)) {
                $heirs = $ahliWaris->heirs;
            }

            // Normalize NIK if it's an array
            $nik = is_array($ahliWaris->nik) ? implode(', ', $ahliWaris->nik) : $ahliWaris->nik;

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';
            $villageCode = '';

            // Get province data
            if (!empty($ahliWaris->province_id)) {
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $ahliWaris->province_id) {
                        $provinceName = $province['name'];
                        $provinceCode = $province['code'];

                        // Now get district data using province code
                        if (!empty($ahliWaris->district_id) && !empty($provinceCode)) {
                            $districts = $this->wilayahService->getKabupaten($provinceCode);
                            foreach ($districts as $district) {
                                if ($district['id'] == $ahliWaris->district_id) {
                                    $districtName = $district['name'];
                                    $districtCode = $district['code'];

                                    // Now get subdistrict data using district code
                                    if (!empty($ahliWaris->subdistrict_id) && !empty($districtCode)) {
                                        $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                                        foreach ($subdistricts as $subdistrict) {
                                            if ($subdistrict['id'] == $ahliWaris->subdistrict_id) {
                                                $subdistrictName = $subdistrict['name'];
                                                $subdistrictCode = $subdistrict['code'];

                                                // Finally get village data using subdistrict code
                                                if (!empty($ahliWaris->village_id) && !empty($subdistrictCode)) {
                                                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                                                    foreach ($villages as $village) {
                                                        if ($village['id'] == $ahliWaris->village_id) {
                                                            $villageName = $village['name'];
                                                            $villageCode = $village['code'];
                                                            break;
                                                        }
                                                    }
                                                }
                                                break;
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // Format dates for human-readable display
            $birthDate = '';
            if (!empty($ahliWaris->birth_date) && !is_array($ahliWaris->birth_date)) {
                try {
                    $birthDate = \Carbon\Carbon::parse($ahliWaris->birth_date)->locale('id')->isoFormat('D MMMM Y');
                } catch (\Exception $e) {
                    $birthDate = $ahliWaris->birth_date;
                }
            }

            $deathDate = '';
            if (!empty($ahliWaris->death_date) && !is_array($ahliWaris->death_date)) {
                try {
                    $deathDate = \Carbon\Carbon::parse($ahliWaris->death_date)->locale('id')->isoFormat('D MMMM Y');
                } catch (\Exception $e) {
                    $deathDate = $ahliWaris->death_date;
                }
            }

            $letterDate = '';
            if (!empty($ahliWaris->letter_date) && !is_array($ahliWaris->letter_date)) {
                try {
                    $letterDate = \Carbon\Carbon::parse($ahliWaris->letter_date)->locale('id')->isoFormat('D MMMM Y');
                } catch (\Exception $e) {
                    $letterDate = $ahliWaris->letter_date;
                }
            }

            // Format gender
            $gender = '';
            if (isset($ahliWaris->gender)) {
                $gender = $ahliWaris->gender == 1 ? 'Laki-Laki' : 'Perempuan';
            }

            // Format religion - Fix for the illegal offset type error
            $religions = [
                1 => 'Islam',
                2 => 'Kristen',
                3 => 'Katholik',
                4 => 'Hindu',
                5 => 'Buddha',
                6 => 'Kong Hu Cu',
                7 => 'Lainnya'
            ];

            // Convert to string or integer before using as array key
            $religionKey = null;
            if (isset($ahliWaris->religion)) {
                // Try to convert to integer first
                $religionKey = is_numeric($ahliWaris->religion) ? (int)$ahliWaris->religion : null;

                // If it's a string that contains a number, convert it
                if (is_string($ahliWaris->religion) && ctype_digit($ahliWaris->religion)) {
                    $religionKey = (int)$ahliWaris->religion;
                }
            }

            // Now safely check if the key exists in the religions array
            $religion = ($religionKey !== null && isset($religions[$religionKey]))
                ? $religions[$religionKey]
                : ($ahliWaris->religion ?? '');

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($ahliWaris->signing)) {
                $penandatangan = Penandatangan::find($ahliWaris->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get user image based on matching district_id
            $districtLogo = null;
            if (!empty($ahliWaris->district_id)) {
                $userWithLogo = User::where('districts_id', $ahliWaris->district_id)
                    ->whereNotNull('image')
                    ->first();

                if ($userWithLogo && $userWithLogo->image) {
                    $districtLogo = $userWithLogo->image;
                }
            }

            // Log the logo information for debugging
            \Log::info('District logo for AhliWaris ID: ' . $id, [
                'district_id' => $ahliWaris->district_id,
                'logo_found' => !is_null($districtLogo),
                'logo_path' => $districtLogo
            ]);

            // Get kepala desa data based on matching village_id
            $kepalaDesaName = null;
            $kepalaDesaSignature = null;

            \Log::info('Debugging AhliWaris generatePDF - village_id: ' . ($ahliWaris->village_id ?? 'NULL'));

            if (!empty($ahliWaris->village_id)) {
                // Find admin desa user for this village
                $adminDesaUser = User::where('villages_id', $ahliWaris->village_id)
                    ->where('role', 'admin desa')
                    ->first();

                \Log::info('Admin desa user found: ' . ($adminDesaUser ? 'YES' : 'NO'), [
                    'village_id' => $ahliWaris->village_id,
                    'admin_user_id' => $adminDesaUser ? $adminDesaUser->id : null
                ]);

                if ($adminDesaUser) {
                    // Get kepala desa data from the kepala_desa table
                    $kepalaDesa = KepalaDesa::where('user_id', $adminDesaUser->id)->first();

                    \Log::info('Kepala desa record found: ' . ($kepalaDesa ? 'YES' : 'NO'), [
                        'admin_user_id' => $adminDesaUser->id,
                        'kepala_desa_id' => $kepalaDesa ? $kepalaDesa->id : null
                    ]);

                    if ($kepalaDesa) {
                        $kepalaDesaName = $kepalaDesa->nama;
                        $kepalaDesaSignature = $kepalaDesa->tanda_tangan;

                        \Log::info('Kepala desa data extracted', [
                            'nama' => $kepalaDesaName,
                            'tanda_tangan' => $kepalaDesaSignature
                        ]);
                    }
                }
            }

            // Log the kepala desa information for debugging
            \Log::info('Final kepala desa data for AhliWaris ID: ' . $id, [
                'village_id' => $ahliWaris->village_id,
                'kepala_desa_name_found' => !is_null($kepalaDesaName),
                'kepala_desa_name' => $kepalaDesaName,
                'signature_found' => !is_null($kepalaDesaSignature),
                'signature_path' => $kepalaDesaSignature
            ]);

            // Log all variables being sent to view
            $viewData = [
                'ahliWaris' => $ahliWaris,
                'heirs' => $heirs,
                'addressString' => $addressString,
                'normalized_nik' => $nik,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'provinceName' => $provinceName,
                'districtName' => $districtName,
                'subdistrictName' => $subdistrictName,
                'villageName' => $villageName,
                'villageCode' => $villageCode,
                'formatted_birth_date' => $birthDate,
                'formatted_death_date' => $deathDate,
                'formatted_letter_date' => $letterDate,
                'gender' => $gender,
                'religion' => $religion,
                'signing_name' => $signing_name,
                'district_logo' => $districtLogo,
                'kepala_desa_name' => $kepalaDesaName,
                'kepala_desa_signature' => $kepalaDesaSignature
            ];

            \Log::info('All view data being sent', $viewData);

            // Return view with properly processed data
            if (Auth::user()->role === 'admin desa') {
                return view('admin.desa.surat.ahli-waris.AhliWaris', $viewData);
            }

            return view('superadmin.datamaster.surat.ahli-waris.AhliWaris', $viewData);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to safely decode JSON data
     *
     * @param mixed $value
     * @return array
     */
    private function safeJsonDecode($value)
    {
        if (is_array($value)) {
            return $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : [$value];
        } else {
            return [];
        }
    }
}
