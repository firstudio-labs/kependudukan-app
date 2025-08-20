<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Approval Perubahan Biodata</h1>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">NIK</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Diminta Pada</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $item)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $requests->firstItem() + $index }}
                                </th>
                                <td class="px-6 py-4">{{ $item->nik }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-white {{ $item->status === 'pending' ? 'bg-yellow-500' : ($item->status === 'approved' ? 'bg-green-600' : 'bg-red-600') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $item->requested_at ? $item->requested_at->format('d-m-Y H:i') : '-' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.desa.biodata-approval.show', $item->id) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada permintaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->count() > 0)
                <div class="px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $requests->firstItem() }} sampai {{ $requests->lastItem() }} dari {{ $requests->total() }} data
                    </div>
                    {{ $requests->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>
</x-layout>


