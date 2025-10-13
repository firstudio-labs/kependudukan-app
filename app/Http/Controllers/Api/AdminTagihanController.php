<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KategoriTagihan;
use App\Models\SubKategoriTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTagihanController extends Controller
{
    /**
     * Dapatkan user admin dari token atau guard web
     */
    private function getAdminUser(Request $request)
    {
        $user = $request->attributes->get('token_owner') ?? Auth::guard('web')->user();
        return $user;
    }

    /**
     * Pastikan role admin desa atau superadmin/operator
     */
    private function ensureAdminAccess($user)
    {
        if (!$user) {
            return [false, 'Unauthorized'];
        }

        $allowed = ['superadmin', 'admin desa', 'admin kabupaten', 'operator'];
        if (!$user->role || !in_array(strtolower($user->role), $allowed)) {
            return [false, 'Forbidden'];
        }

        return [true, null];
    }

    /**
     * List tagihan per desa admin (villages_id)
     */
    public function index(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        $query = Tagihan::with(['kategori', 'subKategori'])
            ->where('villages_id', $user->villages_id);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', (int) $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', (int) $request->input('tahun'));
        }

        $perPage = (int) $request->input('per_page', 10);
        $items = $query->orderByDesc('tanggal')->paginate($perPage)->withQueryString();

        return response()->json(['data' => $items]);
    }

    /**
     * Buat tagihan baru untuk desa admin
     */
    public function store(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        $validated = $request->validate([
            'nik' => 'required|string',
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'sub_kategori_id' => 'required|exists:sub_kategori_tagihans,id',
            'nominal' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,lunas,belum_lunas',
            'tanggal' => 'required|date'
        ]);

        $validated['villages_id'] = $user->villages_id;

        $tagihan = Tagihan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat',
            'data' => $tagihan
        ], 201);
    }

    /**
     * Detail tagihan (hanya dalam desa admin)
     */
    public function show(Request $request, Tagihan $tagihan)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        if ((int) $tagihan->villages_id !== (int) $user->villages_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tagihan->load(['kategori', 'subKategori']);
        return response()->json(['data' => $tagihan]);
    }

    /**
     * Update tagihan (hanya dalam desa admin)
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        if ((int) $tagihan->villages_id !== (int) $user->villages_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'nik' => 'sometimes|required|string',
            'kategori_id' => 'sometimes|required|exists:kategori_tagihans,id',
            'sub_kategori_id' => 'sometimes|required|exists:sub_kategori_tagihans,id',
            'nominal' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,lunas,belum_lunas',
            'tanggal' => 'sometimes|required|date'
        ]);

        $tagihan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil diperbarui',
            'data' => $tagihan
        ]);
    }

    /**
     * Hapus tagihan (hanya dalam desa admin)
     */
    public function destroy(Request $request, Tagihan $tagihan)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        if ((int) $tagihan->villages_id !== (int) $user->villages_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tagihan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dihapus'
        ]);
    }

    /**
     * Update status cepat
     */
    public function updateStatus(Request $request, Tagihan $tagihan)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) {
            return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        }

        if ((int) $tagihan->villages_id !== (int) $user->villages_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,lunas,belum_lunas'
        ]);

        $tagihan->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $tagihan->status
        ]);
    }

    /**
     * Referensi kategori & sub kategori
     */
    public function kategori()
    {
        $kategori = KategoriTagihan::with('subKategoris')->orderBy('nama_kategori')->get();
        return response()->json(['data' => $kategori]);
    }

    public function subKategoriByKategori($kategoriId)
    {
        $subs = SubKategoriTagihan::where('kategori_id', $kategoriId)->orderBy('nama_sub_kategori')->get();
        return response()->json(['data' => $subs]);
    }
}


