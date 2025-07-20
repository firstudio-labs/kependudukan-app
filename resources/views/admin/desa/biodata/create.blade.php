<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Input/Entry Biodata</h1>

        <form method="POST" action="{{ route('admin.desa.biodata.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" name="nik" pattern="\d{16}" maxlength="16" value="{{ old('nik') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK <span class="text-red-500">*</span></label>
                    <input type="text" id="kk" name="kk" pattern="\d{16}" maxlength="16" value="{{ old('kk') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur <span class="text-red-500">*</span></label>
                    <input type="number" id="age" name="age" value="{{ old('age') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>{{ old('address') }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Provinsi</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ old('province_code') == $province['code'] ? 'selected' : '' }}>
                                {{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ old('province_id') }}">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id') }}">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="sub_district_code" name="sub_district_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" id="sub_district_id" name="sub_district_id" value="{{ old('sub_district_id') }}">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Desa</option>
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ old('village_id') }}">
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" value="{{ old('rt') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                    <input type="text" id="rw" name="rw" value="{{ old('rw') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" pattern="\d{5}" maxlength="5" value="{{ old('postal_code') }}"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                    <select id="citizen_status" name="citizen_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="1" {{ old('citizen_status') == '1' ? 'selected' : '' }}>WNA</option>
                        <option value="2" {{ old('citizen_status') == '2' ? 'selected' : '' }}>WNI</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ old('birth_certificate') == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ old('birth_certificate') == '2' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" value="{{ old('birth_certificate_no') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah <span class="text-red-500">*</span></label>
                    <select id="blood_type" name="blood_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Golongan Darah</option>
                        <option value="1" {{ old('blood_type') == '1' ? 'selected' : '' }}>A</option>
                        <option value="2" {{ old('blood_type') == '2' ? 'selected' : '' }}>B</option>
                        <option value="3" {{ old('blood_type') == '3' ? 'selected' : '' }}>AB</option>
                        <option value="4" {{ old('blood_type') == '4' ? 'selected' : '' }}>O</option>
                        <option value="5" {{ old('blood_type') == '5' ? 'selected' : '' }}>A+</option>
                        <option value="6" {{ old('blood_type') == '6' ? 'selected' : '' }}>A-</option>
                        <option value="7" {{ old('blood_type') == '7' ? 'selected' : '' }}>B+</option>
                        <option value="8" {{ old('blood_type') == '8' ? 'selected' : '' }}>B-</option>
                        <option value="9" {{ old('blood_type') == '9' ? 'selected' : '' }}>AB+</option>
                        <option value="10" {{ old('blood_type') == '10' ? 'selected' : '' }}>AB-</option>
                        <option value="11" {{ old('blood_type') == '11' ? 'selected' : '' }}>O+</option>
                        <option value="12" {{ old('blood_type') == '12' ? 'selected' : '' }}>O-</option>
                        <option value="13" {{ old('blood_type') == '13' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                    <select id="religion" name="religion"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Agama</option>
                        <option value="1" {{ old('religion') == '1' ? 'selected' : '' }}>Islam</option>
                        <option value="2" {{ old('religion') == '2' ? 'selected' : '' }}>Kristen</option>
                        <option value="3" {{ old('religion') == '3' ? 'selected' : '' }}>Katolik</option>
                        <option value="4" {{ old('religion') == '4' ? 'selected' : '' }}>Hindu</option>
                        <option value="5" {{ old('religion') == '5' ? 'selected' : '' }}>Buddha</option>
                        <option value="6" {{ old('religion') == '6' ? 'selected' : '' }}>Konghucu</option>
                        <option value="7" {{ old('religion') == '7' ? 'selected' : '' }}>Kepercayaan</option>
                    </select>
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Perkawinan <span class="text-red-500">*</span></label>
                    <select id="family_status" name="family_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Status Perkawinan</option>
                        <option value="1" {{ old('family_status') == '1' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="2" {{ old('family_status') == '2' ? 'selected' : '' }}>Kawin</option>
                        <option value="3" {{ old('family_status') == '3' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="4" {{ old('family_status') == '4' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                <!-- Gangguan Jiwa -->
                <div>
                    <label for="mental_disorder" class="block text-sm font-medium text-gray-700">Gangguan Jiwa <span class="text-red-500">*</span></label>
                    <select id="mental_disorder" name="mental_disorder"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Status</option>
                        <option value="1" {{ old('mental_disorder') == '1' ? 'selected' : '' }}>Ya</option>
                        <option value="2" {{ old('mental_disorder') == '2' ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="0" {{ old('disabilities') == '0' ? 'selected' : '' }}>Tidak Ada</option>
                        <option value="1" {{ old('disabilities') == '1' ? 'selected' : '' }}>Tuna Netra</option>
                        <option value="2" {{ old('disabilities') == '2' ? 'selected' : '' }}>Tuna Rungu</option>
                        <option value="3" {{ old('disabilities') == '3' ? 'selected' : '' }}>Tuna Grahita</option>
                        <option value="4" {{ old('disabilities') == '4' ? 'selected' : '' }}>Tuna Daksa</option>
                        <option value="5" {{ old('disabilities') == '5' ? 'selected' : '' }}>Tuna Laras</option>
                        <option value="6" {{ old('disabilities') == '6' ? 'selected' : '' }}>Tuna Wicara</option>
                    </select>
                </div>

                <!-- Pekerjaan -->
                <div>
                    <label for="job" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                    <select id="job" name="job"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Pekerjaan</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ old('job') == $job['id'] ? 'selected' : '' }}>
                                {{ $job['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pendidikan -->
                <div>
                    <label for="education" class="block text-sm font-medium text-gray-700">Pendidikan <span class="text-red-500">*</span></label>
                    <select id="education" name="education"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Pendidikan</option>
                        <option value="1" {{ old('education') == '1' ? 'selected' : '' }}>Tidak Sekolah</option>
                        <option value="2" {{ old('education') == '2' ? 'selected' : '' }}>SD</option>
                        <option value="3" {{ old('education') == '3' ? 'selected' : '' }}>SMP</option>
                        <option value="4" {{ old('education') == '4' ? 'selected' : '' }}>SMA</option>
                        <option value="5" {{ old('education') == '5' ? 'selected' : '' }}>D1</option>
                        <option value="6" {{ old('education') == '6' ? 'selected' : '' }}>D2</option>
                        <option value="7" {{ old('education') == '7' ? 'selected' : '' }}>D3</option>
                        <option value="8" {{ old('education') == '8' ? 'selected' : '' }}>D4</option>
                        <option value="9" {{ old('education') == '9' ? 'selected' : '' }}>S1</option>
                        <option value="10" {{ old('education') == '10' ? 'selected' : '' }}>S2</option>
                        <option value="11" {{ old('education') == '11' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>

                <!-- Penghasilan -->
                <div>
                    <label for="income" class="block text-sm font-medium text-gray-700">Penghasilan <span class="text-red-500">*</span></label>
                    <select id="income" name="income"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Penghasilan</option>
                        <option value="1" {{ old('income') == '1' ? 'selected' : '' }}>Kurang dari 500.000</option>
                        <option value="2" {{ old('income') == '2' ? 'selected' : '' }}>500.000 - 1.000.000</option>
                        <option value="3" {{ old('income') == '3' ? 'selected' : '' }}>1.000.000 - 2.000.000</option>
                        <option value="4" {{ old('income') == '4' ? 'selected' : '' }}>2.000.000 - 3.000.000</option>
                        <option value="5" {{ old('income') == '5' ? 'selected' : '' }}>3.000.000 - 5.000.000</option>
                        <option value="6" {{ old('income') == '6' ? 'selected' : '' }}>Lebih dari 5.000.000</option>
                    </select>
                </div>

                <!-- Asuransi -->
                <div>
                    <label for="insurance" class="block text-sm font-medium text-gray-700">Asuransi</label>
                    <select id="insurance" name="insurance"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ old('insurance') == '1' ? 'selected' : '' }}>Ya</option>
                        <option value="2" {{ old('insurance') == '2' ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <!-- No Asuransi -->
                <div>
                    <label for="insurance_no" class="block text-sm font-medium text-gray-700">No Asuransi</label>
                    <input type="text" id="insurance_no" name="insurance_no" value="{{ old('insurance_no') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-layout>
