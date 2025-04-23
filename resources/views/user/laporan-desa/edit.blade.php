<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Laporan</h1>

        <form action="{{ route('user.laporan-desa.update', $laporanDesa->id) }}" method="POST"
            enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Ruang Lingkup -->
                <div>
                    <label for="ruang_lingkup" class="block text-sm font-medium text-gray-700 mb-2">
                        Ruang Lingkup <span class="text-red-500">*</span>
                    </label>
                    <select id="ruang_lingkup" name="ruang_lingkup"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                        required>
                        <option value="" disabled>Pilih Ruang Lingkup</option>
                        @foreach($categories as $ruangLingkup => $bidang)
                            <option value="{{ $ruangLingkup }}" {{ $selectedRuangLingkup == $ruangLingkup ? 'selected' : '' }}>{{ $ruangLingkup }}</option>
                        @endforeach
                    </select>
                    @error('ruang_lingkup')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bidang -->
                <div>
                    <label for="lapor_desa_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Bidang <span class="text-red-500">*</span>
                    </label>
                    <select id="lapor_desa_id" name="lapor_desa_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                        required>
                        <option value="" disabled>Pilih Bidang</option>
                        @if($selectedRuangLingkup)
                            @foreach($categories[$selectedRuangLingkup] as $bidang)
                                <option value="{{ $bidang['id'] }}" {{ $laporanDesa->lapor_desa_id == $bidang['id'] ? 'selected' : '' }}>{{ $bidang['bidang'] }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('lapor_desa_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Judul Laporan -->
                <div class="col-span-1 md:col-span-2">
                    <label for="judul_laporan" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul_laporan" name="judul_laporan"
                        value="{{ old('judul_laporan', $laporanDesa->judul_laporan) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                        required>
                    @error('judul_laporan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Gambar -->
                <div class="col-span-1 md:col-span-2">
                    <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar</label>

                    @if($laporanDesa->gambar)
                        <div class="mt-2 mb-4">
                            <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                            <div class="relative inline-block">
                                <img src="{{ asset('storage/' . $laporanDesa->gambar) }}" alt="Gambar Laporan"
                                    class="w-40 h-auto object-cover rounded-md border-2 border-gray-200">
                            </div>
                        </div>
                    @endif

                    <div class="mt-2">
                        <label for="gambar"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk
                                        upload</span> atau drag and drop</p>
                                <p class="text-xs text-gray-500">JPG, PNG, GIF (MAX. 2MB)</p>
                            </div>
                            <input id="gambar" name="gambar" type="file" accept="image/*" class="hidden" />
                        </label>
                    </div>
                    <div id="file-preview" class="mt-2 hidden">
                        <p class="text-sm text-gray-600 mb-2">File yang dipilih: <span id="file-name"></span></p>
                        <img id="image-preview" class="max-h-40 w-auto rounded border border-gray-200" alt="Preview gambar">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Laporan -->
                <div class="col-span-1 md:col-span-2">
                    <label for="deskripsi_laporan" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Laporan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="deskripsi_laporan" name="deskripsi_laporan" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                        required>{{ old('deskripsi_laporan', $laporanDesa->deskripsi_laporan) }}</textarea>
                    @error('deskripsi_laporan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tag Map Location -->
                <div class="col-span-1 md:col-span-2">
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tag Lokasi Kejadian</h3>
                        <p class="text-sm text-gray-600">Tandai di mana kejadian berlangsung pada peta</p>
                    </div>

                    <!-- Custom Map Input Component with Full Width -->
                    <div>
                        <!-- Input Alamat -->
                        <div class="mb-4">
                            <label for="location_address" class="block text-sm font-medium text-gray-700 mb-2">Lokasi
                                Kejadian</label>
                            <textarea id="location_address" name="location_address" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
                                placeholder="Masukkan alamat">{{ old('location_address', $laporanDesa->lokasi) ?? '' }}</textarea>
                        </div>

                        <!-- Map Container with Improved Height -->
                        <div id="map-container-location_address"
                            class="my-4 rounded-lg overflow-hidden border border-gray-300">
                            <div id="map-location_address" class="w-full h-80 md:h-96 z-10"></div>
                        </div>

                        <!-- Latitude and Longitude Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tag_lat"
                                    class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                                <input type="text" id="tag_lat" name="tag_lat" value="{{ old('tag_lat', $lat) }}"
                                    readonly
                                    class="bg-gray-100 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                            </div>
                            <div>
                                <label for="tag_lng"
                                    class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                                <input type="text" id="tag_lng" name="tag_lng" value="{{ old('tag_lng', $lng) }}"
                                    readonly
                                    class="bg-gray-100 block w-full rounded-md  shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="tag_lokasi" name="tag_lokasi"
                        value="{{ old('tag_lokasi', $laporanDesa->tag_lokasi) ?? '' }}">
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-4 pt-6 border-t">
                <a href="{{ route('user.laporan-desa.index') }}"
                    class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none transition-colors duration-200 font-medium">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] focus:outline-none transition-colors duration-200 font-medium">
                    Perbarui Laporan
                </button>
            </div>
        </form>
    </div>

    @push('js-internal')
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-geosearch/dist/bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize map instance storage
                window.mapInstance = window.mapInstance || {};

                // Map initialization
                const mapId = 'map-location_address';
                const latInput = document.getElementById('tag_lat');
                const lngInput = document.getElementById('tag_lng');
                const tagLokasiInput = document.getElementById('tag_lokasi');

                // Function to initialize map
                function initMap() {
                    const mapElement = document.getElementById(mapId);
                    if (!mapElement) {
                        console.error('Map element not found: ' + mapId);
                        return;
                    }

                    // Remove existing map if needed
                    if (window.mapInstance[mapId]) {
                        window.mapInstance[mapId].remove();
                        window.mapInstance[mapId] = null;
                    }

                    // Get initial coordinates
                    let lat = parseFloat(latInput.value) || -7.310000;
                    let lng = parseFloat(lngInput.value) || 110.290000;

                    // Create new map
                    const map = L.map(mapId).setView([lat, lng], 13);
                    window.mapInstance[mapId] = map;
                    window.map = map; // Compatibility with older code

                    // Add OpenStreetMap base layer
                    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    // Add satellite layer option
                    const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: '© Esri'
                    });

                    // Add layer control
                    L.control.layers({
                        'Standard': osmLayer,
                        'Satellite': esriLayer
                    }).addTo(map);

                    // Add draggable marker
                    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    window.marker = marker;

                    // Update coordinates when marker is dragged
                    marker.on('dragend', function () {
                        const position = marker.getLatLng();
                        latInput.value = position.lat.toFixed(6);
                        lngInput.value = position.lng.toFixed(6);
                        updateTagLokasi();
                    });

                    // Add click event to set marker position
                    map.on('click', function (event) {
                        const { lat, lng } = event.latlng;
                        marker.setLatLng([lat, lng]);
                        latInput.value = lat.toFixed(6);
                        lngInput.value = lng.toFixed(6);
                        updateTagLokasi();
                    });

                    // Add search functionality
                    const provider = new window.GeoSearch.OpenStreetMapProvider();
                    const searchControl = new window.GeoSearch.GeoSearchControl({
                        provider: provider,
                        style: 'bar',
                        autoClose: true,
                        showMarker: false,
                    });
                    map.addControl(searchControl);

                    // Update marker when search result is selected
                    map.on('geosearch/showlocation', function (result) {
                        const { x: lng, y: lat } = result.location;
                        marker.setLatLng([lat, lng]);
                        latInput.value = lat.toFixed(6);
                        lngInput.value = lng.toFixed(6);
                        updateTagLokasi();
                        map.setView([lat, lng], 15);
                    });

                    // Ensure map renders correctly after initialization
                    setTimeout(() => {
                        map.invalidateSize();
                    }, 300);

                    return map;
                }

                // Update tag_lokasi hidden field
                function updateTagLokasi() {
                    const lat = latInput ? latInput.value.trim() : '';
                    const lng = lngInput ? lngInput.value.trim() : '';

                    if (lat && lng && tagLokasiInput) {
                        tagLokasiInput.value = `${lat}, ${lng}`;
                        console.log('Tag lokasi updated:', tagLokasiInput.value);
                    } else if (tagLokasiInput) {
                        tagLokasiInput.value = '';
                    }
                }

                // Initialize map
                setTimeout(initMap, 300);

                // Update tag_lokasi when lat/lng inputs change
                if (latInput) {
                    latInput.addEventListener('change', updateTagLokasi);
                    latInput.addEventListener('input', updateTagLokasi);
                }

                if (lngInput) {
                    lngInput.addEventListener('change', updateTagLokasi);
                    lngInput.addEventListener('input', updateTagLokasi);
                }

                // Update tag_lokasi on form submission
                document.querySelector('form').addEventListener('submit', function (e) {
                    updateTagLokasi();
                });

                // Set up file input preview
                const fileInput = document.getElementById('gambar');
                const filePreview = document.getElementById('file-preview');
                const fileName = document.getElementById('file-name');
                const imagePreview = document.getElementById('image-preview');

                fileInput.addEventListener('change', function () {
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        fileName.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + 'MB)';

                        // Create and set image preview
                        const fileReader = new FileReader();
                        fileReader.onload = function (e) {
                            imagePreview.src = e.target.result;
                        };
                        fileReader.readAsDataURL(file);

                        filePreview.classList.remove('hidden');

                        if (file.size > 2 * 1024 * 1024) {
                            alert('File terlalu besar! Maksimal 2MB');
                            fileInput.value = '';
                            filePreview.classList.add('hidden');
                        }
                    } else {
                        filePreview.classList.add('hidden');
                    }
                });

                // JSON object to store all categories
                const categories = @json($categories);

                // Add event listener for ruang_lingkup change
                const ruangLingkupSelect = document.getElementById('ruang_lingkup');
                ruangLingkupSelect.addEventListener('change', function () {
                    const ruangLingkup = this.value;
                    const bidangSelect = document.getElementById('lapor_desa_id');

                    // Clear previous options
                    bidangSelect.innerHTML = '<option value="" disabled selected>Pilih Bidang</option>';

                    // Add new options based on selected ruang_lingkup
                    if (ruangLingkup && categories[ruangLingkup]) {
                        categories[ruangLingkup].forEach(bidang => {
                            const option = document.createElement('option');
                            option.value = bidang.id;
                            option.textContent = bidang.bidang;
                            bidangSelect.appendChild(option);
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layout>