<!-- Wrapper Flex -->
<div class="flex flex-col md:flex-row gap-6">
    <!-- Card Nomor Antrian -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-300 p-6 text-center w-full md:w-1/3 self-start">
        <button class="text-black font-bold px-3 py-1 rounded-xl mb-4">Antrian Layanan Desa</button>
        <div class="border border-gray-300 rounded-2xl p-6">
            <div class="text-sm text-gray-500 mb-1">No Antrian Saat Ini</div>
            @if(session('no_antrian'))
                <div class="text-5xl font-bold">{{ session('no_antrian') }}</div>
                <div class="mt-2 text-green-600 text-sm">Nomor antrian anda</div>
                @if(session('village_name'))
                    <div class="mt-1 text-sm text-gray-600">Desa: {{ session('village_name') }}</div>
                @endif
            @else
                <div class="text-5xl font-bold">-</div>
            @endif
        </div>
        <p class="mt-4 text-sm italic text-gray-600">Quod Enchiridion Epictetus stoici scripsit. Rodrigo Abela</p>
    </div>

    <!-- Form Langsung -->
    <div class="w-full md:w-3/5 md:ml-10">
        <form action="{{ route('guest.pelayanan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                <input type="text" id="nama" name="nama" placeholder="Nama" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Provinsi -->
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

            <!-- Kabupaten -->
            <div>
                <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                <select id="district_code" name="district_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kabupaten</option>
                </select>
                <input type="hidden" id="district_id" name="district_id" value="">
            </div>

            <!-- Kecamatan -->
            <div>
                <label for="sub_district_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                <select id="sub_district_code" name="sub_district_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kecamatan</option>
                </select>
                <input type="hidden" id="sub_district_id" name="sub_district_id" value="">
            </div>

            <!-- Desa -->
            <div>
                <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                <select id="village_code" name="village_code" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Desa</option>
                </select>
                <input type="hidden" id="village_id" name="village_id" value="">
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                <input type="text" id="alamat" name="alamat" placeholder="Alamat" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Keperluan -->
            <div class="md:col-span-2">
                <label for="keperluan" class="block text-sm font-medium text-gray-700">Keperluan <span class="text-red-500">*</span></label>
                <select id="keperluan" name="keperluan" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">-- Pilih Keperluan --</option>
                    @foreach($keperluanList as $keperluan)
                        <option value="{{ $keperluan->id }}">{{ $keperluan->judul }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol -->
        <div class="md:col-span-2">
            <button type="submit" class="bg-[#969BE7] text-white px-6 py-2 rounded-xl hover:bg-[#7d82d6]">Submit</button>
            <button type="button" class="ml-4 bg-yellow-500 text-white px-4 py-2 rounded-xl hover:bg-yellow-600">Tanya Petugas</button>
        </div>
        </form>
    </div>
</div>

<!-- Include necessary scripts for component -->
<script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
<script src="{{ asset('js/location-selector.js') }}"></script>

<script>
    // Show success message if available
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showSuccessAlert("{{ session('success') }}");
        @endif
    });
</script>
