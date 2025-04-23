<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Laporan Desa</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form action="{{ route('user.laporan-desa.update', $laporanDesa->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ruang Lingkup -->
                    <div>
                        <label for="ruang_lingkup" class="block text-sm font-medium text-gray-700">Ruang Lingkup <span
                                class="text-red-500">*</span></label>
                        <select id="ruang_lingkup" name="ruang_lingkup"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
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
                        <label for="lapor_desa_id" class="block text-sm font-medium text-gray-700">Bidang <span
                                class="text-red-500">*</span></label>
                        <select id="lapor_desa_id" name="lapor_desa_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
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
                    <div class="md:col-span-2">
                        <label for="judul_laporan" class="block text-sm font-medium text-gray-700">Judul Laporan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="judul_laporan" name="judul_laporan"
                            value="{{ old('judul_laporan', $laporanDesa->judul_laporan) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                        @error('judul_laporan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi Laporan -->
                    <div class="md:col-span-2">
                        <label for="deskripsi_laporan" class="block text-sm font-medium text-gray-700">Deskripsi Laporan
                            <span class="text-red-500">*</span></label>
                        <textarea id="deskripsi_laporan" name="deskripsi_laporan" rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>{{ old('deskripsi_laporan', $laporanDesa->deskripsi_laporan) }}</textarea>
                        @error('deskripsi_laporan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Gambar -->
                    <div class="md:col-span-2">
                        <label for="gambar" class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar</label>

                        @if($laporanDesa->gambar)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                                <div class="relative inline-block">
                                    <img src="{{ asset('storage/' . $laporanDesa->gambar) }}" alt="Gambar Laporan"
                                        class="w-40 h-auto object-cover rounded-md border-2 border-gray-200">
                                </div>
                            </div>
                        @endif

                        <div class="mt-2">
                            <input type="file" id="gambar" name="gambar" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF (max 2MB). Biarkan kosong jika
                                tidak ingin mengubah gambar.</p>
                        </div>

                        <div id="file-preview" class="mt-2 hidden">
                            <p class="text-sm text-gray-600 mb-2">File yang dipilih: <span id="file-name"></span></p>
                            <img id="image-preview" class="max-h-40 w-auto rounded border border-gray-200"
                                alt="Preview gambar">
                        </div>

                        @error('gambar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tag Lokasi Map -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="bg-white rounded-lg">
                            <x-map-input label="Lokasi Kejadian" addressId="location_address"
                                addressName="location_address"
                                address="{{ old('location_address', $laporanDesa->lokasi) }}" latitudeId="tag_lat"
                                latitudeName="tag_lat" latitude="{{ old('tag_lat', $lat) }}" longitudeId="tag_lng"
                                longitudeName="tag_lng" longitude="{{ old('tag_lng', $lng) }}" modalId="" />

                            <input type="hidden" id="tag_lokasi" name="tag_lokasi"
                                value="{{ old('tag_lokasi', "$lat, $lng") }}">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4 pt-6">
                    <a href="{{ route('user.laporan-desa.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] focus:outline-none">
                        Perbarui Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('js-internal')
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-geosearch/dist/bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize map functionality
                const tagLatInput = document.getElementById('tag_lat');
                const tagLngInput = document.getElementById('tag_lng');
                const tagLokasiInput = document.getElementById('tag_lokasi');

                function updateTagLokasi() {
                    const lat = tagLatInput ? tagLatInput.value.trim() : '';
                    const lng = tagLngInput ? tagLngInput.value.trim() : '';

                    if (lat && lng && tagLokasiInput) {
                        tagLokasiInput.value = `${lat}, ${lng}`;
                        console.log('Tag lokasi updated:', tagLokasiInput.value);
                    } else if (tagLokasiInput) {
                        tagLokasiInput.value = '';
                    }
                }

                if (tagLatInput) {
                    tagLatInput.addEventListener('change', updateTagLokasi);
                    tagLatInput.addEventListener('input', updateTagLokasi);
                }

                if (tagLngInput) {
                    tagLngInput.addEventListener('change', updateTagLokasi);
                    tagLngInput.addEventListener('input', updateTagLokasi);
                }

                updateTagLokasi();

                // Update tag_lokasi before form submission
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        updateTagLokasi();
                    });
                }

                // Image preview functionality
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
            });
        </script>

        <script>
            // JSON object to store all categories
            const categories = @json($categories);

            // When ruang_lingkup changes, update the bidang dropdown
            document.getElementById('ruang_lingkup').addEventListener('change', function () {
                const ruangLingkup = this.value;
                const bidangSelect = document.getElementById('lapor_desa_id');

                // Clear current options
                bidangSelect.innerHTML = '';

                if (ruangLingkup && categories[ruangLingkup]) {
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Pilih Bidang';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    bidangSelect.appendChild(defaultOption);

                    // Add options based on ruang_lingkup selection
                    categories[ruangLingkup].forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.bidang;
                        bidangSelect.appendChild(option);
                    });

                    // Enable the select
                    bidangSelect.disabled = false;
                } else {
                    // If no ruang_lingkup selected, disable bidang select
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Pilih Ruang Lingkup Terlebih Dahulu';
                    option.disabled = true;
                    option.selected = true;
                    bidangSelect.appendChild(option);
                    bidangSelect.disabled = true;
                }
            });
        </script>
    @endpush
</x-layout>