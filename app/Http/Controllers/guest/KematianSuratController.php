<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\Kematian;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Pelayanan;

class KematianSuratController extends Controller
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
     * Display the death certificate form for guest users
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

        return view('guest.surat.kematian', compact(
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
     * Store a newly created death certificate in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Guest Kematian store request', $request->all());

        // Manual validation to provide better error messages
        $validator = Validator::make($request->all(), [
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',
            'letter_number' => 'nullable|string',
            'nik' => 'required|string|max:16',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'job_type_id' => 'required',
            'religion' => 'required',
            'citizen_status' => 'required',
            'address' => 'required|string',
            'info' => 'required|string|max:255',
            'rt' => 'nullable|string',
            'rt_letter_date' => 'nullable|date',
            'death_cause' => 'nullable|string|max:255',
            'death_place' => 'nullable|string|max:255',
            'reporter_name' => 'nullable|string|max:255',
            'reporter_relation' => 'nullable|string',
            'death_date' => 'nullable|date',
            'signing' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Formulir memiliki kesalahan. Silakan periksa kembali.');
        }

        try {
            DB::beginTransaction();

            // Prepare data without type casting to maintain original values
            $data = [
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
                'letter_number' => $request->letter_number,
                'nik' => $request->nik,
                'full_name' => $request->full_name,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'job_type_id' => $request->job_type_id,
                'religion' => $request->religion,
                'citizen_status' => $request->citizen_status,
                'address' => $request->address,
                'info' => $request->info,
                'rt' => $request->rt,
                'rt_letter_date' => $request->rt_letter_date,
                'death_cause' => $request->death_cause,
                'death_place' => $request->death_place,
                'reporter_name' => $request->reporter_name,
                'reporter_relation' => $request->reporter_relation,
                'death_date' => $request->death_date,
                'signing' => $request->signing,
            ];

            // Debug what's being saved
            Log::info('Saving kematian data', ['data' => $data]);

            $kematian = Kematian::create($data);

            DB::commit();

            Log::info('Kematian created successfully', ['id' => $kematian->id]);

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
            DB::rollBack();
            Log::error('Failed to create kematian: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat surat keterangan kematian: ' . $e->getMessage());
        }
    }
}
