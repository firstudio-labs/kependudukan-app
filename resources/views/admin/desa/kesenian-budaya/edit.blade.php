<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Kesenian & Budaya</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data dan lokasi.</p>

        <form method="POST" action="{{ route('admin.desa.kesenian-budaya.update', $item) }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ old('jenis', $item->jenis)===$j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jenis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $item->nama) }}" required placeholder="Nama Kesenian/Budaya"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $item->alamat) }}" placeholder="Alamat (otomatis dari peta, bisa dilengkapi)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('alamat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" name="kontak" value="{{ old('kontak', $item->kontak) }}" placeholder="Nomor/WA/Email (opsional)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('kontak')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
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
                    <p class="text-xs text-gray-500 mt-2">Klik peta atau pilih hasil pencarian. Koordinat dan alamat akan terisi otomatis.</p>
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
        document.addEventListener('DOMContentLoaded', function(){
            const tagLokasiInput = document.getElementById('tag_lokasi');
            const alamatInput = document.getElementById('alamat');
            const defaultLatLng = (tagLokasiInput.value && tagLokasiInput.value.includes(',')) ? tagLokasiInput.value.split(',').map(function(x){return parseFloat(x.trim());}) : [-6.1753924,106.8271528];
            const map = L.map('map').setView(defaultLatLng, 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            let marker = L.marker(defaultLatLng).addTo(map);
            async function reverseGeocode(lat,lng){ try { const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`); const d = await r.json(); return d.display_name || ''; } catch(e){ return ''; } }
            async function forwardGeocode(q){ try { const r = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(q)}`); const d = await r.json(); return Array.isArray(d) ? d : []; } catch(e){ return []; } }
            function renderResults(results){
                const box = document.getElementById('map-search-results');
                if (!results || results.length===0) { box.classList.add('hidden'); box.innerHTML=''; return; }
                box.innerHTML = results.slice(0,8).map(function(r){ const name=r.display_name || `${r.lat},${r.lon}`; return `<button type=\"button\" data-lat=\"${r.lat}\" data-lon=\"${r.lon}\" data-name=\"${name.replace(/\"/g,'&quot;')}\" class=\"w-full text-left px-3 py-2 hover:bg-gray-50 border-b last:border-b-0\">${name}</button>`; }).join('');
                box.classList.remove('hidden');
                box.querySelectorAll('button').forEach(function(btn){ btn.addEventListener('click', function(){ const lat=parseFloat(this.getAttribute('data-lat')); const lon=parseFloat(this.getAttribute('data-lon')); const name=this.getAttribute('data-name'); map.setView([lat,lon],16); marker.setLatLng([lat,lon]); tagLokasiInput.value=`${lat.toFixed(6)},${lon.toFixed(6)}`; alamatInput.value=name; box.classList.add('hidden'); }); });
            }
            map.on('click', async function(e){ const lat=e.latlng.lat.toFixed(6); const lng=e.latlng.lng.toFixed(6); marker.setLatLng(e.latlng); tagLokasiInput.value=`${lat},${lng}`; const addr=await reverseGeocode(lat,lng); if (addr) alamatInput.value=addr; });
            const input = document.getElementById('map-search'); const btn = document.getElementById('map-search-btn');
            btn.addEventListener('click', async function(){ const q=(input.value||'').trim(); if(!q) return; const results=await forwardGeocode(q); renderResults(results); });
            input.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); btn.click(); }});
        });
    </script>
</x-layout>


