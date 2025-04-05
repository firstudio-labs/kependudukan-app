<x-layout>
    <x-surat.form-layout-create
        title="Buat Surat Administrasi"
        route="{{ route('superadmin.surat.administrasi.store') }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :signers="$signers">

        <x-slot name="additionalFields">
            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1">WNA</option>
                    <option value="2">WNI</option>
                </select>
            </div>

            <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Isi Pernyataan -->
            <div class="col-span-2">
                <label for="statement_content" class="block text-sm font-medium text-gray-700">Isi Pernyataan <span class="text-red-500">*</span></label>
                <textarea id="statement_content" name="statement_content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            <!-- Tujuan -->
            <div class="col-span-2">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Tujuan <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>
        </x-slot>

        <x-slot name="scripts">
            <script src="{{ asset('js/location-dropdowns.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize citizen data select fields
                    initializeCitizenSelect('{{ route("citizens.administrasi") }}');

                    // Setup location dropdown events
                    setupLocationDropdowns();

                    // Setup form validation
                    setupFormValidation();

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

                        // Add options with ID as value
                        @foreach($signers as $signer)
                        signingDropdown.innerHTML += `<option value="{{ $signer->id }}">{{ $signer->judul }} - {{ $signer->keterangan }}</option>`;
                        @endforeach
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-create>
</x-layout>
