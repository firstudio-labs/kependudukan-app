<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengguna Mobile</h1>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK / No HP"
                       class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">No HP</th>
                        <th class="px-6 py-3">Provinsi</th>
                        <th class="px-6 py-3">Kabupaten</th>
                        <th class="px-6 py-3">Kecamatan</th>
                        <th class="px-6 py-3">Desa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">{{ $row->nik }}</td>
                            <td class="px-6 py-4">{{ $row->full_name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->no_hp }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['provinsi'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['kabupaten'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['kecamatan'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['desa'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6 text-center text-gray-500">Tidak ada data.</td>
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


