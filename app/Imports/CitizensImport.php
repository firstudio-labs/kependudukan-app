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

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if (empty($row['nik']) || empty($row['kk']) || empty($row['full_name'])) {
                    continue;
                }

                // Convert row data to appropriate format
                $citizenData = $this->formatRowData($row);

                // Skip if NIK is invalid
                if (strlen((string)$citizenData['nik']) !== 16) {
                    $this->errors[] = "Baris " . ($index + 2) . ": NIK harus 16 digit";
                    continue;
                }

                // Check if citizen already exists (to determine create or update)
                $existingCitizen = $this->citizenService->getCitizenByNIK($citizenData['nik']);

                if (isset($existingCitizen['data']) && !empty($existingCitizen['data'])) {
                    // Update existing citizen
                    $response = $this->citizenService->updateCitizen($citizenData['nik'], $citizenData);
                } else {
                    // Create new citizen
                    $response = $this->citizenService->createCitizen($citizenData);
                }

                if (isset($response['status']) && ($response['status'] === 'OK' || $response['status'] === 'CREATED')) {
                    $this->successCount++;
                } else {
                    $this->errors[] = "Baris " . ($index + 2) . ": " . ($response['message'] ?? 'Gagal menyimpan data');
                }
            } catch (\Exception $e) {
                Log::error('Import error: ' . $e->getMessage());
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    /**
     * Convert row data to the format expected by the API
     */
    private function formatRowData($row)
    {
        $data = [
            'nik' => (int) $row['nik'],
            'kk' => (int) $row['kk'],
            'full_name' => $row['full_name'],
            'gender' => (int) $row['gender'],
            'birth_date' => $this->formatDate($row['birth_date']),
            'birth_place' => $row['birth_place'],
            'age' => (int) $row['age'],
            'address' => $row['address'],
            'rt' => (string) $row['rt'],
            'rw' => (string) $row['rw'],
            'province_id' => (int) $row['province_id'],
            'district_id' => (int) $row['district_id'],
            'sub_district_id' => (int) $row['sub_district_id'],
            'village_id' => (int) $row['village_id'],
            'postal_code' => $row['postal_code'] ?? '0',
            'citizen_status' => 2, // Set to 2 (WNI) regardless of what's in the Excel file
            'religion' => (int) $row['religion'],
            'blood_type' => (int) $row['blood_type'],
            'family_status' => (int) $row['family_status'],
            'father' => $row['father'] ?? '',
            'mother' => $row['mother'] ?? '',
            'nik_father' => $row['nik_father'] ?? ' ',
            'nik_mother' => $row['nik_mother'] ?? ' ',
            'birth_certificate' => (int) ($row['birth_certificate'] ?? 2),
            'birth_certificate_no' => $row['birth_certificate_no'] ?? ' ',
            'marital_status' => (int) ($row['marital_status'] ?? 1),
            'marital_certificate' => (int) ($row['marital_certificate'] ?? 2),
            'marital_certificate_no' => $row['marital_certificate_no'] ?? ' ',
            'marriage_date' => isset($row['marriage_date']) ? $this->formatDate($row['marriage_date']) : ' ',
            'divorce_certificate' => (int) ($row['divorce_certificate'] ?? 2),
            'divorce_certificate_no' => $row['divorce_certificate_no'] ?? ' ',
            'divorce_certificate_date' => isset($row['divorce_certificate_date']) ? $this->formatDate($row['divorce_certificate_date']) : ' ',
            'education_status' => (int) $row['education_status'],
            'job_type_id' => (int) $row['job_type_id'],
            'mental_disorders' => (int) ($row['mental_disorders'] ?? 2),
            'disabilities' => (int) ($row['disabilities'] ?? 6),
            'coordinate' => $row['coordinate'] ?? ' ',
        ];

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
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nik' => 'required',
            'kk' => 'required',
            'full_name' => 'required',
            'gender' => 'required',
            'birth_date' => 'required',
            'age' => 'required',
        ];
    }
}
