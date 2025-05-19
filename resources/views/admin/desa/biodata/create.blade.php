<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Input/Entry Biodata</h1>

        <form method="POST" action="{{ route('admin.desa.biodata.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" id="nik" name="nik" pattern="\d{16}" maxlength="16"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" pattern="\d{16}" maxlength="16"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Nama Lengkap - Fixed id and name to match label -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select id="gender" name="gender"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="1">Laki-Laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                    <input type="number" id="age" name="age"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                </div>

                <!-- Provinsi section - Modify to use code as value, but store ID as data attribute -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span
                            class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Provinsi</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">
                                {{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <!-- Hidden input to store the actual ID for database storage -->
                    <input type="hidden" id="province_id" name="province_id" value="">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span
                            class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <!-- Hidden input to store the actual ID for database storage -->
                    <input type="hidden" id="district_id" name="district_id" value="">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan <span
                            class="text-red-500">*</span></label>
                    <select id="sub_district_code" name="sub_district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <!-- Hidden input to store the actual ID for database storage -->
                    <input type="hidden" id="sub_district_id" name="sub_district_id" value="">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span
                            class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Desa</option>
                    </select>
                    <!-- Hidden input to store the actual ID for database storage -->
                    <input type="hidden" id="village_id" name="village_id" value="">
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" pattern="\d{5}" maxlength="5"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status"
                        class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                    <select id="citizen_status" name="citizen_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="1">WNA</option>
                        <option value="2">WNI</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta
                        Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select id="blood_type" name="blood_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
                        <option value="13">Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span
                            class="text-red-500">*</span></label>
                    <select id="religion" name="religion"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
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

                <!-- Status Perkawinan -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status
                        Perkawinan</label>
                    <select id="marital_status" name="marital_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Belum Kawin</option>
                        <option value="2">Kawin Tercatat</option>
                        <option value="3">Kawin Belum Tercatat</option>
                        <option value="4">Cerai Hidup Tercatat</option>
                        <option value="5">Cerai Hidup Belum Tercatat</option>
                        <option value="6">Cerai Mati</option>
                    </select>
                </div>

                <!-- Akta Perkawinan -->
                <div>
                    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta
                        Perkawinan</label>
                    <select id="marital_certificate" name="marital_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perkawinan -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta
                        Perkawinan</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perkawinan -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal
                        Perkawinan</label>
                    <input type="date" id="marriage_date" name="marriage_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
                <div>
                    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta
                        Cerai</label>
                    <select id="divorce_certificate" name="divorce_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perceraian -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta
                        Perceraian</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perceraian -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal
                        Perceraian</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Status Hubungan Dalam Keluarga -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam
                        Keluarga</label>
                    <select id="family_status" name="family_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
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

                <!-- Kelainan Fisik dan Mental -->
                <div>
                    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan
                        Mental</label>
                    <select id="mental_disorders" name="mental_disorders"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Fisik</option>
                        <option value="2">Netra/Buta</option>
                        <option value="3">Rungu/Wicara</option>
                        <option value="4">Mental/Jiwa</option>
                        <option value="5">Fisik dan Mental</option>
                        <option value="6">Lainnya</option>
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan
                        Terakhir</label>
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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Jenis Pekerjaan</option>
                        @forelse($jobs as $job)
                            <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                        @empty
                            <option value="">Tidak ada data pekerjaan</option>
                        @endforelse
                    </select>
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

                <!-- Tag Lokasi -->
                <div>
                    <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log,
                        Lat)</label>
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

                <!-- Hamlet/Dusun -->
                <div>
                    <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun</label>
                    <input type="text" id="hamlet" name="hamlet"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Foreign Address (for WNA) -->
                <div>
                    <label for="foreign_address" class="block text-sm font-medium text-gray-700">Alamat Luar
                        Negeri</label>
                    <textarea id="foreign_address" name="foreign_address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                </div>

                <!-- City (for WNA) -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Kota (Luar Negeri)</label>
                    <input type="text" id="city" name="city"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- State/Province (for WNA) -->
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">Provinsi/State (Luar
                        Negeri)</label>
                    <input type="text" id="state" name="state"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Country (for WNA) -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                    <input type="text" id="country" name="country"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Foreign Postal Code -->
                <div>
                    <label for="foreign_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos (Luar
                        Negeri)</label>
                    <input type="text" id="foreign_postal_code" name="foreign_postal_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="Active" selected>Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Deceased">Deceased</option>
                        <option value="Moved">Moved</option>
                    </select>
                </div>

                <div>
    <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
    <input type="number" id="rf_id_tag" name="rf_id_tag"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
</div>
            </div>
            <!-- RF ID Tag -->


            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- Add meta tag for base URL -->
    <meta name="base-url" content="{{ url('/') }}">

    <!-- Set flash messages as data attributes for SweetAlert -->

    <body data-success-message="{{ session('success') }}" data-error-message="{{ session('error') }}">

        <!-- Include JavaScript files -->
        <script src="{{ asset('js/biodata-common.js') }}"></script>
        <script src="{{ asset('js/biodata-create.js') }}"></script>
</x-layout>
