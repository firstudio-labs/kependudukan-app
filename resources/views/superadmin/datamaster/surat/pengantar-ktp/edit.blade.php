<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Surat Pengantar KTP</h1>

        <form method="POST" action="{{ route('superadmin.surat.pengantar-ktp.update', $ktp->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Section -->
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $ktp->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="{{ $ktp->province_id }}">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach($districts as $district)
                            <option value="{{ $district['code'] }}" data-id="{{ $district['id'] }}" {{ $ktp->district_id == $district['id'] ? 'selected' : '' }}>{{ $district['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="{{ $ktp->district_id }}">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['code'] }}" data-id="{{ $subDistrict['id'] }}" {{ $ktp->subdistrict_id == $subDistrict['id'] ? 'selected' : '' }}>{{ $subDistrict['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $ktp->subdistrict_id }}">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village['code'] }}" data-id="{{ $village['id'] }}" {{ $ktp->village_id == $village['id'] ? 'selected' : '' }}>{{ $village['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="{{ $ktp->village_id }}">
                </div>

                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="number" id="letter_number" name="letter_number" value="{{ $ktp->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <input type="text" id="signing" name="signing" value="{{ $ktp->signing }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <!-- Jenis Permohonan -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Jenis Permohonan</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div>
                        <label for="application_type" class="block text-sm font-medium text-gray-700">Permohonan KTP <span class="text-red-500">*</span></label>
                        <select id="application_type" name="application_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Permohonan</option>
                            <option value="Baru" {{ $ktp->application_type == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Perpanjang" {{ $ktp->application_type == 'Perpanjang' ? 'selected' : '' }}>Perpanjang</option>
                            <option value="Pergantian" {{ $ktp->application_type == 'Pergantian' ? 'selected' : '' }}>Pergantian</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pemohon KTP -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-semibold text-gray-700">Data Pemohon</h2>
                    <button type="button" id="addApplicant" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Tambah Pemohon
                    </button>
                </div>

                <div id="applicant-container">
                    @if(isset($ktp->nik) && is_array($ktp->nik))
                        @foreach($ktp->nik as $index => $nik)
                            <div class="applicant-row border p-4 rounded-md mb-4 bg-gray-50">
                                <div class="flex justify-between mb-3">
                                    <h3 class="font-medium">Pemohon #{{ $index + 1 }}</h3>
                                    @if($index > 0)
                                        <button type="button" class="remove-applicant px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">
                                            Hapus
                                        </button>
                                    @else
                                        <button type="button" class="remove-applicant px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600" style="display: none;">
                                            Hapus
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- NIK -->
                                    <div>
                                        <label for="nik_{{ $index }}" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                        <input type="text" id="nik_{{ $index }}" name="nik[]" value="{{ $nik }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    </div>

                                    <!-- Nama Lengkap -->
                                    <div>
                                        <label for="full_name_{{ $index }}" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                        <input type="text" id="full_name_{{ $index }}" name="full_name[]" value="{{ $ktp->full_name[$index] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="applicant-row border p-4 rounded-md mb-4 bg-gray-50">
                            <div class="flex justify-between mb-3">
                                <h3 class="font-medium">Pemohon #1</h3>
                                <button type="button" class="remove-applicant px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600" style="display: none;">
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- NIK -->
                                <div>
                                    <label for="nik_0" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                                    <input type="text" id="nik_0" name="nik[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                </div>

                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="full_name_0" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" id="full_name_0" name="full_name[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- KK dan Alamat Information -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Kartu Keluarga & Alamat</h2>
                <div class="border p-4 rounded-md mb-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nomor Kartu Keluarga -->
                        <div>
                            <label for="family_card_number" class="block text-sm font-medium text-gray-700">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                            <input type="text" id="family_card_number" name="family_card_number" value="{{ $ktp->family_card_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $ktp->address }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                        <!-- RT -->
                        <div>
                            <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input type="text" id="rt" name="rt" value="{{ $ktp->rt }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- RW -->
                        <div>
                            <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input type="text" id="rw" name="rw" value="{{ $ktp->rw }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Dusun -->
                        <div>
                            <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun <span class="text-red-500">*</span></label>
                            <input type="text" id="hamlet" name="hamlet" value="{{ $ktp->hamlet }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <!-- Desa/Kelurahan -->
                        <div>
                            <label for="village_name" class="block text-sm font-medium text-gray-700">Desa/Kelurahan <span class="text-red-500">*</span></label>
                            <input type="text" id="village_name" name="village_name" value="{{ $ktp->village_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Kecamatan -->
                        <div>
                            <label for="subdistrict_name" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                            <input type="text" id="subdistrict_name" name="subdistrict_name" value="{{ $ktp->subdistrict_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Update
                </button>
            </div>
        </form>
    </div>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
</x-layout>
