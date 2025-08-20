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
                                    {{ old('province', $berita->province_id) == $province['code'] ? 'selected' : '' }}>
                                    {{ $province['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Hidden field untuk menyimpan province_id -->
                        <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id', $berita->province_id) }}">
                    </div>

                    <div>
                        <label for="districts_id" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten</label>
                        <select name="districts_id" id="districts_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('districts_id') border-red-500 @enderror"
                            onchange="loadKecamatan()" {{ $berita->province_id ? '' : 'disabled' }}>
                            <option value="">Pilih Provinsi terlebih dahulu</option>
                            @foreach($kabupaten as $kab)
                                <option value="{{ $kab['code'] }}" {{ old('districts_id', $berita->districts_id) == $kab['code'] ? 'selected' : '' }}>
                                    {{ $kab['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('districts_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sub_districts_id" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <select name="sub_districts_id" id="sub_districts_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sub_districts_id') border-red-500 @enderror"
                            onchange="loadDesa()" {{ $berita->districts_id ? '' : 'disabled' }}>
                            <option value="">Pilih Kabupaten terlebih dahulu</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec['code'] }}" {{ old('sub_districts_id', $berita->sub_districts_id) == $kec['code'] ? 'selected' : '' }}>
                                    {{ $kec['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('sub_districts_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="villages_id" class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                        <select name="villages_id" id="villages_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('villages_id') border-red-500 @enderror" 
                            {{ $berita->sub_districts_id ? '' : 'disabled' }}>
                            <option value="">Pilih Kecamatan terlebih dahulu</option>
                            @foreach($desa as $des)
                                <option value="{{ $des['code'] }}" {{ old('villages_id', $berita->villages_id) == $des['code'] ? 'selected' : '' }}>
                                    {{ $des['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('villages_id')
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
            const districtsSelect = document.getElementById('districts_id');
            const subDistrictsSelect = document.getElementById('sub_districts_id');
            const villagesSelect = document.getElementById('villages_id');

            // Reset dependent dropdowns
            districtsSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            subDistrictsSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villagesSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            districtsSelect.disabled = true;
            subDistrictsSelect.disabled = true;
            villagesSelect.disabled = true;

            if (provinceCode) {
                // Save province code to hidden field
                document.getElementById('province_id').value = provinceCode;

                // Enable districts dropdown
                districtsSelect.disabled = false;

                const url = `/api/wilayah/kabupaten-by-province?province_code=${provinceCode}`;

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
                                option.value = kabupaten.code; // Use code as value
                                option.textContent = kabupaten.name;
                                districtsSelect.appendChild(option);
                            });
                        } else {
                            districtsSelect.innerHTML = '<option value="">Invalid data format</option>';
                        }
                    })
                    .catch(error => {
                        districtsSelect.innerHTML = '<option value="">Error loading kabupaten</option>';
                    });
            }
        }

        // Load kecamatan berdasarkan kabupaten
        function loadKecamatan() {
            const districtsSelect = document.getElementById('districts_id');
            const districtsCode = districtsSelect.value;
            const subDistrictsSelect = document.getElementById('sub_districts_id');
            const villagesSelect = document.getElementById('villages_id');

            // Reset dependent dropdowns
            subDistrictsSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villagesSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            subDistrictsSelect.disabled = true;
            villagesSelect.disabled = true;

            if (districtsCode) {
                // Find the districts data from the loaded kabupaten data
                const districtsData = window.kabupatenData || [];
                const selectedDistricts = districtsData.find(d => d.code == districtsCode);
                
                if (selectedDistricts) {
                    // Enable subDistricts dropdown
                    subDistrictsSelect.disabled = false;
                    
                    const url = `/api/wilayah/kecamatan-by-kabupaten?kabupaten_code=${districtsCode}`;
                    
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
                                    option.value = kecamatan.code; // Use code as value
                                    option.textContent = kecamatan.name;
                                    subDistrictsSelect.appendChild(option);
                                });
                            } else {
                                subDistrictsSelect.innerHTML = '<option value="">Invalid data format</option>';
                            }
                        })
                        .catch(error => {
                            subDistrictsSelect.innerHTML = '<option value="">Error loading kecamatan</option>';
                        });
                }
            }
        }

        // Load desa berdasarkan kecamatan
        function loadDesa() {
            const subDistrictsSelect = document.getElementById('sub_districts_id');
            const subDistrictsCode = subDistrictsSelect.value;
            const villagesSelect = document.getElementById('villages_id');

            // Reset dependent dropdowns
            villagesSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Disable dependent dropdowns
            villagesSelect.disabled = true;

            if (subDistrictsCode) {
                // Find the subDistricts data from the loaded kecamatan data
                const subDistrictsData = window.kecamatanData || [];
                const selectedSubDistricts = subDistrictsData.find(s => s.code == subDistrictsCode);
                
                if (selectedSubDistricts) {
                    // Enable villages dropdown
                    villagesSelect.disabled = false;
                    
                    const url = `/api/wilayah/desa-by-kecamatan?kecamatan_code=${subDistrictsCode}`;
                    
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
                                    option.value = desa.code; // Use code as value
                                    option.textContent = desa.name;
                                    villagesSelect.appendChild(option);
                                });
                            } else {
                                villagesSelect.innerHTML = '<option value="">Invalid data format</option>';
                            }
                        })
                        .catch(error => {
                            villagesSelect.innerHTML = '<option value="">Error loading desa</option>';
                        });
                }
            }
        }

        // Load data wilayah saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set nilai yang sudah ada jika ada data
            @if($berita->districts_id && $berita->sub_districts_id && $berita->villages_id)
                // Trigger load kabupaten dan set nilai
                loadKabupaten();
                
                // Set timeout untuk memastikan data kabupaten sudah ter-load
                setTimeout(() => {
                    // Set nilai kabupaten
                    document.getElementById('districts_id').value = '{{ $berita->districts_id }}';
                    
                    // Trigger load kecamatan
                    loadKecamatan();
                    
                    setTimeout(() => {
                        // Set nilai kecamatan
                        document.getElementById('sub_districts_id').value = '{{ $berita->sub_districts_id }}';
                        
                        // Trigger load desa
                        loadDesa();
                        
                        setTimeout(() => {
                            // Set nilai desa
                            document.getElementById('villages_id').value = '{{ $berita->villages_id }}';
                        }, 500);
                    }, 500);
                }, 500);
            @endif
        });
    </script>
</x-layout>