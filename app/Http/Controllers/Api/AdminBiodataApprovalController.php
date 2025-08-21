<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminBiodataApprovalController extends Controller
{
	protected $citizenService;

	public function __construct(CitizenService $citizenService)
	{
		$this->citizenService = $citizenService;
		$this->middleware('auth:sanctum');
	}

	/**
	 * Get list of pending biodata change requests for admin desa
	 */
	public function getPendingRequests(Request $request)
	{
		try {
			$user = Auth::user();
			
			$allowed = $user && (($user->role === 'admin desa') || ($user->role === 'admin_desa') || !empty($user->villages_id));
			if (!$allowed) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Akses ditolak. Hanya admin desa yang dapat mengakses'
				], 403);
			}

			$requests = ProfileChangeRequest::where('village_id', $user->villages_id)
				->where('status', 'pending')
				->orderBy('created_at', 'desc')
				->get()
				->map(function ($request) {
					return [
						'id' => $request->id,
						'nik' => $request->nik,
						'requested_at' => $request->requested_at,
						'user_name' => $request->requested_changes['full_name'] ?? 'N/A',
						'requested_changes_summary' => $this->getChangesSummary($request->requested_changes),
						'current_data_summary' => $this->getCurrentDataSummary($request->current_data),
					];
				});

			return response()->json([
				'status' => 'SUCCESS',
				'data' => [
					'requests' => $requests,
					'total_pending' => $requests->count(),
					'village_id' => $user->villages_id,
					'admin_name' => $user->name,
				]
			]);

		} catch (\Exception $e) {
			Log::error('Error getting pending requests: ' . $e->getMessage());
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Terjadi kesalahan saat mengambil data permintaan'
			], 500);
		}
	}

	/**
	 * Get specific request detail for admin review
	 */
	public function getRequestDetail(Request $request, $requestId)
	{
		try {
			$user = Auth::user();
			
			$allowed = $user && (($user->role === 'admin desa') || ($user->role === 'admin_desa') || !empty($user->villages_id));
			if (!$allowed) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Akses ditolak. Hanya admin desa yang dapat mengakses'
				], 403);
			}

			$profileRequest = ProfileChangeRequest::where('id', $requestId)
				->where('village_id', $user->villages_id)
				->first();

			if (!$profileRequest) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Permintaan tidak ditemukan'
				], 404);
			}

			return response()->json([
				'status' => 'SUCCESS',
				'data' => [
					'id' => $profileRequest->id,
					'nik' => $profileRequest->nik,
					'status' => $profileRequest->status,
					'requested_at' => $profileRequest->requested_at,
					'reviewed_at' => $profileRequest->reviewed_at,
					'reviewed_by' => $profileRequest->reviewed_by,
					'reviewer_name' => $profileRequest->reviewer ? $profileRequest->reviewer->name : null,
					'reviewer_note' => $profileRequest->reviewer_note,
					'requested_changes' => $profileRequest->requested_changes,
					'current_data' => $profileRequest->current_data,
					'village_id' => $profileRequest->village_id,
					'changes_summary' => $this->getChangesSummary($profileRequest->requested_changes),
					'current_data_summary' => $this->getCurrentDataSummary($profileRequest->current_data),
				]
			]);

		} catch (\Exception $e) {
			Log::error('Error getting request detail: ' . $e->getMessage());
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Terjadi kesalahan saat mengambil detail permintaan'
			], 500);
		}
	}

	/**
	 * Approve biodata change request
	 */
	public function approve(Request $request, $requestId)
	{
		try {
			$user = Auth::user();
			
			$allowed = $user && (($user->role === 'admin desa') || ($user->role === 'admin_desa') || !empty($user->villages_id));
			if (!$allowed) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Akses ditolak. Hanya admin desa yang dapat mengakses'
				], 403);
			}

			$validator = Validator::make($request->all(), [
				'reviewer_note' => 'nullable|string|max:1000',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Validasi gagal',
					'errors' => $validator->errors()
				], 422);
			}

			$profileRequest = ProfileChangeRequest::where('id', $requestId)
				->where('village_id', $user->villages_id)
				->where('status', 'pending')
				->first();

			if (!$profileRequest) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Permintaan tidak ditemukan atau tidak dapat disetujui'
				], 404);
			}

			// Convert form data to API format by merging with existing data
			$apiData = $this->convertFormDataToApiFormat($profileRequest->requested_changes, $profileRequest);
			
			if (empty($apiData)) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Tidak ada data valid yang dapat diperbarui'
				], 400);
			}

			// Update citizen via API
			$updateResult = $this->citizenService->updateCitizen($profileRequest->nik, $apiData);

			if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Gagal menerapkan perubahan ke data penduduk: ' . ($updateResult['message'] ?? 'Unknown error')
				], 500);
			}

			// Update request status
			$profileRequest->update([
				'status' => 'approved',
				'reviewed_at' => now(),
				'reviewed_by' => $user->id,
				'reviewer_note' => $request->reviewer_note,
			]);

			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Permintaan disetujui dan data diperbarui',
				'data' => [
					'request_id' => $profileRequest->id,
					'status' => 'approved',
					'reviewed_at' => $profileRequest->reviewed_at,
					'reviewer_note' => $profileRequest->reviewer_note,
					'admin_name' => $user->name,
					'nik' => $profileRequest->nik,
				]
			]);

		} catch (\Exception $e) {
			Log::error('Error approving request: ' . $e->getMessage());
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Terjadi kesalahan saat memproses approval: ' . $e->getMessage()
			], 500);
		}
	}

	/**
	 * Reject biodata change request
	 */
	public function reject(Request $request, $requestId)
	{
		try {
			$user = Auth::user();
			
			$allowed = $user && (($user->role === 'admin desa') || ($user->role === 'admin_desa') || !empty($user->villages_id));
			if (!$allowed) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Akses ditolak. Hanya admin desa yang dapat mengakses'
				], 403);
			}

			$validator = Validator::make($request->all(), [
				'reviewer_note' => 'required|string|max:1000',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Validasi gagal',
					'errors' => $validator->errors()
				], 422);
			}

			$profileRequest = ProfileChangeRequest::where('id', $requestId)
				->where('village_id', $user->villages_id)
				->where('status', 'pending')
				->first();

			if (!$profileRequest) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Permintaan tidak ditemukan atau tidak dapat ditolak'
				], 404);
			}

			// Update request status
			$profileRequest->update([
				'status' => 'rejected',
				'reviewed_at' => now(),
				'reviewed_by' => $user->id,
				'reviewer_note' => $request->reviewer_note,
			]);

			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Permintaan ditolak',
				'data' => [
					'request_id' => $profileRequest->id,
					'status' => 'rejected',
					'reviewed_at' => $profileRequest->reviewed_at,
					'reviewer_note' => $profileRequest->reviewer_note,
					'admin_name' => $user->name,
					'nik' => $profileRequest->nik,
				]
			]);

		} catch (\Exception $e) {
			Log::error('Error rejecting request: ' . $e->getMessage());
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Terjadi kesalahan saat memproses rejection: ' . $e->getMessage()
			], 500);
		}
	}

	/**
	 * Convert form data to API format by merging with existing data
	 * PRINCIPLE: Don't make up data - only use what exists and what user wants to change
	 */
	private function convertFormDataToApiFormat($formData, $requestModel)
	{
		try {
			// Get existing citizen data from API
			$existingData = $this->citizenService->getCitizenByNIK($requestModel->nik ?? '');
			
			if (!$existingData || !is_array($existingData)) {
				Log::error('Could not fetch existing citizen data from API', [
					'nik' => $requestModel->nik ?? 'unknown'
				]);
				throw new \Exception('Tidak dapat mengambil data penduduk yang sudah ada');
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

	/**
	 * Get summary of requested changes for mobile display
	 */
	private function getChangesSummary($requestedChanges)
	{
		$summary = [];
		
		if (isset($requestedChanges['full_name'])) {
			$summary[] = 'Nama: ' . $requestedChanges['full_name'];
		}
		if (isset($requestedChanges['gender'])) {
			$summary[] = 'Jenis Kelamin: ' . $requestedChanges['gender'];
		}
		if (isset($requestedChanges['birth_place'])) {
			$summary[] = 'Tempat Lahir: ' . $requestedChanges['birth_place'];
		}
		if (isset($requestedChanges['address'])) {
			$summary[] = 'Alamat: ' . $requestedChanges['address'];
		}
		if (isset($requestedChanges['rt'])) {
			$summary[] = 'RT: ' . $requestedChanges['rt'];
		}
		if (isset($requestedChanges['rw'])) {
			$summary[] = 'RW: ' . $requestedChanges['rw'];
		}

		return $summary;
	}

	/**
	 * Get summary of current data for mobile display
	 */
	private function getCurrentDataSummary($currentData)
	{
		$summary = [];
		
		if (isset($currentData['full_name'])) {
			$summary[] = 'Nama: ' . $currentData['full_name'];
		}
		if (isset($currentData['gender'])) {
			$summary[] = 'Jenis Kelamin: ' . $currentData['gender'];
		}
		if (isset($currentData['birth_place'])) {
			$summary[] = 'Tempat Lahir: ' . $currentData['birth_place'];
		}
		if (isset($currentData['address'])) {
			$summary[] = 'Alamat: ' . $currentData['address'];
		}
		if (isset($currentData['rt'])) {
			$summary[] = 'RT: ' . $currentData['rt'];
		}
		if (isset($currentData['rw'])) {
			$summary[] = 'RW: ' . $currentData['rw'];
		}

		return $summary;
	}
}
