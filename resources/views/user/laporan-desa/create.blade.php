<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Laporan Desa</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form method="POST" action="{{ route('user.laporan-desa.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ruang Lingkup -->
                    <div>
                        <label for="ruang_lingkup" class="block text-sm font-medium text-gray-700">Ruang Lingkup <span
                                class="text-red-500">*</span></label>
                        <select id="ruang_lingkup" name="ruang_lingkup"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="" disabled selected>Pilih Ruang Lingkup</option>
                            @foreach($categories as $ruangLingkup => $bidangList)
                                <option value="{{ $ruangLingkup }}" {{ old('ruang_lingkup') == $ruangLingkup ? 'selected' : '' }}>{{ $ruangLingkup }}</option>
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
                            <option value="" disabled selected>Pilih Bidang</option>
                            @if(old('ruang_lingkup'))
                                @foreach($categories[old('ruang_lingkup')] as $bidang)
                                    <option value="{{ $bidang['id'] }}" {{ old('lapor_desa_id') == $bidang['id'] ? 'selected' : '' }}>{{ $bidang['bidang'] }}</option>
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
                        <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}"
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
                            required>{{ old('deskripsi_laporan') }}</textarea>
                        @error('deskripsi_laporan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Gambar -->
                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Upload Gambar</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF (max 2MB).</p>
                        @error('gambar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            placeholder="Masukkan lokasi kejadian">
                        @error('lokasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-2">
                    <a href="{{ route('user.laporan-desa.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] focus:outline-none focus:bg-[#2D336B]">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

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
</x-layout></div>