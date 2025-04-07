@props([
    'label',
    'addressId',
    'addressName',
    'address' => '',
    'latitudeId',
    'latitudeName',
    'latitude' => '',
    'longitudeId',
    'longitudeName',
    'longitude' => '',
    'modalId' // Tambahkan prop untuk ID modal
])

<div>
    <!-- Input Alamat dengan Label Dinamis -->
    <x-textarea
        :label="$label"
        :id="$addressId"
        :name="$addressName"
        rows="4"
        placeholder="Masukkan alamat"
        required
        :value="$address"
    />

    <!-- Container untuk Peta -->
    <div id="map-container-{{ $addressId }}" class="my-4 rounded-lg overflow-hidden">
        <div id="map-{{ $addressId }}" class="z-10 h-72 w-full"></div>
    </div>

    <!-- Input Latitude dan Longitude -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input :label="'Latitude'" :id="$latitudeId" :name="$latitudeName" type="text" readonly :value="$latitude" />
        <x-input :label="'Longitude'" :id="$longitudeId" :name="$longitudeName" type="text" readonly :value="$longitude" />
    </div>

    <!-- Script Leaflet -->
    @push('js-internal')
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-geosearch/dist/bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Simpan referensi global untuk peta dan marker
                window.mapInstance = window.mapInstance || {};
                
                const mapId = 'map-{{ $addressId }}';
                const latInput = document.getElementById('{{ $latitudeId }}');
                const lonInput = document.getElementById('{{ $longitudeId }}');
                
                function initMap() {
                    
                    
                    // Periksa apakah elemen peta ada
                    const mapElement = document.getElementById(mapId);
                    if (!mapElement) {
                        console.error('Elemen peta tidak ditemukan: ' + mapId);
                        return;
                    }
                    
                    // Hapus peta yang sudah ada jika perlu
                    if (window.mapInstance[mapId]) {
                        console.log('Menghapus peta yang sudah ada');
                        window.mapInstance[mapId].remove();
                        window.mapInstance[mapId] = null;
                    }

                    // Dapatkan koordinat awal
                    let lat = parseFloat(latInput.value) || -7.310000;
                    let lon = parseFloat(lonInput.value) || 110.290000;

                    // Buat peta baru
                    const map = L.map(mapId).setView([lat, lon], 13);
                    window.mapInstance[mapId] = map; // Simpan referensi
                    window.map = map; // Kompatibilitas dengan kode lama
                    
                    // Tambahkan Tile Layer OpenStreetMap
                    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    // Menggunakan layer satelit dari Esri
                    const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: '© Esri'
                    });

                    // Tambahkan kontrol layer
                    L.control.layers({
                        'Umum': osmLayer,
                        'Satelit': esriLayer
                    }).addTo(map);

                    // Tambahkan Marker Draggable
                    const marker = L.marker([lat, lon], { draggable: true }).addTo(map);
                    window.marker = marker; // Simpan referensi

                    marker.on('dragend', function () {
                        const position = marker.getLatLng();
                        latInput.value = position.lat.toFixed(6);
                        lonInput.value = position.lng.toFixed(6);
                    });

                    // Tambahkan Event Klik untuk Mendapatkan Koordinat
                    map.on('click', function (event) {
                        const { lat, lng } = event.latlng;
                        marker.setLatLng([lat, lng]);
                        latInput.value = lat.toFixed(6);
                        lonInput.value = lng.toFixed(6);
                    });

                    // Tambahkan Fitur Search
                    const provider = new window.GeoSearch.OpenStreetMapProvider();
                    const searchControl = new window.GeoSearch.GeoSearchControl({
                        provider: provider,
                        style: 'bar',
                        autoClose: true,
                        showMarker: false,
                    });

                    map.addControl(searchControl);

                    // Event untuk Search
                    map.on('geosearch/showlocation', function (result) {
                        const { x: lon, y: lat } = result.location;
                        marker.setLatLng([lat, lon]);
                        latInput.value = lat.toFixed(6);
                        lonInput.value = lon.toFixed(6);
                        map.setView([lat, lon], 13);
                    });
                    
                    // Perbarui ukuran peta setelah diinisialisasi
                    setTimeout(() => {
                        map.invalidateSize();
                        
                    }, 300);
                    
                    return map;
                }
                
                // Fungsi untuk memperbarui marker dengan koordinat baru
                function updateMapLocation(lat, lng) {
                    if (!window.mapInstance[mapId]) {
                        console.log('Peta belum diinisialisasi, memulai inisialisasi...');
                        initMap();
                        return;
                    }
                    
                    const map = window.mapInstance[mapId];
                    lat = parseFloat(lat);
                    lng = parseFloat(lng);
                    
                    if (isNaN(lat) || isNaN(lng)) {
                        console.error('Koordinat tidak valid:', lat, lng);
                        return;
                    }
                    
                    console.log('Memperbarui lokasi peta:', lat, lng);
                    
                    // Perbarui marker jika ada
                    if (window.marker) {
                        window.marker.setLatLng([lat, lng]);
                    } else {
                        window.marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                        
                        window.marker.on('dragend', function () {
                            const position = window.marker.getLatLng();
                            latInput.value = position.lat.toFixed(6);
                            lonInput.value = position.lng.toFixed(6);
                        });
                    }
                    
                    // Perbarui tampilan peta
                    map.setView([lat, lng], 15);
                    map.invalidateSize();
                }

                // Fungsi Pantau Modal
                function observeModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (!modal) {
                        console.error('Modal tidak ditemukan:', modalId);
                        return;
                    }

                    console.log('Mengamati modal:', modalId);
                    
                    // Observer untuk memantau perubahan kelas
                    const observer = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === "class") {
                                if (modal.classList.contains('flex')) {
                                    console.log('Modal ditampilkan, mempersiapkan peta...');
                                    // Tambahkan delay untuk memastikan DOM dirender
                                    setTimeout(() => {
                                        if (!window.mapInstance[mapId]) {
                                            console.log('Inisialisasi peta baru di modal');
                                            initMap();
                                        } else {
                                            console.log('Memperbarui ukuran peta yang sudah ada');
                                            window.mapInstance[mapId].invalidateSize();
                                            
                                            // Perbarui marker jika ada nilai koordinat
                                            const lat = parseFloat(latInput.value);
                                            const lng = parseFloat(lonInput.value);
                                            if (!isNaN(lat) && !isNaN(lng)) {
                                                updateMapLocation(lat, lng);
                                            }
                                        }
                                    }, 500);
                                }
                            }
                        });
                    });

                    observer.observe(modal, { attributes: true });
                }
                
                // Handler untuk event kustom map-reinitialize
                document.addEventListener('map-reinitialize', function() {
                    console.log('Event map-reinitialize ditangkap, memulai inisialisasi ulang peta');
                    initMap();
                });
                
                // Handler untuk event kustom update-map-location
                document.addEventListener('update-map-location', function(event) {
                    if (event.detail && event.detail.lat && event.detail.lng) {
                        console.log('Event update-map-location ditangkap:', event.detail);
                        updateMapLocation(event.detail.lat, event.detail.lng);
                    }
                });
                
                // Pantau perubahan nilai input koordinat
                latInput.addEventListener('change', function() {
                    if (lonInput.value) {
                        updateMapLocation(latInput.value, lonInput.value);
                    }
                });
                
                lonInput.addEventListener('change', function() {
                    if (latInput.value) {
                        updateMapLocation(latInput.value, lonInput.value);
                    }
                });
                
                // Inisialisasi
                if ('{{ $modalId }}') {
                    console.log('Mengatur observer untuk modal: {{ $modalId }}');
                    observeModal('{{ $modalId }}');
                } else {
                    
                    setTimeout(initMap, 300);
                }
            });
        </script>
    @endpush
</div>