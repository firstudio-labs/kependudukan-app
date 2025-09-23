<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Sarana Umum</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data sarana umum.</p>

        <form method="POST" action="{{ route('admin.desa.sarana-umum.update', $item) }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis Sarana <span class="text-red-500">*</span></label>
                    <select id="jenis-sarana" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Jenis</option>
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ $item->kategori?->jenis_sarana === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kategori Sarana <span class="text-red-500">*</span></label>
                    <select name="kategori_sarana_id" id="kategori-sarana" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Kategori</option>
                    </select>
                    @error('kategori_sarana_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nama Sarana <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sarana" value="{{ old('nama_sarana', $item->nama_sarana) }}" required placeholder="Nama Sarana"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama_sarana')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Alamat (Tag Lokasi)</label>
                    <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $item->alamat) }}" placeholder="Koordinat atau alamat"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('alamat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Tag Lokasi (Koordinat)</label>
                    <input type="text" id="tag_lokasi" name="tag_lokasi" value="{{ old('tag_lokasi', $item->tag_lokasi) }}" placeholder="Lat,Lng" readonly
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-100 p-2.5" />
                    @error('tag_lokasi')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    <div class="mt-3">
                        <div class="flex items-center gap-2">
                            <input type="text" id="map-search" placeholder="Cari alamat/lokasi..." class="flex-1 rounded-lg border border-gray-300 p-2.5 text-sm focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                            <button type="button" id="map-search-btn" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari Lokasi</button>
                        </div>
                        <div id="map-search-results" class="mt-2 hidden border border-gray-200 rounded-lg bg-white max-h-48 overflow-y-auto text-sm"></div>
                    </div>
                    <div id="map" class="mt-3 h-56 w-full rounded border"></div>
                    <p class="text-xs text-gray-500 mt-2">Klik pada peta untuk memilih titik. Koordinat akan terisi, alamat diisi otomatis dan bisa dilengkapi.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" name="kontak" value="{{ old('kontak', $item->kontak) }}" placeholder="Nomor/WA/Email (opsional)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('kontak')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        async function loadKategoriByJenis(jenis, selectedId = null) {
            const kategoriSelect = document.getElementById('kategori-sarana');
            kategoriSelect.innerHTML = '<option value="">Memuat...</option>';
            if (!jenis) { kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>'; return; }
            try {
                const res = await fetch(`{{ route('admin.desa.kategori-sarana.by-jenis') }}?jenis_sarana=${encodeURIComponent(jenis)}`);
                const data = await res.json();
                kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>' + data.map(function(item){
                    const sel = (String(selectedId) === String(item.id)) ? 'selected' : '';
                    return `<option value="${item.id}" ${sel}>${item.kategori}</option>`;
                }).join('');
            } catch (e) {
                kategoriSelect.innerHTML = '<option value="">Gagal memuat</option>';
            }
        }
        document.addEventListener('DOMContentLoaded', function(){
            const jenisSelect = document.getElementById('jenis-sarana');
            const currentJenis = jenisSelect.value;
            const currentKategoriId = '{{ $item->kategori_sarana_id }}';
            loadKategoriByJenis(currentJenis, currentKategoriId);
            jenisSelect.addEventListener('change', function(){ loadKategoriByJenis(jenisSelect.value); });

            // Peta
            const tagLokasiInput = document.getElementById('tag_lokasi');
            const alamatInput = document.getElementById('alamat');
            const mapContainer = document.getElementById('map');
            const defaultLatLng = (tagLokasiInput.value && tagLokasiInput.value.includes(',')) ? tagLokasiInput.value.split(',').map(function(x){return parseFloat(x.trim());}) : [-6.1753924,106.8271528];
            const map = L.map('map').setView(defaultLatLng, 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            let marker = L.marker(defaultLatLng).addTo(map);
            // Paksa render penuh untuk menghindari peta kecil/abu-abu saat di server
            setTimeout(function(){ map.invalidateSize(); }, 300);
            setTimeout(function(){ map.invalidateSize(); }, 800);
            window.addEventListener('load', function(){ map.invalidateSize(); });
            if (document.fonts && document.fonts.ready) { document.fonts.ready.then(function(){ map.invalidateSize(); }); }
            if (window.ResizeObserver && mapContainer) { const ro = new ResizeObserver(function(){ map.invalidateSize(); }); ro.observe(mapContainer); }
            async function reverseGeocode(lat, lng){
                try { const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`); const data = await res.json(); return data.display_name || ''; } catch(e){ return ''; }
            }
            async function forwardGeocode(query){
                try { const res = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}`); const data = await res.json(); return Array.isArray(data) ? data : []; } catch(e){ return []; }
            }
            function renderSearchResults(results){
                const mapSearchResults = document.getElementById('map-search-results');
                if (!results || results.length === 0) { mapSearchResults.classList.add('hidden'); mapSearchResults.innerHTML = ''; return; }
                const limited = results.slice(0, 8);
                mapSearchResults.innerHTML = limited.map(function(r){
                    const name = r.display_name || `${r.lat},${r.lon}`;
                    return `<button type=\"button\" data-lat=\"${r.lat}\" data-lon=\"${r.lon}\" data-name=\"${name.replace(/\"/g,'&quot;')}\" class=\"w-full text-left px-3 py-2 hover:bg-gray-50 border-b last:border-b-0\">${name}</button>`;
                }).join('');
                mapSearchResults.classList.remove('hidden');
                mapSearchResults.querySelectorAll('button').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        const lat = parseFloat(this.getAttribute('data-lat'));
                        const lon = parseFloat(this.getAttribute('data-lon'));
                        const name = this.getAttribute('data-name');
                        map.setView([lat, lon], 16);
                        marker.setLatLng([lat, lon]);
                        tagLokasiInput.value = `${lat.toFixed(6)},${lon.toFixed(6)}`;
                        alamatInput.value = name;
                        mapSearchResults.classList.add('hidden');
                    });
                });
            }
            map.on('click', async function(e){
                const lat = e.latlng.lat.toFixed(6); const lng = e.latlng.lng.toFixed(6);
                marker.setLatLng(e.latlng);
                tagLokasiInput.value = `${lat},${lng}`;
                const addr = await reverseGeocode(lat,lng);
                if (addr) alamatInput.value = addr;
            });
            const mapSearchInput = document.getElementById('map-search');
            const mapSearchBtn = document.getElementById('map-search-btn');
            mapSearchBtn.addEventListener('click', async function(){
                const q = (mapSearchInput.value || '').trim(); if (!q) return;
                const results = await forwardGeocode(q);
                renderSearchResults(results);
            });
            mapSearchInput.addEventListener('keydown', async function(e){ if (e.key === 'Enter') { e.preventDefault(); mapSearchBtn.click(); } });
        });
    </script>
</x-layout>


