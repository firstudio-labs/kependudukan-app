<x-layout>
    <x-surat.form-layout-edit
        title="Edit Surat Izin Keramaian"
        route="{{ route('admin.desa.surat.keramaian.update', $keramaian->id) }}"
        :jobs="$jobs"
        :provinces="$provinces"
        :data="$keramaian"
        :signers="$signers">

        <x-slot name="additionalFields">
            <input type="hidden" name="is_accepted" value="1">

            <!-- Kewarganegaraan - Added to Data Pribadi section -->
            <div class="col-span-2 md:col-span-1">
                <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <option value="">Pilih Kewarganegaraan</option>
                    <option value="1" {{ $keramaian->citizen_status == '1' ? 'selected' : '' }}>WNA</option>
                    <option value="2" {{ $keramaian->citizen_status == '2' ? 'selected' : '' }}>WNI</option>
                </select>
            </div>

            <!-- Event Information Section -->
            <div class="col-span-2">
                <h3 class="text-md font-semibold text-gray-700 mt-4 mb-2">Informasi Keramaian</h3>
                <div class="border p-4 rounded-md mb-2 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Hari -->
                        <div>
                            <label for="day" class="block text-sm font-medium text-gray-700">Hari <span class="text-red-500">*</span></label>
                            <select id="day" name="day" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin" {{ $keramaian->day == 'Senin' ? 'selected' : '' }}>Senin</option>
                                <option value="Selasa" {{ $keramaian->day == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="Rabu" {{ $keramaian->day == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="Kamis" {{ $keramaian->day == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="Jumat" {{ $keramaian->day == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="Sabtu" {{ $keramaian->day == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                <option value="Minggu" {{ $keramaian->day == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>

                        <!-- Waktu -->
                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700">Waktu <span class="text-red-500">*</span></label>
                            <input type="time" id="time" name="time" value="{{ \Carbon\Carbon::parse($keramaian->time)->format('H:i') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tanggal Acara -->
                        <div>
                            <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Acara <span class="text-red-500">*</span></label>
                            <input type="date" id="event_date" name="event_date" value="{{ $keramaian->event_date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Tempat Kegiatan -->
                        <div>
                            <label for="place" class="block text-sm font-medium text-gray-700">Tempat Kegiatan <span class="text-red-500">*</span></label>
                            <input type="text" id="place" name="place" value="{{ $keramaian->place }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        </div>

                        <!-- Hiburan -->
                        <div>
                            <label for="entertainment" class="block text-sm font-medium text-gray-700">Hiburan <span class="text-red-500">*</span></label>
                            <input type="text" id="entertainment" name="entertainment" value="{{ $keramaian->entertainment }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Jenis hiburan yang diadakan" required>
                        </div>

                        <!-- Acara -->
                        <div>
                            <label for="event" class="block text-sm font-medium text-gray-700">Acara <span class="text-red-500">*</span></label>
                            <input type="text" id="event" name="event" value="{{ $keramaian->event }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Nama/jenis acara" required>
                        </div>

                        <!-- Undangan -->
                        <div>
                            <label for="invitation" class="block text-sm font-medium text-gray-700">Undangan <span class="text-red-500">*</span></label>
                            <input type="text" id="invitation" name="invitation" value="{{ $keramaian->invitation }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Jumlah undangan" required>
                        </div>
                    </div>
                </div>
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

                    // Initialize signing dropdown
                    if (typeof initializeSigningDropdown === 'function') {
                        initializeSigningDropdown();
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
