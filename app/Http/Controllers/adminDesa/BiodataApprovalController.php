<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Models\InformasiUsahaChangeRequest;
use App\Models\InformasiUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Log;

class BiodataApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = ProfileChangeRequest::query()->where('village_id', $user->villages_id);
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.desa.biodata-approval.index', compact('requests'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        return view('admin.desa.biodata-approval.show', compact('requestModel'));
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

                    try {
                $citizenService = app(CitizenService::class);
                
                // Convert form data to API format by merging with existing data
                $apiData = $this->convertFormDataToApiFormat($requestModel->requested_changes ?? [], $requestModel);
                
                // Validate that we have at least some data to update
                if (empty($apiData)) {
                    Log::warning('No valid data to update', [
                        'nik' => $requestModel->nik,
                        'original_data' => $requestModel->requested_changes
                    ]);
                    return back()->with('error', 'Tidak ada data valid yang dapat diperbarui');
                }

            // Log the data being sent to API
            Log::info('Sending data to API for approval', [
                'nik' => $requestModel->nik,
                'api_data' => $apiData
            ]);
            
            $updateResult = $citizenService->updateCitizen($requestModel->nik, $apiData);

            if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
                Log::error('Failed to update citizen via API', [
                    'nik' => $requestModel->nik,
                    'data' => $apiData,
                    'result' => $updateResult
                ]);
                return back()->with('error', 'Gagal menerapkan perubahan ke data penduduk: ' . ($updateResult['message'] ?? 'Unknown error'));
            }

            $requestModel->status = 'approved';
            $requestModel->reviewed_at = now();
            $requestModel->reviewed_by = $user->id;
            $requestModel->reviewer_note = $request->input('reviewer_note');
            $requestModel->save();

            // Opsional: Approve juga Informasi Usaha dalam satu klik jika ada request pending
            try {
                $penduduk = \App\Models\Penduduk::where('nik', $requestModel->nik)->first();
                if ($penduduk) {
                    $usahaReq = \App\Models\InformasiUsahaChangeRequest::where('penduduk_id', $penduduk->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                    if ($usahaReq) {
                        $data = $usahaReq->requested_changes ?? [];
                        $informasiUsaha = $usahaReq->informasi_usaha_id
                            ? \App\Models\InformasiUsaha::find($usahaReq->informasi_usaha_id)
                            : new \App\Models\InformasiUsaha(['penduduk_id' => $penduduk->id]);

                        if (array_key_exists('nama_usaha', $data)) $informasiUsaha->nama_usaha = $data['nama_usaha'];
                        if (array_key_exists('kelompok_usaha', $data)) $informasiUsaha->kelompok_usaha = $data['kelompok_usaha'];
                        if (array_key_exists('alamat', $data)) $informasiUsaha->alamat = $data['alamat'];
                        if (array_key_exists('tag_lokasi', $data)) $informasiUsaha->tag_lokasi = $data['tag_lokasi'];
                        if (array_key_exists('province_id', $data) && $data['province_id']) $informasiUsaha->province_id = (int)$data['province_id'];
                        if (array_key_exists('districts_id', $data) && $data['districts_id']) $informasiUsaha->districts_id = (int)$data['districts_id'];
                        if (array_key_exists('sub_districts_id', $data) && $data['sub_districts_id']) $informasiUsaha->sub_districts_id = (int)$data['sub_districts_id'];
                        if (array_key_exists('villages_id', $data) && $data['villages_id']) $informasiUsaha->villages_id = (int)$data['villages_id'];

                        if (!empty($data['foto'])) {
                            $path = $data['foto'];
                            if (is_string($path) && str_contains($path, 'informasi_usaha_temp/')) {
                                $filename = basename($path);
                                $newPath = 'informasi_usaha/' . $filename;
                                try { \Storage::disk('public')->move($path, $newPath); $informasiUsaha->foto = $newPath; }
                                catch (\Exception $e) { $informasiUsaha->foto = $path; }
                            } else {
                                // Jika path bukan temp dan bukan URL, pastikan kita simpan relatif ke storage
                                if (!preg_match('#^https?://#', $path) && !str_starts_with($path, 'informasi_usaha/')) {
                                    $informasiUsaha->foto = 'informasi_usaha/' . ltrim($path, '/');
                                } else {
                                    $informasiUsaha->foto = $path;
                                }
                            }
                        }

                        // Pastikan simpan KK agar satu KK satu usaha
                        if (empty($informasiUsaha->kk)) {
                            // Ambil KK dari requested/current jika ada
                            $kk = $usahaReq->current_data['kk'] ?? $usahaReq->requested_changes['kk'] ?? null;
                            if (!$kk) {
                                try {
                                    $citizen = app(\App\Services\CitizenService::class)->getCitizenByNIK((int)($requestModel->nik ?? 0));
                                    $kk = $citizen['data']['kk'] ?? ($citizen['kk'] ?? null);
                                } catch (\Exception $e) { $kk = null; }
                            }
                            $informasiUsaha->kk = $kk;
                        }
                        $informasiUsaha->save();

                        $usahaReq->status = 'approved';
                        $usahaReq->reviewed_at = now();
                        $usahaReq->reviewer_id = $user->id;
                        $usahaReq->reviewer_note = $request->input('reviewer_note');
                        $usahaReq->save();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Auto-approve Informasi Usaha gagal: '.$e->getMessage());
                // tidak menggagalkan approval biodata
            }

            return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan disetujui dan data diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in approve method: ' . $e->getMessage(), [
                'nik' => $requestModel->nik ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal memproses approval: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

        $requestModel->status = 'rejected';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $user->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan ditolak');
    }

    // =====================
    // Informasi Usaha Approval (gabung di controller ini)
    // =====================
    public function usahaIndex(Request $request)
    {
        $status = $request->query('status');
        $query = InformasiUsahaChangeRequest::query();
        if ($status) {
            $query->where('status', $status);
        }
        $requests = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.desa.informasi-usaha-approval.index', ['items' => $requests]);
    }

    public function usahaShow($id)
    {
        $item = InformasiUsahaChangeRequest::findOrFail($id);
        return view('admin.desa.informasi-usaha-approval.show', compact('item'));
    }

    public function usahaApprove(Request $request, $id)
    {
        $item = InformasiUsahaChangeRequest::findOrFail($id);
        abort_unless($item->status === 'pending', 400, 'Status tidak valid');

        try {
            $data = $item->requested_changes ?? [];
            $informasiUsaha = $item->informasi_usaha_id
                ? InformasiUsaha::find($item->informasi_usaha_id)
                : new InformasiUsaha(['penduduk_id' => $item->penduduk_id]);

            if (array_key_exists('nama_usaha', $data)) $informasiUsaha->nama_usaha = $data['nama_usaha'];
            $informasiUsaha->kelompok_usaha = $data['kelompok_usaha'] ?? $informasiUsaha->kelompok_usaha;
            if (array_key_exists('alamat', $data)) $informasiUsaha->alamat = $data['alamat'];
            if (array_key_exists('tag_lokasi', $data)) $informasiUsaha->tag_lokasi = $data['tag_lokasi'];
            // Set data wilayah bila dikirim pada requested_changes
            if (array_key_exists('province_id', $data)) $informasiUsaha->province_id = $data['province_id'];
            if (array_key_exists('districts_id', $data)) $informasiUsaha->districts_id = $data['districts_id'];
            if (array_key_exists('sub_districts_id', $data)) $informasiUsaha->sub_districts_id = $data['sub_districts_id'];
            if (array_key_exists('villages_id', $data)) $informasiUsaha->villages_id = $data['villages_id'];
            if (!empty($data['foto'])) {
                // Jika file berada di folder temp, pindahkan ke folder permanen
                $path = $data['foto'];
                if (is_string($path) && str_contains($path, 'informasi_usaha_temp/')) {
                    $filename = basename($path);
                    $newPath = 'informasi_usaha/' . $filename;
                    try {
                        \Storage::disk('public')->move($path, $newPath);
                        $informasiUsaha->foto = $newPath;
                    } catch (\Exception $e) {
                        // Jika gagal memindahkan, tetap gunakan path lama
                        $informasiUsaha->foto = $path;
                    }
                } else {
                    $informasiUsaha->foto = $path;
                }
            }
            $informasiUsaha->save();

        	$item->status = 'approved';
        	$item->reviewed_at = now();
        	$item->reviewed_by = Auth::id();
        	$item->reviewer_note = $request->input('reviewer_note');
        	$item->save();

            return redirect()->route('admin.desa.informasi-usaha-approval.index')->with('success', 'Permintaan Informasi Usaha disetujui');
        } catch (\Exception $e) {
            Log::error('Approve Informasi Usaha gagal: '.$e->getMessage());
            return back()->with('error', 'Gagal memproses approval: '.$e->getMessage());
        }
    }

    public function usahaReject(Request $request, $id)
    {
        $item = InformasiUsahaChangeRequest::findOrFail($id);
        abort_unless($item->status === 'pending', 400, 'Status tidak valid');
        $item->status = 'rejected';
        $item->reviewed_at = now();
        $item->reviewed_by = Auth::id();
        $item->reviewer_note = $request->input('reviewer_note');
        $item->save();
        return redirect()->route('admin.desa.informasi-usaha-approval.index')->with('success', 'Permintaan Informasi Usaha ditolak');
    }

    /**
     * Convert form data to API format by merging with existing data
     * PRINCIPLE: Don't make up data - only use what exists and what user wants to change
     */
    private function convertFormDataToApiFormat($formData, $requestModel)
    {
        try {
            // Get existing citizen data from API (ensure we extract the data payload)
            $citizenService = app(CitizenService::class);
            $existing = $citizenService->getCitizenByNIK($requestModel->nik ?? '');
            $existingData = [];
            if (is_array($existing)) {
                $existingData = $existing['data'] ?? $existing;
            }

            if (empty($existingData) || !is_array($existingData)) {
                Log::error('Could not fetch existing citizen data from API', [
                    'nik' => $requestModel->nik ?? 'unknown'
                ]);
                return back()->with('error', 'Tidak dapat mengambil data penduduk yang sudah ada. Silakan coba lagi.');
            }

            Log::info('Fetched existing citizen data from API', [
                'nik' => $requestModel->nik,
                'existing_data' => $existingData
            ]);

            // Start with existing data - DON'T MAKE UP DATA
            $apiData = $existingData;

            // Only update fields that are actually changed in the form
            // Keep all other existing data exactly as it is
            if (isset($formData['full_name']) && !empty($formData['full_name'])) {
                $apiData['full_name'] = trim($formData['full_name']);
            }

            if (isset($formData['kk']) && !empty($formData['kk'])) {
                $apiData['kk'] = (int) $formData['kk'];
            }

            if (isset($formData['gender']) && !empty($formData['gender'])) {
                // Convert gender text to numeric format
                if (strtolower($formData['gender']) === 'laki-laki' || strtolower($formData['gender']) === 'laki-laki') {
                    $apiData['gender'] = 1;
                } elseif (strtolower($formData['gender']) === 'perempuan') {
                    $apiData['gender'] = 2;
                }
            }

            if (isset($formData['age']) && is_numeric($formData['age'])) {
                $apiData['age'] = (int) $formData['age'];
            }

            if (isset($formData['birth_place']) && !empty($formData['birth_place'])) {
                $apiData['birth_place'] = trim($formData['birth_place']);
            }

            if (isset($formData['birth_date']) && !empty($formData['birth_date'])) {
                // Ensure birth_date is in valid format
                try {
                    $date = new \DateTime($formData['birth_date']);
                    $apiData['birth_date'] = $date->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning('Invalid birth_date format from form, keeping existing data', [
                        'form_date' => $formData['birth_date'],
                        'existing_date' => $existingData['birth_date'] ?? 'not set'
                    ]);
                    // Keep existing birth_date if form data is invalid
                }
            }

            if (isset($formData['address']) && !empty($formData['address'])) {
                $apiData['address'] = trim($formData['address']);
            }

            if (isset($formData['rt']) && !empty($formData['rt'])) {
                $apiData['rt'] = trim($formData['rt']);
            }

            if (isset($formData['rw']) && !empty($formData['rw'])) {
                $apiData['rw'] = trim($formData['rw']);
            }

            // Only update location IDs if they are valid integers
            if (isset($formData['province_id']) && is_numeric($formData['province_id'])) {
                $apiData['province_id'] = (int) $formData['province_id'];
            }

            if (isset($formData['district_id']) && is_numeric($formData['district_id'])) {
                $apiData['district_id'] = (int) $formData['district_id'];
            }

            if (isset($formData['sub_district_id']) && is_numeric($formData['sub_district_id'])) {
                $apiData['sub_district_id'] = (int) $formData['sub_district_id'];
            }

            if (isset($formData['village_id']) && is_numeric($formData['village_id'])) {
                $apiData['village_id'] = (int) $formData['village_id'];
            }

            // Include other selectable fields if provided (numeric IDs)
            if (isset($formData['blood_type']) && is_numeric($formData['blood_type'])) {
                $apiData['blood_type'] = (int) $formData['blood_type'];
            }

            if (isset($formData['education_status']) && is_numeric($formData['education_status'])) {
                $apiData['education_status'] = (int) $formData['education_status'];
            }

            if (isset($formData['job_type_id']) && is_numeric($formData['job_type_id'])) {
                $apiData['job_type_id'] = (int) $formData['job_type_id'];
            }

            // CRITICAL: Don't add any default values or make up data
            // The API should have all the required data already
            // We only send what exists + what user wants to change

            Log::info('Final API data (existing + changes only)', [
                'nik' => $requestModel->nik,
                'form_data' => $formData,
                'existing_data' => $existingData,
                'final_api_data' => $apiData,
                'message' => 'No data made up - only existing data + user changes'
            ]);

            return $apiData;

        } catch (\Exception $e) {
            Log::error('Error in convertFormDataToApiFormat', [
                'nik' => $requestModel->nik ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't fallback to default values - that would be making up data
            throw new \Exception('Gagal memproses data: ' . $e->getMessage());
        }
    }


}


