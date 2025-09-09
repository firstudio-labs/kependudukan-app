<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Permintaan Perubahan</h1>

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Informasi Umum -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h2 class="font-semibold text-gray-700 mb-3">Informasi Umum</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-600 mb-2"><strong>NIK:</strong> {{ $requestModel->nik }}</div>
                    <div class="text-gray-600 mb-2"><strong>Nama Penduduk:</strong> {{ $requestModel->requested_changes['full_name'] ?? ($requestModel->current_data['full_name'] ?? 'Tidak tersedia') }}</div>
                    <div class="text-gray-600 mb-2"><strong>Desa:</strong> 
                        @php
                            $villageName = 'Tidak tersedia';
                            $adminVillageId = auth()->user()->villages_id;
                            
                            if ($adminVillageId) {
                                // Coba dari current_data dulu (data penduduk)
                                $currentData = $requestModel->current_data ?? [];
                                
                                // Cek berbagai kemungkinan field desa
                                if (isset($currentData['desa']) && !empty($currentData['desa'])) {
                                    $villageName = $currentData['desa'];
                                } elseif (isset($currentData['village_name']) && !empty($currentData['village_name'])) {
                                    $villageName = $currentData['village_name'];
                                } elseif (isset($currentData['villages_name']) && !empty($currentData['villages_name'])) {
                                    $villageName = $currentData['villages_name'];
                                } elseif (isset($currentData['village']) && !empty($currentData['village'])) {
                                    $villageName = $currentData['village'];
                                } else {
                                    // Coba dari WilayahService
                                    try {
                                        $villageData = app(\App\Services\WilayahService::class)->getVillageById($adminVillageId);
                                        
                                        if ($villageData && isset($villageData['name'])) {
                                            $villageName = $villageData['name'];
                                        } elseif ($villageData && isset($villageData['data']['name'])) {
                                            $villageName = $villageData['data']['name'];
                                        } else {
                                            // Fallback: coba cari dari data penduduk berdasarkan village_id
                                            if (isset($currentData['village_id']) && $currentData['village_id'] == $adminVillageId) {
                                                // Jika village_id sama, coba ambil dari field lain
                                                if (isset($currentData['address'])) {
                                                    // Parse address untuk mendapatkan nama desa (dinamis)
                                                    $address = $currentData['address'];
                                                    $addressParts = explode(',', $address);
                                                    
                                                    // Ambil bagian pertama dari address (biasanya nama desa/kelurahan)
                                                    if (!empty($addressParts[0])) {
                                                        $villageName = trim($addressParts[0]);
                                                    } else {
                                                        $villageName = 'Desa ID: ' . $adminVillageId;
                                                    }
                                                } else {
                                                    $villageName = 'Desa ID: ' . $adminVillageId;
                                                }
                                            } else {
                                                $villageName = 'Desa ID: ' . $adminVillageId;
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Jika service error, gunakan fallback dari address
                                        if (isset($currentData['address'])) {
                                            $address = $currentData['address'];
                                            $addressParts = explode(',', $address);
                                            
                                            // Ambil bagian pertama dari address (biasanya nama desa/kelurahan)
                                            if (!empty($addressParts[0])) {
                                                $villageName = trim($addressParts[0]);
                                            } else {
                                                $villageName = 'Desa ID: ' . $adminVillageId . ' (Error: ' . $e->getMessage() . ')';
                                            }
                                        } else {
                                            $villageName = 'Desa ID: ' . $adminVillageId . ' (Error: ' . $e->getMessage() . ')';
                                        }
                                    }
                                }
                            }
                        @endphp
                        {{ $villageName }}
                    </div>
                </div>
                <div>
                    <div class="text-gray-600 mb-2"><strong>Status:</strong> 
                        @if($requestModel->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu Review</span>
                        @elseif($requestModel->status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                        @elseif($requestModel->status === 'rejected')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                        @endif
                    </div>
                    <div class="text-gray-600 mb-2"><strong>Tanggal Request:</strong> {{ $requestModel->requested_at ? $requestModel->requested_at->format('d M Y H:i') : '-' }}</div>
                    @if($requestModel->reviewed_at)
                        <div class="text-gray-600 mb-2"><strong>Tanggal Review:</strong> {{ $requestModel->reviewed_at->format('d M Y H:i') }}</div>
                        <div class="text-gray-600 mb-2"><strong>Reviewer:</strong> 
                            @php
                                $reviewerName = 'Admin';
                                $reviewedBy = $requestModel->reviewed_by;
                                if ($reviewedBy) {
                                    $reviewer = \App\Models\User::find($reviewedBy);
                                    if ($reviewer) {
                                        // Coba berbagai field yang mungkin ada
                                        $reviewerName = $reviewer->name ?? $reviewer->nama ?? $reviewer->username ?? 'User ID: ' . $reviewedBy;
                                    } else {
                                        $reviewerName = 'User ID: ' . $reviewedBy . ' (tidak ditemukan)';
                                    }
                                }
                            @endphp
                            {{ $reviewerName }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @php
            $changes = $requestModel->requested_changes ?? [];
            $current = $requestModel->current_data ?? [];

            // Mapping label untuk tampilkan nama field yang ramah
            $fieldLabels = [
                'full_name' => 'Nama Lengkap',
                'gender' => 'Jenis Kelamin',
                'age' => 'Umur',
                'birth_place' => 'Tempat Lahir',
                'birth_date' => 'Tanggal Lahir',
                'address' => 'Alamat',
                'rt' => 'RT',
                'rw' => 'RW',
                'province_id' => 'Provinsi',
                'district_id' => 'Kabupaten',
                'sub_district_id' => 'Kecamatan',
                'village_id' => 'Desa',
                'blood_type' => 'Golongan Darah',
                'education_status' => 'Pendidikan Terakhir',
                'job_type_id' => 'Pekerjaan',
            ];

            // Mapper nilai numerik ke label untuk beberapa field select
            $bloodTypes = [1=>'A',2=>'B',3=>'AB',4=>'O',5=>'A+',6=>'A-',7=>'B+',8=>'B-',9=>'AB+',10=>'AB-',11=>'O+',12=>'O-',13=>'Tidak Tahu'];
            $educationStatuses = [1=>'Tidak/Belum Sekolah',2=>'Belum tamat SD/Sederajat',3=>'Tamat SD',4=>'SLTP/SMP/Sederajat',5=>'SLTA/SMA/Sederajat',6=>'Diploma I/II',7=>'Akademi/Diploma III/ Sarjana Muda',8=>'Diploma IV/ Strata I/ Strata II',9=>'Strata III',10=>'Lainnya'];

            $valueFormatter = function($key, $val) use ($bloodTypes, $educationStatuses) {
                if ($val === null || $val === '') return '';
                switch ($key) {
                    case 'gender':
                        return in_array(strtolower((string)$val), ['2','perempuan']) ? 'Perempuan' : 'Laki-laki';
                    case 'blood_type':
                        return $bloodTypes[(int)$val] ?? $val;
                    case 'education_status':
                        return $educationStatuses[(int)$val] ?? $val;
                    case 'job_type_id':
                        try {
                            $job = app(\App\Services\JobService::class)->getJobById((int)$val);
                            return $job['name'] ?? $val;
                        } catch (\Exception $e) { return $val; }
                    default:
                        return $val;
                }
            };

            // Hitung list perubahan saja (key yang ada di requested_changes)
            $changedKeys = array_keys($changes);

            // Urutan field yang diinginkan (mulai dari Nama Lengkap dan seterusnya)
            $fieldOrder = [
                'full_name',
                'gender',
                'age',
                'birth_place',
                'birth_date',
                'address',
                'rt',
                'rw',
                'province_id',
                'district_id',
                'sub_district_id',
                'village_id',
                'blood_type',
                'education_status',
                'job_type_id',
            ];

            // Susun key yang berubah mengikuti urutan yang diinginkan
            $orderedKeys = [];
            foreach ($fieldOrder as $key) {
                if (in_array($key, $changedKeys, true)) {
                    $orderedKeys[] = $key;
                }
            }
            // Tambahkan key lain yang tidak ada dalam fieldOrder di bagian akhir (tetap terlihat)
            foreach ($changedKeys as $key) {
                if (!in_array($key, $orderedKeys, true)) {
                    $orderedKeys[] = $key;
                }
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Data Saat Ini</h2>
                <div class="text-sm text-gray-800 space-y-2">
                    @forelse($orderedKeys as $key)
                        @php
                            $label = $fieldLabels[$key] ?? ucfirst(str_replace('_',' ',$key));
                            $currentVal = $valueFormatter($key, $current[$key] ?? ($current['data'][$key] ?? ''));
                        @endphp
                        <div>
                            <strong>{{ $label }}:</strong> {{ $currentVal !== '' ? $currentVal : '-' }}
                        </div>
                    @empty
                        <div class="text-gray-500">Tidak ada data untuk ditampilkan</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Perubahan Diminta</h2>
                <div class="text-sm text-gray-800 space-y-2">
                    @forelse($orderedKeys as $key)
                        @php
                            $label = $fieldLabels[$key] ?? ucfirst(str_replace('_',' ',$key));
                            $newVal = $valueFormatter($key, $changes[$key] ?? '');
                        @endphp
                        <div>
                            <strong>{{ $label }}:</strong> {{ $newVal !== '' ? $newVal : '-' }}
                        </div>
                    @empty
                        <div class="text-gray-500">Tidak ada perubahan</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-3">Aksi Review</h2>

            @if($requestModel->status === 'pending')
                <form id="approvalForm" class="space-y-4" method="POST">
                    @csrf
                    <div>
                        <label for="reviewer_note" class="block text-sm font-medium text-gray-700">Catatan untuk Penduduk</label>
                        <textarea id="reviewer_note" name="reviewer_note" rows="3" class="mt-1 p-2 w-full border rounded" placeholder="Berikan catatan atau alasan approval/rejection..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Catatan ini akan dikirim ke penduduk untuk informasi lebih lanjut</p>
                    </div>
                    <div class="flex gap-3">
                        <button formaction="{{ route('admin.desa.biodata-approval.approve', $requestModel->id) }}" formmethod="POST" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            Setujui
                        </button>
                        <button formaction="{{ route('admin.desa.biodata-approval.reject', $requestModel->id) }}" formmethod="POST" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Tolak
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-700 mb-2">Catatan Reviewer</h3>
                    @if($requestModel->reviewer_note)
                        <div class="text-sm text-gray-800 bg-white p-3 rounded border">
                            {{ $requestModel->reviewer_note }}
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fa-solid fa-info-circle"></i>
                            Catatan ini telah dikirim ke penduduk
                        </p>
                    @else
                        <div class="text-sm text-gray-500 italic">
                            Tidak ada catatan dari reviewer
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</x-layout>


