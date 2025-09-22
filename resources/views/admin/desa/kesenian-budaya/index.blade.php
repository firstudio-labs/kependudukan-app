<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Kesenian & Budaya</h1>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" action="" class="flex items-center gap-3">
                <select name="jenis" onchange="this.form.submit()"
                        class="block p-2 pr-8 w-56 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                    <option value="">Semua Jenis</option>
                    @foreach($allowedJenis as $j)
                        <option value="{{ $j }}" {{ request('jenis')===$j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <div class="relative flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/alamat/kontak..."
                           class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
                </div>
            </form>
            <a href="{{ route('admin.desa.kesenian-budaya.create') }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5">Tambah Data</a>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Alamat</th>
                        <th class="px-6 py-3">Kontak</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">{{ $item->jenis }}</td>
                            <td class="px-6 py-4">{{ $item->nama }}</td>
                            <td class="px-6 py-4">{{ $item->alamat ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->kontak ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.desa.kesenian-budaya.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('admin.desa.kesenian-budaya.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data.</td>
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


