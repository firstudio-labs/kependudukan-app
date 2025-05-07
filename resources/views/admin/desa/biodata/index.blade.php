<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Biodata</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data biodata..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <div class="flex space-x-2">



                <!-- Tombol Export -->
                <form method="GET" action="{{ route('admin.desa.biodata.index') }}">
                    @csrf
                    <input type="hidden" name="export" value="1">
                    <button type="submit"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </form>

                <button type="button" onclick="window.location.href='{{ route('admin.desa.biodata.create') }}'"
                    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data Biodata</span>
                </button>



            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">SHDK</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pagination = $citizens['data']['pagination'] ?? [
                            'current_page' => 1,
                            'items_per_page' => 10,
                            'total_items' => 0
                        ];

                        $currentPage = $pagination['current_page'];
                        $itemsPerPage = $pagination['items_per_page'];
                        $totalItems = $pagination['total_items'];
                        $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
                        $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
                    @endphp
                    @forelse($citizens['data']['citizens'] ?? [] as $index => $citizen)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $startNumber + $index }}</th>
                            <td class="px-6 py-4">{{ $citizen['nik'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['full_name'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['address'] }}</td>
                            <td class="px-6 py-4">{{ $citizen['family_status'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($citizen['status'] == 'Active') bg-green-100 text-green-800
                                    @elseif($citizen['status'] == 'Inactive') bg-yellow-100 text-yellow-800
                                    @elseif($citizen['status'] == 'Deceased') bg-red-100 text-red-800
                                    @elseif($citizen['status'] == 'Moved') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $citizen['status'] ?? 'Active' }}
                                </span>
                            </td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <button onclick="showDetailModal({{ json_encode($citizen) }})"
                                    class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('admin.desa.biodata.edit', ['nik' => $citizen['nik'], 'page' => $currentPage]) }}"
                                    class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form
                                    action="{{ route('admin.desa.biodata.destroy', ['id' => $citizen['nik'], 'page' => $currentPage]) }}"
                                    method="POST" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($citizens['data']['pagination']) && $citizens['data']['pagination']['total_page'] > 0)
                @php
                    // Create a proper pagination object that the component can use
                    $paginationData = new \Illuminate\Pagination\LengthAwarePaginator(
                        $citizens['data']['citizens'] ?? [],
                        $citizens['data']['pagination']['total_items'] ?? 0,
                        $citizens['data']['pagination']['items_per_page'] ?? 10,
                        $citizens['data']['pagination']['current_page'] ?? 1,
                        [
                            'path' => request()->url(),
                            'query' => request()->query(),
                        ]
                    );
                @endphp

                <x-pagination :data="$paginationData" />
            @else
                <div class="px-4 py-3 text-sm text-gray-700">
                    Showing 0 results
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Modal Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-[#7886C7] bg-gray-50">
                    <h3 class="text-xl font-semibold text-[#2D336B]">Detail Biodata</h3>
                    <button onclick="closeDetailModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi Pribadi -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Pribadi</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK</span>
                                    <span id="detailNIK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nomor KK</span>
                                    <span id="detailKK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                                    <span id="detailFullName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Jenis Kelamin</span>
                                    <span id="detailGender" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tempat, Tanggal Lahir</span>
                                    <span class="font-medium">
                                        <span id="detailBirthPlace"></span>,
                                        <span id="detailBirthDate"></span>
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Usia</span>
                                    <span id="detailAge" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Alamat -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Alamat</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat Lengkap</span>
                                    <span id="detailAddress" class="font-medium"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">RT</span>
                                        <span id="detailRT" class="font-medium"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">RW</span>
                                        <span id="detailRW" class="font-medium"></span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Desa/Kelurahan</span>
                                    <span id="detailVillageId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kecamatan</span>
                                    <span id="detailSubDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kabupaten/Kota</span>
                                    <span id="detailDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Provinsi</span>
                                    <span id="detailProvinceId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos</span>
                                    <span id="detailPostalCode" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Lainnya -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Lainnya</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status Kewarganegaraan</span>
                                    <span id="detailCitizenStatus" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Agama</span>
                                    <span id="detailReligion" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Golongan Darah</span>
                                    <span id="detailBloodType" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status Pendidikan</span>
                                    <span id="detailEducationStatus" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Pekerjaan</span>
                                    <span id="detailJobName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Status dalam Keluarga</span>
                                    <span id="detailFamilyStatus" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Orangtua -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Orangtua</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Ayah</span>
                                    <span id="detailFather" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK Ayah</span>
                                    <span id="detailNikFather" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Ibu</span>
                                    <span id="detailMother" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">NIK Ibu</span>
                                    <span id="detailNikMother" class="font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button onclick="closeDetailModal()" type="button"
                        class="text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File name display for Excel import
            const fileInput = document.getElementById('excel_file');
            const fileNameDisplay = document.getElementById('file-name');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (fileInput.files.length > 0) {
                        fileNameDisplay.textContent = 'File selected: ' + fileInput.files[0].name;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });
            }

            // Kode SweetAlert yang lebih bagus dan lengkap
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    iconColor: '#28a745',
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#2D336B',
                    position: 'center',
                    iconColor: '#dc3545',
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: "{{ session('warning') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    iconColor: '#ffc107',
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            @endif

            @if (session('import_errors'))
                Swal.fire({
                    icon: 'error',
                    title: 'Import Error',
                    html: "{!! session('import_errors') !!}",
                    confirmButtonColor: '#2D336B',
                });
            @endif
        });

        // API config
        const baseUrl = 'https://api-kependudukan.desaverse.id/api';
        const apiKey = '{{ config('services.kependudukan.key') }}';

        // Cache for location names and codes
        const locationCache = {};

        // Function to fetch location data based on ID and type
        async function fetchLocationData(type, id) {
            if (!id) return null;

            // Check cache first
            const cacheKey = `${type}_${id}`;
            if (locationCache[cacheKey]) {
                return locationCache[cacheKey];
            }

            try {
                switch (type) {
                    case 'province':
                        // For provinces, directly fetch all provinces and find by ID
                        const provResponse = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (provResponse.data && provResponse.data.data) {
                            const province = provResponse.data.data.find(p => String(p.id) === String(id));
                            if (province) {
                                locationCache[cacheKey] = province; // Cache the entire province object
                                return province;
                            }
                        }
                        break;

                    case 'district':
                        // For districts, need to iterate through provinces to find the district
                        const provincesResponse = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (provincesResponse.data && provincesResponse.data.data) {
                            for (const province of provincesResponse.data.data) {
                                try {
                                    const distResponse = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (distResponse.data && distResponse.data.data) {
                                        const district = distResponse.data.data.find(d => String(d.id) === String(id));
                                        if (district) {
                                            district.province = province; // Add parent province reference
                                            locationCache[cacheKey] = district;
                                            return district;
                                        }
                                    }
                                } catch (e) {
                                    // Continue to next province
                                    continue;
                                }
                            }
                        }
                        break;

                    case 'subdistrict':
                        // For subdistricts, first need to find the district
                        // If we have the parent district_id, use it to narrow down search
                        let parentDistrictData = null;
                        const parentDistrictId = arguments[2]; // Optional parent district ID

                        if (parentDistrictId) {
                            parentDistrictData = await fetchLocationData('district', parentDistrictId);
                        }

                        if (parentDistrictData) {
                            // If we found the parent district, search within it
                            try {
                                const subdistResponse = await axios.get(
                                    `${baseUrl}/sub-districts/${parentDistrictData.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                if (subdistResponse.data && subdistResponse.data.data) {
                                    const subdistrict = subdistResponse.data.data.find(sd => String(sd.id) === String(
                                        id));
                                    if (subdistrict) {
                                        subdistrict.district = parentDistrictData; // Add parent district reference
                                        locationCache[cacheKey] = subdistrict;
                                        return subdistrict;
                                    }
                                }
                            } catch (e) {
                                // If error, continue to full search
                            }
                        }

                        // If no parent district or not found within parent, search all provinces and districts
                        const allProvincesForSubdist = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (allProvincesForSubdist.data && allProvincesForSubdist.data.data) {
                            for (const province of allProvincesForSubdist.data.data) {
                                try {
                                    const districtsInProvince = await axios.get(
                                        `${baseUrl}/districts/${province.code}`, {
                                            headers: {
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json',
                                                'X-API-Key': apiKey
                                            }
                                        });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(
                                                    `${baseUrl}/sub-districts/${district.code}`, {
                                                        headers: {
                                                            'Accept': 'application/json',
                                                            'Content-Type': 'application/json',
                                                            'X-API-Key': apiKey
                                                        }
                                                    });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    const subdistrict = subdistrictsInDistrict.data.data.find(sd =>
                                                        String(sd.id) === String(id));
                                                    if (subdistrict) {
                                                        district.province = province;
                                                        subdistrict.district = district;
                                                        locationCache[cacheKey] = subdistrict;
                                                        return subdistrict;
                                                    }
                                                }
                                            } catch (e) {
                                                // Continue to next district
                                                continue;
                                            }
                                        }
                                    }
                                } catch (e) {
                                    // Continue to next province
                                    continue;
                                }
                            }
                        }
                        break;

                    case 'village':
                        // For villages, first try with parent subdistrict if available
                        const parentSubdistrictId = arguments[2]; // Optional parent subdistrict ID
                        let parentSubdistrictData = null;

                        if (parentSubdistrictId) {
                            const parentDistrictId = arguments[3]; // Optional parent district ID
                            parentSubdistrictData = await fetchLocationData('subdistrict', parentSubdistrictId,
                                parentDistrictId);
                        }

                        if (parentSubdistrictData) {
                            // If we found the parent subdistrict, search within it
                            try {
                                const villageResponse = await axios.get(
                                    `${baseUrl}/villages/${parentSubdistrictData.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                if (villageResponse.data && villageResponse.data.data) {
                                    const village = villageResponse.data.data.find(v => String(v.id) === String(id));
                                    if (village) {
                                        village.subdistrict = parentSubdistrictData; // Add parent subdistrict reference
                                        locationCache[cacheKey] = village;
                                        return village;
                                    }
                                }
                            } catch (e) {
                                // If error, continue to full search
                            }
                        }

                        // If no parent subdistrict or not found within parent, search all locations
                        const allProvincesForVillage = await axios.get(`${baseUrl}/provinces`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-API-Key': apiKey
                            }
                        });

                        if (allProvincesForVillage.data && allProvincesForVillage.data.data) {
                            for (const province of allProvincesForVillage.data.data) {
                                try {
                                    const districtsInProvince = await axios.get(
                                        `${baseUrl}/districts/${province.code}`, {
                                            headers: {
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json',
                                                'X-API-Key': apiKey
                                            }
                                        });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(
                                                    `${baseUrl}/sub-districts/${district.code}`, {
                                                        headers: {
                                                            'Accept': 'application/json',
                                                            'Content-Type': 'application/json',
                                                            'X-API-Key': apiKey
                                                        }
                                                    });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    for (const subdistrict of subdistrictsInDistrict.data.data) {
                                                        try {
                                                            const villagesInSubdistrict = await axios.get(
                                                                `${baseUrl}/villages/${subdistrict.code}`, {
                                                                    headers: {
                                                                        'Accept': 'application/json',
                                                                        'Content-Type': 'application/json',
                                                                        'X-API-Key': apiKey
                                                                    }
                                                                });

                                                            if (villagesInSubdistrict.data && villagesInSubdistrict.data
                                                                .data) {
                                                                const village = villagesInSubdistrict.data.data.find(
                                                                    v => String(v.id) === String(id));
                                                                if (village) {
                                                                    district.province = province;
                                                                    subdistrict.district = district;
                                                                    village.subdistrict = subdistrict;
                                                                    locationCache[cacheKey] = village;
                                                                    return village;
                                                                }
                                                            }
                                                        } catch (e) {
                                                            // Continue to next subdistrict
                                                            continue;
                                                        }
                                                    }
                                                }
                                            } catch (e) {
                                                // Continue to next district
                                                continue;
                                            }
                                        }
                                    }
                                } catch (e) {
                                    // Continue to next province
                                    continue;
                                }
                            }
                        }
                        break;
                }

                return null;
            } catch (error) {
                console.error("Error fetching location data:", error);
                return null;
            }
        }

        // Delete confirmation function dengan SweetAlert yang lebih baik
        function confirmDelete(event) {
            event.preventDefault(); // Menghentikan pengiriman form default
            const form = event.target; // Form yang akan di-submit

            Swal.fire({
                title: 'Konfirmasi Hapus Data',
                text: "Data biodata yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2D336B',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Ya, hapus!',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Batal',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                backdrop: `rgba(0,0,23,0.4)`,
                reverseButtons: true,
                focusConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading state saat menghapus
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    form.submit();
                }
            });

            return false; // Menghentikan pengiriman form
        }

        // Updated showDetailModal function dengan loading state
        async function showDetailModal(biodata) {
            // Tampilkan modal dengan loading state
            document.getElementById('detailModal').classList.remove('hidden');

            // Tampilkan loading pada modal
            const modalBody = document.querySelector('#detailModal .p-4.md\\:p-5.overflow-y-auto');
            const originalContent = modalBody.innerHTML;

            modalBody.innerHTML = `
                <div class="flex justify-center items-center py-10">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-[#7886C7]"></div>
                </div>
                <div class="text-center text-gray-500 mt-3">Memuat data detail...</div>
            `;

            // Konversi data sebelum ditampilkan
            const genderMap = {
                '1': 'Laki-laki',
                '2': 'Perempuan'
            };
            const citizenStatusMap = {
                '1': 'WNI',
                '2': 'WNA'
            };
            const bloodTypeMap = {
                '1': 'A',
                '2': 'B',
                '3': 'AB',
                '4': 'O',
                '5': 'A+',
                '6': 'A-',
                '7': 'B+',
                '8': 'B-',
                '9': 'AB+',
                '10': 'AB-',
                '11': 'O+',
                '12': 'O-',
                '13': 'Tidak Tahu'
            };
            const religionMap = {
                '1': 'Islam',
                '2': 'Kristen',
                '3': 'Katolik',
                '4': 'Hindu',
                '5': 'Buddha',
                '6': 'Konghucu',
                '7': 'Kepercayaan Terhadap Tuhan YME'
            };
            const educationStatusMap = {
                '1': 'Tidak/Belum Sekolah',
                '2': 'Belum Tamat SD/Sederajat',
                '3': 'Tamat SD/Sederajat',
                '4': 'SLTP/Sederajat',
                '5': 'SLTA/Sederajat',
                '6': 'Diploma I/II',
                '7': 'Akademi/Diploma III/S. Muda',
                '8': 'Diploma IV/Strata I',
                '9': 'Strata II',
                '10': 'Strata III'
            };
            const familyStatusMap = {
                '1': 'Anak',
                '2': 'Kepala Keluarga',
                '3': 'Istri',
                '4': 'Orang Tua',
                '5': 'Mertua',
                '6': 'Cucu',
                '7': 'Famili Lain'
            };

            try {
                // Siapkan semua data yang perlu di-fetch
                const promises = [];

                // Tambahkan fetch location data promises
                if (biodata.province_id)
                    promises.push(fetchLocationData('province', biodata.province_id));
                else
                    promises.push(null);

                if (biodata.district_id)
                    promises.push(fetchLocationData('district', biodata.district_id));
                else
                    promises.push(null);

                if (biodata.sub_district_id)
                    promises.push(fetchLocationData('subdistrict', biodata.sub_district_id, biodata.district_id));
                else
                    promises.push(null);

                if (biodata.village_id)
                    promises.push(fetchLocationData('village', biodata.village_id, biodata.sub_district_id, biodata
                        .district_id));
                else
                    promises.push(null);

                // Add job data promise
                if (biodata.job_type_id) {
                    const jobPromise = axios.get(`${baseUrl}/jobs`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-API-Key': apiKey
                        }
                    }).then(response => {
                        if (response.data && response.data.data) {
                            return response.data.data.find(j => String(j.id) === String(biodata.job_type_id)) ||
                                null;
                        }
                        return null;
                    }).catch(error => {
                        console.error("Error fetching job data:", error);
                        return null;
                    });
                    promises.push(jobPromise);
                } else {
                    promises.push(null);
                }

                // Wait for all promises to resolve
                const [provinceData, districtData, subdistrictData, villageData, jobData] = await Promise.all(promises);

                // Kembalikan konten asli modal
                modalBody.innerHTML = originalContent;

                // Set values dengan konversi
                document.getElementById('detailGender').innerText = genderMap[biodata.gender] || biodata.gender || '-';
                document.getElementById('detailCitizenStatus').innerText = citizenStatusMap[biodata.citizen_status] ||
                    biodata.citizen_status || '-';
                document.getElementById('detailBloodType').innerText = bloodTypeMap[biodata.blood_type] ||
                    biodata.blood_type || '-';
                document.getElementById('detailReligion').innerText = religionMap[biodata.religion] ||
                    biodata.religion || '-';
                document.getElementById('detailEducationStatus').innerText = educationStatusMap[biodata
                        .education_status] ||
                    biodata.education_status || '-';
                document.getElementById('detailFamilyStatus').innerText = familyStatusMap[biodata.family_status] ||
                    biodata.family_status || '-';

                // Improved format date function that handles more date formats
                const formatDate = (dateStr) => {
                    if (!dateStr || dateStr === " " || dateStr === "null") return '-';

                    // Try to detect the format and parse the date correctly
                    try {
                        let date;

                        // Check if dateStr is in dd/MM/yyyy format
                        if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
                            const parts = dateStr.split('/');
                            // Create date with format: year, month (0-based), day
                            date = new Date(
                                parseInt(parts[2]),
                                parseInt(parts[1]) - 1,
                                parseInt(parts[0])
                            );
                        }
                        // Check if dateStr is in yyyy-MM-dd format
                        else if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                            const parts = dateStr.split('-');
                            date = new Date(
                                parseInt(parts[0]),
                                parseInt(parts[1]) - 1,
                                parseInt(parts[2])
                            );
                        }
                        // Otherwise try standard date parsing
                        else {
                            date = new Date(dateStr);
                        }

                        // Verify that the date is valid
                        if (isNaN(date.getTime())) {
                            console.error('Invalid date after parsing:', dateStr);
                            return '-';
                        }

                        // Format the date in Indonesian locale
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    } catch (error) {
                        console.error('Error formatting date:', error, dateStr);
                        return '-';
                    }
                };

                // Set nilai-nilai lainnya
                document.getElementById('detailNIK').innerText = biodata.nik || '-';
                document.getElementById('detailKK').innerText = biodata.kk || '-';
                document.getElementById('detailFullName').innerText = biodata.full_name || '-';
                document.getElementById('detailBirthDate').innerText = formatDate(biodata.birth_date);
                document.getElementById('detailAge').innerText = biodata.age ? `${biodata.age} Tahun` : '-';
                document.getElementById('detailBirthPlace').innerText = biodata.birth_place || '-';

                // Set basic address data
                document.getElementById('detailAddress').innerText = biodata.address || '-';
                document.getElementById('detailRT').innerText = biodata.rt || '-';
                document.getElementById('detailRW').innerText = biodata.rw || '-';
                document.getElementById('detailPostalCode').innerText = biodata.postal_code || '-';

                // Set data orangtua
                document.getElementById('detailFather').innerText = biodata.father || '-';
                document.getElementById('detailNikFather').innerText = biodata.nik_father || '-';
                document.getElementById('detailMother').innerText = biodata.mother || '-';
                document.getElementById('detailNikMother').innerText = biodata.nik_mother || '-';

                // Set job data
                document.getElementById('detailJobName').innerText = jobData ? jobData.name : (biodata.job_type_id ||
                    '-');

                // Set location data
                document.getElementById('detailProvinceId').innerText = provinceData ? provinceData.name : (biodata
                    .province_id || '-');
                document.getElementById('detailDistrictId').innerText = districtData ? districtData.name : (biodata
                    .district_id || '-');
                document.getElementById('detailSubDistrictId').innerText = subdistrictData ? subdistrictData.name : (
                    biodata.sub_district_id || '-');
                document.getElementById('detailVillageId').innerText = villageData ? villageData.name : (biodata
                    .village_id || '-');

            } catch (error) {
                console.error("Error displaying detail modal:", error);

                // Restore modal content on error
                modalBody.innerHTML = originalContent;

                // Set basic values in case of error
                document.getElementById('detailNIK').innerText = biodata.nik || '-';
                document.getElementById('detailKK').innerText = biodata.kk || '-';
                document.getElementById('detailFullName').innerText = biodata.full_name || '-';
                document.getElementById('detailAddress').innerText = biodata.address || '-';
                document.getElementById('detailProvinceId').innerText = biodata.province_id || '-';
                document.getElementById('detailDistrictId').innerText = biodata.district_id || '-';
                document.getElementById('detailSubDistrictId').innerText = biodata.sub_district_id || '-';
                document.getElementById('detailVillageId').innerText = biodata.village_id || '-';

                // Tampilkan pesan error jika gagal memuat data
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat detail. Silahkan coba lagi.',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                });
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>

    <style>
        /* Custom styles for SweetAlert toasts */
        .colored-toast.swal2-icon-success {
            background-color: #f0fdf4 !important;
            border-left: 4px solid #28a745 !important;
        }

        .colored-toast.swal2-icon-error {
            background-color: #fef2f2 !important;
            border-left: 4px solid #dc3545 !important;
        }

        .colored-toast.swal2-icon-warning {
            background-color: #fffbeb !important;
            border-left: 4px solid #ffc107 !important;
        }

        .colored-toast .swal2-title {
            color: #333 !important;
        }

        /* Tambahkan link ke animate.css jika belum ada */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
    </style>
</x-layout>
