<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Tambah Kesenian & Budaya</h1>
        <p class="text-sm text-gray-500 mb-6">Isi data dan tandai lokasi pada peta.</p>

        <form method="POST" action="{{ route('admin.desa.kesenian-budaya.store') }}" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Jenis</option>
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ old('jenis')===$j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jenis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama Kesenian/Budaya"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}" placeholder="Alamat (otomatis dari peta, bisa dilengkapi)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('alamat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" name="kontak" value="{{ old('kontak') }}" placeholder="Nomor/WA/Email (opsional)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('kontak')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <x-map-input 
                        label="Lokasi Kesenian Budaya" 
                        addressId="alamat" 
                        addressName="alamat" 
                        address="{{ old('alamat') }}" 
                        latitudeId="tag_lat" 
                        latitudeName="tag_lat" 
                        latitude="{{ old('tag_lat') }}" 
                        longitudeId="tag_lng" 
                        longitudeName="tag_lng" 
                        longitude="{{ old('tag_lng') }}" 
                        modalId="" 
                    />
                    @error('alamat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('tag_lat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('tag_lng')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>

</x-layout>


