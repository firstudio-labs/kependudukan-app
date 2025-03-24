<?php

namespace App\Http\Controllers;

use App\Models\Penandatangan;
use Illuminate\Http\Request;

class PenandatangananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Penandatangan::query();

        if ($search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        }

        $penandatangans = $query->paginate(10);

        return view('superadmin.datamaster.surat.penandatangan.index', compact('penandatangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.datamaster.surat.penandatangan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);

        Penandatangan::create([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('superadmin.datamaster.surat.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $penandatangan = Penandatangan::findOrFail($id);
        return view('superadmin.datamaster.surat.penandatangan.edit', compact('penandatangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);

        $penandatangan = Penandatangan::findOrFail($id);
        $penandatangan->update([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('superadmin.datamaster.surat.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $penandatangan = Penandatangan::findOrFail($id);
        $penandatangan->delete();

        return redirect()->route('superadmin.datamaster.surat.penandatangan.index')
            ->with('success', 'Data penandatangan berhasil dihapus.');
    }
}
