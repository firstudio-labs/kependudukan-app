<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Permintaan Perubahan</h1>

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Informasi Umum -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h2 class="font-semibold text-gray-700 mb-3">Informasi Umum</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-600 mb-2"><strong>NIK:</strong> {{ $requestModel->nik }}</div>
                    <div class="text-gray-600 mb-2"><strong>Nama Penduduk:</strong> {{ $requestModel->requested_changes['full_name'] ?? ($requestModel->current_data['full_name'] ?? 'Tidak tersedia') }}</div>
                    <div class="text-gray-600 mb-2"><strong>Desa:</strong> {{ $requestModel->village ? $requestModel->village->name : 'Tidak tersedia' }}</div>
                </div>
                <div>
                    <div class="text-gray-600 mb-2"><strong>Status:</strong> 
                        @if($requestModel->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Menunggu Review</span>
                        @elseif($requestModel->status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                        @elseif($requestModel->status === 'rejected')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                        @endif
                    </div>
                    <div class="text-gray-600 mb-2"><strong>Tanggal Request:</strong> {{ $requestModel->requested_at ? $requestModel->requested_at->format('d M Y H:i') : '-' }}</div>
                    @if($requestModel->reviewed_at)
                        <div class="text-gray-600 mb-2"><strong>Tanggal Review:</strong> {{ $requestModel->reviewed_at->format('d M Y H:i') }}</div>
                        <div class="text-gray-600 mb-2"><strong>Reviewer:</strong> {{ $requestModel->reviewer ? $requestModel->reviewer->name : 'Admin' }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Data Saat Ini</h2>
                <div class="text-sm text-gray-800 space-y-2">
                    @if(isset($requestModel->current_data['full_name']))
                        <div><strong>Nama:</strong> {{ $requestModel->current_data['full_name'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['gender']))
                        <div><strong>Jenis Kelamin:</strong> {{ $requestModel->current_data['gender'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['birth_place']))
                        <div><strong>Tempat Lahir:</strong> {{ $requestModel->current_data['birth_place'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['birth_date']))
                        <div><strong>Tanggal Lahir:</strong> {{ $requestModel->current_data['birth_date'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['address']))
                        <div><strong>Alamat:</strong> {{ $requestModel->current_data['address'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['rt']))
                        <div><strong>RT:</strong> {{ $requestModel->current_data['rt'] }}</div>
                    @endif
                    @if(isset($requestModel->current_data['rw']))
                        <div><strong>RW:</strong> {{ $requestModel->current_data['rw'] }}</div>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold text-gray-700 mb-3">Perubahan Diminta</h2>
                <div class="text-sm text-gray-800 space-y-2">
                    @if(isset($requestModel->requested_changes['full_name']))
                        <div><strong>Nama:</strong> {{ $requestModel->requested_changes['full_name'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['gender']))
                        <div><strong>Jenis Kelamin:</strong> {{ $requestModel->requested_changes['gender'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['birth_place']))
                        <div><strong>Tempat Lahir:</strong> {{ $requestModel->requested_changes['birth_place'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['birth_date']))
                        <div><strong>Tanggal Lahir:</strong> {{ $requestModel->requested_changes['birth_date'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['address']))
                        <div><strong>Alamat:</strong> {{ $requestModel->requested_changes['address'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['rt']))
                        <div><strong>RT:</strong> {{ $requestModel->requested_changes['rt'] }}</div>
                    @endif
                    @if(isset($requestModel->requested_changes['rw']))
                        <div><strong>RW:</strong> {{ $requestModel->requested_changes['rw'] }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h2 class="font-semibold text-gray-700 mb-3">Aksi Review</h2>

            @if($requestModel->status === 'pending')
                <form id="approvalForm" class="space-y-4" method="POST">
                    @csrf
                    <div>
                        <label for="reviewer_note" class="block text-sm font-medium text-gray-700">Catatan untuk Penduduk</label>
                        <textarea id="reviewer_note" name="reviewer_note" rows="3" class="mt-1 p-2 w-full border rounded" placeholder="Berikan catatan atau alasan approval/rejection..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Catatan ini akan dikirim ke penduduk untuk informasi lebih lanjut</p>
                    </div>
                    <div class="flex gap-3">
                        <button formaction="{{ route('admin.desa.biodata-approval.approve', $requestModel->id) }}" formmethod="POST" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            Setujui
                        </button>
                        <button formaction="{{ route('admin.desa.biodata-approval.reject', $requestModel->id) }}" formmethod="POST" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Tolak
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-700 mb-2">Catatan Reviewer</h3>
                    @if($requestModel->reviewer_note)
                        <div class="text-sm text-gray-800 bg-white p-3 rounded border">
                            {{ $requestModel->reviewer_note }}
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fa-solid fa-info-circle"></i>
                            Catatan ini telah dikirim ke penduduk
                        </p>
                    @else
                        <div class="text-sm text-gray-500 italic">
                            Tidak ada catatan dari reviewer
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</x-layout>


