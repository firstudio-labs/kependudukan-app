<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\KategoriSarana;
use App\Models\SaranaUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaranaUmumController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $search = $request->input('search');
        $jenis = $request->input('jenis_sarana');

        $query = SaranaUmum::with('kategori')->where('user_id', $user->id);
        if (!empty($jenis)) {
            $query->whereHas('kategori', function($q) use ($jenis) { $q->where('jenis_sarana', $jenis); });
        }
        if (!empty($search)) {
            $query->where(function($q) use ($search){
                $q->where('nama_sarana','like',"%{$search}%")
                  ->orWhere('alamat','like',"%{$search}%")
                  ->orWhere('kontak','like',"%{$search}%");
            });
        }
        $items = $query->orderBy('created_at','desc')->paginate(10);
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.sarana-umum.index', compact('items','search','jenis','allowedJenis'));
    }

    public function create()
    {
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.sarana-umum.create', compact('allowedJenis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_sarana_id' => 'required|exists:kategori_saranas,id',
            'nama_sarana' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'tag_lat' => 'nullable|numeric',
            'tag_lng' => 'nullable|numeric',
            'kontak' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);
        
        // Gabungkan lat dan lng menjadi tag_lokasi
        if ($validated['tag_lat'] && $validated['tag_lng']) {
            $validated['tag_lokasi'] = $validated['tag_lat'] . ',' . $validated['tag_lng'];
        }
        
        $validated['user_id'] = Auth::guard('web')->id();
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('uploads/sarana', 'public');
        }
        SaranaUmum::create($validated);
        return redirect()->route('admin.desa.sarana-umum.index')->with('success','Sarana umum ditambahkan');
    }

    public function edit(SaranaUmum $saranaUmum)
    {
        $this->authorizeOwner($saranaUmum);
        $allowedJenis = [
            'Pendidikan','Tempat Ibadah','Sarana Kesehatan','TPS','Sanitasi',
            'Akses Digital','Gedung Serbaguna','Sarana Olahraga & Kesenian','Jalan Desa','Lainnya'
        ];
        return view('admin.desa.sarana-umum.edit', [
            'item' => $saranaUmum->load('kategori'),
            'allowedJenis' => $allowedJenis
        ]);
    }

    public function update(Request $request, SaranaUmum $saranaUmum)
    {
        $this->authorizeOwner($saranaUmum);
        $validated = $request->validate([
            'kategori_sarana_id' => 'required|exists:kategori_saranas,id',
            'nama_sarana' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'tag_lat' => 'nullable|numeric',
            'tag_lng' => 'nullable|numeric',
            'kontak' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);
        
        // Gabungkan lat dan lng menjadi tag_lokasi
        if ($validated['tag_lat'] && $validated['tag_lng']) {
            $validated['tag_lokasi'] = $validated['tag_lat'] . ',' . $validated['tag_lng'];
        }
        
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('uploads/sarana', 'public');
        }
        $saranaUmum->update($validated);
        return redirect()->route('admin.desa.sarana-umum.index')->with('success','Sarana umum diperbarui');
    }

    public function destroy(SaranaUmum $saranaUmum)
    {
        $this->authorizeOwner($saranaUmum);
        $saranaUmum->delete();
        return redirect()->route('admin.desa.sarana-umum.index')->with('success','Sarana umum dihapus');
    }

    private function authorizeOwner(SaranaUmum $saranaUmum): void
    {
        if ($saranaUmum->user_id !== Auth::guard('web')->id()) {
            abort(403);
        }
    }
}


