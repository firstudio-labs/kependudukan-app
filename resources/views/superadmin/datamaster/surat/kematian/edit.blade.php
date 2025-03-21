<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Keterangan Kematian</h1>

        <form method="POST" action="{{ route('superadmin.surat.kematian.update', $kematian->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $kematian->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $kematian->province_id }}">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $kematian->district_id == $district['id'] ? 'selected' : '' }}>{{ $district['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $kematian->district_id }}">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $kematian->subdistrict_id == $subDistrict['id'] ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $kematian->subdistrict_id }}">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $kematian->village_id == $village['id'] ? 'selected' : '' }}>{{ $village['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $kematian->village_id }}">
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="number" id="letter_number" name="letter_number" value="{{ $kematian->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $kematian->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Deceased Person Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Almarhum/Almarhumah</h2>
                <div id="deceased-container" class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nik" name="nik" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih NIK</option>
                                @if(isset($kematian->nik) && !is_array($kematian->nik))
                                    <option value="{{ $kematian->nik }}" selected>{{ $kematian->nik }}</option>
                                @elseif(isset($kematian->nik[0]))
                                    <option value="{{ $kematian->nik[0] }}" selected>{{ $kematian->nik[0] }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <select id="full_name" name="full_name" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama</option>
                                @if(isset($kematian->full_name) && !is_array($kematian->full_name))
                                    <option value="{{ $kematian->full_name }}" selected>{{ $kematian->full_name }}</option>
                                @elseif(isset($kematian->full_name[0]))
                                    <option value="{{ $kematian->full_name[0] }}" selected>{{ $kematian->full_name[0] }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir -->
                        <div>
                            <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="birth_place" name="birth_place" value="{{ is_array($kematian->birth_place) ? ($kematian->birth_place[0] ?? '') : $kematian->birth_place }}" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ is_array($kematian->birth_date) ? (isset($kematian->birth_date[0]) ? date('Y-m-d', strtotime($kematian->birth_date[0])) : '') : (isset($kematian->birth_date) ? date('Y-m-d', strtotime($kematian->birth_date)) : '') }}" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="gender" name="gender" class="gender mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="1" {{ (is_array($kematian->gender) ? ($kematian->gender[0] ?? '') : $kematian->gender) == '1' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="2" {{ (is_array($kematian->gender) ? ($kematian->gender[0] ?? '') : $kematian->gender) == '2' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Pekerjaan -->
                        <div>
                            <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                            <select id="job_type_id" name="job_type_id" class="job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Pekerjaan</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job['id'] }}" {{
                                        (is_array($kematian->job_type_id) ? ($kematian->job_type_id[0] ?? '') : $kematian->job_type_id) == $job['id']
                                        || (is_array($kematian->job) ? ($kematian->job[0] ?? '') : $kematian->job) == $job['name']
                                        ? 'selected' : ''
                                    }}>{{ $job['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="job" name="job" value="{{ is_array($kematian->job) ? ($kematian->job[0] ?? '') : $kematian->job }}">
                        </div>

                        <!-- Agama -->
                        <div>
                            <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                            <select id="religion" name="religion" class="religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Agama</option>
                                <option value="1" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '1' ? 'selected' : '' }}>Islam</option>
                                <option value="2" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '2' ? 'selected' : '' }}>Kristen</option>
                                <option value="3" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '3' ? 'selected' : '' }}>Katholik</option>
                                <option value="4" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '4' ? 'selected' : '' }}>Hindu</option>
                                <option value="5" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '5' ? 'selected' : '' }}>Buddha</option>
                                <option value="6" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                                <option value="7" {{ (is_array($kematian->religion) ? ($kematian->religion[0] ?? '') : $kematian->religion) == '7' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Kewarganegaraan -->
                        <div>
                            <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                            <select id="citizen_status" name="citizen_status" class="citizen-status mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kewarganegaraan</option>
                                <option value="1" {{ (is_array($kematian->citizen_status) ? ($kematian->citizen_status[0] ?? '') : $kematian->citizen_status) == '1' ? 'selected' : '' }}>WNA</option>
                                <option value="2" {{ (is_array($kematian->citizen_status) ? ($kematian->citizen_status[0] ?? '') : $kematian->citizen_status) == '2' ? 'selected' : '' }}>WNI</option>
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ is_array($kematian->address) ? ($kematian->address[0] ?? '') : $kematian->address }}</textarea>
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
                            <input type="text" id="info" name="info" value="{{ $kematian->info }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RT Asal Surat -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT Asal Surat</label>
                            <input type="number" id="rt" name="rt" value="{{ $kematian->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tanggal Surat RT -->
                        <div>
                            <label for="rt_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat RT</label>
                            <input type="date" id="rt_letter_date" name="rt_letter_date" value="{{ $kematian->rt_letter_date ? date('Y-m-d', strtotime($kematian->rt_letter_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Penyebab Kematian -->
                        <div>
                            <label for="death_cause" class="block text-sm font-medium text-gray-700">Penyebab Kematian <span class="text-red-500">*</span></label>
                            <input type="text" id="death_cause" name="death_cause" value="{{ $kematian->death_cause }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Kematian -->
                        <div>
                            <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Kematian <span class="text-red-500">*</span></label>
                            <input type="text" id="death_place" name="death_place" value="{{ $kematian->death_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Meninggal -->
                        <div>
                            <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                            <input type="date" id="death_date" name="death_date" value="{{ isset($kematian->death_date) ? date('Y-m-d', strtotime($kematian->death_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Nama Pelapor -->
                        <div>
                            <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor <span class="text-red-500">*</span></label>
                            <input type="text" id="reporter_name" name="reporter_name" value="{{ $kematian->reporter_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Hubungan Pelapor -->
                        <div>
                            <label for="reporter_relation" class="block text-sm font-medium text-gray-700">Hubungan Pelapor <span class="text-red-500">*</span></label>
                            <select id="reporter_relation" name="reporter_relation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="Suami" {{ $kematian->reporter_relation == 'Suami' || $kematian->reporter_relation == '1' ? 'selected' : '' }}>Suami</option>
                                <option value="Istri" {{ $kematian->reporter_relation == 'Istri' || $kematian->reporter_relation == '2' ? 'selected' : '' }}>Istri</option>
                                <option value="Anak" {{ $kematian->reporter_relation == 'Anak' || $kematian->reporter_relation == '3' ? 'selected' : '' }}>Anak</option>
                                <option value="Ayah" {{ $kematian->reporter_relation == 'Ayah' || $kematian->reporter_relation == '4' ? 'selected' : '' }}>Ayah</option>
                                <option value="Ibu" {{ $kematian->reporter_relation == 'Ibu' || $kematian->reporter_relation == '5' ? 'selected' : '' }}>Ibu</option>
                                <option value="Saudara" {{ $kematian->reporter_relation == 'Saudara' || $kematian->reporter_relation == '6' ? 'selected' : '' }}>Saudara</option>
                                <option value="Lainnya" {{ $kematian->reporter_relation == 'Lainnya' || $kematian->reporter_relation == '7' ? 'selected' : '' }}>Lainnya</option>
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
            // Location selection handling
            setupLocationHandlers();

            // Initialize Select2 for NIK and Name fields
            initializeDeceasedSelects();
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
            const originalProvinceId = "{{ $kematian->province_id }}";
            const originalDistrictId = "{{ $kematian->district_id }}";
            const originalSubdistrictId = "{{ $kematian->subdistrict_id }}";
            const originalVillageId = "{{ $kematian->village_id }}";

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

            // Find the selected province option and get its code value
            const selectedProvinceOption = [...provinceSelect.options].find(option =>
                option.getAttribute('data-id') === originalProvinceId);

            if (selectedProvinceOption) {
                // Load the district data for this province
                loadDistricts(selectedProvinceOption.value, originalDistrictId);
            }
        }

        function initializeDeceasedSelects() {
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

                    // Now initialize Select2 with the pre-loaded data
                    setupDeceasedSelects();
                },
                error: function(error) {
                    console.error('Failed to load citizen data:', error);
                    // Initialize with empty data as fallback
                    setupDeceasedSelects();
                }
            });

            function setupDeceasedSelects() {
                // Create NIK and name options arrays
                const nikOptions = [];
                const nameOptions = [];

                // Process citizen data for Select2
                for (let i = 0; i < allCitizens.length; i++) {
                    const citizen = allCitizens[i];

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
                }

                // Define isUpdating in the global scope so all handlers can access it
                let isUpdating = false;

                // Initialize NIK select with pre-loaded data
                $('#nik').select2({
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
                }).on('select2:select', function(e) {
                    if (isUpdating) return;
                    isUpdating = true;

                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        // Update full name select
                        $('#full_name').val(citizen.full_name).trigger('change.select2');
                        // Populate deceased person fields
                        populateDeceasedFields(citizen);
                    }

                    isUpdating = false;
                });

                // Initialize Name select with pre-loaded data
                $('#full_name').select2({
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
                }).on('select2:select', function(e) {
                    if (isUpdating) return;
                    isUpdating = true;

                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        // Update NIK select
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $('#nik').val(nikValue).trigger('change.select2');
                        // Populate deceased person fields
                        populateDeceasedFields(citizen);
                    }

                    isUpdating = false;
                });

                // Make sure the current values are properly displayed in Select2 dropdowns
                const currentNik = $('#nik').val();
                const currentName = $('#full_name').val();

                if (currentNik && currentNik !== '') {
                    // Check if the option exists in our data
                    if ($('#nik').find(`option[value="${currentNik}"]`).length === 0) {
                        // Create and select the option with the existing value
                        const newOption = new Option(currentNik, currentNik, true, true);
                        $('#nik').append(newOption).trigger('change');
                    } else {
                        // Just make sure the option is selected
                        $('#nik').val(currentNik).trigger('change');
                    }
                }

                if (currentName && currentName !== '') {
                    // Check if the option exists in our data
                    if ($('#full_name').find(`option[value="${currentName}"]`).length === 0) {
                        // Create and select the option with the existing value
                        const newOption = new Option(currentName, currentName, true, true);
                        $('#full_name').append(newOption).trigger('change');
                    } else {
                        // Just make sure the option is selected
                        $('#full_name').val(currentName).trigger('change');
                    }
                }
            }

            // Helper function to populate deceased person fields
            function populateDeceasedFields(citizen) {
                // Birth place
                $('#birth_place').val(citizen.birth_place || '');

                // Format birth date if needed
                if (citizen.birth_date) {
                    let birthDate = citizen.birth_date;
                    if (birthDate.includes('/')) {
                        const parts = birthDate.split('/');
                        birthDate = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                    } else if (birthDate.includes(' ')) {
                        // Handle datetime format (YYYY-MM-DD HH:MM:SS)
                        birthDate = birthDate.split(' ')[0];
                    }
                    $('#birth_date').val(birthDate);
                }

                // Set gender
                $('#gender').val(citizen.gender).trigger('change');

                // Set job
                if (citizen.job_type_id) {
                    $('#job_type_id').val(citizen.job_type_id).trigger('change');
                    // Update the hidden job field with the job name
                    const selectedOption = $('#job_type_id option:selected');
                    if (selectedOption.length) {
                        $('#job').val(selectedOption.text());
                    }
                } else if (citizen.job) {
                    $('#job').val(citizen.job);
                    // Try to find a matching job in the dropdown
                    $('#job_type_id option').each(function() {
                        if ($(this).text().toLowerCase() === citizen.job.toLowerCase()) {
                            $('#job_type_id').val($(this).val()).trigger('change');
                        }
                    });
                }

                // Set religion
                $('#religion').val(citizen.religion).trigger('change');

                // Set citizen status
                $('#citizen_status').val(citizen.citizen_status).trigger('change');

                // Set address
                $('#address').val(citizen.address || '');
            }

            // Add job select change handler to update the hidden job field
            $('#job_type_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.val() !== '') {
                    $('#job').val(selectedOption.text());
                }
            });
        }
    </script>
</x-layout>
