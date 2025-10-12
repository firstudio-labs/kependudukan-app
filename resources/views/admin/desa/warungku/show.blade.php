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
                <div class="mb-2"><span class="text-gray-500">Provinsi:</span> {{ $item->province_name ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Kabupaten:</span> {{ $item->district_name ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Kecamatan:</span> {{ $item->sub_district_name ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Desa:</span> {{ $item->village_name ?? '-' }}</div>
                <div class="mb-2"><span class="text-gray-500">Tag Lokasi:</span> {{ $item->tag_lokasi ?? '-' }}</div>
                <div class="mb-2">
                    <span class="text-gray-500">Status:</span> 
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
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
                <div class="mb-2"><span class="text-gray-500">No HP:</span> {{ optional($item->penduduk)->no_hp ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="font-semibold text-[#2D336B] mb-2">Produk (Warungku)</h2>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">Foto</th>
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
                                <td class="px-6 py-3">
                                    <div class="w-16 h-16 rounded overflow-hidden bg-gray-100">
                                        @if($produk->foto_url)
                                            <img src="{{ $produk->foto_url }}" alt="Foto {{ $produk->nama_produk }}" 
                                                 class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-200"
                                                 onclick="showImageModal('{{ $produk->foto_url }}', '{{ $produk->nama_produk }}')" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 font-medium">{{ $produk->nama_produk }}</td>
                                <td class="px-6 py-3">{{ optional($produk->warungkuMaster)->klasifikasi ?? '-' }}</td>
                                <td class="px-6 py-3">{{ optional($produk->warungkuMaster)->jenis ?? '-' }}</td>
                                <td class="px-6 py-3 font-medium">Rp {{ number_format($produk->harga,0,',','.') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-sm font-medium">{{ $produk->stok }} unit</span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $produk->stok > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $produk->stok > 0 ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500">Belum ada produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>

<script>
// Fungsi untuk menampilkan foto dalam modal
function showImageModal(imageSrc, title) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-4xl max-h-full overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">${title}</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <img src="${imageSrc}" alt="${title}" class="max-w-full max-h-96 mx-auto rounded">
        </div>
    `;
    document.body.appendChild(modal);
    
    // Tutup modal saat klik di luar gambar
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}
</script>
