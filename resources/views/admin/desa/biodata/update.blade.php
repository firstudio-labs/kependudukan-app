<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Update Biodata</h1>

        <form method="POST" action="{{ route('admin.desa.biodata.update', $citizen['data']['nik']) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_page" value="{{ request('page', 1) }}">

            <!-- RF ID Tag -->
            <div class="mb-4">
                <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
                <input type="number" id="rf_id_tag" name="rf_id_tag" value="{{ $citizen['data']['rf_id_tag'] ?? '' }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" name="nik" value="{{ $citizen['data']['nik'] }}" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK <span class="text-red-500">*</span></label>
                    <input type="text" id="kk" name="kk" value="{{ $citizen['data']['kk'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ $citizen['data']['full_name'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="1" {{ $citizen['data']['gender'] == '1' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="2" {{ $citizen['data']['gender'] == '2' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ $citizen['data']['birth_place'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ $citizen['data']['birth_date'] }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur <span class="text-red-500">*</span></label>
                    <input type="number" id="age" name="age" value="{{ $citizen['data']['age'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                    <select id="religion" name="religion"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="1" {{ $citizen['data']['religion'] == '1' ? 'selected' : '' }}>Islam</option>
                        <option value="2" {{ $citizen['data']['religion'] == '2' ? 'selected' : '' }}>Kristen</option>
                        <option value="3" {{ $citizen['data']['religion'] == '3' ? 'selected' : '' }}>Katholik</option>
                        <option value="4" {{ $citizen['data']['religion'] == '4' ? 'selected' : '' }}>Hindu</option>
                        <option value="5" {{ $citizen['data']['religion'] == '5' ? 'selected' : '' }}>Buddha</option>
                        <option value="6" {{ $citizen['data']['religion'] == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                        <option value="7" {{ $citizen['data']['religion'] == '7' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['birth_certificate'] == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['birth_certificate'] == '2' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" value="{{ $citizen['data']['birth_certificate_no'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select id="marital_status" name="marital_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['marital_status'] == '1' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="2" {{ $citizen['data']['marital_status'] == '2' ? 'selected' : '' }}>Kawin Tercatat</option>
                        <option value="3" {{ $citizen['data']['marital_status'] == '3' ? 'selected' : '' }}>Kawin Belum Tercatat</option>
                        <option value="4" {{ $citizen['data']['marital_status'] == '4' ? 'selected' : '' }}>Cerai Hidup Tercatat</option>
                        <option value="5" {{ $citizen['data']['marital_status'] == '5' ? 'selected' : '' }}>Cerai Hidup Belum Tercatat</option>
                        <option value="6" {{ $citizen['data']['marital_status'] == '6' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                <!-- Akta Kawin -->
                <div>
                    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Kawin <span class="text-red-500">*</span></label>
                    <select id="marital_certificate" name="marital_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['marital_certificate'] == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['marital_certificate'] == '2' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Kawin -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Kawin</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no" value="{{ $citizen['data']['marital_certificate_no'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Kawin -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Kawin</label>
                    <input type="date" id="marriage_date" name="marriage_date" value="{{ $citizen['data']['marriage_date'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
                <div>
                    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
                    <select id="divorce_certificate" name="divorce_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['divorce_certificate'] == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['divorce_certificate'] == '2' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Cerai -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Cerai</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no" value="{{ $citizen['data']['divorce_certificate_no'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Akta Cerai -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Akta Cerai</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date" value="{{ $citizen['data']['divorce_certificate_date'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah <span class="text-red-500">*</span></label>
                    <select id="blood_type" name="blood_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="1" {{ $citizen['data']['blood_type'] == '1' ? 'selected' : '' }}>A</option>
                        <option value="2" {{ $citizen['data']['blood_type'] == '2' ? 'selected' : '' }}>B</option>
                        <option value="3" {{ $citizen['data']['blood_type'] == '3' ? 'selected' : '' }}>AB</option>
                        <option value="4" {{ $citizen['data']['blood_type'] == '4' ? 'selected' : '' }}>O</option>
                        <option value="5" {{ $citizen['data']['blood_type'] == '5' ? 'selected' : '' }}>A+</option>
                        <option value="6" {{ $citizen['data']['blood_type'] == '6' ? 'selected' : '' }}>A-</option>
                        <option value="7" {{ $citizen['data']['blood_type'] == '7' ? 'selected' : '' }}>B+</option>
                        <option value="8" {{ $citizen['data']['blood_type'] == '8' ? 'selected' : '' }}>B-</option>
                        <option value="9" {{ $citizen['data']['blood_type'] == '9' ? 'selected' : '' }}>AB+</option>
                        <option value="10" {{ $citizen['data']['blood_type'] == '10' ? 'selected' : '' }}>AB-</option>
                        <option value="11" {{ $citizen['data']['blood_type'] == '11' ? 'selected' : '' }}>O+</option>
                        <option value="12" {{ $citizen['data']['blood_type'] == '12' ? 'selected' : '' }}>O-</option>
                        <option value="13" {{ $citizen['data']['blood_type'] == '13' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                    <select id="citizen_status" name="citizen_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="1" {{ $citizen['data']['citizen_status'] == '1' ? 'selected' : '' }}>WNA</option>
                        <option value="2" {{ $citizen['data']['citizen_status'] == '2' ? 'selected' : '' }}>WNI</option>
                    </select>
                </div>

                <!-- Status Dalam Keluarga -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Dalam Keluarga <span class="text-red-500">*</span></label>
                    <select id="family_status" name="family_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['family_status'] == '1' ? 'selected' : '' }}>Anak</option>
                        <option value="2" {{ $citizen['data']['family_status'] == '2' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="3" {{ $citizen['data']['family_status'] == '3' ? 'selected' : '' }}>Istri</option>
                        <option value="4" {{ $citizen['data']['family_status'] == '4' ? 'selected' : '' }}>Orang Tua</option>
                        <option value="5" {{ $citizen['data']['family_status'] == '5' ? 'selected' : '' }}>Mertua</option>
                        <option value="6" {{ $citizen['data']['family_status'] == '6' ? 'selected' : '' }}>Cucu</option>
                        <option value="7" {{ $citizen['data']['family_status'] == '7' ? 'selected' : '' }}>Famili Lain</option>
                    </select>
                </div>

                <!-- Kelainan Fisik & Mental -->
                <div>
                    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik & Mental <span class="text-red-500">*</span></label>
                    <select id="mental_disorders" name="mental_disorders"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Status</option>
                        <option value="1" {{ $citizen['data']['mental_disorders'] == '1' ? 'selected' : '' }}>Ya</option>
                        <option value="2" {{ $citizen['data']['mental_disorders'] == '2' ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="0" {{ $citizen['data']['disabilities'] == '0' ? 'selected' : '' }}>Tidak Ada</option>
                        <option value="1" {{ $citizen['data']['disabilities'] == '1' ? 'selected' : '' }}>Fisik</option>
                        <option value="2" {{ $citizen['data']['disabilities'] == '2' ? 'selected' : '' }}>Netra/Buta</option>
                        <option value="3" {{ $citizen['data']['disabilities'] == '3' ? 'selected' : '' }}>Rungu/Wicara</option>
                        <option value="4" {{ $citizen['data']['disabilities'] == '4' ? 'selected' : '' }}>Mental/Jiwa</option>
                        <option value="5" {{ $citizen['data']['disabilities'] == '5' ? 'selected' : '' }}>Fisik dan Mental</option>
                        <option value="6" {{ $citizen['data']['disabilities'] == '6' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Pendidikan -->
                <div>
                    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan <span class="text-red-500">*</span></label>
                    <select id="education_status" name="education_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Pendidikan</option>
                        <option value="1" {{ $citizen['data']['education_status'] == '1' ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                        <option value="2" {{ $citizen['data']['education_status'] == '2' ? 'selected' : '' }}>Belum tamat SD/Sederajat</option>
                        <option value="3" {{ $citizen['data']['education_status'] == '3' ? 'selected' : '' }}>Tamat SD/Sederajat</option>
                        <option value="4" {{ $citizen['data']['education_status'] == '4' ? 'selected' : '' }}>SLTP/SMP/Sederajat</option>
                        <option value="5" {{ $citizen['data']['education_status'] == '5' ? 'selected' : '' }}>SLTA/SMA/Sederajat</option>
                        <option value="6" {{ $citizen['data']['education_status'] == '6' ? 'selected' : '' }}>Diploma I/II</option>
                        <option value="7" {{ $citizen['data']['education_status'] == '7' ? 'selected' : '' }}>Akademi/Diploma III/Sarjana Muda</option>
                        <option value="8" {{ $citizen['data']['education_status'] == '8' ? 'selected' : '' }}>Diploma IV/Strata I/Strata II</option>
                        <option value="9" {{ $citizen['data']['education_status'] == '9' ? 'selected' : '' }}>Strata III</option>
                        <option value="10" {{ $citizen['data']['education_status'] == '10' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Pekerjaan -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                    <select id="job_type_id" name="job_type_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Pekerjaan</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ $citizen['data']['job_type_id'] == $job['id'] ? 'selected' : '' }}>
                                {{ $job['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" autocomplete="off" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>{{ $citizen['data']['address'] }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                        <option value="">Pilih Provinsi</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $citizen['data']['province_id'] == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $citizen['data']['province_id'] }}">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $citizen['data']['district_id'] == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $citizen['data']['district_id'] }}">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="sub_district_code" name="sub_district_code" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $citizen['data']['sub_district_id'] == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="sub_district_id" name="sub_district_id" value="{{ $citizen['data']['sub_district_id'] }}">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $citizen['data']['village_id'] == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $citizen['data']['village_id'] }}">
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" value="{{ $citizen['data']['rt'] }}" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                    <input type="text" id="rw" name="rw" value="{{ $citizen['data']['rw'] }}" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100"
                        required>
                </div>

                <!-- Kode Pos -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" maxlength="5" disabled
                        value="{{ empty($citizen['data']['postal_code']) || $citizen['data']['postal_code'] == 0 ? '' : $citizen['data']['postal_code'] }}"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100">
                </div>

                <!-- Nama Ayah -->
                <div>
                    <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input type="text" id="father" name="father" value="{{ $citizen['data']['father'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Ayah -->
                <div>
                    <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <input type="text" id="nik_father" name="nik_father" maxlength="16" value="{{ $citizen['data']['nik_father'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ibu -->
                <div>
                    <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input type="text" id="mother" name="mother" value="{{ $citizen['data']['mother'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Ibu -->
                <div>
                    <label for="nik_mother" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                    <input type="text" id="nik_mother" name="nik_mother" maxlength="16" value="{{ $citizen['data']['nik_mother'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Telepon -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" id="telephone" name="telephone" value="{{ $citizen['data']['telephone'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ $citizen['data']['email'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Dusun -->
                <div>
                    <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun</label>
                    <input type="text" id="hamlet" name="hamlet" value="{{ $citizen['data']['hamlet'] ?? '' }}" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100">
                </div>

                <!-- Koordinat (Hidden) -->
                <input type="hidden" id="coordinate" name="coordinate" value="{{ $citizen['data']['coordinate'] ?? '' }}">

                <!-- Hidden fields untuk data yang tidak ditampilkan tapi tetap dikirim -->
                <input type="hidden" name="foreign_address" value="{{ $citizen['data']['foreign_address'] ?? '' }}">
                <input type="hidden" name="city" value="{{ $citizen['data']['city'] ?? '' }}">
                <input type="hidden" name="state" value="{{ $citizen['data']['state'] ?? '' }}">
                <input type="hidden" name="country" value="{{ $citizen['data']['country'] ?? '' }}">
                <input type="hidden" name="foreign_postal_code" value="{{ $citizen['data']['foreign_postal_code'] ?? '' }}">
                <input type="hidden" name="status" value="{{ $citizen['data']['status'] ?? 'Active' }}">
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Update
                </button>
            </div>
        </form>
    </div>

    <!-- Add meta tag for base URL -->
    <meta name="base-url" content="{{ url('/') }}">

    <!-- Pass citizen data to JavaScript -->
    <script>
        window.citizenData = @json($citizen['data']);
    </script>

    <!-- Include JavaScript files -->
    <script src="{{ asset('js/biodata-common.js') }}"></script>
    <script src="{{ asset('js/biodata-update.js') }}"></script>
</x-layout>
