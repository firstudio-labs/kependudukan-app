<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Biodata</h1>

        <form method="POST" action="{{ route('superadmin.biodata.update', $citizen['data']['nik']) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_page" value="{{ request()->query('page', 1) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK (readonly) -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" id="nik" value="{{ $citizen['data']['nik'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 bg-gray-100" readonly>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" value="{{ $citizen['data']['kk'] }}" pattern="\d{16}" maxlength="16" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="{{ $citizen['data']['full_name'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1" {{ $citizen['data']['gender'] == 1 ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="2" {{ $citizen['data']['gender'] == 2 ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Example of a date field -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ $citizen['data']['birth_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Example of a select field -->


                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                    <input type="number" id="age" name="age" value="{{ $citizen['data']['age'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ $citizen['data']['birth_place'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $citizen['data']['address'] }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_id" name="province_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['id'] }}" data-code="{{ $province['code'] }}" {{ $citizen['data']['province_id'] == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten </label>
                    <select id="district_id" name="district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['id'] }}" data-code="{{ $district['code'] }}" {{ $citizen['data']['district_id'] == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan </label>
                    <select id="sub_district_id" name="sub_district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan </option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['id'] }}" data-code="{{ $subDistrict['code'] }}" {{ $citizen['data']['sub_district_id'] == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa</label>
                    <select id="village_id" name="village_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['id'] }}" data-code="{{ $village['code'] }}" {{ $citizen['data']['village_id'] == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" value="{{ $citizen['data']['rt'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" value="{{ $citizen['data']['rw'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code"
                        value="{{ $citizen['data']['postal_code'] && $citizen['data']['postal_code'] != '0' ? $citizen['data']['postal_code'] : '' }}"
                        pattern="\d{5}" maxlength="5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1" {{ $citizen['data']['citizen_status'] == 1 ? 'selected' : '' }}>WNI</option>
                        <option value="2" {{ $citizen['data']['citizen_status'] == 2 ? 'selected' : '' }}>WNA</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['birth_certificate'] == 1 ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['birth_certificate'] == 2 ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" value="{{ $citizen['data']['birth_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select id="blood_type" name="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1" {{ $citizen['data']['blood_type'] == 1 ? 'selected' : '' }}>A</option>
                        <option value="2" {{ $citizen['data']['blood_type'] == 2 ? 'selected' : '' }}>B</option>
                        <option value="3" {{ $citizen['data']['blood_type'] == 3 ? 'selected' : '' }}>AB</option>
                        <option value="4" {{ $citizen['data']['blood_type'] == 4 ? 'selected' : '' }}>O</option>
                        <option value="5" {{ $citizen['data']['blood_type'] == 5 ? 'selected' : '' }}>A+</option>
                        <option value="6" {{ $citizen['data']['blood_type'] == 6 ? 'selected' : '' }}>A-</option>
                        <option value="7" {{ $citizen['data']['blood_type'] == 7 ? 'selected' : '' }}>B+</option>
                        <option value="8" {{ $citizen['data']['blood_type'] == 8 ? 'selected' : '' }}>B-</option>
                        <option value="9" {{ $citizen['data']['blood_type'] == 9 ? 'selected' : '' }}>AB+</option>
                        <option value="10" {{ $citizen['data']['blood_type'] == 10 ? 'selected' : '' }}>AB-</option>
                        <option value="11" {{ $citizen['data']['blood_type'] == 11 ? 'selected' : '' }}>O+</option>
                        <option value="12" {{ $citizen['data']['blood_type'] == 12 ? 'selected' : '' }}>O-</option>
                        <option value="13" {{ $citizen['data']['blood_type'] == 13 ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1" {{ $citizen['data']['religion'] == 1 ? 'selected' : '' }}>Islam</option>
                        <option value="2" {{ $citizen['data']['religion'] == 2 ? 'selected' : '' }}>Kristen</option>
                        <option value="3" {{ $citizen['data']['religion'] == 3 ? 'selected' : '' }}>Katholik</option>
                        <option value="4" {{ $citizen['data']['religion'] == 4 ? 'selected' : '' }}>Hindu</option>
                        <option value="5" {{ $citizen['data']['religion'] == 5 ? 'selected' : '' }}>Buddha</option>
                        <option value="6" {{ $citizen['data']['religion'] == 6 ? 'selected' : '' }}>Kong Hu Cu</option>
                        <option value="7" {{ $citizen['data']['religion'] == 7 ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select id="marital_status" name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['marital_status'] == 1 ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="2" {{ $citizen['data']['marital_status'] == 2 ? 'selected' : '' }}>Kawin Tercatat</option>
                        <option value="3" {{ $citizen['data']['marital_status'] == 3 ? 'selected' : '' }}>Kawin Belum Tercatat</option>
                        <option value="4" {{ $citizen['data']['marital_status'] == 4 ? 'selected' : '' }}>Cerai Hidup Tercatat</option>
                        <option value="5" {{ $citizen['data']['marital_status'] == 5 ? 'selected' : '' }}>Cerai Hidup Belum Tercatat</option>
                        <option value="6" {{ $citizen['data']['marital_status'] == 6 ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                <!-- Akta Perkawinan -->
                <div>
                    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Perkawinan</label>
                    <select id="marital_certificate" name="marital_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['marital_certificate'] == 1 ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['marital_certificate'] == 2 ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perkawinan -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perkawinan</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no" value="{{ $citizen['data']['marital_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perkawinan -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Perkawinan</label>
                    <input type="date" id="marriage_date" name="marriage_date" value="{{ $citizen['data']['marriage_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
                <div>
                    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
                    <select id="divorce_certificate" name="divorce_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['divorce_certificate'] == 1 ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['divorce_certificate'] == 2 ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perceraian -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perceraian</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no" value="{{ $citizen['data']['divorce_certificate_no'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perceraian -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Perceraian</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date" value="{{ $citizen['data']['divorce_certificate_date'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>


                <!-- Status Hubungan Dalam Keluarga -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam Keluarga</label>
                    <select id="family_status" name="family_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="1" {{ $citizen['data']['family_status'] == 1 ? 'selected' : '' }}>ANAK</option>
                        <option value="2" {{ $citizen['data']['family_status'] == 2 ? 'selected' : '' }}>KEPALA KELUARGA</option>
                        <option value="3" {{ $citizen['data']['family_status'] == 3 ? 'selected' : '' }}>ISTRI</option>
                        <option value="4" {{ $citizen['data']['family_status'] == 4 ? 'selected' : '' }}>ORANG TUA</option>
                        <option value="5" {{ $citizen['data']['family_status'] == 5 ? 'selected' : '' }}>MERTUA</option>
                        <option value="6" {{ $citizen['data']['family_status'] == 6 ? 'selected' : '' }}>CUCU</option>
                        <option value="7" {{ $citizen['data']['family_status'] == 7 ? 'selected' : '' }}>FAMILI LAIN</option>
                    </select>
                </div>

                <!-- Kelainan Fisik dan Mental -->
                <div>
                    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan Mental</label>
                    <select id="mental_disorders" name="mental_disorders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['mental_disorders'] == 1 ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $citizen['data']['mental_disorders'] == 2 ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['disabilities'] == 1 ? 'selected' : '' }}>Fisik</option>
                        <option value="2" {{ $citizen['data']['disabilities'] == 2 ? 'selected' : '' }}>Netra/Buta</option>
                        <option value="3" {{ $citizen['data']['disabilities'] == 3 ? 'selected' : '' }}>Rungu/Wicara</option>
                        <option value="4" {{ $citizen['data']['disabilities'] == 4 ? 'selected' : '' }}>Mental/Jiwa</option>
                        <option value="5" {{ $citizen['data']['disabilities'] == 5 ? 'selected' : '' }}>Fisik dan Mental</option>
                        <option value="6" {{ $citizen['data']['disabilities'] == 6 ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                    <select id="education_status" name="education_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $citizen['data']['education_status'] == 1 ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                        <option value="2" {{ $citizen['data']['education_status'] == 2 ? 'selected' : '' }}>Belum tamat SD/Sederajat</option>
                        <option value="3" {{ $citizen['data']['education_status'] == 3 ? 'selected' : '' }}>Tamat SD</option>
                        <option value="4" {{ $citizen['data']['education_status'] == 4 ? 'selected' : '' }}>SLTP/SMP/Sederajat</option>
                        <option value="5" {{ $citizen['data']['education_status'] == 5 ? 'selected' : '' }}>SLTA/SMA/Sederajat</option>
                        <option value="6" {{ $citizen['data']['education_status'] == 6 ? 'selected' : '' }}>Diploma I/II</option>
                        <option value="7" {{ $citizen['data']['education_status'] == 7 ? 'selected' : '' }}>Akademi/Diploma III/ Sarjana Muda</option>
                        <option value="8" {{ $citizen['data']['education_status'] == 8 ? 'selected' : '' }}>Diploma IV/ Strata I/ Strata II</option>
                        <option value="9" {{ $citizen['data']['education_status'] == 9 ? 'selected' : '' }}>Strata III</option>
                        <option value="10" {{ $citizen['data']['education_status'] == 10 ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Jenis Pekerjaan -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Jenis Pekerjaan</label>
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Pekerjaan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ $citizen['data']['job_type_id'] == $job['id'] ? 'selected' : '' }}>
                                {{ $job['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- NIK Ibu -->
                <div>
                    <label for="nik_mother" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                    <input type="text" id="nik_mother" name="nik_mother" value="{{ $citizen['data']['nik_mother'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ibu -->
                <div>
                    <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input type="text" id="mother" name="mother" value="{{ $citizen['data']['mother'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Ayah -->
                <div>
                    <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <input type="text" id="nik_father" name="nik_father" value="{{ $citizen['data']['nik_father'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ayah -->
                <div>
                    <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input type="text" id="father" name="father" value="{{ $citizen['data']['father'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tag Lokasi -->
                <div>
                    <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log, Lat)</label>
                    <input type="text" id="coordinate" name="coordinate" value="{{ $citizen['data']['coordinate'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Update
                </button>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements
            const provinceSelect = document.getElementById('province_id');
            const districtSelect = document.getElementById('district_id');
            const subDistrictSelect = document.getElementById('sub_district_id');
            const villageSelect = document.getElementById('village_id');

            // API config
            const baseUrl = 'http://api-kependudukan.desaverse.id:3000/api';
            const apiKey = '{{ config('services.kependudukan.key') }}';

            // Use a single function for resetting selects with better performance
            function resetSelect(select, defaultText = 'Pilih') {
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
            }

            // Optimize select population
            function populateSelect(select, data, defaultText, selectedId = null) {
                const fragment = document.createDocumentFragment();
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = defaultText;
                fragment.appendChild(defaultOption);

                if (Array.isArray(data)) {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.dataset.code = item.code;
                        option.textContent = item.name;
                        if (selectedId && item.id == selectedId) {
                            option.selected = true;
                        }
                        fragment.appendChild(option);
                    });
                }

                select.innerHTML = '';
                select.appendChild(fragment);
                select.disabled = false;
            }

            // Use async/await for better handling of API calls
            async function fetchLocationData(endpoint) {
                try {
                    const cacheKey = `${baseUrl}/${endpoint}`;
                    const cachedData = sessionStorage.getItem(cacheKey);
                    if (cachedData) {
                        return JSON.parse(cachedData);
                    }

                    // If not in cache, fetch from server
                    const response = await axios.get(`${baseUrl}/${endpoint}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    });

                    // Store in sessionStorage for future use
                    if (response.data && response.data.data) {
                        sessionStorage.setItem(cacheKey, JSON.stringify(response.data.data));
                        return response.data.data;
                    }

                    return [];
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return [];
                }
            }

            // Initialize location dropdowns
            async function initializeLocations() {
                if (provinceSelect.value) {
                    const selectedProvinceOption = provinceSelect.options[provinceSelect.selectedIndex];
                    const provinceCode = selectedProvinceOption.getAttribute('data-code');
                    // Load districts (kabupaten)
                    const districts = await fetchLocationData(`districts/${provinceCode}`);
                    populateSelect(districtSelect, districts, 'Pilih Kabupaten', {{ $citizen['data']['district_id'] }});
                    // Get selected district code
                    const selectedDistrictOption = districtSelect.querySelector('option:checked');
                    if (selectedDistrictOption) {
                        const districtCode = selectedDistrictOption.getAttribute('data-code');
                        const subDistricts = await fetchLocationData(`sub-districts/${districtCode}`);
                        populateSelect(subDistrictSelect, subDistricts, 'Pilih Kecamatan', {{ $citizen['data']['sub_district_id'] }});
                        // Get selected subdistrict code
                        const selectedSubDistrictOption = subDistrictSelect.querySelector('option:checked');
                        if (selectedSubDistrictOption) {
                            const subDistrictCode = selectedSubDistrictOption.getAttribute('data-code');
                            const villages = await fetchLocationData(`villages/${subDistrictCode}`);
                            populateSelect(villageSelect, villages, 'Pilih Desa', {{ $citizen['data']['village_id'] }});
                        }
                    }
                }
            }

            // Initialize locations on page load
            initializeLocations();

            // Province change handler
            provinceSelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                const provinceCode = selectedOption.getAttribute('data-code');

                resetSelect(districtSelect, 'Loading...');
                resetSelect(subDistrictSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');

                if (provinceCode) {
                    const districts = await fetchLocationData(`districts/${provinceCode}`);
                    populateSelect(districtSelect, districts, 'Pilih Kabupaten');
                }
            });

            // District change handler
            districtSelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                const districtCode = selectedOption.getAttribute('data-code');

                resetSelect(subDistrictSelect, 'Loading...');
                resetSelect(villageSelect, 'Pilih Desa');

                if (districtCode) {
                    const subDistricts = await fetchLocationData(`sub-districts/${districtCode}`);
                    populateSelect(subDistrictSelect, subDistricts, 'Pilih Kecamatan');
                }
            });

            // Sub-district change handler
            subDistrictSelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                const subDistrictCode = selectedOption.getAttribute('data-code');

                resetSelect(villageSelect, 'Loading...');

                if (subDistrictCode) {
                    const villages = await fetchLocationData(`villages/${subDistrictCode}`);
                    populateSelect(villageSelect, villages, 'Pilih Desa');
                }
            });
        });
    </script>
</x-layout>
