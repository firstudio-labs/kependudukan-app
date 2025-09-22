<x-layout>
    <div class="p-4 mt-14">
        <div class="bg-white p-4 rounded shadow max-w-3xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <img src="{{ $barangWarungku->foto_url ?? asset('images/statistik.jpg') }}" class="w-full h-64 object-cover rounded" />
                </div>
                <div>
                    <h1 class="text-2xl font-semibold">{{ $barangWarungku->nama_produk }}</h1>
                    <div class="text-xl text-green-700 mt-1">Rp {{ number_format($barangWarungku->harga,0,',','.') }}</div>
                    <div class="text-sm text-gray-500">Stok: {{ $barangWarungku->stok }}</div>
                    @if($barangWarungku->deskripsi)
                        <div class="mt-3 text-sm text-gray-700 whitespace-pre-line"><strong>Deskripsi:</strong>{{ $barangWarungku->deskripsi }}</div>
                    @endif

                    @php $usaha = $barangWarungku->informasiUsaha; $owner = $usaha?->penduduk; @endphp
                    <div class="mt-3 text-sm">
                        <div><strong>Klasifikasi:</strong> {{ ucfirst($jenisMaster->klasifikasi ?? '-') }}</div>
                        <div><strong>Jenis:</strong> {{ $jenisMaster->jenis ?? '-' }}</div>
                    </div>
                    @if($owner)
                        <div class="mt-4 space-y-1 text-sm">
                            <div><strong>Nama Usaha:</strong> {{ $usaha->nama_usaha ?? '-' }}</div>
                            <div><strong>Kontak:</strong> {{ $owner->no_hp ?? '-' }}</div>
                            <div><strong>Alamat:</strong> {{ $usaha->alamat ?? ($owner->alamat ?? '-') }}</div>
                            <div><strong>Lokasi:</strong> {{ $usaha->tag_lokasi ?? ($owner->tag_lokasi ?? '-') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>


