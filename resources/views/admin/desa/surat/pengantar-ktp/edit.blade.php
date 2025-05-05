<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Pengantar KTP</h1>

        <form method="POST" action="{{ route('admin.desa.surat.pengantar-ktp.update', $pengantarKtp->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_accepted" value="1">

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
                                <option value="{{ $pengantarKtp->nik }}" selected>{{ $pengantarKtp->nik }}</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap with Search -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama Lengkap</option>
                                <option value="{{ $pengantarKtp->full_name }}" selected>{{ $pengantarKtp->full_name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Nomor Kartu Keluarga -->
                        <div>
                            <label for="kk" class="block text-sm font-medium text-gray-700">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                            <input type="text" id="kk" name="kk" value="{{ $pengantarKtp->kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $pengantarKtp->address }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- RT -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" value="{{ $pengantarKtp->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                        </div>

                        <!-- RW -->
                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input type="text" id="rw" name="rw" value="{{ $pengantarKtp->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                        </div>

                        <!-- Dusun -->
                        <div>
                            <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun <span class="text-red-500">*</span></label>
                            <input type="text" id="hamlet" name="hamlet" value="{{ $pengantarKtp->hamlet }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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
                                    <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $province['id'] == $pengantarKtp->province_id ? 'selected' : '' }}>{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="province_id" name="province_id" value="{{ $pengantarKtp->province_id }}">
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $district['id'] == $pengantarKtp->district_id ? 'selected' : '' }}>{{ $district['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="{{ $pengantarKtp->district_id }}">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($subDistricts as $subDistrict)
                                    <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $subDistrict['id'] == $pengantarKtp->subdistrict_id ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $pengantarKtp->subdistrict_id }}">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Desa</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $village['id'] == $pengantarKtp->village_id ? 'selected' : '' }}>{{ $village['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="{{ $pengantarKtp->village_id }}">
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
                                <option value="Baru" {{ $pengantarKtp->application_type == 'Baru' ? 'selected' : '' }}>Baru</option>
                                <option value="Perpanjang" {{ $pengantarKtp->application_type == 'Perpanjang' ? 'selected' : '' }}>Perpanjang</option>
                                <option value="Pergantian" {{ $pengantarKtp->application_type == 'Pergantian' ? 'selected' : '' }}>Pergantian</option>
                            </select>
                        </div>

                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" value="{{ $pengantarKtp->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Pejabat Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->id }}" {{ $pengantarKtp->signing == $signer->id ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>
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
                    Accept
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
            // Ensure the signing field is correctly submitted as an ID
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const signingSelect = document.getElementById('signing');
                if (signingSelect.value) {
                    // Make sure it's treated as a numeric ID
                    signingSelect.value = parseInt(signingSelect.value, 10) || signingSelect.value;
                }
            });

            let isUpdating = false;
            let allCitizens = [];

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
                } catch (error) {
                    console.error('Error fetching citizens data:', error);
                }
            }

            // Function to populate NIK and name dropdowns with data
            function populateCitizensDropdowns(citizens) {
                if (!citizens || !Array.isArray(citizens)) return;

                // Clear existing options first but keep selected ones
                const currentNik = '{{ $pengantarKtp->nik }}';
                const currentName = '{{ $pengantarKtp->full_name }}';

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

            // Handle NIK select change - Update all related fields including KK, address, etc.
            $('#nikSelect').on('select2:select', function (e) {
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

                                    // Trigger a change event to load district data
                                    $(provinceSelect).trigger('change');
                                    break;
                                }
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error in nikSelect handler:', error);
                } finally {
                    isUpdating = false;
                }
            });

            // Full name select change handler - similar to NIK but starting with name
            $('#fullNameSelect').on('select2:select', function (e) {
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

                                    // Trigger a change event to load district data
                                    $(provinceSelect).trigger('change');
                                    break;
                                }
                            }
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

                if (provinceCode && !districtSelect.disabled) {
                    // The districts are already loaded, so we don't need to reload them
                    return;
                }

                // If we need to load new districts...
                if (provinceCode) {
                    // Show loading state
                    districtSelect.innerHTML = '<option value="">Loading...</option>';
                    districtSelect.disabled = false;

                    // Fetch districts for this province
                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => response.json())
                        .then(data => {
                            districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                            if (data && data.length > 0) {
                                data.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.code;
                                    option.textContent = district.name;
                                    option.setAttribute('data-id', district.id);

                                    // Check if this district matches the one we want to select
                                    if (district.id == {{ $pengantarKtp->district_id ?? 'null' }}) {
                                        option.selected = true;
                                        districtIdInput.value = district.id;
                                    }

                                    districtSelect.appendChild(option);
                                });

                                districtSelect.disabled = false;

                                // If we selected a district, trigger a change event to load subdisticts
                                if (districtIdInput.value) {
                                    $(districtSelect).trigger('change');
                                }
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

                if (districtCode && !subDistrictSelect.disabled) {
                    // The subdistricts are already loaded, so we don't need to reload them
                    return;
                }

                // If we need to load new subdistricts...
                if (districtCode) {
                    // Show loading state
                    subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
                    subDistrictSelect.disabled = false;

                    // Fetch subdistricts for this district
                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

                            if (data && data.length > 0) {
                                data.forEach(subdistrict => {
                                    const option = document.createElement('option');
                                    option.value = subdistrict.code;
                                    option.textContent = subdistrict.name;
                                    option.setAttribute('data-id', subdistrict.id);

                                    // Check if this subdistrict matches the one we want to select
                                    if (subdistrict.id == {{ $pengantarKtp->subdistrict_id ?? 'null' }}) {
                                        option.selected = true;
                                        subDistrictIdInput.value = subdistrict.id;
                                    }

                                    subDistrictSelect.appendChild(option);
                                });

                                subDistrictSelect.disabled = false;

                                // If we selected a subdistrict, trigger a change event to load villages
                                if (subDistrictIdInput.value) {
                                    $(subDistrictSelect).trigger('change');
                                }
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

                if (subDistrictCode && !villageSelect.disabled) {
                    // The villages are already loaded, so we don't need to reload them
                    return;
                }

                // If we need to load new villages...
                if (subDistrictCode) {
                    // Show loading state
                    villageSelect.innerHTML = '<option value="">Loading...</option>';
                    villageSelect.disabled = false;

                    // Fetch villages for this subdistrict
                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                            if (data && data.length > 0) {
                                data.forEach(village => {
                                    const option = document.createElement('option');
                                    option.value = village.code;
                                    option.textContent = village.name;
                                    option.setAttribute('data-id', village.id);

                                    // Check if this village matches the one we want to select
                                    if (village.id == {{ $pengantarKtp->village_id ?? 'null' }}) {
                                        option.selected = true;
                                        villageIdInput.value = village.id;
                                    }

                                    villageSelect.appendChild(option);
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

            // Load citizens data when the page loads
            fetchCitizens();
        });
    </script>
</x-layout>
