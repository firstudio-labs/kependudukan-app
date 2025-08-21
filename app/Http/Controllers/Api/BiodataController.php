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

class BiodataController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    /**
     * Get current user biodata
     */
    public function getBiodata(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Get citizen data from API
            $citizenData = $this->citizenService->getCitizenByNIK($user->nik);
            
            if (!$citizenData) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nik' => $user->nik,
                        'email' => $user->email,
                        'no_hp' => $user->no_hp ?? null,
                    ],
                    'biodata' => $citizenData
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting biodata: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil data biodata'
            ], 500);
        }
    }

    /**
     * Request biodata update approval - MAIN FUNCTION FOR MOBILE APP
     */
    public function requestUpdate(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Validation rules - sama dengan form web
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'kk' => 'nullable|string',
                'gender' => 'required|string|in:Laki-laki,Perempuan',
                'age' => 'nullable|numeric',
                'birth_place' => 'nullable|string|max:255',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'province_id' => 'required|numeric',
                'district_id' => 'required|numeric',
                'sub_district_id' => 'required|numeric',
                'village_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get current citizen data for comparison
            $currentData = $this->citizenService->getCitizenByNIK($user->nik);
            
            if (!$currentData) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data penduduk saat ini tidak ditemukan'
                ], 404);
            }

            // Check if there are actual changes
            $hasChanges = false;
            $requestedChanges = $request->only([
                'full_name', 'kk', 'gender', 'age', 'birth_place', 
                'birth_date', 'address', 'rt', 'rw', 
                'province_id', 'district_id', 'sub_district_id', 'village_id'
            ]);

            foreach ($requestedChanges as $key => $value) {
                if (isset($currentData[$key]) && $currentData[$key] != $value) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Tidak ada perubahan data yang diminta'
                ], 400);
            }

            // Check if there's already a pending request
            $existingRequest = ProfileChangeRequest::where('nik', $user->nik)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Anda masih memiliki permintaan perubahan yang sedang diproses'
                ], 400);
            }

            // Create profile change request
            $profileChangeRequest = ProfileChangeRequest::create([
                'nik' => $user->nik,
                'village_id' => $request->village_id,
                'current_data' => $currentData,
                'requested_changes' => $requestedChanges,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permintaan perubahan biodata berhasil dikirim ke admin desa',
                'data' => [
                    'request_id' => $profileChangeRequest->id,
                    'status' => 'pending',
                    'requested_at' => $profileChangeRequest->requested_at,
                    'requested_changes' => $requestedChanges,
                    'village_id' => $request->village_id,
                    'message' => 'Permintaan Anda sedang menunggu approval dari admin desa'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error requesting biodata update: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengirim permintaan perubahan'
            ], 500);
        }
    }

    /**
     * Get user's biodata change history
     */
    public function getHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $history = ProfileChangeRequest::where('nik', $user->nik)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'status' => $request->status,
                        'requested_at' => $request->requested_at,
                        'reviewed_at' => $request->reviewed_at,
                        'reviewer_name' => $request->reviewer ? $request->reviewer->name : null,
                        'reviewer_note' => $request->reviewer_note,
                        'requested_changes' => $request->requested_changes,
                        'current_data' => $request->current_data,
                        'status_message' => $this->getStatusMessage($request->status),
                    ];
                });

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'history' => $history,
                    'total_requests' => $history->count(),
                    'pending_requests' => $history->where('status', 'pending')->count(),
                    'approved_requests' => $history->where('status', 'approved')->count(),
                    'rejected_requests' => $history->where('status', 'rejected')->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting biodata history: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil history'
            ], 500);
        }
    }

    /**
     * Get specific request detail
     */
    public function getRequestDetail(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $profileRequest = ProfileChangeRequest::where('id', $requestId)
                ->where('nik', $user->nik)
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
                    'status' => $profileRequest->status,
                    'status_message' => $this->getStatusMessage($profileRequest->status),
                    'requested_at' => $profileRequest->requested_at,
                    'reviewed_at' => $profileRequest->reviewed_at,
                    'reviewer_name' => $profileRequest->reviewer ? $profileRequest->reviewer->name : null,
                    'reviewer_note' => $profileRequest->reviewer_note,
                    'requested_changes' => $profileRequest->requested_changes,
                    'current_data' => $profileRequest->current_data,
                    'village_id' => $profileRequest->village_id,
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
     * Cancel pending request
     */
    public function cancelRequest(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $profileRequest = ProfileChangeRequest::where('id', $requestId)
                ->where('nik', $user->nik)
                ->where('status', 'pending')
                ->first();

            if (!$profileRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Permintaan tidak ditemukan atau tidak dapat dibatalkan'
                ], 404);
            }

            $profileRequest->delete();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permintaan perubahan biodata berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error canceling request: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat membatalkan permintaan'
            ], 500);
        }
    }

    /**
     * Get status message for mobile display
     */
    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'pending':
                return 'Menunggu approval dari admin desa';
            case 'approved':
                return 'Permintaan disetujui dan data telah diperbarui';
            case 'rejected':
                return 'Permintaan ditolak oleh admin desa';
            default:
                return 'Status tidak diketahui';
        }
    }
}
