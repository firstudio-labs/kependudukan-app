<x-guest.surat-layout title="Surat Keterangan Domisili Usaha">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Keterangan Domisili Usaha"
            route="{{ route('guest.surat.domisili-usaha.store') }}"
            :jobs="$jobs"
            :provinces="$provinces"
            section_title="Data Pemohon"
            :queueNumber="$queueNumber"
            :villageName="$villageName ?? null">

            @slot('additionalFields')
                <!-- Kewarganegaraan -->
                <div>
                    <label for="citizen_status" class="block text-sm font-medium text-gray-700">Kewarganegaraan <span class="text-red-500">*</span></label>
                    <select id="citizen_status" name="citizen_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="1">WNA</option>
                        <option value="2">WNI</option>
                    </select>
                </div>

                <!-- Tanggal Surat -->
                {{-- <div>
                    <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" id="letter_date" name="letter_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Jenis Usaha -->
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700">Jenis Usaha <span class="text-red-500">*</span></label>
                    <input type="text" id="business_type" name="business_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Alamat Usaha -->
                <div>
                    <label for="business_address" class="block text-sm font-medium text-gray-700">Alamat Usaha <span class="text-red-500">*</span></label>
                    <textarea id="business_address" name="business_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div>

                <!-- Tahun Berdiri -->
                <div>
                    <label for="business_year" class="block text-sm font-medium text-gray-700">Tahun Berdiri <span class="text-red-500">*</span></label>
                    <input type="text" id="business_year" name="business_year" maxlength="4" pattern="\d{4}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                    <p class="text-xs text-gray-500 mt-1">Format: YYYY (contoh: 2020)</p>
                </div>

                <!-- Digunakan Untuk -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Digunakan Untuk <span class="text-red-500">*</span></label>
                    <textarea id="purpose" name="purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div> --}}
            @endslot

            @slot('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Auto-fill business address when citizen is selected
                        const citizenSelect = document.querySelector('#nikSelect');
                        if (citizenSelect) {
                            // Update when selection changes
                            citizenSelect.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                if (selectedOption && selectedOption.dataset.address) {
                                    // You could optionally pre-fill business address with residential address
                                    // const businessAddressField = document.querySelector('#business_address');
                                    // businessAddressField.value = selectedOption.dataset.address;
                                }
                            });
                        }
                    });
                </script>
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
