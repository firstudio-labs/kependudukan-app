<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengumuman Desa</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('user.pengumuman.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari pengumuman..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($pengumuman as $item)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                    @if($item->gambar)
                        <img src="{{ asset('storage/'.$item->gambar) }}" alt="{{ strip_tags($item->judul) }}" class="h-40 w-full object-cover">
                    @else
                        <div class="h-40 w-full bg-gray-100 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 line-clamp-2">{{ Str::of(html_entity_decode($item->judul))->stripTags() }}</h3>
                        <p class="text-sm text-gray-600 mt-2 line-clamp-3">{{ Str::limit(strip_tags(html_entity_decode($item->deskripsi)), 150) }}</p>
                        <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $item->created_at->format('d M Y') }}</span>
                            <button onclick="showDetail({{ $item->id }})" class="text-blue-600 hover:text-blue-800">Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">Belum ada pengumuman.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $pengumuman->links('pagination::tailwind') }}</div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="relative w-full max-w-3xl bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Detail Pengumuman</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <h4 id="dJudul" class="text-lg font-bold text-gray-900"></h4>
                    <div id="dDeskripsi" class="prose max-w-none"></div>
                    <div id="dGambar" class="mt-2"></div>
                </div>
                <div class="flex items-center justify-end p-4 border-t">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(id) {
            const modal = document.getElementById('detailModal');
            const j = document.getElementById('dJudul');
            const d = document.getElementById('dDeskripsi');
            const g = document.getElementById('dGambar');
            j.textContent = 'Memuat...';
            d.textContent = 'Memuat...';
            g.innerHTML = '';
            modal.classList.remove('hidden');
            fetch(`/user/pengumuman/${id}`)
                .then(r=>r.json())
                .then(x=>{
                    if (x.status === 'success') {
                        j.textContent = x.data.judul || '-';
                        d.innerHTML = x.data.deskripsi || '-';
                        if (x.data.gambar) {
                            g.innerHTML = `<img src="/storage/${x.data.gambar}" alt="Gambar" class="max-h-64 rounded">`;
                        } else {
                            g.innerHTML = '<span class="text-sm text-gray-500">Tidak ada gambar</span>';
                        }
                    } else { throw new Error('bad'); }
                })
                .catch(()=>{ j.textContent='Gagal memuat'; d.textContent='-'; });
        }
        function closeModal(){ document.getElementById('detailModal').classList.add('hidden'); }
    </script>
</x-layout>


