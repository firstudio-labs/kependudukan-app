<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = BeritaDesa::with(['user']);

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
                $query->where('id_desa', (int) $villageId);
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
        
        // Tambahkan data wilayah untuk setiap berita
        foreach ($berita as $item) {
            $item->wilayah_info = $this->getWilayahInfo($item);
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
        $berita = BeritaDesa::with(['user'])->findOrFail($id);
        
        // Tambahkan data wilayah untuk berita
        $berita->wilayah_info = $this->getWilayahInfo($berita);
        
        return response()->json(['data' => $berita]);
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
                // Log error if needed
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
                // Log error if needed
            }
        }
        
        if ($berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
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
                // Log error if needed
            }
        }
        
        if ($berita->id_desa && $berita->id_kecamatan && $berita->id_kabupaten && $berita->id_provinsi) {
            try {
                // Get kabupaten data first to get the code
                $kabupaten = $this->wilayahService->getKabupaten($berita->id_provinsi);
                $kabupatenData = collect($kabupaten)->firstWhere('id', $berita->id_kabupaten);
                
                if ($kabupatenData) {
                    // Get kecamatan data to get the code
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
                // Log error if needed
            }
        }
        
        return $wilayah;
    }
}