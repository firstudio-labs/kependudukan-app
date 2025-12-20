<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ProfileChangeRequestController extends Controller
{
	public function __construct()
	{
		// Gunakan Sanctum standar, tanpa middleware kustom
		$this->middleware('auth:sanctum');
	}

	/**
	 * Helper untuk mendapatkan konteks token (owner, type, role)
	 */
	private function resolveTokenContext(): array
	{
		$owner = Auth::user();
		$type = 'user';
		$role = method_exists($owner, 'getAttribute') ? ($owner->getAttribute('role') ?? null) : null;

		// Deteksi model penduduk lewat namespace/kelas atau ketiadaan role
		if ($owner && (stripos(get_class($owner), 'Penduduk') !== false || empty($role))) {
			$type = 'penduduk';
			$role = 'penduduk';
		}

		return [
			'owner' => $owner,
			'type' => $type,
			'role' => $role,
		];
	}

	/**
	 * Store a newly created change request from penduduk
	 */
	public function store(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'nik' => 'required|string',
				'current_data' => 'nullable|array',
				'requested_changes' => 'required|array',
				'status' => 'nullable|in:pending',
				'requested_at' => 'nullable|date',
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Validasi gagal',
					'errors' => $validator->errors()
				], 422);
			}

			$ctx = $this->resolveTokenContext();
			if (!$ctx['owner'] || $ctx['type'] !== 'penduduk') {
				return response()->json([
					'status' => 'UNAUTHORIZED',
					'message' => 'Hanya penduduk yang dapat mengajukan perubahan biodata'
				], 401);
			}

			$villageId = null;
			try {
				$citizenService = app(CitizenService::class);
				$citizen = $citizenService->getCitizenByNIK($request->nik);
				$villageId = $citizen['data']['village_id'] ?? $citizen['village_id'] ?? $citizen['data']['villages_id'] ?? $citizen['villages_id'] ?? null;
			} catch (\Exception $e) {
				Log::warning('Failed to fetch citizen for village id: ' . $e->getMessage());
			}

			$changeRequest = ProfileChangeRequest::create([
				'nik' => $request->nik,
				'village_id' => $villageId,
				'current_data' => $request->input('current_data', []),
				'requested_changes' => $request->input('requested_changes', []),
				'status' => 'pending',
				'requested_at' => $request->input('requested_at', now()),
			]);

			return response()->json([
				'status' => 'OK',
				'message' => 'Permintaan perubahan biodata berhasil dibuat',
				'data' => $changeRequest
			], 201);
		} catch (\Exception $e) {
			Log::error('Error creating profile change request: ' . $e->getMessage());
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Terjadi kesalahan saat membuat permintaan'
			], 500);
		}
	}

	/**
	 * List requests for admin desa by village
	 */
	public function index(Request $request)
	{
		$ctx = $this->resolveTokenContext();
		if (!$ctx['owner'] || $ctx['type'] !== 'user' || $ctx['role'] !== 'admin desa') {
			return response()->json([
				'status' => 'UNAUTHORIZED',
				'message' => 'Hanya admin desa yang dapat melihat daftar permintaan'
			], 401);
		}

		$villageId = $ctx['owner']->villages_id ?? null;
		$status = $request->query('status');

		$query = ProfileChangeRequest::query()->where('village_id', $villageId);
		if ($status) {
			$query->where('status', $status);
		}

		$requests = $query->orderByDesc('created_at')->paginate(20);

		return response()->json([
			'status' => 'OK',
			'data' => $requests
		]);
	}

	/**
	 * Show single request detail for admin desa
	 */
	public function show(Request $request, $id)
	{
		$ctx = $this->resolveTokenContext();
		if (!$ctx['owner'] || $ctx['type'] !== 'user' || $ctx['role'] !== 'admin desa') {
			return response()->json([
				'status' => 'UNAUTHORIZED',
				'message' => 'Hanya admin desa yang dapat melihat detail permintaan'
			], 401);
		}

		$requestModel = ProfileChangeRequest::findOrFail($id);

		if ($requestModel->village_id !== ($ctx['owner']->villages_id ?? null)) {
			return response()->json([
				'status' => 'FORBIDDEN',
				'message' => 'Anda tidak berhak mengakses permintaan ini'
			], 403);
		}

		return response()->json([
			'status' => 'OK',
			'data' => $requestModel
		]);
	}

	/**
	 * Approve a pending change request
	 */
	public function approve(Request $request, $id)
	{
		$ctx = $this->resolveTokenContext();
		if (!$ctx['owner'] || $ctx['type'] !== 'user' || $ctx['role'] !== 'admin desa') {
			return response()->json([
				'status' => 'UNAUTHORIZED',
				'message' => 'Hanya admin desa yang dapat melakukan approval'
			], 401);
		}

		$requestModel = ProfileChangeRequest::findOrFail($id);

		if ($requestModel->status !== 'pending') {
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Permintaan tidak dalam status pending'
			], 400);
		}

		if ($requestModel->village_id !== ($ctx['owner']->villages_id ?? null)) {
			return response()->json([
				'status' => 'FORBIDDEN',
				'message' => 'Anda tidak berhak menyetujui permintaan ini'
			], 403);
		}

		// Merge existing API data + requested changes (Don't make up data)
		try {
			$apiData = $this->convertFormDataToApiFormat($requestModel->requested_changes ?? [], $requestModel);

			if (empty($apiData)) {
				Log::warning('No valid data to update', [
					'nik' => $requestModel->nik,
					'original_data' => $requestModel->requested_changes
				]);
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Tidak ada data valid yang dapat diperbarui'
				], 400);
			}

			Log::info('Sending data to API for approval', [
				'nik' => $requestModel->nik,
				'api_data' => $apiData
			]);

			$citizenService = app(CitizenService::class);
			$updateResult = $citizenService->updateCitizen($requestModel->nik, $apiData);

			if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
				Log::error('Failed to update citizen via API', [
					'nik' => $requestModel->nik,
					'data' => $apiData,
					'result' => $updateResult
				]);
				return response()->json([
					'status' => 'ERROR',
					'message' => 'Gagal menerapkan perubahan ke data penduduk: ' . ($updateResult['message'] ?? 'Unknown error')
				], 500);
			}

			$requestModel->status = 'approved';
			$requestModel->reviewed_at = now();
			$requestModel->reviewed_by = $ctx['owner']->id;
			$requestModel->reviewer_note = $request->input('reviewer_note');
			$requestModel->save();

			// Invalidate cache citizen data karena data telah berubah
			// Ambil KK dari current_data atau requested_changes
			$kk = $requestModel->current_data['kk'] ?? $requestModel->requested_changes['kk'] ?? null;
			$this->invalidateCitizenCache($requestModel->nik, $kk);

			return response()->json([
				'status' => 'OK',
				'message' => 'Permintaan disetujui dan data diperbarui',
				'data' => $requestModel
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
	 * Reject a pending change request
	 */
	public function reject(Request $request, $id)
	{
		$ctx = $this->resolveTokenContext();
		if (!$ctx['owner'] || $ctx['type'] !== 'user' || $ctx['role'] !== 'admin desa') {
			return response()->json([
				'status' => 'UNAUTHORIZED',
				'message' => 'Hanya admin desa yang dapat menolak permintaan'
			], 401);
		}

		$requestModel = ProfileChangeRequest::findOrFail($id);

		if ($requestModel->status !== 'pending') {
			return response()->json([
				'status' => 'ERROR',
				'message' => 'Permintaan tidak dalam status pending'
			], 400);
		}

		if ($requestModel->village_id !== ($ctx['owner']->villages_id ?? null)) {
			return response()->json([
				'status' => 'FORBIDDEN',
				'message' => 'Anda tidak berhak menolak permintaan ini'
			], 403);
		}

		$requestModel->status = 'rejected';
		$requestModel->reviewed_at = now();
		$requestModel->reviewed_by = $ctx['owner']->id;
		$requestModel->reviewer_note = $request->input('reviewer_note');
		$requestModel->save();

		return response()->json([
			'status' => 'OK',
			'message' => 'Permintaan ditolak',
			'data' => $requestModel
		]);
	}

	/**
	 * Convert form data to API format by merging with existing data
	 * PRINCIPLE: Don't make up data - only use what exists and what user wants to change
	 */
	private function convertFormDataToApiFormat(array $formData, ProfileChangeRequest $requestModel): array
	{
		$citizenService = app(CitizenService::class);
		$existingData = $citizenService->getCitizenByNIK($requestModel->nik ?? '');

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

		$apiData = $existingData; // start with existing

		// Update only changed fields
		if (isset($formData['full_name']) && !empty($formData['full_name'])) {
			$apiData['full_name'] = trim($formData['full_name']);
		}

		if (isset($formData['kk']) && !empty($formData['kk'])) {
			$apiData['kk'] = (int) $formData['kk'];
		}

		if (isset($formData['gender']) && !empty($formData['gender'])) {
			$gender = strtolower(trim((string) $formData['gender']));
			$apiData['gender'] = in_array($gender, ['laki-laki', 'l', '1', 'male', 'pria']) ? 1 : (in_array($gender, ['perempuan', 'p', '2', 'female', 'wanita']) ? 2 : ($apiData['gender'] ?? null));
		}

		if (isset($formData['age']) && is_numeric($formData['age'])) {
			$apiData['age'] = (int) $formData['age'];
		}

		if (isset($formData['birth_place']) && !empty($formData['birth_place'])) {
			$apiData['birth_place'] = trim($formData['birth_place']);
		}

		if (isset($formData['birth_date']) && !empty($formData['birth_date'])) {
			try {
				$date = new \DateTime($formData['birth_date']);
				$apiData['birth_date'] = $date->format('Y-m-d');
			} catch (\Exception $e) {
				Log::warning('Invalid birth_date format from form, keeping existing', [
					'form_date' => $formData['birth_date'],
					'existing_date' => $existingData['birth_date'] ?? null
				]);
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

		Log::info('Final API data (existing + changes only)', [
			'nik' => $requestModel->nik,
			'form_data' => $formData,
			'existing_data' => $existingData,
			'final_api_data' => $apiData,
			'message' => 'No data made up - only existing data + user changes'
		]);

		return $apiData;
	}

	/**
	 * Invalidate cache terkait citizen data
	 */
	private function invalidateCitizenCache($nik, $kk = null)
	{
		try {
			$nik = (int) $nik;
			$citizenCacheKey = "citizen_nik_{$nik}";
			$citizenStaleCacheKey = "citizen_nik_stale_{$nik}";
			
			Cache::forget($citizenCacheKey);
			Cache::forget($citizenStaleCacheKey);
			
			// Invalidate cache family members jika ada KK
			if ($kk) {
				$familyCacheKey = "family_members_kk_{$kk}";
				$familyStaleCacheKey = "family_members_kk_stale_{$kk}";
				Cache::forget($familyCacheKey);
				Cache::forget($familyStaleCacheKey);
			}
			
			Log::info('Citizen cache invalidated after approval', ['nik' => $nik, 'kk' => $kk]);
		} catch (\Exception $e) {
			Log::error('Error invalidating citizen cache: ' . $e->getMessage());
		}
	}
}


