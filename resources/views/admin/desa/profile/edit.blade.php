<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Profil Admin Desa</h1>
        </div>

        <form action="{{ route('admin.desa.profile.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="md:col-span-1">
                    <div class="flex flex-col items-center">
                        <div class="w-40 h-40 rounded-full bg-gray-200 overflow-hidden mb-4">
                            @if ($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile photo"
                                    class="w-full h-full object-cover" id="preview-image">
                            @else
                                <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="Profile photo" class="w-full h-full object-cover" id="preview-image">
                            @endif
                        </div>
                        <div class="flex flex-col items-center">
                            <label for="image"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
                                Ubah Foto
                            </label>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*"
                                onchange="previewImage(this)">
                            <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, GIF. Maks: 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details Section -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="username"
                                    class="block text-gray-600 text-sm font-medium mb-1">Username</label>
                                <input type="text" id="username" name="username"
                                    value="{{ old('username', $user->username) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('username')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="nik" class="block text-gray-600 text-sm font-medium mb-1">NIK</label>
                                <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}"
                                    class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                            </div>

                            <div>
                                <label for="email" class="block text-gray-600 text-sm font-medium mb-1">Email</label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="no_hp" class="block text-gray-600 text-sm font-medium mb-1">No.
                                    Handphone</label>
                                <input type="text" id="no_hp" name="no_hp"
                                    value="{{ old('no_hp', $user->no_hp) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('no_hp')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="alamat"
                                    class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" class="w-full p-2 border border-gray-300 rounded">{{ old('alamat', $user->alamat) }}</textarea>
                                @error('alamat')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="nama" class="block text-gray-600 text-sm font-medium mb-1">Nama Kepala Desa</label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->kepalaDesa?->nama) }}"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('nama')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="tanda_tangan" class="block text-gray-600 text-sm font-medium mb-1">Tanda Tangan</label>
                                <input type="file" id="tanda_tangan" name="tanda_tangan" accept="image/*"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @if($user->kepalaDesa?->tanda_tangan)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->kepalaDesa->tanda_tangan) }}"
                                             alt="Tanda Tangan" class="h-20">
                                    </div>
                                @endif
                                @error('tanda_tangan')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Ubah Password (opsional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="current_password"
                                    class="block text-gray-600 text-sm font-medium mb-1">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('current_password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-gray-600 text-sm font-medium mb-1">Password
                                    Baru</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full p-2 border border-gray-300 rounded">
                                @error('new_password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation"
                                    class="block text-gray-600 text-sm font-medium mb-1">Konfirmasi Password
                                    Baru</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="w-full p-2 border border-gray-300 rounded">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.desa.profile.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition mr-2">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layout>
