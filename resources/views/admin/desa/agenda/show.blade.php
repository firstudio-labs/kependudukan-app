<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Agenda</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.desa.agenda.edit', $agenda->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                <a href="{{ route('admin.desa.agenda.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow space-y-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $agenda->judul }}</h2>
            </div>
            <div class="flex gap-4 items-start">
                @if($agenda->gambar)
                    <img src="{{ asset('storage/'.$agenda->gambar) }}" alt="{{ $agenda->judul }}" class="h-32 w-32 object-cover rounded border">
                @endif
                <div class="flex-1 prose max-w-none">
                    {!! $agenda->deskripsi_sanitized !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-600">Alamat</div>
                    <div class="text-gray-800">{{ $agenda->alamat ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Koordinat</div>
                    <div class="text-gray-800">{{ $agenda->tag_lokasi ?? '-' }}</div>
                </div>
            </div>

            @if($agenda->tag_lokasi)
            <div>
                <div id="map" class="w-full h-64 rounded border"></div>
            </div>
            @endif
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @if($agenda->tag_lokasi)
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var parts = '{{ $agenda->tag_lokasi }}'.split(',');
            var lat = parseFloat(parts[0]);
            var lng = parseFloat(parts[1]);
            if (!isNaN(lat) && !isNaN(lng)) {
                var map = L.map('map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
                L.marker([lat, lng]).addTo(map);
            }
        });
    </script>
    @endif
</x-layout>


