<x-guest.surat-layout title="Surat Izin Keramaian">
    <div data-aos="fade-up">
        <x-guest.form-layout
            title="Surat Izin Keramaian"
            route="{{ route('guest.surat.keramaian.store') }}"
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

                {{-- <!-- Event Information Section -->
                <div class="col-span-full">
                    <h3 class="text-md font-semibold text-gray-700 mt-4 mb-2">Informasi Keramaian</h3>
                </div>

                <!-- Hari -->
                <div>
                    <label for="day" class="block text-sm font-medium text-gray-700">Hari <span class="text-red-500">*</span></label>
                    <select id="day" name="day" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                        <option value="">Pilih Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>

                <!-- Waktu -->
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Waktu <span class="text-red-500">*</span></label>
                    <input type="time" id="time" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tanggal Acara -->
                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Acara <span class="text-red-500">*</span></label>
                    <input type="date" id="event_date" name="event_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Tempat Kegiatan -->
                <div>
                    <label for="place" class="block text-sm font-medium text-gray-700">Tempat Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" id="place" name="place" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" required>
                </div>

                <!-- Hiburan -->
                <div>
                    <label for="entertainment" class="block text-sm font-medium text-gray-700">Hiburan <span class="text-red-500">*</span></label>
                    <input type="text" id="entertainment" name="entertainment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Jenis hiburan yang diadakan" required>
                </div>

                <!-- Acara -->
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700">Acara <span class="text-red-500">*</span></label>
                    <input type="text" id="event" name="event" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Nama/jenis acara" required>
                </div>

                <!-- Undangan -->
                <div>
                    <label for="invitation" class="block text-sm font-medium text-gray-700">Undangan <span class="text-red-500">*</span></label>
                    <input type="text" id="invitation" name="invitation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2" placeholder="Jumlah undangan" required>
                </div> --}}
            @endslot
        </x-guest.form-layout>
    </div>
</x-guest.surat-layout>
