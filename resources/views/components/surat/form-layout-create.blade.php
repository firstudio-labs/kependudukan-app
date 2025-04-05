@props(['title', 'route', 'jobs', 'provinces', 'signers' => [], 'section_title' => 'Data Pribadi'])

<div class="p-4 mt-14">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title }}</h1>

    <form method="POST" action="{{ $route }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf

        <!-- Data Pribadi/Almarhum Section -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">{{ $section_title }}</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK -->
                    <div>
                        <label for="nikSelect" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                        <select id="nikSelect" name="nik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih NIK</option>
                        </select>
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
                        <input type="text" id="birth_place" name="birth_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" id="birth_date" name="birth_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="1">Laki-Laki</option>
                            <option value="2">Perempuan</option>
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                        <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Pekerjaan</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                        <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
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

                    <!-- Alamat -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                    </div>

                    <!-- RT -->
                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                        <textarea id="rt" name="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Wilayah Section -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Data Wilayah</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Provinsi -->
                    <div>
                        <label for="province_code" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                        <select id="province_code" name="province_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id">
                    </div>

                    <!-- Kabupaten -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="district_id" name="district_id">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="subdistrict_id" name="subdistrict_id">
                    </div>

                    <!-- Desa -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="village_id" name="village_id">
                    </div>
                </div>
            </div>
        </div>

        <!-- Letter Information -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Surat</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nomor Surat -->
                    <div>
                        <label for="letter_number" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Penandatangan -->
                    <div>
                        <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                        <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Pejabat Penandatangan</option>
                            @if(!empty($signers))
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->judul }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Slot for additional fields specific to each document type -->
                    {{ $additionalFields ?? '' }}
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <button type="button" onclick="window.history.back()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                Batal
            </button>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
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
