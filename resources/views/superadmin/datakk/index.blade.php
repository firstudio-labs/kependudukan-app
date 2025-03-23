<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Data KK</h1>

        <!-- Bar untuk Search dan Tambah Pasien -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
            <form method="GET" action="" class="relative">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data kk..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            {{-- <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.datakk.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Data KK</span>
            </button> --}}
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">No KK</th>
                        <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3">Alamat</th>
                        <th scope="col" class="px-6 py-3">Jumlah Anggota Keluarga</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pagination = $kk['data']['pagination'] ?? [
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
                    @forelse($kk['data']['citizens'] ?? [] as $index => $k)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $startNumber + $index }}</th>
                        <td class="px-6 py-4">{{ $k['kk'] }}</td>
                        <td class="px-6 py-4">{{ $k['full_name'] }}</td>
                        <td class="px-6 py-4">{{ $k['address'] }}</td>
                        <td class="px-6 py-4">{{ $k['jml_anggota_kk'] ?? '-' }}</td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            <button onclick="showDetailModal({{ json_encode($k) }})" class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            {{-- <a href="{{ route('superadmin.datakk.update', $k['id'] ?? $k['kk']) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.destroy', $k['id'] ?? $k['kk']) }}" method="POST" class="delete-form" id="delete-form-{{ $k['id'] ?? $k['kk'] }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form> --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if(isset($kk['data']['pagination']) && $kk['data']['pagination']['total_page'] > 1)
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        @php
                            $totalPages = $kk['data']['pagination']['total_page'];
                            $currentPage = $kk['data']['pagination']['current_page'];

                            // Logic for showing page numbers
                            $startPage = 1;
                            $endPage = $totalPages;
                            $maxVisible = 7; // Number of visible page links excluding Previous/Next

                            if ($totalPages > $maxVisible) {
                                $halfVisible = floor($maxVisible / 2);
                                $startPage = max($currentPage - $halfVisible, 1);
                                $endPage = min($startPage + $maxVisible - 1, $totalPages);

                                if ($endPage - $startPage < $maxVisible - 1) {
                                    $startPage = max($endPage - $maxVisible + 1, 1);
                                }
                            }
                        @endphp

                        <!-- Previous Button -->
                        @if($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}&search={{ request('search') }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                Previous
                            </a>
                        @endif

                        <!-- First Page -->
                        @if($startPage > 1)
                            <a href="?page=1&search={{ request('search') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($startPage > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif

                        <!-- Page Numbers -->
                        @for($i = $startPage; $i <= $endPage; $i++)
                            <a href="?page={{ $i }}&search={{ request('search') }}"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium
                               {{ $i == $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-[#8c93d6]' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        <!-- Last Page -->
                        @if($endPage < $totalPages)
                            @if($endPage < $totalPages - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="?page={{ $totalPages }}&search={{ request('search') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $totalPages }}
                            </a>
                        @endif

                        <!-- Next Button -->
                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}&search={{ request('search') }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                Next
                            </a>
                        @endif
                    </nav>
                @endif
            </div>
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
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-[#7886C7] bg-gray-50">
                    <h3 class="text-xl font-semibold text-[#2D336B]">Detail Data KK</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Informasi KK -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi KK</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nomor KK</span>
                                    <span id="detailKK" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                                    <span id="detailFullName" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Jumlah Anggota KK</span>
                                    <span id="detailJmlAnggota" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Email</span>
                                    <span id="detailEmail" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Telepon</span>
                                    <span id="detailTelepon" class="font-medium"></span>
                                </div>
                            </div>
                        </div>



                        <!-- Informasi Alamat -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Alamat</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat</span>
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
                                    <span class="text-sm text-gray-500">Dusun</span>
                                    <span id="detailDusun" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Provinsi</span>
                                    <span id="detailProvinceId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kabupaten/Kota</span>
                                    <span id="detailDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kecamatan</span>
                                    <span id="detailSubDistrictId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Desa/Kelurahan</span>
                                    <span id="detailVillageId" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos</span>
                                    <span id="detailPostalCode" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Luar Negeri (jika ada) -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Luar Negeri</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Alamat Luar Negeri</span>
                                    <span id="detailAlamatLuarNegeri" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kota</span>
                                    <span id="detailKota" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Negara Bagian</span>
                                    <span id="detailNegaraBagian" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Negara</span>
                                    <span id="detailNegara" class="font-medium"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Kode Pos Luar Negeri</span>
                                    <span id="detailKodePosLuarNegeri" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg mt-4">
                            <h4 class="text-lg font-semibold mb-4 text-[#7886C7]">Informasi Anggota Keluarga</h4>
                            <div id="familyMembersLoading" class="text-center py-4">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#7886C7]"></div>
                                <p class="mt-2 text-gray-600">Memuat data anggota keluarga...</p>
                            </div>
                            <div id="familyMembersError" class="hidden text-center py-4 text-red-500">
                                Gagal memuat data anggota keluarga
                            </div>
                            <div id="familyMembersEmpty" class="hidden text-center py-4 text-gray-500">
                                Tidak ada data anggota keluarga
                            </div>
                            <div id="familyMembersContainer" class="hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Keluarga</th>
                                            </tr>
                                        </thead>
                                        <tbody id="familyMembersTable" class="bg-white divide-y divide-gray-200">
                                            <!-- Family members will be inserted here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button onclick="closeDetailModal()" type="button" class="text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
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

        // API config
        const baseUrl = 'http://api-kependudukan.desaverse.id:3000/api';
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
                switch(type) {
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
                                const subdistResponse = await axios.get(`${baseUrl}/sub-districts/${parentDistrictData.code}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-API-Key': apiKey
                                    }
                                });

                                if (subdistResponse.data && subdistResponse.data.data) {
                                    const subdistrict = subdistResponse.data.data.find(sd => String(sd.id) === String(id));
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
                                    const districtsInProvince = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(`${baseUrl}/sub-districts/${district.code}`, {
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json',
                                                        'X-API-Key': apiKey
                                                    }
                                                });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    const subdistrict = subdistrictsInDistrict.data.data.find(sd => String(sd.id) === String(id));
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
                            parentSubdistrictData = await fetchLocationData('subdistrict', parentSubdistrictId, parentDistrictId);
                        }

                        if (parentSubdistrictData) {
                            // If we found the parent subdistrict, search within it
                            try {
                                const villageResponse = await axios.get(`${baseUrl}/villages/${parentSubdistrictData.code}`, {
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
                                    const districtsInProvince = await axios.get(`${baseUrl}/districts/${province.code}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-API-Key': apiKey
                                        }
                                    });

                                    if (districtsInProvince.data && districtsInProvince.data.data) {
                                        for (const district of districtsInProvince.data.data) {
                                            try {
                                                const subdistrictsInDistrict = await axios.get(`${baseUrl}/sub-districts/${district.code}`, {
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json',
                                                        'X-API-Key': apiKey
                                                    }
                                                });

                                                if (subdistrictsInDistrict.data && subdistrictsInDistrict.data.data) {
                                                    for (const subdistrict of subdistrictsInDistrict.data.data) {
                                                        try {
                                                            const villagesInSubdistrict = await axios.get(`${baseUrl}/villages/${subdistrict.code}`, {
                                                                headers: {
                                                                    'Accept': 'application/json',
                                                                    'Content-Type': 'application/json',
                                                                    'X-API-Key': apiKey
                                                                }
                                                            });

                                                            if (villagesInSubdistrict.data && villagesInSubdistrict.data.data) {
                                                                const village = villagesInSubdistrict.data.data.find(v => String(v.id) === String(id));
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
                return null;
            }
        }

        // Delete confirmation function
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listeners to all delete forms
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Apakah anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#2D336B',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });

        function closeAlert() {
            document.getElementById('success-alert')?.classList.add('opacity-0');
            document.getElementById('error-alert')?.classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('success-alert')?.remove();
                document.getElementById('error-alert')?.remove();
            }, 500);
        }
        setTimeout(closeAlert, 4000);

        async function showDetailModal(data) {
            // Set basic values to modal
            document.getElementById('detailKK').innerText = data.kk || '-';
            document.getElementById('detailFullName').innerText = data.full_name || '-';
            document.getElementById('detailJmlAnggota').innerText = data.jml_anggota_kk || '-';
            document.getElementById('detailEmail').innerText = data.email || '-';
            document.getElementById('detailTelepon').innerText = data.telepon || '-';
            document.getElementById('detailAddress').innerText = data.address || '-';
            document.getElementById('detailRT').innerText = data.rt || '-';
            document.getElementById('detailRW').innerText = data.rw || '-';
            document.getElementById('detailDusun').innerText = data.dusun || '-';
            document.getElementById('detailPostalCode').innerText = data.postal_code || '-';
            document.getElementById('detailAlamatLuarNegeri').innerText = data.alamat_luar_negeri || '-';
            document.getElementById('detailKota').innerText = data.kota || '-';
            document.getElementById('detailNegaraBagian').innerText = data.negara_bagian || '-';
            document.getElementById('detailNegara').innerText = data.negara || '-';
            document.getElementById('detailKodePosLuarNegeri').innerText = data.kode_pos_luar_negeri || '-';

            // Show loading indicators for location data
            document.getElementById('detailProvinceId').innerText = 'Memuat...';
            document.getElementById('detailDistrictId').innerText = 'Memuat...';
            document.getElementById('detailSubDistrictId').innerText = 'Memuat...';
            document.getElementById('detailVillageId').innerText = 'Memuat...';

            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Reset family members section
            document.getElementById('familyMembersLoading').classList.remove('hidden');
            document.getElementById('familyMembersContainer').classList.add('hidden');
            document.getElementById('familyMembersEmpty').classList.add('hidden');
            document.getElementById('familyMembersError').classList.add('hidden');

            // Fetch family members directly using the KK number instead of ID
            if (data.kk) {
                fetchFamilyMembers(data.kk);
            } else {
                document.getElementById('familyMembersLoading').classList.add('hidden');
                document.getElementById('familyMembersEmpty').classList.remove('hidden');
            }

            // Fetch and set location names
            try {
                // Fetch province data
                const provinceData = await fetchLocationData('province', data.province_id);
                if (provinceData) {
                    document.getElementById('detailProvinceId').innerText = provinceData.name || data.province_id;
                } else {
                    document.getElementById('detailProvinceId').innerText = data.province_id || '-';
                }

                // Fetch district data
                const districtData = await fetchLocationData('district', data.district_id);
                if (districtData) {
                    document.getElementById('detailDistrictId').innerText = districtData.name || data.district_id;
                } else {
                    document.getElementById('detailDistrictId').innerText = data.district_id || '-';
                }

                // Fetch subdistrict data using district as parent for optimization
                const subdistrictData = await fetchLocationData('subdistrict', data.sub_district_id, data.district_id);
                if (subdistrictData) {
                    document.getElementById('detailSubDistrictId').innerText = subdistrictData.name || data.sub_district_id;
                } else {
                    document.getElementById('detailSubDistrictId').innerText = data.sub_district_id || '-';
                }

                // Fetch village data using subdistrict and district as parents for optimization
                const villageData = await fetchLocationData('village', data.village_id, data.sub_district_id, data.district_id);
                if (villageData) {
                    document.getElementById('detailVillageId').innerText = villageData.name || data.village_id;
                } else {
                    document.getElementById('detailVillageId').innerText = data.village_id || '-';
                }
            } catch (error) {
                // If API calls fail, fall back to IDs
                document.getElementById('detailProvinceId').innerText = data.province_id || '-';
                document.getElementById('detailDistrictId').innerText = data.district_id || '-';
                document.getElementById('detailSubDistrictId').innerText = data.sub_district_id || '-';
                document.getElementById('detailVillageId').innerText = data.village_id || '-';
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function fetchFamilyMembers(kkNumber) {
            // Check if kkNumber is valid
            if (!kkNumber) {
                document.getElementById('familyMembersLoading').classList.add('hidden');
                document.getElementById('familyMembersEmpty').classList.remove('hidden');
                return;
            }

            // Directly call the API endpoint with our API key
            axios.get(`${baseUrl}/citizens-family/${kkNumber}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-API-Key': apiKey
                }
            })
            .then(response => {
                document.getElementById('familyMembersLoading').classList.add('hidden');

                if (response.data.status === 'OK' && response.data.data && response.data.data.length > 0) {
                    const members = response.data.data;
                    const tableBody = document.getElementById('familyMembersTable');
                    tableBody.innerHTML = '';

                    members.forEach((member, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-4 py-2 whitespace-nowrap">${index + 1}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${member.full_name || '-'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${member.family_status || '-'}</td>
                        `;
                        tableBody.appendChild(row);
                    });

                    document.getElementById('familyMembersContainer').classList.remove('hidden');

                    // Update the count in the KK info section if it wasn't already set
                    const jmlAnggotaElement = document.getElementById('detailJmlAnggota');
                    if (jmlAnggotaElement.innerText === '-') {
                        jmlAnggotaElement.innerText = members.length;
                    }
                } else {
                    document.getElementById('familyMembersEmpty').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching family members:', error);
                document.getElementById('familyMembersLoading').classList.add('hidden');
                document.getElementById('familyMembersError').classList.remove('hidden');
            });
        }
    </script>
</x-layout>
