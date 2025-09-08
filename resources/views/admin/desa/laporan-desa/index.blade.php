<x-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Leaflet for map rendering in detail modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Laporan Desa</h1>

        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('admin.desa.laporan-desa.index') }}"
                class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Laporan</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Judul, bidang, deskripsi..." />
                        <div class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                    <select name="status" id="status"
                        class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-2">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-[#7886C7] hover:bg-[#2D336B] text-white font-semibold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
                        Filter
                    </button>
                    <a href="{{ route('admin.desa.laporan-desa.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
                        Reset
                    </a>
                </div>
            </form>
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
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $index => $laporan)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50" data-id="{{ $laporan->id }}">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $laporans->firstItem() + $index }}
                                </th>
                                <td class="px-6 py-4">{{ $laporan->laporDesa->ruang_lingkup ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $laporan->laporDesa->bidang ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $laporan->judul_laporan }}</td>
                                <td class="px-6 py-4">{{ $laporan->created_at->format('d/m/Y') }}</td>
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
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="#" onclick="showDetailModal({{ $laporan->id }}); return false;"
                                            class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="showStatusDropdown({{ $laporan->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Status
                                            </button>
                                            <div id="statusDropdown-{{ $laporan->id }}"
                                                class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                <div class="py-1" role="menu" aria-orientation="vertical">
                                                    <button onclick="updateStatus({{ $laporan->id }}, 'Menunggu')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                        role="menuitem">Menunggu</button>
                                                    <button onclick="updateStatus({{ $laporan->id }}, 'Diproses')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                        role="menuitem">Diproses</button>
                                                    <button onclick="updateStatus({{ $laporan->id }}, 'Selesai')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                        role="menuitem">Selesai</button>
                                                    <button onclick="updateStatus({{ $laporan->id }}, 'Ditolak')"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                        role="menuitem">Ditolak</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada data laporan.</td>
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
                    <!-- Status Update Form -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Status Laporan:</h4>
                        <div class="flex gap-2 items-center">
                            <div class="flex-shrink-0">
                                <span id="detailStatusBadge" class="px-2 py-1 rounded-full text-xs font-medium"></span>
                            </div>
                            <div class="flex-grow">
                                <select id="statusSelect" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>
                            <button type="button" id="updateStatusBtn"
                                class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-md">
                                Update Status
                            </button>
                        </div>
                        <div id="statusUpdateMessage" class="mt-2 text-sm hidden"></div>
                    </div>

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
                            <p class="text-sm font-semibold text-gray-500">Tanggal:</p>
                            <p id="detailTanggal" class="text-base text-gray-900">-</p>
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

    <script>
        let currentReportId = null;

        function showDetailModal(id) {
            currentReportId = id;

            // Show loading state
            document.getElementById('detailJudul').textContent = 'Loading...';
            document.getElementById('detailRuangLingkup').textContent = 'Memuat...';
            document.getElementById('detailBidang').textContent = 'Memuat...';
            document.getElementById('statusSelect').value = 'Menunggu';
            document.getElementById('detailStatusBadge').className = 'px-2 py-1 rounded-full text-xs font-medium';
            document.getElementById('detailStatusBadge').textContent = 'Memuat...';
            document.getElementById('detailTanggal').textContent = 'Memuat...';
            document.getElementById('detailLokasi').textContent = 'Memuat...';
            document.getElementById('detailDeskripsi').textContent = 'Memuat...';
            document.getElementById('statusUpdateMessage').classList.add('hidden');

            // Clear previous images and show loading
            const imageContainer = document.getElementById('detailGambarLaporan');
            imageContainer.innerHTML = '<p class="text-sm text-gray-500">Memuat gambar...</p>';

            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Fetch laporan data using AJAX with proper error handling
            fetch(`/admin/desa/laporan-desa/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
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

                        // Set the select value and update badge
                        document.getElementById('statusSelect').value = laporan.status;
                        updateStatusBadge(laporan.status);

                        // Format and display date
                        if (laporan.created_at) {
                            const date = new Date(laporan.created_at);
                            document.getElementById('detailTanggal').textContent = date.toLocaleString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        } else {
                            document.getElementById('detailTanggal').textContent = '-';
                        }

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
                <img src="${laporan.gambar_url || ('/storage/' + laporan.gambar)}" alt="Foto Laporan" 
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
                        document.getElementById('statusSelect').value = 'Menunggu';
                        document.getElementById('detailStatusBadge').className = 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
                        document.getElementById('detailStatusBadge').textContent = '-';
                        document.getElementById('detailTanggal').textContent = '-';
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
                    document.getElementById('statusSelect').value = 'Menunggu';
                    document.getElementById('detailStatusBadge').className = 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
                    document.getElementById('detailStatusBadge').textContent = '-';
                    document.getElementById('detailTanggal').textContent = '-';
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

        function updateStatusBadge(status) {
            const badge = document.getElementById('detailStatusBadge');
            badge.textContent = status;

            // Clear all classes first except base classes
            badge.className = 'px-2 py-1 rounded-full text-xs font-medium';

            // Add appropriate color classes based on status
            switch (status) {
                case 'Menunggu':
                    badge.classList.add('bg-yellow-100', 'text-yellow-600');
                    break;
                case 'Diproses':
                    badge.classList.add('bg-blue-100', 'text-blue-600');
                    break;
                case 'Selesai':
                    badge.classList.add('bg-green-100', 'text-green-600');
                    break;
                case 'Ditolak':
                    badge.classList.add('bg-red-100', 'text-red-600');
                    break;
                default:
                    badge.classList.add('bg-gray-100', 'text-gray-600');
            }
        }

        // Add event listener for status update button
        document.getElementById('updateStatusBtn').addEventListener('click', function () {
            if (!currentReportId) return;

            const statusSelect = document.getElementById('statusSelect');
            const newStatus = statusSelect.value;
            const messageEl = document.getElementById('statusUpdateMessage');

            // Show loading message
            messageEl.textContent = 'Memperbarui status...';
            messageEl.className = 'mt-2 text-sm text-blue-500';
            messageEl.classList.remove('hidden');

            // Disable the button during update
            this.disabled = true;

            // Send update request
            fetch(`/admin/desa/laporan-desa/${currentReportId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update UI with success
                    updateStatusBadge(newStatus);
                    messageEl.textContent = 'Status berhasil diperbarui';
                    messageEl.className = 'mt-2 text-sm text-green-500';

                    // Also update the status in the table row if it exists
                    const tableRow = document.querySelector(`tr[data-id="${currentReportId}"]`);
                    if (tableRow) {
                        const statusCell = tableRow.querySelector('td:nth-child(6)');
                        if (statusCell) {
                            // Get the span element inside the cell
                            const statusSpan = statusCell.querySelector('span');
                            if (statusSpan) {
                                // Clear all existing classes and add new ones
                                statusSpan.className = 'px-2 py-1 rounded-full text-xs font-medium';
                                switch (newStatus) {
                                    case 'Menunggu':
                                        statusSpan.classList.add('text-yellow-600', 'bg-yellow-100');
                                        break;
                                    case 'Diproses':
                                        statusSpan.classList.add('text-blue-600', 'bg-blue-100');
                                        break;
                                    case 'Selesai':
                                        statusSpan.classList.add('text-green-600', 'bg-green-100');
                                        break;
                                    case 'Ditolak':
                                        statusSpan.classList.add('text-red-600', 'bg-red-100');
                                        break;
                                    default:
                                        statusSpan.classList.add('text-gray-600', 'bg-gray-100');
                                }
                                statusSpan.textContent = newStatus;
                            }
                        }
                    }

                    // Update status count cards
                    updateStatusCountCards();

                    // Show a small toast notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status laporan berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    messageEl.textContent = 'Gagal memperbarui status: ' + error.message;
                    messageEl.className = 'mt-2 text-sm text-red-500';
                })
                .finally(() => {
                    // Re-enable the button
                    this.disabled = false;

                    // Hide message after 3 seconds
                    setTimeout(() => {
                        if (messageEl.classList.contains('text-green-500')) {
                            messageEl.classList.add('hidden');
                        }
                    }, 3000);
                });
        });

        function updateStatusCountCards() {
            // If we're not on a page with status count cards, this function does nothing
            if (!document.querySelector('.status-count-card')) return;

            // Fetch updated counts from the server
            fetch('/admin/desa/laporan-desa?format=json', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.statusCounts) {
                        // Update each count
                        Object.keys(data.statusCounts).forEach(status => {
                            const countElement = document.querySelector(`.status-count-${status.toLowerCase()}`);
                            if (countElement) {
                                countElement.textContent = data.statusCounts[status];
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching updated counts:', error);
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentReportId = null;
        }

        function showStatusDropdown(id) {
            // Hide all other dropdowns first
            document.querySelectorAll('[id^="statusDropdown-"]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });

            // Toggle the clicked dropdown
            const dropdown = document.getElementById(`statusDropdown-${id}`);
            dropdown.classList.toggle('hidden');

            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!dropdown.contains(e.target) && !e.target.matches(`[onclick="showStatusDropdown(${id})"]`)) {
                    dropdown.classList.add('hidden');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        function updateStatus(id, newStatus) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mengubah status menjadi "${newStatus}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send update request
                    fetch(`/admin/desa/laporan-desa/${id}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Update the status badge in the table
                            const tableRow = document.querySelector(`tr[data-id="${id}"]`);
                            if (tableRow) {
                                const statusCell = tableRow.querySelector('td:nth-child(6)');
                                if (statusCell) {
                                    const statusSpan = statusCell.querySelector('span');
                                    if (statusSpan) {
                                        // Clear all existing classes and add new ones
                                        statusSpan.className = 'px-2 py-1 rounded-full text-xs font-medium';
                                        switch (newStatus) {
                                            case 'Menunggu':
                                                statusSpan.classList.add('text-yellow-600', 'bg-yellow-100');
                                                break;
                                            case 'Diproses':
                                                statusSpan.classList.add('text-blue-600', 'bg-blue-100');
                                                break;
                                            case 'Selesai':
                                                statusSpan.classList.add('text-green-600', 'bg-green-100');
                                                break;
                                            case 'Ditolak':
                                                statusSpan.classList.add('text-red-600', 'bg-red-100');
                                                break;
                                            default:
                                                statusSpan.classList.add('text-gray-600', 'bg-gray-100');
                                        }
                                        statusSpan.textContent = newStatus;
                                    }
                                }
                            }

                            // Update status count cards
                            updateStatusCountCards();

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Status laporan berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            console.error('Error updating status:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal memperbarui status: ' + error.message
                            });
                        });
                }
            });
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