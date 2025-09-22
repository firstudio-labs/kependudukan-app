<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Pengguna</h1>

        <!-- Form Edit User -->
        <form action="{{ route('superadmin.datamaster.user.update', $user->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}"
                                    {{ old('province_id', $user->province_id) == $province['id'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="{{ old('province_id', $user->province_id) }}">
                        @error('province_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- District/City -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" {{ count($districts) > 0 ? '' : 'disabled' }}>
                            <option value="">Pilih Kabupaten/Kota</option>
                            @foreach($districts as $district)
                                <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}"
                                    {{ old('districts_id', $user->districts_id) == $district['id'] ? 'selected' : '' }}>
                                    {{ $district['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="districts_id" name="districts_id" value="{{ old('districts_id', $user->districts_id) }}">
                        @error('districts_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Subdistrict -->
                    <div>
                        <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="sub_district_code" name="sub_district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" {{ count($subDistricts) > 0 ? '' : 'disabled' }}>
                            <option value="">Pilih Kecamatan</option>
                            @foreach($subDistricts as $subDistrict)
                                <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}"
                                    {{ old('sub_districts_id', $user->sub_districts_id) == $subDistrict['id'] ? 'selected' : '' }}>
                                    {{ $subDistrict['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="sub_districts_id" name="sub_districts_id" value="{{ old('sub_districts_id', $user->sub_districts_id) }}">
                        @error('sub_districts_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Village -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" {{ count($villages) > 0 ? '' : 'disabled' }}>
                            <option value="">Pilih Desa/Kelurahan</option>
                            @foreach($villages as $village)
                                <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}"
                                    {{ old('villages_id', $user->villages_id) == $village['id'] ? 'selected' : '' }}>
                                    {{ $village['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="villages_id" name="villages_id" value="{{ old('villages_id', $user->villages_id) }}">
                        @error('villages_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- BIODATA -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold pb-2 border-b border-gray-300 mb-4 text-gray-700">Biodata</h2>
                <div class="grid grid-cols-1 gap-4">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" placeholder="Masukkan NIK" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        @error('nik')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kepala Desa <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" placeholder="Masukkan Nama Lengkap" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        @error('nama')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Masukkan Username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('username')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan Email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan Nomor HP" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('no_hp')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin desa" {{ old('role', $user->role) == 'admin desa' ? 'selected' : '' }}>Admin Desa</option>
                            <option value="admin kabupaten" {{ old('role', $user->role) == 'admin kabupaten' ? 'selected' : '' }}>Admin Kabupaten</option>
                            <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator</option>
                        </select>
                        @error('role')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Image/Logo Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Logo (Opsional)</label>

                        @if($user->image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $user->image) }}" alt="User Logo" class="w-32 h-32 object-cover rounded-md">
                            <p class="text-sm text-gray-500 mt-1">Logo saat ini</p>
                        </div>
                        @endif

                        <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maks: 2MB</p>
                        @error('image')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Foto Pengguna Upload -->
                    <div>
                        <label for="foto_pengguna" class="block text-sm font-medium text-gray-700">Foto Pengguna (Opsional)</label>

                        @if($user->foto_pengguna)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="User Photo" class="w-32 h-32 object-cover rounded-md">
                            <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                        @endif

                        <input type="file" id="foto_pengguna" name="foto_pengguna" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maks: 2MB</p>
                        @error('foto_pengguna')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- UBAH KATA SANDI -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold pb-2 border-b border-gray-300 mb-4 text-gray-700">Ubah Kata Sandi</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" minlength="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
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

        // Hidden inputs for IDs - Update variable names to match the actual HTML IDs
        const provinceIdInput = document.getElementById('province_id');
        const districtIdInput = document.getElementById('districts_id');
        const subDistrictIdInput = document.getElementById('sub_districts_id');
        const villageIdInput = document.getElementById('villages_id');

        // Log the initial values for debugging
        console.log('Initial location values:', {
            provinceId: provinceIdInput.value,
            districtId: districtIdInput.value,
            subDistrictId: subDistrictIdInput.value,
            villageId: villageIdInput.value
        });

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

                    // Select the option if it matches the selected ID
                    if (selectedId && item.id == selectedId) {
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
            }
        }

        // Update hidden input when selection changes
        function updateHiddenInput(select, hiddenInput) {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.hasAttribute('data-id')) {
                const newId = selectedOption.getAttribute('data-id');
                hiddenInput.value = newId;
                console.log(`Updated ${hiddenInput.id} to ${newId}`);
            } else {
                hiddenInput.value = '';
                console.log(`Cleared ${hiddenInput.id}`);
            }
        }

        // Initialize hidden inputs from selected options
        function initializeHiddenInputs() {
            const selects = [
                { select: provinceSelect, hiddenInput: provinceIdInput },
                { select: districtSelect, hiddenInput: districtIdInput },
                { select: subDistrictSelect, hiddenInput: subDistrictIdInput },
                { select: villageSelect, hiddenInput: villageIdInput }
            ];

            selects.forEach(item => {
                if (item.select.selectedIndex > 0) {
                    const selectedOption = item.select.options[item.select.selectedIndex];
                    if (selectedOption && selectedOption.hasAttribute('data-id')) {
                        const id = selectedOption.getAttribute('data-id');
                        item.hiddenInput.value = id;
                        console.log(`Initialized ${item.hiddenInput.id} to ${id}`);
                    }
                }
            });
        }

        // Call once to ensure hidden inputs have the correct values on page load
        initializeHiddenInputs();

        // Province change handler
        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;
            console.log(`Province selected: ${provinceCode}`);

            // Update the hidden input with the ID
            updateHiddenInput(this, provinceIdInput);

            resetSelect(districtSelect, 'Loading...', districtIdInput);
            resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
            resetSelect(villageSelect, 'Pilih Desa/Kelurahan', villageIdInput);

            if (provinceCode) {
                fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(`Received ${data.length} districts:`, data);
                        if (data && data.length > 0) {
                            populateSelect(districtSelect, data, 'Pilih Kabupaten/Kota', districtIdInput);
                            districtSelect.disabled = false;
                        } else {
                            resetSelect(districtSelect, 'No data available', districtIdInput);
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

        // Updated Village change handler that ensures ID is set correctly
        villageSelect.addEventListener('change', function() {
            // Get the selected option
            const selectedOption = this.options[this.selectedIndex];
            const villageCode = this.value;

            console.log(`Village selection changed to: ${this.value}`);

            // Check if a valid selection was made
            if (this.selectedIndex > 0 && selectedOption) {
                // Get the data-id attribute value (this is the actual ID we need)
                const villageId = selectedOption.getAttribute('data-id');
                console.log(`Selected village ID: ${villageId}, Code: ${villageCode}`);

                if (villageId) {
                    // Update the hidden input with the village ID
                    villageIdInput.value = villageId;
                    console.log(`Updated villages_id input to: ${villageId}`);
                } else {
                    console.error("Missing data-id attribute on selected village option!");
                }
            } else {
                // If no selection, clear the hidden input
                villageIdInput.value = '';
                console.log("Cleared villages_id input");
            }
        });

        // Add an extra check before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            // Verify that all fields have values before submission

            // Check villages specifically - force update from current selection
            if (villageSelect.selectedIndex > 0) {
                const selectedVillage = villageSelect.options[villageSelect.selectedIndex];
                const villageId = selectedVillage.getAttribute('data-id');

                if (villageId) {
                    // Ensure we set the ID correctly
                    villageIdInput.value = villageId;
                    console.log(`Form submit: Setting villages_id to ${villageId}`);
                }
            }

            // Check if selections were made but IDs are missing
            if (villageSelect.selectedIndex > 0 && !villageIdInput.value) {
                e.preventDefault();
                console.error("Village selected but villages_id is empty!");

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Data desa tidak valid. Silakan pilih ulang desa.',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }

            // Final debug log of all data being submitted
            console.log("FORM SUBMISSION DATA:");
            console.log("Province ID: " + provinceIdInput.value);
            console.log("District ID: " + districtIdInput.value);
            console.log("Subdistrict ID: " + subDistrictIdInput.value);
            console.log("Village ID: " + villageIdInput.value);
        });

        // Add a direct monitor to detect if hidden inputs are being modified
        // This helps track if something else might be affecting the values
        const monitorHiddenInput = function(input) {
            const originalValue = input.value;

            // Use an interval to check if value changes
            setInterval(function() {
                if (input.value !== originalValue) {
                    console.log(`Hidden input ${input.id} value changed from ${originalValue} to ${input.value}`);
                }
            }, 1000);

            // Also monitor for programmatic changes using a proxy
            const descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
            const originalSetter = descriptor.set;

            Object.defineProperty(input, 'value', {
                set: function(val) {
                    console.log(`${input.id} value being set to: ${val}`);
                    originalSetter.call(this, val);
                }
            });
        };

        // Apply monitoring to village ID input
        monitorHiddenInput(villageIdInput);
    });

    // Handle success and error messages with SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
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
    </script>
    @endpush
</x-layout>
