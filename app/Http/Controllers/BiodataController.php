<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // You'll need to install Laravel Excel package
use App\Imports\CitizensImport; // We'll create this import class
use Illuminate\Support\Facades\Auth;

class BiodataController extends Controller
{
    protected $citizenService;
    protected $jobService;
    protected $wilayahService;

    public function __construct(
        CitizenService $citizenService,
        JobService $jobService,
        WilayahService $wilayahService
    ) {
        $this->citizenService = $citizenService;
        $this->jobService = $jobService;
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10); // Set your desired limit here
        $search = $request->input('search');

        // Jika user adalah admin desa, ambil data warga berdasarkan village_id admin
        if (Auth::user()->role == 'admin desa') {
            $villageId = Auth::user()->villages_id;

            if ($search) {
                $citizens = $this->citizenService->getCitizensByVillageId($villageId, $page, $limit, $search);
            } else {
                // Ambil warga berdasarkan village_id admin desa dengan parameter page
                $citizens = $this->citizenService->getCitizensByVillageId($villageId, $page);
            }

            // Siapkan data paginasi untuk view
            $paginationData = [];
            if (isset($citizens['data']['pagination'])) {
                $paginationData = [
                    'current_page' => $citizens['data']['pagination']['current_page'],
                    'total_page' => $citizens['data']['pagination']['total_page'],
                    'base_url' => route('admin.desa.biodata.index') . '?',
                    'search' => $search
                ];
            }

            return view('admin.desa.biodata.index', compact('citizens', 'search', 'paginationData'));        } else {
            // Untuk superadmin tampilkan semua data
            if ($search) {
                $citizens = $this->citizenService->searchCitizens($search);
                // If search returns null or error, fallback to getting all citizens
                if (!$citizens || isset($citizens['status']) && $citizens['status'] === 'ERROR') {
                    $citizens = $this->citizenService->getAllCitizens($page);
                    session()->flash('warning', 'Search failed, showing all results instead');
                }
            } else {
                $citizens = $this->citizenService->getAllCitizens($page);
            }

            return view('superadmin.biodata.index', compact('citizens', 'search'));
        }
    }

    public function create()
    {
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];


        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.biodata.create', compact(
                'provinces',
                'jobs',
                'districts',
                'subDistricts',
                'villages'
            ));
        }

        return view('superadmin.biodata.create', compact(
            'provinces',
            'jobs',
            'districts',
        ));
    }

    public function store(Request $request)
    {
        try {
            // Check if this is a family member submission from the KK page
            $isFromKKPage = $request->header('Referer') && str_contains($request->header('Referer'), 'datakk/create');

            $validatedData = $request->validate([
                'nik' => 'required|size:16',
                'kk' => 'required|size:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|integer|in:1,2',
                'birth_date' => 'required|date',
                'age' => 'required|integer',
                'birth_place' => 'required|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'required|string|max:3',
                'rw' => 'required|string|max:3',
                'postal_code' => 'nullable|digits:5',
                'citizen_status' => 'required|integer|in:1,2',
                'birth_certificate' => 'integer|in:1,2',
                'birth_certificate_no' => 'nullable|string',
                'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                'religion' => 'required|in:1,2,3,4,5,6,7',
                'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                'marital_certificate' => 'required|in:1,2',
                'marital_certificate_no' => 'nullable|string',
                'marriage_date' => 'nullable|date',
                'divorce_certificate' => 'nullable|integer|in:1,2',
                'divorce_certificate_no' => 'nullable|string',
                'divorce_certificate_date' => 'nullable|date',
                'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                'mental_disorders' => 'required|integer|in:1,2',
                'disabilities' => 'nullable|integer|in:1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255',
                'coordinate' => 'nullable|string|max:255',
                // New fields
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                'rf_id_tag' => 'nullable|integer',
            ]);

            // Batch process nullable fields
            $this->processNullableFields($validatedData);

            // Convert NIK and KK to integers
            $validatedData['nik'] = (int) $validatedData['nik'];
            $validatedData['kk'] = (int) $validatedData['kk'];
            $validatedData['religion'] = (int) $validatedData['religion'];

            $response = $this->citizenService->createCitizen($validatedData);

            if ($response['status'] === 'CREATED') {
                // If coming from KK page, redirect back there
                if ($isFromKKPage) {

                    if (Auth::user()->role == 'admin desa') {
                        return redirect()
                            ->route('admin.desa.datakk.create')
                            ->with('success', 'Anggota keluarga berhasil ditambahkan!');
                    }

                    return redirect()
                        ->route('superadmin.datakk.create')
                        ->with('success', 'Anggota keluarga berhasil ditambahkan!');
                }

                if (Auth::user()->role == 'admin desa') {
                    return redirect()
                        ->route('admin.desa.biodata.index')
                        ->with('success', $response['message']);
                }

                // Otherwise, go to the regular biodata index
                return redirect()
                    ->route('superadmin.biodata.index')
                    ->with('success', $response['message']);
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal menyimpan data');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($nik)
    {
        $citizen = $this->citizenService->getCitizenByNIK($nik);
        if (!$citizen || !isset($citizen['data'])) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        // Format date fields to yyyy-MM-dd for HTML date inputs
        $this->formatDatesForView($citizen['data']);

        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Get location data for pre-selecting dropdowns
        $districts = $this->wilayahService->getKabupaten($citizen['data']['province_id']);
        $subDistricts = $this->wilayahService->getKecamatan($citizen['data']['district_id']);
        $villages = $this->wilayahService->getDesa($citizen['data']['sub_district_id']);

        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.biodata.update', compact(
                'citizen',
                'provinces',
                'jobs',
                'districts',
                'subDistricts',
                'villages'
            ));
        }

        return view('superadmin.biodata.update', compact(
            'citizen',
            'provinces',
            'jobs',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    public function update(Request $request, $nik)
    {
        try {
            $page = $request->input('current_page', 1);

            $validatedData = $request->validate([
                'kk' => 'required|size:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|integer|in:1,2',
                'birth_date' => 'required|date',
                'age' => 'required|integer',
                'birth_place' => 'required|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'required|string|max:3',
                'rw' => 'required|string|max:3',
                'postal_code' => 'nullable|digits:5',
                'citizen_status' => 'required|integer|in:1,2',
                'birth_certificate' => 'integer|in:1,2',
                'birth_certificate_no' => 'nullable|string',
                'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                'religion' => 'required|integer|in:1,2,3,4,5,6,7',
                'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                'marital_certificate' => 'required|in:1,2',
                'marital_certificate_no' => 'nullable|string',
                'marriage_date' => 'nullable|date',
                'divorce_certificate' => 'nullable|integer|in:1,2',
                'divorce_certificate_no' => 'nullable|string',
                'divorce_certificate_date' => 'nullable|date',
                'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                'mental_disorders' => 'required|integer|in:1,2',
                'disabilities' => 'nullable|integer|in:1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255',
                'coordinate' => 'nullable|string|max:255',
                // New fields
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                'rf_id_tag' => 'nullable|integer',
            ]);

            // Process nullable fields
            $this->processNullableFields($validatedData);
            $nik = (int) $nik;

            // Convert KK to integer
            $validatedData['kk'] = (int) $validatedData['kk'];

            $response = $this->citizenService->updateCitizen($nik, $validatedData);

            if ($response['status'] === 'OK') {

                if (Auth::user()->role == 'admin desa') {
                    return redirect()
                        ->route('admin.desa.biodata.index')
                        ->with('success', 'Biodata berhasil diperbarui!');
                }

                return redirect()
                    ->route('superadmin.biodata.index', ['page' => $page])
                    ->with('success', 'Biodata berhasil diperbarui!');
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal memperbarui data');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id, Request $request)
    {
        $page = $request->query('page', 1);
        $response = $this->citizenService->deleteCitizen($id);

        if ($response['status'] === 'OK') {
            if (Auth::user()->role == 'admin desa') {
                return redirect()
                    ->route('admin.desa.biodata.index')
                    ->with('success', 'Biodata berhasil dihapus!');
            }

            return redirect()
                ->route('superadmin.biodata.index', ['page' => $page])
                ->with('success', 'Biodata berhasil dihapus!');
        }

        if (Auth::user()->role == 'admin desa') {
            return redirect()
                ->route('admin.desa.biodata.index')
                ->with('error', 'Gagal menghapus biodata: ' . $response['message']);
        }

        return redirect()
            ->route('superadmin.biodata.index', ['page' => $page])
            ->with('error', 'Gagal menghapus biodata: ' . $response['message']);
    }

    private function getJobs()
    {
        try {
            $response = Http::get('https://api.desaverse.id/jobs');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Jobs API request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching jobs: ' . $e->getMessage());
            return [];
        }
    }

    public function getDistricts($provinceCode)
    {
        $districts = $this->wilayahService->getKabupaten($provinceCode);
        return response()->json($districts);
    }

    public function getSubDistricts($districtCode)
    {
        $subDistricts = $this->wilayahService->getKecamatan($districtCode);
        return response()->json($subDistricts);
    }

    public function getVillages($subDistrictCode)
    {
        $villages = $this->wilayahService->getDesa($subDistrictCode);
        return response()->json($villages);
    }

    private function formatDatesForView(&$data)
    {
        $dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];

        foreach ($dateFields as $field) {
            if (isset($data[$field]) && !empty($data[$field]) && $data[$field] !== " ") {
                try {
                    // Check if it's already in yyyy-MM-dd format
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data[$field])) {
                        continue;
                    }

                    // Handle dd/MM/yyyy format
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data[$field])) {
                        $parts = explode('/', $data[$field]);
                        if (count($parts) === 3) {
                            $data[$field] = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                            continue;
                        }
                    }

                    // Try standard date parsing as fallback
                    $timestamp = strtotime($data[$field]);
                    if ($timestamp !== false) {
                        $data[$field] = date('Y-m-d', $timestamp);
                    }
                } catch (\Exception $e) {
                    Log::error('Error formatting date: ' . $e->getMessage());
                    // Keep original value if we can't format it
                }
            }
        }
    }

    private function formatDates(array $data)
    {
        $dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];

        foreach ($dateFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }

        return $data;
    }

    public function getCitizenByNIK($nik)
    {
        // Ambil data citizen dari service
        $citizen = $this->citizenService->getCitizenByNIK($nik);

        // Jika data tidak ditemukan, return null
        if (!$citizen || !isset($citizen['data'])) {
            return null;
        }

        // Before returning the citizen data, ensure the select field values are numeric
        $this->normalizeSelectValues($citizen['data']);

        return $citizen;
    }

    private function normalizeSelectValues(&$data)
    {
        $genderMap = ['Laki-Laki' => 1, 'Perempuan' => 2];
        $citizenStatusMap = ['WNI' => 1, 'WNA' => 2];
        $certificateMap = ['Ada' => 1, 'Tidak Ada' => 2];
        $bloodTypeMap = [
            'A' => 1,
            'B' => 2,
            'AB' => 3,
            'O' => 4,
            'A+' => 5,
            'A-' => 6,
            'B+' => 7,
            'B-' => 8,
            'AB+' => 9,
            'AB-' => 10,
            'O+' => 11,
            'O-' => 12,
            'Tidak Tahu' => 13
        ];
        $religionMap = [
            'Islam' => 1,
            'Kristen' => 2,
            'Katolik' => 3,
            'Katholik' => 3,
            'Hindu' => 4,
            'Buddha' => 5,
            'Budha' => 5,
            'Kong Hu Cu' => 6,
            'Konghucu' => 6,
            'Lainnya' => 7
        ];
        $maritalStatusMap = [
            'Belum Kawin' => 1,
            'Kawin Tercatat' => 2,
            'Kawin Belum Tercatat' => 3,
            'Cerai Hidup Tercatat' => 4,
            'Cerai Hidup Belum Tercatat' => 5,
            'Cerai Mati' => 6
        ];
        $familyStatusMap = [
            'ANAK' => 1,
            'Anak' => 1,
            'KEPALA KELUARGA' => 2,
            'Kepala Keluarga' => 2,
            'ISTRI' => 3,
            'Istri' => 3,
            'ORANG TUA' => 4,
            'Orang Tua' => 4,
            'MERTUA' => 5,
            'Mertua' => 5,
            'CUCU' => 6,
            'Cucu' => 6,
            'FAMILI LAIN' => 7,
            'Famili Lain' => 7
        ];
        $disabilitiesMap = [
            'Fisik' => 1,
            'Netra/Buta' => 2,
            'Rungu/Wicara' => 3,
            'Mental/Jiwa' => 4,
            'Fisik dan Mental' => 5,
            'Lainnya' => 6
        ];
        $educationStatusMap = [
            'Tidak/Belum Sekolah' => 1,
            'Belum tamat SD/Sederajat' => 2,
            'Tamat SD' => 3,
            'SLTP/SMP/Sederajat' => 4,
            'SLTA/SMA/Sederajat' => 5,
            'Diploma I/II' => 6,
            'Akademi/Diploma III/ Sarjana Muda' => 7,
            'Diploma IV/ Strata I/ Strata II' => 8,
            'Strata III' => 9,
            'Lainnya' => 10
        ];

        $fieldsToNormalize = [
            'gender' => $genderMap,
            'citizen_status' => $citizenStatusMap,
            'birth_certificate' => $certificateMap,
            'blood_type' => $bloodTypeMap,
            'religion' => $religionMap,
            'marital_status' => $maritalStatusMap,
            'marital_certificate' => $certificateMap,
            'divorce_certificate' => $certificateMap,
            'family_status' => $familyStatusMap,
            'mental_disorders' => $certificateMap,
            'disabilities' => $disabilitiesMap,
            'education_status' => $educationStatusMap,
        ];

        foreach ($fieldsToNormalize as $field => $mapping) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $value = trim($data[$field]);

                if (array_key_exists($value, $mapping)) {
                    $data[$field] = $mapping[$value];
                    Log::info("Normalized {$field} from '{$value}' to {$data[$field]}");
                } else if (is_numeric($value)) {
                    $data[$field] = (int)$value;
                } else if (!empty($value)) {
                    Log::warning("Could not normalize {$field} value: '{$value}'");
                }
            }
        }

        return $data;
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            ]);

            $import = new CitizensImport($this->citizenService);
            Excel::import($import, $request->file('excel_file'));

            if (count($import->errors) > 0) {
                $errorMessages = "<ul class='text-left'>";
                foreach ($import->errors as $error) {
                    $errorMessages .= "<li>• {$error}</li>";
                }
                $errorMessages .= "</ul>";

                return redirect()->route('superadmin.biodata.index')
                    ->with('import_errors', $errorMessages);
            }

            return redirect()->route('superadmin.biodata.index')
                ->with('success', 'Import data berhasil! ' . $import->successCount . ' data telah diimport.');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.biodata.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            // Ambil data dari service
            $response = null;

            // Jika user adalah admin desa, hanya ambil data warga berdasarkan village_id admin
            if (Auth::user()->role == 'admin desa') {
                $villageId = Auth::user()->villages_id;
                $response = $this->citizenService->getCitizensByVillageId($villageId);
            } else {
                // Untuk superadmin tampilkan semua data
                $response = $this->citizenService->getAllCitizens();
            }

            $exportData = [];

            // Header untuk Excel
            $exportData[] = [
                'NIK',
                'Nomor KK',
                'Nama Lengkap',
                'Jenis Kelamin',
                'Tanggal Lahir',
                'Tempat Lahir',
                'Usia',
                'Alamat',
                'RT',
                'RW',
                'Provinsi',
                'Kabupaten',
                'Kecamatan',
                'Desa',
                'Kode Pos',
                'Status Kewarganegaraan',
                'Agama',
                'Golongan Darah',
                'Status Dalam Keluarga',
                'Nama Ayah',
                'Nama Ibu',
                'NIK Ayah',
                'NIK Ibu',
            ];

            // Periksa response struktur data
            if (isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
                $citizens = $response['data']['citizens'];

                foreach ($citizens as $citizen) {
                    $exportData[] = [
                        $citizen['nik'] ?? '',
                        $citizen['kk'] ?? '',
                        $citizen['full_name'] ?? '',
                        $citizen['gender'] ?? '',
                        $citizen['birth_date'] ?? '',
                        $citizen['birth_place'] ?? '',
                        $citizen['age'] ?? '',
                        $citizen['address'] ?? '',
                        $citizen['rt'] ?? '',
                        $citizen['rw'] ?? '',
                        $citizen['province_id'] ?? '',
                        $citizen['district_id'] ?? '',
                        $citizen['sub_district_id'] ?? '',
                        $citizen['village_id'] ?? '',
                        $citizen['postal_code'] ?? '',
                        $citizen['citizen_status'] ?? '',
                        $citizen['religion'] ?? '',
                        $citizen['blood_type'] ?? '',
                        $citizen['family_status'] ?? '',
                        $citizen['father'] ?? '',
                        $citizen['mother'] ?? '',
                        $citizen['nik_father'] ?? '',
                        $citizen['nik_mother'] ?? '',
                    ];
                }
            } else {
                // Jika data kosong, beri pesan
                if (Auth::user()->role == 'admin desa') {
                    return redirect()->route('admin.desa.biodata.index')
                        ->with('error', 'Tidak ada data yang bisa diekspor atau format data tidak sesuai');
                }

                return redirect()->route('superadmin.biodata.index')
                    ->with('error', 'Tidak ada data yang bisa diekspor atau format data tidak sesuai');
            }

            $filename = 'biodata_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new \App\Exports\CitizensExport($exportData), $filename);
        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());

            if (Auth::user()->role == 'admin desa') {
                return redirect()->route('admin.desa.biodata.index')
                    ->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
            }

            return redirect()->route('superadmin.biodata.index')
                ->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/biodata_template.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->route('superadmin.biodata.index')
                ->with('error', 'Template tidak ditemukan.');
        }

        return Response::download($filePath, 'template_biodata.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function processNullableFields(&$data)
    {
        $nullableIntegerFields = ['marital_status', 'marital_certificate', 'divorce_certificate', 'postal_code', 'disabilities'];
        foreach ($nullableIntegerFields as $field) {
            $data[$field] = empty($data[$field]) ? 0 : (int) $data[$field];
        }

        $nullableStringFields = [
            'birth_certificate_no',
            'marital_certificate_no',
            'divorce_certificate_no',
            'nik_mother',
            'nik_father',
            'mother',
            'father',
            'coordinate',
            'telephone',
            'email',
            'hamlet',
            'foreign_address',
            'city',
            'state',
            'country',
            'foreign_postal_code',
            'status'
        ];
        foreach ($nullableStringFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : $data[$field];
        }

        $nullableDateFields = ['marriage_date', 'divorce_certificate_date'];
        foreach ($nullableDateFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : date('Y-m-d', strtotime($data[$field]));
        }

        $integerFields = [
            'gender',
            'age',
            'province_id',
            'district_id',
            'sub_district_id',
            'village_id',
            'citizen_status',
            'birth_certificate',
            'blood_type',
            'religion',
            'family_status',
            'mental_disorders',
            'education_status',
            'job_type_id'
        ];
        foreach ($integerFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }

        $dateFields = ['birth_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
    }
}
