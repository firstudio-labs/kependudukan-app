<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AhliWaris;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class AhliWarisController extends Controller
{
    protected $wilayahService;
    protected $citizenService;

    /**
     * Create a new controller instance.
     *
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     */
    public function __construct(WilayahService $wilayahService, CitizenService $citizenService)
    {
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

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('heir_name', 'like', "%{$search}%")
                  ->orWhere('deceased_name', 'like', "%{$search}%")
                  ->orWhere('death_place', 'like', "%{$search}%")
                  ->orWhere('inheritance_type', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%");
            });
        }

        $ahliWarisList = $query->paginate(10);

        return view('superadmin.datamaster.surat.ahli-waris.index', compact('ahliWarisList'));
    }

    /**
     * Show the form for creating a new heir certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get regions data from service
        $provinces = $this->wilayahService->getProvinces();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.ahli-waris.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages'
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
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            AhliWaris::create($request->all());
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

        // Get provinces data from service
        $provinces = $this->wilayahService->getProvinces();

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // If we have province_id, try to get districts


        return view('superadmin.datamaster.surat.ahli-waris.edit', compact(
            'ahliWaris',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
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
            'letter_number' => 'nullable|integer',
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
            'signing' => 'nullable|string|max:255',
        ]);

        try {
            $ahliWaris = AhliWaris::findOrFail($id);
            $ahliWaris->update($request->all());

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

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data
            if (!empty($ahliWaris->province_id)) {
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $ahliWaris->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($ahliWaris->district_id) && !empty($provinceName)) {
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $ahliWaris->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $ahliWaris->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($ahliWaris->subdistrict_id) && !empty($districtName)) {
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $ahliWaris->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $ahliWaris->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($ahliWaris->village_id) && !empty($subdistrictName)) {
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $ahliWaris->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $ahliWaris->village_id) {
                            $villageName = $village['name'];
                            break;
                        }
                    }
                }
            }

            // Format dates for display
            $deathDate = !empty($ahliWaris->death_date) ? \Carbon\Carbon::parse($ahliWaris->death_date)->format('d-m-Y') : '';
            $deathCertDate = !empty($ahliWaris->death_certificate_date) ? \Carbon\Carbon::parse($ahliWaris->death_certificate_date)->format('d-m-Y') : '';
            $inheritanceLetterDate = !empty($ahliWaris->inheritance_letter_date) ? \Carbon\Carbon::parse($ahliWaris->inheritance_letter_date)->format('d-m-Y') : date('d-m-Y');

            // Process array data from JSON if stored as strings
            $niks = $this->safeJsonDecode($ahliWaris->nik);
            $fullNames = $this->safeJsonDecode($ahliWaris->full_name);
            $birthPlaces = $this->safeJsonDecode($ahliWaris->birth_place);
            $birthDates = $this->safeJsonDecode($ahliWaris->birth_date);
            $genders = $this->safeJsonDecode($ahliWaris->gender);
            $religions = $this->safeJsonDecode($ahliWaris->religion);
            $addresses = $this->safeJsonDecode($ahliWaris->address);
            $familyStatuses = $this->safeJsonDecode($ahliWaris->family_status);

            // Format the heirs data for the template
            $heirs = [];
            for ($i = 0; $i < count($niks); $i++) {
                // Format dates and convert family status and gender to text
                $formattedBirthDate = isset($birthDates[$i]) ? \Carbon\Carbon::parse($birthDates[$i])->format('d-m-Y') : '';

                // Map gender codes to text
                $genderText = '';
                if (isset($genders[$i])) {
                    $genderText = $genders[$i] == 1 ? 'Laki-Laki' : 'Perempuan';
                }

                // Map religion codes to text
                $religionText = '';
                if (isset($religions[$i])) {
                    $religionMap = [
                        1 => 'Islam',
                        2 => 'Kristen',
                        3 => 'Katholik',
                        4 => 'Hindu',
                        5 => 'Buddha',
                        6 => 'Kong Hu Cu',
                        7 => 'Lainnya'
                    ];
                    $religionText = $religionMap[$religions[$i]] ?? '';
                }

                // Map family status codes to text
                $familyStatusText = '';
                if (isset($familyStatuses[$i])) {
                    $statusMap = [
                        1 => 'ANAK',
                        2 => 'KEPALA KELUARGA',
                        3 => 'ISTRI',
                        4 => 'ORANG TUA',
                        5 => 'MERTUA',
                        6 => 'CUCU',
                        7 => 'FAMILI LAIN'
                    ];
                    $familyStatusText = $statusMap[$familyStatuses[$i]] ?? '';
                }

                $heirs[] = [
                    'nik' => $niks[$i] ?? '',
                    'full_name' => $fullNames[$i] ?? '',
                    'birth_place' => $birthPlaces[$i] ?? '',
                    'birth_date' => $formattedBirthDate,
                    'gender' => $genderText,
                    'religion' => $religionText,
                    'address' => $addresses[$i] ?? '',
                    'family_status' => $familyStatusText
                ];
            }

            return view('superadmin.datamaster.surat.ahli-waris.AhliWaris', [
                'ahliWaris' => $ahliWaris,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'subdistrict_name' => $subdistrictName,
                'village_name' => $villageName,
                'heirs' => $heirs,
                'formatted_death_date' => $deathDate,
                'formatted_death_certificate_date' => $deathCertDate,
                'formatted_inheritance_letter_date' => $inheritanceLetterDate
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF for AhliWaris: ' . $e->getMessage(), [
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
