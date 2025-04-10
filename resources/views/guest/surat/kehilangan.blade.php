<x-guest.surat-layout title="Surat Kehilangan">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Kehilangan"
            route="{{ route('guest.surat.kehilangan.store') }}"
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
                    <input type="date" id="letter_date" name="letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Telah Kehilangan - Spans 2 columns -->
                <div class="col-span-full">
                    <label for="lost_items" class="block text-sm font-medium text-gray-700">Telah Kehilangan <span class="text-red-500">*</span></label>
                    <textarea id="lost_items" name="lost_items" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Contoh: Telah kehilangan 1 (satu) buah STNK kendaraan bermotor roda dua dengan identitas Nomor Polisi XYZ, Merk Honda, Type ABC, Nomor Rangka 123456, Nomor Mesin 7890, Warna Hitam, Tahun Pembuatan 2020, atas nama John Doe.</p>
                </div>
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
