<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Log;

class BiodataApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = ProfileChangeRequest::query()->where('village_id', $user->villages_id);
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.desa.biodata-approval.index', compact('requests'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        return view('admin.desa.biodata-approval.show', compact('requestModel'));
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

                    try {
                $citizenService = app(CitizenService::class);
                
                // Convert form data to API format by merging with existing data
                $apiData = $this->convertFormDataToApiFormat($requestModel->requested_changes ?? [], $requestModel);
                
                // Validate that we have at least some data to update
                if (empty($apiData)) {
                    Log::warning('No valid data to update', [
                        'nik' => $requestModel->nik,
                        'original_data' => $requestModel->requested_changes
                    ]);
                    return back()->with('error', 'Tidak ada data valid yang dapat diperbarui');
                }

            // Log the data being sent to API
            Log::info('Sending data to API for approval', [
                'nik' => $requestModel->nik,
                'api_data' => $apiData
            ]);
            
            $updateResult = $citizenService->updateCitizen($requestModel->nik, $apiData);

            if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
                Log::error('Failed to update citizen via API', [
                    'nik' => $requestModel->nik,
                    'data' => $apiData,
                    'result' => $updateResult
                ]);
                return back()->with('error', 'Gagal menerapkan perubahan ke data penduduk: ' . ($updateResult['message'] ?? 'Unknown error'));
            }

            $requestModel->status = 'approved';
            $requestModel->reviewed_at = now();
            $requestModel->reviewed_by = $user->id;
            $requestModel->reviewer_note = $request->input('reviewer_note');
            $requestModel->save();

            return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan disetujui dan data diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in approve method: ' . $e->getMessage(), [
                'nik' => $requestModel->nik ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

        $requestModel->status = 'rejected';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $user->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan ditolak');
    }

    /**
     * Convert form data to API format by merging with existing data
     * PRINCIPLE: Don't make up data - only use what exists and what user wants to change
     */
    private function convertFormDataToApiFormat($formData, $requestModel)
    {
        try {
            // Get existing citizen data from API
            $citizenService = app(CitizenService::class);
            $existingData = $citizenService->getCitizenByNIK($requestModel->nik ?? '');
            
            if (!$existingData || !is_array($existingData)) {
                Log::error('Could not fetch existing citizen data from API', [
                    'nik' => $requestModel->nik ?? 'unknown'
                ]);
                return back()->with('error', 'Tidak dapat mengambil data penduduk yang sudah ada. Silakan coba lagi.');
            }

            Log::info('Fetched existing citizen data from API', [
                'nik' => $requestModel->nik,
                'existing_data' => $existingData
            ]);

            // Start with existing data - DON'T MAKE UP DATA
            $apiData = $existingData;

            // Only update fields that are actually changed in the form
            // Keep all other existing data exactly as it is
            if (isset($formData['full_name']) && !empty($formData['full_name'])) {
                $apiData['full_name'] = trim($formData['full_name']);
            }

            if (isset($formData['kk']) && !empty($formData['kk'])) {
                $apiData['kk'] = (int) $formData['kk'];
            }

            if (isset($formData['gender']) && !empty($formData['gender'])) {
                // Convert gender text to numeric format
                if (strtolower($formData['gender']) === 'laki-laki' || strtolower($formData['gender']) === 'laki-laki') {
                    $apiData['gender'] = 1;
                } elseif (strtolower($formData['gender']) === 'perempuan') {
                    $apiData['gender'] = 2;
                }
            }

            if (isset($formData['age']) && is_numeric($formData['age'])) {
                $apiData['age'] = (int) $formData['age'];
            }

            if (isset($formData['birth_place']) && !empty($formData['birth_place'])) {
                $apiData['birth_place'] = trim($formData['birth_place']);
            }

            if (isset($formData['birth_date']) && !empty($formData['birth_date'])) {
                // Ensure birth_date is in valid format
                try {
                    $date = new \DateTime($formData['birth_date']);
                    $apiData['birth_date'] = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning('Invalid birth_date format from form, keeping existing data', [
                        'form_date' => $formData['birth_date'],
                        'existing_date' => $existingData['birth_date'] ?? 'not set'
                    ]);
                    // Keep existing birth_date if form data is invalid
                }
            }

            if (isset($formData['address']) && !empty($formData['address'])) {
                $apiData['address'] = trim($formData['address']);
            }

            if (isset($formData['rt']) && !empty($formData['rt'])) {
                $apiData['rt'] = trim($formData['rt']);
            }

            if (isset($formData['rw']) && !empty($formData['rw'])) {
                $apiData['rw'] = trim($formData['rw']);
            }

            // Only update location IDs if they are valid integers
            if (isset($formData['province_id']) && is_numeric($formData['province_id'])) {
                $apiData['province_id'] = (int) $formData['province_id'];
            }

            if (isset($formData['district_id']) && is_numeric($formData['district_id'])) {
                $apiData['district_id'] = (int) $formData['district_id'];
            }

            if (isset($formData['sub_district_id']) && is_numeric($formData['sub_district_id'])) {
                $apiData['sub_district_id'] = (int) $formData['sub_district_id'];
            }

            if (isset($formData['village_id']) && is_numeric($formData['village_id'])) {
                $apiData['village_id'] = (int) $formData['village_id'];
            }

            // CRITICAL: Don't add any default values or make up data
            // The API should have all the required data already
            // We only send what exists + what user wants to change

            Log::info('Final API data (existing + changes only)', [
                'nik' => $requestModel->nik,
                'form_data' => $formData,
                'existing_data' => $existingData,
                'final_api_data' => $apiData,
                'message' => 'No data made up - only existing data + user changes'
            ]);

            return $apiData;

        } catch (\Exception $e) {
            Log::error('Error in convertFormDataToApiFormat', [
                'nik' => $requestModel->nik ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't fallback to default values - that would be making up data
            throw new \Exception('Gagal memproses data: ' . $e->getMessage());
        }
    }


}


