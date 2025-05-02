<x-guest.surat-layout>
    <x-guest.form-layout
        title="Permohonan Surat Keterangan Catatan Kepolisian (SKCK)"
        :route="route('guest.surat.skck.store')"
        :jobs="$jobs"
        :provinces="$provinces"
        :queueNumber="$queueNumber"
        :villageName="session('village_name') ?? null"
        section_title="Data Pemohon SKCK">

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

            {{-- <!-- Tanggal Surat -->
            <div class="col-span-2 md:col-span-1">
                <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                <input type="date" id="letter_date" name="letter_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
            </div>

            <!-- Dipergunakan Untuk -->
            <div class="col-span-2">
                <label for="purpose" class="block text-sm font-medium text-gray-700">Dipergunakan Untuk <span class="text-red-500">*</span></label>
                <textarea id="purpose" name="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
            </div> --}}
        </x-slot>

        <x-slot name="scripts">
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Set default date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('letter_date').value = today;

                    // Pre-select WNI for citizen_status
                    document.getElementById('citizen_status').value = "2";
                });
            </script>
        </x-slot>
    </x-guest.form-layout>
</x-guest.surat-layout>
