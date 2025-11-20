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

        // user pemerintah desa (tabel users) pada desa yang sama - optimasi dengan select spesifik
        $pemerintah = User::query()
            ->where('villages_id', $villageId)
            ->select([
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
            ])
            ->get();

        // Optimasi: ambil user_ids sekali untuk digunakan di semua query
        $userIds = $pemerintah->pluck('id');
        
        // entitas terkait user_id - optimasi dengan eager loading dan select spesifik
        $kepalaDesa = KepalaDesa::whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'nama', 'foto'])
            ->get();
        $perangkatDesa = PerangkatDesa::whereIn('user_id', $userIds)->get();
        $dataWilayah = DataWilayah::whereIn('user_id', $userIds)->get();
        $usahaDesa = UsahaDesa::whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'jenis', 'nama', 'ijin', 'tahun_didirikan', 'ketua', 'foto'])
            ->get();
        // sertakan kategori agar bisa dipakai di response
        $saranaUmum = SaranaUmum::with('kategori')
            ->whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'kategori_sarana_id', 'nama_sarana', 'tag_lokasi', 'alamat', 'kontak', 'foto'])
            ->get();
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'jenis', 'nama', 'tag_lokasi', 'alamat', 'kontak', 'foto'])
            ->get();
        $abdes = Abdes::whereIn('user_id', $userIds)->get();

        // helper sederhana untuk bangun URL publik foto
        $toUrl = function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#', $path)) return $path;
            return asset('storage/' . ltrim($path, '/'));
        };

        // Statistik penduduk desa dari CitizenService - menggunakan getAllVillageStats untuk optimasi (1 API call instead of 4)
        // Parameter ?refresh_stats=1 untuk bypass cache jika diperlukan
        $citizenService = app(CitizenService::class);
        try {
            $useCache = !$request->has('refresh_stats');
            $allStats = $citizenService->getAllVillageStats($villageId, $useCache);
            $genderStats = $allStats['gender'] ?? ['male' => 0, 'female' => 0, 'total' => 0];
            $ageGroupStats = $allStats['age'] ?? ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0];
            $educationStats = $allStats['education'] ?? ['groups' => [], 'total_with_education' => 0];
            $religionStats = $allStats['religion'] ?? ['groups' => [], 'total_with_religion' => 0];
            
            // Jika statistik masih 0 dan menggunakan cache, coba tanpa cache
            if ($useCache && $genderStats['total'] == 0 && $ageGroupStats['total_with_age'] == 0) {
                \Log::warning('Stats are 0 with cache, retrying without cache', ['village_id' => $villageId]);
                $allStats = $citizenService->getAllVillageStats($villageId, false);
                $genderStats = $allStats['gender'] ?? $genderStats;
                $ageGroupStats = $allStats['age'] ?? $ageGroupStats;
                $educationStats = $allStats['education'] ?? $educationStats;
                $religionStats = $allStats['religion'] ?? $religionStats;
            }
        } catch (\Exception $e) {
            // Fallback jika terjadi error
            \Log::error('Error getting village stats: ' . $e->getMessage(), [
                'village_id' => $villageId,
                'trace' => $e->getTraceAsString()
            ]);
            $genderStats = ['male' => 0, 'female' => 0, 'total' => 0];
            $ageGroupStats = ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0];
            $educationStats = ['groups' => [], 'total_with_education' => 0];
            $religionStats = ['groups' => [], 'total_with_religion' => 0];
        }
        
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
            // sertakan kategori di tiap item untuk kemudahan konsumsi
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


