<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Berita Desa 
            @if(isset($berita->first()->wilayah_info['desa']) && !str_contains($berita->first()->wilayah_info['desa'], 'ID:'))
                {{ $berita->first()->wilayah_info['desa'] }}
            @else
                {{ $berita->first()->villages_id ?? 'Anda' }}
            @endif
        </h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ request()->routeIs('admin.desa.berita-desa.pending') ? route('admin.desa.berita-desa.pending') : route('admin.desa.berita-desa.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari berita..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
            @if(($context ?? 'approved') === 'approved')
                <div>
                    <a href="{{ route('admin.desa.berita-desa.create') }}"
                        class="flex items-center justify-center bg-[#7886C7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#2D336B] transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-4 h-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Berita</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Judul Berita</th>
                            <th class="px-6 py-3">Deskripsi</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3">Tanggal</th>
                            @if(($context ?? 'approved') === 'pending')
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Aksi</th>
                            @else
                                <th class="px-6 py-3">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita as $index => $item)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $berita->firstItem() + $index }}
                                </th>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ strip_tags($item->judul) }}</div>
                                    @if($item->gambar)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $item->gambar) }}" 
                                                 alt="Gambar {{ $item->judul }}"
                                                 class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:scale-105 transition-transform duration-200 cursor-pointer"
                                                 onclick="showImageModal('{{ asset('storage/' . $item->gambar) }}', '{{ $item->judul }}')"
                                            />
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        {{ Str::limit(strip_tags($item->deskripsi), 80) }}
                                        @if(strlen($item->deskripsi) > 80)
                                            <span class="text-blue-600 text-xs">...selengkapnya</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if(isset($item->wilayah_info) && !empty($item->wilayah_info))
                                        <div class="text-xs space-y-1">
                                            @if(isset($item->wilayah_info['provinsi']))
                                                <div class="text-gray-600">
                                                    <span class="font-medium">Provinsi:</span>
                                                    <span class="ml-1">
                                                        @if(!str_contains($item->wilayah_info['provinsi'], 'ID:'))
                                                            {{ $item->wilayah_info['provinsi'] }}
                                                        @else
                                                            {{ $item->province_id }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                            @if(isset($item->wilayah_info['kabupaten']))
                                                <div class="text-gray-600">
                                                    <span class="font-medium">Kabupaten:</span>
                                                    <span class="ml-1">
                                                        @if(!str_contains($item->wilayah_info['kabupaten'], 'ID:'))
                                                            {{ $item->wilayah_info['kabupaten'] }}
                                                        @else
                                                            {{ $item->districts_id }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                            @if(isset($item->wilayah_info['kecamatan']))
                                                <div class="text-gray-600">
                                                    <span class="font-medium">Kecamatan:</span>
                                                    <span class="ml-1">
                                                        @if(!str_contains($item->wilayah_info['kecamatan'], 'ID:'))
                                                            {{ $item->wilayah_info['kecamatan'] }}
                                                        @else
                                                            {{ $item->sub_districts_id }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                            @if(isset($item->wilayah_info['desa']))
                                                <div class="text-gray-800 font-medium">
                                                    <span class="font-medium">Desa:</span>
                                                    <span class="ml-1">
                                                        @if(!str_contains($item->wilayah_info['desa'], 'ID:'))
                                                            {{ $item->wilayah_info['desa'] }}
                                                        @else
                                                            {{ $item->villages_id }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900">{{ $item->created_at->format('d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->created_at->format('M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                @if(($context ?? 'approved') === 'pending')
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded {{ $item->status === 'approved' ? 'bg-green-100 text-green-700' : ($item->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ ucfirst($item->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="showDetailModal({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                                aria-label="Lihat Detail">
                                                <i class="fa-solid fa-eye mr-2"></i>
                                                Detail
                                            </button>
                                            <form action="{{ route('admin.desa.berita-desa.approve', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    <i class="fa-solid fa-check mr-2"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.desa.berita-desa.reject', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <i class="fa-solid fa-xmark mr-2"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="showDetailModal({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                                aria-label="Lihat Detail">
                                                <i class="fa-solid fa-eye mr-2"></i>
                                                Detail
                                            </button>
                                            <a href="{{ route('admin.desa.berita-desa.edit', $item->id) }}"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors duration-200"
                                                aria-label="Edit Berita">
                                                <i class="fa-solid fa-pen-to-square mr-2"></i>
                                                Edit
                                            </a>
                                            <button onclick="confirmDelete({{ $item->id }})" 
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200"
                                                aria-label="Hapus Berita">
                                                <i class="fa-solid fa-trash mr-2"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fa-solid fa-newspaper text-2xl text-gray-400"></i>
                                        </div>
                                        <div class="text-gray-500">
                                            <div class="font-medium text-lg">Belum ada berita</div>
                                            <div class="text-sm">Berita desa akan muncul di sini</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            @if($berita->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $berita->firstItem() }} sampai {{ $berita->lastItem() }} dari {{ $berita->total() }}
                        hasil
                    </div>
                    {{ $berita->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Berita Desa -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Modal Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-gray-300 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900" id="detailJudul">
                        Detail Berita
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
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Judul Berita:</p>
                        <p id="detailJudulBerita" class="text-base text-gray-900">-</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500">Deskripsi:</p>
                        <p id="detailDeskripsi" class="text-base text-gray-900">-</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500">Komentar:</p>
                        <p id="detailKomentar" class="text-base text-gray-900">-</p>
                    </div>

                    <!-- Gambar Berita Section -->
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">Gambar Berita:</p>
                        <div id="detailGambar" class="max-h-[350px] overflow-auto">
                            <!-- Image will be added here dynamically -->
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

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
        <div class="relative max-w-4xl max-h-full p-4">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl font-bold z-10">
                &times;
            </button>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg" />
            <div class="text-center mt-4">
                <h3 id="modalTitle" class="text-white text-lg font-semibold"></h3>
            </div>
        </div>
    </div>

    <script>
        function showDetailModal(id) {
            // Show loading state
            document.getElementById('detailJudulBerita').textContent = 'Memuat...';
            document.getElementById('detailDeskripsi').textContent = 'Memuat...';
            document.getElementById('detailKomentar').textContent = 'Memuat...';

            // Clear previous image and show loading
            const imageContainer = document.getElementById('detailGambar');
            imageContainer.innerHTML = '<p class="text-sm text-gray-500">Memuat gambar...</p>';

            // Show the modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Fetch berita data using AJAX
            fetch(`/admin/desa/berita-desa/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
                        const berita = data.data;

                        // Update modal content (render CKEditor HTML)
                        document.getElementById('detailJudulBerita').innerHTML = berita.judul || '-';
                        document.getElementById('detailDeskripsi').innerHTML = berita.deskripsi || '-';
                        document.getElementById('detailKomentar').innerHTML = berita.komentar || '-';

                        // Clear previous image
                        imageContainer.innerHTML = '';

                        // Add image if available
                        if (berita.gambar) {
                            const imgElement = document.createElement('div');
                            imgElement.innerHTML = `
                            <img src="/storage/${berita.gambar}" alt="Gambar Berita" 
                                class="w-full h-auto max-h-[300px] object-contain rounded-lg shadow-sm">
                        `;
                            imageContainer.appendChild(imgElement);
                        } else {
                            imageContainer.innerHTML = '<p class="text-sm text-gray-500">Tidak ada gambar</p>';
                        }
                    } else {
                        throw new Error('Data format tidak valid');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data berita: ' + error.message,
                    });
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/desa/berita-desa/${id}`;

                    // Tambahkan CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Tambahkan method DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function showImageModal(imageSrc, title) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
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