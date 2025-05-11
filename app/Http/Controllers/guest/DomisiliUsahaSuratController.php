<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use App\Models\DomisiliUsaha;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Pelayanan;

class DomisiliUsahaSuratController extends Controller
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
     * Display the business domicile form for guest users
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

        return view('guest.surat.domisili-usaha', compact(
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
     * Store a newly created business domicile certificate in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Guest Domisili Usaha store request', $request->all());

        // Manual validation to provide better error messages
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:16',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'job_type_id' => 'required',
            'religion' => 'required',
            'citizen_status' => 'required',
            'address' => 'required|string',
            'rt' => 'required|string',
            'business_address' => 'nullable|string',
            'business_type' => 'nullable|string|max:255',
            'business_year' => 'nullable|string|max:4',
            'letter_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
            'village_id' => 'required',
            'letter_number' => 'nullable|string',
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
                'nik' => $request->nik,
                'full_name' => $request->full_name,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'job_type_id' => $request->job_type_id,
                'religion' => $request->religion,
                'citizen_status' => $request->citizen_status,
                'address' => $request->address,
                'rt' => $request->rt,
                'business_address' => $request->business_address,
                'business_type' => $request->business_type,
                'business_year' => $request->business_year,
                'letter_date' => $request->letter_date,
                'purpose' => $request->purpose,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'village_id' => $request->village_id,
                'letter_number' => $request->letter_number,
                'signing' => $request->signing,
            ];

            // Debug what's being saved
            Log::info('Saving domisili usaha data', ['data' => $data]);

            $domisiliUsaha = DomisiliUsaha::create($data);

            DB::commit();

            Log::info('Domisili Usaha created successfully', ['id' => $domisiliUsaha->id]);

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
            Log::error('Failed to create domisili usaha: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat surat keterangan domisili usaha: ' . $e->getMessage());
        }
    }
}
