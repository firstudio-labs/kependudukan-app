<x-layout>
    <div class="p-4 mt-14">
        <di class="border-gray-200 rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Profil Penduduk</h1>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @php
if (Auth::guard('web')->check()) {
    $userData = Auth::guard('web')->user();
    $userType = 'web';
} elseif (Auth::guard('penduduk')->check()) {
    $userData = Auth::guard('penduduk')->user();
    $userType = 'penduduk';
} else {
    $userData = null;
    $userType = null;
}
            @endphp

            <!-- Tabel Informasi Pribadi -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Data Pribadi</h2>
                        <p class="text-gray-500">Informasi personal penduduk</p>
                    </div>
                    <div>
                        <button type="button" onclick="toggleEditForm()" id="btnToggleEdit"
                            class="inline-flex items-center gap-2 bg-[#4A47DC] hover:bg-[#2D336B] text-white px-4 py-2 rounded-lg shadow-sm transition-all">
                            <i class="fa-solid fa-user-pen"></i>
                            <span id="btnToggleEditText">Edit Biodata (Minta Approval)</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $userData->nik ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">No. HP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $userData->no_hp ?? 'Belum diisi' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">No. KK</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($userData->no_kk)
                                        {{ $userData->no_kk }}
                                    @elseif(isset($userData->citizen_data['kk']))
                                        {{ $userData->citizen_data['kk'] }}
                                    @else
                                        Belum diisi
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if (isset($userData->citizen_data['full_name']))
                                        {{ $userData->citizen_data['full_name'] }}
                                    @elseif(isset($userData->citizen_data['nama']))
                                        {{ $userData->citizen_data['nama'] }}
                                    @else
                                        {{ $userData->nama_lengkap ?? '-' }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Nama Kepala Keluarga</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($userData->keluarga && $userData->keluarga->kepalaKeluarga)
                                        {{ $userData->keluarga->kepalaKeluarga->nama_lengkap }}
                                    @elseif(isset($userData->citizen_data['family_status']) && $userData->citizen_data['family_status'] == 'KEPALA KELUARGA')
                                        {{ $userData->citizen_data['full_name'] ?? ($userData->citizen_data['nama_kepala_keluarga'] ?? 'Belum diisi') }}
                                    @elseif(isset($userData->family_members))
                                        @php
    $kepalaKeluarga = collect($userData->family_members)->firstWhere(
        'family_status',
        'KEPALA KELUARGA',
    );
                                        @endphp
                                        {{ $kepalaKeluarga['full_name'] ?? ($userData->citizen_data['nama_kepala_keluarga'] ?? 'Belum diisi') }}
                                    @else
                                        {{ $userData->citizen_data['nama_kepala_keluarga'] ?? 'Belum diisi' }}
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Terdaftar Sejak</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $userData->created_at ? $userData->created_at->format('d M Y') : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Form Edit Biodata (Request Approval) -->
            <div id="editBiodataForm" class="bg-white p-6 rounded-lg shadow-md mb-6 hidden">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Edit Biodata</h2>
                <form method="POST" action="{{ route('user.profile.request-approval') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="nik" value="{{ $userData->nik }}" />
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700">NIK</label>
                        <input value="{{ $userData->nik }}" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Nama Lengkap</label>
                        <input name="full_name" value="{{ $userData->citizen_data['full_name'] ?? ($userData->citizen_data['nama'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Nomor KK</label>
                        <input name="kk" value="{{ $userData->citizen_data['kk'] ?? '' }}" class="mt-1 w-full border rounded p-2 bg-gray-100" readonly />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Jenis Kelamin</label>
                        <select name="gender" class="mt-1 w-full border rounded p-2" required>
                            @php
                                // Handle berbagai kemungkinan format gender dari API
                                $genderVal = $userData->citizen_data['gender'] ?? '';
                                
                                // Normalize gender value untuk comparison yang lebih robust
                                $normalizedGender = '';
                                if (!empty($genderVal)) {
                                    $genderLower = strtolower(trim($genderVal));
                                    if (in_array($genderLower, ['laki-laki', 'laki laki', 'l', '1', 'male', 'pria'])) {
                                        $normalizedGender = 'Laki-laki';
                                    } elseif (in_array($genderLower, ['perempuan', 'p', '2', 'female', 'wanita'])) {
                                        $normalizedGender = 'Perempuan';
                                    }
                                }
                            @endphp
                            <option value="" {{ $normalizedGender == '' ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ $normalizedGender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $normalizedGender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @if(!empty($genderVal) && $normalizedGender == '')
                            <p class="text-xs text-orange-600 mt-1">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                                Format gender tidak dikenali: "{{ $genderVal }}". Silakan pilih manual.
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Umur</label>
                        <input name="age" value="{{ $userData->citizen_data['age'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Tempat Lahir</label>
                        <input name="birth_place" value="{{ $userData->citizen_data['birth_place'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ isset($userData->citizen_data['birth_date']) ? substr($userData->citizen_data['birth_date'],0,10) : '' }}" class="mt-1 w-full border rounded p-2" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700">Alamat</label>
                        <textarea name="address" class="mt-1 w-full border rounded p-2" rows="2">{{ $userData->citizen_data['address'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">RT</label>
                        <input name="rt" value="{{ $userData->citizen_data['rt'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">RW</label>
                        <input name="rw" value="{{ $userData->citizen_data['rw'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Provinsi</label>
                        <select name="province_code" id="province_code" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" 
                                    data-id="{{ $province['id'] }}"
                                    {{ ($userData->citizen_data['province_id'] ?? ($userData->citizen_data['provinsi_id'] ?? ($userData->citizen_data['provinceId'] ?? ''))) == $province['id'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="{{ $userData->citizen_data['province_id'] ?? ($userData->citizen_data['provinsi_id'] ?? ($userData->citizen_data['provinceId'] ?? '')) }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Kabupaten</label>
                        <select name="district_code" id="district_code" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                        <input type="hidden" id="district_id" name="district_id" value="{{ $userData->citizen_data['district_id'] ?? ($userData->citizen_data['kabupaten_id'] ?? ($userData->citizen_data['city_id'] ?? ($userData->citizen_data['districts_id'] ?? ''))) }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Kecamatan</label>
                        <select name="sub_district_code" id="sub_district_code" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" id="sub_district_id" name="sub_district_id" value="{{ $userData->citizen_data['sub_district_id'] ?? ($userData->citizen_data['kecamatan_id'] ?? ($userData->citizen_data['sub_districts_id'] ?? '')) }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Desa</label>
                        <select name="village_code" id="village_code" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled>
                            <option value="">Pilih Desa</option>
                        </select>
                        <input type="hidden" id="village_id" name="village_id" value="{{ $userData->citizen_data['village_id'] ?? ($userData->citizen_data['villages_id'] ?? ($userData->citizen_data['desa_id'] ?? '')) }}" />
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-3 mt-2">
                        <button type="button" onclick="toggleEditForm()" class="inline-flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50">
                            <i class="fa-solid fa-xmark"></i>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 bg-[#4A47DC] hover:bg-[#2D336B] text-white px-4 py-2 rounded-lg shadow-sm">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Permintaan Approval</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Alamat -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Alamat</h2>
                        <p class="text-gray-500">Informasi alamat penduduk</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Provinsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($userData->citizen_data['province_name']))
                                        {{ $userData->citizen_data['province_name'] }}
                                    @elseif(isset($userData->citizen_data['provinsi']))
                                        {{ $userData->citizen_data['provinsi'] }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kabupaten</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($userData->citizen_data['district_name']))
                                        {{ $userData->citizen_data['district_name'] }}
                                    @elseif(isset($userData->citizen_data['kabupaten']))
                                        {{ $userData->citizen_data['kabupaten'] }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($userData->citizen_data['sub_district_name']))
                                        {{ $userData->citizen_data['sub_district_name'] }}
                                    @elseif(isset($userData->citizen_data['kecamatan']))
                                        {{ $userData->citizen_data['kecamatan'] }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Desa</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(isset($userData->citizen_data['village_name']))
                                        {{ $userData->citizen_data['village_name'] }}
                                    @elseif(isset($userData->citizen_data['desa']))
                                        {{ $userData->citizen_data['desa'] }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- History Perubahan Biodata -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">History Perubahan Biodata</h2>
                        <p class="text-gray-500">Riwayat permintaan perubahan dan status approval</p>
                    </div>
                    <button type="button" onclick="toggleHistorySection()" id="btnToggleHistory" class="inline-flex items-center gap-2 bg-[#4A47DC] hover:bg-[#2D336B] text-white px-4 py-2 rounded-lg shadow-sm transition-all">
                        <i class="fa-solid fa-history" id="historyIcon"></i>
                        <span id="btnToggleHistoryText">Lihat History</span>
                    </button>
                </div>

                <!-- History Content (Hidden by default) -->
                <div id="historyContent" class="hidden">
                    @php
                        // Ambil data history perubahan biodata dari user
                        $biodataHistory = \App\Models\ProfileChangeRequest::where('nik', $userData->nik)
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp

                    @if($biodataHistory->count() > 0)
                        <div class="space-y-4">
                            @foreach($biodataHistory as $request)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="text-sm font-medium text-gray-700">
                                                    Permintaan #{{ $request->id }}
                                                </span>
                                                @if($request->status === 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                        Menunggu Review
                                                    </span>
                                                @elseif($request->status === 'approved')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                        Disetujui
                                                    </span>
                                                @elseif($request->status === 'rejected')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <div class="text-gray-600 mb-1"><strong>Tanggal Request:</strong> {{ $request->created_at->format('d M Y H:i') }}</div>
                                                    @if($request->reviewed_at)
                                                        <div class="text-gray-600 mb-1"><strong>Tanggal Review:</strong> {{ $request->reviewed_at->format('d M Y H:i') }}</div>
                                                        <div class="text-gray-600 mb-1"><strong>Reviewer:</strong> {{ $request->reviewer ? $request->reviewer->name : 'Admin Desa' }}</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-gray-600 mb-1"><strong>Status:</strong> 
                                                        @if($request->status === 'pending')
                                                            <span class="text-yellow-600">Menunggu review admin desa</span>
                                                        @elseif($request->status === 'approved')
                                                            <span class="text-green-600">Perubahan telah disetujui dan diterapkan</span>
                                                        @elseif($request->status === 'rejected')
                                                            <span class="text-red-600">Perubahan ditolak oleh admin desa</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Detail Perubahan -->
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <h4 class="font-medium text-gray-700 mb-2">Detail Perubahan:</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                                    @if(isset($request->requested_changes['full_name']))
                                                        <div><strong>Nama:</strong> {{ $request->requested_changes['full_name'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['gender']))
                                                        <div><strong>Jenis Kelamin:</strong> {{ $request->requested_changes['gender'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['birth_place']))
                                                        <div><strong>Tempat Lahir:</strong> {{ $request->requested_changes['birth_place'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['birth_date']))
                                                        <div><strong>Tanggal Lahir:</strong> {{ $request->requested_changes['birth_date'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['address']))
                                                        <div><strong>Alamat:</strong> {{ $request->requested_changes['address'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['rt']))
                                                        <div><strong>RT:</strong> {{ $request->requested_changes['rt'] }}</div>
                                                    @endif
                                                    @if(isset($request->requested_changes['rw']))
                                                        <div><strong>RW:</strong> {{ $request->requested_changes['rw'] }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Catatan dari Admin -->
                                            @if($request->reviewer_note)
                                                <div class="mt-3 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                                    <h4 class="font-medium text-blue-700 mb-2">
                                                        <i class="fa-solid fa-comment-dots mr-2"></i>
                                                        Catatan dari Admin Desa:
                                                    </h4>
                                                    <div class="text-sm text-blue-800">
                                                        {{ $request->reviewer_note }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-3">
                                <i class="fa-solid fa-history text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-600 mb-2">Belum Ada History</h3>
                            <p class="text-gray-500">Anda belum pernah mengajukan perubahan biodata</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabel Anggota Keluarga -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <div class="mb-6" id="familyMembersSection">
                    <div class="animate-pulse" id="familyMembersLoading">
                        <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-full mb-2.5"></div>
                        <div class="h-4 bg-gray-200 rounded w-full mb-2.5"></div>
                        <div class="h-4 bg-gray-200 rounded w-full mb-2.5"></div>
                    </div>

                    <div id="familyMembersContainer" class="hidden">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Daftar Anggota Keluarga</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIK</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Hubungan
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dokumen
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="familyMembersTable">
                                    <!-- Anggota Keluarga Tabel diload -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lokasi -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Tag Lokasi</h2>
                    </div>
                </div>

                @php
$lat = '';
$lng = '';

if (!empty($userData->tag_lokasi)) {
    $coordinates = explode(',', $userData->tag_lokasi);
    if (count($coordinates) >= 2) {
        $lat = trim($coordinates[0]);
        $lng = trim($coordinates[1]);
    }
}
                @endphp

                <x-map-input label="Lokasi Tempat Tinggal" addressId="user_address" addressName="user_address"
                    address="{{ $userData->alamat ?? '' }}" latitudeId="tagLat" latitudeName="tag_lat"
                    latitude="{{ $lat }}" longitudeId="tagLng" longitudeName="tag_lng" longitude="{{ $lng }}"
                    modalId="" />

                <div class="mt-4">
                    <button type="button" id="saveLocationBtn"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                        Simpan Tag Lokasi
                    </button>
                    <span id="locationSaveStatus" class="ml-2 text-sm hidden"></span>
                </div>
            </div>

            <!-- Foto KK -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Foto Kartu Keluarga</h2>
                        <p class="text-gray-500">Unggah foto Kartu Keluarga untuk keperluan verifikasi data</p>
                    </div>
                </div>

                <div id="fotoKkStatus" class="text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Belum diunggah</span>
                    </div>
                </div>

                <div id="fotoKkPreview" class="hidden mb-4">
                    <a href="#" id="viewFotoKk" target="_blank" class="block relative">
                        <img src="" alt="Kartu Keluarga" class="max-h-60 max-w-full rounded-lg border border-gray-200">

                    </a>
                </div>

                <form id="uploadFotoKkForm" class="mt-2">
                    <input type="hidden" name="document_type" value="foto_kk">
                    <div class="flex items-center">
                        <input type="file" name="file" id="fotoKkFile" accept="image/jpeg,image/png,application/pdf"
                            class="hidden">
                        <label for="fotoKkFile"
                            class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1.5 px-3 rounded-lg">
                            Pilih File
                        </label>
                        <span id="fotoKkFileName" class="ml-2 text-sm text-gray-600">Tidak ada file dipilih</span>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                            Upload Foto KK
                        </button>
                        <button type="button" id="deleteFotoKk"
                            class="hidden text-red-600 hover:text-red-800 text-sm py-1.5 px-4 border border-red-600 hover:border-red-800 rounded-lg">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>

            <!-- Foto Rumah -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Foto Rumah</h2>
                        <p class="text-gray-500">Unggah foto tampak depan rumah untuk keperluan verifikasi alamat</p>
                    </div>
                </div>

                <div id="fotoRumahStatus" class="text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Belum diunggah</span>
                    </div>
                </div>

                <div id="fotoRumahPreview" class="hidden mb-4">
                    <a href="#" id="viewFotoRumah" target="_blank" class="block relative">
                        <img src="" alt="Foto Rumah" class="max-h-60 max-w-full rounded-lg border border-gray-200">

                    </a>
                </div>

                <form id="uploadFotoRumahForm" class="mt-2">
                    <input type="hidden" name="document_type" value="foto_rumah">
                    <div class="flex items-center">
                        <input type="file" name="file" id="fotoRumahFile" accept="image/jpeg,image/png" class="hidden">
                        <label for="fotoRumahFile"
                            class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1.5 px-3 rounded-lg">
                            Pilih File
                        </label>
                        <span id="fotoRumahFileName" class="ml-2 text-sm text-gray-600">Tidak ada file dipilih</span>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                            Upload Foto Rumah
                        </button>
                        <button type="button" id="deleteFotoRumah"
                            class="hidden text-red-600 hover:text-red-800 text-sm py-1.5 px-4 border border-red-600 hover:border-red-800 rounded-lg">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modal Dokumen -->
            <div id="documentModal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow-md">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900" id="documentModalTitle">
                                Kelola Dokumen
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                onclick="closeDocumentModal()">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <div class="p-4 md:p-5 space-y-4">
                            <input type="hidden" id="memberNIK" value="">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Foto Diri -->
                                <div class="border rounded-lg p-4">
                                    <h4 class="text-md font-semibold mb-2">Foto Diri</h4>
                                    <div id="fotoDiriStatus" class="text-sm text-gray-600 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Belum diunggah</span>
                                        </div>
                                    </div>

                                    <div id="fotoDiriPreview" class="hidden mb-3">
                                        <a href="#" id="viewFotoDiri" target="_blank" class="block relative">
                                            <img src="" alt="Foto Diri"
                                                class="max-h-40 max-w-full rounded-lg border border-gray-200">

                                        </a>
                                        <div class="flex space-x-2 mt-2">
                                            <button type="button" onclick="deleteDocument('foto_diri')"
                                                class="text-red-600 text-xs hover:text-red-800">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <form id="uploadFotoDiriForm" class="mt-2">
                                        <input type="hidden" name="document_type" value="foto_diri">
                                        <div class="flex items-center">
                                            <input type="file" name="file" id="fotoDiriFile"
                                                accept="image/jpeg,image/png" class="hidden">
                                            <label for="fotoDiriFile"
                                                class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded-lg">
                                                Pilih File
                                            </label>
                                            <span id="fotoDiriFileName" class="ml-2 text-sm text-gray-600">Tidak ada
                                                file
                                                dipilih</span>
                                        </div>
                                        <button type="submit"
                                            class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                                            Upload Foto Diri
                                        </button>
                                    </form>
                                </div>

                                <!-- Foto KTP -->
                                <div class="border rounded-lg p-4">
                                    <h4 class="text-md font-semibold mb-2">Foto KTP</h4>
                                    <div id="fotoKtpStatus" class="text-sm text-gray-600 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Belum diunggah</span>
                                        </div>
                                    </div>

                                    <div id="fotoKtpPreview" class="hidden mb-3">
                                        <a href="#" id="viewFotoKtp" target="_blank" class="block relative">
                                            <img src="" alt="Foto KTP"
                                                class="max-h-40 max-w-full rounded-lg border border-gray-200">

                                        </a>
                                        <div class="flex space-x-2 mt-2">
                                            <button type="button" onclick="deleteDocument('foto_ktp')"
                                                class="text-red-600 text-xs hover:text-red-800">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <form id="uploadFotoKtpForm" class="mt-2">
                                        <input type="hidden" name="document_type" value="foto_ktp">
                                        <div class="flex items-center">
                                            <input type="file" name="file" id="fotoKtpFile"
                                                accept="image/jpeg,image/png,application/pdf" class="hidden">
                                            <label for="fotoKtpFile"
                                                class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded-lg">
                                                Pilih File
                                            </label>
                                            <span id="fotoKtpFileName" class="ml-2 text-sm text-gray-600">Tidak ada file
                                                dipilih</span>
                                        </div>
                                        <button type="submit"
                                            class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                                            Upload Foto KTP
                                        </button>
                                    </form>
                                </div>

                                <!-- Foto Akta -->
                                <div class="border rounded-lg p-4">
                                    <h4 class="text-md font-semibold mb-2">Akta Kelahiran</h4>
                                    <div id="fotoAktaStatus" class="text-sm text-gray-600 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Belum diunggah</span>
                                        </div>
                                    </div>

                                    <div id="fotoAktaPreview" class="hidden mb-3">
                                        <a href="#" id="viewFotoAkta" target="_blank" class="block relative">
                                            <img src="" alt="Akta Kelahiran"
                                                class="max-h-40 max-w-full rounded-lg border border-gray-200">

                                        </a>
                                        <div class="flex space-x-2 mt-2">
                                            <button type="button" onclick="deleteDocument('foto_akta')"
                                                class="text-red-600 text-xs hover:text-red-800">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <form id="uploadFotoAktaForm" class="mt-2">
                                        <input type="hidden" name="document_type" value="foto_akta">
                                        <div class="flex items-center">
                                            <input type="file" name="file" id="fotoAktaFile"
                                                accept="image/jpeg,image/png,application/pdf" class="hidden">
                                            <label for="fotoAktaFile"
                                                class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded-lg">
                                                Pilih File
                                            </label>
                                            <span id="fotoAktaFileName" class="ml-2 text-sm text-gray-600">Tidak ada
                                                file
                                                dipilih</span>
                                        </div>
                                        <button type="submit"
                                            class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                                            Upload Akta Kelahiran
                                        </button>
                                    </form>
                                </div>

                                <!-- Ijazah -->
                                <div class="border rounded-lg p-4">
                                    <h4 class="text-md font-semibold mb-2">Ijazah</h4>
                                    <div id="ijazahStatus" class="text-sm text-gray-600 mb-2">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Belum diunggah</span>
                                        </div>
                                    </div>

                                    <div id="ijazahPreview" class="hidden mb-3">
                                        <a href="#" id="viewIjazah" target="_blank" class="block relative">
                                            <img src="" alt="Ijazah"
                                                class="max-h-40 max-w-full rounded-lg border border-gray-200">

                                        </a>
                                        <div class="flex space-x-2 mt-2">
                                            <button type="button" onclick="deleteDocument('ijazah')"
                                                class="text-red-600 text-xs hover:text-red-800">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <form id="uploadIjazahForm" class="mt-2">
                                        <input type="hidden" name="document_type" value="ijazah">
                                        <div class="flex items-center">
                                            <input type="file" name="file" id="ijazahFile"
                                                accept="image/jpeg,image/png,application/pdf" class="hidden">
                                            <label for="ijazahFile"
                                                class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded-lg">
                                                Pilih File
                                            </label>
                                            <span id="ijazahFileName" class="ml-2 text-sm text-gray-600">Tidak ada file
                                                dipilih</span>
                                        </div>
                                        <button type="submit"
                                            class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm py-1.5 px-4 rounded-lg">
                                            Upload Ijazah
                                        </button>
                                    </form>
                                </div>
                            </div>


                        </div>

                        <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b">
                            <button type="button" onclick="closeDocumentModal()"
                                class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Tutup
                            </button>
                        </div>

                        <!-- Wilayah -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Set hidden input values for location IDs
                                const provinceIdInput = document.getElementById('province_id');
                                const districtIdInput = document.getElementById('district_id');
                                const subDistrictIdInput = document.getElementById('sub_district_id');
                                const villageIdInput = document.getElementById('village_id');

                                // Get dropdown elements
                                const provinceSelect = document.getElementById('province_code');
                                const districtSelect = document.getElementById('district_code');
                                const subDistrictSelect = document.getElementById('sub_district_code');
                                const villageSelect = document.getElementById('village_code');

                                // Ensure hidden inputs have the correct values
                                if (provinceIdInput && districtIdInput && subDistrictIdInput && villageIdInput) {
                                    console.log('Location IDs set:', {
                                        province: provinceIdInput.value,
                                        district: districtIdInput.value,
                                        subDistrict: subDistrictIdInput.value,
                                        village: villageIdInput.value
                                    });
                                }

                                // Function to populate disabled dropdowns with data
                                async function populateDisabledDropdowns() {
                                    if (!provinceSelect.value) return;

                                    try {
                                        // Load districts
                                        const districtResponse = await fetch(`{{ url('/location/districts') }}/${provinceSelect.value}`);
                                        if (districtResponse.ok) {
                                            const districts = await districtResponse.json();
                                            populateSelect(districtSelect, districts, 'Pilih Kabupaten', districtIdInput.value);
                                        }

                                        // Load sub-districts if district is available
                                        if (districtSelect.value) {
                                            const subDistrictResponse = await fetch(`{{ url('/location/sub-districts') }}/${districtSelect.value}`);
                                            if (subDistrictResponse.ok) {
                                                const subDistricts = await subDistrictResponse.json();
                                                populateSelect(subDistrictSelect, subDistricts, 'Pilih Kecamatan', subDistrictIdInput.value);
                                            }
                                        }

                                        // Load villages if sub-district is available
                                        if (subDistrictSelect.value) {
                                            const villageResponse = await fetch(`{{ url('/location/villages') }}/${subDistrictSelect.value}`);
                                            if (villageResponse.ok) {
                                                const villages = await villageResponse.json();
                                                populateSelect(villageSelect, villages, 'Pilih Desa', villageIdInput.value);
                                            }
                                        }
                                    } catch (error) {
                                        console.error('Error populating dropdowns:', error);
                                    }
                                }

                                // Helper function to populate select options
                                function populateSelect(select, data, defaultText, selectedId = null) {
                                    try {
                                        const fragment = document.createDocumentFragment();
                                        const defaultOption = document.createElement('option');
                                        defaultOption.value = '';
                                        defaultOption.textContent = defaultText;
                                        fragment.appendChild(defaultOption);

                                        if (Array.isArray(data)) {
                                            data.forEach(item => {
                                                const option = document.createElement('option');
                                                option.value = item.code;
                                                option.setAttribute('data-id', item.id);
                                                option.textContent = item.name;

                                                // Check if this should be selected
                                                if (selectedId && item.id == selectedId) {
                                                    option.selected = true;
                                                }

                                                fragment.appendChild(option);
                                            });
                                        }

                                        select.innerHTML = '';
                                        select.appendChild(fragment);
                                    } catch (error) {
                                        console.error('Error populating select:', error);
                                        select.innerHTML = `<option value="">Error loading data</option>`;
                                    }
                                }

                                // Initialize dropdowns when page loads
                                populateDisabledDropdowns();
                            });
                        </script>

                        <!-- Anggota Keluarga -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {

                                const UI = {
                                    section: document.getElementById('familyMembersSection'),
                                    loading: document.getElementById('familyMembersLoading'),
                                    container: document.getElementById('familyMembersContainer'),
                                    table: document.getElementById('familyMembersTable')
                                };


                                const ICONS = {
                                    view: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                      </svg>`,
                                    edit: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                      </svg>`,
                                    delete: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                         </svg>`
                                };

                                // CSS Classes
                                const CLASSES = {
                                    cell: "px-6 py-4 whitespace-nowrap",
                                    text: "text-sm text-gray-900",
                                    textBold: "text-sm font-medium text-gray-900",
                                    buttonBlue: "text-blue-600 hover:text-blue-900",
                                    buttonGreen: "text-green-600 hover:text-green-900",
                                    buttonRed: "text-red-600 hover:text-red-900",
                                    buttonText: "text-sm font-medium"
                                };

                                function initializeFamilyMembers() {
                                    const familyMembersData = @json($userData->family_members ?? []);

                                    UI.loading.style.display = 'none';
                                    UI.container.classList.remove('hidden');

                                    if (familyMembersData.length > 0) {
                                        renderFamilyMembersTable(familyMembersData);
                                    } else {
                                        renderEmptyState();
                                    }
                                }


                                function getMemberName(member) {
                                    return member.full_name || member.nama || '-';
                                }

                                function getMemberStatus(member) {
                                    return member.family_status || member.hubungan_keluarga || '-';
                                }

                                function createTableCell(content, additionalClasses = '') {
                                    return `<td class="${CLASSES.cell} ${additionalClasses}">${content}</td>`;
                                }

                                function renderFamilyMembersTable(members) {
                                    members.forEach(member => {
                                        const row = document.createElement('tr');

                                        row.innerHTML = createTableCell(
                                            `<div class="${CLASSES.text}">${member.nik || '-'}</div>`
                                        );

                                        row.innerHTML += createTableCell(
                                            `<div class="${CLASSES.textBold}">${getMemberName(member)}</div>`
                                        );

                                        row.innerHTML += createTableCell(
                                            `<div class="${CLASSES.text}">${getMemberStatus(member)}</div>`
                                        );

                                        row.innerHTML += createTableCell(
                                            `<button type="button" onclick="openDocumentModal('${member.nik}', '${getMemberName(member)}')"
                                         class="${CLASSES.buttonBlue} ${CLASSES.buttonText}">
                                            Kelola Dokumen
                                         </button>`
                                        );

                                        row.innerHTML += createTableCell(
                                            `<div class="${CLASSES.text}">${member.tag_lokasi || '-'}</div>`
                                        );



                                        UI.table.appendChild(row);
                                    });
                                }

                                function renderEmptyState() {
                                    const emptyRow = document.createElement('tr');
                                    emptyRow.innerHTML = `
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada anggota keluarga yang ditemukan
                                    </td>
                                `;
                                    UI.table.appendChild(emptyRow);
                                }

                                initializeFamilyMembers();
                            });
                        </script>

                        <!-- Modal Dokumen -->
                        <script>
                            let currentNIK = '';
                            let currentName = '';

                            function openDocumentModal(nik, fullName, viewOnly = false) {
                                currentNIK = nik;
                                currentName = fullName;

                                if (!fullName) {
                                    const familyMembersData = @json($userData->family_members ?? []);
                                    const member = familyMembersData.find(m => m.nik === nik);
                                    if (member) {
                                        fullName = member.full_name || member.nama || `Anggota Keluarga (${nik})`;
                                        currentName = fullName;
                                    } else {
                                        fullName = `Anggota Keluarga (${nik})`;
                                    }
                                }

                                document.getElementById('documentModalTitle').textContent =
                                    `${viewOnly ? 'Lihat' : 'Kelola'} Dokumen - ${fullName}`;
                                document.getElementById('memberNIK').value = nik;

                                resetDocumentForms();

                                fetchDocumentStatus(nik);

                                if (viewOnly) {
                                    const statusElements = document.querySelectorAll('#documentModal [id$="Status"]');
                                    statusElements.forEach(element => {
                                        element.innerHTML = `
                                        <div class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Memuat data...</span>
                                        </div>
                                    `;
                                    });

                                    const uploadForms = document.querySelectorAll('#documentModal form[id^="upload"]');
                                    uploadForms.forEach(form => {
                                        form.style.display = 'none';
                                    });
                                }

                                const modal = document.getElementById('documentModal');
                                modal.classList.remove('hidden');
                                modal.classList.add('flex');
                            }

                            function closeDocumentModal() {
                                const modal = document.getElementById('documentModal');
                                modal.classList.add('hidden');
                                modal.classList.remove('flex');
                            }

                            function resetDocumentForms() {
                                document.getElementById('uploadFotoDiriForm').reset();
                                document.getElementById('uploadFotoKtpForm').reset();
                                document.getElementById('uploadFotoAktaForm').reset();
                                document.getElementById('uploadIjazahForm').reset();
                                // No need to reset the standalone forms here since they're outside the modal

                                document.getElementById('fotoDiriPreview').classList.add('hidden');
                                document.getElementById('fotoKtpPreview').classList.add('hidden');
                                document.getElementById('fotoAktaPreview').classList.add('hidden');
                                document.getElementById('ijazahPreview').classList.add('hidden');

                                const statusHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Belum diunggah</span>
                                    </div>
                                `;

                                document.getElementById('fotoDiriStatus').innerHTML = statusHTML;
                                document.getElementById('fotoKtpStatus').innerHTML = statusHTML;
                                document.getElementById('fotoAktaStatus').innerHTML = statusHTML;
                                document.getElementById('ijazahStatus').innerHTML = statusHTML;

                                document.getElementById('fotoDiriFileName').textContent = "Tidak ada file dipilih";
                                document.getElementById('fotoKtpFileName').textContent = "Tidak ada file dipilih";
                                document.getElementById('fotoAktaFileName').textContent = "Tidak ada file dipilih";
                                document.getElementById('ijazahFileName').textContent = "Tidak ada file dipilih";
                            }


                            function fetchDocumentStatus(nik) {
                                fetch(`/user/family-member/${nik}/documents`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            if (data.tag_lokasi) {
                                                document.getElementById('tagLokasi').value = data.tag_lokasi;
                                            }

                                            updateDocumentStatus('fotoDiri', data.documents?.foto_diri || null);
                                            updateDocumentStatus('fotoKtp', data.documents?.foto_ktp || null);
                                            updateDocumentStatus('fotoAkta', data.documents?.foto_akta || null);
                                            updateDocumentStatus('ijazah', data.documents?.ijazah || null);
                                        } else {
                                            console.error('Error retrieving document data. Please try again later.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching document status:', error);

                                        const statusElements = document.querySelectorAll('#documentModal [id$="Status"]');
                                        statusElements.forEach(element => {
                                            element.innerHTML = `
                                            <div class="flex items-center text-red-500">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Gagal memuat data</span>
                                            </div>
                                        `;
                                        });
                                    });
                            }

                            function updateDocumentStatus(docType, docInfo) {
                                const statusElem = document.getElementById(`${docType}Status`);
                                const previewElem = document.getElementById(`${docType}Preview`);
                                const viewLinkElem = document.getElementById(`view${docType.charAt(0).toUpperCase() + docType.slice(1)}`);
                                const docTypeKey = docType.replace(/([A-Z])/g, '_$1').toLowerCase();

                                if (docInfo && docInfo.exists) {
                                    statusElem.innerHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-green-600">Dokumen tersedia</span>
                                        <span class="ml-2 text-xs text-gray-500">(${new Date(docInfo.updated_at).toLocaleDateString()})</span>
                                    </div>
                                `;

                                    if (docInfo.preview_url) {
                                        const imgElem = previewElem.querySelector('img');
                                        imgElem.src = docInfo.preview_url;
                                        previewElem.classList.remove('hidden');

                                        viewLinkElem.href = `/user/family-member/${currentNIK}/document/${docTypeKey}/view`;
                                    } else if (docInfo.extension && ['pdf'].includes(docInfo.extension.toLowerCase())) {
                                        previewElem.innerHTML =
                                            `<a href="/user/family-member/${currentNIK}/document/${docTypeKey}/view" target="_blank" class="block relative">
                                            <div class="p-4 bg-gray-100 rounded-lg text-center">
                                                <svg class="w-10 h-10 mx-auto text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="mt-2 text-sm font-medium">PDF Document</p>
                                                <span class="absolute inset-0 bg-black bg-opacity-0 flex items-center justify-center hover:bg-opacity-10 transition-all rounded-lg">
                                                    <span class="text-blue-600 opacity-0 hover:opacity-100">Lihat</span>
                                                </span>
                                            </div>
                                        </a>
                                        <div class="flex space-x-2 mt-2">
                                            <button type="button" onclick="deleteDocument('${docTypeKey}')" class="text-red-600 text-xs hover:text-red-800">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    `;
                                        previewElem.classList.remove('hidden');
                                    }
                                } else {
                                    statusElem.innerHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Belum diunggah</span>
                                    </div>
                                `;
                                    previewElem.classList.add('hidden');
                                }
                            }

                            function setupDocumentUpload(formId, docType) {
                                const form = document.getElementById(formId);
                                const fileInput = form.querySelector('input[type="file"]');
                                const fileNameSpan = document.getElementById(`${docType}FileName`);

                                fileInput.addEventListener('change', function () {
                                    if (this.files.length > 0) {
                                        fileNameSpan.textContent = this.files[0].name;
                                    } else {
                                        fileNameSpan.textContent = "Tidak ada file dipilih";
                                    }
                                });

                                form.addEventListener('submit', function (e) {
                                    e.preventDefault();

                                    if (!fileInput.files || fileInput.files.length === 0) {
                                        alert("Silakan pilih file terlebih dahulu.");
                                        return;
                                    }

                                    const formData = new FormData(this);
                                    formData.append('_token', '{{ csrf_token() }}');

                                    const submitBtn = form.querySelector('button[type="submit"]');
                                    const originalBtnText = submitBtn.innerHTML;
                                    submitBtn.innerHTML = `
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengupload...
                                `;
                                    submitBtn.disabled = true;

                                    fetch(`/user/family-member/${currentNIK}/upload-document`, {
                                        method: 'POST',
                                        body: formData
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                fetchDocumentStatus(currentNIK);

                                                alert("Dokumen berhasil diunggah!");

                                                fileInput.value = '';
                                                fileNameSpan.textContent = "Tidak ada file dipilih";
                                            } else {
                                                alert("Gagal mengunggah dokumen: " + (data.message || "Terjadi kesalahan"));
                                            }


                                            submitBtn.innerHTML = originalBtnText;
                                            submitBtn.disabled = false;
                                        })
                                        .catch(error => {
                                            console.error('Error uploading document:', error);

                                            alert("Terjadi kesalahan saat mengunggah dokumen.");

                                            submitBtn.innerHTML = originalBtnText;
                                            submitBtn.disabled = false;
                                        });
                                });
                            }

                            // Hapus dokumen
                            function deleteDocument(docType) {
                                if (confirm("Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.")) {
                                    fetch(`/user/family-member/${currentNIK}/delete-document/${docType}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                fetchDocumentStatus(currentNIK);
                                                alert("Dokumen berhasil dihapus!");
                                            } else {
                                                alert("Gagal menghapus dokumen: " + (data.message || "Terjadi kesalahan"));
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error deleting document:', error);
                                            alert("Terjadi kesalahan saat menghapus dokumen.");
                                        });
                                }
                            }



                            document.addEventListener('DOMContentLoaded', function () {
                                const fileInputs = document.querySelectorAll('input[type="file"]');
                                fileInputs.forEach(input => {
                                    const fileNameId = input.id.replace('File', 'FileName');
                                    const fileNameElem = document.getElementById(fileNameId);

                                    if (fileNameElem) {
                                        input.addEventListener('change', function () {
                                            fileNameElem.textContent = this.files.length > 0 ? this.files[0].name :
                                                "Tidak ada file dipilih";
                                        });
                                    }
                                });

                                setupDocumentUpload('uploadFotoDiriForm', 'fotoDiri');
                                setupDocumentUpload('uploadFotoKtpForm', 'fotoKtp');
                                setupDocumentUpload('uploadFotoAktaForm', 'fotoAkta');
                                setupDocumentUpload('uploadIjazahForm', 'ijazah');
                            });
                        </script>

                        <!--  -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Get user's NIK
                                const userNik = "{{ $userData->nik ?? '' }}";
                                if (!userNik) {
                                    console.error('User NIK not found');
                                    return;
                                }

                                // Fetch document status for standalone document sections
                                fetchStandaloneDocuments(userNik);

                                // Setup document upload for standalone document sections
                                setupStandaloneDocumentUpload('uploadFotoKkForm', 'fotoKk', userNik);
                                setupStandaloneDocumentUpload('uploadFotoRumahForm', 'fotoRumah', userNik);

                                // Setup delete handlers
                                document.getElementById('deleteFotoKk').addEventListener('click', function () {
                                    deleteStandaloneDocument('foto_kk', userNik);
                                });

                                document.getElementById('deleteFotoRumah').addEventListener('click', function () {
                                    deleteStandaloneDocument('foto_rumah', userNik);
                                });

                                // Function to fetch document status
                                function fetchStandaloneDocuments(nik) {
                                    fetch(`/user/family-member/${nik}/documents`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                updateStandaloneDocumentStatus('fotoKk', data.documents?.foto_kk || null);
                                                updateStandaloneDocumentStatus('fotoRumah', data.documents?.foto_rumah || null);
                                            } else {
                                                console.error('Error in document data response:');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching document status:', error);
                                        });
                                }

                                // Function to update document status display
                                function updateStandaloneDocumentStatus(docType, docInfo) {
                                    const statusElem = document.getElementById(`${docType}Status`);
                                    const previewElem = document.getElementById(`${docType}Preview`);
                                    const viewLinkElem = document.getElementById(`view${docType.charAt(0).toUpperCase() + docType.slice(1)}`);
                                    const deleteBtn = document.getElementById(`delete${docType.charAt(0).toUpperCase() + docType.slice(1)}`);
                                    const docTypeKey = docType.replace(/([A-Z])/g, '_$1').toLowerCase();

                                    if (!statusElem) {
                                        console.error(`Status element for ${docType} not found`);
                                        return;
                                    }

                                    if (docInfo && docInfo.exists) {
                                        statusElem.innerHTML = `
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-green-600">Dokumen tersedia</span>
                                                <span class="ml-2 text-xs text-gray-500">(${new Date(docInfo.updated_at).toLocaleDateString()})</span>
                                            </div>
                                        `;

                                        if (docInfo.preview_url && previewElem) {
                                            const imgElem = previewElem.querySelector('img');
                                            if (imgElem) {
                                                imgElem.src = docInfo.preview_url;
                                                previewElem.classList.remove('hidden');
                                            }

                                            if (viewLinkElem) {
                                                viewLinkElem.href = `/user/family-member/${userNik}/document/${docTypeKey}/view`;
                                            }
                                        } else if (docInfo.extension && ['pdf'].includes(docInfo.extension.toLowerCase()) && previewElem) {
                                            previewElem.innerHTML = `
                                                <a href="/user/family-member/${userNik}/document/${docTypeKey}/view" target="_blank" class="block relative">
                                                    <div class="p-4 bg-gray-100 rounded-lg text-center">
                                                        <svg class="w-10 h-10 mx-auto text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <p class="mt-2 text-sm font-medium">PDF Document</p>
                                                        <span class="absolute inset-0 bg-black bg-opacity-0 flex items-center justify-center hover:bg-opacity-10 transition-all rounded-lg">
                                                            <span class="text-blue-600 opacity-0 hover:opacity-100">Lihat</span>
                                                        </span>
                                                    </div>
                                                </a>
                                            `;
                                            previewElem.classList.remove('hidden');
                                        }

                                        if (deleteBtn) {
                                            deleteBtn.classList.remove('hidden');
                                        }
                                    } else {
                                        statusElem.innerHTML = `
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Belum diunggah</span>
                                            </div>
                                        `;
                                        if (previewElem) {
                                            previewElem.classList.add('hidden');
                                        }

                                        if (deleteBtn) {
                                            deleteBtn.classList.add('hidden');
                                        }
                                    }
                                }

                                // Function to setup document upload
                                function setupStandaloneDocumentUpload(formId, docType, nik) {
                                    const form = document.getElementById(formId);
                                    if (!form) {
                                        console.error(`Form ${formId} not found`);
                                        return;
                                    }

                                    const fileInput = form.querySelector('input[type="file"]');
                                    const fileNameSpan = document.getElementById(`${docType}FileName`);

                                    if (fileInput && fileNameSpan) {
                                        fileInput.addEventListener('change', function () {
                                            if (this.files.length > 0) {
                                                fileNameSpan.textContent = this.files[0].name;
                                            } else {
                                                fileNameSpan.textContent = "Tidak ada file dipilih";
                                            }
                                        });
                                    }

                                    form.addEventListener('submit', function (e) {
                                        e.preventDefault();

                                        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                                            alert("Silakan pilih file terlebih dahulu.");
                                            return;
                                        }

                                        const formData = new FormData(this);
                                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                                        const submitBtn = form.querySelector('button[type="submit"]');
                                        const originalBtnText = submitBtn.innerHTML;
                                        submitBtn.innerHTML = `
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Mengupload...
                                        `;
                                        submitBtn.disabled = true;

                                        fetch(`/user/family-member/${nik}/upload-document`, {
                                            method: 'POST',
                                            body: formData
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    fetchStandaloneDocuments(nik);

                                                    alert("Dokumen berhasil diunggah!");

                                                    fileInput.value = '';
                                                    fileNameSpan.textContent = "Tidak ada file dipilih";
                                                } else {
                                                    alert("Gagal mengunggah dokumen: " + (data.message || "Terjadi kesalahan"));
                                                }

                                                submitBtn.innerHTML = originalBtnText;
                                                submitBtn.disabled = false;
                                            })
                                            .catch(error => {
                                                console.error('Error uploading document:', error);

                                                alert("Terjadi kesalahan saat mengunggah dokumen.");

                                                submitBtn.innerHTML = originalBtnText;
                                                submitBtn.disabled = false;
                                            });
                                    });
                                }

                                function deleteStandaloneDocument(docType, nik) {
                                    if (confirm("Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.")) {
                                        fetch(`/user/family-member/${nik}/delete-document/${docType}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                            }
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    fetchStandaloneDocuments(nik);
                                                    alert("Dokumen berhasil dihapus!");
                                                } else {
                                                    alert("Gagal menghapus dokumen: " + (data.message || "Terjadi kesalahan"));
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error deleting document:', error);
                                                alert("Terjadi kesalahan saat menghapus dokumen.");
                                            });
                                    }
                                }
                            });
                        </script>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const saveLocationBtn = document.getElementById('saveLocationBtn');
                                const locationSaveStatus = document.getElementById('locationSaveStatus');
                                const tagLatInput = document.getElementById('tagLat');
                                const tagLngInput = document.getElementById('tagLng');

                                function initializeExistingLocation() {

                                    const nik = "{{ $userData->nik ?? '' }}";
                                    if (!nik) {
                                        console.log('NIK pengguna tidak tersedia, tidak dapat memuat koordinat');
                                        return;
                                    }



                                    fetch(`https://api-kependudukan.desaverse.id/api/citizens/${nik}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-API-Key': '{{ config('services.kependudukan.key') }}',
                                            'Accept': 'application/json'
                                        }
                                    })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error(`HTTP error! Status: ${response.status}`);
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            if (data.data && data.data.coordinate) {
                                                const coordinates = data.data.coordinate.split(',');
                                                if (coordinates.length === 2) {
                                                    const lat = coordinates[0].trim();
                                                    const lng = coordinates[1].trim();

                                                    tagLatInput.value = lat;
                                                    tagLngInput.value = lng;


                                                    if (window.map && window.marker) {
                                                        window.marker.setLatLng([lat, lng]);
                                                        window.map.setView([lat, lng], 15);
                                                    }

                                                }
                                            } else {
                                                console.log('Tidak ada koordinat tersimpan untuk pengguna ini');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error loading location:', error);
                                        });
                                }


                                initializeExistingLocation();

                                function resetSaveButton() {
                                    if (saveLocationBtn) {
                                        saveLocationBtn.disabled = false;
                                        saveLocationBtn.textContent = 'Simpan Tag Lokasi';
                                    }
                                }

                                if (saveLocationBtn) {
                                    saveLocationBtn.addEventListener('click', function () {

                                        const lat = document.getElementById('tagLat').value;
                                        const lng = document.getElementById('tagLng').value;

                                        if (!lat || !lng) {
                                            showLocationStatus('error', 'Pilih lokasi pada peta terlebih dahulu');
                                            return;
                                        }


                                        const coordinate = `${lat},${lng}`;
                                        console.log('Sending coordinate:', coordinate);


                                        saveLocationBtn.disabled = true;
                                        saveLocationBtn.innerHTML = `
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Menyimpan...
                                        `;


                                        const nik = "{{ $userData->nik ?? '' }}";
                                        if (!nik) {
                                            showLocationStatus('error', 'NIK pengguna tidak ditemukan');
                                            resetSaveButton();
                                            return;
                                        }


                                        fetch(`https://api-kependudukan.desaverse.id/api/citizens/${nik}`, {
                                            method: 'GET',
                                            headers: {
                                                'X-API-Key': '{{ config('services.kependudukan.key') }}',
                                                'Accept': 'application/json'
                                            }
                                        })
                                            .then(response => {
                                                if (!response.ok) {
                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                }
                                                return response.json();
                                            })
                                            .then(data => {
                                                if (!data.data) {
                                                    throw new Error('Data tidak ditemukan');
                                                }

                                                const citizenData = convertDataToApiFormat(data.data);

                                                citizenData.coordinate = coordinate;


                                                // Update the citizen data
                                                return fetch(`https://api-kependudukan.desaverse.id/api/citizens/${nik}`, {
                                                    method: 'PUT',
                                                    headers: {
                                                        'X-API-Key': '{{ config('services.kependudukan.key') }}',
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify(citizenData)
                                                });
                                            })
                                            .then(response => {
                                                if (!response.ok) {

                                                    return response.text().then(text => {
                                                        let errorMessage = `HTTP error! Status: ${response.status}`;
                                                        try {
                                                            const errorData = JSON.parse(text);
                                                            console.error('Error details:', errorData);
                                                            errorMessage += `, Message: ${errorData.message || 'Unknown error'}`;
                                                        } catch (e) {
                                                            console.error('Non-JSON error response:', text);
                                                        }
                                                        throw new Error(errorMessage);
                                                    });
                                                }
                                                return response.json();
                                            })
                                            .then(data => {
                                                if (data.status === 'OK') {
                                                    showLocationStatus('success', 'Lokasi berhasil disimpan');
                                                } else {
                                                    throw new Error(data.message || 'Gagal menyimpan lokasi');
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error saving location:', error);
                                                showLocationStatus('error', `Gagal menyimpan lokasi: ${error.message}`);
                                            })
                                            .finally(() => {
                                                resetSaveButton();
                                            });
                                    });
                                }


                                function convertDataToApiFormat(data) {

                                    const result = { ...data };


                                    if (result.gender === 'Laki-Laki') result.gender = 1;
                                    else if (result.gender === 'Perempuan') result.gender = 2;


                                    if (result.citizen_status === 'WNI') result.citizen_status = 1;
                                    else if (result.citizen_status === 'WNA') result.citizen_status = 2;


                                    const familyStatusMap = {
                                        'ANAK': 1, 'Anak': 1,
                                        'KEPALA KELUARGA': 2, 'Kepala Keluarga': 2,
                                        'ISTRI': 3, 'Istri': 3,
                                        'ORANG TUA': 4, 'Orang Tua': 4,
                                        'MERTUA': 5, 'Mertua': 5,
                                        'CUCU': 6, 'Cucu': 6,
                                        'FAMILI LAIN': 7, 'Famili Lain': 7
                                    };
                                    if (typeof result.family_status === 'string' && familyStatusMap[result.family_status]) {
                                        result.family_status = familyStatusMap[result.family_status];
                                    }

                                    const bloodTypeMap = {
                                        'A': 1, 'B': 2, 'AB': 3, 'O': 4,
                                        'A+': 5, 'A-': 6, 'B+': 7, 'B-': 8,
                                        'AB+': 9, 'AB-': 10, 'O+': 11, 'O-': 12,
                                        'Tidak Tahu': 13
                                    };
                                    if (bloodTypeMap[result.blood_type]) result.blood_type = bloodTypeMap[result.blood_type];

                                    const religionMap = {
                                        'Islam': 1, 'Kristen': 2, 'Katolik': 3, 'Katholik': 3,
                                        'Hindu': 4, 'Buddha': 5, 'Budha': 5, 'Kong Hu Cu': 6,
                                        'Konghucu': 6, 'Lainnya': 7
                                    };
                                    if (religionMap[result.religion]) result.religion = religionMap[result.religion];

                                    const maritalMap = {
                                        'Belum Kawin': 1, 'Kawin Tercatat': 2, 'Kawin Belum Tercatat': 3,
                                        'Cerai Hidup Tercatat': 4, 'Cerai Hidup Belum Tercatat': 5, 'Cerai Mati': 6
                                    };
                                    if (maritalMap[result.marital_status]) result.marital_status = maritalMap[result.marital_status];

                                    if (result.birth_certificate === 'Ada') result.birth_certificate = 1;
                                    else if (result.birth_certificate === 'Tidak Ada') result.birth_certificate = 2;

                                    if (result.marital_certificate === 'Ada') result.marital_certificate = 1;
                                    else if (result.marital_certificate === 'Tidak Ada') result.marital_certificate = 2;

                                    if (result.divorce_certificate === 'Ada') result.divorce_certificate = 1;
                                    else if (result.divorce_certificate === 'Tidak Ada') result.divorce_certificate = 2;

                                    if (result.mental_disorders === 'Ada') result.mental_disorders = 1;
                                    else if (result.mental_disorders === 'Tidak Ada') result.mental_disorders = 2;

                                    const disabilitiesMap = {
                                        'Fisik': 1, 'Netra/Buta': 2, 'Rungu/Wicara': 3,
                                        'Mental/Jiwa': 4, 'Fisik dan Mental': 5, 'Lainnya': 6 //opsi tidak ada
                                    };

                                    if (typeof result.disabilities === 'string') {
                                        if (result.disabilities === '' || result.disabilities === ' ') {
                                            result.disabilities = null;
                                        } else if (disabilitiesMap[result.disabilities]) {
                                            result.disabilities = disabilitiesMap[result.disabilities];
                                        }
                                    }

                                    const educationMap = {
                                        'Tidak/Belum Sekolah': 1, 'Belum tamat SD/Sederajat': 2, 'Tamat SD': 3,
                                        'SLTP/SMP/Sederajat': 4, 'SLTA/SMA/Sederajat': 5, 'Diploma I/II': 6,
                                        'Akademi/Diploma III/ Sarjana Muda': 7, 'Diploma IV/ Strata I/ Strata II': 8,
                                        'Strata III': 9, 'Lainnya': 10
                                    };
                                    if (educationMap[result.education_status]) result.education_status = educationMap[result.education_status];

                                    if (result.birth_date && result.birth_date.includes('/')) {
                                        const parts = result.birth_date.split('/');
                                        if (parts.length === 3) {
                                            result.birth_date = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                        }
                                    }

                                    if (result.marriage_date === '') result.marriage_date = ' ';
                                    if (result.divorce_certificate_date === '') result.divorce_certificate_date = ' ';

                                    if (result.birth_certificate_no === '') result.birth_certificate_no = ' ';
                                    if (result.marital_certificate_no === '') result.marital_certificate_no = ' ';
                                    if (result.divorce_certificate_no === '') result.divorce_certificate_no = ' ';

                                    return result;
                                }

                                function showLocationStatus(type, message) {
                                    locationSaveStatus.textContent = message;
                                    locationSaveStatus.classList.remove('hidden', 'text-green-600', 'text-red-600');

                                    if (type === 'success') {
                                        locationSaveStatus.classList.add('text-green-600');
                                    } else {
                                        locationSaveStatus.classList.add('text-red-600');
                                    }

                                    setTimeout(() => {
                                        locationSaveStatus.classList.add('hidden');
                                    }, 5000);
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
            <script>
                function toggleEditForm() {
                    const el = document.getElementById('editBiodataForm');
                    const btn = document.getElementById('btnToggleEdit');
                    const btnText = document.getElementById('btnToggleEditText');
                    if (!el) return;
                    const isHidden = el.classList.contains('hidden');
                    if (isHidden) {
                        el.classList.remove('hidden');
                        btn.classList.remove('bg-[#4A47DC]');
                        btn.classList.add('bg-[#2D336B]');
                        btnText.textContent = 'Tutup Form Edit';
                        el.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        el.classList.add('hidden');
                        btn.classList.remove('bg-[#2D336B]');
                        btn.classList.add('bg-[#4A47DC]');
                        btnText.textContent = 'Edit Biodata (Minta Approval)';
                    }
                }
            </script>
            <!-- Form Validation -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.querySelector('form[action*="request-approval"]');
                    if (!form) return;

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Get form elements
                        const fullName = document.querySelector('input[name="full_name"]').value.trim();
                        const gender = document.querySelector('select[name="gender"]').value;
                        const provinceId = document.getElementById('province_id').value;
                        const districtId = document.getElementById('district_id').value;
                        const subDistrictId = document.getElementById('sub_district_id').value;
                        const villageId = document.getElementById('village_id').value;

                        // Validation
                        let isValid = true;
                        let errorMessage = '';

                        if (!fullName) {
                            errorMessage += 'Nama Lengkap harus diisi\n';
                            isValid = false;
                        }

                        if (!gender) {
                            errorMessage += 'Jenis Kelamin harus dipilih\n';
                            isValid = false;
                        }

                        if (!provinceId) {
                            errorMessage += 'Provinsi harus tersedia\n';
                            isValid = false;
                        }

                        if (!districtId) {
                            errorMessage += 'Kabupaten harus tersedia\n';
                            isValid = false;
                        }

                        if (!subDistrictId) {
                            errorMessage += 'Kecamatan harus tersedia\n';
                            isValid = false;
                        }

                        if (!villageId) {
                            errorMessage += 'Desa harus tersedia\n';
                            isValid = false;
                        }

                        if (!isValid) {
                            alert('Mohon lengkapi data berikut:\n\n' + errorMessage);
                            return false;
                        }

                        // If validation passes, submit the form
                        form.submit();
                    });
                });
            </script>
            <script>
                function toggleHistorySection() {
                    const historyContent = document.getElementById('historyContent');
                    const historyIcon = document.getElementById('historyIcon');
                    const historyText = document.getElementById('btnToggleHistoryText');
                    if (!historyContent) return;
                    const isHidden = historyContent.classList.contains('hidden');
                    if (isHidden) {
                        historyContent.classList.remove('hidden');
                        historyIcon.classList.remove('fa-history');
                        historyIcon.classList.add('fa-times');
                        historyText.textContent = 'Tutup History';
                    } else {
                        historyContent.classList.add('hidden');
                        historyIcon.classList.remove('fa-times');
                        historyIcon.classList.add('fa-history');
                        historyText.textContent = 'Lihat History';
                    }
                }
            </script>
    </div>
</x-layout>