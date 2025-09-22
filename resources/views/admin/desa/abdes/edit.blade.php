<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit APBDES</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data anggaran.</p>

        <form method="POST" action="{{ route('admin.desa.abdes.update', $item) }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ old('jenis', $item->jenis)===$j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jenis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        @foreach($allowedKategori as $k)
                            <option value="{{ $k }}" {{ old('kategori', $item->kategori)===$k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                    @error('kategori')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jumlah Anggaran <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                        <input type="text" inputmode="decimal" name="jumlah_anggaran" value="{{ old('jumlah_anggaran', $item->jumlah_anggaran) }}" required placeholder="0,00"
                               class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 pl-9 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan angka saja, otomatis diformat saat disimpan.</p>
                    @error('jumlah_anggaran')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>


