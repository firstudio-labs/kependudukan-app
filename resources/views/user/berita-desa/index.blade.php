<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Berita Desa 
            @if($berita->count() > 0 && isset($berita->first()->wilayah_info['desa']) && !str_contains($berita->first()->wilayah_info['desa'], 'ID:'))
                {{ $berita->first()->wilayah_info['desa'] }}
            @elseif($berita->count() > 0 && isset($berita->first()->wilayah_info['kecamatan']) && !str_contains($berita->first()->wilayah_info['kecamatan'], 'ID:'))
                {{ $berita->first()->wilayah_info['kecamatan'] }}
            @elseif($berita->count() > 0 && isset($berita->first()->wilayah_info['kabupaten']) && !str_contains($berita->first()->wilayah_info['kabupaten'], 'ID:'))
                {{ $berita->first()->wilayah_info['kabupaten'] }}
            @elseif($berita->count() > 0 && isset($berita->first()->wilayah_info['provinsi']) && !str_contains($berita->first()->wilayah_info['provinsi'], 'ID:'))
                {{ $berita->first()->wilayah_info['provinsi'] }}
            @elseif($berita->count() > 0)
                {{ $berita->first()->villages_id ?? 'Anda' }}
            @else
                Anda
            @endif
        </h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('user.berita-desa.index') }}" class="relative w-full max-w-xs">
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
                            <th class="px-6 py-3">Aksi</th>
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
                                <td class="px-6 py-4">
                                    <button onclick="showDetailModal({{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada data berita.</td>
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

                    <!-- Wilayah Section -->
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-3">Informasi Lokasi:</p>
                        <div id="detailWilayah" class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 text-sm font-semibold rounded-full">D</span>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Desa</div>
                                    <div id="detailDesa" class="font-medium text-gray-900">-</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 text-sm font-semibold rounded-full">K</span>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Kecamatan</div>
                                    <div id="detailKecamatan" class="font-medium text-gray-900">-</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-600 text-sm font-semibold rounded-full">B</span>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Kabupaten</div>
                                    <div id="detailKabupaten" class="font-medium text-gray-900">-</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 text-sm font-semibold rounded-full">P</span>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">Provinsi</div>
                                    <div id="detailProvinsi" class="font-medium text-gray-900">-</div>
                                </div>
                            </div>
                        </div>
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
            fetch(`/user/berita-desa/${id}`, {
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
                    if (data.data) {
                        const berita = data.data;

                        // Update modal content (render CKEditor HTML)
                        document.getElementById('detailJudulBerita').innerHTML = berita.judul || '-';
                        document.getElementById('detailDeskripsi').innerHTML = berita.deskripsi || '-';
                        document.getElementById('detailKomentar').innerHTML = berita.komentar || '-';

                        // Update wilayah info dengan format baru
                        const wilayah = berita.wilayah_info || {};
                        
                        // Update setiap field wilayah
                        document.getElementById('detailDesa').textContent = wilayah.desa || 'Tidak tersedia';
                        document.getElementById('detailKecamatan').textContent = wilayah.kecamatan || 'Tidak tersedia';
                        document.getElementById('detailKabupaten').textContent = wilayah.kabupaten || 'Tidak tersedia';
                        document.getElementById('detailProvinsi').textContent = wilayah.provinsi || 'Tidak tersedia';

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