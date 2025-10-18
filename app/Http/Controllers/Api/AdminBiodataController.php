<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminBiodataController extends Controller
{
    protected $citizenService;
    protected $wilayahService;
    protected $jobService;

    public function __construct(CitizenService $citizenService, WilayahService $wilayahService, JobService $jobService)
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
        $this->jobService = $jobService;
    }

    private function ensureAdminDesa(Request $request)
    {
        $role = $request->attributes->get('token_owner_role');
        if ($role !== 'admin desa') {
            abort(response()->json([
                'status' => 'ERROR',
                'message' => 'Forbidden: hanya admin desa'
            ], 403));
        }
    }

    public function show(Request $request, $nik)
    {
        $this->ensureAdminDesa($request);

        try {
            $citizen = $this->citizenService->getCitizenByNIK((int) $nik);
            if (!$citizen || !isset($citizen['data'])) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            // Normalize select values untuk memastikan konsistensi
            $this->normalizeSelectValues($citizen['data']);

            // Format date fields untuk view
            $this->formatDatesForView($citizen['data']);

            return response()->json([
                'status' => 'OK',
                'data' => $citizen['data']
            ]);
        } catch (\Exception $e) {
            Log::error('AdminBiodata show error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function update(Request $request, $nik)
    {
        $this->ensureAdminDesa($request);

        try {
            $validated = $request->validate([
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
                'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255',
                'coordinate' => 'nullable|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                'rf_id_tag' => 'nullable|string',
            ]);

            // Process nullable fields seperti di BiodataController
            $this->processNullableFields($validated);

            // Convert NIK and KK to integers
            $validated['nik'] = (int) $nik;
            $validated['kk'] = (int) $validated['kk'];
            $validated['religion'] = (int) $validated['religion'];

            $result = $this->citizenService->updateCitizen($nik, $validated);
            if (!$result || !isset($result['status']) || $result['status'] === 'ERROR') {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => $result['message'] ?? 'Gagal memperbarui biodata'
                ], 422);
            }

            return response()->json([
                'status' => 'OK',
                'message' => $result['message'] ?? 'Biodata berhasil diperbarui',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AdminBiodata update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // Dropdown endpoints (Admin Desa)
    public function provinces(Request $request)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getProvinces();
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function districts(Request $request, $provinceCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getKabupaten($provinceCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function subDistricts(Request $request, $districtCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getKecamatan($districtCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function villages(Request $request, $subDistrictCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getDesa($subDistrictCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function jobs(Request $request)
    {
        $this->ensureAdminDesa($request);
        $data = $this->jobService->getAllJobs();
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function enums(Request $request)
    {
        $this->ensureAdminDesa($request);

        $data = [
            'gender' => [
                ['value' => 1, 'label' => 'Laki-Laki'],
                ['value' => 2, 'label' => 'Perempuan'],
            ],
            'religion' => [
                ['value' => 1, 'label' => 'Islam'],
                ['value' => 2, 'label' => 'Kristen'],
                ['value' => 3, 'label' => 'Katholik'],
                ['value' => 4, 'label' => 'Hindu'],
                ['value' => 5, 'label' => 'Buddha'],
                ['value' => 6, 'label' => 'Kong Hu Cu'],
                ['value' => 7, 'label' => 'Lainnya'],
            ],
            'birth_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'marital_status' => [
                ['value' => 1, 'label' => 'Belum Kawin'],
                ['value' => 2, 'label' => 'Kawin Tercatat'],
                ['value' => 3, 'label' => 'Kawin Belum Tercatat'],
                ['value' => 4, 'label' => 'Cerai Hidup Tercatat'],
                ['value' => 5, 'label' => 'Cerai Hidup Belum Tercatat'],
                ['value' => 6, 'label' => 'Cerai Mati'],
            ],
            'marital_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'divorce_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'blood_type' => [
                ['value' => 1, 'label' => 'A'],
                ['value' => 2, 'label' => 'B'],
                ['value' => 3, 'label' => 'AB'],
                ['value' => 4, 'label' => 'O'],
                ['value' => 5, 'label' => 'A+'],
                ['value' => 6, 'label' => 'A-'],
                ['value' => 7, 'label' => 'B+'],
                ['value' => 8, 'label' => 'B-'],
                ['value' => 9, 'label' => 'AB+'],
                ['value' => 10, 'label' => 'AB-'],
                ['value' => 11, 'label' => 'O+'],
                ['value' => 12, 'label' => 'O-'],
                ['value' => 13, 'label' => 'Tidak Tahu'],
            ],
            'citizen_status' => [
                ['value' => 1, 'label' => 'WNA'],
                ['value' => 2, 'label' => 'WNI'],
            ],
            'family_status' => [
                ['value' => 1, 'label' => 'Anak'],
                ['value' => 2, 'label' => 'Kepala Keluarga'],
                ['value' => 3, 'label' => 'Istri'],
                ['value' => 4, 'label' => 'Orang Tua'],
                ['value' => 5, 'label' => 'Mertua'],
                ['value' => 6, 'label' => 'Cucu'],
                ['value' => 7, 'label' => 'Famili Lain'],
            ],
            'mental_disorders' => [
                ['value' => 1, 'label' => 'Ya'],
                ['value' => 2, 'label' => 'Tidak'],
            ],
            'disabilities' => [
                ['value' => 0, 'label' => 'Tidak Ada'],
                ['value' => 1, 'label' => 'Fisik'],
                ['value' => 2, 'label' => 'Netra/Buta'],
                ['value' => 3, 'label' => 'Rungu/Wicara'],
                ['value' => 4, 'label' => 'Mental/Jiwa'],
                ['value' => 5, 'label' => 'Fisik dan Mental'],
                ['value' => 6, 'label' => 'Lainnya'],
            ],
            'education_status' => [
                ['value' => 1, 'label' => 'Tidak/Belum Sekolah'],
                ['value' => 2, 'label' => 'Belum tamat SD/Sederajat'],
                ['value' => 3, 'label' => 'Tamat SD/Sederajat'],
                ['value' => 4, 'label' => 'SLTP/SMP/Sederajat'],
                ['value' => 5, 'label' => 'SLTA/SMA/Sederajat'],
                ['value' => 6, 'label' => 'Diploma I/II'],
                ['value' => 7, 'label' => 'Akademi/Diploma III/Sarjana Muda'],
                ['value' => 8, 'label' => 'Diploma IV/Strata I/Strata II'],
                ['value' => 9, 'label' => 'Strata III'],
                ['value' => 10, 'label' => 'Lainnya'],
            ],
        ];

        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    /**
     * Process nullable fields seperti di BiodataController
     */
    private function processNullableFields(&$data)
    {
        $nullableIntegerFields = ['marital_status', 'marital_certificate', 'divorce_certificate', 'postal_code'];
        foreach ($nullableIntegerFields as $field) {
            $data[$field] = empty($data[$field]) ? 0 : (int) $data[$field];
        }

        // Special handling for disabilities field
        if (isset($data['disabilities'])) {
            if (empty($data['disabilities']) || $data['disabilities'] == '0') {
                $data['disabilities'] = null;
            } else {
                $data['disabilities'] = (int) $data['disabilities'];
            }
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
            'status',
            'rf_id_tag',
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
            'job_type_id',
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

    /**
     * Normalize select values seperti di BiodataController
     */
    private function normalizeSelectValues(&$data)
    {
        $genderMap = [
            'Laki-Laki' => 1,
            'Laki-laki' => 1,
            'Perempuan' => 2,
            'laki-laki' => 1,
            'laki-laki' => 1,
            'perempuan' => 2,
            'LAKI-LAKI' => 1,
            'PEREMPUAN' => 2
        ];
        $citizenStatusMap = ['WNI' => 2, 'WNA' => 1, 'wni' => 2, 'wna' => 1];
        $certificateMap = [
            'Ada' => 1,
            'Tidak Ada' => 2,
            'ada' => 1,
            'tidak ada' => 2,
            'ADA' => 1,
            'TIDAK ADA' => 2
        ];
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
            'Tidak Tahu' => 13,
            'a' => 1,
            'b' => 2,
            'ab' => 3,
            'o' => 4,
            'a+' => 5,
            'a-' => 6,
            'b+' => 7,
            'b-' => 8,
            'ab+' => 9,
            'ab-' => 10,
            'o+' => 11,
            'o-' => 12,
            'tidak tahu' => 13
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
            'Lainnya' => 7,
            'islam' => 1,
            'kristen' => 2,
            'katolik' => 3,
            'katholik' => 3,
            'hindu' => 4,
            'buddha' => 5,
            'budha' => 5,
            'kong hu cu' => 6,
            'konghucu' => 6,
            'lainnya' => 7
        ];
        $maritalStatusMap = [
            'Belum Kawin' => 1,
            'Kawin Tercatat' => 2,
            'Kawin Belum Tercatat' => 3,
            'Cerai Hidup Tercatat' => 4,
            'Cerai Hidup Belum Tercatat' => 5,
            'Cerai Mati' => 6,
            'BELUM KAWIN' => 1,
            'KAWIN TERCATAT' => 2,
            'KAWIN BELUM TERCATAT' => 3,
            'CERAI HIDUP TERCATAT' => 4,
            'CERAI HIDUP BELUM TERCATAT' => 5,
            'CERAI MATI' => 6
        ];
        $familyStatusMap = [
            'ANAK' => 1,
            'Anak' => 1,
            'anak' => 1,
            'KEPALA KELUARGA' => 2,
            'Kepala Keluarga' => 2,
            'kepala keluarga' => 2,
            'ISTRI' => 3,
            'Istri' => 3,
            'istri' => 3,
            'ORANG TUA' => 4,
            'Orang Tua' => 4,
            'orang tua' => 4,
            'MERTUA' => 5,
            'Mertua' => 5,
            'mertua' => 5,
            'CUCU' => 6,
            'Cucu' => 6,
            'cucu' => 6,
            'FAMILI LAIN' => 7,
            'Famili Lain' => 7,
            'famili lain' => 7
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
            'Tamat SD/Sederajat' => 3,
            'SLTP/SMP/Sederajat' => 4,
            'SLTA/SMA/Sederajat' => 5,
            'Diploma I/II' => 6,
            'Akademi/Diploma III/ Sarjana Muda' => 7,
            'Diploma IV/ Strata I/ Strata II' => 8,
            'Strata III' => 9,
            'Lainnya' => 10,
            'tidak/belum sekolah' => 1,
            'belum tamat sd/sederajat' => 2,
            'tamat sd' => 3,
            'tamat sd/sederajat' => 3,
            'sltp/smp/sederajat' => 4,
            'slta/sma/sederajat' => 5,
            'diploma i/ii' => 6,
            'akademi/diploma iii/ sarjana muda' => 7,
            'diploma iv/ strata i/ strata ii' => 8,
            'strata iii' => 9,
            'lainnya' => 10
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
            if (isset($data[$field])) {
                $value = trim($data[$field]);

                // If it's already numeric, keep it as is
                if (is_numeric($value)) {
                    $data[$field] = (int)$value;
                    continue;
                }

                // Try to map string values to numeric
                if (array_key_exists($value, $mapping)) {
                    $data[$field] = $mapping[$value];
                } else if (!empty($value)) {
                    // Set default values for unknown string values
                    switch ($field) {
                        case 'gender':
                            $data[$field] = 1; // Default to Laki-laki
                            break;
                        case 'citizen_status':
                            $data[$field] = 2; // Default to WNI
                            break;
                        case 'birth_certificate':
                        case 'marital_certificate':
                        case 'divorce_certificate':
                        case 'mental_disorders':
                            $data[$field] = 2; // Default to Tidak Ada
                            break;
                        case 'blood_type':
                            $data[$field] = 13; // Default to Tidak Tahu
                            break;
                        case 'religion':
                            $data[$field] = 1; // Default to Islam
                            break;
                        case 'marital_status':
                            $data[$field] = 1; // Default to Belum Kawin
                            break;
                        case 'family_status':
                            $data[$field] = 2; // Default to Kepala Keluarga
                            break;
                        case 'disabilities':
                            $data[$field] = null; // Default to null (Tidak Ada)
                            break;
                        case 'education_status':
                            $data[$field] = 1; // Default to Tidak/Belum Sekolah
                            break;
                    }
                } else {
                    // Handle empty values
                    switch ($field) {
                        case 'disabilities':
                            $data[$field] = null; // Empty disabilities = null
                            break;
                        default:
                            // Keep existing logic for other fields
                            break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Format dates untuk view seperti di BiodataController
     */
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
}


