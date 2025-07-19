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
                <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Ketik nama untuk mencari...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Ketik minimal 3 karakter untuk mencari nama</p>
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
                <input t
