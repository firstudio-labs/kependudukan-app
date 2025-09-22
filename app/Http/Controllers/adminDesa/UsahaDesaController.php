<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\UsahaDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsahaDesaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $search = $request->input('search');
        $jenis = $request->input('jenis');

        $allowedJenis = ['BUMDES', 'Koperasi', 'Mandiri/Perseorangan', 'KUB (Kelompok Usaha Bersama)', 'Korporasi/Perusahaan'];

        $query = UsahaDesa::where('user_id', $user->id);

        if (!empty($jenis) && in_array($jenis, $allowedJenis)) {
            $query->where('jenis', $jenis);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('ijin', 'like', "%{$search}%")
                  ->orWhere('ketua', 'like', "%{$search}%")
                  ->orWhere('tahun_didirikan', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.desa.usaha.index', compact('items', 'search', 'jenis', 'allowedJenis'));
    }

    public function create()
    {
        return view('admin.desa.usaha.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:BUMDES,Koperasi,Mandiri/Perseorangan,KUB (Kelompok Usaha Bersama),Korporasi/Perusahaan',
            'nama' => 'required|string|max:255',
            'ijin' => 'nullable|string|max:255',
            'tahun_didirikan' => 'nullable|digits:4',
            'ketua' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::guard('web')->id();
        UsahaDesa::create($validated);

        return redirect()->route('admin.desa.usaha.index')->with('success', 'Usaha Desa berhasil ditambahkan');
    }

    public function edit(UsahaDesa $usaha)
    {
        $this->authorizeOwner($usaha);
        return view('admin.desa.usaha.edit', compact('usaha'));
    }

    public function update(Request $request, UsahaDesa $usaha)
    {
        $this->authorizeOwner($usaha);

        $validated = $request->validate([
            'jenis' => 'required|in:BUMDES,Koperasi,Mandiri/Perseorangan,KUB (Kelompok Usaha Bersama),Korporasi/Perusahaan',
            'nama' => 'required|string|max:255',
            'ijin' => 'nullable|string|max:255',
            'tahun_didirikan' => 'nullable|digits:4',
            'ketua' => 'nullable|string|max:255',
        ]);

        $usaha->update($validated);
        return redirect()->route('admin.desa.usaha.index')->with('success', 'Usaha Desa berhasil diperbarui');
    }

    public function destroy(UsahaDesa $usaha)
    {
        $this->authorizeOwner($usaha);
        $usaha->delete();
        return redirect()->route('admin.desa.usaha.index')->with('success', 'Usaha Desa berhasil dihapus');
    }

    private function authorizeOwner(UsahaDesa $usaha): void
    {
        if ($usaha->user_id !== Auth::guard('web')->id()) {
            abort(403);
        }
    }
}


