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

    /**
     * Get all districts/kabupaten with pagination (no province filter)
     */
    public function getKabupatenByPage($page = 1)
    {
        try {
            // Log the request for debugging
            Log::info("Requesting districts with page: {$page}", [
                'url' => "{$this->baseUrl}/api/districts",
                'params' => ['page' => $page]
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/districts", [
                'page' => $page
            ]);

            // Log raw response for debugging
            Log::info("Districts API response code: {$response->status()}", [
                'body_preview' => substr($response->body(), 0, 500) // Log first 500 chars
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Log the structure of the response
                Log::info("Districts API response structure", [
                    'has_data_key' => isset($responseData['data']),
                    'data_structure' => json_encode(array_keys($responseData['data'] ?? [])),
                    'status' => $responseData['status'] ?? 'no status'
                ]);

                // Check if the structure matches the Postman response
                if (isset($responseData['data']['districts']) && is_array($responseData['data']['districts'])) {
                    $districts = collect($responseData['data']['districts'])->map(function ($district) {
                        return [
                            'id' => $district['id'] ?? '',
                            'code' => $district['code'] ?? '',
                            'name' => $district['name'] ?? 'Unknown District',
                            'province_code' => $district['province_code'] ?? ''
                        ];
                    })->all();

                    // Get pagination from the correct location in the response
                    $pagination = $responseData['data']['pagination'] ?? null;

                    // Log the processed data
                    Log::info("Processed districts data", [
                        'count' => count($districts),
                        'first_item' => !empty($districts) ? $districts[0] : null,
                        'pagination' => $pagination
                    ]);

                    return [
                        'data' => $districts,
                        'meta' => [
                            'current_page' => $pagination['current_page'] ?? 1,
                            'per_page' => $pagination['items_per_page'] ?? 10,
                            'total' => $pagination['total_items'] ?? count($districts),
                            'total_pages' => $pagination['total_page'] ?? 1
                        ]
                    ];
                }

                Log::error('Invalid district response format - missing districts array in data object');
            } else {
                Log::error("Districts API request failed: {$response->status()}", [
                    'error' => $response->body()
                ]);
            }

            return ['data' => [], 'meta' => null];
        } catch (\Exception $e) {
            Log::error("Error fetching districts by page: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return ['data' => [], 'meta' => null];
        }
    }

    /**
     * Get all sub-districts/kecamatan with pagination
     */
    public function getKecamatanByPage($page = 1)
    {
        try {
            // Log the request for debugging
            Log::info("Requesting sub-districts with page: {$page}", [
                'url' => "{$this->baseUrl}/api/sub-districts",
                'params' => ['page' => $page]
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/sub-districts", [
                'page' => $page
            ]);

            // Log raw response for debugging
            Log::info("Sub-districts API raw response", [
                'status_code' => $response->status(),
                'raw_body' => substr($response->body(), 0, 1000) // Log the first 1000 chars
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Log the structure of the response
                Log::info("Sub-districts API response structure", [
                    'has_data_key' => isset($responseData['data']),
                    'data_keys' => isset($responseData['data']) ? array_keys($responseData['data']) : 'no data keys',
                    'status' => $responseData['status'] ?? 'no status'
                ]);

                // IMPORTANT: The API response uses "subs_districts" (with underscore) as the key, not "sub_districts"
                if (isset($responseData['data']['subs_districts']) && is_array($responseData['data']['subs_districts'])) {
                    $subDistricts = collect($responseData['data']['subs_districts'])->map(function ($subDistrict) {
                        return [
                            'id' => $subDistrict['id'] ?? '',
                            'code' => $subDistrict['code'] ?? '',
                            'name' => $subDistrict['name'] ?? 'Unknown Sub District',
                            'district_code' => $subDistrict['district_code'] ?? ''
                        ];
                    })->all();

                    // Get pagination from the correct location in the response
                    $pagination = $responseData['data']['pagination'] ?? null;

                    // Log the processed data
                    Log::info("Processed sub-districts data", [
                        'count' => count($subDistricts),
                        'first_item' => !empty($subDistricts) ? $subDistricts[0] : null,
                        'pagination' => $pagination
                    ]);

                    return [
                        'data' => $subDistricts,
                        'meta' => [
                            'current_page' => $pagination['current_page'] ?? 1,
                            'per_page' => $pagination['items_per_page'] ?? 10,
                            'total' => $pagination['total_items'] ?? count($subDistricts),
                            'total_pages' => $pagination['total_page'] ?? 1
                        ]
                    ];
                }

                Log::error('Invalid sub-district response format - missing subs_districts array in data object', [
                    'available_keys' => isset($responseData['data']) ? array_keys($responseData['data']) : 'no data'
                ]);
            } else {
                Log::error("Sub-districts API request failed: {$response->status()}", [
                    'error' => $response->body()
                ]);
            }

            return ['data' => [], 'meta' => null];
        } catch (\Exception $e) {
            Log::error("Error fetching sub-districts by page: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return ['data' => [], 'meta' => null];
        }
    }

    /**
     * Get all villages/desa with pagination
     */
    public function getDesaByPage($page = 1)
    {
        try {
            // Log the request for debugging
            Log::info("Requesting villages with page: {$page}", [
                'url' => "{$this->baseUrl}/api/villages",
                'params' => ['page' => $page]
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/villages", [
                'page' => $page
            ]);

            // Log raw response for debugging
            Log::info("Villages API response code: {$response->status()}", [
                'body_preview' => substr($response->body(), 0, 500) // Log first 500 chars
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Log the structure of the response
                Log::info("Villages API response structure", [
                    'has_data_key' => isset($responseData['data']),
                    'data_structure' => json_encode(array_keys($responseData['data'] ?? [])),
                    'status' => $responseData['status'] ?? 'no status'
                ]);

                // Check if the structure matches the expected response
                if (isset($responseData['data']['villages']) && is_array($responseData['data']['villages'])) {
                    $villages = collect($responseData['data']['villages'])->map(function ($village) {
                        return [
                            'id' => $village['id'] ?? '',
                            'code' => $village['code'] ?? '',
                            'name' => $village['name'] ?? 'Unknown Village',
                            'sub_district_code' => $village['sub_district_code'] ?? ''
                        ];
                    })->all();

                    // Get pagination from the correct location in the response
                    $pagination = $responseData['data']['pagination'] ?? null;

                    // Log the processed data
                    Log::info("Processed villages data", [
                        'count' => count($villages),
                        'first_item' => !empty($villages) ? $villages[0] : null,
                        'pagination' => $pagination
                    ]);

                    return [
                        'data' => $villages,
                        'meta' => [
                            'current_page' => $pagination['current_page'] ?? 1,
                            'per_page' => $pagination['items_per_page'] ?? 10,
                            'total' => $pagination['total_items'] ?? count($villages),
                            'total_pages' => $pagination['total_page'] ?? 1
                        ]
                    ];
                }

                Log::error('Invalid village response format - missing villages array in data object');
            } else {
                Log::error("Villages API request failed: {$response->status()}", [
                    'error' => $response->body()
                ]);
            }

            return ['data' => [], 'meta' => null];
        } catch (\Exception $e) {
            Log::error("Error fetching villages by page: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return ['data' => [], 'meta' => null];
        }
    }

    /**
     * Get village data by ID
     *
     * @param string $id
     * @return array|null
     */
    public function getVillageById($id)
    {
        // Bersihkan cache untuk debugging
        $cacheKey = "village_{$id}";
        Cache::forget($cacheKey);

        // Log info untuk debugging
        Log::info("Mencoba mendapatkan data desa dengan ID: {$id}");

        try {
            // Coba endpoint pertama
            $endpoint = "/api/village/{$id}";
            Log::info("Mencoba endpoint: {$this->baseUrl}{$endpoint}");

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}{$endpoint}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Respons dari endpoint pertama:", ['data' => $data]);

                if (isset($data['name']) && !empty($data['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                } elseif (isset($data['data']['name']) && !empty($data['data']['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                }
            }

            // Coba endpoint kedua
            $endpoint = "/api/villages/{$id}";
            Log::info("Mencoba endpoint: {$this->baseUrl}{$endpoint}");

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}{$endpoint}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Respons dari endpoint kedua:", ['data' => $data]);

                if (isset($data['name']) && !empty($data['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                } elseif (isset($data['data']['name']) && !empty($data['data']['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                }
            }

            // Coba endpoint ketiga
            $endpoint = "/api/village/detail/{$id}";
            Log::info("Mencoba endpoint: {$this->baseUrl}{$endpoint}");

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}{$endpoint}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Respons dari endpoint ketiga:", ['data' => $data]);

                if (isset($data['name']) && !empty($data['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                } elseif (isset($data['data']['name']) && !empty($data['data']['name'])) {
                    Cache::put($cacheKey, $data, now()->addDay());
                    return $data;
                }
            }

            // Jika semua endpoint gagal, coba cari dari daftar desa
            Log::info("Mencoba mencari desa dari daftar desa");

            // Ambil semua daftar kecamatan (menggunakan metode yang sudah ada)
            $kecamatanPages = $this->getKecamatanByPage();

            foreach ($kecamatanPages['data'] as $kecamatan) {
                $kecamatanCode = $kecamatan['code'];
                $desaList = $this->getDesa($kecamatanCode);

                foreach ($desaList as $desa) {
                    if ($desa['id'] == $id) {
                        $data = [
                            'id' => $desa['id'],
                            'code' => $desa['code'],
                            'name' => $desa['name']
                        ];

                        Log::info("Desa ditemukan dari daftar:", ['data' => $data]);
                        Cache::put($cacheKey, $data, now()->addDay());
                        return $data;
                    }
                }
            }

            Log::error("Gagal menemukan data desa dengan ID: {$id}");
            return null;
        } catch (\Exception $e) {
            Log::error("Error mengambil data desa: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
