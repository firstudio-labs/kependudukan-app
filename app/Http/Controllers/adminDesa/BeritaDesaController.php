<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaDesaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdminDesa();

        $query = BeritaDesa::with('user')->where('id_desa', Auth::user()->villages_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komentar', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);

        return view('admin.desa.berita-desa.index', compact('berita'));
    }

    public function create()
    {
        $this->authorizeAdminDesa();
        return view('admin.desa.berita-desa.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdminDesa();

        // Log untuk debugging
        \Log::info('AdminDesa BeritaDesaController store method called');
        \Log::info('User ID: ' . Auth::id());
        \Log::info('User Role: ' . Auth::user()->role);
        \Log::info('User Province ID: ' . Auth::user()->province_id);
        \Log::info('User District ID: ' . Auth::user()->districts_id);
        \Log::info('User Sub District ID: ' . Auth::user()->sub_districts_id);
        \Log::info('User Village ID: ' . Auth::user()->villages_id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['user_id'] = Auth::id();
        
        // Gunakan pola yang sama dengan lapor desa yang sudah berhasil
        // Ambil data dari admin desa yang login
        $adminDesa = Auth::user();
        
        // Pastikan data wilayah ada dan valid
        if ($adminDesa->province_id && $adminDesa->districts_id && 
            $adminDesa->sub_districts_id && $adminDesa->villages_id) {
            
            $data['id_provinsi'] = $adminDesa->province_id;
            $data['id_kabupaten'] = $adminDesa->districts_id;
            $data['id_kecamatan'] = $adminDesa->sub_districts_id;
            $data['id_desa'] = $adminDesa->villages_id;
            
            \Log::info('Wilayah data dari admin desa:', [
                'province_id' => $adminDesa->province_id,
                'districts_id' => $adminDesa->districts_id,
                'sub_districts_id' => $adminDesa->sub_districts_id,
                'villages_id' => $adminDesa->villages_id
            ]);
        } else {
            \Log::error('Data wilayah admin desa tidak lengkap:', [
                'province_id' => $adminDesa->province_id,
                'districts_id' => $adminDesa->districts_id,
                'sub_districts_id' => $adminDesa->sub_districts_id,
                'villages_id' => $adminDesa->villages_id
            ]);
            
            return redirect()->back()
                ->with('error', 'Data wilayah admin desa tidak lengkap. Silakan hubungi superadmin.')
                ->withInput();
        }

        // Log data yang akan disimpan
        \Log::info('Data to be saved:', $data);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $beritaName = Str::slug(substr($request->judul, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $beritaName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
            $data['gambar'] = $path;
        }

        $berita = BeritaDesa::create($data);

        // Log berita yang berhasil dibuat
        \Log::info('Berita created successfully:', $berita->toArray());

        return redirect()->route('admin.desa.berita-desa.index')
            ->with('success', 'Berita desa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->id_desa == Auth::user()->villages_id, 403);
        return view('admin.desa.berita-desa.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->id_desa == Auth::user()->villages_id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        
        // Gunakan pola yang sama dengan store method
        $adminDesa = Auth::user();
        
        // Pastikan data wilayah ada dan valid
        if ($adminDesa->province_id && $adminDesa->districts_id && 
            $adminDesa->sub_districts_id && $adminDesa->villages_id) {
            
            $data['id_provinsi'] = $adminDesa->province_id;
            $data['id_kabupaten'] = $adminDesa->districts_id;
            $data['id_kecamatan'] = $adminDesa->sub_districts_id;
            $data['id_desa'] = $adminDesa->villages_id;
        } else {
            return redirect()->back()
                ->with('error', 'Data wilayah admin desa tidak lengkap. Silakan hubungi superadmin.')
                ->withInput();
        }

        if ($request->hasFile('gambar')) {
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

        return redirect()->route('admin.desa.berita-desa.index')
            ->with('success', 'Berita desa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->id_desa == Auth::user()->villages_id, 403);

        if ($berita->gambar && Storage::exists('public/' . $berita->gambar)) {
            Storage::delete('public/' . $berita->gambar);
        }

        $berita->delete();

        return redirect()->route('admin.desa.berita-desa.index')
            ->with('success', 'Berita desa berhasil dihapus');
    }

    public function show($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::with('user')->findOrFail($id);
        abort_unless($berita->id_desa == Auth::user()->villages_id, 403);
        return response()->json([
            'status' => 'success',
            'data' => $berita
        ]);
    }

    private function authorizeAdminDesa(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
    }
}


