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

        {{-- Informasi Usaha terkait (jika ada pengajuan) --}}
        @php
            $usahaRequest = null;
            try {
                $penduduk = \App\Models\Penduduk::where('nik', $requestModel->nik)->first();
                if ($penduduk) {
                    $usahaRequest = \App\Models\InformasiUsahaChangeRequest::where('penduduk_id', $penduduk->id)
                        ->orderByDesc('created_at')
                        ->first();
                }
            } catch (\Exception $e) { $usahaRequest = null; }
        @endphp

        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-3">Informasi Usaha (Terkait Penduduk)</h2>
            @if($usahaRequest)
                <div class="text-sm text-gray-800 mb-3">
                    <span class="mr-2"><strong>Status:</strong>
                        @if($usahaRequest->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu Review</span>
                        @elseif($usahaRequest->status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                        @endif
                    </span>
                    <span><strong>Tanggal:</strong> {{ $usahaRequest->created_at->format('d M Y H:i') }}</span>
                </div>

                @php
                    $uCur = $usahaRequest->current_data ?? [];
                    $uReq = $usahaRequest->requested_changes ?? [];
                    $curLat = $curLng = $newLat = $newLng = '';
                    if (!empty($uCur['tag_lokasi'])) { $p = explode(',', $uCur['tag_lokasi']); if (count($p)>=2) { $curLat = trim($p[0]); $curLng = trim($p[1]); }}
                    if (!empty($uReq['tag_lokasi'])) { $p = explode(',', $uReq['tag_lokasi']); if (count($p)>=2) { $newLat = trim($p[0]); $newLng = trim($p[1]); }}
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium mb-2">Data Saat Ini</h3>
                        <div class="text-sm text-gray-700 space-y-1">
                            <div><strong>Nama Usaha:</strong> {{ $uCur['nama_usaha'] ?? '-' }}</div>
                            <div><strong>Kelompok Usaha:</strong> {{ $uCur['kelompok_usaha'] ?? '-' }}</div>
                            <div><strong>Alamat:</strong> {{ $uCur['alamat'] ?? '-' }}</div>
                            <div><strong>Tag Lokasi:</strong> {{ $uCur['tag_lokasi'] ?? '-' }}
                                @if($curLat && $curLng)
                                    <a href="https://www.openstreetmap.org/?mlat={{ $curLat }}&mlon={{ $curLng }}#map=17/{{ $curLat }}/{{ $curLng }}" target="_blank" class="text-blue-600 underline ml-1">Lihat Peta</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium mb-2">Perubahan Diminta</h3>
                        <div class="text-sm text-gray-700 space-y-1">
                            <div><strong>Nama Usaha:</strong> {{ $uReq['nama_usaha'] ?? '-' }}</div>
                            <div><strong>Kelompok Usaha:</strong> {{ $uReq['kelompok_usaha'] ?? '-' }}</div>
                            <div><strong>Alamat:</strong> {{ $uReq['alamat'] ?? '-' }}</div>
                            <div><strong>Tag Lokasi:</strong> {{ $uReq['tag_lokasi'] ?? '-' }}
                                @if($newLat && $newLng)
                                    <a href="https://www.openstreetmap.org/?mlat={{ $newLat }}&mlon={{ $newLng }}#map=17/{{ $newLat }}/{{ $newLng }}" target="_blank" class="text-blue-600 underline ml-1">Lihat Peta</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @php 
                    $curFoto = $uCur['foto'] ?? null;
                    $newFoto = $uReq['foto'] ?? null;

                    $curFotoUrl = null;
                    if ($curFoto) {
                        if (preg_match('#^https?://#', $curFoto) || str_starts_with($curFoto, 'data:')) {
                            $curFotoUrl = $curFoto;
                        } else {
                            $curFotoUrl = asset('storage/' . ltrim($curFoto, '/'));
                        }
                    }

                    $newFotoUrl = null;
                    if ($newFoto) {
                        if (preg_match('#^https?://#', $newFoto) || str_starts_with($newFoto, 'data:')) {
                            $newFotoUrl = $newFoto;
                        } else {
                            $newFotoUrl = asset('storage/' . ltrim($newFoto, '/'));
                        }
                    }
                @endphp

                @if($newFotoUrl || $curFotoUrl)
                    <div class="mt-4">
                        <h3 class="font-medium mb-2">Foto Tempat Usaha</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($curFotoUrl)
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">Foto Saat Ini</div>
                                    <img src="{{ $curFotoUrl }}" class="h-40 rounded border" />
                                </div>
                            @endif
                            @if($newFotoUrl)
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">Foto Baru Diajukan</div>
                                    <img src="{{ $newFotoUrl }}" class="h-40 rounded border" />
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="text-sm text-gray-500">Belum ada pengajuan Informasi Usaha terkait penduduk ini.</div>
            @endif
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


