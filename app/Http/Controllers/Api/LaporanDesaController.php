<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\LaporanDesa;
use Illuminate\Support\Facades\Cache;

class LaporanDesaController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $tokenOwnerType = $request->attributes->get('token_owner_type');
            $tokenOwnerRole = $request->attributes->get('token_owner_role');

            // Build cache key berdasarkan user dan filter
            $userId = $tokenOwner->id;
            $villageId = $tokenOwner->villages_id ?? null;
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            // Cache key berbeda untuk penduduk vs admin
            if ($tokenOwnerType === 'penduduk') {
                $cacheKey = "laporan_desa_index_user_{$userId}_{$search}_{$perPage}_{$page}";
            } else {
                $cacheKey = "laporan_desa_index_village_{$villageId}_{$search}_{$perPage}_{$page}";
            }
            
            $useCache = !$request->has('refresh'); // Support ?refresh=1 untuk bypass cache

            // Cek cache terlebih dahulu
            if ($useCache && Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey), 200);
            }

            $query = LaporanDesa::query();

            // Filter based on user role and type
            if ($tokenOwnerType === 'penduduk') {
                // Regular users only see their own reports
                $query->where('user_id', $tokenOwner->id);
            } elseif ($tokenOwnerType === 'user' && $tokenOwnerRole === 'admin desa') {
                // Admin desa only see reports from their village
                $query->where('village_id', $tokenOwner->villages_id);
            }

            // Handle search parameter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul_laporan', 'like', "%{$search}%")
                        ->orWhere('deskripsi_laporan', 'like', "%{$search}%");
                });
            }

            // Handle pagination
            $laporans = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Add gambar_url to each laporan item
            $items = collect($laporans->items())->map(function ($laporan) {
                if ($laporan->gambar) {
                    $laporan->gambar_url = '/storage/' . $laporan->gambar;
                }
                return $laporan;
            });

            $response = [
                'status' => 'success',
                'data' => $items,
            ];

            // Cache hasil secara permanen (hanya di-clear saat ada perubahan data)
            Cache::forever($cacheKey, $response);
            
            // Simpan cache key ke daftar untuk memudahkan clearing
            if ($tokenOwnerType === 'penduduk') {
                $cacheKeysList = Cache::get("laporan_desa_cache_keys_user_{$userId}", []);
                if (!in_array($cacheKey, $cacheKeysList)) {
                    $cacheKeysList[] = $cacheKey;
                    Cache::forever("laporan_desa_cache_keys_user_{$userId}", $cacheKeysList);
                }
            } elseif ($villageId) {
                $cacheKeysList = Cache::get("laporan_desa_cache_keys_village_{$villageId}", []);
                if (!in_array($cacheKey, $cacheKeysList)) {
                    $cacheKeysList[] = $cacheKey;
                    Cache::forever("laporan_desa_cache_keys_village_{$villageId}", $cacheKeysList);
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reports: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        try {
            $categories = [];
            $laporDesas = LaporDesa::all()->groupBy('ruang_lingkup');

            foreach ($laporDesas as $ruangLingkup => $items) {
                $categories[$ruangLingkup] = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'bidang' => $item->bidang
                    ];
                })->toArray();
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'categories' => $categories
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in API LaporDesa create: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load form data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'lapor_desa_id' => 'required|exists:lapor_desas,id',
                'judul_laporan' => 'required|string|max:255',
                'deskripsi_laporan' => 'required|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tag_lat' => 'nullable|numeric',
                'tag_lng' => 'nullable|numeric',
                'location_address' => 'nullable|string|max:500',
            ]);

            $data = $request->only([
                'lapor_desa_id',
                'judul_laporan',
                'deskripsi_laporan',
            ]);

            // Alamat dari peta (editable user sebelum submit)
            if ($request->filled('location_address')) {
                $data['lokasi'] = trim($request->input('location_address'));
            }

            // Handle tag location from map coordinates
            $tag_lokasi = null;
            if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
                $latitude = number_format((float) $request->tag_lat, 6, '.', '');
                $longitude = number_format((float) $request->tag_lng, 6, '.', '');
                $tag_lokasi = "$latitude, $longitude";
            } elseif ($request->filled('tag_lokasi')) {
                // Use the directly provided tag_lokasi value
                $tag_lokasi = $request->tag_lokasi;
            }
            $data['tag_lokasi'] = $tag_lokasi;

            // Handle image upload
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $reportName = Str::slug(substr($request->judul_laporan, 0, 30));
                $timestamp = time();
                $fileName = $timestamp . '_' . $reportName . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/foto-lapordesa', $fileName, 'public');
                $data['gambar'] = $path;
            }

            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');

            // Set user_id and village_id
            $data['user_id'] = $tokenOwner->id;

            // Get the citizen data from the service to properly retrieve village_id
            $pendudukNIK = $tokenOwner->nik;
            $citizenService = app(\App\Services\CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($pendudukNIK);

            if ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['villages_id'])) {
                // Use village_id from citizen profile data
                $data['village_id'] = $citizenData['data']['villages_id'];
            } elseif ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['village_id'])) {
                // Alternative field name
                $data['village_id'] = $citizenData['data']['village_id'];
            } else {
                // Fallback to user's villages_id if available, or default to 1
                $data['village_id'] = $tokenOwner->villages_id ?? 1;
            }

            // Set initial status
            $data['status'] = 'Menunggu';

            $laporanDesa = LaporanDesa::create($data);

            // Clear cache untuk user dan village
            $this->clearLaporanDesaCache($tokenOwner->id, $data['village_id'] ?? null);

            // Add gambar_url if image exists
            if ($laporanDesa->gambar) {
                $laporanDesa->gambar_url = '/storage/' . $laporanDesa->gambar;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dikirim dan sedang menunggu diproses.',
                'data' => $laporanDesa
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in API LaporDesa store: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $laporanDesa = LaporanDesa::findOrFail($id);

            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');

            // Check permissions
            if ($laporanDesa->user_id !== $tokenOwner->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to edit this report'
                ], 403);
            }

            if ($laporanDesa->status !== 'Menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya laporan dengan status Menunggu yang dapat diedit.'
                ], 400);
            }

            $categories = [];
            $laporDesas = LaporDesa::all()->groupBy('ruang_lingkup');

            foreach ($laporDesas as $ruangLingkup => $items) {
                $categories[$ruangLingkup] = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'bidang' => $item->bidang
                    ];
                })->toArray();
            }

            // Parse coordinates from tag_lokasi
            $lat = '';
            $lng = '';
            if (!empty($laporanDesa->tag_lokasi)) {
                $coordinates = explode(',', $laporanDesa->tag_lokasi);
                if (count($coordinates) >= 2) {
                    $lat = trim($coordinates[0]);
                    $lng = trim($coordinates[1]);
                }
            }

            $selectedRuangLingkup = $laporanDesa->laporDesa->ruang_lingkup ?? null;

            // Add image URL if exists
            if ($laporanDesa->gambar) {
                $laporanDesa->gambar_url = '/storage/' . $laporanDesa->gambar;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'laporan' => $laporanDesa,
                    'categories' => $categories,
                    'selectedRuangLingkup' => $selectedRuangLingkup,
                    'coordinates' => [
                        'lat' => $lat,
                        'lng' => $lng
                    ]
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Report not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in API LaporDesa edit: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve report data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $laporanDesa = LaporanDesa::findOrFail($id);

            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');

            // Check permissions
            if ($laporanDesa->user_id !== $tokenOwner->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to update this report'
                ], 403);
            }

            if ($laporanDesa->status !== 'Menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya laporan dengan status Menunggu yang dapat diedit.'
                ], 400);
            }

            $validated = $request->validate([
                'lapor_desa_id' => 'required|exists:lapor_desas,id',
                'judul_laporan' => 'required|string|max:255',
                'deskripsi_laporan' => 'required|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tag_lat' => 'nullable|numeric',
                'tag_lng' => 'nullable|numeric',
                'location_address' => 'nullable|string|max:500',
            ]);

            $data = $request->only([
                'lapor_desa_id',
                'judul_laporan',
                'deskripsi_laporan',
            ]);

            // Alamat dari peta (editable user)
            if ($request->filled('location_address')) {
                $data['lokasi'] = trim($request->input('location_address'));
            }

            // Handle tag location from map coordinates
            $tag_lokasi = null;
            if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
                $latitude = number_format((float) $request->tag_lat, 6, '.', '');
                $longitude = number_format((float) $request->tag_lng, 6, '.', '');
                $tag_lokasi = "$latitude, $longitude";
            } elseif ($request->filled('tag_lokasi')) {
                // Use the directly provided tag_lokasi value
                $tag_lokasi = $request->tag_lokasi;
            }
            $data['tag_lokasi'] = $tag_lokasi;

            if ($request->hasFile('gambar')) {
                // Delete previous image if exists
                if ($laporanDesa->gambar && Storage::exists('public/' . $laporanDesa->gambar)) {
                    Storage::delete('public/' . $laporanDesa->gambar);
                }

                $file = $request->file('gambar');
                $reportName = Str::slug(substr($request->judul_laporan, 0, 30));
                $timestamp = time();
                $fileName = $timestamp . '_' . $reportName . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/foto-lapordesa', $fileName, 'public');

                // Store the full relative path
                $data['gambar'] = $path;
            }

            // Update village_id from citizen profile data
            $pendudukNIK = $tokenOwner->nik;
            $citizenService = app(\App\Services\CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($pendudukNIK);

            if ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['villages_id'])) {
                // Use village_id from citizen profile data
                $data['village_id'] = $citizenData['data']['villages_id'];
            } elseif ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['village_id'])) {
                // Alternative field name
                $data['village_id'] = $citizenData['data']['village_id'];
            }

            $laporanDesa->update($data);

            // Clear cache untuk user dan village
            $this->clearLaporanDesaCache($tokenOwner->id, $laporanDesa->village_id);

            // Return the updated model with relative path format for gambar
            $updatedLaporan = $laporanDesa->fresh();

            // Format the gambar URL before returning it
            if ($updatedLaporan->gambar) {
                $updatedLaporan->gambar_url = '/storage/' . $updatedLaporan->gambar;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil diperbarui.',
                'data' => $updatedLaporan
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Report not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in API LaporDesa update: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $laporanDesa = LaporanDesa::findOrFail($id);

            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');

            // Check permissions
            if ($laporanDesa->user_id !== $tokenOwner->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to delete this report'
                ], 403);
            }

            if ($laporanDesa->status !== 'Menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya laporan dengan status Menunggu yang dapat dihapus.'
                ], 400);
            }

            // Delete image if exists
            if ($laporanDesa->gambar && Storage::exists('public/' . $laporanDesa->gambar)) {
                Storage::delete('public/' . $laporanDesa->gambar);
            }

            $userId = $laporanDesa->user_id;
            $villageId = $laporanDesa->village_id;
            
            $laporanDesa->delete();

            // Clear cache untuk user dan village
            $this->clearLaporanDesaCache($userId, $villageId);

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Report not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in API LaporDesa destroy: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cache untuk laporan desa berdasarkan user_id dan village_id
     */
    private function clearLaporanDesaCache($userId, $villageId = null)
    {
        // Clear cache untuk user
        $userCacheKeys = Cache::get("laporan_desa_cache_keys_user_{$userId}", []);
        foreach ($userCacheKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget("laporan_desa_cache_keys_user_{$userId}");

        // Clear cache untuk village (jika ada)
        if ($villageId) {
            $villageCacheKeys = Cache::get("laporan_desa_cache_keys_village_{$villageId}", []);
            foreach ($villageCacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget("laporan_desa_cache_keys_village_{$villageId}");
        }
    }

}

