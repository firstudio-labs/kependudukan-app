<x-layout>
    <div class="p-4 mt-14">
        <div class="bg-white p-4 rounded shadow max-w-2xl mx-auto">
            <h1 class="text-xl font-semibold mb-4">{{ $item ? 'Edit' : 'Tambah' }} Produk</h1>
            
            <style>
                /* Pastikan dropdown bisa menampilkan semua item dengan scroll */
                #klasifikasiSelect, #jenisSelect {
                    max-height: none !important;
                }
                
                /* Untuk browser yang membatasi tinggi select */
                select {
                    max-height: none !important;
                }
                
                /* Pastikan option bisa di-scroll */
                select option {
                    padding: 8px 12px;
                    white-space: nowrap;
                    overflow: visible;
                }
            </style>
            <form method="POST" action="{{ $item ? route('user.warungku.update', $item->id) : route('user.warungku.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-3">
                @csrf
                @if($item)
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-sm text-gray-700">Nama Produk</label>
                    <input name="nama_produk" value="{{ old('nama_produk', $item->nama_produk ?? '') }}" class="mt-1 w-full border rounded p-2" required />
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Klasifikasi</label>
                    <select id="klasifikasiSelect" class="mt-1 w-full border rounded p-2">
                        <option value="">Pilih Klasifikasi</option>
                        @foreach($klass as $k)
                            <option value="{{ $k }}" {{ (string)old('klasifikasi', ($item ? ($jenis->firstWhere('id', $item->jenis_master_id)->klasifikasi ?? '') : '')) === (string)$k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Jenis Barang/Jasa</label>
                    <select id="jenisSelect" name="jenis_master_id" class="mt-1 w-full border rounded p-2" size="1">
                        <option value="">Pilih Jenis</option>
                        @foreach($jenis as $j)
                            <option value="{{ $j->id }}" data-klasifikasi="{{ $j->klasifikasi }}" {{ (string)old('jenis_master_id', $item->jenis_master_id ?? '') === (string)$j->id ? 'selected' : '' }}>{{ $j->jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="mt-1 w-full border rounded p-2">{{ old('deskripsi', $item->deskripsi ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-700">Harga</label>
                        <input type="number" name="harga" step="0.01" min="0" value="{{ old('harga', $item->harga ?? 0) }}" class="mt-1 w-full border rounded p-2" required />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Stok</label>
                        <input type="number" name="stok" min="0" value="{{ old('stok', $item->stok ?? 0) }}" class="mt-1 w-full border rounded p-2" required />
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Foto</label>
                    <input id="fotoInput" type="file" name="foto" accept="image/jpeg,image/png" class="mt-1 w-full border rounded p-2 bg-white" />
                    @if($item && $item->foto_url)
                        <img id="fotoPreview" src="{{ $item->foto_url }}" class="mt-2 h-32 rounded" />
                    @else
                        <img id="fotoPreview" class="mt-2 h-32 rounded hidden" />
                    @endif
                </div>
                <div class="flex gap-2 justify-end">
                    <a href="{{ route('user.warungku.my') }}" class="px-4 py-2 border rounded">Batal</a>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>

<script>
    // Dependent Jenis by klasifikasi in form
    (function(){
        const klas = document.getElementById('klasifikasiSelect');
        const jenis = document.getElementById('jenisSelect');
        if(klas && jenis){
            // Set klasifikasi berdasarkan atribut data-klasifikasi dari jenis terpilih saat edit
            const selectedGroup = jenis.selectedOptions[0]?.getAttribute('data-klasifikasi') || '';
            if(selectedGroup){
                klas.value = selectedGroup;
            }
            function apply(){
                const grup = klas.value;
                Array.from(jenis.options).forEach(opt => {
                    if(!opt.value){ opt.hidden = false; return; }
                    const group = opt.getAttribute('data-klasifikasi');
                    opt.hidden = grup && group !== grup;
                });
                const sel = jenis.options[jenis.selectedIndex];
                if(sel && sel.getAttribute('data-klasifikasi') && sel.getAttribute('data-klasifikasi') !== grup){
                    jenis.value = '';
                }
            }
            klas.addEventListener('change', apply);
            apply();
        }
        // Preview foto
        const input = document.getElementById('fotoInput');
        const preview = document.getElementById('fotoPreview');
        if(input && preview){
            input.addEventListener('change', () => {
                const f = input.files && input.files[0];
                if(!f){ preview.classList.add('hidden'); return; }
                const url = URL.createObjectURL(f);
                preview.src = url; preview.classList.remove('hidden');
            });
        }
    })();
</script>


