<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\BeritaDesa;
use App\Models\Tagihan;
use App\Models\Aset;
use App\Models\Penduduk;
use App\Models\InformasiUsaha;
use App\Models\BarangWarungku;
use App\Models\Domisili;
use App\Models\DomisiliUsaha;
use App\Models\LaporanDesa;
use App\Models\Administration;
use App\Models\Kehilangan;
use App\Models\SKCK;
use App\Models\AhliWaris;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\IzinKeramaian;
use App\Models\RumahSewa;
use App\Models\PengantarKtp;
use App\Services\CitizenService;

class BiodataRelatedDataController extends Controller
{
    public function beritaByNik(string $nik)
    {
        try {
            $items = BeritaDesa::query()
                ->where('nik_penduduk', $nik)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(['id','judul','status','created_at']);

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Throwable $e) {
            Log::error('beritaByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function tagihanByNik(string $nik)
    {
        try {
            // 1) Ambil KK lewat Citizen Service berdasarkan NIK
            /** @var CitizenService $citizen */
            $citizen = app(CitizenService::class);
            $citizenData = $citizen->getCitizenByNIK($nik);

            $kk = null;
            if (is_array($citizenData)) {
                $kk = $citizenData['data']['kk'] ?? $citizenData['kk'] ?? null;
            }

            // 2) Fallback: coba dari DB lokal jika ada
            if (!$kk) {
                $penduduk = Penduduk::where('nik', $nik)->first(['kk']);
                $kk = optional($penduduk)->kk;
            }

            // 3) Jika tidak ada KK, fallback ke tagihan per-NIK agar tetap ada data
            if (!$kk) {
                $items = Tagihan::with(['kategori','subKategori'])
                    ->where('nik', $nik)
                    ->orderBy('tanggal', 'desc')
                    ->limit(50)
                    ->get();

                $data = $items->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'kategori' => $t->kategori->nama_kategori ?? null,
                        'sub_kategori' => $t->subKategori->nama_sub_kategori ?? null,
                        'nominal' => $t->nominal,
                        'status' => $t->status,
                        'tanggal' => optional($t->tanggal)->format('Y-m-d'),
                        'keterangan' => $t->keterangan,
                    ];
                });

                return response()->json(['success' => true, 'data' => $data]);
            }

            // 4) Ambil anggota keluarga berdasarkan KK dari Citizen Service
            $familyResp = $citizen->getFamilyMembersByKK($kk);
            $familyMembers = [];
            if (is_array($familyResp) && isset($familyResp['data']) && is_array($familyResp['data'])) {
                $familyMembers = collect($familyResp['data'])
                    ->pluck('nik')
                    ->filter()
                    ->map(function ($v) { return (string) $v; })
                    ->values()
                    ->all();
            }

            // Jika tidak ada daftar NIK keluarga, fallback ke NIK tunggal
            if (empty($familyMembers)) {
                $familyMembers = [(string) $nik];
            }

            // 5) Ambil semua tagihan untuk seluruh anggota keluarga agar tampil sama untuk tiap anggota
            $items = Tagihan::with(['kategori','subKategori'])
                ->whereIn('nik', $familyMembers)
                ->orderBy('tanggal', 'desc')
                ->limit(100)
                ->get();

            $data = $items->map(function ($t) {
                return [
                    'id' => $t->id,
                    'kategori' => $t->kategori->nama_kategori ?? null,
                    'sub_kategori' => $t->subKategori->nama_sub_kategori ?? null,
                    'nominal' => $t->nominal,
                    'status' => $t->status,
                    'tanggal' => optional($t->tanggal)->format('Y-m-d'),
                    'keterangan' => $t->keterangan,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            Log::error('tagihanByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function asetByNik(string $nik)
    {
        try {
            // 1) Ambil KK lewat Citizen Service berdasarkan NIK
            /** @var CitizenService $citizen */
            $citizen = app(CitizenService::class);
            $citizenData = $citizen->getCitizenByNIK($nik);

            $kk = null;
            if (is_array($citizenData)) {
                $kk = $citizenData['data']['kk'] ?? $citizenData['kk'] ?? null;
            }

            // 2) Fallback: coba dari DB lokal jika ada
            if (!$kk) {
                $penduduk = Penduduk::where('nik', $nik)->first(['kk']);
                $kk = optional($penduduk)->kk;
            }

            // 3) Jika tidak ada KK, fallback ke aset per-NIK agar tetap ada data
            if (!$kk) {
                $penduduk = Penduduk::where('nik', $nik)->first(['id', 'nik', 'alamat']);
                if (!$penduduk) {
                    return response()->json(['success' => true, 'data' => []]);
                }

                $items = Aset::with(['klasifikasi', 'jenisAset'])
                    ->where('user_id', $penduduk->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                $data = $items->map(function ($aset) use ($penduduk) {
                    return [
                        'id' => $aset->id,
                        'nama_aset' => $aset->nama_aset,
                        'nik_pemilik' => $aset->nik_pemilik ?: $penduduk->nik,
                        'nama_pemilik' => $aset->nama_pemilik,
                        'alamat' => $aset->address ?: $penduduk->alamat,
                        'klasifikasi' => optional($aset->klasifikasi)->jenis_klasifikasi,
                        'jenis_aset' => optional($aset->jenisAset)->jenis_aset,
                        'foto_aset_depan' => $aset->foto_aset_depan ? '/storage/' . $aset->foto_aset_depan : null,
                        'foto_aset_samping' => $aset->foto_aset_samping ? '/storage/' . $aset->foto_aset_samping : null,
                        'tag_lokasi' => $aset->tag_lokasi,
                    ];
                });

                return response()->json(['success' => true, 'data' => $data]);
            }

            // 4) Ambil anggota keluarga berdasarkan KK dari Citizen Service
            $familyResp = $citizen->getFamilyMembersByKK($kk);
            $familyMembers = [];
            if (is_array($familyResp) && isset($familyResp['data']) && is_array($familyResp['data'])) {
                $familyMembers = collect($familyResp['data'])
                    ->pluck('nik')
                    ->filter()
                    ->map(function ($v) { return (string) $v; })
                    ->values()
                    ->all();
            }

            // Jika tidak ada daftar NIK keluarga, fallback ke NIK tunggal
            if (empty($familyMembers)) {
                $familyMembers = [(string) $nik];
            }

            // 5) Ambil semua penduduk lokal berdasarkan NIK keluarga untuk mendapatkan user_id
            $pendudukData = Penduduk::whereIn('nik', $familyMembers)
                ->get(['id', 'nik', 'alamat']);

            $pendudukIds = $pendudukData->pluck('id')->toArray();

            // Jika tidak ada penduduk lokal, fallback ke penduduk dengan NIK yang diminta
            if (empty($pendudukIds)) {
                $penduduk = Penduduk::where('nik', $nik)->first(['id', 'nik', 'alamat']);
                if ($penduduk) {
                    $pendudukIds = [$penduduk->id];
                    $pendudukData = collect([$penduduk]);
                }
            }

            // 6) Ambil semua aset untuk seluruh anggota keluarga agar tampil sama untuk tiap anggota
            $items = Aset::with(['klasifikasi', 'jenisAset'])
                ->whereIn('user_id', $pendudukIds)
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            $data = $items->map(function ($aset) use ($pendudukData) {
                // Cari data penduduk berdasarkan user_id
                $penduduk = $pendudukData->firstWhere('id', $aset->user_id);
                
                return [
                    'id' => $aset->id,
                    'nama_aset' => $aset->nama_aset,
                    'nik_pemilik' => $aset->nik_pemilik ?: optional($penduduk)->nik,
                    'nama_pemilik' => $aset->nama_pemilik,
                    'alamat' => $aset->address ?: optional($penduduk)->alamat,
                    'klasifikasi' => optional($aset->klasifikasi)->jenis_klasifikasi,
                    'jenis_aset' => optional($aset->jenisAset)->jenis_aset,
                    'foto_aset_depan' => $aset->foto_aset_depan ? '/storage/' . $aset->foto_aset_depan : null,
                    'foto_aset_samping' => $aset->foto_aset_samping ? '/storage/' . $aset->foto_aset_samping : null,
                    'tag_lokasi' => $aset->tag_lokasi,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            Log::error('asetByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function warungProdukByNik(string $nik)
    {
        try {
            // 1) Ambil data KK dari Citizen Service berdasarkan NIK
            /** @var CitizenService $citizen */
            $citizen = app(CitizenService::class);
            $citizenData = $citizen->getCitizenByNIK($nik);

            $kk = null;
            if (is_array($citizenData)) {
                $kk = $citizenData['data']['kk'] ?? $citizenData['kk'] ?? null;
            }

            // Fallback: coba baca dari DB lokal jika ada kaitan
            if (!$kk) {
                $penduduk = Penduduk::where('nik', $nik)->first(['id','kk']);
                $kk = optional($penduduk)->kk;
            }

            if (!$kk) {
                return response()->json(['success' => true, 'informasi_usaha' => null, 'data' => []]);
            }

            // 2) Cari InformasiUsaha berdasarkan KK
            $info = InformasiUsaha::where('kk', $kk)->first();
            if (!$info) {
                return response()->json(['success' => true, 'informasi_usaha' => null, 'data' => []]);
            }

            // 3) Ambil barang + jenis/klasifikasi dari WarungkuMaster
            $items = BarangWarungku::with('warungkuMaster')
                ->where('informasi_usaha_id', $info->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'nama_produk' => $b->nama_produk,
                        'harga' => (float) $b->harga,
                        'stok' => (int) $b->stok,
                        'deskripsi' => $b->deskripsi,
                        'foto_url' => $b->foto_url,
                        'klasifikasi' => optional($b->warungkuMaster)->klasifikasi,
                        'jenis' => optional($b->warungkuMaster)->jenis,
                        'created_at' => optional($b->created_at)->format('Y-m-d H:i:s'),
                    ];
                });

            // Siapkan info pemilik (jika tersedia)
            $ownerNik = null;
            $ownerName = null;
            try {
                $ownerPenduduk = $info->penduduk()->first(['nik']);
                $ownerNik = optional($ownerPenduduk)->nik;
                if ($ownerNik) {
                    $ownerApi = $citizen->getCitizenByNIK($ownerNik);
                    if (is_array($ownerApi)) {
                        $ownerName = $ownerApi['data']['full_name'] ?? $ownerApi['full_name'] ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // abaikan jika gagal mengambil nama pemilik
            }

            return response()->json([
                'success' => true,
                'informasi_usaha' => [
                    'id' => $info->id,
                    'nama_usaha' => $info->nama_usaha,
                    'alamat' => $info->alamat,
                    'kk' => $info->kk,
                    'kelompok_usaha' => $info->kelompok_usaha,
                    'tag_lokasi' => $info->tag_lokasi,
                    'foto_url' => $info->foto_url,
                    'province_id' => $info->province_id,
                    'districts_id' => $info->districts_id,
                    'sub_districts_id' => $info->sub_districts_id,
                    'villages_id' => $info->villages_id,
                    'penduduk_id' => $info->penduduk_id,
                    'pemilik' => [
                        'nik' => $ownerNik,
                        'nama' => $ownerName,
                    ],
                ],
                'data' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::error('warungProdukByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function domisiliByNik(string $nik)
    {
        try {
            $domisili = Domisili::query()
                ->where('nik', $nik)
                ->orWhere('nik_pemohon', $nik)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get(['id','full_name','nik','address','created_at']);

            $domisiliUsaha = DomisiliUsaha::query()
                ->where('nik', $nik)
                ->orWhere('nik_pemohon', $nik)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get(['id','full_name','nik','business_name','address','created_at']);

            return response()->json([
                'success' => true,
                'domisili' => $domisili,
                'domisili_usaha' => $domisiliUsaha,
            ]);
        } catch (\Throwable $e) {
            Log::error('domisiliByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'domisili' => [], 'domisili_usaha' => []]);
        }
    }

    public function laporanByNik(string $nik)
    {
        try {
            $penduduk = Penduduk::where('nik', $nik)->first();
            if (!$penduduk) return response()->json(['success' => true, 'data' => []]);

            $items = LaporanDesa::where('user_id', $penduduk->id)
                ->orderBy('created_at','desc')
                ->limit(50)
                ->get(['id','judul_laporan','status','created_at']);

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Throwable $e) {
            Log::error('laporanByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    public function pendudukLocationByNik(string $nik)
    {
        try {
            $penduduk = Penduduk::where('nik', $nik)->first(['id','nik','alamat','tag_lokasi']);
            if (!$penduduk) {
                return response()->json(['success' => true, 'data' => null]);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'nik' => $penduduk->nik,
                    'alamat' => $penduduk->alamat,
                    'tag_lokasi' => $penduduk->tag_lokasi,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('pendudukLocationByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => null]);
        }
    }

    public function lettersByNik(string $nik)
    {
        try {
            $items = collect();

            // Administrasi Umum
            $admin = Administration::query()
                ->where('nik', $nik)
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'administrasi',
                        'type_label' => 'Administrasi Umum',
                        'id' => $row->id,
                        'nik' => $row->nik,
                        'full_name' => $row->full_name,
                        'purpose' => $row->purpose ?? $row->statement_content,
                        'letter_date' => optional($row->letter_date ?: $row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted,
                    ];
                });
            $items = $items->merge($admin);

            // Kehilangan
            $kehilangan = Kehilangan::query()
                ->where('nik', $nik)
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'kehilangan',
                        'type_label' => 'Kehilangan',
                        'id' => $row->id,
                        'nik' => $row->nik,
                        'full_name' => $row->full_name,
                        'purpose' => $row->lost_items,
                        'letter_date' => optional($row->letter_date ?: $row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted,
                    ];
                });
            $items = $items->merge($kehilangan);

            // SKCK
            $skck = SKCK::query()
                ->where('nik', $nik)
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'skck',
                        'type_label' => 'SKCK',
                        'id' => $row->id,
                        'nik' => $row->nik,
                        'full_name' => $row->full_name,
                        'purpose' => $row->purpose,
                        'letter_date' => optional($row->letter_date ?: $row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted,
                    ];
                });
            $items = $items->merge($skck);

            // Domisili (cek dua kolom nik)
            $domisili = Domisili::query()
                ->where(function ($q) use ($nik) {
                    $q->where('nik', $nik)->orWhere('nik_pemohon', $nik);
                })
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'domisili',
                        'type_label' => 'Domisili',
                        'id' => $row->id,
                        'nik' => $row->nik,
                        'full_name' => $row->full_name,
                        'purpose' => 'Surat Domisili',
                        'letter_date' => optional($row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted ?? null,
                    ];
                });
            $items = $items->merge($domisili);

            // Domisili Usaha (cek dua kolom nik)
            $domUsaha = DomisiliUsaha::query()
                ->where(function ($q) use ($nik) {
                    $q->where('nik', $nik)->orWhere('nik_pemohon', $nik);
                })
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'domisili_usaha',
                        'type_label' => 'Domisili Usaha',
                        'id' => $row->id,
                        'nik' => $row->nik,
                        'full_name' => $row->full_name ?? $row->owner_name,
                        'purpose' => $row->business_name,
                        'letter_date' => optional($row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted ?? null,
                    ];
                });
            $items = $items->merge($domUsaha);

            // Pengantar KTP (jika ada)
            if (class_exists(PengantarKtp::class)) {
                try {
                    $pengantar = PengantarKtp::query()
                        ->where('nik', $nik)
                        ->latest()
                        ->limit(50)
                        ->get()
                        ->map(function ($row) {
                            return [
                                'type' => 'pengantar_ktp',
                                'type_label' => 'Pengantar KTP',
                                'id' => $row->id,
                                'nik' => $row->nik,
                                'full_name' => $row->full_name ?? null,
                                'purpose' => 'Pengantar KTP',
                                'letter_date' => optional($row->created_at)->format('Y-m-d'),
                                'is_accepted' => $row->is_accepted ?? null,
                            ];
                        });
                    $items = $items->merge($pengantar);
                } catch (\Throwable $e) {
                    // abaikan jika model/kolom berbeda
                }
            }

            // Rumah Sewa
            try {
                $rumahSewa = RumahSewa::query()
                    ->where('nik', $nik)
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'type' => 'rumah_sewa',
                            'type_label' => 'Rumah Sewa',
                            'id' => $row->id,
                            'nik' => $row->nik,
                            'full_name' => $row->full_name,
                            'purpose' => $row->rental_address,
                            'letter_date' => optional($row->valid_until ?: $row->created_at)->format('Y-m-d'),
                            'is_accepted' => $row->is_accepted ?? null,
                        ];
                    });
                $items = $items->merge($rumahSewa);
            } catch (\Throwable $e) {}

            // Izin Keramaian
            try {
                $keramaian = IzinKeramaian::query()
                    ->where('nik', $nik)
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'type' => 'keramaian',
                            'type_label' => 'Izin Keramaian',
                            'id' => $row->id,
                            'nik' => $row->nik,
                            'full_name' => $row->full_name,
                            'purpose' => $row->event ?? $row->entertainment,
                            'letter_date' => optional($row->event_date ?: $row->created_at)->format('Y-m-d'),
                            'is_accepted' => $row->is_accepted ?? null,
                        ];
                    });
                $items = $items->merge($keramaian);
            } catch (\Throwable $e) {}

            // Kematian
            try {
                $kematian = Kematian::query()
                    ->where('nik', $nik)
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'type' => 'kematian',
                            'type_label' => 'Kematian',
                            'id' => $row->id,
                            'nik' => $row->nik,
                            'full_name' => $row->full_name,
                            'purpose' => $row->death_cause ?? 'Surat Keterangan Kematian',
                            'letter_date' => optional($row->death_date ?: $row->created_at)->format('Y-m-d'),
                            'is_accepted' => $row->is_accepted ?? null,
                        ];
                    });
                $items = $items->merge($kematian);
            } catch (\Throwable $e) {}

            // Kelahiran: cocokkan father_nik / mother_nik
            try {
                $kelahiran = Kelahiran::query()
                    ->where('father_nik', $nik)
                    ->orWhere('mother_nik', $nik)
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->map(function ($row) use ($nik) {
                        $nama = $row->father_full_name ?? $row->mother_full_name ?? null;
                        return [
                            'type' => 'kelahiran',
                            'type_label' => 'Kelahiran',
                            'id' => $row->id,
                            'nik' => $nik,
                            'full_name' => $nama,
                            'purpose' => $row->child_name ? ('Kelahiran: ' . $row->child_name) : 'Surat Keterangan Kelahiran',
                            'letter_date' => optional($row->child_birth_date ?: $row->created_at)->format('Y-m-d'),
                            'is_accepted' => $row->is_accepted ?? null,
                        ];
                    });
                $items = $items->merge($kelahiran);
            } catch (\Throwable $e) {}

            // Ahli Waris: kolom bisa array/json -> gunakan whereJsonContains bila ada
            try {
                $ahliWarisQ = AhliWaris::query();
                try { $ahliWarisQ->orWhereJsonContains('nik', $nik); } catch (\Throwable $e) {}
                $ahliWarisQ->orWhere('nik', $nik);
                $ahliWaris = $ahliWarisQ->latest()->limit(50)->get()->map(function ($row) use ($nik) {
                    $fullName = is_array($row->full_name) ? implode(', ', array_filter($row->full_name)) : ($row->full_name ?? null);
                    return [
                        'type' => 'ahli_waris',
                        'type_label' => 'Ahli Waris',
                        'id' => $row->id,
                        'nik' => $nik,
                        'full_name' => $fullName,
                        'purpose' => $row->inheritance_type ?? 'Surat Keterangan Ahli Waris',
                        'letter_date' => optional($row->inheritance_letter_date ?: $row->created_at)->format('Y-m-d'),
                        'is_accepted' => $row->is_accepted ?? null,
                    ];
                });
                $items = $items->merge($ahliWaris);
            } catch (\Throwable $e) {}

            // Urutkan dan batasi
            $items = $items->sortByDesc('letter_date')->values()->take(100);

            return response()->json(['success' => true, 'data' => $items]);
        } catch (\Throwable $e) {
            Log::error('lettersByNik error: '.$e->getMessage());
            return response()->json(['success' => false, 'data' => []]);
        }
    }
}


