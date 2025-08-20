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
                        <button type="button" onclick="toggleEditForm()" class="bg-[#4A47DC] text-white px-4 py-2 rounded">
                            Edit Biodata (Minta Approval)
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
                        <input name="gender" value="{{ $userData->citizen_data['gender'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
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
                        <input name="birth_date" value="{{ $userData->citizen_data['birth_date'] ?? '' }}" class="mt-1 w-full border rounded p-2" />
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
                        <input value="{{ $userData->citizen_data['province_name'] ?? ($userData->citizen_data['province'] ?? '') }}" class="mt-1 w-full border rounded p-2 bg-gray-100" readonly />
                        <input type="hidden" id="province_id" name="province_id" value="{{ $userData->citizen_data['province_id'] ?? '' }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Kabupaten</label>
                        <input value="{{ $userData->citizen_data['district_name'] ?? ($userData->citizen_data['district'] ?? $userData->citizen_data['city'] ?? '') }}" class="mt-1 w-full border rounded p-2 bg-gray-100" readonly />
                        <input type="hidden" id="district_id" name="district_id" value="{{ $userData->citizen_data['district_id'] ?? '' }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Kecamatan</label>
                        <input value="{{ $userData->citizen_data['sub_district_name'] ?? ($userData->citizen_data['sub_district'] ?? $userData->citizen_data['kecamatan'] ?? '') }}" class="mt-1 w-full border rounded p-2 bg-gray-100" readonly />
                        <input type="hidden" id="sub_district_id" name="sub_district_id" value="{{ $userData->citizen_data['sub_district_id'] ?? '' }}" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Desa</label>
                        <input value="{{ $userData->citizen_data['village_name'] ?? ($userData->citizen_data['village'] ?? '') }}" class="mt-1 w-full border rounded p-2 bg-gray-100" readonly />
                        <input type="hidden" id="village_id" name="village_id" value="{{ $userData->citizen_data['village_id'] ?? '' }}" />
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-3 mt-2">
                        <button type="button" onclick="toggleEditForm()" class="px-4 py-2 border rounded">Batal</button>
                        <button type="submit" class="bg-[#4A47DC] text-white px-4 py-2 rounded">Minta Persetujuan Admin</button>
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
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($userData->alamat)
                                        {{ $userData->alamat }}
                                    @elseif(isset($userData->citizen_data['address']))
                                        {{ $userData->citizen_data['address'] }}
                                    @else
                                        Belum diisi
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Provinsi</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="provinceDisplay">
                                    @if ($userData->provinsi)
                                        {{ $userData->provinsi->nama ?? $userData->provinsi->name }}
                                    @elseif(isset($userData->citizen_data['province']))
                                        {{ $userData->citizen_data['province'] }}
                                    @elseif(isset($userData->citizen_data['provinsi']))
                                        {{ $userData->citizen_data['provinsi'] }}
                                    @else
                                        <span class="text-gray-400">Memuat...</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kabupaten/Kota</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="districtDisplay">
                                    @if ($userData->kabupaten)
                                        {{ $userData->kabupaten->nama ?? $userData->kabupaten->name }}
                                    @elseif(isset($userData->citizen_data['city']))
                                        {{ $userData->citizen_data['city'] }}
                                    @elseif(isset($userData->citizen_data['kabupaten']))
                                        {{ $userData->citizen_data['kabupaten'] }}
                                    @elseif(isset($userData->citizen_data['district']))
                                        {{ $userData->citizen_data['district'] }}
                                    @else
                                        <span class="text-gray-400">Memuat...</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="subDistrictDisplay">
                                    @if ($userData->kecamatan)
                                        {{ $userData->kecamatan->nama ?? $userData->kecamatan->name }}
                                    @elseif(isset($userData->citizen_data['district']))
                                        {{ $userData->citizen_data['district'] }}
                                    @elseif(isset($userData->citizen_data['kecamatan']))
                                        {{ $userData->citizen_data['kecamatan'] }}
                                    @elseif(isset($userData->citizen_data['sub_district']))
                                        {{ $userData->citizen_data['sub_district'] }}
                                    @else
                                        <span class="text-gray-400">Memuat...</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kelurahan</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="villageDisplay">
                                    @if ($userData->kelurahan)
                                        {{ $userData->kelurahan->nama }}
                                    @elseif(isset($userData->citizen_data['village']))
                                        {{ $userData->citizen_data['village'] }}
                                    @else
                                        <span class="text-gray-400">Memuat...</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">RT/RW</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if (isset($userData->rt) && isset($userData->rw))
                                        {{ $userData->rt }}/{{ $userData->rw }}
                                    @elseif (isset($userData->rt_rw))
                                        {{ $userData->rt_rw }}
                                    @elseif (isset($userData->citizen_data['rt_rw']))
                                        {{ $userData->citizen_data['rt_rw'] }}
                                    @elseif (isset($userData->citizen_data['rt']) && isset($userData->citizen_data['rw']))
                                        {{ $userData->citizen_data['rt'] }}/{{ $userData->citizen_data['rw'] }}
                                    @else
                                        Belum diisi
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
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
                                // Configuration
                                const config = {
                                    baseUrl: 'https://api-kependudukan.desaverse.id/api',
                                    apiKey: '{{ config('services.kependudukan.key') }}',
                                    locationCache: {},
                                    locationIds: {
                                        provinceId: {{ isset($userData->citizen_data['province_id']) ? $userData->citizen_data['province_id'] : 'null' }},
                                        districtId: {{ isset($userData->citizen_data['district_id']) ? $userData->citizen_data['district_id'] : 'null' }},
                                        subDistrictId: {{ isset($userData->citizen_data['sub_district_id']) ? $userData->citizen_data['sub_district_id'] : 'null' }},
                                        villageId: {{ isset($userData->citizen_data['village_id']) ? $userData->citizen_data['village_id'] : 'null' }}
                                }
                                };


                                const api = {
                                    getHeaders() {
                                        return {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': config.apiKey
                                        };
                                    },

                                    async request(url) {
                                        try {
                                            const response = await axios.get(url, { headers: this.getHeaders() });
                                            return response.data?.data || [];
                                        } catch (error) {
                                            console.error(`API request error occurred`);
                                            return [];
                                        }
                                    }
                                };

                                const cache = {
                                    get(type, id) {
                                        const cacheKey = `${type}_${id}`;
                                        return config.locationCache[cacheKey] || null;
                                    },


                                    set(type, id, data) {
                                        const cacheKey = `${type}_${id}`;
                                        config.locationCache[cacheKey] = data;
                                    }
                                };


                                const locationService = {

                                    async getProvince(id) {
                                        if (!id) return null;

                                        const cachedData = cache.get('province', id);
                                        if (cachedData) return cachedData;

                                        const provinces = await api.request(`${config.baseUrl}/provinces`);
                                        const province = provinces.find(p => String(p.id) === String(id));

                                        if (province) {
                                            cache.set('province', id, province);
                                            return province;
                                        }

                                        return null;
                                    },


                                    async getDistrict(id) {
                                        if (!id) return null;

                                        const cachedData = cache.get('district', id);
                                        if (cachedData) return cachedData;

                                        const provinces = await api.request(`${config.baseUrl}/provinces`);

                                        for (const province of provinces) {
                                            try {
                                                const districts = await api.request(`${config.baseUrl}/districts/${province.code}`);
                                                const district = districts.find(d => String(d.id) === String(id));

                                                if (district) {
                                                    district.province = province;
                                                    cache.set('district', id, district);
                                                    return district;
                                                }
                                            } catch (e) {

                                            }
                                        }

                                        return null;
                                    },


                                    async getSubDistrict(id, parentDistrictId) {
                                        if (!id) return null;

                                        const cachedData = cache.get('subdistrict', id);
                                        if (cachedData) return cachedData;


                                        if (parentDistrictId) {
                                            const parentDistrict = await this.getDistrict(parentDistrictId);

                                            if (parentDistrict) {
                                                try {
                                                    const subdistricts = await api.request(
                                                        `${config.baseUrl}/sub-districts/${parentDistrict.code}`
                                                    );

                                                    const subdistrict = subdistricts.find(sd => String(sd.id) === String(id));

                                                    if (subdistrict) {
                                                        subdistrict.district = parentDistrict;
                                                        cache.set('subdistrict', id, subdistrict);
                                                        return subdistrict;
                                                    }
                                                } catch (e) {

                                                }
                                            }
                                        }


                                        const provinces = await api.request(`${config.baseUrl}/provinces`);

                                        for (const province of provinces) {
                                            try {
                                                const districts = await api.request(`${config.baseUrl}/districts/${province.code}`);

                                                for (const district of districts) {
                                                    try {
                                                        const subdistricts = await api.request(
                                                            `${config.baseUrl}/sub-districts/${district.code}`
                                                        );

                                                        const subdistrict = subdistricts.find(sd => String(sd.id) === String(id));

                                                        if (subdistrict) {
                                                            district.province = province;
                                                            subdistrict.district = district;
                                                            cache.set('subdistrict', id, subdistrict);
                                                            return subdistrict;
                                                        }
                                                    } catch (e) {

                                                    }
                                                }
                                            } catch (e) {

                                            }
                                        }

                                        return null;
                                    },

                                    async getVillage(id, parentSubDistrictId, parentDistrictId) {
                                        if (!id) return null;

                                        const cachedData = cache.get('village', id);
                                        if (cachedData) return cachedData;


                                        if (parentSubDistrictId) {
                                            const parentSubDistrict = await this.getSubDistrict(
                                                parentSubDistrictId,
                                                parentDistrictId
                                            );

                                            if (parentSubDistrict) {
                                                try {
                                                    const villages = await api.request(
                                                        `${config.baseUrl}/villages/${parentSubDistrict.code}`
                                                    );

                                                    const village = villages.find(v => String(v.id) === String(id));

                                                    if (village) {
                                                        village.subdistrict = parentSubDistrict;
                                                        cache.set('village', id, village);
                                                        return village;
                                                    }
                                                } catch (e) {

                                                }
                                            }
                                        }


                                        const provinces = await api.request(`${config.baseUrl}/provinces`);

                                        for (const province of provinces) {
                                            try {
                                                const districts = await api.request(`${config.baseUrl}/districts/${province.code}`);

                                                for (const district of districts) {
                                                    try {
                                                        const subdistricts = await api.request(
                                                            `${config.baseUrl}/sub-districts/${district.code}`
                                                        );

                                                        for (const subdistrict of subdistricts) {
                                                            try {
                                                                const villages = await api.request(
                                                                    `${config.baseUrl}/villages/${subdistrict.code}`
                                                                );

                                                                const village = villages.find(v => String(v.id) === String(id));

                                                                if (village) {
                                                                    district.province = province;
                                                                    subdistrict.district = district;
                                                                    village.subdistrict = subdistrict;
                                                                    cache.set('village', id, village);
                                                                    return village;
                                                                }
                                                            } catch (e) {

                                                            }
                                                        }
                                                    } catch (e) {

                                                    }
                                                }
                                            } catch (e) {

                                            }
                                        }

                                        return null;
                                    }
                                };

                                async function fetchLocationData(type, id) {
                                    if (!id) return null;

                                    try {
                                        switch (type) {
                                            case 'province':
                                                return await locationService.getProvince(id);
                                            case 'district':
                                                return await locationService.getDistrict(id);
                                            case 'subdistrict':
                                                const parentDistrictId = arguments[2];
                                                return await locationService.getSubDistrict(id, parentDistrictId);
                                            case 'village':
                                                const parentSubdistrictId = arguments[2];
                                                const parentDistForVillage = arguments[3];
                                                return await locationService.getVillage(id, parentSubdistrictId, parentDistForVillage);
                                            default:
                                                console.error(`Unknown location type: ${type}`);
                                                return null;
                                        }
                                    } catch (error) {
                                        console.error(`Error fetching location data`);
                                        return null;
                                    }
                                }

                                async function updateLocationDisplays() {
                                    const elements = {
                                        province: document.getElementById('provinceDisplay'),
                                        district: document.getElementById('districtDisplay'),
                                        subDistrict: document.getElementById('subDistrictDisplay'),
                                        village: document.getElementById('villageDisplay')
                                    };

                                    let updatesNeeded = 0;
                                    let updatesCompleted = 0;

                                    async function updateElement(element, type, id, ...parentIds) {
                                        if (!element || !element.innerText.includes('Memuat') || !id) {
                                            return;
                                        }

                                        updatesNeeded++;

                                        try {
                                            const data = await fetchLocationData(type, id, ...parentIds);

                                            if (data) {
                                                element.innerText = data.name;
                                            } else {
                                                element.innerText = 'Data tidak tersedia';
                                            }
                                        } catch (error) {
                                            element.innerText = 'Gagal memuat data';
                                            console.error(`Failed to load location data`);
                                        }

                                        updatesCompleted++;
                                    }

                                    await Promise.all([
                                        updateElement(elements.province, 'province', config.locationIds.provinceId),
                                        updateElement(elements.district, 'district', config.locationIds.districtId),
                                        updateElement(
                                            elements.subDistrict,
                                            'subdistrict',
                                            config.locationIds.subDistrictId,
                                            config.locationIds.districtId
                                        ),
                                        updateElement(
                                            elements.village,
                                            'village',
                                            config.locationIds.villageId,
                                            config.locationIds.subDistrictId,
                                            config.locationIds.districtId
                                        )
                                    ]);

                                }


                                function initialize() {
                                    if (typeof axios !== 'undefined') {

                                        updateLocationDisplays();
                                    } else {
                                        console.error('Axios is not loaded. Location data cannot be fetched dynamically.');
                                        const script = document.createElement('script');
                                        script.src = 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';
                                        script.onload = function () {
                                            console.log('Axios loaded dynamically, now updating location data...');
                                            updateLocationDisplays();
                                        };
                                        document.head.appendChild(script);
                                    }
                                }

                                initialize();
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
                    if (!el) return;
                    if (el.classList.contains('hidden')) {
                        el.classList.remove('hidden');
                        el.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        el.classList.add('hidden');
                    }
                }
            </script>
            <script src="{{ asset('js/location-selector-edit.js') }}"></script>
    </div>
</x-layout>