<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BeritaDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaDesa::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komentar', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);
        return view('superadmin.berita-desa.index', compact('berita'));
    }

    public function create()
    {
        return view('superadmin.berita-desa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $beritaName = Str::slug(substr($request->judul, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $beritaName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
            $data['gambar'] = $path;
        }

        BeritaDesa::create($data);

        return redirect()->route('superadmin.berita-desa.index')
            ->with('success', 'Berita desa berhasil ditambahkan');
    }

    public function show($id)
    {
        $berita = BeritaDesa::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $berita
            ]);
        }

        return view('superadmin.berita-desa.show', compact('berita'));
    }

    public function edit($id)
    {
        $berita = BeritaDesa::findOrFail($id);
        return view('superadmin.berita-desa.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string',
        ]);

        $berita = BeritaDesa::findOrFail($id);
        $data = $request->only(['judul', 'deskripsi', 'komentar']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar && Storage::exists('public/' . $berita->gambar)) {
                Storage::delete('public/' . $berita->gambar);
            }

            $file = $request->file('gambar');
            $beritaName = Str::slug(substr($request->judul, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $beritaName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
            $data['gambar'] = $path;
        }

        $berita->update($data);

        return redirect()->route('superadmin.berita-desa.index')
            ->with('success', 'Berita desa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $berita = BeritaDesa::findOrFail($id);

        // Hapus gambar jika ada
        if ($berita->gambar && Storage::exists('public/' . $berita->gambar)) {
            Storage::delete('public/' . $berita->gambar);
        }

        $berita->delete();

        return redirect()->route('superadmin.berita-desa.index')
            ->with('success', 'Berita desa berhasil dihapus');
    }
}