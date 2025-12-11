<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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

class ProfilDesaController extends Controller
{
    protected $citizenService;

    /**
     * Constructor dengan dependency injection untuk konsistensi dengan PemerintahDesaController
     */
    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    /**
     * Helper untuk membangun URL publik foto (dipindah ke method untuk reuse)
     */
    private function toUrl($path)
    {
        if (!$path) return null;
        if (preg_match('#^https?://#', $path)) return $path;
        return asset('storage/' . ltrim($path, '/'));
    }

    public function show(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        // Get village_id from user or from CitizenService if not available
        $villageId = $user->villages_id;
        
        // If village_id is null and user has NIK, try to get it from CitizenService
        if (!$villageId && isset($user->nik)) {
            $citizenData = $this->citizenService->getCitizenByNIK($user->nik);
            
            if ($citizenData && isset($citizenData['data']['village_id'])) {
                $villageId = $citizenData['data']['village_id'];
            }
        }

        // Early return jika village_id masih null
        if (!$villageId) {
            return response()->json([
                'message' => 'Village ID tidak ditemukan',
                'desa' => ['village_id' => null],
                'data_wilayah' => [],
                'usaha_desa' => [],
                'sarana_umum' => [],
                'sarana_umum_by_kategori' => [],
                'kesenian_budaya' => [],
                'abdes' => [],
                'statistik_penduduk' => ['male' => 0, 'female' => 0, 'total' => 0],
                'statistik_umur' => ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0],
                'statistik_pendidikan' => ['groups' => [], 'total_with_education' => 0],
                'statistik_agama' => ['groups' => [], 'total_with_religion' => 0],
                'warungku_klasifikasi_jenis' => [],
            ]);
        }

        // Query semua user dengan villages_id yang sama
        $pemerintah = User::query()
            ->where('villages_id', $villageId)
            ->select(['id'])
            ->get();

        $userIds = $pemerintah->pluck('id');

