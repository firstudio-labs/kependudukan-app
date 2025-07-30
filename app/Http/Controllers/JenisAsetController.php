<?php

namespace App\Http\Controllers;

use App\Models\JenisAset;
use App\Models\Klasifikasi;
use Illuminate\Http\Request;

class JenisAsetController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = JenisAset::query();

        if ($search) {
            $query->where('kode', 'LIKE', "%{$search}%")
                ->orWhere('jenis_aset', 'LIKE', "%{$search}%")
                ->orWhere('keterangan', 'LIKE', "%{$search}%");
        }

        $jenisAset = $query->with('klasifikasi')->paginate(10);

        return view('superadmin.datamaster.jenis-aset.index', compact('jenisAset'));
    }

    public function create()
    {
        $klasifikasis = Klasifikasi::all();
        return view('superadmin.datamaster.jenis-aset.create', compact('klasifikasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|integer|unique:jenis_aset',
            'jenis_aset' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'klasifikasi_id' => 'required|exists:klasifikasi,id',
        ]);

        JenisAset::create([
            'kode' => $request->kode,
            'jenis_aset' => $request->jenis_aset,
            'keterangan' => $request->keterangan,
            'klasifikasi_id' => $request->klasifikasi_id,
        ]);

        return redirect()->route('superadmin.datamaster.jenis-aset.index')
            ->with('success', 'Jenis Aset berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jenisAset = JenisAset::findOrFail($id);
        $klasifikasis = Klasifikasi::all();
        return view('superadmin.datamaster.jenis-aset.edit', compact('jenisAset', 'klasifikasis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|integer|unique:jenis_aset,kode,' . $id,
            'jenis_aset' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'klasifikasi_id' => 'required|exists:klasifikasi,id',
        ]);

        $jenisAset = JenisAset::findOrFail($id);
        $jenisAset->update([
            'kode' => $request->kode,
            'jenis_aset' => $request->jenis_aset,
            'keterangan' => $request->keterangan,
            'klasifikasi_id' => $request->klasifikasi_id,
        ]);

        return redirect()->route('superadmin.datamaster.jenis-aset.index')
            ->with('success', 'Jenis Aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenisAset = JenisAset::findOrFail($id);
        $jenisAset->delete();

        return redirect()->route('superadmin.datamaster.jenis-aset.index')
            ->with('success', 'Jenis Aset berhasil dihapus.');
    }
}
