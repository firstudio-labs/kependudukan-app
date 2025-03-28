<x-layout>
    <div class="p-4 mt-14">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Klasifikasi</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('superadmin.datamaster.klasifikasi.update', $klasifikasi->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 mb-6">
                    <div>
                        <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode <span class="text-red-600">*</span></label>
                        <input type="number" name="kode" id="kode" value="{{ old('kode', $klasifikasi->kode) }}" 
                               class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('kode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Masukkan kode numerik unik untuk klasifikasi</p>
                    </div>

                    <div>
                        <label for="jenis_klasifikasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Klasifikasi <span class="text-red-600">*</span></label>
                        <input type="text" name="jenis_klasifikasi" id="jenis_klasifikasi" 
                               value="{{ old('jenis_klasifikasi', $klasifikasi->jenis_klasifikasi) }}"
                               class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('jenis_klasifikasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="4"
                                  class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan', $klasifikasi->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('superadmin.datamaster.klasifikasi.index') }}'" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-[#7886C7] hover:bg-[#2D336B] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5C69A7]">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>