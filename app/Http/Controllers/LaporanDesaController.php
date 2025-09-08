<?php

namespace App\Http\Controllers;

use App\Models\LaporDesa;
use App\Models\LaporanDesa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanDesa::query();

        // For regular users, only show their own reports
        if (Auth::guard('penduduk')->check()) {
            $query->where('user_id', Auth::guard('penduduk')->id());
        }
        // For admin desa, only show reports from their village
        elseif (Auth::user() && Auth::user()->role === 'admin desa') {
            $query->where('village_id', Auth::user()->villages_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_laporan', 'like', "%{$search}%")
                    ->orWhere('deskripsi_laporan', 'like', "%{$search}%");
            });
        }


        $laporans = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('user.laporan-desa.index', compact('laporans'));
    }

    public function create()
    {
        $categories = [];
        $laporDesas = LaporDesa::all()->groupBy('ruang_lingkup');

        foreach ($laporDesas as $ruangLingkup => $items) {
            $categories[$ruangLingkup] = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'bidang' => $item->bidang
                ];
            })->toArray();
        }

        return view('user.laporan-desa.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lapor_desa_id' => 'required|exists:lapor_desas,id',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tag_lat' => 'nullable|numeric',
            'tag_lng' => 'nullable|numeric',
            'location_address' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'lapor_desa_id',
            'judul_laporan',
            'deskripsi_laporan',
        ]);

        // Address from map (editable by user before submit)
        if ($request->filled('location_address')) {
            $data['lokasi'] = trim($request->input('location_address'));
        }

        // Handle tag location from map coordinates
        $tag_lokasi = null;
        if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
            $latitude = number_format((float) $request->tag_lat, 6, '.', '');
            $longitude = number_format((float) $request->tag_lng, 6, '.', '');
            $tag_lokasi = "$latitude, $longitude";
        }
        $data['tag_lokasi'] = $tag_lokasi;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $reportName = Str::slug(substr($request->judul_laporan, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $reportName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/foto-lapordesa', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Set user_id and village_id
        if (Auth::guard('penduduk')->check()) {
            $data['user_id'] = Auth::guard('penduduk')->id();

            // Get the citizen data from the service to properly retrieve village_id
            $pendudukNIK = Auth::guard('penduduk')->user()->nik;
            $citizenService = app(\App\Services\CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($pendudukNIK);

            if ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['villages_id'])) {
                // Use village_id from citizen profile data
                $data['village_id'] = $citizenData['data']['villages_id'];
            } elseif ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['village_id'])) {
                // Alternative field name
                $data['village_id'] = $citizenData['data']['village_id'];
            } else {
                // Fallback to user's villages_id if available, or default to 1
                $data['village_id'] = Auth::guard('penduduk')->user()->villages_id ?? 1;
            }
        }

        // Set initial status
        $data['status'] = 'Menunggu';

        LaporanDesa::create($data);

        return redirect()->route('user.laporan-desa.index')
            ->with('success', 'Laporan berhasil dikirim dan sedang menunggu diproses.');
    }

    public function show($id)
    {
        try {
            $laporan = LaporanDesa::with('laporDesa')->findOrFail($id);

            // For API requests, return JSON response
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $laporan
                ]);
            }

            return view('user.laporan-desa.show', compact('laporan'));
        } catch (\Exception $e) {
            \Log::error('Error in laporan-desa show: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $laporanDesa = LaporanDesa::findOrFail($id);

        if ($laporanDesa->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($laporanDesa->status !== 'Menunggu') {
            return redirect()->route('user.laporan-desa.index')
                ->with('error', 'Hanya laporan dengan status Menunggu yang dapat diedit.');
        }

        $categories = [];
        $laporDesas = LaporDesa::all()->groupBy('ruang_lingkup');

        foreach ($laporDesas as $ruangLingkup => $items) {
            $categories[$ruangLingkup] = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'bidang' => $item->bidang
                ];
            })->toArray();
        }

        // Parse coordinates from tag_lokasi
        $lat = '';
        $lng = '';
        if (!empty($laporanDesa->tag_lokasi)) {
            $coordinates = explode(',', $laporanDesa->tag_lokasi);
            if (count($coordinates) >= 2) {
                $lat = trim($coordinates[0]);
                $lng = trim($coordinates[1]);
            }
        }

        $selectedRuangLingkup = $laporanDesa->laporDesa->ruang_lingkup ?? null;

        return view('user.laporan-desa.edit', compact('laporanDesa', 'categories', 'selectedRuangLingkup', 'lat', 'lng'));
    }

    public function update(Request $request, $id)
    {
        $laporanDesa = LaporanDesa::findOrFail($id);

        if ($laporanDesa->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($laporanDesa->status !== 'Menunggu') {
            return redirect()->route('user.laporan-desa.index')
                ->with('error', 'Hanya laporan dengan status Menunggu yang dapat diedit.');
        }

        $request->validate([
            'lapor_desa_id' => 'required|exists:lapor_desas,id',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tag_lat' => 'nullable|numeric',
            'tag_lng' => 'nullable|numeric',
            'location_address' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'lapor_desa_id',
            'judul_laporan',
            'deskripsi_laporan',
        ]);

        // Address from map (editable by user)
        if ($request->filled('location_address')) {
            $data['lokasi'] = trim($request->input('location_address'));
        }

        // Handle tag location from map coordinates
        $tag_lokasi = null;
        if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
            $latitude = number_format((float) $request->tag_lat, 6, '.', '');
            $longitude = number_format((float) $request->tag_lng, 6, '.', '');
            $tag_lokasi = "$latitude, $longitude";
        }
        $data['tag_lokasi'] = $tag_lokasi;

        if ($request->hasFile('gambar')) {
            // Delete previous image if exists
            if ($laporanDesa->gambar && Storage::exists('public/' . $laporanDesa->gambar)) {
                Storage::delete('public/' . $laporanDesa->gambar);
            }

            $file = $request->file('gambar');
            $reportName = Str::slug(substr($request->judul_laporan, 0, 30));
            $timestamp = time();
            $filename = $timestamp . '_' . $reportName . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/documents/foto-lapordesa', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Update village_id if there was a change in the user's profile
        if (Auth::guard('penduduk')->check()) {
            $pendudukNIK = Auth::guard('penduduk')->user()->nik;
            $citizenService = app(\App\Services\CitizenService::class);
            $citizenData = $citizenService->getCitizenByNIK($pendudukNIK);

            if ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['villages_id'])) {
                // Use village_id from citizen profile data
                $data['village_id'] = $citizenData['data']['villages_id'];
            } elseif ($citizenData && isset($citizenData['data']) && isset($citizenData['data']['village_id'])) {
                // Alternative field name
                $data['village_id'] = $citizenData['data']['village_id'];
            }
        }

        $laporanDesa->update($data);

        return redirect()->route('user.laporan-desa.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $laporanDesa = LaporanDesa::findOrFail($id);

        if ($laporanDesa->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($laporanDesa->status !== 'Menunggu') {
            return redirect()->route('user.laporan-desa.index')
                ->with('error', 'Hanya laporan dengan status Menunggu yang dapat dihapus.');
        }

        // Delete image if exists
        if ($laporanDesa->gambar && Storage::exists('public/' . $laporanDesa->gambar)) {
            Storage::delete('public/' . $laporanDesa->gambar);
        }

        $laporanDesa->delete();

        return redirect()->route('user.laporan-desa.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }


}
