<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Kelahiran</h1>

        <form method="POST" action="{{ route('superadmin.surat.kelahiran.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Father Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Ayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ayah -->
                        <div>
                            <label for="father_nik" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                            <select id="father_nik" name="father_nik" class="father-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap Ayah -->
                        <div>
                            <label for="father_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ayah</label>
                            <select id="father_full_name" name="father_full_name" class="father-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ayah -->
                        <div>
                            <label for="father_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="father_birth_place" name="father_birth_place" class="father-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ayah -->
                        <div>
                            <label for="father_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="father_birth_date" name="father_birth_date" class="father-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ayah -->
                        <div>
                            <label for="father_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="father_job" name="father_job" class="father-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                @empty
                                    <option value="">Tidak ada data pekerjaan</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Agama Ayah -->
                        <div>
                            <label for="father_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                            <select id="father_religion" name="father_religion" class="father-religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ayah -->
                        <div>
                            <label for="father_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="father_address" name="father_address" class="father-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mother Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Ibu</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ibu -->
                        <div>
                            <label for="mother_nik" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                            <select id="mother_nik" name="mother_nik" class="mother-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap Ibu -->
                        <div>
                            <label for="mother_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ibu</label>
                            <select id="mother_full_name" name="mother_full_name" class="mother-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ibu -->
                        <div>
                            <label for="mother_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="mother_birth_place" name="mother_birth_place" class="mother-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ibu -->
                        <div>
                            <label for="mother_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="mother_birth_date" name="mother_birth_date" class="mother-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ibu -->
                        <div>
                            <label for="mother_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="mother_job" name="mother_job" class="mother-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                @empty
                                    <option value="">Tidak ada data pekerjaan</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Agama Ibu -->
                        <div>
                            <label for="mother_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                            <select id="mother_religion" name="mother_religion" class="mother-religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ibu -->
                        <div>
                            <label for="mother_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="mother_address" name="mother_address" class="mother-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wilayah Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Provinsi -->
                        <div>
                            <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                            <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="province_id" name="province_id" value="">
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Desa</option>
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Anak</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Anak -->
                        <div>
                            <label for="child_name" class="block text-sm font-medium text-gray-700">Nama Anak <span class="text-red-500">*</span></label>
                            <input type="text" id="child_name" name="child_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Jenis Kelamin Anak -->
                        <div>
                            <label for="child_gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="child_gender" name="child_gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="1">Laki-Laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Anak -->
                        <div>
                            <label for="child_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="child_birth_place" name="child_birth_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Lahir Anak -->
                        <div>
                            <label for="child_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="child_birth_date" name="child_birth_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Anak Ke -->
                        <div>
                            <label for="child_order" class="block text-sm font-medium text-gray-700">Anak Ke <span class="text-red-500">*</span></label>
                            <input type="number" id="child_order" name="child_order" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Agama Anak -->
                        <div>
                            <label for="child_religion" class="block text-sm font-medium text-gray-700">Agama Anak <span class="text-red-500">*</span></label>
                            <select id="child_religion" name="child_religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Anak -->
                        <div>
                            <label for="child_address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="child_address" name="child_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Surat Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
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
            // Define isUpdating in the global scope so all handlers can access it
            let isUpdating = false;

            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Cascading region selection code for province, district, subdistrict, village
            // Keep original province/district/subdistrict/village cascading selects
            const provinceSelect = document.getElementById('province_code');
            const districtSelect = document.getElementById('district_code');
            const subDistrictSelect = document.getElementById('subdistrict_code');
            const villageSelect = document.getElementById('village_code');

            // Hidden inputs for IDs
            const provinceIdInput = document.getElementById('province_id');
            const districtIdInput = document.getElementById('district_id');
            const subDistrictIdInput = document.getElementById('subdistrict_id');
            const villageIdInput = document.getElementById('village_id');

            // Helper function to reset select options
            function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
                if (hiddenInput) hiddenInput.value = '';
            }

            // Helper function to populate select options
            function populateSelect(select, data, defaultText, hiddenInput = null) {
                try {
                    select.innerHTML = `<option value="">${defaultText}</option>`;

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.code;
                        option.textContent = item.name;
                        option.setAttribute('data-id', item.id);
                        select.appendChild(option);
                    });

                    select.disabled = false;

                    if (hiddenInput) hiddenInput.value = '';
                } catch (error) {
                    select.innerHTML = `<option value="">Error loading data</option>`;
                    select.disabled = true;
                    if (hiddenInput) hiddenInput.value = '';
                }
            }

            // Update hidden input when selection changes
            function updateHiddenInput(select, hiddenInput) {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.hasAttribute('data-id')) {
                    hiddenInput.value = selectedOption.getAttribute('data-id');
                } else {
                    hiddenInput.value = '';
                }
            }

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                const provinceCode = this.value;
                updateHiddenInput(this, provinceIdInput);

                resetSelect(districtSelect, 'Loading...', districtIdInput);
                resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                if (provinceCode) {
                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput);
                                districtSelect.disabled = false;
                            } else {
                                resetSelect(districtSelect, 'No data available', districtIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(districtSelect, 'Error loading data', districtIdInput);
                        });
                }
            });

            // District change handler
            districtSelect.addEventListener('change', function() {
                const districtCode = this.value;
                updateHiddenInput(this, districtIdInput);

                resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                if (districtCode) {
                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput);
                                subDistrictSelect.disabled = false;
                            } else {
                                resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                        });
                }
            });

            // Sub-district change handler
            subDistrictSelect.addEventListener('change', function() {
                const subDistrictCode = this.value;
                updateHiddenInput(this, subDistrictIdInput);

                resetSelect(villageSelect, 'Loading...', villageIdInput);

                if (subDistrictCode) {
                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput);
                                villageSelect.disabled = false;
                            } else {
                                resetSelect(villageSelect, 'No data available', villageIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(villageSelect, 'Error loading data', villageIdInput);
                        });
                }
            });

            // Village change handler
            villageSelect.addEventListener('change', function() {
                updateHiddenInput(this, villageIdInput);
            });

            // Form validation - modify to allow submission even with errors
            document.querySelector('form').addEventListener('submit', function(e) {
                // Remove preventDefault to allow natural form submission
                // e.preventDefault();

                // Check all required fields
                const requiredFields = document.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                // Check location data
                const provinceId = document.getElementById('province_id').value;
                const districtId = document.getElementById('district_id').value;
                const subDistrictId = document.getElementById('subdistrict_id').value;
                const villageId = document.getElementById('village_id').value;

                if (!isValid || !provinceId || !districtId || !subDistrictId || !villageId) {
                    // Add alert but allow form to submit (for debugging)
                    alert('Ada beberapa field yang belum terisi dengan benar. Form akan tetap dikirim untuk debugging.');
                    // Log form data for debugging
                    console.log('Form Data:', {
                        provinceId,
                        districtId,
                        subDistrictId,
                        villageId,
                        'child_name': document.getElementById('child_name').value,
                        'child_gender': document.getElementById('child_gender').value,
                        'child_religion': document.getElementById('child_religion').value
                    });
                    // Do not return false to allow submission
                }
            });

            // Load all citizens first before initializing Select2
            $.ajax({
                url: '{{ route("citizens.administrasi") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    limit: 10000 // Increase limit to load more citizens at once
                },
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    // Transform the response to match what we expect
                    let processedData = data;
                    if (data && data.data && Array.isArray(data.data)) {
                        processedData = data.data;
                    } else if (data && Array.isArray(data)) {
                        processedData = data;
                    }

                    // Make sure we have valid data
                    if (!Array.isArray(processedData)) {
                        return;
                    }

                    allCitizens = processedData;

                    // Now initialize Select2 with the pre-loaded data
                    initializeParentSelect2WithData();
                },
                error: function(error) {
                    // Initialize Select2 anyway, but it will use AJAX for searching
                    initializeParentSelect2WithData();
                }
            });

            function initializeParentSelect2WithData() {
                // Create NIK options arrays for father and mother
                const fatherNikOptions = [];
                const fatherNameOptions = [];
                const motherNikOptions = [];
                const motherNameOptions = [];

                // Process citizen data for Select2
                for (let i = 0; i < allCitizens.length; i++) {
                    const citizen = allCitizens[i];

                    // Handle cases where NIK might be coming from various fields
                    let nikValue = null;

                    if (typeof citizen.nik !== 'undefined' && citizen.nik !== null) {
                        nikValue = citizen.nik;
                    } else if (typeof citizen.id !== 'undefined' && citizen.id !== null && !isNaN(citizen.id)) {
                        // If id is numeric (not a name), it might be the NIK
                        nikValue = citizen.id;
                    }

                    if (nikValue !== null) {
                        const nikString = nikValue.toString();
                        fatherNikOptions.push({
                            id: nikString,
                            text: nikString,
                            citizen: citizen
                        });
                        motherNikOptions.push({
                            id: nikString,
                            text: nikString,
                            citizen: citizen
                        });
                    }

                    // Only add if full_name is available
                    if (citizen.full_name) {
                        fatherNameOptions.push({
                            id: citizen.full_name,
                            text: citizen.full_name,
                            citizen: citizen
                        });
                        motherNameOptions.push({
                            id: citizen.full_name,
                            text: citizen.full_name,
                            citizen: citizen
                        });
                    }
                }

                // Initialize Father NIK Select2
                $('#father_nik').select2({
                    placeholder: 'Pilih NIK',
                    width: '100%',
                    data: fatherNikOptions,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: function(data) {
                        if (data.loading) return data.text;
                        return '<div>' + data.text + '</div>';
                    }
                }).on("select2:open", function() {
                    // This ensures all options are visible when dropdown opens
                    $('.select2-results__options').css('max-height', '400px');
                });

                // Initialize Father Full Name Select2
                $('#father_full_name').select2({
                    placeholder: 'Pilih Nama Lengkap',
                    width: '100%',
                    data: fatherNameOptions,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        }
                    }
                }).on("select2:open", function() {
                    // This ensures all options are visible when dropdown opens
                    $('.select2-results__options').css('max-height', '400px');
                });

                // Initialize Mother NIK Select2
                $('#mother_nik').select2({
                    placeholder: 'Pilih NIK',
                    width: '100%',
                    data: motherNikOptions,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: function(data) {
                        if (data.loading) return data.text;
                        return '<div>' + data.text + '</div>';
                    }
                }).on("select2:open", function() {
                    // This ensures all options are visible when dropdown opens
                    $('.select2-results__options').css('max-height', '400px');
                });

                // Initialize Mother Full Name Select2
                $('#mother_full_name').select2({
                    placeholder: 'Pilih Nama Lengkap',
                    width: '100%',
                    data: motherNameOptions,
                    language: {
                        noResults: function() {
                            return 'Tidak ada data yang ditemukan';
                        },
                        searching: function() {
                            return 'Mencari...';
                        }
                    }
                }).on("select2:open", function() {
                    // This ensures all options are visible when dropdown opens
                    $('.select2-results__options').css('max-height', '400px');
                });

                // When Father NIK is selected, fill in other father fields
                $('#father_nik').on('select2:select', function (e) {
                    if (isUpdating) return; // Prevent recursion
                    isUpdating = true;

                    // Get the selected citizen data
                    const citizen = e.params.data.citizen;

                    if (citizen) {
                        // Set Full Name in dropdown
                        $('#father_full_name').val(citizen.full_name).trigger('change.select2'); // Just update UI, not trigger full change

                        populateParentFields(citizen, 'father');

                        // Auto-fill location fields with father's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ayah');

                            // Also populate child address from father's address
                            if (citizen.address) {
                                $('#child_address').val(citizen.address);
                            }
                        }
                    }

                    isUpdating = false;
                });

                // When Father Full Name is selected, fill in other father fields
                $('#father_full_name').on('select2:select', function (e) {
                    if (isUpdating) return; // Prevent recursion
                    isUpdating = true;

                    const citizen = e.params.data.citizen;

                    if (citizen) {
                        // Set NIK in dropdown without triggering the full change event
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $('#father_nik').val(nikValue).trigger('change.select2');  // Just update the UI

                        populateParentFields(citizen, 'father');

                        // Auto-fill location fields with father's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ayah');
                        }
                    }

                    isUpdating = false;
                });

                // When Mother NIK is selected, fill in other mother fields
                $('#mother_nik').on('select2:select', function (e) {
                    if (isUpdating) return; // Prevent recursion
                    isUpdating = true;

                    // Get the selected citizen data
                    const citizen = e.params.data.citizen;

                    if (citizen) {
                        // Set Full Name in dropdown
                        $('#mother_full_name').val(citizen.full_name).trigger('change.select2'); // Just update UI, not trigger full change

                        populateParentFields(citizen, 'mother');

                        // Auto-fill location fields with mother's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ibu');

                            // Also populate child address from mother's address
                            if (citizen.address) {
                                $('#child_address').val(citizen.address);
                            }
                        }
                    }

                    isUpdating = false;
                });

                // When Mother Full Name is selected, fill in other mother fields
                $('#mother_full_name').on('select2:select', function (e) {
                    if (isUpdating) return; // Prevent recursion
                    isUpdating = true;

                    const citizen = e.params.data.citizen;

                    if (citizen) {
                        // Set NIK in dropdown without triggering the full change event
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $('#mother_nik').val(nikValue).trigger('change.select2');  // Just update the UI

                        populateParentFields(citizen, 'mother');

                        // Auto-fill location fields with mother's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ibu');
                        }
                    }

                    isUpdating = false;
                });
            }
        });

        // Function to populate parent fields with citizen data
        function populateParentFields(citizen, parentType) {
            // Set fields based on parent type (father or mother)
            document.getElementById(`${parentType}_birth_place`).value = citizen.birth_place || '';

            // Handle birth_date - reformatting if needed
            if (citizen.birth_date) {
                // Check if birth_date is in DD/MM/YYYY format and convert it
                if (citizen.birth_date.includes('/')) {
                    const [day, month, year] = citizen.birth_date.split('/');
                    document.getElementById(`${parentType}_birth_date`).value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                } else {
                    document.getElementById(`${parentType}_birth_date`).value = citizen.birth_date;
                }
            } else {
                document.getElementById(`${parentType}_birth_date`).value = '';
            }

            // Set address field
            document.getElementById(`${parentType}_address`).value = citizen.address || '';

            // Handle religion selection - convert string to numeric value
            let religion = citizen.religion;
            if (typeof religion === 'string') {
                const religionMap = {
                    'islam': 1,
                    'kristen': 2,
                    'katholik': 3,
                    'hindu': 4,
                    'buddha': 5,
                    'kong hu cu': 6,
                    'lainnya': 7
                };
                religion = religionMap[religion.toLowerCase()] || '';
            }
            document.getElementById(`${parentType}_religion`).value = religion;

            // Set job type ID if available
            if (citizen.job_type_id) {
                document.getElementById(`${parentType}_job`).value = citizen.job_type_id;
            }
        }

        // Function to populate location fields from citizen data
        function populateLocationFromCitizen(citizen, parentType) {
            // Support both naming conventions for subdistrict
            const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;

            // Only attempt to populate if we have valid location data
            if (!citizen.province_id || !citizen.district_id || !subDistrictId || !citizen.village_id) {
                return;
            }

            // Set hidden ID fields directly without confirmation
            $('#province_id').val(citizen.province_id);
            $('#district_id').val(citizen.district_id);
            $('#subdistrict_id').val(subDistrictId);
            $('#village_id').val(citizen.village_id);

            // Find and select the correct province option
            const provinceSelect = document.getElementById('province_code');
            let provinceFound = false;

            for (let i = 0; i < provinceSelect.options.length; i++) {
                const option = provinceSelect.options[i];
                if (option.getAttribute('data-id') == citizen.province_id) {
                    provinceSelect.value = option.value;
                    provinceFound = true;

                    // Now load districts with improved error handling
                    fetch(`{{ url('/location/districts') }}/${option.value}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(districts => {
                            if (!districts || !Array.isArray(districts) || districts.length === 0) {
                                return;
                            }

                            // Populate district dropdown
                            const districtSelect = document.getElementById('district_code');
                            districtSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                            let districtFound = false;
                            let selectedDistrictCode = null;

                            districts.forEach(district => {
                                const districtOption = document.createElement('option');
                                districtOption.value = district.code;
                                districtOption.textContent = district.name;
                                districtOption.setAttribute('data-id', district.id);

                                if (district.id == citizen.district_id) {
                                    districtOption.selected = true;
                                    selectedDistrictCode = district.code;
                                    districtFound = true;
                                }

                                districtSelect.appendChild(districtOption);
                            });

                            districtSelect.disabled = false;

                            if (districtFound && selectedDistrictCode) {
                                // Load the subdistricts and villages
                                loadSubdistrictsAndVillages(selectedDistrictCode, subDistrictId, citizen.village_id);
                            }
                        })
                        .catch(error => {
                            const districtSelect = document.getElementById('district_code');
                            districtSelect.innerHTML = '<option value="">Error loading data</option>';
                        });

                    break;
                }
            }
        }

        // Helper function to load subdistricts and villages
        function loadSubdistrictsAndVillages(districtCode, subDistrictId, villageId) {
            fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(subdistricts => {
                    if (!subdistricts || !Array.isArray(subdistricts) || subdistricts.length === 0) {
                        return;
                    }

                    // Populate subdistrict dropdown
                    const subdistrictSelect = document.getElementById('subdistrict_code');
                    subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    subdistricts.forEach(subdistrict => {
                        const subdistrictOption = document.createElement('option');
                        subdistrictOption.value = subdistrict.code;
                        subdistrictOption.textContent = subdistrict.name;
                        subdistrictOption.setAttribute('data-id', subdistrict.id);
                        if (subdistrict.id == subDistrictId) {
                            subdistrictOption.selected = true;
                        }
                        subdistrictSelect.appendChild(subdistrictOption);
                    });
                    subdistrictSelect.disabled = false;

                    // Ensure we update the hidden input for subdistrict
                    if (subdistrictSelect.selectedIndex > 0) {
                        const selectedOption = subdistrictSelect.options[subdistrictSelect.selectedIndex];
                        $('#subdistrict_id').val(selectedOption.getAttribute('data-id'));

                        // Find the selected subdistrict code to load villages
                        const selectedSubdistrictCode = selectedOption.value;

                        if (selectedSubdistrictCode) {
                            loadVillages(selectedSubdistrictCode, villageId);
                        }
                    } else {
                        // Try to find matching subdistrict
                        for (let i = 0; i < subdistrictSelect.options.length; i++) {
                            if (subdistrictSelect.options[i].getAttribute('data-id') == subDistrictId) {
                                subdistrictSelect.selectedIndex = i;
                                $('#subdistrict_id').val(subDistrictId);
                                loadVillages(subdistrictSelect.options[i].value, villageId);
                                break;
                            }
                        }
                    }
                })
                .catch(error => {
                    const subdistrictSelect = document.getElementById('subdistrict_code');
                    subdistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }

        // Separate function to load villages for better organization
        function loadVillages(subdistrictCode, villageId) {
            fetch(`{{ url('/location/villages') }}/${subdistrictCode}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(villages => {
                    if (!villages || !Array.isArray(villages) || villages.length === 0) {
                        return;
                    }

                    // Populate village dropdown
                    const villageSelect = document.getElementById('village_code');
                    villageSelect.innerHTML = '<option value="">Pilih Desa</option>';
                    villages.forEach(village => {
                        const villageOption = document.createElement('option');
                        villageOption.value = village.code;
                        villageOption.textContent = village.name;
                        villageOption.setAttribute('data-id', village.id);
                        if (village.id == villageId) {
                            villageOption.selected = true;
                        }
                        villageSelect.appendChild(villageOption);
                    });
                    villageSelect.disabled = false;

                    // Ensure we update the hidden input for village
                    if (villageSelect.selectedIndex > 0) {
                        const selectedOption = villageSelect.options[villageSelect.selectedIndex];
                        $('#village_id').val(selectedOption.getAttribute('data-id'));
                    }
                })
                .catch(error => {
                    const villageSelect = document.getElementById('village_code');
                    villageSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        }
    </script>
</x-layout>
