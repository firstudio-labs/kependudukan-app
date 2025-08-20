<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Permintaan Perubahan</h1>

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

        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <form id="approvalForm" class="space-y-4" method="POST">
                @csrf
                <div>
                    <label for="reviewer_note" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="reviewer_note" name="reviewer_note" rows="3" class="mt-1 p-2 w-full border rounded"></textarea>
                </div>
                <div class="flex gap-3">
                    <button formaction="{{ route('admin.desa.biodata-approval.approve', $requestModel->id) }}" formmethod="POST" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Setujui</button>
                    <button formaction="{{ route('admin.desa.biodata-approval.reject', $requestModel->id) }}" formmethod="POST" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Tolak</button>
                </div>
            </form>
        </div>

    </div>
</x-layout>


