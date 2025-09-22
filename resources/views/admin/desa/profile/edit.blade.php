<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Profil Admin Desa</h1>
        </div>

        <form action="{{ route('admin.desa.profile.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="md:col-span-1">
                    <div class="flex flex-col items-center space-y-6">
                        <!-- Foto Pengguna -->
                        <div class="flex flex-col items-center">
                            <div class="w-40 h-40 rounded-full bg-gray-200 overflow-hidden mb-4">
                                @if ($user->foto_pengguna)
                                    <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="Foto Pengguna"
                                        class="w-full h-full object-cover" id="preview-foto-pengguna">
                                @elseif($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Foto Pengguna"
                                        class="w-full h-full object-cover" id="preview-foto-pengguna">
                                @else
                                    <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                        alt="Foto Pengguna" class="w-full h-full object-cover" id="preview-foto-pengguna">
                                @endif
                            </div>
                            <label for="foto_pengguna"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
                                Ubah Foto Pengguna
                            </label>
                            <input type="file" id="foto_pengguna" name="foto_pengguna" class="hidden" accept="image/*"
                                onchange="previewFotoPengguna(this)">
                            <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, GIF. Maks: 2MB</p>
                        </div>

                        <!-- Logo -->
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded bg-gray-100 overflow-hidden mb-4 border border-gray-300">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Logo" class="w-full h-full object-contain p-2" id="preview-logo">
                                @else
                                    <span class="flex items-center justify-center w-full h-full text-gray-400" id="preview-logo-text">Tidak ada logo</span>
                                @endif
                            </div>
                            <label for="image"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition cursor-pointer">
                                Ubah Logo
                            </label>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*"
                                onchange="previewLogo(this)">
                            <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, GIF. Maks: 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details Section -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nik" class="block text-gray-600 text-sm font-medium mb-1">NIK</label>
                                <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}"
                                    class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                            </div>

                            <div>
                                <label for="username"
                                    class="block text-gray-600 text-sm font-medium mb-1">Username</label>
                                <input type="text" id="username" name="username"
                                    value="{{ old('username', $user->username) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('username')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="nama" class="block text-gray-600 text-sm font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}"
                                    class="w-full p-2 border border-gray-300 rounded" required>
                                @error('nama')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-gray-600 text-sm font-medium mb-1">Email</label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="no_hp" class="block text-gray-600 text-sm font-medium mb-1">No.
                                    Handphone</label>
                                <input type="text" id="no_hp" name="no_hp"
                                    value="{{ old('no_hp', $user->no_hp) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('no_hp')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-600 text-sm font-medium mb-1">Peta Lokasi</label>
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" id="map-search" placeholder="Cari lokasi (contoh: Alun-alun, Jl. Sudirman, Kota)"
                                           class="w-full p-2 border border-gray-300 rounded">
                                    <button type="button" id="map-search-btn" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
                                </div>
                                <div id="map" class="w-full h-64 rounded border border-gray-300"></div>
                                <div id="map-search-results" class="mt-2 bg-white border border-gray-200 rounded hidden"></div>
                                <p class="text-xs text-gray-500 mt-2">Klik pada peta untuk memilih lokasi. Alamat akan diisi otomatis dari titik koordinat yang dipilih dan bisa Anda sunting kembali.</p>
                            </div>

                            <div>
                                <label for="tag_lokasi" class="block text-gray-600 text-sm font-medium mb-1">Tag Lokasi (lat,lng)</label>
                                <input type="text" id="tag_lokasi" name="tag_lokasi" value="{{ old('tag_lokasi', $user->tag_lokasi) }}"
                                    class="w-full p-2 border border-gray-300 rounded" placeholder="-6.2,106.8">
                                @error('tag_lokasi')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="alamat"
                                    class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" class="w-full p-2 border border-gray-300 rounded">{{ old('alamat', $user->alamat) }}</textarea>
                                @error('alamat')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="nama_kepala_desa" class="block text-gray-600 text-sm font-medium mb-1">Nama Kepala Desa</label>
                                <input type="text" id="nama_kepala_desa" name="nama_kepala_desa" value="{{ old('nama_kepala_desa', $user->kepalaDesa?->nama) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('nama_kepala_desa')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="tanda_tangan" class="block text-gray-600 text-sm font-medium mb-1">Tanda Tangan</label>
                                <input type="file" id="tanda_tangan" name="tanda_tangan" accept="image/*"
                                    class="w-full p-2 border border-gray-300 rounded" onchange="previewTandaTangan(this)">
                                <div class="mt-2">
                                    @if($user->kepalaDesa?->tanda_tangan)
                                        <img id="preview-tanda-tangan" src="{{ asset('storage/' . $user->kepalaDesa->tanda_tangan) }}"
                                             alt="Tanda Tangan" class="h-20">
                                    @else
                                        <img id="preview-tanda-tangan" alt="Tanda Tangan" class="h-20 hidden">
                                    @endif
                                </div>
                                @error('tanda_tangan')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Ubah Password (opsional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="current_password"
                                    class="block text-gray-600 text-sm font-medium mb-1">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('current_password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-gray-600 text-sm font-medium mb-1">Password
                                    Baru</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('new_password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation"
                                    class="block text-gray-600 text-sm font-medium mb-1">Konfirmasi Password
                                    Baru</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="w-full p-2 border border-gray-300 rounded">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.desa.profile.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition mr-2">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function previewFotoPengguna(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('preview-foto-pengguna').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewLogo(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    const previewLogo = document.getElementById('preview-logo');
                    const previewLogoText = document.getElementById('preview-logo-text');

                    if (previewLogo) {
                        previewLogo.src = e.target.result;
                        previewLogo.style.display = 'block';
                    }

                    if (previewLogoText) {
                        previewLogoText.style.display = 'none';
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewTandaTangan(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.getElementById('preview-tanda-tangan');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewPerangkatFoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const row = input.closest('[data-row]');
                    if (!row) return;
                    const img = row.querySelector('[data-foto-preview]');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePerangkatRow(btn) {
            const row = btn.closest('[data-row]');
            if (!row) return;
            const container = document.getElementById('perangkat-list');
            if (container && container.children.length <= 1) {
                // minimal satu baris kosong
                const inputs = row.querySelectorAll('input');
                inputs.forEach(function (i) { if (i.type !== 'hidden') i.value = ''; });
                const img = row.querySelector('[data-foto-preview]');
                if (img) { img.src = ''; img.classList.add('hidden'); }
                return;
            }
            row.remove();
            renumberPerangkatRows();
        }

        function addPerangkatRow() {
            const container = document.getElementById('perangkat-list');
            if (!container) return;
            const index = container.querySelectorAll('[data-row]').length;
            const template = `
                <div class="border rounded p-3" data-row>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1">Nama</label>
                            <input type="text" name="perangkat[${index}][nama]" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1">Jabatan</label>
                            <input type="text" name="perangkat[${index}][jabatan]" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-600 text-sm font-medium mb-1">Foto</label>
                            <input type="file" accept="image/*" name="perangkat[${index}][foto]" onchange="previewPerangkatFoto(this)" class="w-full p-2 border border-gray-300 rounded">
                            <img alt="Foto Perangkat" class="h-20 mt-2 hidden" data-foto-preview>
                        </div>
                        <div class="md:col-span-5">
                            <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                            <input type="text" name="perangkat[${index}][alamat]" class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="button" class="px-3 py-2 bg-red-600 text-white rounded w-full" onclick="removePerangkatRow(this)">Hapus</button>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', template);
        }

        function renumberPerangkatRows() {
            const container = document.getElementById('perangkat-list');
            if (!container) return;
            const rows = container.querySelectorAll('[data-row]');
            rows.forEach(function (row, idx) {
                row.querySelectorAll('input').forEach(function (input) {
                    const name = input.getAttribute('name');
                    if (!name) return;
                    const newName = name.replace(/perangkat\[\d+\]/, 'perangkat[' + idx + ']');
                    input.setAttribute('name', newName);
                });
            });
        }

        // Map + geocoding
        document.addEventListener('DOMContentLoaded', function () {
            const tagLokasiInput = document.getElementById('tag_lokasi');
            const alamatTextarea = document.getElementById('alamat');
            const searchInput = document.getElementById('map-search');
            const searchBtn = document.getElementById('map-search-btn');
            const searchResults = document.getElementById('map-search-results');

            // Parse existing tag_lokasi or default center (Monas Jakarta)
            let defaultLat = -6.1753924;
            let defaultLng = 106.8271528;
            if (tagLokasiInput && tagLokasiInput.value) {
                const parts = tagLokasiInput.value.split(',').map(function (v) { return parseFloat(v.trim()); });
                if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                    defaultLat = parts[0];
                    defaultLng = parts[1];
                }
            }

            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            const map = L.map('map').setView([defaultLat, defaultLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: false }).addTo(map);

            function setTagLokasi(lat, lng) {
                if (tagLokasiInput) {
                    tagLokasiInput.value = lat.toFixed(7) + ',' + lng.toFixed(7);
                }
            }

            function reverseGeocode(lat, lng) {
                // Reset alamat saat koordinat berubah
                if (alamatTextarea) {
                    alamatTextarea.value = '';
                }
                const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'kependudukan-app/1.0 (contact: admin@example.com)'
                    }
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data && data.display_name && alamatTextarea && !alamatTextarea.dataset.userEdited) {
                        alamatTextarea.value = data.display_name;
                    }
                })
                .catch(function () { /* silent */ });
            }

            // Set initial
            setTagLokasi(defaultLat, defaultLng);

            // Pindahkan marker saat peta di-klik
            map.on('click', function (e) {
                const pos = e.latlng;
                marker.setLatLng(pos);
                setTagLokasi(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });

            // When user edits alamat manually, mark as userEdited so we don't overwrite unexpectedly later
            if (alamatTextarea) {
                const markEdited = function () { alamatTextarea.dataset.userEdited = '1'; };
                alamatTextarea.addEventListener('input', markEdited);
                alamatTextarea.addEventListener('change', markEdited);
            }

            // Allow typing lat,lng manually updates marker
            if (tagLokasiInput) {
                tagLokasiInput.addEventListener('change', function () {
                    const parts = tagLokasiInput.value.split(',').map(function (v) { return parseFloat(v.trim()); });
                    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                        marker.setLatLng([parts[0], parts[1]]);
                        map.setView([parts[0], parts[1]], map.getZoom());
                        reverseGeocode(parts[0], parts[1]);
                    }
                });
            }

            function performSearch(query) {
                if (!query || query.trim().length < 3) {
                    if (searchResults) {
                        searchResults.classList.add('hidden');
                        searchResults.innerHTML = '';
                    }
                    return;
                }
                const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&q=' + encodeURIComponent(query) + '&limit=5';
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'kependudukan-app/1.0 (contact: admin@example.com)'
                    }
                })
                .then(function (res) { return res.json(); })
                .then(function (list) {
                    if (!Array.isArray(list) || !searchResults) return;
                    if (list.length === 0) {
                        searchResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada hasil</div>';
                        searchResults.classList.remove('hidden');
                        return;
                    }
                    searchResults.innerHTML = '';
                    list.forEach(function (item) {
                        const row = document.createElement('div');
                        row.className = 'p-2 hover:bg-gray-50 cursor-pointer text-sm';
                        row.textContent = item.display_name;
                        row.addEventListener('click', function () {
                            const lat = parseFloat(item.lat);
                            const lon = parseFloat(item.lon);
                            marker.setLatLng([lat, lon]);
                            map.setView([lat, lon], 17);
                            setTagLokasi(lat, lon);
                            // Reset edited flag agar alamat diupdate dari hasil pencarian
                            if (alamatTextarea) { delete alamatTextarea.dataset.userEdited; }
                            reverseGeocode(lat, lon);
                            searchResults.classList.add('hidden');
                            searchResults.innerHTML = '';
                        });
                        searchResults.appendChild(row);
                    });
                    searchResults.classList.remove('hidden');
                })
                .catch(function () { /* silent */ });
            }

            if (searchBtn && searchInput) {
                searchBtn.addEventListener('click', function () {
                    performSearch(searchInput.value);
                });
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch(searchInput.value);
                    }
                });
                // Hide results on outside click
                document.addEventListener('click', function (e) {
                    if (searchResults && !searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchBtn) {
                        searchResults.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-layout>
