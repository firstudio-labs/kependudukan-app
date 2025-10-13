<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KepalaDesa;
use App\Models\PerangkatDesa;
use App\Models\DataWilayah;
use App\Models\UsahaDesa;
use App\Models\SaranaUmum;
use App\Models\KesenianBudaya;
use App\Models\Abdes;
use App\Models\InformasiUsaha;
use App\Models\BarangWarungku;
use App\Models\WarungkuMaster;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPemerintahDesaController extends Controller
{
    private function getAdminUser(Request $request)
    {
        return $request->attributes->get('token_owner') ?? Auth::guard('web')->user();
    }

    private function ensureAdmin($user)
    {
        if (!$user) return [false, 'Unauthorized'];
        $allowed = ['superadmin', 'admin desa', 'admin kabupaten', 'operator'];
        if (!$user->role || !in_array(strtolower($user->role), $allowed)) {
            return [false, 'Forbidden'];
        }
        return [true, null];
    }

    public function show(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdmin($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        $villageId = $user->villages_id;

        if (!$villageId && isset($user->nik)) {
            $citizenService = app(CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($user->nik);
            if ($citizenData && isset($citizenData['data']['village_id'])) {
                $villageId = $citizenData['data']['village_id'];
            }
        }

        $pemerintah = User::query()->where('villages_id', $villageId)->get([
            'id','nama','username','role','no_hp','foto_pengguna','alamat','tag_lokasi',
            'province_id','districts_id','sub_districts_id','villages_id'
        ]);

        $kepalaDesa = KepalaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $perangkatDesa = PerangkatDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $dataWilayah = DataWilayah::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $usahaDesa = UsahaDesa::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $saranaUmum = SaranaUmum::with('kategori')->whereIn('user_id', $pemerintah->pluck('id'))->get();
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $pemerintah->pluck('id'))->get();
        $abdes = Abdes::whereIn('user_id', $pemerintah->pluck('id'))->get();

        $toUrl = function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#', $path)) return $path;
            return asset('storage/' . ltrim($path, '/'));
        };

        $citizenService = app(CitizenService::class);
        $genderStats = $citizenService->getGenderStatsByVillage($villageId);
        $ageGroupStats = $citizenService->getAgeGroupStatsByVillage($villageId);
        $educationStats = $citizenService->getEducationStatsByVillage($villageId);
        $religionStats = $citizenService->getReligionStatsByVillage($villageId);

        $informasiUsahaIds = InformasiUsaha::where('villages_id', $villageId)->pluck('id');
        $barangCountsByJenis = BarangWarungku::whereIn('informasi_usaha_id', $informasiUsahaIds)
            ->select('jenis_master_id', DB::raw('COUNT(*) as total_barang'))
            ->groupBy('jenis_master_id')
            ->pluck('total_barang', 'jenis_master_id');
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

        $saranaUmumByKategori = $saranaUmum
            ->groupBy('kategori_sarana_id')
            ->map(function ($items) use ($toUrl) {
                $first = $items->first();
                $kategori = optional($first->kategori);
                return [
                    'kategori' => [
                        'id' => $kategori->id,
                        'jenis_sarana' => $kategori->jenis_sarana ?? null,
                        'kategori' => $kategori->kategori ?? null,
                    ],
                    'sarana' => $items->map(function ($s) use ($toUrl) {
                        return [
                            'id' => $s->id,
                            'nama_sarana' => $s->nama_sarana,
                            'tag_lokasi' => $s->tag_lokasi,
                            'alamat' => $s->alamat,
                            'kontak' => $s->kontak,
                            'foto' => $s->foto,
                            'foto_url' => $toUrl($s->foto),
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
            'kepala_desa' => $kepalaDesa->map(function ($k) use ($toUrl) {
                return [
                    'id' => $k->id,
                    'user_id' => $k->user_id,
                    'nama' => $k->nama,
                    'foto' => $k->foto,
                    'foto_url' => $toUrl($k->foto),
                ];
            }),
            'perangkat_desa' => $perangkatDesa,
            'data_wilayah' => $dataWilayah,
            'usaha_desa' => $usahaDesa->map(function ($u) use ($toUrl) {
                return [
                    'id' => $u->id,
                    'user_id' => $u->user_id,
                    'jenis' => $u->jenis,
                    'nama' => $u->nama,
                    'ijin' => $u->ijin,
                    'tahun_didirikan' => $u->tahun_didirikan,
                    'ketua' => $u->ketua,
                    'foto' => $u->foto,
                    'foto_url' => $toUrl($u->foto),
                ];
            }),
            'sarana_umum' => $saranaUmum->map(function ($s) use ($toUrl) {
                return [
                    'id' => $s->id,
                    'user_id' => $s->user_id,
                    'kategori_sarana_id' => $s->kategori_sarana_id,
                    'nama_sarana' => $s->nama_sarana,
                    'tag_lokasi' => $s->tag_lokasi,
                    'alamat' => $s->alamat,
                    'kontak' => $s->kontak,
                    'foto' => $s->foto,
                    'foto_url' => $toUrl($s->foto),
                    'kategori' => $s->kategori ? [
                        'id' => $s->kategori->id,
                        'jenis_sarana' => $s->kategori->jenis_sarana,
                        'kategori' => $s->kategori->kategori,
                    ] : null,
                ];
            }),
            'sarana_umum_by_kategori' => $saranaUmumByKategori,
            'kesenian_budaya' => $kesenianBudaya->map(function ($k) use ($toUrl) {
                return [
                    'id' => $k->id,
                    'user_id' => $k->user_id,
                    'jenis' => $k->jenis,
                    'nama' => $k->nama,
                    'tag_lokasi' => $k->tag_lokasi,
                    'alamat' => $k->alamat,
                    'kontak' => $k->kontak,
                    'foto' => $k->foto,
                    'foto_url' => $toUrl($k->foto),
                ];
            }),
            'abdes' => $abdes,
            'statistik_penduduk' => $genderStats,
            'statistik_umur' => $ageGroupStats,
            'statistik_pendidikan' => $educationStats,
            'statistik_agama' => $religionStats,
            'warungku_klasifikasi_jenis' => $jenisMasters,
        ]);
    }
}


