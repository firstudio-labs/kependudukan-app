<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Berita Desa</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('admin.desa.berita-desa.index') }}" class="relative w-full max-w-xs">
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
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Judul Berita</th>
                            <th class="px-6 py-3">Deskripsi</th>
                            <th class="px-6 py-3">Komentar</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita as $index => $item)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $berita->firstItem() + $index }}
                                </th>
                                <td class="px-6 py-4">{{ $item->judul }}</td>
                                <td class="px-6 py-4">{{ Str::limit($item->deskripsi, 50) }}</td>
                                <td class="px-6 py-4">{{ Str::limit($item->komentar, 50) }}</td>
                                <td class="flex items-center px-6 py-4 space-x-2">
                                    <button onclick="showDetailModal({{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-800" aria-label="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.desa.berita-desa.edit', $item->id) }}"
                                        class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button onclick="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-800"
                                        aria-label="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data berita.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            @if($berita->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing {{ $berita->firstItem() }} to {{ $berita->lastItem() }} of {{ $berita->total() }}
                        results
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

                        // Update modal content
                        document.getElementById('detailJudulBerita').textContent = berita.judul || '-';
                        document.getElementById('detailDeskripsi').textContent = berita.deskripsi || '-';
                        document.getElementById('detailKomentar').textContent = berita.komentar || '-';

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