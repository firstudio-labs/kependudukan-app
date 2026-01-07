<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PreloadCacheJob;
use App\Models\Pengumuman;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PengumumanController extends Controller
{
    protected $cacheStore;
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
        $this->cacheStore = $this->getCacheStore();
    }

    /**
     * Get cache store - prefer Redis jika tersedia, fallback ke default
     */
    private function getCacheStore()
    {
        try {
            if (config('cache.default') === 'redis' || config('cache.stores.redis')) {
                return Cache::store('redis');
            }
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia, menggunakan default cache: ' . $e->getMessage());
        }
        return Cache::store(config('cache.default', 'file'));
    }

    public function index(Request $request)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $this->citizenService->getCitizenByNIK($nik) : null;
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

            // Build cache key
            $search = $request->input('search', '');
            $perPage = (int) $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $cacheKey = "pengumuman_index_{$villageId}_{$search}_{$perPage}_{$page}";
            $useCache = !$request->has('refresh'); // Support ?refresh=1 untuk bypass cache

            // Cek cache terlebih dahulu menggunakan cache store optimal
            if ($useCache && $this->cacheStore->has($cacheKey)) {
                return response()->json($this->cacheStore->get($cacheKey), 200);
            }

            // Query dengan eager loading untuk konsistensi dengan web controller
            $query = Pengumuman::query();
            
            if ($villageId) {
                $query->where('villages_id', (int) $villageId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('judul', 'like', "%{$s}%")
                      ->orWhere('deskripsi', 'like', "%{$s}%");
                });
            }

            $items = $query->latest()->paginate($perPage);

            $data = collect($items->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url,
                    'user_id' => $item->user_id,
                    'province_id' => $item->province_id,
                    'districts_id' => $item->districts_id,
                    'sub_districts_id' => $item->sub_districts_id,
                    'villages_id' => $item->villages_id,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            $response = [
                'status' => 'success',
                'data' => $data,
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'last_page' => $items->lastPage(),
                ]
            ];

            // Cache hasil secara permanen menggunakan cache store optimal (hanya di-clear saat ada perubahan data)
            $this->cacheStore->forever($cacheKey, $response);
            
            // Simpan cache key ke daftar untuk memudahkan clearing
            if ($villageId) {
                $cacheKeysList = $this->cacheStore->get("pengumuman_cache_keys_{$villageId}", []);
                if (!in_array($cacheKey, $cacheKeysList)) {
                    $cacheKeysList[] = $cacheKey;
                    $this->cacheStore->forever("pengumuman_cache_keys_{$villageId}", $cacheKeysList);
                }
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch pengumuman: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Clear cache untuk pengumuman berdasarkan village_id
     */
    private function clearPengumumanCache($villageId)
    {
        if (!$villageId) return;
        
        // Simpan daftar cache keys yang perlu di-clear
        $cacheKeysList = $this->cacheStore->get("pengumuman_cache_keys_{$villageId}", []);
        
        // Clear semua cache keys yang tersimpan
        foreach ($cacheKeysList as $key) {
            $this->cacheStore->forget($key);
        }
        
        // Reset daftar cache keys
        $this->cacheStore->forget("pengumuman_cache_keys_{$villageId}");

        PreloadCacheJob::dispatch('pengumuman', (int) $villageId)->delay(now()->addSeconds(5));
    }

    public function show(Request $request, $id)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $pengumuman = Pengumuman::findOrFail($id);

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $this->citizenService->getCitizenByNIK($nik) : null;
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

            if ($villageId && (string) $pengumuman->villages_id !== (string) $villageId) {
                return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
            }

            return response()->json(['status' => 'success', 'data' => [
                'id' => $pengumuman->id,
                'judul' => $pengumuman->judul,
                'deskripsi' => $pengumuman->deskripsi,
                'gambar' => $pengumuman->gambar,
                'gambar_url' => $pengumuman->gambar_url,
                'user_id' => $pengumuman->user_id,
                'province_id' => $pengumuman->province_id,
                'districts_id' => $pengumuman->districts_id,
                'sub_districts_id' => $pengumuman->sub_districts_id,
                'villages_id' => $pengumuman->villages_id,
                'status' => $pengumuman->status,
                'created_at' => $pengumuman->created_at,
                'updated_at' => $pengumuman->updated_at,
            ]], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Pengumuman tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch pengumuman: ' . $e->getMessage()], 500);
        }
    }
}


