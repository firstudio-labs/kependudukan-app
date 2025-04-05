<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Pengantar KTP</h1>

        <form method="POST" action="{{ route('superadmin.surat.pengantar-ktp.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Data Pemohon Section -->
            <div class="mt-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Pemohon</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK with Search -->
                        <div>
                            <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nikSelect" name="nik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap with Search -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama Lengkap</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Nomor Kartu Keluarga -->
                        <div>
                            <label for="kk" class="block text-sm font-medium text-gray-700">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                            <input type="text" id="kk" name="kk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- RT -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                        </div>

                        <!-- RW -->
                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input type="text" id="rw" name="rw" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                        </div>

                        <!-- Dusun -->
                        <div>
                            <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun <span class="text-red-500">*</span></label>
                            <input type="text" id="hamlet" name="hamlet" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wilayah Section -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Provinsi -->
                        <div>
                            <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                            <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="province_id" name="province_id" value="">
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Desa</option>
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Surat Section -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Jenis Permohonan -->
                        <div>
                            <label for="application_type" class="block text-sm font-medium text-gray-700">Permohonan KTP <span class="text-red-500">*</span></label>
                            <select id="application_type" name="application_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Jenis Permohonan</option>
                                <option value="Baru">Baru</option>
                                <option value="Perpanjang">Perpanjang</option>
                                <option value="Pergantian">Pergantian</option>
                            </select>
                        </div>

                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Pejabat Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
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
                } catch (error) {
                    console.error('Error fetching citizens data:', error);
                }
            }

            // Function to populate NIK and name dropdowns with data
            function populateCitizensDropdowns(citizens) {
                if (!citizens || !Array.isArray(citizens)) return;

                // Clear existing options first
                $('#nikSelect').empty().append('<option value="">Pilih NIK</option>');
                $('#fullNameSelect').empty().append('<option value="">Pilih Nama Lengkap</option>');

                // Create NIK options array and Name options array
                const nikOptions = [];
                const nameOptions = [];

                // Process citizen data for Select2
                citizens.forEach(citizen => {
                    // For NIK dropdown
                    if (citizen.nik) {
                        const nikString = citizen.nik.toString();
                        nikOptions.push({
                            id: nikString,
                            text: nikString,
                            citizen: citizen
                        });
                    }

                    // For Full Name dropdown
                    if (citizen.full_name) {
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
                        // CRITICAL FIX: Do not set name values to text inputs
                        // Old code: $('input[name="subdistrict_name"]').val(selectedSubdistrict.name);
                        // Instead, ensure we're setting the ID in the hidden field
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
                        // CRITICAL FIX: Do not set name values to text inputs
                        // Old code: $('input[name="village_name"]').val(selectedVillage.name);
                        // Instead, ensure we're setting the ID in the hidden field
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

                        // Update the main location fields (top section)
                        if (provinceId) {
                            // Set the province_id hidden input
                            $('#province_id').val(provinceId);

                            // Find and select the correct province in the dropdown
                            let provinceFound = false;
                            const provinceSelect = document.getElementById('province_code');
                            for (let i = 0; i < provinceSelect.options.length; i++) {
                                const option = provinceSelect.options[i];
                                if (option.getAttribute('data-id') == provinceId) {
                                    provinceSelect.value = option.value;
                                    provinceFound = true;

                                    // Trigger loading of districts for this province
                                    const provinceCode = option.value;

                                    // Show loading state
                                    const districtSelect = document.getElementById('district_code');
                                    districtSelect.innerHTML = '<option value="">Loading...</option>';
                                    districtSelect.disabled = false;

                                    // Load districts
                                    const districts = await fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                                        .then(response => response.json())
                                        .catch(error => {
                                            console.error('Error loading districts:', error);
                                            return [];
                                        });

                                    // Populate district dropdown
                                    districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                                    let districtFound = false;

                                    if (districts && districts.length > 0) {
                                        districts.forEach(district => {
                                            const districtOption = document.createElement('option');
                                            districtOption.value = district.code;
                                            districtOption.textContent = district.name;
                                            districtOption.setAttribute('data-id', district.id);
                                            districtSelect.appendChild(districtOption);

                                            // Select this option if it matches the citizen's district
                                            if (district.id == districtId) {
                                                districtOption.selected = true;
                                                districtFound = true;
                                                $('#district_id').val(districtId);

                                                // Also load subdistricts for this district
                                                const districtCode = district.code;
                                                const subDistrictSelect = document.getElementById('subdistrict_code');
                                                subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
                                                subDistrictSelect.disabled = false;

                                                fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                                                    .then(response => response.json())
                                                    .then(subdistricts => {
                                                        subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                                                        let subDistrictFound = false;

                                                        if (subdistricts && subdistricts.length > 0) {
                                                            subdistricts.forEach(subdistrict => {
                                                                const subDistrictOption = document.createElement('option');
                                                                subDistrictOption.value = subdistrict.code;
                                                                subDistrictOption.textContent = subdistrict.name;
                                                                subDistrictOption.setAttribute('data-id', subdistrict.id);
                                                                subDistrictSelect.appendChild(subDistrictOption);

                                                                // Select this option if it matches the citizen's subdistrict
                                                                if (subdistrict.id == subDistrictId) {
                                                                    subDistrictOption.selected = true;
                                                                    subDistrictFound = true;
                                                                    $('#subdistrict_id').val(subDistrictId);

                                                                    // Also load villages for this subdistrict
                                                                    const subDistrictCode = subdistrict.code;
                                                                    const villageSelect = document.getElementById('village_code');
                                                                    villageSelect.innerHTML = '<option value="">Loading...</option>';
                                                                    villageSelect.disabled = false;

                                                                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                                                                        .then(response => response.json())
                                                                        .then(villages => {
                                                                            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                                            if (villages && villages.length > 0) {
                                                                                villages.forEach(village => {
                                                                                    const villageOption = document.createElement('option');
                                                                                    villageOption.value = village.code;
                                                                                    villageOption.textContent = village.name;
                                                                                    villageOption.setAttribute('data-id', village.id);
                                                                                    villageSelect.appendChild(villageOption);

                                                                                    // Select this option if it matches the citizen's village
                                                                                    if (village.id == villageId) {
                                                                                        villageOption.selected = true;
                                                                                        $('#village_id').val(villageId);
                                                                                    }
                                                                                });
                                                                            }
                                                                        })
                                                                        .catch(error => {
                                                                            console.error('Error loading villages:', error);
                                                                            villageSelect.innerHTML = '<option value="">Error loading data</option>';
                                                                        });
                                                                }
                                                            });
                                                        }

                                                        if (!subDistrictFound) {
                                                            subDistrictSelect.disabled = !districtFound;
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error loading subdistricts:', error);
                                                        subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                                                    });
                                            }
                                        });
                                    }

                                    if (!districtFound) {
                                        districtSelect.disabled = !provinceFound;
                                    }

                                    break;
                                }
                            }
                        }

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

            // Full name select change handler - similar to NIK but starting with name
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

                        // Update the main location fields (top section)
                        if (provinceId) {
                            // Set the province_id hidden input
                            $('#province_id').val(provinceId);

                            // Find and select the correct province in the dropdown
                            let provinceFound = false;
                            const provinceSelect = document.getElementById('province_code');
                            for (let i = 0; i < provinceSelect.options.length; i++) {
                                const option = provinceSelect.options[i];
                                if (option.getAttribute('data-id') == provinceId) {
                                    provinceSelect.value = option.value;
                                    provinceFound = true;

                                    // Trigger loading of districts for this province
                                    const provinceCode = option.value;

                                    // Show loading state
                                    const districtSelect = document.getElementById('district_code');
                                    districtSelect.innerHTML = '<option value="">Loading...</option>';
                                    districtSelect.disabled = false;

                                    // Load districts
                                    const districts = await fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                                        .then(response => response.json())
                                        .catch(error => {
                                            console.error('Error loading districts:', error);
                                            return [];
                                        });

                                    // Populate district dropdown
                                    districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                                    let districtFound = false;

                                    if (districts && districts.length > 0) {
                                        districts.forEach(district => {
                                            const districtOption = document.createElement('option');
                                            districtOption.value = district.code;
                                            districtOption.textContent = district.name;
                                            districtOption.setAttribute('data-id', district.id);
                                            districtSelect.appendChild(districtOption);

                                            // Select this option if it matches the citizen's district
                                            if (district.id == districtId) {
                                                districtOption.selected = true;
                                                districtFound = true;
                                                $('#district_id').val(districtId);

                                                // Also load subdistricts for this district
                                                const districtCode = district.code;
                                                const subDistrictSelect = document.getElementById('subdistrict_code');
                                                subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
                                                subDistrictSelect.disabled = false;

                                                fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                                                    .then(response => response.json())
                                                    .then(subdistricts => {
                                                        subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                                                        let subDistrictFound = false;

                                                        if (subdistricts && subdistricts.length > 0) {
                                                            subdistricts.forEach(subdistrict => {
                                                                const subDistrictOption = document.createElement('option');
                                                                subDistrictOption.value = subdistrict.code;
                                                                subDistrictOption.textContent = subdistrict.name;
                                                                subDistrictOption.setAttribute('data-id', subdistrict.id);
                                                                subDistrictSelect.appendChild(subDistrictOption);

                                                                // Select this option if it matches the citizen's subdistrict
                                                                if (subdistrict.id == subDistrictId) {
                                                                    subDistrictOption.selected = true;
                                                                    subDistrictFound = true;
                                                                    $('#subdistrict_id').val(subDistrictId);

                                                                    // Also load villages for this subdistrict
                                                                    const subDistrictCode = subdistrict.code;
                                                                    const villageSelect = document.getElementById('village_code');
                                                                    villageSelect.innerHTML = '<option value="">Loading...</option>';
                                                                    villageSelect.disabled = false;

                                                                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                                                                        .then(response => response.json())
                                                                        .then(villages => {
                                                                            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                                            if (villages && villages.length > 0) {
                                                                                villages.forEach(village => {
                                                                                    const villageOption = document.createElement('option');
                                                                                    villageOption.value = village.code;
                                                                                    villageOption.textContent = village.name;
                                                                                    villageOption.setAttribute('data-id', village.id);
                                                                                    villageSelect.appendChild(villageOption);

                                                                                    // Select this option if it matches the citizen's village
                                                                                    if (village.id == villageId) {
                                                                                        villageOption.selected = true;
                                                                                        $('#village_id').val(villageId);
                                                                                    }
                                                                                });
                                                                            }
                                                                        })
                                                                        .catch(error => {
                                                                            console.error('Error loading villages:', error);
                                                                            villageSelect.innerHTML = '<option value="">Error loading data</option>';
                                                                        });
                                                                }
                                                            });
                                                        }

                                                        if (!subDistrictFound) {
                                                            subDistrictSelect.disabled = !districtFound;
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error loading subdistricts:', error);
                                                        subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                                                    });
                                            }
                                        });
                                    }

                                    if (!districtFound) {
                                        districtSelect.disabled = !provinceFound;
                                    }

                                    break;
                                }
                            }
                        }

                        // If we have location IDs, populate the location dropdowns in the address section
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

            // Add event handlers for kk_subdistrict_code and kk_village_code
            $('#kk_subdistrict_code').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const subDistrictId = selectedOption.attr('data-id');
                const subDistrictCode = $(this).val();
                const subDistrictName = selectedOption.text();

                // Update hidden field and text input
                $('#kk_subdistrict_id').val(subDistrictId || '');
                $('input[name="subdistrict_name"]').val(subDistrictName || '');

                // If we have a subdistrict code but no villages, load villages
                if (subDistrictCode && $('#kk_village_code option').length <= 1) {
                    // Show loading state
                    $('#kk_village_code').html('<option value="">Loading...</option>').prop('disabled', true);

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
                            $('#kk_village_code').html('<option value="">Pilih Desa</option>').prop('disabled', false);

                            villages.forEach(village => {
                                const option = document.createElement('option');
                                option.value = village.code;
                                option.textContent = village.name;
                                option.setAttribute('data-id', village.id);
                                document.getElementById('kk_village_code').appendChild(option);

                                // Also update the village maps
                                villageCodeMap[village.id] = village.code;
                                villageIdMap[village.code] = village.id;
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching villages:', error);
                            $('#kk_village_code').html('<option value="">Error loading villages</option>').prop('disabled', false);
                        });
                }
            });

            $('#kk_village_code').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const villageId = selectedOption.attr('data-id');
                const villageCode = $(this).val();
                const villageName = selectedOption.text();

                // Update hidden field and text input
                $('#kk_village_id').val(villageId || '');
                $('input[name="village_name"]').val(villageName || '');
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

            // Additional validation before form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                // Get the values from hidden fields
                const subdistrictName = document.getElementById('subdistrict_name_hidden').value;
                const villageName = document.getElementById('village_name_hidden').value;

                // Ensure we have valid IDs (numeric values)
                if (!subdistrictName || isNaN(parseInt(subdistrictName))) {
                    e.preventDefault();
                    alert('Error: Subdistrict ID is invalid. Please select a valid Kecamatan.');
                    return false;
                }

                if (!villageName || isNaN(parseInt(villageName))) {
                    e.preventDefault();
                    alert('Error: Village ID is invalid. Please select a valid Desa/Kelurahan.');
                    return false;
                }
            });

            // Make sure field names are exactly what the controller expects
            document.querySelector('form').addEventListener('submit', function(e) {
                // Prevent default submission to check form data
                e.preventDefault();

                // Get all form values
                const formData = new FormData(this);
                const formValues = {};

                // Convert FormData to object for logging
                for (let [key, value] of formData.entries()) {
                    formValues[key] = value;
                }

                // Critical field checks - must match database column names
                const criticalFields = [
                    'province_id', 'district_id', 'subdistrict_id', 'village_id',
                    'application_type', 'nik', 'full_name', 'kk', 'address', 'rt', 'rw',
                    'hamlet', 'subdistrict_name', 'village_name'
                ];

                let missingFields = [];
                criticalFields.forEach(field => {
                    if (!formData.get(field) || formData.get(field).trim() === '') {
                        missingFields.push(field);
                    }
                });

                // Check if any critical fields are missing
                if (missingFields.length > 0) {
                    alert('Missing required fields: ' + missingFields.join(', '));
                    return false;
                }

                // Make sure numeric values are actually numeric
                const numericFields = ['province_id', 'district_id', 'subdistrict_id', 'village_id', 'nik', 'kk', 'subdistrict_name', 'village_name'];
                let invalidFields = [];

                numericFields.forEach(field => {
                    const value = formData.get(field);
                    if (value && isNaN(Number(value))) {
                        invalidFields.push(field);
                    }
                });

                if (invalidFields.length > 0) {
                    alert('Non-numeric values in numeric fields: ' + invalidFields.join(', '));
                    return false;
                }

                // If all checks pass, submit the form
                this.submit();
            });

            // Before form submission, add explicit logging and verification
            document.querySelector('form').addEventListener('submit', function(e) {
                // Prevent default to check values
                e.preventDefault();

                // Get all form values for logging
                const formData = new FormData(this);
                const formValues = {};
                for (let [key, value] of formData.entries()) {
                    formValues[key] = value;
                }

                // Explicitly check subdistrict_name and village_name
                const subDistrictName = formData.get('subdistrict_name');
                const villageName = formData.get('village_name');

                // Force the values to be IDs if they're not already
                if (subDistrictName && isNaN(parseInt(subDistrictName))) {
                    // If it's not a number (ID), get the ID from the selector
                    const subDistrictId = $('#sub_district_selector option:selected').attr('data-id');
                    $('#subdistrict_name_hidden').val(subDistrictId || '');
                }

                if (villageName && isNaN(parseInt(villageName))) {
                    // If it's not a number (ID), get the ID from the selector
                    const villageId = $('#village_selector option:selected').attr('data-id');
                    $('#village_name_hidden').val(villageId || '');
                }

                // Final check before submission
                const finalSubDistrictName = $('#subdistrict_name_hidden').val();
                const finalVillageName = $('#village_name_hidden').val();

                if (!finalSubDistrictName || isNaN(parseInt(finalSubDistrictName))) {
                    alert('Error: Kecamatan tidak valid. Silakan pilih kecamatan yang valid.');
                    return false;
                }

                if (!finalVillageName || isNaN(parseInt(finalVillageName))) {
                    alert('Error: Desa tidak valid. Silakan pilih desa yang valid.');
                    return false;
                }

                // If everything is correct, submit the form
                this.submit();
            });

            // Form submission handler - simplified version that still validates but is less verbose
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
