<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Pengumuman</h1>

        <form method="POST" action="{{ route('admin.desa.pengumuman.update', $pengumuman->id) }}" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700">Judul Pengumuman <span class="text-red-500">*</span></label>
                    <input type="text" id="judul" name="judul"
                        class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3"
                        value="{{ old('judul', $pengumuman->judul) }}" required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar (opsional)</label>
                    <div class="mt-1">
                        <label for="gambar" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded cursor-pointer border border-gray-300">
                            <i class="fa-solid fa-upload mr-2"></i> Pilih Gambar
                        </label>
                        <input type="file" id="gambar" name="gambar" class="hidden" accept="image/png,image/jpeg,image/jpg" onchange="previewImagePengumumanEdit(this)" aria-describedby="gambar_help_pengumuman_edit gambar_filename_pengumuman_edit">
                        <span id="gambar_filename_pengumuman_edit" class="ml-3 text-sm text-gray-600 align-middle">{{ $pengumuman->gambar ? basename($pengumuman->gambar) : 'Belum ada file dipilih' }}</span>
                    </div>
                    <p id="gambar_help_pengumuman_edit" class="mt-2 text-sm text-gray-500">PNG, JPG (MAX. 4MB).</p>
                    <div id="preview" class="mt-2">
                        @if ($pengumuman->gambar)
                            <img id="preview_image_pengumuman_edit" src="{{ asset('storage/' . $pengumuman->gambar) }}" alt="Preview" class="max-w-xs rounded-lg shadow-md">
                        @else
                            <img id="preview_image_pengumuman_edit" src="#" alt="Preview" class="hidden max-w-xs rounded-lg shadow-md">
                        @endif
                    </div>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="8"
                        class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">{{ old('deskripsi', $pengumuman->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('admin.desa.pengumuman.index') }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B]">Simpan</button>
            </div>
        </form>
    </div>

    <style>
        .ck-editor__editable[role="textbox"] { min-height: 320px; }
        .ck-editor__editable { width: 100%; box-sizing: border-box; }
    </style>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        function previewImagePengumumanEdit(input) {
            const img = document.getElementById('preview_image_pengumuman_edit');
            const name = document.getElementById('gambar_filename_pengumuman_edit');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
                name.textContent = input.files[0].name;
            } else {
                img.src = '#';
                img.classList.add('hidden');
                name.textContent = 'Belum ada file dipilih';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('deskripsi')) {
                ClassicEditor.create(document.querySelector('#deskripsi')).then(e=>{e.ui.view.editable.element.style.minHeight='320px'}).catch(()=>{});
            }
            
        });
    </script>
</x-layout>


