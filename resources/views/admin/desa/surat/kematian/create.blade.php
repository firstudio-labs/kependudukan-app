<x-layout>
    <x-surat.form-layout-create
        title="Buat Surat Keterangan Kematian"
        route="{{ route('admin.desa.surat.kematian.store') }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :signers="$signers"
        section_title="Data Almarhum/Almarhumah">

        <x-slot name="additionalFields">
            <!-- Kewarganegaraan - Added to Data Almarhum/Almarhumah section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1">WNA</option>
                    <option value="2">WNI</option>
                </select>
            </div>

            <!-- Informasi Kematian fields moved to Informasi Surat section -->
            <!-- Dasar Keterangan -->
            <div class="col-span-2 md:col-span-1">
                <label for="info" class="block text-sm font-medium text-gray-700">Dasar Keterangan <span class="text-red-500">*</span></label>
                <input type="text" id="info" name="info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tanggal Surat RT -->
            <div class="col-span-2 md:col-span-1">
                <label for="rt_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat RT</label>
                <input type="date" id="rt_letter_date" name="rt_letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
            </div>

            <!-- Penyebab Kematian -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_cause" class="block text-sm font-medium text-gray-700">Penyebab Kematian <span class="text-red-500">*</span></label>
                <input type="text" id="death_cause" name="death_cause" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tempat Kematian -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Kematian <span class="text-red-500">*</span></label>
                <input type="text" id="death_place" name="death_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tanggal Meninggal -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                <input type="date" id="death_date" name="death_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Nama Pelapor -->
            <div class="col-span-2 md:col-span-1">
                <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor <span class="text-red-500">*</span></label>
                <input type="text" id="reporter_name" name="reporter_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Hubungan Pelapor -->
            <div class="col-span-2 md:col-span-1">
                <label for="reporter_relation" class="block text-sm font-medium text-gray-700">Hubungan Pelapor <span class="text-red-500">*</span></label>
                <select id="reporter_relation" name="reporter_relation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Hubungan</option>
                    <option value="1">Suami</option>
                    <option value="2">Istri</option>
                    <option value="3">Anak</option>
                    <option value="4">Ayah</option>
                    <option value="5">Ibu</option>
                    <option value="6">Saudara</option>
                    <option value="7">Lainnya</option>
                </select>
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

                    // Move citizenship field to Data Almarhum/Almarhumah section
                    const citizenField = document.querySelector('#citizen_status').closest('div');
                    const dataPribadiSection = document.querySelector('.mt-8:first-of-type .border');
                    const gridContainer = dataPribadiSection.querySelector('.grid');
                    gridContainer.appendChild(citizenField);

                    // Initialize signing dropdown
                    if (typeof initializeSigningDropdown === 'function') {
                        initializeSigningDropdown();
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-create>
</x-layout>
