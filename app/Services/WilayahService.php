<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahService
{
    public function getProvinces()
    {
        try {
            $provinces = Http::get('https://api.desaverse.id/wilayah/provinsi')->json();

            if (!empty($provinces)) {
                // Get kabupaten count for each province
                foreach ($provinces as &$province) {
                    $kabupaten = Http::get("https://api.desaverse.id/wilayah/provinsi/{$province['code']}/kota")->json();
                    $province['kabupaten_count'] = count($kabupaten ?? []);
                }

                return $provinces;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            return [];
        }
    }

    public function getKabupaten($provinceCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/provinsi/{$provinceCode}/kota");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Error fetching kabupaten: ' . $e->getMessage());
            return [];
        }
    }

    public function getProvinceDetail($provinceCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/provinsi/{$provinceCode}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching province detail: ' . $e->getMessage());
            return null;
        }
    }

    public function getKecamatan($kotaCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kota/{$kotaCode}/kecamatan");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Error fetching kecamatan: ' . $e->getMessage());
            return [];
        }
    }

    public function getKotaDetail($kotaCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kota/{$kotaCode}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching kota detail: ' . $e->getMessage());
            return null;
        }
    }

    public function getDesa($kecamatanCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kecamatan/{$kecamatanCode}/kelurahan");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Error fetching desa: ' . $e->getMessage());
            return [];
        }
    }

    public function getKecamatanDetail($kecamatanCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/kecamatan/{$kecamatanCode}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching kecamatan detail: ' . $e->getMessage());
            return null;
        }
    }
}
