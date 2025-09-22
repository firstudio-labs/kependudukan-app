<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangWarungku;
use App\Models\InformasiUsaha;
use App\Models\WarungkuMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarungkuController extends Controller
{
    // Global listing with filters
    public function index(Request $request)
    {
        $query = BarangWarungku::query()->with(['informasiUsaha:id,nama_usaha,province_id,districts_id,sub_districts_id,villages_id,alamat,penduduk_id']);

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('klasifikasi')) {
            $query->whereIn('jenis_master_id', function($sub) use ($request){
                $sub->select('id')->from('warungku_masters')->where('klasifikasi', $request->klasifikasi);
            });
        }
        if ($request->filled('jenis_id')) {
            $query->where('jenis_master_id', $request->jenis_id);
        }

        // wilayah
        $query->when($request->filled('province_id'), fn($q)=>$q->whereHas('informasiUsaha', fn($s)=>$s->where('province_id', $request->province_id)));
        $query->when($request->filled('district_id'), fn($q)=>$q->whereHas('informasiUsaha', fn($s)=>$s->where('districts_id', $request->district_id)));
        $query->when($request->filled('sub_district_id'), fn($q)=>$q->whereHas('informasiUsaha', fn($s)=>$s->where('sub_districts_id', $request->sub_district_id)));
        $query->when($request->filled('village_id'), fn($q)=>$q->whereHas('informasiUsaha', fn($s)=>$s->where('villages_id', $request->village_id)));

        $perPage = (int) $request->input('per_page', 12);
        $items = $query->latest()->paginate($perPage)->withQueryString();

        return response()->json($items);
    }

    // Show one product detail
    public function show(BarangWarungku $barangWarungku)
    {
        $barangWarungku->load('informasiUsaha.penduduk');
        $jenisMaster = WarungkuMaster::select('id','jenis','klasifikasi')->find($barangWarungku->jenis_master_id);
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
                    'province_id' => $barangWarungku->informasiUsaha?->province_id,
                    'districts_id' => $barangWarungku->informasiUsaha?->districts_id,
                    'sub_districts_id' => $barangWarungku->informasiUsaha?->sub_districts_id,
                    'villages_id' => $barangWarungku->informasiUsaha?->villages_id,
                    'pemilik' => [
                        'id' => $barangWarungku->informasiUsaha?->penduduk?->id,
                        'nama_lengkap' => $barangWarungku->informasiUsaha?->penduduk?->nama_lengkap,
                        'no_hp' => $barangWarungku->informasiUsaha?->penduduk?->no_hp,
                    ]
                ]
            ]
        ]);
    }

    // My items (auth penduduk token)
    public function my(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha) return response()->json(['data' => [], 'message' => 'Belum ada informasi usaha'], 200);

        $query = BarangWarungku::where('informasi_usaha_id', $informasiUsaha->id);
        if ($request->filled('search')) $query->where('nama_produk', 'like', '%' . $request->search . '%');
        if ($request->filled('klasifikasi')) {
            $query->whereIn('jenis_master_id', function($sub) use ($request){
                $sub->select('id')->from('warungku_masters')->where('klasifikasi', $request->klasifikasi);
            });
        }
        if ($request->filled('jenis_id')) $query->where('jenis_master_id', $request->jenis_id);

        $items = $query->latest()->paginate((int)$request->input('per_page', 12))->withQueryString();
        return response()->json($items);
    }

    // Create product (owner only)
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
            'foto' => 'nullable|image|max:2048',
        ]);

        $item = new BarangWarungku($validated);
        $item->informasi_usaha_id = $informasiUsaha->id;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('warungku', 'public');
            $item->foto = $path;
        }
        $item->save();

        return response()->json(['message' => 'Produk berhasil dibuat', 'data' => $item], 201);
    }

    // Update product (owner only)
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
            'foto' => 'nullable|image|max:2048',
        ]);

        $barangWarungku->fill($validated);
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('warungku', 'public');
            $barangWarungku->foto = $path;
        }
        $barangWarungku->save();

        return response()->json(['message' => 'Produk berhasil diperbarui', 'data' => $barangWarungku]);
    }

    // Delete product (owner only)
    public function destroy(BarangWarungku $barangWarungku)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();
        if (!$informasiUsaha || $barangWarungku->informasi_usaha_id !== $informasiUsaha->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $barangWarungku->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}


