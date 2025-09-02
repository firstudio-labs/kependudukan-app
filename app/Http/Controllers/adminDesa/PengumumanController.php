<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);

        $query = Pengumuman::with('user')->where('villages_id', Auth::user()->villages_id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        $pengumuman = $query->latest()->paginate(10);
        return view('admin.desa.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
        return view('admin.desa.pengumuman.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string'
        ]);

        $data = $request->only(['judul','deskripsi']);
        $data['user_id'] = Auth::id();
        $admin = Auth::user();
        $data['province_id'] = $admin->province_id;
        $data['districts_id'] = $admin->districts_id;
        $data['sub_districts_id'] = $admin->sub_districts_id;
        $data['villages_id'] = $admin->villages_id;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $slug = Str::slug(substr($request->judul, 0, 30));
            $filename = time() . '_' . $slug . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/pengumuman', $filename, 'public');
            $data['gambar'] = $path;
        }

        Pengumuman::create($data);
        return redirect()->route('admin.desa.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function edit($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
        $pengumuman = Pengumuman::findOrFail($id);
        abort_unless($pengumuman->villages_id == Auth::user()->villages_id, 403);
        return view('admin.desa.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
        $pengumuman = Pengumuman::findOrFail($id);
        abort_unless($pengumuman->villages_id == Auth::user()->villages_id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string'
        ]);

        $data = $request->only(['judul','deskripsi']);
        $admin = Auth::user();
        $data['province_id'] = $admin->province_id;
        $data['districts_id'] = $admin->districts_id;
        $data['sub_districts_id'] = $admin->sub_districts_id;
        $data['villages_id'] = $admin->villages_id;

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::exists('public/' . $pengumuman->gambar)) {
                Storage::delete('public/' . $pengumuman->gambar);
            }
            $file = $request->file('gambar');
            $slug = Str::slug(substr($request->judul, 0, 30));
            $filename = time() . '_' . $slug . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/pengumuman', $filename, 'public');
            $data['gambar'] = $path;
        }

        $pengumuman->update($data);
        return redirect()->route('admin.desa.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
        $pengumuman = Pengumuman::findOrFail($id);
        abort_unless($pengumuman->villages_id == Auth::user()->villages_id, 403);

        if ($pengumuman->gambar && Storage::exists('public/' . $pengumuman->gambar)) {
            Storage::delete('public/' . $pengumuman->gambar);
        }

        $pengumuman->delete();
        return redirect()->route('admin.desa.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }

    public function show($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
        $pengumuman = Pengumuman::with('user')->findOrFail($id);
        abort_unless($pengumuman->villages_id == Auth::user()->villages_id, 403);
        return response()->json([
            'status' => 'success',
            'data' => $pengumuman
        ]);
    }
}


