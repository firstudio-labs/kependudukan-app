<x-layout>
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Data Tagihan</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Kelola Tagihan</h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.desa.master-tagihan.tagihan.create-multiple') }}" 
                       class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fa-solid fa-plus mr-1"></i>Tambah Multiple
                    </a>
                    <button onclick="toggleTagihanForm()" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fa-solid fa-plus mr-1"></i>Tambah Tagihan
                    </button>
                </div>
            </div>

            <div id="tagihanForm" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                <form id="tagihanFormElement" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penduduk <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <!-- Search input untuk NIK/Nama -->
                                <input type="text" id="pendudukSearchInput" placeholder="Cari NIK atau nama penduduk..."
                                    class="block w-full p-2 pl-3 pr-10 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7] mb-2">
                                <div class="absolute top-2 right-2 pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                
                                <!-- Dropdown penduduk -->
                                <select name="nik" id="pendudukSelect" required
                                    class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                    <option value="">Pilih Penduduk</option>
                                    @if(!empty($penduduks))
                                        @foreach($penduduks as $penduduk)
                                            <option value="{{ $penduduk['nik'] ?? '' }}" 
                                                data-name="{{ $penduduk['full_name'] ?? 'N/A' }}"
                                                data-nik="{{ $penduduk['nik'] ?? 'N/A' }}">
                                                {{ $penduduk['full_name'] ?? 'N/A' }} - {{ $penduduk['nik'] ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Tidak ada data penduduk</option>
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none" style="top: 40px;">
                                    <i class="fa-solid fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="tagihanKategoriSelect" required onchange="loadSubKategoris()"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori <span class="text-red-500">*</span></label>
                            <select name="sub_kategori_id" id="tagihanSubKategoriSelect" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="">Pilih Sub Kategori</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Bulan Ini</label>
                            <input type="number" name="nominal" id="tagihanNominalInput" step="0.01" min="0" placeholder="Nominal Bulan Ini"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                            <p class="text-xs text-gray-500 mt-1">Carry-over tunggakan akan ditambahkan otomatis</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="tagihanStatusSelect" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="pending">Pending</option>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" id="tagihanTanggalInput" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="tagihanKeteranganInput" rows="2" placeholder="Keterangan"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]"></textarea>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 flex gap-2">
                            <button type="submit" id="tagihanSubmitBtn" class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Simpan Tagihan
                            </button>
                            <button type="button" onclick="toggleTagihanForm()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <form method="GET" class="mb-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1">Pencarian</label>
                        <input type="text" name="search_tagihan" value="{{ $searchTagihan }}" placeholder="Cari NIK/No KK"
                            class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1">Kategori</label>
                        <select name="filter_kategori" id="filterKategoriSelect" onchange="loadFilterSubKategoris()"
                            class="block p-2 w-48 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('filter_kategori') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1">Sub Kategori</label>
                        <select name="filter_sub_kategori" id="filterSubKategoriSelect"
                            class="block p-2 w-48 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Sub Kategori</option>
                            @if(request('filter_kategori'))
                                @php
                                    $selectedKategori = $kategoris->firstWhere('id', request('filter_kategori'));
                                @endphp
                                @if($selectedKategori && $selectedKategori->subKategoris)
                                    @foreach($selectedKategori->subKategoris as $subKategori)
                                        <option value="{{ $subKategori->id }}" {{ request('filter_sub_kategori') == $subKategori->id ? 'selected' : '' }}>
                                            {{ $subKategori->nama_sub_kategori }}
                                        </option>
                                    @endforeach
                                @endif
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1">Tanggal dari</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="block p-2 w-44 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1">Tanggal sampai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="block p-2 w-44 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Terapkan</button>
                        <a href="{{ route('admin.desa.master-tagihan.tagihan.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Reset</a>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed] sticky top-0">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Penduduk</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Sub Kategori</th>
                            <th class="px-6 py-3">Nominal</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $tagihan)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $nikKey = (string) $tagihan->nik;
                                        $pendudukData = $pendudukLookup->get($nikKey);
                                    @endphp
                                    <div>
                                        <div class="font-medium">{{ $pendudukData['full_name'] ?? 'Data tidak ditemukan' }}</div>
                                        <div class="text-xs text-gray-500">{{ $pendudukData['nik'] ?? ('NIK: ' . $nikKey) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $tagihan->kategori->nama_kategori }}</td>
                                <td class="px-6 py-4">{{ $tagihan->subKategori->nama_sub_kategori }}</td>
                                <td class="px-6 py-4">
                                    @if($tagihan->nominal)
                                        <div class="font-medium">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</div>
                                        @if(strpos($tagihan->keterangan ?? '', 'Carry-over tunggakan bulan sebelumnya') !== false)
                                            <div class="text-xs text-gray-500">(termasuk tunggakan)</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <select onchange="updateStatus({{ $tagihan->id }}, this.value)" 
                                        class="text-xs font-medium rounded-full border-0 focus:ring-2 focus:ring-blue-500 {{ 
                                            $tagihan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($tagihan->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                                        }}">
                                        <option value="pending" {{ $tagihan->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="lunas" {{ $tagihan->status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="belum_lunas" {{ $tagihan->status === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">{{ $tagihan->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button onclick="showDetailTagihan({{ $tagihan->id }})"
                                            class="text-gray-700 hover:text-gray-900 p-1 rounded hover:bg-gray-100"
                                            title="Detail tagihan">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </button>
                                        <button onclick="editTagihan({{ $tagihan->id }})" 
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100" 
                                            title="Edit tagihan">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.desa.master-tagihan.tagihan.destroy', $tagihan->id) }}" 
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100" 
                                                title="Hapus tagihan">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data tagihan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2 text-sm text-gray-600">
                Total {{ $tagihans->count() }} data tagihan
            </div>
        </div>
    </div>

    <script>
        function toggleTagihanForm() {
            const form = document.getElementById('tagihanForm');
            const isHidden = form.classList.contains('hidden');
            if (isHidden) {
                resetTagihanForm();
                form.classList.remove('hidden');
                // Pastikan semua opsi penduduk ditampilkan saat form dibuka
                filterPenduduk();
            } else {
                form.classList.add('hidden');
            }
        }

        function resetTagihanForm() {
            document.getElementById('tagihanFormElement').action = '{{ route("admin.desa.master-tagihan.tagihan.store") }}';
            document.getElementById('tagihanFormElement').method = 'POST';
            document.getElementById('pendudukSearchInput').value = '';
            document.getElementById('pendudukSelect').value = '';
            document.getElementById('tagihanKategoriSelect').value = '';
            document.getElementById('tagihanSubKategoriSelect').innerHTML = '<option value="">Pilih Sub Kategori</option>';
            document.getElementById('tagihanNominalInput').value = '';
            document.getElementById('tagihanStatusSelect').value = 'pending';
            // Set default tanggal ke hari ini (YYYY-MM-DD)
            document.getElementById('tagihanTanggalInput').value = new Date().toISOString().split('T')[0];
            document.getElementById('tagihanKeteranganInput').value = '';
            document.getElementById('tagihanSubmitBtn').textContent = 'Simpan Tagihan';
            // Reset filter penduduk
            filterPenduduk();
        }

        function editTagihan(id) {
            fetch(`/admin/desa/master-tagihan/tagihan/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tagihanForm').classList.remove('hidden');
                    document.getElementById('tagihanFormElement').action = `/admin/desa/master-tagihan/tagihan/${id}`;
                    document.getElementById('tagihanFormElement').method = 'POST';
                    document.getElementById('tagihanKategoriSelect').value = data.kategori_id;
                    document.getElementById('tagihanNominalInput').value = data.nominal_bulan_ini || '';
                    document.getElementById('tagihanStatusSelect').value = data.status;
                    document.getElementById('tagihanTanggalInput').value = data.tanggal;
                    document.getElementById('tagihanKeteranganInput').value = data.keterangan || '';
                    document.getElementById('tagihanSubmitBtn').textContent = 'Update Tagihan';
                    
                    // Load sub kategoris for selected kategori
                    loadSubKategoris(data.sub_kategori_id);
                    
                    // Add hidden input for PUT
                    let methodInput = document.getElementById('tagihanFormElement').querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        document.getElementById('tagihanFormElement').appendChild(methodInput);
                    } else {
                        methodInput.value = 'PUT';
                    }
                })
                .catch(() => alert('Gagal memuat data tagihan'));
        }

        function loadSubKategoris(selectedSubKategoriId = null) {
            const kategoriId = document.getElementById('tagihanKategoriSelect').value;
            const subKategoriSelect = document.getElementById('tagihanSubKategoriSelect');
            subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
            if (kategoriId) {
                fetch(`/admin/desa/master-tagihan/sub-kategoris/${kategoriId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subKategori => {
                            const option = document.createElement('option');
                            option.value = subKategori.id;
                            option.textContent = subKategori.nama_sub_kategori;
                            if (selectedSubKategoriId && subKategori.id == selectedSubKategoriId) {
                                option.selected = true;
                            }
                            subKategoriSelect.appendChild(option);
                        });
                    })
                    .catch(() => {});
            }
        }

        function showDetailTagihan(id) {
            fetch(`/admin/desa/master-tagihan/tagihan/${id}`)
                .then(response => response.json())
                .then(data => {
                    const content = [];
                    content.push(`NIK: ${data.nik}`);
                    content.push(`Kategori: ${data.kategori?.nama_kategori ?? '-'}`);
                    content.push(`Sub Kategori: ${data.sub_kategori?.nama_sub_kategori ?? '-'}`);
                    content.push(`Nominal: Rp ${Number(data.nominal || 0).toLocaleString('id-ID')}`);
                    content.push(`Status: ${data.status}`);
                    content.push(`Tanggal: ${data.tanggal}`);
                    if (data.keterangan) {
                        content.push('');
                        content.push('Rincian:');
                        content.push(data.keterangan);
                    }
                    alert(content.join('\n'));
                })
                .catch(() => alert('Gagal memuat detail tagihan'));
        }

        function updateStatus(tagihanId, newStatus) {
            if (!confirm('Yakin ingin mengubah status tagihan?')) {
                location.reload();
                return;
            }
            fetch(`/admin/desa/master-tagihan/tagihan/${tagihanId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Gagal memperbarui status');
                    location.reload();
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan');
                location.reload();
            });
        }

        // Fungsi untuk memfilter penduduk berdasarkan search
        function filterPenduduk() {
            const searchTerm = document.getElementById('pendudukSearchInput').value.toLowerCase();
            const select = document.getElementById('pendudukSelect');
            const options = select.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') {
                    // Selalu tampilkan option "Pilih Penduduk"
                    option.style.display = 'block';
                    return;
                }
                
                const name = option.getAttribute('data-name')?.toLowerCase() || '';
                const nik = option.getAttribute('data-nik')?.toLowerCase() || '';
                
                // Jika search kosong, tampilkan semua opsi
                if (searchTerm === '') {
                    option.style.display = 'block';
                } else if (name.includes(searchTerm) || nik.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        // Fungsi untuk memuat sub kategori berdasarkan kategori yang dipilih di filter
        function loadFilterSubKategoris() {
            const kategoriId = document.getElementById('filterKategoriSelect').value;
            const subKategoriSelect = document.getElementById('filterSubKategoriSelect');
            subKategoriSelect.innerHTML = '<option value="">Semua Sub Kategori</option>';
            
            if (kategoriId) {
                fetch(`/admin/desa/master-tagihan/sub-kategoris/${kategoriId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subKategori => {
                            const option = document.createElement('option');
                            option.value = subKategori.id;
                            option.textContent = subKategori.nama_sub_kategori;
                            subKategoriSelect.appendChild(option);
                        });
                    })
                    .catch(() => {
                        console.log('Gagal memuat sub kategori');
                    });
            }
        }

        // Event listener untuk search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('pendudukSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', filterPenduduk);
                // Pastikan semua opsi penduduk ditampilkan saat halaman dimuat
                filterPenduduk();
            }
            
            // Inisialisasi filter sub kategori jika ada kategori yang sudah dipilih
            const filterKategoriSelect = document.getElementById('filterKategoriSelect');
            if (filterKategoriSelect && filterKategoriSelect.value) {
                loadFilterSubKategoris();
            }
        });
    </script>
</x-layout>


