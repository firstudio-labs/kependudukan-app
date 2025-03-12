<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Biodata</h1>

        <form method="POST" action="{{ route('superadmin.biodata.update', $citizen['data']['nik']) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_page" value="{{ request()->query('page', 1) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK (readonly) -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" id="nik" value="{{ $citizen['data']['nik'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" value="{{ $citizen['data']['kk'] }}" pattern="\d{16}" maxlength="16" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="{{ $citizen['data']['full_name'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1">Laki-Laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>

                <!-- Example of a date field -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ $citizen['data']['birth_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Example of a select field -->


                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                    <input type="number" id="age" name="age" value="{{ $citizen['data']['age'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ $citizen['data']['birth_place'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $citizen['data']['address'] }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $citizen['data']['province_id'] == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $citizen['data']['province_id'] }}">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten </label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $citizen['data']['district_id'] == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $citizen['data']['district_id'] }}">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan </label>
                    <select id="sub_district_code" name="sub_district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan </option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $citizen['data']['sub_district_id'] == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="sub_district_id" name="sub_district_id" value="{{ $citizen['data']['sub_district_id'] }}">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa</label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $citizen['data']['village_id'] == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $citizen['data']['village_id'] }}">
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" value="{{ $citizen['data']['rt'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" value="{{ $citizen['data']['rw'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code"
                        value="{{ $citizen['data']['postal_code'] && $citizen['data']['postal_code'] != '0' ? $citizen['data']['postal_code'] : '' }}"
                        pattern="\d{5}" maxlength="5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1">WNI</option>
                        <option value="2">WNA</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" value="{{ $citizen['data']['birth_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select id="blood_type" name="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1">A</option>
                        <option value="2">B</option>
                        <option value="3">AB</option>
                        <option value="4">O</option>
                        <option value="5">A+</option>
                        <option value="6">A-</option>
                        <option value="7">B+</option>
                        <option value="8">B-</option>
                        <option value="9">AB+</option>
                        <option value="10">AB-</option>
                        <option value="11">O+</option>
                        <option value="12">O-</option>
                        <option value="13">Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1">Islam</option>
                        <option value="2">Kristen</option>
                        <option value="3">Katholik</option>
                        <option value="4">Hindu</option>
                        <option value="5">Buddha</option>
                        <option value="6">Kong Hu Cu</option>
                        <option value="7">Lainnya</option>
                    </select>
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select id="marital_status" name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Belum Kawin</option>
                        <option value="2">Kawin Tercatat</option>
                        <option value="3">Kawin Belum Tercatat</option>
                        <option value="4">Cerai Hidup Tercatat</option>
                        <option value="5">Cerai Hidup Belum Tercatat</option>
                        <option value="6">Cerai Mati</option>
                    </select>
                </div>

                <!-- Akta Perkawinan -->
                <div>
                    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Perkawinan</label>
                    <select id="marital_certificate" name="marital_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perkawinan -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perkawinan</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no" value="{{ $citizen['data']['marital_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perkawinan -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Perkawinan</label>
                    <input type="date" id="marriage_date" name="marriage_date" value="{{ $citizen['data']['marriage_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
                <div>
                    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
                    <select id="divorce_certificate" name="divorce_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perceraian -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perceraian</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no" value="{{ $citizen['data']['divorce_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perceraian -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Perceraian</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date" value="{{ $citizen['data']['divorce_certificate_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>


                <!-- Status Hubungan Dalam Keluarga -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam Keluarga</label>
                    <select id="family_status" name="family_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1">ANAK</option>
                        <option value="2">KEPALA KELUARGA</option>
                        <option value="3">ISTRI</option>
                        <option value="4">ORANG TUA</option>
                        <option value="5">MERTUA</option>
                        <option value="6">CUCU</option>
                        <option value="7">FAMILI LAIN</option>
                    </select>
                </div>

                <!-- Kelainan Fisik dan Mental -->
                <div>
                    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan Mental</label>
                    <select id="mental_disorders" name="mental_disorders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Fisik</option>
                        <option value="2">Netra/Buta</option>
                        <option value="3">Rungu/Wicara</option>
                        <option value="4">Mental/Jiwa</option>
                        <option value="5">Fisik dan Mental</option>
                        <option value="6">Lainnya</option>
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                    <select id="education_status" name="education_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Tidak/Belum Sekolah</option>
                        <option value="2">Belum tamat SD/Sederajat</option>
                        <option value="3">Tamat SD</option>
                        <option value="4">SLTP/SMP/Sederajat</option>
                        <option value="5">SLTA/SMA/Sederajat</option>
                        <option value="6">Diploma I/II</option>
                        <option value="7">Akademi/Diploma III/ Sarjana Muda</option>
                        <option value="8">Diploma IV/ Strata I/ Strata II</option>
                        <option value="9">Strata III</option>
                        <option value="10">Lainnya</option>
                    </select>
                </div>

                <!-- Jenis Pekerjaan -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Jenis Pekerjaan</label>
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Pekerjaan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ $citizen['data']['job_type_id'] == $job['id'] ? 'selected' : '' }}>
                                {{ $job['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- NIK Ibu -->
                <div>
                    <label for="nik_mother" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                    <input type="text" id="nik_mother" name="nik_mother" value="{{ $citizen['data']['nik_mother'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ibu -->
                <div>
                    <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input type="text" id="mother" name="mother" value="{{ $citizen['data']['mother'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Ayah -->
                <div>
                    <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <input type="text" id="nik_father" name="nik_father" value="{{ $citizen['data']['nik_father'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ayah -->
                <div>
                    <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input type="text" id="father" name="father" value="{{ $citizen['data']['father'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tag Lokasi -->
                <div>
                    <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log, Lat)</label>
                    <input type="text" id="coordinate" name="coordinate" value="{{ $citizen['data']['coordinate'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Update
                </button>
            </div>
        </form>
    </div>


    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Store citizen data but remove console.log
        const citizenData = @json($citizen['data']);

        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements
            const provinceSelect = document.getElementById('province_code');
            const districtSelect = document.getElementById('district_code');
            const subDistrictSelect = document.getElementById('sub_district_code');
            const villageSelect = document.getElementById('village_code');

            // Hidden inputs for IDs
            const provinceIdInput = document.getElementById('province_id');
            const districtIdInput = document.getElementById('district_id');
            const subDistrictIdInput = document.getElementById('sub_district_id');
            const villageIdInput = document.getElementById('village_id');

            // Store fixed ID values that we already know from the page load
            const fixedProvinceId = "{{ $citizen['data']['province_id'] }}";
            const fixedDistrictId = "{{ $citizen['data']['district_id'] }}";
            const fixedSubDistrictId = "{{ $citizen['data']['sub_district_id'] }}";
            const fixedVillageId = "{{ $citizen['data']['village_id'] }}";

            // Helper function to reset select options
            function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
                if (hiddenInput) hiddenInput.value = '';
            }

            // Helper function to populate select options with code as value and id as data attribute
            function populateSelect(select, data, defaultText, selectedCode = null, hiddenInput = null, fixedId = null) {
                try {
                    const fragment = document.createDocumentFragment();
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = defaultText;
                    fragment.appendChild(defaultOption);

                    let foundSelected = false;

                    if (Array.isArray(data)) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.code;
                            option.setAttribute('data-id', item.id);
                            option.textContent = item.name;

                            // Check if this should be selected
                            // Either: 1. It matches the selectedCode OR 2. Its ID matches the fixedId
                            if ((selectedCode && item.code == selectedCode) || (fixedId && item.id == fixedId)) {
                                option.selected = true;
                                if (hiddenInput) hiddenInput.value = item.id;
                                foundSelected = true;
                            }

                            fragment.appendChild(option);
                        });
                    }

                    select.innerHTML = '';
                    select.appendChild(fragment);
                    select.disabled = false;

                    // If we're using a fixed ID but didn't find a match, make sure to set the hidden input
                    if (!foundSelected && fixedId && hiddenInput) {
                        hiddenInput.value = fixedId;
                    }

                    return foundSelected;
                } catch (error) {
                    console.error('Error populating select:', error);
                    select.innerHTML = `<option value="">Error loading data</option>`;
                    select.disabled = true;
                    return false;
                }
            }

            // Update hidden input when selection changes
            function updateHiddenInput(select, hiddenInput) {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    hiddenInput.value = selectedOption.getAttribute('data-id');
                }
            }

            // Load districts based on province code
            function loadDistricts(provinceCode) {
                return new Promise((resolve, reject) => {
                    if (!provinceCode) {
                        resetSelect(districtSelect, 'Pilih Kabupaten', districtIdInput);
                        resolve(false);
                        return;
                    }

                    resetSelect(districtSelect, 'Loading...', districtIdInput);

                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.length > 0) {
                                // Try to match using the fixed District ID we already know
                                const foundSelected = populateSelect(districtSelect, data, 'Pilih Kabupaten', null, districtIdInput, fixedDistrictId);
                                districtSelect.disabled = false;
                                resolve(foundSelected);
                            } else {
                                resetSelect(districtSelect, 'No data available', districtIdInput);
                                resolve(false);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching districts:', error);
                            resetSelect(districtSelect, 'Error loading data', districtIdInput);
                            reject(error);
                        });
                });
            }

            // Load sub-districts based on district code
            function loadSubDistricts(districtCode) {
                return new Promise((resolve, reject) => {
                    if (!districtCode) {
                        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                        resolve(false);
                        return;
                    }

                    resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);

                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.length > 0) {
                                // Try to match using the fixed Sub-District ID we already know
                                const foundSelected = populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', null, subDistrictIdInput, fixedSubDistrictId);
                                subDistrictSelect.disabled = false;
                                resolve(foundSelected);
                            } else {
                                resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                                resolve(false);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching sub-districts:', error);
                            resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                            reject(error);
                        });
                });
            }

            // Load villages based on sub-district code
            function loadVillages(subDistrictCode) {
                return new Promise((resolve, reject) => {
                    if (!subDistrictCode) {
                        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                        resolve(false);
                        return;
                    }

                    resetSelect(villageSelect, 'Loading...', villageIdInput);

                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.length > 0) {
                                // Try to match using the fixed Village ID we already know
                                const foundSelected = populateSelect(villageSelect, data, 'Pilih Desa', null, villageIdInput, fixedVillageId);
                                villageSelect.disabled = false;
                                resolve(foundSelected);
                            } else {
                                resetSelect(villageSelect, 'No data available', villageIdInput);
                                resolve(false);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching villages:', error);
                            resetSelect(villageSelect, 'Error loading data', villageIdInput);
                            reject(error);
                        });
                });
            }

            // Initialize location dropdowns with already selected values
            async function initializeLocations() {
                // First, make sure our hidden inputs have the right values from the existing citizen data
                provinceIdInput.value = fixedProvinceId;
                districtIdInput.value = fixedDistrictId;
                subDistrictIdInput.value = fixedSubDistrictId;
                villageIdInput.value = fixedVillageId;

                // If the province dropdown doesn't have a selected value with matching ID,
                // we need to find which province code corresponds to our province ID
                if (!provinceSelect.querySelector(`option[data-id="${fixedProvinceId}"]`)) {
                    // This is unlikely since we populate provinces server-side
                }

                // If we have a province code but no district data loaded, load districts
                if (provinceSelect.value) {
                    const districtFound = await loadDistricts(provinceSelect.value);

                    // If we found and selected the district, load sub-districts
                    if (districtFound && districtSelect.value) {
                        const subDistrictFound = await loadSubDistricts(districtSelect.value);

                        // If we found and selected the sub-district, load villages
                        if (subDistrictFound && subDistrictSelect.value) {
                            await loadVillages(subDistrictSelect.value);
                        }
                    }
                }
            }

            // Initialize locations on page load
            initializeLocations();

            // Province change handler
            provinceSelect.addEventListener('change', async function() {
                const provinceCode = this.value;

                // Update the hidden input with the ID
                updateHiddenInput(this, provinceIdInput);

                // Reset and load new districts
                await loadDistricts(provinceCode);

                // Reset sub-district and village
                resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
            });

            // District change handler
            districtSelect.addEventListener('change', async function() {
                const districtCode = this.value;

                // Update hidden input with ID
                updateHiddenInput(this, districtIdInput);

                // Reset and load new sub-districts
                await loadSubDistricts(districtCode);

                // Reset village
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
            });

            // Sub-district change handler
            subDistrictSelect.addEventListener('change', async function() {
                const subDistrictCode = this.value;

                // Update hidden input with ID
                updateHiddenInput(this, subDistrictIdInput);

                // Reset and load new villages
                await loadVillages(subDistrictCode);
            });

            // Village change handler
            villageSelect.addEventListener('change', function() {
                // Update hidden input with ID
                updateHiddenInput(this, villageIdInput);
            });

            // Format dates to YYYY-MM-DD for HTML date inputs
            function formatDateForInput(dateString) {
                if (!dateString || dateString === " " || dateString === "null") return "";

                // Check if the date is already in yyyy-MM-dd format
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                    return dateString;
                }

                try {
                    // Handle different possible date formats
                    let date;

                    // Check for dd/MM/yyyy format
                    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateString)) {
                        const parts = dateString.split('/');
                        date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
                    } else {
                        // Otherwise try to parse the date directly
                        date = new Date(dateString);
                    }

                    // Make sure the date is valid
                    if (isNaN(date.getTime())) {
                        return "";
                    }

                    // Format to YYYY-MM-DD
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                } catch (error) {
                    return "";
                }
            }

            // Apply date formatting to all date input fields
            function reformatAllDateInputs() {
                const dateInputs = document.querySelectorAll('input[type="date"]');
                dateInputs.forEach(input => {
                    const originalValue = input.getAttribute('value') || input.value;

                    if (originalValue && originalValue !== " ") {
                        const formattedDate = formatDateForInput(originalValue);
                        input.value = formattedDate;
                    }
                });

                // Specifically check these fields
                const dateFields = ['birth_date', 'marriage_date', 'divorce_certificate_date'];
                dateFields.forEach(fieldId => {
                    const input = document.getElementById(fieldId);
                    if (input) {
                        const originalValue = input.getAttribute('value') || input.value;

                        if (originalValue && originalValue !== " ") {
                            // For problematic dates, manually construct from parts if needed
                            if (/^\d{2}\/\d{2}\/\d{4}$/.test(originalValue)) {
                                const parts = originalValue.split('/');
                                const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                input.value = formattedDate;
                            } else {
                                const formattedDate = formatDateForInput(originalValue);
                                input.value = formattedDate;
                            }
                        }
                    }
                });
            }

            // Call date formatting after a short delay to ensure DOM is ready
            setTimeout(reformatAllDateInputs, 100);

            // Form validation to check both date formats and location IDs
            document.querySelector('form').addEventListener('submit', function(e) {
                // Check location IDs
                const provinceId = document.getElementById('province_id').value;
                const districtId = document.getElementById('district_id').value;
                const subDistrictId = document.getElementById('sub_district_id').value;
                const villageId = document.getElementById('village_id').value;

                if (!provinceId || !districtId || !subDistrictId || !villageId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa',
                    });
                    return false;
                }

                // Ensure all date fields are correctly formatted
                const dateInputs = document.querySelectorAll('input[type="date"]');
                let allDatesValid = true;

                dateInputs.forEach(input => {
                    if (input.value && !/^\d{4}-\d{2}-\d{2}$/.test(input.value)) {
                        // Try to fix it one last time
                        const fixedDate = formatDateForInput(input.value);
                        if (fixedDate && /^\d{4}-\d{2}-\d{2}$/.test(fixedDate)) {
                            input.value = fixedDate;
                        } else {
                            allDatesValid = false;
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Tanggal Salah',
                                text: `Format tanggal untuk "${input.id}" tidak valid. Format yang benar adalah YYYY-MM-DD.`,
                            });
                            return false;
                        }
                    }
                });

                if (!allDatesValid) {
                    e.preventDefault();
                    return false;
                }
            });

            // Function to directly force set select values - updated to handle both text and numeric values
            function setSelectValueDirectly(selectId, value) {
                if (value === undefined || value === null) return;

                const select = document.getElementById(selectId);
                if (!select) return;

                // Get the value type and make comparison accordingly
                const isNumeric = !isNaN(parseInt(value));

                // Value mapping for text to numeric conversion
                const valueMappings = {
                    'gender': { 'Laki-Laki': '1', 'laki-laki': '1', 'Perempuan': '2', 'perempuan': '2' },
                    'citizen_status': { 'WNI': '1', 'wni': '1', 'WNA': '2', 'wna': '2' },
                    'birth_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
                    'blood_type': { 'A': '1', 'B': '2', 'AB': '3', 'O': '4', 'A+': '5', 'A-': '6', 'B+': '7', 'B-': '8', 'AB+': '9', 'AB-': '10', 'O+': '11', 'O-': '12', 'Tidak Tahu': '13' },
                    'religion': { 'Islam': '1', 'islam': '1', 'Kristen': '2', 'kristen': '2', 'Katholik': '3', 'katholik': '3', 'katolik': '3', 'Hindu': '4', 'hindu': '4', 'Buddha': '5', 'buddha': '5', 'Budha': '5', 'budha': '5', 'Kong Hu Cu': '6', 'kong hu cu': '6', 'konghucu': '6', 'Lainnya': '7', 'lainnya': '7' },
                    'marital_status': { 'Belum Kawin': '1', 'belum kawin': '1', 'Kawin Tercatat': '2', 'kawin tercatat': '2', 'Kawin Belum Tercatat': '3', 'kawin belum tercatat': '3', 'Cerai Hidup Tercatat': '4', 'cerai hidup tercatat': '4', 'Cerai Hidup Belum Tercatat': '5', 'cerai hidup belum tercatat': '5', 'Cerai Mati': '6', 'cerai mati': '6' },
                    'marital_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
                    'divorce_certificate': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
                    'family_status': { 'ANAK': '1', 'Anak': '1', 'anak': '1', 'KEPALA KELUARGA': '2', 'Kepala Keluarga': '2', 'kepala keluarga': '2', 'ISTRI': '3', 'Istri': '3', 'istri': '3', 'ORANG TUA': '4', 'Orang Tua': '4', 'orang tua': '4', 'MERTUA': '5', 'Mertua': '5', 'mertua': '5', 'CUCU': '6', 'Cucu': '6', 'cucu': '6', 'FAMILI LAIN': '7', 'Famili Lain': '7', 'famili lain': '7' },
                    'mental_disorders': { 'Ada': '1', 'ada': '1', 'Tidak Ada': '2', 'tidak ada': '2' },
                    'disabilities': { 'Fisik': '1', 'fisik': '1', 'Netra/Buta': '2', 'netra/buta': '2', 'Rungu/Wicara': '3', 'rungu/wicara': '3', 'Mental/Jiwa': '4', 'mental/jiwa': '4', 'Fisik dan Mental': '5', 'fisik dan mental': '5', 'Lainnya': '6', 'lainnya': '6' },
                    'education_status': { 'Tidak/Belum Sekolah': '1', 'tidak/belum sekolah': '1', 'Belum tamat SD/Sederajat': '2', 'belum tamat sd/sederajat': '2', 'Tamat SD': '3', 'tamat sd': '3', 'SLTP/SMP/Sederajat': '4', 'sltp/smp/sederajat': '4', 'SLTA/SMA/Sederajat': '5', 'slta/sma/sederajat': '5', 'Diploma I/II': '6', 'diploma i/ii': '6', 'Akademi/Diploma III/ Sarjana Muda': '7', 'akademi/diploma iii/ sarjana muda': '7', 'Diploma IV/ Strata I/ Strata II': '8', 'diploma iv/ strata i/ strata ii': '8', 'Strata III': '9', 'strata iii': '9', 'Lainnya': '10', 'lainnya': '10' }
                };

                // Attempt conversion first
                let valueToUse = value;
                if (typeof value === 'string' && valueMappings[selectId]) {
                    const lowerValue = value.toLowerCase();
                    // Try to map the string value to a numeric value
                    for (const [key, val] of Object.entries(valueMappings[selectId])) {
                        if (key.toLowerCase() === lowerValue) {
                            valueToUse = val;
                            break;
                        }
                    }
                }

                // Method 1: Try to find the option with the exact value
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === String(valueToUse)) {
                        select.selectedIndex = i;
                        select.dispatchEvent(new Event('change'));
                        return true;
                    }
                }

                // Method 2: Try case-insensitive text content match
                if (typeof value === 'string') {
                    const lowerValue = value.toLowerCase();
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].textContent.toLowerCase() === lowerValue) {
                            select.selectedIndex = i;
                            select.dispatchEvent(new Event('change'));
                            return true;
                        }
                    }
                }

                // Method 3: For numeric values, try straight numeric comparison
                if (isNumeric) {
                    const numValue = parseInt(value);
                    for (let i = 0; i < select.options.length; i++) {
                        if (parseInt(select.options[i].value) === numValue) {
                            select.selectedIndex = i;
                            select.dispatchEvent(new Event('change'));
                            return true;
                        }
                    }
                }

                // If value is a number but stored as a string in the dropdown values
                if (isNumeric) {
                    const numValue = String(parseInt(value));
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === numValue) {
                            select.selectedIndex = i;
                            select.dispatchEvent(new Event('change'));
                            return true;
                        }
                    }
                }

                return false;
            }

            // Function to force set all form values from citizen data - removed console logs
            function forceSyncFormWithData() {
                // Define critical fields for selection
                const criticalFields = ['gender', 'citizen_status', 'birth_certificate', 'blood_type',
                                      'religion', 'marital_status', 'marital_certificate',
                                      'divorce_certificate', 'family_status', 'mental_disorders',
                                      'disabilities', 'education_status'];

                // Set all fields one by one
                setSelectValueDirectly('gender', citizenData.gender);
                setSelectValueDirectly('citizen_status', citizenData.citizen_status);
                setSelectValueDirectly('birth_certificate', citizenData.birth_certificate);
                setSelectValueDirectly('blood_type', citizenData.blood_type);
                setSelectValueDirectly('religion', citizenData.religion);
                setSelectValueDirectly('marital_status', citizenData.marital_status);
                setSelectValueDirectly('marital_certificate', citizenData.marital_certificate);
                setSelectValueDirectly('divorce_certificate', citizenData.divorce_certificate);
                setSelectValueDirectly('family_status', citizenData.family_status);
                setSelectValueDirectly('mental_disorders', citizenData.mental_disorders);
                setSelectValueDirectly('disabilities', citizenData.disabilities);
                setSelectValueDirectly('education_status', citizenData.education_status);
                setSelectValueDirectly('job_type_id', citizenData.job_type_id);
            }

            // Apply date formatting and force select values - increase timeout to ensure DOM is ready
            setTimeout(function() {
                // Format dates
                reformatAllDateInputs();

                // Force set select values from citizen data
                forceSyncFormWithData();
            }, 300); // Increased timeout to 300ms
        });
    </script>
</x-layout>
