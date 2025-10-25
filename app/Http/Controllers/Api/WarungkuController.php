<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangWarungku;
use App\Models\InformasiUsaha;
use App\Models\WarungkuMaster;
use App\Services\ImageConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WilayahService;

class WarungkuController extends Controller
{
    // Global listing with filters - sama dengan User controller
    public function index(Request $request)
    {
        $query = BarangWarungku::query()->with(['informasiUsaha.penduduk']);

        // Hanya tampilkan produk dari toko yang aktif (untuk penduduk)
        $query->whereHas('informasiUsaha', function ($q) {
            $q->where('status', 'aktif');
        });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('nama_produk', 'like', "%{$s}%");
        }

        if ($request->filled('province_id')) {
            $query->whereHas('informasiUsaha', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }
        if ($request->filled('district_id')) {
            $query->whereHas('informasiUsaha', function ($q) use ($request) {
                $q->where('districts_id', $request->district_id);
            });
        }
        if ($request->filled('sub_district_id')) {
            $query->whereHas('informasiUsaha', function ($q) use ($request) {
                $q->where('sub_districts_id', $request->sub_district_id);
            });
        }
        if ($request->filled('village_id')) {
            $query->whereHas('informasiUsaha', function ($q) use ($request) {
                $q->where('villages_id', $request->village_id);
            });
        }

        if ($request->filled('klasifikasi')) {
            // Filter berdasarkan nilai enum klasifikasi (barang/jasa) di tabel master
            $query->whereIn('jenis_master_id', function($sub) use ($request){
                $sub->select('id')->from('warungku_masters')->where('klasifikasi', $request->klasifikasi);
            });
        }
        if ($request->filled('jenis_id')) {
            $query->where('jenis_master_id', $request->jenis_id);
        }

        $perPage = (int) $request->input('per_page', 12);
        $items = $query->latest()->paginate($perPage)->withQueryString();

        // Tambahkan data klasifikasi dan jenis untuk filter
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();

        return response()->json([
            'data' => $items,
            'filters' => [
                'klasifikasi' => $klass,
                'jenis' => $jenis
            ]
        ]);
    }

    // Edit payload (owner only): data produk + opsi dropdown
    public function edit(Request $request, BarangWarungku $barangWarungku)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha || $barangWarungku->informasi_usaha_id !== $informasiUsaha->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $barangWarungku->load('informasiUsaha');
        $jenisMaster = WarungkuMaster::select('id','jenis','klasifikasi')->find($barangWarungku->jenis_master_id);

        // Opsi dropdown
        $klasifikasiOptions = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenisOptions = WarungkuMaster::select('id','jenis','klasifikasi')->orderBy('klasifikasi')->orderBy('jenis')->get();

