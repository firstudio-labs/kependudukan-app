<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BiodataChangeController extends Controller
{
    public function index()
    {
        $penduduk = Auth::guard('penduduk')->user();
        $requests = ProfileChangeRequest::where('nik', $penduduk->nik)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.biodata-change.index', compact('requests'));
    }

    public function create()
    {
        $penduduk = Auth::guard('penduduk')->user();
        $citizenService = app(CitizenService::class);
        $citizen = $citizenService->getCitizenByNIK($penduduk->nik);

        $currentData = $citizen['data'] ?? $citizen ?? [];

        return view('user.biodata-change.create', [
            'nik' => $penduduk->nik,
            'currentData' => $currentData,
        ]);
    }

    public function store(Request $request)
    {
        $penduduk = Auth::guard('penduduk')->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'kk' => 'nullable|numeric',
            'gender' => 'nullable|string',
            'age' => 'nullable|numeric',
            'birth_place' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'province_name' => 'nullable|string',
            'district_name' => 'nullable|string',
            'sub_district_name' => 'nullable|string',
            'village_name' => 'nullable|string',
        ]);

        try {
            $citizenService = app(CitizenService::class);
            $citizen = $citizenService->getCitizenByNIK($penduduk->nik);
            $villageId = $citizen['data']['village_id'] ?? $citizen['village_id'] ?? $citizen['data']['villages_id'] ?? $citizen['villages_id'] ?? null;

            $currentData = $citizen['data'] ?? $citizen ?? [];

            $changeRequest = ProfileChangeRequest::create([
                'nik' => $penduduk->nik,
                'village_id' => $villageId,
                'current_data' => $currentData,
                'requested_changes' => $validated,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            return redirect()->route('user.biodata-change.index')->with('success', 'Permintaan perubahan terkirim. Menunggu persetujuan admin desa.');
        } catch (\Exception $e) {
            Log::error('Error creating profile change request (web): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim permintaan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $penduduk = Auth::guard('penduduk')->user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->nik === $penduduk->nik, 403);

        return view('user.biodata-change.show', compact('requestModel'));
    }
}
