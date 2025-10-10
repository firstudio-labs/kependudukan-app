<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
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
                'kategori' => $t->kategori ? $t->kategori->nama_kategori : null,
                'sub_kategori' => $t->subKategori ? $t->subKategori->nama_sub_kategori : null,
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
                'tanggal' => optional($tagihan->tanggal)->format('Y-m-d'),
                'status' => $tagihan->status,
                'nominal' => (float) $tagihan->nominal,
                'keterangan' => $tagihan->keterangan,
                'kategori' => $tagihan->kategori ? [
                    'id' => $tagihan->kategori->id,
                    'nama' => $tagihan->kategori->nama_kategori,
                ] : null,
                'sub_kategori' => $tagihan->subKategori ? [
                    'id' => $tagihan->subKategori->id,
                    'nama' => $tagihan->subKategori->nama_sub_kategori,
                ] : null,
            ]
        ]);
    }
}


