<x-layout>
    <div class="p-3 sm:p-4 mt-12 sm:mt-14">

        <!-- Judul H1 -->
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Tambah Data KK</h1>

        <!-- Form Data KK -->
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Data Kartu Keluarga</h2>

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

                <div class="col-span-1 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        readonly></textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" autocomplete="postal-code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        readonly>
                </div>

                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" autocomplete="address-line1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        readonly>
                </div>

                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" autocomplete="address-line2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        readonly>
                </div>

                <div>
                    <label for="jml_anggota_kk" class="block text-sm font-medium text-gray-700">Jumlah Anggota
                        Keluarga</label>
                    <input type="text" id="jml_anggota_kk" name="jml_anggota_kk" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2 bg-gray-50"
                        readonly>
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_id" name="province_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="province_id" id="province_id_hidden">
                </div>

                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select id="district_id" name="district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" name="district_id" id="district_id_hidden">
                </div>

                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select id="sub_district_id" name="sub_district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" name="sub_district_id" id="sub_district_id_hidden">
                </div>

                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                    <select id="village_id" name="village_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                    <input type="hidden" name="village_id" id="village_id_hidden">
                </div>

                <div>
                    <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                    <input type="text" name="dusun" id="dusun" autocomplete="address-level5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        readonly>
                </div>
            </div>

            <!-- Daftar anggota keluarga yang sudah ada -->
            <div class="mt-6">
                <h3 class="text-md font-medium text-gray-700 mb-3">Daftar Anggota Keluarga</h3>
                <div id="familyMembersContainer" class="bg-gray-50 p-4 rounded-lg">
                    <!-- Family member fields will be inserted here -->
                    <div class="text-gray-500 italic text-sm" id="emptyFamilyMessage">Pilih No KK untuk melihat anggota keluarga</div>
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
                            rows="2" readonly></textarea>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="city" id="city" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2" readonly>
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">Provinsi/Negara
                            Bagian</label>
                        <input type="text" name="state" id="state" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2" readonly>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="country" id="country" autocomplete="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2" readonly>
                    </div>

                    <div>
                        <label for="foreign_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos Luar
                            Negeri</label>
                        <input type="text" name="foreign_postal_code" id="foreign_postal_code"
                            autocomplete="postal-code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah Anggota Keluarga -->
        <form id="addFamilyMemberForm" method="POST" action="{{ route('superadmin.datakk.store-family-member') }}" class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
            @csrf
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Anggota Keluarga</h2>

            <!-- Form hanya aktif jika KK sudah dipilih -->
            <div id="familyMemberFormFields" class="opacity-50 pointer-events-none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Add hidden fields for ALL required data -->
                    <input type="hidden" id="kk" name="kk">
                    <input type="hidden" id="form_address" name="address">
                    <input type="hidden" id="form_postal_code" name="postal_code">
                    <input type="hidden" id="form_rt" name="rt">
                    <input type="hidden" id="form_rw" name="rw">
                    <input type="hidden" id="form_province_id" name="province_id">
                    <input type="hidden" id="form_district_id" name="district_id">
                    <input type="hidden" id="form_sub_district_id" name="sub_district_id">
                    <input type="hidden" id="form_village_id" name="village_id">
                    <input type="hidden" id="form_hamlet" name="hamlet">

                    <!-- Foreign address hidden fields -->
                    <input type="hidden" id="form_foreign_address" name="foreign_address">
                    <input type="hidden" id="form_city" name="city">
                    <input type="hidden" id="form_state" name="state">
                    <input type="hidden" id="form_country" name="country">
                    <input type="hidden" id="form_foreign_postal_code" name="foreign_postal_code">

                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" id="nik" name="nik" pattern="\d{16}" maxlength="16"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="member_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="member_full_name" name="full_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select id="gender" name="gender"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="1">Laki-Laki</option>
                            <option value="2">Perempuan</option>
                        </select>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Umur -->
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                        <input type="number" id="age" name="age"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" id="birth_place" name="birth_place"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Akta Lahir -->
                    <div>
                        <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                        <select id="birth_certificate" name="birth_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Status</option>
                            <option value="1">Ada</option>
                            <option value="2" selected>Tidak Ada</option>
                        </select>
                    </div>

                    <!-- No Akta Lahir -->
                    <div>
                        <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                        <input type="text" id="birth_certificate_no" name="birth_certificate_no"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Akta Perkawinan -->
                    <div>
                        <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Perkawinan</label>
                        <select id="marital_certificate" name="marital_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Status</option>
                            <option value="1">Ada</option>
                            <option value="2" selected>Tidak Ada</option>
                        </select>
                    </div>

                    <!-- No Akta Perkawinan -->
                    <div>
                        <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perkawinan</label>
                        <input type="text" id="marital_certificate_no" name="marital_certificate_no"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Perkawinan -->
                    <div>
                        <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Perkawinan</label>
                        <input type="date" id="marriage_date" name="marriage_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Akta Cerai -->
                    <div>
                        <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
                        <select id="divorce_certificate" name="divorce_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Status</option>
                            <option value="1">Ada</option>
                            <option value="2" selected>Tidak Ada</option>
                        </select>
                    </div>

                    <!-- No Akta Perceraian -->
                    <div>
                        <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perceraian</label>
                        <input type="text" id="divorce_certificate_no" name="divorce_certificate_no"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Tanggal Perceraian -->
                    <div>
                        <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Perceraian</label>
                        <input type="date" id="divorce_certificate_date" name="divorce_certificate_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Status Hubungan Dalam Keluarga -->
                    <div>
                        <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam Keluarga</label>
                        <select id="family_status" name="family_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Status</option>
                            <option value="1">ANAK</option>
                            <option value="3">ISTRI</option>
                            <option value="4">ORANG TUA</option>
                            <option value="5">MERTUA</option>
                            <option value="6">CUCU</option>
                            <option value="7">FAMILI LAIN</option>
                        </select>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                        <select id="religion" name="religion"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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

                    <!-- Pendidikan Terakhir -->
                    <div>
                        <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                        <select id="education_status" name="education_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Pendidikan</option>
                            <option value="1">Tidak/Belum Sekolah</option>
                            <option value="2">Belum tamat SD/Sederajat</option>
                            <option value="3">Tamat SD</option>
                            <option value="4">SLTP/SMP/Sederajat</option>
                            <option value="5">SLTA/SMA/Sederajat</option>
                            <option value="6">Diploma I/II</option>
                            <option value="7">Akademi/Diploma III/ Sarjana Muda</option>
                            <option value="8">Diploma IV/ Strata I/ Strata II</option>
                            <option value="9">Strata III</option>
                            <option value="10">Lainnya</option>
                        </select>
                    </div>

                    <!-- Jenis Pekerjaan -->
                    <div>
                        <label for="job_type_id" class="block text-sm font-medium text-gray-700">Jenis Pekerjaan</label>
                        <select id="job_type_id" name="job_type_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Pekerjaan</option>
                            @forelse($jobs as $job)
                                <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                            @empty
                                <option value="">Tidak ada data pekerjaan</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Tag Lokasi -->
                    <div>
                        <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log, Lat)</label>
                        <input type="text" id="coordinate" name="coordinate"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Telephone -->
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="text" id="telephone" name="telephone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- NIK Ibu -->
                    <div>
                        <label for="nik_mother" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                        <input type="text" id="nik_mother" name="nik_mother"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                        <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                        <input type="text" id="mother" name="mother"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- NIK Ayah -->
                    <div>
                        <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                        <input type="text" id="nik_father" name="nik_father"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Nama Ayah -->
                    <div>
                        <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                        <input type="text" id="father" name="father"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Convert hidden fields to visible form controls -->
                    <!-- Kewarganegaraan -->
                    <div>
                        <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                        <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Kewarganegaraan</option>
                            <option value="1">WNA</option>
                            <option value="2" selected>WNI</option>
                        </select>
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                        <select id="blood_type" name="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Golongan Darah</option>
                            <option value="1">A</option>
                            <option value="2">B</option>
                            <option value="3">AB</option>
                            <option value="4">O</option>
                            <option value="5">A+</option>
                            <option value="6">A-</option>
                            <option value="7">B+</option>
                            <option value="8">B-</option>
                            <option value="9">AB+</option>
                            <option value="10">AB-</option>
                            <option value="11">O+</option>
                            <option value="12">O-</option>
                            <option value="13" selected>Tidak Tahu</option>
                        </select>
                    </div>

                    <!-- Status Perkawinan -->
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                        <select id="marital_status" name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Status</option>
                            <option value="1" selected>Belum Kawin</option>
                            <option value="2">Kawin Tercatat</option>
                            <option value="3">Kawin Belum Tercatat</option>
                            <option value="4">Cerai Hidup Tercatat</option>
                            <option value="5">Cerai Hidup Belum Tercatat</option>
                            <option value="6">Cerai Mati</option>
                        </select>
                    </div>

                    <!-- Kelainan Fisik dan Mental -->
                    <div>
                        <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan Mental</label>
                        <select id="mental_disorders" name="mental_disorders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="1">Ada</option>
                            <option value="2" selected>Tidak Ada</option>
                        </select>
                    </div>

                    <!-- Penyandang Cacat -->
                    <div>
                        <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                        <select id="disabilities" name="disabilities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Status</option>
                            <option value="1">Fisik</option>
                            <option value="2">Netra/Buta</option>
                            <option value="3">Rungu/Wicara</option>
                            <option value="4">Mental/Jiwa</option>
                            <option value="5">Fisik dan Mental</option>
                            <option value="6" selected>Lainnya</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Deceased">Deceased</option>
                            <option value="Moved">Moved</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit" id="addFamilyMemberBtn"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                        Tambah Anggota Keluarga
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-6 flex justify-end">
            <button type="button" onclick="window.history.back()"
                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </button>
        </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            // ===== INISIALISASI ELEMEN DOM =====
            const kkSelect = document.getElementById('kkSelect');
            const familyMemberForm = document.getElementById('familyMemberFormFields');
            const addFamilyMemberForm = document.getElementById('addFamilyMemberForm');
            const emptyFamilyMessage = document.getElementById('emptyFamilyMessage');

            console.log("Form elements:", {
                kkSelect: kkSelect ? "Found" : "Not found",
                familyMemberForm: familyMemberForm ? "Found" : "Not found",
                addFamilyMemberForm: addFamilyMemberForm ? "Found" : "Not found"
            });

            // ===== FUNGSI UTAMA UNTUK MENGELOLA DATA KK =====

            // 1. Fungsi untuk menyimpan data KK di localStorage
            function saveKKDataToLocalStorage() {
                const kkData = {
                    kk: kkSelect.value,
                    address: document.getElementById('address').value,
                    postal_code: document.getElementById('postal_code').value,
                    rt: document.getElementById('rt').value,
                    rw: document.getElementById('rw').value,
                    province_id: document.getElementById('province_id_hidden').value,
                    district_id: document.getElementById('district_id_hidden').value,
                    sub_district_id: document.getElementById('sub_district_id_hidden').value,
                    village_id: document.getElementById('village_id_hidden').value,
                    dusun: document.getElementById('dusun').value,
                    jml_anggota_kk: document.getElementById('jml_anggota_kk').value,

                    // Data alamat luar negeri
                    foreign_address: document.getElementById('foreign_address').value,
                    city: document.getElementById('city').value,
                    state: document.getElementById('state').value,
                    country: document.getElementById('country').value,
                    foreign_postal_code: document.getElementById('foreign_postal_code').value
                };

                localStorage.setItem('kkDetailData', JSON.stringify(kkData));
                console.log('Data KK berhasil disimpan ke localStorage:', kkData);

                return kkData;
            }

            // 2. Fungsi untuk memuat data KK dari localStorage
            function loadKKDataFromLocalStorage() {
                try {
                    const savedData = localStorage.getItem('kkDetailData');
                    if (!savedData) return null;

                    const kkData = JSON.parse(savedData);
                    console.log('Data KK berhasil dimuat dari localStorage:', kkData);
                    return kkData;
                } catch (error) {
                    console.error('Error saat memuat data KK:', error);
                    return null;
                }
            }

            // 3. Fungsi untuk mengisi form dengan data KK
            function fillKKFormFields(kkData) {
                if (!kkData || !kkData.kk) return false;

                // Tunggu sampai Select2 siap
                const checkSelect2Ready = setInterval(() => {
                    if (kkSelect && $(kkSelect).data('select2')) {
                        clearInterval(checkSelect2Ready);

                        // Set nilai pada dropdown KK
                        $(kkSelect).val(kkData.kk).trigger('change');

                        // Isi semua field KK
                        document.getElementById('address').value = kkData.address || '';
                        document.getElementById('postal_code').value = kkData.postal_code || '';
                        document.getElementById('rt').value = kkData.rt || '';
                        document.getElementById('rw').value = kkData.rw || '';
                        document.getElementById('province_id_hidden').value = kkData.province_id || '';
                        document.getElementById('district_id_hidden').value = kkData.district_id || '';
                        document.getElementById('sub_district_id_hidden').value = kkData.sub_district_id || '';
                        document.getElementById('village_id_hidden').value = kkData.village_id || '';
                        document.getElementById('dusun').value = kkData.dusun || '';
                        document.getElementById('jml_anggota_kk').value = kkData.jml_anggota_kk || '';

                        // Data alamat luar negeri
                        document.getElementById('foreign_address').value = kkData.foreign_address || '';
                        document.getElementById('city').value = kkData.city || '';
                        document.getElementById('state').value = kkData.state || '';
                        document.getElementById('country').value = kkData.country || '';
                        document.getElementById('foreign_postal_code').value = kkData.foreign_postal_code || '';

                        // Isi hidden fields pada form anggota keluarga
                        copyDataToFamilyMemberForm(kkData);

                        // Aktifkan form anggota keluarga
                        enableFamilyMemberForm();

                        return true;
                    }
                }, 200);

                // Jika Select2 belum dimuat, coba cara lain sebagai fallback
                setTimeout(() => {
                    if (kkSelect) {
                        kkSelect.value = kkData.kk;

                        // Trigger change event
                        const event = new Event('change');
                        kkSelect.dispatchEvent(event);

                        copyDataToFamilyMemberForm(kkData);
                        enableFamilyMemberForm();
                    }
                }, 1000);

                return true;
            }

            // 4. Fungsi untuk menyalin data KK ke form anggota keluarga
            function copyDataToFamilyMemberForm(kkData) {
                // Set data KK ke hidden fields
                document.getElementById('kk').value = kkData.kk;
                document.getElementById('form_address').value = kkData.address || '';
                document.getElementById('form_postal_code').value = kkData.postal_code || '';
                document.getElementById('form_rt').value = kkData.rt || '';
                document.getElementById('form_rw').value = kkData.rw || '';
                document.getElementById('form_province_id').value = kkData.province_id || '';
                document.getElementById('form_district_id').value = kkData.district_id || '';
                document.getElementById('form_sub_district_id').value = kkData.sub_district_id || '';
                document.getElementById('form_village_id').value = kkData.village_id || '';
                document.getElementById('form_hamlet').value = kkData.dusun || '';

                // Data alamat luar negeri
                document.getElementById('form_foreign_address').value = kkData.foreign_address || '';
                document.getElementById('form_city').value = kkData.city || '';
                document.getElementById('form_state').value = kkData.state || '';
                document.getElementById('form_country').value = kkData.country || '';
                document.getElementById('form_foreign_postal_code').value = kkData.foreign_postal_code || '';
            }

            // 5. Fungsi untuk mengaktifkan form anggota keluarga
            function enableFamilyMemberForm() {
                if (familyMemberForm) {
                    familyMemberForm.classList.remove('opacity-50', 'pointer-events-none');
                    console.log("Form anggota keluarga diaktifkan");
                }
            }

            // 6. Fungsi untuk reset form anggota keluarga (hanya field anggota, bukan KK)
            function resetFamilyMemberForm() {
                document.getElementById('nik').value = '';
                document.getElementById('member_full_name').value = '';
                document.getElementById('gender').value = '';
                document.getElementById('birth_date').value = '';
                document.getElementById('age').value = '';
                document.getElementById('birth_place').value = '';
                document.getElementById('family_status').value = '';
                document.getElementById('religion').value = '';
                document.getElementById('education_status').value = '';
                document.getElementById('job_type_id').value = '';
                document.getElementById('telephone').value = '';
                document.getElementById('email').value = '';
                document.getElementById('mother').value = '';
                document.getElementById('father').value = '';
                document.getElementById('nik_mother').value = '';
                document.getElementById('nik_father').value = '';
                document.getElementById('birth_certificate').value = '2'; // Reset ke default
                document.getElementById('birth_certificate_no').value = '';
                document.getElementById('marital_certificate').value = '2';
                document.getElementById('marital_certificate_no').value = '';
                document.getElementById('marriage_date').value = '';
                document.getElementById('divorce_certificate').value = '2';
                document.getElementById('divorce_certificate_no').value = '';
                document.getElementById('divorce_certificate_date').value = '';
                document.getElementById('blood_type').value = '13';
                document.getElementById('marital_status').value = '1';
                document.getElementById('mental_disorders').value = '2';
                document.getElementById('disabilities').value = '6';
                document.getElementById('citizen_status').value = '2';
                document.getElementById('status').value = 'Active';
                document.getElementById('coordinate').value = '';
            }

            // ===== EVENT LISTENERS =====

            // PERBAIKAN UNTUK MENGATASI KK HILANG SETELAH SUBMIT

            // 1. Gunakan sessionStorage untuk data KK yang dipilih (lebih andal untuk redirect)
            if (kkSelect) {
                kkSelect.addEventListener('change', function() {
                    const selectedValue = this.value;
                    if (selectedValue) {
                        // Simpan ke sessionStorage (lebih stabil saat redirect)
                        console.log("Menyimpan KK ke sessionStorage:", selectedValue);
                        sessionStorage.setItem('selectedKK', selectedValue);
                        
                        // Set ke hidden field
                        document.getElementById('kk').value = selectedValue;
                    }
                });
            }

            // 2. Fungsi untuk memastikan nilai KK tersedia dari berbagai sumber
            function ensureKKValue() {
                // Coba dari hidden field terlebih dahulu
                let kkValue = document.getElementById('kk').value;
                
                // Jika kosong, coba dari select biasa
                if (!kkValue && kkSelect) {
                    kkValue = kkSelect.value;
                }
                
                // Jika masih kosong dan menggunakan Select2, coba dari data API Select2
                if (!kkValue && $(kkSelect).data('select2')) {
                    kkValue = $(kkSelect).select2('val') || $(kkSelect).find(':selected').val();
                }
                
                // Jika masih kosong, coba dari sessionStorage
                if (!kkValue) {
                    kkValue = sessionStorage.getItem('selectedKK');
                }
                
                // Jika masih kosong, coba dari localStorage sebagai fallback terakhir
                if (!kkValue) {
                    try {
                        const savedData = JSON.parse(localStorage.getItem('kkDetailData'));
                        if (savedData && savedData.kk) {
                            kkValue = savedData.kk;
                        }
                    } catch (e) {
                        console.error('Error parsing kkDetailData from localStorage:', e);
                    }
                }
                
                // Update hidden field dengan nilai yang ditemukan
                if (kkValue) {
                    document.getElementById('kk').value = kkValue;
                }
                
                console.log('Hasil final ensureKKValue:', kkValue);
                return kkValue;
            }

            // 3. Override event submit untuk memastikan KK tersedia sebelum form dikirim
            if (addFamilyMemberForm) {
                // Simpan referensi ke event handler asli
                const originalSubmitHandler = addFamilyMemberForm.onsubmit;
                
                // Tambahkan handler baru yang diprioritaskan
                addFamilyMemberForm.onsubmit = function(e) {
                    // Pastikan KK terpilih dan tersimpan
                    ensureKKValue();
                    
                    // Double-check nilai KK
                    const currentKK = document.getElementById('kk').value;
                    if (!currentKK) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert("No KK tidak ditemukan. Silakan pilih No KK terlebih dahulu.");
                        return false;
                    }
                    
                    // Simpan ke localStorage dan sessionStorage sebagai backup
                    saveKKDataToLocalStorage();
                    sessionStorage.setItem('selectedKK', currentKK);
                    
                    // Debug log
                    console.log("Form disubmit dengan KK:", currentKK);
                    
                    // Panggil handler asli jika tersedia
                    if (typeof originalSubmitHandler === 'function') {
                        return originalSubmitHandler.call(this, e);
                    }
                    
                    // Default: lanjutkan submit
                    return true;
                };
            }

            // 4. Perbaikan load data dari localStorage/sessionStorage saat halaman dimuat
            window.addEventListener('load', function() {
                console.log("Window loaded, checking for saved KK data...");
                
                // Prioritaskan sessionStorage untuk KK terakhir yang dipilih
                const savedKK = sessionStorage.getItem('selectedKK');
                if (savedKK && kkSelect) {
                    console.log("Found saved KK in sessionStorage:", savedKK);
                    
                    // Tunggu sampai options dropdown tersedia
                    const checkOptionsInterval = setInterval(function() {
                        const options = kkSelect.options;
                        if (options.length > 1) {
                            clearInterval(checkOptionsInterval);
                            
                            // Cari option yang sesuai
                            let optionFound = false;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === savedKK) {
                                    kkSelect.value = savedKK;
                                    optionFound = true;
                                    
                                    // Trigger change event
                                    if ($(kkSelect).data('select2')) {
                                        $(kkSelect).val(savedKK).trigger('change');
                                    } else {
                                        const event = new Event('change');
                                        kkSelect.dispatchEvent(event);
                                    }
                                    
                                    console.log("KK option found and selected:", savedKK);
                                    break;
                                }
                            }
                            
                            if (!optionFound) {
                                console.warn("Saved KK not found in options:", savedKK);
                            }
                        }
                    }, 200);
                    
                    // Safety timeout
                    setTimeout(function() {
                        clearInterval(checkOptionsInterval);
                    }, 5000);
                } else {
                    console.log("No saved KK in sessionStorage, falling back to localStorage");
                }
            });

            // ===== TAMBAHAN UI ELEMENTS =====

            // Tambahkan tombol untuk mengaktifkan form & menghapus data
            const formHeader = document.querySelector('#addFamilyMemberForm h2');
            if (formHeader) {
                // Tombol aktifkan form
                const enableButton = document.createElement('button');
                enableButton.type = 'button';
                enableButton.className = 'ml-4 px-3 py-1 bg-blue-500 text-white rounded text-sm';
                enableButton.textContent = 'Aktifkan Form';
                enableButton.onclick = function() {
                    enableFamilyMemberForm();
                    this.textContent = 'Form Aktif';
                    this.disabled = true;
                    this.className = 'ml-4 px-3 py-1 bg-green-500 text-white rounded text-sm';
                };

                // Tombol hapus data
                const clearButton = document.createElement('button');
                clearButton.type = 'button';
                clearButton.className = 'ml-2 px-3 py-1 bg-red-500 text-white rounded text-sm';
                clearButton.textContent = 'Bersihkan Data';
                clearButton.onclick = function() {
                    localStorage.removeItem('kkDetailData');
                    location.reload();
                };

                formHeader.appendChild(enableButton);
                formHeader.appendChild(clearButton);
            }

            // ===== INISIALISASI SAAT HALAMAN DIMUAT =====

            // Aktifkan form untuk testing
            enableFamilyMemberForm();

            // Ambil data KK yang tersimpan
            const savedKKData = loadKKDataFromLocalStorage();

            // Jika ada data KK tersimpan, isi form dengan data tersebut
            if (savedKKData) {
                fillKKFormFields(savedKKData);

                // Jika ada pesan sukses (setelah submit), reset form anggota keluarga
                if ({{ session('success') ? 'true' : 'false' }}) {
                    resetFamilyMemberForm();

                    // Tampilkan pesan sukses
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Anggota keluarga berhasil ditambahkan. Anda dapat melanjutkan menambah anggota berikutnya.',
                            confirmButtonText: 'Lanjutkan'
                        });
                    }, 500);
                }
            }

            // Hapus event beforeunload yang menghapus data KK
            // Ini mencegah data terhapus saat refresh halaman
            window.removeEventListener('beforeunload', function() {});
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
