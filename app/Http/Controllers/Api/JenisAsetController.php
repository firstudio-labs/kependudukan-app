<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisAset;
use Illuminate\Support\Facades\Log;

class JenisAsetController extends Controller
{
    public function index(Request $request)
    {
        // Validate API key
        $apiKey = $request->header('X-API-Key');
        $validApiKey = config('services.kependudukan.key');

        if ($apiKey !== $validApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid API Key.'
            ], 401);
        }

        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);

            $query = JenisAset::query();

            if ($search) {
                $query->where('kode', 'LIKE', "%{$search}%")
                    ->orWhere('jenis_aset', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%");
            }

            $jenisAset = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $jenisAset->items(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching jenis aset data: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch jenis aset data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
