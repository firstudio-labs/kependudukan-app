<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\WarungkuMaster;
use Illuminate\Http\Request;

class WarungkuMasterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $klasifikasi = $request->input('klasifikasi');
        $query = WarungkuMaster::query();

        if (!empty($klasifikasi) && in_array($klasifikasi, ['barang', 'jasa'])) {
            $query->where('klasifikasi', $klasifikasi);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis', 'like', "%{$search}%")
                  ->orWhere('klasifikasi', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('klasifikasi')->orderBy('jenis')->paginate(10);
        return view('superadmin.datamaster.warungku.index', compact('items', 'search', 'klasifikasi'));
    }

    public function create()
    {
        return view('superadmin.datamaster.warungku.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'klasifikasi' => 'required|in:barang,jasa',
            'jenis' => 'required|string|max:255',
        ]);

        WarungkuMaster::create($validated);
        return redirect()->route('superadmin.datamaster.warungku.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(WarungkuMaster $warungku)
    {
        return view('superadmin.datamaster.warungku.edit', ['item' => $warungku]);
    }

    public function update(Request $request, WarungkuMaster $warungku)
    {
        $validated = $request->validate([
            'klasifikasi' => 'required|in:barang,jasa',
            'jenis' => 'required|string|max:255',
        ]);

        $warungku->update($validated);
        return redirect()->route('superadmin.datamaster.warungku.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(WarungkuMaster $warungku)
    {
        $warungku->delete();
        return redirect()->route('superadmin.datamaster.warungku.index')->with('success', 'Data berhasil dihapus');
    }
}


