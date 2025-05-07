<x-layout>
    <div class="p-4 mt-14" id="birth-form-container"
         data-citizen-route="{{ route('citizens.administrasi') }}"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Kelahiran</h1>

        <form method="POST" action="{{ route('admin.desa.surat.kelahiran.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Father Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Ayah</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ayah -->
                        <div>
                            <label for="father_nik" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                            <select id="father_nik" name="father_nik" class="father-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap Ayah -->
                        <div>
                            <label for="father_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ayah</label>
                            <select id="father_full_name" name="father_full_name" class="father-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ayah -->
                        <div>
                            <label for="father_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="father_birth_place" name="father_birth_place" class="father-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ayah -->
                        <div>
                            <label for="father_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="father_birth_date" name="father_birth_date" class="father-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ayah -->
                        <div>
                            <label for="father_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="father_job" name="father_job" class="father-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                @empty
                                    <option value="">Tidak ada data pekerjaan</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Agama Ayah -->
                        <div>
                            <label for="father_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                            <select id="father_religion" name="father_religion" class="father-religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Agama</option>
                                <option value="1">Islam</option>
                                <option value="2">Kristen</option>
                                <option value="3">Katholik</option>
                                <option value="4">Hindu</option>
                                <option value="5">Buddha</option>
                                <option value="6">Kong Hu Cu</option>
                                <option value="7">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ayah -->
                        <div>
                            <label for="father_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="father_address" name="father_address" class="father-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mother Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Ibu</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIK Ibu -->
                        <div>
                            <label for="mother_nik" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                            <select id="mother_nik" name="mother_nik" class="mother-nik-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih NIK</option>
                            </select>
                        </div>

                        <!-- Nama Lengkap Ibu -->
                        <div>
                            <label for="mother_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ibu</label>
                            <select id="mother_full_name" name="mother_full_name" class="mother-fullname-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Nama</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Ibu -->
                        <div>
                            <label for="mother_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" id="mother_birth_place" name="mother_birth_place" class="mother-birth-place mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Tanggal Lahir Ibu -->
                        <div>
                            <label for="mother_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="mother_birth_date" name="mother_birth_date" class="mother-birth-date mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Pekerjaan Ibu -->
                        <div>
                            <label for="mother_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <select id="mother_job" name="mother_job" class="mother-job mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Pekerjaan</option>
                                @forelse($jobTypes as $job)
                                    <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                @empty
                                    <option value="">Tidak ada data pekerjaan</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Agama Ibu -->
                        <div>
                            <label for="mother_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                            <select id="mother_religion" name="mother_religion" class="mother-religion mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                                <option value="">Pilih Agama</option>
                                <option value="1">Islam</option>
                                <option value="2">Kristen</option>
                                <option value="3">Katholik</option>
                                <option value="4">Hindu</option>
                                <option value="5">Buddha</option>
                                <option value="6">Kong Hu Cu</option>
                                <option value="7">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Ibu -->
                        <div>
                            <label for="mother_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="mother_address" name="mother_address" class="mother-address mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
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

            <!-- Child Information Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Anak</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Anak -->
                        <div>
                            <label for="child_name" class="block text-sm font-medium text-gray-700">Nama Anak <span class="text-red-500">*</span></label>
                            <input type="text" id="child_name" name="child_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Jenis Kelamin Anak -->
                        <div>
                            <label for="child_gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="child_gender" name="child_gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="1">Laki-Laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Tempat Lahir Anak -->
                        <div>
                            <label for="child_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="child_birth_place" name="child_birth_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Lahir Anak -->
                        <div>
                            <label for="child_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="child_birth_date" name="child_birth_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Anak Ke -->
                        <div>
                            <label for="child_order" class="block text-sm font-medium text-gray-700">Anak Ke <span class="text-red-500">*</span></label>
                            <input type="number" id="child_order" name="child_order" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Agama Anak -->
                        <div>
                            <label for="child_religion" class="block text-sm font-medium text-gray-700">Agama Anak <span class="text-red-500">*</span></label>
                            <select id="child_religion" name="child_religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Agama</option>
                                <option value="1">Islam</option>
                                <option value="2">Kristen</option>
                                <option value="3">Katholik</option>
                                <option value="4">Hindu</option>
                                <option value="5">Buddha</option>
                                <option value="6">Kong Hu Cu</option>
                                <option value="7">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- Alamat Anak -->
                        <div>
                            <label for="child_address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="child_address" name="child_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Surat Section -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Surat -->
                        <div>
                            <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        </div>

                        <!-- Penandatangan -->
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

    <script src="{{ asset('js/birth-certificate.js') }}"></script>
</x-layout>
