<x-layout>
    <div class="p-3 sm:p-4 mt-12 sm:mt-14">

        <!-- Judul H1 -->
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Edit Data KK</h1>

        <!-- Form Edit Data KK -->
        <form method="POST" action="{{ route('admin.desa.datakk.update', $kkData['kk']) }}" class="bg-white p-4 sm:p-6 rounded-lg shadow-md mb-8">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-gray-800 mb-4">Data Kartu Keluarga</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Kolom 1: Data Utama -->
                <div class="col-span-1 sm:col-span-2 md:col-span-1">
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" value="{{ $kkData['kk'] }}" autocomplete="off" maxlength="16" pattern="\d{16}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2 bg-gray-100" readonly>
                </div>

                <div class="col-span-1 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">{{ $kkData['address'] ?? '' }}</textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ $kkData['postal_code'] ?? '' }}" autocomplete="postal-code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" value="{{ $kkData['rt'] ?? '' }}" autocomplete="address-line1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" value="{{ $kkData['rw'] ?? '' }}" autocomplete="address-line2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_code" name="province_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" {{ ($kkData['province_id'] ?? '') == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="province_id" id="province_id" value="{{ $kkData['province_id'] ?? '' }}">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select id="district_code" name="district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        {{ empty($districts) ? 'disabled' : '' }}>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" {{ ($kkData['district_id'] ?? '') == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="district_id" id="district_id" value="{{ $kkData['district_id'] ?? '' }}">
                </div>

                <div>
                    <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select id="sub_district_code" name="sub_district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        {{ empty($subDistricts) ? 'disabled' : '' }}>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" {{ ($kkData['sub_district_id'] ?? '') == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="sub_district_id" id="sub_district_id" value="{{ $kkData['sub_district_id'] ?? '' }}">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                    <select id="village_code" name="village_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        {{ empty($villages) ? 'disabled' : '' }}>
                        <option value="">Pilih Desa/Kelurahan</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" {{ ($kkData['village_id'] ?? '') == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="village_id" id="village_id" value="{{ $kkData['village_id'] ?? '' }}">
                </div>

                <div>
                    <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                    <input type="text" name="dusun" id="dusun" value="{{ $kkData['hamlet'] ?? '' }}" autocomplete="address-level5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
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
                        <label for="foreign_address" class="block text-sm font-medium text-gray-700">Alamat Luar
                            Negeri</label>
                        <textarea name="foreign_address" id="foreign_address" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                            rows="2">{{ $kkData['foreign_address'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="city" id="city" value="{{ $kkData['city'] ?? '' }}" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">Provinsi/Negara
                            Bagian</label>
                        <input type="text" name="state" id="state" value="{{ $kkData['state'] ?? '' }}" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="country" id="country" value="{{ $kkData['country'] ?? '' }}" autocomplete="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="foreign_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos Luar
                            Negeri</label>
                        <input type="text" name="foreign_postal_code" id="foreign_postal_code" value="{{ $kkData['foreign_postal_code'] ?? '' }}"
                            autocomplete="postal-code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('admin.desa.datakk.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Batal</a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Add meta tag for base URL -->
    <meta name="base-url" content="{{ url('/') }}">

    <!-- External JavaScript files -->
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script src="{{ asset('js/location-selector.js') }}"></script>

    <script>
        // Set flash messages as data attributes for the SweetAlert utility to use
        document.body.setAttribute('data-success-message', "{{ session('success') }}");
        document.body.setAttribute('data-error-message', "{{ session('error') }}");

        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener for province_code dropdown
            $('#province_code').on('change', function() {
                const provinceCode = $(this).val();
                const provinceOption = $(this).find('option:selected');
                const provinceText = provinceOption.text();

                // Get the province ID from the data attribute or use a lookup
                if (provinceCode) {
                    @foreach($provinces as $province)
                        if ("{{ $province['code'] }}" === provinceCode) {
                            document.getElementById('province_id').value = "{{ $province["id"] }}";
                        }
                    @endforeach
                }
            });

            // Add event listeners for other location dropdowns to ensure IDs are properly set
            $('#district_code').on('change', function() {
                const districtCode = $(this).val();
                if (districtCode) {
                    // Make an AJAX request to get the district details
                    $.get(`${getBaseUrl()}/location/districts/${$('#province_code').val()}`, function(data) {
                        const districts = data;
                        const district = districts.find(d => d.code === districtCode);
                        if (district) {
                            document.getElementById('district_id').value = district.id;
                        }
                    });
                }
            });

            $('#sub_district_code').on('change', function() {
                const subDistrictCode = $(this).val();
                if (subDistrictCode) {
                    // Make an AJAX request to get the sub-district details
                    $.get(`${getBaseUrl()}/location/sub-districts/${$('#district_code').val()}`, function(data) {
                        const subDistricts = data;
                        const subDistrict = subDistricts.find(sd => sd.code === subDistrictCode);
                        if (subDistrict) {
                            document.getElementById('sub_district_id').value = subDistrict.id;
                        }
                    });
                }
            });

            $('#village_code').on('change', function() {
                const villageCode = $(this).val();
                if (villageCode) {
                    // Make an AJAX request to get the village details
                    $.get(`${getBaseUrl()}/location/villages/${$('#sub_district_code').val()}`, function(data) {
                        const villages = data;
                        const village = villages.find(v => v.code === villageCode);
                        if (village) {
                            document.getElementById('village_id').value = village.id;
                        }
                    });
                }
            });

            // Get base URL function if not already defined
            function getBaseUrl() {
                const metaUrl = document.querySelector('meta[name="base-url"]');
                return metaUrl ? metaUrl.getAttribute('content') : window.location.origin;
            }
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
