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

}
