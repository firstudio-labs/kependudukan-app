<?php

namespace App\Http\Controllers;

use App\Models\Klasifikasi;
use Illuminate\Http\Request;

class KlasifikasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Klasifikasi::query();

        if ($search) {
            $query->where('kode', 'LIKE', "%{$search}%")
                ->orWhere('jenis_klasifikasi', 'LIKE', "%{$search}%")
                ->orWhere('keterangan', 'LIKE', "%{$search}%");
        }

        $klasifikasi = $query->paginate(10);

        return view('superadmin.datamaster.klasifikasi.index', compact('klasifikasi'));
    }

    public function create()
    {
        return view('superadmin.datamaster.klasifikasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|integer|unique:klasifikasi',
            'jenis_klasifikasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Klasifikasi::create([
            'kode' => $request->kode,
            'jenis_klasifikasi' => $request->jenis_klasifikasi,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('superadmin.datamaster.klasifikasi.index')
            ->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $klasifikasi = Klasifikasi::findOrFail($id);
        return view('superadmin.datamaster.klasifikasi.edit', compact('klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|integer|unique:klasifikasi,kode,' . $id,
            'jenis_klasifikasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $klasifikasi = Klasifikasi::findOrFail($id);
        $klasifikasi->update([
            'kode' => $request->kode,
            'jenis_klasifikasi' => $request->jenis_klasifikasi,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('superadmin.datamaster.klasifikasi.index')
            ->with('success', 'Klasifikasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $klasifikasi = Klasifikasi::findOrFail($id);
        $klasifikasi->delete();

        return redirect()->route('superadmin.datamaster.klasifikasi.index')
            ->with('success', 'Klasifikasi berhasil dihapus.');
    }
}