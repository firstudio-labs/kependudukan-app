<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Desa</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('user.laporan-desa.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari laporan..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>

            <div>
                <a href="{{ route('user.laporan-desa.create') }}"
                    class="flex items-center justify-center bg-[#7886C7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#2D336B] transition duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Laporan</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Ruang Lingkup</th>
                            <th class="px-6 py-3">Bidang</th>
                            <th class="px-6 py-3">Judul Laporan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $index => $laporan)
                                                <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                        {{ $laporans->firstItem() + $index }}
                                                    </th>
                                                    <td class="px-6 py-4">{{ $laporan->laporDesa->ruang_lingkup ?? '-' }}</td>
                                                    <td class="px-6 py-4">{{ $laporan->laporDesa->bidang ?? '-' }}</td>
                                                    <td class="px-6 py-4">{{ $laporan->judul_laporan }}</td>
                                                    <td class="px-6 py-4">
                                                        @php
    $statusColors = [
        'Menunggu' => 'text-yellow-600 bg-yellow-100',
        'Diproses' => 'text-blue-600 bg-blue-100',
        'Selesai' => 'text-green-600 bg-green-100',
        'Ditolak' => 'text-red-600 bg-red-100'
    ];
    $statusColor = $statusColors[$laporan->status] ?? 'text-gray-600 bg-gray-100';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $laporan->status }}
                        </span>
                    </td>
                    <td class="flex items-center px-6 py-4 space-x-2">
                        <button onclick="showDetailModal({{ $laporan->id }})"
                            class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <a href="{{ route('user.laporan-desa.edit', $laporan->id) }}"
                            class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('user.laporan-desa.destroy', $laporan->id) }}" method="POST"
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
                                <td colspan="6" class="text-center py-4">Tidak ada data laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            @if($laporans->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }}
                        results
                    </div>
                    {{ $laporans->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

 <!-- Modal Detail Laporan Desa -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-black opacity-50"></div>
    
        <!-- Modal Content -->
        <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-gray-300 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900" id="detailJudul">
                    Detail Laporan
                </h3>
                <button type="button" onclick="closeDetailModal()" 
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
    
            <!-- Modal Body -->
            <div class="p-4 md:p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Ruang Lingkup:</p>
                        <p id="detailRuangLingkup" class="text-base text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Bidang:</p>
                        <p id="detailBidang" class="text-base text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Status:</p>
                        <p id="detailStatus" class="text-base text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Lokasi:</p>
                        <p id="detailLokasi" class="text-base text-gray-900">-</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">Deskripsi Laporan:</p>
                    <p id="detailDeskripsi" class="text-base text-gray-900">-</p>
                </div>
    
                <!-- Foto Laporan Section -->
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-2">Foto Laporan:</p>
                    <div id="detailGambarLaporan" class="max-h-[350px] overflow-auto">
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

    <!-- Leaflet for map rendering in detail modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        function showDetailModal(id) {
            // Show loading state
            document.getElementById('detailJudul').textContent = 'Loading...';
            document.getElementById('detailRuangLingkup').textContent = 'Memuat...';
            document.getElementById('detailBidang').textContent = 'Memuat...';
            document.getElementById('detailStatus').textContent = 'Memuat...';
            document.getElementById('detailLokasi').textContent = 'Memuat...';
            document.getElementById('detailDeskripsi').textContent = 'Memuat...';

            // Clear previous images and show loading
            const imageContainer = document.getElementById('detailGambarLaporan');
            imageContainer.innerHTML = '<p class="text-sm text-gray-500">Memuat gambar...</p>';

            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Fetch laporan data using AJAX with proper error handling
            fetch(`/user/laporan-desa/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Log more details for debugging
                    console.error('Fetch error:', response.status, response.statusText);
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && data.data) {
                    const laporan = data.data;

                    // Update modal content
                    document.getElementById('detailJudul').textContent = laporan.judul_laporan || 'Detail Laporan';
                    document.getElementById('detailRuangLingkup').textContent = laporan.lapor_desa?.ruang_lingkup || '-';
                    document.getElementById('detailBidang').textContent = laporan.lapor_desa?.bidang || '-';
                    document.getElementById('detailStatus').textContent = laporan.status || '-';
                    
                    // Handle location with map if coordinates are available
                    const locationContainer = document.getElementById('detailLokasi');
                    
                    if (laporan.tag_lokasi) {
                        // Split coordinates from tag_lokasi field (format: "lat, lng")
                        const coordinates = laporan.tag_lokasi.split(',').map(coord => coord.trim());
                        
                        if (coordinates.length === 2) {
                            const lat = coordinates[0];
                            const lng = coordinates[1];
                            
                            // Create map container if Leaflet is available
                            if (typeof L !== 'undefined') {
                                locationContainer.innerHTML = '';
                                
                                const mapDiv = document.createElement('div');
                                mapDiv.id = 'locationMap';
                                mapDiv.style.height = '200px';
                                mapDiv.style.width = '100%';
                                mapDiv.style.marginTop = '8px';
                                mapDiv.style.borderRadius = '4px';
                                locationContainer.appendChild(mapDiv);
                                
                                // Add text location if available
                                if (laporan.lokasi) {
                                    const locText = document.createElement('p');
                                    locText.className = 'text-sm text-gray-500 mt-2';
                                    locText.textContent = laporan.lokasi;
                                    locationContainer.appendChild(locText);
                                }
                                
                                // Initialize map after the modal is fully visible
                                setTimeout(() => {
                                    const map = L.map('locationMap').setView([lat, lng], 15);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; OpenStreetMap contributors'
                                    }).addTo(map);
                                    
                                    // Add marker
                                    L.marker([lat, lng]).addTo(map);
                                    
                                    // Ensure map renders correctly
                                    map.invalidateSize();
                                }, 300);
                            } else {
                                locationContainer.textContent = `${lat}, ${lng}${laporan.lokasi ? ' - ' + laporan.lokasi : ''}`;
                            }
                        } else {
                            locationContainer.textContent = laporan.lokasi || '-';
                        }
                    } else {
                        locationContainer.textContent = laporan.lokasi || '-';
                    }
                    
                    document.getElementById('detailDeskripsi').textContent = laporan.deskripsi_laporan || '-';
                    
                    // Clear previous images
                    imageContainer.innerHTML = '';
                    
                    // Add image if available
                     if (laporan.gambar) {
                        const imgElement = document.createElement('div');
                        imgElement.innerHTML = `
                <img src="${laporan.gambar_url}" alt="Foto Laporan" 
                     class="w-full h-auto max-h-[300px] object-contain rounded-lg shadow-sm">
            `;
                        imageContainer.appendChild(imgElement);
                    } else {
                        imageContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada foto</p>';
                    }
                } else {
                    // Handle error in data format
                    document.getElementById('detailJudul').textContent = 'Detail Laporan';
                    document.getElementById('detailRuangLingkup').textContent = '-';
                    document.getElementById('detailBidang').textContent = '-';
                    document.getElementById('detailStatus').textContent = '-';
                    document.getElementById('detailLokasi').textContent = '-';
                    document.getElementById('detailDeskripsi').textContent = '-';
                    imageContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada foto</p>';
                    
                    console.error('Data format error:', data);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data laporan: Format data tidak valid',
                    });
                }
            })
            .catch(error => {
                // Handle network errors
                document.getElementById('detailJudul').textContent = 'Detail Laporan';
                document.getElementById('detailRuangLingkup').textContent = '-';
                document.getElementById('detailBidang').textContent = '-';
                document.getElementById('detailStatus').textContent = '-';
                document.getElementById('detailLokasi').textContent = '-';
                document.getElementById('detailDeskripsi').textContent = '-';
                imageContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada foto</p>';
                
                console.error('Error fetching data:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data laporan: ' + error.message,
                });
            });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>

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
    </script>
</x-layout>