<x-layout>
    <div class="p-4 mt-14">


        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Jenis Pekerjaan</h1>

        <!-- Bar untuk Search dan Tambah Pasien -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
<!-- Input Pencarian -->
<form method="GET" action="" class="relative">
    <input
        type="text"
        name="search"
        id="search"
        value=""
        class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Cari data kk..."
    />
    <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
        </svg>
    </button>
</form>

            <button
    type="button"
    onclick="window.location.href='{{ route('superadmin.datamaster.job.create') }}'"
    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    <span>Tambah Data Pekerjaan</span>
</button>


        </div>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3">Kode</th>
                        <th scope="col" class="px-6 py-3">Jenis Pekerjaan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)

                        @if (!empty($job) && isset($job['id']))
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $job['code'] }}</th>
                                <td class="px-6 py-4">{{ $job['name'] }}</td>
                                <td class="flex items-center px-6 py-4 space-x-2">
                                    <a href="{{ route('jobs.edit', $job['id']) }}" class="text-yellow-600 hover:text-yellow-800">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form id="delete-form-{{ $job['id'] }}" action="{{ route('superadmin.datamaster.job.destroy', $job['id']) }}" method="POST" onsubmit="confirmDelete(event, {{ $job['id'] }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">Data tidak valid.</td>
                            </tr>
                        @endif
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
                    @php
                        $pagination = [
                            'current_page' => $jobs->currentPage(),
                            'items_per_page' => $jobs->perPage(),
                            'total_items' => $jobs->total()
                        ];

                        $currentPage = $pagination['current_page'];
                        $itemsPerPage = $pagination['items_per_page'];
                        $totalItems = $pagination['total_items'];
                        $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
                        $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
                    @endphp
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if($jobs->lastPage() > 1)
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        @php
                            $totalPages = $jobs->lastPage();
                            $currentPage = $jobs->currentPage();

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







    <script>
        // Replace existing alert script with SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Add SweetAlert confirmation for delete
        function confirmDelete(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

    </script>
</x-layout>
