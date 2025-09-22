<x-layout>
    <div class="p-4 mt-14">
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-semibold">Produk Saya</h1>
                <a href="{{ route('user.warungku.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Tambah Produk</a>
            </div>

            <form method="GET" class="mb-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="flex-1 border rounded p-2" />
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded" title="Cari">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button type="button" id="toggleFilter" class="px-4 py-2 border rounded" title="Filter">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </div>

                <style>
                    .filter-panel{max-height:0;opacity:0;overflow:hidden;margin-top:0;transition:max-height .28s ease,opacity .22s ease,margin-top .28s ease}
                    .filter-panel.open{max-height:600px;opacity:1;margin-top:.75rem}
                </style>
                <div id="filterPanel" class="filter-panel grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Klasifikasi</label>
                        <select id="klasifikasiFilter" name="klasifikasi" class="w-full border rounded p-2">
                            <option value="">Semua Klasifikasi</option>
                            @foreach($klass as $k)
                                <option value="{{ $k }}" {{ request('klasifikasi') == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jenis</label>
                        <select id="jenisFilter" name="jenis_id" class="w-full border rounded p-2">
                            <option value="">Semua Jenis</option>
                            @foreach($jenis as $j)
                                <option value="{{ $j->id }}" data-klasifikasi="{{ $j->klasifikasi }}" {{ request('jenis_id') == $j->id ? 'selected' : '' }}>{{ $j->jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded" title="Terapkan Filter">
                            <i class="fa-solid fa-filter"></i>
                            <span class="ml-1 hidden md:inline">Terapkan</span>
                        </button>
                    </div>
                </div>
            </form>
            <div id="filterDivider" class="border-t border-gray-200 my-3"></div>

            @if(!$informasiUsaha)
                <div class="p-3 bg-yellow-50 text-yellow-700 rounded">Anda belum mengisi Informasi Usaha. Silakan isi pada form edit biodata di halaman profil.</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                @forelse($items as $item)
                    <div class="border rounded p-3">
                        <a href="{{ route('user.warungku.show', $item->id) }}" class="block">
                            <img src="{{ $item->foto_url ?? asset('images/statistik.jpg') }}" class="w-full h-40 object-cover rounded mb-2" />
                            <div class="font-medium hover:underline mb-1">{{ $item->nama_produk }}</div>
                        </a>
                        <div class="text-xs text-gray-600 mb-1">{{ Str::limit($item->deskripsi, 90) }}</div>
                        <div class="text-sm text-green-700">Rp {{ number_format($item->harga,0,',','.') }}</div>
                        <div class="text-xs text-gray-500">Stok: {{ $item->stok }}</div>
                        <div class="flex gap-2 mt-2">
                            <a href="{{ route('user.warungku.show', $item->id) }}" class="px-2 py-1 text-sm border rounded">Detail</a>
                            <a href="{{ route('user.warungku.edit', $item->id) }}" class="px-2 py-1 text-sm bg-green-600 text-white rounded">Edit</a>
                            <form method="POST" action="{{ route('user.warungku.destroy', $item->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 text-sm bg-red-600 text-white rounded">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">Belum ada produk.</div>
                @endforelse
            </div>

            @if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</x-layout>

<script>
    (function(){
        const panel = document.getElementById('filterPanel');
        const toggleBtn = document.getElementById('toggleFilter');
        const klas = document.getElementById('klasifikasiFilter');
        const jenis = document.getElementById('jenisFilter');
        if(toggleBtn && panel){
            toggleBtn.addEventListener('click', ()=> panel.classList.toggle('open'));
            const hasFilter = '{{ request('klasifikasi') || request('jenis_id') ? '1' : '' }}';
            if(hasFilter){ panel.classList.add('open'); }
        }
        if(klas && jenis){
            function apply(){
                const v = klas.value;
                Array.from(jenis.options).forEach(opt => {
                    if(!opt.value){ opt.hidden=false; return; }
                    const group = opt.getAttribute('data-klasifikasi');
                    opt.hidden = v && group !== v;
                });
                const sel = jenis.options[jenis.selectedIndex];
                if(sel && sel.getAttribute('data-klasifikasi') && sel.getAttribute('data-klasifikasi') !== v){ jenis.value=''; }
            }
            klas.addEventListener('change', apply); apply();
        }
    })();
</script>


