<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Keterangan Ahli Waris</h1>

        <form method="POST" action="{{ route('superadmin.surat.ahli-waris.update', $ahliWaris->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama Ahli Waris -->
                <div>
                    <label for="heir_name" class="block text-sm font-medium text-gray-700">Nama Ahli Waris <span class="text-red-500">*</span></label>
                    <input type="text" id="heir_name" name="heir_name" value="{{ $ahliWaris->heir_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Almarhum -->
                <div>
                    <label for="deceased_name" class="block text-sm font-medium text-gray-700">Nama Almarhum <span class="text-red-500">*</span></label>
                    <input type="text" id="deceased_name" name="deceased_name" value="{{ $ahliWaris->deceased_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Meninggal -->
                <div>
                    <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Meninggal <span class="text-red-500">*</span></label>
                    <input type="text" id="death_place" name="death_place" value="{{ $ahliWaris->death_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Meninggal -->
                <div>
                    <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                    <input type="date" id="death_date" name="death_date" value="{{ date('Y-m-d', strtotime($ahliWaris->death_date)) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nomor Akte Kematian -->
                <div>
                    <label for="death_certificate_number" class="block text-sm font-medium text-gray-700">Nomor Akte Kematian</label>
                    <input type="number" id="death_certificate_number" name="death_certificate_number" value="{{ $ahliWaris->death_certificate_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Akte Kematian -->
                <div>
                    <label for="death_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Akte Kematian</label>
                    <input type="date" id="death_certificate_date" name="death_certificate_date" value="{{ $ahliWaris->death_certificate_date ? date('Y-m-d', strtotime($ahliWaris->death_certificate_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Surat Waris -->
                <div>
                    <label for="inheritance_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat Waris</label>
                    <input type="date" id="inheritance_letter_date" name="inheritance_letter_date" value="{{ $ahliWaris->inheritance_letter_date ? date('Y-m-d', strtotime($ahliWaris->inheritance_letter_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Jenis Warisan -->
                <div>
                    <label for="inheritance_type" class="block text-sm font-medium text-gray-700">Jenis Warisan <span class="text-red-500">*</span></label>
                    <input type="text" id="inheritance_type" name="inheritance_type" value="{{ $ahliWaris->inheritance_type }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="number" id="letter_number" name="letter_number" value="{{ $ahliWaris->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $ahliWaris->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Provinsi section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $ahliWaris->province_id == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $ahliWaris->province_id }}">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $ahliWaris->district_id == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $ahliWaris->district_id }}">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $ahliWaris->subdistrict_id == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $ahliWaris->subdistrict_id }}">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $ahliWaris->village_id == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $ahliWaris->village_id }}">
                </div>
            </div>

            <!-- Daftar Ahli Waris Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Daftar Ahli Waris</h2>
                <div id="heirs-container">
                    @php
                        // Helper function to safely get array data from potentially JSON strings
                        function safeJsonDecode($value) {
                            if (is_array($value)) {
                                return $value;
                            } elseif (is_string($value)) {
                                return json_decode($value, true) ?? [];
                            } else {
                                return [];
                            }
                        }

                        // Helper function to format date to YYYY-MM-DD
                        function formatDate($date) {
                            if (empty($date)) return '';
                            return date('Y-m-d', strtotime($date));
                        }

                        $niks = safeJsonDecode($ahliWaris->nik);
                        $fullNames = safeJsonDecode($ahliWaris->full_name);
                        $birthPlaces = safeJsonDecode($ahliWaris->birth_place);
                        $birthDates = safeJsonDecode($ahliWaris->birth_date);
                        $genders = safeJsonDecode($ahliWaris->gender);
                        $religions = safeJsonDecode($ahliWaris->religion);
                        $addresses = safeJsonDecode($ahliWaris->address);
                        $familyStatuses = safeJsonDecode($ahliWaris->family_status);
                        $heirCount = count($niks);
                    @endphp

                    @for ($i = 0; $i < $heirCount; $i++)
                    <div class="heir-row border p-4 rounded-md mb-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                <select name="nik[]" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="{{ $niks[$i] ?? '' }}">{{ $niks[$i] ?? 'Pilih NIK' }}</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <select name="full_name[]" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="{{ $fullNames[$i] ?? '' }}">{{ $fullNames[$i] ?? 'Pilih Nama' }}</option>
                                </select>
                            </div>

                            <!-- Hubungan Keluarga -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hubungan Keluarga <span class="text-red-500">*</span></label>
                                <select name="family_status[]" class="family-status mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Hubungan</option>
                                    <option value="1" {{ ($familyStatuses[$i] ?? '') == 1 ? 'selected' : '' }}>ANAK</option>
                                    <option value="2" {{ ($familyStatuses[$i] ?? '') == 2 ? 'selected' : '' }}>KEPALA KELUARGA</option>
                                    <option value="3" {{ ($familyStatuses[$i] ?? '') == 3 ? 'selected' : '' }}>ISTRI</option>
                                    <option value="4" {{ ($familyStatuses[$i] ?? '') == 4 ? 'selected' : '' }}>ORANG TUA</option>
                                    <option value="5" {{ ($familyStatuses[$i] ?? '') == 5 ? 'selected' : '' }}>MERTUA</option>
                                    <option value="6" {{ ($familyStatuses[$i] ?? '') == 6 ? 'selected' : '' }}>CUCU</option>
                                    <option value="7" {{ ($familyStatuses[$i] ?? '') == 7 ? 'selected' : '' }}>FAMILI LAIN</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <!-- Tempat Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="birth_place[]" value="{{ $birthPlaces[$i] ?? '' }}" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date[]" value="{{ isset($birthDates[$i]) ? formatDate($birthDates[$i]) : '' }}" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender[]" class="gender mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="1" {{ ($genders[$i] ?? '') == 1 ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="2" {{ ($genders[$i] ?? '') == 2 ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <!-- Agama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                                <select name="religion[]" class="religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="1" {{ ($religions[$i] ?? '') == 1 ? 'selected' : '' }}>Islam</option>
                                    <option value="2" {{ ($religions[$i] ?? '') == 2 ? 'selected' : '' }}>Kristen</option>
                                    <option value="3" {{ ($religions[$i] ?? '') == 3 ? 'selected' : '' }}>Katholik</option>
                                    <option value="4" {{ ($religions[$i] ?? '') == 4 ? 'selected' : '' }}>Hindu</option>
                                    <option value="5" {{ ($religions[$i] ?? '') == 5 ? 'selected' : '' }}>Buddha</option>
                                    <option value="6" {{ ($religions[$i] ?? '') == 6 ? 'selected' : '' }}>Kong Hu Cu</option>
                                    <option value="7" {{ ($religions[$i] ?? '') == 7 ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="address[]" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $addresses[$i] ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button type="button" class="remove-heir bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="mt-4">
                    <button type="button" id="add-heir" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#7886C7]">
                        Tambah Ahli Waris
                    </button>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Perbarui
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

        document.addEventListener('DOMContentLoaded', function() {
            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Load all citizens first before initializing heir rows
            $.ajax({
                url: '{{ route("citizens.administrasi") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    limit: 10000 // Increase limit to load more citizens at once
                },
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    // Transform the response to match what we expect
                    let processedData = data;
                    if (data && data.data && Array.isArray(data.data)) {
                        processedData = data.data;
                    } else if (data && Array.isArray(data)) {
                        processedData = data;
                    }

                    // Make sure we have valid data
                    if (!Array.isArray(processedData)) {
                        console.error('Invalid citizen data format');
                        return;
                    }

                    allCitizens = processedData;
                    // Remove or comment the console.log message
                    // console.log(`Loaded ${allCitizens.length} citizens`);

                    // Now setup the heirs interface
                    setupHeirsInterface();
                },
                error: function(error) {
                    console.error('Failed to load citizen data:', error);
                    // Setup the heirs interface anyway with empty data
                    setupHeirsInterface();
                }
            });

            function setupHeirsInterface() {
                // Province/district/subdistrict/village cascading selects
                const provinceSelect = document.getElementById('province_code');
                const districtSelect = document.getElementById('district_code');
                const subDistrictSelect = document.getElementById('subdistrict_code');
                const villageSelect = document.getElementById('village_code');

                // Hidden inputs for IDs
                const provinceIdInput = document.getElementById('province_id');
                const districtIdInput = document.getElementById('district_id');
                const subDistrictIdInput = document.getElementById('subdistrict_id');
                const villageIdInput = document.getElementById('village_id');

                // Store original values to repopulate dropdowns
                const originalProvinceId = "{{ $ahliWaris->province_id }}";
                const originalDistrictId = "{{ $ahliWaris->district_id }}";
                const originalSubdistrictId = "{{ $ahliWaris->subdistrict_id }}";
                const originalVillageId = "{{ $ahliWaris->village_id }}";

                // Helper function to reset select options
                function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
                    select.innerHTML = `<option value="">${defaultText}</option>`;
                    select.disabled = true;
                    if (hiddenInput) hiddenInput.value = '';
                }

                // Helper function to populate select options
                function populateSelect(select, data, defaultText, hiddenInput = null, selectedId = null) {
                    try {
                        select.innerHTML = `<option value="">${defaultText}</option>`;

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.code;
                            option.textContent = item.name;
                            option.setAttribute('data-id', item.id);
                            if (selectedId && item.id.toString() === selectedId.toString()) {
                                option.selected = true;
                                if (hiddenInput) hiddenInput.value = item.id;
                            }
                            select.appendChild(option);
                        });

                        select.disabled = false;
                    } catch (error) {
                        console.error('Error populating select:', error);
                        select.innerHTML = `<option value="">Error loading data</option>`;
                        select.disabled = true;
                        if (hiddenInput) hiddenInput.value = '';
                    }
                }

                // Update hidden input when selection changes
                function updateHiddenInput(select, hiddenInput) {
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.hasAttribute('data-id')) {
                        hiddenInput.value = selectedOption.getAttribute('data-id');
                    } else {
                        hiddenInput.value = '';
                    }
                }

                // Load districts for a province
                function loadDistricts(provinceCode, selectedId = null) {
                    resetSelect(districtSelect, 'Loading...', districtIdInput);
                    resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                    if (provinceCode) {
                        fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput, selectedId);
                                    districtSelect.disabled = false;

                                    // If we have a district selected, load its subdistricts
                                    const selectedDistrictOption = [...districtSelect.options].find(option =>
                                        option.getAttribute('data-id') === selectedId);

                                    if (selectedDistrictOption) {
                                        loadSubdistricts(selectedDistrictOption.value, originalSubdistrictId);
                                    }
                                } else {
                                    resetSelect(districtSelect, 'No data available', districtIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(districtSelect, 'Error loading data', districtIdInput);
                            });
                    }
                }

                // Load subdistricts for a district
                function loadSubdistricts(districtCode, selectedId = null) {
                    resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                    if (districtCode) {
                        fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput, selectedId);
                                    subDistrictSelect.disabled = false;

                                    // If we have a subdistrict selected, load its villages
                                    const selectedSubdistrictOption = [...subDistrictSelect.options].find(option =>
                                        option.getAttribute('data-id') === selectedId);

                                    if (selectedSubdistrictOption) {
                                        loadVillages(selectedSubdistrictOption.value, originalVillageId);
                                    }
                                } else {
                                    resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                            });
                    }
                }

                // Load villages for a subdistrict
                function loadVillages(subDistrictCode, selectedId = null) {
                    resetSelect(villageSelect, 'Loading...', villageIdInput);

                    if (subDistrictCode) {
                        fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput, selectedId);
                                    villageSelect.disabled = false;
                                } else {
                                    resetSelect(villageSelect, 'No data available', villageIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(villageSelect, 'Error loading data', villageIdInput);
                            });
                    }
                }

                // Province change handler
                provinceSelect.addEventListener('change', function() {
                    const provinceCode = this.value;
                    // Update the hidden input with the ID
                    updateHiddenInput(this, provinceIdInput);

                    // Load districts for the selected province
                    if (provinceCode) {
                        loadDistricts(provinceCode);
                    } else {
                        resetSelect(districtSelect, 'Pilih Kabupaten', districtIdInput);
                        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                    }
                });

                // District change handler
                districtSelect.addEventListener('change', function() {
                    const districtCode = this.value;
                    // Update hidden input with ID
                    updateHiddenInput(this, districtIdInput);

                    // Load subdistricts for the selected district
                    if (districtCode) {
                        loadSubdistricts(districtCode);
                    } else {
                        resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                    }
                });

                // Sub-district change handler
                subDistrictSelect.addEventListener('change', function() {
                    const subDistrictCode = this.value;
                    // Update hidden input with ID
                    updateHiddenInput(this, subDistrictIdInput);

                    // Load villages for the selected subdistrict
                    if (subDistrictCode) {
                        loadVillages(subDistrictCode);
                    } else {
                        resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                    }
                });

                // Village change handler
                villageSelect.addEventListener('change', function() {
                    // Update hidden input with ID
                    updateHiddenInput(this, villageIdInput);
                });

                // Handle adding more heirs
                const heirsContainer = document.getElementById('heirs-container');
                const addHeirButton = document.getElementById('add-heir');

                // Clone an empty heir row to use as template for new rows
                let heirTemplate;
                if (document.querySelector('.heir-row')) {
                    heirTemplate = document.querySelector('.heir-row').cloneNode(true);

                    // Reset the template values for new rows
                    const inputs = heirTemplate.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.type === 'text' || input.type === 'number' || input.type === 'date' || input.tagName === 'TEXTAREA') {
                            input.value = '';
                        } else if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        }
                    });
                }

                // Process citizens for Select2
                function prepareCitizenOptions() {
                    const nikOptions = [];
                    const nameOptions = [];

                    allCitizens.forEach(citizen => {
                        // Handle cases where NIK might be coming from various fields
                        let nikValue = null;

                        if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                            nikValue = citizen.nik;
                        } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                            nikValue = citizen.id;
                        }

                        if (nikValue !== null) {
                            const nikString = nikValue.toString();
                            nikOptions.push({
                                id: nikString,
                                text: nikString,
                                citizen: citizen
                            });
                        }

                        // Only add if full_name is available
                        if (citizen.full_name) {
                            nameOptions.push({
                                id: citizen.full_name,
                                text: citizen.full_name,
                                citizen: citizen
                            });
                        }
                    });

                    return { nikOptions, nameOptions };
                }

                // Function to initialize Select2 on an heir row
                function initializeHeirSelect2(heirRow) {
                    const { nikOptions, nameOptions } = prepareCitizenOptions();
                    const nikSelect = heirRow.querySelector('.nik-select');
                    const nameSelect = heirRow.querySelector('.fullname-select');

                    // Get current values to preserve them
                    const currentNik = nikSelect.value;
                    const currentName = nameSelect.value;

                    // Track if we're in the middle of an update to prevent recursion
                    let isUpdating = false;

                    // Initialize NIK Select2 with pre-loaded data
                    $(nikSelect).select2({
                        placeholder: 'Pilih NIK',
                        width: '100%',
                        data: nikOptions,
                        language: {
                            noResults: function() {
                                return 'Tidak ada data yang ditemukan';
                            },
                            searching: function() {
                                return 'Mencari...';
                            }
                        },
                        escapeMarkup: function(markup) {
                            return markup;
                        }
                    }).on("select2:open", function() {
                        $('.select2-results__options').css('max-height', '400px');
                    });

                    // If there was a value, set it
                    if (currentNik) {
                        if (nikOptions.some(option => option.id === currentNik)) {
                            $(nikSelect).val(currentNik).trigger('change');
                        } else {
                            // If NIK not found in options, create a new option
                            const newOption = new Option(currentNik, currentNik, true, true);
                            $(nikSelect).append(newOption).trigger('change');
                        }
                    }

                    // Initialize Name Select2 with pre-loaded data
                    $(nameSelect).select2({
                        placeholder: 'Pilih Nama',
                        width: '100%',
                        data: nameOptions,
                        language: {
                            noResults: function() {
                                return 'Tidak ada data yang ditemukan';
                            },
                            searching: function() {
                                return 'Mencari...';
                            }
                        },
                        escapeMarkup: function(markup) {
                            return markup;
                        }
                    }).on("select2:open", function() {
                        $('.select2-results__options').css('max-height', '400px');
                    });

                    // If there was a value, set it
                    if (currentName) {
                        if (nameOptions.some(option => option.id === currentName)) {
                            $(nameSelect).val(currentName).trigger('change');
                        } else {
                            // If name not found in options, create a new option
                            const newOption = new Option(currentName, currentName, true, true);
                            $(nameSelect).append(newOption).trigger('change');
                        }
                    }

                    // NIK select change handler
                    $(nikSelect).on('select2:select', function(e) {
                        if (isUpdating) return;
                        isUpdating = true;

                        const citizen = e.params.data.citizen;
                        if (citizen) {
                            const row = $(this).closest('.heir-row');

                            // Update full name
                            $(row).find('.fullname-select').val(citizen.full_name).trigger('change.select2');

                            // Fill personal data fields - only personal info
                            populateHeirFieldsFromCitizen(row, citizen);
                        }

                        isUpdating = false;
                    });

                    // Full name select change handler
                    $(nameSelect).on('select2:select', function(e) {
                        if (isUpdating) return;
                        isUpdating = true;

                        const citizen = e.params.data.citizen;
                        if (citizen) {
                            const row = $(this).closest('.heir-row');

                            // Update NIK
                            if (citizen.nik) {
                                $(row).find('.nik-select').val(citizen.nik.toString()).trigger('change.select2');
                            }

                            // Fill personal data fields - only personal info
                            populateHeirFieldsFromCitizen(row, citizen);
                        }

                        isUpdating = false;
                    });
                }

                // Function to populate heir fields from citizen data
                function populateHeirFieldsFromCitizen(row, citizen) {
                    // Birth place
                    $(row).find('.birth-place').val(citizen.birth_place || '');

                    // Birth date - handle formatting
                    if (citizen.birth_date) {
                        let birthDate = citizen.birth_date;
                        if (birthDate.includes('/')) {
                            const [day, month, year] = birthDate.split('/');
                            birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        }
                        $(row).find('.birth-date').val(birthDate);
                    }

                    // Gender - handle conversion
                    let gender = citizen.gender;
                    if (typeof gender === 'string') {
                        if (gender.toLowerCase() === 'laki-laki') {
                            gender = 1;
                        } else if (gender.toLowerCase() === 'perempuan') {
                            gender = 2;
                        }
                    }
                    $(row).find('.gender').val(gender).trigger('change');

                    // Religion - handle conversion
                    let religion = citizen.religion;
                    if (typeof religion === 'string') {
                        const religionMap = {
                            'islam': 1,
                            'kristen': 2,
                            'katholik': 3,
                            'hindu': 4,
                            'buddha': 5,
                            'kong hu cu': 6,
                            'lainnya': 7
                        };
                        religion = religionMap[religion.toLowerCase()] || '';
                    }
                    $(row).find('.religion').val(religion).trigger('change');

                    // Address
                    $(row).find('.address').val(citizen.address || '');

                    // Family Status - handle numeric values from API
                    if (citizen.family_status !== undefined && citizen.family_status !== null) {
                        let familyStatusValue = citizen.family_status;

                        // If it's a string that contains a number, convert it to number
                        if (typeof familyStatusValue === 'string' && !isNaN(parseInt(familyStatusValue))) {
                            familyStatusValue = parseInt(familyStatusValue);
                        }
                        // If it's a string with text, try to map it to corresponding number
                        else if (typeof familyStatusValue === 'string') {
                            const statusMap = {
                                'anak': 1,
                                'kepala keluarga': 2,
                                'istri': 3,
                                'orang tua': 4,
                                'mertua': 5,
                                'cucu': 6,
                                'famili lain': 7
                            };

                            const normalizedStatus = familyStatusValue.toLowerCase().trim();
                            if (statusMap[normalizedStatus] !== undefined) {
                                familyStatusValue = statusMap[normalizedStatus];
                            }
                        }

                        // Set the numeric value in the dropdown
                        if (!isNaN(familyStatusValue) && familyStatusValue > 0) {
                            $(row).find('select[name="family_status[]"]').val(familyStatusValue).trigger('change');
                        }
                    }
                }

                // Function to add a new heir row
                function addHeirRow() {
                    const heirRowClone = heirTemplate.cloneNode(true);
                    heirsContainer.appendChild(heirRowClone);

                    // Initialize Select2 on the new row
                    initializeHeirSelect2(heirRowClone);

                    // Remove heir button functionality
                    heirRowClone.querySelector('.remove-heir').addEventListener('click', function() {
                        if (document.querySelectorAll('#heirs-container .heir-row').length > 1) {
                            this.closest('.heir-row').remove();
                        } else {
                            alert('Minimal harus ada satu ahli waris');
                        }
                    });
                }

                // Initialize existing heir rows with Select2
                const existingHeirRows = document.querySelectorAll('#heirs-container .heir-row');
                existingHeirRows.forEach(row => {
                    initializeHeirSelect2(row);

                    // Add remove button handler
                    row.querySelector('.remove-heir').addEventListener('click', function() {
                        if (document.querySelectorAll('#heirs-container .heir-row').length > 1) {
                            this.closest('.heir-row').remove();
                        } else {
                            alert('Minimal harus ada satu ahli waris');
                        }
                    });
                });

                // Add heir button click handler
                addHeirButton.addEventListener('click', addHeirRow);

                // Form validation for required fields and location data
                document.querySelector('form').addEventListener('submit', function(e) {
                    // Check required fields
                    const requiredFields = document.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value) {
                            isValid = false;
                            field.classList.add('border-red-500');
                        } else {
                            field.classList.remove('border-red-500');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        alert('Semua bidang yang bertanda * wajib diisi');
                        return false;
                    }

                    const provinceId = document.getElementById('province_id').value;
                    const districtId = document.getElementById('district_id').value;
                    const subDistrictId = document.getElementById('subdistrict_id').value;
                    const villageId = document.getElementById('village_id').value;

                    if (!provinceId || !districtId || !subDistrictId || !villageId) {
                        e.preventDefault();
                        alert('Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa');
                        return false;
                    }

                    // Check if at least one heir is added
                    const heirRows = document.querySelectorAll('#heirs-container .heir-row');
                    if (heirRows.length === 0) {
                        e.preventDefault();
                        alert('Harap menambahkan setidaknya satu ahli waris');
                        return false;
                    }
                });

                // Initialize dropdowns with existing location data on page load
                // Find the selected province option and get its code value
                const selectedProvinceOption = [...provinceSelect.options].find(option =>
                    option.getAttribute('data-id') === originalProvinceId);

                if (selectedProvinceOption) {
                    // Load the district data for this province
                    loadDistricts(selectedProvinceOption.value, originalDistrictId);
                }
            }
        });
    </script>
</x-layout>

