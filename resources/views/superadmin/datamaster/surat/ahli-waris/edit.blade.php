<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Keterangan Ahli Waris</h1>

        <form method="POST" action="{{ route('superadmin.surat.ahli-waris.update', $ahliWaris->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_accepted" value="1">

            <!-- Daftar Ahli Waris Section (Moved to top) -->
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Daftar Ahli Waris</h2>
                <div id="heirs-container">
                    @php
                        // Safe array processing function
                        $processArray = function($input) {
                            if (is_array($input)) return $input;
                            if (is_string($input)) {
                                $decoded = json_decode($input, true);
                                return $decoded !== null ? $decoded : [];
                            }
                            return [];
                        };

                        // Process all arrays safely
                        $niks = $processArray($ahliWaris->nik);
                        $fullNames = $processArray($ahliWaris->full_name);
                        $birthPlaces = $processArray($ahliWaris->birth_place);
                        $birthDates = $processArray($ahliWaris->birth_date);
                        $genders = $processArray($ahliWaris->gender);
                        $religions = $processArray($ahliWaris->religion);
                        $addresses = $processArray($ahliWaris->address);
                        $familyStatuses = $processArray($ahliWaris->family_status);

                        $heirCount = count($niks);
                    @endphp

                    @for($i = 0; $i < $heirCount; $i++)
                    <div class="heir-row border p-4 rounded-md mb-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                <select name="nik[]" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="{{ isset($niks[$i]) ? $niks[$i] : '' }}">{{ isset($niks[$i]) ? $niks[$i] : 'Pilih NIK' }}</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <select name="full_name[]" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="{{ isset($fullNames[$i]) ? $fullNames[$i] : '' }}">{{ isset($fullNames[$i]) ? $fullNames[$i] : 'Pilih Nama' }}</option>
                                </select>
                            </div>

                            <!-- Hubungan Keluarga -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hubungan Keluarga <span class="text-red-500">*</span></label>
                                <select name="family_status[]" class="family-status mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Hubungan</option>
                                    <option value="1" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 1 ? 'selected' : '' }}>ANAK</option>
                                    <option value="2" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 2 ? 'selected' : '' }}>KEPALA KELUARGA</option>
                                    <option value="3" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 3 ? 'selected' : '' }}>ISTRI</option>
                                    <option value="4" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 4 ? 'selected' : '' }}>ORANG TUA</option>
                                    <option value="5" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 5 ? 'selected' : '' }}>MERTUA</option>
                                    <option value="6" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 6 ? 'selected' : '' }}>CUCU</option>
                                    <option value="7" {{ isset($familyStatuses[$i]) && $familyStatuses[$i] == 7 ? 'selected' : '' }}>FAMILI LAIN</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <!-- Tempat Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="birth_place[]" value="{{ isset($birthPlaces[$i]) ? $birthPlaces[$i] : '' }}" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date[]" value="{{ isset($birthDates[$i]) ? $birthDates[$i] : '' }}" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender[]" class="gender mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="1" {{ isset($genders[$i]) && $genders[$i] == 1 ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="2" {{ isset($genders[$i]) && $genders[$i] == 2 ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <!-- Agama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                                <select name="religion[]" class="religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Agama</option>
                                    <option value="1" {{ isset($religions[$i]) && $religions[$i] == 1 ? 'selected' : '' }}>Islam</option>
                                    <option value="2" {{ isset($religions[$i]) && $religions[$i] == 2 ? 'selected' : '' }}>Kristen</option>
                                    <option value="3" {{ isset($religions[$i]) && $religions[$i] == 3 ? 'selected' : '' }}>Katholik</option>
                                    <option value="4" {{ isset($religions[$i]) && $religions[$i] == 4 ? 'selected' : '' }}>Hindu</option>
                                    <option value="5" {{ isset($religions[$i]) && $religions[$i] == 5 ? 'selected' : '' }}>Buddha</option>
                                    <option value="6" {{ isset($religions[$i]) && $religions[$i] == 6 ? 'selected' : '' }}>Kong Hu Cu</option>
                                    <option value="7" {{ isset($religions[$i]) && $religions[$i] == 7 ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="address[]" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ isset($addresses[$i]) ? $addresses[$i] : '' }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button type="button" class="remove-heir bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="mt-4">
                    <button type="button" id="add-heir" class="bg-[#2D336B] text-white px-4 py-2 rounded hover:bg-[#7886C7]">
                        Tambah Ahli Waris
                    </button>
                </div>
            </div>

            <!-- Data Wilayah Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-4 rounded-md mb-4 bg-gray-50">
                    <!-- Provinsi -->
                    <div>
                        <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                        <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $ahliWaris->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="{{ $ahliWaris->province_id }}">
                    </div>

                    <!-- Kabupaten -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Kabupaten</option>
                            @foreach($districts as $district)
                                <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $ahliWaris->district_id == $district['id'] ? 'selected' : '' }}>
                                    {{ $district['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="district_id" name="district_id" value="{{ $ahliWaris->district_id }}">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Kecamatan</option>
                            @foreach($subDistricts as $subDistrict)
                                <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $ahliWaris->subdistrict_id == $subDistrict['id'] ? 'selected' : '' }}>
                                    {{ $subDistrict['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $ahliWaris->subdistrict_id }}">
                    </div>

                    <!-- Desa -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Desa</option>
                            @foreach($villages as $village)
                                <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $ahliWaris->village_id == $village['id'] ? 'selected' : '' }}>
                                    {{ $village['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="village_id" name="village_id" value="{{ $ahliWaris->village_id }}">
                    </div>
                </div>
            </div>

            <!-- Informasi Surat Section (Moved to bottom) -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-4 rounded-md mb-4 bg-gray-50">
                    <!-- Nama Ahli Waris -->
                    <div>
                        <label for="heir_name" class="block text-sm font-medium text-gray-700">Nama Ahli Waris <span class="text-red-500">*</span></label>
                        <input type="text" id="heir_name" name="heir_name" value="{{ $ahliWaris->heir_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nama Almarhum -->
                    <div>
                        <label for="deceased_name" class="block text-sm font-medium text-gray-700">Nama Almarhum <span class="text-red-500">*</span></label>
                        <input type="text" id="deceased_name" name="deceased_name" value="{{ $ahliWaris->deceased_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tempat Meninggal -->
                    <div>
                        <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Meninggal <span class="text-red-500">*</span></label>
                        <input type="text" id="death_place" name="death_place" value="{{ $ahliWaris->death_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tanggal Meninggal -->
                    <div>
                        <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                        <input type="date" id="death_date" name="death_date" value="{{ date('Y-m-d', strtotime($ahliWaris->death_date)) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nomor Akte Kematian -->
                    <div>
                        <label for="death_certificate_number" class="block text-sm font-medium text-gray-700">Nomor Akte Kematian</label>
                        <input type="number" id="death_certificate_number" name="death_certificate_number" value="{{ $ahliWaris->death_certificate_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Akte Kematian -->
                    <div>
                        <label for="death_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Akte Kematian</label>
                        <input type="date" id="death_certificate_date" name="death_certificate_date" value="{{ $ahliWaris->death_certificate_date ? date('Y-m-d', strtotime($ahliWaris->death_certificate_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Surat Waris -->
                    <div>
                        <label for="inheritance_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat Waris</label>
                        <input type="date" id="inheritance_letter_date" name="inheritance_letter_date" value="{{ $ahliWaris->inheritance_letter_date ? date('Y-m-d', strtotime($ahliWaris->inheritance_letter_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Jenis Warisan -->
                    <div>
                        <label for="inheritance_type" class="block text-sm font-medium text-gray-700">Jenis Warisan <span class="text-red-500">*</span></label>
                        <input type="text" id="inheritance_type" name="inheritance_type" value="{{ $ahliWaris->inheritance_type }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nomor Surat -->
                    <div>
                        <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" value="{{ $ahliWaris->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Pejabat Penandatangan dropdown -->
                    <div>
                        <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                        <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Pejabat</option>
                            @foreach($signers as $signer)
                                <option value="{{ $signer->id }}" {{ $ahliWaris->signing == $signer->id ? 'selected' : '' }}>
                                    {{ $signer->judul }} - {{ $signer->keterangan }}
                                </option>
                            @endforeach
                        </select>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Load all citizens first before initializing heir rows
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

                    // Now setup the heirs interface
                    setupHeirsInterface();
                },
                error: function(error) {
                    console.error('Failed to load citizen data:', error);
                    // Setup the heirs interface anyway with empty data
                    setupHeirsInterface();
                }
            });

            function setupHeirsInterface() {
                // Setup location dropdowns directly (replacing the setupEditLocationDropdowns call)
                setupLocationDropdowns();

                // Initialize Select2 for citizen fields
                initializeHeirSelect2Fields();

                // Add heir button functionality
                setupHeirRowManagement();
            }

            // Function to setup location dropdowns directly
            function setupLocationDropdowns() {
                const provinceSelect = document.getElementById('province_code');
                const districtSelect = document.getElementById('district_code');
                const subDistrictSelect = document.getElementById('subdistrict_code');
                const villageSelect = document.getElementById('village_code');

                const provinceIdInput = document.getElementById('province_id');
                const districtIdInput = document.getElementById('district_id');
                const subDistrictIdInput = document.getElementById('subdistrict_id');
                const villageIdInput = document.getElementById('village_id');

                // Store initial values
                const initialProvinceId = "{{ $ahliWaris->province_id }}";
                const initialDistrictId = "{{ $ahliWaris->district_id }}";
                const initialSubdistrictId = "{{ $ahliWaris->subdistrict_id }}";
                const initialVillageId = "{{ $ahliWaris->village_id }}";

                // Initialize province select
                if (provinceSelect) {
                    provinceSelect.addEventListener('change', function() {
                        const provinceCode = this.value;
                        const selectedOption = this.options[this.selectedIndex];

                        if (selectedOption && selectedOption.hasAttribute('data-id')) {
                            provinceIdInput.value = selectedOption.getAttribute('data-id');
                        }

                        loadDistricts(provinceCode);
                    });
                }

                // Initialize district select
                if (districtSelect) {
                    districtSelect.addEventListener('change', function() {
                        const districtCode = this.value;
                        const selectedOption = this.options[this.selectedIndex];

                        if (selectedOption && selectedOption.hasAttribute('data-id')) {
                            districtIdInput.value = selectedOption.getAttribute('data-id');
                        }

                        loadSubdistricts(districtCode);
                    });
                }

                // Initialize subdistrict select
                if (subDistrictSelect) {
                    subDistrictSelect.addEventListener('change', function() {
                        const subdistrictCode = this.value;
                        const selectedOption = this.options[this.selectedIndex];

                        if (selectedOption && selectedOption.hasAttribute('data-id')) {
                            subDistrictIdInput.value = selectedOption.getAttribute('data-id');
                        }

                        loadVillages(subdistrictCode);
                    });
                }

                // Initialize village select
                if (villageSelect) {
                    villageSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];

                        if (selectedOption && selectedOption.hasAttribute('data-id')) {
                            villageIdInput.value = selectedOption.getAttribute('data-id');
                        }
                    });
                }

                // Load districts based on province code
                function loadDistricts(provinceCode) {
                    if (!provinceCode) return;

                    // Reset dependent dropdowns
                    resetSelect(districtSelect, 'Loading districts...');
                    resetSelect(subDistrictSelect, 'Pilih Kecamatan');
                    resetSelect(villageSelect, 'Pilih Desa');

                    // Fetch districts
                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => response.json())
                        .then(data => {
                            populateSelect(districtSelect, data, 'Pilih Kabupaten', initialDistrictId);
                            districtSelect.disabled = false;

                            // If there's an initial district value, load its subdistricts
                            if (initialDistrictId) {
                                const districtOption = Array.from(districtSelect.options).find(
                                    option => option.getAttribute('data-id') === initialDistrictId
                                );

                                if (districtOption) {
                                    loadSubdistricts(districtOption.value);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading districts:', error);
                            resetSelect(districtSelect, 'Error loading districts');
                        });
                }

                // Load subdistricts based on district code
                function loadSubdistricts(districtCode) {
                    if (!districtCode) return;

                    // Reset dependent dropdowns
                    resetSelect(subDistrictSelect, 'Loading subdistricts...');
                    resetSelect(villageSelect, 'Pilih Desa');

                    // Fetch subdistricts
                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', initialSubdistrictId);
                            subDistrictSelect.disabled = false;

                            // If there's an initial subdistrict value, load its villages
                            if (initialSubdistrictId) {
                                const subdistrictOption = Array.from(subDistrictSelect.options).find(
                                    option => option.getAttribute('data-id') === initialSubdistrictId
                                );

                                if (subdistrictOption) {
                                    loadVillages(subdistrictOption.value);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading subdistricts:', error);
                            resetSelect(subDistrictSelect, 'Error loading subdistricts');
                        });
                }

                // Load villages based on subdistrict code
                function loadVillages(subdistrictCode) {
                    if (!subdistrictCode) return;

                    // Reset dependent dropdown
                    resetSelect(villageSelect, 'Loading villages...');

                    // Fetch villages
                    fetch(`{{ url('/location/villages') }}/${subdistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            populateSelect(villageSelect, data, 'Pilih Desa', initialVillageId);
                            villageSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error loading villages:', error);
                            resetSelect(villageSelect, 'Error loading villages');
                        });
                }

                // Helper function to reset a select element
                function resetSelect(selectElement, loadingText = 'Pilih...') {
                    if (!selectElement) return;

                    selectElement.innerHTML = `<option value="">${loadingText}</option>`;
                    selectElement.disabled = true;
                }

                // Helper function to populate a select element
                function populateSelect(selectElement, items, defaultText, selectedId = null) {
                    if (!selectElement) return;

                    selectElement.innerHTML = `<option value="">${defaultText}</option>`;

                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.code;
                        option.textContent = item.name;
                        option.setAttribute('data-id', item.id);

                        if (selectedId && item.id.toString() === selectedId.toString()) {
                            option.selected = true;
                        }

                        selectElement.appendChild(option);
                    });
                }

                // Load initial location data
                if (initialProvinceId && provinceSelect) {
                    // Find the province option with the matching data-id
                    const provinceOption = Array.from(provinceSelect.options).find(
                        option => option.getAttribute('data-id') === initialProvinceId
                    );

                    if (provinceOption) {
                        loadDistricts(provinceOption.value);
                    }
                }
            }

            // Initialize Select2 for all existing and new heir rows
            function initializeHeirSelect2Fields() {
                const heirRows = document.querySelectorAll('.heir-row');
                heirRows.forEach(function(row) {
                    initializeHeirSelect2(row);
                });
            }

            // Function to initialize Select2 on an heir row
            function initializeHeirSelect2(heirRow) {
                const nikSelect = heirRow.querySelector('.nik-select');
                const nameSelect = heirRow.querySelector('.fullname-select');

                if (nikSelect) {
                    $(nikSelect).select2({
                        data: prepareCitizenOptions(),
                        placeholder: 'Pilih NIK',
                        allowClear: false // Remove the X icon
                    });
                }

                if (nameSelect) {
                    $(nameSelect).select2({
                        data: prepareCitizenOptions(),
                        placeholder: 'Pilih Nama',
                        allowClear: false // Remove the X icon
                    });
                }

                if (nikSelect && nameSelect) {
                    setupChangeHandlers(nikSelect, nameSelect);
                }
            }

            // Process citizens for Select2
            function prepareCitizenOptions() {
                return allCitizens.map(citizen => ({
                    id: citizen.nik,
                    text: citizen.nik,
                    fullName: citizen.full_name,
                    birthPlace: citizen.birth_place,
                    birthDate: citizen.birth_date,
                    gender: citizen.gender,
                    religion: citizen.religion,
                    address: citizen.address
                }));
            }

            // Setup change handlers for NIK and Name selects
            function setupChangeHandlers(nikSelect, nameSelect) {
                $(nikSelect).on('change', function() {
                    const selectedNik = $(this).val();
                    const citizen = allCitizens.find(c => c.nik === selectedNik);

                    if (citizen) {
                        $(nameSelect).val(citizen.full_name).trigger('change');
                        populateHeirFieldsFromCitizen($(this).closest('.heir-row'), citizen);
                    }
                });

                $(nameSelect).on('change', function() {
                    const selectedName = $(this).val();
                    const citizen = allCitizens.find(c => c.full_name === selectedName);

                    if (citizen) {
                        $(nikSelect).val(citizen.nik).trigger('change');
                        populateHeirFieldsFromCitizen($(this).closest('.heir-row'), citizen);
                    }
                });
            }

            // Setup heir row management (add/remove)
            function setupHeirRowManagement() {
                const addHeirButton = document.getElementById('add-heir');
                const heirsContainer = document.getElementById('heirs-container');

                if (addHeirButton) {
                    addHeirButton.addEventListener('click', function() {
                        const newRow = document.createElement('div');
                        newRow.className = 'heir-row border p-4 rounded-md mb-4 bg-gray-50';
                        newRow.innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                    <select name="nik[]" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <select name="full_name[]" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hubungan Keluarga <span class="text-red-500">*</span></label>
                                    <select name="family_status[]" class="family-status mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                        <option value="">Pilih Hubungan</option>
                                        <option value="1">ANAK</option>
                                        <option value="2">KEPALA KELUARGA</option>
                                        <option value="3">ISTRI</option>
                                        <option value="4">ORANG TUA</option>
                                        <option value="5">MERTUA</option>
                                        <option value="6">CUCU</option>
                                        <option value="7">FAMILI LAIN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                    <input type="text" name="birth_place[]" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                    <input type="date" name="birth_date[]" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <select name="gender[]" class="gender mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="1">Laki-Laki</option>
                                        <option value="2">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                                    <select name="religion[]" class="religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                    <textarea name="address[]" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end mt-3">
                                <button type="button" class="remove-heir bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                            </div>
                        `;
                        heirsContainer.appendChild(newRow);
                        initializeHeirSelect2(newRow);
                    });
                }

                heirsContainer.addEventListener('click', function(event) {
                    if (event.target.classList.contains('remove-heir')) {
                        const row = event.target.closest('.heir-row');
                        if (row) {
                            row.remove();
                        }
                    }
                });
            }

            // Function to populate heir fields from citizen data
            function populateHeirFieldsFromCitizen(row, citizen) {
                if (!row || !citizen) return;

                const birthPlaceInput = row.querySelector('.birth-place');
                const birthDateInput = row.querySelector('.birth-date');
                const genderSelect = row.querySelector('.gender');
                const religionSelect = row.querySelector('.religion');
                const addressTextarea = row.querySelector('.address');

                if (birthPlaceInput) birthPlaceInput.value = citizen.birthPlace || '';
                if (birthDateInput) birthDateInput.value = citizen.birthDate || '';
                if (genderSelect) genderSelect.value = citizen.gender || '';
                if (religionSelect) religionSelect.value = citizen.religion || '';
                if (addressTextarea) addressTextarea.value = citizen.address || '';
            }
        });

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
    </script>
</x-layout>
