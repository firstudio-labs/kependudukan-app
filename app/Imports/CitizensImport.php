<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Log;

class CitizensImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $citizenService;
    public $errors = [];
    public $successCount = 0;
    public $processedRows = 0;
    public $skippedRows = 0;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        Log::info('=== STARTING EXCEL IMPORT ===');
        Log::info('Total rows in Excel: ' . $rows->count());

        // Log headers/columns
        if ($rows->count() > 0) {
            $firstRow = $rows->first();
            Log::info('Excel columns found: ' . implode(', ', array_keys($firstRow->toArray())));
        }

        foreach ($rows as $index => $row) {
            $this->processedRows++;
            Log::info("Processing row " . ($index + 2) . ": " . json_encode($row->toArray()));

            try {
                // Skip empty rows - check multiple possible column names
                $nik = $row['nik'] ?? $row['NIK'] ?? null;
                $kk = $row['kk'] ?? $row['KK'] ?? $row['no_kk'] ?? $row['No_KK'] ?? null;
                $fullName = $row['full_name'] ?? $row['nama_lgkp'] ?? $row['nama'] ?? $row['Nama'] ?? null;

                if (empty($nik) || empty($kk) || empty($fullName)) {
                    Log::warning("Row " . ($index + 2) . " skipped - missing required fields (nik: " . ($nik ?? 'empty') . ", kk: " . ($kk ?? 'empty') . ", full_name: " . ($fullName ?? 'empty') . ")");
                    Log::warning("Available columns in row: " . implode(', ', array_keys($row->toArray())));
                    $this->skippedRows++;
                    continue;
                }

                // Convert row data to appropriate format
                $citizenData = $this->formatRowData($row, $nik, $kk, $fullName);
                Log::info("Formatted data for row " . ($index + 2) . ": " . json_encode($citizenData));

                // Skip if NIK is invalid
                if (strlen((string)$citizenData['nik']) !== 16) {
                    $errorMsg = "Baris " . ($index + 2) . ": NIK harus 16 digit (current: " . strlen((string)$citizenData['nik']) . " digits)";
                    Log::error($errorMsg);
                    $this->errors[] = $errorMsg;
                    continue;
                }

                // Log NIK validation
                Log::info("NIK validation passed: " . $citizenData['nik'] . " (length: " . strlen((string)$citizenData['nik']) . ")");

                // Validate required fields
                $requiredFields = ['nik', 'kk', 'full_name', 'gender', 'birth_date', 'birth_place', 'address', 'province_id', 'district_id', 'sub_district_id', 'village_id', 'rt', 'rw'];
                $missingFields = [];
                foreach ($requiredFields as $field) {
                    if (empty($citizenData[$field]) && $citizenData[$field] !== 0) {
                        $missingFields[] = $field;
                        Log::warning("Missing required field: {$field}");
                    }
                }

                if (!empty($missingFields)) {
                    $errorMsg = "Baris " . ($index + 2) . ": Field yang diperlukan kosong: " . implode(', ', $missingFields);
                    Log::error($errorMsg);
                    $this->errors[] = $errorMsg;
                    continue;
                }

                Log::info("All required fields validation passed for row " . ($index + 2));

                // Check if citizen already exists (to determine create or update)
                $existingCitizen = $this->citizenService->getCitizenByNIK($citizenData['nik']);
                Log::info("Checking existing citizen for NIK " . $citizenData['nik'] . ": " . (isset($existingCitizen['data']) ? 'EXISTS' : 'NOT FOUND'));

                if (isset($existingCitizen['data']) && !empty($existingCitizen['data'])) {
                    // Update existing citizen
                    Log::info("Updating existing citizen with NIK: " . $citizenData['nik']);
                    $response = $this->citizenService->updateCitizen($citizenData['nik'], $citizenData);
                } else {
                    // Create new citizen
                    Log::info("Creating new citizen with NIK: " . $citizenData['nik']);
                    $response = $this->citizenService->createCitizen($citizenData);
                }

                Log::info("API response for row " . ($index + 2) . ": " . json_encode($response));

                if (isset($response['status']) && ($response['status'] === 'OK' || $response['status'] === 'CREATED')) {
                    $this->successCount++;
                    Log::info("Row " . ($index + 2) . " processed successfully");
                } else {
                    $errorMsg = "Baris " . ($index + 2) . ": " . ($response['message'] ?? 'Gagal menyimpan data');
                    Log::error($errorMsg . " - Response: " . json_encode($response));
                    $this->errors[] = $errorMsg;
                }
            } catch (\Exception $e) {
                $errorMsg = "Baris " . ($index + 2) . ": " . $e->getMessage();
                Log::error('Import error for row ' . ($index + 2) . ': ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                $this->errors[] = $errorMsg;
            }
        }

        Log::info('=== IMPORT SUMMARY ===');
        Log::info('Total rows processed: ' . $this->processedRows);
        Log::info('Successfully imported: ' . $this->successCount);
        Log::info('Skipped rows: ' . $this->skippedRows);
        Log::info('Errors: ' . count($this->errors));
        if (count($this->errors) > 0) {
            Log::error('Error details: ' . implode('; ', $this->errors));
        }
        Log::info('=== ENDING EXCEL IMPORT ===');
    }

    /**
     * Convert row data to the format expected by the API
     */
    private function formatRowData($row, $nik = null, $kk = null, $fullName = null)
    {
        // Mapping nama kolom Excel ke field yang dibutuhkan oleh API
        $map = [
            'nik' => 'nik',
            'no_kk' => 'kk',
            'nama_lgkp' => 'full_name',
            'jenis_kelamin' => 'gender',
            'tanggal_lahir' => 'birth_date',
            'tempat_lahir' => 'birth_place',
            'umur' => 'age',
            'alamat' => 'address',
            'no_rt' => 'rt',
            'no_rw' => 'rw',
            'no_prop' => 'province_id',
            'no_kab' => 'district_id',
            'no_kec' => 'sub_district_id',
            'no_kel' => 'village_id',
            'kode_pos' => 'postal_code',
            'agama' => 'religion',
            'golongan_darah' => 'blood_type',
            'shdk' => 'family_status',
            'nama_ayah' => 'father',
            'nama_ibu' => 'mother',
            'akta_lahir' => 'birth_certificate',
            'no_akta_lahir' => 'birth_certificate_no',
            'status_kawin' => 'marital_status',
            'akta_kawin' => 'marital_certificate',
            'no_akta_kawin' => 'marital_certificate_no',
            'marriage_date' => 'marriage_date',
            'akta_cerai' => 'divorce_certificate',
            'no_akta_cerai' => 'divorce_certificate_no',
            'divorce_certificate_date' => 'divorce_certificate_date',
            'pendidikan' => 'education_status',
            'pekerjaan' => 'job_type_id',
            'nik_ayah' => 'nik_father',
            'nik_ibu' => 'nik_mother',
            // tambahkan mapping lain jika perlu
        ];
        // Lakukan mapping dari kolom Excel ke field standar
        foreach ($map as $excelKey => $apiKey) {
            if (isset($row[$excelKey]) && !isset($row[$apiKey])) {
                $row[$apiKey] = $row[$excelKey];
                Log::info("Mapped {$excelKey} to {$apiKey}: " . $row[$excelKey]);
            }
        }

        // Mapping nama ke angka untuk field tertentu (lengkap seperti normalizeSelectValues)
        $genderMap = [
            'Laki-Laki' => 1, 'Laki-laki' => 1, 'Perempuan' => 2,
            'laki-laki' => 1, 'laki-laki' => 1, 'perempuan' => 2,
            'LAKI-LAKI' => 1, 'PEREMPUAN' => 2
        ];
        $citizenStatusMap = ['WNI' => 2, 'WNA' => 1];
        $certificateMap = [
            'Ada' => 1, 'Tidak Ada' => 2,
            'ada' => 1, 'tidak ada' => 2,
            'ADA' => 1, 'TIDAK ADA' => 2
        ];
        $bloodTypeMap = [
            'A' => 1, 'B' => 2, 'AB' => 3, 'O' => 4,
            'A+' => 5, 'A-' => 6, 'B+' => 7, 'B-' => 8,
            'AB+' => 9, 'AB-' => 10, 'O+' => 11, 'O-' => 12, 'Tidak Tahu' => 13,
            'a' => 1, 'b' => 2, 'ab' => 3, 'o' => 4,
            'a+' => 5, 'a-' => 6, 'b+' => 7, 'b-' => 8,
            'ab+' => 9, 'ab-' => 10, 'o+' => 11, 'o-' => 12, 'tidak tahu' => 13
        ];
        $religionMap = [
            'Islam' => 1, 'Kristen' => 2, 'Katolik' => 3, 'Katholik' => 3,
            'Hindu' => 4, 'Buddha' => 5, 'Budha' => 5,
            'Kong Hu Cu' => 6, 'Konghucu' => 6, 'Lainnya' => 7,
            'islam' => 1, 'kristen' => 2, 'katolik' => 3, 'katholik' => 3,
            'hindu' => 4, 'buddha' => 5, 'budha' => 5,
            'kong hu cu' => 6, 'konghucu' => 6, 'lainnya' => 7
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
            'ANAK' => 1, 'Anak' => 1, 'anak' => 1,
            'KEPALA KELUARGA' => 2, 'Kepala Keluarga' => 2, 'kepala keluarga' => 2,
            'ISTRI' => 3, 'Istri' => 3, 'istri' => 3,
            'ORANG TUA' => 4, 'Orang Tua' => 4, 'orang tua' => 4,
            'MERTUA' => 5, 'Mertua' => 5, 'mertua' => 5,
            'CUCU' => 6, 'Cucu' => 6, 'cucu' => 6,
            'FAMILI LAIN' => 7, 'Famili Lain' => 7, 'famili lain' => 7
        ];
        $disabilitiesMap = [
            'Fisik' => 1,
            'Netra/Buta' => 2,
            'Rungu/Wicara' => 3,
            'Mental/Jiwa' => 4,
            'Fisik dan Mental' => 5,
            'Lainnya' => 6
        ];


        // Ambil mapping pekerjaan dari API (cache di static agar tidak bolak-balik request)
        static $jobNameToId = null;
        if ($jobNameToId === null) {
            try {
                $jobService = app(\App\Services\JobService::class);
                $jobs = $jobService->getAllJobs();
                $jobNameToId = [];
                foreach ($jobs as $job) {
                    if (isset($job['name']) && isset($job['id'])) {
                        $jobNameToId[trim(strtolower($job['name']))] = $job['id'];
                    }
                }
            } catch (\Exception $e) {
                $jobNameToId = [];
            }
        }

        // Helper untuk mengambil nilai, jika tidak ada maka null/default
        $get = function($key, $default = null) use ($row) {
            $value = isset($row[$key]) ? $row[$key] : $default;
            Log::info("Getting value for key '{$key}': " . (is_null($value) ? 'NULL' : $value));
            return $value;
        };

        // Log semua key yang tersedia di row untuk debugging
        Log::info("Available keys in row: " . implode(', ', array_keys($row->toArray())));
        Log::info("Row data: " . json_encode($row->toArray()));

        $data = [
            'nik' => (int) ($nik ?? $get('nik', 0)),
            'kk' => (int) ($kk ?? $get('no_kk', 0)),
            'full_name' => $fullName ?? $get('nama_lgkp', ''),
            'gender' => isset($genderMap[$get('jenis_kelamin', '')]) ? $genderMap[$get('jenis_kelamin', '')] : (int) $get('jenis_kelamin', 1),
            'citizen_status' => 2, // Otomatis WNI
            'birth_certificate' => isset($certificateMap[$get('akta_lahir', '')]) ? $certificateMap[$get('akta_lahir', '')] : (int) $get('akta_lahir', 2),
            'blood_type' => $this->mapBloodType($get('golongan_darah', '')),
            'religion' => isset($religionMap[$get('agama', '')]) ? $religionMap[$get('agama', '')] : (int) $get('agama', 1),
            'marital_status' => isset($maritalStatusMap[$get('status_kawin', '')]) ? $maritalStatusMap[$get('status_kawin', '')] : (int) $get('status_kawin', 1),
            'marital_certificate' => isset($certificateMap[$get('akta_kawin', '')]) ? $certificateMap[$get('akta_kawin', '')] : (int) $get('akta_kawin', 2),
            'divorce_certificate' => isset($certificateMap[$get('akta_cerai', '')]) ? $certificateMap[$get('akta_cerai', '')] : (int) $get('akta_cerai', 2),
            'family_status' => isset($familyStatusMap[$get('shdk', '')]) ? $familyStatusMap[$get('shdk', '')] : (int) $get('shdk', 2),
            'mental_disorders' => 2, // Otomatis 2 (Tidak Ada) jika tidak ada kolom
            'disabilities' => isset($disabilitiesMap[$get('disabilities', '')]) ? $disabilitiesMap[$get('disabilities', '')] : (isset($row['disabilities']) ? (int) $row['disabilities'] : 0),
            'education_status' => $this->mapEducationStatus($get('pendidikan', '')),
            'birth_date' => $this->formatDate($get('tanggal_lahir', '')),
            'birth_place' => $get('tempat_lahir', ''),
            'age' => (int) $get('umur', 0),
            'address' => $get('alamat', ''),
            'rt' => (string) $get('no_rt', ''),
            'rw' => (string) $get('no_rw', ''),
            'province_id' => (int) $get('no_prop', 0),
            'district_id' => (int) $get('no_kab', 0),
            'sub_district_id' => (int) $get('no_kec', 0),
            'village_id' => (int) $get('no_kel', 0),
            'postal_code' => $get('kode_pos', '0'),
            'father' => $get('nama_ayah', ''),
            'mother' => $get('nama_ibu', ''),
            'nik_father' => $get('nik_ayah', ' '),
            'nik_mother' => $get('nik_ibu', ' '),
            'birth_certificate_no' => $get('no_akta_lahir', ' '),
            'marital_certificate_no' => $get('no_akta_kawin', ' '),
            'marriage_date' => $get('marriage_date') ? $this->formatDate($get('marriage_date')) : ' ',
            'divorce_certificate_no' => $get('no_akta_cerai', ' '),
            'divorce_certificate_date' => $get('divorce_certificate_date') ? $this->formatDate($get('divorce_certificate_date')) : ' ',
            'job_type_id' => (int) $get('pekerjaan', 0),
            'coordinate' => $get('coordinate', ' '),
            'telephone' => $get('telephone', null),
            'email' => $get('email', null),
            'hamlet' => $get('hamlet', null),
            'foreign_address' => $get('foreign_address', null),
            'city' => $get('city', null),
            'state' => $get('state', null),
            'country' => $get('country', null),
            'foreign_postal_code' => $get('foreign_postal_code', null),
            'status' => $get('status', null),
            'rf_id_tag' => $get('rf_id_tag', null),
        ];

        // Log processed data untuk debugging
        Log::info("Processed data: " . json_encode($data));

        // Log mapping details untuk debugging
        Log::info("Mapping details:");
        Log::info("- Gender mapping: " . $get('jenis_kelamin', '') . " -> " . $data['gender']);
        Log::info("- Birth certificate mapping: " . $get('akta_lahir', '') . " -> " . $data['birth_certificate']);
        Log::info("- Blood type mapping: " . $get('golongan_darah', '') . " -> " . $data['blood_type']);
        Log::info("- Religion mapping: " . $get('agama', '') . " -> " . $data['religion']);
        Log::info("- Marital status mapping: " . $get('status_kawin', '') . " -> " . $data['marital_status']);
        Log::info("- Marital certificate mapping: " . $get('akta_kawin', '') . " -> " . $data['marital_certificate']);
        Log::info("- Divorce certificate mapping: " . $get('akta_cerai', '') . " -> " . $data['divorce_certificate']);
        Log::info("- Family status mapping: " . $get('shdk', '') . " -> " . $data['family_status']);
        Log::info("- Education status mapping: " . $get('pendidikan', '') . " -> " . $data['education_status']);
        Log::info("- Blood type mapping: " . $get('golongan_darah', '') . " -> " . $data['blood_type']);

        // Mapping job_type_id: jika value di Excel berupa nama, konversi ke id
        $jobValue = $get('pekerjaan', null);
        $jobId = null;
        if (is_numeric($jobValue)) {
            $jobId = (int)$jobValue;
        } elseif (is_string($jobValue) && $jobValue !== '') {
            $jobKey = trim(strtolower($jobValue));
            $jobId = $jobNameToId[$jobKey] ?? 0;
        } else {
            $jobId = 0;
        }
        $data['job_type_id'] = $jobId;

        Log::info("Job mapping - Original value: {$jobValue}, Mapped to ID: {$jobId}");

        return $data;
    }

    /**
     * Format dates from Excel to Y-m-d format
     */
    private function formatDate($date)
    {
        if (empty($date)) return ' ';

        // Handle Excel date format
        if (is_numeric($date)) {
            // Convert Excel date to PHP date
            $unixDate = ($date - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }

        // Try to parse as regular date
        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Map blood type with special handling for empty or invalid values
     */
    private function mapBloodType($value)
    {
        $bloodTypeMap = [
            'A' => 1, 'B' => 2, 'AB' => 3, 'O' => 4,
            'A+' => 5, 'A-' => 6, 'B+' => 7, 'B-' => 8,
            'AB+' => 9, 'AB-' => 10, 'O+' => 11, 'O-' => 12, 'Tidak Tahu' => 13,
            'a' => 1, 'b' => 2, 'ab' => 3, 'o' => 4,
            'a+' => 5, 'a-' => 6, 'b+' => 7, 'b-' => 8,
            'ab+' => 9, 'ab-' => 10, 'o+' => 11, 'o-' => 12, 'tidak tahu' => 13
        ];

        // Handle empty, null, or "-" values
        if (empty($value) || $value === '-' || $value === 'null' || $value === 'NULL') {
            Log::info("Blood type empty/invalid, defaulting to 13 (Tidak Tahu)");
            return 13; // Tidak Tahu
        }

        // Check if value exists in mapping
        if (isset($bloodTypeMap[$value])) {
            Log::info("Blood type mapping found: '{$value}' -> {$bloodTypeMap[$value]}");
            return $bloodTypeMap[$value];
        }

        // If numeric, validate range
        if (is_numeric($value)) {
            $intValue = (int) $value;
            if ($intValue >= 1 && $intValue <= 13) {
                Log::info("Blood type numeric value: '{$value}' -> {$intValue}");
                return $intValue;
            }
        }

        // Log unmapped value
        Log::warning("Blood type unmapped value: '{$value}', defaulting to 13 (Tidak Tahu)");

        // Default to "Tidak Tahu" if value is invalid
        return 13;
    }

    /**
     * Map education status with special handling for empty or invalid values
     */
    private function mapEducationStatus($value)
    {
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
            'lainnya' => 10,
            // Tambahan mapping untuk nilai yang mungkin ada di Excel
            'SD' => 3,
            'SMP' => 4,
            'SMA' => 5,
            'S1' => 8,
            'S2' => 8,
            'S3' => 9,
            'sd' => 3,
            'smp' => 4,
            'sma' => 5,
            's1' => 8,
            's2' => 8,
            's3' => 9,
            // Tambahan variasi format
            'TAMAT SD/SEDERAJAT' => 3,
            'TAMAT SD' => 3,
            'BELUM TAMAT SD/SEDERAJAT' => 2,
            'BELUM TAMAT SD' => 2,
            'TIDAK/BELUM SEKOLAH' => 1,
            'SLTP/SMP' => 4,
            'SLTA/SMA' => 5,
            'DIPLOMA I/II' => 6,
            'AKADEMI/DIPLOMA III/SARJANA MUDA' => 7,
            'DIPLOMA IV/STRATA I/STRATA II' => 8,
            'STRATA III' => 9,
            'LAINNYA' => 10
        ];

        // Handle empty, null, or "-" values
        if (empty($value) || $value === '-' || $value === 'null' || $value === 'NULL') {
            Log::info("Education status empty/invalid, defaulting to 1 (Tidak/Belum Sekolah)");
            return 1; // Tidak/Belum Sekolah
        }

        // Check if value exists in mapping
        if (isset($educationStatusMap[$value])) {
            Log::info("Education status mapping found: '{$value}' -> {$educationStatusMap[$value]}");
            return $educationStatusMap[$value];
        }

        // If numeric, validate range
        if (is_numeric($value)) {
            $intValue = (int) $value;
            if ($intValue >= 1 && $intValue <= 10) {
                Log::info("Education status numeric value: '{$value}' -> {$intValue}");
                return $intValue;
            }
        }

        // Log unmapped value
        Log::warning("Education status unmapped value: '{$value}', defaulting to 1 (Tidak/Belum Sekolah)");

        // Default to "Tidak/Belum Sekolah" if value is invalid
        return 1;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nik' => 'required',
            'no_kk' => 'required',
            'nama_lgkp' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'umur' => 'required',
            'tempat_lahir' => 'required',
            'alamat' => 'required',
            'no_rt' => 'required',
            'no_rw' => 'required',
            'no_prop' => 'required',
            'no_kab' => 'required',
            'no_kec' => 'required',
            'no_kel' => 'required',
        ];
    }
}

