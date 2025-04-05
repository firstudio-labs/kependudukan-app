<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Izin Rumah Sewa</h1>

        <form method="POST" action="{{ route('superadmin.surat.rumah-sewa.update', $rumahSewa->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <!-- Organizer Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Penyelenggara</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nikSelect" name="nik" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih NIK</option>
                                @if($rumahSewa->nik)
                                    <option value="{{ $rumahSewa->nik }}" selected>{{ $rumahSewa->nik }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- Nama Penyelenggara -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Penyelenggara <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama</option>
                                @if($rumahSewa->full_name)
                                    <option value="{{ $rumahSewa->full_name }}" selected>{{ $rumahSewa->full_name }}</option>
                                @endif
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
                                <option value="">Pilih Nama Penanggung Jawab</option>
                                @if($rumahSewa->responsible_name)
                                    <option value="{{ $rumahSewa->responsible_name }}" selected>{{ $rumahSewa->responsible_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wilayah Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Provinsi -->
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

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kabupaten</option>
                                <!-- Districts will be loaded via JavaScript -->
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="{{ $rumahSewa->district_id }}">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kecamatan</option>
                                <!-- Subdistricts will be loaded via JavaScript -->
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $rumahSewa->subdistrict_id }}">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Desa</option>
                                <!-- Villages will be loaded via JavaScript -->
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="{{ $rumahSewa->village_id }}">
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

                        <!-- RT Field -->
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- Berlaku Ijin Sampai -->
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700">Berlaku Ijin Sampai</label>
                            <input type="date" id="valid_until" name="valid_until" value="{{ $rumahSewa->valid_until ? date('Y-m-d', strtotime($rumahSewa->valid_until)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" value="{{ $rumahSewa->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Pejabat Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->judul }}" {{ $rumahSewa->signing == $signer->judul ? 'selected' : '' }}>
                                        {{ $signer->judul }} - {{ $signer->keterangan }}
                                    </option>
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
            // Ensure the signing field is correctly submitted as an ID
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const signingSelect = document.getElementById('signing');
                if (signingSelect.value) {
                    // Make sure it's treated as a numeric ID
                    signingSelect.value = parseInt(signingSelect.value, 10) || signingSelect.value;
                }
            });

            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Store original values for comparison
            const originalProvinceId = "{{ $rumahSewa->province_id }}";
            const originalDistrictId = "{{ $rumahSewa->district_id }}";
            const originalSubdistrictId = "{{ $rumahSewa->subdistrict_id }}";
            const originalVillageId = "{{ $rumahSewa->village_id }}";

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

                const provinceIdInput = document.getElementById('province_id');
                const districtIdInput = document.getElementById('district_id');
                const subDistrictIdInput = document.getElementById('subdistrict_id');
                const villageIdInput = document.getElementById('village_id');

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

                            // Pre-select the option if it matches selectedId
                            if (selectedId && item.id.toString() === selectedId.toString()) {
                                option.selected = true;
                                if (hiddenInput) hiddenInput.value = item.id;
                            }

                            select.appendChild(option);
                        });

                        select.disabled = false;
                    } catch (error) {
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

                // Initialize location dropdowns with existing data
                function initializeLocationDropdowns() {
                    // Find selected province option
                    const selectedProvinceOption = [...provinceSelect.options].find(option =>
                        option.getAttribute('data-id') === originalProvinceId
                    );

                    if (selectedProvinceOption) {
                        // Load districts for selected province
                        fetch(`{{ url('/location/districts') }}/${selectedProvinceOption.value}`)
                            .then(response => response.json())
                            .then(districts => {
                                populateSelect(districtSelect, districts, 'Pilih Kabupaten', districtIdInput, originalDistrictId);

                                // Find selected district option
                                const selectedDistrictOption = [...districtSelect.options].find(option =>
                                    option.getAttribute('data-id') === originalDistrictId
                                );

                                if (selectedDistrictOption) {
                                    // Load subdistricts for selected district
                                    fetch(`{{ url('/location/sub-districts') }}/${selectedDistrictOption.value}`)
                                        .then(response => response.json())
                                        .then(subdistricts => {
                                            populateSelect(subDistrictSelect, subdistricts, 'Pilih Kecamatan', subDistrictIdInput, originalSubdistrictId);

                                            // Find selected subdistrict option
                                            const selectedSubdistrictOption = [...subDistrictSelect.options].find(option =>
                                                option.getAttribute('data-id') === originalSubdistrictId
                                            );

                                            if (selectedSubdistrictOption) {
                                                // Load villages for selected subdistrict
                                                fetch(`{{ url('/location/villages') }}/${selectedSubdistrictOption.value}`)
                                                    .then(response => response.json())
                                                    .then(villages => {
                                                        populateSelect(villageSelect, villages, 'Pilih Desa', villageIdInput, originalVillageId);
                                                    })
                                                    .catch(error => {
                                                        console.error('Error loading villages:', error);
                                                    });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error loading subdistricts:', error);
                                        });
                                }
                            })
                            .catch(error => {
                                console.error('Error loading districts:', error);
                            });
                    }
                }

                // Province change handler
                provinceSelect.addEventListener('change', function() {
                    const provinceCode = this.value;
                    updateHiddenInput(this, provinceIdInput);

                    resetSelect(districtSelect, 'Loading...', districtIdInput);
                    resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                    if (provinceCode) {
                        fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput);
                                    districtSelect.disabled = false;
                                } else {
                                    resetSelect(districtSelect, 'No data available', districtIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(districtSelect, 'Error loading data', districtIdInput);
                            });
                    }
                });

                // District change handler
                districtSelect.addEventListener('change', function() {
                    const districtCode = this.value;
                    updateHiddenInput(this, districtIdInput);

                    resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                    if (districtCode) {
                        fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                                    subDistrictSelect.disabled = false;
                                } else {
                                    resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                            });
                    }
                });

                // Sub-district change handler
                subDistrictSelect.addEventListener('change', function() {
                    const subDistrictCode = this.value;
                    updateHiddenInput(this, subDistrictIdInput);

                    resetSelect(villageSelect, 'Loading...', villageIdInput);

                    if (subDistrictCode) {
                        fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput);
                                    villageSelect.disabled = false;
                                } else {
                                    resetSelect(villageSelect, 'No data available', villageIdInput);
                                }
                            })
                            .catch(error => {
                                resetSelect(villageSelect, 'Error loading data', villageIdInput);
                            });
                    }
                });

                // Village change handler
                villageSelect.addEventListener('change', function() {
                    updateHiddenInput(this, villageIdInput);
                });

                // Initialize location dropdowns on page load
                initializeLocationDropdowns();
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

                        // Also populate location fields from citizen data
                        populateLocationFields(citizen);
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

                        // Also populate location fields from citizen data
                        populateLocationFields(citizen);
                    }

                    isUpdating = false;
                });
            }

            // New function to populate location fields from citizen data
            function populateLocationFields(citizen) {
                // Support both naming conventions for subdistrict
                const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;

                // Only attempt to populate if we have valid location data
                if (!citizen.province_id || !citizen.district_id || !subDistrictId || !citizen.village_id) {
                    console.log('Incomplete location data for citizen');
                    return;
                }

                // Set hidden ID fields directly
                $('#province_id').val(citizen.province_id);
                $('#district_id').val(citizen.district_id);
                $('#subdistrict_id').val(subDistrictId);
                $('#village_id').val(citizen.village_id);

                // Find and select the correct province option
                const provinceSelect = document.getElementById('province_code');
                let provinceFound = false;

                for (let i = 0; i < provinceSelect.options.length; i++) {
                    const option = provinceSelect.options[i];
                    if (option.getAttribute('data-id') == citizen.province_id) {
                        provinceSelect.value = option.value;
                        provinceFound = true;

                        // Now load districts
                        fetch(`{{ url('/location/districts') }}/${option.value}`)
                            .then(response => response.json())
                            .then(districts => {
                                if (!districts || !Array.isArray(districts) || districts.length === 0) return;

                                // Populate district dropdown
                                const districtSelect = document.getElementById('district_code');
                                districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                                let districtFound = false;
                                let selectedDistrictCode = null;

                                districts.forEach(district => {
                                    const districtOption = document.createElement('option');
                                    districtOption.value = district.code;
                                    districtOption.textContent = district.name;
                                    districtOption.setAttribute('data-id', district.id);

                                    if (district.id == citizen.district_id) {
                                        districtOption.selected = true;
                                        selectedDistrictCode = district.code;
                                        districtFound = true;
                                    }

                                    districtSelect.appendChild(districtOption);
                                });

                                districtSelect.disabled = false;

                                if (districtFound && selectedDistrictCode) {
                                    // Load subdistricts
                                    fetch(`{{ url('/location/sub-districts') }}/${selectedDistrictCode}`)
                                        .then(response => response.json())
                                        .then(subdistricts => {
                                            if (!subdistricts || !Array.isArray(subdistricts) || subdistricts.length === 0) return;

                                            // Populate subdistrict dropdown
                                            const subdistrictSelect = document.getElementById('subdistrict_code');
                                            subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

                                            let subdistrictFound = false;
                                            let selectedSubdistrictCode = null;

                                            subdistricts.forEach(subdistrict => {
                                                const subdistrictOption = document.createElement('option');
                                                subdistrictOption.value = subdistrict.code;
                                                subdistrictOption.textContent = subdistrict.name;
                                                subdistrictOption.setAttribute('data-id', subdistrict.id);

                                                if (subdistrict.id == subDistrictId) {
                                                    subdistrictOption.selected = true;
                                                    selectedSubdistrictCode = subdistrict.code;
                                                    subdistrictFound = true;
                                                }

                                                subdistrictSelect.appendChild(subdistrictOption);
                                            });

                                            subdistrictSelect.disabled = false;

                                            if (subdistrictFound && selectedSubdistrictCode) {
                                                // Load villages
                                                fetch(`{{ url('/location/villages') }}/${selectedSubdistrictCode}`)
                                                    .then(response => response.json())
                                                    .then(villages => {
                                                        if (!villages || !Array.isArray(villages) || villages.length === 0) return;

                                                        // Populate village dropdown
                                                        const villageSelect = document.getElementById('village_code');
                                                        villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

                                                        villages.forEach(village => {
                                                            const villageOption = document.createElement('option');
                                                            villageOption.value = village.code;
                                                            villageOption.textContent = village.name;
                                                            villageOption.setAttribute('data-id', village.id);

                                                            if (village.id == citizen.village_id) {
                                                                villageOption.selected = true;
                                                            }

                                                            villageSelect.appendChild(villageOption);
                                                        });

                                                        villageSelect.disabled = false;
                                                    })
                                                    .catch(error => {
                                                        console.error('Error loading villages:', error);
                                                    });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error loading subdistricts:', error);
                                        });
                                }
                            })
                            .catch(error => {
                                console.error('Error loading districts:', error);
                            });

                        break;
                    }
                }

                if (!provinceFound) {
                    console.log('Matching province not found in dropdown');
                }
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
