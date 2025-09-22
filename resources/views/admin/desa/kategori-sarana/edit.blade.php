<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Kategori Sarana</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui jenis sarana dan kategori.</p>

        <form method="POST" action="{{ route('admin.desa.kategori-sarana.update', $item) }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis Sarana <span class="text-red-500">*</span></label>
                    <select name="jenis_sarana" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ old('jenis_sarana', $item->jenis_sarana)===$j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jenis_sarana')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="kategori" value="{{ old('kategori', $item->kategori) }}" required placeholder="Misal: Sekolah, Masjid, Posyandu, ..."
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('kategori')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>


