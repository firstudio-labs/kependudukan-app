<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiAuthService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kependudukan.url');
        $this->apiKey = config('services.kependudukan.key');
    }

    /**
     * Make an authenticated request to the API
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array|null Response data or null on error
     */
    public function request($method, $endpoint, $data = [])
    {
        try {
            $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

            Log::info("Making {$method} request to: {$url}");

            $response = Http::withHeaders([
                        'X-API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->$method($url, $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("API request failed: {$response->status()}", [
                    'url' => $url,
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error("API request exception: {$e->getMessage()}", [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Login a user via API
     *
     * @param string $nik User NIK
     * @param string $password User password
     * @return array|null Auth response or null on failure
     */
    public function login($nik, $password)
    {
        return $this->request('post', 'auth/login', [
            'nik' => $nik,
            'password' => $password
        ]);
    }

    /**
     * Register a new user via API
     *
     * @param array $userData User registration data
     * @return array|null Registration response or null on failure
     */
    public function register($userData)
    {
        return $this->request('post', 'auth/register', $userData);
    }
}