<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\AhliWaris;
use App\Models\Penandatangan;
use Illuminate\Support\Facades\Session;
use App\Models\Pelayanan;
use Illuminate\Support\Facades\Log;

class AhliWarisSuratController extends Controller
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
     * Display the ahli waris form for guest users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get province and job data
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Get signers
        try {
            $signers = Penandatangan::all();
        } catch (\Exception $e) {
            Log::error('Error fetching signers in guest controller', ['error' => $e->getMessage()]);
            $signers = collect(); // Empty collection as fallback
        }

        // Initialize empty arrays for location data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        // Get queue number from session if it exists
        $queueNumber = session('no_antrian');
        $villageName = session('village_name');

        // If no queue number in session, get the latest one from the Pelayanan table
        if (!$queueNumber) {
            // Get the latest queue number from today's records
            $today = now()->format('Y-m-d');
            $latestPelayanan = Pelayanan::whereDate('created_at', $today)
                ->whereNotNull('no_antrian')
                ->orderBy('no_antrian', 'desc')
                ->first();

            if ($latestPelayanan) {
                // Use the formatted queue number accessor to get number with leading zeros
                $queueNumber = $latestPelayanan->formatted_queue_number;

                // Try to get village name if we have the village_id
                if ($latestPelayanan->village_id && !$villageName) {
                    try {
                        $villageData = $this->wilayahService->getVillageById($latestPelayanan->village_id);
                        $villageName = $villageData['name'] ?? null;
                    } catch (\Exception $e) {
                        // Log error but continue
                        \Log::error("Error getting village name: " . $e->getMessage());
                    }
                }
            } else {
                // Default if no records exist
                $queueNumber = '01';
            }
        }

        return view('guest.surat.ahli-waris', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers',
            'queueNumber',
            'villageName'
        ));
    }

    /**
     * Store a newly created ahli waris certificate in storage.
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
            'nik' => 'nullable|array',
            'full_name' => 'nullable|array',
            'birth_place' => 'nullable|array',
            'birth_date' => 'nullable|array',
            'gender' => 'nullable|array',
            'religion' => 'nullable|array',
            'address' => 'nullable|array',
            'family_status' => 'nullable|array',
            'heir_name' => 'nullable|string|max:255',
            'deceased_name' => 'nullable|string|max:255',
            'death_place' => 'nullable|string|max:255',
            'death_date' => 'nullable|date',
            'death_certificate_number' => 'nullable|string',
            'death_certificate_date' => 'nullable|date',
            'inheritance_letter_date' => 'nullable|date',
            'inheritance_type' => 'required|string|max:255',
            'signing' => 'nullable|string',
        ]);

        try {
            // Create the ahli waris record
            AhliWaris::create($request->all());

            // Get location parameters for the redirect
            $provinceId = $request->input('province_id');
            $districtId = $request->input('district_id');
            $subDistrictId = $request->input('subdistrict_id');
            $villageId = $request->input('village_id');

            // Redirect with location parameters
            return redirect()->route('guest.pelayanan.list', [
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'sub_district_id' => $subDistrictId,
                'village_id' => $villageId
            ])->with('success', 'Silakan menuju bagian administrasi');
        } catch (\Exception $e) {
            Log::error('Error creating ahli waris certificate: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);
            return back()->with('error', 'Gagal membuat Surat Keterangan Ahli Waris: ' . $e->getMessage());
        }
    }
}
