<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Pengantar KTP</h1>

        <form method="POST" action="{{ route('superadmin.surat.pengantar-ktp.update', $ktp->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $province['id'] == $ktp->province_id ? 'selected' : '' }}>{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $ktp->province_id }}">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $district['id'] == $ktp->district_id ? 'selected' : '' }}>{{ $district['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $ktp->district_id }}">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $subDistrict['id'] == $ktp->subdistrict_id ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $ktp->subdistrict_id }}">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $village['id'] == $ktp->village_id ? 'selected' : '' }}>{{ $village['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $ktp->village_id }}">
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="text" id="letter_number" name="letter_number" value="{{ $ktp->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $ktp->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Jenis Permohonan -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Jenis Permohonan</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div>
                        <label for="application_type" class="block text-sm font-medium text-gray-700">Permohonan KTP <span class="text-red-500">*</span></label>
                        <select id="application_type" name="application_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Permohonan</option>
                            <option value="Baru" {{ $ktp->application_type == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Perpanjang" {{ $ktp->application_type == 'Perpanjang' ? 'selected' : '' }}>Perpanjang</option>
                            <option value="Pergantian" {{ $ktp->application_type == 'Pergantian' ? 'selected' : '' }}>Pergantian</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pemohon KTP -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-semibold text-gray-700">Data Pemohon</h2>
                </div>

                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK with Search -->
                        <div>
                            <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nikSelect" name="nik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih NIK</option>
                                <option value="{{ $ktp->nik }}" selected>{{ $ktp->nik }}</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap with Search -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama Lengkap</option>
                                <option value="{{ $ktp->full_name }}" selected>{{ $ktp->full_name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KK dan Alamat Information -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Kartu Keluarga & Alamat</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Kartu Keluarga - now a simple text input -->
                        <div>
                            <label for="kk" class="block text-sm font-medium text-gray-700">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                            <input type="number" id="kk" name="kk" value="{{ $ktp->kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $ktp->address }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- RT -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" value="{{ $ktp->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RW -->
                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input type="text" id="rw" name="rw" value="{{ $ktp->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Dusun -->
                        <div>
                            <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun <span class="text-red-500">*</span></label>
                            <input type="text" id="hamlet" name="hamlet" value="{{ $ktp->hamlet }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Changed: Kecamatan as select that will be populated from API -->
                        <div>
                            <label for="sub_district_selector" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <select id="sub_district_selector"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                                required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach(isset($addressSubDistricts) ? $addressSubDistricts : $subDistricts as $subDistrict)
                                    <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $subDistrict['id'] == $ktp->subdistrict_name ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                                @endforeach
                            </select>
                            <!-- Hidden field to store sub_district ID -->
                            <input type="hidden" id="sub_district_id_hidden" value="{{ $ktp->subdistrict_name }}">
                            <!-- This is the field that will be submitted with the form -->
                            <input type="hidden" name="subdistrict_name" id="subdistrict_name_hidden" value="{{ $ktp->subdistrict_name }}">
                        </div>

                        <div>
                            <label for="village_selector" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                            <select id="village_selector"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                                required>
                                <option value="">Pilih Desa/Kelurahan</option>
                                @foreach(isset($addressVillages) ? $addressVillages : $villages as $village)
                                    <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $village['id'] == $ktp->village_name ? 'selected' : '' }}>{{ $village['name'] }}</option>
                                @endforeach
                            </select>
                            <!-- Hidden field to store village ID -->
                            <input type="hidden" id="village_id_hidden" value="{{ $ktp->village_name }}">
                            <!-- This is the field that will be submitted with the form -->
                            <input type="hidden" name="village_name" id="village_name_hidden" value="{{ $ktp->village_name }}">
                        </div>
                    </div>
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
            // Define isUpdating in the global scope so all handlers can access it
            let isUpdating = false;

            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Create mapping objects to convert between IDs and codes
            let provinceCodeMap = {};
            let districtCodeMap = {};
            let subDistrictCodeMap = {};
            let villageCodeMap = {};

            // Reverse maps to get ID from code
            let provinceIdMap = {};
            let districtIdMap = {};
            let subDistrictIdMap = {};
            let villageIdMap = {};

            // Initialize Select2 for NIK and Full Name selects
            $('#nikSelect').select2({
                placeholder: 'Pilih NIK',
                width: '100%',
                language: {
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            $('#fullNameSelect').select2({
                placeholder: 'Pilih Nama Lengkap',
                width: '100%',
                language: {
                    noResults: function() {
                        return 'Tidak ada data yang ditemukan';
                    },
                    searching: function() {
                        return 'Mencari...';
                    }
                }
            });

            // Function to initialize the address section selectors with correct values
            async function initializeAddressSelectors() {
                try {
                    // Get the subdistrict and village IDs from the database record
                    const subDistrictId = {{ $ktp->subdistrict_name ?? 'null' }};
                    const villageId = {{ $ktp->village_name ?? 'null' }};

                    if (!subDistrictId) return;

                    // First, we need to find the district code from the top form
                    const districtId = {{ $ktp->district_id ?? 'null' }};
                    if (!districtId) return;

                    // Get the province data to find the province code
                    const provinceId = {{ $ktp->province_id ?? 'null' }};
                    if (!provinceId) return;

                    // Load province mappings if not already loaded
                    if (Object.keys(provinceCodeMap).length === 0) {
                        await loadProvinceCodeMap();
                    }

                    // Get province code
                    const provinceCode = provinceCodeMap[provinceId];
                    if (!provinceCode) {
                        console.warn('Could not find province code for ID:', provinceId);
                        return;
                    }

                    // Load district data for this province
                    const districts = await loadDistrictCodeMap(provinceCode);
                    if (!districts || districts.length === 0) {
                        console.warn('No districts found for province code:', provinceCode);
                        return;
                    }

                    // Find district code
                    const districtData = districts.find(d => d.id === districtId);
                    if (!districtData) {
                        console.warn('Could not find district data for ID:', districtId);
                        return;
                    }

                    const districtCode = districtData.code;

                    // Now load subdistrict data for this district
                    const subdistricts = await loadSubDistrictCodeMap(districtCode);
                    if (!subdistricts || subdistricts.length === 0) {
                        console.warn('No subdistricts found for district code:', districtCode);
                        return;
                    }

                    // Find the subdistrict data for our ID
                    const subDistrictData = subdistricts.find(sd => sd.id === subDistrictId);
                    if (!subDistrictData) {
                        console.warn('Could not find subdistrict data for ID:', subDistrictId);

                        // Still update the hidden fields with the known IDs so form submission works
                        $('#sub_district_id_hidden').val(subDistrictId);
                        $('#subdistrict_name_hidden').val(subDistrictId);
                        return;
                    }

                    const subDistrictCode = subDistrictData.code;
                    const subDistrictName = subDistrictData.name;

                    // Update the subdistrict dropdown with all options from the API
                    $('#sub_district_selector').html('<option value="">Pilih Kecamatan</option>');
                    subdistricts.forEach(subdistrict => {
                        const option = $('<option></option>')
                            .val(subdistrict.code)
                            .text(subdistrict.name)
                            .attr('data-id', subdistrict.id);

                        if (subdistrict.id === subDistrictId) {
                            option.prop('selected', true);
                        }

                        $('#sub_district_selector').append(option);
                    });

                    // Ensure the subdistrict is selected and hidden fields are set
                    $('#sub_district_selector').val(subDistrictCode);
                    $('#sub_district_id_hidden').val(subDistrictId);
                    $('#subdistrict_name_hidden').val(subDistrictId);

                    // Now load village data for this subdistrict
                    const villages = await loadVillageCodeMap(subDistrictCode);
                    if (!villages || villages.length === 0) {
                        console.warn('No villages found for subdistrict code:', subDistrictCode);

                        // Still update hidden fields
                        $('#village_id_hidden').val(villageId);
                        $('#village_name_hidden').val(villageId);
                        return;
                    }

                    // Find village data for our ID
                    const villageData = villages.find(v => v.id === villageId);
                    if (!villageData) {
                        console.warn('Could not find village data for ID:', villageId);

                        // Still update hidden fields
                        $('#village_id_hidden').val(villageId);
                        $('#village_name_hidden').val(villageId);
                        return;
                    }

                    const villageCode = villageData.code;
                    const villageName = villageData.name;

                    // Update the village dropdown with all options from the API
                    $('#village_selector').html('<option value="">Pilih Desa/Kelurahan</option>');
                    villages.forEach(village => {
                        const option = $('<option></option>')
                            .val(village.code)
                            .text(village.name)
                            .attr('data-id', village.id);

                        if (village.id === villageId) {
                            option.prop('selected', true);
                        }

                        $('#village_selector').append(option);
                    });

                    // Ensure the village is selected and hidden fields are set
                    $('#village_selector').val(villageCode);
                    $('#village_id_hidden').val(villageId);
                    $('#village_name_hidden').val(villageId);

                    console.log('Successfully initialized address section selectors with:', {
                        subDistrict: { id: subDistrictId, code: subDistrictCode, name: subDistrictName },
                        village: { id: villageId, code: villageCode, name: villageName }
                    });

                } catch (error) {
                    console.error('Error initializing address selectors:', error);
                }
            }

            // Function to load province codes and store the ID-to-code mapping
            async function loadProvinceCodeMap() {
                try {
                    const response = await $.ajax({
                        url: `{{ url('/location/provinces') }}`,
                        type: 'GET'
                    });

                    let provinces = [];
                    if (response && Array.isArray(response)) {
                        provinces = response;
                    } else if (response && response.data && Array.isArray(response.data)) {
                        provinces = response.data;
                    }

                    if (provinces.length > 0) {
                        // Create mappings in both directions
                        provinces.forEach(province => {
                            provinceCodeMap[province.id] = province.code;
                            provinceIdMap[province.code] = province.id;
                        });
                    }
                } catch (error) {
                    console.error('Error loading province maps:', error);
                }
            }

            // Function to get district code map for a specific province
            async function loadDistrictCodeMap(provinceCode) {
                try {
                    const response = await $.ajax({
                        url: `{{ url('/location/districts') }}/${provinceCode}`,
                        type: 'GET'
                    });

                    let districts = [];
                    if (response && Array.isArray(response)) {
                        districts = response;
                    } else if (response && response.data && Array.isArray(response.data)) {
                        districts = response.data;
                    }

                    // Reset the maps before adding new data
                    districtCodeMap = {};
                    districtIdMap = {};

                    if (districts.length > 0) {
                        // Create mappings in both directions
                        districts.forEach(district => {
                            districtCodeMap[district.id] = district.code;
                            districtIdMap[district.code] = district.id;
                        });
                    }
                    return districts;
                } catch (error) {
                    console.error('Error loading district maps:', error);
                    return [];
                }
            }

            // Function to get subdistrict code map for a specific district
            async function loadSubDistrictCodeMap(districtCode) {
                try {
                    const response = await $.ajax({
                        url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                        type: 'GET'
                    });

                    let subDistricts = [];
                    if (response && Array.isArray(response)) {
                        subDistricts = response;
                    } else if (response && response.data && Array.isArray(response.data)) {
                        subDistricts = response.data;
                    }

                    // Reset the maps before adding new data
                    subDistrictCodeMap = {};
                    subDistrictIdMap = {};

                    if (subDistricts.length > 0) {
                        // Create mappings in both directions
                        subDistricts.forEach(subDistrict => {
                            subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                            subDistrictIdMap[subDistrict.code] = subDistrict.id;
                        });
                    }
                    return subDistricts;
                } catch (error) {
                    console.error('Error loading subdistrict maps:', error);
                    return [];
                }
            }

            // Function to get village code map for a specific subdistrict
            async function loadVillageCodeMap(subDistrictCode) {
                try {
                    const response = await $.ajax({
                        url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                        type: 'GET'
                    });

                    let villages = [];
                    if (response && Array.isArray(response)) {
                        villages = response;
                    } else if (response && response.data && Array.isArray(response.data)) {
                        villages = response.data;
                    }

                    // Reset the maps before adding new data
                    villageCodeMap = {};
                    villageIdMap = {};

                    if (villages.length > 0) {
                        // Create mappings in both directions
                        villages.forEach(village => {
                            villageCodeMap[village.id] = village.code;
                            villageIdMap[village.code] = village.id;
                        });
                    }
                    return villages;
                } catch (error) {
                    console.error('Error loading village maps:', error);
                    return [];
                }
            }

            // Load citizens data from the administrasi route
            async function fetchCitizens() {
                try {
                    const response = await $.ajax({
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
                        }
                    });

                    // Transform the response to match what we expect
                    let citizensList = [];
                    if (response && response.data && Array.isArray(response.data)) {
                        citizensList = response.data;
                    } else if (response && Array.isArray(response)) {
                        citizensList = response;
                    }

                    allCitizens = citizensList;

                    // Process citizens data and populate dropdowns
                    populateCitizensDropdowns(citizensList);

                    // Also load the province code mappings
                    await loadProvinceCodeMap();

                    // Initialize the address section selectors once data is loaded
                    initializeAddressSelectors();

                    return citizensList;
                } catch (error) {
                    console.error('Error fetching citizens data:', error);
                }
            }

            // Function to populate NIK and name dropdowns with data
            function populateCitizensDropdowns(citizens) {
                if (!citizens || !Array.isArray(citizens)) return;

                // Clear existing options first but keep selected ones
                const currentNik = $('#nikSelect').val();
                const currentName = $('#fullNameSelect').val();

                $('#nikSelect').empty().append('<option value="">Pilih NIK</option>');
                $('#fullNameSelect').empty().append('<option value="">Pilih Nama Lengkap</option>');

                // Add the current values back as options
                if (currentNik) {
                    $('#nikSelect').append(`<option value="${currentNik}" selected>${currentNik}</option>`);
                }
                if (currentName) {
                    $('#fullNameSelect').append(`<option value="${currentName}" selected>${currentName}</option>`);
                }

                // Create NIK options array and Name options array
                const nikOptions = [];
                const nameOptions = [];

                // Process citizen data for Select2
                citizens.forEach(citizen => {
                    // Skip if this is the current citizen
                    if (citizen.nik == currentNik) return;

                    // For NIK dropdown
                    if (citizen.nik) {
                        const nikString = citizen.nik.toString();
                        nikOptions.push({
                            id: nikString,
                            text: nikString,
                            citizen: citizen
                        });
                    }

                    // For Full Name dropdown (skip if this is the current citizen)
                    if (citizen.full_name && citizen.full_name != currentName) {
                        nameOptions.push({
                            id: citizen.full_name,
                            text: citizen.full_name,
                            citizen: citizen
                        });
                    }
                });

                // Initialize NIK Select2 with data
                $('#nikSelect').select2({
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
                    }
                });

                // Initialize Full Name Select2 with data
                $('#fullNameSelect').select2({
                    placeholder: 'Pilih Nama Lengkap',
                    width: '100%',
                    data: nameOptions,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        }
                    }
                });
            }

            // Function to populate location dropdowns using ID or code
            async function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
                try {
                    // Update the address section selectors hidden fields
                    $('#sub_district_id_hidden').val(subDistrictId || '');
                    $('#village_id_hidden').val(villageId || '');

                    // Also set the ID values to the name fields (not the actual names)
                    $('#subdistrict_name_hidden').val(subDistrictId || '');
                    $('#village_name_hidden').val(villageId || '');

                    // If we have province ID but not the code, we need to load it
                    if (provinceId && !provinceCodeMap[provinceId]) {
                        await loadProvinceCodeMap();
                    }

                    // Get the province code
                    const provinceCode = provinceCodeMap[provinceId];
                    if (!provinceCode) {
                        return;
                    }

                    // Now load district data for this province without changing the top dropdown
                    const districts = await loadDistrictCodeMap(provinceCode);

                    // Get the district code
                    const districtCode = districtCodeMap[districtId];
                    if (!districtCode) {
                        return;
                    }

                    // Now load subdistrict data for this district
                    const subdistricts = await loadSubDistrictCodeMap(districtCode);

                    // Get the subdistrict code
                    const subDistrictCode = subDistrictCodeMap[subDistrictId];
                    if (!subDistrictCode) {
                        return;
                    }

                    // Now load village data for this subdistrict
                    const villages = await loadVillageCodeMap(subDistrictCode);

                    // Get the village code
                    const villageCode = villageCodeMap[villageId];
                    if (!villageCode) {
                        return;
                    }

                    // Update only the address info section fields

                    // Update subdistrict in address section
                    const selectedSubdistrict = subdistricts.find(sd => sd.id == subDistrictId);
                    if (selectedSubdistrict) {
                        // Ensure we're setting the ID in the hidden field
                        $('#subdistrict_name_hidden').val(subDistrictId);

                        // Update the sub_district_selector dropdown with available options
                        $('#sub_district_selector').html('<option value="">Pilih Kecamatan</option>');
                        subdistricts.forEach(subdistrict => {
                            const option = $('<option></option>')
                                .val(subdistrict.code)
                                .text(subdistrict.name)
                                .attr('data-id', subdistrict.id);

                            if (subdistrict.id == subDistrictId) {
                                option.prop('selected', true);
                            }

                            $('#sub_district_selector').append(option);
                        });
                    }

                    // Update village in address section
                    const selectedVillage = villages.find(v => v.id == villageId);
                    if (selectedVillage) {
                        // Ensure we're setting the ID in the hidden field
                        $('#village_name_hidden').val(villageId);

                        // Update the village_selector dropdown with available options
                        $('#village_selector').html('<option value="">Pilih Desa</option>');
                        villages.forEach(village => {
                            const option = $('<option></option>')
                                .val(village.code)
                                .text(village.name)
                                .attr('data-id', village.id);

                            if (village.id == villageId) {
                                option.prop('selected', true);
                            }

                            $('#village_selector').append(option);
                        });
                    }

                } catch (error) {
                    console.error('Error populating location dropdowns:', error);
                }
            }

            // Handle NIK select change - Update all related fields including KK, address, etc.
            $('#nikSelect').on('select2:select', async function (e) {
                if (isUpdating) return; // Prevent recursion
                isUpdating = true;

                try {
                    const selectedData = e.params.data;
                    if (selectedData && selectedData.citizen) {
                        const citizen = selectedData.citizen;

                        // Update name dropdown
                        $('#fullNameSelect').val(citizen.full_name).trigger('change.select2');

                        // Fill in KK, address, and other fields
                        $('#kk').val(citizen.kk || '');
                        $('#address').val(citizen.address || '');
                        $('#rt').val(citizen.rt || '');
                        $('#rw').val(citizen.rw || '');
                        $('#hamlet').val(citizen.hamlet || citizen.dusun || '');

                        // Set location IDs from citizen data
                        const provinceId = citizen.province_id;
                        const districtId = citizen.district_id;
                        const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;
                        const villageId = citizen.village_id;

                        // If we have location IDs, populate ONLY the address section location dropdowns
                        if (subDistrictId || villageId) {
                            await populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);
                        }
                    }
                } catch (error) {
                    console.error('Error in nikSelect handler:', error);
                } finally {
                    isUpdating = false;
                }
            });

            // Handle Full Name select change - Update NIK and all related fields
            $('#fullNameSelect').on('select2:select', async function (e) {
                if (isUpdating) return; // Prevent recursion
                isUpdating = true;

                try {
                    const selectedData = e.params.data;
                    if (selectedData && selectedData.citizen) {
                        const citizen = selectedData.citizen;

                        // Update NIK dropdown
                        $('#nikSelect').val(citizen.nik.toString()).trigger('change.select2');

                        // Fill in KK, address, and other fields
                        $('#kk').val(citizen.kk || '');
                        $('#address').val(citizen.address || '');
                        $('#rt').val(citizen.rt || '');
                        $('#rw').val(citizen.rw || '');
                        $('#hamlet').val(citizen.hamlet || citizen.dusun || '');


                        // Set location IDs from citizen data
                        const provinceId = citizen.province_id;
                        const districtId = citizen.district_id;
                        const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;
                        const villageId = citizen.village_id;

                        // If we have location IDs, populate the location dropdowns
                        if (subDistrictId || villageId) {
                            await populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);
                        }
                    }
                } catch (error) {
                    console.error('Error in fullNameSelect handler:', error);
                } finally {
                    isUpdating = false;
                }
            });

            // Location selects event handlers for the top form section
            const provinceSelect = document.getElementById('province_code');
            const districtSelect = document.getElementById('district_code');
            const subDistrictSelect = document.getElementById('subdistrict_code');
            const villageSelect = document.getElementById('village_code');

            // Hidden inputs for IDs
            const provinceIdInput = document.getElementById('province_id');
            const districtIdInput = document.getElementById('district_id');
            const subDistrictIdInput = document.getElementById('subdistrict_id');
            const villageIdInput = document.getElementById('village_id');

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                const provinceCode = this.value;
                const selectedOption = this.options[this.selectedIndex];

                // Update the province ID hidden input
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    provinceIdInput.value = selectedOption.getAttribute('data-id');
                } else {
                    provinceIdInput.value = '';
                }

                // Reset and disable dependent dropdowns
                districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                districtSelect.disabled = !provinceCode;

                subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                subDistrictSelect.disabled = true;

                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = true;

                // Clear dependent hidden inputs
                districtIdInput.value = '';
                subDistrictIdInput.value = '';
                villageIdInput.value = '';

                if (provinceCode) {
                    // Show loading state
                    districtSelect.innerHTML = '<option value="">Loading...</option>';

                    // Fetch districts for this province
                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => response.json())
                        .then(data => {
                            let districts = [];
                            if (data && Array.isArray(data)) {
                                districts = data;
                            } else if (data && data.data && Array.isArray(data.data)) {
                                districts = data.data;
                            }

                            districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                            if (districts.length > 0) {
                                // Update district maps
                                districtCodeMap = {};
                                districtIdMap = {};

                                districts.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.code;
                                    option.textContent = district.name;
                                    option.setAttribute('data-id', district.id);
                                    districtSelect.appendChild(option);

                                    // Update district maps
                                    districtCodeMap[district.id] = district.code;
                                    districtIdMap[district.code] = district.id;
                                });

                                districtSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            districtSelect.innerHTML = '<option value="">Error loading data</option>';
                            console.error('Error fetching districts:', error);
                        });
                }
            });

            // District change handler
            districtSelect.addEventListener('change', function() {
                const districtCode = this.value;
                const selectedOption = this.options[this.selectedIndex];

                // Update the district ID hidden input
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    districtIdInput.value = selectedOption.getAttribute('data-id');
                } else {
                    districtIdInput.value = '';
                }

                // Reset and disable dependent dropdowns
                subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                subDistrictSelect.disabled = !districtCode;

                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = true;

                // Clear dependent hidden inputs
                subDistrictIdInput.value = '';
                villageIdInput.value = '';

                if (districtCode) {
                    // Show loading state
                    subDistrictSelect.innerHTML = '<option value="">Loading...</option>';

                    // Fetch subdistricts for this district
                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            let subdistricts = [];
                            if (data && Array.isArray(data)) {
                                subdistricts = data;
                            } else if (data && data.data && Array.isArray(data.data)) {
                                subdistricts = data.data;
                            }

                            subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

                            if (subdistricts.length > 0) {
                                // Update subdistrict maps
                                subDistrictCodeMap = {};
                                subDistrictIdMap = {};

                                subdistricts.forEach(subDistrict => {
                                    const option = document.createElement('option');
                                    option.value = subDistrict.code;
                                    option.textContent = subDistrict.name;
                                    option.setAttribute('data-id', subDistrict.id);
                                    subDistrictSelect.appendChild(option);

                                    // Update subdistrict maps
                                    subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                                    subDistrictIdMap[subDistrict.code] = subDistrict.id;
                                });

                                subDistrictSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                            console.error('Error fetching subdistricts:', error);
                        });
                }
            });

            // Subdistrict change handler
            subDistrictSelect.addEventListener('change', function() {
                const subDistrictCode = this.value;
                const selectedOption = this.options[this.selectedIndex];

                // Update the subdistrict ID hidden input
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    subDistrictIdInput.value = selectedOption.getAttribute('data-id');
                } else {
                    subDistrictIdInput.value = '';
                }

                // Reset and disable dependent dropdown
                villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                villageSelect.disabled = !subDistrictCode;

                // Clear dependent hidden input
                villageIdInput.value = '';

                if (subDistrictCode) {
                    // Show loading state
                    villageSelect.innerHTML = '<option value="">Loading...</option>';

                    // Fetch villages for this subdistrict
                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            let villages = [];
                            if (data && Array.isArray(data)) {
                                villages = data;
                            } else if (data && data.data && Array.isArray(data.data)) {
                                villages = data.data;
                            }

                            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                            if (villages.length > 0) {
                                // Update village maps
                                villageCodeMap = {};
                                villageIdMap = {};

                                villages.forEach(village => {
                                    const option = document.createElement('option');
                                    option.value = village.code;
                                    option.textContent = village.name;
                                    option.setAttribute('data-id', village.id);
                                    villageSelect.appendChild(option);

                                    // Update village maps
                                    villageCodeMap[village.id] = village.code;
                                    villageIdMap[village.code] = village.id;
                                });

                                villageSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            villageSelect.innerHTML = '<option value="">Error loading data</option>';
                            console.error('Error fetching villages:', error);
                        });
                }
            });

            // Village change handler
            villageSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                // Update the village ID hidden input
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    villageIdInput.value = selectedOption.getAttribute('data-id');
                } else {
                    villageIdInput.value = '';
                }
            });

            // Add event handlers for sub_district_selector and village_selector
            $('#sub_district_selector').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const subDistrictId = selectedOption.attr('data-id');
                const subDistrictCode = $(this).val();
                const subDistrictName = selectedOption.text();

                // Store the ID, not the name
                $('#sub_district_id_hidden').val(subDistrictId || '');
                $('#subdistrict_name_hidden').val(subDistrictId || '');

                // If we have a subdistrict code but no villages, load villages
                if (subDistrictCode) {
                    // Show loading state
                    $('#village_selector').html('<option value="">Loading...</option>').prop('disabled', true);

                    // Fetch villages for this subdistrict
                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            let villages = [];
                            if (data && Array.isArray(data)) {
                                villages = data;
                            } else if (data && data.data && Array.isArray(data.data)) {
                                villages = data.data;
                            }

                            // Clear and repopulate village dropdown
                            $('#village_selector').html('<option value="">Pilih Desa</option>').prop('disabled', false);

                            villages.forEach(village => {
                                const option = document.createElement('option');
                                option.value = village.code;
                                option.textContent = village.name;
                                option.setAttribute('data-id', village.id);
                                document.getElementById('village_selector').appendChild(option);

                                // Also update the village maps
                                villageCodeMap[village.id] = village.code;
                                villageIdMap[village.code] = village.id;
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching villages:', error);
                            $('#village_selector').html('<option value="">Error loading villages</option>').prop('disabled', false);
                        });
                }
            });

            $('#village_selector').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const villageId = selectedOption.attr('data-id');
                const villageCode = $(this).val();
                const villageName = selectedOption.text();

                // Store the ID, not the name
                $('#village_id_hidden').val(villageId || '');
                $('#village_name_hidden').val(villageId || '');
            });

            // Load citizens data when the page loads
            fetchCitizens();

            // Final form validation before submission
            document.querySelector('form').addEventListener('submit', function(e) {
                // Prevent default to check values
                e.preventDefault();

                // Get the values from hidden fields
                const subdistrictName = document.getElementById('subdistrict_name_hidden').value;
                const villageName = document.getElementById('village_name_hidden').value;

                // Ensure we have valid numeric IDs
                if (!subdistrictName || isNaN(parseInt(subdistrictName))) {
                    alert('Error: Kecamatan tidak valid. Silakan pilih kecamatan yang valid.');
                    return false;
                }

                if (!villageName || isNaN(parseInt(villageName))) {
                    alert('Error: Desa tidak valid. Silakan pilih desa yang valid.');
                    return false;
                }

                // If everything is correct, submit the form
                this.submit();
            });
        });
    </script>
</x-layout>
