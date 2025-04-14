<x-guest.surat-layout title="Surat Keterangan Pengantar KTP">
    <!-- Title Heading -->
<h1 class="text-2xl font-extrabold text-gray-800 text-shadow mb-4">Portal Layanan Desa</h1>

<!-- Wrapper Flex -->
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Card Nomor Antrian -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-md border border-white/20 p-6 text-center w-full lg:w-1/3 self-start">
        <button class="text-black font-semibold px-4 py-2 rounded-xl mb-4 bg-white/10 backdrop-blur-lg border border-white/20 shadow-sm">
            Antrian Layanan Desa
        </button>

        <div class="border border-white/20 rounded-2xl p-6 bg-white/5 backdrop-blur-lg shadow-inner">
            <div class="text-sm text-black mb-1">No Antrian Saat Ini</div>
            <div class="text-5xl font-bold text-black drop-shadow-md">{{ $queueNumber }}</div>
            @if($villageName)
                <div class="mt-2 text-[#a7a7ee] text-sm">Nomor antrian anda</div>
                <div class="mt-1 text-sm text-gray-600">Desa: {{ $villageName }}</div>
            @endif
        </div>

        <p class="mt-4 text-sm italic text-black">Quod Enchiridion Epictetus stoici scripsit. Rodrigo Abela</p>
    </div>
    <div class="w-full lg:w-2/3" id="birth-form-container"
         data-citizen-route="{{ route('citizens.administrasi') }}"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Keterangan Kelahiran</h1>

        <form method="POST" action="{{ route('guest.surat.kelahiran.store') }}">
            @csrf

            <!-- Data Wilayah Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Wilayah</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                    <select id="province_code" name="province_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="province_id" name="province_id" value="">
                </div>

                <div>
                    <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                    <select id="district_code" name="district_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" id="district_id" name="district_id" value="">
                </div>

                <div>
                    <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="">
                </div>

                <div>
                    <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                    <select id="village_code" name="village_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" disabled required>
                        <option value="">Pilih Desa</option>
                    </select>
                    <input type="hidden" id="village_id" name="village_id" value="">
                </div>
            </div>

            <!-- Father Information Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Ayah</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="father_nik" class="block text-sm font-medium text-gray-700">NIK Ayah</label>
                    <select id="father_nik" name="father_nik" class="father-nik-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih NIK</option>
                    </select>
                </div>

                <div>
                    <label for="father_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ayah</label>
                    <select id="father_full_name" name="father_full_name" class="father-fullname-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Nama</option>
                    </select>
                </div>

                <div>
                    <label for="father_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="father_birth_place" name="father_birth_place" class="father-birth-place mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="father_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="father_birth_date" name="father_birth_date" class="father-birth-date mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="father_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                    <select id="father_job" name="father_job" class="father-job mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Pekerjaan</option>
                        @forelse($jobTypes as $job)
                            <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                        @empty
                            <option value="">Tidak ada data pekerjaan</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label for="father_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="father_religion" name="father_religion" class="father-religion mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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

            <div class="mt-2">
                <label for="father_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea id="father_address" name="father_address" class="father-address mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
            </div>

            <!-- Mother Information Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Ibu</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="mother_nik" class="block text-sm font-medium text-gray-700">NIK Ibu</label>
                    <select id="mother_nik" name="mother_nik" class="mother-nik-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih NIK</option>
                    </select>
                </div>

                <div>
                    <label for="mother_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap Ibu</label>
                    <select id="mother_full_name" name="mother_full_name" class="mother-fullname-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Nama</option>
                    </select>
                </div>

                <div>
                    <label for="mother_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" id="mother_birth_place" name="mother_birth_place" class="mother-birth-place mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="mother_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="mother_birth_date" name="mother_birth_date" class="mother-birth-date mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="mother_job" class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                    <select id="mother_job" name="mother_job" class="mother-job mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Pekerjaan</option>
                        @forelse($jobTypes as $job)
                            <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                        @empty
                            <option value="">Tidak ada data pekerjaan</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label for="mother_religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <select id="mother_religion" name="mother_religion" class="mother-religion mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
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

            <div class="mt-2">
                <label for="mother_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea id="mother_address" name="mother_address" class="mother-address mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"></textarea>
            </div>

            <!-- Child Information Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Anak</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="child_name" class="block text-sm font-medium text-gray-700">Nama Anak <span class="text-red-500">*</span></label>
                    <input type="text" id="child_name" name="child_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <div>
                    <label for="child_gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="child_gender" name="child_gender" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="1">Laki-Laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="child_birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" id="child_birth_place" name="child_birth_place" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <div>
                    <label for="child_birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="child_birth_date" name="child_birth_date" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <div>
                    <label for="child_order" class="block text-sm font-medium text-gray-700">Anak Ke <span class="text-red-500">*</span></label>
                    <input type="number" id="child_order" name="child_order" min="1" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <div>
                    <label for="child_religion" class="block text-sm font-medium text-gray-700">Agama Anak <span class="text-red-500">*</span></label>
                    <select id="child_religion" name="child_religion" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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

            <div class="mt-2">
                <label for="child_address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                <textarea id="child_address" name="child_address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            {{-- <!-- Informasi Surat Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Informasi Surat</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <select id="signing" name="signing" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Pejabat</option>
                        @foreach($signers as $signer)
                            <option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/birth-certificate.js') }}"></script>
</x-guest.surat-layout>
