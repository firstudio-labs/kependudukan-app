<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WilayahService
{
    public function getProvinces()
    {
        try {
            $response = Http::get('https://api.desaverse.id/wilayah/provinsi');
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getKabupaten($provinceCode)
    {
        try {
            $response = Http::get("https://api.desaverse.id/wilayah/provinsi/{$provinceCode}/kota");
            return $response->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getProvinceDetail($provinceCode)
    {
        try {
            return Http::get("https://api.desaverse.id/wilayah/provinsi/{$provinceCode}")->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getKecamatan($kotaCode)
    {
        try {
            return Http::get("https://api.desaverse.id/wilayah/kota/{$kotaCode}/kecamatan")->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getKotaDetail($kotaCode)
    {
        try {
            return Http::get("https://api.desaverse.id/wilayah/kota/{$kotaCode}")->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDesa($kecamatanCode)
    {
        try {
            return Http::get("https://api.desaverse.id/wilayah/kecamatan/{$kecamatanCode}/kelurahan")->json() ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getKecamatanDetail($kecamatanCode)
    {
        try {
            return Http::get("https://api.desaverse.id/wilayah/kecamatan/{$kecamatanCode}")->json();
        } catch (\Exception $e) {
            return null;
        }
    }


}
