<x-guest.surat-layout title="Surat Keterangan Pengantar KTP">
    <!-- Title Heading -->
<h1 class="text-2xl font-extrabold text-gray-800 text-shadow mb-4">Portal Layanan Desa</h1>

<!-- Form Section - Full Width -->
<div class="w-full" id="rental-house-form-container"
     data-citizen-route="{{ route('citizens.administrasi') }}"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Izin Rumah Sewa</h1>

    <form method="POST" action="{{ route('guest.surat.rumah-sewa.store') }}">
        @csrf

        <!-- Organizer Information Section -->
        <div class="mb-2 mt-6">
            <h2 class="text-xl font-bold text-gray-800">Data Penyelenggara</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

            <div>
                <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Penyelenggara <span class="text-red-500">*</span></label>
                <input type="text" id="fullNameSelect" name="full_name" placeholder="Nama Penyelenggara" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                <p class="text-xs text-gray-500 mt-1">Masukkan nama penyelenggara secara manual</p>
            </div>

            <div>
                <label for="responsibleNameSelect" class="block text-sm font-medium text-gray-700">Nama Penanggung Jawab <span class="text-red-500">*</span></label>
                <select id="responsibleNameSelect" name="responsible_name" class="responsiblename-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Nama Penanggung Jawab</option>
                </select>
            </div>

            <div>
                <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
                <input type="text" id="rf_id_tag" name="rf_id_tag"
                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 transition-colors duration-200"
                       placeholder="Scan RF ID Tag">
                <p class="text-xs text-gray-500 mt-1">Masukkan RF ID Tag untuk mengisi data otomatis</p>
            </div>
        </div>

        <div class="mt-2">
            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Penyelenggara <span class="text-red-500">*</span></label>
            <textarea id="address" name="address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
        </div>

        <!-- Rental Property Information Section -->
        <div class="mb-2 mt-6">
            <h2 class="text-xl font-bold text-gray-800">Informasi Rumah Sewa</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="street" class="block text-sm font-medium text-gray-700">Jalan <span class="text-red-500">*</span></label>
                <input type="text" id="street" name="street" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <div>
                <label for="alley_number" class="block text-sm font-medium text-gray-700">Gang/Nomor <span class="text-red-500">*</span></label>
                <input type="text" id="alley_number" name="alley_number" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <div>
                <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                <input type="text" id="rt" name="rt" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <div>
                <label for="rw" class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                <input type="text" id="rw" name="rw" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <div>
                <label for="rental_purpose" class="block text-sm font-medium text-gray-700">Tujuan Sewa <span class="text-red-500">*</span></label>
                <input type="text" id="rental_purpose" name="rental_purpose" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <div>
                <label for="rental_duration" class="block text-sm font-medium text-gray-700">Jangka Waktu Sewa <span class="text-red-500">*</span></label>
                <input type="text" id="rental_duration" name="rental_duration" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>
        </div>

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

<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/rental-house-url.js') }}"></script>
</x-guest.surat-layout>
