<?php

namespace App\Http\Controllers;

use App\Models\BeritaDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaDesa::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komentar', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);

        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.berita-desa.index', compact('berita'));
        }

        return view('user.berita-desa.index', compact('berita'));
    }

    public function create()
    {
        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.berita-desa.create');
        }

        return view('user.berita-desa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/berita-desa', $filename);
            $data['gambar'] = str_replace('public/', '', $path);
        }

        BeritaDesa::create($data);

        if (Auth::user()->role == 'admin desa') {
            return redirect()->route('admin.desa.berita-desa.index')
                ->with('success', 'Berita desa berhasil ditambahkan');
        }

        return redirect()->route('user.berita-desa.index')
            ->with('success', 'Berita desa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $berita = BeritaDesa::findOrFail($id);

        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.berita-desa.edit', compact('berita'));
        }

        return view('user.berita-desa.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = BeritaDesa::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar) {
                Storage::delete('public/' . $berita->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($request->judul) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/berita-desa', $filename);
            $data['gambar'] = str_replace('public/', '', $path);
        }

        $berita->update($data);

        if (Auth::user()->role == 'admin desa') {
            return redirect()->route('admin.desa.berita-desa.index')
                ->with('success', 'Berita desa berhasil diperbarui');
        }

        return redirect()->route('user.berita-desa.index')
            ->with('success', 'Berita desa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $berita = BeritaDesa::findOrFail($id);

        if ($berita->gambar) {
            Storage::delete('public/' . $berita->gambar);
        }

        $berita->delete();

        if (Auth::user()->role == 'admin desa') {
            return redirect()->route('admin.desa.berita-desa.index')
                ->with('success', 'Berita desa berhasil dihapus');
        }

        return redirect()->route('user.berita-desa.index')
            ->with('success', 'Berita desa berhasil dihapus');
    }

    public function show($id)
    {
        $berita = BeritaDesa::with('user')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $berita
        ]);
    }
}