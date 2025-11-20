<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Penduduk;

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

            // For coordinate-only or coordinate+address updates, we need to get existing data first
            if ((count($data) === 1 && isset($data['coordinate'])) ||
                (count($data) === 2 && isset($data['coordinate']) && isset($data['address']))
            ) {
                Log::info('Coordinate/address update - getting existing data first', [
                    'nik' => $nik,
                    'coordinate' => $data['coordinate'],
                    'address' => $data['address'] ?? 'not_provided'
                ]);

                // Get existing citizen data first
                $existingData = $this->getCitizenByNIK($nik);
                if (!$existingData || !isset($existingData['data'])) {
                    Log::error('Cannot update coordinate - citizen data not found', [
                        'nik' => $nik
                    ]);
                    return [
                        'status' => 'ERROR',
                        'message' => 'Citizen data not found'
                    ];
                }

                // Merge coordinate and address with existing data
                $updateData = $existingData['data'];
                $updateData['coordinate'] = $data['coordinate'];
                if (isset($data['address'])) {
                    $updateData['address'] = $data['address'];
                }

                // Filter out problematic fields that might cause JSON parsing issues
                $excludeFields = ['id', 'status', 'rf_id_tag', 'telephone', 'email', 'hamlet', 'foreign_address', 'city', 'state', 'country', 'foreign_postal_code', 'birth_certificate_no', 'marital_certificate_no', 'marriage_date', 'divorce_certificate_no', 'divorce_certificate_date', 'nik_mother', 'mother', 'nik_father', 'father', 'disabilities'];
                foreach ($excludeFields as $field) {
                    unset($updateData[$field]);
                }

                // Normalize data to numeric format for API
                $updateData = $this->normalizeDataForApi($updateData);

                Log::info('Coordinate update with existing data', [
                    'nik' => $nik,
                    'coordinate' => $data['coordinate'],
                    'has_existing_data' => !empty($updateData),
                    'update_data_keys' => array_keys($updateData),
                    'update_data_sample' => array_slice($updateData, 0, 5, true),
                    'data_preservation_check' => [
                        'original_gender' => $existingData['data']['gender'] ?? 'not_found',
                        'normalized_gender' => $updateData['gender'] ?? 'not_found',
                        'original_religion' => $existingData['data']['religion'] ?? 'not_found',
                        'normalized_religion' => $updateData['religion'] ?? 'not_found',
                        'original_marital_status' => $existingData['data']['marital_status'] ?? 'not_found',
                        'normalized_marital_status' => $updateData['marital_status'] ?? 'not_found',
                        'coordinate_updated' => $updateData['coordinate'] ?? 'not_found'
                    ],
                    'required_fields_check' => [
                        'citizen_status' => $updateData['citizen_status'] ?? 'missing',
                        'birth_certificate' => $updateData['birth_certificate'] ?? 'missing',
                        'blood_type' => $updateData['blood_type'] ?? 'missing',
                        'religion' => $updateData['religion'] ?? 'missing',
                        'marital_status' => $updateData['marital_status'] ?? 'missing',
                        'marital_certificate' => $updateData['marital_certificate'] ?? 'missing',
                        'divorce_certificate' => $updateData['divorce_certificate'] ?? 'missing',
                        'mental_disorders' => $updateData['mental_disorders'] ?? 'missing',
                        'education_status' => $updateData['education_status'] ?? 'missing',
                        'family_status' => $updateData['family_status'] ?? 'missing',
                        'job_type_id' => $updateData['job_type_id'] ?? 'missing'
                    ]
                ]);

                $response = Http::withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->put("{$this->baseUrl}/api/citizens/{$nik}", $updateData);

                if ($response->successful()) {
                    $result = $response->json();
                    Log::info('Coordinate updated successfully', [
                        'nik' => $nik,
                        'result' => $result
                    ]);
                    return $result;
                } else {
                    $responseBody = $response->body();
                    Log::error('Coordinate update failed', [
                        'nik' => $nik,
                        'status_code' => $response->status(),
                        'response_body' => $responseBody,
                        'response_headers' => $response->headers(),
                        'request_data_size' => strlen(json_encode($updateData))
                    ]);

                    // Try to parse error message
                    $errorMessage = 'Unknown error';
                    try {
                        $errorJson = json_decode($responseBody, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($errorJson['message'])) {
                            $errorMessage = $errorJson['message'];
                        } else {
                            $errorMessage = $responseBody;
                        }
                    } catch (\Exception $e) {
                        $errorMessage = $responseBody;
                    }

                    return [
                        'status' => 'ERROR',
                        'message' => 'Failed to update coordinate: ' . $errorMessage
                    ];
                }
            }

            // Filter out empty values and ensure data is clean
            $cleanData = array_filter($data, function ($value) {
                return $value !== null && $value !== '' && $value !== 'null';
            });

            // Only add required fields if they are completely missing
            // This preserves existing citizen data
            $requiredFields = [
                'citizen_status' => 1,
                'birth_certificate' => 2,
                'blood_type' => 13,
                'religion' => 1,
                'marital_status' => 1,
                'marital_certificate' => 2,
                'divorce_certificate' => 2,
                'mental_disorders' => 2,
                'education_status' => 1,
                'family_status' => 2,
                'job_type_id' => 1
            ];

            // Add required fields only if they don't exist at all
            foreach ($requiredFields as $field => $defaultValue) {
                if (!isset($cleanData[$field]) || $cleanData[$field] === null) {
                    $cleanData[$field] = $defaultValue;
                    Log::info("Added missing required field for citizen update: {$field} = {$defaultValue}");
                }
            }

            // Normalize mapped fields to API numeric values (e.g., blood_type 'B' -> 2)
            $cleanData = $this->normalizeDataForApi($cleanData);

            // Log the data being sent
            Log::info('Updating citizen via API', [
                'nik' => $nik,
                'original_data' => $data,
                'clean_data' => $cleanData,
                'api_url' => "{$this->baseUrl}/api/citizens/{$nik}"
            ]);

            // Convert KK to integer if exists in data
            if (isset($cleanData['kk'])) {
                $cleanData['kk'] = (int) $cleanData['kk'];
            }

            // Ensure numeric fields are properly typed
            $numericFields = ['age', 'province_id', 'district_id', 'sub_district_id', 'village_id', 'citizen_status', 'birth_certificate', 'blood_type', 'religion', 'marital_status', 'marital_certificate', 'divorce_certificate', 'mental_disorders', 'education_status', 'family_status', 'job_type_id'];
            foreach ($numericFields as $field) {
                if (isset($cleanData[$field]) && is_numeric($cleanData[$field])) {
                    $cleanData[$field] = (int) $cleanData[$field];
                }
            }

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->put("{$this->baseUrl}/api/citizens/{$nik}", $cleanData);

            if ($response->successful()) {
                // Clear related caches after successful update
                $this->clearCitizenCaches($nik, $cleanData);

                $result = $response->json();
                Log::info('Citizen updated successfully', [
                    'nik' => $nik,
                    'result' => $result
                ]);

                return $result;
            } else {
                $errorBody = $response->body();
                $statusCode = $response->status();

                Log::error('API request failed when updating citizen', [
                    'nik' => $nik,
                    'status_code' => $statusCode,
                    'response_body' => $errorBody,
                    'request_data' => $cleanData,
                    'headers' => $response->headers()
                ]);

                // Try to parse error message from response
                $errorMessage = 'Unknown error';
                try {
                    $errorJson = json_decode($errorBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($errorJson['message'])) {
                        $errorMessage = $errorJson['message'];
                    } else {
                        $errorMessage = $errorBody;
                    }
                } catch (\Exception $e) {
                    $errorMessage = $errorBody;
                }

                return [
                    'status' => 'ERROR',
                    'message' => "API request failed with status {$statusCode}: {$errorMessage}"
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error updating citizen', [
                'nik' => $nik,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'ERROR',
                'message' => 'Error updating citizen: ' . $e->getMessage()
            ];
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

    public function getCitizenByNIK($nik, bool $useCache = true)
    {
        try {
            // Convert to integer to ensure consistent format
            $nik = (int) $nik;

            $cacheKey = "citizen_by_nik_{$nik}";
            $cache = $this->cacheStore();
            $cacheTTL = now()->addHours(1); // Cache selama 1 jam

            // Cek cache terlebih dahulu jika useCache = true
            if ($useCache && $cache->has($cacheKey)) {
                return $cache->get($cacheKey);
            }

            $response = Http::timeout(15)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens/{$nik}");

            if ($response->successful()) {
                $result = $response->json();
                
                // Simpan ke cache jika berhasil
                if ($useCache) {
                    $cache->put($cacheKey, $result, $cacheTTL);
                }
                
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
     * Get all citizens with local search filtering (for superadmin)
     */
    public function getAllCitizensWithSearch($page = 1, $limit = 10, $search = null)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                // Extract citizens array from response
                $allCitizens = [];
                if (isset($data['data']) && is_array($data['data'])) {
                    $allCitizens = $data['data'];
                } elseif (isset($data['citizens']) && is_array($data['citizens'])) {
                    $allCitizens = $data['citizens'];
                } elseif (is_array($data)) {
                    $allCitizens = $data;
                }

                // Apply search filter if provided
                if ($search) {
                    $searchLower = strtolower($search);
                    $allCitizens = collect($allCitizens)->filter(function ($citizen) use ($searchLower) {
                        return str_contains(strtolower($citizen['full_name'] ?? ''), $searchLower) ||
                            str_contains((string) ($citizen['nik'] ?? ''), $searchLower) ||
                            str_contains((string) ($citizen['kk'] ?? ''), $searchLower);
                    })->values()->all();
                }

                // Apply pagination
                $totalItems = count($allCitizens);
                $offset = ($page - 1) * $limit;
                $paginatedCitizens = array_slice($allCitizens, $offset, $limit);

                return [
                    'status' => 'OK',
                    'message' => 'Citizens retrieved' . ($search ? ' with search' : ''),
                    'data' => [
                        'citizens' => $paginatedCitizens,
                        'pagination' => [
                            'current_page' => $page,
                            'items_per_page' => $limit,
                            'total_items' => $totalItems,
                            'total_page' => ceil($totalItems / $limit),
                            'next_page' => ($page * $limit) < $totalItems ? $page + 1 : null,
                            'prev_page' => $page > 1 ? $page - 1 : null,
                        ],
                    ]
                ];
            } else {
                Log::error('API request failed for getAllCitizensWithSearch', [
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
            Log::error('Error in getAllCitizensWithSearch: ' . $e->getMessage());
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
                            str_contains((string) $citizen['nik'], $searchLower) ||
                            str_contains((string) $citizen['kk'], $searchLower); // Tambahkan search KK
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
                return $this->filterCitizensByVillageId($villageId, $page, $limit, $search);
            }
        } catch (\Exception $e) {
            Log::error('Exception fetching citizens by village ID', [
                'error' => $e->getMessage(),
                'village_id' => $villageId
            ]);
            return $this->filterCitizensByVillageId($villageId, $page, $limit, $search);
        }
    }

    /**
     * Filter citizens by village ID manually (fallback method)
     *
     * @param int $villageId
     * @return array
     */
    private function filterCitizensByVillageId($villageId, $page = 1, $limit = 10, $search = null)
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
        $filteredCitizens = array_filter($allCitizens, function ($citizen) use ($villageId) {
            // Cek kedua kolom yang mungkin ada
            return (isset($citizen['villages_id']) && $citizen['villages_id'] == $villageId) ||
                (isset($citizen['village_id']) && $citizen['village_id'] == $villageId);
        });

        // Tambahkan filter pencarian jika parameter search diisi
        if ($search) {
            $searchLower = strtolower($search);
            $filteredCitizens = array_filter($filteredCitizens, function ($citizen) use ($searchLower) {
                return str_contains(strtolower($citizen['full_name'] ?? ''), $searchLower) ||
                    str_contains((string) ($citizen['nik'] ?? ''), $searchLower) ||
                    str_contains((string) ($citizen['kk'] ?? ''), $searchLower); // Tambahkan search KK
            });
        }

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
                    'next_page' => ($page * $limit) < $totalItems ? $page + 1 : null,
                    'prev_page' => $page > 1 ? $page - 1 : null,
                ],
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
        $filteredCitizens = array_filter($allCitizens, function ($citizen) use ($villageName) {
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

    /**
     * Clear all related caches when citizen data is updated
     */
    private function clearCitizenCaches($nik, $data)
    {
        try {
            // Clear family members cache if KK is updated
            if (isset($data['kk'])) {
                Cache::forget("family_members_kk_{$data['kk']}");
            }

            // Clear all citizens cache patterns
            $cachePatterns = [
                'admin_citizens_all',
                'admin_citizens_all_village_*',
                'citizens_all_1_100',
                'citizens_all_1_1000',
                'citizens_all_1_10000',
                'citizens_all_1_100_village_*',
                'citizens_all_1_1000_village_*',
                'citizens_all_1_10000_village_*',
                'citizens_search_*',
                '*_village_*',
                'family_members_kk_*'
            ];

            foreach ($cachePatterns as $pattern) {
                $this->clearCacheByPattern($pattern);
            }

            // Clear village stats cache if village_id is available
            $villageId = $data['villages_id'] ?? $data['village_id'] ?? null;
            if ($villageId) {
                $this->clearVillageStatsCache((int) $villageId);
            }
        } catch (\Exception $e) {
            Log::error('Error clearing citizen caches: ' . $e->getMessage());
        }
    }

    public function clearVillageStatsCache(int $villageId): void
    {
        $cache = $this->cacheStore();
        foreach ($this->villageCacheTypes() as $type) {
            $cache->forget($this->villageStatsCacheKey($type, $villageId));
        }
    }

    /**
     * Normalize data for API format - using same mapping as BiodataController
     */
    private function normalizeDataForApi($data)
    {
        $normalized = $data;

        // Use the same mapping as BiodataController for consistency
        $genderMap = [
            'Laki-Laki' => 1,
            'Laki-laki' => 1,
            'Perempuan' => 2,
            'laki-laki' => 1,
            'laki-laki' => 1,
            'perempuan' => 2,
            'LAKI-LAKI' => 1,
            'PEREMPUAN' => 2
        ];
        $citizenStatusMap = ['WNI' => 2, 'WNA' => 1, 'wni' => 2, 'wna' => 1];
        $certificateMap = [
            'Ada' => 1,
            'Tidak Ada' => 2,
            'ada' => 1,
            'tidak ada' => 2,
            'ADA' => 1,
            'TIDAK ADA' => 2
        ];
        $bloodTypeMap = [
            'A' => 1,
            'B' => 2,
            'AB' => 3,
            'O' => 4,
            'A+' => 5,
            'A-' => 6,
            'B+' => 7,
            'B-' => 8,
            'AB+' => 9,
            'AB-' => 10,
            'O+' => 11,
            'O-' => 12,
            'Tidak Tahu' => 13,
            'a' => 1,
            'b' => 2,
            'ab' => 3,
            'o' => 4,
            'a+' => 5,
            'a-' => 6,
            'b+' => 7,
            'b-' => 8,
            'ab+' => 9,
            'ab-' => 10,
            'o+' => 11,
            'o-' => 12,
            'tidak tahu' => 13
        ];
        $religionMap = [
            'Islam' => 1,
            'Kristen' => 2,
            'Katolik' => 3,
            'Katholik' => 3,
            'Hindu' => 4,
            'Buddha' => 5,
            'Budha' => 5,
            'Kong Hu Cu' => 6,
            'Konghucu' => 6,
            'Lainnya' => 7,
            'islam' => 1,
            'kristen' => 2,
            'katolik' => 3,
            'katholik' => 3,
            'hindu' => 4,
            'buddha' => 5,
            'budha' => 5,
            'kong hu cu' => 6,
            'konghucu' => 6,
            'lainnya' => 7
        ];
        $maritalStatusMap = [
            'Belum Kawin' => 1,
            'Kawin Tercatat' => 2,
            'Kawin Belum Tercatat' => 3,
            'Cerai Hidup Tercatat' => 4,
            'Cerai Hidup Belum Tercatat' => 5,
            'Cerai Mati' => 6,
            'BELUM KAWIN' => 1,
            'KAWIN TERCATAT' => 2,
            'KAWIN BELUM TERCATAT' => 3,
            'CERAI HIDUP TERCATAT' => 4,
            'CERAI HIDUP BELUM TERCATAT' => 5,
            'CERAI MATI' => 6
        ];
        $familyStatusMap = [
            'ANAK' => 1,
            'Anak' => 1,
            'anak' => 1,
            'KEPALA KELUARGA' => 2,
            'Kepala Keluarga' => 2,
            'kepala keluarga' => 2,
            'ISTRI' => 3,
            'Istri' => 3,
            'istri' => 3,
            'ORANG TUA' => 4,
            'Orang Tua' => 4,
            'orang tua' => 4,
            'MERTUA' => 5,
            'Mertua' => 5,
            'mertua' => 5,
            'CUCU' => 6,
            'Cucu' => 6,
            'cucu' => 6,
            'FAMILI LAIN' => 7,
            'Famili Lain' => 7,
            'famili lain' => 7,
            'LAINNYA' => 7,
            'Lainnya' => 7,
            'lainnya' => 7
        ];
        $educationStatusMap = [
            'Tidak/Belum Sekolah' => 1,
            'Belum tamat SD/Sederajat' => 2,
            'Tamat SD/Sederajat' => 3,
            'SLTP/SMP/Sederajat' => 4,
            'SLTA/SMA/Sederajat' => 5,
            'Diploma I/II' => 6,
            'Akademi/Diploma III/ Sarjana Muda' => 7,
            'Diploma IV/ Strata I/ Strata II' => 8,
            'Strata III' => 9,
            'Lainnya' => 10,
            'tidak/belum sekolah' => 1,
            'belum tamat sd/sederajat' => 2,
            'tamat sd/sederajat' => 3,
            'sltp/smp/sederajat' => 4,
            'slta/sma/sederajat' => 5,
            'diploma i/ii' => 6,
            'akademi/diploma iii/ sarjana muda' => 7,
            'diploma iv/ strata i/ strata ii' => 8,
            'strata iii' => 9,
            'lainnya' => 10
        ];

        $fieldsToNormalize = [
            'gender' => $genderMap,
            'citizen_status' => $citizenStatusMap,
            'birth_certificate' => $certificateMap,
            'blood_type' => $bloodTypeMap,
            'religion' => $religionMap,
            'marital_status' => $maritalStatusMap,
            'marital_certificate' => $certificateMap,
            'divorce_certificate' => $certificateMap,
            'family_status' => $familyStatusMap,
            'mental_disorders' => $certificateMap,
            'education_status' => $educationStatusMap,
        ];

        // Normalize each field using the same logic as BiodataController
        foreach ($fieldsToNormalize as $field => $mapping) {
            if (isset($normalized[$field])) {
                $value = trim($normalized[$field]);

                // If it's already numeric, keep it as is
                if (is_numeric($value)) {
                    $normalized[$field] = (int)$value;
                    continue;
                }

                // Try to map string values to numeric
                if (array_key_exists($value, $mapping)) {
                    $normalized[$field] = $mapping[$value];
                    Log::info("Normalized {$field} from '{$value}' to {$normalized[$field]}");
                } else if (!empty($value)) {
                    Log::warning("Could not normalize {$field} value: '{$value}'");
                    // Don't set default values - preserve original data
                }
            }
        }

        // Convert KK to integer if exists
        if (isset($normalized['kk'])) {
            $normalized['kk'] = (int) $normalized['kk'];
        }

        // Ensure numeric fields are properly typed
        $numericFields = ['age', 'province_id', 'district_id', 'sub_district_id', 'village_id', 'job_type_id'];
        foreach ($numericFields as $field) {
            if (isset($normalized[$field]) && is_numeric($normalized[$field])) {
                $normalized[$field] = (int) $normalized[$field];
            }
        }

        // Only add required fields if they are completely missing (not just empty)
        // This preserves existing data and only adds truly missing fields
        $requiredFields = [
            'citizen_status' => 1,
            'birth_certificate' => 2,
            'blood_type' => 13,
            'religion' => 1,
            'marital_status' => 1,
            'marital_certificate' => 2,
            'divorce_certificate' => 2,
            'mental_disorders' => 2,
            'education_status' => 1,
            'family_status' => 2,
            'job_type_id' => 1
        ];

        foreach ($requiredFields as $field => $defaultValue) {
            // Only add default if field is completely missing or null
            if (!isset($normalized[$field]) || $normalized[$field] === null) {
                $normalized[$field] = $defaultValue;
                Log::info("Added missing required field: {$field} = {$defaultValue}");
            }
        }

        return $normalized;
    }

    /**
     * Clear cache by pattern using Redis or file cache
     */
    private function clearCacheByPattern($pattern)
    {
        try {
            // For file cache, we need to clear all related keys
            $keys = [
                $pattern,
                $pattern . '_village_*',
                $pattern . '_search_*'
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Also clear any cached data that might contain this citizen
            Cache::forget('admin_citizens_all');
            Cache::forget('citizens_all_1_100');
            Cache::forget('citizens_all_1_1000');
            Cache::forget('citizens_all_1_10000');
        } catch (\Exception $e) {
            Log::error('Error clearing cache pattern: ' . $e->getMessage());
        }
    }

    /**
     * Clear cache specifically for a citizen by NIK
     */
    private function clearCacheByNIK($nik)
    {
        try {
            // Clear any cache that might contain this specific citizen
            $cacheKeys = [
                "citizen_{$nik}",
                "citizen_data_{$nik}",
                "citizen_by_nik_{$nik}"
            ];

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
        } catch (\Exception $e) {
            Log::error('Error clearing cache by NIK: ' . $e->getMessage());
        }
    }

    /**
     * Konversi koordinat menjadi alamat lengkap menggunakan reverse geocoding
     */
    public function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            // Gunakan OpenStreetMap Nominatim API untuk reverse geocoding
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
                'accept-language' => 'id'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['display_name'])) {
                    // Format alamat yang lebih mudah dibaca
                    $address = $data['display_name'];

                    // Coba ambil komponen alamat yang lebih spesifik
                    $addressComponents = $data['address'] ?? [];
                    $formattedAddress = '';

                    // Bangun alamat dari komponen yang tersedia
                    if (isset($addressComponents['house_number'])) {
                        $formattedAddress .= $addressComponents['house_number'] . ', ';
                    }
                    if (isset($addressComponents['road'])) {
                        $formattedAddress .= $addressComponents['road'] . ', ';
                    }
                    if (isset($addressComponents['suburb'])) {
                        $formattedAddress .= $addressComponents['suburb'] . ', ';
                    }
                    if (isset($addressComponents['village'])) {
                        $formattedAddress .= $addressComponents['village'] . ', ';
                    }
                    if (isset($addressComponents['city_district'])) {
                        $formattedAddress .= $addressComponents['city_district'] . ', ';
                    }
                    if (isset($addressComponents['city'])) {
                        $formattedAddress .= $addressComponents['city'] . ', ';
                    }
                    if (isset($addressComponents['state'])) {
                        $formattedAddress .= $addressComponents['state'] . ', ';
                    }
                    if (isset($addressComponents['country'])) {
                        $formattedAddress .= $addressComponents['country'];
                    }

                    // Bersihkan koma di akhir
                    $formattedAddress = rtrim($formattedAddress, ', ');

                    return $formattedAddress ?: $address;
                }
            }

            return "Koordinat: {$latitude}, {$longitude}";
        } catch (\Exception $e) {
            Log::error('Error getting address from coordinates: ' . $e->getMessage());
            return "Koordinat: {$latitude}, {$longitude}";
        }
    }

    /**
     * Ambil data anggota keluarga dengan informasi lokasi
     */
    public function getFamilyMembersWithLocation($kk)
    {
        try {
            // Ambil data keluarga dari API
            $familyData = $this->getFamilyMembersByKK($kk);

            if (!$familyData || !isset($familyData['data'])) {
                Log::warning('No family data found for KK: ' . $kk);
                return [
                    'status' => 'OK',
                    'data' => []
                ];
            }

            $members = $familyData['data'];
            $membersWithLocation = [];

            foreach ($members as $member) {
                $memberData = $member;

                // Pastikan NIK ada
                if (!isset($member['nik']) || empty($member['nik'])) {
                    Log::warning('Member without NIK found: ' . json_encode($member));
                    $memberData['location_address'] = 'NIK tidak valid';
                    $memberData['coordinates'] = null;
                    $membersWithLocation[] = $memberData;
                    continue;
                }

                // Ambil data lokasi dari database lokal jika ada
                $penduduk = Penduduk::where('nik', $member['nik'])->first();

                if ($penduduk && $penduduk->tag_lokasi) {
                    $coordinates = explode(',', $penduduk->tag_lokasi);
                    if (count($coordinates) >= 2) {
                        $lat = trim($coordinates[0]);
                        $lng = trim($coordinates[1]);

                        // Validasi koordinat
                        if (is_numeric($lat) && is_numeric($lng)) {
                            // Konversi koordinat ke alamat
                            $address = $this->getAddressFromCoordinates($lat, $lng);
                            $memberData['location_address'] = $address;
                            $memberData['coordinates'] = $penduduk->tag_lokasi;
                        } else {
                            $memberData['location_address'] = 'Koordinat tidak valid';
                            $memberData['coordinates'] = $penduduk->tag_lokasi;
                        }
                    } else {
                        $memberData['location_address'] = 'Format koordinat tidak valid';
                        $memberData['coordinates'] = $penduduk->tag_lokasi;
                    }
                } else {
                    $memberData['location_address'] = 'Belum ada lokasi';
                    $memberData['coordinates'] = null;
                }

                $membersWithLocation[] = $memberData;
            }

            return [
                'status' => 'OK',
                'data' => $membersWithLocation
            ];
        } catch (\Exception $e) {
            Log::error('Error getting family members with location: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'data' => [],
                'message' => $e->getMessage()
            ];
        }
    }

    public function getGenderStatsByVillage($villageId, bool $useCache = true)
    {
        $cacheKey = $this->villageStatsCacheKey('gender', (int) $villageId);
        $cache = $this->cacheStore();
        $cacheTTL = now()->addMinutes(30); // Cache selama 30 menit

        if ($useCache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $payload = $this->buildGenderStatsByVillage($villageId);
        
        if ($useCache) {
            $cache->put($cacheKey, $payload, $cacheTTL);
        }

        return $payload;
    }

    private function buildGenderStatsByVillage($villageId)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                $citizens = collect($data['data'])
                    ->where('village_id', $villageId);

                // Count male with various gender formats
                $maleCount = $citizens->filter(function ($citizen) {
                    $gender = strtolower(trim($citizen['gender'] ?? ''));
                    return in_array($gender, ['l', 'laki-laki', 'Laki-laki', 'LAKI-LAKI, laki laki', 'LAKI LAKI', 'Laki laki', 'male', 'm']);
                })->count();

                // Count female with various gender formats
                $femaleCount = $citizens->filter(function ($citizen) {
                    $gender = strtolower(trim($citizen['gender'] ?? ''));
                    return in_array($gender, ['p', 'perempuan', 'female', 'f']);
                })->count();

                return [
                    'male' => $maleCount,
                    'female' => $femaleCount,
                    'total' => $citizens->count()
                ];
            } else {
                Log::error('API request failed when fetching gender stats by village ID', [
                    'status_code' => $response->status(),
                    'village_id' => $villageId
                ]);

                // Fallback to local database if API fails
                return $this->getGenderStatsByVillageFromLocal($villageId);
            }
        } catch (\Exception $e) {
            Log::error('Error getting gender stats by village: ' . $e->getMessage());
            return $this->getGenderStatsByVillageFromLocal($villageId);
        }
    }

    public function getAgeGroupStatsByVillage($villageId, bool $useCache = true)
    {
        $cacheKey = $this->villageStatsCacheKey('age', (int) $villageId);
        $cache = $this->cacheStore();
        $cacheTTL = now()->addMinutes(30); // Cache selama 30 menit

        if ($useCache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $payload = $this->buildAgeGroupStatsByVillage($villageId);
        
        if ($useCache) {
            $cache->put($cacheKey, $payload, $cacheTTL);
        }

        return $payload;
    }

    private function buildAgeGroupStatsByVillage($villageId)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                $citizens = collect($data['data'])
                    ->where('village_id', $villageId);

                $now = now();
                $groups = [
                    '0_17' => 0,
                    '18_30' => 0,
                    '31_45' => 0,
                    '46_60' => 0,
                    '61_plus' => 0,
                ];

                foreach ($citizens as $citizen) {
                    $age = null;

                    // Try multiple possible keys for date of birth or age
                    $dob = $citizen['birth_date'] ?? $citizen['tanggal_lahir'] ?? $citizen['tgl_lahir'] ?? $citizen['date_of_birth'] ?? null;
                    if ($dob) {
                        try {
                            $age = \Carbon\Carbon::parse($dob)->diffInYears($now);
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }
                    if ($age === null) {
                        $age = $citizen['age'] ?? $citizen['umur'] ?? null;
                        if (is_string($age)) {
                            $age = (int) preg_replace('/[^0-9]/', '', $age);
                        }
                    }

                    if ($age === null || $age < 0 || $age > 130) {
                        continue;
                    }

                    if ($age <= 17) {
                        $groups['0_17']++;
                    } elseif ($age <= 30) {
                        $groups['18_30']++;
                    } elseif ($age <= 45) {
                        $groups['31_45']++;
                    } elseif ($age <= 60) {
                        $groups['46_60']++;
                    } else {
                        $groups['61_plus']++;
                    }
                }

                $total = array_sum($groups);

                return [
                    'groups' => $groups,
                    'total_with_age' => $total,
                ];
            }

            // fallback to local
            return $this->getAgeGroupStatsByVillageFromLocal($villageId);
        } catch (\Exception $e) {
            return $this->getAgeGroupStatsByVillageFromLocal($villageId);
        }
    }

    private function getAgeGroupStatsByVillageFromLocal($villageId)
    {
        try {
            $citizens = Penduduk::where('villages_id', $villageId)->get();

            $now = now();
            $groups = [
                '0_17' => 0,
                '18_30' => 0,
                '31_45' => 0,
                '46_60' => 0,
                '61_plus' => 0,
            ];

            foreach ($citizens as $citizen) {
                $age = null;
                $dob = $citizen->tanggal_lahir ?? $citizen->tgl_lahir ?? $citizen->birth_date ?? null;
                if ($dob) {
                    try {
                        $age = \Carbon\Carbon::parse($dob)->diffInYears($now);
                    } catch (\Exception $e) {
                        $age = null;
                    }
                }
                if ($age === null) {
                    $age = $citizen->umur ?? null;
                    if (is_string($age)) {
                        $age = (int) preg_replace('/[^0-9]/', '', $age);
                    }
                }

                if ($age === null || $age < 0 || $age > 130) {
                    continue;
                }

                if ($age <= 17) {
                    $groups['0_17']++;
                } elseif ($age <= 30) {
                    $groups['18_30']++;
                } elseif ($age <= 45) {
                    $groups['31_45']++;
                } elseif ($age <= 60) {
                    $groups['46_60']++;
                } else {
                    $groups['61_plus']++;
                }
            }

            $total = array_sum($groups);

            return [
                'groups' => $groups,
                'total_with_age' => $total,
            ];
        } catch (\Exception $e) {
            return [
                'groups' => [
                    '0_17' => 0,
                    '18_30' => 0,
                    '31_45' => 0,
                    '46_60' => 0,
                    '61_plus' => 0,
                ],
                'total_with_age' => 0,
            ];
        }
    }

    public function getEducationStatsByVillage($villageId, bool $useCache = true)
    {
        $cacheKey = $this->villageStatsCacheKey('education', (int) $villageId);
        $cache = $this->cacheStore();
        $cacheTTL = now()->addMinutes(30); // Cache selama 30 menit

        if ($useCache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $payload = $this->buildEducationStatsByVillage($villageId);
        
        if ($useCache) {
            $cache->put($cacheKey, $payload, $cacheTTL);
        }

        return $payload;
    }

    private function buildEducationStatsByVillage($villageId)
    {
        // Define all possible education categories based on the form options
        $allEducationCategories = [
            'tidak/belum sekolah',
            'belum tamat sd/sederajat',
            'tamat sd/sederajat',
            'sltp/smp/sederajat',
            'slta/sma/sederajat',
            'diploma i/ii',
            'akademi/diploma iii/ sarjana muda',
            'diploma iv/ strata i/ strata ii',
            'strata iii',
            'lainnya'
        ];

        try {
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();

                $citizens = collect($data['data'])
                    ->where('village_id', $villageId);

                // Initialize all categories with 0
                $groups = [];
                foreach ($allEducationCategories as $category) {
                    $groups[$category] = 0;
                }

                foreach ($citizens as $citizen) {
                    // Hanya gunakan kolom education_status dari Citizen Service
                    $edu = $citizen['education_status'] ?? null;

                    if (!$edu || (is_string($edu) && trim($edu) === '')) {
                        $key = 'tidak/belum sekolah'; // Default to first category instead of unknown
                    } else {
                        $key = $this->normalizeEducation((string) $edu);
                    }

                    if (isset($groups[$key])) {
                        $groups[$key]++;
                    }
                }

                $total = array_sum($groups);

                return [
                    'groups' => $groups,
                    'total_with_education' => $total,
                ];
            }

            return $this->getEducationStatsByVillageFromLocal($villageId);
        } catch (\Exception $e) {
            return $this->getEducationStatsByVillageFromLocal($villageId);
        }
    }

    private function getEducationStatsByVillageFromLocal($villageId)
    {
        // Define all possible education categories based on the form options
        $allEducationCategories = [
            'tidak/belum sekolah',
            'belum tamat sd/sederajat',
            'tamat sd/sederajat',
            'sltp/smp/sederajat',
            'slta/sma/sederajat',
            'diploma i/ii',
            'akademi/diploma iii/ sarjana muda',
            'diploma iv/ strata i/ strata ii',
            'strata iii',
            'lainnya'
        ];

        try {
            $citizens = Penduduk::where('villages_id', $villageId)->get();

            // Initialize all categories with 0
            $groups = [];
            foreach ($allEducationCategories as $category) {
                $groups[$category] = 0;
            }

            foreach ($citizens as $citizen) {
                // Fallback lokal: gunakan kolom education_status jika tersedia, jika tidak anggap tidak/belum sekolah
                $edu = $citizen->education_status ?? null;
                if (!$edu || (is_string($edu) && trim($edu) === '')) {
                    $key = 'tidak/belum sekolah'; // Default to first category instead of unknown
                } else {
                    $key = $this->normalizeEducation((string) $edu);
                }

                if (isset($groups[$key])) {
                    $groups[$key]++;
                }
            }

            $total = array_sum($groups);
            return [
                'groups' => $groups,
                'total_with_education' => $total,
            ];
        } catch (\Exception $e) {
            // Return all categories with 0 values even on error
            $groups = [];
            foreach ($allEducationCategories as $category) {
                $groups[$category] = 0;
            }
            return [
                'groups' => $groups,
                'total_with_education' => 0,
            ];
        }
    }

    private function normalizeEducation(string $raw): string
    {
        $v = strtolower(trim($raw));
        $v = str_replace(['.', '  '], [' ', ' '], $v);

        // Pemetaan ke kategori yang sesuai dengan form options
        $map = [
            'tidak/belum sekolah' => 'tidak/belum sekolah',
            'tidak sekolah' => 'tidak/belum sekolah',
            'belum sekolah' => 'tidak/belum sekolah',
            'belum tamat sd/sederajat' => 'belum tamat sd/sederajat',
            'belum tamat sd' => 'belum tamat sd/sederajat',
            'tamat sd/sederajat' => 'tamat sd/sederajat',
            'tamat sd' => 'tamat sd/sederajat',
            'sltp/smp/sederajat' => 'sltp/smp/sederajat',
            'smp' => 'sltp/smp/sederajat',
            'mts' => 'sltp/smp/sederajat',
            'slta/sma/sederajat' => 'slta/sma/sederajat',
            'sma' => 'slta/sma/sederajat',
            'smk' => 'slta/sma/sederajat',
            'ma' => 'slta/sma/sederajat',
            'diploma i/ii' => 'diploma i/ii',
            'd1' => 'diploma i/ii',
            'd2' => 'diploma i/ii',
            'akademi/diploma iii/ sarjana muda' => 'akademi/diploma iii/ sarjana muda',
            'd3' => 'akademi/diploma iii/ sarjana muda',
            'akademi' => 'akademi/diploma iii/ sarjana muda',
            'diploma iv/ strata i/ strata ii' => 'diploma iv/ strata i/ strata ii',
            'd4' => 'diploma iv/ strata i/ strata ii',
            's1' => 'diploma iv/ strata i/ strata ii',
            'sarjana' => 'diploma iv/ strata i/ strata ii',
            'strata iii' => 'strata iii',
            's2' => 'strata iii',
            's3' => 'strata iii',
            'magister' => 'strata iii',
            'doktor' => 'strata iii',
            'lainnya' => 'lainnya',
        ];

        if (isset($map[$v])) return $map[$v];

        // Heuristik untuk variasi ejaan
        if (str_contains($v, 'tidak') || str_contains($v, 'belum')) return 'tidak/belum sekolah';
        if (str_contains($v, 'sd') && str_contains($v, 'belum')) return 'belum tamat sd/sederajat';
        if (str_contains($v, 'sd') && !str_contains($v, 'belum')) return 'tamat sd/sederajat';
        if (str_contains($v, 'smp') || str_contains($v, 'mts') || str_contains($v, 'sltp')) return 'sltp/smp/sederajat';
        if (str_contains($v, 'sma') || str_contains($v, 'smk') || str_contains($v, 'ma') || str_contains($v, 'slta')) return 'slta/sma/sederajat';
        if (str_contains($v, 'd1') || str_contains($v, 'd2')) return 'diploma i/ii';
        if (str_contains($v, 'd3') || str_contains($v, 'akademi')) return 'akademi/diploma iii/ sarjana muda';
        if (str_contains($v, 'd4') || str_contains($v, 's1') || str_contains($v, 'sarjana')) return 'diploma iv/ strata i/ strata ii';
        if (str_contains($v, 's2') || str_contains($v, 's3') || str_contains($v, 'magister') || str_contains($v, 'doktor')) return 'strata iii';

        return 'lainnya';
    }

    public function getReligionStatsByVillage($villageId, bool $useCache = true)
    {
        $cacheKey = $this->villageStatsCacheKey('religion', (int) $villageId);
        $cache = $this->cacheStore();
        $cacheTTL = now()->addMinutes(30); // Cache selama 30 menit

        if ($useCache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $payload = $this->buildReligionStatsByVillage($villageId);
        
        if ($useCache) {
            $cache->put($cacheKey, $payload, $cacheTTL);
        }

        return $payload;
    }

    private function buildReligionStatsByVillage($villageId)
    {
        // Define all possible religion categories based on the form options
        $allReligionCategories = [
            'islam',
            'kristen',
            'katolik',
            'hindu',
            'buddha',
            'konghucu',
            'lainnya'
        ];

        try {
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();
                $citizens = collect($data['data'])->where('village_id', $villageId);

                // Initialize all categories with 0
                $groups = [];
                foreach ($allReligionCategories as $category) {
                    $groups[$category] = 0;
                }

                foreach ($citizens as $citizen) {
                    $rel = $citizen['religion'] ?? $citizen['agama'] ?? null;
                    $key = $this->normalizeReligion((string) ($rel ?? ''));
                    if (isset($groups[$key])) {
                        $groups[$key]++;
                    }
                }

                $total = array_sum($groups);
                return [
                    'groups' => $groups,
                    'total_with_religion' => $total,
                ];
            }

            return $this->getReligionStatsByVillageFromLocal($villageId);
        } catch (\Exception $e) {
            return $this->getReligionStatsByVillageFromLocal($villageId);
        }
    }

    private function getReligionStatsByVillageFromLocal($villageId)
    {
        // Define all possible religion categories based on the form options
        $allReligionCategories = [
            'islam',
            'kristen',
            'katolik',
            'hindu',
            'buddha',
            'konghucu',
            'lainnya'
        ];

        try {
            $citizens = Penduduk::where('villages_id', $villageId)->get();

            // Initialize all categories with 0
            $groups = [];
            foreach ($allReligionCategories as $category) {
                $groups[$category] = 0;
            }

            foreach ($citizens as $citizen) {
                $rel = $citizen->religion ?? $citizen->agama ?? null;
                $key = $this->normalizeReligion((string) ($rel ?? ''));
                if (isset($groups[$key])) {
                    $groups[$key]++;
                }
            }
            $total = array_sum($groups);
            return [
                'groups' => $groups,
                'total_with_religion' => $total,
            ];
        } catch (\Exception $e) {
            // Return all categories with 0 values even on error
            $groups = [];
            foreach ($allReligionCategories as $category) {
                $groups[$category] = 0;
            }
            return [
                'groups' => $groups,
                'total_with_religion' => 0,
            ];
        }
    }

    private function normalizeReligion(string $raw): string
    {
        $v = strtolower(trim($raw));
        if ($v === '' || $v === 'null' || $v === '-') return 'lainnya';

        // normalisasi umum untuk agama di Indonesia
        $map = [
            'islam' => 'islam',
            'moslem' => 'islam',
            'muslim' => 'islam',
            'kristen' => 'kristen',
            'protestan' => 'kristen',
            'katholik' => 'katolik',
            'katolik' => 'katolik',
            'catholic' => 'katolik',
            'hindu' => 'hindu',
            'budha' => 'buddha',
            'buddha' => 'buddha',
            'konghucu' => 'konghucu',
            'kong hu cu' => 'konghucu',
            'confucian' => 'konghucu',
            'kepercayaan' => 'kepercayaan',
            'lainnya' => 'lainnya',
        ];

        if (isset($map[$v])) return $map[$v];

        // heuristik sederhana untuk variasi ejaan
        if (str_contains($v, 'islam') || str_contains($v, 'muslim')) return 'islam';
        if (str_contains($v, 'krist') || str_contains($v, 'protest')) return 'kristen';
        if (str_contains($v, 'katol') || str_contains($v, 'cath')) return 'katolik';
        if (str_contains($v, 'hind')) return 'hindu';
        if (str_contains($v, 'bud')) return 'buddha';
        if (str_contains($v, 'kong') || str_contains($v, 'confuc')) return 'konghucu';

        return 'lainnya';
    }
    /**
     * Get all village statistics in one API call (optimized for performance)
     * This method fetches /api/all-citizens once and calculates all stats
     */
    public function getAllVillageStats($villageId, bool $useCache = true): array
    {
        // Validasi village_id
        if (!$villageId || $villageId <= 0) {
            return [
                'gender' => ['male' => 0, 'female' => 0, 'total' => 0],
                'age' => ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0],
                'education' => ['groups' => [], 'total_with_education' => 0],
                'religion' => ['groups' => [], 'total_with_religion' => 0],
            ];
        }

        $villageId = (int) $villageId;
        $cacheKey = $this->villageStatsCacheKey('all_stats', $villageId);
        $cache = $this->cacheStore();
        $cacheTTL = now()->addMinutes(30); // Cache selama 30 menit

        if ($useCache && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $payload = $this->buildAllVillageStats($villageId);
        
        // Pastikan payload memiliki struktur yang benar sebelum di-cache
        if ($useCache && !empty($payload) && isset($payload['gender']) && isset($payload['age']) && isset($payload['education']) && isset($payload['religion'])) {
            $cache->put($cacheKey, $payload, $cacheTTL);
        }

        return $payload;
    }

    private function buildAllVillageStats(int $villageId): array
    {
        try {
            // Single API call untuk semua data dengan timeout yang lebih panjang
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/all-citizens");

            if ($response->successful()) {
                $data = $response->json();
                
                // Pastikan data['data'] ada dan merupakan array
                if (isset($data['data']) && is_array($data['data'])) {
                    $citizens = collect($data['data'])->where('village_id', $villageId);

                    // Hitung semua statistik dari data yang sama
                    $genderStats = $this->calculateGenderStats($citizens);
                    $ageStats = $this->calculateAgeStats($citizens);
                    $educationStats = $this->calculateEducationStats($citizens);
                    $religionStats = $this->calculateReligionStats($citizens);

                    return [
                        'gender' => $genderStats,
                        'age' => $ageStats,
                        'education' => $educationStats,
                        'religion' => $religionStats,
                    ];
                } else {
                    Log::warning('Invalid API response structure for all-citizens', [
                        'village_id' => $villageId,
                        'response_keys' => array_keys($data ?? [])
                    ]);
                }
            } else {
                Log::warning('API request failed for all-citizens', [
                    'village_id' => $villageId,
                    'status_code' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting all village stats: ' . $e->getMessage(), [
                'village_id' => $villageId,
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Fallback ke database lokal
        return $this->getAllVillageStatsFromLocal($villageId);
    }

    private function calculateGenderStats($citizens): array
    {
        $maleCount = $citizens->filter(function ($citizen) {
            $gender = strtolower(trim($citizen['gender'] ?? ''));
            return in_array($gender, ['l', 'laki-laki', 'Laki-laki', 'LAKI-LAKI', 'laki laki', 'LAKI LAKI', 'Laki laki', 'male', 'm']);
        })->count();

        $femaleCount = $citizens->filter(function ($citizen) {
            $gender = strtolower(trim($citizen['gender'] ?? ''));
            return in_array($gender, ['p', 'perempuan', 'Perempuan', 'PEREMPUAN', 'female', 'f']);
        })->count();

        return [
            'male' => $maleCount,
            'female' => $femaleCount,
            'total' => $citizens->count()
        ];
    }

    private function calculateAgeStats($citizens): array
    {
        $now = now();
        $groups = [
            '0_17' => 0,
            '18_30' => 0,
            '31_45' => 0,
            '46_60' => 0,
            '61_plus' => 0,
        ];

        foreach ($citizens as $citizen) {
            $age = null;
            $dob = $citizen['birth_date'] ?? $citizen['tanggal_lahir'] ?? $citizen['tgl_lahir'] ?? $citizen['date_of_birth'] ?? null;
            if ($dob) {
                try {
                    $age = \Carbon\Carbon::parse($dob)->diffInYears($now);
                } catch (\Exception $e) {
                    $age = null;
                }
            }
            if ($age === null) {
                $age = $citizen['age'] ?? $citizen['umur'] ?? null;
                if (is_string($age)) {
                    $age = (int) preg_replace('/[^0-9]/', '', $age);
                }
            }

            if ($age === null || $age < 0 || $age > 130) {
                continue;
            }

            if ($age <= 17) {
                $groups['0_17']++;
            } elseif ($age <= 30) {
                $groups['18_30']++;
            } elseif ($age <= 45) {
                $groups['31_45']++;
            } elseif ($age <= 60) {
                $groups['46_60']++;
            } else {
                $groups['61_plus']++;
            }
        }

        return [
            'groups' => $groups,
            'total_with_age' => array_sum($groups),
        ];
    }

    private function calculateEducationStats($citizens): array
    {
        $allEducationCategories = [
            'tidak/belum sekolah',
            'belum tamat sd/sederajat',
            'tamat sd/sederajat',
            'sltp/smp/sederajat',
            'slta/sma/sederajat',
            'diploma i/ii',
            'akademi/diploma iii/ sarjana muda',
            'diploma iv/ strata i/ strata ii',
            'strata iii',
            'lainnya'
        ];

        $groups = [];
        foreach ($allEducationCategories as $category) {
            $groups[$category] = 0;
        }

        foreach ($citizens as $citizen) {
            $edu = $citizen['education_status'] ?? null;
            if (!$edu || (is_string($edu) && trim($edu) === '')) {
                $key = 'tidak/belum sekolah';
            } else {
                $key = $this->normalizeEducation((string) $edu);
            }

            if (isset($groups[$key])) {
                $groups[$key]++;
            }
        }

        return [
            'groups' => $groups,
            'total_with_education' => array_sum($groups),
        ];
    }

    private function calculateReligionStats($citizens): array
    {
        $allReligionCategories = [
            'islam',
            'kristen',
            'katolik',
            'hindu',
            'buddha',
            'konghucu',
            'lainnya'
        ];

        $groups = [];
        foreach ($allReligionCategories as $category) {
            $groups[$category] = 0;
        }

        foreach ($citizens as $citizen) {
            $rel = $citizen['religion'] ?? $citizen['agama'] ?? null;
            $key = $this->normalizeReligion((string) ($rel ?? ''));
            if (isset($groups[$key])) {
                $groups[$key]++;
            }
        }

        return [
            'groups' => $groups,
            'total_with_religion' => array_sum($groups),
        ];
    }

    private function getAllVillageStatsFromLocal(int $villageId): array
    {
        try {
            $citizens = Penduduk::where('villages_id', $villageId)->get();
            $citizensCollection = $citizens->map(function ($citizen) {
                return [
                    'gender' => $citizen->gender,
                    'birth_date' => $citizen->tanggal_lahir ?? $citizen->tgl_lahir ?? $citizen->birth_date,
                    'age' => $citizen->umur,
                    'education_status' => $citizen->education_status,
                    'religion' => $citizen->religion ?? $citizen->agama,
                ];
            });

            return [
                'gender' => $this->calculateGenderStats($citizensCollection),
                'age' => $this->calculateAgeStats($citizensCollection),
                'education' => $this->calculateEducationStats($citizensCollection),
                'religion' => $this->calculateReligionStats($citizensCollection),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting all village stats from local: ' . $e->getMessage());
            return [
                'gender' => ['male' => 0, 'female' => 0, 'total' => 0],
                'age' => ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0],
                'education' => ['groups' => [], 'total_with_education' => 0],
                'religion' => ['groups' => [], 'total_with_religion' => 0],
            ];
        }
    }

    private function getGenderStatsByVillageFromLocal($villageId)
    {
        try {
            $citizens = Penduduk::where('villages_id', $villageId)->get();

            // Count male with various gender formats
            $maleCount = $citizens->filter(function ($citizen) {
                $gender = strtolower(trim($citizen->gender ?? ''));
                return in_array($gender, ['l', 'laki-laki', 'Laki-laki', 'LAKI-LAKI, laki laki', 'LAKI LAKI', 'Laki laki', 'male', 'm']);
            })->count();

            // Count female with various gender formats
            $femaleCount = $citizens->filter(function ($citizen) {
                $gender = strtolower(trim($citizen->gender ?? ''));
                return in_array($gender, ['p', 'perempuan', 'Perempuan', 'PEREMPUAN', 'female', 'f']);
            })->count();

            return [
                'male' => $maleCount,
                'female' => $femaleCount,
                'total' => $citizens->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting gender stats from local database: ' . $e->getMessage());
            return [
                'male' => 0,
                'female' => 0,
                'total' => 0
            ];
        }
    }

    private function villageStatsCacheKey(string $type, int $villageId): string
    {
        return "citizen_service:{$type}_stats:village:{$villageId}";
    }

    private function villageCacheTypes(): array
    {
        return ['gender', 'age', 'education', 'religion', 'all_stats'];
    }

    private function cacheStore()
    {
        try {
            return Cache::store($this->cacheStoreName);
        } catch (\InvalidArgumentException $e) {
            return Cache::store(config('cache.default'));
        }
    }
}
