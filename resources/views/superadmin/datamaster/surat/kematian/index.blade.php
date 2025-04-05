<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Surat Keterangan Kematian</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="" class="relative w-full max-w-xs">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari surat kematian..."
                />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 1110.15-10.15 7.5 7.5 0 01-10.15 10.15z" />
                    </svg>
                </button>
            </form>

            <button
                type="button"
                onclick="window.location.href='{{ route('superadmin.surat.kematian.create') }}'"
                class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-5 py-2.5 flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Surat Kematian</span>
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">Penyebab Kematian</th>
                        <th class="px-6 py-3">Nama Pelapor</th>
                        <th class="px-6 py-3">Tanggal Meninggal</th>
                        <th class="px-6 py-3">Pejabat Penandatangan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kematianList as $index => $kematian)
                    <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $index + 1 }}</th>
                        <td class="px-6 py-4">
                            @if(is_array($kematian->nik))
                                {{ $kematian->nik[0] ?? '-' }}
                            @else
                                {{ $kematian->nik ?? '-' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(is_array($kematian->full_name))
                                {{ $kematian->full_name[0] ?? '-' }}
                            @else
                                {{ $kematian->full_name ?? '-' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $kematian->death_cause ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $kematian->reporter_name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($kematian->death_date)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $kematian->signer ? $kematian->signer->judul : $kematian->signing }}
                        </td>
                        <td class="flex items-center px-6 py-4 space-x-2">

                            <a href="{{ route('superadmin.surat.kematian.export-pdf', $kematian->id) }}" class="text-blue-600 hover:text-blue-800" aria-label="Export PDF" target="_blank">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('superadmin.surat.kematian.edit', $kematian->id) }}" class="text-yellow-600 hover:text-yellow-800" aria-label="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('superadmin.surat.kematian.delete', $kematian->id) }}" method="POST" onsubmit="return confirmDelete(event)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:underline ml-3">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada data surat kematian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Section -->
            @if($kematianList->hasPages())
            <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing {{ $kematianList->firstItem() }} to {{ $kematianList->lastItem() }} of {{ $kematianList->total() }} results
                </div>
                {{ $kematianList->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        // Delete confirmation function
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2D336B',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return false;
        }


    </script>
</x-layout>
