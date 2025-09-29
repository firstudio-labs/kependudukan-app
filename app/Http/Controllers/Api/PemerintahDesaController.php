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
use Illuminate\Support\Facades\DB;

class PemerintahDesaController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        // Get village_id from user or from CitizenService if not available
        $villageId = $user->villages_id;
        
        // If village_id is null and user has NIK, try to get it from CitizenService
        if (!$villageId && isset($user->nik)) {
            $citizenService = app(CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($user->nik);
            
            if ($citizenData && isset($citizenData['data']['village_id'])) {
                $villageId = $citizenData['data']['village_id'];
            }
        }

        // user pemerintah desa (tabel users) pada desa yang sama
        $pemerintah = User::query()->where('villages_id', $villageId)->get([
            'id',
            'nama',
            'username',
            'role',
            'no_hp',
            'foto_pengguna',
            'alamat',
            'tag_lokasi',
            'province_id',
            'districts_id',
            'sub_districts_id',
            'villages_id'
        ]);

        // entitas terkait user_id
        $kepalaDesa = KepalaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $perangkatDesa = PerangkatDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $dataWilayah = DataWilayah::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $usahaDesa = UsahaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        // sertakan kategori agar bisa dipakai di response
        $saranaUmum = SaranaUmum::with('kategori')
            ->whereIn('user_id', $pemerintah->pluck('id'))
            ->get();
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $abdes = Abdes::whereIn('user_id', $pemerintah->pluck('id'))->get();

        // Statistik penduduk desa dari CitizenService
        $citizenService = app(CitizenService::class);
        $genderStats = $citizenService->getGenderStatsByVillage($villageId); // harapkan ['male' => x, 'female' => y]
        $ageGroupStats = $citizenService->getAgeGroupStatsByVillage($villageId); // statistik umur
        $educationStats = $citizenService->getEducationStatsByVillage($villageId); // statistik pendidikan
        $religionStats = $citizenService->getReligionStatsByVillage($villageId); // statistik agama
        
        // Klasifikasi & jenis dari barang warungku milik penduduk di desa tersebut
        $informasiUsahaIds = InformasiUsaha::where('villages_id', $villageId)->pluck('id');

        // Hitung total barang per jenis (jenis_master_id) untuk seluruh InformasiUsaha di desa
        $barangCountsByJenis = BarangWarungku::whereIn('informasi_usaha_id', $informasiUsahaIds)
            ->select('jenis_master_id', DB::raw('COUNT(*) as total_barang'))
            ->groupBy('jenis_master_id')
            ->pluck('total_barang', 'jenis_master_id');

        // Ambil master jenis yang hanya muncul pada data barang
        $jenisMasters = WarungkuMaster::whereIn('id', $barangCountsByJenis->keys())
            ->get(['id','jenis','klasifikasi'])
            ->map(function ($master) use ($barangCountsByJenis) {
                return [
                    'id' => $master->id,
                    'jenis' => $master->jenis,
                    'klasifikasi' => $master->klasifikasi,
                    'total_barang' => (int) ($barangCountsByJenis[$master->id] ?? 0),
                ];
            })
            ->values();

        // bangun struktur pengelompokan sarana umum per kategori
        $saranaUmumByKategori = $saranaUmum
            ->groupBy('kategori_sarana_id')
            ->map(function ($items) {
                $first = $items->first();
                $kategori = optional($first->kategori);
                return [
                    'kategori' => [
                        'id' => $kategori->id,
                        'jenis_sarana' => $kategori->jenis_sarana ?? null,
                        'kategori' => $kategori->kategori ?? null,
                    ],
                    'sarana' => $items->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'nama_sarana' => $s->nama_sarana,
                            'tag_lokasi' => $s->tag_lokasi,
                            'alamat' => $s->alamat,
                            'kontak' => $s->kontak,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json([
            'desa' => [
                'village_id' => $villageId,
            ],
            'pemerintah_desa' => $pemerintah,
            'kepala_desa' => $kepalaDesa,
            'perangkat_desa' => $perangkatDesa,
            'data_wilayah' => $dataWilayah,
            'usaha_desa' => $usahaDesa,
            // sertakan kategori di tiap item untuk kemudahan konsumsi
            'sarana_umum' => $saranaUmum->map(function ($s) {
                return [
                    'id' => $s->id,
                    'user_id' => $s->user_id,
                    'kategori_sarana_id' => $s->kategori_sarana_id,
                    'nama_sarana' => $s->nama_sarana,
                    'tag_lokasi' => $s->tag_lokasi,
                    'alamat' => $s->alamat,
                    'kontak' => $s->kontak,
                    'kategori' => $s->kategori ? [
                        'id' => $s->kategori->id,
                        'jenis_sarana' => $s->kategori->jenis_sarana,
                        'kategori' => $s->kategori->kategori,
                    ] : null,
                ];
            }),
            'sarana_umum_by_kategori' => $saranaUmumByKategori,
            'kesenian_budaya' => $kesenianBudaya,
            'abdes' => $abdes,
            'statistik_penduduk' => $genderStats,
            'statistik_umur' => $ageGroupStats,
            'statistik_pendidikan' => $educationStats,
            'statistik_agama' => $religionStats,
            'warungku_klasifikasi_jenis' => $jenisMasters,
        ]);
    }
}


