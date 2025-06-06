<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Pengajuan Surat</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('user.riwayat-surat.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari surat..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nomor Surat</th>
                            <th class="px-6 py-3">Jenis Surat</th>
                            <th class="px-6 py-3">Keperluan/Keterangan</th>
                            <th class="px-6 py-3">Tanggal Pengajuan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allLetters as $index => $letter)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $index + 1 }}
                                </th>
                                <td class="px-6 py-4">{{ $letter->letter_number ?? 'Belum ditetapkan' }}</td>
                                <td class="px-6 py-4">{{ $letter->letter_type }}</td>
                                <td class="px-6 py-4">{{ $letter->purpose ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $letter->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            1 => 'text-green-600 bg-green-100',
                                            0 => 'text-yellow-600 bg-yellow-100',
                                            null => 'text-yellow-600 bg-yellow-100'
                                        ];
                                        $statusText = [
                                            1 => 'Disetujui',
                                            0 => 'Dalam Proses',
                                            null => 'Dalam Proses'
                                        ];
                                        $status = $letter->is_accepted;
                                        $statusColor = $statusColors[$status] ?? 'text-yellow-600 bg-yellow-100';
                                        $statusName = $statusText[$status] ?? 'Dalam Proses';
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusName }}
                                    </span>
                                </td>
                                <td class="flex items-center px-6 py-4 space-x-2">
                                    <button
                                        onclick="showDetailModal('{{ strtolower($letter->letter_type) }}', {{ $letter->id }})"
                                        class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">Tidak ada data riwayat pengajuan surat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section (if pagination is available) -->
            @if(method_exists($allLetters, 'links') && $allLetters->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing {{ $allLetters->firstItem() }} to {{ $allLetters->lastItem() }} of
                        {{ $allLetters->total() }}
                        results
                    </div>
                    {{ $allLetters->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">
                                Detail Surat
                            </h3>
                            <div class="border-b border-gray-200 mb-4"></div>
                            <div class="mt-2 w-full" id="modalContent">
                                <!-- Content will be loaded dynamically -->
                                <div class="flex justify-center">
                                    <div
                                        class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeDetailModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal handling functions
        function showDetailModal(letterType, letterId) {
            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Set loading state
            document.getElementById('modalContent').innerHTML = `
                <div class="flex justify-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
                </div>
            `;

            // Define endpoints for different letter types
            const endpoints = {
                'skck': '/user/surat/skck/',
                'administrasi': '/user/surat/administrasi/',
                'domisili': '/user/surat/domisili/',
                'domisiliusaha': '/user/surat/domisili-usaha/',
                'kehilangan': '/user/surat/kehilangan/',
                'pengantarktp': '/user/surat/ktp/',
                'izinrumahsewa': '/user/surat/rumah-sewa/',
                'izinkeramaian': '/user/surat/keramaian/',
                'kelahiran': '/user/surat/kelahiran/',
                'kematian': '/user/surat/kematian/',
                'ahliwaris': '/user/surat/ahli-waris/'
            };

            // Get the endpoint for this letter type
            const endpoint = endpoints[letterType];
            if (!endpoint) {
                document.getElementById('modalContent').innerHTML = `
                    <div class="p-4 text-red-600 text-center">
                        Tipe surat tidak tersedia.
                    </div>
                `;
                return;
            }

            // Fetch letter details
            fetch(`${endpoint}${letterId}/detail`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Terjadi kesalahan saat memuat data surat');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Data surat tidak ditemukan');
                    }

                    // Get the letter data based on letter type
                    const letterData = data[letterType];
                    if (!letterData) {
                        throw new Error('Data surat tidak ditemukan');
                    }

                    // Update modal title with letter type name
                    const letterTypeNames = {
                        'skck': 'SKCK',
                        'administrasi': 'Administrasi',
                        'domisili': 'Domisili',
                        'domisiliusaha': 'Domisili Usaha',
                        'kehilangan': 'Kehilangan',
                        'pengantarktp': 'Pengantar KTP',
                        'izinrumahsewa': 'Izin Rumah Sewa',
                        'izinkeramaian': 'Izin Keramaian',
                        'kelahiran': 'Kelahiran',
                        'kematian': 'Kematian',
                        'ahliwaris': 'Ahli Waris'
                    };

                    document.getElementById('modalTitle').textContent = `Detail Surat ${letterTypeNames[letterType] || letterType}`;

                    // Populate the modal with letter data
                    populateModalContent(letterType, letterData);
                })
                .catch(error => {
                    console.error('Error fetching letter details:', error);
                    document.getElementById('modalContent').innerHTML = `
                    <div class="p-4 text-red-600 text-center">
                        ${error.message || 'Terjadi kesalahan saat memuat data surat. Silahkan coba lagi nanti.'}
                    </div>
                `;
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function createTableRow(label, value) {
            return `
                <tr>
                    <td class="px-4 py-2 font-medium text-gray-700">${label}</td>
                    <td class="px-4 py-2 text-gray-900">${value || '-'}</td>
                </tr>
            `;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function populateModalContent(letterType, letterData) {
            // Create a formatted table with letter details
            let contentHtml = `
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <tbody class="divide-y divide-gray-200">`;

            // Common fields for all letter types
            contentHtml += createTableRow("Nomor Surat", letterData.letter_number || "Belum ditetapkan");
            contentHtml += createTableRow("Tanggal Pengajuan", formatDate(letterData.created_at));

            // Add status
            let statusText, statusColor;
            if (letterData.is_accepted === 1) {
                statusText = "Disetujui";
                statusColor = "text-green-600 bg-green-100";
            } else if (letterData.is_accepted === 0) {
                statusText = "Dalam Proses";
                statusColor = "text-yellow-600 bg-yellow-100";
            } else {
                statusText = "Dalam Proses";
                statusColor = "text-yellow-600 bg-yellow-100";
            }

            contentHtml += createTableRow("Status", `<span class="px-2 py-1 rounded-full text-xs font-medium ${statusColor}">${statusText}</span>`);

            // Add specific fields based on letter type
            switch (letterType) {
                case 'skck':
                    contentHtml += createTableRow("Keperluan", letterData.purpose);
                    break;
                case 'administrasi':
                    contentHtml += createTableRow("Keperluan", letterData.purpose);
                    contentHtml += createTableRow("Isi Pernyataan", letterData.statement_content);
                    break;
                case 'domisili':
                    contentHtml += createTableRow("Keperluan", letterData.purpose);
                    break;
                case 'domisiliusaha':
                    contentHtml += createTableRow("Nama Usaha", letterData.business_name);
                    contentHtml += createTableRow("Alamat Usaha", letterData.business_address);
                    break;
                case 'kehilangan':
                    contentHtml += createTableRow("Barang yang Hilang", letterData.lost_items);
                    break;
                case 'pengantarktp':
                    contentHtml += createTableRow("Keperluan", "Pembuatan KTP");
                    break;
                case 'izinrumahsewa':
                    contentHtml += createTableRow("Alamat Rumah Sewa", letterData.rental_address);
                    break;
                case 'izinkeramaian':
                    contentHtml += createTableRow("Acara", letterData.event);
                    contentHtml += createTableRow("Tanggal Acara", formatDate(letterData.event_date));
                    break;
                case 'kelahiran':
                    contentHtml += createTableRow("Nama Anak", letterData.child_name);
                    contentHtml += createTableRow("Tempat Lahir", letterData.child_birth_place);
                    contentHtml += createTableRow("Tanggal Lahir", formatDate(letterData.child_birth_date));
                    break;
                case 'kematian':
                    contentHtml += createTableRow("Nama Almarhum", letterData.deceased_name);
                    contentHtml += createTableRow("Tanggal Meninggal", formatDate(letterData.death_date));
                    break;
                case 'ahliwaris':
                    contentHtml += createTableRow("Nama Ahli Waris", letterData.heir_name);
                    contentHtml += createTableRow("Hubungan", letterData.relationship);
                    break;
            }

            contentHtml += `
                        </tbody>
                    </table>
                </div>`;

            document.getElementById('modalContent').innerHTML = contentHtml;
        }
    </script>

    <script>
        @if(session('success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        @endif

        @if(session('error'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        @endif
    </script>
</x-layout>