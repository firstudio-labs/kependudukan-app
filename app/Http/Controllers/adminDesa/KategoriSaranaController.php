<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\KategoriSarana;
use Illuminate\Http\Request;

class KategoriSaranaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis_sarana');

        $query = KategoriSarana::query();
        if (!empty($jenis)) {
            $query->where('jenis_sarana', $jenis);
        }
        if (!empty($search)) {
            $query->where('kategori', 'like', "%{$search}%");
        }

        $items = $query->orderBy('jenis_sarana')->orderBy('kategori')->paginate(10);
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.kategori-sarana.index', compact('items','search','jenis','allowedJenis'));
    }

    public function create()
    {
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.kategori-sarana.create', compact('allowedJenis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_sarana' => 'required|in:Pendidikan,Tempat Ibadah,Sarana Kesehatan,TPS,Sanitasi,Akses Digital,Gedung Serbaguna,Sarana Olahraga & Kesenian,Jalan Desa,Lainnya',
            'kategori' => 'required|string|max:255',
        ]);
        KategoriSarana::create($validated);
        return redirect()->route('admin.desa.kategori-sarana.index')->with('success', 'Kategori sarana ditambahkan');
    }

    public function edit(KategoriSarana $kategoriSarana)
    {
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.kategori-sarana.edit', ['item' => $kategoriSarana, 'allowedJenis' => $allowedJenis]);
    }

    public function update(Request $request, KategoriSarana $kategoriSarana)
    {
        $validated = $request->validate([
            'jenis_sarana' => 'required|in:Pendidikan,Tempat Ibadah,Sarana Kesehatan,TPS,Sanitasi,Akses Digital,Gedung Serbaguna,Sarana Olahraga & Kesenian,Jalan Desa,Lainnya',
            'kategori' => 'required|string|max:255',
        ]);
        $kategoriSarana->update($validated);
        return redirect()->route('admin.desa.kategori-sarana.index')->with('success', 'Kategori sarana diperbarui');
    }

    public function destroy(KategoriSarana $kategoriSarana)
    {
        $kategoriSarana->delete();
        return redirect()->route('admin.desa.kategori-sarana.index')->with('success', 'Kategori sarana dihapus');
    }

    // AJAX: daftar kategori berdasarkan jenis
    public function byJenis(Request $request)
    {
        $jenis = $request->query('jenis_sarana');
        $items = KategoriSarana::where('jenis_sarana', $jenis)->orderBy('kategori')->get(['id','kategori']);
        return response()->json($items);
    }
}


