<!-- filepath: c:\laragon\www\kependudukan-app\resources\views\admin.desa\datamaster\surat\penandatangan\create.blade.php -->
<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Data Penandatangan</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form method="POST" action="{{ route('admin.desa.datamaster.surat.penandatangan.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        value="{{ old('judul') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('judul')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <input
                        type="text"
                        id="keterangan"
                        name="keterangan"
                        value="{{ old('keterangan') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.desa.datamaster.surat.penandatangan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
