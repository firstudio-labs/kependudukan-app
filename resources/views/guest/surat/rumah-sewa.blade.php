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

            <!-- Hidden Location Fields -->
            <input type="hidden" id="province_id" name="province_id" value="{{ request('province_id') }}">
            <input type="hidden" id="district_id" name="district_id" value="{{ request('district_id') }}">
            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ request('sub_district_id') }}">
            <input type="hidden" id="village_id" name="village_id" value="{{ request('village_id') }}">

            <!-- Organizer Information Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Penyelenggara</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
                    <input type="text" id="rf_id_tag" name="rf_id_tag"
                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 transition-colors duration-200"
                           placeholder="Scan RF ID Tag">
                    <p class="text-xs text-gray-500 mt-1">Masukkan RF ID Tag untuk mengisi data otomatis</p>
                </div>
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
            </div>

            <div class="mt-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Alamat Penyelenggara <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            <!-- Rental Property Information Section -->
            <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Informasi Rumah Sewa</h2>
            </div>

            <!-- Main Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <!-- Alamat Rumah Sewa -->
                <div>
                    <label for="rental_address" class="block text-sm font-medium text-gray-700">Alamat Rumah Sewa <span class="text-red-500">*</span></label>
                    <textarea id="rental_address" name="rental_address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div>

                <!-- Jalan -->
                <div>
                    <label for="street" class="block text-sm font-medium text-gray-700">Jalan <span class="text-red-500">*</span></label>
                    <input type="text" id="street" name="street" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>
            </div>

            <!-- Location Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <!-- Gang/Nomor -->
                <div>
                    <label for="alley_number" class="block text-sm font-medium text-gray-700">Gang/Nomor <span class="text-red-500">*</span></label>
                    <input type="text" id="alley_number" name="alley_number" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- RT Field -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <small class="text-gray-500">Format: 001, 002, 003, dll (dengan angka 0 di depan).</small>
                </div>
            </div>

            <!-- Property Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <!-- Luas Bangunan -->
                <div>
                    <label for="building_area" class="block text-sm font-medium text-gray-700">Luas Bangunan <span class="text-red-500">*</span></label>
                    <input type="text" id="building_area" name="building_area" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: 100 m²" required>
                </div>

                <!-- Jumlah Kamar -->
                <div>
                    <label for="room_count" class="block text-sm font-medium text-gray-700">Jumlah Kamar <span class="text-red-500">*</span></label>
                    <input type="number" id="room_count" name="room_count" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Rumah/Kamar Sewa -->
                <div>
                    <label for="rental_type" class="block text-sm font-medium text-gray-700">Jenis Rumah/Kamar Sewa <span class="text-red-500">*</span></label>
                    <input type="text" id="rental_type" name="rental_type" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: Kos, Kontrakan, Rumah Sewa" required>
                </div>
            </div>

            <!-- Administrative Details -->
            {{-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nomor Surat -->
                <div>
                    <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div> --}}

                {{-- <!-- Pejabat Penandatangan -->
                <div>
                    <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                    <select id="signing" name="signing" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                        <option value="">Pilih Pejabat</option>
                        @foreach($signers as $signer)
                            <option value="{{ $signer->judul }}">
                                {{ $signer->judul }} - {{ $signer->keterangan }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
            {{-- </div> --}}

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

    <!-- Alert untuk error dan success -->
    @if(session('error'))
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="errorModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Gagal!</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">{{ session('error') }}</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="closeErrorModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="successModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Berhasil!</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">{{ session('success') }}</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="closeSuccessModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/rental-house-url.js') }}"></script>

    <script>
        // Script untuk menutup modal
        document.addEventListener('DOMContentLoaded', function() {
            const closeErrorModal = document.getElementById('closeErrorModal');
            const closeSuccessModal = document.getElementById('closeSuccessModal');
            const errorModal = document.getElementById('errorModal');
            const successModal = document.getElementById('successModal');

            if (closeErrorModal) {
                closeErrorModal.addEventListener('click', function() {
                    errorModal.style.display = 'none';
                });
            }

            if (closeSuccessModal) {
                closeSuccessModal.addEventListener('click', function() {
                    successModal.style.display = 'none';
                });
            }

            // Auto close modal after 5 seconds
            if (errorModal) {
                setTimeout(() => {
                    errorModal.style.display = 'none';
                }, 5000);
            }

            if (successModal) {
                setTimeout(() => {
                    successModal.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</x-guest.surat-layout>
