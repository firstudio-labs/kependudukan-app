<?php

namespace App\Http\Controllers\superadmin;

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
        $this->authorizeSuperadmin();

        // Ambil parameter filter
        $provinceId = $request->get('province_id');
        $districtId = $request->get('district_id');
        $subDistrictId = $request->get('sub_district_id');
        $villageId = $request->get('village_id');

        // Ambil semua data penduduk dengan no_hp
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Ambil data provinsi
        $provinces = $wilayahService->getProvinces();
        
        // Jika ada filter, tampilkan data yang difilter berdasarkan level
        if ($provinceId || $districtId || $subDistrictId || $villageId) {
            return $this->showFilteredData($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService);
        }
        
        // Hitung jumlah pengguna mobile per provinsi
        $provinceStats = $this->calculateProvinceStats($allCitizens, $provinces);

        // Pagination untuk provinsi
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($provinceStats);
        $items = collect($provinceStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.index', [
            'provinces' => $paginator,
            'provincesList' => $provinces,
            'districts' => [],
            'subDistricts' => [],
            'villages' => [],
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => $subDistrictId,
            'villageId' => $villageId,
        ]);
    }

    public function showProvince(Request $request, $provinceId, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

        // Ambil data provinsi
        $provinces = $wilayahService->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        // Ambil data kabupaten
        $districts = $wilayahService->getKabupaten($province['code']);
        
        // Ambil semua data penduduk dengan no_hp
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Hitung jumlah pengguna mobile per kabupaten
        $districtStats = $this->calculateDistrictStats($allCitizens, $districts, $provinceId);

        // Pagination untuk kabupaten
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($districtStats);
        $items = collect($districtStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.province', [
            'province' => $province,
            'districts' => $paginator,
            'provincesList' => $provinces,
            'provinceId' => $provinceId,
            'districtId' => null,
            'subDistrictId' => null,
            'villageId' => null,
        ]);
    }

    public function showDistrict(Request $request, $provinceId, $districtId, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

        // Ambil data provinsi dan kabupaten
        $provinces = $wilayahService->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        // Ambil data kecamatan
        $subDistricts = $wilayahService->getKecamatan($district['code']);
        
        // Ambil semua data penduduk dengan no_hp
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Hitung jumlah pengguna mobile per kecamatan
        $subDistrictStats = $this->calculateSubDistrictStats($allCitizens, $subDistricts, $provinceId, $districtId);

        // Pagination untuk kecamatan
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($subDistrictStats);
        $items = collect($subDistrictStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.district', [
            'province' => $province,
            'district' => $district,
            'subDistricts' => $paginator,
        ]);
    }

    public function showSubDistrict(Request $request, $provinceId, $districtId, $subDistrictId, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

        // Ambil data provinsi, kabupaten, dan kecamatan
        $provinces = $wilayahService->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        // Ambil data desa
        $villages = $wilayahService->getDesa($subDistrict['code']);
        
        // Ambil semua data penduduk dengan no_hp
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Hitung jumlah pengguna mobile per desa
        $villageStats = $this->calculateVillageStats($allCitizens, $villages, $provinceId, $districtId, $subDistrictId);

        // Pagination untuk desa
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($villageStats);
        $items = collect($villageStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.subdistrict', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'villages' => $paginator,
        ]);
    }

    public function showVillage(Request $request, $provinceId, $districtId, $subDistrictId, $villageId, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

        // Ambil data wilayah
        $provinces = $wilayahService->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        $villages = $wilayahService->getDesa($subDistrict['code']);
        $village = collect($villages)->firstWhere('id', $villageId);
        
        if (!$village) {
            abort(404, 'Desa tidak ditemukan');
        }

        // Ambil data penduduk dengan no_hp dari desa tertentu
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Filter berdasarkan desa
        $filteredCitizens = collect($allCitizens)->filter(function($citizen) use ($villageId) {
            return isset($citizen['village_id']) && $citizen['village_id'] == $villageId;
        })->values()->all();

        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });

        // Filter hanya yang memiliki no_hp yang valid di DB lokal
        $mobileUsers = collect($filteredCitizens)->filter(function($citizen) use ($localPhones) {
            $nik = $citizen['nik'] ?? '';
            return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
        })->values()->all();

        // Mapping data untuk setiap penduduk
        $mapped = collect($mobileUsers)->map(function ($citizen) use ($wilayahService, $localPhones) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $nik = $citizen['nik'] ?? '';
            $obj->no_hp = $localPhones[$nik] ?? '';
            $obj->kk = $citizen['kk'] ?? $citizen['no_kk'] ?? '-';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // Pagination
        $perPage = 10;
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

        return view('superadmin.mobile-users.village', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'village' => $village,
            'items' => $paginator,
        ]);
    }

    public function showDetail(Request $request, $level, $id, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

        // Ambil semua data penduduk dengan no_hp
        $allCitizens = $this->getAllMobileUsers($citizenService);
        
        // Filter berdasarkan level dan ID
        $filteredCitizens = $this->filterCitizensByLevel($allCitizens, $level, $id, $wilayahService);
        
        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });

        // Mapping data untuk setiap penduduk
        $mapped = collect($filteredCitizens)->map(function ($citizen) use ($wilayahService, $localPhones) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $nik = $citizen['nik'] ?? '';
            $obj->no_hp = $localPhones[$nik] ?? '';
            $obj->kk = $citizen['kk'] ?? $citizen['no_kk'] ?? '-';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // Pagination
        $perPage = 10;
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

        // Ambil informasi level untuk breadcrumb
        $levelInfo = $this->getLevelInfo($level, $id, $wilayahService);

        return view('superadmin.mobile-users.detail', [
            'items' => $paginator,
            'level' => $level,
            'levelInfo' => $levelInfo,
        ]);
    }

    public function show(string $nik, CitizenService $citizenService, WilayahService $wilayahService)
    {
        $this->authorizeSuperadmin();

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

        return view('superadmin.mobile-users.show', compact('item'));
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
     * Ambil semua data penduduk dengan no_hp
     */
    private function getAllMobileUsers(CitizenService $citizenService)
    {
        try {
            // Ambil semua NIK yang memiliki no_hp dari database lokal
            $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
                ->where('no_hp', '!=', '')
                ->pluck('no_hp', 'nik');
            
            $localPhones = collect($localPhones)->filter(function($noHp) {
                $digits = preg_replace('/\D+/', '', (string) $noHp);
                return strlen($digits) >= 8;
            });
            
            \Log::info("MobileUsersController - Valid local phones: " . $localPhones->count());
            
            // Ambil semua data citizen menggunakan getAllCitizensWithHighLimit
            $resp = $citizenService->getAllCitizensWithHighLimit();
            
            \Log::info("MobileUsersController - API Response status: " . ($resp['status'] ?? 'unknown'));
            \Log::info("MobileUsersController - API Response has data: " . (isset($resp['data']) ? 'yes' : 'no'));
            \Log::info("MobileUsersController - API Response data structure: " . json_encode(array_keys($resp['data'] ?? [])));
            
            // Cek berbagai kemungkinan struktur response
            $allCitizens = [];
            if (isset($resp['data']['citizens']) && is_array($resp['data']['citizens'])) {
                $allCitizens = $resp['data']['citizens'];
                \Log::info("MobileUsersController - Found citizens in data.citizens: " . count($allCitizens));
            } elseif (isset($resp['citizens']) && is_array($resp['citizens'])) {
                $allCitizens = $resp['citizens'];
                \Log::info("MobileUsersController - Found citizens in citizens: " . count($allCitizens));
            } elseif (isset($resp['data']) && is_array($resp['data'])) {
                $allCitizens = $resp['data'];
                \Log::info("MobileUsersController - Found citizens in data: " . count($allCitizens));
            } else {
                \Log::info("MobileUsersController - No citizens found in response");
            }
            
            if ($allCitizens instanceof \Illuminate\Support\Collection) {
                $allCitizens = $allCitizens->toArray();
            } else if (!is_array($allCitizens)) {
                $allCitizens = [];
            }

            \Log::info("MobileUsersController - Total citizens loaded: " . count($allCitizens));
            
            // Debug: Cek sample data citizen
            if (count($allCitizens) > 0) {
                $sampleCitizen = $allCitizens[0];
                \Log::info("MobileUsersController - Sample citizen data: " . json_encode($sampleCitizen));
            }

            // Return semua data tanpa filter no_hp dulu, filter akan dilakukan di method yang memanggil
            return $allCitizens;

        } catch (\Exception $e) {
            \Log::error("Error getting all mobile users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung statistik pengguna mobile per provinsi
     */
    private function calculateProvinceStats($allCitizens, $provinces)
    {
        $stats = [];
        
        \Log::info("MobileUsersController - calculateProvinceStats - Total citizens: " . count($allCitizens));
        
        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });
        
        \Log::info("MobileUsersController - calculateProvinceStats - Valid local phones: " . $localPhones->count());
        
        // Kelompokkan citizen berdasarkan provinsi
        $citizensByProvince = collect($allCitizens)->groupBy(function($citizen) {
            return $citizen['province_id'] ?? 'unknown';
        });
        
        \Log::info("MobileUsersController - calculateProvinceStats - Provinces found in data: " . $citizensByProvince->keys()->implode(', '));
        
        foreach ($provinces as $province) {
            $provinceId = $province['id'];
            $citizensInProvince = $citizensByProvince->get($provinceId, collect());
            
            \Log::info("MobileUsersController - calculateProvinceStats - Province {$province['name']} (ID: {$provinceId}) - Citizens in province: " . $citizensInProvince->count());
            
            // Hitung yang memiliki no_hp valid
            $count = $citizensInProvince->filter(function($citizen) use ($localPhones) {
                $nik = $citizen['nik'] ?? '';
                return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
            })->count();
            
            \Log::info("MobileUsersController - calculateProvinceStats - Province {$province['name']} - Mobile users count: {$count}");
            
            $stats[] = [
                'id' => $province['id'],
                'name' => $province['name'],
                'mobile_users_count' => $count,
            ];
        }
        
        return $stats;
    }

    /**
     * Hitung statistik pengguna mobile per kabupaten
     */
    private function calculateDistrictStats($allCitizens, $districts, $provinceId)
    {
        $stats = [];
        
        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });
        
        // Kelompokkan citizen berdasarkan kabupaten di provinsi tertentu
        $citizensByDistrict = collect($allCitizens)->filter(function($citizen) use ($provinceId) {
            return isset($citizen['province_id']) && $citizen['province_id'] == $provinceId;
        })->groupBy(function($citizen) {
            return $citizen['district_id'] ?? 'unknown';
        });
        
        \Log::info("MobileUsersController - calculateDistrictStats - Province ID: {$provinceId} - Districts found in data: " . $citizensByDistrict->keys()->implode(', '));
        
        foreach ($districts as $district) {
            $districtId = $district['id'];
            $citizensInDistrict = $citizensByDistrict->get($districtId, collect());
            
            \Log::info("MobileUsersController - calculateDistrictStats - District {$district['name']} (ID: {$districtId}) - Citizens in district: " . $citizensInDistrict->count());
            
            // Hitung yang memiliki no_hp valid
            $count = $citizensInDistrict->filter(function($citizen) use ($localPhones) {
                $nik = $citizen['nik'] ?? '';
                return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
            })->count();
            
            \Log::info("MobileUsersController - calculateDistrictStats - District {$district['name']} - Mobile users count: {$count}");
            
            $stats[] = [
                'id' => $district['id'],
                'name' => $district['name'],
                'mobile_users_count' => $count,
            ];
        }
        
        return $stats;
    }

    /**
     * Hitung statistik pengguna mobile per kecamatan
     */
    private function calculateSubDistrictStats($allCitizens, $subDistricts, $provinceId, $districtId)
    {
        $stats = [];
        
        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });
        
        // Kelompokkan citizen berdasarkan kecamatan di kabupaten tertentu
        $citizensBySubDistrict = collect($allCitizens)->filter(function($citizen) use ($provinceId, $districtId) {
            return isset($citizen['province_id']) && $citizen['province_id'] == $provinceId &&
                   isset($citizen['district_id']) && $citizen['district_id'] == $districtId;
        })->groupBy(function($citizen) {
            return $citizen['sub_district_id'] ?? 'unknown';
        });
        
        \Log::info("MobileUsersController - calculateSubDistrictStats - Province ID: {$provinceId}, District ID: {$districtId} - SubDistricts found in data: " . $citizensBySubDistrict->keys()->implode(', '));
        
        foreach ($subDistricts as $subDistrict) {
            $subDistrictId = $subDistrict['id'];
            $citizensInSubDistrict = $citizensBySubDistrict->get($subDistrictId, collect());
            
            \Log::info("MobileUsersController - calculateSubDistrictStats - SubDistrict {$subDistrict['name']} (ID: {$subDistrictId}) - Citizens in subdistrict: " . $citizensInSubDistrict->count());
            
            // Hitung yang memiliki no_hp valid
            $count = $citizensInSubDistrict->filter(function($citizen) use ($localPhones) {
                $nik = $citizen['nik'] ?? '';
                return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
            })->count();
            
            \Log::info("MobileUsersController - calculateSubDistrictStats - SubDistrict {$subDistrict['name']} - Mobile users count: {$count}");
            
            $stats[] = [
                'id' => $subDistrict['id'],
                'name' => $subDistrict['name'],
                'mobile_users_count' => $count,
            ];
        }
        
        return $stats;
    }

    /**
     * Hitung statistik pengguna mobile per desa
     */
    private function calculateVillageStats($allCitizens, $villages, $provinceId, $districtId, $subDistrictId)
    {
        $stats = [];
        
        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });
        
        // Kelompokkan citizen berdasarkan desa di kecamatan tertentu
        $citizensByVillage = collect($allCitizens)->filter(function($citizen) use ($provinceId, $districtId, $subDistrictId) {
            return isset($citizen['province_id']) && $citizen['province_id'] == $provinceId &&
                   isset($citizen['district_id']) && $citizen['district_id'] == $districtId &&
                   isset($citizen['sub_district_id']) && $citizen['sub_district_id'] == $subDistrictId;
        })->groupBy(function($citizen) {
            return $citizen['village_id'] ?? 'unknown';
        });
        
        \Log::info("MobileUsersController - calculateVillageStats - Province ID: {$provinceId}, District ID: {$districtId}, SubDistrict ID: {$subDistrictId} - Villages found in data: " . $citizensByVillage->keys()->implode(', '));
        
        foreach ($villages as $village) {
            $villageId = $village['id'];
            $citizensInVillage = $citizensByVillage->get($villageId, collect());
            
            \Log::info("MobileUsersController - calculateVillageStats - Village {$village['name']} (ID: {$villageId}) - Citizens in village: " . $citizensInVillage->count());
            
            // Hitung yang memiliki no_hp valid
            $count = $citizensInVillage->filter(function($citizen) use ($localPhones) {
                $nik = $citizen['nik'] ?? '';
                return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
            })->count();
            
            \Log::info("MobileUsersController - calculateVillageStats - Village {$village['name']} - Mobile users count: {$count}");
            
            $stats[] = [
                'id' => $village['id'],
                'name' => $village['name'],
                'mobile_users_count' => $count,
            ];
        }
        
        return $stats;
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

    /**
     * Filter citizens berdasarkan level dan ID
     */
    private function filterCitizensByLevel($allCitizens, $level, $id, $wilayahService)
    {
        switch ($level) {
            case 'province':
                return collect($allCitizens)->filter(function($citizen) use ($id) {
                    return isset($citizen['province_id']) && $citizen['province_id'] == $id;
                })->values()->all();
                
            case 'district':
                return collect($allCitizens)->filter(function($citizen) use ($id) {
                    return isset($citizen['district_id']) && $citizen['district_id'] == $id;
                })->values()->all();
                
            case 'subdistrict':
                return collect($allCitizens)->filter(function($citizen) use ($id) {
                    return isset($citizen['sub_district_id']) && $citizen['sub_district_id'] == $id;
                })->values()->all();
                
            case 'village':
                return collect($allCitizens)->filter(function($citizen) use ($id) {
                    return isset($citizen['village_id']) && $citizen['village_id'] == $id;
                })->values()->all();
                
            default:
                return [];
        }
    }

    /**
     * Ambil informasi level untuk breadcrumb
     */
    private function getLevelInfo($level, $id, $wilayahService)
    {
        try {
            switch ($level) {
                case 'province':
                    $provinces = $wilayahService->getProvinces();
                    $province = collect($provinces)->firstWhere('id', $id);
                    return [
                        'type' => 'province',
                        'name' => $province['name'] ?? 'Provinsi',
                        'breadcrumb' => $province['name'] ?? 'Provinsi'
                    ];
                    
                case 'district':
                    $provinces = $wilayahService->getProvinces();
                    $districts = [];
                    $province = null;
                    
                    // Cari provinsi yang mengandung kabupaten ini
                    foreach ($provinces as $prov) {
                        $kabupaten = $wilayahService->getKabupaten($prov['code']);
                        $district = collect($kabupaten)->firstWhere('id', $id);
                        if ($district) {
                            $districts = $kabupaten;
                            $province = $prov;
                            break;
                        }
                    }
                    
                    $district = collect($districts)->firstWhere('id', $id);
                    return [
                        'type' => 'district',
                        'name' => $district['name'] ?? 'Kabupaten/Kota',
                        'breadcrumb' => ($province['name'] ?? 'Provinsi') . ' → ' . ($district['name'] ?? 'Kabupaten/Kota')
                    ];
                    
                case 'subdistrict':
                    $provinces = $wilayahService->getProvinces();
                    $subDistricts = [];
                    $province = null;
                    $district = null;
                    
                    // Cari provinsi dan kabupaten yang mengandung kecamatan ini
                    foreach ($provinces as $prov) {
                        $kabupaten = $wilayahService->getKabupaten($prov['code']);
                        foreach ($kabupaten as $kab) {
                            $kecamatan = $wilayahService->getKecamatan($kab['code']);
                            $subDistrict = collect($kecamatan)->firstWhere('id', $id);
                            if ($subDistrict) {
                                $subDistricts = $kecamatan;
                                $province = $prov;
                                $district = $kab;
                                break 2;
                            }
                        }
                    }
                    
                    $subDistrict = collect($subDistricts)->firstWhere('id', $id);
                    return [
                        'type' => 'subdistrict',
                        'name' => $subDistrict['name'] ?? 'Kecamatan',
                        'breadcrumb' => ($province['name'] ?? 'Provinsi') . ' → ' . ($district['name'] ?? 'Kabupaten/Kota') . ' → ' . ($subDistrict['name'] ?? 'Kecamatan')
                    ];
                    
                case 'village':
                    $provinces = $wilayahService->getProvinces();
                    $villages = [];
                    $province = null;
                    $district = null;
                    $subDistrict = null;
                    
                    // Cari provinsi, kabupaten, dan kecamatan yang mengandung desa ini
                    foreach ($provinces as $prov) {
                        $kabupaten = $wilayahService->getKabupaten($prov['code']);
                        foreach ($kabupaten as $kab) {
                            $kecamatan = $wilayahService->getKecamatan($kab['code']);
                            foreach ($kecamatan as $kec) {
                                $desa = $wilayahService->getDesa($kec['code']);
                                $village = collect($desa)->firstWhere('id', $id);
                                if ($village) {
                                    $villages = $desa;
                                    $province = $prov;
                                    $district = $kab;
                                    $subDistrict = $kec;
                                    break 3;
                                }
                            }
                        }
                    }
                    
                    $village = collect($villages)->firstWhere('id', $id);
                    return [
                        'type' => 'village',
                        'name' => $village['name'] ?? 'Desa/Kelurahan',
                        'breadcrumb' => ($province['name'] ?? 'Provinsi') . ' → ' . ($district['name'] ?? 'Kabupaten/Kota') . ' → ' . ($subDistrict['name'] ?? 'Kecamatan') . ' → ' . ($village['name'] ?? 'Desa/Kelurahan')
                    ];
                    
                default:
                    return [
                        'type' => 'unknown',
                        'name' => 'Wilayah',
                        'breadcrumb' => 'Wilayah'
                    ];
            }
        } catch (\Exception $e) {
            \Log::error('Error getting level info: ' . $e->getMessage());
            return [
                'type' => 'error',
                'name' => 'Wilayah',
                'breadcrumb' => 'Wilayah'
            ];
        }
    }

    /**
     * Tampilkan data yang difilter berdasarkan wilayah
     */
    private function showFilteredData($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService)
    {
        // Tentukan level filter dan tampilkan data sesuai level
        if ($villageId) {
            // Level desa - tampilkan detail pengguna mobile di desa tersebut (show.blade.php)
            return $this->showVillageDetail($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService);
        } elseif ($subDistrictId) {
            // Level kecamatan - tampilkan desa-desa di kecamatan tersebut (tabel desa)
            return $this->showSubDistrictData($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $wilayahService);
        } elseif ($districtId) {
            // Level kabupaten - tampilkan kecamatan-kecamatan di kabupaten tersebut (tabel kecamatan)
            return $this->showDistrictData($request, $allCitizens, $provinces, $provinceId, $districtId, $wilayahService);
        } elseif ($provinceId) {
            // Level provinsi - tampilkan kabupaten-kabupaten di provinsi tersebut (tabel kabupaten)
            return $this->showProvinceData($request, $allCitizens, $provinces, $provinceId, $wilayahService);
        }

        // Fallback ke tampilan provinsi
        return $this->index($request, app(CitizenService::class), $wilayahService);
    }

    /**
     * Tampilkan data provinsi dengan kabupaten-kabupaten di dalamnya
     */
    private function showProvinceData($request, $allCitizens, $provinces, $provinceId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $districtStats = $this->calculateDistrictStats($allCitizens, $districts, $provinceId);

        // Pagination untuk kabupaten
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($districtStats);
        $items = collect($districtStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.province', [
            'province' => $province,
            'districts' => $paginator,
            'provincesList' => $provinces,
            'provinceId' => $provinceId,
            'districtId' => null,
            'subDistrictId' => null,
            'villageId' => null,
        ]);
    }

    /**
     * Tampilkan data kabupaten dengan kecamatan-kecamatan di dalamnya
     */
    private function showDistrictData($request, $allCitizens, $provinces, $provinceId, $districtId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrictStats = $this->calculateSubDistrictStats($allCitizens, $subDistricts, $provinceId, $districtId);

        // Pagination untuk kecamatan
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($subDistrictStats);
        $items = collect($subDistrictStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.district', [
            'province' => $province,
            'district' => $district,
            'subDistricts' => $paginator,
            'provincesList' => $provinces,
            'districts' => $districts,
            'subDistrictsList' => $subDistricts,
            'villages' => [],
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => null,
            'villageId' => null,
        ]);
    }

    /**
     * Tampilkan data kecamatan dengan desa-desa di dalamnya
     */
    private function showSubDistrictData($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        $villages = $wilayahService->getDesa($subDistrict['code']);
        $villageStats = $this->calculateVillageStats($allCitizens, $villages, $provinceId, $districtId, $subDistrictId);

        // Pagination untuk desa
        $perPage = 10;
        $currentPage = (int) max(1, (int) $request->get('page', 1));
        $total = count($villageStats);
        $items = collect($villageStats)->slice(($currentPage - 1) * $perPage, $perPage)->values();
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

        return view('superadmin.mobile-users.subdistrict', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'villages' => $paginator,
            'provincesList' => $provinces,
            'districts' => $districts,
            'subDistricts' => $subDistricts,
            'villagesList' => $villages,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => $subDistrictId,
            'villageId' => null,
        ]);
    }

    /**
     * Tampilkan data desa dengan pengguna mobile di dalamnya
     */
    private function showVillageData($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        $villages = $wilayahService->getDesa($subDistrict['code']);
        $village = collect($villages)->firstWhere('id', $villageId);
        if (!$village) {
            abort(404, 'Desa tidak ditemukan');
        }

        // Filter pengguna mobile di desa tersebut
        $mobileUsers = $this->getMobileUsersInVillage($allCitizens, $villageId);

        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });

        // Mapping data untuk setiap penduduk
        $mapped = collect($mobileUsers)->map(function ($citizen) use ($wilayahService, $localPhones) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $nik = $citizen['nik'] ?? '';
            $obj->no_hp = $localPhones[$nik] ?? '';
            $obj->kk = $citizen['kk'] ?? $citizen['no_kk'] ?? '-';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // Pagination
        $perPage = 10;
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

        return view('superadmin.mobile-users.village', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'village' => $village,
            'items' => $paginator,
            'provincesList' => $provinces,
            'districts' => $districts,
            'subDistricts' => $subDistricts,
            'villages' => $villages,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => $subDistrictId,
            'villageId' => $villageId,
        ]);
    }

    /**
     * Tampilkan detail pengguna mobile di desa yang dipilih dari filter (show.blade.php)
     */
    private function showVillageDetail($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        $villages = $wilayahService->getDesa($subDistrict['code']);
        $village = collect($villages)->firstWhere('id', $villageId);
        if (!$village) {
            abort(404, 'Desa tidak ditemukan');
        }

        // Filter pengguna mobile di desa tersebut
        $mobileUsers = $this->getMobileUsersInVillage($allCitizens, $villageId);
        
        \Log::info("MobileUsersController - Village ID: {$villageId}, Found mobile users: " . count($mobileUsers));

        // Siapkan mapping no_hp dari DB lokal
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });

        \Log::info("MobileUsersController - Local phones count: " . $localPhones->count());

        // Filter hanya yang memiliki no_hp yang valid di DB lokal
        $mobileUsers = collect($mobileUsers)->filter(function($citizen) use ($localPhones) {
            $nik = $citizen['nik'] ?? '';
            return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
        })->values()->all();
        
        \Log::info("MobileUsersController - After local phone filter: " . count($mobileUsers));

        // Mapping data untuk setiap penduduk
        $mapped = collect($mobileUsers)->map(function ($citizen) use ($wilayahService, $localPhones) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $nik = $citizen['nik'] ?? '';
            $obj->no_hp = $localPhones[$nik] ?? '';
            $obj->kk = $citizen['kk'] ?? $citizen['no_kk'] ?? '-';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // Pagination
        $perPage = 10;
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

        return view('superadmin.mobile-users.show', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'village' => $village,
            'items' => $paginator,
            'provincesList' => $provinces,
            'districts' => $districts,
            'subDistricts' => $subDistricts,
            'villages' => $villages,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => $subDistrictId,
            'villageId' => $villageId,
        ]);
    }


    /**
     * Tampilkan pengguna mobile di desa yang dipilih dari filter (seperti admin desa)
     */
    private function showVillageMobileUsers($request, $allCitizens, $provinces, $provinceId, $districtId, $subDistrictId, $villageId, $wilayahService)
    {
        $province = collect($provinces)->firstWhere('id', $provinceId);
        if (!$province) {
            abort(404, 'Provinsi tidak ditemukan');
        }

        $districts = $wilayahService->getKabupaten($province['code']);
        $district = collect($districts)->firstWhere('id', $districtId);
        if (!$district) {
            abort(404, 'Kabupaten tidak ditemukan');
        }

        $subDistricts = $wilayahService->getKecamatan($district['code']);
        $subDistrict = collect($subDistricts)->firstWhere('id', $subDistrictId);
        if (!$subDistrict) {
            abort(404, 'Kecamatan tidak ditemukan');
        }

        $villages = $wilayahService->getDesa($subDistrict['code']);
        $village = collect($villages)->firstWhere('id', $villageId);
        if (!$village) {
            abort(404, 'Desa tidak ditemukan');
        }

        // Filter pengguna mobile di desa tersebut (seperti admin desa)
        $mobileUsers = $this->getMobileUsersInVillage($allCitizens, $villageId);
        
        \Log::info("MobileUsersController - Village ID: {$villageId}, Found mobile users: " . count($mobileUsers));

        // Siapkan mapping no_hp dari DB lokal (seperti admin desa)
        $localPhones = \App\Models\Penduduk::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp', 'nik');
        
        $localPhones = collect($localPhones)->filter(function($noHp) {
            $digits = preg_replace('/\D+/', '', (string) $noHp);
            return strlen($digits) >= 8;
        });

        \Log::info("MobileUsersController - Local phones count: " . $localPhones->count());

        // Filter hanya yang memiliki no_hp yang valid di DB lokal (seperti admin desa)
        $mobileUsers = collect($mobileUsers)->filter(function($citizen) use ($localPhones) {
            $nik = $citizen['nik'] ?? '';
            return isset($localPhones[$nik]) && trim((string)$localPhones[$nik]) !== '';
        })->values()->all();
        
        \Log::info("MobileUsersController - After local phone filter: " . count($mobileUsers));

        // Mapping data untuk setiap penduduk (seperti admin desa)
        $mapped = collect($mobileUsers)->map(function ($citizen) use ($wilayahService, $localPhones) {
            $obj = new \stdClass();
            $obj->nik = $citizen['nik'] ?? '';
            $obj->full_name = $citizen['full_name'] ?? $citizen['nama_lengkap'] ?? '-';
            $nik = $citizen['nik'] ?? '';
            $obj->no_hp = $localPhones[$nik] ?? '';
            $obj->kk = $citizen['kk'] ?? $citizen['no_kk'] ?? '-';
            $obj->wilayah = $this->getWilayahNames($wilayahService, $citizen);
            return $obj;
        });

        // Pagination (seperti admin desa)
        $perPage = 10;
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

        return view('superadmin.mobile-users.village', [
            'province' => $province,
            'district' => $district,
            'subDistrict' => $subDistrict,
            'village' => $village,
            'items' => $paginator,
            'provincesList' => $provinces,
            'districts' => $districts,
            'subDistricts' => $subDistricts,
            'villages' => $villages,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'subDistrictId' => $subDistrictId,
            'villageId' => $villageId,
        ]);
    }

    /**
     * Ambil pengguna mobile di desa tertentu
     */
    private function getMobileUsersInVillage($allCitizens, $villageId)
    {
        return collect($allCitizens)->filter(function($citizen) use ($villageId) {
            return isset($citizen['village_id']) && $citizen['village_id'] == $villageId;
        })->values()->all();
    }



    private function authorizeSuperadmin(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }
    }
}