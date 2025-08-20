<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaDesaController extends Controller
{
    private WilayahService $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }
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
        
        // Gunakan pola Lapor Desa: turunkan semua level wilayah dari villages_id
        // agar konsisten dan menghindari mismatch
        $adminDesa = Auth::user();
        if (!$adminDesa || !$adminDesa->villages_id) {
            \Log::error('villages_id admin desa tidak ditemukan', ['user' => optional($adminDesa)->id]);
            return redirect()->back()->with('error', 'Wilayah admin desa belum terkonfigurasi.')->withInput();
        }
        $villageIdStr = (string)$adminDesa->villages_id;
        // Pastikan cukup panjang untuk substr
        if (strlen($villageIdStr) < 10) {
            \Log::warning('Format villages_id tidak sesuai 10 digit', ['villages_id' => $villageIdStr]);
        }
        $data['id_provinsi'] = (int) substr($villageIdStr, 0, 2);
        $data['id_kabupaten'] = (int) substr($villageIdStr, 0, 4);
        $data['id_kecamatan'] = (int) substr($villageIdStr, 0, 6);
        $data['id_desa'] = (int) $adminDesa->villages_id;
        \Log::info('Wilayah diturunkan dari villages_id', [
            'villages_id' => $adminDesa->villages_id,
            'id_provinsi' => $data['id_provinsi'],
            'id_kabupaten' => $data['id_kabupaten'],
            'id_kecamatan' => $data['id_kecamatan'],
            'id_desa' => $data['id_desa'],
        ]);

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
        // Turunkan wilayah dari villages_id (konsisten dengan store)
        $adminDesa = Auth::user();
        if (!$adminDesa || !$adminDesa->villages_id) {
            return redirect()->back()->with('error', 'Wilayah admin desa belum terkonfigurasi.')->withInput();
        }
        $villageIdStr = (string)$adminDesa->villages_id;
        $data['id_provinsi'] = (int) substr($villageIdStr, 0, 2);
        $data['id_kabupaten'] = (int) substr($villageIdStr, 0, 4);
        $data['id_kecamatan'] = (int) substr($villageIdStr, 0, 6);
        $data['id_desa'] = (int) $adminDesa->villages_id;

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
        // Tambahkan informasi nama wilayah untuk ditampilkan di view
        $berita->wilayah_info = $this->getWilayahInfo($berita);
        return response()->json([
            'status' => 'success',
            'data' => $berita
        ]);
    }

    private function authorizeAdminDesa(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
    }

    private function getWilayahInfo($berita): array
    {
        $wilayah = [];

        if ($berita->id_provinsi) {
            try {
                $provinces = $this->wilayahService->getProvinces();
                $province = collect($provinces)->firstWhere('code', $berita->id_provinsi);
                if ($province) {
                    $wilayah['provinsi'] = $province['name'];
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if ($berita->id_kabupaten && $berita->id_provinsi) {
            try {
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                if ($kabupatenData) {
                    $wilayah['kabupaten'] = $kabupatenData['name'];
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if ($berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                if ($kabupatenData) {
                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                    $kecamatanData = collect($kecamatan)->firstWhere('id', $berita->id_kecamatan);
                    if ($kecamatanData) {
                        $wilayah['kecamatan'] = $kecamatanData['name'];
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if ($berita->id_desa && $berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                if ($kabupatenData) {
                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                    $kecamatanData = collect($kecamatan)->firstWhere('id', $berita->id_kecamatan);
                    if ($kecamatanData) {
                        $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                        $desaData = collect($desa)->firstWhere('id', $berita->id_desa);
                        if ($desaData) {
                            $wilayah['desa'] = $desaData['name'];
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $wilayah;
    }
}


