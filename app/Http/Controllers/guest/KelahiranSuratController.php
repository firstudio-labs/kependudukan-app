<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\Kelahiran;
use App\Models\Penandatangan;
use Illuminate\Support\Facades\Session;
use App\Models\Pelayanan;
use Illuminate\Support\Facades\Log;

class KelahiranSuratController extends Controller
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
     * Display the birth certificate form for guest users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobTypes = $this->jobService->getAllJobs();

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

        return view('guest.surat.kelahiran', compact(
            'jobTypes',
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
     * Store a newly created birth certificate in storage.
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
            'signing' => 'nullable|string',
        ]);

        try {
            // Get all request data for the Kelahiran model
            $data = $request->all();

            // Ensure child_gender is stored correctly
            $data['child_gender'] = $request->child_gender;

            // Create the kelahiran record
            Kelahiran::create($data);

            return redirect()->route('guest.surat.kelahiran')
                ->with('success', 'Surat Keterangan Kelahiran berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('Error creating birth certificate: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);
            return back()->with('error', 'Gagal membuat Surat Keterangan Kelahiran: ' . $e->getMessage());
        }
    }
}
