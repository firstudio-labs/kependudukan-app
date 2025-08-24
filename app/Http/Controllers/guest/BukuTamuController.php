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
        // Debug: Log semua data yang diterima
        \Log::info('BukuTamu store request data received');
        \Log::info('Request keys: ' . implode(', ', array_keys($request->all())));

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'keperluan' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'tanda_tangan' => 'nullable|string',
            'foto' => 'nullable|string|max:100000', // Tambah max length untuk base64
            'province_id' => 'required|string|max:255',
            'district_id' => 'required|string|max:255',
            'sub_district_id' => 'required|string|max:255',
            'village_id' => 'required|string|max:255',
        ]);

        // Debug: Log data yang sudah divalidasi
        \Log::info('BukuTamu validation passed');

        // Handle foto base64 data
        if ($request->filled('foto') && $request->foto !== '') {
            $fotoData = $request->foto;
            \Log::info('Foto data received, length: ' . strlen($fotoData));

            // Jika data terlalu panjang, kompres lagi
            if (strlen($fotoData) > 50000) {
                \Log::warning('Foto data too long, compressing...');
                // Simpan tanpa foto untuk sementara
                $validated['foto'] = null;
            } else {
                $validated['foto'] = $fotoData;
            }
        } else {
            \Log::info('No foto data received');
            $validated['foto'] = null;
        }

        try {
            // Debug: Log data yang akan disimpan
            \Log::info('Attempting to create BukuTamu');

            $bukuTamu = BukuTamu::create($validated);

            // Debug: Log success
            \Log::info('BukuTamu created successfully with ID: ' . $bukuTamu->id);

            return redirect()->route('guest.buku-tamu', [
                'province_id' => $validated['province_id'],
                'district_id' => $validated['district_id'],
                'sub_district_id' => $validated['sub_district_id'],
                'village_id' => $validated['village_id']
            ])->with('success', 'Terima kasih! Data kunjungan Anda telah tersimpan.');

        } catch (\Exception $e) {
            // Debug: Log detailed error
            \Log::error('Error saving buku tamu: ' . $e->getMessage());
            \Log::error('Error class: ' . get_class($e));
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
