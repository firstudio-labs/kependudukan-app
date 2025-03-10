<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KK;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Cache;

class DataKKController extends Controller
{
    protected $citizenService;
    protected $wilayahService;

    public function __construct(CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $kk = KK::when($search, function ($query, $search) {
            return $query->where('full_name', 'like', "%{$search}%")
                ->orWhere('kk', 'like', "%{$search}%");
        })->paginate(10);

        return view('superadmin.datakk.index', compact('kk', 'search'));
    }

    public function create()
    {
        // Get provinces data with caching
        $provinces = Cache::remember('provinces', 3600, function () {
            return $this->getProvinces();
        });

        return view('superadmin.datakk.create', compact('provinces'));
    }

    /**
     * Get provinces with proper structure for the dropdown
     */
    private function getProvinces()
    {
        try {
            $baseUrl = config('services.kependudukan.url');
            $apiKey = config('services.kependudukan.key');

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->get("{$baseUrl}/api/provinces");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'kk' => 'required|string',
            'full_name' => 'required|string',
            'address' => 'required|string',
            // Add other validation rules as needed
        ]);

        // Create KK record
        $kk = KK::create([
            'kk' => $request->kk,
            'full_name' => $request->full_name,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'jml_anggota_kk' => $request->jml_anggota_kk,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'sub_district_id' => $request->sub_district_id,
            'village_id' => $request->village_id,
            'dusun' => $request->dusun,
            'alamat_luar_negeri' => $request->alamat_luar_negeri,
            'kota' => $request->kota,
            'negara_bagian' => $request->negara_bagian,
            'negara' => $request->negara,
            'kode_pos_luar_negeri' => $request->kode_pos_luar_negeri,
        ]);

        // If you have family members in the request, create them
        if ($request->has('family_members')) {
            foreach ($request->family_members as $member) {
                if (!empty($member['full_name']) && !empty($member['family_status'])) {
                    $kk->familyMembers()->create([
                        'full_name' => $member['full_name'],
                        'family_status' => $member['family_status'],
                    ]);
                }
            }
        }

        return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil disimpan');
    }

    public function edit($id)
    {
        $kk = KK::findOrFail($id);

        // Get provinces data with caching
        $provinces = Cache::remember('provinces', 3600, function () {
            return $this->getProvinces();
        });

        return view('superadmin.datakk.update', compact('kk', 'provinces'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input tanpa kk dan full_name
        $validatedData = $request->validate([
            'address' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'jml_anggota_kk' => 'required|integer',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'province_id' => 'required|string|max:50',
            'district_id' => 'required|string|max:50',
            'sub_district_id' => 'required|string|max:50',
            'village_id' => 'required|string|max:50',
            'dusun' => 'nullable|string|max:50',
            'alamat_luar_negeri' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'negara_bagian' => 'nullable|string|max:50',
            'negara' => 'nullable|string|max:50',
            'kode_pos_luar_negeri' => 'nullable|string|max:10',
        ]);

        $kk = KK::findOrFail($id);
        $kk->fill($validatedData);
        $kk->save();

        return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $kk = KK::findOrFail($id);
            $kk->delete();

            return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.datakk.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function fetchAllCitizens()
    {
        $citizens = $this->citizenService->getAllCitizens();

        if ($citizens) {
            return response()->json($citizens);
        } else {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    public function getFamilyMembers(Request $request)
    {
        $kk = $request->input('kk');
        $cacheKey = "family_members_{$kk}";

        // Load family members with appropriate citizen data including location information
        return Cache::remember($cacheKey, 300, function () use ($kk) {
            $familyMembers = $this->citizenService->getFamilyMembersByKK($kk);

            if ($familyMembers && isset($familyMembers['data']) && is_array($familyMembers['data'])) {
                // Sort family members to put Kepala Keluarga first
                $sortedMembers = collect($familyMembers['data'])->sortBy(function ($member) {
                    $order = [
                        'KEPALA KELUARGA' => 1,
                        'ISTRI' => 2,
                        'ANAK' => 3
                    ];
                    return $order[$member['family_status']] ?? 999;
                })->values()->all();

                return response()->json([
                    'status' => 'OK',
                    'count' => count($sortedMembers),
                    'data' => $sortedMembers
                ]);
            }

            return response()->json([
                'status' => 'ERROR',
                'count' => 0,
                'message' => 'Gagal mengambil data anggota keluarga'
            ]);
        });
    }

    public function getFamilyMembersByKK($kk_id)
    {
        try {
            $kk = KK::findOrFail($kk_id);

            // Cache family members for better performance
            $cacheKey = "kk_family_members_{$kk_id}";

            return Cache::remember($cacheKey, 300, function () use ($kk) {
                $familyMembers = $kk->familyMembers()->get();

                return response()->json([
                    'status' => 'OK',
                    'count' => $familyMembers->count(),
                    'data' => $familyMembers
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Gagal mengambil data anggota keluarga'
            ], 500);
        }
    }
}

