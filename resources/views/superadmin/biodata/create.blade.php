<x-layout>
    <div class="p-4 mt-14">
        @if (session('success'))
    <div id="success-alert" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-white bg-green-500 rounded-lg shadow-lg transition-opacity duration-500">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.414V8a1 1 0 10-2 0v5.414l-.707-.707a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.707.707z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ session('success') }}</span>
        <button onclick="closeAlert()" class="ml-4 text-white focus:outline-none">
            ✖
        </button>
    </div>
@endif

@if ($errors->any())
    <div id="error-alert" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-white bg-red-500 rounded-lg shadow-lg transition-opacity duration-500">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.414V8a1 1 0 10-2 0v5.414l-.707-.707a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.707.707z" clip-rule="evenodd"></path>
        </svg>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button onclick="closeAlert()" class="ml-4 text-white focus:outline-none">
            ✖
        </button>
    </div>
@endif
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Input/Entry Biodata</h1>

        <!-- Form Tambah Biodata -->
        <form method="POST" action="{{ route('biodata.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" id="nik" name="nik" pattern="\d{16}" maxlength="16" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- No KK -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">No KK</label>
                    <input type="text" id="kk" name="kk" pattern="\d{16}" maxlength="16" class="..." required>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
<div>
    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
        <option value="">Pilih Jenis Kelamin</option>
        <option value="Laki-Laki">Laki-Laki</option>
        <option value="Perempuan">Perempuan</option>
    </select>
</div>


                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700">Umur</label>
                    <input type="number" id="age" name="age" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
<textarea id="address" name="address" autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>

                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi</label>
                    <select id="province_id" name="province_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                    <select id="district_id" name="district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Kabupaten</option>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                    <select id="sub_district_id" name="sub_district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>

                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa</label>
                    <select id="village_id" name="village_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Desa</option>
                    </select>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Kode POS -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                    <input
                        type="text"
                        id="postal_code"
                        name="postal_code"
                        pattern="\d{5}"
                        maxlength="5"
                        autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        placeholder="Masukkan Kode Pos"
                    >
                </div>

                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan</label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="WNI">WNI</option>
                        <option value="WNA">WNA</option>
                    </select>
                </div>

                <!-- Akta Lahir -->
                <div>
                    <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Akta Lahir</label>
                    <select id="birth_certificate" name="birth_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Status</option>
                        <option value="1">Ada</option>
                        <option value="2">Tidak Ada</option>
                    </select>
                </div>

                <!-- No Akta Lahir -->
                <div>
                    <label for="birth_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Lahir</label>
                    <input type="text" id="birth_certificate_no" name="birth_certificate_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

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
                        <option value="13">Tidak Tahu</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
                    <label for="marital_status" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select id="marital_status" name="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
    <label for="marital_certificate" class="block text-sm font-medium text-gray-700">Akta Perkawinan</label>
    <select id="marital_certificate" name="marital_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
        <option value="">Pilih Status</option>
        <option value="1">Ada</option>
        <option value="2">Tidak Ada</option>
    </select>
</div>

                <!-- No Akta Perkawinan -->
                <div>
                    <label for="marital_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perkawinan</label>
                    <input type="text" id="marital_certificate_no" name="marital_certificate_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perkawinan -->
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Perkawinan</label>
                    <input type="date" id="marriage_date" name="marriage_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Akta Cerai -->
<div>
    <label for="divorce_certificate" class="block text-sm font-medium text-gray-700">Akta Cerai</label>
    <select id="divorce_certificate" name="divorce_certificate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
        <option value="">Pilih Status</option>
        <option value="1">Ada</option>
        <option value="2">Tidak Ada</option>
    </select>
</div>

                <!-- No Akta Perceraian -->
                <div>
                    <label for="divorce_certificate_no" class="block text-sm font-medium text-gray-700">No Akta Perceraian</label>
                    <input type="text" id="divorce_certificate_no" name="divorce_certificate_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tanggal Perceraian -->
                <div>
                    <label for="divorce_certificate_date" class="block text-sm font-medium text-gray-700">Tanggal Perceraian</label>
                    <input type="date" id="divorce_certificate_date" name="divorce_certificate_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Status Hubungan Dalam Keluarga -->
<div>
    <label for="family_status" class="block text-sm font-medium text-gray-700">Status Hubungan Dalam Keluarga</label>
    <select id="family_status" name="family_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
        <option value="">Pilih Status</option>
        <option value="KEPALA KELUARGA">KEPALA KELUARGA</option>
        <option value="ISTRI">ISTRI</option>
        <option value="ANAK">ANAK</option>
        <option value="MERTUA">MERTUA</option>
        <option value="ORANG TUA">ORANG TUA</option>
        <option value="CUCU">CUCU</option>
        <option value="FAMILI LAIN">FAMILI LAIN</option>
        <option value="LAINNYA">LAINNYA</option>
    </select>
