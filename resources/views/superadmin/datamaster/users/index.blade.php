<x-layout>
    <div class="p-2 sm:p-4 mt-14">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Daftar Pengguna</h1>
            <a href="{{ route('superadmin.datamaster.user.create') }}" class="bg-[#7886C7] hover:bg-[#2D336B] text-white px-4 py-2 rounded-md flex items-center w-full sm:w-auto justify-center">
                <span class="mr-2">+</span> Tambah Pengguna
            </a>
        </div>

        

        <div class="mb-6">
            <form method="GET" action="{{ route('superadmin.datamaster.user.index') }}" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, username, email atau No HP..."
                       class="border border-gray-300 rounded-md p-2 flex-grow w-full">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md whitespace-nowrap">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('superadmin.datamaster.user.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Responsive Table -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <div class="font-medium">{{ $user->nik }}</div>
                                <!-- Mobile-only info -->
                                <div class="md:hidden mt-1 space-y-1">
                                    <div><span class="font-semibold">Username:</span> {{ $user->username ?? '-' }}</div>
                                    <div><span class="font-semibold">Email:</span> {{ $user->email ?? '-' }}</div>
                                    <div><span class="font-semibold">No HP:</span> {{ $user->no_hp ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->username ?? '-' }}
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email ?? '-' }}
                            </td>
                            <td class="hidden md:table-cell px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->no_hp ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->role === 'superadmin' ? 'bg-red-100 text-red-800' :
                                  ($user->role === 'admin' ? 'bg-blue-100 text-blue-800' :
                                  ($user->role === 'operator' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('superadmin.datamaster.user.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('superadmin.datamaster.user.destroy', $user->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" aria-label="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm before deleting using SweetAlert
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // Display success or error messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
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
        });
    </script>
</x-layout>