        // Early return jika tidak ada pemerintah desa
        if ($userIds->isEmpty()) {
            return response()->json([
                'desa' => ['village_id' => $villageId],
                'data_wilayah' => [],
                'usaha_desa' => [],
                'sarana_umum' => [],
                'sarana_umum_by_kategori' => [],
                'kesenian_budaya' => [],
                'abdes' => [],
                'statistik_penduduk' => ['male' => 0, 'female' => 0, 'total' => 0],
                'statistik_umur' => ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0],
                'statistik_pendidikan' => ['groups' => [], 'total_with_education' => 0],
                'statistik_agama' => ['groups' => [], 'total_with_religion' => 0],
                'warungku_klasifikasi_jenis' => [],
            ]);
        }

        // Query semua data yang di-comment di PemerintahDesaController
        $dataWilayah = DataWilayah::whereIn('user_id', $userIds)->get();
        
        $usahaDesa = UsahaDesa::whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'jenis', 'nama', 'ijin', 'tahun_didirikan', 'ketua', 'foto'])
            ->get();
        
        $saranaUmum = SaranaUmum::with('kategori:id,jenis_sarana,kategori')
            ->whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'kategori_sarana_id', 'nama_sarana', 'tag_lokasi', 'alamat', 'kontak', 'foto'])
            ->get();
        
        $kesenianBudaya = KesenianBudaya::whereIn('user_id', $userIds)
            ->select(['id', 'user_id', 'jenis', 'nama', 'tag_lokasi', 'alamat', 'kontak', 'foto'])
            ->get();
        
        $abdes = Abdes::whereIn('user_id', $userIds)->get();

        // Statistik penduduk desa
        $useCache = !$request->has('refresh_stats');
        $allStats = $this->citizenService->getAllVillageStats($villageId, $useCache);
        $genderStats = $allStats['gender'] ?? ['male' => 0, 'female' => 0, 'total' => 0];
        $ageGroupStats = $allStats['age'] ?? ['groups' => ['0_17' => 0, '18_30' => 0, '31_45' => 0, '46_60' => 0, '61_plus' => 0], 'total_with_age' => 0];
        $educationStats = $allStats['education'] ?? ['groups' => [], 'total_with_education' => 0];
        $religionStats = $allStats['religion'] ?? ['groups' => [], 'total_with_religion' => 0];
        
        // Warungku klasifikasi jenis
        $informasiUsahaIds = InformasiUsaha::where('villages_id', $villageId)->pluck('id');
        if ($informasiUsahaIds->isNotEmpty()) {
            $barangCountsByJenis = BarangWarungku::whereIn('informasi_usaha_id', $informasiUsahaIds)
                ->select('jenis_master_id', DB::raw('COUNT(*) as total_barang'))
                ->groupBy('jenis_master_id')
                ->pluck('total_barang', 'jenis_master_id');

            $jenisMasters = WarungkuMaster::whereIn('id', $barangCountsByJenis->keys())
                ->select(['id', 'jenis', 'klasifikasi'])
                ->get()
                ->map(function ($master) use ($barangCountsByJenis) {
                    return [
                        'id' => $master->id,
                        'jenis' => $master->jenis,
                        'klasifikasi' => $master->klasifikasi,
                        'total_barang' => (int) ($barangCountsByJenis[$master->id] ?? 0),
                    ];
                })
                ->values();
        } else {
            $jenisMasters = collect([]);
        }

        // Sarana umum by kategori
        $saranaUmumByKategori = $saranaUmum
            ->groupBy('kategori_sarana_id')
            ->map(function ($items) {
                $first = $items->first();
                $kategori = $first->kategori;
                
                return [
                    'kategori' => $kategori ? [
                        'id' => $kategori->id,
                        'jenis_sarana' => $kategori->jenis_sarana ?? null,
                        'kategori' => $kategori->kategori ?? null,
                    ] : null,
                    'sarana' => $items->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'nama_sarana' => $s->nama_sarana,
                            'tag_lokasi' => $s->tag_lokasi,
                            'alamat' => $s->alamat,
                            'kontak' => $s->kontak,
                            'foto' => $s->foto,
                            'foto_url' => $this->toUrl($s->foto),
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json([
            'desa' => [
                'village_id' => $villageId,
            ],
            'data_wilayah' => $dataWilayah->map(function ($dw) {
                return [
                    'id' => $dw->id,
                    'user_id' => $dw->user_id,
                    'luas_wilayah' => $dw->luas_wilayah,
                    'foto_peta' => $dw->foto_peta,
                    'foto_peta_url' => $this->toUrl($dw->foto_peta),
                    'batas_wilayah' => $dw->batas_wilayah,
                    'jumlah_dusun' => $dw->jumlah_dusun,
                    'jumlah_rt' => $dw->jumlah_rt,
                ];
            }),
            'usaha_desa' => $usahaDesa->map(function ($u) {
                return [
                    'id' => $u->id,
                    'user_id' => $u->user_id,
                    'jenis' => $u->jenis,
                    'nama' => $u->nama,
                    'ijin' => $u->ijin,
                    'tahun_didirikan' => $u->tahun_didirikan,
                    'ketua' => $u->ketua,
                    'foto' => $u->foto,
                    'foto_url' => $this->toUrl($u->foto),
                ];
            }),
            'sarana_umum' => $saranaUmum->map(function ($s) {
                return [
                    'id' => $s->id,
                    'user_id' => $s->user_id,
                    'kategori_sarana_id' => $s->kategori_sarana_id,
                    'nama_sarana' => $s->nama_sarana,
                    'tag_lokasi' => $s->tag_lokasi,
                    'alamat' => $s->alamat,
                    'kontak' => $s->kontak,
                    'foto' => $s->foto,
                    'foto_url' => $this->toUrl($s->foto),
                    'kategori' => $s->kategori ? [
                        'id' => $s->kategori->id,
                        'jenis_sarana' => $s->kategori->jenis_sarana,
                        'kategori' => $s->kategori->kategori,
                    ] : null,
                ];
            }),
            'sarana_umum_by_kategori' => $saranaUmumByKategori,
            'kesenian_budaya' => $kesenianBudaya->map(function ($k) {
                return [
                    'id' => $k->id,
                    'user_id' => $k->user_id,
                    'jenis' => $k->jenis,
                    'nama' => $k->nama,
                    'tag_lokasi' => $k->tag_lokasi,
                    'alamat' => $k->alamat,
                    'kontak' => $k->kontak,
                    'foto' => $k->foto,
                    'foto_url' => $this->toUrl($k->foto),
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
