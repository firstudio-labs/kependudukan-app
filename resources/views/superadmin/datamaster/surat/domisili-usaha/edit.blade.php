<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Keterangan Domisili Usaha"
        route="{{ route('superadmin.surat.domisili-usaha.update', $domisiliUsaha->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$domisiliUsaha"
        :signers="$signers">

        <x-slot name="additionalFields">
            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ $domisiliUsaha->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ $domisiliUsaha->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" value="{{ $domisiliUsaha->letter_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Jenis Usaha -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_type" class="block text-sm font-medium text-gray-700">Jenis Usaha <span class="text-red-500">*</span></label>
                <input type="text" id="business_type" name="business_type" value="{{ $domisiliUsaha->business_type }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Alamat Usaha -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_address" class="block text-sm font-medium text-gray-700">Alamat Usaha <span class="text-red-500">*</span></label>
                <textarea id="business_address" name="business_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $domisiliUsaha->business_address }}</textarea>
            </div>

            <!-- Tahun Berdiri -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_year" class="block text-sm font-medium text-gray-700">Tahun Berdiri <span class="text-red-500">*</span></label>
                <input type="text" id="business_year" name="business_year" maxlength="4" pattern="\d{4}" value="{{ $domisiliUsaha->business_year }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                <p class="text-xs text-gray-500 mt-1">Format: YYYY (contoh: 2020)</p>
            </div>

            <!-- Digunakan Untuk -->
            <div class="col-span-2 md:col-span-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Digunakan Untuk <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $domisiliUsaha->purpose }}</textarea>
            </div>
        </x-slot>

        <x-slot name="scripts">
            <script src="{{ asset('js/edit-location-dropdowns.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Move citizenship field to Data Pribadi section
                    const citizenField = document.querySelector('#citizen_status').closest('div');
                    const dataPribadiSection = document.querySelector('.mt-8:first-of-type .border');
                    const gridContainer = dataPribadiSection.querySelector('.grid');
                    gridContainer.appendChild(citizenField);

                    // Initialize signing dropdown with ID as value
                    const signingDropdown = document.querySelector('#signing');
                    if (signingDropdown) {
                        // Clear existing options
                        signingDropdown.innerHTML = '<option value="">Pilih Pejabat</option>';

                        // Add options with ID as value and set selected
                        @foreach($signers as $signer)
                        signingDropdown.innerHTML += `<option value="{{ $signer->id }}" {{ $domisiliUsaha->signing == $signer->id ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>`;
                        @endforeach
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-edit>
</x-layout>
