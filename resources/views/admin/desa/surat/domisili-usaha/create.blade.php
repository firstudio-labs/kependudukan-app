<x-layout>
    <x-surat.form-layout-create
        title="Buat Surat Keterangan Domisili Usaha"
        route="{{ route('admin.desa.surat.domisili-usaha.store') }}"
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
                <input type="date" id="letter_date" name="letter_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Jenis Usaha -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_type" class="block text-sm font-medium text-gray-700">Jenis Usaha <span class="text-red-500">*</span></label>
                <input type="text" id="business_type" name="business_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Alamat Usaha -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_address" class="block text-sm font-medium text-gray-700">Alamat Usaha <span class="text-red-500">*</span></label>
                <textarea id="business_address" name="business_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>

            <!-- Tahun Berdiri -->
            <div class="col-span-2 md:col-span-1">
                <label for="business_year" class="block text-sm font-medium text-gray-700">Tahun Berdiri <span class="text-red-500">*</span></label>
                <input type="text" id="business_year" name="business_year" maxlength="4" pattern="\d{4}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                <p class="text-xs text-gray-500 mt-1">Format: YYYY (contoh: 2020)</p>
            </div>

            <!-- Digunakan Untuk -->
            <div class="col-span-2 md:col-span-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Digunakan Untuk <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div>
        </x-slot>

        <x-slot name="scripts">
            <script src="{{ asset('js/location-dropdowns.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize citizen data select fields
                    initializeCitizenSelect('{{ route("citizens.administrasi") }}', null, {
                        filterByVillage: true,
                        useTextInput: true,
                        isAdminDesa: true
                    });
                    // Setup location dropdown events - Pass the provinces data
                    setupLocationDropdowns({!! json_encode($provinces) !!});

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
                    const rfIdInput = document.getElementById('rf_id_tag');
                    if (rfIdInput) {
                        rfIdInput.title = "Masukkan RF ID Tag untuk mengisi data otomatis";
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-create>
</x-layout>
