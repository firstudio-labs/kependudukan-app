 <x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Aset</h1>

        <form method="POST" action="{{ route('user.kelola-aset.update', $aset->id) }}" enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIK Pemilik -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK Pemilik</label>
                    <div class="relative">
                        <input type="text" id="nik-input" name="nik"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            value="{{ old('nik', $aset->nik_pemilik) }}" placeholder="Pilih atau ketik NIK">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button type="button" id="toggle-nik-dropdown" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="nik-dropdown"
                        class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                        <!-- NIK options will be populated here -->
                    </div>
                </div>
                
                <!-- Nama Pemilik -->
                <div>
                    <label for="nama_pemilik" class="block text-sm font-medium text-gray-700">Nama Pemilik</label>
                    <input type="text" id="nama_pemilik" name="nama_pemilik"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        value="{{ old('nama_pemilik', $aset->nama_pemilik) }}">
                    <p class="mt-1 text-xs text-gray-500">Nama akan terisi otomatis saat NIK dipilih, atau dapat diisi manual</p>
                </div>

                <!-- Nama Aset -->
                <div>
                    <label for="nama_aset" class="block text-sm font-medium text-gray-700">Nama Aset</label>
                    <input type="text" id="nama_aset" name="nama_aset"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        value="{{ old('nama_aset', $aset->nama_aset) }}">
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" autocomplete="off"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>{{ old('address', $aset->address) }}</textarea>
                </div>

                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Provinsi <span
                            class="text-red-500">*</span></label>
                    <select id="province_id" name="province_id"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="" selected disabled>Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['id'] }}" {{ $aset->province_id == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Kabupaten -->
                <div>
                    <label for="district_id"  class="block text-sm font-medium text-gray-700">Kabupaten <span
                            class="text-red-500">*</span></label>
                    <select id="district_id" name="district_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="" selected disabled>Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['id'] }}" {{ $aset->district_id == $district['id'] ? 'selected' : '' }}>
                                {{ $district['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700">Kecamatan <span
                            class="text-red-500">*</span></label>
                    <select id="sub_district_id" name="sub_district_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="" selected disabled>Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['id'] }}" {{ $aset->sub_district_id == $subDistrict['id'] ? 'selected' : '' }}>
                                {{ $subDistrict['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Desa -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Desa <span
                            class="text-red-500">*</span></label>
                    <select id="village_id" name="village_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="" selected disabled>Pilih Desa/Kelurahan</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['id'] }}" {{ $aset->village_id == $village['id'] ? 'selected' : '' }}>
                                {{ $village['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT</label>
                    <input type="text" id="rt" name="rt"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        value="{{ old('rt', $aset->rt) }}">
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW</label>
                    <input type="text" id="rw" name="rw"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        value="{{ old('rw', $aset->rw) }}">
                </div>

                <!-- Klasifikasi -->
                <div>
                    <label for="klasifikasi_id" class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                    <select id="klasifikasi_id" name="klasifikasi_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Klasifikasi</option>
                        @foreach($klasifikasi as $k)
                            <option value="{{ $k->id }}" {{ $k->id == $aset->klasifikasi_id ? 'selected' : '' }}>
                                {{ $k->kode }} - {{ $k->jenis_klasifikasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Jenis Aset -->
                <div>
                    <label for="jenis_aset_id" class="block text-sm font-medium text-gray-700">Jenis Aset</label>
                    <select id="jenis_aset_id" name="jenis_aset_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        required>
                        <option value="">Pilih Jenis Aset</option>
                        @foreach($jenis_aset as $ja)
                            <option value="{{ $ja->id }}" {{ $ja->id == $aset->jenis_aset_id ? 'selected' : '' }}>
                                {{ $ja->kode }} - {{ $ja->jenis_aset }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Foto Aset (Depan) -->
                <div>
                    <label for="foto_aset_depan" class="block text-sm font-medium text-gray-700">Foto Aset (Depan)</label>
                    @if($aset->foto_aset_depan)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $aset->foto_aset_depan) }}" alt="Foto Aset Depan" class="h-32 w-auto object-cover rounded">
                            <p class="mt-1 text-xs text-gray-500">Foto saat ini - upload baru untuk mengganti</p>
                        </div>
                    @endif
                    <input type="file" id="foto_aset_depan" name="foto_aset_depan" 
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2 mt-1"
                        accept="image/png,image/jpeg,image/jpg,image/gif">
                    <p class="mt-1 text-sm text-gray-500">PNG, JPG or GIF (MAX. 2MB).</p>
                    @error('foto_aset_depan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Aset (Samping) -->
                <div>
                    <label for="foto_aset_samping" class="block text-sm font-medium text-gray-700">Foto Aset (Samping)</label>
                    @if($aset->foto_aset_samping)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $aset->foto_aset_samping) }}" alt="Foto Aset Samping" class="h-32 w-auto object-cover rounded">
                            <p class="mt-1 text-xs text-gray-500">Foto saat ini - upload baru untuk mengganti</p>
                        </div>
                    @endif
                    <input type="file" id="foto_aset_samping" name="foto_aset_samping" 
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2 mt-1"
                        accept="image/png,image/jpeg,image/jpg,image/gif">
                    <p class="mt-1 text-sm text-gray-500">PNG, JPG or GIF (MAX. 2MB).</p>
                    @error('foto_aset_samping')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tag Lokasi Map -->
                <div class="col-span-1 md:col-span-2">
                    <div class="bg-white rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-sm font-medium text-gray-700">Tag Lokasi Aset</h2>
                            </div>
                        </div>

                        @php

$latitude = '';
$longitude = '';
if ($aset->tag_lokasi) {
    $coordinates = explode(',', $aset->tag_lokasi);
    if (count($coordinates) == 2) {
        $latitude = trim($coordinates[0]);
        $longitude = trim($coordinates[1]);
    }
}
                        @endphp
                
                        <x-map-input label="Lokasi Aset" addressId="asset_location" addressName="asset_location"
                            address="{{ old('asset_location', $aset->address) }}" latitudeId="tag_lat" latitudeName="tag_lat"
                            latitude="{{ old('tag_lat', $latitude) }}" longitudeId="tag_lng" longitudeName="tag_lng"
                            longitude="{{ old('tag_lng', $longitude) }}" modalId="" />
                
                       
                        <input type="hidden" id="tag_lokasi" name="tag_lokasi" value="{{ old('tag_lokasi', $aset->tag_lokasi) ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan Perubahan
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


        let nikData = [];
        let selectedNikData = null;

        async function fetchNIKData() {
            try {
                const nikDropdown = document.getElementById('nik-dropdown');

                nikDropdown.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Loading NIK data...</div>';
                nikDropdown.classList.remove('hidden');

               
                const response = await fetch('{{ route("api.all-citizens") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Request failed: ${response.status}`);
                }

                const responseData = await response.json();

                nikDropdown.innerHTML = '';

                if (responseData.status === 'OK' && responseData.data && Array.isArray(responseData.data)) {
                    nikData = responseData.data;

                    nikData.sort((a, b) => String(a.nik).localeCompare(String(b.nik)));

                    nikData.forEach(citizen => {
                        if (citizen.nik) {
                            const option = document.createElement('div');
                            option.className = 'px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer';
                            option.textContent = citizen.nik;
                            option.setAttribute('data-nik', citizen.nik);
                            option.setAttribute('data-name', citizen.full_name || '');
                            option.addEventListener('click', function () {
                                selectNIK(citizen.nik, citizen.full_name || '');
                            });
                            nikDropdown.appendChild(option);
                        }
                    });


                } else {
                    console.error('Invalid or empty data format in response');
                    nikDropdown.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Tidak ada data NIK tersedia</div>';
                }
            } catch (error) {
                console.error('Error fetching NIK data:', error);
                document.getElementById('nik-dropdown').innerHTML =
                    '<div class="px-4 py-2 text-sm text-red-500">Error loading NIK data</div>' +
                    '<div class="px-4 py-2 text-sm text-red-500">Silakan muat ulang halaman</div>';
            }
        }

        function selectNIK(nik, name) {
            const nikInput = document.getElementById('nik-input');
            const namaInput = document.getElementById('nama_pemilik');
            const dropdown = document.getElementById('nik-dropdown');

            nikInput.value = nik;

            if (name && namaInput) {
                namaInput.value = name;
                selectedNikData = { nik, name };
            }

            dropdown.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const nikInput = document.getElementById('nik-input');
            const nikDropdown = document.getElementById('nik-dropdown');
            const toggleButton = document.getElementById('toggle-nik-dropdown');

            toggleButton.addEventListener('click', function () {
                nikDropdown.classList.toggle('hidden');

                if (nikData.length === 0) {
                    fetchNIKData();
                }
            });

            nikInput.addEventListener('input', function () {
                const searchText = this.value.toLowerCase();

                if (nikData.length > 0) {
                    const filteredData = nikData.filter(citizen =>
                        String(citizen.nik).toLowerCase().includes(searchText)
                    );

                    nikDropdown.innerHTML = '';

                    filteredData.forEach(citizen => {
                        if (citizen.nik) {
                            const option = document.createElement('div');
                            option.className = 'px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer';
                            option.textContent = citizen.nik;
                            option.setAttribute('data-nik', citizen.nik);
                            option.setAttribute('data-name', citizen.full_name || '');
                            option.addEventListener('click', function () {
                                selectNIK(citizen.nik, citizen.full_name || '');
                            });
                            nikDropdown.appendChild(option);
                        }
                    });

                    nikDropdown.classList.remove('hidden');
                } else {
                    fetchNIKData();
                }
            });

            document.addEventListener('click', function (event) {
                if (!nikInput.contains(event.target) &&
                    !nikDropdown.contains(event.target) &&
                    !toggleButton.contains(event.target)) {
                    nikDropdown.classList.add('hidden');
                }
            });

            fetchNIKData();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
                
            const config = {

                locationCache: {},
                locationIds: {
                    provinceId: {{ $aset->province_id ?? 'null' }},
                    districtId: {{ $aset->district_id ?? 'null' }},
                    subDistrictId: {{ $aset->sub_district_id ?? 'null' }},
                    villageId: {{ $aset->village_id ?? 'null' }}
                }
            };


            const provinceSelect = document.getElementById('province_id');
            const districtSelect = document.getElementById('district_id');
            const subDistrictSelect = document.getElementById('sub_district_id');
            const villageSelect = document.getElementById('village_id');

            const api = {
                async fetchProvinces() {
                    try {
                        const response = await fetch('{{ route("api.provinces") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        return data.data || [];
                    } catch (error) {
                        console.error('Error fetching provinces:', error);
                        return [];
                    }
                },

                async fetchDistricts(provinceCode) {
                    try {
                        const response = await fetch(`{{ route("api.districts") }}?province_code=${provinceCode}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        return data.data || [];
                    } catch (error) {
                        console.error(`Error fetching districts:`, error);
                        return [];
                    }
                },

                async fetchSubDistricts(districtCode) {
                    try {
                        const response = await fetch(`{{ route("api.subdistricts") }}?district_code=${districtCode}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        return data.data || [];
                    } catch (error) {
                        console.error(`Error fetching sub-districts:`, error);
                        return [];
                    }
                },

                async fetchVillages(subDistrictCode) {
                    try {
                        const response = await fetch(`{{ route("api.villages") }}?subdistrict_code=${subDistrictCode}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        return data.data || [];
                    } catch (error) {
                        console.error(`Error fetching villages:`, error);
                        return [];
                    }
                }
            };
           
            function populateSelect(selectElement, options, selectedId = null) {
                
                const placeholder = selectElement.options[0];
                selectElement.innerHTML = '';
                selectElement.appendChild(placeholder);

                options.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.id;
                    optionElement.textContent = option.name;

                    if (selectedId && option.id == selectedId) {
                        optionElement.selected = true;
                    }

                    selectElement.appendChild(optionElement);
                });

                selectElement.disabled = options.length === 0;
            }

            async function handleProvinceChange() {
                const provinceId = provinceSelect.value;

                districtSelect.innerHTML = '<option value="" selected disabled>Pilih Kabupaten</option>';
                subDistrictSelect.innerHTML = '<option value="" selected disabled>Pilih Kecamatan</option>';
                villageSelect.innerHTML = '<option value="" selected disabled>Pilih Desa/Kelurahan</option>';

                districtSelect.disabled = true;
                subDistrictSelect.disabled = true;
                villageSelect.disabled = true;

                const provinces = await api.fetchProvinces();
                const selectedProvince = provinces.find(p => p.id == provinceId);

                if (selectedProvince) {
                   
                    const districts = await api.fetchDistricts(selectedProvince.code);
                    populateSelect(districtSelect, districts, config.locationIds.districtId);
                    districtSelect.disabled = false;
                }
            }

            async function handleDistrictChange() {
                const districtId = districtSelect.value;

                
                subDistrictSelect.innerHTML = '<option value="" selected disabled>Pilih Kecamatan</option>';
                villageSelect.innerHTML = '<option value="" selected disabled>Pilih Desa/Kelurahan</option>';

                subDistrictSelect.disabled = true;
                villageSelect.disabled = true;

               
                const provinceId = provinceSelect.value;
                const provinces = await api.fetchProvinces();
                const selectedProvince = provinces.find(p => p.id == provinceId);

                if (selectedProvince) {
                    const districts = await api.fetchDistricts(selectedProvince.code);
                    const selectedDistrict = districts.find(d => d.id == districtId);

                    if (selectedDistrict) {
                        
                        const subDistricts = await api.fetchSubDistricts(selectedDistrict.code);
                        populateSelect(subDistrictSelect, subDistricts, config.locationIds.subDistrictId);
                        subDistrictSelect.disabled = false;
                    }
                }
            }

            async function handleSubDistrictChange() {
                const subDistrictId = subDistrictSelect.value;

                
                villageSelect.innerHTML = '<option value="" selected disabled>Pilih Desa/Kelurahan</option>';
                villageSelect.disabled = true;

                
                const provinceId = provinceSelect.value;
                const districtId = districtSelect.value;

                const provinces = await api.fetchProvinces();
                const selectedProvince = provinces.find(p => p.id == provinceId);

                if (selectedProvince) {
                    const districts = await api.fetchDistricts(selectedProvince.code);
                    const selectedDistrict = districts.find(d => d.id == districtId);

                    if (selectedDistrict) {
                        const subDistricts = await api.fetchSubDistricts(selectedDistrict.code);
                        const selectedSubDistrict = subDistricts.find(sd => sd.id == subDistrictId);

                        if (selectedSubDistrict) {
                            
                            const villages = await api.fetchVillages(selectedSubDistrict.code);
                            populateSelect(villageSelect, villages, config.locationIds.villageId);
                            villageSelect.disabled = false;
                        }
                    }
                }
            }

            
            provinceSelect.addEventListener('change', handleProvinceChange);
            districtSelect.addEventListener('change', handleDistrictChange);
            subDistrictSelect.addEventListener('change', handleSubDistrictChange);

            
            if (config.locationIds.provinceId) {
              
                (async function initLocationDropdowns() {
                    
                    const provinces = await api.fetchProvinces();
                    populateSelect(provinceSelect, provinces, config.locationIds.provinceId);

                    if (config.locationIds.districtId) {
                        await handleProvinceChange();

                        if (config.locationIds.subDistrictId) {
                            await handleDistrictChange();
                        
                            if (config.locationIds.villageId) {
                                await handleSubDistrictChange();
                            }
                        }
                    }
                })();
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const tagLatInput = document.getElementById('tag_lat');
            const tagLngInput = document.getElementById('tag_lng');
            const tagLokasiInput = document.getElementById('tag_lokasi');

            function updateTagLokasi() {
                const lat = tagLatInput ? tagLatInput.value.trim() : '';
                const lng = tagLngInput ? tagLngInput.value.trim() : '';

                if (lat && lng && tagLokasiInput) {
                    tagLokasiInput.value = `${lat}, ${lng}`;
                    console.log('Tag lokasi updated:', tagLokasiInput.value);
                } else if (tagLokasiInput) {
                    tagLokasiInput.value = '';
                }
            }

            if (tagLatInput) {
                tagLatInput.addEventListener('change', updateTagLokasi);
                tagLatInput.addEventListener('input', updateTagLokasi);
            }

            if (tagLngInput) {
                tagLngInput.addEventListener('change', updateTagLokasi);
                tagLngInput.addEventListener('input', updateTagLokasi);
            }

            updateTagLokasi();

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    updateTagLokasi();
                    
                });
            }
        });
    </script>

    
  
</x-layout>