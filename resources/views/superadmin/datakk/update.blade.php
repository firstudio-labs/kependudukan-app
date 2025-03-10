<x-layout>
    <div class="p-4 mt-14">

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
                        <select id="province_id" name="province_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" {{ $kk->province_id == $province['id'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden field to store province ID for backend -->
                        <input type="hidden" name="province_id" id="province_id_hidden" value="{{ $kk->province_id }}">
                    </div>
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                        <select id="district_id" name="district_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kabupaten</option>
                            <!-- Kabupaten akan diisi oleh JavaScript -->
                        </select>
                        <!-- Hidden field to store district ID for backend -->
                        <input type="hidden" name="district_id" id="district_id_hidden" value="{{ $kk->district_id }}">
                    </div>
                    <div>
                        <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="sub_district_id" name="sub_district_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kecamatan</option>
                            <!-- Kecamatan akan diisi oleh JavaScript -->
                        </select>
                        <!-- Hidden field to store sub_district ID for backend -->
                        <input type="hidden" name="sub_district_id" id="sub_district_id_hidden" value="{{ $kk->sub_district_id }}">
                    </div>
                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village_id" name="village_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="">Pilih Desa/Kelurahan</option>
                            <!-- Desa/Kelurahan akan diisi oleh JavaScript -->
                        </select>
                        <!-- Hidden field to store village ID for backend -->
                        <input type="hidden" name="village_id" id="village_id_hidden" value="{{ $kk->village_id }}">
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
            // API config
            const baseUrl = 'http://api-kependudukan.desaverse.id:3000/api';
            const apiKey = '{{ config('services.kependudukan.key') }}';

            // Global variables to store location code mappings
            let provinceCodeMap = {};
            let districtCodeMap = {};
            let subDistrictCodeMap = {};
            let villageCodeMap = {};

            // Reverse maps to get ID from code
            let provinceIdMap = {};
            let districtIdMap = {};
            let subDistrictIdMap = {};
            let villageIdMap = {};

            // Local variables to store current location data
            let currentProvinceCode = null;
            let currentDistrictCode = null;
            let currentSubDistrictCode = null;
            let currentVillageCode = null;

            // Function to close alert
            function closeAlert(alertId) {
                document.getElementById(alertId).classList.add('hidden');
            }

            // Function to load province codes and create mappings
            async function loadProvinceData() {
                try {
                    const response = await axios.get(`${baseUrl}/provinces`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    });

                    if (response.data && response.data.data) {
                        const provinces = response.data.data;

                        // Create mappings in both directions
                        provinces.forEach(province => {
                            provinceCodeMap[province.id] = province.code;
                            provinceIdMap[province.code] = province.id;
                        });

                        return provinces;
                    }
                } catch (error) {
                    console.error('Error loading provinces:', error);
                }
                return [];
            }

            // Function to load districts for a province and create mappings
            async function loadDistrictData(provinceCode) {
                if (!provinceCode) return [];

                try {
                    const response = await axios.get(`${baseUrl}/districts/${provinceCode}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    });

                    if (response.data && response.data.data) {
                        const districts = response.data.data;

                        // Reset and recreate district mappings
                        districtCodeMap = {};
                        districtIdMap = {};

                        // Create mappings in both directions
                        districts.forEach(district => {
                            districtCodeMap[district.id] = district.code;
                            districtIdMap[district.code] = district.id;
                        });

                        return districts;
                    }
                } catch (error) {
                    console.error(`Error loading districts for province ${provinceCode}:`, error);
                }
                return [];
            }

            // Function to load subdistricts for a district and create mappings
            async function loadSubDistrictData(districtCode) {
                if (!districtCode) return [];

                try {
                    const response = await axios.get(`${baseUrl}/sub-districts/${districtCode}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    });

                    if (response.data && response.data.data) {
                        const subDistricts = response.data.data;

                        // Reset and recreate subdistrict mappings
                        subDistrictCodeMap = {};
                        subDistrictIdMap = {};

                        // Create mappings in both directions
                        subDistricts.forEach(subDistrict => {
                            subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                            subDistrictIdMap[subDistrict.code] = subDistrict.id;
                        });

                        return subDistricts;
                    }
                } catch (error) {
                    console.error(`Error loading subdistricts for district ${districtCode}:`, error);
                }
                return [];
            }

            // Function to load villages for a subdistrict and create mappings
            async function loadVillageData(subDistrictCode) {
                if (!subDistrictCode) return [];

                try {
                    const response = await axios.get(`${baseUrl}/villages/${subDistrictCode}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    });

                    if (response.data && response.data.data) {
                        const villages = response.data.data;

                        // Reset and recreate village mappings
                        villageCodeMap = {};
                        villageIdMap = {};

                        // Create mappings in both directions
                        villages.forEach(village => {
                            villageCodeMap[village.id] = village.code;
                            villageIdMap[village.code] = village.id;
                        });

                        return villages;
                    }
                } catch (error) {
                    console.error(`Error loading villages for subdistrict ${subDistrictCode}:`, error);
                }
                return [];
            }

            // Function to find and set the code for a given ID
            async function findLocationCodeById(type, id) {
                if (!id) return null;

                switch (type) {
                    case 'province':
                        // Load provinces if mapping is empty
                        if (Object.keys(provinceCodeMap).length === 0) {
                            await loadProvinceData();
                        }
                        return provinceCodeMap[id] || null;

                    case 'district':
                        // Requires province code to be set
                        if (!currentProvinceCode) return null;
                        if (Object.keys(districtCodeMap).length === 0) {
                            await loadDistrictData(currentProvinceCode);
                        }
                        return districtCodeMap[id] || null;

                    case 'subdistrict':
                        // Requires district code to be set
                        if (!currentDistrictCode) return null;
                        if (Object.keys(subDistrictCodeMap).length === 0) {
                            await loadSubDistrictData(currentDistrictCode);
                        }
                        return subDistrictCodeMap[id] || null;

                    case 'village':
                        // Requires subdistrict code to be set
                        if (!currentSubDistrictCode) return null;
                        if (Object.keys(villageCodeMap).length === 0) {
                            await loadVillageData(currentSubDistrictCode);
                        }
                        return villageCodeMap[id] || null;
                }
                return null;
            }

            // Inisialisasi data wilayah with improved code mapping
            async function initializeLocationData() {
                const provinceId = "{{ $kk->province_id }}";
                const districtId = "{{ $kk->district_id }}";
                const subDistrictId = "{{ $kk->sub_district_id }}";
                const villageId = "{{ $kk->village_id }}";

                // Step 1: Load all provinces and setup mapping
                await loadProvinceData();

                // Step 2: Find province code for this KK's province ID
                currentProvinceCode = await findLocationCodeById('province', provinceId);
                if (currentProvinceCode) {
                    // Set the province dropdown value
                    $('#province_id').val(currentProvinceCode);

                    // Step 3: Load districts for this province
                    const districts = await loadDistrictData(currentProvinceCode);

                    // Populate district dropdown
                    $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');
                    districts.forEach(district => {
                        $('#district_id').append(`<option value="${district.code}">${district.name}</option>`);
                    });

                    // Step 4: Find district code for this KK's district ID
                    currentDistrictCode = await findLocationCodeById('district', districtId);
                    if (currentDistrictCode) {
                        // Set the district dropdown value
                        $('#district_id').val(currentDistrictCode).prop('disabled', false);

                        // Step 5: Load subdistricts for this district
                        const subDistricts = await loadSubDistrictData(currentDistrictCode);

                        // Populate subdistrict dropdown
                        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');
                        subDistricts.forEach(subDistrict => {
                            $('#sub_district_id').append(`<option value="${subDistrict.code}">${subDistrict.name}</option>`);
                        });

                        // Step 6: Find subdistrict code for this KK's subdistrict ID
                        currentSubDistrictCode = await findLocationCodeById('subdistrict', subDistrictId);
                        if (currentSubDistrictCode) {
                            // Set the subdistrict dropdown value
                            $('#sub_district_id').val(currentSubDistrictCode).prop('disabled', false);

                            // Step 7: Load villages for this subdistrict
                            const villages = await loadVillageData(currentSubDistrictCode);

                            // Populate village dropdown
                            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
                            villages.forEach(village => {
                                $('#village_id').append(`<option value="${village.code}">${village.name}</option>`);
                            });

                            // Step 8: Find village code for this KK's village ID
                            currentVillageCode = await findLocationCodeById('village', villageId);
                            if (currentVillageCode) {
                                // Set the village dropdown value
                                $('#village_id').val(currentVillageCode).prop('disabled', false);
                            }
                        }
                    }
                }
            }

            // Event handler for province dropdown
            $('#province_id').on('change', function () {
                const provinceCode = $(this).val();
                currentProvinceCode = provinceCode;

                // Set the hidden province ID field
                const provinceId = provinceIdMap[provinceCode];
                $('#province_id_hidden').val(provinceId || '');

                // Reset dropdowns
                $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>').prop('disabled', true);
                $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

                // Reset hidden fields
                $('#district_id_hidden').val('');
                $('#sub_district_id_hidden').val('');
                $('#village_id_hidden').val('');

                // Reset current code variables
                currentDistrictCode = null;
                currentSubDistrictCode = null;
                currentVillageCode = null;

                if (provinceCode) {
                    // Show loading state
                    $('#district_id').empty().append('<option value="">Loading...</option>');

                    // Load districts for selected province
                    loadDistrictData(provinceCode).then(districts => {
                        $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

                        if (districts.length > 0) {
                            districts.forEach(district => {
                                $('#district_id').append(`<option value="${district.code}">${district.name}</option>`);
                            });
                        }

                        $('#district_id').prop('disabled', false);
                    });
                }
            });

            // Event handler for district dropdown
            $('#district_id').on('change', function () {
                const districtCode = $(this).val();
                currentDistrictCode = districtCode;

                // Set the hidden district ID field
                const districtId = districtIdMap[districtCode];
                $('#district_id_hidden').val(districtId || '');

                // Reset dropdowns
                $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

                // Reset hidden fields
                $('#sub_district_id_hidden').val('');
                $('#village_id_hidden').val('');

                // Reset current code variables
                currentSubDistrictCode = null;
                currentVillageCode = null;

                if (districtCode) {
                    // Show loading state
                    $('#sub_district_id').empty().append('<option value="">Loading...</option>');

                    // Load subdistricts for selected district
                    loadSubDistrictData(districtCode).then(subDistricts => {
                        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                        if (subDistricts.length > 0) {
                            subDistricts.forEach(subDistrict => {
                                $('#sub_district_id').append(`<option value="${subDistrict.code}">${subDistrict.name}</option>`);
                            });
                        }

                        $('#sub_district_id').prop('disabled', false);
                    });
                }
            });

            // Event handler for subdistrict dropdown
            $('#sub_district_id').on('change', function () {
                const subDistrictCode = $(this).val();
                currentSubDistrictCode = subDistrictCode;

                // Set the hidden subdistrict ID field
                const subDistrictId = subDistrictIdMap[subDistrictCode];
                $('#sub_district_id_hidden').val(subDistrictId || '');

                // Reset dropdown
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

                // Reset hidden field
                $('#village_id_hidden').val('');

                // Reset current code variable
                currentVillageCode = null;

                if (subDistrictCode) {
                    // Show loading state
                    $('#village_id').empty().append('<option value="">Loading...</option>');

                    // Load villages for selected subdistrict
                    loadVillageData(subDistrictCode).then(villages => {
                        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                        if (villages.length > 0) {
                            villages.forEach(village => {
                                $('#village_id').append(`<option value="${village.code}">${village.name}</option>`);
                            });
                        }

                        $('#village_id').prop('disabled', false);
                    });
                }
            });

            // Event handler for village dropdown
            $('#village_id').on('change', function () {
                const villageCode = $(this).val();
                currentVillageCode = villageCode;

                // Set the hidden village ID field
                const villageId = villageIdMap[villageCode];
                $('#village_id_hidden').val(villageId || '');
            });

            // Initialize location data when page loads
            initializeLocationData();
        });
    </script>
</x-layout>
