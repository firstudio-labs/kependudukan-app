<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\RumahSewa;
use App\Models\Penandatangan;
use Illuminate\Support\Facades\Session;
use App\Models\Pelayanan;
use Illuminate\Support\Facades\Log;

class RumahSewaSuratController extends Controller
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
     * Display the rumah sewa form for guest users
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

        return view('guest.surat.rumah-sewa', compact(
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
     * Store a newly created rental house permit in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('RumahSewa Guest Store Request:', $request->all());

        // Validation rules that match the form fields exactly
        $validated = $request->validate([
            // Location fields
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',

            // Personal information fields
            'nik' => 'required|string',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'responsible_name' => 'required|string|max:255',

            // Rental property details
            'rental_address' => 'required|string',
            'street' => 'required|string|max:255',
            'alley_number' => 'required|string|max:50',
            'rt' => 'required|string|max:10',
            'building_area' => 'required|string|max:255',
            'room_count' => 'required|integer',
            'rental_type' => 'required|string|max:255',
            'valid_until' => 'nullable|date',
            
            // Administrative fields
            'letter_number' => 'nullable|string|max:255',
            'signing' => 'nullable|string|max:255',
            
            // Optional fields
            'rf_id_tag' => 'nullable|string',
        ]);

        try {
            // Create new rental house record
            $rumahSewa = new RumahSewa();
            $rumahSewa->fill($validated);
            $rumahSewa->save();

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
            // Log the error
            Log::error('Failed to create Rumah Sewa: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat izin rumah sewa: ' . $e->getMessage());
        }
    }
}
