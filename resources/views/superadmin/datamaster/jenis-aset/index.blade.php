<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Jenis Aset</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data Jenis Aset..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <div class="flex space-x-2">
                <button type="button"
                    onclick="window.location.href='{{ route('superadmin.datamaster.jenis-aset.create') }}'"
                    class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Data Jenis Aset</span>
                </button>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Jenis Aset</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisAset as $index => $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ ($jenisAset->currentPage() - 1) * $jenisAset->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-6 py-4">{{ $item->kode }}</td>
                            <td class="px-6 py-4">{{ $item->jenis_aset }}</td>
                            <td class="px-6 py-4">{{ $item->keterangan ?? '-' }}</td>
                            <td class="flex items-center px-6 py-4 space-x-2">
                                <a href="{{ route('superadmin.datamaster.jenis-aset.edit', $item->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('superadmin.datamaster.jenis-aset.destroy', $item->id) }}"
                                    method="POST" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-800">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada jenis aset</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $jenisAset->firstItem() ?? 0 }} to {{ $jenisAset->lastItem() ?? 0 }} of
                    {{ $jenisAset->total() ?? 0 }} results
                </div>
                @if($jenisAset->hasPages())
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        <!-- Previous Button -->
                        @if($jenisAset->onFirstPage())
                            <span
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <span class="sr-only">Previous</span>
                                Previous
                            </span>
                        @else
                            <a href="{{ $jenisAset->previousPageUrl() }}{{ !empty(request('search')) ? '&search=' . request('search') : '' }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                Previous
                            </a>
                        @endif

                        <!-- Page Numbers -->
                        @php
                            $currentPage = $jenisAset->currentPage();
                            $lastPage = $jenisAset->lastPage();

                            $startPage = max($currentPage - 2, 1);
                            $endPage = min($startPage + 4, $lastPage);

                            if ($endPage - $startPage < 4) {
                                $startPage = max($endPage - 4, 1);
                            }
                        @endphp

                        <!-- First Page & Ellipsis -->
                        @if($startPage > 1)
                            <a href="{{ $jenisAset->url(1) }}{{ !empty(request('search')) ? '&search=' . request('search') : '' }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($startPage > 2)
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif

                        <!-- Page Links -->
                        @for($i = $startPage; $i <= $endPage; $i++)
                            <a href="{{ $jenisAset->url($i) }}{{ !empty(request('search')) ? '&search=' . request('search') : '' }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium
                                        {{ $i == $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-[#8c93d6]' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        <!-- Last Page & Ellipsis -->
                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $jenisAset->url($lastPage) }}{{ !empty(request('search')) ? '&search=' . request('search') : '' }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif

                        <!-- Next Button -->
                        @if($jenisAset->hasMorePages())
                            <a href="{{ $jenisAset->nextPageUrl() }}{{ !empty(request('search')) ? '&search=' . request('search') : '' }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                Next
                            </a>
                        @else
                            <span
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <span class="sr-only">Next</span>
                                Next
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>
</x-layout>