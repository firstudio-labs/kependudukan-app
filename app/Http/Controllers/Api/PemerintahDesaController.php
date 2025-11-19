<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CitizenService;
use App\Services\PemerintahDesaSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemerintahDesaController extends Controller
{
    public function show(
        Request $request,
        PemerintahDesaSummaryService $summaryService,
        CitizenService $citizenService
    )
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        // Get village_id from user or from CitizenService if not available
        $villageId = $user->villages_id;
        
        // If village_id is null and user has NIK, try to get it from CitizenService
        if (!$villageId && isset($user->nik)) {
            $citizenData = $citizenService->getCitizenByNIK($user->nik);
            
            if ($citizenData && isset($citizenData['data']['village_id'])) {
                $villageId = $citizenData['data']['village_id'];
            }
        }

        if (!$villageId) {
            return response()->json(['message' => 'villages_id tidak ditemukan'], 422);
        }

        $summary = $summaryService->getSummaryForVillage((int) $villageId);

        return response()->json($summary);
    }
}


