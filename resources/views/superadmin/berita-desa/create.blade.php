<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Berita Desa</title>
</head>
<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Berita Desa</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('superadmin.berita-desa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('judul') border-red-500 @enderror"
                        required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">Gambar Berita</label>
                    <input type="file" name="gambar" id="gambar" accept="image/png,image/jpg,image/jpeg"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gambar') border-red-500 @enderror"
                        onchange="previewImage(this)">
                    <p class="mt-1 text-sm text-gray-500">Format: PNG, JPG. Maksimal 4MB</p>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="imagePreview" class="mt-2 hidden">
                        <img src="" alt="Preview" class="max-w-xs rounded-lg shadow-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                        required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field Wilayah -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                        <select name="province" id="province" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="loadKabupaten()">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['id'] }}" {{ old('province') == $province['id'] ? 'selected' : '' }} data-code="{{ $province['code'] }}">
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden field untuk menyimpan province_id -->
                        <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id') }}">
                    </div>

                    <div>
                        <label for="districts_id" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten</label>
                        <select name="districts_id" id="districts_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('districts_id') border-red-500 @enderror"
                            onchange="loadKecamatan()" disabled>
                            <option value="">Pilih Provinsi terlebih dahulu</option>
                        </select>
                        @error('districts_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sub_districts_id" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <select name="sub_districts_id" id="sub_districts_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sub_districts_id') border-red-500 @enderror"
                            onchange="loadDesa()" disabled>
                            <option value="">Pilih Kabupaten terlebih dahulu</option>
                        </select>
                        @error('sub_districts_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="villages_id" class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                        <select name="villages_id" id="villages_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('villages_id') border-red-500 @enderror" disabled>
                            <option value="">Pilih Kecamatan terlebih dahulu</option>
                        </select>
                        @error('villages_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="komentar" id="komentar" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('komentar') border-red-500 @enderror">{{ old('komentar') }}</textarea>
                    @error('komentar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('superadmin.berita-desa.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-[#7886C7] focus:ring-offset-2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }

        // Load kabupaten berdasarkan provinsi
        function loadKabupaten() {
            const provinceSelect = document.getElementById('province');
            const provinceId = provinceSelect.value;
            const kabupatenSelect = document.getElementById('districts_id');
            const kecamatanSelect = document.getElementById('sub_districts_id');
            const desaSelect = document.getElementById('villages_id');

            // Set hidden field province_id
            document.getElementById('province_id').value = provinceId;

            // Reset dependent dropdowns
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            kabupatenSelect.disabled = true;
            kecamatanSelect.disabled = true;
            desaSelect.disabled = true;

            if (provinceId) {
                // Enable kabupaten dropdown
                kabupatenSelect.disabled = false;
                
                // Cari data provinsi untuk mendapatkan code yang benar
                const provinceData = window.provinceData || [];
                const selectedProvince = provinceData.find(p => p.id == provinceId);
                
                if (selectedProvince) {
                    const url = `/api/wilayah/kabupaten-by-province?province_code=${selectedProvince.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => {
                            return response.json();
                        })
                        .then(data => {
                            if (Array.isArray(data)) {
                                window.kabupatenData = data; // Store kabupaten data globally
                                data.forEach(kabupaten => {
                                    const option = document.createElement('option');
                                    option.value = kabupaten.id; // Gunakan 'id' untuk value dropdown
                                    option.textContent = kabupaten.name;
                                    kabupatenSelect.appendChild(option);
                                });
                            } else {
                                kabupatenSelect.innerHTML = '<option value="">Invalid data format</option>';
                            }
                        })
                        .catch(error => {
                            kabupatenSelect.innerHTML = '<option value="">Error loading kabupaten</option>';
                        });
                }
            }
        }

        // Load kecamatan berdasarkan kabupaten
        function loadKecamatan() {
            const kabupatenSelect = document.getElementById('districts_id');
            const kabupatenId = kabupatenSelect.value;
            const kecamatanSelect = document.getElementById('sub_districts_id');
            const desaSelect = document.getElementById('villages_id');

            // Reset dependent dropdowns
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            kecamatanSelect.disabled = true;
            desaSelect.disabled = true;

            if (kabupatenId) {
                // Find the kabupaten data from the loaded kabupaten data
                const kabupatenData = window.kabupatenData || [];
                const selectedKabupaten = kabupatenData.find(k => k.id == kabupatenId);
                
                if (selectedKabupaten) {
                    // Enable kecamatan dropdown
                    kecamatanSelect.disabled = false;
                    
                    const url = `/api/wilayah/kecamatan-by-kabupaten?kabupaten_code=${selectedKabupaten.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => {
                            return response.json();
                        })
                        .then(data => {
                            if (Array.isArray(data)) {
                                window.kecamatanData = data; // Store kecamatan data globally
                                data.forEach(kecamatan => {
                                    const option = document.createElement('option');
                                    option.value = kecamatan.id; // Gunakan 'id' untuk value dropdown
                                    option.textContent = kecamatan.name;
                                    kecamatanSelect.appendChild(option);
                                });
                            } else {
                                kecamatanSelect.innerHTML = '<option value="">Invalid data format</option>';
                            }
                        })
                        .catch(error => {
                            kecamatanSelect.innerHTML = '<option value="">Error loading kecamatan</option>';
                        });
                }
            }
        }

        // Load desa berdasarkan kecamatan
        function loadDesa() {
            const kecamatanSelect = document.getElementById('sub_districts_id');
            const kecamatanId = kecamatanSelect.value;
            const desaSelect = document.getElementById('villages_id');

            // Reset dependent dropdowns
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            desaSelect.disabled = true;

            if (kecamatanId) {
                // Find the kecamatan data from the loaded kecamatan data
                const kecamatanData = window.kecamatanData || [];
                const selectedKecamatan = kecamatanData.find(k => k.id == kecamatanId);
                
                if (selectedKecamatan) {
                    // Enable desa dropdown
                    desaSelect.disabled = false;
                    
                    const url = `/api/wilayah/desa-by-kecamatan?kecamatan_code=${selectedKecamatan.code}`;
                    
                    fetch(url, {
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => {
                            return response.json();
                        })
                        .then(data => {
                            if (Array.isArray(data)) {
                                window.desaData = data; // Store desa data globally
                                data.forEach(desa => {
                                    const option = document.createElement('option');
                                    option.value = desa.id; // Gunakan 'id' untuk value dropdown
                                    option.textContent = desa.name;
                                    desaSelect.appendChild(option);
                                });
                            } else {
                                desaSelect.innerHTML = '<option value="">Invalid data format</option>';
                            }
                        })
                        .catch(error => {
                            desaSelect.innerHTML = '<option value="">Error loading desa</option>';
                        });
                }
            }
        }

        // Load province data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Simpan data provinsi yang sudah ada di dropdown
            const provinceSelect = document.getElementById('province');
            const provinces = [];
            for (let i = 0; i < provinceSelect.options.length; i++) {
                const option = provinceSelect.options[i];
                if (option.value) {
                    provinces.push({
                        id: option.value,
                        code: option.getAttribute('data-code') || option.value,
                        name: option.textContent
                    });
                }
            }
            window.provinceData = provinces;
        });
    </script>
</x-layout>