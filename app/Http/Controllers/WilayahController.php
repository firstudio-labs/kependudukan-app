<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Log;

class WilayahController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function showProvinsi()
    {
        try {
            $provinces = $this->wilayahService->getProvinces();
            Log::info('Provinces data:', ['provinces' => $provinces]);

            // Convert array to collection and paginate
            $perPage = 10; // Number of items per page
            $currentPage = request()->query('page', 1);
            $provinces = new \Illuminate\Pagination\LengthAwarePaginator(
                collect($provinces)->forPage($currentPage, $perPage),
                count($provinces),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('superadmin.datamaster.wilayah.provinsi.index', compact('provinces'));
        } catch (\Exception $e) {
            Log::error('Error in showProvinsi: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.provinsi.index')->with('error', 'Gagal memuat data provinsi');
        }
    }

    public function showKabupaten($provinceCode)
    {
        try {
            $kabupaten = $this->wilayahService->getKabupaten($provinceCode);
            $province = $this->wilayahService->getProvinceDetail($provinceCode);

            // Convert array to collection and paginate
            $perPage = 10; // Number of items per page
            $currentPage = request()->query('page', 1);
            $kabupaten = new \Illuminate\Pagination\LengthAwarePaginator(
                collect($kabupaten)->forPage($currentPage, $perPage),
                count($kabupaten),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('superadmin.datamaster.wilayah.kabupaten.index', compact('kabupaten', 'province'));
        } catch (\Exception $e) {
            Log::error('Error in showKabupaten: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.kabupaten.index')->with('error', 'Gagal memuat data kabupaten');
        }
    }

    public function showKecamatan($kotaCode)
    {
        try {
            $kecamatan = $this->wilayahService->getKecamatan($kotaCode);
            $kota = $this->wilayahService->getKotaDetail($kotaCode);
            return view('superadmin.datamaster.wilayah.kecamatan.index', compact('kecamatan', 'kota'));
        } catch (\Exception $e) {
            Log::error('Error in showKecamatan: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.kecamatan.index')->with('error', 'Gagal memuat data kecamatan');
        }
    }

    public function showDesa($kecamatanCode)
    {
        try {
            $desa = $this->wilayahService->getDesa($kecamatanCode);
            $kecamatan = $this->wilayahService->getKecamatanDetail($kecamatanCode);
            return view('superadmin.datamaster.wilayah.desa.index', compact('desa', 'kecamatan'));
        } catch (\Exception $e) {
            Log::error('Error in showDesa: ' . $e->getMessage());
            return view('superadmin.datamaster.wilayah.desa.index')->with('error', 'Gagal memuat data desa');
        }
    }

    
}
