<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buku Tamu</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari buku tamu..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">No Telepon</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Keperluan</th>
                        <th class="px-6 py-3">Pesan</th>
                        <th class="px-6 py-3">Tanda Tangan</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">Tanggal Kunjungan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bukuTamus as $index => $bukuTamu)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $index + 1 }}</th>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $bukuTamu->nama }}</td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $bukuTamu->alamat }}">
                                {{ \Illuminate\Support\Str::limit($bukuTamu->alamat, 50, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $bukuTamu->no_telepon }}</td>
                        <td class="px-6 py-4">{{ $bukuTamu->email ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $bukuTamu->keperluan }}">
                                {{ \Illuminate\Support\Str::limit($bukuTamu->keperluan, 30, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $bukuTamu->pesan }}">
                                {{ \Illuminate\Support\Str::limit($bukuTamu->pesan, 30, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($bukuTamu->tanda_tangan)
                                @php
                                    $tandaTanganSrc = $bukuTamu->tanda_tangan;
                                    if (!str_starts_with($tandaTanganSrc, 'data:')) {
                                        $tandaTanganSrc = 'data:image/png;base64,' . $tandaTanganSrc;
                                    }
                                @endphp
                                <button type="button" onclick="showSignature('{{ addslashes($tandaTanganSrc) }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fa-solid fa-signature"></i> Lihat
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($bukuTamu->foto)
                                @php
                                    $fotoSrc = $bukuTamu->foto;
                                    if (!str_starts_with($fotoSrc, 'data:')) {
                                        $fotoSrc = 'data:image/jpeg;base64,' . $fotoSrc;
                                    }
                                @endphp
                                <button type="button" onclick="showPhoto('{{ addslashes($fotoSrc) }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fa-solid fa-image"></i> Lihat
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($bukuTamu->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            <button type="button" onclick="showDetail({{ $bukuTamu->id }})" class="text-blue-600 hover:text-blue-800" aria-label="Detail" title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <form action="{{ route('admin.desa.buku-tamu.delete', $bukuTamu->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:underline">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">Tidak ada data buku tamu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    @php
                        $pagination = [
                            'current_page' => $bukuTamus->currentPage(),
                            'items_per_page' => $bukuTamus->perPage(),
                            'total_items' => $bukuTamus->total()
                        ];

                        $currentPage = $pagination['current_page'];
                        $itemsPerPage = $pagination['items_per_page'];
                        $totalItems = $pagination['total_items'];
                        $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
                        $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
                    @endphp
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if($bukuTamus->lastPage() > 1)
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        @php
                            $totalPages = $bukuTamus->lastPage();
                            $currentPage = $bukuTamus->currentPage();

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
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Buku Tamu</h3>
                        <button type="button" onclick="closeDetail()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="detailContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tanda Tangan -->
    <div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Tanda Tangan</h3>
                        <button type="button" onclick="closeSignature()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <div id="signatureContent" class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50">
                            <!-- Signature will be displayed here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto -->
    <div id="photoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Foto</h3>
                        <button type="button" onclick="closePhoto()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <div id="photoContent" class="rounded-lg overflow-hidden">
                            <!-- Photo will be displayed here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script>
        function showDetail(id) {
            // Fetch data via AJAX
            fetch(`/admin/desa/buku-tamu/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                                    <p class="mt-1 text-sm text-gray-900">${data.nama || '-'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No Telepon</label>
                                    <p class="mt-1 text-sm text-gray-900">${data.no_telepon || '-'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">${data.email || '-'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                                    <p class="mt-1 text-sm text-gray-900">${data.tanggal_kunjungan || '-'}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                <p class="mt-1 text-sm text-gray-900">${data.alamat || '-'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Keperluan</label>
                                <p class="mt-1 text-sm text-gray-900">${data.keperluan || '-'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pesan</label>
                                <p class="mt-1 text-sm text-gray-900">${data.pesan || '-'}</p>
                            </div>
                            ${data.foto ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Foto</label>
                                <div class="mt-2">
                                    <img src="${data.foto}" alt="Foto" class="max-w-xs h-auto rounded-lg">
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data');
                });
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetail();
            }
        });

        // Tanda Tangan functions
        function showSignature(signatureData) {
            if (signatureData) {
                // Tanda tangan sudah dalam format data URL lengkap (data:image/png;base64,...)
                let signatureSrc = signatureData;

                // Jika data tidak memiliki prefix data URL, tambahkan
                if (!signatureData.startsWith('data:')) {
                    signatureSrc = `data:image/png;base64,${signatureData}`;
                }

                const signatureImg = `<img src="${signatureSrc}" alt="Tanda Tangan" class="max-w-full h-auto mx-auto" style="max-height: 300px;">`;
                document.getElementById('signatureContent').innerHTML = signatureImg;
                document.getElementById('signatureModal').classList.remove('hidden');
            } else {
                alert('Tanda tangan tidak tersedia');
            }
        }

        function closeSignature() {
            document.getElementById('signatureModal').classList.add('hidden');
        }

        // Foto functions
        function showPhoto(photoData) {
            if (photoData) {
                // Foto sudah dalam format data URL lengkap (data:image/jpeg;base64,...)
                let photoSrc = photoData;

                // Jika data tidak memiliki prefix data URL, tambahkan
                if (!photoData.startsWith('data:')) {
                    photoSrc = `data:image/jpeg;base64,${photoData}`;
                }

                const photoImg = `<img src="${photoSrc}" alt="Foto" class="max-w-full h-auto mx-auto rounded-lg" style="max-height: 500px;">`;
                document.getElementById('photoContent').innerHTML = photoImg;
                document.getElementById('photoModal').classList.remove('hidden');
            } else {
                alert('Foto tidak tersedia');
            }
        }

        function closePhoto() {
            document.getElementById('photoModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('signatureModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSignature();
            }
        });

        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhoto();
            }
        });
    </script>
</x-layout>
