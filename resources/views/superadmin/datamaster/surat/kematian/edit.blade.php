<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Keterangan Kematian"
        route="{{ route('superadmin.surat.kematian.update', $kematian->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$kematian"
        :signers="$signers"
        section_title="Data Almarhum/Almarhumah">

        <x-slot name="additionalFields">
            <!-- Kewarganegaraan - Added to Data Almarhum/Almarhumah section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ (is_array($kematian->citizen_status) ? ($kematian->citizen_status[0] ?? '') : $kematian->citizen_status) == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ (is_array($kematian->citizen_status) ? ($kematian->citizen_status[0] ?? '') : $kematian->citizen_status) == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Informasi Kematian fields moved to Informasi Surat section -->
            <!-- Dasar Keterangan -->
            <div class="col-span-2 md:col-span-1">
                <label for="info" class="block text-sm font-medium text-gray-700">Dasar Keterangan <span class="text-red-500">*</span></label>
                <input type="text" id="info" name="info" value="{{ $kematian->info }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tanggal Surat RT -->
            <div class="col-span-2 md:col-span-1">
                <label for="rt_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat RT</label>
                <input type="date" id="rt_letter_date" name="rt_letter_date" value="{{ $kematian->rt_letter_date ? date('Y-m-d', strtotime($kematian->rt_letter_date)) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
            </div>

            <!-- Penyebab Kematian -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_cause" class="block text-sm font-medium text-gray-700">Penyebab Kematian <span class="text-red-500">*</span></label>
                <input type="text" id="death_cause" name="death_cause" value="{{ $kematian->death_cause }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tempat Kematian -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Kematian <span class="text-red-500">*</span></label>
                <input type="text" id="death_place" name="death_place" value="{{ $kematian->death_place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Tanggal Meninggal -->
            <div class="col-span-2 md:col-span-1">
                <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                @php
                    // Ensure death_date is properly formatted for the date input
                    $deathDate = '';
                    if (isset($kematian->death_date)) {
                        if ($kematian->death_date instanceof \DateTime) {
                            $deathDate = $kematian->death_date->format('Y-m-d');
                        } else {
                            $deathDate = date('Y-m-d', strtotime($kematian->death_date));
                        }
                    }
                @endphp
                <input type="date" id="death_date" name="death_date" value="{{ $deathDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Nama Pelapor -->
            <div class="col-span-2 md:col-span-1">
                <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor <span class="text-red-500">*</span></label>
                <input type="text" id="reporter_name" name="reporter_name" value="{{ $kematian->reporter_name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Hubungan Pelapor -->
            <div class="col-span-2 md:col-span-1">
                <label for="reporter_relation" class="block text-sm font-medium text-gray-700">Hubungan Pelapor <span class="text-red-500">*</span></label>
                <select id="reporter_relation" name="reporter_relation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Hubungan</option>
                    <option value="Suami" {{ $kematian->reporter_relation == 'Suami' || $kematian->reporter_relation == '1' ? 'selected' : '' }}>Suami</option>
                    <option value="Istri" {{ $kematian->reporter_relation == 'Istri' || $kematian->reporter_relation == '2' ? 'selected' : '' }}>Istri</option>
                    <option value="Anak" {{ $kematian->reporter_relation == 'Anak' || $kematian->reporter_relation == '3' ? 'selected' : '' }}>Anak</option>
                    <option value="Ayah" {{ $kematian->reporter_relation == 'Ayah' || $kematian->reporter_relation == '4' ? 'selected' : '' }}>Ayah</option>
                    <option value="Ibu" {{ $kematian->reporter_relation == 'Ibu' || $kematian->reporter_relation == '5' ? 'selected' : '' }}>Ibu</option>
                    <option value="Saudara" {{ $kematian->reporter_relation == 'Saudara' || $kematian->reporter_relation == '6' ? 'selected' : '' }}>Saudara</option>
                    <option value="Lainnya" {{ $kematian->reporter_relation == 'Lainnya' || $kematian->reporter_relation == '7' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
        </x-slot>

        <x-slot name="scripts">
            <script src="{{ asset('js/edit-location-dropdowns.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
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
    </x-surat.form-layout-edit>
</x-layout>
