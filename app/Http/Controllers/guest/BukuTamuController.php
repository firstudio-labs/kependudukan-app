<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use App\Models\BukuTamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuTamuController extends Controller
{
    /**
     * Display the buku tamu form page
     *
     * @param string $province_id
     * @param string $district_id
     * @param string $sub_district_id
     * @param string $village_id
     * @return \Illuminate\View\View
     */
    public function index($province_id, $district_id, $sub_district_id, $village_id)
    {
        return view('guest.buku-tamu.index', compact(
            'province_id',
            'district_id',
            'sub_district_id',
            'village_id'
        ));
    }

    /**
     * Store a new buku tamu entry
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'keperluan' => 'required|string|max:255',
            'pesan' => 'nullable|string', // Tetap menggunakan 'pesan' sesuai database
            'tanda_tangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Menambahkan validasi foto
            'province_id' => 'required|string|max:255',
            'district_id' => 'required|string|max:255',
            'sub_district_id' => 'required|string|max:255',
            'village_id' => 'required|string|max:255',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('buku-tamu', 'public');
            $validated['foto'] = $fotoPath;
        }

        BukuTamu::create($validated);

        return redirect()->route('guest.buku-tamu', [
            'province_id' => $validated['province_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'village_id' => $validated['village_id']
        ])->with('success', 'Terima kasih! Data kunjungan Anda telah tersimpan.');
    }
} 
