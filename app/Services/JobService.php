<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class JobService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kependudukan.url');
        $this->apiKey = config('services.kependudukan.key');
    }

    public function getAllJobs()
    {
        $cacheKey = 'jobs_list_all';
        $staleCacheKey = 'jobs_list_stale';
        
        // Cek cache terlebih dahulu (TTL 24 jam untuk mobile app)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Jika cache expired, coba ambil stale cache sebagai fallback
        $staleData = Cache::get($staleCacheKey);

        // Coba fetch data baru dari API dengan timeout lebih lama dan retry
        try {
            $response = Http::timeout(30) // Perpanjang timeout menjadi 30 detik
                ->retry(2, 1000) // Retry 2 kali dengan delay 1 detik
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                ])->get("{$this->baseUrl}/api/jobs");

            if ($response->successful()) {
                $result = $response->json()['data'] ?? [];
                
                // Cache hasil selama 24 jam (lebih lama untuk mobile app)
                Cache::put($cacheKey, $result, now()->addHours(24));
                
                // Simpan juga sebagai stale cache (30 hari) untuk fallback jika API gagal di masa depan
                Cache::put($staleCacheKey, $result, now()->addDays(30));
                
                return $result;
            } else {
                Log::error('API request failed: ' . $response->status());
                
                // Jika API gagal tapi ada stale data, gunakan stale data sebagai fallback
                if ($staleData) {
                    Log::info('Using stale cache data for jobs list due to API failure');
                    return $staleData;
                }
                
                return [];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Timeout atau connection error
            Log::warning('API connection timeout/error for jobs list - ' . $e->getMessage());
            
            // Gunakan stale data jika ada
            if ($staleData) {
                Log::info('Using stale cache data for jobs list due to connection timeout');
                return $staleData;
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching jobs data: ' . $e->getMessage());
            
            // Gunakan stale data jika ada
            if ($staleData) {
                Log::info('Using stale cache data for jobs list due to exception');
                return $staleData;
            }
            
            return [];
        }
    }

    public function createJob($data)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->post("{$this->baseUrl}/api/jobs", $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error creating job: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => 'Failed to create job'];
        }
    }

    public function getJobById($id)
    {
        try {
            Log::info("Mengambil data Job ID: " . $id);

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/jobs/{$id}");

            Log::info("Response dari API: " . $response->body());

            $data = $response->json();

            if (isset($data['data']) && isset($data['data']['id'])) {
                return $data['data'];
            } else {
                Log::error("Job dengan ID {$id} tidak ditemukan.");
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching job: ' . $e->getMessage());
            return null;
        }
    }

    public function updateJob($id, $data)
    {
        try {
            Log::info("Mengirim request update ke API untuk Job ID {$id} dengan data: ", $data);

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->put("{$this->baseUrl}/api/jobs/{$id}", $data);

            Log::info("Response dari API: " . $response->body());

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error updating job: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => 'Failed to update job'];
        }
    }

    public function deleteJob($id)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->delete("{$this->baseUrl}/api/jobs/{$id}");

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error deleting job: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => 'Failed to delete job'];
        }
    }

    public function searchJobs($term)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/jobs-search");

            if ($response->successful()) {
                return $response->json()['data'];
            } else {
                Log::error('API search request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error searching jobs data: ' . $e->getMessage());
            return [];
        }
    }
}
