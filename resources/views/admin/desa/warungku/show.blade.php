<x-layout>
    <div class="p-4 mt-14">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Detail Informasi Usaha</h1>
            <a href="{{ route('admin.desa.warungku.index') }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5">Kembali</a>
        </div>

        <div class="bg-white border rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="mb-2"><span class="text-gray-500">Nama Usaha:</span> {{ $item->nama_usaha }}</div>
                <div class="mb-2"><span class="text-gray-500">Kelompok Usaha:</span> {{ $item->kelompok_usaha ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Alamat:</span> {{ $item->alamat ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Tag Lokasi:</span> {{ $item->tag_lokasi ?? '-' }}</div>
            </div>
            <div>
                <div class="mb-3">
                    <div class="w-40 h-40 rounded overflow-hidden bg-gray-100">
                        @if($item->foto_url)
                            <img src="{{ $item->foto_url }}" alt="Foto" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">Tidak ada foto</div>
                        @endif
                    </div>
                </div>
                <div class="mb-2"><span class="text-gray-500">Pemilik:</span> {{ $ownerName ?? optional($item->penduduk)->nama ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">NIK:</span> {{ optional($item->penduduk)->nik ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="font-semibold text-[#2D336B] mb-2">Produk (Warungku)</h2>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">Nama Produk</th>
                            <th class="px-6 py-3">Klasifikasi</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->barangWarungkus as $produk)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $produk->nama_produk }}</td>
                                <td class="px-6 py-3">{{ optional($produk->warungkuMaster)->klasifikasi ?? '-' }}</td>
                                <td class="px-6 py-3">{{ optional($produk->warungkuMaster)->jenis ?? '-' }}</td>
                                <td class="px-6 py-3">{{ number_format($produk->harga,0,',','.') }}</td>
                                <td class="px-6 py-3">{{ $produk->stok }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-500">Belum ada produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>


