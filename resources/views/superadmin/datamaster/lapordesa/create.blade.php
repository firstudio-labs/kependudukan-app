<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Data LaporDes</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form method="POST" action="{{ route('superadmin.datamaster.lapordesa.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ruang Lingkup -->
                    <div>
                        <label for="ruang_lingkup" class="block text-sm font-medium text-gray-700">Ruang Lingkup <span
                                class="text-red-500">*</span></label>
                        <select id="ruang_lingkup" name="ruang_lingkup"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                            <option value="" disabled selected>Pilih Ruang Lingkup</option>
                            <option value="Pemdes" {{ old('ruang_lingkup') == 'Pemdes' ? 'selected' : '' }}>Pemdes
                            </option>
                            <option value="BPD" {{ old('ruang_lingkup') == 'BPD' ? 'selected' : '' }}>BPD</option>
                        </select>
                        @error('ruang_lingkup')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bidang -->
                    <div>
                        <label for="bidang" class="block text-sm font-medium text-gray-700">Bidang <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="bidang" name="bidang" value="{{ old('bidang') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                            required>
                        @error('bidang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>

                <div class="flex justify-end mt-6 space-x-2">
                    <a href="{{ route('superadmin.datamaster.lapordesa.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] focus:outline-none focus:bg-[#2D336B]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>