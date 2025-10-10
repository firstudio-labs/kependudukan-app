<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KategoriTagihan;
use App\Models\SubKategoriTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
{
    /**
     * List tagihan untuk penduduk yang login (berdasarkan NIK)
     */
    public function index(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? (Auth::guard('penduduk')->user() ?? Auth::user());
        if (!$user || empty($user->nik)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = Tagihan::query()->with(['kategori', 'subKategori'])->where('nik', (string) $user->nik);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', (int) $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', (int) $request->input('tahun'));
        }

        $perPage = (int) $request->input('per_page', 10);
        $items = $query->orderByDesc('tanggal')->paginate($perPage)->withQueryString();

        // Map response ringkas untuk mobile
        $data = $items->getCollection()->map(function ($t) {
            return [
                'id' => $t->id,
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

        if ((string) $tagihan->nik !== (string) $user->nik) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tagihan->load(['kategori', 'subKategori']);

        return response()->json([
            'data' => [
                'id' => $tagihan->id,
                'nik' => $tagihan->nik,
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


