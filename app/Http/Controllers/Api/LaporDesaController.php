<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporDesa;
use App\Models\LaporanDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\CitizenService;

class LaporDesaController extends Controller
{
    protected $citizenService;
    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }
    public function index()
    {
        if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();

        } elseif (Auth::guard('penduduk')->check()) {
            $userData = Auth::guard('penduduk')->user();

            if ($userData) {
                try {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $userData->citizen_data = $citizenData['data'];

                        if (isset($userData->citizen_data['kk'])) {
                            $userData->no_kk = $userData->citizen_data['kk'];
                            $familyData = $this->citizenService->getFamilyMembersByKK($userData->citizen_data['kk']);

                            if ($familyData && isset($familyData['data'])) {
                                $userData->family_members = $familyData['data'];

                                Log::info('Family members retrieved successfully', [
                                    'count' => count($userData->family_members),
                                    'kk' => $userData->citizen_data['kk'],
                                ]);
                            } else {
                                Log::error('Failed to retrieve family members', [
                                    'kk' => $userData->citizen_data['kk'],
                                    'response' => $familyData,
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat mengambil data penduduk.',
                    ], 500);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login to access your profile.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile data retrieved successfully.',
            'data' => $userData,
        ]);
    }

    public function getCategories()
    {
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
            'data' => $categories
        ]);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lapor_desa_id' => 'required|exists:lapor_desas,id',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tag_lat' => 'nullable|numeric',
            'tag_lng' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Debug authentication information
            $isAuthenticated = Auth::guard('penduduk')->check();
            $token = $request->bearerToken();
            
            // Log auth debug info
            \Log::info('Auth Debug:', [
                'is_authenticated' => $isAuthenticated,
                'has_token' => !empty($token),
                'headers' => $request->headers->all()
            ]);
            
            // Modified authentication check - allow the request temporarily for testing
            if (!$isAuthenticated) {
               
                $penduduk = \App\Models\Penduduk::first(); // This is for testing only!
                if ($penduduk) {
                    Auth::guard('penduduk')->login($penduduk);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not authenticated. Please check your API authentication.',
                        'auth_debug' => [
                            'is_authenticated' => $isAuthenticated,
                            'has_token' => !empty($token)
                        ]
                    ], 401);
                }
            }

            $data = [
                'lapor_desa_id' => $request->lapor_desa_id,
                'judul_laporan' => $request->judul_laporan,
                'deskripsi_laporan' => $request->deskripsi_laporan,
                'user_id' => Auth::guard('penduduk')->id(),
                'village_id' => Auth::guard('penduduk')->user()->villages_id ?? 1,
                'status' => 'Menunggu'
            ];

            // Handle tag location from map coordinates
            if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
                $latitude = number_format((float) $request->tag_lat, 6, '.', '');
                $longitude = number_format((float) $request->tag_lng, 6, '.', '');
                $data['tag_lokasi'] = "$latitude, $longitude";
            }

            // Handle image upload
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                $file = $request->file('gambar');
                $fileName = time() . '_' . Str::slug($request->judul_laporan) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/laporan-desa', $fileName);
                $data['gambar'] = $fileName;
            }

            // Create the report
            $laporan = LaporanDesa::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dikirim dan sedang menunggu diproses.',
                'data' => $laporan
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating laporan desa: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function show($id)
    {
        try {
            $laporan = LaporanDesa::with('laporDesa')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $laporan
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in laporan-desa show API: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve report: ' . $e->getMessage()
            ], 404);
        }
    }

    
    public function update(Request $request, $id)
    {
        try {
            $laporanDesa = LaporanDesa::findOrFail($id);

            // Check if the user is authorized to update this report
            if (Auth::guard('penduduk')->check() && $laporanDesa->user_id !== Auth::guard('penduduk')->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action'
                ], 403);
            }

            // Check if the report status allows editing
            if ($laporanDesa->status !== 'Menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya laporan dengan status Menunggu yang dapat diedit'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'lapor_desa_id' => 'required|exists:lapor_desas,id',
                'judul_laporan' => 'required|string|max:255',
                'deskripsi_laporan' => 'required|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tag_lat' => 'nullable|numeric',
                'tag_lng' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

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
            }
            $data['tag_lokasi'] = $tag_lokasi;

            // Handle image upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($laporanDesa->gambar && Storage::exists('public/laporan-desa/' . $laporanDesa->gambar)) {
                    Storage::delete('public/laporan-desa/' . $laporanDesa->gambar);
                }

                $file = $request->file('gambar');
                $fileName = time() . '_' . Str::slug($request->judul_laporan) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/laporan-desa', $fileName);
                $data['gambar'] = $fileName;
            }

            $laporanDesa->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil diperbarui',
                'data' => $laporanDesa
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $laporanDesa = LaporanDesa::findOrFail($id);

            // Check if the user is authorized to delete this report
            if (Auth::guard('penduduk')->check() && $laporanDesa->user_id !== Auth::guard('penduduk')->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action'
                ], 403);
            }

            // Check if the report status allows deletion
            if ($laporanDesa->status !== 'Menunggu') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya laporan dengan status Menunggu yang dapat dihapus'
                ], 422);
            }

            // Delete image if exists
            if ($laporanDesa->gambar && Storage::exists('public/laporan-desa/' . $laporanDesa->gambar)) {
                Storage::delete('public/laporan-desa/' . $laporanDesa->gambar);
            }

            $laporanDesa->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete report: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function adminIndex(Request $request)
    // {
    //     try {
    //         $query = LaporanDesa::query();

    //         // Admin desa only sees reports from their village
    //         if (Auth::user() && Auth::user()->role === 'admin desa') {
    //             $query->where('village_id', Auth::user()->villages_id);
    //         }

    //         if ($request->has('search') && !empty($request->search)) {
    //             $search = $request->search;
    //             $query->where(function ($q) use ($search) {
    //                 $q->where('judul_laporan', 'like', "%{$search}%")
    //                     ->orWhere('deskripsi_laporan', 'like', "%{$search}%");
    //             });
    //         }

    //         $perPage = $request->get('per_page', 10);
    //         $laporans = $query->with(['laporDesa', 'user'])
    //             ->orderBy('created_at', 'desc')
    //             ->paginate($perPage);

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $laporans->items(),
    //             'meta' => [
    //                 'current_page' => $laporans->currentPage(),
    //                 'last_page' => $laporans->lastPage(),
    //                 'per_page' => $laporans->perPage(),
    //                 'total' => $laporans->total()
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve reports: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function updateStatus(Request $request, $id)
    {
        try {
            $laporan = LaporanDesa::findOrFail($id);

            // Only admin desa from the same village can update status
            if (Auth::user()->role === 'admin desa' && $laporan->village_id != Auth::user()->villages_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $laporan->update([
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status laporan berhasil diperbarui',
                'data' => $laporan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

}

