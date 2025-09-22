<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\Abdes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbdesController extends Controller
{
    private array $allowedJenis = ['Perencanaan','Realisasi'];
    private array $allowedKategori = [
        'Pemerintahan Desa','Pembangunan Desa','Pembinaan Desa','Pemberdayaan Masyarakat','Penanggulangan Bencana dan Darurat Desa'
    ];

    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $jenis = $request->input('jenis');
        $kategori = $request->input('kategori');
        $search = $request->input('search');

        $query = Abdes::where('user_id', $user->id);
        if (!empty($jenis) && in_array($jenis, $this->allowedJenis)) $query->where('jenis', $jenis);
        if (!empty($kategori) && in_array($kategori, $this->allowedKategori)) $query->where('kategori', $kategori);
        if (!empty($search)) {
            $query->where(function($q) use ($search){
                $q->where('kategori','like',"%{$search}%");
            });
        }
        $items = $query->orderBy('created_at','desc')->paginate(10);
        $totalAnggaran = (clone $query)->sum('jumlah_anggaran');

        $allowedJenis = $this->allowedJenis;
        $allowedKategori = $this->allowedKategori;
        return view('admin.desa.abdes.index', compact('items','allowedJenis','allowedKategori','jenis','kategori','search','totalAnggaran'));
    }

    public function create()
    {
        $allowedJenis = $this->allowedJenis;
        $allowedKategori = $this->allowedKategori;
        return view('admin.desa.abdes.create', compact('allowedJenis','allowedKategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:Perencanaan,Realisasi',
            'kategori' => 'required|in:Pemerintahan Desa,Pembangunan Desa,Pembinaan Desa,Pemberdayaan Masyarakat,Penanggulangan Bencana dan Darurat Desa',
            'jumlah_anggaran' => 'required|numeric|min:0',
        ]);
        $validated['user_id'] = Auth::guard('web')->id();
        Abdes::create($validated);
        return redirect()->route('admin.desa.abdes.index')->with('success','Data APBDes ditambahkan');
    }

    public function edit(Abdes $abdes)
    {
        $this->authorizeOwner($abdes);
        $allowedJenis = $this->allowedJenis;
        $allowedKategori = $this->allowedKategori;
        return view('admin.desa.abdes.edit', ['item' => $abdes, 'allowedJenis' => $allowedJenis, 'allowedKategori' => $allowedKategori]);
    }

    public function update(Request $request, Abdes $abdes)
    {
        $this->authorizeOwner($abdes);
        $validated = $request->validate([
            'jenis' => 'required|in:Perencanaan,Realisasi',
            'kategori' => 'required|in:Pemerintahan Desa,Pembangunan Desa,Pembinaan Desa,Pemberdayaan Masyarakat,Penanggulangan Bencana dan Darurat Desa',
            'jumlah_anggaran' => 'required|numeric|min:0',
        ]);
        $abdes->update($validated);
        return redirect()->route('admin.desa.abdes.index')->with('success','Data APBDes diperbarui');
    }

    public function destroy(Abdes $abdes)
    {
        $this->authorizeOwner($abdes);
        $abdes->delete();
        return redirect()->route('admin.desa.abdes.index')->with('success','Data APBDes dihapus');
    }

    private function authorizeOwner(Abdes $abdes): void
    {
        if ($abdes->user_id !== Auth::guard('web')->id()) abort(403);
    }
}


