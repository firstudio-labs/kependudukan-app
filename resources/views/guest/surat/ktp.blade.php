<x-guest.surat-layout title="Surat Keterangan Pengantar KTP">
    <!-- Title Heading -->
<h1 class="text-2xl font-extrabold text-gray-800 text-shadow mb-4">Portal Layanan Desa</h1>

<!-- Form Section - Full Width -->
<div class="w-full" id="ktp-form-container"
     data-citizen-route="{{ route('citizens.administrasi') }}"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Pengantar KTP</h1>

    <form method="POST" action="{{ route('guest.surat.ktp.store') }}">
        @csrf

        <!-- Data Pemohon Section -->
        <div class="mb-2 mt-6">
            <h2 class="text-xl font-bold text-gray-800">Data Pemohon</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- NIK with Search -->
            <div>
                <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                <input type="text" id="nikSelect" name="nik"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                       placeholder="Masukkan NIK (16 digit)"
                       maxlength="16"
                       pattern="\d{16}"
                       required>
                <p class="text-xs text-gray-500 mt-1">Masukkan 16 digit NIK untuk pencarian otomatis</p>
            </div>

            <!-- Nama Lengkap with Search -->
            <div>
                <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Ketik nama untuk mencari...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Ketik minimal 3 karakter untuk mencari nama</p>
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

            <!-- RF ID Tag -->
            <div>
                <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
                <input type="text" id="rf_id_tag" name="rf_id_tag"
                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 transition-colors duration-200"
                       placeholder="Scan RF ID Tag">
                <p class="text-xs text-gray-500 mt-1">Masukkan RF ID Tag untuk mengisi data otomatis</p>
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

<!-- JavaScript Variables for use in external file -->
<script>
    const BASE_URL = "{{ url('/') }}";
    const CITIZENS_URL = "{{ route('citizens.administrasi') }}";
    const SUCCESS_MESSAGE = "{{ session('success') }}";
    const ERROR_MESSAGE = "{{ session('error') }}";
</script>

<!-- Include the external JavaScript file -->
<script src="{{ asset('js/pengantar-ktp-url.js') }}"></script>
</x-guest.surat-layout>
