<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Services\CitizenService;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileUsersController extends Controller
{
    public function index(Request $request, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeAdminDesa();

        $adminVillageId = Auth::user()->villages_id;
        $search = $request->get('search');

        // 1. Ambil data penduduk dari tabel penduduk yang memiliki no_hp (tidak NULL)
        $query = Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '');

        // 2. Filter berdasarkan search (NIK atau no_hp)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // 3. Pagination dari database
        $perPage = 10;
        $penduduks = $query->paginate($perPage);

        // 4. Ambil data admin untuk perbandingan wilayah
        $adminCitizenData = $this->getCitizenDataByNik($citizenService, Auth::user()->nik);
        if (!$adminCitizenData) {
            // Jika data admin tidak ditemukan, return empty data
            return view('admin.desa.mobile-users.index', [
                'items' => $penduduks,
                'search' => $search,
            ]);
        }

        $adminProvinceId = $adminCitizenData['province_id'] ?? null;
        $adminDistrictId = $adminCitizenData['district_id'] ?? null;
        $adminSubDistrictId = $adminCitizenData['sub_district_id'] ?? null;

        // 5. Mapping data untuk setiap penduduk
        $mapped = $penduduks->getCollection()->map(function ($penduduk) use ($citizenService, $wilayahService, $adminVillageId, $adminProvinceId, $adminDistrictId, $adminSubDistrictId) {
            // 6. Ambil data lengkap dari CitizenService menggunakan NIK
            $citizenData = $this->getCitizenDataByNik($citizenService, $penduduk->nik);
            
            // 7. Cek apakah data wilayah dari CitizenService sama dengan admin
            if (!$citizenData) {
                return null; // Skip jika data tidak ditemukan
            }

            $pendudukProvinceId = $citizenData['province_id'] ?? null;
            $pendudukDistrictId = $citizenData['district_id'] ?? null;
            $pendudukSubDistrictId = $citizenData['sub_district_id'] ?? null;
            $pendudukVillageId = $citizenData['village_id'] ?? null;

            // Cek apakah penduduk dan admin dari wilayah yang sama (provinsi, kabupaten, kecamatan, desa)
            if ($pendudukProvinceId != $adminProvinceId || 
                $pendudukDistrictId != $adminDistrictId || 
                $pendudukSubDistrictId != $adminSubDistrictId || 
                $pendudukVillageId != $adminVillageId) {
                return null; // Skip jika bukan dari wilayah yang sama
            }

            $obj = new \stdClass();
            $obj->nik = $penduduk->nik;
            // 8. Ambil fullname dari CitizenService (prioritas) atau fallback ke database lokal
            $obj->full_name = $citizenData['full_name'] ?? $citizenData['nama_lengkap'] ?? $penduduk->nama_lengkap ?? $penduduk->nama ?? '-';
            $obj->no_hp = $penduduk->no_hp;
            
            // 9. Ambil nama wilayah dari CitizenService (prioritas) atau WilayahService (fallback)
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizenData);

            return $obj;
        })->filter(); // Remove null values

        // 8. Replace collection dengan data yang sudah di-mapping dan difilter
        $penduduks->setCollection($mapped);

        return view('admin.desa.mobile-users.index', [
            'items' => $penduduks,
            'search' => $search,
        ]);
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
                return $response['data'];
            }
            
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


