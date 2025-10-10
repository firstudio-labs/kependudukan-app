<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Kesenian & Budaya</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data dan lokasi.</p>

        <form method="POST" action="{{ route('admin.desa.kesenian-budaya.update', $item) }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $item->nama) }}" required placeholder="Nama Kesenian/Budaya"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    @php
                        $lat = '';
                        $lng = '';
                        if (!empty($item->tag_lokasi)) {
                            $coordinates = explode(',', $item->tag_lokasi);
                            if (count($coordinates) >= 2) {
                                $lat = trim($coordinates[0]);
                                $lng = trim($coordinates[1]);
                            }
                        }
                    @endphp
                    <x-map-input 
                        label="Lokasi Kesenian Budaya" 
                        addressId="alamat" 
                        addressName="alamat" 
                        address="{{ old('alamat', $item->alamat) }}" 
                        latitudeId="tag_lat" 
                        latitudeName="tag_lat" 
                        latitude="{{ old('tag_lat', $lat) }}" 
                        longitudeId="tag_lng" 
                        longitudeName="tag_lng" 
                        longitude="{{ old('tag_lng', $lng) }}" 
                        modalId="" 
                    />
                    @error('alamat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('tag_lat')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('tag_lng')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Kontak</label>
                    <input type="text" name="kontak" value="{{ old('kontak', $item->kontak) }}" placeholder="Nomor/WA/Email (opsional)"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('kontak')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Foto (opsional)</label>
                @if($item->foto)
                    <div class="mb-2"><img src="{{ asset('storage/'.$item->foto) }}" alt="Foto" class="h-24 rounded"></div>
                @endif
                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                @error('foto')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <img id="foto-preview" alt="Preview" class="h-24 rounded mt-2 hidden">
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.querySelector('input[name=\"foto\"]');
            const preview = document.getElementById('foto-preview');
            if (input && preview) {
                input.addEventListener('change', function(){
                    const file = input.files && input.files[0];
                    if (file) {
                        preview.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</x-layout>


