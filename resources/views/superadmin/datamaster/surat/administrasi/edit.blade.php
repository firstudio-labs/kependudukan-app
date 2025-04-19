<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Administrasi"
        route="{{ route('superadmin.surat.administrasi.update', $administration->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$administration"
        :signers="$signers">

        <x-slot name="additionalFields">
            <input type="hidden" name="is_accepted" value="1">
            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ $administration->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ $administration->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" value="{{ $administration->letter_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Isi Pernyataan -->
            <div class="col-span-2">
                <label for="statement_content" class="block text-sm font-medium text-gray-700">Isi Pernyataan <span class="text-red-500">*</span></label>
                <textarea id="statement_content" name="statement_content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $administration->statement_content }}</textarea>
            </div>

            <!-- Tujuan -->
            <div class="col-span-2">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Tujuan <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $administration->purpose }}</textarea>
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
                        signingDropdown.innerHTML += `<option value="{{ $signer->id }}" {{ $administration->signing == $signer->id ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>`;
                        @endforeach
                    }

                    // Change button text from "Perbarui" to "Accept"
                    const submitButton = document.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.textContent = 'Accept';
                    }
                });
            </script>
        </x-slot>
    </x-surat.form-layout-edit>
</x-layout>
