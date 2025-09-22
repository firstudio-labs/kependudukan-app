<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KepalaDesa;
use App\Models\PerangkatDesa;
use App\Models\DataWilayah;
use App\Models\UsahaDesa;
use App\Models\SaranaUmum;
use App\Models\KategoriSarana;
use App\Models\KesenianBudaya;
use App\Models\Abdes;
use App\Models\InformasiUsaha;
use App\Models\BarangWarungku;
use App\Models\WarungkuMaster;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemerintahDesaController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $villageId = $user->villages_id;

        // user pemerintah desa (tabel users) pada desa yang sama
        $pemerintah = User::query()->where('villages_id', $villageId)->get(['id','nama','username','role','no_hp','foto_pengguna','province_id','districts_id','sub_districts_id','villages_id']);

        // entitas terkait user_id
        $kepalaDesa = KepalaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $perangkatDesa = PerangkatDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $dataWilayah = DataWilayah::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $usahaDesa = UsahaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $saranaUmum = SaranaUmum::with('kategoriSarana')->whereIn('user_id', $pemerintah->pluck('id'))->get();
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $abdes = Abdes::whereIn('user_id', $pemerintah->pluck('id'))->get();

        // Statistik penduduk desa dari CitizenService
        $citizenService = app(CitizenService::class);
        $genderStats = $citizenService->getGenderStatsByVillage($villageId); // harapkan ['male' => x, 'female' => y]

        // Klasifikasi & jenis dari barang warungku milik penduduk di desa tersebut
        $informasiUsahaIds = InformasiUsaha::where('villages_id', $villageId)->pluck('id');
        $jenisIds = BarangWarungku::whereIn('informasi_usaha_id', $informasiUsahaIds)->distinct()->pluck('jenis_master_id');
        $jenisMasters = WarungkuMaster::whereIn('id', $jenisIds)->get(['id','jenis','klasifikasi']);

        return response()->json([
            'desa' => [
                'village_id' => $villageId,
            ],
            'pemerintah_desa' => $pemerintah,
            'kepala_desa' => $kepalaDesa,
            'perangkat_desa' => $perangkatDesa,
            'data_wilayah' => $dataWilayah,
            'usaha_desa' => $usahaDesa,
            'sarana_umum' => $saranaUmum,
            'kesenian_budaya' => $kesenianBudaya,
            'abdes' => $abdes,
            'statistik_penduduk' => $genderStats,
            'warungku_klasifikasi_jenis' => $jenisMasters,
        ]);
    }
}


