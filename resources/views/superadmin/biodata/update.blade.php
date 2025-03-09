<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Update Biodata</h1>

        <form method="POST" action="{{ route('superadmin.biodata.update', $biodata->nik) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="number" id="nik" name="nik" value="{{ $biodata->nik }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <p class="text-sm text-gray-500">ID Unik</p>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="number" id="kk" name="kk" value="{{ $biodata->kk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="{{ $biodata->full_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-Laki" {{ $biodata->gender == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="Perempuan" {{ $biodata->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date"
                        value="{{ $biodata->birth_date ? \Carbon\Carbon::parse($biodata->birth_date)->format('Y-m-d') : '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                    <input type="number" id="age" name="age" value="{{ $biodata->age }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ $biodata->birth_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $biodata->address }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_id" name="province_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['id'] }}"
                                    data-code="{{ $province['code'] }}"
                                    {{ $biodata->province_id == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_id" name="district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="sub_district_id" name="sub_district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_id" name="village_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                    </select>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" value="{{ $biodata->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" value="{{ $biodata->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode POS</label>
                    <input type="number" id="postal_code" name="postal_code" autocomplete="postal-code" value="{{ $biodata->postal_code }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="WNI" {{ $biodata->citizen_status == 'WNI' ? 'selected' : '' }}>WNI</option>
                        <option value="WNA" {{ $biodata->citizen_status == 'WNA' ? 'selected' : '' }}>WNA</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="Ada" {{ $biodata->birth_certificate == 'Ada' ? 'selected' : '' }}>Ada</option>
                        <option value="Tidak Ada" {{ $biodata->birth_certificate == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" value="{{ $biodata->birth_certificate_no }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select id="blood_type" name="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Golongan Darah</option>
                        <option value="A" {{ $biodata->blood_type == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ $biodata->blood_type == 'B' ? 'selected' : '' }}>B</option>
                        <option value="AB" {{ $biodata->blood_type == 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="O" {{ $biodata->blood_type == 'O' ? 'selected' : '' }}>O</option>
                        <option value="A+" {{ $biodata->blood_type == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ $biodata->blood_type == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ $biodata->blood_type == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ $biodata->blood_type == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ $biodata->blood_type == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ $biodata->blood_type == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ $biodata->blood_type == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ $biodata->blood_type == 'O-' ? 'selected' : '' }}>O-</option>
                        <option value="Tidak Tahu" {{ $biodata->blood_type == 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ $biodata->religion == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ $biodata->religion == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katholik" {{ $biodata->religion == 'Katholik' ? 'selected' : '' }}>Katholik</option>
                        <option value="Hindu" {{ $biodata->religion == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ $biodata->religion == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Kong Hu Cu" {{ $biodata->religion == 'Kong Hu Cu' ? 'selected' : '' }}>Kong Hu Cu</option>
                        <option value="Lainya...." {{ $biodata->religion == 'Lainya....' ? 'selected' : '' }}>Lainnya....</option>
                    </select>
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select id="marital_status" name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="Belum Kawin" {{ $biodata->marital_status == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="Kawin Tercatat" {{ $biodata->marital_status == 'Kawin Tercatat' ? 'selected' : '' }}>Kawin Tercatat</option>
                        <option value="Kawin Belum Tercatat" {{ $biodata->marital_status == 'Kawin Belum Tercatat' ? 'selected' : '' }}>Kawin Belum Tercatat</option>
                        <option value="Cerai Hidup Tercatat" {{ $biodata->marital_status == 'Cerai Hidup Tercatat' ? 'selected' : '' }}>Cerai Hidup Tercatat</option>
                        <option value="Cerai Hidup Belum Tercatat" {{ $biodata->marital_status == 'Cerai Hidup Belum Tercatat' ? 'selected' : '' }}>Cerai Hidup Belum Tercatat</option>
                        <option value="Cerai Mati" {{ $biodata->marital_status == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                <!-- Akta Perkawinan -->
                <div>
                    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Perkawinan</label>
                    <select id="marital_certificate" name="marital_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="Ada" {{ $biodata->marital_certificate == 'Ada' ? 'selected' : '' }}>Ada</option>
                        <option value="Tidak Ada" {{ $biodata->marital_certificate == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perkawinan -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perkawinan</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no" value="{{ $biodata->marital_certificate_no }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perkawinan -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Perkawinan</label>
                    <input type="date" id="marriage_date" name="marriage_date" value="{{ $biodata->marriage_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $biodata->marriage_date)->format('Y-m-d') : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
                <div>
                    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
                    <select id="divorce_certificate" name="divorce_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="Ada" {{ $biodata->divorce_certificate == 'Ada' ? 'selected' : '' }}>Ada</option>
                        <option value="Tidak Ada" {{ $biodata->divorce_certificate == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Perceraian -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perceraian</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no" value="{{ $biodata->divorce_certificate_no }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perceraian -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Perceraian</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date" value="{{ $biodata->divorce_certificate_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $biodata->divorce_certificate_date)->format('Y-m-d') : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Jobs -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Jenis Pekerjaan</label>
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Pekerjaan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['id'] }}" {{ $biodata->job_type_id == $job['id'] ? 'selected' : '' }}>
                                {{ $job['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mental Disorders -->
                <div>
                    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan Mental</label>
                    <select id="mental_disorders" name="mental_disorders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="1" {{ $biodata->mental_disorders == '1' ? 'selected' : '' }}>Ada</option>
                        <option value="2" {{ $biodata->mental_disorders == '2' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>

                <!-- Family Status -->
                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam Keluarga</label>
                    <select id="family_status" name="family_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Status</option>
                        <option value="1" {{ (string)$biodata->family_status === '1' ? 'selected' : '' }}>KEPALA KELUARGA</option>
                        <option value="2" {{ (string)$biodata->family_status === '2' ? 'selected' : '' }}>ISTRI</option>
                        <option value="3" {{ (string)$biodata->family_status === '3' ? 'selected' : '' }}>ANAK</option>
                        <option value="4" {{ (string)$biodata->family_status === '4' ? 'selected' : '' }}>MERTUA</option>
                        <option value="5" {{ (string)$biodata->family_status === '5' ? 'selected' : '' }}>ORANG TUA</option>
                        <option value="6" {{ (string)$biodata->family_status === '6' ? 'selected' : '' }}>CUCU</option>
                        <option value="7" {{ (string)$biodata->family_status === '7' ? 'selected' : '' }}>FAMILI LAIN</option>
                        <option value="8" {{ (string)$biodata->family_status === '8' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>

                <!-- Disabilities -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <select id="disabilities" name="disabilities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1" {{ (string)$biodata->disabilities === '1' ? 'selected' : '' }}>Fisik</option>
                        <option value="2" {{ (string)$biodata->disabilities === '2' ? 'selected' : '' }}>Netra/Buta</option>
                        <option value="3" {{ (string)$biodata->disabilities === '3' ? 'selected' : '' }}>Rungu/Wicara</option>
                        <option value="4" {{ (string)$biodata->disabilities === '4' ? 'selected' : '' }}>Mental/Jiwa</option>
                        <option value="5" {{ (string)$biodata->disabilities === '5' ? 'selected' : '' }}>Fisik dan Mental</option>
                        <option value="6" {{ (string)$biodata->disabilities === '6' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Education Status -->
                <div>
                    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                    <select id="education_status" name="education_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Pendidikan</option>
                        <option value="1" {{ (string)$biodata->education_status === '1' ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                        <option value="2" {{ (string)$biodata->education_status === '2' ? 'selected' : '' }}>Belum tamat SD/Sederajat</option>
                        <option value="3" {{ (string)$biodata->education_status === '3' ? 'selected' : '' }}>Tamat SD</option>
                        <option value="4" {{ (string)$biodata->education_status === '4' ? 'selected' : '' }}>SLTP/SMP/Sederajat</option>
                        <option value="5" {{ (string)$biodata->education_status === '5' ? 'selected' : '' }}>SLTA/SMA/Sederajat</option>
                        <option value="6" {{ (string)$biodata->education_status === '6' ? 'selected' : '' }}>Diploma I/II</option>
                        <option value="7" {{ (string)$biodata->education_status === '7' ? 'selected' : '' }}>Akademi/Diploma III/Sarjana Muda</option>
                        <option value="8" {{ (string)$biodata->education_status === '8' ? 'selected' : '' }}>Diploma IV/Strata I/Strata II</option>
                        <option value="9" {{ (string)$biodata->education_status === '9' ? 'selected' : '' }}>Strata III</option>
                        <option value="10" {{ (string)$biodata->education_status === '10' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- NIK Mother -->
                <div>
                    <label for="nik_mother" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                    <input type="text" id="nik_mother" name="nik_mother" value="{{ $biodata->nik_mother }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Mother Name -->
                <div>
                    <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input type="text" id="mother" name="mother" value="{{ $biodata->mother }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Father -->
                <div>
                    <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <input type="text" id="nik_father" name="nik_father" value="{{ $biodata->nik_father }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Father Name -->
                <div>
                    <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input type="text" id="father" name="father" value="{{ $biodata->father }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Coordinate -->
                <div>
                    <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log, Lat)</label>
                    <input type="text" id="coordinate" name="coordinate" value="{{ $biodata->coordinate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="submit" class="w-full bg-indigo-500 text-white p-2 rounded-md shadow-md hover:bg-indigo-600">Update</button>
                <a href="{{ route('superadmin.biodata.index') }}" class="w-full bg-gray-500 text-white p-2 rounded-md shadow-md hover:bg-gray-600 text-center ml-4">Batal</a>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', async function() {
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const subDistrictSelect = document.getElementById('sub_district_id');
        const villageSelect = document.getElementById('village_id');

        // Store initial values
        const initialDistrict = "{{ $biodata->district_id }}";
        const initialSubDistrict = "{{ $biodata->sub_district_id }}";
        const initialVillage = "{{ $biodata->village_id }}";

        // Helper function for populating select
        function populateSelect(selectElement, data, placeholder, selectedValue = '') {
            selectElement.innerHTML = `<option value="">${placeholder}</option>`;
            if (Array.isArray(data)) {
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.setAttribute('data-code', item.code);
                    option.textContent = item.name;
                    if (item.id == selectedValue) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            }
            selectElement.disabled = false;
        }

        // Initial load of data
        async function loadInitialData() {
            if (provinceSelect.value) {
                const selectedProvince = provinceSelect.options[provinceSelect.selectedIndex];
                const provinceCode = selectedProvince.getAttribute('data-code');

                try {
                    // Load districts
                    const districtsResponse = await fetch(`/api/wilayah/provinsi/${provinceCode}/kota`);
                    const districts = await districtsResponse.json();
                    populateSelect(districtSelect, districts, 'Pilih Kabupaten', initialDistrict);

                    if (initialDistrict) {
                        // Get selected district's code
                        const selectedDistrict = districtSelect.options[districtSelect.selectedIndex];
                        const districtCode = selectedDistrict.getAttribute('data-code');

                        // Load subdistricts using the code
                        const subDistrictsResponse = await fetch(`/api/wilayah/kota/${districtCode}/kecamatan`);
                        const subDistricts = await subDistrictsResponse.json();
                        populateSelect(subDistrictSelect, subDistricts, 'Pilih Kecamatan', initialSubDistrict);

                        if (initialSubDistrict) {
                            // Get selected subdistrict's code
                            const selectedSubDistrict = subDistrictSelect.options[subDistrictSelect.selectedIndex];
                            const subDistrictCode = selectedSubDistrict.getAttribute('data-code');

                            // Load villages using the code
                            const villagesResponse = await fetch(`/api/wilayah/kecamatan/${subDistrictCode}/kelurahan`);
                            const villages = await villagesResponse.json();
                            populateSelect(villageSelect, villages, 'Pilih Desa', initialVillage);
                        }
                    }
                } catch (error) {
                    console.error('Error loading data:', error);
                }
            }
        }

        // Load initial data
        await loadInitialData();

        // Event listeners
        provinceSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption.getAttribute('data-code');

            districtSelect.innerHTML = '<option value="">Loading...</option>';
            subDistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (provinceCode) {
                try {
                    const response = await fetch(`/api/wilayah/provinsi/${provinceCode}/kota`);
                    const districts = await response.json();
                    populateSelect(districtSelect, districts, 'Pilih Kabupaten');
                } catch (error) {
                    console.error('Error:', error);
                    districtSelect.innerHTML = '<option value="">Error loading data</option>';
                }
            }
        });

        districtSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const districtCode = selectedOption.getAttribute('data-code');

            subDistrictSelect.innerHTML = '<option value="">Loading...</option>';
            villageSelect.innerHTML = '<option value="">Pilih Desa</option>';

            if (districtCode) {
                try {
                    const response = await fetch(`/api/wilayah/kota/${districtCode}/kecamatan`);
                    const subDistricts = await response.json();
                    populateSelect(subDistrictSelect, subDistricts, 'Pilih Kecamatan');
                } catch (error) {
                    console.error('Error:', error);
                    subDistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                }
            }
        });

        subDistrictSelect.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const subDistrictCode = selectedOption.getAttribute('data-code');

            villageSelect.innerHTML = '<option value="">Loading...</option>';

            if (subDistrictCode) {
                try {
                    const response = await fetch(`/api/wilayah/kecamatan/${subDistrictCode}/kelurahan`);
                    const villages = await response.json();
                    populateSelect(villageSelect, villages, 'Pilih Desa');
                } catch (error) {
                    console.error('Error:', error);
                    villageSelect.innerHTML = '<option value="">Error loading data</option>';
                }
            }
        });
    });
    </script>
</x-layout>
