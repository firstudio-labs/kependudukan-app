<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Tagihan;
    use App\Models\KategoriTagihan;
    use App\Models\SubKategoriTagihan;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use App\Services\CitizenService;

    class TagihanController extends Controller
    {
        protected CitizenService $citizenService;

        public function __construct(CitizenService $citizenService)
        {
            $this->citizenService = $citizenService;
        }
        /**
         * List tagihan untuk penduduk yang login (berdasarkan NIK)
         */
        public function index(Request $request)
        {
            $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
            if (!$user || empty($user->nik)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Kumpulkan NIK pemilik + anggota keluarga berdasarkan KK
            $nikUser = (string) $user->nik;

            $nikList = [$nikUser];
            try {
                // Dapatkan KK dari data citizen
                $citizen = $this->citizenService->getCitizenByNIK($nikUser);
                $kk = $citizen['data']['kk'] ?? ($citizen['data']['no_kk'] ?? null);
                if ($kk) {
                    $family = $this->citizenService->getFamilyMembersByKK($kk);
                    if ($family && isset($family['data']) && is_array($family['data'])) {
                        $nikFamily = collect($family['data'])
                            ->pluck('nik')
                            ->filter()
                            ->map(fn($n) => (string) $n)
                            ->values()
                            ->all();
                        $nikList = array_values(array_unique(array_merge($nikList, $nikFamily)));
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan error, tetap gunakan NIK user saja
            }

            $query = Tagihan::query()->with(['kategori', 'subKategori'])
                ->whereIn('nik', $nikList);

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal', (int) $request->input('bulan'));
            }
            if ($request->filled('tahun')) {
                $query->whereYear('tanggal', (int) $request->input('tahun'));
            }
            if ($request->filled('kategori_id')) {
                $query->where('kategori_id', $request->input('kategori_id'));
            }
            if ($request->filled('sub_kategori_id')) {
                $query->where('sub_kategori_id', $request->input('sub_kategori_id'));
            }
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal', '>=', $request->input('start_date'));
            }
            if ($request->filled('end_date')) {
                $query->whereDate('tanggal', '<=', $request->input('end_date'));
            }

            $perPage = (int) $request->input('per_page', 10);
            $items = $query->orderByDesc('tanggal')->paginate($perPage)->withQueryString();

            // Map response ringkas untuk mobile
            // Siapkan mapping NIK->nama untuk response (berdasarkan NIK pada tagihan)
            $nikToName = [];
            try {
                if (!empty($kk ?? null)) {
                    $family = $this->citizenService->getFamilyMembersByKK($kk);
                    if ($family && isset($family['data']) && is_array($family['data'])) {
                        foreach ($family['data'] as $m) {
                            $nikToName[(string)($m['nik'] ?? '')] = $m['full_name'] ?? ($m['name'] ?? ($m['nama_lengkap'] ?? null));
                        }
                    }
                }
                if (!isset($nikToName[$nikUser])) {
                    $nikToName[$nikUser] = $citizen['data']['full_name'] ?? ($citizen['data']['name'] ?? null);
                }
            } catch (\Throwable $e) {}

            // Tambahan: pastikan semua NIK dalam halaman memiliki nama dengan fallback getCitizenByNIK(nik tagihan)
            try {
                $allNiksInPage = $items->getCollection()->pluck('nik')->map(fn($n) => (string)$n)->unique();
                foreach ($allNiksInPage as $nikRow) {
                    if (!isset($nikToName[$nikRow]) || empty($nikToName[$nikRow])) {
                        $cit = $this->citizenService->getCitizenByNIK($nikRow);
                        if (is_array($cit) && isset($cit['data'])) {
                            $nikToName[$nikRow] = $cit['data']['full_name'] ?? ($cit['data']['name'] ?? ($cit['data']['nama_lengkap'] ?? null));
                        }
                    }
                }
            } catch (\Throwable $e) {}

            $data = $items->getCollection()->map(function ($t) use ($nikToName) {
                return [
                    'id' => $t->id,
                    'nik' => (string) $t->nik,
                    'full_name' => $nikToName[(string)$t->nik] ?? null,
                    'tanggal' => optional($t->tanggal)->format('Y-m-d'),
                    'status' => $t->status,
                    'nominal' => (float) $t->nominal,
                    'keterangan' => $t->keterangan,
                    'villages_id' => $t->villages_id,
                    'kategori' => $t->kategori ? [
                        'id' => $t->kategori->id,
                        'nama' => $t->kategori->nama_kategori,
                    ] : null,
                    'sub_kategori' => $t->subKategori ? [
                        'id' => $t->subKategori->id,
                        'nama' => $t->subKategori->nama_sub_kategori,
                    ] : null,
                ];
            });
            $items->setCollection($data);

            return response()->json([
                'data' => $items,
            ]);
        }

        /**
         * Detail satu tagihan (hanya milik NIK yang login)
         */
        public function show(Request $request, Tagihan $tagihan)
        {
            $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
            if (!$user || empty($user->nik)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Izinkan akses jika pemilik tagihan atau anggota keluarga dalam satu KK
            $allow = false;
            try {
                $viewerNik = (string) $user->nik;
                $ownerNik = (string) $tagihan->nik;

                if ($viewerNik === $ownerNik) {
                    $allow = true;
                } else {
                    $viewer = $this->citizenService->getCitizenByNIK($viewerNik);
                    $owner = $this->citizenService->getCitizenByNIK($ownerNik);
                    $viewerKk = $viewer['data']['kk'] ?? ($viewer['data']['no_kk'] ?? null);
                    $ownerKk = $owner['data']['kk'] ?? ($owner['data']['no_kk'] ?? null);
                    if ($viewerKk && $ownerKk && (string)$viewerKk === (string)$ownerKk) {
                        $allow = true;
                    }
                }
            } catch (\Throwable $e) {
                $allow = false;
            }
            if (!$allow) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $tagihan->load(['kategori', 'subKategori']);

            // Ambil nama berdasarkan NIK pada tagihan secara langsung
            $ownerFullName = null;
            try {
                $ownerNik = (string) $tagihan->nik;
                $ownerData = $this->citizenService->getCitizenByNIK($ownerNik);
                if (is_array($ownerData) && isset($ownerData['data'])) {
                    $ownerFullName = $ownerData['data']['full_name'] ?? ($ownerData['data']['name'] ?? ($ownerData['data']['nama_lengkap'] ?? null));
                }
            } catch (\Throwable $e) {}

            return response()->json([
                'data' => [
                    'id' => $tagihan->id,
                    'nik' => $tagihan->nik,
                    'full_name' => $ownerFullName,
                    'villages_id' => $tagihan->villages_id,
                    'tanggal' => optional($tagihan->tanggal)->format('Y-m-d'),
                    'tanggal_formatted' => optional($tagihan->tanggal)->format('d F Y'),
                    'status' => $tagihan->status,
                    'status_label' => $this->getStatusLabel($tagihan->status),
                    'nominal' => (float) $tagihan->nominal,
                    'nominal_formatted' => 'Rp ' . number_format($tagihan->nominal, 0, ',', '.'),
                    'keterangan' => $tagihan->keterangan,
                    'kategori' => $tagihan->kategori ? [
                        'id' => $tagihan->kategori->id,
                        'nama' => $tagihan->kategori->nama_kategori,
                    ] : null,
                    'sub_kategori' => $tagihan->subKategori ? [
                        'id' => $tagihan->subKategori->id,
                        'nama' => $tagihan->subKategori->nama_sub_kategori,
                    ] : null,
                    'created_at' => $tagihan->created_at ? $tagihan->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $tagihan->updated_at ? $tagihan->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ]);
        }

        /**
         * Daftar kategori tagihan yang tersedia
         */
        public function kategori()
        {
            $kategori = KategoriTagihan::with('subKategoris')->get()->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama' => $k->nama_kategori,
                    'sub_kategori' => $k->subKategoris->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'nama' => $sub->nama_sub_kategori,
                        ];
                    }),
                ];
            });

            return response()->json([
                'data' => $kategori,
            ]);
        }

        /**
         * Helper untuk mendapatkan label status tagihan
         */
        private function getStatusLabel($status)
        {
            switch ($status) {
                case 'belum_bayar':
                    return 'Belum Bayar';
                case 'sudah_bayar':
                    return 'Sudah Bayar';
                case 'terlambat':
                    return 'Terlambat';
                case 'dibatalkan':
                    return 'Dibatalkan';
                default:
                    return ucfirst($status);
            }
        }
    }


