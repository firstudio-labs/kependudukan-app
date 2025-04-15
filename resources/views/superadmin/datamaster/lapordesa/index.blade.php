<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Master LaporDes</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data LaporDes..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0a7.5 7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <div class="flex space-x-2">
                <button type="button"
                    onclick="window.location.href='{{ route('superadmin.datamaster.lapordesa.create') }}'"
                    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data LaporDes</span>
                </button>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Ruang Lingkup</th>
                        <th class="px-6 py-3">Bidang</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lapordesas as $index => $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ ($lapordesas->currentPage() - 1) * $lapordesas->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-6 py-4">{{ $item->ruang_lingkup }}</td>
                            <td class="px-6 py-4">{{ $item->bidang }}</td>
                            <td class="px-6 py-4">{{ $item->keterangan ?? '-' }}</td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <a href="{{ route('superadmin.datamaster.lapordesa.edit', $item->id) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.datamaster.lapordesa.destroy', $item->id) }}"
                                    method="POST" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data LaporDes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $lapordesas->firstItem() ?? 0 }} to {{ $lapordesas->lastItem() ?? 0 }} of
                    {{ $lapordesas->total() ?? 0 }} results
                </div>
                {{ $lapordesas->links() }}
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</x-layout>