        return response()->json([
            'data' => [
                'id' => $barangWarungku->id,
                'nama_produk' => $barangWarungku->nama_produk,
                'harga' => $barangWarungku->harga,
                'stok' => $barangWarungku->stok,
                'deskripsi' => $barangWarungku->deskripsi,
                'foto_url' => $barangWarungku->foto_url,
                'jenis_master_id' => $barangWarungku->jenis_master_id,
                'jenis' => $jenisMaster?->jenis,
                'klasifikasi' => $jenisMaster?->klasifikasi,
            ],
            'filters' => [
                'klasifikasi' => $klasifikasiOptions,
                'jenis' => $jenisOptions,
            ]
        ]);
    }

    // Show one product detail
    public function show(BarangWarungku $barangWarungku)
    {
        $barangWarungku->load('informasiUsaha.penduduk');
        $jenisMaster = WarungkuMaster::select('id','jenis','klasifikasi')->find($barangWarungku->jenis_master_id);
        
        // Hitung statistik produk dari toko yang sama
        $informasiUsahaId = $barangWarungku->informasi_usaha_id;
        $totalProducts = BarangWarungku::where('informasi_usaha_id', $informasiUsahaId)->count();
        $availableProducts = BarangWarungku::where('informasi_usaha_id', $informasiUsahaId)->where('stok', '>', 0)->count();
        $outOfStockProducts = BarangWarungku::where('informasi_usaha_id', $informasiUsahaId)->where('stok', '=', 0)->count();
        
        return response()->json([
            'data' => [
                'id' => $barangWarungku->id,
                'nama_produk' => $barangWarungku->nama_produk,
                'harga' => $barangWarungku->harga,
                'stok' => $barangWarungku->stok,
                'deskripsi' => $barangWarungku->deskripsi,
                'foto_url' => $barangWarungku->foto_url,
                'jenis' => $jenisMaster?->jenis,
                'klasifikasi' => $jenisMaster?->klasifikasi,
                'informasi_usaha' => [
                    'id' => $barangWarungku->informasiUsaha?->id,
                    'nama_usaha' => $barangWarungku->informasiUsaha?->nama_usaha,
                    'alamat' => $barangWarungku->informasiUsaha?->alamat,
                    'tag_lokasi' => $barangWarungku->informasiUsaha?->tag_lokasi,
                    'foto_url' => $barangWarungku->informasiUsaha?->foto_url,
                    'province_id' => $barangWarungku->informasiUsaha?->province_id,
                    'districts_id' => $barangWarungku->informasiUsaha?->districts_id,
                    'sub_districts_id' => $barangWarungku->informasiUsaha?->sub_districts_id,
                    'villages_id' => $barangWarungku->informasiUsaha?->villages_id,
                    'pemilik' => [
                        'id' => $barangWarungku->informasiUsaha?->penduduk?->id,
                        'nama_lengkap' => $barangWarungku->informasiUsaha?->penduduk?->nama_lengkap,
                        'no_hp' => $barangWarungku->informasiUsaha?->penduduk?->no_hp,
                    ]
                ],
                'statistik_toko' => [
                    'total_produk' => $totalProducts,
                    'produk_tersedia' => $availableProducts,
                    'produk_habis' => $outOfStockProducts
                ]
            ]
        ]);
    }

    // My items (auth penduduk token) - sama dengan User controller
    public function my(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha) return response()->json(['data' => [], 'message' => 'Belum ada informasi usaha'], 200);

        $query = BarangWarungku::where('informasi_usaha_id', $informasiUsaha->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('nama_produk', 'like', "%{$s}%");
        }

        if ($request->filled('klasifikasi')) {
            $query->whereIn('jenis_master_id', function($sub) use ($request){
                $sub->select('id')->from('warungku_masters')->where('klasifikasi', $request->klasifikasi);
            });
        }
        if ($request->filled('jenis_id')) {
            $query->where('jenis_master_id', $request->jenis_id);
        }

        $items = $query->latest()->paginate((int)$request->input('per_page', 12))->withQueryString();
        
        // Tambahkan data klasifikasi dan jenis untuk filter
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();

        return response()->json([
            'data' => $items,
            'informasi_usaha' => $informasiUsaha,
            'filters' => [
                'klasifikasi' => $klass,
                'jenis' => $jenis
            ]
        ]);
    }

    // Create product (owner only) - sama dengan User controller
    public function store(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha) return response()->json(['message' => 'Informasi usaha belum ada'], 400);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_master_id' => 'nullable|integer|exists:warungku_masters,id',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:10240', // 10MB max, akan di-resize dan konversi ke WebP
        ]);

        $item = new BarangWarungku($validated);
        $item->informasi_usaha_id = $informasiUsaha->id;
        if ($request->hasFile('foto')) {
            // Convert to WebP format with optimized dimensions (max 800x600)
            $path = ImageConverterService::convertToWebP($request->file('foto'), 'warungku', 85, 800, 600);
            if ($path) {
                $item->foto = $path;
            }
        }
        $item->save();

        return response()->json(['message' => 'Produk berhasil dibuat', 'data' => $item], 201);
    }

    // Update product (owner only) - sama dengan User controller
    public function update(Request $request, BarangWarungku $barangWarungku)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha || $barangWarungku->informasi_usaha_id !== $informasiUsaha->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_master_id' => 'nullable|integer|exists:warungku_masters,id',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:10240', // 10MB max, akan di-resize dan konversi ke WebP
        ]);

        $barangWarungku->fill($validated);
        if ($request->hasFile('foto')) {
            // Delete old image if exists
            if ($barangWarungku->foto) {
                ImageConverterService::deleteImage($barangWarungku->foto);
            }
            
            // Convert to WebP format with optimized dimensions (max 800x600)
            $path = ImageConverterService::convertToWebP($request->file('foto'), 'warungku', 85, 800, 600);
            if ($path) {
                $barangWarungku->foto = $path;
            }
        }
        $barangWarungku->save();

        return response()->json(['message' => 'Produk berhasil diperbarui', 'data' => $barangWarungku]);
    }

    // Delete product (owner only) - sama dengan User controller
    public function destroy(Request $request, BarangWarungku $barangWarungku)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha || $barangWarungku->informasi_usaha_id !== $informasiUsaha->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Delete associated image file
        if ($barangWarungku->foto) {
            ImageConverterService::deleteImage($barangWarungku->foto);
        }

        $barangWarungku->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    // Dropdown filters: klasifikasi & jenis
    public function filters()
    {
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->orderBy('klasifikasi')->orderBy('jenis')->get();

        return response()->json([
            'klasifikasi' => $klass,
            'jenis' => $jenis,
        ]);
    }

    // Wilayah options via WilayahService
    public function wilayahProvinces(WilayahService $wilayahService)
    {
        return response()->json(['data' => $wilayahService->getProvinces()]);
    }

    public function wilayahDistricts(WilayahService $wilayahService, $provinceCode)
    {
        return response()->json(['data' => $wilayahService->getKabupaten($provinceCode)]);
    }

    public function wilayahSubDistricts(WilayahService $wilayahService, $districtCode)
    {
        return response()->json(['data' => $wilayahService->getKecamatan($districtCode)]);
    }

    public function wilayahVillages(WilayahService $wilayahService, $subDistrictCode)
    {
        return response()->json(['data' => $wilayahService->getDesa($subDistrictCode)]);
    }

    // API Wilayah untuk Filter - berdasarkan ID
    public function getDistrictsByProvince(Request $request, WilayahService $wilayahService)
    {
        $provinceId = $request->get('province_id');
        if (!$provinceId) {
            return response()->json(['data' => []]);
        }

        $provinces = $wilayahService->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        
        if (!$province) {
            return response()->json(['data' => []]);
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        return response()->json($districts);
    }

    public function getSubDistrictsByDistrict(Request $request, WilayahService $wilayahService)
    {
        $districtId = $request->get('district_id');
        if (!$districtId) {
            return response()->json(['data' => []]);
        }

        $provinces = $wilayahService->getProvinces();
        foreach ($provinces as $province) {
            $districts = $wilayahService->getKabupaten($province['code']);
            $district = collect($districts)->firstWhere('id', $districtId);
            if ($district) {
                $subDistricts = $wilayahService->getKecamatan($district['code']);
                return response()->json($subDistricts);
            }
        }

        return response()->json(['data' => []]);
    }

    public function getVillagesBySubDistrict(Request $request, WilayahService $wilayahService)
    {
        $subDistrictId = $request->get('sub_district_id');
        if (!$subDistrictId) {
            return response()->json(['data' => []]);
        }

        $provinces = $wilayahService->getProvinces();
        foreach ($provinces as $province) {
            $districts = $wilayahService->getKabupaten($province['code']);
            foreach ($districts as $district) {
                $subDistricts = $wilayahService->getKecamatan($district['code']);
                $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
                if ($subDistrict) {
                    $villages = $wilayahService->getDesa($subDistrict['code']);
                    return response()->json($villages);
                }
            }
        }

        return response()->json(['data' => []]);
    }

    // Dropdown khusus form: klasifikasi list
    public function klasifikasiList()
    {
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        return response()->json(['data' => $klass]);
    }

    // Dropdown khusus form: jenis by klasifikasi
    public function jenisByKlasifikasi(Request $request)
    {
        $request->validate([
            'klasifikasi' => 'required|in:barang,jasa',
        ]);

        $items = WarungkuMaster::select('id','jenis','klasifikasi')
            ->where('klasifikasi', $request->klasifikasi)
            ->orderBy('jenis')
            ->get();

        return response()->json(['data' => $items]);
    }
}