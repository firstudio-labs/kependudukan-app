<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Berita Desa</h1>

        <form method="POST" action="{{ route('admin.desa.berita-desa.update', $berita->id) }}"
            enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Judul Berita -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700">Judul Berita <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="judul" name="judul"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2"
                        value="{{ old('judul', $berita->judul) }}" required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar -->
                <div>
                    <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar</label>
                    <input type="file" id="gambar" name="gambar"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2 mt-1"
                        accept="image/png,image/jpeg,image/jpg" onchange="previewImage(this, 'preview')">
                    <p class="mt-1 text-sm text-gray-500">PNG, JPG (MAX. 4MB).</p>
                    <div id="preview" class="mt-2">
                        @if ($berita->gambar)
                            <img id="preview_image" src="{{ asset('storage/' . $berita->gambar) }}" alt="Preview"
                                class="max-w-xs rounded-lg shadow-md">
                        @else
                            <img id="preview_image" src="#" alt="Preview" class="hidden max-w-xs rounded-lg shadow-md">
                        @endif
                    </div>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="col-span-1 md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Berita <span
                            class="text-red-500">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ old('deskripsi', $berita->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Komentar -->
                <div class="col-span-1 md:col-span-2">
                    <label for="komentar" class="block text-sm font-medium text-gray-700">Komentar</label>
                    <textarea id="komentar" name="komentar" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-2">{{ old('komentar', $berita->komentar) }}</textarea>
                    @error('komentar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#7886C7] text-base font-medium text-white hover:bg-[#2D336B] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewImage = document.getElementById('preview_image');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                previewImage.src = '#';
                previewImage.classList.add('hidden');
            }
        }
    </script>
</x-layout>