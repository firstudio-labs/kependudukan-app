<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // You'll need to install Laravel Excel package
use App\Imports\CitizensImport; // We'll create this import class
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Added for cache invalidation
use Illuminate\Support\Facades\DB; // Added for database queries
use App\Models\InformasiUsahaChangeRequest;
use App\Models\Penduduk;
use App\Models\InformasiUsaha;
use App\Services\CitizenServiceV2;

class BiodataController extends Controller
{
    protected $citizenService;
    protected $citizenServiceV2;
    protected $jobService;
    protected $wilayahService;

    public function __construct(
        CitizenService $citizenService,
        CitizenServiceV2 $citizenServiceV2,
        JobService $jobService,
        WilayahService $wilayahService
    ) {
        $this->citizenService = $citizenService;
        $this->citizenServiceV2 = $citizenServiceV2;
        $this->jobService = $jobService;
        $this->wilayahService = $wilayahService;
    }
    /**
     * Ambil daftar anggota keluarga (NIK + nama) untuk dropdown di profil.
     */
    public function getProfileFamilyMembers()
    {
        try {
            $user = auth()->guard('penduduk')->user() ?: auth()->guard('web')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $members = [];

            // Ambil dari API berdasarkan KK bila tersedia, jika tidak coba cari KK lewat NIK
            $kk = $user->citizen_data['kk'] ?? $user->no_kk ?? null;
            if (!$kk && !empty($user->nik)) {
                try {
                    $citizenSelf = $this->citizenService->getCitizenByNIK((int) $user->nik);
                    $kk = $citizenSelf['data']['kk'] ?? $citizenSelf['kk'] ?? $kk;
                } catch (\Exception $e) {
                    Log::warning('Fallback getCitizenByNIK failed when fetching KK: ' . $e->getMessage());
                }
            }

            if ($kk) {
                $resp = $this->citizenService->getFamilyMembersByKK($kk);
                if (isset($resp['data']) && is_array($resp['data'])) {
                    foreach ($resp['data'] as $m) {
                        $members[] = [
                            'nik' => $m['nik'] ?? '',
                            'full_name' => $m['full_name'] ?? ($m['nama'] ?? ''),
                            'family_status' => $m['family_status'] ?? ($m['hubungan_keluarga'] ?? ''),
                        ];
                    }
                }
            }

            // Fallback: gunakan family_members yang mungkin sudah ditempel di session user
            if (empty($members) && isset($user->family_members) && is_array($user->family_members)) {
                foreach ($user->family_members as $m) {
                    $members[] = [
                        'nik' => $m['nik'] ?? '',
                        'full_name' => $m['full_name'] ?? ($m['nama'] ?? ''),
                        'family_status' => $m['family_status'] ?? ($m['hubungan_keluarga'] ?? ''),
                    ];
                }
            }

            // Pastikan user sendiri ada di daftar
            $selfNik = $user->nik ?? null;
            if ($selfNik && !collect($members)->pluck('nik')->contains($selfNik)) {
                $members[] = [
                    'nik' => $selfNik,
                    'full_name' => $user->nama_lengkap ?? ($user->nama ?? 'Pengguna'),
                    'family_status' => $user->citizen_data['family_status'] ?? ($user->hubungan_keluarga ?? ''),
                ];
            }

            return response()->json(['success' => true, 'data' => $members]);
        } catch (\Exception $e) {
            Log::error('getProfileFamilyMembers error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat anggota keluarga'], 500);
        }
    }

    /**
     * Ambil detail biodata by NIK untuk prefill form profil (numeric select values).
     */
    public function getCitizenForProfile($nik)
    {
        try {
            $data = $this->getLatestDataForForm($nik);
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            $this->normalizeSelectValues($data);
            $this->formatDatesForView($data);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('getCitizenForProfile error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat data'], 500);
        }
    }

    /**
     * Dapatkan data terbaru untuk form edit (termasuk perubahan yang sudah diapprove)
     */
    public function getLatestDataForForm($nik)
    {
        try {
            $citizen = $this->citizenService->getCitizenByNIK((int) $nik);
            if (!$citizen || !isset($citizen['data'])) {
                return null;
            }

            // Sumber kebenaran adalah API. Jangan timpa dengan riwayat approved lokal,
            // karena bisa jadi approval sebelumnya gagal tersinkron ke API.
            // Kembalikan langsung data API agar form selalu konsisten dengan server.
            $data = $citizen['data'];
            return $data;
        } catch (\Exception $e) {
            Log::error('getLatestDataForForm error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Simpan request approval perubahan biodata dari halaman profil.
     */
    public function requestApprovalFromProfile(Request $request)
    {
        try {
            $user = auth()->guard('penduduk')->user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login.');
            }

            $validated = $request->validate([
                'nik' => 'required|digits:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|string|in:Laki-laki,Perempuan',
                'age' => 'nullable|numeric',
                'birth_place' => 'nullable|string|max:255',
                'birth_date' => 'nullable|date',
                'address' => 'nullable|string',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'province_id' => 'required|numeric',
                'district_id' => 'required|numeric',
                'sub_district_id' => 'required|numeric',
                'village_id' => 'required|numeric',
                'blood_type' => 'nullable|numeric',
                'education_status' => 'nullable|numeric',
                'job_type_id' => 'nullable|numeric',
            ]);

            $citizen = $this->citizenService->getCitizenByNIK((int) $validated['nik']);
            $villageId = $citizen['data']['village_id'] ?? $citizen['village_id'] ?? $citizen['data']['villages_id'] ?? $citizen['villages_id'] ?? null;
            $currentData = $citizen['data'] ?? $citizen ?? [];
            $kkNumber = $currentData['kk'] ?? ($user->citizen_data['kk'] ?? $user->no_kk ?? null);

            \App\Models\ProfileChangeRequest::create([
                'nik' => $validated['nik'],
                'village_id' => $villageId,
                'current_data' => $currentData,
                'requested_changes' => $validated,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Jika ada data Informasi Usaha di form, kirim juga change request nya
            $namaUsaha = trim((string) $request->input('nama_usaha', ''));
            $kelompokUsaha = trim((string) $request->input('kelompok_usaha', ''));
            $alamatUsaha = trim((string) $request->input('usaha_address', $request->input('alamat_usaha', '')));
            $tagLat = trim((string) $request->input('tag_lat', ''));
            $tagLng = trim((string) $request->input('tag_lng', ''));
            $hasFoto = $request->hasFile('foto_usaha');

            if ($namaUsaha || $kelompokUsaha || $alamatUsaha || ($tagLat && $tagLng) || $hasFoto) {
                $penduduk = $user instanceof Penduduk ? $user : Penduduk::where('nik', $validated['nik'])->first();
                // Cari usaha berdasarkan KK agar satu KK hanya satu Informasi Usaha
                $existingUsaha = null;
                if ($kkNumber) {
                    $existingUsaha = InformasiUsaha::where('kk', $kkNumber)->first();
                }
                if (!$existingUsaha && $penduduk) {
                    $existingUsaha = InformasiUsaha::where('penduduk_id', $penduduk->id)->first();
                }
                // Jika usaha sudah ada namun pengaju bukan pemiliknya, jangan buat change request
                if ($existingUsaha && $existingUsaha->penduduk_id && $penduduk && (int)$existingUsaha->penduduk_id !== (int)$penduduk->id) {
                    Log::info('Skip InformasiUsahaChangeRequest karena pengaju bukan pemilik usaha pada KK yang sama', [
                        'nik_pengaju' => $validated['nik'],
                        'penduduk_id_pengaju' => $penduduk->id ?? null,
                        'penduduk_id_pemilik' => $existingUsaha->penduduk_id,
                        'kk' => $kkNumber,
                    ]);
                } else {
                    $requested = [
                        'nama_usaha' => $namaUsaha ?: null,
                        'kelompok_usaha' => $kelompokUsaha ?: null,
                        'alamat' => $alamatUsaha ?: null,
                        'tag_lokasi' => ($tagLat && $tagLng) ? ($tagLat . ',' . $tagLng) : null,
                        // Gunakan nilai wilayah dari form biodata yang sedang diajukan (paling akurat), fallback ke user bila perlu
                        'province_id' => (int)($validated['province_id'] ?? ($user->province_id ?? 0)) ?: null,
                        'districts_id' => (int)($validated['district_id'] ?? ($user->district_id ?? ($user->districts_id ?? 0))) ?: null,
                        'sub_districts_id' => (int)($validated['sub_district_id'] ?? ($user->sub_district_id ?? ($user->sub_districts_id ?? 0))) ?: null,
                        'villages_id' => (int)($validated['village_id'] ?? ($user->village_id ?? ($user->villages_id ?? 0))) ?: null,
                    ];
                    if ($hasFoto) {
                        $path = $request->file('foto_usaha')->store('informasi_usaha_temp', 'public');
                        $requested['foto'] = $path;
                    }

                    InformasiUsahaChangeRequest::create([
                        'penduduk_id' => $penduduk?->id ?? 0,
                        'informasi_usaha_id' => $existingUsaha?->id,
                        'requested_changes' => $requested,
                        'current_data' => $existingUsaha ? $existingUsaha->toArray() : ['kk' => $kkNumber],
                        'status' => 'pending',
                    ]);
                }
            }

            return redirect()->route('user.profile.index')->with('success', 'Permintaan perubahan biodata dikirim. Menunggu persetujuan admin desa.');
        } catch (\Exception $e) {
            Log::error('requestApprovalFromProfile error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim permintaan: ' . $e->getMessage())->withInput();
        }
    }

    public function index(Request $request)
    {
        // Cek apakah ini request untuk export
        if ($request->has('export')) {
            return $this->export();
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        // Jika user adalah admin desa, ambil data warga berdasarkan village_id admin
        if (Auth::user()->role == 'admin desa') {
            $villageId = Auth::user()->villages_id;
            $citizens = $this->citizenServiceV2->getAllCitizensWithSearch($page, $limit, $search, $villageId);
            return view('admin.desa.biodata.index', compact('citizens', 'search'));
        } else {
            $citizens = $this->citizenServiceV2->getAllCitizensWithSearch($page, $limit, $search);
            return view('superadmin.biodata.index', compact('citizens', 'search'));
        }
    }

    public function create()
    {
        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Initialize empty arrays for district, sub-district, and village data
        $districts = [];
        $subDistricts = [];
        $villages = [];


        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.biodata.create', compact(
                'provinces',
                'jobs',
                'districts',
                'subDistricts',
                'villages'
            ));
        }

        return view('superadmin.biodata.create', compact(
            'provinces',
            'jobs',
            'districts',
        ));
    }

    public function store(Request $request)
    {
        try {
            // Check if this is a family member submission from the KK page
            $isFromKKPage = $request->header('Referer') && str_contains($request->header('Referer'), 'datakk/create');

            $validatedData = $request->validate([
                'nik' => 'required|size:16',
                'kk' => 'required|size:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|integer|in:1,2',
                'birth_date' => 'required|date',
                'age' => 'required|integer',
                'birth_place' => 'required|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'required|string|max:3',
                'rw' => 'required|string|max:3',
                'postal_code' => 'nullable|digits:5',
                'citizen_status' => 'required|integer|in:1,2',
                'birth_certificate' => 'integer|in:1,2',
                'birth_certificate_no' => 'nullable|string',
                'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                'religion' => 'required|in:1,2,3,4,5,6,7',
                'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                'marital_certificate' => 'required|in:1,2',
                'marital_certificate_no' => 'nullable|string',
                'marriage_date' => 'nullable|date',
                'divorce_certificate' => 'nullable|integer|in:1,2',
                'divorce_certificate_no' => 'nullable|string',
                'divorce_certificate_date' => 'nullable|date',
                'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                'mental_disorders' => 'required|integer|in:1,2',
                'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255',
                'coordinate' => 'nullable|string|max:255',
                // New fields
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                'rf_id_tag' => 'nullable|string',
            ]);

            // Batch process nullable fields
            $this->processNullableFields($validatedData);

            // Convert NIK and KK to integers
            $validatedData['nik'] = (int) $validatedData['nik'];
            $validatedData['kk'] = (int) $validatedData['kk'];
            $validatedData['religion'] = (int) $validatedData['religion'];

            $response = $this->citizenService->createCitizen($validatedData);

            if ($response['status'] === 'CREATED') {
                // If coming from KK page, redirect back there
                if ($isFromKKPage) {

                    if (Auth::user()->role == 'admin desa') {
                        return redirect()
                            ->route('admin.desa.datakk.create')
                            ->with('success', 'Anggota keluarga berhasil ditambahkan!');
                    }

                    return redirect()
                        ->route('superadmin.datakk.create')
                        ->with('success', 'Anggota keluarga berhasil ditambahkan!');
                }

                if (Auth::user()->role == 'admin desa') {
                    return redirect()
                        ->route('admin.desa.biodata.index')
                        ->with('success', $response['message']);
                }

                // Otherwise, go to the regular biodata index
                return redirect()
                    ->route('superadmin.biodata.index')
                    ->with('success', $response['message']);
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal menyimpan data');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($nik)
    {
        $citizen = $this->citizenService->getCitizenByNIK($nik);
        if (!$citizen || !isset($citizen['data'])) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        // Normalize select values to ensure they are numeric for the form
        $this->normalizeSelectValues($citizen['data']);

        // Format date fields to yyyy-MM-dd for HTML date inputs
        $this->formatDatesForView($citizen['data']);

        $provinces = $this->wilayahService->getProvinces();
        $jobs = $this->jobService->getAllJobs();

        // Get location data for pre-selecting dropdowns
        $districts = $this->wilayahService->getKabupaten($citizen['data']['province_id']);
        $subDistricts = $this->wilayahService->getKecamatan($citizen['data']['district_id']);
        $villages = $this->wilayahService->getDesa($citizen['data']['sub_district_id']);

        // Get all citizens data for RFID scanning
        $allCitizens = [];
        try {
            if (Auth::user()->role == 'admin desa') {
                $villageId = Auth::user()->villages_id;
                $response = $this->citizenServiceV2->getAllCitizensWithSearch(1, 10000, '', $villageId);
            } else {
                $response = $this->citizenServiceV2->getAllCitizensWithSearch(1, 10000, '');
            }

            if (isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
                $allCitizens = $response['data']['citizens'];
            } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
                $allCitizens = $response['citizens'];
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $allCitizens = $response['data'];
            }
        } catch (\Exception $e) {
            Log::error('Error loading citizens for RFID scanning: ' . $e->getMessage());
        }

        if (Auth::user()->role == 'admin desa') {
            return view('admin.desa.biodata.update', compact(
                'citizen',
                'provinces',
                'jobs',
                'districts',
                'subDistricts',
                'villages',
                'allCitizens'
            ));
        }

        return view('superadmin.biodata.update', compact(
            'citizen',
            'provinces',
            'jobs',
            'districts',
            'subDistricts',
            'villages',
            'allCitizens'
        ));
    }

    public function update(Request $request, $nik)
    {
        try {
            $page = $request->input('current_page', 1);

            $validatedData = $request->validate([
                'kk' => 'required|size:16',
                'full_name' => 'required|string|max:255',
                'gender' => 'required|integer|in:1,2',
                'birth_date' => 'required|date',
                'age' => 'required|integer',
                'birth_place' => 'required|string|max:255',
                'address' => 'required|string',
                'province_id' => 'required|integer',
                'district_id' => 'required|integer',
                'sub_district_id' => 'required|integer',
                'village_id' => 'required|integer',
                'rt' => 'required|string|max:3',
                'rw' => 'required|string|max:3',
                'postal_code' => 'nullable|digits:5',
                'citizen_status' => 'required|integer|in:1,2',
                'birth_certificate' => 'integer|in:1,2',
                'birth_certificate_no' => 'nullable|string',
                'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
                'religion' => 'required|integer|in:1,2,3,4,5,6,7',
                'marital_status' => 'nullable|integer|in:1,2,3,4,5,6',
                'marital_certificate' => 'required|in:1,2',
                'marital_certificate_no' => 'nullable|string',
                'marriage_date' => 'nullable|date',
                'divorce_certificate' => 'nullable|integer|in:1,2',
                'divorce_certificate_no' => 'nullable|string',
                'divorce_certificate_date' => 'nullable|date',
                'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
                'mental_disorders' => 'required|integer|in:1,2',
                'disabilities' => 'nullable|integer|in:0,1,2,3,4,5,6',
                'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
                'job_type_id' => 'required|integer',
                'nik_mother' => 'nullable|string|size:16',
                'mother' => 'nullable|string|max:255',
                'nik_father' => 'nullable|string|size:16',
                'father' => 'nullable|string|max:255',
                'coordinate' => 'nullable|string|max:255',
                // New fields
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'hamlet' => 'nullable|string|max:100',
                'foreign_address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'foreign_postal_code' => 'nullable|string|max:20',
                'status' => 'nullable|string|in:Active,Inactive,Deceased,Moved',
                'rf_id_tag' => 'nullable|string',
            ]);
            // dd($validatedData);

            // Process nullable fields
            $this->processNullableFields($validatedData);
            $nik = (int) $nik;

            // Convert KK to integer
            $validatedData['kk'] = (int) $validatedData['kk'];

            // Calculate age based on birth_date before saving
            if (isset($validatedData['birth_date']) && !empty($validatedData['birth_date'])) {
                $birthDate = new \DateTime($validatedData['birth_date']);
                $today = new \DateTime();
                $age = $today->diff($birthDate)->y;

                // If birthday hasn't occurred this year yet, subtract 1
                $currentYear = $today->format('Y');
                $birthYear = $birthDate->format('Y');
                $birthMonth = (int)$birthDate->format('m');
                $birthDay = (int)$birthDate->format('d');
                $currentMonth = (int)$today->format('m');
                $currentDay = (int)$today->format('d');

                if ($currentMonth < $birthMonth || ($currentMonth == $birthMonth && $currentDay < $birthDay)) {
                    $age--;
                }

                $validatedData['age'] = max(0, $age); // Ensure age is not negative
            }

            $response = $this->citizenService->updateCitizen($nik, $validatedData);

            if ($response['status'] === 'OK') {
                // Clear additional caches that might be used in guest forms
                $this->clearGuestFormCaches();

                if (Auth::user()->role == 'admin desa') {
                    return redirect()
                        ->route('admin.desa.biodata.index', ['page' => $page])
                        ->with('success', 'Biodata berhasil diperbarui!');
                }

                return redirect()
                    ->route('superadmin.biodata.index', ['page' => $page])
                    ->with('success', 'Biodata berhasil diperbarui!');
            }

            return back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Gagal memperbarui data');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id, Request $request)
    {
        $page = $request->query('page', 1);
        $response = $this->citizenService->deleteCitizen($id);

        if ($response['status'] === 'OK') {
            if (Auth::user()->role == 'admin desa') {
                return redirect()
                    ->route('admin.desa.biodata.index')
                    ->with('success', 'Biodata berhasil dihapus!');
            }

            return redirect()
                ->route('superadmin.biodata.index', ['page' => $page])
                ->with('success', 'Biodata berhasil dihapus!');
        }

        if (Auth::user()->role == 'admin desa') {
            return redirect()
                ->route('admin.desa.biodata.index')
                ->with('error', 'Gagal menghapus biodata: ' . $response['message']);
        }

        return redirect()
            ->route('superadmin.biodata.index', ['page' => $page])
            ->with('error', 'Gagal menghapus biodata: ' . $response['message']);
    }

    private function getJobs()
    {
        try {
            $response = Http::get('https://api.desaverse.id/jobs');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Jobs API request failed: ' . $response->status());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching jobs: ' . $e->getMessage());
            return [];
        }
    }

    public function getDistricts($provinceCode)
    {
        $districts = $this->wilayahService->getKabupaten($provinceCode);
        return response()->json($districts);
    }

    public function getSubDistricts($districtCode)
    {
        $subDistricts = $this->wilayahService->getKecamatan($districtCode);
        return response()->json($subDistricts);
    }

    public function getVillages($subDistrictCode)
    {
        $villages = $this->wilayahService->getDesa($subDistrictCode);
        return response()->json($villages);
    }

    private function formatDatesForView(&$data)
    {
        $dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];

        foreach ($dateFields as $field) {
            // More strict validation for empty/null values
            if (!isset($data[$field]) ||
                $data[$field] === null ||
                $data[$field] === '' ||
                $data[$field] === ' ' ||
                $data[$field] === 'null' ||
                trim($data[$field]) === '') {

                // Set to empty string for HTML date input
                $data[$field] = '';
                continue;
            }

            try {
                $value = trim($data[$field]);

                // Check if it's already in yyyy-MM-dd format and valid
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    // Validate the date is actually valid
                    $parts = explode('-', $value);
                    if (count($parts) === 3 &&
                        checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                        continue;
                    }
                }

                // Handle dd/MM/yyyy format
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                    $parts = explode('/', $value);
                    if (count($parts) === 3) {
                        $newDate = $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                        // Validate the converted date
                        $dateParts = explode('-', $newDate);
                        if (checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
                            $data[$field] = $newDate;
                            continue;
                        }
                    }
                }

                // Try standard date parsing as fallback
                $timestamp = strtotime($value);
                if ($timestamp !== false) {
                    $formatted = date('Y-m-d', $timestamp);
                    // Validate the parsed date
                    $dateParts = explode('-', $formatted);
                    if (checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
                        $data[$field] = $formatted;
                        continue;
                    }
                }

                // If we can't format it properly, set to empty
                Log::warning("Invalid date format for {$field}: '{$value}' - setting to empty");
                $data[$field] = '';

            } catch (\Exception $e) {
                Log::error('Error formatting date for ' . $field . ': ' . $e->getMessage());
                $data[$field] = '';
            }
        }
    }

    private function formatDates(array $data)
    {
        $dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];

        foreach ($dateFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }

        return $data;
    }

    public function getCitizenByNIK($nik)
    {
        // Ambil data citizen dari service
        $citizen = $this->citizenService->getCitizenByNIK($nik);

        // Jika data tidak ditemukan, return null
        if (!$citizen || !isset($citizen['data'])) {
            return null;
        }

        // Before returning the citizen data, ensure the select field values are numeric
        $this->normalizeSelectValues($citizen['data']);

        return $citizen;
    }

    private function normalizeSelectValues(&$data)
    {
        $genderMap = [
            'Laki-Laki' => 1,
            'Laki-laki' => 1,
            'Perempuan' => 2,
            'laki-laki' => 1,
            'laki-laki' => 1,
            'perempuan' => 2,
            'LAKI-LAKI' => 1,
            'PEREMPUAN' => 2
        ];
        $citizenStatusMap = ['WNI' => 2, 'WNA' => 1, 'wni' => 2, 'wna' => 1];
        $certificateMap = [
            'Ada' => 1,
            'Tidak Ada' => 2,
            'ada' => 1,
            'tidak ada' => 2,
            'ADA' => 1,
            'TIDAK ADA' => 2
        ];
        $bloodTypeMap = [
            'A' => 1,
            'B' => 2,
            'AB' => 3,
            'O' => 4,
            'A+' => 5,
            'A-' => 6,
            'B+' => 7,
            'B-' => 8,
            'AB+' => 9,
            'AB-' => 10,
            'O+' => 11,
            'O-' => 12,
            'Tidak Tahu' => 13,
            'a' => 1,
            'b' => 2,
            'ab' => 3,
            'o' => 4,
            'a+' => 5,
            'a-' => 6,
            'b+' => 7,
            'b-' => 8,
            'ab+' => 9,
            'ab-' => 10,
            'o+' => 11,
            'o-' => 12,
            'tidak tahu' => 13
        ];
        $religionMap = [
            'Islam' => 1,
            'Kristen' => 2,
            'Katolik' => 3,
            'Katholik' => 3,
            'Hindu' => 4,
            'Buddha' => 5,
            'Budha' => 5,
            'Kong Hu Cu' => 6,
            'Konghucu' => 6,
            'Lainnya' => 7,
            'islam' => 1,
            'kristen' => 2,
            'katolik' => 3,
            'katholik' => 3,
            'hindu' => 4,
            'buddha' => 5,
            'budha' => 5,
            'kong hu cu' => 6,
            'konghucu' => 6,
            'lainnya' => 7
        ];
        $maritalStatusMap = [
            'Belum Kawin' => 1,
            'Kawin Tercatat' => 2,
            'Kawin Belum Tercatat' => 3,
            'Cerai Hidup Tercatat' => 4,
            'Cerai Hidup Belum Tercatat' => 5,
            'Cerai Mati' => 6,
            'BELUM KAWIN' => 1,
            'KAWIN TERCATAT' => 2,
            'KAWIN BELUM TERCATAT' => 3,
            'CERAI HIDUP TERCATAT' => 4,
            'CERAI HIDUP BELUM TERCATAT' => 5,
            'CERAI MATI' => 6
        ];
        $familyStatusMap = [
            'ANAK' => 1,
            'Anak' => 1,
            'anak' => 1,
            'KEPALA KELUARGA' => 2,
            'Kepala Keluarga' => 2,
            'kepala keluarga' => 2,
            'ISTRI' => 3,
            'Istri' => 3,
            'istri' => 3,
            'ORANG TUA' => 4,
            'Orang Tua' => 4,
            'orang tua' => 4,
            'MERTUA' => 5,
            'Mertua' => 5,
            'mertua' => 5,
            'CUCU' => 6,
            'Cucu' => 6,
            'cucu' => 6,
            'FAMILI LAIN' => 7,
            'Famili Lain' => 7,
            'famili lain' => 7
        ];
        $disabilitiesMap = [
            'Fisik' => 1,
            'Netra/Buta' => 2,
            'Rungu/Wicara' => 3,
            'Mental/Jiwa' => 4,
            'Fisik dan Mental' => 5,
            'Lainnya' => 6
        ];
        $educationStatusMap = [
            'Tidak/Belum Sekolah' => 1,
            'Belum tamat SD/Sederajat' => 2,
            'Tamat SD' => 3,
            'Tamat SD/Sederajat' => 3,
            'SLTP/SMP/Sederajat' => 4,
            'SLTA/SMA/Sederajat' => 5,
            'Diploma I/II' => 6,
            'Akademi/Diploma III/ Sarjana Muda' => 7,
            'Diploma IV/ Strata I/ Strata II' => 8,
            'Strata III' => 9,
            'Lainnya' => 10,
            'tidak/belum sekolah' => 1,
            'belum tamat sd/sederajat' => 2,
            'tamat sd' => 3,
            'tamat sd/sederajat' => 3,
            'sltp/smp/sederajat' => 4,
            'slta/sma/sederajat' => 5,
            'diploma i/ii' => 6,
            'akademi/diploma iii/ sarjana muda' => 7,
            'diploma iv/ strata i/ strata ii' => 8,
            'strata iii' => 9,
            'lainnya' => 10
        ];

        $fieldsToNormalize = [
            'gender' => $genderMap,
            'citizen_status' => $citizenStatusMap,
            'birth_certificate' => $certificateMap,
            'blood_type' => $bloodTypeMap,
            'religion' => $religionMap,
            'marital_status' => $maritalStatusMap,
            'marital_certificate' => $certificateMap,
            'divorce_certificate' => $certificateMap,
            'family_status' => $familyStatusMap,
            'mental_disorders' => $certificateMap,
            'disabilities' => $disabilitiesMap,
            'education_status' => $educationStatusMap,
        ];

        foreach ($fieldsToNormalize as $field => $mapping) {
            if (isset($data[$field])) {
                $value = trim($data[$field]);

                // If it's already numeric, keep it as is
                if (is_numeric($value)) {
                    $data[$field] = (int)$value;
                    continue;
                }

                // Try to map string values to numeric
                if (array_key_exists($value, $mapping)) {
                    $data[$field] = $mapping[$value];
                    Log::info("Normalized {$field} from '{$value}' to {$data[$field]}");
                } else if (!empty($value)) {
                    Log::warning("Could not normalize {$field} value: '{$value}'");
                    // Set default values for unknown string values
                    switch ($field) {
                        case 'gender':
                            $data[$field] = 1; // Default to Laki-laki
                            break;
                        case 'citizen_status':
                            $data[$field] = 2; // Default to WNI
                            break;
                        case 'birth_certificate':
                        case 'marital_certificate':
                        case 'divorce_certificate':
                        case 'mental_disorders':
                            $data[$field] = 2; // Default to Tidak Ada
                            break;
                        case 'blood_type':
                            $data[$field] = 13; // Default to Tidak Tahu
                            break;
                        case 'religion':
                            $data[$field] = 1; // Default to Islam
                            break;
                        case 'marital_status':
                            $data[$field] = 1; // Default to Belum Kawin
                            break;
                        case 'family_status':
                            $data[$field] = 2; // Default to Kepala Keluarga
                            break;
                        case 'disabilities':
                            $data[$field] = null; // Default to null (Tidak Ada)
                            break;
                        case 'education_status':
                            $data[$field] = 1; // Default to Tidak/Belum Sekolah
                            break;
                    }
                } else {
                    // Handle empty values
                    switch ($field) {
                        case 'disabilities':
                            $data[$field] = null; // Empty disabilities = null
                            break;
                        default:
                            // Keep existing logic for other fields
                            break;
                    }
                }
            }
        }

        return $data;
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt,application/csv,text/csv,text/plain|max:10240',
            ]);

            // Get the API endpoint from config
            $apiUrl = config('services.kependudukan.url') . '/api/citizens/import/upload';
            $apiKey = config('services.kependudukan.key');

            // Prepare the file for upload
            $file = $request->file('csv_file');

            // Create a multipart form data request
            $response = Http::withHeaders([
                // 'Accept' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->attach('file', $file->get(), $file->getClientOriginalName())
            ->post($apiUrl);

            // Preserve current page and search parameters
            $redirectParams = [];
            if ($request->has('current_page')) {
                $redirectParams['page'] = $request->input('current_page');
            }
            if ($request->has('current_search')) {
                $redirectParams['search'] = $request->input('current_search');
            }

            if ($response->successful()) {
                $responseData = $response->json();

                // Check if there are any errors in the response
                if (isset($responseData['errors']) && count($responseData['errors']) > 0) {
                    $errorMessages = "<ul class='text-left'>";
                    foreach ($responseData['errors'] as $error) {
                        $errorMessages .= "<li>• {$error}</li>";
                    }
                    $errorMessages .= "</ul>";

                    $summaryMessage = "Import selesai dengan beberapa error:<br>";
                    $summaryMessage .= "• Total baris diproses: " . ($responseData['processed_rows'] ?? 0) . "<br>";
                    $summaryMessage .= "• Berhasil diimport: " . ($responseData['success_count'] ?? 0) . "<br>";
                    $summaryMessage .= "• Baris yang dilewati: " . ($responseData['skipped_rows'] ?? 0) . "<br>";
                    $summaryMessage .= "• Jumlah error: " . count($responseData['errors']) . "<br><br>";
                    $summaryMessage .= "Detail error:";

                    return redirect()->route('superadmin.biodata.index', $redirectParams)
                        ->with('import_errors', $summaryMessage . $errorMessages);
                }

                $successMessage = $responseData['message'] ?? "Data berhasil diimport";
                return redirect()->route('superadmin.biodata.index', $redirectParams)
                    ->with('success', $successMessage);
            } else {
                $errorMessage = 'Gagal import data';
                if ($response->json() && isset($response->json()['message'])) {
                    $errorMessage .= ': ' . $response->json()['message'];
                } else {
                    $errorMessage .= ' (HTTP ' . $response->status() . ')';
                }

                return redirect()->route('superadmin.biodata.index', $redirectParams)
                    ->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Import failed with exception: ' . $e->getMessage());

            // Preserve current page and search parameters
            $redirectParams = [];
            if ($request->has('current_page')) {
                $redirectParams['page'] = $request->input('current_page');
            }
            if ($request->has('current_search')) {
                $redirectParams['search'] = $request->input('current_search');
            }

            return redirect()->route('superadmin.biodata.index', $redirectParams)
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // Controller code (assuming this is in your CitizenController or similar)
public function export()
{
    try {
        // Set memory limit dan execution time untuk handle 100k penduduk
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 menit
        
        $exportData = [];
        
        // CATATAN: Header akan di-handle oleh CitizensExport::headings()
        // Jadi tidak perlu menambahkan header di sini

        // Ambil semua data citizen
        if (Auth::user()->role == 'admin desa') {
            $villageIdAdmin = Auth::user()->villages_id;
            $response = $this->citizenService->getAllCitizensWithHighLimit();
            $citizens = [];
            if (isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
                $citizens = $response['data']['citizens'];
            } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
                $citizens = $response['citizens'];
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $citizens = $response['data'];
            }

            // Filter hanya untuk desa admin
            $citizens = array_filter($citizens, function ($c) use ($villageIdAdmin) {
                return (isset($c['village_id']) && $c['village_id'] == $villageIdAdmin) ||
                    (isset($c['villages_id']) && $c['villages_id'] == $villageIdAdmin);
            });
        } else {
            // Superadmin: ambil semua data
            $response = $this->citizenService->getAllCitizensWithHighLimit();
            $citizens = [];
            if (isset($response['data']['citizens']) && is_array($response['data']['citizens'])) {
                $citizens = $response['data']['citizens'];
            } elseif (isset($response['citizens']) && is_array($response['citizens'])) {
                $citizens = $response['citizens'];
            } elseif (isset($response['data']) && is_array($response['data'])) {
                $citizens = $response['data'];
            }
        }

         // OPTIMASI: Collect unique village IDs untuk batch loading
        // District & Sub-District akan di-load on-demand dari village code (lebih reliable)
        $uniqueProvinceIds = [];
        $uniqueVillageIds = [];

        foreach ($citizens as $citizen) {
            if (!empty($citizen['province_id'])) {
                $uniqueProvinceIds[$citizen['province_id']] = true;
            }
            $villageId = $citizen['village_id'] ?? $citizen['villages_id'] ?? null;
            if ($villageId) {
                $uniqueVillageIds[$villageId] = true;
            }
        }

        // Pre-load semua data wilayah sekaligus
        $provinceCache = [];
        $districtCache = []; // Akan diisi on-demand dari village code
        $subDistrictCache = []; // Akan diisi on-demand dari village code
        $villageCache = [];

        // OPTIMASI: Load provinces (biasanya cuma beberapa, cepat)
        foreach (array_keys($uniqueProvinceIds) as $provinceId) {
            try {
                $provinceCache[$provinceId] = Cache::remember("province_{$provinceId}", 21600, function() use ($provinceId) {
                    return $this->wilayahService->getProvinceById($provinceId);
                });
            } catch (\Exception $e) {
                Log::warning("Failed to load province {$provinceId}: " . $e->getMessage());
                $provinceCache[$provinceId] = null;
            }
        }

        // OPTIMASI: Load villages dengan chunk (sudah ada caching internal di getVillageById)
        $villageChunks = array_chunk(array_keys($uniqueVillageIds), 50, true);
        foreach ($villageChunks as $chunk) {
            foreach ($chunk as $villageId) {
                try {
                    $villageCache[$villageId] = $this->wilayahService->getVillageById($villageId);
                } catch (\Exception $e) {
                    Log::warning("Failed to load village {$villageId}: " . $e->getMessage());
                    $villageCache[$villageId] = null;
                }
            }
        }

        // Log info untuk monitoring
        $totalCitizens = count($citizens);
        Log::info("Export started", [
            'total_citizens' => $totalCitizens,
            'unique_provinces' => count($uniqueProvinceIds),
            'unique_villages' => count($uniqueVillageIds),
            'note' => 'District & Sub-district loaded on-demand from village code'
        ]);

        // Sekarang loop export data dengan cache yang sudah diisi
        $processedCount = 0;
        foreach ($citizens as $citizen) {
            $processedCount++;
            // NIK & KK sebagai string supaya tidak hilang nol di depan
            $nik = !empty($citizen['nik']) ? strval($citizen['nik']) : '';
            $kk = !empty($citizen['kk']) ? strval($citizen['kk']) : '';

            $provinceId    = $citizen['province_id'] ?? null;
            $districtId    = $citizen['district_id'] ?? null;
            $subDistrictId = $citizen['sub_district_id'] ?? null;
            $villageId     = $citizen['village_id'] ?? ($citizen['villages_id'] ?? null);

            $noProp = $noKab = $noKec = $noKel = '';
            $namaProp = $namaKab = $namaKec = $namaKel = '';

            // Ambil data desa (village) berdasarkan village_id, lalu pecah code jadi NO_PROP, NO_KAB, NO_KEC, NO_KEL
            if ($villageId && isset($villageCache[$villageId])) {
                $villageData = $villageCache[$villageId];

                if ($villageData) {
                    // Handle kemungkinan struktur response berbeda
                    $villageCode = $villageData['code'] ?? ($villageData['data']['code'] ?? null);
                    $villageName = $villageData['name'] ?? ($villageData['data']['name'] ?? '');

                    if ($villageCode) {
                        $code = str_pad($villageCode, 10, '0', STR_PAD_LEFT);
                        $noProp = substr($code, 0, 2);
                        $noKab  = substr($code, 2, 2);
                        $noKec  = substr($code, 4, 2);
                        $noKel  = substr($code, 6, 4);
                        
                        // PERBAIKAN: Derive district_code dan sub_district_code dari village code
                        // Format: 1101012001 -> province: 11, district: 1101, sub_district: 110101
                        $provinceCodeFromVillage = substr($code, 0, 2);
                        $districtCodeFromVillage = substr($code, 0, 4); // 4 digit pertama
                        $subDistrictCodeFromVillage = substr($code, 0, 6); // 6 digit pertama
                        
                        // Get district name langsung dari API dengan district code
                        if (!isset($districtCache[$districtCodeFromVillage])) {
                            try {
                                $districts = $this->wilayahService->getKabupaten($provinceCodeFromVillage);
                                foreach ($districts as $dist) {
                                    if (isset($dist['code']) && $dist['code'] == $districtCodeFromVillage) {
                                        $districtCache[$districtCodeFromVillage] = $dist;
                                        break;
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning("Failed to get district by code {$districtCodeFromVillage}: " . $e->getMessage());
                            }
                        }
                        
                        // Get sub-district name langsung dari API dengan sub_district code
                        if (!isset($subDistrictCache[$subDistrictCodeFromVillage])) {
                            try {
                                $subDistricts = $this->wilayahService->getKecamatan($districtCodeFromVillage);
                                foreach ($subDistricts as $subDist) {
                                    if (isset($subDist['code']) && $subDist['code'] == $subDistrictCodeFromVillage) {
                                        $subDistrictCache[$subDistrictCodeFromVillage] = $subDist;
                                        break;
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning("Failed to get sub-district by code {$subDistrictCodeFromVillage}: " . $e->getMessage());
                            }
                        }
                        
                        // Set nama dari cache yang baru dibuat
                        if (isset($districtCache[$districtCodeFromVillage])) {
                            $namaKab = $districtCache[$districtCodeFromVillage]['name'] ?? '';
                        }
                        
                        if (isset($subDistrictCache[$subDistrictCodeFromVillage])) {
                            $namaKec = $subDistrictCache[$subDistrictCodeFromVillage]['name'] ?? '';
                        }
                    }

                    $namaKel = $villageName;
                }
            }

            // Nama provinsi (dari cache yang sudah di-load)
            if ($provinceId && isset($provinceCache[$provinceId])) {
                $provinceData = $provinceCache[$provinceId];
                if ($provinceData && is_array($provinceData)) {
                    $namaProp = $provinceData['name'] ?? ($provinceData['data']['name'] ?? '');
                }
            }

            $exportData[] = [
                // NIK, NO_KK, NAMA_LGKP
                $nik,
                $kk,
                $citizen['full_name'] ?? '',

                // JENIS_KELAMIN (kode apa adanya dari API)
                $citizen['gender'] ?? '',

                // TANGGAL_LAHIR (apa adanya)
                $citizen['birth_date'] ?? '',

                // UMUR
                $citizen['age'] ?? '',

                // TEMPAT_LAHIR
                $citizen['birth_place'] ?? '',

                // ALAMAT
                $citizen['address'] ?? '',

                // NO_RT, NO_RW
                $citizen['rt'] ?? '',
                $citizen['rw'] ?? '',

                // KODE_POS
                $citizen['postal_code'] ?? '',

                // NO_PROP, NAMA_PROP, NO_KAB, NAMA_KAB, NO_KEC, NAMA_KEC, NO_KEL, KELURAHAN
                $noProp,
                $namaProp,
                $noKab,
                $namaKab,
                $noKec,
                $namaKec,
                $noKel,
                $namaKel,

                // SHDK (status dalam keluarga)
                $citizen['family_status'] ?? '',

                // STATUS_KAWIN
                $citizen['marital_status'] ?? '',

                // PENDIDIKAN
                $citizen['education_status'] ?? '',

                // AGAMA
                $citizen['religion'] ?? '',

                // PEKERJAAN (gunakan job_type_id jika tidak ada nama pekerjaan lain)
                $citizen['job_type'] ?? ($citizen['job'] ?? ($citizen['job_type_id'] ?? '')),

                // GOLONGAN_DARAH
                $citizen['blood_type'] ?? '',

                // AKTA_LAHIR & NO_AKTA_LAHIR
                $citizen['birth_certificate'] ?? '',
                $citizen['birth_certificate_no'] ?? '',

                // AKTA_KAWIN & NO_AKTA_KAWIN
                $citizen['marital_certificate'] ?? '',
                $citizen['marital_certificate_no'] ?? '',

                // AKTA_CERAI & NO_AKTA_CERAI
                $citizen['divorce_certificate'] ?? '',
                $citizen['divorce_certificate_no'] ?? '',

                // NIK_AYAH, NAMA_AYAH, NIK_IBU, NAMA_IBU
                !empty($citizen['nik_father']) ? strval($citizen['nik_father']) : '',
                $citizen['father'] ?? '',
                !empty($citizen['nik_mother']) ? strval($citizen['nik_mother']) : '',
                $citizen['mother'] ?? '',
            ];
            
            // Free memory setiap 1000 records untuk handle 100k penduduk
            if ($processedCount % 1000 === 0) {
                gc_collect_cycles();
                Log::info("Export progress: {$processedCount}/{$totalCitizens} records processed");
            }
        }

        // Log completion
        Log::info("Export completed", [
            'total_exported' => count($exportData),
            'execution_time' => round(microtime(true) - LARAVEL_START, 2) . 's'
        ]);

        $filename = 'biodata_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CitizensExport($exportData), $filename);
    } catch (\Exception $e) {
        \Log::error('Error exporting data: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
    }
}

    public function downloadTemplate()
    {
        try {
            // Get the API endpoint from config
            $apiUrl = config('services.kependudukan.url') . '/api/citizens/import/template/download';
            $apiKey = config('services.kependudukan.key');

            // Make API request to download template
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->get($apiUrl);

            if ($response->successful()) {
                // Get the content type from the response
                $contentType = $response->header('Content-Type', 'text/csv');

                // Get filename from Content-Disposition header or create default
                $contentDisposition = $response->header('Content-Disposition');
                $filename = 'template_biodata_' . date('Ymd_His') . '.csv';

                if ($contentDisposition && preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                    $filename = $matches[1];
                }

                // Return the file content directly
                return Response::make($response->body(), 200, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                $errorMessage = 'Gagal mengunduh template';
                if ($response->json() && isset($response->json()['message'])) {
                    $errorMessage .= ': ' . $response->json()['message'];
                } else {
                    $errorMessage .= ' (HTTP ' . $response->status() . ')';
                }

                return redirect()->route('superadmin.biodata.index')
                    ->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Error downloading template from API: ' . $e->getMessage());
            return redirect()->route('superadmin.biodata.index')
                ->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }

    /**
     * Get template information from API
     */
    public function getTemplateInfo()
    {
        try {
            // Get the API endpoint from config
            $apiUrl = config('services.kependudukan.url') . '/api/citizens/import/template';
            $apiKey = config('services.kependudukan.key');

            // Make API request to get template info
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-API-Key' => $apiKey,
            ])->get($apiUrl);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil informasi template'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error getting template info from API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi template: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processNullableFields(&$data)
    {
        $nullableIntegerFields = ['marital_status', 'marital_certificate', 'divorce_certificate', 'postal_code'];
        foreach ($nullableIntegerFields as $field) {
            $data[$field] = empty($data[$field]) ? 0 : (int) $data[$field];
        }

        // Special handling for disabilities field
        if (isset($data['disabilities'])) {
            if (empty($data['disabilities']) || $data['disabilities'] == '0') {
                $data['disabilities'] = null;
            } else {
                $data['disabilities'] = (int) $data['disabilities'];
            }
        }

        $nullableStringFields = [
            'birth_certificate_no',
            'marital_certificate_no',
            'divorce_certificate_no',
            'nik_mother',
            'nik_father',
            'mother',
            'father',
            'coordinate',
            'telephone',
            'email',
            'hamlet',
            'foreign_address',
            'city',
            'state',
            'country',
            'foreign_postal_code',
            'status',
        ];
        foreach ($nullableStringFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : $data[$field];
        }

        $nullableDateFields = ['marriage_date', 'divorce_certificate_date'];
        foreach ($nullableDateFields as $field) {
            $data[$field] = empty($data[$field]) ? " " : date('Y-m-d', strtotime($data[$field]));
        }

        $integerFields = [
            'gender',
            'age',
            'province_id',
            'district_id',
            'sub_district_id',
            'village_id',
            'citizen_status',
            'birth_certificate',
            'blood_type',
            'religion',
            'family_status',
            'mental_disorders',
            'education_status',
            'job_type_id',
        ];
        foreach ($integerFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }

        $dateFields = ['birth_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }
    }

    /**
     * Clear caches used by guest forms
     */
    private function clearGuestFormCaches()
    {
        try {
            // Clear all cache keys that might be used by guest forms
            $cacheKeys = [
                'admin_citizens_all',
                'admin_citizens_all_village_*',
                'citizens_all_1_100',
                'citizens_all_1_1000',
                'citizens_all_1_10000',
                'citizens_all_1_100_village_*',
                'citizens_all_1_1000_village_*',
                'citizens_all_1_10000_village_*',
                'citizens_search_*',
                'family_members_kk_*'
            ];

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }

            // Also clear any cache that might contain citizen data
            Cache::forget('admin_citizens_all');
            Cache::forget('citizens_all_1_100');
            Cache::forget('citizens_all_1_1000');
            Cache::forget('citizens_all_1_10000');
        } catch (\Exception $e) {
            Log::error('Error clearing guest form caches: ' . $e->getMessage());
        }
    }
}
