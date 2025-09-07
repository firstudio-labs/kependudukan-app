<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Semua Surat Desa</h1>

        <div class="flex justify-between items-center mb-4 gap-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari surat (nama, nik, tujuan, dst)..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <form method="GET" action="" class="w-full max-w-xs">
                <div class="flex items-center gap-2">
                    <select name="type" class="p-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua jenis</option>
                        <option value="administrasi" {{ request('type')==='administrasi' ? 'selected' : '' }}>Administrasi Umum</option>
                        <option value="kehilangan" {{ request('type')==='kehilangan' ? 'selected' : '' }}>Kehilangan</option>
                        <option value="skck" {{ request('type')==='skck' ? 'selected' : '' }}>SKCK</option>
                        <option value="domisili" {{ request('type')==='domisili' ? 'selected' : '' }}>Domisili</option>
                        <option value="domisili_usaha" {{ request('type')==='domisili_usaha' ? 'selected' : '' }}>Domisili Usaha</option>
                        <option value="ahli_waris" {{ request('type')==='ahli_waris' ? 'selected' : '' }}>Ahli Waris</option>
                        <option value="kelahiran" {{ request('type')==='kelahiran' ? 'selected' : '' }}>Kelahiran</option>
                        <option value="kematian" {{ request('type')==='kematian' ? 'selected' : '' }}>Kematian</option>
                        <option value="keramaian" {{ request('type')==='keramaian' ? 'selected' : '' }}>Izin Keramaian</option>
                        <option value="rumah_sewa" {{ request('type')==='rumah_sewa' ? 'selected' : '' }}>Rumah Sewa</option>
                    </select>
                    <button type="submit" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-4 py-2">Filter</button>
                </div>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Jenis Surat</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Keterangan/Tujuan</th>
                        <th class="px-6 py-3">Tanggal Surat</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}</th>
                        <td class="px-6 py-4">{{ $item['type_label'] }}</td>
                        <td class="px-6 py-4">{{ $item['nik'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item['full_name'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $item['purpose'] }}">
                                {{ \Illuminate\Support\Str::limit($item['purpose'] ?? '-', 50, '...') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item['letter_date'])->format('d-m-Y') }}</td>
                        <td class="flex items-center px-6 py-4 space-x-2">
                            @php
                                $routes = [
                                    'administrasi' => ['pdf' => 'admin.desa.surat.administrasi.pdf', 'edit' => 'admin.desa.surat.administrasi.edit', 'delete' => 'admin.desa.surat.administrasi.delete'],
                                    'kehilangan' => ['pdf' => 'admin.desa.surat.kehilangan.pdf', 'edit' => 'admin.desa.surat.kehilangan.edit', 'delete' => 'admin.desa.surat.kehilangan.delete'],
                                    'skck' => ['pdf' => 'admin.desa.surat.skck.pdf', 'edit' => 'admin.desa.surat.skck.edit', 'delete' => 'admin.desa.surat.skck.delete'],
                                    'domisili' => ['pdf' => 'admin.desa.surat.domisili.pdf', 'edit' => 'admin.desa.surat.domisili.edit', 'delete' => 'admin.desa.surat.domisili.delete'],
                                    'domisili_usaha' => ['pdf' => 'admin.desa.surat.domisili-usaha.pdf', 'edit' => 'admin.desa.surat.domisili-usaha.edit', 'delete' => 'admin.desa.surat.domisili-usaha.delete'],
                                    'ahli_waris' => ['pdf' => 'admin.desa.surat.ahli-waris.pdf', 'edit' => 'admin.desa.surat.ahli-waris.edit', 'delete' => 'admin.desa.surat.ahli-waris.delete'],
                                    'kelahiran' => ['pdf' => 'admin.desa.surat.kelahiran.pdf', 'edit' => 'admin.desa.surat.kelahiran.edit', 'delete' => 'admin.desa.surat.kelahiran.delete'],
                                    'kematian' => ['pdf' => 'admin.desa.surat.kematian.pdf', 'edit' => 'admin.desa.surat.kematian.edit', 'delete' => 'admin.desa.surat.kematian.delete'],
                                    'keramaian' => ['pdf' => 'admin.desa.surat.keramaian.pdf', 'edit' => 'admin.desa.surat.keramaian.edit', 'delete' => 'admin.desa.surat.keramaian.delete'],
                                    'rumah_sewa' => ['pdf' => 'admin.desa.surat.rumah-sewa.pdf', 'edit' => 'admin.desa.surat.rumah-sewa.edit', 'delete' => 'admin.desa.surat.rumah-sewa.delete'],
                                ];
                                $rt = $routes[$item['type']] ?? null;
                            @endphp
                            @if($rt)
                                <a href="{{ route($rt['pdf'], $item['id']) }}" class="text-blue-600 hover:text-blue-800" aria-label="Export PDF" target="_blank" title="Export PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                @if(!($item['is_accepted'] ?? false))
                                    <a href="{{ route($rt['edit'], $item['id']) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                                <form action="{{ route($rt['delete'], $item['id']) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Tidak ada data surat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    @php
                        $pagination = [
                            'current_page' => $items->currentPage(),
                            'items_per_page' => $items->perPage(),
                            'total_items' => $items->total()
                        ];

                        $currentPage = $pagination['current_page'];
                        $itemsPerPage = $pagination['items_per_page'];
                        $totalItems = $pagination['total_items'];
                        $startNumber = ($currentPage - 1) * $itemsPerPage + 1;
                        $endNumber = min($startNumber + $itemsPerPage - 1, $totalItems);
                    @endphp
                    Showing {{ $startNumber }} to {{ $endNumber }} of {{ $totalItems }} results
                </div>
                @if($items->lastPage() > 1)
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        @php
                            $totalPages = $items->lastPage();
                            $currentPage = $items->currentPage();

                            $startPage = 1;
                            $endPage = $totalPages;
                            $maxVisible = 7;

                            if ($totalPages > $maxVisible) {
                                $halfVisible = floor($maxVisible / 2);
                                $startPage = max($currentPage - $halfVisible, 1);
                                $endPage = min($startPage + $maxVisible - 1, $totalPages);

                                if ($endPage - $startPage < $maxVisible - 1) {
                                    $startPage = max($endPage - $maxVisible + 1, 1);
                                }
                            }
                        @endphp

                        @if($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}&search={{ request('search') }}&type={{ request('type') }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                Previous
                            </a>
                        @endif

                        @if($startPage > 1)
                            <a href="?page=1&search={{ request('search') }}&type={{ request('type') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                            @if($startPage > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                            @endif
                        @endif

                        @for($i = $startPage; $i <= $endPage; $i++)
                            <a href="?page={{ $i }}&search={{ request('search') }}&type={{ request('type') }}"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium {{ $i == $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-[#8c93d6]' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if($endPage < $totalPages)
                            @if($endPage < $totalPages - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                            @endif
                            <a href="?page={{ $totalPages }}&search={{ request('search') }}&type={{ request('type') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $totalPages }}</a>
                        @endif

                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}&search={{ request('search') }}&type={{ request('type') }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                Next
                            </a>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
</x-layout>


