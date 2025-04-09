@props(['title', 'route', 'jobs', 'provinces', 'data', 'signers' => [], 'section_title' => 'Data Pribadi'])

<div class="p-4 mt-14">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title }}</h1>

    <form method="POST" action="{{ $route }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <!-- Data Pribadi/Almarhum Section -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">{{ $section_title }}</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700">NIK <span class="text-red-500">*</span></label>
                        <input type="text" id="nik" name="nik" pattern="\d{16}" maxlength="16" value="{{ $data->nik }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ $data->full_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="birth_place" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ $data->birth_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                        @php
                            // Ensure birth_date is properly formatted for the date input
                            $birthDate = '';
                            if (isset($data->birth_date)) {
                                if ($data->birth_date instanceof \DateTime) {
                                    $birthDate = $data->birth_date->format('Y-m-d');
                                } else {
                                    $birthDate = date('Y-m-d', strtotime($data->birth_date));
                                }
                            }
                        @endphp
                        <input type="date" id="birth_date" name="birth_date" value="{{ $birthDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="1" {{ $data->gender == '1' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="2" {{ $data->gender == '2' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="job_type_id" class="block text-sm font-medium text-gray-700">Pekerjaan <span class="text-red-500">*</span></label>
                        <select id="job_type_id" name="job_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Pekerjaan</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job['id'] }}" {{ $data->job_type_id == $job['id'] ? 'selected' : '' }}>{{ $job['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                        <select id="religion" name="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Pilih Agama</option>
                            <option value="1" {{ $data->religion == '1' ? 'selected' : '' }}>Islam</option>
                            <option value="2" {{ $data->religion == '2' ? 'selected' : '' }}>Kristen</option>
                            <option value="3" {{ $data->religion == '3' ? 'selected' : '' }}>Katholik</option>
                            <option value="4" {{ $data->religion == '4' ? 'selected' : '' }}>Hindu</option>
                            <option value="5" {{ $data->religion == '5' ? 'selected' : '' }}>Buddha</option>
                            <option value="6" {{ $data->religion == '6' ? 'selected' : '' }}>Kong Hu Cu</option>
                            <option value="7" {{ $data->religion == '7' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $data->address }}</textarea>
                    </div>

                    <!-- RT -->
                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                        <textarea id="rt" name="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $data->rt }}</textarea>
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
                                <option value="{{ $province['code'] }}" data-id="{{ $province['id'] }}" {{ $data->province_id == $province['id'] ? 'selected' : '' }}>{{ $province['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="province_id" name="province_id" value="{{ $data->province_id }}">
                    </div>

                    <!-- Kabupaten -->
                    <div>
                        <label for="district_code" class="block text-sm font-medium text-gray-700">Kabupaten <span class="text-red-500">*</span></label>
                        <select id="district_code" name="district_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="district_id" name="district_id" value="{{ $data->district_id }}">
                    </div>

                    <!-- Kecamatan -->
                    <div>
                        <label for="subdistrict_code" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select id="subdistrict_code" name="subdistrict_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ $data->subdistrict_id }}">
                    </div>

                    <!-- Desa -->
                    <div>
                        <label for="village_code" class="block text-sm font-medium text-gray-700">Desa <span class="text-red-500">*</span></label>
                        <select id="village_code" name="village_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                            <option value="">Memuat data...</option>
                        </select>
                        <input type="hidden" id="village_id" name="village_id" value="{{ $data->village_id }}">
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
                        <input type="text" id="letter_number" name="letter_number" value="{{ $data->letter_number }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                    </div>

                    <!-- Penandatangan -->
                    <div>
                        <label for="signing" class="block text-sm font-medium text-gray-700">Pejabat Penandatangan</label>
                        <select id="signing" name="signing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                            <option value="">Pilih Pejabat Penandatangan</option>
                            @if(!empty($signers))
                                @foreach($signers as $signer)
                                    <option value="{{ $signer->judul }}" {{ $data->signing == $signer->judul ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>
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
                Perbarui
            </button>
        </div>
    </form>
</div>

<!-- Sweet Alert Utilities -->
<script src="{{ asset('js/sweet-alert-utils.js') }}"></script>

<!-- Slot for additional scripts specific to each document type -->
{{ $scripts ?? '' }}

@if(!empty($signers))
<script>
    // Pass signers data to JavaScript as a global variable
    var signerOptions = @json($signers);
</script>
@endif

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
