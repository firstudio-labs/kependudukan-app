<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\Administration; // Add this import for the Administration model
use Illuminate\Support\Facades\Session;
use App\Models\Pelayanan; // Add this import for the Pelayanan model

class AdministrasiUmumController extends Controller
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
     * Display the administration form for guest users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $jobs = $this->jobService->getAllJobs();
        $provinces = $this->wilayahService->getProvinces();
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

        return view('guest.surat.administrasi', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'queueNumber',
            'villageName'
        ));
    }

    /**
     * Store a newly created administration letter in storage
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
            'religion' => 'required|string',
            'citizen_status' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'letter_date' => 'nullable|date',
            'statement_content' => 'nullable|string',
            'purpose' => 'nullable|string',
            'province_id' => 'required|string',
            'district_id' => 'required|string',
            'subdistrict_id' => 'required|string',
            'village_id' => 'required|string',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            Administration::create($request->all());
            
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
            return back()->with('error', 'Gagal membuat surat administrasi: ' . $e->getMessage());
        }
    }
}
