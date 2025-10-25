<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengguna Mobile</h1>

        <!-- Filter Wilayah -->
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filter Wilayah</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Provinsi -->
                <div>
                    <label for="province_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Provinsi</label>
                    <select name="province_id" id="province_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Provinsi</option>
                        @foreach($provincesList as $province)
                            <option value="{{ $province['id'] }}" {{ $provinceId == $province['id'] ? 'selected' : '' }}>
                                {{ $province['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kabupaten/Kota -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kabupaten/Kota</label>
                    <select name="district_id" id="district_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" {{ !$provinceId ? 'disabled' : '' }}>
                        <option value="">Semua Kabupaten/Kota</option>
                        @if(isset($districts) && count($districts) > 0)
                            @foreach($districts as $district)
                                <option value="{{ $district['id'] }}" {{ $districtId == $district['id'] ? 'selected' : '' }}>
                                    {{ $district['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="sub_district_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kecamatan</label>
                    <select name="sub_district_id" id="sub_district_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" {{ !$districtId ? 'disabled' : '' }}>
                        <option value="">Semua Kecamatan</option>
                        @if(isset($subDistricts) && count($subDistricts) > 0)
                            @foreach($subDistricts as $subDistrict)
                                <option value="{{ $subDistrict['id'] }}" {{ $subDistrictId == $subDistrict['id'] ? 'selected' : '' }}>
                                    {{ $subDistrict['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Desa/Kelurahan -->
                <div>
                    <label for="village_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Desa/Kelurahan</label>
                    <select name="village_id" id="village_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" {{ !$subDistrictId ? 'disabled' : '' }}>
                        <option value="">Semua Desa/Kelurahan</option>
                        @if(isset($villages) && count($villages) > 0)
                            @foreach($villages as $village)
                                <option value="{{ $village['id'] }}" {{ $villageId == $village['id'] ? 'selected' : '' }}>
                                    {{ $village['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">
                        Filter
                    </button>
                    <a href="{{ route('superadmin.mobile-users.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">PROVINSI</th>
                        <th class="px-6 py-3 text-center">JUMLAH PENGGUNA MOBILE</th>
                        <th class="px-6 py-3 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $index => $province)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $province['name'] }}</td>
                            <td class="px-6 py-4 text-center">{{ $province['mobile_users_count'] }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('superadmin.mobile-users.province', $province['id']) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm mr-3">Lihat Kabupaten/Kota</a>
                                <a href="{{ route('superadmin.mobile-users.detail', ['province', $province['id']]) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-gray-500">Tidak ada data provinsi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $provinces->withQueryString()->links() }}
        </div>
    </div>

    <script>
        // JavaScript untuk cascade dropdown wilayah
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const districtSelect = document.getElementById('district_id');
            const subDistrictSelect = document.getElementById('sub_district_id');
            const villageSelect = document.getElementById('village_id');

            // Reset cascade dropdowns
            function resetCascadeDropdowns(startFrom) {
                if (startFrom <= 1) {
                    districtSelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
                    districtSelect.disabled = true;
                    // Reset sub-district dan village juga
                    subDistrictSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                    subDistrictSelect.disabled = true;
                    villageSelect.innerHTML = '<option value="">Semua Desa/Kelurahan</option>';
                    villageSelect.disabled = true;
                }
                if (startFrom <= 2) {
                    subDistrictSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                    subDistrictSelect.disabled = true;
                    villageSelect.innerHTML = '<option value="">Semua Desa/Kelurahan</option>';
                    villageSelect.disabled = true;
                }
                if (startFrom <= 3) {
                    villageSelect.innerHTML = '<option value="">Semua Desa/Kelurahan</option>';
                    villageSelect.disabled = true;
                }
            }

            // Load districts when province changes
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                resetCascadeDropdowns(1);
                
                if (provinceId) {
                    loadDistricts(provinceId);
                }
            });

            // Load sub-districts when district changes
            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                resetCascadeDropdowns(2);
                
                if (districtId) {
                    loadSubDistricts(districtId);
                }
            });

            // Load villages when sub-district changes
            subDistrictSelect.addEventListener('change', function() {
                const subDistrictId = this.value;
                resetCascadeDropdowns(3);
                
                if (subDistrictId) {
                    loadVillages(subDistrictId);
                }
            });

            // Load data untuk dropdown yang sudah dipilih sebelumnya
            if (provinceSelect.value) {
                loadDistricts(provinceSelect.value);
            }
            if (districtSelect.value) {
                loadSubDistricts(districtSelect.value);
            }
            if (subDistrictSelect.value) {
                loadVillages(subDistrictSelect.value);
            }

            // Function untuk load districts
            function loadDistricts(provinceId) {
                // Cari data provinsi untuk mendapatkan code yang benar
                const provinceData = @json($provincesList);
                const selectedProvince = provinceData.find(p => p.id == provinceId);
                
                if (selectedProvince) {
                    const url = `/api/wilayah/kabupaten-by-province?province_code=${selectedProvince.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            districtSelect.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
                            if (Array.isArray(data)) {
                                // Simpan data kabupaten untuk digunakan nanti
                                window.districtData = data;
                                data.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.id;
                                    option.textContent = district.name;
                                    // Pertahankan pilihan yang sudah ada
                                    if (district.id == '{{ $districtId }}') {
                                        option.selected = true;
                                    }
                                    districtSelect.appendChild(option);
                                });
                                // Enable dropdown setelah data di-load
                                districtSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading districts:', error);
                            districtSelect.innerHTML = '<option value="">Error loading kabupaten</option>';
                        });
                }
            }

            // Function untuk load sub-districts
            function loadSubDistricts(districtId) {
                // Cari data kabupaten untuk mendapatkan code yang benar
                // Gunakan data yang sudah di-load via AJAX terlebih dahulu
                let districtData = window.districtData || @json($districts ?? []);
                const selectedDistrict = districtData.find(d => d.id == districtId);
                
                if (selectedDistrict) {
                    const url = `/api/wilayah/kecamatan-by-kabupaten?kabupaten_code=${selectedDistrict.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            subDistrictSelect.innerHTML = '<option value="">Semua Kecamatan</option>';
                            if (Array.isArray(data)) {
                                // Simpan data kecamatan untuk digunakan nanti
                                window.subDistrictData = data;
                                data.forEach(subDistrict => {
                                    const option = document.createElement('option');
                                    option.value = subDistrict.id;
                                    option.textContent = subDistrict.name;
                                    // Pertahankan pilihan yang sudah ada
                                    if (subDistrict.id == '{{ $subDistrictId }}') {
                                        option.selected = true;
                                    }
                                    subDistrictSelect.appendChild(option);
                                });
                                // Enable dropdown setelah data di-load
                                subDistrictSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading sub-districts:', error);
                            subDistrictSelect.innerHTML = '<option value="">Error loading kecamatan</option>';
                        });
                }
            }

            // Function untuk load villages
            function loadVillages(subDistrictId) {
                // Cari data kecamatan untuk mendapatkan code yang benar
                // Gunakan data yang sudah di-load via AJAX terlebih dahulu
                let subDistrictData = window.subDistrictData || @json($subDistricts ?? []);
                const selectedSubDistrict = subDistrictData.find(s => s.id == subDistrictId);
                
                if (selectedSubDistrict) {
                    const url = `/api/wilayah/desa-by-kecamatan?kecamatan_code=${selectedSubDistrict.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            villageSelect.innerHTML = '<option value="">Semua Desa/Kelurahan</option>';
                            if (Array.isArray(data)) {
                                // Simpan data desa untuk digunakan nanti
                                window.villageData = data;
                                data.forEach(village => {
                                    const option = document.createElement('option');
                                    option.value = village.id;
                                    option.textContent = village.name;
                                    // Pertahankan pilihan yang sudah ada
                                    if (village.id == '{{ $villageId }}') {
                                        option.selected = true;
                                    }
                                    villageSelect.appendChild(option);
                                });
                                // Enable dropdown setelah data di-load
                                villageSelect.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading villages:', error);
                            villageSelect.innerHTML = '<option value="">Error loading desa</option>';
                        });
                }
            }
        });
    </script>
</x-layout>