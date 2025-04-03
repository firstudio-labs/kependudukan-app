<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Aset</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data aset..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <div class="flex space-x-2">
                <button type="button" onclick="window.location.href='{{ route('user.kelola-aset.create') }}'"
                    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data Aset</span>
                </button>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Pemilik</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">Klasifikasi</th>
                        <th class="px-6 py-3">Jenis Aset</th>
                        <th class="px-6 py-3">Nama Aset</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pagination = $assets['data']['pagination'] ?? [
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
                    @forelse($assets as $index => $asset)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-6 py-4">{{ $asset->nik_pemilik }}</td>
                            <td class="px-6 py-4">{{ $asset->nama_pemilik }}</td>
                            <td class="px-6 py-4">{{ $asset->address }}</td>
                            <td class="px-6 py-4">{{ $asset->klasifikasi->jenis_klasifikasi ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $asset->jenisAset->jenis_aset ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $asset->nama_aset }}</td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <button onclick="showDetailModal({{ $asset->toJson() }})"
                                    class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('user.kelola-aset.edit', ['id' => $asset->id]) }}"
                                    class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('user.kelola-aset.destroy', ['id' => $asset->id]) }}" method="POST"
                                    onsubmit="return confirmDelete(event)">
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
                            <td colspan="8" class="text-center py-4">Tidak ada data aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if(isset($assets['data']['pagination']) && $assets['data']['pagination']['total_page'] > 1)
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        @php
                            $totalPages = $assets['data']['pagination']['total_page'];
                            $currentPage = $assets['data']['pagination']['current_page'];

                            $startPage = 1;
                            $endPage = $totalPages;
                            $maxVisible = 7; 

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
                            <a href="?page={{ $currentPage - 1 }}&search={{ request('search') }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                Previous
                            </a>
                        @endif

                        <!-- First Page -->
                        @if($startPage > 1)
                            <a href="?page=1&search={{ request('search') }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($startPage > 2)
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
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
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="?page={{ $totalPages }}&search={{ request('search') }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $totalPages }}
                            </a>
                        @endif

                        <!-- Next Button -->
                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}&search={{ request('search') }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Detail Aset
                    </h3>
                    <button type="button" onclick="closeDetailModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 md:p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">NIK Pemilik:</p>
                            <p id="detailNIK" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Nama Pemilik:</p>
                            <p id="detailNamaPemilik" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Nama Aset:</p>
                            <p id="detailNamaAset" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Alamat:</p>
                            <p id="detailAlamat" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Provinsi:</p>
                            <p id="detailProvinsi" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Kabupaten:</p>
                            <p id="detailKabupaten" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Kecamatan:</p>
                            <p id="detailKecamatan" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Desa:</p>
                            <p id="detailDesa" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">RT/RW:</p>
                            <p id="detailRTRW" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Klasifikasi:</p>
                            <p id="detailKlasifikasi" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Jenis Aset:</p>
                            <p id="detailJenisAset" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Tag Lokasi:</p>
                            <p id="detailTagLokasi" class="text-base text-gray-900">-</p>
                        </div>
                    </div>

                    <!-- Foto Aset Section -->
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">Foto Aset:</p>
                        <div id="detailFotoAset" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Images will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200">
                    <button type="button" onclick="closeDetailModal()"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Configuration
            const config = {
                baseUrl: 'http://api-kependudukan.desaverse.id:3000/api',
                apiKey: '{{ config('services.kependudukan.key') }}',
                locationCache: {}
            };

            const api = {
                getHeaders() {
                    return {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-API-Key': config.apiKey
                    };
                },

                async request(url) {
                    try {
                        const response = await axios.get(url, { headers: this.getHeaders() });
                        return response.data?.data || [];
                    } catch (error) {
                        console.error(`API request error (${url}):`, error.message);
                        return [];
                    }
                }
            };

            // Original showDetailModal function
            window.showDetailModal = function (asset) {
                // Use the existing location names if available from the controller
                document.getElementById('detailNIK').textContent = asset.nik_pemilik || '-';
                document.getElementById('detailNamaPemilik').textContent = asset.nama_pemilik || '-';
                document.getElementById('detailNamaAset').textContent = asset.nama_aset || '-';
                document.getElementById('detailAlamat').textContent = asset.address || '-';
                document.getElementById('detailProvinsi').textContent = asset.province_name || 'Memuat...';
                document.getElementById('detailKabupaten').textContent = asset.district_name || 'Memuat...';
                document.getElementById('detailKecamatan').textContent = asset.sub_district_name || 'Memuat...';
                document.getElementById('detailDesa').textContent = asset.village_name || 'Memuat...';
                document.getElementById('detailRTRW').textContent = (asset.rt || '-') + ' / ' + (asset.rw || '-');
                document.getElementById('detailKlasifikasi').textContent = asset.klasifikasi ? asset.klasifikasi.jenis_klasifikasi : '-';
                document.getElementById('detailJenisAset').textContent = asset.jenis_aset ? asset.jenis_aset.jenis_aset : '-';
                document.getElementById('detailTagLokasi').textContent = asset.tag_lokasi || '-';

                // Display asset photos
                const fotoContainer = document.getElementById('detailFotoAset');
                fotoContainer.innerHTML = '';

                if (asset.foto_aset_depan) {
                    const imgDepan = document.createElement('div');
                    imgDepan.innerHTML = `
                        <img src="/storage/${asset.foto_aset_depan}" class="w-full h-auto max-h-48 object-cover rounded" alt="Foto Depan">
                    `;
                    fotoContainer.appendChild(imgDepan);
                }

                if (asset.foto_aset_samping) {
                    const imgSamping = document.createElement('div');
                    imgSamping.innerHTML = `
                        <img src="/storage/${asset.foto_aset_samping}" class="w-full h-auto max-h-48 object-cover rounded" alt="Foto Samping">
                    `;
                    fotoContainer.appendChild(imgSamping);
                }

                if (!asset.foto_aset_depan && !asset.foto_aset_samping) {
                    fotoContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada foto</p>';
                }

                // If location names are missing, fetch them
                if (!asset.province_name || !asset.district_name || !asset.sub_district_name || !asset.village_name) {
                    fetchLocationNames(asset);
                }

                // Show the modal
                document.getElementById('detailModal').classList.remove('hidden');
            };

            // Function to fetch location names
            async function fetchLocationNames(asset) {
                if (asset.province_id) {
                    try {
                        // Get province name
                        const provinces = await api.request(`${config.baseUrl}/provinces`);
                        const province = provinces.find(p => p.id == asset.province_id);
                        if (province) {
                            document.getElementById('detailProvinsi').textContent = province.name;
                            asset.province_name = province.name;
                        }

                        if (asset.district_id) {
                            // Get district name
                            const districts = await api.request(`${config.baseUrl}/districts/${province.code}`);
                            const district = districts.find(d => d.id == asset.district_id);
                            if (district) {
                                document.getElementById('detailKabupaten').textContent = district.name;
                                asset.district_name = district.name;

                                if (asset.sub_district_id) {
                                    // Get sub-district name
                                    const subDistricts = await api.request(`${config.baseUrl}/sub-districts/${district.code}`);
                                    const subDistrict = subDistricts.find(sd => sd.id == asset.sub_district_id);
                                    if (subDistrict) {
                                        document.getElementById('detailKecamatan').textContent = subDistrict.name;
                                        asset.sub_district_name = subDistrict.name;

                                        if (asset.village_id) {
                                            // Get village name
                                            const villages = await api.request(`${config.baseUrl}/villages/${subDistrict.code}`);
                                            const village = villages.find(v => v.id == asset.village_id);
                                            if (village) {
                                                document.getElementById('detailDesa').textContent = village.name;
                                                asset.village_name = village.name;
                                            } else {
                                                document.getElementById('detailDesa').textContent = 'Data tidak tersedia';
                                            }
                                        }
                                    } else {
                                        document.getElementById('detailKecamatan').textContent = 'Data tidak tersedia';
                                        document.getElementById('detailDesa').textContent = 'Data tidak tersedia';
                                    }
                                }
                            } else {
                                document.getElementById('detailKabupaten').textContent = 'Data tidak tersedia';
                                document.getElementById('detailKecamatan').textContent = 'Data tidak tersedia';
                                document.getElementById('detailDesa').textContent = 'Data tidak tersedia';
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching location data:', error);
                        document.getElementById('detailProvinsi').textContent = 'Error loading data';
                        document.getElementById('detailKabupaten').textContent = 'Error loading data';
                        document.getElementById('detailKecamatan').textContent = 'Error loading data';
                        document.getElementById('detailDesa').textContent = 'Error loading data';
                    }
                }
            }
        });
    </script>

    <script>
        function showDetailModal(asset) {
            // Populate modal with asset details
            document.getElementById('detailNIK').textContent = asset.nik_pemilik || '-';
            document.getElementById('detailNamaPemilik').textContent = asset.nama_pemilik || '-';
            document.getElementById('detailNamaAset').textContent = asset.nama_aset || '-';
            document.getElementById('detailAlamat').textContent = asset.address || '-';
            document.getElementById('detailProvinsi').textContent = asset.province_name || '-';
            document.getElementById('detailKabupaten').textContent = asset.district_name || '-';
            document.getElementById('detailKecamatan').textContent = asset.sub_district_name || '-';
            document.getElementById('detailDesa').textContent = asset.village_name || '-';
            document.getElementById('detailRTRW').textContent = (asset.rt || '-') + ' / ' + (asset.rw || '-');
            document.getElementById('detailKlasifikasi').textContent = asset.klasifikasi ? asset.klasifikasi.jenis_klasifikasi : '-';
            document.getElementById('detailJenisAset').textContent = asset.jenis_aset ? asset.jenis_aset.jenis_aset : '-';
            document.getElementById('detailTagLokasi').textContent = asset.tag_lokasi || '-';

            // Display asset photos
            const fotoContainer = document.getElementById('detailFotoAset');
            fotoContainer.innerHTML = '';

            if (asset.foto_aset_depan) {
                const imgDepan = document.createElement('div');
                imgDepan.innerHTML = `
                    <p class="text-xs text-gray-500 mb-1">Foto Depan</p>
                    <img src="/storage/${asset.foto_aset_depan}" class="w-full h-auto max-h-48 object-cover rounded" alt="Foto Depan">
                `;
                fotoContainer.appendChild(imgDepan);
            }

            if (asset.foto_aset_samping) {
                const imgSamping = document.createElement('div');
                imgSamping.innerHTML = `
                    <p class="text-xs text-gray-500 mb-1">Foto Samping</p>
                    <img src="/storage/${asset.foto_aset_samping}" class="w-full h-auto max-h-48 object-cover rounded" alt="Foto Samping">
                `;
                fotoContainer.appendChild(imgSamping);
            }

            if (!asset.foto_aset_depan && !asset.foto_aset_samping) {
                fotoContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada foto</p>';
            }

            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function confirmDelete(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data aset ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }
    </script>
</x-layout>