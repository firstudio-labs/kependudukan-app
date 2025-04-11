<x-guest.surat-layout title="Surat Keterangan Kematian">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Keterangan Kematian"
            route="{{ route('guest.surat.kematian.store') }}"
            :jobs="$jobs"
            :provinces="$provinces"
            section_title="Data Almarhum/Almarhumah"
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

                <!-- Dasar Keterangan -->
                <div>
                    <label for="info" class="block text-sm font-medium text-gray-700">Dasar Keterangan <span class="text-red-500">*</span></label>
                    <input type="text" id="info" name="info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Surat RT -->
                <div>
                    <label for="rt_letter_date" class="block text-sm font-medium text-gray-700">Tanggal Surat RT</label>
                    <input type="date" id="rt_letter_date" name="rt_letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">
                </div>

                <!-- Penyebab Kematian -->
                <div>
                    <label for="death_cause" class="block text-sm font-medium text-gray-700">Penyebab Kematian <span class="text-red-500">*</span></label>
                    <input type="text" id="death_cause" name="death_cause" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Kematian -->
                <div>
                    <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Kematian <span class="text-red-500">*</span></label>
                    <input type="text" id="death_place" name="death_place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Meninggal -->
                <div>
                    <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Meninggal <span class="text-red-500">*</span></label>
                    <input type="date" id="death_date" name="death_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Nama Pelapor -->
                <div>
                    <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor <span class="text-red-500">*</span></label>
                    <input type="text" id="reporter_name" name="reporter_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Hubungan Pelapor -->
                <div>
                    <label for="reporter_relation" class="block text-sm font-medium text-gray-700">Hubungan Pelapor <span class="text-red-500">*</span></label>
                    <select id="reporter_relation" name="reporter_relation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Hubungan</option>
                        <option value="1">Suami</option>
                        <option value="2">Istri</option>
                        <option value="3">Anak</option>
                        <option value="4">Ayah</option>
                        <option value="5">Ibu</option>
                        <option value="6">Saudara</option>
                        <option value="7">Lainnya</option>
                    </select>
                </div>
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
