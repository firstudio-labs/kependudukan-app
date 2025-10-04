<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Penduduk;

class CitizenServiceV2
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kependudukan.url');
        $this->apiKey = config('services.kependudukan.key');
    }

    public function getAllCitizensWithSearch($page = 1, $limit = 10, $search = null, $vilage_id = null)
    {
        try {
            $url = "{$this->baseUrl}/api/v2/citizens?page={$page}&items_per_page={$limit}&search={$search}";
            if ($vilage_id) {
                $url .= "&village_id={$vilage_id}";
            }
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($url);

            if ($response->successful()) {
                $result =  $response->json();
                return $result;
            } else {
                Log::error('API request failed for getAllCitizensWithSearch', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return [
                    'status' => 'ERROR',
                    'message' => 'Failed to fetch citizens V2',
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

    public function getAllHeadCitizensWithSearch($page = 1, $limit = 10, $search = null, $vilage_id = null)
    {
        try {
            $url = "{$this->baseUrl}/api/v2/family-heads?page={$page}&items_per_page={$limit}&search={$search}";
            if ($vilage_id) {
                $url .= "&village_id={$vilage_id}";
            }
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
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

    public function getHeadOfFamilyByKk($kk)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/v2/family-heads-by-kk/{$kk}");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
                return null;
            }

            Log::error('API request failed: ' . $response->status());
            return null;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('HTTP request exception in getHeadOfFamilyByKk: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching head of family by KK: ' . $e->getMessage());
            return null;
        }
    }

    public function updateCitizenByKK(string $kk, $data): array
    {
        try {
            $url = "{$this->baseUrl}/api/v2/citizen-by-kk/{$kk}";

            $numericFields = ['province_id', 'district_id', 'sub_district_id', 'village_id', 'postal_code'];
            foreach ($numericFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = is_numeric($data[$field]) ? (int) $data[$field] : null;
                }
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-API-Key' => $this->apiKey,
            ])->timeout(10)
                ->put($url, $data);

            if ($response->successful()) {
                return [
                    'status' => 'SUCCESS',
                    'message' => 'Citizen updated successfully',
                    'data' => $response->json(),
                ];
            }

            Log::warning('Failed to update citizen by KK', [
                'kk' => $kk,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'status' => 'ERROR',
                'message' => "Failed to update citizen (HTTP {$response->status()})",
                'data' => $response->json() ?? [],
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('HTTP request exception in updateCitizenByKK', [
                'kk' => $kk,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'ERROR',
                'message' => 'Request failed: ' . $e->getMessage(),
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Exception in updateCitizenByKK', [
                'kk' => $kk,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'ERROR',
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }
}
