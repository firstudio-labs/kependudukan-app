<x-layout>
    <div class="p-4 mt-14">
        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Users</h1>

        <!-- Bar untuk Search dan Tambah User -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
            <form method="GET" action="{{ route('superadmin.datamaster.user.index') }}" class="relative">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari pengguna..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <a href="{{ route('superadmin.datamaster.user.create') }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pengguna Baru</span>
            </a>
        </div>

        <!-- User Table -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->nik }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->no_hp }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user->role == 'superadmin') bg-purple-100 text-purple-800
                                    @elseif($user->role == 'admin') bg-blue-100 text-blue-800
                                    @elseif($user->role == 'operator') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('superadmin.datamaster.user.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('superadmin.datamaster.user.destroy', $user->id) }}" method="POST" class="inline ml-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(event, {{ $user->id }})" class="text-red-600 hover:text-red-900 border-none bg-transparent p-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengguna</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                @if($users->total() > 0)
                    Showing {{ ($users->currentPage() - 1) * $users->perPage() + 1 }} to
                    {{ min($users->currentPage() * $users->perPage(), $users->total()) }}
                    of {{ $users->total() }} results
                @else
                    Showing 0 results
                @endif
            </div>
            @if($users->lastPage() > 1)
                <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                    @php
                        $totalPages = $users->lastPage();
                        $currentPage = $users->currentPage();

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
