<x-layout>
    <div class="p-4 mt-6 max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Permintaan</h1>

        <div class="mb-4">
            <span class="px-2 py-1 rounded text-white {{ $requestModel->status === 'pending' ? 'bg-yellow-500' : ($requestModel->status === 'approved' ? 'bg-green-600' : 'bg-red-600') }}">
                {{ ucfirst($requestModel->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Data Saat Ini</h2>
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($requestModel->current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Perubahan Diminta</h2>
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($requestModel->requested_changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('user.biodata-change.index') }}" class="px-4 py-2 border rounded">Kembali</a>
        </div>
    </div>
</x-layout>


