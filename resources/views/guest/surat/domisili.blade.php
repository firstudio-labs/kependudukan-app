<x-guest.surat-layout title="Surat Keterangan Domisili">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Keterangan Domisili"
            route="{{ route('guest.surat.domisili.store') }}"
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
                <div>
                    <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" id="letter_date" name="letter_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Berdomisili Di -->
                <div>
                    <label for="domicile_address" class="block text-sm font-medium text-gray-700">Berdomisili Di <span class="text-red-500">*</span></label>
                    <textarea id="domicile_address" name="domicile_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div>

                <!-- Digunakan Untuk -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Digunakan Untuk <span class="text-red-500">*</span></label>
                    <textarea id="purpose" name="purpose" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div>
            @endslot

            @slot('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Auto-fill domicile address when citizen is selected
                        const citizenSelect = document.querySelector('#nikSelect');
                        if (citizenSelect) {
                            // Update when selection changes
                            citizenSelect.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                if (selectedOption && selectedOption.dataset.address) {
                                    const domicileField = document.querySelector('#domicile_address');
                                    domicileField.value = selectedOption.dataset.address;
                                }
                            });
                        }
                    });
                </script>
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
