<x-layout>
    <div class="p-4 mt-14">


        <!-- Judul H1 -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Users</h1>

        <!-- Bar untuk Search dan Tambah Pasien -->
        <div class="flex justify-between items-center mb-4">
            <!-- Input Pencarian -->
            <form method="GET" action="{{ route('superadmin.datamaster.user.index') }}" class="relative">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari data users..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.datamaster.user.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Data User</span>
            </button>


        </div>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">NIK</th>
                        <th scope="col" class="px-6 py-3">Username</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">No HP</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>


                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)

                        @if (!empty($user) && isset($user->id))
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $user->id }}</th>
                                <td class="px-6 py-4">{{ $user->nik }}</td>
                                <td class="px-6 py-4">{{ $user->username }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->no_hp }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-2.5 h-2.5 rounded-full mr-2
                                        {{ $user->role === 'superadmin' ? 'bg-purple-600' :
                                           ($user->role === 'admin desa' ? 'bg-blue-600' :
                                           ($user->role === 'admin kabupaten' ? 'bg-indigo-600' :
                                           ($user->role === 'operator' ? 'bg-teal-600' :
                                           'bg-gray-600'))) }}"></div>
                                        {{ ucfirst($user->role) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-2.5 h-2.5 rounded-full mr-2
                                        {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"></div>
                                        {{ $user->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                                    </div>
                                </td>

                                <td class="flex items-center px-6 py-4 space-x-2">
                                    <a href="{{ route('superadmin.datamaster.user.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form id="delete-form-{{ $user->id }}" action="{{ route('superadmin.datamaster.user.destroy', $user->id) }}" method="POST" onsubmit="confirmDelete(event, {{ $user->id }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500">Data tidak valid.</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <!-- Pagination Section -->
            <x-pagination :data="$users" />
        </div>
    </div>

    <script src="{{ asset('js/sweet-alert-utils.js') }}"></script>

</x-layout>

