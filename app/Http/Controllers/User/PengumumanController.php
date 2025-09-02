<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    protected $citizenService;
    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index(Request $request)
    {
        $query = Pengumuman::query();
        if (Auth::guard('penduduk')->check()) {
            $penduduk = Auth::guard('penduduk')->user();
            $citizenData = $this->citizenService->getCitizenByNIK($penduduk->nik);
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;
            if ($villageId) {
                $query->where('villages_id', (int) $villageId);
            }
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")->orWhere('deskripsi', 'like', "%{$s}%");
            });
        }
        $pengumuman = $query->latest()->paginate(10);
        return view('user.pengumuman.index', compact('pengumuman'));
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $pengumuman]);
    }
}


