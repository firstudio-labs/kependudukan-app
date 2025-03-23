<x-layout>
    <div class="p-3 sm:p-4 mt-12 sm:mt-14">

        <!-- Judul H1 -->
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Tambah Data KK</h1>

        <!-- Form Tambah Data KK -->
        <form method="POST" action="{{ route('kk.store') }}" class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Kolom 1: Data Utama -->
                <div class="col-span-1 sm:col-span-2 md:col-span-1">
                    <label for="kkSelect" class="block text-sm font-medium text-gray-700">No KK</label>
                    <select id="kkSelect" name="kk" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        style="max-height: 200px; overflow-y: auto;">
                        <option value="">Pilih No KK</option>
                        <!-- Opsi lainnya -->
                    </select>
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-1">
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <select id="full_name" name="full_name" autocomplete="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        style="max-height: 200px; overflow-y: auto;">
                        <option value="">Pilih Nama Lengkap</option>
                        <!-- Opsi lainnya -->
                    </select>
                </div>

                <div class="col-span-1 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        required rows="2"></textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" autocomplete="postal-code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" autocomplete="address-line1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" autocomplete="address-line2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        required>
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" id="telepon" name="telepon" autocomplete="tel"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="jml_anggota_kk" class="block text-sm font-medium text-gray-700">Jumlah Anggota
                        Keluarga</label>
                    <input type="text" id="jml_anggota_kk" name="jml_anggota_kk" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2 bg-gray-50"
                        readonly>
                </div>

                <!-- Dynamic Family Members Fields -->
                <div id="familyMembersContainer" class="col-span-1 sm:col-span-2">
                    <!-- Family member fields will be inserted here -->
                </div>
            </div>

            <!-- Kategori: Data Wilayah -->
            <div class="mt-5 sm:mt-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4">Data Wilayah</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    {{-- <div>
                        <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select id="province_id" name="province_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <!-- Hidden field to store province ID for backend -->
                        <input type="hidden" name="province_id" id="province_id_hidden">
                    </div>

                    <!-- Kabupaten stays as select but will be populated via JS -->
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                        <select id="district_id" name="district_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                        <!-- Hidden field to store district ID for backend -->
                        <input type="hidden" name="district_id" id="district_id_hidden">
                    </div> --}}

                    <div>
                        <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <select id="sub_district_id" name="sub_district_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <!-- Hidden field to store sub_district ID for backend -->
                        <input type="hidden" name="sub_district_id" id="sub_district_id_hidden">
                    </div>

                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                        <select id="village_id" name="village_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            required>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        <!-- Hidden field to store village ID for backend -->
                        <input type="hidden" name="village_id" id="village_id_hidden">
                    </div>

                    <div>
                        <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                        <input type="text" name="dusun" id="dusun" autocomplete="address-level5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Kategori: Alamat di Luar Negeri -->
            <div class="mt-5 sm:mt-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2 sm:mb-3">
                    Alamat di Luar Negeri <span class="text-red-500">*</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mb-3">Hanya diisi oleh WNI di luar wilayah NKRI.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="col-span-1 sm:col-span-2">
                        <label for="alamat_luar_negeri" class="block text-sm font-medium text-gray-700">Alamat Luar
                            Negeri</label>
                        <textarea name="alamat_luar_negeri" id="alamat_luar_negeri" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            rows="2"></textarea>
                    </div>

                    <div>
                        <label for="kota" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="kota" id="kota" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="negara_bagian" class="block text-sm font-medium text-gray-700">Provinsi/Negara
                            Bagian</label>
                        <input type="text" name="negara_bagian" id="negara_bagian" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="negara" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="negara" id="negara" autocomplete="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="kode_pos_luar_negeri" class="block text-sm font-medium text-gray-700">Kode Pos Luar
                            Negeri</label>
                        <input type="text" name="kode_pos_luar_negeri" id="kode_pos_luar_negeri"
                            autocomplete="postal-code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </button>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Simpan</button>
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

        // Fungsi untuk menutup alert
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        // Menutup alert secara otomatis setelah 5 detik
        setTimeout(function () {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                successAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => successAlert.classList.add('hidden'), 1000);
            }

            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => errorAlert.classList.add('hidden'), 1000);
            }
        }, 5000);

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

        // Function to load province codes and store the ID-to-code mapping
        async function loadProvinceCodeMap() {
            try {
                const response = await $.ajax({
                    url: `{{ url('/location/provinces') }}`,
                    type: 'GET'
                });

                if (response.data && Array.isArray(response.data)) {
                    // Create mappings in both directions
                    response.data.forEach(province => {
                        provinceCodeMap[province.id] = province.code;
                        provinceIdMap[province.code] = province.id;
                    });
                }
            } catch (error) {
                // Silent error handling
            }
        }

        // Function to get district code map for a specific province
        async function loadDistrictCodeMap(provinceCode) {
            try {
                const response = await $.ajax({
                    url: `{{ url('/location/districts') }}/${provinceCode}`,
                    type: 'GET'
                });

                if (response && Array.isArray(response)) {
                    // Create mappings in both directions
                    response.forEach(district => {
                        districtCodeMap[district.id] = district.code;
                        districtIdMap[district.code] = district.id;
                    });
                }
            } catch (error) {
                // Silent error handling
            }
        }

        // Function to get subdistrict code map for a specific district
        async function loadSubDistrictCodeMap(districtCode) {
            try {
                const response = await $.ajax({
                    url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                    type: 'GET'
                });

                if (response && Array.isArray(response)) {
                    // Create mappings in both directions
                    response.forEach(subDistrict => {
                        subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                        subDistrictIdMap[subDistrict.code] = subDistrict.id;
                    });
                }
            } catch (error) {
                // Silent error handling
            }
        }

        // Function to get village code map for a specific subdistrict
        async function loadVillageCodeMap(subDistrictCode) {
            try {
                const response = await $.ajax({
                    url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                    type: 'GET'
                });

                if (response && Array.isArray(response)) {
                    // Create mappings in both directions
                    response.forEach(village => {
                        villageCodeMap[village.id] = village.code;
                        villageIdMap[village.code] = village.id;
                    });
                }
            } catch (error) {
                // Silent error handling
            }
        }

        // Fungsi untuk mengambil data dari API through controllers
        async function fetchCitizens() {
            try {
                // Use the all citizens endpoint
                const response = await axios.get('{{ route("citizens.all") }}');
                const data = response.data;

                // Get the citizens array regardless of structure
                let citizensList = [];

                if (data.status === 'OK') {
                    if (Array.isArray(data.data)) {
                        citizensList = data.data;
                    } else if (data.data && typeof data.data === 'object') {
                        // In case data.data is a single object
                        if (data.data.citizens && Array.isArray(data.data.citizens)) {
                            // If structure is { data: { citizens: [...] } }
                            citizensList = data.data.citizens;
                        } else {
                            // If it's a single citizen, make it an array
                            citizensList = [data.data];
                        }
                    }

                    const kkSelect = document.getElementById('kkSelect');
                    const fullNameSelect = document.getElementById('full_name');

                    if (!kkSelect || !fullNameSelect) {
                        return;
                    }

                    // Clear existing options
                    kkSelect.innerHTML = '<option value="">Pilih No KK</option>';
                    fullNameSelect.innerHTML = '<option value="">Pilih Nama Lengkap</option>';

                    // Filter only heads of family from all citizens
                    const headsOfFamily = citizensList.filter(citizen =>
                        citizen.family_status === 'KEPALA KELUARGA');

                    // Add options for heads of family
                    if (headsOfFamily.length > 0) {
                        for (const citizen of headsOfFamily) {
                            const kkOption = document.createElement('option');
                            kkOption.value = citizen.kk;
                            kkOption.textContent = citizen.kk;

                            // Set all data attributes
                            kkOption.setAttribute('data-full-name', citizen.full_name);
                            kkOption.setAttribute('data-address', citizen.address || '');
                            kkOption.setAttribute('data-postal-code', citizen.postal_code || '');
                            kkOption.setAttribute('data-rt', citizen.rt || '');
                            kkOption.setAttribute('data-rw', citizen.rw || '');
                            kkOption.setAttribute('data-telepon', citizen.telepon || '');
                            kkOption.setAttribute('data-email', citizen.email || '');
                            kkOption.setAttribute('data-province-id', citizen.province_id || '');
                            kkOption.setAttribute('data-district-id', citizen.district_id || '');
                            kkOption.setAttribute('data-sub-district-id', citizen.sub_district_id || '');
                            kkOption.setAttribute('data-village-id', citizen.village_id || '');
                            kkOption.setAttribute('data-dusun', citizen.dusun || '');

                            kkSelect.appendChild(kkOption);

                            const fullNameOption = document.createElement('option');
                            fullNameOption.value = citizen.full_name;
                            fullNameOption.textContent = citizen.full_name;

                            // Set all data attributes
                            fullNameOption.setAttribute('data-kk', citizen.kk || '');
                            fullNameOption.setAttribute('data-address', citizen.address || '');
                            fullNameOption.setAttribute('data-postal-code', citizen.postal_code || '');
                            fullNameOption.setAttribute('data-rt', citizen.rt || '');
                            fullNameOption.setAttribute('data-rw', citizen.rw || '');
                            fullNameOption.setAttribute('data-telepon', citizen.telepon || '');
                            fullNameOption.setAttribute('data-email', citizen.email || '');
                            fullNameOption.setAttribute('data-province-id', citizen.province_id || '');
                            fullNameOption.setAttribute('data-district-id', citizen.district_id || '');
                            fullNameOption.setAttribute('data-sub-district-id', citizen.sub_district_id || '');
                            fullNameOption.setAttribute('data-village-id', citizen.village_id || '');
                            fullNameOption.setAttribute('data-dusun', citizen.dusun || '');

                            fullNameSelect.appendChild(fullNameOption);
                        }

                        // Initialize Select2 after populating options
                        $('#kkSelect').select2({
                            placeholder: 'Pilih No KK',
                            width: '100%'
                        });

                        $('#full_name').select2({
                            placeholder: 'Pilih Nama Lengkap',
                            width: '100%'
                        });
                    }
                }
            } catch (error) {
                // Silently handle errors
            }
        }

        // Define isUpdating in the global scope so all handlers can access it
        let isUpdating = false;

        // Event listener saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Select2 untuk elemen select No KK
            $('#kkSelect').select2({
                placeholder: 'Pilih No KK',
                width: '100%'
            });

            // Inisialisasi Select2 untuk elemen select Nama Lengkap
            $('#full_name').select2({
                placeholder: 'Pilih Nama Lengkap',
                width: '100%'
            });

            // Event listener untuk perubahan pada Select2 No KK
            $('#kkSelect').on('change', function () {
                if (isUpdating) return; // Hindari rekursi
                isUpdating = true;

                const selectedKK = $(this).val(); // Ambil nilai yang dipilih
                const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

                if (selectedKK) {
                    // Ambil data dari atribut data-*
                    const fullName = selectedOption.attr('data-full-name');
                    const address = selectedOption.attr('data-address');
                    const postalCode = selectedOption.attr('data-postal-code');
                    const rt = selectedOption.attr('data-rt');
                    const rw = selectedOption.attr('data-rw');
                    const telepon = selectedOption.attr('data-telepon') || '';
                    const email = selectedOption.attr('data-email') || '';
                    // Get location IDs directly (not codes)
                    const provinceId = selectedOption.attr('data-province-id');
                    const districtId = selectedOption.attr('data-district-id');
                    const subDistrictId = selectedOption.attr('data-sub-district-id');
                    const villageId = selectedOption.attr('data-village-id');
                    const dusun = selectedOption.attr('data-dusun') || '';

                    // Isi field yang sesuai
                    $('#full_name').val(fullName || '').trigger('change.select2');
                    $('#address').val(address || '');
                    $('#postal_code').val(postalCode || '');
                    $('#rt').val(rt || '');
                    $('#rw').val(rw || '');
                    $('#telepon').val(telepon);
                    $('#email').val(email);
                    $('#dusun').val(dusun);

                    // Store the location IDs in hidden fields
                    $('#province_id_hidden').val(provinceId || '');
                    $('#district_id_hidden').val(districtId || '');
                    $('#sub_district_id_hidden').val(subDistrictId || '');
                    $('#village_id_hidden').val(villageId || '');

                    // Use the new function to populate location dropdowns
                    populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);

                    // Fetch family members through controller route
                    $.ajax({
                        url: "{{ route('getFamilyMembers') }}",
                        type: "GET",
                        data: { kk: selectedKK },
                        success: function (response) {
                            if (response.status === "OK") {
                                $('#jml_anggota_kk').val(response.count);

                                // Clear previous fields
                                $('#familyMembersContainer').empty();

                                // Create fields for each family member
                                if (response.data && Array.isArray(response.data)) {
                                    response.data.forEach((member, index) => {
                                        const fieldHtml = `
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                        <input type="text"
                                            value="${member.full_name} - ${member.family_status}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                            readonly>
                                        <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                        <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                    </div>
                                `;
                                        $('#familyMembersContainer').append(fieldHtml);
                                    });
                                }
                            } else {
                                $('#jml_anggota_kk').val(0);
                                $('#familyMembersContainer').empty();
                            }
                        },
                        error: function () {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    });

                } else {
                    // Kosongkan field jika tidak ada pilihan
                    $('#full_name').val('').trigger('change.select2');
                    $('#address').val('');
                    $('#postal_code').val('');
                    $('#rt').val('');
                    $('#rw').val('');
                    $('#telepon').val('');
                    $('#email').val('');
                    $('#provinc_id').val('');
                    $('#district_id').val('');
                    $('#sub_district_id').val('');
                    $('#village_id').val('');
                    $('#dusun').val('');
                }

                isUpdating = false; // Reset flag
            });

            // Event handler for fullNameSelect change event
            $('#full_name').on('change', function() {
                if (isUpdating) return; // Hindari rekursi
                isUpdating = true;

                const selectedName = $(this).val(); // Ambil nilai yang dipilih
                const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

                if (selectedName) {
                    // Set KK value but don't trigger change to avoid recursion
                    const kk = selectedOption.attr('data-kk');
                    $('#kkSelect').val(kk).trigger('change.select2');

                    // Directly populate all fields from the name selection data attributes
                    const address = selectedOption.attr('data-address');
                    const postalCode = selectedOption.attr('data-postal-code');
                    const rt = selectedOption.attr('data-rt');
                    const rw = selectedOption.attr('data-rw');
                    const telepon = selectedOption.attr('data-telepon') || '';
                    const email = selectedOption.attr('data-email') || '';
                    // Get location IDs directly (not codes)
                    const provinceId = selectedOption.attr('data-province-id');
                    const districtId = selectedOption.attr('data-district-id');
                    const subDistrictId = selectedOption.attr('data-sub-district-id');
                    const villageId = selectedOption.attr('data-village-id');
                    const dusun = selectedOption.attr('data-dusun') || '';

                    // Fill in all form fields
                    $('#address').val(address || '');
                    $('#postal_code').val(postalCode || '');
                    $('#rt').val(rt || '');
                    $('#rw').val(rw || '');
                    $('#telepon').val(telepon);
                    $('#email').val(email);
                    $('#dusun').val(dusun);

                    // Store the location IDs in hidden fields
                    $('#province_id_hidden').val(provinceId || '');
                    $('#district_id_hidden').val(districtId || '');
                    $('#sub_district_id_hidden').val(subDistrictId || '');
                    $('#village_id_hidden').val(villageId || '');

                    // Use the new function to populate location dropdowns
                    populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);

                    // Fetch family members through controller route
                    $.ajax({
                        url: "{{ route('getFamilyMembers') }}",
                        type: "GET",
                        data: { kk: kk },
                        success: function (response) {
                            if (response.status === "OK") {
                                $('#jml_anggota_kk').val(response.count);

                                // Clear previous fields
                                $('#familyMembersContainer').empty();

                                // Create fields for each family member
                                if (response.data && Array.isArray(response.data)) {
                                    response.data.forEach((member, index) => {
                                        const fieldHtml = `
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                        <input type="text"
                                            value="${member.full_name} - ${member.family_status}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                            readonly>
                                        <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                        <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                    </div>
                                `;
                                        $('#familyMembersContainer').append(fieldHtml);
                                    });
                                }
                            } else {
                                $('#jml_anggota_kk').val(0);
                                $('#familyMembersContainer').empty();
                            }
                        },
                        error: function () {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    });
                } else {
                    // Clear fields if no name is selected
                    $('#address').val('');
                    $('#postal_code').val('');
                    $('#rt').val('');
                    $('#rw').val('');
                    $('#telepon').val('');
                    $('#email').val('');
                    $('#province_id').val('');
                    $('#district_id').val('');
                    $('#sub_district_id').val('');
                    $('#village_id').val('');
                    $('#dusun').val('');
                    $('#jml_anggota_kk').val('');
                    $('#familyMembersContainer').empty();
                }

                isUpdating = false; // Reset flag
            });

            // Fetch citizens data when page loads
            fetchCitizens();
        });

        // New function to properly fetch and set location data
        function fetchAndSetLocationData(provinceCode, districtCode, subDistrictCode, villageCode) {
            // Step 1: Set province and load its options
            if (!provinceCode) return;

            // Set province value and hidden ID
            $('#province_id').val(provinceCode);
            $('#province_id_hidden').val(provinceIdMap[provinceCode] || '');

            // Step 2: Fetch district data based on province code
            $.ajax({
                url: `{{ url('/location/districts') }}/${provinceCode}`,
                type: 'GET',
                success: function (response) {
                    // Clear and prepare district dropdown
                    $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

                    // Create fresh ID to Code mappings for districts
                    districtIdMap = {};
                    districtCodeMap = {};

                    // Populate district options
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function (item) {
                            // Store both mappings
                            districtIdMap[item.code] = item.id;
                            districtCodeMap[item.id] = item.code;

                            $('#district_id').append(`<option value="${item.code}">${item.name}</option>`);
                        });
                    }

                    // Enable district dropdown and set the selected value using the code
                    $('#district_id').prop('disabled', false).val(districtCode);

                    // Set the hidden district ID
                    if (districtCode && districtIdMap[districtCode]) {
                        $('#district_id_hidden').val(districtIdMap[districtCode]);
                    }

                    // If district code exists, fetch sub-districts
                    if (districtCode) {
                        $.ajax({
                            url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                            type: 'GET',
                            success: function (response) {
                                // Clear and prepare sub-district dropdown
                                $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                                // Create fresh mappings for subdistricts from this district
                                subDistrictIdMap = {};
                                subDistrictCodeMap = {};

                                // Populate sub-district options
                                if (response.data && Array.isArray(response.data)) {
                                    response.data.forEach(function (item) {
                                        // Store both mappings
                                        subDistrictIdMap[item.code] = item.id;
                                        subDistrictCodeMap[item.id] = item.code;

                                        $('#sub_district_id').append(`<option value="${item.code}">${item.name}</option>`);
                                    });
                                }

                                // Enable sub-district dropdown and set the selected value using the code
                                $('#sub_district_id').prop('disabled', false).val(subDistrictCode);

                                // Set the hidden sub-district ID
                                if (subDistrictCode && subDistrictIdMap[subDistrictCode]) {
                                    $('#sub_district_id_hidden').val(subDistrictIdMap[subDistrictCode]);
                                }

                                // If sub-district code exists, fetch villages
                                if (subDistrictCode) {
                                    $.ajax({
                                        url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                                        type: 'GET',
                                        success: function (response) {
                                            // Clear and prepare village dropdown
                                            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                                            // Create fresh mappings for villages from this subdistrict
                                            villageIdMap = {};
                                            villageCodeMap = {};

                                            // Populate village options
                                            if (response.data && Array.isArray(response.data)) {
                                                response.data.forEach(function (item) {
                                                    // Store both mappings
                                                    villageIdMap[item.code] = item.id;
                                                    villageCodeMap[item.id] = item.code;

                                                    $('#village_id').append(`<option value="${item.code}">${item.name}</option>`);
                                                });
                                            }

                                            // Enable village dropdown and set the selected value using the code
                                            $('#village_id').prop('disabled', false).val(villageCode);

                                            // Set the hidden village ID
                                            if (villageCode && villageIdMap[villageCode]) {
                                                $('#village_id_hidden').val(villageIdMap[villageCode]);
                                            }
                                        },
                                        error: function (error) {
                                            $('#village_id').empty().append('<option value="">Error loading data</option>');
                                        }
                                    });
                                }
                            },
                            error: function (error) {
                                $('#sub_district_id').empty().append('<option value="">Error loading data</option>');
                            }
                        });
                    }
                },
                error: function (error) {
                    $('#district_id').empty().append('<option value="">Error loading data</option>');
                }
            });
        }

        // Event handler untuk dropdown provinsi
        $('#province_id').on('change', function () {
            const provinceCode = $(this).val();

            // Set the hidden province ID field
            if (provinceCode && provinceIdMap[provinceCode]) {
                $('#province_id_hidden').val(provinceIdMap[provinceCode]);
            } else {
                $('#province_id_hidden').val('');
            }

            // Reset dropdown kabupaten, kecamatan, dan desa
            $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>').prop('disabled', true);
            $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            // Reset hidden fields too
            $('#district_id_hidden').val('');
            $('#sub_district_id_hidden').val('');
            $('#village_id_hidden').val('');

            if (provinceCode) {
                // Tampilkan loading state
                $('#district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Ambil data kabupaten dari API
                $.ajax({
                    url: `{{ url('/location/districts') }}/${provinceCode}`,
                    type: 'GET',
                    success: function (response) {
                        $('#district_id').empty().append('<option value="">Pilih Kabupaten</option>');

                        // Create fresh ID to Code mappings for districts
                        districtIdMap = {};
                        districtCodeMap = {};

                        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                // Store mappings in both directions for this district
                                districtIdMap[item.code] = item.id;
                                districtCodeMap[item.id] = item.code;

                                $('#district_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#district_id').prop('disabled', false);
                    },
                    error: function (error) {
                        $('#district_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

        // Add event handler for district_id change
        $('#district_id').on('change', function () {
            const districtCode = $(this).val();

            // Set the hidden district ID field
            if (districtCode && districtIdMap[districtCode]) {
                $('#district_id_hidden').val(districtIdMap[districtCode]);
            } else {
                $('#district_id_hidden').val('');
            }

            // Reset sub-district and village dropdowns
            $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            // Reset hidden fields too
            $('#sub_district_id_hidden').val('');
            $('#village_id_hidden').val('');

            if (districtCode) {
                // Show loading state
                $('#sub_district_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Fetch sub-districts from API
                $.ajax({
                    url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                    type: 'GET',
                    success: function (response) {
                        $('#sub_district_id').empty().append('<option value="">Pilih Kecamatan</option>');

                        // Create fresh ID to Code mappings for subdistricts
                        subDistrictIdMap = {};
                        subDistrictCodeMap = {};

                        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                // Store mappings in both directions for this subdistrict
                                subDistrictIdMap[item.code] = item.id;
                                subDistrictCodeMap[item.id] = item.code;

                                $('#sub_district_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#sub_district_id').prop('disabled', false);
                    },
                    error: function (error) {
                        $('#sub_district_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

        // Add event handler for sub_district_id change
        $('#sub_district_id').on('change', function () {
            const subDistrictCode = $(this).val();

            // Set the hidden sub-district ID field
            if (subDistrictCode && subDistrictIdMap[subDistrictCode]) {
                $('#sub_district_id_hidden').val(subDistrictIdMap[subDistrictCode]);
            } else {
                $('#sub_district_id_hidden').val('');
            }

            // Reset village dropdown
            $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);

            // Reset hidden field too
            $('#village_id_hidden').val('');

            if (subDistrictCode) {
                // Show loading state
                $('#village_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');

                // Fetch villages from API
                $.ajax({
                    url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                    type: 'GET',
                    success: function (response) {
                        $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                        // Create fresh ID to Code mappings for villages
                        villageIdMap = {};
                        villageCodeMap = {};

                        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function (item) {
                                // Store mappings in both directions for this village
                                villageIdMap[item.code] = item.id;
                                villageCodeMap[item.id] = item.code;

                                $('#village_id').append(`<option value="${item.code}">${item.name}</option>`);
                            });
                        }

                        $('#village_id').prop('disabled', false);
                    },
                    error: function (error) {
                        $('#village_id').empty().append('<option value="">Error loading data</option>');
                    }
                });
            }
        });

        // Add event handler for village_id change
        $('#village_id').on('change', function() {
            const villageCode = $(this).val();

            // Set the hidden village ID field
            if (villageCode && villageIdMap[villageCode]) {
                $('#village_id_hidden').val(villageIdMap[villageCode]);
            } else {
                $('#village_id_hidden').val('');
            }
        });

        // Add this function to map location IDs to their corresponding codes and populate dropdowns
        async function populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId) {
            try {
                // First, store the IDs in hidden fields
                $('#province_id_hidden').val(provinceId || '');
                $('#district_id_hidden').val(districtId || '');
                $('#sub_district_id_hidden').val(subDistrictId || '');
                $('#village_id_hidden').val(villageId || '');

                // Then populate province dropdown (should already be populated on page load)
                if (provinceId) {
                    // Get province data from API
                    const provinceResponse = await axios.get('{{ route("location.provinces") }}');
                    if (provinceResponse.data && provinceResponse.data.data) {
                        // Find matching province by ID
                        const province = provinceResponse.data.data.find(p => p.id == provinceId);
                        if (province) {
                            // Update province dropdown
                            $('#province_id').val(province.code);

                            // Now get district data for this province
                            const districtResponse = await axios.get(`{{ url('/location/districts') }}/${province.code}`);

                            if (districtResponse.data && Array.isArray(districtResponse.data)) {
                                // Clear and repopulate district dropdown
                                $('#district_id').empty()
                                    .append('<option value="">Pilih Kabupaten</option>')
                                    .prop('disabled', false);

                                // Add all districts
                                districtResponse.data.forEach(district => {
                                    $('#district_id').append(
                                        `<option value="${district.code}" data-id="${district.id}">${district.name}</option>`
                                    );
                                    // Store mapping
                                    districtIdMap[district.code] = district.id;
                                    districtCodeMap[district.id] = district.code;
                                });

                                // If we have a district ID, select it
                                if (districtId) {
                                    // Find the district by ID
                                    const district = districtResponse.data.find(d => d.id == districtId);
                                    if (district) {
                                        $('#district_id').val(district.code);

                                        // Now get subdistrict data
                                        const subDistrictResponse = await axios.get(`{{ url('/location/sub-districts') }}/${district.code}`);

                                        if (subDistrictResponse.data && Array.isArray(subDistrictResponse.data)) {
                                            // Clear and repopulate subdistrict dropdown
                                            $('#sub_district_id').empty()
                                                .append('<option value="">Pilih Kecamatan</option>')
                                                .prop('disabled', false);

                                            // Add all subdistricts
                                            subDistrictResponse.data.forEach(subDistrict => {
                                                $('#sub_district_id').append(
                                                    `<option value="${subDistrict.code}" data-id="${subDistrict.id}">${subDistrict.name}</option>`
                                                );
                                                // Store mapping
                                                subDistrictIdMap[subDistrict.code] = subDistrict.id;
                                                subDistrictCodeMap[subDistrict.id] = subDistrict.code;
                                            });

                                            // If we have a subdistrict ID, select it
                                            if (subDistrictId) {
                                                // Find the subdistrict by ID
                                                const subDistrict = subDistrictResponse.data.find(sd => sd.id == subDistrictId);
                                                if (subDistrict) {
                                                    $('#sub_district_id').val(subDistrict.code);

                                                    // Now get village data
                                                    const villageResponse = await axios.get(`{{ url('/location/villages') }}/${subDistrict.code}`);

                                                    if (villageResponse.data && Array.isArray(villageResponse.data)) {
                                                        // Clear and repopulate village dropdown
                                                        $('#village_id').empty()
                                                            .append('<option value="">Pilih Desa/Kelurahan</option>')
                                                            .prop('disabled', false);

                                                        // Add all villages
                                                        villageResponse.data.forEach(village => {
                                                            $('#village_id').append(
                                                                `<option value="${village.code}" data-id="${village.id}">${village.name}</option>`
                                                            );
                                                            // Store mapping
                                                            villageIdMap[village.code] = village.id;
                                                            villageCodeMap[village.id] = village.code;
                                                        });

                                                        // If we have a village ID, select it
                                                        if (villageId) {
                                                            // Find the village by ID
                                                            const village = villageResponse.data.find(v => v.id == villageId);
                                                            if (village) {
                                                                $('#village_id').val(village.code);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (error) {
                // Silent error handling
            }
        }

        // Update the KK select change handler to use the new function
        $('#kkSelect').on('change', function () {
            if (isUpdating) return; // Hindari rekursi
            isUpdating = true;

            const selectedKK = $(this).val(); // Ambil nilai yang dipilih
            const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

            if (selectedKK) {
                // Ambil data dari atribut data-*
                const fullName = selectedOption.attr('data-full-name');
                const address = selectedOption.attr('data-address');
                const postalCode = selectedOption.attr('data-postal-code');
                const rt = selectedOption.attr('data-rt');
                const rw = selectedOption.attr('data-rw');
                const telepon = selectedOption.attr('data-telepon') || '';
                const email = selectedOption.attr('data-email') || '';
                // Get location IDs directly (not codes)
                const provinceId = selectedOption.attr('data-province-id');
                const districtId = selectedOption.attr('data-district-id');
                const subDistrictId = selectedOption.attr('data-sub-district-id');
                const villageId = selectedOption.attr('data-village-id');
                const dusun = selectedOption.attr('data-dusun') || '';

                // Isi field yang sesuai
                $('#full_name').val(fullName || '').trigger('change.select2');
                $('#address').val(address || '');
                $('#postal_code').val(postalCode || '');
                $('#rt').val(rt || '');
                $('#rw').val(rw || '');
                $('#telepon').val(telepon);
                $('#email').val(email);
                $('#dusun').val(dusun);

                // Store the location IDs in hidden fields
                $('#province_id_hidden').val(provinceId || '');
                $('#district_id_hidden').val(districtId || '');
                $('#sub_district_id_hidden').val(subDistrictId || '');
                $('#village_id_hidden').val(villageId || '');

                // Use the new function to populate location dropdowns
                populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);

                // Fetch family members through controller route
                $.ajax({
                    url: "{{ route('getFamilyMembers') }}",
                    type: "GET",
                    data: { kk: selectedKK },
                    success: function (response) {
                        if (response.status === "OK") {
                            $('#jml_anggota_kk').val(response.count);

                            // Clear previous fields
                            $('#familyMembersContainer').empty();

                            // Create fields for each family member
                            if (response.data && Array.isArray(response.data)) {
                                response.data.forEach((member, index) => {
                                    const fieldHtml = `
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                    <input type="text"
                                        value="${member.full_name} - ${member.family_status}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                        readonly>
                                    <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                    <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                </div>
                            `;
                                    $('#familyMembersContainer').append(fieldHtml);
                                });
                            }
                        } else {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    },
                    error: function () {
                        $('#jml_anggota_kk').val(0);
                        $('#familyMembersContainer').empty();
                    }
                });

            } else {
                // Kosongkan field jika tidak ada pilihan
                $('#full_name').val('').trigger('change.select2');
                $('#address').val('');
                $('#postal_code').val('');
                $('#rt').val('');
                $('#rw').val('');
                $('#telepon').val('');
                $('#email').val('');
                $('#provinc_id').val('');
                $('#district_id').val('');
                $('#sub_district_id').val('');
                $('#village_id').val('');
                $('#dusun').val('');
            }

            isUpdating = false; // Reset flag
        });

        // Similar update for fullNameSelect change handler
        $('#full_name').on('change', function() {
            if (isUpdating) return; // Hindari rekursi
            isUpdating = true;

            const selectedName = $(this).val(); // Ambil nilai yang dipilih
            const selectedOption = $(this).find('option:selected'); // Ambil opsi yang dipilih

            if (selectedName) {
                // Set KK value but don't trigger change to avoid recursion
                const kk = selectedOption.attr('data-kk');
                $('#kkSelect').val(kk).trigger('change.select2');

                // Directly populate all fields from the name selection data attributes
                const address = selectedOption.attr('data-address');
                const postalCode = selectedOption.attr('data-postal-code');
                const rt = selectedOption.attr('data-rt');
                const rw = selectedOption.attr('data-rw');
                const telepon = selectedOption.attr('data-telepon') || '';
                const email = selectedOption.attr('data-email') || '';
                // Get location IDs directly (not codes)
                const provinceId = selectedOption.attr('data-province-id');
                const districtId = selectedOption.attr('data-district-id');
                const subDistrictId = selectedOption.attr('data-sub-district-id');
                const villageId = selectedOption.attr('data-village-id');
                const dusun = selectedOption.attr('data-dusun') || '';

                // Fill in all form fields
                $('#address').val(address || '');
                $('#postal_code').val(postalCode || '');
                $('#rt').val(rt || '');
                $('#rw').val(rw || '');
                $('#telepon').val(telepon);
                $('#email').val(email);
                $('#dusun').val(dusun);

                // Store the location IDs in hidden fields
                $('#province_id_hidden').val(provinceId || '');
                $('#district_id_hidden').val(districtId || '');
                $('#sub_district_id_hidden').val(subDistrictId || '');
                $('#village_id_hidden').val(villageId || '');

                // Use the new function to populate location dropdowns
                populateLocationDropdowns(provinceId, districtId, subDistrictId, villageId);

                // Fetch family members through controller route
                $.ajax({
                    url: "{{ route('getFamilyMembers') }}",
                    type: "GET",
                    data: { kk: kk },
                    success: function (response) {
                        if (response.status === "OK") {
                            $('#jml_anggota_kk').val(response.count);

                            // Clear previous fields
                            $('#familyMembersContainer').empty();

                            // Create fields for each family member
                            if (response.data && Array.isArray(response.data)) {
                                response.data.forEach((member, index) => {
                                    const fieldHtml = `
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Anggota ${index + 1}</label>
                                    <input type="text"
                                        value="${member.full_name} - ${member.family_status}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                                        readonly>
                                    <input type="hidden" name="family_members[${index}][full_name]" value="${member.full_name}">
                                    <input type="hidden" name="family_members[${index}][family_status]" value="${member.family_status}">
                                </div>
                            `;
                                    $('#familyMembersContainer').append(fieldHtml);
                                });
                            }
                        } else {
                            $('#jml_anggota_kk').val(0);
                            $('#familyMembersContainer').empty();
                        }
                    },
                    error: function () {
                        $('#jml_anggota_kk').val(0);
                        $('#familyMembersContainer').empty();
                    }
                });
            } else {
                // Clear fields if no name is selected
                $('#address').val('');
                $('#postal_code').val('');
                $('#rt').val('');
                $('#rw').val('');
                $('#telepon').val('');
                $('#email').val('');
                $('#province_id').val('');
                $('#district_id').val('');
                $('#sub_district_id').val('');
                $('#village_id').val('');
                $('#dusun').val('');
                $('#jml_anggota_kk').val('');
                $('#familyMembersContainer').empty();
            }

            isUpdating = false; // Reset flag
        });
    </script>

    <style>
        /* Make Select2 responsive on mobile */
        @media (max-width: 640px) {
            .select2-container .select2-selection--single {
                height: 40px !important;
                padding: 4px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 32px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }

            .select2-dropdown {
                width: auto !important;
                max-width: 90vw !important;
            }

            #familyMembersContainer .mb-4 {
                margin-bottom: 0.75rem !important;
            }
        }

        /* Custom styling for form elements */
        textarea:focus, input:focus, select:focus {
            outline: none;
        }

        /* Improve responsive form fields */
        .form-responsive-height {
            min-height: 42px;
        }
    </style>
</x-layout>
