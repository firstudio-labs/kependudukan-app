<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">APBDES</h1>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" action="" class="flex items-center gap-3">
                <select name="jenis" onchange="this.form.submit()" class="block p-2 pr-8 w-48 text-sm bg-white rounded-lg border border-gray-300">
                    <option value="">Semua Jenis</option>
                    @foreach($allowedJenis as $j)
                        <option value="{{ $j }}" {{ request('jenis')===$j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <select name="kategori" onchange="this.form.submit()" class="block p-2 pr-8 w-72 text-sm bg-white rounded-lg border border-gray-300">
                    <option value="">Semua Kategori</option>
                    @foreach($allowedKategori as $k)
                        <option value="{{ $k }}" {{ request('kategori')===$k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
                <div class="relative flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..." class="block p-2 pl-3 w-64 text-sm bg-gray-50 rounded-lg border border-gray-300" />
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
                </div>
            </form>
            <a href="{{ route('admin.desa.abdes.create') }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5">Tambah Data</a>
        </div>

        <div class="mb-3 text-sm text-gray-700">Total Anggaran (sesuai filter): <span class="font-semibold">Rp {{ number_format($totalAnggaran, 2, ',', '.') }}</span></div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Jumlah Anggaran</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">{{ $item->jenis }}</td>
                            <td class="px-6 py-4">{{ $item->kategori }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($item->jumlah_anggaran, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.desa.abdes.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 mr-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('admin.desa.abdes.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-[#f7f7fa] text-gray-800 font-semibold">
                        <td class="px-6 py-3" colspan="3">Total Anggaran (sesuai filter)</td>
                        <td class="px-6 py-3">Rp {{ number_format($totalAnggaran, 2, ',', '.') }}</td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</x-layout>


