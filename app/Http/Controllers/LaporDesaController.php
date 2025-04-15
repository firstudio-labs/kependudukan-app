<?php

namespace App\Http\Controllers;

use App\Models\LaporDesa;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporDesa::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ruang_lingkup', 'like', "%{$search}%")
                    ->orWhere('bidang', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $lapordesas = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.datamaster.lapordesa.index', compact('lapordesas'));
    }

    public function create()
    {
        return view('superadmin.datamaster.lapordesa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruang_lingkup' => 'required|string|max:255',
            'bidang' => 'required|string|max:255',
            'keterangan' => 'nullable|string'
        ]);

        LaporDesa::create($request->all());

        return redirect()->route('superadmin.datamaster.lapordesa.index')
            ->with('success', 'Lapor Desa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $laporDesa = LaporDesa::findOrFail($id);

        return view('superadmin.datamaster.lapordesa.edit', compact('laporDesa'));
    }

    public function update(Request $request, $id)
    {
        $laporDesa = LaporDesa::findOrFail($id);

        $request->validate([
            'ruang_lingkup' => 'required|string|max:255',
            'bidang' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $laporDesa->update($request->all());

        return redirect()->route('superadmin.datamaster.lapordesa.index')
            ->with('success', 'Lapor Desa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $laporDesa = LaporDesa::findOrFail($id);
        $laporDesa->delete();

        return redirect()->route('superadmin.datamaster.lapordesa.index')
            ->with('success', 'Lapor Desa berhasil dihapus.');
    }
}