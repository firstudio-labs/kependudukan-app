<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Services\CitizenService;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MobileUsersController extends Controller
{
    public function index(Request $request, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeAdminDesa();

        $adminVillageId = Auth::user()->villages_id;
        $search = $request->get('search');
        
        \Log::info("MobileUsersController - Admin Village ID: {$adminVillageId}, Search: {$search}");

        // 1. Ambil data penduduk dari CitizenService berdasarkan villages_id admin
        $perPage = 10;
        
        try {
            // Ambil semua penduduk dari desa admin menggunakan CitizenService
            $citizensResponse = $citizenService->getCitizensByVillageId($adminVillageId, 1, 1000);
            $allCitizens = $citizensResponse['data']['citizens'] ?? [];
            
            \Log::info("Found " . count($allCitizens) . " citizens in village {$adminVillageId}");
            
            // Filter berdasarkan search jika ada
            if ($search) {
                $allCitizens = collect($allCitizens)->filter(function($citizen) use ($search) {
                    $nik = $citizen['nik'] ?? '';
                    $noHp = $citizen['no_hp'] ?? '';
                    $fullName = $citizen['full_name'] ?? '';
                    
                    return str_contains(strtolower($nik), strtolower($search)) ||
                           str_contains(strtolower($noHp), strtolower($search)) ||
                           str_contains(strtolower($fullName), strtolower($search));
                })->values()->all();
                
                \Log::info("After search filter: " . count($allCitizens) . " citizens");
            }
            
            // Filter hanya yang memiliki no_hp
            $allCitizens = collect($allCitizens)->filter(function($citizen) {
                $noHp = $citizen['no_hp'] ?? '';
                return !empty($noHp) && $noHp !== '';
            })->values()->all();
            
            \Log::info("After no_hp filter: " . count($allCitizens) . " citizens");
            
        } catch (\Exception $e) {
            \Log::error("Error getting citizens by village ID: " . $e->getMessage());
            $allCitizens = [];
        }

        // 2. Mapping data untuk setiap penduduk
        $mapped = collect($allCitizens)->map(function ($citizen) use ($wilayahService) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $obj->no_hp = $citizen['no_hp'] ?? '';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // 3. Bangun paginator manual
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = $mapped->count();
        $items = $mapped->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('admin.desa.mobile-users.index', [
            'items' => $paginator,
            'search' => $search,
        ]);
    }

    public function show(string $nik, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeAdminDesa();

        $citizenData = $this->getCitizenDataByNik($citizenService, $nik);
        if (!$citizenData) {
            abort(404);
        }

        // Fallback ke database lokal jika ada data yang tidak lengkap
        $local = Penduduk::where('nik', $nik)->first();

        $wilayah = $this->getWilayahNames($wilayahService, $citizenData);

        $item = (object) [
            'nik' => $nik,
            'full_name' => $citizenData['full_name'] ?? ($citizenData['nama_lengkap'] ?? '-'),
            'no_hp' => $citizenData['no_hp'] ?? ($local->no_hp ?? null),
            'kk' => $citizenData['kk'] ?? ($citizenData['no_kk'] ?? ($local->no_kk ?? null)),
            'wilayah' => $wilayah,
            'raw' => $citizenData,
        ];

        return view('admin.desa.mobile-users.show', compact('item'));
    }

    /**
     * Ambil data lengkap penduduk dari CitizenService berdasarkan NIK
     */
    private function getCitizenDataByNik(CitizenService $citizenService, $nik)
    {
        try {
            // Ambil data penduduk berdasarkan NIK menggunakan method yang sudah ada
            $response = $citizenService->getCitizenByNIK($nik);
            
            if (isset($response['data'])) {
                \Log::info("Successfully retrieved citizen data for NIK: {$nik}");
                return $response['data'];
            }
            
            \Log::warning("No data found for NIK: {$nik}");
            return null;
        } catch (\Exception $e) {
            \Log::error("Error getting citizen data for NIK {$nik}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil nama wilayah menggunakan pendekatan yang sama seperti BeritaDesa
     */
    private function getWilayahNames(WilayahService $wilayahService, $citizenData)
    {
        if (!$citizenData) {
            return [
                'provinsi' => '-',
                'kabupaten' => '-',
                'kecamatan' => '-',
                'desa' => '-',
            ];
        }

        $wilayah = [];
        
        // Always set fallback first for safety (seperti di BeritaDesa)
        if (isset($citizenData['province_id']) && $citizenData['province_id']) {
            $wilayah['provinsi'] = 'Provinsi ID: ' . $citizenData['province_id'];
            
            try {
                $provinces = $wilayahService->getProvinces();
                
                if (is_array($provinces) && !empty($provinces)) {
                    // Provinsi menggunakan 'id' field, BUKAN 'code' field
                    $province = collect($provinces)->firstWhere('id', (int) $citizenData['province_id']);
                    
                    if ($province && isset($province['name'])) {
                        $wilayah['provinsi'] = $province['name'];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error getting province info: ' . $e->getMessage());
            }
        }
        
        if (isset($citizenData['district_id']) && $citizenData['district_id']) {
            $wilayah['kabupaten'] = 'Kabupaten ID: ' . $citizenData['district_id'];
            
            try {
                if (isset($citizenData['province_id']) && $citizenData['province_id']) {
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $citizenData['province_id']);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Kabupaten menggunakan 'id' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $citizenData['district_id']);
                                
                                if ($kabupatenData && isset($kabupatenData['name'])) {
                                    $wilayah['kabupaten'] = $kabupatenData['name'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error getting kabupaten info: ' . $e->getMessage());
            }
        }
        
        if (isset($citizenData['sub_district_id']) && $citizenData['sub_district_id']) {
            $wilayah['kecamatan'] = 'Kecamatan ID: ' . $citizenData['sub_district_id'];
            
            try {
                if (isset($citizenData['district_id']) && $citizenData['district_id'] && isset($citizenData['province_id']) && $citizenData['province_id']) {
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $citizenData['province_id']);
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $citizenData['district_id']);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    // Gunakan kabupaten CODE untuk API call kecamatan
                                    $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Kecamatan menggunakan 'id' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $citizenData['sub_district_id']);
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
                \Log::error('Error getting kecamatan info: ' . $e->getMessage());
            }
        }
        
        if (isset($citizenData['village_id']) && $citizenData['village_id']) {
            $wilayah['desa'] = 'Desa ID: ' . $citizenData['village_id'];
            
            try {
                if (isset($citizenData['sub_district_id']) && $citizenData['sub_district_id'] && 
                    isset($citizenData['district_id']) && $citizenData['district_id'] && 
                    isset($citizenData['province_id']) && $citizenData['province_id']) {
                    
                    // Dapatkan province code dulu untuk API call kabupaten
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $citizenData['province_id']);
                        if ($provinceData && isset($provinceData['code'])) {
                            // Gunakan province CODE untuk API call kabupaten
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $citizenData['district_id']);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    // Gunakan kabupaten CODE untuk API call kecamatan
                                    $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $citizenData['sub_district_id']);
                                        if ($kecamatanData && isset($kecamatanData['code'])) {
                                            // Gunakan kecamatan CODE untuk API call desa
                                            $desa = $wilayahService->getDesa($kecamatanData['code']);
                                            if (is_array($desa) && !empty($desa)) {
                                                // Desa menggunakan 'id' field
                                                $desaData = collect($desa)->firstWhere('id', (int) $citizenData['village_id']);
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
                \Log::error('Error getting desa info: ' . $e->getMessage());
            }
        }

        return [
            'provinsi' => $wilayah['provinsi'] ?? '-',
            'kabupaten' => $wilayah['kabupaten'] ?? '-',
            'kecamatan' => $wilayah['kecamatan'] ?? '-',
            'desa' => $wilayah['desa'] ?? '-',
        ];
    }

    private function authorizeAdminDesa(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin desa') {
            abort(403, 'Unauthorized');
        }
    }
}


