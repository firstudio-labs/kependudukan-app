<?php

namespace App\Services;

use App\Models\Abdes;
use App\Models\BarangWarungku;
use App\Models\DataWilayah;
use App\Models\InformasiUsaha;
use App\Models\KepalaDesa;
use App\Models\KesenianBudaya;
use App\Models\PerangkatDesa;
use App\Models\SaranaUmum;
use App\Models\UsahaDesa;
use App\Models\User;
use App\Models\WarungkuMaster;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PemerintahDesaSummaryService
{
    private const DEFAULT_CACHE_MINUTES = 10;

    public function __construct(private readonly CitizenService $citizenService)
    {
    }

    public function getSummaryForVillage(int $villageId): array
    {
        $cacheKey = "pemerintah_desa_summary_{$villageId}";
        $ttl = now()->addMinutes(config('cache.pemerintah_desa_summary_ttl', self::DEFAULT_CACHE_MINUTES));

        return Cache::remember($cacheKey, $ttl, function () use ($villageId) {
            return $this->buildSummary($villageId);
        });
    }

    public function flushSummaryCache(int $villageId): void
    {
        Cache::forget("pemerintah_desa_summary_{$villageId}");
    }

    private function buildSummary(int $villageId): array
    {
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

        $userIds = $pemerintah->pluck('id');
        $kepalaDesa = KepalaDesa::whereIn('user_id', $userIds)->get();
        $perangkatDesa = PerangkatDesa::whereIn('user_id', $userIds)->get();
        $dataWilayah = DataWilayah::whereIn('user_id', $userIds)->get();
        $usahaDesa = UsahaDesa::whereIn('user_id', $userIds)->get();
        $saranaUmum = SaranaUmum::with('kategori')->whereIn('user_id', $userIds)->get();
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $userIds)->get();
        $abdes = Abdes::whereIn('user_id', $userIds)->get();

        $toUrl = fn ($path) => $this->buildStorageUrl($path);

        $genderStats = $this->citizenService->getGenderStatsByVillage($villageId);
        $ageGroupStats = $this->citizenService->getAgeGroupStatsByVillage($villageId);
        $educationStats = $this->citizenService->getEducationStatsByVillage($villageId);
        $religionStats = $this->citizenService->getReligionStatsByVillage($villageId);

        $informasiUsahaIds = InformasiUsaha::where('villages_id', $villageId)->pluck('id');
        $barangCountsByJenis = BarangWarungku::whereIn('informasi_usaha_id', $informasiUsahaIds)
            ->select('jenis_master_id', DB::raw('COUNT(*) as total_barang'))
            ->groupBy('jenis_master_id')
            ->pluck('total_barang', 'jenis_master_id');

        $jenisMasters = WarungkuMaster::whereIn('id', $barangCountsByJenis->keys())
            ->get(['id', 'jenis', 'klasifikasi'])
            ->map(function ($master) use ($barangCountsByJenis) {
                return [
                    'id' => $master->id,
                    'jenis' => $master->jenis,
                    'klasifikasi' => $master->klasifikasi,
                    'total_barang' => (int) ($barangCountsByJenis[$master->id] ?? 0),
                ];
            })->values();

        $saranaUmumByKategori = $saranaUmum->groupBy('kategori_sarana_id')->map(function ($items) use ($toUrl) {
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
        })->values();

        return [
            'desa' => ['village_id' => $villageId],
            'pemerintah_desa' => $pemerintah,
            'kepala_desa' => $kepalaDesa->map(fn ($k) => [
                'id' => $k->id,
                'user_id' => $k->user_id,
                'nama' => $k->nama,
                'foto' => $k->foto,
                'foto_url' => $toUrl($k->foto),
            ]),
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
        ];
    }

    private function buildStorageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

