<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat SKCK</h1>

        <form method="POST" action="{{ route('superadmin.surat.skck.update', $skck->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <input type="number" id="nik" name="nik" value="{{ $skck->nik }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ $skck->full_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ $skck->birth_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ $skck->birth_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="1" {{ $skck->gender == '1' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="2" {{ $skck->gender == '2' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Pekerjaan -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Pekerjaan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ $skck->job_type_id == $job['id'] ? 'selected' : '' }}>{{ $job['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Agama</option>
                        <option value="1" {{ $skck->religion == '1' ? 'selected' : '' }}>Islam</option>
                        <option value="2" {{ $skck->religion == '2' ? 'selected' : '' }}>Kristen</option>
                        <option value="3" {{ $skck->religion == '3' ? 'selected' : '' }}>Katholik</option>
                        <option value="4" {{ $skck->religion == '4' ? 'selected' : '' }}>Hindu</option>
                        <option value="5" {{ $skck->religion == '5' ? 'selected' : '' }}>Buddha</option>
                        <option value="6" {{ $skck->religion == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                        <option value="7" {{ $skck->religion == '7' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="1" {{ $skck->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                        <option value="2" {{ $skck->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                    </select>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $skck->address }}</textarea>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" value="{{ $skck->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="text" id="letter_number" name="letter_number" value="{{ $skck->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Surat -->
                <div>
                    <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" id="letter_date" name="letter_date" value="{{ $skck->letter_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $skck->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Dipergunakan Untuk -->
                <div class="md:col-span-2">
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Dipergunakan Untuk <span class="text-red-500">*</span></label>
                    <textarea id="purpose" name="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $skck->purpose }}</textarea>
                </div>

                <!-- Provinsi section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $skck->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $skck->province_id }}">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $skck->district_id }}">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $skck->subdistrict_id }}">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Memuat data...</option>
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $skck->village_id }}">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
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
            const originalProvinceId = "{{ $skck->province_id }}";
            const originalDistrictId = "{{ $skck->district_id }}";
            const originalSubdistrictId = "{{ $skck->subdistrict_id }}";
            const originalVillageId = "{{ $skck->village_id }}";

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

            // Form validation to check if both IDs and codes are set
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

            // Initialize dropdown data on page load
            // Find the selected province option and get its code value
            const selectedProvinceOption = [...provinceSelect.options].find(option =>
                option.getAttribute('data-id') === originalProvinceId);

            if (selectedProvinceOption) {
                // Load the district data for this province
                loadDistricts(selectedProvinceOption.value, originalDistrictId);
            }
        });
    </script>
</x-layout>
