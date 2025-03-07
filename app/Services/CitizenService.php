<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

                        $allHeadsOfFamily = array_merge($allHeadsOfFamily, $headsOfFamily);

                        $hasMoreData = $data['data']['pagination']['has_more_pages'];
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

        return $allHeadsOfFamily;
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

    public function createCitizen($data)
    {
        try {
            Log::info('Sending request to API:', [
                'url' => "{$this->baseUrl}/api/citizens",
                'data' => $data,
                'headers' => [
                    'X-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/citizens", $data);

            Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API request failed:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return [
                'status' => 'ERROR',
                'message' => 'API request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Error creating biodata: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => 'Failed to create biodata: ' . $e->getMessage()
            ];
        }
    }

    public function updateCitizen($nik, $data)
    {
        try {
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
            return ['status' => 'ERROR', 'message' => 'Error updating citizen'];
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
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens-family/{$kk}");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('API request failed: ' . $response->status());
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
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/citizens/{$nik}");

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
}

