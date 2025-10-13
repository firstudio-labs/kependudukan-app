<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BeritaDesa;

class AdminBeritaDesaApiController extends Controller
{
    private function getAdminUser(Request $request)
    {
        return $request->attributes->get('token_owner') ?? Auth::guard('web')->user();
    }

    private function ensureAdminAccess($user)
    {
        if (!$user) return [false, 'Unauthorized'];
        $allowed = ['superadmin', 'admin desa', 'admin kabupaten', 'operator'];
        if (!$user->role || !in_array(strtolower($user->role), $allowed)) return [false, 'Forbidden'];
        return [true, null];
    }

    public function index(Request $request)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);

        $query = BeritaDesa::query()->where('villages_id', $user->villages_id);
        if ($request->filled('status')) $query->where('status', $request->input('status'));
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
            'komentar' => 'nullable|string',
            'gambar' => 'nullable|string',
            'status' => 'nullable|in:archived,published'
        ]);
        $validated['villages_id'] = $user->villages_id;
        $validated['status'] = $validated['status'] ?? 'published';
        $berita = BeritaDesa::create($validated);
        return response()->json(['success' => true, 'data' => $berita], 201);
    }

    public function show(Request $request, BeritaDesa $berita)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $berita->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        return response()->json(['data' => $berita]);
    }

    public function update(Request $request, BeritaDesa $berita)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $berita->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'komentar' => 'nullable|string',
            'gambar' => 'nullable|string',
            'status' => 'nullable|in:archived,published'
        ]);
        $berita->update($validated);
        return response()->json(['success' => true, 'data' => $berita]);
    }

    public function destroy(Request $request, BeritaDesa $berita)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $berita->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        $berita->delete();
        return response()->json(['success' => true]);
    }

    public function publish(Request $request, BeritaDesa $berita)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $berita->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        $berita->status = 'published';
        $berita->save();
        return response()->json(['success' => true, 'data' => $berita]);
    }

    public function archive(Request $request, BeritaDesa $berita)
    {
        $user = $this->getAdminUser($request);
        [$ok, $err] = $this->ensureAdminAccess($user);
        if (!$ok) return response()->json(['message' => $err], $err === 'Unauthorized' ? 401 : 403);
        if ((int) $berita->villages_id !== (int) $user->villages_id) return response()->json(['message' => 'Forbidden'], 403);
        $berita->status = 'archived';
        $berita->save();
        return response()->json(['success' => true, 'data' => $berita]);
    }
}


