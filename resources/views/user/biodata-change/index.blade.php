<x-layout>
    <div class="p-4 mt-6 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Permintaan Perubahan Biodata</h1>

        <div class="mb-4">
            <a href="{{ route('user.biodata-change.create') }}" class="bg-[#4A47DC] text-white px-4 py-2 rounded">Buat Permintaan</a>
        </div>

        <div class="bg-white shadow rounded overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Diminta Pada</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $index => $item)
                    <tr class="border-b">
                        <td class="px-6 py-3">{{ $requests->firstItem() + $index }}</td>
                        <td class="px-6 py-3">{{ ucfirst($item->status) }}</td>
                        <td class="px-6 py-3">{{ $item->requested_at ? $item->requested_at->format('d-m-Y H:i') : '-' }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('user.biodata-change.show', $item->id) }}" class="text-blue-600">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4">Belum ada permintaan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links('pagination::tailwind') }}
        </div>
    </div>
</x-layout>


