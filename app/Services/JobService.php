<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/jobs");

            if ($response->successful()) {

                return $response->json()['data'];
            } else {
                Log::error('API request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching jobs data: ' . $e->getMessage());
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

        if (isset($data['id'])) {
            return $data;
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
}
