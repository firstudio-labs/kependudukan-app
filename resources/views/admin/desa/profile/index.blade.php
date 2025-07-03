<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Profil Admin Desa</h1>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="md:col-span-1">
                    <div class="flex flex-col items-center">
                        <div class="w-40 h-40 rounded-full bg-gray-200 overflow-hidden mb-4">
                            @if ($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile photo"
                                    class="w-full h-full object-cover" id="profile-image">
                            @else
                                <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="Profile photo" class="w-full h-full object-cover" id="profile-image">
                            @endif
                        </div>
                        <button type="button" id="change-photo-btn"
                            class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] transition">
                            Ubah Logo
                        </button>
                    </div>
                </div>

                <!-- Profile Details Section -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Username</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->username ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">NIK</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->nik ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Email</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->email ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">No. Handphone</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->no_hp ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->alamat ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Role</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ ucfirst($user->role) ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Status</label>
                                <div
                                    class="bg-gray-50 p-2 rounded border border-gray-300 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Nama Kepala Desa</h3>
                        <p class="text-gray-600">{{ $user->kepalaDesa?->nama ?? '-' }}</p>
                    </div>

                    @if($user->kepalaDesa?->tanda_tangan)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Tanda Tangan</h3>
                        <img src="{{ asset('storage/' . $user->kepalaDesa->tanda_tangan) }}"
                             alt="Tanda Tangan" class="h-20">
                    </div>
                    @endif

                    <div class="flex justify-end">
                        <a href="{{ route('admin.desa.profile.edit') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Edit Profil
                        </a>
                    </div>
                </div>
                </ div>
            </div>
        </div>

        <!-- Photo Change Modal -->
        <div id="photo-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden pointer-events-none">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 pointer-events-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Ubah Logo</h3>
                    <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.desa.profile.update-photo') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="mb-4">
                        <div class="flex flex-col items-center">
                            <div class="w-40 h-40 rounded-full bg-gray-200 overflow-hidden mb-4">
                                @if ($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Preview"
                                        class="w-full h-full object-cover" id="preview-image">
                                @else
                                    <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                        alt="Preview" class="w-full h-full object-cover" id="preview-image">
                                @endif
                            </div>

                            <label for="image"
                                class="cursor-pointer px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] transition">
                                Pilih Logo
                            </label>
                            <input type="file" id="image" name="image" class="hidden" accept="image/*"
                                onchange="previewImage(this)">
                            <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maks: 2MB</p>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-btn"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#7886C7] text-white rounded-md hover:bg-[#2D336B] transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Modal functionality
            const modal = document.getElementById('photo-modal');
            const changePhotoBtn = document.getElementById('change-photo-btn');
            const closeModalBtn = document.getElementById('close-modal');
            const cancelBtn = document.getElementById('cancel-btn');

            changePhotoBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                modal.classList.add('backdrop-blur-sm');
                modal.classList.remove('pointer-events-none');
                modal.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
            });

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('backdrop-blur-sm');
                modal.classList.add('pointer-events-none');
                modal.style.backgroundColor = '';
            }

            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        document.getElementById('preview-image').src = e.target.result;
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            @if (session('success'))
                setTimeout(() => {
                    const successMessage = "{{ session('success') }}";
                    alert(successMessage);
                }, 300);
            @endif
        </script>
</x-layout>
