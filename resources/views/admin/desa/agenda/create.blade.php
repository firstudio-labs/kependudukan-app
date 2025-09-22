<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buat Agenda Desa</h1>
            <a href="{{ route('admin.desa.agenda.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
        </div>

        <form action="{{ route('admin.desa.agenda.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Judul</label>
                    <input type="text" name="judul" class="w-full p-2 border border-gray-300 rounded" required>
                    @error('judul')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Gambar</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full p-2 border border-gray-300 rounded" onchange="previewAgendaGambar(this)">
                    <img id="preview-agenda-gambar" class="h-24 mt-2 hidden">
                    @error('gambar')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-600 text-sm font-medium mb-1">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="w-full p-2 border border-gray-300 rounded" rows="8"></textarea>
                    @error('deskripsi')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-600 text-sm font-medium mb-1">Peta Lokasi</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" id="map-search" placeholder="Cari lokasi (contoh: Balai Desa, Jl...)"
                               class="w-full p-2 border border-gray-300 rounded">
                        <button type="button" id="map-search-btn" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
                    </div>
                    <div id="map" class="w-full h-64 rounded border border-gray-300"></div>
                    <div id="map-search-results" class="mt-2 bg-white border border-gray-200 rounded hidden"></div>
                </div>

                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Tag Lokasi (lat,lng)</label>
                    <input type="text" id="tag_lokasi" name="tag_lokasi" class="w-full p-2 border border-gray-300 rounded" placeholder="-6.2,106.8">
                    @error('tag_lokasi')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
                    @error('alamat')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                    <p class="text-xs text-gray-500 mt-1">Alamat otomatis dari marker, tetap bisa disunting.</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>

    <style>
        .ck-editor__editable[role="textbox"] { min-height: 300px; }
    </style>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('deskripsi')) {
                ClassicEditor.create(document.querySelector('#deskripsi'), {
                    toolbar: {
                        items: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','|','undo','redo']
                    }
                }).then(editor => {
                    editor.ui.view.editable.element.style.minHeight = '300px';
                }).catch(() => {});
            }
        });

        function previewAgendaGambar(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview-agenda-gambar');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tagLokasiInput = document.getElementById('tag_lokasi');
            const alamatTextarea = document.getElementById('alamat');
            const searchInput = document.getElementById('map-search');
            const searchBtn = document.getElementById('map-search-btn');
            const searchResults = document.getElementById('map-search-results');

            let defaultLat = -6.1753924;
            let defaultLng = 106.8271528;

            const map = L.map('map').setView([defaultLat, defaultLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            const marker = L.marker([defaultLat, defaultLng], { draggable: false }).addTo(map);

            function setTagLokasi(lat, lng) { if (tagLokasiInput) tagLokasiInput.value = lat.toFixed(7)+','+lng.toFixed(7); }
            function reverseGeocode(lat, lng) {
                if (alamatTextarea) alamatTextarea.value = '';
                const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
                fetch(url, { headers: { 'Accept': 'application/json', 'User-Agent': 'kependudukan-app/1.0' }})
                .then(r=>r.json()).then(data=>{ if (data && data.display_name && alamatTextarea) alamatTextarea.value = data.display_name; }).catch(()=>{});
            }
            setTagLokasi(defaultLat, defaultLng);
            map.on('click', function (e) { const p=e.latlng; marker.setLatLng(p); setTagLokasi(p.lat,p.lng); reverseGeocode(p.lat,p.lng); });

            function performSearch(query) {
                if (!query || query.trim().length<3) { if (searchResults){ searchResults.classList.add('hidden'); searchResults.innerHTML=''; } return; }
                const url='https://nominatim.openstreetmap.org/search?format=jsonv2&q='+encodeURIComponent(query)+'&limit=5';
                fetch(url,{ headers:{ 'Accept':'application/json','User-Agent':'kependudukan-app/1.0'}})
                .then(r=>r.json()).then(list=>{
                    if (!Array.isArray(list)||!searchResults) return;
                    searchResults.innerHTML='';
                    list.forEach(item=>{
                        const row=document.createElement('div');
                        row.className='p-2 hover:bg-gray-50 cursor-pointer text-sm';
                        row.textContent=item.display_name;
                        row.addEventListener('click',function(){ const lat=parseFloat(item.lat),lon=parseFloat(item.lon); marker.setLatLng([lat,lon]); map.setView([lat,lon],17); setTagLokasi(lat,lon); reverseGeocode(lat,lon); searchResults.classList.add('hidden'); searchResults.innerHTML=''; });
                        searchResults.appendChild(row);
                    });
                    searchResults.classList.remove('hidden');
                }).catch(()=>{});
            }
            if (searchBtn && searchInput) {
                searchBtn.addEventListener('click', function(){ performSearch(searchInput.value); });
                searchInput.addEventListener('keydown', function(e){ if (e.key==='Enter'){ e.preventDefault(); performSearch(searchInput.value); } });
                document.addEventListener('click', function(e){ if (searchResults && !searchResults.contains(e.target) && e.target!==searchInput && e.target!==searchBtn){ searchResults.classList.add('hidden'); }});
            }
        });
    </script>
</x-layout>


