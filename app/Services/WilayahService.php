<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    // public function getIdByCode($code, $type)
    // {
    //     try {
    //         $endpoint = match($type) {
    //             'province' => "provinsi/{$code}/kota",
    //             'district' => "kota/{$code}/kecamatan",
    //             'subdistrict' => "kecamatan/{$code}/kelurahan",
    //             'village' => "kelurahan/{$code}",
    //             default => throw new \Exception("Invalid type")
    //         };

    //         $response = Http::get("https://api.desaverse.id/wilayah/{$endpoint}");
    //         if ($response->successful()) {
    //             $data = $response->json();
    //             return $data['id'];
    //         }
    //         return null;
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }
}
