<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WilayahService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kependudukan.url');
        $this->apiKey = config('services.kependudukan.key');
    }

    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/provinces");

            if ($response->successful()) {
                $responseData = $response->json();

                // Check if response contains data field with provinces array
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    // Transform the province data from the data array
                    return collect($responseData['data'])->map(function ($province) {
                        return [
                            'id' => $province['id'],
                            'code' => $province['code'],
                            'name' => $province['name']
                        ];
                    })->all();
                }

                Log::error('Invalid province response format: data field missing or not an array');
                return [];
            }

            Log::error('Province API request failed: ' . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get districts/kabupaten for a province
     */
    public function getKabupaten($provinceCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/districts/{$provinceCode}");

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    return collect($responseData['data'])->map(function ($district) {
                        // If district name is empty, use the code as the name
                        $name = !empty($district['name']) ? $district['name'] : 'Kabupaten ' . $district['code'];

                        return [
                            'id' => $district['id'],
                            'code' => $district['code'],
                            'name' => $name
                        ];
                    })->all();
                }

                Log::error('Invalid district response format for province: ' . $provinceCode);
            } else {
                Log::error('Districts API request failed: ' . $response->status() . ' - ' . $response->body());
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching districts: ' . $e->getMessage());
            return [];
        }
    }

    public function getKecamatan($kotaCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/sub-districts/{$kotaCode}");

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    return collect($responseData['data'])->map(function ($subDistrict) {
                        $name = !empty($subDistrict['name']) ? $subDistrict['name'] : 'Kecamatan ' . $subDistrict['code'];

                        return [
                            'id' => $subDistrict['id'],
                            'code' => $subDistrict['code'],
                            'name' => $name
                        ];
                    })->all();
                }

                Log::error('Invalid sub-district response format for district: ' . $kotaCode);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching sub-districts: ' . $e->getMessage());
            return [];
        }
    }


    public function getDesa($kecamatanCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/villages/{$kecamatanCode}");

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    return collect($responseData['data'])->map(function ($village) {
                        $name = !empty($village['name']) ? $village['name'] : 'Desa ' . $village['code'];

                        return [
                            'id' => $village['id'],
                            'code' => $village['code'],
                            'name' => $name
                        ];
                    })->all();
                }

                Log::error('Invalid village response format for sub-district: ' . $kecamatanCode);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching villages: ' . $e->getMessage());
            return [];
        }
    }

    public function getKotaDetail($kotaCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/districts/{$kotaCode}");

            if ($response->successful()) {
                $responseData = $response->json();
                return isset($responseData['data']) ? $responseData['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching district details: ' . $e->getMessage());
            return null;
        }
    }



    public function getProvinceDetail($provinceCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/provinces/{$provinceCode}");

            if ($response->successful()) {
                $responseData = $response->json();
                return isset($responseData['data']) ? $responseData['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching province details: ' . $e->getMessage());
            return null;
        }
    }

    public function getKecamatanDetail($kecamatanCode)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/sub-districts/{$kecamatanCode}");

            if ($response->successful()) {
                $responseData = $response->json();
                return isset($responseData['data']) ? $responseData['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching sub-district details: ' . $e->getMessage());
            return null;
        }
    }
}
