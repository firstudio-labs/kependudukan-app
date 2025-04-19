@props(['title', 'route', 'jobs', 'provinces', 'signers' => [], 'section_title' => 'Data Pribadi'])

<div class="p-2 sm:p-4 mt-8 sm:mt-14">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">{{ $title }}</h1>

    <form method="POST" action="{{ $route }}" class="bg-white p-3 sm:p-6 rounded-lg shadow-md">
        @csrf

        <!-- Data Pribadi/Almarhum Section -->
        <div class="mt-4 sm:mt-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">{{ $section_title }}</h2>
            <div class="border p-3 sm:p-4 rounded-md mb-4 bg-gray-50">
                <div class="flex flex-col space-y-4">
                    <!-- NIK -->
                    <div>
                        <label for="nikSelect" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                        <select id="nikSelect" name="nik" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Pilih NIK</option>
                        </select>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="fullNameSelect" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <select id="fullNameSelect" name="full_name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Pilih Nama Lengkap</option>
                        </select>
                    </div>

                    <!-- Tempat & Tanggal Lahir on same row on larger screens -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tempat Lahir -->
                        <div>
                            <label for="birth_place" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="birth_place" name="birth_place" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="birth_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="birth_date" name="birth_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                        </div>
                    </div>

                    <!-- Jenis Kelamin & Pekerjaan on same row on larger screens -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="gender" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="gender" name="gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="1">Laki-Laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>

                        <!-- Pekerjaan -->
                        <div>
                            <label for="job_type_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Pekerjaan <span class="text-red-500">*</span></label>
                            <select id="job_type_id" name="job_type_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                                <option value="">Pilih Pekerjaan</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Agama (standalone) -->
                    <div>
                        <label for="religion" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Agama <span class="text-red-500">*</span></label>
                        <select id="religion" name="religion" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
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

                    <!-- Alamat (standalone - full width) -->
                    <div>
                        <label for="address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required></textarea>
                    </div>

                    <!-- RT (standalone - with fixed height) -->
                    <div>
                        <label for="rt" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">RT <span class="text-red-500">*</span></label>
                        <input type="text" id="rt" name="rt" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Wilayah Section -->
        <div class="mt-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">Data Wilayah</h2>
            <div class="border p-3 sm:p-4 rounded-md mb-4 bg-gray-50">
                <div class="flex flex-col space-y-4">
                    <!-- Provinsi -->
                    <div>
                        <label for="province_code" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                        <select id="province_code" name="province_code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id">
                    </div>

                    <!-- Kabupaten -->
                    <div>
                        <label for="district_code" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Kabupaten <span class="text-red-500">*</span></label>
                        <select id="district_code" name="district_code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="district_id" name="district_id">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label for="subdistrict_code" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                        <select id="subdistrict_code" name="subdistrict_code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="subdistrict_id" name="subdistrict_id">
                    </div>

                    <!-- Desa -->
                    <div>
                        <label for="village_code" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Desa <span class="text-red-500">*</span></label>
                        <select id="village_code" name="village_code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="village_id" name="village_id">
                    </div>
                </div>
            </div>
        </div>

        <!-- Letter Information -->
        <div class="mt-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-3">Informasi Surat</h2>
            <div class="border p-3 sm:p-4 rounded-md mb-4 bg-gray-50">
                <div class="flex flex-col space-y-4">
                    <!-- Nomor Surat -->
                    <div>
                        <label for="letter_number" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2">
                    </div>

                    <!-- Penandatangan -->
                    <div>
                        <label for="signing" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Pejabat Penandatangan</label>
                        <select id="signing" name="signing" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base p-1.5 sm:p-2">
                            <option value="">Pilih Pejabat Penandatangan</option>
                            @if(!empty($signers))
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->judul }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Additional Fields Slot -->
                    <div>
                        {{ $additionalFields ?? '' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4">
            <button type="button" onclick="window.history.back()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </button>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#7886C7] hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Sweet Alert Utilities -->
<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>

<!-- Make signers data available to JavaScript -->
@if(!empty($signers))
<script>
    // Pass signers data to JavaScript as a global variable
    var signerOptions = @json($signers);
</script>
@endif

<!-- Slot for additional scripts specific to each document type -->
{{ $scripts ?? '' }}

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
