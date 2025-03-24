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
                            @foreach($districts as $district)
                                <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $kk->district_id == $district['id'] ? 'selected' : '' }}>
                                    {{ $district['name'] }}
                                </option>
                            @endforeach
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
                            @foreach($subDistricts as $subDistrict)
                                <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $kk->sub_district_id == $subDistrict['id'] ? 'selected' : '' }}>
                                    {{ $subDistrict['name'] }}
                                </option>
                            @endforeach
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
                            @foreach($villages as $village)
                                <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $kk->village_id == $village['id'] ? 'selected' : '' }}>
                                    {{ $village['name'] }}
                                </option>
                            @endforeach
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
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Simpan Perubahan</button>
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

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize current values from the KK data
            const currentProvinceId = "{{ $kk->province_id }}";
            const currentDistrictId = "{{ $kk->district_id }}";
            const currentSubDistrictId = "{{ $kk->sub_district_id }}";
            const currentVillageId = "{{ $kk->village_id }}";

            // Make sure hidden values are set
            $('#province_id_hidden').val(currentProvinceId);
            $('#district_id_hidden').val(currentDistrictId);
            $('#sub_district_id_hidden').val(currentSubDistrictId);
            $('#village_id_hidden').val(currentVillageId);

            // Event handler for province dropdown changes
            $('#province_id').on('change', function() {
                const provinceCode = $(this).val();

                // Store the province ID in the hidden field
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.attr('data-id')) {
                    $('#province_id_hidden').val(selectedOption.attr('data-id'));
                }

                // Clear and disable the dependent dropdowns
                $('#district_id')
                    .empty()
                    .append('<option value="">Pilih Kabupaten</option>')
                    .prop('disabled', true);

                $('#sub_district_id')
                    .empty()
                    .append('<option value="">Pilih Kecamatan</option>')
                    .prop('disabled', true);

                $('#village_id')
                    .empty()
                    .append('<option value="">Pilih Desa/Kelurahan</option>')
                    .prop('disabled', true);

                // Clear the hidden fields for dependent dropdowns
                $('#district_id_hidden').val('');
                $('#sub_district_id_hidden').val('');
                $('#village_id_hidden').val('');

                if (provinceCode) {
                    // Show loading state
                    $('#district_id')
                        .empty()
                        .append('<option value="">Loading...</option>')
                        .prop('disabled', true);

                    // Fetch districts for this province
                    $.ajax({
                        url: `{{ url('/location/districts') }}/${provinceCode}`,
                        type: 'GET',
                        success: function(response) {
                            $('#district_id')
                                .empty()
                                .append('<option value="">Pilih Kabupaten</option>');

                            if (response && Array.isArray(response)) {
                                response.forEach(function(district) {
                                    const option = $('<option>')
                                        .val(district.code)
                                        .text(district.name)
                                        .attr('data-id', district.id);

                                    $('#district_id').append(option);
                                });

                                $('#district_id').prop('disabled', false);
                            }
                        },
                        error: function(err) {
                            $('#district_id')
                                .empty()
                                .append('<option value="">Error loading data</option>')
                                .prop('disabled', true);
                        }
                    });
                }
            });

            // Event handler for district dropdown changes
            $('#district_id').on('change', function() {
                const districtCode = $(this).val();

                // Store the district ID in the hidden field
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.attr('data-id')) {
                    $('#district_id_hidden').val(selectedOption.attr('data-id'));
                }

                // Clear and disable the dependent dropdowns
                $('#sub_district_id')
                    .empty()
                    .append('<option value="">Pilih Kecamatan</option>')
                    .prop('disabled', true);

                $('#village_id')
                    .empty()
                    .append('<option value="">Pilih Desa/Kelurahan</option>')
                    .prop('disabled', true);

                // Clear the hidden fields for dependent dropdowns
                $('#sub_district_id_hidden').val('');
                $('#village_id_hidden').val('');

                if (districtCode) {
                    // Show loading state
                    $('#sub_district_id')
                        .empty()
                        .append('<option value="">Loading...</option>')
                        .prop('disabled', true);

                    // Fetch sub-districts for this district
                    $.ajax({
                        url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                        type: 'GET',
                        success: function(response) {
                            $('#sub_district_id')
                                .empty()
                                .append('<option value="">Pilih Kecamatan</option>');

                            if (response && Array.isArray(response)) {
                                response.forEach(function(subDistrict) {
                                    const option = $('<option>')
                                        .val(subDistrict.code)
                                        .text(subDistrict.name)
                                        .attr('data-id', subDistrict.id);

                                    $('#sub_district_id').append(option);
                                });

                                $('#sub_district_id').prop('disabled', false);
                            }
                        },
                        error: function(err) {
                            $('#sub_district_id')
                                .empty()
                                .append('<option value="">Error loading data</option>')
                                .prop('disabled', true);
                        }
                    });
                }
            });

            // Event handler for sub-district dropdown changes
            $('#sub_district_id').on('change', function() {
                const subDistrictCode = $(this).val();

                // Store the sub-district ID in the hidden field
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.attr('data-id')) {
                    $('#sub_district_id_hidden').val(selectedOption.attr('data-id'));
                }

                // Clear and disable the village dropdown
                $('#village_id')
                    .empty()
                    .append('<option value="">Pilih Desa/Kelurahan</option>')
                    .prop('disabled', true);

                // Clear the hidden field for village
                $('#village_id_hidden').val('');

                if (subDistrictCode) {
                    // Show loading state
                    $('#village_id')
                        .empty()
                        .append('<option value="">Loading...</option>')
                        .prop('disabled', true);

                    // Fetch villages for this sub-district
                    $.ajax({
                        url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                        type: 'GET',
                        success: function(response) {
                            $('#village_id')
                                .empty()
                                .append('<option value="">Pilih Desa/Kelurahan</option>');

                            if (response && Array.isArray(response)) {
                                response.forEach(function(village) {
                                    const option = $('<option>')
                                        .val(village.code)
                                        .text(village.name)
                                        .attr('data-id', village.id);

                                    $('#village_id').append(option);
                                });

                                $('#village_id').prop('disabled', false);
                            }
                        },
                        error: function(err) {
                            $('#village_id')
                                .empty()
                                .append('<option value="">Error loading data</option>')
                                .prop('disabled', true);
                        }
                    });
                }
            });

            // Event handler for village dropdown changes
            $('#village_id').on('change', function() {
                // Store the village ID in the hidden field
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.length && selectedOption.attr('data-id')) {
                    $('#village_id_hidden').val(selectedOption.attr('data-id'));
                }
            });

            // Initialize locations for existing data
            function initializeLocations() {
                // Make sure we have the province ID and it's selected in the dropdown
                if (currentProvinceId) {
                    // Get the currently selected province code
                    let provinceCode = $('#province_id').val();
                    if (!provinceCode) {
                        // If we don't have a code, try to find the option with the matching ID
                        const provinceOption = $(`#province_id option[data-id="${currentProvinceId}"]`);
                        if (provinceOption.length) {
                            provinceCode = provinceOption.val();
                            $('#province_id').val(provinceCode);
                        }
                    }

                    // Fetch and populate districts
                    if (provinceCode) {
                        $.ajax({
                            url: `{{ url('/location/districts') }}/${provinceCode}`,
                            type: 'GET',
                            success: function(response) {
                                $('#district_id')
                                    .empty()
                                    .append('<option value="">Pilih Kabupaten</option>');

                                if (response && Array.isArray(response)) {
                                    // Add all district options
                                    response.forEach(function(district) {
                                        const option = $('<option>')
                                            .val(district.code)
                                            .text(district.name)
                                            .attr('data-id', district.id);

                                        // Select this option if it matches our current district ID
                                        if (district.id == currentDistrictId) {
                                            option.prop('selected', true);
                                        }

                                        $('#district_id').append(option);
                                    });

                                    $('#district_id').prop('disabled', false);

                                    // If we have a district selected, load sub-districts
                                    if (currentDistrictId) {
                                        const selectedDistrictOption = $(`#district_id option[data-id="${currentDistrictId}"]`);
                                        if (selectedDistrictOption.length) {
                                            const districtCode = selectedDistrictOption.val();

                                            // Now fetch sub-districts
                                            $.ajax({
                                                url: `{{ url('/location/sub-districts') }}/${districtCode}`,
                                                type: 'GET',
                                                success: function(response) {
                                                    $('#sub_district_id')
                                                        .empty()
                                                        .append('<option value="">Pilih Kecamatan</option>');

                                                    if (response && Array.isArray(response)) {
                                                        // Add all sub-district options
                                                        response.forEach(function(subDistrict) {
                                                            const option = $('<option>')
                                                                .val(subDistrict.code)
                                                                .text(subDistrict.name)
                                                                .attr('data-id', subDistrict.id);

                                                            // Select this option if it matches our current sub-district ID
                                                            if (subDistrict.id == currentSubDistrictId) {
                                                                option.prop('selected', true);
                                                            }

                                                            $('#sub_district_id').append(option);
                                                        });

                                                        $('#sub_district_id').prop('disabled', false);

                                                        // If we have a sub-district selected, load villages
                                                        if (currentSubDistrictId) {
                                                            const selectedSubDistrictOption = $(`#sub_district_id option[data-id="${currentSubDistrictId}"]`);
                                                            if (selectedSubDistrictOption.length) {
                                                                const subDistrictCode = selectedSubDistrictOption.val();

                                                                // Now fetch villages
                                                                $.ajax({
                                                                    url: `{{ url('/location/villages') }}/${subDistrictCode}`,
                                                                    type: 'GET',
                                                                    success: function(response) {
                                                                        $('#village_id')
                                                                            .empty()
                                                                            .append('<option value="">Pilih Desa/Kelurahan</option>');

                                                                        if (response && Array.isArray(response)) {
                                                                            // Add all village options
                                                                            response.forEach(function(village) {
                                                                                const option = $('<option>')
                                                                                    .val(village.code)
                                                                                    .text(village.name)
                                                                                    .attr('data-id', village.id);

                                                                                // Select this option if it matches our current village ID
                                                                                if (village.id == currentVillageId) {
                                                                                    option.prop('selected', true);
                                                                                }

                                                                                $('#village_id').append(option);
                                                                            });

                                                                            $('#village_id').prop('disabled', false);
                                                                        }
                                                                    },
                                                                    error: function(err) {
                                                                        // Silent error handling
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                },
                                                error: function(err) {
                                                    // Silent error handling
                                                }
                                            });
                                        }
                                    }
                                }
                            },
                            error: function(err) {
                                // Silent error handling
                            }
                        });
                    }
                }
            }

            // Call the initialization function to populate location dropdowns
            initializeLocations();
        });
    </script>
</x-layout>
