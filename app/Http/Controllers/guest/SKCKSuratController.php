<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SKCK;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\Pelayanan; // Add this import
use Illuminate\Support\Facades\DB;

class SKCKSuratController extends Controller
{
    protected $jobService;
    protected $wilayahService;
    protected $citizenService;

    public function __construct(JobService $jobService, WilayahService $wilayahService, CitizenService $citizenService)
    {
        $this->jobService = $jobService;
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
    }

    /**
     * Display SKCK form for guest
     */
    public function index()
    {
        // Get jobs and provinces data
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

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

        return view('guest.surat.skck', compact('jobs', 'provinces', 'queueNumber', 'villageName'));
    }

    /**
     * Store SKCK request from guest
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
            'letter_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'signing' => 'nullable|string',
        ]);

        try {
            // Create SKCK with status pending
            $data = $request->all();
            $data['status'] = 'pending'; // Set status to pending for guest submissions

            SKCK::create($data);

            // Get village name for display in antrian
            $villageName = '';
            try {
                $villages = $this->wilayahService->getDesa($request->subdistrict_code);
                foreach ($villages as $village) {
                    if ($village['id'] == $request->village_id) {
                        $villageName = $village['name'];
                        break;
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error getting village name: " . $e->getMessage());
            }

            // Get queue number from session or generate a new one
            $queueNumber = session('no_antrian');
            if (!$queueNumber) {
                // Get the latest queue number from today's records
                $today = now()->format('Y-m-d');
                $latestPelayanan = Pelayanan::whereDate('created_at', $today)
                    ->whereNotNull('no_antrian')
                    ->orderBy('no_antrian', 'desc')
                    ->first();

                if ($latestPelayanan) {
                    $queueNumber = $latestPelayanan->formatted_queue_number;
                } else {
                    $queueNumber = '01';
                }
            }

            return redirect()->route('guest.surat.skck')
                ->with('success', 'Permohonan SKCK berhasil dikirim!')
                ->with('village_name', $villageName);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim permohonan: ' . $e->getMessage());
        }
    }
}
