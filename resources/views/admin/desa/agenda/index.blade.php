<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Agenda Desa</h1>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('admin.desa.agenda.index') }}" class="relative w-full max-w-xs">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari agenda..." />
                <button type="submit" class="absolute top-1/2 left-2 w-5 h-5 text-gray-400 transform -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
            <div>
                <a href="{{ route('admin.desa.agenda.create') }}"
                    class="flex items-center justify-center bg-[#7886C7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-[#2D336B] transition duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-4 h-4 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Agenda</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Judul</th>
                            <th class="px-6 py-3">Deskripsi</th>
                            <th class="px-6 py-3">Gambar</th>
                            <th class="px-6 py-3">Alamat</th>
                            <th class="px-6 py-3">Tanggal dibuat</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $index => $agenda)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $agendas->firstItem() + $index }}
                                </th>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ strip_tags($agenda->judul) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">{!! Str::limit($agenda->deskripsi_sanitized, 100) !!}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($agenda->gambar)
                                        <img src="{{ asset('storage/' . $agenda->gambar) }}" 
                                            alt="Gambar {{ $agenda->judul }}"
                                            class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:scale-105 transition-transform duration-200 cursor-pointer"
                                            onclick="showAgendaImageModal('{{ asset('storage/' . $agenda->gambar) }}', '{{ $agenda->judul }}')"
                                        />
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $agenda->alamat ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900">{{ $agenda->created_at->format('d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $agenda->created_at->format('M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $agenda->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.desa.agenda.show', $agenda->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">Detail</a>
                                        <a href="{{ route('admin.desa.agenda.edit', $agenda->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100">Edit</a>
                                        <form action="{{ route('admin.desa.agenda.destroy', $agenda->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus agenda ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fa-solid fa-calendar-days text-2xl text-gray-400"></i>
                                        </div>
                                        <div class="text-gray-500">
                                            <div class="font-medium text-lg">Belum ada agenda</div>
                                            <div class="text-sm">Agenda desa akan muncul di sini</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agendas->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $agendas->firstItem() }} sampai {{ $agendas->lastItem() }} dari {{ $agendas->total() }} hasil
                    </div>
                    {{ $agendas->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        <!-- Image Modal Agenda -->
        <div id="agendaImageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
            <div class="relative max-w-4xl max-h-full p-4">
                <button onclick="closeAgendaImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl font-bold z-10">&times;</button>
                <img id="agendaModalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg" />
                <div class="text-center mt-4">
                    <h3 id="agendaModalTitle" class="text-white text-lg font-semibold"></h3>
                </div>
            </div>
        </div>

        <script>
            function showAgendaImageModal(imageSrc, title) {
                document.getElementById('agendaModalImage').src = imageSrc;
                document.getElementById('agendaModalTitle').textContent = title;
                document.getElementById('agendaImageModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeAgendaImageModal() {
                document.getElementById('agendaImageModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            document.getElementById('agendaImageModal').addEventListener('click', function(e) {
                if (e.target === this) { closeAgendaImageModal(); }
            });
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeAgendaImageModal(); } });
        </script>
    </div>
</x-layout>


