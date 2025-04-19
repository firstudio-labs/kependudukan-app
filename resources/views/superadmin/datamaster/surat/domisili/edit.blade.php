<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Keterangan Domisili"
        route="{{ route('superadmin.surat.domisili.update', $domisili->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$domisili"
        :signers="$signers">

        <x-slot name="additionalFields">
            <input type="hidden" name="is_accepted" value="1">
            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ $domisili->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ $domisili->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" value="{{ $domisili->letter_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Berdomisili Di -->
            <div class="col-span-2 md:col-span-1">
                <label for="domicile_address" class="block text-sm font-medium text-gray-700">Berdomisili Di <span class="text-red-500">*</span></label>
                <textarea id="domicile_address" name="domicile_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $domisili->domicile_address }}</textarea>
            </div>

            <!-- Digunakan Untuk -->
            <div class="col-span-2 md:col-span-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Digunakan Untuk <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $domisili->purpose }}</textarea>
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
                        signingDropdown.innerHTML += `<option value="{{ $signer->id }}" {{ $domisili->signing == $signer->id ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>`;
                        @endforeach
                    }

                    // Change button text from "Perbarui" to "Accept"
                    const submitButton = document.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.textContent = 'Accept';
                    }

                    // Auto-fill domicile address when citizen is selected
                    const citizenSelect = document.querySelector('#nik');
                    if (citizenSelect) {
                        // Set initial value for domicile address if citizen is already selected
                        const domicileField = document.querySelector('#domicile_address');
                        const selectedOption = citizenSelect.options[citizenSelect.selectedIndex];
                        if (selectedOption && selectedOption.dataset.address && !domicileField.value) {
                            domicileField.value = selectedOption.dataset.address;
                        }

                        // Update when selection changes
                        citizenSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            if (selectedOption && selectedOption.dataset.address) {
                                domicileField.value = selectedOption.dataset.address;
                            }
                        });
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-edit>
</x-layout>
