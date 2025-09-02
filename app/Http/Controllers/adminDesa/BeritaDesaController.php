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
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $this->authorizeAdminDesa();

        $query = BeritaDesa::with('user')
            ->where('villages_id', Auth::user()->villages_id)
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komentar', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);
        
        // Buat object dummy dengan data admin yang login untuk menggunakan getWilayahInfo
        $adminUser = Auth::user();
        $dummyBerita = (object) [
            'province_id' => $adminUser->province_id,
            'districts_id' => $adminUser->districts_id,
            'sub_districts_id' => $adminUser->sub_districts_id,
            'villages_id' => $adminUser->villages_id
        ];
        
        // Tambahkan data wilayah untuk setiap berita berdasarkan admin yang login
        foreach ($berita as $item) {
            // Gunakan data wilayah dari admin yang login, bukan dari berita
            $item->wilayah_info = $this->getWilayahInfo($dummyBerita);
        }

        $context = 'approved';
        return view('admin.desa.berita-desa.index', compact('berita', 'context'));
    }

    public function pending(Request $request)
    {
        $this->authorizeAdminDesa();

        $query = BeritaDesa::with('user')
            ->where('villages_id', Auth::user()->villages_id)
            ->where('status', 'pending');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komentar', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);

        // Wilayah info pakai data admin login (konsisten dengan index)
        $adminUser = Auth::user();
        $dummyBerita = (object) [
            'province_id' => $adminUser->province_id,
            'districts_id' => $adminUser->districts_id,
            'sub_districts_id' => $adminUser->sub_districts_id,
            'villages_id' => $adminUser->villages_id
        ];
        foreach ($berita as $item) {
            $item->wilayah_info = $this->getWilayahInfo($dummyBerita);
        }

        $context = 'pending';
        return view('admin.desa.berita-desa.index', compact('berita', 'context'));
    }

    public function create()
    {
        $this->authorizeAdminDesa();
        
        // Buat object dummy dengan data admin yang login untuk menggunakan getWilayahInfo
        $adminUser = Auth::user();
        $dummyBerita = (object) [
            'province_id' => $adminUser->province_id,
            'districts_id' => $adminUser->districts_id,
            'sub_districts_id' => $adminUser->sub_districts_id,
            'villages_id' => $adminUser->villages_id
        ];
        
        $adminWilayahInfo = $this->getWilayahInfo($dummyBerita);
        return view('admin.desa.berita-desa.create', compact('adminWilayahInfo'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdminDesa();

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['user_id'] = Auth::id();
        $data['status'] = 'approved';
        
        // Ambil wilayah dari user yang login (admin desa)
        $adminDesa = Auth::user();
        if (!$adminDesa) {
            return redirect()->back()->with('error', 'Data admin desa tidak ditemukan.')->withInput();
        }
        
        // Set wilayah berdasarkan user yang login
        $data['province_id'] = $adminDesa->province_id;
        $data['districts_id'] = $adminDesa->districts_id;
        $data['sub_districts_id'] = $adminDesa->sub_districts_id;
        $data['villages_id'] = $adminDesa->villages_id;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $beritaName = Str::slug(substr($request->judul, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $beritaName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
            $data['gambar'] = $path;
        }

        $berita = BeritaDesa::create($data);

        return redirect()->route('admin.desa.berita-desa.index')
            ->with('success', 'Berita desa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);
        
        // Buat object dummy dengan data admin yang login untuk menggunakan getWilayahInfo
        $adminUser = Auth::user();
        $dummyBerita = (object) [
            'province_id' => $adminUser->province_id,
            'districts_id' => $adminUser->districts_id,
            'sub_districts_id' => $adminUser->sub_districts_id,
            'villages_id' => $adminUser->villages_id
        ];
        
        $adminWilayahInfo = $this->getWilayahInfo($dummyBerita);
        return view('admin.desa.berita-desa.edit', compact('berita', 'adminWilayahInfo'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['status'] = $berita->status; // pertahankan status saat update biasa
        
        // Set wilayah berdasarkan user yang login (konsisten dengan store)
        $adminDesa = Auth::user();
        if (!$adminDesa) {
            return redirect()->back()->with('error', 'Data admin desa tidak ditemukan.')->withInput();
        }
        
        $data['province_id'] = $adminDesa->province_id;
        $data['districts_id'] = $adminDesa->districts_id;
        $data['sub_districts_id'] = $adminDesa->sub_districts_id;
        $data['villages_id'] = $adminDesa->villages_id;

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
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);

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
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);
        return response()->json([
            'status' => 'success',
            'data' => $berita
        ]);
    }

    public function approve($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);
        $berita->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Berita disetujui.');
    }

    public function reject($id)
    {
        $this->authorizeAdminDesa();
        $berita = BeritaDesa::findOrFail($id);
        abort_unless($berita->villages_id == Auth::user()->villages_id, 403);
        $berita->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Berita ditolak.');
    }

    private function authorizeAdminDesa(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin desa', 403);
    }

    /**
     * Get wilayah information for a berita
     */
    private function getWilayahInfo($berita)
    {
        $wilayah = [];
        
        // Always set fallback first for safety
        if ($berita->province_id) {
            $wilayah['provinsi'] = 'Provinsi ID: ' . $berita->province_id;
            
            try {
                $provinces = $this->wilayahService->getProvinces();
                
                if (is_array($provinces) && !empty($provinces)) {
                    // Provinsi menggunakan 'id' field, BUKAN 'code' field
                    $province = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                    
                    if ($province && isset($province['name'])) {
                        $wilayah['provinsi'] = $province['name'];
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->districts_id) {
            $wilayah['kabupaten'] = 'Kabupaten ID: ' . $berita->districts_id;
            
            try {
                if ($berita->province_id) {
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Kabupaten menggunakan 'id' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['name'])) {
                                    $wilayah['kabupaten'] = $kabupatenData['name'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->sub_districts_id) {
            $wilayah['kecamatan'] = 'Kecamatan ID: ' . $berita->sub_districts_id;
            
            try {
                if ($berita->districts_id && $berita->province_id) {
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    // Gunakan kabupaten CODE untuk API call kecamatan
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Kecamatan menggunakan 'id' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        if ($kecamatanData && isset($kecamatanData['name'])) {
                                            $wilayah['kecamatan'] = $kecamatanData['name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->villages_id) {
            $wilayah['desa'] = 'Desa ID: ' . $berita->villages_id;
            
            try {
                if ($berita->sub_districts_id && $berita->districts_id && $berita->province_id) {
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    // Gunakan kabupaten CODE untuk API call kecamatan
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        if ($kecamatanData && isset($kecamatanData['code'])) {
                                            // Gunakan kecamatan CODE untuk API call desa
                                            $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                                            if (is_array($desa) && !empty($desa)) {
                                                // Desa menggunakan 'id' field
                                                $desaData = collect($desa)->firstWhere('id', (int) $berita->villages_id);
                                                if ($desaData && isset($desaData['name'])) {
                                                    $wilayah['desa'] = $desaData['name'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        return $wilayah;
    }
}


