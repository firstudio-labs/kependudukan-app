<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use App\Models\Keperluan;
use App\Models\Pelayanan;
use Illuminate\Http\Request;
use App\Services\WilayahService;


class PelayananController extends Controller
{
    protected $wilayahService;

    public function __construct(
        WilayahService $wilayahService
    ) {
        $this->wilayahService = $wilayahService;
    }
    /**
     * Display the pelayanan index page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $provinces = $this->wilayahService->getProvinces();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        $keperluanList = Keperluan::all();
        return view('guest.pelayanan.index', compact('keperluanList', 'provinces',
            'districts',
            'subDistricts',
            'villages'));
    }

    /**
     * Store a new pelayanan request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'province_id' => 'required|string|max:255',
            'district_id' => 'required|string|max:255',
            'sub_district_id' => 'required|string|max:255',
            'village_id' => 'required|string|max:255',
            'alamat' => 'required|string',
            'keperluan' => 'required|exists:keperluan,id',
        ]);

        // Create the pelayanan record
        $pelayanan = new Pelayanan($validated);

        // Get the keperluan
        $keperluan = Keperluan::find($validated['keperluan']);

        // Generate queue number if not a document service
        if (!$pelayanan->isPelayananSurat()) {
            // Generate queue number specific to this village and today's date
            $pelayanan->no_antrian = Pelayanan::generateQueueNumber($validated['village_id']);
        }

        $pelayanan->save();

        // Query to get village name for display
        $villageName = null;
        try {
            // Check if the method exists to avoid errors
            if (method_exists($this->wilayahService, 'getVillageById')) {
                $villageData = $this->wilayahService->getVillageById($validated['village_id']);
                $villageName = $villageData['name'] ?? null;
            } else {
                // Fallback: Try to get name from the villages data using code
                $villages = $this->wilayahService->getVillages($validated['sub_district_id']);
                foreach ($villages as $village) {
                    if ($village['id'] === $validated['village_id']) {
                        $villageName = $village['name'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // If there's an error, just continue without the village name
            \Log::error("Error getting village name: " . $e->getMessage());
        }

        // Instead of redirecting to pelayanan.index, redirect to homepage's pelayanan section
        if ($pelayanan->isPelayananSurat()) {
            // For document service, redirect to homepage with success message
            return redirect()->route('homepage', ['#pelayanan'])
                ->with('success', 'Pendaftaran pelayanan surat berhasil')
                ->with('pelayanan_id', $pelayanan->id);
        } else {
            // For queue number service, redirect to homepage with queue number and success message
            return redirect()->route('homepage', ['#pelayanan'])
                ->with('success', 'Pengambilan nomor antrian berhasil')
                ->with('no_antrian', $pelayanan->no_antrian)
                ->with('village_name', $villageName)
                ->with('pelayanan_id', $pelayanan->id);
        }
    }

    /**
     * Display the pelayanan surat form
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showSuratForm($id)
    {
        $pelayanan = Pelayanan::findOrFail($id);
        return view('guest.pelayanan.index', compact('pelayanan'));
    }

    /**
     * Display the queue number
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showAntrian($id)
    {
        $pelayanan = Pelayanan::findOrFail($id);
        return view('guest.pelayanan.index', compact('pelayanan'));
    }
}
