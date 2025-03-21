<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengantarKtp;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class PengantarKtpController extends Controller
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
     * Display a listing of the KTP application letters
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PengantarKtp::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_card_number', 'like', "%{$search}%")
                  ->orWhere('application_type', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $ktpList = $query->paginate(10);

        return view('superadmin.datamaster.surat.pengantar-ktp.index', compact('ktpList'));
    }

    /**
     * Show the form for creating a new KTP application letter.
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

        return view('superadmin.datamaster.surat.pengantar-ktp.create', compact(
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Store a newly created KTP application letter in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'province_id' => 'required|string',
            'district_id' => 'required|string',
            'subdistrict_id' => 'required|string',
            'village_id' => 'required|string',
            'letter_number' => 'nullable|integer',
            'application_type' => 'required|string|in:Baru,Perpanjang,Pergantian',
            'nik' => 'required|integer',
            'full_name' => 'required|string',
            'kk' => 'required|string', // Changed from family_card_number to kk
            'address' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'hamlet' => 'required|string',
            'village_name' => 'required|string',
            'subdistrict_name' => 'required|string',
            'signing' => 'nullable|string',
        ]);

        try {
            PengantarKtp::create($request->all());
            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified KTP application letter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);

            // Get location names using wilayah service
            if (!empty($ktp->province_id)) {
                $province = $this->wilayahService->getProvinceById($ktp->province_id);
                if ($province) {
                    $ktp->province_name = $province['name'];
                }
            }

            if (!empty($ktp->district_id)) {
                $district = $this->wilayahService->getDistrictById($ktp->district_id);
                if ($district) {
                    $ktp->district_name = $district['name'];
                }
            }

            if (!empty($ktp->subdistrict_id)) {
                $subdistrict = $this->wilayahService->getSubDistrictById($ktp->subdistrict_id);
                if ($subdistrict) {
                    $ktp->subdistrict_name_admin = $subdistrict['name'];
                }
            }

            if (!empty($ktp->village_id)) {
                $village = $this->wilayahService->getVillageById($ktp->village_id);
                if ($village) {
                    $ktp->village_name_admin = $village['name'];
                }
            }

            return response()->json([
                'success' => true,
                'ktp' => $ktp
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified KTP application letter.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ktp = PengantarKtp::findOrFail($id);

        // Get provinces data from service
        $provinces = $this->wilayahService->getProvinces();

        // Initialize arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // If we have province_id, try to get districts
        if (!empty($ktp->province_id)) {
            try {
                $provinceData = $this->wilayahService->getProvinceById($ktp->province_id);
                if ($provinceData && isset($provinceData['code'])) {
                    $districts = $this->wilayahService->getDistrictsByProvinceCode($provinceData['code']);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error("Error fetching districts: " . $e->getMessage());
            }
        }

        // If we have district_id, try to get subdistricts
        if (!empty($ktp->district_id)) {
            try {
                $districtData = $this->wilayahService->getDistrictById($ktp->district_id);
                if ($districtData && isset($districtData['code'])) {
                    $subDistricts = $this->wilayahService->getSubDistrictsByDistrictCode($districtData['code']);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error("Error fetching subdistricts: " . $e->getMessage());
            }
        }

        // If we have subdistrict_id, try to get villages
        if (!empty($ktp->subdistrict_id)) {
            try {
                $subDistrictData = $this->wilayahService->getSubDistrictById($ktp->subdistrict_id);
                if ($subDistrictData && isset($subDistrictData['code'])) {
                    $villages = $this->wilayahService->getVillagesBySubDistrictCode($subDistrictData['code']);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error("Error fetching villages: " . $e->getMessage());
            }
        }

        return view('superadmin.datamaster.surat.pengantar-ktp.edit', compact(
            'ktp',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    /**
     * Update the specified KTP application letter in storage.
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
            'application_type' => 'required|string|in:Baru,Perpanjang,Pergantian',
            'nik' => 'required|integer',
            'full_name' => 'required|string',
            'kk' => 'required|string', // Changed from family_card_number to kk
            'address' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'hamlet' => 'required|string',
            'village_name' => 'required|string',
            'subdistrict_name' => 'required|string',
            'signing' => 'nullable|string',
        ]);

        try {
            $ktp = PengantarKtp::findOrFail($id);
            $ktp->update($request->all());

            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified KTP application letter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);
            $ktp->delete();

            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat pengantar KTP: ' . $e->getMessage());
        }
    }
}
