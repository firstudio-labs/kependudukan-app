<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Surat Administrasi Umum</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari surat administrasi..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.surat.administrasi.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Surat Administrasi Umum</span>
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Menyatakan Bahwa</th>
                        <th class="px-6 py-3">Digunakan Untuk</th>
                        <th class="px-6 py-3">Tanggal Surat</th>
                        <th class="px-6 py-3">Pejabat Penandatangan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($administrations as $index => $administration)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $index + 1 }}</th>
                        <td class="px-6 py-4">{{ $administration->nik }}</td>
                        <td class="px-6 py-4">{{ $administration->full_name }}</td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $administration->statement_content }}">
                                {{ \Illuminate\Support\Str::limit($administration->statement_content, 50, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $administration->purpose }}">
                                {{ \Illuminate\Support\Str::limit($administration->purpose, 50, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($administration->letter_date)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $administration->signer ? $administration->signer->judul : $administration->signing }}
                        </td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            @if($administration->is_accepted)
                                <span class="text-green-600 font-semibold">Acceptance</span>
                            @endif
                            <a href="{{ route('superadmin.surat.administrasi.pdf', $administration->id) }}" class="text-blue-600 hover:text-blue-800" aria-label="Export PDF" target="_blank" title="Export PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            @if(!$administration->is_accepted)
                                <a href="{{ route('superadmin.surat.administrasi.edit', $administration->id) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @endif
                            <form action="{{ route('superadmin.surat.administrasi.delete', $administration->id) }}" method="POST" class="delete-form">
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
                        <td colspan="8" class="text-center py-4">Tidak ada data surat administrasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            @if($administrations->hasPages())
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $administrations->firstItem() }} to {{ $administrations->lastItem() }} of {{ $administrations->total() }} results
                </div>
                {{ $administrations->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
</x-layout>
