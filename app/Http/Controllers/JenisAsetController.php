<?php

namespace App\Http\Controllers;

use App\Models\JenisAset;
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

        $jenisAset = $query->paginate(10);

        return view('superadmin.datamaster.jenis-aset.index', compact('jenisAset'));
    }

    public function create()
    {
        return view('superadmin.datamaster.jenis-aset.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|integer|unique:jenis_aset',
            'jenis_aset' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        JenisAset::create([
            'kode' => $request->kode,
            'jenis_aset' => $request->jenis_aset,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('superadmin.datamaster.jenis-aset.index')
            ->with('success', 'Jenis Aset berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jenisAset = JenisAset::findOrFail($id);
        return view('superadmin.datamaster.jenis-aset.edit', compact('jenisAset'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|integer|unique:jenis_aset,kode,' . $id,
            'jenis_aset' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $jenisAset = JenisAset::findOrFail($id);
        $jenisAset->update([
            'kode' => $request->kode,
            'jenis_aset' => $request->jenis_aset,
            'keterangan' => $request->keterangan,
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