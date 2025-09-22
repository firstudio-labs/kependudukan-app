<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Profil Admin Desa</h1>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Photo Section -->
                <div class="md:col-span-1">
                    <div class="flex flex-col items-center space-y-6">
                        <!-- Foto Pengguna -->
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden mb-2">
                                @if($user->foto_pengguna)
                                    <img src="{{ asset('storage/' . $user->foto_pengguna) }}" alt="Foto Pengguna" class="w-full h-full object-cover">
                                @else
                                    <img src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="Foto Pengguna" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <span class="text-sm text-gray-600">Foto Pengguna</span>
                        </div>
                        <!-- Logo -->
                        <div class="flex flex-col items-center">
                            <div class="w-24 h-24 rounded bg-gray-100 overflow-hidden mb-2 border border-gray-300">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Logo" class="w-full h-full object-contain p-2">
                                @else
                                    <span class="flex items-center justify-center w-full h-full text-gray-400">Tidak ada logo</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-600">Logo</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Details Section -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Informasi Pribadi</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-gray-600 text-sm font-medium mb-1">Peta Lokasi</label>
                                <div id="map-view" class="w-full h-60 rounded border border-gray-300"></div>
                                <div class="text-sm text-gray-700 mt-2">
                                    <span class="font-medium">Koordinat:</span>
                                    <span id="koordinat-text">{{ $user->tag_lokasi ?? '-' }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Username</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->username ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">NIK</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->nik ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Email</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->email ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">No. Handphone</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->no_hp ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ $user->alamat ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Role</label>
                                <div class="bg-gray-50 p-2 rounded border border-gray-300 text-gray-800">
                                    {{ ucfirst($user->role) ?? 'Belum diatur' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 text-sm font-medium mb-1">Status</label>
                                <div
                                    class="bg-gray-50 p-2 rounded border border-gray-300 {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Non-aktif' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Nama Kepala Desa</h3>
                        <p class="text-gray-600">{{ $user->kepalaDesa?->nama ?? '-' }}</p>
                    </div>

                    @if($user->kepalaDesa?->tanda_tangan)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Tanda Tangan</h3>
                        <img src="{{ asset('storage/' . $user->kepalaDesa->tanda_tangan) }}"
                             alt="Tanda Tangan" class="h-20">
                    </div>
                    @endif

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.desa.profile.edit') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Edit Profil
                        </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
    @php
        $editId = request()->query('edit');
        $editing = $editId ? ($user->perangkatDesa->firstWhere('id', (int)$editId) ?? null) : null;
        $showForm = (bool) $editing;
    @endphp
    <div class="bg-white p-6 rounded-lg shadow-md mt-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Perangkat Desa</h2>
            <button type="button" id="toggle-perangkat-btn"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Tambah Perangkat Desa
            </button>
        </div>
        @php $perangkat = $user->perangkatDesa ?? collect(); @endphp
        @if($perangkat->count() === 0)
            <p class="text-gray-600">Belum ada data perangkat desa.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($perangkat as $p)
                    <div class="border rounded-lg p-4 flex gap-3">
                        <div class="w-20 h-20 rounded bg-gray-100 overflow-hidden flex-shrink-0 border">
                            @if($p->foto)
                                <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">Tidak ada foto</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">{{ $p->nama }}</div>
                            <div class="text-sm text-gray-600">{{ $p->jabatan }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ $p->alamat ?? '-' }}</div>
                            <div class="mt-2 flex gap-2">
                                <a href="{{ route('admin.desa.profile.index', ['edit' => $p->id]) }}#perangkat-form" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                                <form action="{{ route('admin.desa.profile.perangkat.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md mt-4 {{ $showForm ? '' : 'hidden' }}" id="perangkat-form">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ $editing ? 'Edit' : 'Tambah' }} Perangkat Desa</h2>
        <form action="{{ $editing ? route('admin.desa.profile.perangkat.item.update', $editing->id) : route('admin.desa.profile.perangkat.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($editing)
                @method('PUT')
            @endif
            <div id="batch-container" class="space-y-3 {{ $editing ? 'hidden' : '' }}">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-3" data-row>
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="perangkat[0][nama]" class="w-full p-2 border border-gray-300 rounded" {{ $editing ? '' : 'required' }}>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1">Jabatan</label>
                        <input type="text" name="perangkat[0][jabatan]" class="w-full p-2 border border-gray-300 rounded" {{ $editing ? '' : 'required' }}>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-medium mb-1">Foto</label>
                        <input type="file" name="perangkat[0][foto]" accept="image/*" class="w-full p-2 border border-gray-300 rounded" onchange="previewBatchFoto(this)">
                        <img alt="Preview" class="h-20 mt-2 hidden" data-preview>
                    </div>
                    <div class="md:col-span-5">
                        <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                        <input type="text" name="perangkat[0][alamat]" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" class="px-3 py-2 bg-red-600 text-white rounded w-full" onclick="removeBatchRow(this)">Hapus</button>
                    </div>
                </div>
            </div>

            @if($editing)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="single-edit">
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $editing->nama ?? '') }}" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $editing->jabatan ?? '') }}" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-600 text-sm font-medium mb-1">Alamat</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $editing->alamat ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="w-full p-2 border border-gray-300 rounded" onchange="previewSingleFoto(this)">
                    <img id="single-edit-preview" src="{{ $editing && $editing->foto ? asset('storage/' . $editing->foto) : '' }}" alt="Foto" class="h-20 mt-2 {{ $editing && $editing->foto ? '' : 'hidden' }}">
                </div>
            </div>
            @endif

            @if(!$editing)
            <div class="flex justify-between">
                <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700" onclick="addBatchRow()">Tambah Baris</button>
            </div>
            @endif

            <div class="flex items-center justify-between">
                <button type="button" id="cancel-perangkat-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-5 py-2 {{ $editing ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded">{{ $editing ? 'Simpan Perubahan' : 'Tambah Semua' }}</button>
            </div>
        </form>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md mt-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Data Wilayah</h2>
            <button type="button" id="toggle-wilayah-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Data Wilayah</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-gray-600">Luas Wilayah</div>
                    <div class="text-gray-800 font-medium">
                        @if(!empty($dataWilayah->luas_wilayah))
                            {{ $dataWilayah->luas_wilayah }} m²
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Jumlah Dusun</div>
                        <div class="text-gray-800 font-medium">{{ $dataWilayah->jumlah_dusun ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Jumlah RT</div>
                        <div class="text-gray-800 font-medium">{{ $dataWilayah->jumlah_rt ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Batas Wilayah</div>
                    @php $b = $dataWilayah->batas_wilayah ?? []; @endphp
                    <div class="text-gray-800">
                        <div>Utara: <span class="font-medium">{{ $b['utara'] ?? '-' }}</span></div>
                        <div>Timur: <span class="font-medium">{{ $b['timur'] ?? '-' }}</span></div>
                        <div>Barat: <span class="font-medium">{{ $b['barat'] ?? '-' }}</span></div>
                        <div>Selatan: <span class="font-medium">{{ $b['selatan'] ?? '-' }}</span></div>
                    </div>
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-2">Foto Peta</div>
                @if(!empty($dataWilayah->foto_peta))
                    <img src="{{ asset('storage/' . $dataWilayah->foto_peta) }}" alt="Foto Peta" class="w-full max-w-md rounded border">
                @else
                    <div class="text-gray-400">Belum ada foto peta</div>
                @endif
            </div>
        </div>
        <div id="form-wilayah" class="mt-6 hidden">
            <form action="{{ route('admin.desa.profile.data-wilayah.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Luas Wilayah</label>
                    <div class="relative">
                        <input type="text" name="luas_wilayah" value="{{ old('luas_wilayah', $dataWilayah->luas_wilayah ?? '') }}" class="w-full p-2 pr-12 border border-gray-300 rounded">
                        <span class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 text-sm bg-gray-50 border-l border-gray-300 rounded-r">m²</span>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Jumlah Dusun</label>
                    <input type="text" name="jumlah_dusun" value="{{ old('jumlah_dusun', $dataWilayah->jumlah_dusun ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Jumlah RT</label>
                    <input type="text" name="jumlah_rt" value="{{ old('jumlah_rt', $dataWilayah->jumlah_rt ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-medium mb-1">Foto Peta</label>
                    <input type="file" name="foto_peta" accept="image/*" class="w-full p-2 border border-gray-300 rounded" onchange="previewFotoPeta(this)">
                    @if(!empty($dataWilayah->foto_peta))
                        <img id="preview-foto-peta" src="{{ asset('storage/' . $dataWilayah->foto_peta) }}" class="h-24 mt-2">
                    @else
                        <img id="preview-foto-peta" class="h-24 mt-2 hidden">
                    @endif
                </div>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1">Batas Wilayah Utara</label>
                        <input type="text" name="batas_wilayah[utara]" value="{{ old('batas_wilayah.utara', $dataWilayah->batas_wilayah['utara'] ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1">Batas Wilayah Timur</label>
                        <input type="text" name="batas_wilayah[timur]" value="{{ old('batas_wilayah.timur', $dataWilayah->batas_wilayah['timur'] ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1">Batas Wilayah Barat</label>
                        <input type="text" name="batas_wilayah[barat]" value="{{ old('batas_wilayah.barat', $dataWilayah->batas_wilayah['barat'] ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-medium mb-1">Batas Wilayah Selatan</label>
                        <input type="text" name="batas_wilayah[selatan]" value="{{ old('batas_wilayah.selatan', $dataWilayah->batas_wilayah['selatan'] ?? '') }}" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                </div>
                <div class="md:col-span-2 flex justify-end gap-2">
                    <button type="button" id="cancel-wilayah-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const toggle = document.getElementById('toggle-wilayah-btn');
            const cancel = document.getElementById('cancel-wilayah-btn');
            const form = document.getElementById('form-wilayah');
            if (toggle && form) {
                toggle.addEventListener('click', function(){
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) {
                        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            }
            if (cancel && form) {
                cancel.addEventListener('click', function(){
                    form.classList.add('hidden');
                });
            }

            // Preview gambar untuk input Foto Peta
            window.previewFotoPeta = function(input) {
                const img = document.getElementById('preview-foto-peta');
                if (!img) return;
                if (input && input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    img.src = '';
                    img.classList.add('hidden');
                }
            }
        });
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formSection = document.getElementById('perangkat-form');
            const toggleBtn = document.getElementById('toggle-perangkat-btn');
            const cancelBtn = document.getElementById('cancel-perangkat-btn');
            window.previewBatchFoto = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        const row = input.closest('[data-row]');
                        if (!row) return;
                        const img = row.querySelector('[data-preview]');
                        if (img) {
                            img.src = e.target.result;
                            img.classList.remove('hidden');
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            window.previewSingleFoto = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById('single-edit-preview');
                        if (img) {
                            img.src = e.target.result;
                            img.classList.remove('hidden');
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            function renumberRows() {
                const rows = document.querySelectorAll('#batch-container [data-row]');
                rows.forEach(function(row, idx){
                    row.querySelectorAll('input').forEach(function(input){
                        const name = input.getAttribute('name');
                        if (!name) return;
                        input.setAttribute('name', name.replace(/perangkat\[\d+\]/, 'perangkat[' + idx + ']'));
                    });
                });
            }
            window.addBatchRow = function () {
                const container = document.getElementById('batch-container');
                const count = container.querySelectorAll('[data-row]').length;
                const tpl = `
                <div class=\"grid grid-cols-1 md:grid-cols-6 gap-3\" data-row>
                    <div class=\"md:col-span-2\">
                        <label class=\"block text-gray-600 text-sm font-medium mb-1\">Nama</label>
                        <input type=\"text\" name=\"perangkat[${count}][nama]\" class=\"w-full p-2 border border-gray-300 rounded\" required>
                    </div>
                    <div class=\"md:col-span-2\">
                        <label class=\"block text-gray-600 text-sm font-medium mb-1\">Jabatan</label>
                        <input type=\"text\" name=\"perangkat[${count}][jabatan]\" class=\"w-full p-2 border border-gray-300 rounded\" required>
                    </div>
                    <div class=\"md:col-span-2\">
                        <label class=\"block text-gray-600 text-sm font-medium mb-1\">Foto</label>
                        <input type=\"file\" name=\"perangkat[${count}][foto]\" accept=\"image/*\" class=\"w-full p-2 border border-gray-300 rounded\" onchange=\"previewBatchFoto(this)\">
                        <img alt=\"Preview\" class=\"h-20 mt-2 hidden\" data-preview>
                    </div>
                    <div class=\"md:col-span-5\">
                        <label class=\"block text-gray-600 text-sm font-medium mb-1\">Alamat</label>
                        <input type=\"text\" name=\"perangkat[${count}][alamat]\" class=\"w-full p-2 border border-gray-300 rounded\">
                    </div>
                    <div class=\"md:col-span-1 flex items-end\">
                        <button type=\"button\" class=\"px-3 py-2 bg-red-600 text-white rounded w-full\" onclick=\"removeBatchRow(this)\">Hapus</button>
                    </div>
                </div>`;
                container.insertAdjacentHTML('beforeend', tpl);
                renumberRows();
            }
            window.removeBatchRow = function (btn) {
                const row = btn.closest('[data-row]');
                const container = document.getElementById('batch-container');
                if (container.querySelectorAll('[data-row]').length <= 1) {
                    // kosongkan saja jika tinggal satu
                    row.querySelectorAll('input').forEach(function(i){ if (i.type === 'text') i.value=''; if (i.type==='file') i.value=null; });
                    return;
                }
                row.remove();
                renumberRows();
            }
            if (toggleBtn && formSection) {
                toggleBtn.addEventListener('click', function () {
                    const isHidden = formSection.classList.contains('hidden');
                    if (isHidden) {
                        formSection.classList.remove('hidden');
                        toggleBtn.textContent = 'Tutup Form Perangkat';
                        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        formSection.classList.add('hidden');
                        toggleBtn.textContent = 'Tambah Perangkat Desa';
                    }
                });
            }
            if (cancelBtn && formSection && toggleBtn) {
                cancelBtn.addEventListener('click', function () {
                    // Reset ke mode tambah (hapus query edit) dengan hanya menyembunyikan form secara UI
                    formSection.classList.add('hidden');
                    toggleBtn.textContent = 'Tambah Perangkat Desa';
                    // Opsional: bersihkan input
                    const inputs = formSection.querySelectorAll('input[type="text"], input[type="file"]');
                    inputs.forEach(function(i){ if (i.type === 'text') i.value = ''; if (i.type === 'file') i.value = null; });
                });
            }
            // Jika ada query ?edit, pastikan form terlihat dan tombol berubah label
            if (window.location.search.indexOf('edit=') !== -1 && formSection && toggleBtn) {
                formSection.classList.remove('hidden');
                toggleBtn.textContent = 'Tutup Form Perangkat';
                // Scroll ke form
                setTimeout(function(){ formSection.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('map-view');
            if (!mapEl) return;

            let lat = -6.1753924; // default Monas
            let lng = 106.8271528;
            const tagLokasi = "{{ $user->tag_lokasi ?? '' }}";
            if (tagLokasi) {
                const parts = tagLokasi.split(',').map(function (v) { return parseFloat(v.trim()); });
                if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                    lat = parts[0];
                    lng = parts[1];
                }
            }

            const map = L.map('map-view').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map);
        });
    </script>
</x-layout>
