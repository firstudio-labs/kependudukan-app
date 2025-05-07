<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporDesa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class LaporDesaController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Verify API key from request header
            $providedApiKey = $request->header('X-API-Key');
            $validApiKey = Config::get('services.kependudukan.key');

            if (!$providedApiKey || $providedApiKey !== $validApiKey) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Invalid API key.'
                ], 401);
            }

            // Build the query
            $query = LaporDesa::query();

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ruang_lingkup', 'like', "%{$search}%")
                        ->orWhere('bidang', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            // Get the data with pagination
            $perPage = $request->input('per_page', 10);
            $lapordesas = $query->orderBy('id', 'desc')
                ->paginate($perPage);

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'data' => $lapordesas->items(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve lapor desa data: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
