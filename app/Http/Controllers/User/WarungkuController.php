<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BarangWarungku;
use App\Models\InformasiUsaha;
use App\Models\WarungkuMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarungkuController extends Controller
{
    // Global view
    public function index(Request $request)
    {
        $query = BarangWarungku::query()->with(['informasiUsaha.penduduk']);

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

        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();

        return view('user.warungku.index', compact('items', 'klass', 'jenis'));
    }

    // My items list
    public function my(Request $request)
    {
        $user = Auth::guard('penduduk')->user();
        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->first();

        $items = collect();
        if ($informasiUsaha) {
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

            $items = $query->latest()->paginate(12)->withQueryString();
        }

        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();

        return view('user.warungku.my', compact('items', 'informasiUsaha', 'klass', 'jenis'));
    }

    public function create()
    {
        $user = Auth::guard('penduduk')->user();
        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->firstOrFail();
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();
        return view('user.warungku.form', [
            'informasiUsaha' => $informasiUsaha,
            'item' => null,
            'klass' => $klass,
            'jenis' => $jenis,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('penduduk')->user();
        $informasiUsaha = InformasiUsaha::where('penduduk_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_master_id' => 'nullable|integer',
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

        return redirect()->route('user.warungku.my')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(BarangWarungku $barangWarungku)
    {
        $user = Auth::guard('penduduk')->user();
        abort_unless($barangWarungku->informasiUsaha && $barangWarungku->informasiUsaha->penduduk_id === $user->id, 403);
        $klass = WarungkuMaster::select('klasifikasi')->distinct()->pluck('klasifikasi');
        $jenis = WarungkuMaster::select('id', 'jenis', 'klasifikasi')->get();
        return view('user.warungku.form', [
            'informasiUsaha' => $barangWarungku->informasiUsaha,
            'item' => $barangWarungku,
            'klass' => $klass,
            'jenis' => $jenis,
        ]);
    }

    public function update(Request $request, BarangWarungku $barangWarungku)
    {
        $user = Auth::guard('penduduk')->user();
        abort_unless($barangWarungku->informasiUsaha && $barangWarungku->informasiUsaha->penduduk_id === $user->id, 403);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'klasifikasi_master_id' => 'nullable|integer',
            'jenis_master_id' => 'nullable|integer',
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

        return redirect()->route('user.warungku.my')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(BarangWarungku $barangWarungku)
    {
        $user = Auth::guard('penduduk')->user();
        abort_unless($barangWarungku->informasiUsaha && $barangWarungku->informasiUsaha->penduduk_id === $user->id, 403);
        $barangWarungku->delete();
        return redirect()->route('user.warungku.my')->with('success', 'Produk berhasil dihapus');
    }

    public function show(BarangWarungku $barangWarungku)
    {
        $barangWarungku->load('informasiUsaha.penduduk');
        $jenisMaster = WarungkuMaster::select('id','jenis','klasifikasi')->find($barangWarungku->jenis_master_id);
        return view('user.warungku.show', compact('barangWarungku','jenisMaster'));
    }
}


