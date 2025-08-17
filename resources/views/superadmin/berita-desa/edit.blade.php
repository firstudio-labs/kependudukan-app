<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Berita Desa</title>
</head>
<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Berita Desa</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('superadmin.berita-desa.update', $berita->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Berita</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $berita->judul) }}"
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
                    <div id="imagePreview" class="mt-2 {{ $berita->gambar ? '' : 'hidden' }}">
                        <img src="{{ $berita->gambar ? asset('storage/' . $berita->gambar) : '' }}" alt="Preview"
                            class="max-w-xs rounded-lg shadow-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
                        required>{{ old('deskripsi', $berita->deskripsi) }}</textarea>
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
                                <option value="{{ $province['code'] }}" 
                                    {{ old('province', $berita->id_provinsi) == $province['code'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden field untuk menyimpan id_provinsi -->
                        <input type="hidden" name="id_provinsi" id="id_provinsi" value="{{ old('id_provinsi', $berita->id_provinsi) }}">
                    </div>

                    <div>
                        <label for="id_kabupaten" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten</label>
                        <select name="id_kabupaten" id="id_kabupaten" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_kabupaten') border-red-500 @enderror"
                            onchange="loadKecamatan()" {{ $berita->id_provinsi ? '' : 'disabled' }}>
                            <option value="">Pilih Provinsi terlebih dahulu</option>
                            @foreach($kabupaten as $kab)
                                <option value="{{ $kab['id'] }}" {{ old('id_kabupaten', $berita->id_kabupaten) == $kab['id'] ? 'selected' : '' }}>
                                    {{ $kab['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kabupaten')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_kecamatan" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <select name="id_kecamatan" id="id_kecamatan" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_kecamatan') border-red-500 @enderror"
                            onchange="loadDesa()" {{ $berita->id_kabupaten ? '' : 'disabled' }}>
                            <option value="">Pilih Kabupaten terlebih dahulu</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec['id'] }}" {{ old('id_kecamatan', $berita->id_kecamatan) == $kec['id'] ? 'selected' : '' }}>
                                    {{ $kec['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kecamatan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_desa" class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                        <select name="id_desa" id="id_desa" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_desa') border-red-500 @enderror" 
                            {{ $berita->id_kecamatan ? '' : 'disabled' }}>
                            <option value="">Pilih Kecamatan terlebih dahulu</option>
                            @foreach($desa as $d)
                                <option value="{{ $d['id'] }}" {{ old('id_desa', $berita->id_desa) == $d['id'] ? 'selected' : '' }}>
                                    {{ $d['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_desa')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="komentar" id="komentar" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('komentar') border-red-500 @enderror">{{ old('komentar', $berita->komentar) }}</textarea>
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
                        Simpan Perubahan
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
            const provinceCode = provinceSelect.value;
            const kabupatenSelect = document.getElementById('id_kabupaten');
            const kecamatanSelect = document.getElementById('id_kecamatan');
            const desaSelect = document.getElementById('id_desa');

            // Reset dependent dropdowns
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            kabupatenSelect.disabled = true;
            kecamatanSelect.disabled = true;
            desaSelect.disabled = true;

            if (provinceCode) {
                // Save province code to hidden field
                document.getElementById('id_provinsi').value = provinceCode;

                // Enable kabupaten dropdown
                kabupatenSelect.disabled = false;

                const url = `/api/wilayah/kabupaten-by-province?province_code=${provinceCode}`;

                fetch(url, {
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data)) {
                            window.kabupatenData = data; // Store kabupaten data globally
                            data.forEach(kabupaten => {
                                const option = document.createElement('option');
                                option.value = kabupaten.id;
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

        // Load kecamatan berdasarkan kabupaten
        function loadKecamatan() {
            const kabupatenSelect = document.getElementById('id_kabupaten');
            const kabupatenCode = kabupatenSelect.value;
            const kecamatanSelect = document.getElementById('id_kecamatan');
            const desaSelect = document.getElementById('id_desa');

            // Reset dependent dropdowns
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            kecamatanSelect.disabled = true;
            desaSelect.disabled = true;

            if (kabupatenCode) {
                // Find the kabupaten code from the original data
                const kabupatenData = window.kabupatenData || [];
                const selectedKabupaten = kabupatenData.find(k => k.id == kabupatenCode);
                
                if (selectedKabupaten && selectedKabupaten.code) {
                    const wilayahCode = selectedKabupaten.code;
                    
                    // Enable kecamatan dropdown
                    kecamatanSelect.disabled = false;
                    
                    const url = `/api/wilayah/kecamatan-by-kabupaten?kabupaten_code=${wilayahCode}`;
                    
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
                                    option.value = kecamatan.id;
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
            const kecamatanSelect = document.getElementById('id_kecamatan');
            const kecamatanCode = kecamatanSelect.value;
            const desaSelect = document.getElementById('id_desa');

            // Reset dependent dropdowns
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            desaSelect.disabled = true;

            if (kecamatanCode) {
                // Find the kecamatan code from the original data
                const kecamatanData = window.kecamatanData || [];
                const selectedKecamatan = kecamatanData.find(k => k.id == kecamatanCode);
                
                if (selectedKecamatan && selectedKecamatan.code) {
                    const wilayahCode = selectedKecamatan.code;
                    
                    // Enable desa dropdown
                    desaSelect.disabled = false;
                    
                    const url = `/api/wilayah/desa-by-kecamatan?kecamatan_code=${wilayahCode}`;
                    
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
                                data.forEach(desa => {
                                    const option = document.createElement('option');
                                    option.value = desa.id;
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

        // Load data wilayah saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set nilai yang sudah ada jika ada data
            @if($berita->id_kabupaten && $berita->id_kecamatan && $berita->id_desa)
                // Trigger load kabupaten dan set nilai
                loadKabupaten();
                
                // Set timeout untuk memastikan data kabupaten sudah ter-load
                setTimeout(() => {
                    // Set nilai kabupaten
                    document.getElementById('id_kabupaten').value = '{{ $berita->id_kabupaten }}';
                    
                    // Trigger load kecamatan
                    loadKecamatan();
                    
                    setTimeout(() => {
                        // Set nilai kecamatan
                        document.getElementById('id_kecamatan').value = '{{ $berita->id_kecamatan }}';
                        
                        // Trigger load desa
                        loadDesa();
                        
                        setTimeout(() => {
                            // Set nilai desa
                            document.getElementById('id_desa').value = '{{ $berita->id_desa }}';
                        }, 500);
                    }, 500);
                }, 500);
            @endif
        });
    </script>
</x-layout>