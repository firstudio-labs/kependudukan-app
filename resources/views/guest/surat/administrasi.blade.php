<x-guest.surat-layout title="Surat Administrasi Umum">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Administrasi Umum"
            route="{{ route('guest.surat.administrasi.store') }}"
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

                {{-- <!-- Tanggal Surat -->
                <div>
                    <label for="letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" id="letter_date" name="letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Isi Pernyataan - Spans 2 columns -->
                <div class="col-span-full">
                    <label for="statement_content" class="block text-sm font-medium text-gray-700">Isi Pernyataan <span class="text-red-500">*</span></label>
                    <textarea id="statement_content" name="statement_content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div>

                <!-- Tujuan - Spans 2 columns -->
                <div class="col-span-full">
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Tujuan <span class="text-red-500">*</span></label>
                    <textarea id="purpose" name="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                </div> --}}
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
