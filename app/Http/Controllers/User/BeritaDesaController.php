<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use App\Services\WilayahService;
use Illuminate\Http\Request;

class BeritaDesaController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request)
    {
        $query = BeritaDesa::with(['user']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);
        
        // Tambahkan data wilayah untuk setiap berita
        foreach ($berita as $item) {
            $item->wilayah_info = $this->getWilayahInfo($item);
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