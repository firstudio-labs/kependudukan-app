<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengguna Mobile</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi ringkas pengguna beserta detail wilayah</p>
            </div>
            <a href="{{ route('admin.desa.mobile-users.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Kembali</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kartu Identitas -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Identitas</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs text-gray-500">NIK</div>
                        <div class="font-medium break-all">{{ $item->nik }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Nama Lengkap</div>
                        <div class="font-medium">{{ $item->full_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">No HP</div>
                        <div class="font-medium">{{ $item->no_hp ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">No KK</div>
                        <div class="font-medium break-all">{{ $item->kk ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Kartu Wilayah -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Wilayah</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs text-gray-500">Provinsi</div>
                            <div class="font-medium">{{ $item->wilayah['provinsi'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Kabupaten</div>
                            <div class="font-medium">{{ $item->wilayah['kabupaten'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Kecamatan</div>
                            <div class="font-medium">{{ $item->wilayah['kecamatan'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Desa</div>
                            <div class="font-medium">{{ $item->wilayah['desa'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>


