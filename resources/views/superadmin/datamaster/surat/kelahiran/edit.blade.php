<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Keterangan Kelahiran</h1>

        <form method="POST" action="{{ route('superadmin.surat.kelahiran.update', $kelahiran->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_accepted" value="1">

            <!-- Data Pribadi Ayah Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Pribadi Ayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ayah -->
                        <div>
                            <label for="father_nik" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                            <select id="father_nik" name="father_nik" class="father-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                                @if($kelahiran->father_nik)
                                    <option value="{{ $kelahiran->father_nik }}" selected>{{ $kelahiran->father_nik }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- Nama Lengkap Ayah -->
                        <div>
                            <label for="father_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ayah</label>
                            <select id="father_full_name" name="father_full_name" class="father-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                                @if($kelahiran->father_full_name)
                                    <option value="{{ $kelahiran->father_full_name }}" selected>{{ $kelahiran->father_full_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ayah -->
                        <div>
                            <label for="father_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="father_birth_place" name="father_birth_place" value="{{ $kelahiran->father_birth_place }}" class="father-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ayah -->
                        <div>
                            <label for="father_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="father_birth_date" name="father_birth_date" value="{{ $kelahiran->father_birth_date ? date('Y-m-d', strtotime($kelahiran->father_birth_date)) : '' }}" class="father-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ayah -->
                        <div>
                            <label for="father_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="father_job" name="father_job" class="father-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}" {{ $kelahiran->father_job == $job['id'] ? 'selected' : '' }}>{{ $job['name'] }}</option>
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
                                <option value="1" {{ $kelahiran->father_religion == '1' ? 'selected' : '' }}>Islam</option>
                                <option value="2" {{ $kelahiran->father_religion == '2' ? 'selected' : '' }}>Kristen</option>
                                <option value="3" {{ $kelahiran->father_religion == '3' ? 'selected' : '' }}>Katholik</option>
                                <option value="4" {{ $kelahiran->father_religion == '4' ? 'selected' : '' }}>Hindu</option>
                                <option value="5" {{ $kelahiran->father_religion == '5' ? 'selected' : '' }}>Buddha</option>
                                <option value="6" {{ $kelahiran->father_religion == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                                <option value="7" {{ $kelahiran->father_religion == '7' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ayah -->
                        <div>
                            <label for="father_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="father_address" name="father_address" class="father-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $kelahiran->father_address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pribadi Ibu Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Pribadi Ibu</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ibu -->
                        <div>
                            <label for="mother_nik" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                            <select id="mother_nik" name="mother_nik" class="mother-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                                @if($kelahiran->mother_nik)
                                    <option value="{{ $kelahiran->mother_nik }}" selected>{{ $kelahiran->mother_nik }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- Nama Lengkap Ibu -->
                        <div>
                            <label for="mother_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ibu</label>
                            <select id="mother_full_name" name="mother_full_name" class="mother-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                                @if($kelahiran->mother_full_name)
                                    <option value="{{ $kelahiran->mother_full_name }}" selected>{{ $kelahiran->mother_full_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ibu -->
                        <div>
                            <label for="mother_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="mother_birth_place" name="mother_birth_place" value="{{ $kelahiran->mother_birth_place }}" class="mother-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ibu -->
                        <div>
                            <label for="mother_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="mother_birth_date" name="mother_birth_date" value="{{ $kelahiran->mother_birth_date ? date('Y-m-d', strtotime($kelahiran->mother_birth_date)) : '' }}" class="mother-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ibu -->
                        <div>
                            <label for="mother_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="mother_job" name="mother_job" class="mother-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}" {{ $kelahiran->mother_job == $job['id'] ? 'selected' : '' }}>{{ $job['name'] }}</option>
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
                                <option value="1" {{ $kelahiran->mother_religion == '1' ? 'selected' : '' }}>Islam</option>
                                <option value="2" {{ $kelahiran->mother_religion == '2' ? 'selected' : '' }}>Kristen</option>
                                <option value="3" {{ $kelahiran->mother_religion == '3' ? 'selected' : '' }}>Katholik</option>
                                <option value="4" {{ $kelahiran->mother_religion == '4' ? 'selected' : '' }}>Hindu</option>
                                <option value="5" {{ $kelahiran->mother_religion == '5' ? 'selected' : '' }}>Buddha</option>
                                <option value="6" {{ $kelahiran->mother_religion == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                                <option value="7" {{ $kelahiran->mother_religion == '7' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ibu -->
                        <div>
                            <label for="mother_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="mother_address" name="mother_address" class="mother-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ $kelahiran->mother_address }}</textarea>
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
                                    <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $kelahiran->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="province_id" name="province_id" value="{{ $kelahiran->province_id }}">
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kabupaten</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $kelahiran->district_id == $district['id'] ? 'selected' : '' }}>{{ $district['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="{{ $kelahiran->district_id }}">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($subDistricts as $subDistrict)
                                    <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $kelahiran->subdistrict_id == $subDistrict['id'] ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $kelahiran->subdistrict_id }}">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Desa</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $kelahiran->village_id == $village['id'] ? 'selected' : '' }}>{{ $village['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="{{ $kelahiran->village_id }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pribadi Anak Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Pribadi Anak</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Anak -->
                        <div>
                            <label for="child_name" class="block text-sm font-medium text-gray-700">Nama Anak <span class="text-red-500">*</span></label>
                            <input type="text" id="child_name" name="child_name" value="{{ $kelahiran->child_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Jenis Kelamin Anak -->
                        <div>
                            <label for="child_gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="child_gender" name="child_gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="1" {{ $kelahiran->child_gender == '1' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="2" {{ $kelahiran->child_gender == '2' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Anak -->
                        <div>
                            <label for="child_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="child_birth_place" name="child_birth_place" value="{{ $kelahiran->child_birth_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Lahir Anak -->
                        <div>
                            <label for="child_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="child_birth_date" name="child_birth_date" value="{{ $kelahiran->child_birth_date ? date('Y-m-d', strtotime($kelahiran->child_birth_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Anak Ke -->
                        <div>
                            <label for="child_order" class="block text-sm font-medium text-gray-700">Anak Ke <span class="text-red-500">*</span></label>
                            <input type="number" id="child_order" name="child_order" min="1" value="{{ $kelahiran->child_order }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Agama Anak -->
                        <div>
                            <label for="child_religion" class="block text-sm font-medium text-gray-700">Agama Anak <span class="text-red-500">*</span></label>
                            <select id="child_religion" name="child_religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Agama</option>
                                <option value="1" {{ $kelahiran->child_religion == '1' ? 'selected' : '' }}>Islam</option>
                                <option value="2" {{ $kelahiran->child_religion == '2' ? 'selected' : '' }}>Kristen</option>
                                <option value="3" {{ $kelahiran->child_religion == '3' ? 'selected' : '' }}>Katholik</option>
                                <option value="4" {{ $kelahiran->child_religion == '4' ? 'selected' : '' }}>Hindu</option>
                                <option value="5" {{ $kelahiran->child_religion == '5' ? 'selected' : '' }}>Buddha</option>
                                <option value="6" {{ $kelahiran->child_religion == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                                <option value="7" {{ $kelahiran->child_religion == '7' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Anak -->
                        <div>
                            <label for="child_address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="child_address" name="child_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $kelahiran->child_address }}</textarea>
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
                            <input type="text" id="letter_number" name="letter_number" value="{{ $kelahiran->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->id }}" {{ $kelahiran->signing == $signer->id ? 'selected' : '' }}>
                                        {{ $signer->judul }} - {{ $signer->keterangan }}
                                    </option>
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
                    Accept
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure the signing field is correctly submitted as an ID
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const signingSelect = document.getElementById('signing');
                if (signingSelect.value) {
                    // Make sure it's treated as a numeric ID
                    signingSelect.value = parseInt(signingSelect.value, 10) || signingSelect.value;
                }
            });

            // Define isUpdating in the global scope so all handlers can access it
            let isUpdating = false;

            // Store the loaded citizens for reuse
            let allCitizens = [];

            // Cascading region selection code for province, district, subdistrict, village
            const provinceSelect = document.getElementById('province_code');
            const districtSelect = document.getElementById('district_code');
            const subDistrictSelect = document.getElementById('subdistrict_code');
            const villageSelect = document.getElementById('village_code');

            // Hidden inputs for IDs
            const provinceIdInput = document.getElementById('province_id');
            const districtIdInput = document.getElementById('district_id');
            const subDistrictIdInput = document.getElementById('subdistrict_id');
            const villageIdInput = document.getElementById('village_id');

            // Store original values to repopulate dropdowns
            const originalProvinceId = "{{ $kelahiran->province_id }}";
            const originalDistrictId = "{{ $kelahiran->district_id }}";
            const originalSubdistrictId = "{{ $kelahiran->subdistrict_id }}";
            const originalVillageId = "{{ $kelahiran->village_id }}";

            // Helper function to reset select options
            function resetSelect(select, defaultText = 'Pilih', hiddenInput = null) {
                select.innerHTML = `<option value="">${defaultText}</option>`;
                select.disabled = true;
                if (hiddenInput) hiddenInput.value = '';
            }

            // Helper function to populate select options with code as value and id as data attribute
            function populateSelect(select, data, defaultText, hiddenInput = null, selectedId = null) {
                try {
                    select.innerHTML = `<option value="">${defaultText}</option>`;

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.code;
                        option.textContent = item.name;
                        option.setAttribute('data-id', item.id);
                        if (selectedId && item.id.toString() === selectedId.toString()) {
                            option.selected = true;
                            if (hiddenInput) hiddenInput.value = item.id;
                        }
                        select.appendChild(option);
                    });

                    select.disabled = false;
                } catch (error) {
                    console.error('Error populating select:', error);
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

            // Load districts for a province
            function loadDistricts(provinceCode, selectedId = null) {
                resetSelect(districtSelect, 'Loading...', districtIdInput);
                resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                if (provinceCode) {
                    fetch(`{{ url('/location/districts') }}/${provinceCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(districtSelect, data, 'Pilih Kabupaten', districtIdInput, selectedId);
                                districtSelect.disabled = false;

                                // If we have a district selected, load its subdistricts
                                const selectedDistrictOption = [...districtSelect.options].find(option =>
                                    option.getAttribute('data-id') === selectedId);

                                if (selectedDistrictOption) {
                                    loadSubdistricts(selectedDistrictOption.value, originalSubdistrictId);
                                }
                            } else {
                                resetSelect(districtSelect, 'No data available', districtIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(districtSelect, 'Error loading data', districtIdInput);
                        });
                }
            }

            // Load subdistricts for a district
            function loadSubdistricts(districtCode, selectedId = null) {
                resetSelect(subDistrictSelect, 'Loading...', subDistrictIdInput);
                resetSelect(villageSelect, 'Pilih Desa', villageIdInput);

                if (districtCode) {
                    fetch(`{{ url('/location/sub-districts') }}/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(subDistrictSelect, data, 'Pilih Kecamatan', subDistrictIdInput, selectedId);
                                subDistrictSelect.disabled = false;

                                // If we have a subdistrict selected, load its villages
                                const selectedSubdistrictOption = [...subDistrictSelect.options].find(option =>
                                    option.getAttribute('data-id') === selectedId);

                                if (selectedSubdistrictOption) {
                                    loadVillages(selectedSubdistrictOption.value, originalVillageId);
                                }
                            } else {
                                resetSelect(subDistrictSelect, 'No data available', subDistrictIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(subDistrictSelect, 'Error loading data', subDistrictIdInput);
                        });
                }
            }

            // Load villages for a subdistrict
            function loadVillages(subDistrictCode, selectedId = null) {
                resetSelect(villageSelect, 'Loading...', villageIdInput);

                if (subDistrictCode) {
                    fetch(`{{ url('/location/villages') }}/${subDistrictCode}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                populateSelect(villageSelect, data, 'Pilih Desa', villageIdInput, selectedId);
                                villageSelect.disabled = false;
                            } else {
                                resetSelect(villageSelect, 'No data available', villageIdInput);
                            }
                        })
                        .catch(error => {
                            resetSelect(villageSelect, 'Error loading data', villageIdInput);
                        });
                }
            }

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                const provinceCode = this.value;
                // Update the hidden input with the ID
                updateHiddenInput(this, provinceIdInput);

                // Load districts for the selected province
                if (provinceCode) {
                    loadDistricts(provinceCode);
                } else {
                    resetSelect(districtSelect, 'Pilih Kabupaten', districtIdInput);
                    resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                }
            });

            // District change handler
            districtSelect.addEventListener('change', function() {
                const districtCode = this.value;
                // Update hidden input with ID
                updateHiddenInput(this, districtIdInput);

                // Load subdistricts for the selected district
                if (districtCode) {
                    loadSubdistricts(districtCode);
                } else {
                    resetSelect(subDistrictSelect, 'Pilih Kecamatan', subDistrictIdInput);
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                }
            });

            // Sub-district change handler
            subDistrictSelect.addEventListener('change', function() {
                const subDistrictCode = this.value;
                // Update hidden input with ID
                updateHiddenInput(this, subDistrictIdInput);

                // Load villages for the selected subdistrict
                if (subDistrictCode) {
                    loadVillages(subDistrictCode);
                } else {
                    resetSelect(villageSelect, 'Pilih Desa', villageIdInput);
                }
            });

            // Village change handler
            villageSelect.addEventListener('change', function() {
                // Update hidden input with ID
                updateHiddenInput(this, villageIdInput);
            });

            // Initialize dropdown data on page load
            // Find the selected province option and get its code value
            const selectedProvinceOption = [...provinceSelect.options].find(option =>
                option.getAttribute('data-id') === originalProvinceId);

            if (selectedProvinceOption) {
                // Load the district data for this province
                loadDistricts(selectedProvinceOption.value, originalDistrictId);
            } else {
                console.log('No matching province found for ID:', originalProvinceId);
            }

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
                // Same Select2 initialization code as in create.blade.php
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

                // Initialize Select2 for father and mother NIK and name fields
                // with their existing values pre-selected

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
                    }
                }).val('{{ $kelahiran->father_nik }}').trigger('change');

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
                }).val('{{ $kelahiran->father_full_name }}').trigger('change');

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
                    }
                }).val('{{ $kelahiran->mother_nik }}').trigger('change');

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
                }).val('{{ $kelahiran->mother_full_name }}').trigger('change');

                // Event handlers similar to create.blade.php
                $('#father_nik').on('select2:select', function (e) {
                    if (isUpdating) return;
                    isUpdating = true;
                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        $('#father_full_name').val(citizen.full_name).trigger('change.select2');
                        populateParentFields(citizen, 'father');

                        // Auto-fill location fields with father's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ayah');
                        }
                    }
                    isUpdating = false;
                });

                $('#father_full_name').on('select2:select', function (e) {
                    if (isUpdating) return;
                    isUpdating = true;
                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $('#father_nik').val(nikValue).trigger('change.select2');
                        populateParentFields(citizen, 'father');

                        // Auto-fill location fields with father's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ayah');
                        }
                    }
                    isUpdating = false;
                });

                $('#mother_nik').on('select2:select', function (e) {
                    if (isUpdating) return;
                    isUpdating = true;
                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        $('#mother_full_name').val(citizen.full_name).trigger('change.select2');
                        populateParentFields(citizen, 'mother');

                        // Auto-fill location fields with mother's location data
                        if (citizen.province_id && citizen.district_id && (citizen.subdistrict_id || citizen.sub_district_id) && citizen.village_id) {
                            populateLocationFromCitizen(citizen, 'ibu');
                        }
                    }
                    isUpdating = false;
                });

                $('#mother_full_name').on('select2:select', function (e) {
                    if (isUpdating) return;
                    isUpdating = true;
                    const citizen = e.params.data.citizen;
                    if (citizen) {
                        const nikValue = citizen.nik ? citizen.nik.toString() : '';
                        $('#mother_nik').val(nikValue).trigger('change.select2');
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
                document.getElementById(`job_type_id_${parentType}`).value = citizen.job_type_id;
                // Also update the job name hidden field
                const jobSelect = document.getElementById(`job_type_id_${parentType}`);
                const selectedOption = jobSelect.options[jobSelect.selectedIndex];
                if (selectedOption) {
                    document.getElementById(`${parentType}_job_name`).value = selectedOption.text;
                }
            }
        }

        // Function to populate location fields from citizen data
        function populateLocationFromCitizen(citizen, parentType) {
            // Only attempt to populate if we have valid location data
            const subDistrictId = citizen.subdistrict_id || citizen.sub_district_id;
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
                                throw new Error('Network response was not ok');
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
                                // Now load subdistricts
                                fetch(`{{ url('/location/sub-districts') }}/${selectedDistrictCode}`)
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

                                        let subdistrictFound = false;
                                        let selectedSubdistrictCode = null;

                                        subdistricts.forEach(subdistrict => {
                                            const subdistrictOption = document.createElement('option');
                                            subdistrictOption.value = subdistrict.code;
                                            subdistrictOption.textContent = subdistrict.name;
                                            subdistrictOption.setAttribute('data-id', subdistrict.id);

                                            if (subdistrict.id == subDistrictId) {
                                                subdistrictOption.selected = true;
                                                selectedSubdistrictCode = subdistrict.code;
                                                subdistrictFound = true;
                                            }

                                            subdistrictSelect.appendChild(subdistrictOption);
                                        });

                                        subdistrictSelect.disabled = false;

                                        if (subdistrictFound && selectedSubdistrictCode) {
                                            // Finally, load villages
                                            fetch(`{{ url('/location/villages') }}/${selectedSubdistrictCode}`)
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

                                                        if (village.id == citizen.village_id) {
                                                            villageOption.selected = true;
                                                        }

                                                        villageSelect.appendChild(villageOption);
                                                    });

                                                    villageSelect.disabled = false;
                                                })
                                                .catch(error => {
                                                    const villageSelect = document.getElementById('village_code');
                                                    villageSelect.innerHTML = '<option value="">Error loading data</option>';
                                                });
                                        }
                                    })
                                    .catch(error => {
                                        const subdistrictSelect = document.getElementById('subdistrict_code');
                                        subdistrictSelect.innerHTML = '<option value="">Error loading data</option>';
                                    });
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
    </script>
</x-layout>
