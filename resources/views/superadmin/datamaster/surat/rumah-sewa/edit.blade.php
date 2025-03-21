<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Izin Rumah Sewa</h1>

        <form method="POST" action="{{ route('superadmin.surat.rumah-sewa.update', $rumahSewa->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $rumahSewa->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $rumahSewa->province_id }}">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $rumahSewa->district_id }}">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $rumahSewa->subdistrict_id }}">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $rumahSewa->village_id }}">
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="number" id="letter_number" name="letter_number" value="{{ $rumahSewa->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $rumahSewa->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Organizer Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Penyelenggara</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nikSelect" name="nik" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="{{ $rumahSewa->nik }}">{{ $rumahSewa->nik }}</option>
                            </select>
                        </div>

                        <!-- Nama Penyelenggara -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Penyelenggara <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="{{ $rumahSewa->full_name }}">{{ $rumahSewa->full_name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Alamat Penyelenggara -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Penyelenggara <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $rumahSewa->address }}</textarea>
                        </div>

                        <!-- Nama Penanggung Jawab -->
                        <div>
                            <label for="responsibleNameSelect" class="block text-sm font-medium text-gray-700">Nama Penanggung Jawab <span class="text-red-500">*</span></label>
                            <select id="responsibleNameSelect" name="responsible_name" class="responsiblename-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="{{ $rumahSewa->responsible_name }}">{{ $rumahSewa->responsible_name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Property Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Rumah Sewa</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Alamat Rumah Sewa -->
                        <div>
                            <label for="rental_address" class="block text-sm font-medium text-gray-700">Alamat Rumah Sewa <span class="text-red-500">*</span></label>
                            <textarea id="rental_address" name="rental_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $rumahSewa->rental_address }}</textarea>
                        </div>

                        <!-- Jalan -->
                        <div>
                            <label for="street" class="block text-sm font-medium text-gray-700">Jalan <span class="text-red-500">*</span></label>
                            <input type="text" id="street" name="street" value="{{ $rumahSewa->street }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- Kelurahan -->
                        <div>
                            <label for="village_name" class="block text-sm font-medium text-gray-700">Kelurahan <span class="text-red-500">*</span></label>
                            <input type="text" id="village_name" name="village_name" value="{{ $rumahSewa->village_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Gang/Nomor -->
                        <div>
                            <label for="alley_number" class="block text-sm font-medium text-gray-700">Gang/Nomor <span class="text-red-500">*</span></label>
                            <input type="text" id="alley_number" name="alley_number" value="{{ $rumahSewa->alley_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RT Field - Changed from number to text to preserve leading zeros -->
                        <div class="form-group">
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" value="{{ $rumahSewa->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Contoh: 001, 002, 003, dll.</small>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- Luas Bangunan -->
                        <div>
                            <label for="building_area" class="block text-sm font-medium text-gray-700">Luas Bangunan <span class="text-red-500">*</span></label>
                            <input type="text" id="building_area" name="building_area" value="{{ $rumahSewa->building_area }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: 100 m²" required>
                        </div>

                        <!-- Jumlah Kamar -->
                        <div>
                            <label for="room_count" class="block text-sm font-medium text-gray-700">Jumlah Kamar <span class="text-red-500">*</span></label>
                            <input type="number" id="room_count" name="room_count" value="{{ $rumahSewa->room_count }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Jenis Rumah/Kamar Sewa -->
                        <div>
                            <label for="rental_type" class="block text-sm font-medium text-gray-700">Jenis Rumah/Kamar Sewa <span class="text-red-500">*</span></label>
                            <input type="text" id="rental_type" name="rental_type" value="{{ $rumahSewa->rental_type }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: Kos, Kontrakan, Rumah Sewa" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Berlaku Ijin Sampai -->
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700">Berlaku Ijin Sampai</label>
                            <input type="date" id="valid_until" name="valid_until" value="{{ $rumahSewa->valid_until ? $rumahSewa->valid_until->format('Y-m-d') : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
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

        document.addEventListener('DOMContentLoaded', function() {
            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Load all citizens first before initializing Select2
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

                    // Setup the interfaces
                    setupLocationHandlers();
                    setupOrganizerFields();
                    setupResponsibleNameField();
                },
                error: function(error) {
                    console.error('Failed to load citizen data:', error);
                    // Setup the interfaces anyway with empty data
                    setupLocationHandlers();
                    setupOrganizerFields();
                    setupResponsibleNameField();
                }
            });

            function setupLocationHandlers() {
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
                const originalProvinceId = "{{ $rumahSewa->province_id }}";
                const originalDistrictId = "{{ $rumahSewa->district_id }}";
                const originalSubdistrictId = "{{ $rumahSewa->subdistrict_id }}";
                const originalVillageId = "{{ $rumahSewa->village_id }}";

                // Helper function to reset select options
                function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
                    select.innerHTML = `<option value="">${defaultText}</option>`;
                    select.disabled = true;
                    if (hiddenInput) hiddenInput.value = '';
                }

                // Helper function to populate select options with code as value and id as data attribute
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

                // Initialize dropdown data on page load
                // Find the selected province option and get its code value
                const selectedProvinceOption = [...provinceSelect.options].find(option =>
                    option.getAttribute('data-id') === originalProvinceId);

                if (selectedProvinceOption) {
                    // Load the district data for this province
                    loadDistricts(selectedProvinceOption.value, originalDistrictId);
                }
            }

            // Setup organizer fields (NIK, name, address) as a connected unit
            function setupOrganizerFields() {
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

                const { nikOptions, nameOptions } = prepareCitizenOptions();
                const nikSelect = document.getElementById('nikSelect');
                const nameSelect = document.getElementById('fullNameSelect');

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

                // NIK select change handler
                $(nikSelect).on('select2:select', function(e) {
                    if (isUpdating) return;
                    isUpdating = true;

                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        // Update full name
                        $(nameSelect).val(citizen.full_name).trigger('change.select2');

                        // Fill address field
                        $('#address').val(citizen.address || '');
                    }

                    isUpdating = false;
                });

                // Full name select change handler
                $(nameSelect).on('select2:select', function(e) {
                    if (isUpdating) return;
                    isUpdating = true;

                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        // Update NIK
                        if (citizen.nik) {
                            $(nikSelect).val(citizen.nik.toString()).trigger('change.select2');
                        }

                        // Fill address field
                        $('#address').val(citizen.address || '');
                    }

                    isUpdating = false;
                });
            }

            // Setup responsible name field as an independent selection
            function setupResponsibleNameField() {
                // Process citizens for Select2 - only names
                function prepareNameOptions() {
                    const nameOptions = [];

                    allCitizens.forEach(citizen => {
                        // Only add if full_name is available
                        if (citizen.full_name) {
                            nameOptions.push({
                                id: citizen.full_name,
                                text: citizen.full_name
                            });
                        }
                    });

                    return nameOptions;
                }

                const nameOptions = prepareNameOptions();
                const responsibleSelect = document.getElementById('responsibleNameSelect');

                // Initialize Name Select2 with pre-loaded data
                $(responsibleSelect).select2({
                    placeholder: 'Pilih Nama Penanggung Jawab',
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
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const provinceId = document.getElementById('province_id').value;
                const districtId = document.getElementById('district_id').value;
                const subDistrictId = document.getElementById('subdistrict_id').value;
                const villageId = document.getElementById('village_id').value;

                if (!provinceId || !districtId || !subDistrictId || !villageId) {
                    e.preventDefault();
                    alert('Silakan pilih Provinsi, Kabupaten, Kecamatan, dan Desa');
                    return false;
                }
            });
        });
    </script>
</x-layout>
