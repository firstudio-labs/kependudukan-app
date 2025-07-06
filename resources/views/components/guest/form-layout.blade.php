@props([
    'title',
    'route',
    'jobs',
    'provinces',
    'districts' => [],
    'subDistricts' => [],
    'villages' => [],
    'section_title' => 'Data Pribadi',
    'queueNumber' => '01',
    'villageName' => null,
    'province_id' => null,
    'district_id' => null,
    'sub_district_id' => null,
    'village_id' => null
])

<!-- Title Heading -->
<h1 class="text-2xl font-extrabold text-gray-800 text-shadow mb-4">Portal Layanan Desa</h1>

<!-- Wrapper Flex -->
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Card Nomor Antrian -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-md border border-white/20 p-6 text-center w-full lg:w-1/3 self-start">
        {{-- <button class="text-black font-semibold px-4 py-2 rounded-xl mb-4 bg-white/10 backdrop-blur-lg border border-white/20 shadow-sm">
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

        <p class="mt-4 text-sm italic text-black">Quod Enchiridion Epictetus stoici scripsit. Rodrigo Abela</p> --}}
    </div>

    <!-- Form Section -->
    <div class="w-full lg:w-2/3">
        <form method="POST" action="{{ $route }}" class="grid grid-cols-1 gap-4">
            @csrf

            <!-- Form Title -->
            <h3 class="text-lg font-medium text-gray-700 mb-2">{{ $title }}</h3>

            <!-- Hidden Location Fields (instead of visible dropdowns) -->
            <input type="hidden" id="province_id" name="province_id" value="{{ request('province_id', $province_id) }}">
            <input type="hidden" id="district_id" name="district_id" value="{{ request('district_id', $district_id) }}">
            <input type="hidden" id="sub_district_id" name="subdistrict_id" value="{{ request('sub_district_id', $sub_district_id) }}">
            <input type="hidden" id="village_id" name="village_id" value="{{ request('village_id', $village_id) }}">

            <!-- Data Pribadi Section -->
            <div class="mb-2">
                <h2 class="text-xl font-bold text-gray-800">{{ $section_title }}</h2>
            </div>

            <!-- Personal Info Section - 2 columns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIK -->
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

                <!-- Nama Lengkap -->
                <div>
                    <label for="fullNameSelect" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <select id="fullNameSelect" name="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Nama Lengkap</option>
                    </select>
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" id="birth_place" name="birth_place" placeholder="Tempat Lahir" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date" name="birth_date" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="1">Laki-Laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>

                <!-- Pekerjaan -->
                <div>
                    <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                    <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Pekerjaan</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                    <select id="religion" name="religion" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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

                <!-- RT/RW -->
                <div>
                    <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                    <input type="text" id="rt" name="rt" placeholder="RT" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

               <div>
    <label for="rf_id_tag" class="block text-sm font-medium text-gray-700">RF ID Tag</label>
    <input type="text" id="rf_id_tag" name="rf_id_tag"
        value="{{ $citizen['data']['rf_id_tag'] ?? '' }}"
        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2 transition-colors duration-200"
        placeholder="Scan RF ID Tag">
</div>
            </div>

            <!-- Alamat -->
            <div class="mt-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" rows="2" placeholder="Alamat" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            <!-- Additional Fields Slot -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                {{ $additionalFields ?? '' }}
            </div>

            <!-- Tombol -->
            <div class="flex justify-end mt-6">
                <button type="button" onclick="window.history.back()" class="bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-gray-600 mr-4">Batal</button>
                <button type="submit" class="bg-[#969BE7] text-white px-6 py-2 rounded-full hover:bg-[#7d82d6]">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Script for alerts -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showSuccessAlert("{{ session('success') }}");
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showErrorAlert("{{ session('error') }}");
    });
</script>
@endif

<!-- jQuery - Add this before other scripts that depend on it -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Sweet Alert Utilities -->
<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>

<!-- Use the new JS file for citizen-only functionality -->
<script src="{{ asset('js/citizen-only-form.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize citizen data select fields
        initializeCitizenSelect('{{ route("citizens.administrasi") }}');

        // Get location IDs from URL query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const provinceId = urlParams.get('province_id') || '{{ request('province_id', $province_id) }}';
        const districtId = urlParams.get('district_id') || '{{ request('district_id', $district_id) }}';
        const subDistrictId = urlParams.get('sub_district_id') || '{{ request('sub_district_id', $sub_district_id) }}';
        const villageId = urlParams.get('village_id') || '{{ request('village_id', $village_id) }}';

        // Set form hidden input values
        document.getElementById('province_id').value = provinceId;
        document.getElementById('district_id').value = districtId;
        document.getElementById('sub_district_id').value = subDistrictId;
        document.getElementById('village_id').value = villageId;

        // Setup form validation
        setupFormValidation();

        const rfIdInput = document.getElementById('rf_id_tag');
        if (rfIdInput) {
            rfIdInput.title = "Masukkan RF ID Tag untuk mengisi data otomatis";

            // Add helper text below the input
            const helperText = document.createElement('p');
            helperText.className = 'text-xs text-gray-500 mt-1';
            helperText.textContent = 'Masukkan RF ID Tag untuk mengisi data otomatis';
            rfIdInput.parentNode.appendChild(helperText);
        }
    });
</script>

<!-- Slot for additional scripts -->
{{ $scripts ?? '' }}

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
