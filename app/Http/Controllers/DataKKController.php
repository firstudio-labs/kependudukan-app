<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KK; // Pastikan ini diimpor
use App\Services\CitizenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataKKController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index(Request $request)
    {
        $kk = KK::all(); // Ambil semua data KK
        $search = $request->input('search');

        $kk = KK::when($search, function ($query, $search) {
            return $query->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('kk', 'like', "%{$search}%");
        })->paginate(10);

        return view('superadmin.datakk.index', compact('kk', 'search'));
    }

    public function create()
    {
        // Get provinces data
        $provinces = $this->getProvinces();

        return view('superadmin.datakk.create', compact('provinces'));
    }

    private function getProvinces()
    {
        try {
            $response = Http::get('https://api.desaverse.id/wilayah/provinsi');

            if ($response->successful()) {
                Log::info('Provinces data fetched successfully');
                return $response->json();
            } else {
                Log::error('Province API request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            return [];
        }
    }

    // Menyimpan data KK ke database
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kk' => 'required|unique:kk,kk',
                'full_name' => 'required',
                'address' => 'required',
                'postal_code' => 'required',
                'rt' => 'required',
                'rw' => 'required',
                'jml_anggota_kk' => 'required|integer',
                'telepon' => 'nullable',
                'email' => 'nullable|email',
                'province_id' => 'required',
                'district_id' => 'required',
                'sub_district_id' => 'required',
                'village_id' => 'required',
                'dusun' => 'nullable',
                'alamat_luar_negeri' => 'nullable',
                'kota' => 'nullable',
                'negara_bagian' => 'nullable',
                'negara' => 'nullable',
                'kode_pos_luar_negeri' => 'nullable',
            ]);

            // Get family members data from API
            $familyMembers = $this->citizenService->getFamilyMembersByKK($request->kk);

            if ($familyMembers && isset($familyMembers['data']) && is_array($familyMembers['data'])) {
                // Add family members to validated data as JSON
                $validatedData['family_members'] = $familyMembers['data'];
            }

            // Create KK record with family members included
            $kk = KK::create($validatedData);

            return redirect()->route('superadmin.datakk.index')->with('success', 'Data KK berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kk = KK::findOrFail($id);
        $provinces = $this->getProvinces(); // Get provinces data
        return view('superadmin.datakk.update', compact('kk', 'provinces'));
    }

    // Method untuk memproses update data
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

        // Ambil data berdasarkan ID
        $kk = KK::findOrFail($id);

        // Update data kecuali kk dan full_name
        $kk->fill($validatedData);
        $kk->save();

        // Redirect dengan pesan sukses
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
        $familyMembers = $this->citizenService->getFamilyMembersByKK($kk);

        if ($familyMembers && isset($familyMembers['data']) && is_array($familyMembers['data'])) {
            // Sort family members to put Kepala Keluarga first
            $sortedMembers = collect($familyMembers['data'])->sortBy(function ($member) {
                // Custom sorting order for family status
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
    }
}

