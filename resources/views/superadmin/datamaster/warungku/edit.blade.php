<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Warungku</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data klasifikasi dan jenis.</p>

        <form method="POST" action="{{ route('superadmin.datamaster.warungku.update', $item) }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Klasifikasi <span class="text-red-500">*</span></label>
                    <select name="klasifikasi" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-gray-900 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="barang" {{ old('klasifikasi', $item->klasifikasi) === 'barang' ? 'selected' : '' }}>Barang</option>
                        <option value="jasa" {{ old('klasifikasi', $item->klasifikasi) === 'jasa' ? 'selected' : '' }}>Jasa</option>
                    </select>
                    @error('klasifikasi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                    <input type="text" name="jenis" value="{{ old('jenis', $item->jenis) }}" required
                           placeholder="Contoh: Sembako, Percetakan, Servis Elektronik, dll"
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-gray-900 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('jenis')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>


