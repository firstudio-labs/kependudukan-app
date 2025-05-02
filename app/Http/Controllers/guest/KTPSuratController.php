<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\PengantarKtp;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Pelayanan;

class KTPSuratController extends Controller
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
     * Display the KTP application form for guest users
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
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

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

        return view('guest.surat.ktp', compact(
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
     * Store a newly created KTP application letter in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Guest KTP store request', $request->all());

        // Manual validation to provide better error messages
        $validator = Validator::make($request->all(), [
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'application_type' => 'required|string|in:Baru,Perpanjang,Pergantian',
            'nik' => 'required|string',
            'full_name' => 'required|string',
            'kk' => 'required|string',
            'address' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'hamlet' => 'nullable|string', // Changed to nullable to match database
            'signing' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Formulir memiliki kesalahan. Silakan periksa kembali.');
        }

        try {
            DB::beginTransaction();

            // Get village and subdistrict names from service if needed by the model
            try {
                // $subdistrictData = $this->wilayahService->getSubdistrictById($request->subdistrict_id);
                // $villageData = $this->wilayahService->getVillageById($request->village_id);

                // Prepare data without subdistrict_name and village_name fields
                $data = [
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'subdistrict_id' => $request->subdistrict_id,
                    'village_id' => $request->village_id,
                    'letter_number' => $request->letter_number,
                    'application_type' => $request->application_type,
                    'nik' => $request->nik,
                    'full_name' => $request->full_name,
                    'kk' => $request->kk,
                    'address' => $request->address,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'hamlet' => $request->hamlet,
                    'signing' => $request->signing,
                ];

                // Debug what's being saved
                Log::info('Saving KTP data', ['data' => $data]);

                // Create record with only the needed fields
                $ktp = PengantarKtp::create($data);

                DB::commit();

                Log::info('KTP Pengantar created successfully', ['id' => $ktp->id]);

                return redirect()->route('guest.surat.ktp')
                    ->with('success', 'Surat pengantar KTP berhasil dibuat!');
            } catch (\Exception $e) {
                throw new \Exception('Gagal mendapatkan data wilayah: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create KTP pengantar: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat surat pengantar KTP: ' . $e->getMessage());
        }
    }
}
