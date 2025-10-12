<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\InformasiUsaha;
use App\Models\BarangWarungku;
use App\Models\Penduduk;
use App\Services\CitizenService;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarungkuController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdminDesa();

        $villageId = Auth::user()->villages_id;
        $search = $request->get('search');
        $kelompok = $request->get('kelompok');
        $status = $request->get('status', 'semua'); // Default tampilkan semua (aktif dan tidak aktif)

        $query = InformasiUsaha::query()
            ->with(['penduduk','user'])
            ->where('villages_id', $villageId);

        // Filter berdasarkan status
        if ($status === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($status === 'tidak_aktif') {
            $query->where('status', 'tidak_aktif');
        }
        // Jika status = 'semua', tidak ada filter status

        if (!empty($kelompok)) {
            $query->where('kelompok_usaha', $kelompok);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(10);

        // Lengkapi nama pemilik via CitizenService berdasarkan NIK
        $svc = app(CitizenService::class);
        $items->getCollection()->transform(function ($item) use ($svc) {
            $nik = optional($item->penduduk)->nik;
            $ownerName = optional($item->penduduk)->nama;

            if (!empty($nik)) {
                try {
                    $res = $svc->getCitizenByNIK($nik);
                    if (is_array($res)) {
                        $ownerName = $res['data']['full_name'] ?? $res['full_name'] ?? $ownerName;
                    }
                } catch (\Throwable $e) {
                    // fallback ke nama lokal
                }
            } elseif (empty($ownerName)) {
                $informasiUsaha = InformasiUsaha::find($item->informasi_usaha_id);
                $owner = $informasiUsaha ? Penduduk::find($informasiUsaha->penduduk_id) : null;
                $ownerName = $owner->nama ?? null;
            }

            $item->owner_name = $ownerName;
            
            // Add individual location names using WilayahService
            $locationData = $this->getLocationData(
                $item->province_id,
                $item->districts_id,
                $item->sub_districts_id,
                $item->villages_id
            );
            
            $item->province_name = $locationData['province'];
            $item->district_name = $locationData['district'];
            $item->sub_district_name = $locationData['sub_district'];
            $item->village_name = $locationData['village'];
            
            // Fallback: if API data is not available, use location_name and parse it
            if ($item->province_name === 'Prov. ' . $item->province_id || 
                $item->district_name === 'Kab. ' . $item->districts_id || 
                $item->sub_district_name === 'Kec. ' . $item->sub_districts_id || 
                $item->village_name === 'Desa ' . $item->villages_id) {
                
                // Use the existing location_name as fallback
                $fullLocation = $this->getLocationName(
                    $item->province_id,
                    $item->districts_id,
                    $item->sub_districts_id,
                    $item->villages_id
                );
                
                // Parse the full location name
                $locationParts = explode(', ', $fullLocation);
                if (count($locationParts) >= 4) {
                    $item->village_name = $locationParts[0];
                    $item->sub_district_name = $locationParts[1];
                    $item->district_name = $locationParts[2];
                    $item->province_name = $locationParts[3];
                }
            }
            
            // Keep the full location name for backward compatibility
            $item->location_name = $this->getLocationName(
                $item->province_id,
                $item->districts_id,
                $item->sub_districts_id,
                $item->villages_id
            );
            
            return $item;
        });

        return view('admin.desa.warungku.index', [
            'items' => $items,
            'filters' => [
                'search' => $search,
                'kelompok' => $kelompok,
                'status' => $status,
            ],
        ]);
    }

    public function show(int $id)
    {
        $this->authorizeAdminDesa();

        $villageId = Auth::user()->villages_id;
        $item = InformasiUsaha::with(['penduduk', 'barangWarungkus.warungkuMaster'])
            ->where('villages_id', $villageId)
            ->findOrFail($id);

        // Resolusi nama pemilik via CitizenService berdasarkan NIK
        $ownerName = optional($item->penduduk)->nama;
        $nik = optional($item->penduduk)->nik;
        if (!empty($nik)) {
            try {
                $svc = app(CitizenService::class);
                $res = $svc->getCitizenByNIK($nik);
                if (is_array($res)) {
                    $ownerName = $res['data']['full_name'] ?? $res['full_name'] ?? $ownerName;
                }
            } catch (\Throwable $e) {
                // fallback ke nama lokal
            }
        } elseif (empty($ownerName)) {
            $informasiUsaha = InformasiUsaha::find($item->informasi_usaha_id);
            $owner = $informasiUsaha ? Penduduk::find($informasiUsaha->penduduk_id) : null;
            $ownerName = $owner->nama ?? null;
        }

        // Add individual location names for show page
        $locationData = $this->getLocationData(
            $item->province_id,
            $item->districts_id,
            $item->sub_districts_id,
            $item->villages_id
        );
        
        $item->province_name = $locationData['province'];
        $item->district_name = $locationData['district'];
        $item->sub_district_name = $locationData['sub_district'];
        $item->village_name = $locationData['village'];
        
        // Fallback: if API data is not available, use location_name and parse it
        if ($item->province_name === 'Prov. ' . $item->province_id || 
            $item->district_name === 'Kab. ' . $item->districts_id || 
            $item->sub_district_name === 'Kec. ' . $item->sub_districts_id || 
            $item->village_name === 'Desa ' . $item->villages_id) {
            
            // Use the existing location_name as fallback
            $fullLocation = $this->getLocationName(
                $item->province_id,
                $item->districts_id,
                $item->sub_districts_id,
                $item->villages_id
            );
            
            // Parse the full location name
            $locationParts = explode(', ', $fullLocation);
            if (count($locationParts) >= 4) {
                $item->village_name = $locationParts[0];
                $item->sub_district_name = $locationParts[1];
                $item->district_name = $locationParts[2];
                $item->province_name = $locationParts[3];
            }
        }

        return view('admin.desa.warungku.show', [
            'item' => $item,
            'ownerName' => $ownerName,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $this->authorizeAdminDesa();

        $villageId = Auth::user()->villages_id;
        $item = InformasiUsaha::where('villages_id', $villageId)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        $item->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status toko berhasil diperbarui');
    }

    private function authorizeAdminDesa(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin desa') {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Get location data for all levels using the same approach as berita desa
     */
    private function getLocationData($provinceId, $districtId, $subDistrictId, $villageId)
    {
        $wilayahService = app(WilayahService::class);
        $locationData = [
            'province' => '-',
            'district' => '-',
            'sub_district' => '-',
            'village' => '-'
        ];

        try {
            // Get province name
            if ($provinceId) {
                $locationData['province'] = 'Provinsi ID: ' . $provinceId;
                
                $provinces = $wilayahService->getProvinces();
                if (is_array($provinces) && !empty($provinces)) {
                    $province = collect($provinces)->firstWhere('id', (int) $provinceId);
                    if ($province && isset($province['name'])) {
                        $locationData['province'] = $province['name'];
                    }
                }
            }

            // Get district name
            if ($districtId) {
                $locationData['district'] = 'Kabupaten ID: ' . $districtId;
                
                // Get district through province
                if ($provinceId) {
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                                if ($kabupatenData && isset($kabupatenData['name'])) {
                                    $locationData['district'] = $kabupatenData['name'];
                                }
                            }
                        }
                    }
                }
            }

            // Get sub-district name
            if ($subDistrictId) {
                $locationData['sub_district'] = 'Kecamatan ID: ' . $subDistrictId;
                
                // Get sub-district through district
                if ($districtId && $provinceId) {
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $subDistrictId);
                                        if ($kecamatanData && isset($kecamatanData['name'])) {
                                            $locationData['sub_district'] = $kecamatanData['name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Get village name
            if ($villageId) {
                $locationData['village'] = 'Desa ID: ' . $villageId;
                
                // Get village through sub-district
                if ($subDistrictId && $districtId && $provinceId) {
                    $provinces = $wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $subDistrictId);
                                        if ($kecamatanData && isset($kecamatanData['code'])) {
                                            $desa = $wilayahService->getDesa($kecamatanData['code']);
                                            if (is_array($desa) && !empty($desa)) {
                                                $desaData = collect($desa)->firstWhere('id', (int) $villageId);
                                                if ($desaData && isset($desaData['name'])) {
                                                    $locationData['village'] = $desaData['name'];
                                                }
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
            \Log::error("Error getting location data: " . $e->getMessage());
        }

        return $locationData;
    }

    /**
     * Get location name by ID and type
     */
    private function getLocationNameById($type, $id)
    {
        if (!$id) {
            return '-';
        }

        $wilayahService = app(WilayahService::class);
        
        try {
            switch ($type) {
                case 'province':
                    $province = $wilayahService->getProvinceById($id);
                    \Log::info("Province data for ID {$id}:", ['data' => $province]);
                    return $province ? $province['name'] : 'Prov. ' . $id;
                    
                case 'district':
                    $district = $wilayahService->getDistrictById($id);
                    \Log::info("District data for ID {$id}:", ['data' => $district]);
                    return $district ? $district['name'] : 'Kab. ' . $id;
                    
                case 'sub_district':
                    $subDistrict = $wilayahService->getSubDistrictById($id);
                    \Log::info("Sub District data for ID {$id}:", ['data' => $subDistrict]);
                    return $subDistrict ? $subDistrict['name'] : 'Kec. ' . $id;
                    
                case 'village':
                    $village = $wilayahService->getVillageById($id);
                    \Log::info("Village data for ID {$id}:", ['data' => $village]);
                    return $village ? $village['name'] : 'Desa ' . $id;
                    
                default:
                    return '-';
            }
        } catch (\Exception $e) {
            \Log::error("Error getting {$type} name for ID {$id}: " . $e->getMessage());
            return $type . ' ' . $id;
        }
    }

    /**
     * Get full location name using WilayahService
     */
private function getLocationName($provinceId, $districtId, $subDistrictId, $villageId)
    {
        $wilayahService = app(WilayahService::class);
        $locationParts = [];

        try {
            // Get village name
            if ($villageId) {
                $village = $wilayahService->getVillageById($villageId);
                if ($village && !empty($village['name'])) {
                    $locationParts[] = $village['name'];
                } else {
                    $locationParts[] = 'Desa ' . $villageId;
                }
            }

            // Get sub-district name
            if ($subDistrictId) {
                $subDistrict = $wilayahService->getSubDistrictById($subDistrictId);
                if ($subDistrict && !empty($subDistrict['name'])) {
                    $locationParts[] = $subDistrict['name'];
                } else {
                    $locationParts[] = 'Kec. ' . $subDistrictId;
                }
            }

            // Get district name
            if ($districtId) {
                $district = $wilayahService->getDistrictById($districtId);
                if ($district && !empty($district['name'])) {
                    $locationParts[] = $district['name'];
                } else {
                    $locationParts[] = 'Kab. ' . $districtId;
                }
            }

            // Get province name
            if ($provinceId) {
                $province = $wilayahService->getProvinceById($provinceId);
                if ($province && !empty($province['name'])) {
                    $locationParts[] = $province['name'];
                } else {
                    $locationParts[] = 'Prov. ' . $provinceId;
                }
            }

            return implode(', ', array_reverse($locationParts));
        } catch (\Exception $e) {
            // Fallback to ID format if service fails
            $fallbackParts = [];
            if ($villageId) $fallbackParts[] = 'Desa ' . $villageId;
            if ($subDistrictId) $fallbackParts[] = 'Kec. ' . $subDistrictId;
            if ($districtId) $fallbackParts[] = 'Kab. ' . $districtId;
            if ($provinceId) $fallbackParts[] = 'Prov. ' . $provinceId;
            
            return implode(', ', array_reverse($fallbackParts));
        }
    }
}


