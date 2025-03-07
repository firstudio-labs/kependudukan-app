<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Update Biodata</h1>

        <!-- Form Update Biodata -->
        <form method="POST" action="{{ route('biodata.update', $biodata->id) }}" class="bg-white p-6 rounded-lg shadow-md">
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
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <input type="text" id="province_id" name="province_id" value="{{ $biodata->province_id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <input type="text" id="district_id" name="district_id" value="{{ $biodata->district_id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <input type="text" id="sub_district_id" name="sub_district_id" value="{{ $biodata->sub_district_id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa</label>
                    <input type="text" id="village_id" name="village_id" value="{{ $biodata->village_id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
            </div>

            <div class="mt-6 flex justify-between">
                <button type="submit" class="w-full bg-indigo-500 text-white p-2 rounded-md shadow-md hover:bg-indigo-600">Update</button>
                <a href="{{ route('superadmin.biodata.index') }}" class="w-full bg-gray-500 text-white p-2 rounded-md shadow-md hover:bg-gray-600 text-center ml-4">Batal</a>
            </div>
        </form>
    </div>
</x-layout>
