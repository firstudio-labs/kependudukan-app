<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CitizenService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kependudukan.url');
        $this->apiKey = config('services.kependudukan.key');
    }

    public function getCitizenByKK($kk)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens/{$kk}");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching citizen data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all citizens who are heads of families
     *
     * @return array|null
     */
    public function getHeadsOfFamily()
    {
        $allHeadsOfFamily = [];
        $page = 1;
        $hasMoreData = true;

        while ($hasMoreData) {
            try {
                $response = Http::withHeaders([
                    'X-API-Key' => $this->apiKey,
                ])->get("{$this->baseUrl}/api/citizens", [
                    'page' => $page,
                    'limit' => 100
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']['citizens']) && is_array($data['data']['citizens'])) {
                        $headsOfFamily = array_filter($data['data']['citizens'], function ($citizen) {
                            return isset($citizen['family_status']) && $citizen['family_status'] === 'KEPALA KELUARGA';
                        });

                        $allHeadsOfFamily = array_merge($allHeadsOfFamily, array_values($headsOfFamily));

                        $hasMoreData = $data['data']['pagination']['has_more_pages'] ?? false;
                        $page++;
                    } else {
                        Log::error('Invalid API response structure');
                        $hasMoreData = false;
                    }
                } else {
                    Log::error('API request failed: ' . $response->status());
                    $hasMoreData = false;
                }
            } catch (\Exception $e) {
                Log::error('Error fetching citizens data: ' . $e->getMessage());
                $hasMoreData = false;
            }
        }

        return [
            'status' => 'OK',
            'count' => count($allHeadsOfFamily),
            'data' => $allHeadsOfFamily
        ];
    }

    public function getAllCitizens($page = 1, $search = null)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens", [
                'page' => $page,
                'search' => $search,
                'limit' => 10
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching citizens data: ' . $e->getMessage());
            return null;
        }
    }

    // Add a new method with higher limit to fetch more citizens at once
    /**
     * Get all citizens with a high limit and detailed logging
     */
    public function getAllCitizensWithHighLimit($search = null)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                // Check different possible structures for the response
                if (isset($data['citizens']) && is_array($data['citizens'])) {
                    return [
                        'status' => 'OK',
                        'data' => ['citizens' => $data['citizens']]
                    ];
                } elseif (isset($data['data']) && is_array($data['data'])) {
                    return $data;
                } else {
                    // Return a fallback structure
                    return [
                        'status' => 'OK',
                        'data' => ['citizens' => $data]
                    ];
                }
            } else {
                Log::error('API request failed', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return [
                    'status' => 'ERROR',
                    'message' => 'Failed to fetch citizens',
                    'data' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching citizens data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'ERROR',
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function updateCitizen($nik, $data)
    {
        try {
            $nik = (int) $nik;

            // Convert KK to integer if exists in data
            if (isset($data['kk'])) {
                $data['kk'] = (int) $data['kk'];
            }

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->put("{$this->baseUrl}/api/citizens/{$nik}", $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
                return ['status' => 'ERROR', 'message' => 'API request failed'];
            }
        } catch (\Exception $e) {
            Log::error('Error updating citizen: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => 'Error updating citizen: ' . $e->getMessage()];
        }
    }

    public function deleteCitizen($nik)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->delete("{$this->baseUrl}/api/citizens/{$nik}");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
                return ['status' => 'ERROR', 'message' => 'API request failed'];
            }
        } catch (\Exception $e) {
            Log::error('Error deleting citizen: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => 'Error deleting citizen'];
        }
    }

    public function getFamilyMembersByKK($kk)
    {
        try {
            $cacheKey = "family_members_kk_{$kk}";

            // Try to get from cache first
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens-family/{$kk}");

            if ($response->successful()) {
                $result = $response->json();

                // Cache the result for 5 minutes
                Cache::put($cacheKey, $result, now()->addMinutes(5));

                return $result;
            } else {
                Log::error('API request failed when fetching family members: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching family members data: ' . $e->getMessage());
            return null;
        }
    }

    public function getCitizenByNIK($nik)
    {
        try {
            // Convert to integer to ensure consistent format
            $nik = (int) $nik;

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens/{$nik}");

            if ($response->successful()) {
                $result = $response->json();
                return $result;
            } else {
                Log::error('API request failed for NIK: ' . $nik . ', Status: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching citizen data for NIK ' . $nik . ': ' . $e->getMessage());
            return null;
        }
    }

    public function createCitizen($data)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post("{$this->baseUrl}/api/citizens", $data);

            if ($response->successful()) {
                return [
                    'status' => 'CREATED',
                    'message' => 'Data berhasil disimpan'
                ];
            }

            $errorMessage = $response->json()['message'] ?? 'Gagal menyimpan data';
            Log::error('Failed to create citizen: ' . $errorMessage);
            return [
                'status' => 'ERROR',
                'message' => $errorMessage
            ];
        } catch (\Exception $e) {
            Log::error('Exception creating citizen: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search citizens by name
     *
     * @param string $name Name to search for
     * @return array|null
     */
    public function searchCitizens($name)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens-search/full_name", [
                'search' => $name
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API search request failed: ' . $response->status());
                return [
                    'status' => 'ERROR',
                    'message' => 'Failed to search citizens',
                    'data' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error searching citizens data: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get citizens by village ID
     *
     * @param int $villageId
     * @return array
     */
    public function getCitizensByVillageId($villageId, $page = 1, $limit = 10, $search = null)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                $filtered = collect($data['data'])
                    ->where('village_id', $villageId);

                // Tambahkan filter pencarian jika parameter search diisi
                if ($search) {
                    $searchLower = strtolower($search);
                    $filtered = $filtered->filter(function ($citizen) use ($searchLower) {
                        return str_contains(strtolower($citizen['full_name']), $searchLower) ||
                               str_contains((string) $citizen['nik'], $searchLower);
                    });
                }

                $filtered = $filtered->values(); // reset index setelah filter

                $paginated = $filtered->forPage($page, $limit);

                return [
                    'status' => 'OK',
                    'message' => 'Filtered by village_id' . ($search ? ' and search' : ''),
                    'data' => [
                        'citizens' => $paginated->values(),
                        'total_filtered' => $filtered->count(),
                        'pagination' => [
                            'current_page' => $page,
                            'items_per_page' => $limit,
                            'total_items' => $filtered->count(),
                            'total_page' => ceil($filtered->count() / $limit),
                            'next_page' => ($page * $limit) < $filtered->count() ? $page + 1 : null,
                            'prev_page' => $page > 1 ? $page - 1 : null,
                        ],
                    ]
                ];

            } else {
                Log::error('API request failed when fetching citizens by village ID', [
                    'status_code' => $response->status(),
                    'village_id' => $villageId
                ]);
                return $this->filterCitizensByVillageId($villageId);
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching citizens by village ID', [
                'error' => $e->getMessage(),
                'village_id' => $villageId
            ]);
            return $this->filterCitizensByVillageId($villageId);
        }
    }

    /**
     * Filter citizens by village ID manually (fallback method)
     *
     * @param int $villageId
     * @return array
     */
    private function filterCitizensByVillageId($villageId, $page = 1, $limit = 10)
    {
        // Dapatkan semua warga terlebih dahulu
        $allCitizensData = $this->getAllCitizensWithHighLimit();

        // Ekstrak array warga dari respons API
        $allCitizens = [];
        if (isset($allCitizensData['data']['citizens']) && is_array($allCitizensData['data']['citizens'])) {
            $allCitizens = $allCitizensData['data']['citizens'];
        } elseif (isset($allCitizensData['citizens']) && is_array($allCitizensData['citizens'])) {
            $allCitizens = $allCitizensData['citizens'];
        } elseif (isset($allCitizensData['data']) && is_array($allCitizensData['data'])) {
            $allCitizens = $allCitizensData['data'];
        }

        // Filter warga berdasarkan village_id atau villages_id
        $filteredCitizens = array_filter($allCitizens, function($citizen) use ($villageId) {
            // Cek kedua kolom yang mungkin ada
            return (isset($citizen['villages_id']) && $citizen['villages_id'] == $villageId) ||
                   (isset($citizen['village_id']) && $citizen['village_id'] == $villageId);
        });

        $totalItems = count($filteredCitizens);
        $offset = ($page - 1) * $limit;
        $pagedCitizens = array_slice($filteredCitizens, $offset, $limit);

        // Kembalikan format yang sama dengan API
        return [
            'status' => 'OK',
            'data' => [
                'citizens' => array_values($pagedCitizens),
                'pagination' => [
                    'current_page' => $page,
                    'items_per_page' => $limit,
                    'total_items' => $totalItems,
                    'total_page' => ceil($totalItems / $limit),
                    'has_more_pages' => ($page * $limit) < $totalItems
                ]
            ]
        ];
    }

    /**
     * Get citizens by village name
     *
     * @param string $villageName
     * @return array
     */
    public function getCitizensByVillageName($villageName)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens", [
                'village_name' => $villageName,
                'limit' => 1000
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            } else {
                Log::error('API request failed when fetching citizens by village name', [
                    'status_code' => $response->status(),
                    'village_name' => $villageName
                ]);

                // Jika API tidak mendukung filter by village_name, gunakan metode alternatif
                return $this->filterCitizensByVillageName($villageName);
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching citizens by village name', [
                'error' => $e->getMessage(),
                'village_name' => $villageName
            ]);

            // Gunakan metode alternatif jika terjadi error
            return $this->filterCitizensByVillageName($villageName);
        }
    }

    /**
     * Filter citizens by village name manually (fallback method)
     *
     * @param string $villageName
     * @return array
     */
    private function filterCitizensByVillageName($villageName)
    {
        // Dapatkan semua warga terlebih dahulu
        $allCitizensData = $this->getAllCitizensWithHighLimit();

        // Ekstrak array warga dari respons API
        $allCitizens = [];
        if (isset($allCitizensData['data']['citizens']) && is_array($allCitizensData['data']['citizens'])) {
            $allCitizens = $allCitizensData['data']['citizens'];
        } elseif (isset($allCitizensData['citizens']) && is_array($allCitizensData['citizens'])) {
            $allCitizens = $allCitizensData['citizens'];
        } elseif (isset($allCitizensData['data']) && is_array($allCitizensData['data'])) {
            $allCitizens = $allCitizensData['data'];
        }

        // Filter warga berdasarkan village_name
        $filteredCitizens = array_filter($allCitizens, function($citizen) use ($villageName) {
            // Pencocokan nama desa
            if (isset($citizen['village_name'])) {
                return strtolower($citizen['village_name']) == strtolower($villageName);
            }

            // Jika tidak ada village_name, coba cek villages_id dan cocokkan dengan data desa
            if (isset($citizen['villages_id'])) {
                try {
                    $villageData = app(\App\Services\WilayahService::class)->getVillageById($citizen['villages_id']);
                    $citizenVillageName = $villageData['name'] ?? $villageData['data']['name'] ?? '';
                    return strtolower($citizenVillageName) == strtolower($villageName);
                } catch (\Exception $e) {
                    Log::error('Error getting village name in filter: ' . $e->getMessage());
                    return false;
                }
            }

            return false;
        });

        // Kembalikan format yang sama dengan API
        return [
            'status' => 'OK',
            'data' => ['citizens' => array_values($filteredCitizens)]
        ];
    }
}

