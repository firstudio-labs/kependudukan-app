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
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('judul_laporan', 'like', "%{$search}%")
                        ->orWhere('deskripsi_laporan', 'like', "%{$search}%");
                });
            }

            // Handle pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $laporans = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $laporans->items(),
            ]);
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
            ]);

            $data = $request->only([
                'lapor_desa_id',
                'judul_laporan',
                'deskripsi_laporan',
            ]);

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
                $data['gambar'] = $fileName;
            }

            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');

            // Set user_id and village_id
            $data['user_id'] = $tokenOwner->id;
            $data['village_id'] = $tokenOwner->villages_id ?? 1;

            // Set initial status
            $data['status'] = 'Menunggu';

            $laporanDesa = LaporanDesa::create($data);

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
                $laporanDesa->gambar_url = asset('storage/laporan-desa/' . $laporanDesa->gambar);
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
            ]);

            $data = $request->only([
                'lapor_desa_id',
                'judul_laporan',
                'deskripsi_laporan',
            ]);

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
                if ($laporanDesa->gambar && Storage::exists('public/uploads/documents/foto-lapordesa/' . $laporanDesa->gambar)) {
                    Storage::delete('public/uploads/documents/foto-lapordesa/' . $laporanDesa->gambar);
                }

                $file = $request->file('gambar');
                $reportName = Str::slug(substr($request->judul_laporan, 0, 30));
                $timestamp = time();
                $fileName = $timestamp . '_' . $reportName . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/foto-lapordesa', $fileName, 'public');

                // Store only the relative path, not full path
                $data['gambar'] = $fileName;
            }

            $laporanDesa->update($data);

            // Return the updated model with relative path format for gambar
            $updatedLaporan = $laporanDesa->fresh();

            // Format the gambar URL before returning it
            if ($updatedLaporan->gambar) {
                $updatedLaporan->gambar_path = 'uploads/documents/foto-lapordesa/' . $updatedLaporan->gambar;
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
            if ($laporanDesa->gambar && Storage::exists('public/laporan-desa/' . $laporanDesa->gambar)) {
                Storage::delete('public/laporan-desa/' . $laporanDesa->gambar);
            }

            $laporanDesa->delete();

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

}

