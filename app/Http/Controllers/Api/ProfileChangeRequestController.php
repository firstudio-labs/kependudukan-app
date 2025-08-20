<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ApiTokenOwnerMiddleware;
use App\Models\ProfileChangeRequest;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileChangeRequestController extends Controller
{
    public function __construct()
    {
        // Ensure ApiTokenOwnerMiddleware runs for all methods except store (store needs it too for penduduk token)
        $this->middleware(ApiTokenOwnerMiddleware::class);
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

            $tokenOwner = $request->attributes->get('token_owner');
            $tokenOwnerType = $request->attributes->get('token_owner_type');

            if (!$tokenOwner || $tokenOwnerType !== 'penduduk') {
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
        $tokenOwner = $request->attributes->get('token_owner');
        $tokenOwnerType = $request->attributes->get('token_owner_type');
        $tokenOwnerRole = $request->attributes->get('token_owner_role');

        if (!$tokenOwner || $tokenOwnerType !== 'user' || $tokenOwnerRole !== 'admin desa') {
            return response()->json([
                'status' => 'UNAUTHORIZED',
                'message' => 'Hanya admin desa yang dapat melihat daftar permintaan'
            ], 401);
        }

        $villageId = $tokenOwner->villages_id ?? null;
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
        $tokenOwner = $request->attributes->get('token_owner');
        $tokenOwnerType = $request->attributes->get('token_owner_type');
        $tokenOwnerRole = $request->attributes->get('token_owner_role');

        if (!$tokenOwner || $tokenOwnerType !== 'user' || $tokenOwnerRole !== 'admin desa') {
            return response()->json([
                'status' => 'UNAUTHORIZED',
                'message' => 'Hanya admin desa yang dapat melihat detail permintaan'
            ], 401);
        }

        $requestModel = ProfileChangeRequest::findOrFail($id);

        if ($requestModel->village_id !== ($tokenOwner->villages_id ?? null)) {
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
        $tokenOwner = $request->attributes->get('token_owner');
        $tokenOwnerType = $request->attributes->get('token_owner_type');
        $tokenOwnerRole = $request->attributes->get('token_owner_role');

        if (!$tokenOwner || $tokenOwnerType !== 'user' || $tokenOwnerRole !== 'admin desa') {
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

        if ($requestModel->village_id !== ($tokenOwner->villages_id ?? null)) {
            return response()->json([
                'status' => 'FORBIDDEN',
                'message' => 'Anda tidak berhak menyetujui permintaan ini'
            ], 403);
        }

        // Apply changes via CitizenService
        $citizenService = app(CitizenService::class);
        $updateResult = $citizenService->updateCitizen($requestModel->nik, $requestModel->requested_changes ?? []);

        if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal menerapkan perubahan ke data penduduk'
            ], 500);
        }

        $requestModel->status = 'approved';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $tokenOwner->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Permintaan disetujui dan data diperbarui',
            'data' => $requestModel
        ]);
    }

    /**
     * Reject a pending change request
     */
    public function reject(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        $tokenOwnerType = $request->attributes->get('token_owner_type');
        $tokenOwnerRole = $request->attributes->get('token_owner_role');

        if (!$tokenOwner || $tokenOwnerType !== 'user' || $tokenOwnerRole !== 'admin desa') {
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

        if ($requestModel->village_id !== ($tokenOwner->villages_id ?? null)) {
            return response()->json([
                'status' => 'FORBIDDEN',
                'message' => 'Anda tidak berhak menolak permintaan ini'
            ], 403);
        }

        $requestModel->status = 'rejected';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $tokenOwner->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return response()->json([
            'status' => 'OK',
            'message' => 'Permintaan ditolak',
            'data' => $requestModel
        ]);
    }
}


