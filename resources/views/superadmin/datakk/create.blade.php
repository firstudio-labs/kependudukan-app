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
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" autocomplete="off" maxlength="16" pattern="\d{16}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div class="col-span-1 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"></textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" autocomplete="postal-code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" autocomplete="address-line1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" autocomplete="address-line2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                </div>

                <!-- Wilayah -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_code" name="province_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="province_id" id="province_id">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select id="district_code" name="district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" name="district_id" id="district_id">
                </div>

                <div>
                    <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select id="sub_district_code" name="sub_district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" name="sub_district_id" id="sub_district_id">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                    <select id="village_code" name="village_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2"
                        disabled>
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                    <input type="hidden" name="village_id" id="village_id">
                </div>

                <div>
                    <label for="dusun" class="block text-sm font-medium text-gray-700">Dusun/Dukuh/Kampung</label>
                    <input type="text" name="dusun" id="dusun" autocomplete="address-level5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
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
                            rows="2"></textarea>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="city" id="city" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">Provinsi/Negara
                            Bagian</label>
                        <input type="text" name="state" id="state" autocomplete="off"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                        <input type="text" name="country" id="country" autocomplete="country"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>

                    <div>
                        <label for="foreign_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos Luar
                            Negeri</label>
                        <input type="text" name="foreign_postal_code" id="foreign_postal_code"
                            autocomplete="postal-code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-lg p-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah Anggota Keluarga -->
        <form id="addFamilyMemberForm" method="POST" action="{{ route('superadmin.datakk.store-family-members') }}" class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
            @csrf
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Anggota Keluarga</h2>

            <!-- Hidden field to store all family members data as JSON -->
            <input type="hidden" name="family_members_json" id="family_members_json" value="[]">

            <!-- Form fields are now enabled by default -->
            <div id="familyMemberFormFields">
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
                            <option value="2">KEPALA KELUARGA</option>
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
                    <button type="button" id="addToListBtn"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-lg">
                        Tambah ke Daftar
                    </button>
                    <button type="submit" id="submitAllBtn"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                        Simpan Semua Anggota
                    </button>
                </div>
            </div>

            <!-- Table to display added family members -->
            <div class="mt-8" id="familyMembersTableContainer" style="display: none;">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Daftar Anggota Keluarga yang Akan Disimpan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TTL</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="familyMembersTableBody">
                            <!-- Table rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('superadmin.datakk.index') }}"
                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
        </div>
    </div>

    <!-- Add meta tag for base URL -->
    <meta name="base-url" content="{{ url('/') }}">

    <!-- External JavaScript files -->
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script src="{{ asset('js/family-card-manager.js') }}"></script>
    <script src="{{ asset('js/location-selector.js') }}"></script>

    <script>
        // Set flash messages as data attributes for the SweetAlert utility to use
        document.body.setAttribute('data-success-message', "{{ session('success') }}");
        document.body.setAttribute('data-error-message', "{{ session('error') }}");

        // Clear localStorage data when coming to this page from another page (not on refresh)
        document.addEventListener('DOMContentLoaded', function() {
            // Create a flag in sessionStorage to detect page reload vs navigation
            if (sessionStorage.getItem('isPageReload') === null) {
                // First load - set the flag for next time and clear localStorage KK data
                sessionStorage.setItem('isPageReload', 'true');

                // Check if we're coming from another page (not a refresh)
                if (document.referrer && !document.referrer.includes(window.location.pathname)) {
                    localStorage.removeItem('kkDetailData');
                }
            }

            // Reset on page unload
            window.addEventListener('beforeunload', function() {
                sessionStorage.removeItem('isPageReload');
            });

            // ===== INISIALISASI ELEMEN DOM =====
            const kkInput = document.getElementById('kk');
            const familyMemberForm = document.getElementById('familyMemberFormFields');
            const addFamilyMemberForm = document.getElementById('addFamilyMemberForm');
            const emptyFamilyMessage = document.getElementById('emptyFamilyMessage');
            const familyMembersJson = document.getElementById('family_members_json');
            const addToListBtn = document.getElementById('addToListBtn');
            const submitAllBtn = document.getElementById('submitAllBtn');
            const familyMembersTableBody = document.getElementById('familyMembersTableBody');
            const familyMembersTableContainer = document.getElementById('familyMembersTableContainer');

            // Initialize family members array
            let familyMembersArray = [];

            // ===== FUNGSI UNTUK MENGELOLA DAFTAR ANGGOTA KELUARGA =====

            // Function to reset family member form fields without affecting KK data
            function resetFamilyMemberForm() {
                // Reset only family member specific fields
                const fieldsToReset = [
                    'nik', 'member_full_name', 'gender', 'birth_date', 'age', 'birth_place',
                    'family_status', 'religion', 'education_status', 'job_type_id',
                    'birth_certificate', 'birth_certificate_no',
                    'marital_certificate', 'marital_certificate_no', 'marriage_date',
                    'divorce_certificate', 'divorce_certificate_no', 'divorce_certificate_date',
                    'blood_type', 'mental_disorders', 'disabilities', 'citizen_status',
                    'telephone', 'email', 'nik_mother', 'mother', 'nik_father', 'father',
                    'coordinate', 'status'
                ];

                // Reset each field to its default value
                fieldsToReset.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        if (field.tagName === 'SELECT') {
                            field.selectedIndex = 0; // Reset to first option

                            // Handle specific default values
                            if (fieldId === 'birth_certificate' || fieldId === 'marital_certificate' ||
                                fieldId === 'divorce_certificate') {
                                field.value = '2'; // "Tidak Ada"
                            } else if (fieldId === 'blood_type') {
                                field.value = '13'; // "Tidak Tahu"
                            } else if (fieldId === 'marital_status') {
                                field.value = '1'; // "Belum Kawin"
                            } else if (fieldId === 'mental_disorders') {
                                field.value = '2'; // "Tidak Ada"
                            } else if (fieldId === 'disabilities') {
                                field.value = '6'; // "Lainnya"
                            } else if (fieldId === 'citizen_status') {
                                field.value = '2'; // "WNI"
                            } else if (fieldId === 'status') {
                                field.value = 'Active';
                            }
                        } else {
                            field.value = '';
                        }
                    }
                });
            }

            // Function to get all KK data from the form
            function getKKFormData() {
                return {
                    // KK data from the top form
                    kk: document.getElementById('kk').value,
                    address: document.getElementById('address').value,
                    postal_code: document.getElementById('postal_code').value,
                    rt: document.getElementById('rt').value,
                    rw: document.getElementById('rw').value,
                    province_id: document.getElementById('province_id').value,
                    district_id: document.getElementById('district_id').value,
                    sub_district_id: document.getElementById('sub_district_id').value,
                    village_id: document.getElementById('village_id').value,
                    hamlet: document.getElementById('dusun').value,

                    // Foreign address data
                    foreign_address: document.getElementById('foreign_address').value,
                    city: document.getElementById('city').value,
                    state: document.getElementById('state').value,
                    country: document.getElementById('country').value,
                    foreign_postal_code: document.getElementById('foreign_postal_code').value
                };
            }

            // Function to update the table of family members
            function updateFamilyMembersTable() {
                // Clear current table
                familyMembersTableBody.innerHTML = '';

                // Add rows for each family member
                familyMembersArray.forEach((member, index) => {
                    const row = document.createElement('tr');

                    // Convert gender code to text
                    const genderText = member.gender == 1 ? 'L' : 'P';

                    // Convert family status code to text
                    let familyStatusText = 'Lainnya';
                    switch(parseInt(member.family_status)) {
                        case 1: familyStatusText = 'ANAK'; break;
                        case 2: familyStatusText = 'KEPALA KELUARGA'; break;
                        case 3: familyStatusText = 'ISTRI'; break;
                        case 4: familyStatusText = 'ORANG TUA'; break;
                        case 5: familyStatusText = 'MERTUA'; break;
                        case 6: familyStatusText = 'CUCU'; break;
                        case 7: familyStatusText = 'FAMILI LAIN'; break;
                    }

                    // Format birthdate for display
                    const birthInfo = `${member.birth_place}, ${formatDate(member.birth_date)}`;

                    row.innerHTML = `
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${member.nik}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${member.full_name}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${genderText}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${birthInfo}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${familyStatusText}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                            <button type="button" class="text-red-600 hover:text-red-900" onclick="removeFamilyMember(${index})">
                                Hapus
                            </button>
                        </td>
                    `;

                    familyMembersTableBody.appendChild(row);
                });
            }

            // Helper function to format date
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            // Make removeFamilyMember function available globally
            window.removeFamilyMember = function(index) {
                if (confirm('Apakah Anda yakin ingin menghapus anggota keluarga ini?')) {
                    // Remove from array
                    familyMembersArray.splice(index, 1);

                    // Update hidden input
                    familyMembersJson.value = JSON.stringify(familyMembersArray);

                    // Update table
                    updateFamilyMembersTable();

                    // Hide table if no members left
                    if (familyMembersArray.length === 0) {
                        familyMembersTableContainer.style.display = 'none';
                    }
                }
            };

            // Add member to the list without submitting
            if (addToListBtn) {
                addToListBtn.addEventListener('click', function() {
                    // Validate essential fields
                    const nik = document.getElementById('nik').value;
                    const fullName = document.getElementById('member_full_name').value;
                    const gender = document.getElementById('gender').value;
                    const familyStatus = document.getElementById('family_status').value;
                    const kkNumber = document.getElementById('kk').value;

                    if (!kkNumber) {
                        alert('Silakan isi Nomor KK terlebih dahulu');
                        return;
                    }

                    if (!nik || !fullName || !gender || !familyStatus) {
                        alert('Harap isi semua field yang wajib (NIK, Nama Lengkap, Jenis Kelamin, Status Hubungan)');
                        return;
                    }

                    // Save KK data and ensure it's available for all family members
                    const kkData = saveKKDataToLocalStorage();

                    // Get all KK form data
                    const kkFormData = getKKFormData();

                    // Get family member specific data
                    const formData = {
                        nik: nik,
                        kk: kkNumber,
                        full_name: fullName,
                        gender: gender,
                        birth_date: document.getElementById('birth_date').value,
                        age: document.getElementById('age').value,
                        birth_place: document.getElementById('birth_place').value,
                        family_status: familyStatus,
                        religion: document.getElementById('religion').value,
                        education_status: document.getElementById('education_status').value,
                        job_type_id: document.getElementById('job_type_id').value,

                        // Include address fields from KK data
                        address: kkFormData.address,
                        postal_code: kkFormData.postal_code,
                        rt: kkFormData.rt,
                        rw: kkFormData.rw,
                        province_id: kkFormData.province_id,
                        district_id: kkFormData.district_id,
                        sub_district_id: kkFormData.sub_district_id,
                        village_id: kkFormData.village_id,
                        hamlet: kkFormData.hamlet,

                        // Birth details
                        birth_certificate: document.getElementById('birth_certificate').value,
                        birth_certificate_no: document.getElementById('birth_certificate_no').value,

                        // Marital details
                        marital_status: document.getElementById('marital_status').value,
                        marital_certificate: document.getElementById('marital_certificate').value,
                        marital_certificate_no: document.getElementById('marital_certificate_no').value,
                        marriage_date: document.getElementById('marriage_date').value,

                        // Divorce details
                        divorce_certificate: document.getElementById('divorce_certificate').value,
                        divorce_certificate_no: document.getElementById('divorce_certificate_no').value,
                        divorce_certificate_date: document.getElementById('divorce_certificate_date').value,

                        // Other fields
                        blood_type: document.getElementById('blood_type').value,
                        mental_disorders: document.getElementById('mental_disorders').value,
                        disabilities: document.getElementById('disabilities').value,
                        citizen_status: document.getElementById('citizen_status').value,
                        telephone: document.getElementById('telephone').value,
                        email: document.getElementById('email').value,
                        coordinate: document.getElementById('coordinate').value,
                        nik_mother: document.getElementById('nik_mother').value,
                        mother: document.getElementById('mother').value,
                        nik_father: document.getElementById('nik_father').value,
                        father: document.getElementById('father').value,
                        status: document.getElementById('status').value,

                        // Foreign address (copied from KK data)
                        foreign_address: kkFormData.foreign_address,
                        city: kkFormData.city,
                        state: kkFormData.state,
                        country: kkFormData.country,
                        foreign_postal_code: kkFormData.foreign_postal_code,
                    };

                    // Add to array and update JSON
                    familyMembersArray.push(formData);
                    familyMembersJson.value = JSON.stringify(familyMembersArray);

                    // Update UI table
                    updateFamilyMembersTable();

                    // Show the table if it was hidden
                    familyMembersTableContainer.style.display = 'block';

                    // Clear family member form for next entry (but keep KK data)
                    resetFamilyMemberForm();

                    // Replace alert with SweetAlert for better user experience
                    showSuccessAlert(`Anggota keluarga "${fullName}" telah ditambahkan ke daftar. Total: ${familyMembersArray.length} anggota.`);
                });
            }

            // ===== FUNGSI UTAMA UNTUK MENGELOLA DATA KK =====

            // 1. Fungsi untuk menyimpan data KK di localStorage
            function saveKKDataToLocalStorage() {
                const kkData = {
                    kk: kkInput.value,
                    address: document.getElementById('address').value,
                    postal_code: document.getElementById('postal_code').value,
                    rt: document.getElementById('rt').value,
                    rw: document.getElementById('rw').value,
                    province_id: document.getElementById('province_id').value,
                    district_id: document.getElementById('district_id').value,
                    sub_district_id: document.getElementById('sub_district_id').value,
                    village_id: document.getElementById('village_id').value,
                    dusun: document.getElementById('dusun').value,

                    // Data alamat luar negeri
                    foreign_address: document.getElementById('foreign_address').value,
                    city: document.getElementById('city').value,
                    state: document.getElementById('state').value,
                    country: document.getElementById('country').value,
                    foreign_postal_code: document.getElementById('foreign_postal_code').value
                };

                localStorage.setItem('kkDetailData', JSON.stringify(kkData));

                return kkData;
            }

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

            // 3. Add form submit handler to ensure current family member data is included
            if (addFamilyMemberForm) {
                addFamilyMemberForm.addEventListener('submit', function(e) {
                    // Prevent default submission (we'll submit manually if validation passes)
                    e.preventDefault();

                    // Check if there's data in the current form that hasn't been added to the list
                    const currentNik = document.getElementById('nik').value;
                    const currentFullName = document.getElementById('member_full_name').value;

                    // If there's data in the form, add it to the list automatically
                    if (currentNik && currentFullName) {
                        // Get KK form data
                        const kkFormData = getKKFormData();

                        // Check if the KK number is filled out
                        if (!kkFormData.kk) {
                            alert('Silakan isi Nomor KK terlebih dahulu');
                            return;
                        }

                        // Check other required fields
                        const gender = document.getElementById('gender').value;
                        const familyStatus = document.getElementById('family_status').value;
                        const birthDate = document.getElementById('birth_date').value;
                        const age = document.getElementById('age').value;
                        const birthPlace = document.getElementById('birth_place').value;
                        const religion = document.getElementById('religion').value;
                        const jobTypeId = document.getElementById('job_type_id').value;

                        if (!gender || !familyStatus || !birthDate || !age || !birthPlace || !religion || !jobTypeId) {
                            if (confirm('Ada data yang belum lengkap pada form anggota yang sedang diisi. Lengkapi dulu?')) {
                                return; // Stop submission to allow user to complete the form
                            }
                        }

                        // Create member data object (same as in addToListBtn handler)
                        const memberData = {
                            nik: currentNik,
                            kk: kkFormData.kk,
                            full_name: currentFullName,
                            gender: gender || '',
                            birth_date: birthDate || '',
                            age: age || '',
                            birth_place: birthPlace || '',
                            family_status: familyStatus || '',
                            religion: religion || '',
                            education_status: document.getElementById('education_status').value || '',
                            job_type_id: jobTypeId || '',

                            // Include KK data for all members
                            address: kkFormData.address,
                            postal_code: kkFormData.postal_code,
                            rt: kkFormData.rt,
                            rw: kkFormData.rw,
                            province_id: kkFormData.province_id,
                            district_id: kkFormData.district_id,
                            sub_district_id: kkFormData.sub_district_id,
                            village_id: kkFormData.village_id,
                            hamlet: kkFormData.hamlet,

                            // Birth details
                            birth_certificate: document.getElementById('birth_certificate').value || '',
                            birth_certificate_no: document.getElementById('birth_certificate_no').value || '',

                            // Marital details
                            marital_status: document.getElementById('marital_status').value || '',
                            marital_certificate: document.getElementById('marital_certificate').value || '',
                            marital_certificate_no: document.getElementById('marital_certificate_no').value || '',
                            marriage_date: document.getElementById('marriage_date').value || '',

                            // Divorce details
                            divorce_certificate: document.getElementById('divorce_certificate').value || '',
                            divorce_certificate_no: document.getElementById('divorce_certificate_no').value || '',
                            divorce_certificate_date: document.getElementById('divorce_certificate_date').value || '',

                            // Other fields
                            blood_type: document.getElementById('blood_type').value || '',
                            mental_disorders: document.getElementById('mental_disorders').value || '',
                            disabilities: document.getElementById('disabilities').value || '',
                            citizen_status: document.getElementById('citizen_status').value || '',
                            telephone: document.getElementById('telephone').value || '',
                            email: document.getElementById('email').value || '',
                            coordinate: document.getElementById('coordinate').value || '',
                            nik_mother: document.getElementById('nik_mother').value || '',
                            mother: document.getElementById('mother').value || '',
                            nik_father: document.getElementById('nik_father').value || '',
                            father: document.getElementById('father').value || '',
                            status: document.getElementById('status').value || '',

                            // Foreign address data
                            foreign_address: kkFormData.foreign_address,
                            city: kkFormData.city,
                            state: kkFormData.state,
                            country: kkFormData.country,
                            foreign_postal_code: kkFormData.foreign_postal_code,
                        };

                        // Check if this NIK is already in the array to avoid duplicates
                        const existingMemberIndex = familyMembersArray.findIndex(member => member.nik === currentNik);
                        if (existingMemberIndex >= 0) {
                            // Update existing entry
                            familyMembersArray[existingMemberIndex] = memberData;
                        } else {
                            // Add to array
                            familyMembersArray.push(memberData);
                        }

                        // Update the hidden input
                        familyMembersJson.value = JSON.stringify(familyMembersArray);
                    }

                    // Check if we have any family members to submit
                    if (familyMembersArray.length === 0) {
                        alert('Tidak ada anggota keluarga yang akan disimpan. Silakan tambahkan anggota terlebih dahulu.');
                        return;
                    }

                    // Update all family members with the latest KK data
                    const kkFormData = getKKFormData();
                    familyMembersArray = familyMembersArray.map(member => {
                        return {
                            ...member,
                            kk: kkFormData.kk,
                            address: kkFormData.address,
                            postal_code: kkFormData.postal_code,
                            rt: kkFormData.rt,
                            rw: kkFormData.rw,
                            province_id: kkFormData.province_id,
                            district_id: kkFormData.district_id,
                            sub_district_id: kkFormData.sub_district_id,
                            village_id: kkFormData.village_id,
                            hamlet: kkFormData.hamlet,
                            foreign_address: kkFormData.foreign_address,
                            city: kkFormData.city,
                            state: kkFormData.state,
                            country: kkFormData.country,
                            foreign_postal_code: kkFormData.foreign_postal_code
                        };
                    });

                    // Update the hidden input with the latest data
                    familyMembersJson.value = JSON.stringify(familyMembersArray);

                    // Submit the form
                    addFamilyMemberForm.submit();
                });
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
