<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Warungku - Informasi Usaha Desa</h1>
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama usaha/alamat"
                       class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <input type="text" name="kelompok" value="{{ $filters['kelompok'] ?? '' }}" placeholder="Kelompok usaha"
                       class="block p-2 pl-3 w-48 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <select name="status" class="block p-2 pl-3 w-40 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="aktif" {{ ($filters['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ ($filters['status'] ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="semua" {{ ($filters['status'] ?? '') === 'semua' ? 'selected' : '' }}>Semua</option>
                </select>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed]">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">Foto</th>
                        <th class="px-6 py-3">Nama Warung</th>
                        <th class="px-6 py-3">Pemilik</th>
                        <th class="px-6 py-3">No WA</th>
                        <th class="px-6 py-3">Wilayah</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <div class="w-12 h-12 rounded overflow-hidden bg-gray-100">
                                    @if($item->foto_url)
                                        <img src="{{ $item->foto_url }}" alt="Foto" class="w-full h-full object-cover rounded">
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $item->nama_usaha }}</td>
                            <td class="px-6 py-4">{{ $item->owner_name ?? optional($item->penduduk)->nama ?? '-' }}</td>
                            <td class="px-6 py-4">{{ optional($item->penduduk)->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div><strong>Provinsi:</strong> {{ $item->province_name ?? '-' }}</div>
                                    <div><strong>Kabupaten:</strong> {{ $item->district_name ?? '-' }}</div>
                                    <div><strong>Kecamatan:</strong> {{ $item->sub_district_name ?? '-' }}</div>
                                    <div><strong>Desa:</strong> {{ $item->village_name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.desa.warungku.show', $item->id) }}" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-xs px-3 py-1">Detail</a>
                                    <form method="POST" action="{{ route('admin.desa.warungku.update-status', $item->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $item->status === 'aktif' ? 'tidak_aktif' : 'aktif' }}">
                                        <button type="submit" class="text-xs px-3 py-1 rounded font-medium {{ $item->status === 'aktif' ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                            {{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6 text-center text-gray-500">Belum ada data usaha.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</x-layout>

<script>
// Auto hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
