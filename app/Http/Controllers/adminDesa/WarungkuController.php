<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\InformasiUsaha;
use App\Models\BarangWarungku;
use App\Models\Penduduk;
use App\Services\CitizenService;
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

        $query = InformasiUsaha::query()
            ->with(['penduduk','user'])
            ->where('villages_id', $villageId);

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
            return $item;
        });

        return view('admin.desa.warungku.index', [
            'items' => $items,
            'filters' => [
                'search' => $search,
                'kelompok' => $kelompok,
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

        return view('admin.desa.warungku.show', [
            'item' => $item,
            'ownerName' => $ownerName,
        ]);
    }

    private function authorizeAdminDesa(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin desa') {
            abort(403, 'Unauthorized');
        }
    }
}


