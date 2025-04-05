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
                    <div>
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
                    </div>

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

    <!-- Add meta tag for base URL -->
    <meta name="base-url" content="{{ url('/') }}">

    <!-- External JavaScript files -->
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script src="{{ asset('js/family-card-manager.js') }}"></script>

    <script>
        // Set flash messages as data attributes for the SweetAlert utility to use
        document.body.setAttribute('data-success-message', "{{ session('success') }}");
        document.body.setAttribute('data-error-message', "{{ session('error') }}");
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
