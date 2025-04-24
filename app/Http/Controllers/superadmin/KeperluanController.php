<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\Keperluan;
use Illuminate\Http\Request;

class KeperluanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Keperluan::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $keperluans = $query->paginate(10);

        return view('superadmin.datamaster.masterkeperluan.keperluan.index', compact('keperluans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.datamaster.masterkeperluan.keperluan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        Keperluan::create($validatedData);

        return redirect()->route('superadmin.datamaster.masterkeperluan.keperluan.index')
                         ->with('success', 'Data keperluan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keperluan $keperluan)
    {
        return view('superadmin.datamaster.masterkeperluan.keperluan.edit', compact('keperluan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keperluan $keperluan)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        $keperluan->update($validatedData);

        return redirect()->route('superadmin.datamaster.masterkeperluan.keperluan.index')
                         ->with('success', 'Data keperluan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keperluan $keperluan)
    {
        $keperluan->delete();

        return redirect()->route('superadmin.datamaster.masterkeperluan.keperluan.index')
                         ->with('success', 'Data keperluan berhasil dihapus.');
    }
}
