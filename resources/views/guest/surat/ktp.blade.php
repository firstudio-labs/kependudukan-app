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

    <div class="w-full lg:w-2/3">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Pengantar KTP</h1>

        <form method="POST" action="{{ route('guest.surat.ktp.store') }}">
            @csrf

            <!-- Data Wilayah Section -->
            {{-- <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Wilayah</h2>
            </div> --}}

            <!-- Hidden Location Fields (instead of visible dropdowns) -->
            <input type="hidden" id="province_id" name="province_id" value="{{ request('province_id') }}">
            <input type="hidden" id="district_id" name="district_id" value="{{ request('district_id') }}">
            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ request('sub_district_id') }}">
            <input type="hidden" id="village_id" name="village_id" value="{{ request('village_id') }}">

            <!-- Data Pemohon Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Pemohon</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIK with Search -->
                <div>
                    <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <select id="nikSelect" name="nik" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih NIK</option>
                    </select>
                </div>

                <!-- Nama Lengkap with Search -->
                <div>
                    <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Nama Lengkap</option>
                    </select>
                </div>

                <!-- Nomor Kartu Keluarga -->
                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                    <input type="text" id="kk" name="kk" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- RT -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                </div>

                <!-- RW -->
                <div>
                    <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                    <input type="text" id="rw" name="rw" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <small class="text-gray-500">Contoh: 001, 002, dll.</small>
                </div>

                <!-- Dusun -->
                <div>
                    <label for="hamlet" class="block text-sm font-medium text-gray-700">Dusun <span class="text-red-500">*</span></label>
                    <input type="text" id="hamlet" name="hamlet" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
            </div>

            <!-- Alamat -->
            <div class="mt-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            <!-- Informasi Surat Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Informasi Surat</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Jenis Permohonan -->
                <div>
                    <label for="application_type" class="block text-sm font-medium text-gray-700">Permohonan KTP <span class="text-red-500">*</span></label>
                    <select id="application_type" name="application_type" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Permohonan</option>
                        <option value="Baru">Baru</option>
                        <option value="Perpanjang">Perpanjang</option>
                        <option value="Pergantian">Pergantian</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" onclick="window.history.back()" class="bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-gray-600 mr-4">
                    Batal
                </button>
                <button type="submit" class="bg-[#969BE7] text-white px-6 py-2 rounded-full hover:bg-[#7d82d6]">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

    <!-- JavaScript Variables for use in external file -->
    <script>
        const BASE_URL = "{{ url('/') }}";
        const CITIZENS_URL = "{{ route('citizens.administrasi') }}";
        const SUCCESS_MESSAGE = "{{ session('success') }}";
        const ERROR_MESSAGE = "{{ session('error') }}";
    </script>

    <!-- Include the external JavaScript files -->
    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <script src="{{ asset('js/citizen-only-form.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize citizen data select fields
            initializeCitizenSelect('{{ route("citizens.administrasi") }}');

            // Initialize SweetAlert messages
            if ("{{ session('success') }}") {
                showSuccessAlert("{{ session('success') }}");
            }

            if ("{{ session('error') }}") {
                showErrorAlert("{{ session('error') }}");
            }
        });
    </script>
</x-guest.surat-layout>
