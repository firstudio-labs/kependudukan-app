<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Kematian</h1>

        <form method="POST" action="{{ route('superadmin.surat.kematian.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Section -->
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

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Desa</option>
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="">
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="number" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Deceased Person Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Almarhum/Almarhumah</h2>
                <div id="deceased-container">
                    <div class="deceased-row border p-4 rounded-md mb-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- NIK -->
                            <div>
                                <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                <select id="nikSelect" name="nik" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih NIK</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div>
                                <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <select id="fullNameSelect" name="full_name" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Nama</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <!-- Tempat Lahir -->
                            <div>
                                <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" id="birth_place" name="birth_place" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" id="birth_date" name="birth_date" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <!-- Jenis Kelamin -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select id="gender" name="gender" class="gender mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="1">Laki-Laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>

                            <!-- Pekerjaan -->
                            <div>
                                <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                                <select id="job_type_id" name="job_type_id" class="job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Pekerjaan</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Agama -->
                            <div>
                                <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                                <select id="religion" name="religion" class="religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="1">Islam</option>
                                    <option value="2">Kristen</option>
                                    <option value="3">Katholik</option>
                                    <option value="4">Hindu</option>
                                    <option value="5">Buddha</option>
                                    <option value="6">Kong Hu Cu</option>
                                    <option value="7">Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <!-- Kewarganegaraan -->
                            <div>
                                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                                <select id="citizen_status" name="citizen_status" class="citizen-status mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Kewarganegaraan</option>
                                    <option value="1">WNA</option>
                                    <option value="2">WNI</option>
                                </select>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                <textarea id="address" name="address" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Death Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Kematian</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dasar Keterangan -->
                        <div>
                            <label for="info" class="block text-sm font-medium text-gray-700">Dasar Keterangan <span class="text-red-500">*</span></label>
                            <input type="text" id="info" name="info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RT Asal Surat -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT Asal Surat</label>
                            <input type="number" id="rt" name="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tanggal Surat RT -->
                        <div>
                            <label for="rt_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat RT</label>
                            <input type="date" id="rt_letter_date" name="rt_letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Penyebab Kematian -->
                        <div>
                            <label for="death_cause" class="block text-sm font-medium text-gray-700">Penyebab Kematian <span class="text-red-500">*</span></label>
                            <input type="text" id="death_cause" name="death_cause" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Kematian -->
                        <div>
                            <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Kematian <span class="text-red-500">*</span></label>
                            <input type="text" id="death_place" name="death_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Meninggal -->
                        <div>
                            <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                            <input type="date" id="death_date" name="death_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Nama Pelapor -->
                        <div>
                            <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor <span class="text-red-500">*</span></label>
                            <input type="text" id="reporter_name" name="reporter_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Hubungan Pelapor -->
                        <div>
                            <label for="reporter_relation" class="block text-sm font-medium text-gray-700">Hubungan Pelapor <span class="text-red-500">*</span></label>
                            <select id="reporter_relation" name="reporter_relation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="1">Suami</option>
                                <option value="2">Istri</option>
                                <option value="3">Anak</option>
                                <option value="4">Ayah</option>
                                <option value="5">Ibu</option>
                                <option value="6">Saudara</option>
                                <option value="7">Lainnya</option>
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
                    setupDeceasedPersonInterface();
                },
                error: function(error) {
                    console.error('Failed to load citizen data:', error);
                    // Setup the interfaces anyway with empty data
                    setupLocationHandlers();
                    setupDeceasedPersonInterface();
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
                function populateSelect(select, data, defaultText, hiddenInput = null) {
                    try {
                        select.innerHTML = `<option value="">${defaultText}</option>`;

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.code;
                            option.textContent = item.name;
                            option.setAttribute('data-id', item.id);
                            select.appendChild(option);
                        });

                        select.disabled = false;

                        if (hiddenInput) hiddenInput.value = '';
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
            }

            function setupDeceasedPersonInterface() {
                const deceasedRow = document.querySelector('.deceased-row');

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

                function initializeSelect2ForDeceased() {
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

                            // Fill personal data fields
                            populateDeceasedPersonFields(citizen);
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

                            // Fill personal data fields
                            populateDeceasedPersonFields(citizen);
                        }

                        isUpdating = false;
                    });
                }

                // Function to populate deceased person fields from citizen data
                function populateDeceasedPersonFields(citizen) {
                    // Birth place
                    $('#birth_place').val(citizen.birth_place || '');

                    // Birth date - handle formatting
                    if (citizen.birth_date) {
                        let birthDate = citizen.birth_date;
                        if (birthDate.includes('/')) {
                            const [day, month, year] = birthDate.split('/');
                            birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        }
                        $('#birth_date').val(birthDate);
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
                    $('#gender').val(gender).trigger('change');

                    // Job - fill with job type id if available
                    if (citizen.job_type_id) {
                        $('#job_type_id').val(citizen.job_type_id).trigger('change');
                    }

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
                    $('#religion').val(religion).trigger('change');

                    // Citizenship status - handle conversion
                    let citizenStatus = citizen.citizen_status;
                    if (typeof citizenStatus === 'string') {
                        if (citizenStatus.toLowerCase() === 'wna') {
                            citizenStatus = 1;
                        } else if (citizenStatus.toLowerCase() === 'wni') {
                            citizenStatus = 2;
                        }
                    }
                    $('#citizen_status').val(citizenStatus).trigger('change');

                    // Address
                    $('#address').val(citizen.address || '');
                }

                // Initialize Select2 for the deceased person
                initializeSelect2ForDeceased();
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

            // Add job select change handler
            document.getElementById('job_type_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption) {
                    document.getElementById('job_name').value = selectedOption.text;
                }
            });
        });
    </script>
</x-layout>
