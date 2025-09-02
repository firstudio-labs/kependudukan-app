<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Berita Desa</h1>

        <form action="{{ route('user.berita-desa.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md space-y-8">
            @csrf
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Informasi Berita</h2>
                <p class="text-sm text-gray-500 mt-1">Isi judul dan unggah gambar pendukung (opsional).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Berita <span class="text-red-600">*</span></label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path d="M19.5 6h-15A1.5 1.5 0 003 7.5v9A1.5 1.5 0 004.5 18h15a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0019.5 6zm0 10.5h-15v-9h15v9zM6 9h6v1.5H6V9zm0 3h9v1.5H6V12z" />
                                </svg>
                            </span>
                            <input id="judul" type="text" name="judul" value="{{ old('judul') }}"
                                   class="block w-full pl-10 pr-3 py-3 text-base rounded-lg border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-400"
                                   placeholder="Tulis judul berita yang singkat dan jelas" maxlength="255" aria-describedby="judul_help judul_counter" />
                        </div>
                        <div class="flex items-center justify-between">
                            <p id="judul_help" class="text-xs text-gray-500 mt-1">Maksimal 255 karakter.</p>
                            <p id="judul_counter" class="text-xs text-gray-400 mt-1">0/255</p>
                        </div>
                        <p id="judul_error" class="text-red-600 text-sm mt-1 hidden"></p>
                        @error('judul')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar (opsional)</label>
                        <div class="mt-1">
                            <label for="gambar" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded cursor-pointer border border-gray-300">
                                <i class="fa-solid fa-upload mr-2"></i> Pilih Gambar
                            </label>
                            <input id="gambar" type="file" name="gambar" accept="image/*" class="hidden" aria-describedby="gambar_help gambar_filename" onchange="previewImage(this)" />
                            <span id="gambar_filename" class="ml-3 text-sm text-gray-600 align-middle">Belum ada file dipilih</span>
                        </div>
                        <p id="gambar_help" class="text-xs text-gray-500 mt-2">Format: JPG/PNG (maks. 4 MB). Gunakan gambar yang jelas dan relevan.</p>
                        <div class="mt-2">
                            <img id="preview_image" src="#" alt="Preview Gambar" class="hidden max-h-40 rounded border" />
                        </div>
                        @error('gambar')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">Konten Berita</h2>
                <p class="text-sm text-gray-500 mt-1">Tulis isi berita sejelas mungkin, gunakan heading, list, atau tautan bila perlu.</p>

                <div class="mt-4 ck-wrapper-deskripsi">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Berita <span class="text-red-600">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="12" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tulis isi berita secara informatif">{{ old('deskripsi') }}</textarea>
                    <p id="deskripsi_error" class="text-red-600 text-sm mt-1 hidden"></p>
                    @error('deskripsi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 ck-wrapper-komentar">
                    <label for="komentar" class="block text-sm font-medium text-gray-700">Komentar (opsional)</label>
                    <textarea id="komentar" name="komentar" rows="4" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tambahkan catatan atau keterangan jika diperlukan">{{ old('komentar') }}</textarea>
                    @error('komentar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">Kirim untuk Disetujui</button>
                <span class="text-xs text-gray-500">Setelah dikirim, berita menunggu persetujuan admin desa.</span>
                <a href="{{ route('user.berita-desa.index') }}" class="ml-auto text-gray-600 hover:text-gray-800">Batal</a>
            </div>
        </form>
    </div>
    <style>
        /* Pastikan hanya editor deskripsi yang tinggi */
        .ck-wrapper-deskripsi .ck-editor__editable[role="textbox"] { min-height: 380px; }
        /* Editor komentar dibuat kecil */
        .ck-wrapper-komentar .ck-editor__editable[role="textbox"] { min-height: 120px; }
        .ck-editor__editable { width: 100%; box-sizing: border-box; }
    </style>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Judul counter (update berdasarkan isi editor juga)
            const judulCounter = document.getElementById('judul_counter');
            const judulError = document.getElementById('judul_error');
            const deskripsiError = document.getElementById('deskripsi_error');
            let judulEditor, deskripsiEditor, komentarEditor;

            const judulInput = document.getElementById('judul');
            if (judulInput && judulCounter) {
                const updateJudulCount = () => { judulCounter.textContent = `${judulInput.value.length}/255`; };
                updateJudulCount();
                judulInput.addEventListener('input', updateJudulCount);
            }

            if (document.getElementById('deskripsi')) {
                ClassicEditor.create(document.querySelector('#deskripsi'), {
                    toolbar: {
                        items: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','|','undo','redo']
                    }
                }).then(editor => {
                    deskripsiEditor = editor;
                    // Perbesar area editor deskripsi
                    editor.ui.view.editable.element.style.minHeight = '380px';
                }).catch(() => {});
            }
            if (document.getElementById('komentar')) {
                ClassicEditor.create(document.querySelector('#komentar'), {
                    toolbar: ['bold','italic','link','bulletedList','numberedList','undo','redo']
                }).then(editor => { 
                    komentarEditor = editor; 
                    // Pastikan komentar lebih kecil
                    editor.ui.view.editable.element.style.minHeight = '120px';
                }).catch(() => {});
            }
        
            // Validasi submit agar tidak muncul error "invalid form control not focusable"
            const form = document.querySelector('form[action="{{ route('user.berita-desa.store') }}"]');
            if (form) {
                form.addEventListener('submit', function (e) {
                    // Reset error
                    if (judulError) judulError.classList.add('hidden');
                    if (deskripsiError) deskripsiError.classList.add('hidden');

                    // Sinkronkan nilai editor ke textarea (untuk berjaga bila server membaca textarea)
                    try {
                        // judul berupa input text, tidak perlu sinkronisasi editor
                        if (deskripsiEditor) document.getElementById('deskripsi').value = deskripsiEditor.getData();
                        if (komentarEditor) document.getElementById('komentar').value = komentarEditor.getData();
                    } catch (_) {}

                    // Ambil teks plain untuk validasi
                    const plainJudul = document.getElementById('judul').value.trim();
                    const plainDesk = (deskripsiEditor ? deskripsiEditor.getData() : document.getElementById('deskripsi').value).replace(/<[^>]*>/g, '').trim();

                    let hasError = false;
                    if (!plainJudul) {
                        if (judulError) {
                            judulError.textContent = 'Judul wajib diisi';
                            judulError.classList.remove('hidden');
                        }
                        hasError = true;
                    } else if (plainJudul.length > 255) {
                        if (judulError) {
                            judulError.textContent = 'Judul maksimal 255 karakter';
                            judulError.classList.remove('hidden');
                        }
                        hasError = true;
                    }

                    if (!plainDesk) {
                        if (deskripsiError) {
                            deskripsiError.textContent = 'Deskripsi wajib diisi';
                            deskripsiError.classList.remove('hidden');
                        }
                        hasError = true;
                    }

                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });

        function previewImage(input) {
            const img = document.getElementById('preview_image');
            const name = document.getElementById('gambar_filename');
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
    </script>
</x-layout>


