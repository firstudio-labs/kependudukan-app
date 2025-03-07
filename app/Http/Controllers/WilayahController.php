<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Log;

class WilayahController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function showProvinsi()
    {
        try {
            $provinces = $this->wilayahService->getProvinces();
            Log::info('Provinces data:', ['provinces' => $provinces]);
            return view('superadmin.datamaster.wilayah.provinsi.index', compact('provinces'));
        } catch (\Exception $e) {
            Log::error('Error in showProvinsi: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.provinsi.index')->with('error', 'Gagal memuat data provinsi');
        }
    }

    public function showKabupaten($provinceCode)
    {
        try {
            $kabupaten = $this->wilayahService->getKabupaten($provinceCode);
            $province = $this->wilayahService->getProvinceDetail($provinceCode);
            return view('superadmin.datamaster.wilayah.kabupaten.index', compact('kabupaten', 'province'));
        } catch (\Exception $e) {
            Log::error('Error in showKabupaten: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.kabupaten.index')->with('error', 'Gagal memuat data kabupaten');
        }
    }

    public function showKecamatan($kotaCode)
    {
        try {
            $kecamatan = $this->wilayahService->getKecamatan($kotaCode);
            $kota = $this->wilayahService->getKotaDetail($kotaCode);
            return view('superadmin.datamaster.wilayah.kecamatan.index', compact('kecamatan', 'kota'));
        } catch (\Exception $e) {
            Log::error('Error in showKecamatan: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.kecamatan.index')->with('error', 'Gagal memuat data kecamatan');
        }
    }

    public function showDesa($kecamatanCode)
    {
        try {
            $desa = $this->wilayahService->getDesa($kecamatanCode);
            $kecamatan = $this->wilayahService->getKecamatanDetail($kecamatanCode);
            return view('superadmin.datamaster.wilayah.desa.index', compact('desa', 'kecamatan'));
        } catch (\Exception $e) {
            Log::error('Error in showDesa: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.desa.index')->with('error', 'Gagal memuat data desa');
        }
    }

    public function getKotaByProvinsi($code)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/provinsi/{$code}/kota");
            if (!$response->successful()) {
                Log::error('API Error: ' . $response->body());
                return response()->json(['error' => 'API request failed'], 500);
            }
            $data = $response->json();
            Log::info('Kota data:', ['data' => $data]); // Debug log
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getKotaByProvinsi: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getKecamatanByKota($code)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kota/{$code}/kecamatan");
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDesaByKecamatan($code)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kecamatan/{$code}/kelurahan");
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
