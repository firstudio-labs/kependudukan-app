<x-layout>
    <div class="p-4 mt-14" id="rental-house-form-container"
         data-citizen-route="{{ route('citizens.administrasi') }}"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Izin Rumah Sewa</h1>

        <form method="POST" action="{{ route('superadmin.surat.rumah-sewa.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Organizer Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Penyelenggara</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                            <select id="nikSelect" name="nik" class="nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Penyelenggara -->
                        <div>
                            <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Penyelenggara <span class="text-red-500">*</span></label>
                            <select id="fullNameSelect" name="full_name" class="fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Alamat Penyelenggara -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Penyelenggara <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>

                        <!-- Nama Penanggung Jawab -->
                        <div>
                            <label for="responsibleNameSelect" class="block text-sm font-medium text-gray-700">Nama Penanggung Jawab <span class="text-red-500">*</span></label>
                            <select id="responsibleNameSelect" name="responsible_name" class="responsiblename-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Nama Penanggung Jawab</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wilayah Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Provinsi -->
                        <div>
                            <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                            <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="province_id" name="province_id" value="">
                        </div>

                        <!-- Kabupaten -->
                        <div>
                            <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                            <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <input type="hidden" id="district_id" name="district_id" value="">
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                        </div>

                        <!-- Desa -->
                        <div>
                            <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                            <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                                <option value="">Pilih Desa</option>
                            </select>
                            <input type="hidden" id="village_id" name="village_id" value="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Property Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Rumah Sewa</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <!-- Main Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Alamat Rumah Sewa -->
                        <div>
                            <label for="rental_address" class="block text-sm font-medium text-gray-700">Alamat Rumah Sewa <span class="text-red-500">*</span></label>
                            <textarea id="rental_address" name="rental_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>

                        <!-- Jalan -->
                        <div>
                            <label for="street" class="block text-sm font-medium text-gray-700">Jalan <span class="text-red-500">*</span></label>
                            <input type="text" id="street" name="street" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Gang/Nomor -->
                        <div>
                            <label for="alley_number" class="block text-sm font-medium text-gray-700">Gang/Nomor <span class="text-red-500">*</span></label>
                            <input type="text" id="alley_number" name="alley_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RT Field - Changed type to text to preserve leading zeros -->
                        <div class="form-group">
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" value="{{ old('rt') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <small class="text-gray-500">Format: 001, 002, 003, dll (dengan angka 0 di depan).</small>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Luas Bangunan -->
                        <div>
                            <label for="building_area" class="block text-sm font-medium text-gray-700">Luas Bangunan <span class="text-red-500">*</span></label>
                            <input type="text" id="building_area" name="building_area" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: 100 m²" required>
                        </div>

                        <!-- Jumlah Kamar -->
                        <div>
                            <label for="room_count" class="block text-sm font-medium text-gray-700">Jumlah Kamar <span class="text-red-500">*</span></label>
                            <input type="number" id="room_count" name="room_count" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Jenis Rumah/Kamar Sewa -->
                        <div>
                            <label for="rental_type" class="block text-sm font-medium text-gray-700">Jenis Rumah/Kamar Sewa <span class="text-red-500">*</span></label>
                            <input type="text" id="rental_type" name="rental_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: Kos, Kontrakan, Rumah Sewa" required>
                        </div>
                    </div>

                    <!-- Administrative Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Berlaku Ijin Sampai -->
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700">Berlaku Ijin Sampai</label>
                            <input type="date" id="valid_until" name="valid_until" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Pejabat Penandatangan -->
                        <div>
                            <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                            <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pejabat</option>
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/rental-house.js') }}"></script>
</x-layout>