</div>

<!-- Kelainan Fisik dan Mental -->
<div>
    <label for="mental_disorders" class="block text-sm font-medium text-gray-700">Kelainan Fisik dan Mental</label>
    <select id="mental_disorders" name="mental_disorders" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
        <option value="1">Tidak Ada</option>
        <option value="2">Ada</option>
    </select>
</div>

                <!-- Penyandang Cacat -->
                <div>
                    <label for="disabilities" class="block text-sm font-medium text-gray-700">Penyandang Cacat</label>
                    <input type="text" id="disabilities" name="disabilities" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pendidikan Terakhir -->
<div>
    <label for="education_status" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
    <select id="education_status" name="education_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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
                    <input type="text" id="nik_mother" name="nik_mother" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ibu -->
                <div>
                    <label for="mother" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input type="text" id="mother" name="mother" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- NIK Ayah -->
                <div>
                    <label for="nik_father" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <input type="text" id="nik_father" name="nik_father" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Nama Ayah -->
                <div>
                    <label for="father" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input type="text" id="father" name="father" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Tag Lokasi -->
                <div>
                    <label for="coordinate" class="block text-sm font-medium text-gray-700">Tag Lokasi (Log, Lat)</label>
                    <input type="text" id="coordinate" name="coordinate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">Simpan</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Fungsi untuk menutup alert
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        // Menutup alert secara otomatis setelah 5 detik
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                successAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => successAlert.classList.add('hidden'), 1000);
            }

            if (errorAlert) {
                errorAlert.classList.add('opacity-0', 'transition-opacity', 'duration-1000');
                setTimeout(() => errorAlert.classList.add('hidden'), 1000);
            }
        }, 5000);

        // Replace existing script with this new one
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const districtSelect = document.getElementById('district_id');
            const subDistrictSelect = document.getElementById('sub_district_id');
            const villageSelect = document.getElementById('village_id');

            // Helper function to reset select options
            function resetSelect(select, defaultText = 'Pilih') {
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
            }

            // Helper function to populate select options with error handling
            function populateSelect(select, data, defaultText) {
                try {
                    select.innerHTML = `<option value="">${defaultText}</option>`;
                    if (Array.isArray(data)) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.code; // Menggunakan code sebagai value
                            option.textContent = item.name;
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Invalid data format:', data);
                    }
                    select.disabled = false;
                } catch (error) {
                    console.error('Error populating select:', error);
                    resetSelect(select, 'Error loading data');
                }
            }

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                const provinceCode = this.value;
                resetSelect(districtSelect, 'Loading...');
                resetSelect(subDistrictSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');

                if (provinceCode) {
                    console.log('Selected province code:', provinceCode); // Debug
                    axios.get(`/api/wilayah/provinsi/${provinceCode}/kota`)
                        .then(response => {
                            console.log('API Response:', response.data); // Debug
                            if (response.data) {
                                populateSelect(districtSelect, response.data, 'Pilih Kabupaten');
                            } else {
                                resetSelect(districtSelect, 'No data available');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error.response?.data || error.message);
                            resetSelect(districtSelect, 'Error loading data');
                        });
                }
            });

            // District change handler with similar error handling
            districtSelect.addEventListener('change', function() {
                const districtCode = this.value; // Sekarang ini akan berisi code, bukan id
                resetSelect(subDistrictSelect, 'Loading...');
                resetSelect(villageSelect, 'Pilih Desa');

                if (districtCode) {
                    console.log('Selected district code:', districtCode); // Debug
                    axios.get(`/api/wilayah/kota/${districtCode}/kecamatan`)
                        .then(response => {
                            if (response.data) {
                                populateSelect(subDistrictSelect, response.data, 'Pilih Kecamatan');
                            } else {
                                resetSelect(subDistrictSelect, 'No data available');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            resetSelect(subDistrictSelect, 'Error loading data');
                        });
                }
            });

            // Sub-district change handler with similar error handling
            subDistrictSelect.addEventListener('change', function() {
                const subDistrictCode = this.value;
                resetSelect(villageSelect, 'Loading...');

                if (subDistrictCode) {
                    axios.get(`/api/wilayah/kecamatan/${subDistrictCode}/kelurahan`)
                        .then(response => {
                            if (response.data) {
                                populateSelect(villageSelect, response.data, 'Pilih Desa');
                            } else {
                                resetSelect(villageSelect, 'No data available');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            resetSelect(villageSelect, 'Error loading data');
                        });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#district_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var kotaCode = selectedOption.val();
                console.log('Kode Kota yang dipilih:', kotaCode);
            });
        });
    </script>
</x-layout>
