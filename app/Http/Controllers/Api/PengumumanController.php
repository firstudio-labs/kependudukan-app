<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Services\CitizenService;
use Illuminate\Http\Request;

class PengumumanController extends Controller
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

            $perPage = (int) $request->input('per_page', 10);
            $items = $query->latest()->paginate($perPage);

            $data = collect($items->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'last_page' => $items->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch pengumuman: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id, CitizenService $citizenService)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $pengumuman = Pengumuman::findOrFail($id);

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;
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


