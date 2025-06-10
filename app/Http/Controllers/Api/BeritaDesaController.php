<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;

class BeritaDesaController extends Controller
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

            $query = BeritaDesa::query();

            // Handle search parameter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            // Handle pagination
            $perPage = $request->input('per_page', 10);
            $berita = $query->latest()->paginate($perPage);

            // Transform data to include gambar_url
            $items = collect($berita->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'komentar' => $item->komentar,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url, // URL lengkap gambar
                    'user_id' => $item->user_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $items,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch berita desa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
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

            $berita = BeritaDesa::findOrFail($id);

            // Format response with gambar_url
            $data = [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'deskripsi' => $berita->deskripsi,
                'komentar' => $berita->komentar,
                'gambar' => $berita->gambar,
                'gambar_url' => $berita->gambar_url, // URL lengkap gambar
                'user_id' => $berita->user_id,
                'created_at' => $berita->created_at,
                'updated_at' => $berita->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita desa tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch berita desa: ' . $e->getMessage()
            ], 500);
        }
    }
}

