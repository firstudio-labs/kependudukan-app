<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Profil Admin Desa</h1>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="md:col-span-1">
                    <div class="flex flex-col items-center space-y-6">
                        <!-- Foto Pengguna -->
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden mb-2">
                                @if($user->foto_pengguna)
                                    <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="Foto Pengguna" class="w-full h-full object-cover">
                                @else
                                    <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="Foto Pengguna" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <span class="text-sm text-gray-600">Foto Pengguna</span>
                        </div>
                        <!-- Logo -->
                        <div class="flex flex-col items-center">
                            <div class="w-24 h-24 rounded bg-gray-100 overflow-hidden mb-2 border border-gray-300">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Logo" class="w-full h-full object-contain p-2">
                                @else
                                    <span class="flex items-center justify-center w-full h-full text-gray-400">Tidak ada logo</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-600">Logo</span>
                        </div>
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
    </div>
</x-layout>
