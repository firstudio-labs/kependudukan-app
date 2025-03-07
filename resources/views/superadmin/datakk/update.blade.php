<x-layout>
    <div class="p-4 mt-14">
        <!-- Pesan Sukses/Gagal -->
        @if(session('success'))
            <div id="successAlert" class="flex items-center p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-800 dark:text-green-300 relative" role="alert">
                <svg class="w-5 h-5 mr-2 text-green-800 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">Sukses!</span> {{ session('success') }}
                <button type="button" class="absolute top-2 right-2 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900 rounded-lg p-1 transition-all duration-300" onclick="closeAlert('successAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="errorAlert" class="flex items-center p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-800 dark:text-red-300 relative" role="alert">
                <svg class="w-5 h-5 mr-2 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636L5.636 18.364M5.636 5.636l12.728 12.728"></path>
                </svg>
                <span class="font-medium">Gagal!</span> {{ session('error') }}
                <button type="button" class="absolute top-2 right-2 text-red-800 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900 rounded-lg p-1 transition-all duration-300" onclick="closeAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data KK</h1>

        <!-- Form Edit Data KK -->
        <form method="POST" action="{{ route('kk.update', $kk->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT') <!-- Method Spoofing untuk UPDATE -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kolom 1 -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" name="kk" id="kk" value="{{ $kk->kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" value="{{ $kk->full_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $kk->address }}</textarea>
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ $kk->postal_code }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" name="rt" id="rt" value="{{ $kk->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" name="rw" id="rw" value="{{ $kk->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="jml_anggota_kk" class="block text-sm font-medium text-gray-700">Jumlah Anggota Keluarga</label>
                    <input type="number" name="jml_anggota_kk" id="jml_anggota_kk" value="{{ $kk->jml_anggota_kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ $kk->telepon }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ $kk->email }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Kategori: Data Wilayah -->
            <!-- Data Wilayah section -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Data Wilayah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select name="province_id" id="province_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" {{ $kk->province_id == $province['code'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                        <select name="district_id" id="district_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kabupaten</option>
                            <!-- Kabupaten akan diisi oleh JavaScript -->
                        </select>
                    </div>
                    <div>
                        <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select name="sub_district_id" id="sub_district_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kecamatan</option>
                            <!-- Kecamatan akan diisi oleh JavaScript -->
                        </select>
                    </div>
                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select name="village_id" id="village_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Desa/Kelurahan</option>
                            <!-- Desa/Kelurahan akan diisi oleh JavaScript -->
                        </select>
                    </div>
                    <div>
                        <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                        <input type="text" name="dusun" id="dusun" value="{{ $kk->dusun }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Kategori: Alamat di Luar Negeri -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Alamat di Luar Negeri (diisi oleh WNI di luar wilayah NKRI)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="alamat_luar_negeri" class="block text-sm font-medium text-gray-700">Alamat Luar Negeri</label>
                        <textarea name="alamat_luar_negeri" id="alamat_luar_negeri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $kk->alamat_luar_negeri }}</textarea>
                    </div>
                    <div>
                        <label for="kota" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="kota" id="kota" value="{{ $kk->kota }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="negara_bagian" class="block text-sm font-medium text-gray-700">Provinsi/Negara Bagian</label>
                        <input type="text" name="negara_bagian" id="negara_bagian" value="{{ $kk->negara_bagian }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="negara" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="negara" id="negara" value="{{ $kk->negara }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                    <div>
                        <label for="kode_pos_luar_negeri" class="block text-sm font-medium text-gray-700">Kode Pos Luar Negeri</label>
                        <input type="text" name="kode_pos_luar_negeri" id="kode_pos_luar_negeri" value="{{ $kk->kode_pos_luar_negeri }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('superadmin.datakk.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Batal</a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                successAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => successAlert.classList.add('hidden'), 1000);
            }

            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => errorAlert.classList.add('hidden'), 1000);
            }
        }, 5000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to close alert
            function closeAlert(alertId) {
                document.getElementById(alertId).classList.add('hidden');
            }

            // Inisialisasi data wilayah
            initializeLocationData();

            // Function to initialize location data
            async function initializeLocationData() {
                const provinceId = "{{ $kk->province_id }}";
                const districtId = "{{ $kk->district_id }}";
                const subDistrictId = "{{ $kk->sub_district_id }}";
                const villageId = "{{ $kk->village_id }}";

                if (provinceId) {
                    // Load districts for the selected province
                    await loadDistricts(provinceId, districtId);

                    if (districtId) {
                        // Load sub-districts for the selected district
                        await loadSubDistricts(districtId, subDistrictId);

                        if (subDistrictId) {
                            // Load villages for the selected sub-district
                            await loadVillages(subDistrictId, villageId);
                        }
                    }
                }
            }

            // Event handler for province dropdown
            document.getElementById('province_id').addEventListener('change', function () {
                const provinceCode = this.value;

                // Reset dropdowns
                resetDropdown('district_id', 'Pilih Kabupaten');
                resetDropdown('sub_district_id', 'Pilih Kecamatan');
                resetDropdown('village_id', 'Pilih Desa/Kelurahan');

                if (provinceCode) {
                    loadDistricts(provinceCode);
                }
            });

            // Event handler for district dropdown
            document.getElementById('district_id').addEventListener('change', function () {
                const districtCode = this.value;

                // Reset dropdowns
                resetDropdown('sub_district_id', 'Pilih Kecamatan');
                resetDropdown('village_id', 'Pilih Desa/Kelurahan');

                if (districtCode) {
                    loadSubDistricts(districtCode);
                }
            });

            // Event handler for sub-district dropdown
            document.getElementById('sub_district_id').addEventListener('change', function () {
                const subDistrictCode = this.value;

                // Reset dropdown
                resetDropdown('village_id', 'Pilih Desa/Kelurahan');

                if (subDistrictCode) {
                    loadVillages(subDistrictCode);
                }
            });

            // Function to load districts
            async function loadDistricts(provinceCode, selectedDistrictId = null) {
                const districtDropdown = document.getElementById('district_id');

                // Show loading state
                districtDropdown.disabled = true;
                resetDropdown('district_id', 'Loading...');

                try {
                    const response = await axios.get(`/api/wilayah/provinsi/${provinceCode}/kota`);
                    const districts = response.data;

                    resetDropdown('district_id', 'Pilih Kabupaten');

                    if (Array.isArray(districts) && districts.length > 0) {
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.code;
                            option.textContent = district.name;

                            if (selectedDistrictId && district.code == selectedDistrictId) {
                                option.selected = true;
                            }

                            districtDropdown.appendChild(option);
                        });
                    }

                    districtDropdown.disabled = false;

                    // If there's a selected district, trigger change event to load sub-districts
                    if (selectedDistrictId) {
                        const event = new Event('change');
                        districtDropdown.dispatchEvent(event);
                    }

                    return districts;
                } catch (error) {
                    console.error('Error loading districts:', error);
                    resetDropdown('district_id', 'Error loading data');
                    return [];
                }
            }

            // Function to load sub-districts
            async function loadSubDistricts(districtCode, selectedSubDistrictId = null) {
                const subDistrictDropdown = document.getElementById('sub_district_id');

                // Show loading state
                subDistrictDropdown.disabled = true;
                resetDropdown('sub_district_id', 'Loading...');

                try {
                    const response = await axios.get(`/api/wilayah/kota/${districtCode}/kecamatan`);
                    const subDistricts = response.data;

                    resetDropdown('sub_district_id', 'Pilih Kecamatan');

                    if (Array.isArray(subDistricts) && subDistricts.length > 0) {
                        subDistricts.forEach(subDistrict => {
                            const option = document.createElement('option');
                            option.value = subDistrict.code;
                            option.textContent = subDistrict.name;

                            if (selectedSubDistrictId && subDistrict.code == selectedSubDistrictId) {
                                option.selected = true;
                            }

                            subDistrictDropdown.appendChild(option);
                        });
                    }

                    subDistrictDropdown.disabled = false;

                    // If there's a selected sub-district, trigger change event to load villages
                    if (selectedSubDistrictId) {
                        const event = new Event('change');
                        subDistrictDropdown.dispatchEvent(event);
                    }

                    return subDistricts;
                } catch (error) {
                    console.error('Error loading sub-districts:', error);
                    resetDropdown('sub_district_id', 'Error loading data');
                    return [];
                }
            }

            // Function to load villages
            async function loadVillages(subDistrictCode, selectedVillageId = null) {
                const villageDropdown = document.getElementById('village_id');

                // Show loading state
                villageDropdown.disabled = true;
                resetDropdown('village_id', 'Loading...');

                try {
                    const response = await axios.get(`/api/wilayah/kecamatan/${subDistrictCode}/kelurahan`);
                    const villages = response.data;

                    resetDropdown('village_id', 'Pilih Desa/Kelurahan');

                    if (Array.isArray(villages) && villages.length > 0) {
                        villages.forEach(village => {
                            const option = document.createElement('option');
                            option.value = village.code;
                            option.textContent = village.name;

                            if (selectedVillageId && village.code == selectedVillageId) {
                                option.selected = true;
                            }

                            villageDropdown.appendChild(option);
                        });
                    }

                    villageDropdown.disabled = false;
                    return villages;
                } catch (error) {
                    console.error('Error loading villages:', error);
                    resetDropdown('village_id', 'Error loading data');
                    return [];
                }
            }

            // Function to reset dropdown
            function resetDropdown(elementId, placeholderText) {
                const dropdown = document.getElementById(elementId);
                dropdown.innerHTML = ''; // Clear all options

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = placeholderText;
                dropdown.appendChild(placeholder);
            }
        });
    </script>
</x-layout>
