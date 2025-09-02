<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengumuman</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('admin.desa.pengumuman.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari pengumuman..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>

            <div>
                <a href="{{ route('admin.desa.pengumuman.create') }}"
                    class="flex items-center justify-center bg-[#7886C7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#2D336B] transition duration-300 ease-in-out">
                    <i class="fa-solid fa-plus mr-2"></i>
                    <span>Tambah Pengumuman</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Judul</th>
                            <th class="px-6 py-3">Deskripsi</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengumuman as $index => $item)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $pengumuman->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ Str::of(html_entity_decode($item->judul))->stripTags() }}</td>
                                <td class="px-6 py-4">{{ Str::limit(strip_tags(html_entity_decode($item->deskripsi)), 80) }}</td>
                                <td class="px-6 py-4">{{ $item->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <button onclick="showDetailPengumuman({{ $item->id }})" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fa-solid fa-eye"></i></button>
                                    <a href="{{ route('admin.desa.pengumuman.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-800 mr-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="{{ route('admin.desa.pengumuman.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6">Belum ada pengumuman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengumuman->count() > 0)
                <div class="px-4 py-3">{{ $pengumuman->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Pengumuman -->
    <div id="detailPengumumanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="relative w-full max-w-3xl bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Detail Pengumuman</h3>
                    <button type="button" onclick="closeDetailPengumuman()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Judul:</p>
                        <div id="detailJudulPengumuman" class="text-base text-gray-900"></div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Deskripsi:</p>
                        <div id="detailDeskripsiPengumuman" class="prose max-w-none"></div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Gambar:</p>
                        <div id="detailGambarPengumuman"></div>
                    </div>
                </div>
                <div class="flex items-center justify-end p-4 border-t">
                    <button type="button" onclick="closeDetailPengumuman()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetailPengumuman(id) {
            const modal = document.getElementById('detailPengumumanModal');
            const judul = document.getElementById('detailJudulPengumuman');
            const deskripsi = document.getElementById('detailDeskripsiPengumuman');
            const gambar = document.getElementById('detailGambarPengumuman');
            judul.textContent = 'Memuat...';
            deskripsi.textContent = 'Memuat...';
            gambar.innerHTML = '';
            modal.classList.remove('hidden');

            fetch(`/admin/desa/pengumuman/${id}`)
                .then(r => r.json())
                .then(d => {
                    if (d.status === 'success') {
                        judul.textContent = d.data.judul || '-';
                        deskripsi.innerHTML = d.data.deskripsi || '-';
                        if (d.data.gambar) {
                            gambar.innerHTML = `<img src="/storage/${d.data.gambar}" alt="Gambar" class="max-h-64 rounded">`;
                        } else {
                            gambar.innerHTML = '<span class="text-sm text-gray-500">Tidak ada gambar</span>';
                        }
                    } else {
                        throw new Error('Gagal memuat detail');
                    }
                })
                .catch(() => {
                    judul.textContent = 'Gagal memuat';
                    deskripsi.textContent = '-';
                });
        }
        function closeDetailPengumuman() { document.getElementById('detailPengumumanModal').classList.add('hidden'); }
    </script>
</x-layout>


