<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Ahli Waris</h1>

        <form method="POST" action="{{ route('superadmin.surat.ahli-waris.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Daftar Ahli Waris Section (Moved to top) -->
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Daftar Ahli Waris</h2>
                <div id="heirs-container">
                    <!-- Template for heir row, will be cloned by JavaScript -->
                    <div class="heir-row border p-4 rounded-md mb-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                <select name="nik[]" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih NIK</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <select name="full_name[]" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    <option value="">Pilih Nama</option>
                                </select>
                            </div>

                            <!-- Hubungan Keluarga -->
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
                            <!-- Tempat Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="birth_place[]" class="birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date[]" class="birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            </div>

                            <!-- Jenis Kelamin -->
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
                            <!-- Agama -->
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

                            <!-- Alamat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="address[]" class="address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button type="button" class="remove-heir bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                        </div>
                    </div>
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
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="">
                    </div>

                    <!-- Kabupaten -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                            <option value="">Pilih Kabupaten</option>
                        </select>
                        <input type="hidden" id="district_id" name="district_id" value="">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                    </div>

                    <!-- Desa -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                            <option value="">Pilih Desa</option>
                        </select>
                        <input type="hidden" id="village_id" name="village_id" value="">
                    </div>
                </div>
            </div>

            <!-- Informasi Surat Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-4 rounded-md mb-4 bg-gray-50">
                    <!-- Nama Ahli Waris -->
                    <div>
                        <label for="heir_name" class="block text-sm font-medium text-gray-700">Nama Ahli Waris <span class="text-red-500">*</span></label>
                        <input type="text" id="heir_name" name="heir_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nama Almarhum -->
                    <div>
                        <label for="deceased_name" class="block text-sm font-medium text-gray-700">Nama Almarhum <span class="text-red-500">*</span></label>
                        <input type="text" id="deceased_name" name="deceased_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tempat Meninggal -->
                    <div>
                        <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Meninggal <span class="text-red-500">*</span></label>
                        <input type="text" id="death_place" name="death_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tanggal Meninggal -->
                    <div>
                        <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                        <input type="date" id="death_date" name="death_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nomor Akte Kematian -->
                    <div>
                        <label for="death_certificate_number" class="block text-sm font-medium text-gray-700">Nomor Akte Kematian</label>
                        <input type="number" id="death_certificate_number" name="death_certificate_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Akte Kematian -->
                    <div>
                        <label for="death_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Akte Kematian</label>
                        <input type="date" id="death_certificate_date" name="death_certificate_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Surat Waris -->
                    <div>
                        <label for="inheritance_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat Waris</label>
                        <input type="date" id="inheritance_letter_date" name="inheritance_letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Jenis Warisan -->
                    <div>
                        <label for="inheritance_type" class="block text-sm font-medium text-gray-700">Jenis Warisan <span class="text-red-500">*</span></label>
                        <input type="text" id="inheritance_type" name="inheritance_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nomor Surat -->
                    <div>
                        <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Pejabat Penandatangan dropdown -->
                    <div>
                        <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                        <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Pejabat</option>
                            @foreach($signers as $signer)
                                <option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
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
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/location-dropdowns.js') }}"></script>
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
                // Setup location dropdowns using the imported function
                setupLocationDropdowns({!! json_encode($provinces) !!});

                // Handle adding more heirs
                const heirsContainer = document.getElementById('heirs-container');
                const addHeirButton = document.getElementById('add-heir');
                const firstHeirRow = document.querySelector('.heir-row').cloneNode(true);

                // Remove the first heir row template
                heirsContainer.innerHTML = '';

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

                // Function to initialize Select2 on an heir row
                function initializeHeirSelect2(heirRow) {
                    const { nikOptions, nameOptions } = prepareCitizenOptions();
                    const nikSelect = heirRow.querySelector('.nik-select');
                    const nameSelect = heirRow.querySelector('.fullname-select');

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
                            const row = $(this).closest('.heir-row');

                            // Update full name
                            $(row).find('.fullname-select').val(citizen.full_name).trigger('change.select2');

                            // Fill personal data fields - only personal info
                            populateHeirFieldsFromCitizen(row, citizen);

                            // Auto-fill location data if it's not already set
                            if (!$('#province_id').val() || !$('#district_id').val() ||
                                !$('#subdistrict_id').val() || !$('#village_id').val()) {
                                // Use the function from location-dropdowns.js
                                populateLocationDropdowns(
                                    citizen.province_id,
                                    citizen.district_id,
                                    citizen.subdistrict_id || citizen.sub_district_id,
                                    citizen.village_id
                                );
                            }

                            // Do NOT set the signing field here - this should be manually selected
                        }

                        isUpdating = false;
                    });

                    // Full name select change handler
                    $(nameSelect).on('select2:select', function(e) {
                        if (isUpdating) return;
                        isUpdating = true;

                        const citizen = e.params.data.citizen;
                        if (citizen) {
                            const row = $(this).closest('.heir-row');

                            // Update NIK
                            if (citizen.nik) {
                                $(row).find('.nik-select').val(citizen.nik.toString()).trigger('change.select2');
                            }

                            // Fill personal data fields - only personal info
                            populateHeirFieldsFromCitizen(row, citizen);

                            // Auto-fill location data if it's not already set
                            if (!$('#province_id').val() || !$('#district_id').val() ||
                                !$('#subdistrict_id').val() || !$('#village_id').val()) {
                                // Use the function from location-dropdowns.js
                                populateLocationDropdowns(
                                    citizen.province_id,
                                    citizen.district_id,
                                    citizen.subdistrict_id || citizen.sub_district_id,
                                    citizen.village_id
                                );
                            }

                            // Do NOT set the signing field here - this should be manually selected
                        }

                        isUpdating = false;
                    });
                }

                // Function to populate heir fields from citizen data
                function populateHeirFieldsFromCitizen(row, citizen) {
                    // Birth place
                    $(row).find('.birth-place').val(citizen.birth_place || '');

                    // Birth date - handle formatting
                    if (citizen.birth_date) {
                        let birthDate = citizen.birth_date;
                        if (birthDate.includes('/')) {
                            const [day, month, year] = birthDate.split('/');
                            birthDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        }
                        $(row).find('.birth-date').val(birthDate);
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
                    $(row).find('.gender').val(gender).trigger('change');

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
                    $(row).find('.religion').val(religion).trigger('change');

                    // Address
                    $(row).find('.address').val(citizen.address || '');

                    // Family Status - handle numeric values from API
                    if (citizen.family_status !== undefined && citizen.family_status !== null) {
                        let familyStatusValue = citizen.family_status;

                        // If it's a string that contains a number, convert it to number
                        if (typeof familyStatusValue === 'string' && !isNaN(parseInt(familyStatusValue))) {
                            familyStatusValue = parseInt(familyStatusValue);
                        }
                        // If it's a string with text, try to map it to corresponding number
                        else if (typeof familyStatusValue === 'string') {
                            const statusMap = {
                                'anak': 1,
                                'kepala keluarga': 2,
                                'istri': 3,
                                'orang tua': 4,
                                'mertua': 5,
                                'cucu': 6,
                                'famili lain': 7
                            };

                            const normalizedStatus = familyStatusValue.toLowerCase().trim();
                            if (statusMap[normalizedStatus] !== undefined) {
                                familyStatusValue = statusMap[normalizedStatus];
                            }
                        }

                        // Set the numeric value in the dropdown
                        if (!isNaN(familyStatusValue) && familyStatusValue > 0) {
                            $(row).find('select[name="family_status[]"]').val(familyStatusValue).trigger('change');
                        }
                    }
                }

                // Function to add a new heir row
                function addHeirRow() {
                    const heirRowClone = firstHeirRow.cloneNode(true);
                    heirsContainer.appendChild(heirRowClone);

                    // Initialize Select2 on the new row
                    initializeHeirSelect2(heirRowClone);

                    // Remove heir button functionality
                    heirRowClone.querySelector('.remove-heir').addEventListener('click', function() {
                        if (document.querySelectorAll('#heirs-container .heir-row').length > 1) {
                            this.closest('.heir-row').remove();
                        } else {
                            alert('Minimal harus ada satu ahli waris');
                        }
                    });
                }

                // Add first heir row by default
                addHeirRow();

                // Add heir button click handler
                addHeirButton.addEventListener('click', addHeirRow);

                // Form validation for required fields and location data
                setupFormValidation();

                // Make the signing dropdown easier to use with basic styling
                $('#signing').css('width', '100%');
            }
        });
    </script>
</x-layout>
