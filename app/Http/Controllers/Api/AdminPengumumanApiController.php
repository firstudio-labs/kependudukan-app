<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengumuman;

class AdminPengumumanApiController extends Controller
{
    private function getAdminUser(Request $request)
    {
        return $request->attributes->get('token_owner') ?? Auth::guard('web')->user();
    }

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

    public function index(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);

        $query = Pengumuman::query()->where('villages_id', $user->villages_id);
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")->orWhere('deskripsi', 'like', "%{$s}%");
            });
        }
        $perPage = (int) $request->input('per_page', 10);
        $items = $query->latest()->paginate($perPage)->withQueryString();
        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|string'
        ]);
        $validated['villages_id'] = $user->villages_id;
        $pengumuman = Pengumuman::create($validated);
        return response()->json(['success' => true, 'data' => $pengumuman], 201);
    }

    public function show(Request $request, Pengumuman $pengumuman)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $pengumuman->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        return response()->json(['data' => $pengumuman]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $pengumuman->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'gambar' => 'nullable|string'
        ]);
        $pengumuman->update($validated);
        return response()->json(['success' => true, 'data' => $pengumuman]);
    }

    public function destroy(Request $request, Pengumuman $pengumuman)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $pengumuman->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        $pengumuman->delete();
        return response()->json(['success' => true]);
    }
}


