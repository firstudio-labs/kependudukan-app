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
    <div class="w-full lg:w-2/3" id="rental-house-form-container"
         data-citizen-route="{{ route('citizens.administrasi') }}"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Izin Rumah Sewa</h1>

        <form method="POST" action="{{ route('guest.surat.rumah-sewa.store') }}">
            @csrf

            <!-- Data Wilayah Section -->
            {{-- <div class="mb-2 mt-6">
                <h2 class="text-xl font-bold text-gray-800">Data Wilayah</h2>
            </div>
 --}}
            <!-- Hidden Location Fields (instead of visible dropdowns) -->
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
                    <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                    <select id="nikSelect" name="nik" class="nik-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih NIK</option>
                    </select>
                </div>

                <div>
                    <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Penyelenggara <span class="text-red-500">*</span></label>
                    <select id="fullNameSelect" name="full_name" class="fullname-select mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Nama</option>
                    </select>
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
                    <input type="text" id="rt" name="rt" value="{{ old('rt') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <small class="text-gray-500">Format: 001, 002, 003, dll (dengan angka 0 di depan).</small>
                </div>

                <div>
                    <label for="building_area" class="block text-sm font-medium text-gray-700">Luas Bangunan <span class="text-red-500">*</span></label>
                    <input type="text" id="building_area" name="building_area" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: 100 m²" required>
                </div>

                <div>
                    <label for="room_count" class="block text-sm font-medium text-gray-700">Jumlah Kamar <span class="text-red-500">*</span></label>
                    <input type="number" id="room_count" name="room_count" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <div>
                    <label for="rental_type" class="block text-sm font-medium text-gray-700">Jenis Rumah/Kamar Sewa <span class="text-red-500">*</span></label>
                    <input type="text" id="rental_type" name="rental_type" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="contoh: Kos, Kontrakan, Rumah Sewa" required>
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700">Berlaku Ijin Sampai</label>
                    <input type="date" id="valid_until" name="valid_until" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>
            </div>

            <div class="mt-2">
                <label for="rental_address" class="block text-sm font-medium text-gray-700">Alamat Rumah Sewa <span class="text-red-500">*</span></label>
                <textarea id="rental_address" name="rental_address" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
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
</div>

<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>
<script src="{{ asset('js/citizen-only-form.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize citizen data select fields
        initializeCitizenSelect('{{ route("citizens.administrasi") }}');

        // Initialize responsible person field with the same approach as the organizer fields
        $('#responsibleNameSelect').select2({
            placeholder: 'Pilih Nama Penanggung Jawab',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("citizens.administrasi") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    let results = [];
                    if (data.data) {
                        results = data.data.map(citizen => ({
                            id: citizen.full_name,
                            text: citizen.full_name,
                            citizen: citizen
                        }));
                    }
                    return { results };
                }
            },
            templateResult: function(data) {
                if (!data.citizen) return data.text;
                return $(`<div>
                    <div>${data.text}</div>
                    <small class="text-muted">${data.citizen.nik || ''} - ${data.citizen.address || ''}</small>
                </div>`);
            },
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 1 karakter untuk mencari nama...';
                },
                noResults: function() {
                    return 'Tidak ada data yang ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        }).on("select2:open", function() {
            $('.select2-results__options').css('max-height', '400px');
        });

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
