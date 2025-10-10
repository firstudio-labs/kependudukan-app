<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Sarana Umum</h1>
        <p class="text-sm text-gray-500 mb-6">Perbarui data sarana umum.</p>

        <form method="POST" action="{{ route('admin.desa.sarana-umum.update', $item) }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis Sarana <span class="text-red-500">*</span></label>
                    <select id="jenis-sarana" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Jenis</option>
                        @foreach($allowedJenis as $j)
                            <option value="{{ $j }}" {{ $item->kategori?->jenis_sarana === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Kategori Sarana <span class="text-red-500">*</span></label>
                    <select name="kategori_sarana_id" id="kategori-sarana" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Kategori</option>
                    </select>
                    @error('kategori_sarana_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nama Sarana <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sarana" value="{{ old('nama_sarana', $item->nama_sarana) }}" required placeholder="Nama Sarana"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama_sarana')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
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
                        label="Lokasi Sarana Umum" 
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
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="window.history.back()" class="bg-white text-gray-700 px-4 py-2 rounded-md shadow-md border border-gray-300 hover:bg-gray-50">Batal</button>
                <button type="submit" class="bg-[#7886C7] text-white px-4 py-2 rounded-md shadow-md hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        async function loadKategoriByJenis(jenis, selectedId = null) {
            const kategoriSelect = document.getElementById('kategori-sarana');
            kategoriSelect.innerHTML = '<option value="">Memuat...</option>';
            if (!jenis) { 
                kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>'; 
                return; 
            }
            try {
                const res = await fetch(`{{ route('admin.desa.kategori-sarana.by-jenis') }}?jenis_sarana=${encodeURIComponent(jenis)}`);
                const data = await res.json();
                kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>' + data.map(function(item){
                    const sel = (String(selectedId) === String(item.id)) ? 'selected' : '';
                    return `<option value="${item.id}" ${sel}>${item.kategori}</option>`;
                }).join('');
            } catch (e) {
                kategoriSelect.innerHTML = '<option value="">Gagal memuat</option>';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function(){
            const jenisSelect = document.getElementById('jenis-sarana');
            const currentJenis = jenisSelect.value;
            const currentKategoriId = '{{ $item->kategori_sarana_id }}';
            loadKategoriByJenis(currentJenis, currentKategoriId);
            jenisSelect.addEventListener('change', function(){ 
                loadKategoriByJenis(jenisSelect.value); 
            });
            const input = document.querySelector('input[name="foto"]');
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


