<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Klasifikasi;
use Illuminate\Support\Facades\Log;

class KlasifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Check API key in header against the one in .env
        $apiKey = $request->header('X-API-Key');
        $configApiKey = config('services.kependudukan.key');

        if (!$apiKey || $apiKey !== $configApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid API key.'
            ], 401);
        }

        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $query = Klasifikasi::query();

            if ($search) {
                $query->where('kode', 'LIKE', "%{$search}%")
                    ->orWhere('jenis_klasifikasi', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%");
            }

            // Get all data if requested
            if ($request->has('all') && $request->input('all') == true) {
                $klasifikasi = $query->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $klasifikasi
                ]);
            }

            // Otherwise paginate
            $klasifikasi = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $klasifikasi->items(),
                
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching klasifikasi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data: ' . $e->getMessage()
            ], 500);
        }
    }
}
