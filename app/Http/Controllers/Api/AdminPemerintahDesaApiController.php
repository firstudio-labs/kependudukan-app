<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PemerintahDesaSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPemerintahDesaApiController extends Controller
{
    private function getAdminUser(Request $request)
    {
        return $request->attributes->get('token_owner') ?? Auth::guard('web')->user();
    }

    private function ensureAdminAccess($user)
    {
        if (!$user) return [false, 'Unauthorized'];
        $allowed = ['superadmin', 'admin desa', 'admin kabupaten', 'operator'];
        if (!$user->role || !in_array(strtolower($user->role), $allowed)) return [false, 'Forbidden'];
        return [true, null];
    }

    /**
     * Tampilkan ringkasan pemerintah desa untuk admin login.
     * villages_id diambil dari user admin yang login (tabel users).
     */
    public function show(Request $request, PemerintahDesaSummaryService $summaryService)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        $villageId = $user->villages_id;
        if (!$villageId) {
            return response()->json(['message' => 'villages_id tidak ditemukan pada akun admin'], 422);
        }

        $summary = $summaryService->getSummaryForVillage((int) $villageId);

        return response()->json($summary);
    }
}


