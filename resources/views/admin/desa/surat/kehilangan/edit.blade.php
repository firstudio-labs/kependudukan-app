<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Kehilangan"
        route="{{ route('admin.desa.surat.kehilangan.update', $kehilangan->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$kehilangan"
        :signers="$signers">

        <x-slot name="additionalFields">
            <input type="hidden" name="is_accepted" value="1">
            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ $kehilangan->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ $kehilangan->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" value="{{ $kehilangan->letter_date }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Telah Kehilangan -->
            <div class="col-span-2">
                <label for="lost_items" class="block text-sm font-medium text-gray-700">Telah Kehilangan <span class="text-red-500">*</span></label>
                <textarea id="lost_items" name="lost_items" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>{{ $kehilangan->lost_items }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Contoh: Telah kehilangan 1 (satu) buah STNK kendaraan bermotor roda dua dengan identitas Nomor Polisi XYZ, Merk Honda, Type ABC, Nomor Rangka 123456, Nomor Mesin 7890, Warna Hitam, Tahun Pembuatan 2020, atas nama John Doe.</p>
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
                        signingDropdown.innerHTML += `<option value="{{ $signer->id }}" {{ $kehilangan->signing == $signer->id ? 'selected' : '' }}>{{ $signer->judul }} - {{ $signer->keterangan }}</option>`;
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
