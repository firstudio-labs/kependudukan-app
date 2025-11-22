<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgendaDesa;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AgendaDesaController extends Controller
{
    public function index(Request $request, CitizenService $citizenService)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

            // Build cache key
            $search = $request->input('search', '');
            $perPage = (int) $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $cacheKey = "agenda_desa_index_{$villageId}_{$search}_{$perPage}_{$page}";
            $useCache = !$request->has('refresh'); // Support ?refresh=1 untuk bypass cache

            // Cek cache terlebih dahulu
            if ($useCache && Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey), 200);
            }

            $query = AgendaDesa::query();
            if ($villageId) {
                $query->where('villages_id', (int) $villageId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('judul', 'like', "%{$s}%")
                      ->orWhere('deskripsi', 'like', "%{$s}%")
                      ->orWhere('alamat', 'like', "%{$s}%");
                });
            }

            $items = $query->latest()->paginate($perPage);

            $data = collect($items->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'alamat' => $item->alamat,
                    'tag_lokasi' => $item->tag_lokasi,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url,
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

            // Cache hasil secara permanen (hanya di-clear saat ada perubahan data)
            Cache::forever($cacheKey, $response);
            
            // Simpan cache key ke daftar untuk memudahkan clearing
            if ($villageId) {
                $cacheKeysList = Cache::get("agenda_desa_cache_keys_{$villageId}", []);
                if (!in_array($cacheKey, $cacheKeysList)) {
                    $cacheKeysList[] = $cacheKey;
                    Cache::forever("agenda_desa_cache_keys_{$villageId}", $cacheKeysList);
                }
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch agenda desa: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Clear cache untuk agenda desa berdasarkan village_id
     */
    private function clearAgendaDesaCache($villageId)
    {
        if (!$villageId) return;
        
        // Simpan daftar cache keys yang perlu di-clear
        $cacheKeysList = Cache::get("agenda_desa_cache_keys_{$villageId}", []);
        
        // Clear semua cache keys yang tersimpan
        foreach ($cacheKeysList as $key) {
            Cache::forget($key);
        }
        
        // Reset daftar cache keys
        Cache::forget("agenda_desa_cache_keys_{$villageId}");
    }

    public function show(Request $request, $id, CitizenService $citizenService)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $agendaDesa = AgendaDesa::findOrFail($id);

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

            if ($villageId && (string) $agendaDesa->villages_id !== (string) $villageId) {
                return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
            }

            return response()->json(['status' => 'success', 'data' => [
                'id' => $agendaDesa->id,
                'judul' => $agendaDesa->judul,
                'deskripsi' => $agendaDesa->deskripsi,
                'alamat' => $agendaDesa->alamat,
                'tag_lokasi' => $agendaDesa->tag_lokasi,
                'gambar' => $agendaDesa->gambar,
                'gambar_url' => $agendaDesa->gambar_url,
                'created_at' => $agendaDesa->created_at,
                'updated_at' => $agendaDesa->updated_at,
            ]], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Agenda desa tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch agenda desa: ' . $e->getMessage()], 500);
        }
    }
}
