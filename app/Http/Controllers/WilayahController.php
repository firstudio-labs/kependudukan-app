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

    /**
     * Show all kabupaten/districts with pagination (no province filter)
     */
    public function showKabupaten()
    {
        try {
            $page = request()->query('page', 1);
            Log::info("Showing kabupaten page {$page}");

            $result = $this->wilayahService->getKabupatenByPage($page);

            // Log what we got back from the service
            Log::info("Service returned data for kabupaten", [
                'data_count' => count($result['data']),
                'has_meta' => isset($result['meta']),
                'meta_info' => $result['meta'] ?? 'No meta information'
            ]);

            $kabupatenData = $result['data'];
            $meta = $result['meta'];

            // Double check if we have data to show
            if (empty($kabupatenData)) {
                Log::warning("No kabupaten data returned from service for page {$page}");
            }

            // Check if we have valid pagination meta data
            if (empty($meta) || !isset($meta['total'])) {
                Log::warning("No valid pagination metadata. Using fallback pagination.");

                // Prepare data for view using fallback pagination
                $kabupaten = new \Illuminate\Pagination\LengthAwarePaginator(
                    $kabupatenData,
                    count($kabupatenData), // Just use the count of current items as total
                    10, // Default per page
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } else {
                // Normal pagination with meta data
                $kabupaten = new \Illuminate\Pagination\LengthAwarePaginator(
                    $kabupatenData,
                    $meta['total'],
                    $meta['per_page'] ?? 10,
                    $meta['current_page'] ?? $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            }

            // Pass additional debug info to view during troubleshooting
            return view('superadmin.datamaster.wilayah.kabupaten.index', [
                'kabupaten' => $kabupaten,
                'debug_info' => [
                    'api_returned_data' => !empty($kabupatenData),
                    'page_requested' => $page,
                    'items_count' => count($kabupatenData),
                    'pagination_info' => $meta ?? 'No meta data'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error in showKabupaten: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return view('superadmin.datamaster.wilayah.kabupaten.index')
                ->with('error', 'Gagal memuat data kabupaten: ' . $e->getMessage());
        }
    }

    /**
     * Legacy method for showing kabupaten by province (should be updated in routes)
     */


    /**
     * Show all kecamatan with pagination
     */
    public function showKecamatan()
    {
        try {
            $page = request()->query('page', 1);
            Log::info("Showing kecamatan page {$page}");

            $result = $this->wilayahService->getKecamatanByPage($page);

            // Log what we got back from the service
            Log::info("Service returned data for kecamatan", [
                'data_count' => count($result['data']),
                'has_meta' => isset($result['meta']),
                'meta_info' => $result['meta'] ?? 'No meta information'
            ]);

            $kecamatanData = $result['data'];
            $meta = $result['meta'];

            // Double check if we have data to show
            if (empty($kecamatanData)) {
                Log::warning("No kecamatan data returned from service for page {$page}");
            }

            // Check if we have valid pagination meta data
            if (empty($meta) || !isset($meta['total'])) {
                Log::warning("No valid pagination metadata. Using fallback pagination.");

                // Prepare data for view using fallback pagination
                $kecamatan = new \Illuminate\Pagination\LengthAwarePaginator(
                    $kecamatanData,
                    count($kecamatanData), // Just use the count of current items as total
                    10, // Default per page
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } else {
                // Normal pagination with meta data
                $kecamatan = new \Illuminate\Pagination\LengthAwarePaginator(
                    $kecamatanData,
                    $meta['total'],
                    $meta['per_page'] ?? 10,
                    $meta['current_page'] ?? $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            }

            // Pass data to view
            return view('superadmin.datamaster.wilayah.kecamatan.index', [
                'kecamatan' => $kecamatan,
                'debug_info' => [
                    'api_returned_data' => !empty($kecamatanData),
                    'page_requested' => $page,
                    'items_count' => count($kecamatanData),
                    'pagination_info' => $meta ?? 'No meta data'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error in showKecamatan: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return view('superadmin.datamaster.wilayah.kecamatan.index')
                ->with('error', 'Gagal memuat data kecamatan: ' . $e->getMessage());
        }
    }

    /**
     * Legacy method for showing kecamatan by kabupaten
     */

    /**
     * Show all desa with pagination
     */
    public function showDesa()
    {
        try {
            $page = request()->query('page', 1);
            Log::info("Showing desa page {$page}");

            $result = $this->wilayahService->getDesaByPage($page);

            // Log what we got back from the service
            Log::info("Service returned data for desa", [
                'data_count' => count($result['data']),
                'has_meta' => isset($result['meta']),
                'meta_info' => $result['meta'] ?? 'No meta information'
            ]);

            $desaData = $result['data'];
            $meta = $result['meta'];

            // Double check if we have data to show
            if (empty($desaData)) {
                Log::warning("No desa data returned from service for page {$page}");
            }

            // Check if we have valid pagination meta data
            if (empty($meta) || !isset($meta['total'])) {
                Log::warning("No valid pagination metadata. Using fallback pagination.");

                // Prepare data for view using fallback pagination
                $desa = new \Illuminate\Pagination\LengthAwarePaginator(
                    $desaData,
                    count($desaData), // Just use the count of current items as total
                    10, // Default per page
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            } else {
                // Normal pagination with meta data
                $desa = new \Illuminate\Pagination\LengthAwarePaginator(
                    $desaData,
                    $meta['total'],
                    $meta['per_page'] ?? 10,
                    $meta['current_page'] ?? $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            }

            // Pass data to view
            return view('superadmin.datamaster.wilayah.desa.index', [
                'desa' => $desa,
                'debug_info' => [
                    'api_returned_data' => !empty($desaData),
                    'page_requested' => $page,
                    'items_count' => count($desaData),
                    'pagination_info' => $meta ?? 'No meta data'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error in showDesa: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            return view('superadmin.datamaster.wilayah.desa.index')
                ->with('error', 'Gagal memuat data desa: ' . $e->getMessage());
        }
    }

    /**
     * Legacy method for showing desa by kecamatan
     */

}
