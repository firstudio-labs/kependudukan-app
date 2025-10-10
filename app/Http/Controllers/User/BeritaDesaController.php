<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BeritaDesaController extends Controller
{
    protected $wilayahService;
    protected $citizenService;

    public function __construct(WilayahService $wilayahService, CitizenService $citizenService)
    {
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
    }

    public function index(Request $request)
    {
        $query = BeritaDesa::with(['user', 'penduduk'])->where('status', 'published');

        // Filter berdasarkan desa penduduk yang login (guard penduduk)
        if (Auth::guard('penduduk')->check()) {
            $penduduk = Auth::guard('penduduk')->user();

            // Ambil data penduduk dari API untuk mendapatkan village_id/villages_id
            $citizenData = $this->citizenService->getCitizenByNIK($penduduk->nik);

            // Ekstrak village id dari beberapa kemungkinan struktur response
            $villageId = null;
            if (is_array($citizenData)) {
                $villageId = $citizenData['village_id']
                    ?? $citizenData['villages_id']
                    ?? ($citizenData['data']['village_id'] ?? null)
                    ?? ($citizenData['data']['villages_id'] ?? null);
            }

            if (!is_null($villageId)) {
                $query->where('villages_id', (int) $villageId); // Updated field name
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($request->input('per_page', 10));
        $berita = $query->latest()->paginate($perPage);
        
        // Tambahkan data wilayah dan nama penduduk untuk setiap berita
        foreach ($berita as $item) {
            $item->wilayah_info = $this->getWilayahInfo($item);
            $item->nama_penduduk = $this->getNamaPenduduk($item->nik_penduduk);
        }
        
        // Jika diminta sebagai JSON (untuk konsumsi mobile), kembalikan JSON
        if ($request->wantsJson() || $request->ajax() || $request->query('format') === 'json') {
            $items = collect($berita->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'komentar' => $item->komentar,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url,
                    'user_id' => $item->user_id,
                    'wilayah_info' => $item->wilayah_info,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $items,
                'meta' => [
                    'current_page' => $berita->currentPage(),
                    'per_page' => $berita->perPage(),
                    'total' => $berita->total(),
                    'last_page' => $berita->lastPage(),
                ]
            ]);
        }
        
        return view('user.berita-desa.index', compact('berita'));
    }

    public function show($id)
    {
        // Filter berdasarkan desa penduduk yang login (guard penduduk)
        if (Auth::guard('penduduk')->check()) {
            $penduduk = Auth::guard('penduduk')->user();

            // Ambil data penduduk dari API untuk mendapatkan village_id/villages_id
            $citizenData = $this->citizenService->getCitizenByNIK($penduduk->nik);

            // Ekstrak village id dari beberapa kemungkinan struktur response
            $villageId = null;
            if (is_array($citizenData)) {
                $villageId = $citizenData['village_id']
                    ?? $citizenData['villages_id']
                    ?? ($citizenData['data']['village_id'] ?? null)
                    ?? ($citizenData['data']['villages_id'] ?? null);
            }

            if (!is_null($villageId)) {
                // Cari berita dan validasi bahwa berita milik desa penduduk
                $berita = BeritaDesa::with(['user', 'penduduk'])
                    ->where('villages_id', (int) $villageId)
                    ->findOrFail($id);
            } else {
                // Jika tidak ada village_id, return error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data desa tidak ditemukan untuk akun ini'
                ], 404);
            }
        } else {
            // Jika tidak ada penduduk yang login, return error
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        // Tambahkan data wilayah dan nama penduduk untuk berita
        $berita->wilayah_info = $this->getWilayahInfo($berita);
        $berita->nama_penduduk = $this->getNamaPenduduk($berita->nik_penduduk);
        
        return response()->json(['data' => $berita]);
    }

    public function create()
    {
        // Hanya penduduk yang boleh membuat
        abort_unless(Auth::guard('penduduk')->check(), 403);
        return view('user.berita-desa.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::guard('penduduk')->check(), 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'required|string',
            'komentar' => 'nullable|string'
        ]);

        $penduduk = Auth::guard('penduduk')->user();
        $citizenData = $this->citizenService->getCitizenByNIK($penduduk->nik);

        // Ekstrak id wilayah dari berbagai kemungkinan struktur
        $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
        $provinceId = $payload['province_id'] ?? $payload['provinsi_id'] ?? null;
        $districtId = $payload['district_id'] ?? $payload['districts_id'] ?? null;
        $subDistrictId = $payload['sub_district_id'] ?? $payload['sub_districts_id'] ?? null;
        $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

        if (!$villageId) {
            return back()->with('error', 'Gagal mengambil lokasi desa dari data akun.')->withInput();
        }

        $data = $request->only(['judul', 'deskripsi', 'komentar']);
        $data['user_id'] = $penduduk->id ?? null;
        $data['nik_penduduk'] = $penduduk->nik; // Tambahkan NIK penduduk
        $data['province_id'] = $provinceId ? (int) $provinceId : null;
        $data['districts_id'] = $districtId ? (int) $districtId : null;
        $data['sub_districts_id'] = $subDistrictId ? (int) $subDistrictId : null;
        $data['villages_id'] = (int) $villageId;
        // Sesuai requirement: berita yang dibuat penduduk diarsipkan terlebih dahulu
        $data['status'] = 'archived';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $beritaName = \Illuminate\Support\Str::slug(substr($request->judul, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $beritaName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
            $data['gambar'] = $path;
        }

        BeritaDesa::create($data);

        return redirect()->route('user.berita-desa.index')
            ->with('success', 'Berita disimpan dan dikirim untuk persetujuan admin desa.');
    }

    public function sendApproval($id)
    {
        abort_unless(Auth::guard('penduduk')->check(), 403);

        $penduduk = Auth::guard('penduduk')->user();
        $berita = BeritaDesa::where('id', $id)
            ->where('user_id', $penduduk->id)
            ->firstOrFail();

        if ($berita->status === 'published') {
            return back()->with('success', 'Berita sudah dipublikasikan.');
        }

        // Kirim untuk persetujuan: dari archived tetap archived (admin akan mem-publish dari index)
        // Tidak lagi memakai status pending
        return back()->with('success', 'Berita menunggu verifikasi admin desa.');
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
                Log::info('Provinces API Response:', ['data' => $provinces, 'count' => is_array($provinces) ? count($provinces) : 'not array']);
                
                if (is_array($provinces) && !empty($provinces)) {
                    // Perbaikan: Gunakan 'id' field, bukan 'code' field
                    $province = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                    Log::info('Province Found:', ['province_id' => $berita->province_id, 'province' => $province]);
                    
                    if ($province && isset($province['name'])) {
                        $wilayah['provinsi'] = $province['name'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting province info: ' . $e->getMessage());
            }
        }
        
        if ($berita->districts_id) {
            $wilayah['kabupaten'] = 'Kabupaten ID: ' . $berita->districts_id;
            
            try {
                if ($berita->province_id) {
                    Log::info('Calling Kabupaten API:', ['province_id' => $berita->province_id]);
                    
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            Log::info('Kabupaten API Response:', ['data' => $kabupaten, 'count' => is_array($kabupaten) ? count($kabupaten) : 'not array']);
                            
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                Log::info('Kabupaten Found:', ['districts_id' => $berita->districts_id, 'kabupaten' => $kabupatenData]);
                                
                                if ($kabupatenData && isset($kabupatenData['name'])) {
                                    $wilayah['kabupaten'] = $kabupatenData['name'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error getting kabupaten info: ' . $e->getMessage());
            }
        }
        
        if ($berita->sub_districts_id) {
            $wilayah['kecamatan'] = 'Kecamatan ID: ' . $berita->sub_districts_id;
            
            try {
                if ($berita->districts_id && $berita->province_id) {
                    Log::info('Calling Kecamatan API:', ['districts_id' => $berita->districts_id, 'province_id' => $berita->province_id]);
                    
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    Log::info('Calling Kecamatan with kabupaten code:', ['kabupaten_code' => $kabupatenData['code']]);
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    Log::info('Kecamatan API Response:', ['data' => $kecamatan, 'count' => is_array($kecamatan) ? count($kecamatan) : 'not array']);
                                    
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        
                                        Log::info('Kecamatan Found:', ['sub_districts_id' => $berita->sub_districts_id, 'kecamatan' => $kecamatanData]);
                                        
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
                Log::error('Error getting kecamatan info: ' . $e->getMessage());
            }
        }
        
        if ($berita->villages_id) {
            $wilayah['desa'] = 'Desa ID: ' . $berita->villages_id;
            
            try {
                if ($berita->sub_districts_id && $berita->districts_id && $berita->province_id) {
                    Log::info('Calling Desa API:', ['villages_id' => $berita->villages_id, 'sub_districts_id' => $berita->sub_districts_id]);
                    
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        
                                        if ($kecamatanData && isset($kecamatanData['code'])) {
                                            Log::info('Calling Desa with kecamatan code:', ['kecamatan_code' => $kecamatanData['code']]);
                                            $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                                            Log::info('Desa API Response:', ['data' => $desa, 'count' => is_array($desa) ? count($desa) : 'not array']);
                                            
                                            if (is_array($desa) && !empty($desa)) {
                                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                                $desaData = collect($desa)->firstWhere('id', (int) $berita->villages_id);
                                                
                                                Log::info('Desa Found:', ['villages_id' => $berita->villages_id, 'desa' => $desaData]);
                                                
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
                Log::error('Error getting desa info: ' . $e->getMessage());
            }
        }
        
        Log::info('Final Wilayah Info:', $wilayah);
        return $wilayah;
    }

    /**
     * Get nama penduduk from CitizenService
     */
    private function getNamaPenduduk($nik)
    {
        if (!$nik) {
            return null;
        }

        try {
            $citizenData = $this->citizenService->getCitizenByNIK((int) $nik);
            
            if (is_array($citizenData)) {
                $data = $citizenData['data'] ?? $citizenData;
                return $data['full_name'] ?? $data['nama'] ?? 'Nama tidak tersedia';
            }
            
            return 'Nama tidak tersedia';
        } catch (\Exception $e) {
            Log::error('Error getting citizen name: ' . $e->getMessage(), ['nik' => $nik]);
            return 'Nama tidak tersedia';
        }
    }
}