<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Warungku - Informasi Usaha Desa</h1>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama usaha/alamat"
                       class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <input type="text" name="kelompok" value="{{ $filters['kelompok'] ?? '' }}" placeholder="Kelompok usaha"
                       class="block p-2 pl-3 w-48 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">Nama Usaha</th>
                        <th class="px-6 py-3">Kelompok</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">Pemilik</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <div class="w-12 h-12 rounded overflow-hidden bg-gray-100">
                                    @if($item->foto_url)
                                        <img src="{{ $item->foto_url }}" alt="Foto" class="w-full h-full object-cover rounded">
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $item->nama_usaha }}</td>
                            <td class="px-6 py-4">{{ $item->kelompok_usaha ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->alamat ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->owner_name ?? optional($item->penduduk)->nama ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.desa.warungku.show', $item->id) }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-xs px-4 py-2">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-gray-500">Belum ada data usaha.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</x-layout>
