<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengguna Mobile - {{ $levelInfo['name'] }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $levelInfo['breadcrumb'] }}</p>
            </div>
            <a href="{{ route('superadmin.mobile-users.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Kembali</a>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">No HP</th>
                        <th class="px-6 py-3">No KK</th>
                        <th class="px-6 py-3">Provinsi</th>
                        <th class="px-6 py-3">Kabupaten/Kota</th>
                        <th class="px-6 py-3">Kecamatan</th>
                        <th class="px-6 py-3">Desa/Kelurahan</th>
                        <th class="px-6 py-3 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">{{ $row->nik }}</td>
                            <td class="px-6 py-4">{{ $row->full_name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->no_hp }}</td>
                            <td class="px-6 py-4">{{ $row->kk ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['provinsi'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['kabupaten'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['kecamatan'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->wilayah['desa'] ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('superadmin.mobile-users.show', $row->nik) }}" class="px-3 py-1 text-xs rounded bg-[#7886C7] text-white hover:bg-[#2D336B]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-6 text-center text-gray-500">Tidak ada data pengguna mobile.</td>
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
