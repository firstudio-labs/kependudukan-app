<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BeritaDesaController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

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
        
        // Tambahkan data wilayah untuk setiap berita
        foreach ($berita as $item) {
            $item->wilayah_info = $this->getWilayahInfo($item);
        }
        
        return view('superadmin.berita-desa.index', compact('berita'));
    }

    /**
     * Get wilayah information for a berita
     */
    private function getWilayahInfo($berita)
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
                Log::error('Error getting province info: ' . $e->getMessage());
            }
        }
        
        if ($berita->id_kabupaten && $berita->id_provinsi) {
            try {
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                // Cocokkan berdasarkan code karena kita menyimpan kode (2/4/6/10 digit) di DB
                $kabupatenData = collect($kabupaten)->firstWhere('code', (string) $berita->id_kabupaten);
                if ($kabupatenData) {
                    $wilayah['kabupaten'] = $kabupatenData['name'];
                }
            } catch (\Exception $e) {
                Log::error('Error getting kabupaten info: ' . $e->getMessage());
            }
        }
        
        if ($berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('code', (string) $berita->id_kabupaten);
                
                if ($kabupatenData) {
                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                    // Cocokkan berdasarkan code karena kita menyimpan kode
                    $kecamatanData = collect($kecamatan)->firstWhere('code', (string) $berita->id_kecamatan);
                    if ($kecamatanData) {
                        $wilayah['kecamatan'] = $kecamatanData['name'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting kecamatan info: ' . $e->getMessage());
            }
        }
        
        if ($berita->id_desa && $berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('code', (string) $berita->id_kabupaten);
                
                if ($kabupatenData) {
                    // Get kecamatan data to get the code
                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                    $kecamatanData = collect($kecamatan)->firstWhere('code', (string) $berita->id_kecamatan);
                    
                    if ($kecamatanData) {
                        $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                        // Cocokkan berdasarkan code karena kita menyimpan kode desa 10 digit
                        $desaData = collect($desa)->firstWhere('code', (string) $berita->id_desa);
                        if ($desaData) {
                            $wilayah['desa'] = $desaData['name'];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting desa info: ' . $e->getMessage());
            }
        }
        
        return $wilayah;
    }

    public function create()
    {
        // Ambil data wilayah untuk dropdown - hanya provinsi di awal
        $provinces = $this->wilayahService->getProvinces();
        
        // Data kabupaten, kecamatan, dan desa akan di-load via AJAX
        $kabupaten = [];
        $kecamatan = [];
        $desa = [];

        return view('superadmin.berita-desa.create', compact('provinces', 'kabupaten', 'kecamatan', 'desa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string',
            'id_desa' => 'nullable|integer',
            'id_kecamatan' => 'nullable|integer',
            'id_kabupaten' => 'nullable|integer',
            'id_provinsi' => 'nullable|string|max:10',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'komentar', 'id_desa', 'id_kecamatan', 'id_kabupaten', 'id_provinsi']);
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
        $berita = BeritaDesa::with(['user'])->findOrFail($id);
        
        // Tambahkan data wilayah untuk berita
        $berita->wilayah_info = $this->getWilayahInfo($berita);

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
        $provinces = $this->wilayahService->getProvinces();
        
        // Load kabupaten, kecamatan, and desa data for the existing berita
        $kabupaten = [];
        $kecamatan = [];
        $desa = [];
        
        if ($berita->id_provinsi) {
            try {
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
            } catch (\Exception $e) {
                Log::error('Error loading kabupaten for edit: ' . $e->getMessage());
            }
        }
        
        if ($berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                if ($kabupatenData) {
                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                }
            } catch (\Exception $e) {
                Log::error('Error loading kecamatan for edit: ' . $e->getMessage());
            }
        }
        
        if ($berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                if ($kabupatenData) {
                    // Get kecamatan data to get the code
                    $kecamatanData = collect($kecamatan)->firstWhere('id', $berita->id_kecamatan);
                    if ($kecamatanData) {
                        $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error loading desa for edit: ' . $e->getMessage());
            }
        }

        return view('superadmin.berita-desa.edit', compact('berita', 'provinces', 'kabupaten', 'kecamatan', 'desa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string',
            'id_desa' => 'nullable|integer',
            'id_kecamatan' => 'nullable|integer',
            'id_kabupaten' => 'nullable|integer',
            'id_provinsi' => 'nullable|string|max:10',
        ]);

        $berita = BeritaDesa::findOrFail($id);
        $data = $request->only(['judul', 'deskripsi', 'komentar', 'id_desa', 'id_kecamatan', 'id_kabupaten', 'id_provinsi']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar && Storage::exists('public/' . $berita->gambar)) {
                Storage::delete('public/' . $berita->getAttribute('gambar'));
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

    /**
     * Get kabupaten by province code
     */
    public function getKabupatenByProvince(Request $request)
    {
        $provinceCode = $request->get('province_code');
        
        try {
            $kabupaten = $this->wilayahService->getKabupaten($provinceCode);
            return response()->json($kabupaten);
        } catch (\Exception $e) {
            Log::error('Error getting kabupaten: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load kabupaten'], 500);
        }
    }

    /**
     * Get kecamatan by kabupaten code
     */
    public function getKecamatanByKabupaten(Request $request)
    {
        $kabupatenCode = $request->get('kabupaten_code');
        
        try {
            $kecamatan = $this->wilayahService->getKecamatan($kabupatenCode);
            return response()->json($kecamatan);
        } catch (\Exception $e) {
            Log::error('Error getting kecamatan: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load kecamatan'], 500);
        }
    }

    /**
     * Get desa by kecamatan code
     */
    public function getDesaByKecamatan(Request $request)
    {
        $kecamatanCode = $request->get('kecamatan_code');
        
        try {
            $desa = $this->wilayahService->getDesa($kecamatanCode);
            return response()->json($desa);
        } catch (\Exception $e) {
            Log::error('Error getting desa: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load desa'], 500);
        }
    }
}