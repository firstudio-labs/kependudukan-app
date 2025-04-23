<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aset;
use App\Services\CitizenService;
use App\Models\Klasifikasi;
use App\Models\JenisAset;
use Illuminate\Support\Facades\Log;
use App\Services\WilayahService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KelolaAsetController extends Controller
{
    protected $citizenService;
    protected $wilayahService;

    public function __construct(
        CitizenService $citizenService,
        WilayahService $wilayahService
    ) {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);

        $assets = Aset::query()
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                return $query->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('nik_pemilik', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%");
            })
            ->with(['klasifikasi', 'jenisAset'])
            ->paginate(10);

        foreach ($assets as $asset) {
            $this->attachLocationNames($asset);
        }

        return view('user.kelola-aset.index', compact('assets'));
    }

    public function create()
    {
        $klasifikasi = Klasifikasi::all();
        $jenis_aset = JenisAset::all();
        $provinces = $this->wilayahService->getProvinces();

        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('user.kelola-aset.create', compact(
            'klasifikasi',
            'jenis_aset',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_aset' => 'required|string|max:255',
                'nik' => 'nullable|string|max:16',
                'nama_pemilik' => 'nullable|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'klasifikasi_id' => 'required|integer',
                'jenis_aset_id' => 'required|integer',
                'tag_lat' => 'nullable|numeric',
                'tag_lng' => 'nullable|numeric',
                'tag_lokasi' => 'nullable|string',
                'foto_aset_depan' => 'nullable|image|max:2048',
                'foto_aset_samping' => 'nullable|image|max:2048',
            ]);

            $foto_aset_depan = null;
            if ($request->hasFile('foto_aset_depan')) {
                try {
                    $file = $request->file('foto_aset_depan');
                    $assetName = Str::slug(substr($validated['nama_aset'], 0, 30));
                    $timestamp = time();
                    $filename = $timestamp . '_' . $assetName . '_depan.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/documents/foto-aset', $filename, 'public');
                    $foto_aset_depan = $path;
                } catch (\Exception $e) {
                    Log::error('Error saving foto depan', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $foto_aset_samping = null;
            if ($request->hasFile('foto_aset_samping')) {
                try {
                    $file = $request->file('foto_aset_samping');
                    $assetName = Str::slug(substr($validated['nama_aset'], 0, 30));
                    $timestamp = time();
                    $filename = $timestamp . '_' . $assetName . '_samping.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/documents/foto-aset', $filename, 'public');
                    $foto_aset_samping = $path;
                } catch (\Exception $e) {
                    Log::error('Error saving foto samping', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $tag_lokasi = null;
            if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
                $latitude = number_format((float) $request->tag_lat, 6, '.', '');
                $longitude = number_format((float) $request->tag_lng, 6, '.', '');
                $tag_lokasi = "$latitude, $longitude";
            } else if ($request->filled('tag_lokasi')) {
                $tag_lokasi = $request->tag_lokasi;
            }

            $aset = Aset::create([
                'user_id' => Auth::id(),
                'nama_aset' => $validated['nama_aset'],
                'nik_pemilik' => $validated['nik'] ?? null,
                'nama_pemilik' => $validated['nama_pemilik'] ?? null,
                'address' => $validated['address'],
                'province_id' => $validated['province_id'],
                'district_id' => $validated['district_id'],
                'sub_district_id' => $validated['sub_district_id'],
                'village_id' => $validated['village_id'],
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'klasifikasi_id' => $validated['klasifikasi_id'],
                'jenis_aset_id' => $validated['jenis_aset_id'],
                'tag_lokasi' => $tag_lokasi,
                'foto_aset_depan' => $foto_aset_depan,
                'foto_aset_samping' => $foto_aset_samping,
            ]);

            Log::info('Asset created', ['id' => $aset->id]);

            return redirect()
                ->route('user.kelola-aset.index')
                ->with('success', 'Data aset berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error creating asset: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $aset = Aset::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!empty($aset->tag_lokasi)) {
            $aset->tag_lat = $aset->getLatitudeAttribute();
            $aset->tag_lng = $aset->getLongitudeAttribute();
        }

        $klasifikasi = Klasifikasi::all();
        $jenis_aset = JenisAset::all();
        $provinces = $this->wilayahService->getProvinces();

        $districts = $this->wilayahService->getKabupaten($aset->province_id);
        $subDistricts = $this->wilayahService->getKecamatan($aset->district_id);
        $villages = $this->wilayahService->getDesa($aset->sub_district_id);

        return view('user.kelola-aset.edit', compact(
            'aset',
            'klasifikasi',
            'jenis_aset',
            'provinces',
            'districts',
            'subDistricts',
            'villages'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            $aset = Aset::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $validated = $request->validate([
                'nama_aset' => 'required|string|max:255',
                'nik' => 'nullable|string|max:16',
                'nama_pemilik' => 'nullable|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'klasifikasi_id' => 'required|integer',
                'jenis_aset_id' => 'required|integer',
                'tag_lat' => 'nullable|numeric',
                'tag_lng' => 'nullable|numeric',
                'tag_lokasi' => 'nullable|string',
                'foto_aset_depan' => 'nullable|image|max:2048',
                'foto_aset_samping' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('foto_aset_depan')) {
                if ($aset->foto_aset_depan && Storage::disk('public')->exists($aset->foto_aset_depan)) {
                    Storage::disk('public')->delete($aset->foto_aset_depan);
                }

                $file = $request->file('foto_aset_depan');
                $assetName = Str::slug(substr($validated['nama_aset'], 0, 30));
                $timestamp = time();
                $filename = $timestamp . '_' . $assetName . '_depan.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/foto-aset', $filename, 'public');
                $validated['foto_aset_depan'] = $path;
            }

            if ($request->hasFile('foto_aset_samping')) {
                if ($aset->foto_aset_samping && Storage::disk('public')->exists($aset->foto_aset_samping)) {
                    Storage::disk('public')->delete($aset->foto_aset_samping);
                }

                $file = $request->file('foto_aset_samping');
                $assetName = Str::slug(substr($validated['nama_aset'], 0, 30));
                $timestamp = time();
                $filename = $timestamp . '_' . $assetName . '_samping.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/foto-aset', $filename, 'public');
                $validated['foto_aset_samping'] = $path;
            }

            $tag_lokasi = null;
            if ($request->filled('tag_lat') && $request->filled('tag_lng')) {
                $latitude = number_format((float) $request->tag_lat, 6, '.', '');
                $longitude = number_format((float) $request->tag_lng, 6, '.', '');
                $tag_lokasi = "$latitude, $longitude";
            } else if ($request->filled('tag_lokasi')) {
                $tag_lokasi = $request->tag_lokasi;
            }

            $aset->update([
                'nama_aset' => $validated['nama_aset'],
                'nik_pemilik' => $validated['nik'] ?? null,
                'nama_pemilik' => $validated['nama_pemilik'] ?? null,
                'address' => $validated['address'],
                'province_id' => $validated['province_id'],
                'district_id' => $validated['district_id'],
                'sub_district_id' => $validated['sub_district_id'],
                'village_id' => $validated['village_id'],
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'klasifikasi_id' => $validated['klasifikasi_id'],
                'jenis_aset_id' => $validated['jenis_aset_id'],
                'tag_lokasi' => $tag_lokasi,
                'foto_aset_depan' => $validated['foto_aset_depan'] ?? $aset->foto_aset_depan,
                'foto_aset_samping' => $validated['foto_aset_samping'] ?? $aset->foto_aset_samping,
            ]);

            return redirect()
                ->route('user.kelola-aset.index')
                ->with('success', 'Data aset berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function attachLocationNames($asset)
    {
        try {
            // Province
            $provinces = $this->wilayahService->getProvinces();
            foreach ($provinces as $province) {
                if ($province['id'] == $asset->province_id) {
                    $asset->province_name = $province['name'];
                    break;
                }
            }

            // District/Kabupaten
            $districts = $this->wilayahService->getKabupaten($asset->province_id);
            foreach ($districts as $district) {
                if ($district['id'] == $asset->district_id) {
                    $asset->district_name = $district['name'];
                    break;
                }
            }

            // Sub-district/Kecamatan
            $subDistricts = $this->wilayahService->getKecamatan($asset->district_id);
            foreach ($subDistricts as $subDistrict) {
                if ($subDistrict['id'] == $asset->sub_district_id) {
                    $asset->sub_district_name = $subDistrict['name'];
                    break;
                }
            }

            // Village/Desa
            $villages = $this->wilayahService->getDesa($asset->sub_district_id);
            foreach ($villages as $village) {
                if ($village['id'] == $asset->village_id) {
                    $asset->village_name = $village['name'];
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching location names: ' . $e->getMessage());
        }

        return $asset;
    }

    public function destroy($id)
    {
        try {
            $aset = Aset::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($aset->foto_aset_depan && Storage::disk('public')->exists($aset->foto_aset_depan)) {
                Storage::disk('public')->delete($aset->foto_aset_depan);
            }

            if ($aset->foto_aset_samping && Storage::disk('public')->exists($aset->foto_aset_samping)) {
                Storage::disk('public')->delete($aset->foto_aset_samping);
            }

            $aset->delete();

            return redirect()
                ->route('user.kelola-aset.index')
                ->with('success', 'Data aset berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}