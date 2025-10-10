<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Tambah Usaha Desa</h1>
        <p class="text-sm text-gray-500 mb-6">Lengkapi data usaha desa.</p>

        <form method="POST" action="{{ route('admin.desa.usaha.store') }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                        <option value="">Pilih Jenis</option>
                        <option value="BUMDES">BUMDES</option>
                        <option value="Koperasi">Koperasi</option>
                        <option value="Mandiri/Perseorangan">Mandiri/Perseorangan</option>
                        <option value="KUB (Kelompok Usaha Bersama)">KUB (Kelompok Usaha Bersama)</option>
                        <option value="Korporasi/Perusahaan">Korporasi/Perusahaan</option>
                    </select>
                    @error('jenis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama usaha"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('nama')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Ijin</label>
                    <input type="text" name="ijin" value="{{ old('ijin') }}" placeholder="Nomor/jenis izin"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('ijin')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700">Tahun Didirikan</label>
                    <div class="mt-1 relative">
                        <select name="tahun_didirikan"
                                class="appearance-none block w-full rounded-lg border border-gray-300 bg-white p-2.5 pr-10 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]">
                            <option value="">Pilih Tahun</option>
                            @for($y = date('Y'); $y >= 1950; $y--)
                                <option value="{{ $y }}" {{ old('tahun_didirikan') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-calendar"></i>
                        </span>
                    </div>
                    @error('tahun_didirikan')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Ketua</label>
                    <input type="text" name="ketua" value="{{ old('ketua') }}" placeholder="Nama ketua/pengelola"
                           class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 focus:border-[#7886C7] focus:ring-2 focus:ring-[#7886C7]" />
                    @error('ketua')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Foto (opsional)</label>
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
        document.addEventListener('DOMContentLoaded', function(){
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


