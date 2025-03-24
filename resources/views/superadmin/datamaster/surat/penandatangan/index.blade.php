<!-- filepath: c:\laragon\www\kependudukan-app\resources\views\superadmin\datamaster\surat\penandatangan\index.blade.php -->
<x-layout>
    <div class="p-4 mt-14">

        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Penandatangan</h1>

        <!-- Bar untuk Search dan Tambah Penandatangan -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
            <form method="GET" action="{{ route('superadmin.datamaster.surat.penandatangan.index') }}" class="relative">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data penandatangan..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.datamaster.surat.penandatangan.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Data Penandatangan</span>
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Penandatangan</th>
                        <th scope="col" class="px-6 py-3">Keterangan Penandatangan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penandatangans as $index => $penandatangan)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($penandatangans->currentPage() - 1) * $penandatangans->perPage() + $index + 1 }}</td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $penandatangan->judul }}</th>
                            <td class="px-6 py-4">{{ $penandatangan->keterangan }}</td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <a href="{{ route('superadmin.datamaster.surat.penandatangan.edit', $penandatangan->id) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form id="delete-form-{{ $penandatangan->id }}" action="{{ route('superadmin.datamaster.surat.penandatangan.destroy', $penandatangan->id) }}" method="POST" onsubmit="confirmDelete(event, {{ $penandatangan->id }})">
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
                            <td colspan="4" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    @if($penandatangans->count() > 0)
                        Showing {{ $penandatangans->firstItem() }} to {{ $penandatangans->lastItem() }} of {{ $penandatangans->total() }} results
                    @else
                        No results found
                    @endif
                </div>
                {{ $penandatangans->links() }}
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
    </div>
</x-layout>
