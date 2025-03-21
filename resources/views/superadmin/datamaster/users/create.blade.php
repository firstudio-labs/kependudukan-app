<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Pengguna Baru</h1>

        <!-- Form Create User -->
        <form action="{{ route('superadmin.datamaster.user.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- BIODATA -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold pb-2 border-b border-gray-300 mb-4 text-gray-700">Biodata</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" id="nik" name="nik" placeholder="Masukkan NIK" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        @error('nik')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan Username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('username')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan Nomor HP" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('no_hp')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" minlength="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Role</option>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                            <option value="user">User</option>
                        </select>
                        @error('role')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                        @error('alamat')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- TEMPAT WILAYAH NAUNGAN -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold pb-2 border-b border-gray-300 mb-4 text-gray-700">Tempat Wilayah Naungan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Province -->
                    <div>
                        <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="">
                        @error('province_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- District/City -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                        <input type="hidden" id="districts_id" name="districts_id" value="">
                        @error('districts_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Subdistrict -->
                    <div>
                        <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="sub_district_code" name="sub_district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" id="sub_districts_id" name="sub_districts_id" value="">
                        @error('sub_districts_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Village -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        <input type="hidden" id="villages_id" name="villages_id" value="">
                        @error('villages_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="w-full bg-white text-gray-700 p-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="w-full bg-[#7886C7] text-white p-2 rounded-md shadow-md hover:bg-[#2D336B] ml-4">Simpan</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province_code');
        const districtSelect = document.getElementById('district_code');
        const subDistrictSelect = document.getElementById('sub_district_code');
        const villageSelect = document.getElementById('village_code');

        // Hidden inputs for IDs
        const provinceIdInput = document.getElementById('province_id');
        const districtIdInput = document.getElementById('districts_id');
        const subDistrictIdInput = document.getElementById('sub_districts_id');
        const villageIdInput = document.getElementById('villages_id');

        // Log DOM elements to verify they're properly found
        console.log('DOM Elements:', {
            provinceSelect,
            districtSelect,
            subDistrictSelect,
            villageSelect,
            provinceIdInput,
            districtIdInput,
            subDistrictIdInput,
            villageIdInput
        });

        // Helper function to reset select options
        function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
            select.innerHTML = `<option value="">${defaultText}</option>`;
            select.disabled = true;
            if (hiddenInput) hiddenInput.value = '';
        }

        // Helper function to populate select options with code as value and id as data attribute
        function populateSelect(select, data, defaultText, hiddenInput = null) {
            try {
                console.log(`Populating ${select.id} with:`, data);
                select.innerHTML = `<option value="">${defaultText}</option>`;

                // Check if data is available and is an array
                if (!Array.isArray(data)) {
                    console.error(`Data for ${select.id} is not an array:`, data);
                    resetSelect(select, 'Invalid data format', hiddenInput);
                    return;
                }

                data.forEach(item => {
                    if (!item || !item.code || !item.name || !item.id) {
                        console.warn('Invalid item in data:', item);
                        return;
                    }
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = item.name;
                    option.setAttribute('data-id', item.id);
                    select.appendChild(option);
                });

                select.disabled = false;
                console.log(`${select.id} now has ${select.options.length} options`);

                if (hiddenInput) hiddenInput.value = '';
            } catch (error) {
                console.error(`Error populating ${select.id}:`, error);
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
                console.log(`Updated ${hiddenInput.id} to ${hiddenInput.value}`);
            } else {
                hiddenInput.value = '';
                console.log(`Cleared ${hiddenInput.id} value`);
            }
        }

        // Province change handler
        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;
            console.log(`Province selected: ${provinceCode}`);

            if (this.selectedIndex > 0) {
                console.log(`Selected province element:`, this.options[this.selectedIndex]);
                console.log(`Selected province name:`, this.options[this.selectedIndex].textContent);
                console.log(`Selected province data-id:`, this.options[this.selectedIndex].getAttribute('data-id'));
            }

            // Update the hidden input with the ID
            updateHiddenInput(this, provinceIdInput);

            resetSelect(districtSelect, 'Loading...', districtIdInput);
            resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
            resetSelect(villageSelect, 'Pilih Desa/Kelurahan', villageIdInput);

            if (provinceCode) {
                const url = `{{ url('/location/districts') }}/${provinceCode}`;
                console.log(`Fetching districts from URL: ${url}`);

                fetch(url)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.text();  // Get raw text first for debugging
                    })
                    .then(rawText => {
                        console.log('Raw response:', rawText);
                        try {
                            const data = JSON.parse(rawText);
                            console.log(`Parsed response data:`, data);

                            if (data && Array.isArray(data) && data.length > 0) {
                                populateSelect(districtSelect, data, 'Pilih Kabupaten/Kota', districtIdInput);
                                districtSelect.disabled = false;
                            } else if (data && data.error) {
                                console.error('API returned error:', data.error);
                                resetSelect(districtSelect, 'Error: ' + data.error, districtIdInput);
                            } else {
                                console.warn('District data is empty or not in expected format');
                                resetSelect(districtSelect, 'No data available', districtIdInput);
                            }
                        } catch (jsonError) {
                            console.error('JSON parsing error:', jsonError);
                            resetSelect(districtSelect, 'Error parsing data', districtIdInput);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching districts:', error);
                        resetSelect(districtSelect, 'Error loading data', districtIdInput);
                    });
            }
        });

        // District change handler
        districtSelect.addEventListener('change', function() {
            const districtCode = this.value;
            console.log(`District selected: ${districtCode}`);

            // Update hidden input with ID
            updateHiddenInput(this, districtIdInput);

            resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
            resetSelect(villageSelect, 'Pilih Desa/Kelurahan', villageIdInput);

            if (districtCode) {
                fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(`Received ${data.length} sub-districts:`, data);
                        if (data && data.length > 0) {
                            populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                            subDistrictSelect.disabled = false;
                        } else {
                            resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching sub-districts:', error);
                        resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                    });
            }
        });

        // Sub-district change handler
        subDistrictSelect.addEventListener('change', function() {
            const subDistrictCode = this.value;
            console.log(`Sub-district selected: ${subDistrictCode}`);

            // Update hidden input with ID
            updateHiddenInput(this, subDistrictIdInput);

            resetSelect(villageSelect, 'Loading...', villageIdInput);

            if (subDistrictCode) {
                fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(`Received ${data.length} villages:`, data);
                        if (data && data.length > 0) {
                            populateSelect(villageSelect, data, 'Pilih Desa/Kelurahan', villageIdInput);
                            villageSelect.disabled = false;
                        } else {
                            resetSelect(villageSelect, 'No data available', villageIdInput);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching villages:', error);
                        resetSelect(villageSelect, 'Error loading data', villageIdInput);
                    });
            }
        });

        // Village change handler
        villageSelect.addEventListener('change', function() {
            // Update hidden input with ID
            updateHiddenInput(this, villageIdInput);
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            // Check if province_id is set but districts_id is missing when a district is selected
            if (provinceSelect.value && !provinceIdInput.value) {
                e.preventDefault();
                alert('ID Provinsi tidak valid, silakan pilih ulang provinsi.');
                return;
            }
            if (districtSelect.value && !districtIdInput.value) {
                e.preventDefault();
                alert('ID Kabupaten/Kota tidak valid, silakan pilih ulang kabupaten/kota.');
                return;
            }
            if (subDistrictSelect.value && !subDistrictIdInput.value) {
                e.preventDefault();
                alert('ID Kecamatan tidak valid, silakan pilih ulang kecamatan.');
                return;
            }
            if (villageSelect.value && !villageIdInput.value) {
                e.preventDefault();
                alert('ID Desa/Kelurahan tidak valid, silakan pilih ulang desa/kelurahan.');
                return;
            }
        });
    });
    </script>
    @endpush
</x-layout>
