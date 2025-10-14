<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CitizenService;
use App\Services\WilayahService;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminBiodataController extends Controller
{
    protected $citizenService;
    protected $wilayahService;
    protected $jobService;

    public function __construct(CitizenService $citizenService, WilayahService $wilayahService, JobService $jobService)
    {
        $this->citizenService = $citizenService;
        $this->wilayahService = $wilayahService;
        $this->jobService = $jobService;
    }

    private function ensureAdminDesa(Request $request)
    {
        $role = $request->attributes->get('token_owner_role');
        if ($role !== 'admin desa') {
            abort(response()->json([
                'status' => 'ERROR',
                'message' => 'Forbidden: hanya admin desa'
            ], 403));
        }
    }

    public function show(Request $request, $nik)
    {
        $this->ensureAdminDesa($request);

        try {
            $citizen = $this->citizenService->getCitizenByNIK((int) $nik);
            if (!$citizen || !isset($citizen['data'])) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'OK',
                'data' => $citizen['data']
            ]);
        } catch (\Exception $e) {
            Log::error('AdminBiodata show error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function update(Request $request, $nik)
    {
        $this->ensureAdminDesa($request);

        $validated = $request->validate([
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
            'blood_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
            'citizen_status' => 'required|integer|in:1,2',
            'family_status' => 'required|integer|in:1,2,3,4,5,6,7',
            'mental_disorders' => 'required|integer|in:1,2',
            'education_status' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
            'job_type_id' => 'required|integer',
            'rf_id_tag' => 'nullable|integer',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email'
        ]);

        try {
            $result = $this->citizenService->updateCitizen($nik, $validated);
            if (!$result || !isset($result['status']) || $result['status'] === 'ERROR') {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => $result['message'] ?? 'Gagal memperbarui biodata'
                ], 422);
            }

            return response()->json([
                'status' => 'OK',
                'message' => $result['message'] ?? 'Biodata berhasil diperbarui',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('AdminBiodata update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    // Dropdown endpoints (Admin Desa)
    public function provinces(Request $request)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getProvinces();
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function districts(Request $request, $provinceCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getKabupaten($provinceCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function subDistricts(Request $request, $districtCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getKecamatan($districtCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function villages(Request $request, $subDistrictCode)
    {
        $this->ensureAdminDesa($request);
        $data = $this->wilayahService->getDesa($subDistrictCode);
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function jobs(Request $request)
    {
        $this->ensureAdminDesa($request);
        $data = $this->jobService->getAllJobs();
        return response()->json(['status' => 'OK', 'data' => $data]);
    }

    public function enums(Request $request)
    {
        $this->ensureAdminDesa($request);

        $data = [
            'gender' => [
                ['value' => 1, 'label' => 'Laki-Laki'],
                ['value' => 2, 'label' => 'Perempuan'],
            ],
            'religion' => [
                ['value' => 1, 'label' => 'Islam'],
                ['value' => 2, 'label' => 'Kristen'],
                ['value' => 3, 'label' => 'Katholik'],
                ['value' => 4, 'label' => 'Hindu'],
                ['value' => 5, 'label' => 'Buddha'],
                ['value' => 6, 'label' => 'Kong Hu Cu'],
                ['value' => 7, 'label' => 'Lainnya'],
            ],
            'birth_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'marital_status' => [
                ['value' => 1, 'label' => 'Belum Kawin'],
                ['value' => 2, 'label' => 'Kawin Tercatat'],
                ['value' => 3, 'label' => 'Kawin Belum Tercatat'],
                ['value' => 4, 'label' => 'Cerai Hidup Tercatat'],
                ['value' => 5, 'label' => 'Cerai Hidup Belum Tercatat'],
                ['value' => 6, 'label' => 'Cerai Mati'],
            ],
            'marital_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'divorce_certificate' => [
                ['value' => 1, 'label' => 'Ada'],
                ['value' => 2, 'label' => 'Tidak Ada'],
            ],
            'blood_type' => [
                ['value' => 1, 'label' => 'A'],
                ['value' => 2, 'label' => 'B'],
                ['value' => 3, 'label' => 'AB'],
                ['value' => 4, 'label' => 'O'],
                ['value' => 5, 'label' => 'A+'],
                ['value' => 6, 'label' => 'A-'],
                ['value' => 7, 'label' => 'B+'],
                ['value' => 8, 'label' => 'B-'],
                ['value' => 9, 'label' => 'AB+'],
                ['value' => 10, 'label' => 'AB-'],
                ['value' => 11, 'label' => 'O+'],
                ['value' => 12, 'label' => 'O-'],
                ['value' => 13, 'label' => 'Tidak Tahu'],
            ],
            'citizen_status' => [
                ['value' => 1, 'label' => 'WNA'],
                ['value' => 2, 'label' => 'WNI'],
            ],
            'family_status' => [
                ['value' => 1, 'label' => 'Anak'],
                ['value' => 2, 'label' => 'Kepala Keluarga'],
                ['value' => 3, 'label' => 'Istri'],
                ['value' => 4, 'label' => 'Orang Tua'],
                ['value' => 5, 'label' => 'Mertua'],
                ['value' => 6, 'label' => 'Cucu'],
                ['value' => 7, 'label' => 'Famili Lain'],
            ],
            'mental_disorders' => [
                ['value' => 1, 'label' => 'Ya'],
                ['value' => 2, 'label' => 'Tidak'],
            ],
            'disabilities' => [
                ['value' => 0, 'label' => 'Tidak Ada'],
                ['value' => 1, 'label' => 'Fisik'],
                ['value' => 2, 'label' => 'Netra/Buta'],
                ['value' => 3, 'label' => 'Rungu/Wicara'],
                ['value' => 4, 'label' => 'Mental/Jiwa'],
                ['value' => 5, 'label' => 'Fisik dan Mental'],
                ['value' => 6, 'label' => 'Lainnya'],
            ],
            'education_status' => [
                ['value' => 1, 'label' => 'Tidak/Belum Sekolah'],
                ['value' => 2, 'label' => 'Belum tamat SD/Sederajat'],
                ['value' => 3, 'label' => 'Tamat SD/Sederajat'],
                ['value' => 4, 'label' => 'SLTP/SMP/Sederajat'],
                ['value' => 5, 'label' => 'SLTA/SMA/Sederajat'],
                ['value' => 6, 'label' => 'Diploma I/II'],
                ['value' => 7, 'label' => 'Akademi/Diploma III/Sarjana Muda'],
                ['value' => 8, 'label' => 'Diploma IV/Strata I/Strata II'],
                ['value' => 9, 'label' => 'Strata III'],
                ['value' => 10, 'label' => 'Lainnya'],
            ],
        ];

        return response()->json(['status' => 'OK', 'data' => $data]);
    }
}


