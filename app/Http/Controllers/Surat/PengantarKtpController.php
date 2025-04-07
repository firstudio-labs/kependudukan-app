<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengantarKtp;
use App\Services\JobService;
use App\Services\WilayahService;
use App\Services\CitizenService;
use Illuminate\Support\Facades\DB;

class PengantarKtpController extends Controller
{
    protected $wilayahService;
    protected $citizenService;
    protected $jobService;

    /**
     * Create a new controller instance.
     *
     * @param WilayahService $wilayahService
     * @param CitizenService $citizenService
     * @param JobService $jobService
     */
    public function __construct(WilayahService $wilayahService, CitizenService $citizenService, JobService $jobService)
    {
        $this->wilayahService = $wilayahService;
        $this->citizenService = $citizenService;
        $this->jobService = $jobService;
    }

    /**
     * Display a listing of the KTP application letters
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PengantarKtp::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_card_number', 'like', "%{$search}%")
                  ->orWhere('application_type', 'like', "%{$search}%")
                  ->orWhere('signing', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $ktpList = $query->paginate(10);

        return view('superadmin.datamaster.surat.pengantar-ktp.index', compact('ktpList'));
    }

    /**
     * Show the form for creating a new KTP application letter.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get jobs and regions data from services
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();
        $signers = \App\Models\Penandatangan::all(); // Fetch signers

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];

        return view('superadmin.datamaster.surat.pengantar-ktp.create', compact(
            'jobs',
            'provinces',
            'districts',
            'subDistricts',
            'villages',
            'signers'
        ));
    }

    /**
     * Store a newly created KTP application letter in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the request data for debugging
        \Log::info('PengantarKTP Store Request:', $request->all());

        // Validate all required fields based on the database schema
        $validated = $request->validate([
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'application_type' => 'required|string|in:Baru,Perpanjang,Pergantian',
            'nik' => 'required|numeric',
            'full_name' => 'required|string',
            'kk' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'hamlet' => 'required|string', // Dusun
            'signing' => 'nullable|string',
        ]);

        try {
            // Create new PengantarKtp instance with validated data
            $ktp = new PengantarKtp();
            $ktp->province_id = $validated['province_id'];
            $ktp->district_id = $validated['district_id'];
            $ktp->subdistrict_id = $validated['subdistrict_id'];
            $ktp->village_id = $validated['village_id'];
            $ktp->letter_number = $validated['letter_number'];
            $ktp->application_type = $validated['application_type'];
            $ktp->nik = $validated['nik'];
            $ktp->full_name = $validated['full_name'];
            $ktp->kk = $validated['kk'];
            $ktp->address = $validated['address'];
            $ktp->rt = $validated['rt'];
            $ktp->rw = $validated['rw'];
            $ktp->hamlet = $validated['hamlet'];
            $ktp->signing = $validated['signing'];
            $ktp->save();

            // Log successful creation
            \Log::info('PengantarKTP Created Successfully:', $ktp->toArray());

            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil dibuat!');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('PengantarKTP Creation Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->withInput()->with('error', 'Gagal membuat surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified KTP application letter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);

            // Get jobs and provinces data from services
            $provinces = $this->wilayahService->getProvinces();
            $jobs = $this->jobService->getAllJobs();
            $signers = \App\Models\Penandatangan::all(); // Fetch signers

            // Get district data if province_id exists
            $districts = [];
            if (!empty($ktp->province_id)) {
                $provinceCode = null;
                foreach ($provinces as $province) {
                    if ($province['id'] == $ktp->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                }
            }

            // Get subdistrict data if district_id exists
            $subDistricts = [];
            if (!empty($ktp->district_id) && !empty($districts)) {
                $districtCode = null;
                foreach ($districts as $district) {
                    if ($district['id'] == $ktp->district_id) {
                        $districtCode = $district['code'];
                        break;
                    }
                }

                if ($districtCode) {
                    $subDistricts = $this->wilayahService->getKecamatan($districtCode);
                }
            }

            // Get village data if subdistrict_id exists
            $villages = [];
            if (!empty($ktp->subdistrict_id) && !empty($subDistricts)) {
                $subDistrictCode = null;
                foreach ($subDistricts as $subDistrict) {
                    if ($subDistrict['id'] == $ktp->subdistrict_id) {
                        $subDistrictCode = $subDistrict['code'];
                        break;
                    }
                }

                if ($subDistrictCode) {
                    $villages = $this->wilayahService->getDesa($subDistrictCode);
                }
            }

            // Now also fetch the subdistrict data for the bottom address section
            // We need this for the subdistrict_name field
            $addressSubDistricts = [];
            // If we already have the district code from above, reuse it
            if (!empty($districtCode)) {
                $addressSubDistricts = $this->wilayahService->getKecamatan($districtCode);
            }

            // Now also fetch the village data for the bottom address section
            // We need this for the village_name field
            $addressVillages = [];
            // Get the subdistrict code for the address section (might be different from the top section)
            $addressSubDistrictCode = null;
            if (!empty($ktp->subdistrict_name)) {
                foreach ($addressSubDistricts as $subDistrict) {
                    if ($subDistrict['id'] == $ktp->subdistrict_name) {
                        $addressSubDistrictCode = $subDistrict['code'];
                        break;
                    }
                }

                if ($addressSubDistrictCode) {
                    $addressVillages = $this->wilayahService->getDesa($addressSubDistrictCode);
                }
            }

            // If we couldn't get the village data from subdistrict_name, try using the top section's subdistrict code
            if (empty($addressVillages) && !empty($subDistrictCode)) {
                $addressVillages = $this->wilayahService->getDesa($subDistrictCode);
            }

            // Merge the address sections with the regular ones if they're empty
            if (empty($addressSubDistricts)) {
                $addressSubDistricts = $subDistricts;
            }

            if (empty($addressVillages)) {
                $addressVillages = $villages;
            }

            // Add the address section villages and subdistricts to the view
            return view('superadmin.datamaster.surat.pengantar-ktp.edit', compact(
                'ktp',
                'jobs',
                'provinces',
                'districts',
                'subDistricts',
                'villages',
                'addressSubDistricts',
                'addressVillages',
                'signers'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in PengantarKtpController@edit: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('error', 'Gagal memuat data surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified KTP application letter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Log the request data for debugging
        \Log::info('PengantarKTP Update Request:', $request->all());

        // Use the same validation rules as the store method
        $validated = $request->validate([
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'subdistrict_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'letter_number' => 'nullable|string',
            'application_type' => 'required|string|in:Baru,Perpanjang,Pergantian',
            'nik' => 'required|numeric',
            'full_name' => 'required|string',
            'kk' => 'required|numeric',
            'address' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'hamlet' => 'required|string', // Dusun
            'signing' => 'nullable|string',
        ]);

        try {
            $ktp = PengantarKtp::findOrFail($id);

            // Update model with validated data
            $ktp->province_id = $validated['province_id'];
            $ktp->district_id = $validated['district_id'];
            $ktp->subdistrict_id = $validated['subdistrict_id'];
            $ktp->village_id = $validated['village_id'];
            $ktp->letter_number = $validated['letter_number'];
            $ktp->application_type = $validated['application_type'];
            $ktp->nik = $validated['nik'];
            $ktp->full_name = $validated['full_name'];
            $ktp->kk = $validated['kk'];
            $ktp->address = $validated['address'];
            $ktp->rt = $validated['rt'];
            $ktp->rw = $validated['rw'];
            $ktp->hamlet = $validated['hamlet'];
            $ktp->signing = $validated['signing'];
            $ktp->save();

            // Log successful update
            \Log::info('PengantarKTP Updated Successfully:', $ktp->toArray());

            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('PengantarKTP Update Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->withInput()->with('error', 'Gagal memperbarui surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified KTP application letter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);
            $ktp->delete();

            return redirect()->route('superadmin.surat.pengantar-ktp.index')
                ->with('success', 'Surat pengantar KTP berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Export KTP application to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPDF($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($ktp->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($ktp->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get location names using wilayah service
            $provinceName = '';
            $districtName = '';
            $subdistrictName = '';
            $villageName = '';

            // Get province data
            if (!empty($ktp->province_id)) {
                $provinces = $this->wilayahService->getProvinces();
                foreach ($provinces as $province) {
                    if ($province['id'] == $ktp->province_id) {
                        $provinceName = $province['name'];
                        break;
                    }
                }
            }

            // Get district/kabupaten data
            if (!empty($ktp->district_id)) {
                $provinceCode = null;
                foreach ($this->wilayahService->getProvinces() as $province) {
                    if ($province['id'] == $ktp->province_id) {
                        $provinceCode = $province['code'];
                        break;
                    }
                }

                if ($provinceCode) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $ktp->district_id) {
                            $districtName = $district['name'];
                            break;
                        }
                    }
                }
            }

            // Get subdistrict/kecamatan data
            if (!empty($ktp->subdistrict_id)) {
                $districtCode = null;
                if (!empty($provinceCode)) {
                    $districts = $this->wilayahService->getKabupaten($provinceCode);
                    foreach ($districts as $district) {
                        if ($district['id'] == $ktp->district_id) {
                            $districtCode = $district['code'];
                            break;
                        }
                    }
                }

                if ($districtCode) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $ktp->subdistrict_id) {
                            $subdistrictName = $subdistrict['name'];
                            break;
                        }
                    }
                }
            }

            // Get village/desa data
            if (!empty($ktp->village_id)) {
                $subdistrictCode = null;
                if (!empty($districtCode)) {
                    $subdistricts = $this->wilayahService->getKecamatan($districtCode);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $ktp->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $ktp->village_id) {
                            $villageName = $village['name'];
                            $villageCode = $village['code']; // Store the complete village code
                            break;
                        }
                    }
                }
            }

            // Get application type name
            $applicationType = $ktp->application_type;

            // Prepare the nik and full_name data (could be array or string)
            $nik = is_array($ktp->nik) ? ($ktp->nik[0] ?? '') : $ktp->nik;
            $fullName = is_array($ktp->full_name) ? ($ktp->full_name[0] ?? '') : $ktp->full_name;

            // Pass data to the view
            return view('superadmin.datamaster.surat.pengantar-ktp.PengantarKTP', compact(
                'ktp',
                'provinceName',
                'districtName',
                'subdistrictName',
                'villageName',
                'villageCode', // Add the village code
                'applicationType',
                'nik',
                'fullName',
                'signing_name' // Add the signing_name variable
            ));

        } catch (\Exception $e) {
            \Log::error('Error generating PDF for PengantarKTP: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh surat pengantar KTP: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for the specified KTP introduction letter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDF($id)
    {
        try {
            $ktp = PengantarKtp::findOrFail($id);

            // Get the signing name (keterangan) from Penandatangan model
            $signing_name = null;
            if (!empty($ktp->signing)) {
                $penandatangan = \App\Models\Penandatangan::find($ktp->signing);
                if ($penandatangan) {
                    $signing_name = $penandatangan->keterangan;
                }
            }

            // Get village/desa data
            if (!empty($ktp->village_id)) {
                $subdistrictCode = null;
                if (!empty($ktp->subdistrict_id)) {
                    $subdistricts = $this->wilayahService->getKecamatan($ktp->district_id);
                    foreach ($subdistricts as $subdistrict) {
                        if ($subdistrict['id'] == $ktp->subdistrict_id) {
                            $subdistrictCode = $subdistrict['code'];
                            break;
                        }
                    }
                }

                if ($subdistrictCode) {
                    $villages = $this->wilayahService->getDesa($subdistrictCode);
                    foreach ($villages as $village) {
                        if ($village['id'] == $ktp->village_id) {
                            $villageName = $village['name'];
                            $villageCode = $village['code']; // Store the complete village code
                            break;
                        }
                    }
                }
            }

            return view('superadmin.datamaster.surat.pengantar-ktp.PengantarKTP', [
                'ktp' => $ktp,
                'fullName' => $ktp->full_name,
                'provinceName' => $ktp->province_id,
                'districtName' => $ktp->district_id,
                'subdistrictName' => $ktp->subdistrict_id,
                'villageName' => $villageName,
                'villageCode' => $villageCode, // Add the village code
                'nameChars' => str_split($ktp->full_name),
                'nikChars' => str_split($ktp->nik),
                'applicationType' => $ktp->application_type,
                'signing_name' => $signing_name // Pass the signing name to the view
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }
}
