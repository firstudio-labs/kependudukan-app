<x-layout>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Custom Select2 Styling -->
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            min-height: 42px;
        }
        .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: #7886C7;
            box-shadow: 0 0 0 3px rgba(120, 134, 199, 0.1);
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .select2-result-penduduk {
            padding: 8px 12px;
        }
        .select2-result-penduduk__name {
            font-weight: 500;
            color: #374151;
        }
        .select2-result-penduduk__nik {
            font-size: 0.75rem;
            color: #6b7280;
        }
    </style>
    
    <div class="p-4 mt-14">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Master Tagihan</h1>

        <!-- Kategori Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Master Kategori Tagihan</h2>
                <button onclick="toggleKategoriForm()" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-4 py-2">
                    Tambah Kategori
                </button>
            </div>

            <!-- Form Tambah/Edit Kategori (Hidden by default) -->
            <div id="kategoriForm" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                <form id="kategoriFormElement" method="POST">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="nama_kategori" id="kategoriNamaInput" placeholder="Nama Kategori" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                        </div>
                        <button type="submit" id="kategoriSubmitBtn" class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Simpan
                        </button>
                        <button type="button" onclick="toggleKategoriForm()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Kategori -->
            <form method="GET" class="mb-4">
                <div class="flex gap-3">
                    <input type="text" name="search_kategori" value="{{ $searchKategori }}" placeholder="Cari kategori..."
                        class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
                    @if($searchKategori)
                        <a href="{{ route('admin.desa.master-tagihan.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Tabel Kategori -->
            <div class="overflow-x-auto max-h-48 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed] sticky top-0">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Kategori</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoris as $kategori)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $kategori->nama_kategori }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button onclick="editKategori({{ $kategori->id }}, '{{ $kategori->nama_kategori }}')" 
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100" 
                                            title="Edit kategori">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.desa.master-tagihan.kategori.destroy', $kategori->id) }}" 
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100" 
                                                title="Hapus kategori">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Info jumlah data -->
            <div class="mt-2 text-sm text-gray-600">
                Total {{ $kategoris->count() }} data kategori
            </div>
        </div>

        <!-- Sub Kategori Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Master Sub Kategori Tagihan</h2>
                <button onclick="toggleSubKategoriForm()" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-4 py-2">
                    Tambah Sub Kategori
                </button>
            </div>

            <!-- Form Tambah/Edit Sub Kategori (Hidden by default) -->
            <div id="subKategoriForm" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                <form id="subKategoriFormElement" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <select name="kategori_id" id="subKategoriKategoriSelect" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="text" name="nama_sub_kategori" id="subKategoriNamaInput" placeholder="Nama Sub Kategori" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" id="subKategoriSubmitBtn" class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Simpan
                            </button>
                            <button type="button" onclick="toggleSubKategoriForm()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Sub Kategori -->
            <form method="GET" class="mb-4">
                <div class="flex gap-3">
                    <input type="text" name="search_sub_kategori" value="{{ $searchSubKategori }}" placeholder="Cari sub kategori..."
                        class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
                    @if($searchSubKategori)
                        <a href="{{ route('admin.desa.master-tagihan.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Tabel Sub Kategori -->
            <div class="overflow-x-auto max-h-48 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed] sticky top-0">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Nama Sub Kategori</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subKategoris as $subKategori)
                            <tr class="bg-white border-gray-300 border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $subKategori->kategori->nama_kategori }}</td>
                                <td class="px-6 py-4">{{ $subKategori->nama_sub_kategori }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button onclick="editSubKategori({{ $subKategori->id }}, {{ $subKategori->kategori_id }}, '{{ $subKategori->nama_sub_kategori }}')" 
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100" 
                                            title="Edit sub kategori">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.desa.master-tagihan.sub-kategori.destroy', $subKategori->id) }}" 
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus sub kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100" 
                                                title="Hapus sub kategori">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data sub kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Info jumlah data -->
            <div class="mt-2 text-sm text-gray-600">
                Total {{ $subKategoris->count() }} data sub kategori
            </div>
        </div>

        <!-- Tagihan Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Data Tagihan</h2>
                <button onclick="toggleTagihanForm()" class="text-white bg-[#7886C7] hover:bg-[#2D336B] focus:ring-4 focus:ring-[#5C69A7] font-medium rounded-lg text-sm px-4 py-2">
                    Tambah Tagihan
                </button>
            </div>

            <!-- Form Tambah/Edit Tagihan (Hidden by default) -->
            <div id="tagihanForm" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                <form id="tagihanFormElement" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penduduk <span class="text-red-500">*</span></label>
                            <div class="relative">
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
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
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

            <!-- Search Tagihan -->
            <form method="GET" class="mb-4">
                <div class="flex gap-3">
                    <input type="text" name="search_tagihan" value="{{ $searchTagihan }}" placeholder="Cari nama/NIK/No KK/keterangan..."
                        class="block p-2 pl-3 w-64 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#7886C7] text-white hover:bg-[#2D336B]">Cari</button>
                    @if($searchTagihan)
                        <a href="{{ route('admin.desa.master-tagihan.index') }}" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Tabel Tagihan -->
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
                                        $pendudukData = $pendudukLookup->get($tagihan->nik);
                                    @endphp
                                    <div>
                                        <div class="font-medium">{{ $pendudukData['full_name'] ?? 'Data tidak ditemukan' }}</div>
                                        <div class="text-xs text-gray-500">{{ $pendudukData['nik'] ?? 'NIK: ' . $tagihan->nik }}</div>
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

            <!-- Info jumlah data -->
            <div class="mt-2 text-sm text-gray-600">
                Total {{ $tagihans->count() }} data tagihan
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    <!-- Detail Tagihan Modal -->
    <div id="detailTagihanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Tagihan</h3>
                <div id="detailTagihanContent" class="whitespace-pre-line text-sm text-gray-800 bg-gray-50 rounded p-4 border"></div>
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="closeDetailTagihanModal()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Kategori Modal -->
    <div id="editKategoriModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Kategori</h3>
                <form id="editKategoriForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <input type="text" id="editKategoriNama" name="nama_kategori" required
                            class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Update
                        </button>
                        <button type="button" onclick="closeEditKategoriModal()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sub Kategori Modal -->
    <div id="editSubKategoriModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Sub Kategori</h3>
                <form id="editSubKategoriForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <select id="editSubKategoriKategori" name="kategori_id" required
                            class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <input type="text" id="editSubKategoriNama" name="nama_sub_kategori" required
                            class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Update
                        </button>
                        <button type="button" onclick="closeEditSubKategoriModal()" class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Toggle functions
        function toggleKategoriForm() {
            const form = document.getElementById('kategoriForm');
            const isHidden = form.classList.contains('hidden');
            
            if (isHidden) {
                // Reset form for create
                resetKategoriForm();
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        function toggleSubKategoriForm() {
            const form = document.getElementById('subKategoriForm');
            const isHidden = form.classList.contains('hidden');
            
            if (isHidden) {
                // Reset form for create
                resetSubKategoriForm();
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        function toggleTagihanForm() {
            const form = document.getElementById('tagihanForm');
            const isHidden = form.classList.contains('hidden');
            
            if (isHidden) {
                // Reset form for create
                resetTagihanForm();
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        // Reset form functions
        function resetKategoriForm() {
            document.getElementById('kategoriFormElement').action = '{{ route("admin.desa.master-tagihan.kategori.store") }}';
            document.getElementById('kategoriFormElement').method = 'POST';
            document.getElementById('kategoriNamaInput').value = '';
            document.getElementById('kategoriSubmitBtn').textContent = 'Simpan';
        }

        function resetSubKategoriForm() {
            document.getElementById('subKategoriFormElement').action = '{{ route("admin.desa.master-tagihan.sub-kategori.store") }}';
            document.getElementById('subKategoriFormElement').method = 'POST';
            document.getElementById('subKategoriKategoriSelect').value = '';
            document.getElementById('subKategoriNamaInput').value = '';
            document.getElementById('subKategoriSubmitBtn').textContent = 'Simpan';
        }

        function resetTagihanForm() {
            document.getElementById('tagihanFormElement').action = '{{ route("admin.desa.master-tagihan.tagihan.store") }}';
            document.getElementById('tagihanFormElement').method = 'POST';
            $('#pendudukSelect').val('').trigger('change'); // Reset Select2
            document.getElementById('tagihanKategoriSelect').value = '';
            document.getElementById('tagihanSubKategoriSelect').innerHTML = '<option value="">Pilih Sub Kategori</option>';
            document.getElementById('tagihanNominalInput').value = '';
            document.getElementById('tagihanStatusSelect').value = 'pending';
            document.getElementById('tagihanTanggalInput').value = '';
            document.getElementById('tagihanKeteranganInput').value = '';
            document.getElementById('tagihanSubmitBtn').textContent = 'Simpan Tagihan';
        }


        // Edit functions
        function editKategori(id, nama) {
            // Show form and set to edit mode
            document.getElementById('kategoriForm').classList.remove('hidden');
            document.getElementById('kategoriFormElement').action = `/admin/desa/master-tagihan/kategori/${id}`;
            document.getElementById('kategoriFormElement').method = 'POST';
            document.getElementById('kategoriNamaInput').value = nama;
            document.getElementById('kategoriSubmitBtn').textContent = 'Update';
            
            // Add hidden input for PUT method
            let methodInput = document.getElementById('kategoriFormElement').querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                document.getElementById('kategoriFormElement').appendChild(methodInput);
            } else {
                methodInput.value = 'PUT';
            }
        }

        function editSubKategori(id, kategoriId, nama) {
            // Show form and set to edit mode
            document.getElementById('subKategoriForm').classList.remove('hidden');
            document.getElementById('subKategoriFormElement').action = `/admin/desa/master-tagihan/sub-kategori/${id}`;
            document.getElementById('subKategoriFormElement').method = 'POST';
            document.getElementById('subKategoriKategoriSelect').value = kategoriId;
            document.getElementById('subKategoriNamaInput').value = nama;
            document.getElementById('subKategoriSubmitBtn').textContent = 'Update';
            
            // Add hidden input for PUT method
            let methodInput = document.getElementById('subKategoriFormElement').querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                document.getElementById('subKategoriFormElement').appendChild(methodInput);
            } else {
                methodInput.value = 'PUT';
            }
        }

        function editTagihan(id) {
            // Fetch tagihan data via AJAX
            fetch(`/admin/desa/master-tagihan/tagihan/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Show form and set to edit mode
                    document.getElementById('tagihanForm').classList.remove('hidden');
                    document.getElementById('tagihanFormElement').action = `/admin/desa/master-tagihan/tagihan/${id}`;
                    document.getElementById('tagihanFormElement').method = 'POST';
                    
                    // Populate form fields
                    document.getElementById('tagihanKategoriSelect').value = data.kategori_id;
                    document.getElementById('tagihanNominalInput').value = data.nominal_bulan_ini || '';
                    document.getElementById('tagihanStatusSelect').value = data.status;
                    document.getElementById('tagihanTanggalInput').value = data.tanggal;
                    document.getElementById('tagihanKeteranganInput').value = data.keterangan || '';
                    document.getElementById('tagihanSubmitBtn').textContent = 'Update Tagihan';

                    // Set Select2 value and trigger update
                    $('#pendudukSelect').val(data.nik).trigger('change');

                    // Load sub kategoris for selected kategori
                    loadSubKategoris(data.sub_kategori_id);

                    // Add hidden input for PUT method
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
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data tagihan');
                });
        }

        // Load sub kategoris based on selected kategori
        function loadSubKategoris(selectedSubKategoriId = null) {
            const kategoriId = document.getElementById('tagihanKategoriSelect').value;
            const subKategoriSelect = document.getElementById('tagihanSubKategoriSelect');
            
            // Clear existing options
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
                    .catch(error => console.error('Error:', error));
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
                    document.getElementById('detailTagihanContent').textContent = content.join('\n');
                    document.getElementById('detailTagihanModal').classList.remove('hidden');
                })
                .catch(() => {
                    alert('Gagal memuat detail tagihan');
                });
        }

        function closeDetailTagihanModal() {
            document.getElementById('detailTagihanModal').classList.add('hidden');
            document.getElementById('detailTagihanContent').textContent = '';
        }

        function updateStatus(tagihanId, newStatus) {
            if (!confirm('Yakin ingin mengubah status tagihan?')) {
                // Reset dropdown ke nilai sebelumnya
                location.reload();
                return;
            }

            fetch(`/admin/desa/master-tagihan/tagihan/${tagihanId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update warna dropdown berdasarkan status baru
                    const select = document.querySelector(`select[onchange="updateStatus(${tagihanId}, this.value)"]`);
                    select.className = `text-xs font-medium rounded-full border-0 focus:ring-2 focus:ring-blue-500 ${
                        newStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                        (newStatus === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                    }`;
                    
                    // Tampilkan notifikasi sukses
                    showNotification('Status berhasil diperbarui', 'success');
                } else {
                    showNotification('Gagal memperbarui status', 'error');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
                location.reload();
            });
        }

        function showNotification(message, type) {
            // Buat elemen notifikasi
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Hapus notifikasi setelah 3 detik
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }


    </script>

    <!-- jQuery and Select2 JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2 for penduduk dropdown
        $(document).ready(function() {
            // Initialize for create form
            $('#pendudukSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Penduduk',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Tidak ada data penduduk ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                },
                templateResult: function(penduduk) {
                    if (penduduk.loading) {
                        return penduduk.text;
                    }
                    
                    var $container = $(
                        "<div class='select2-result-penduduk clearfix'>" +
                            "<div class='select2-result-penduduk__name font-medium'></div>" +
                            "<div class='select2-result-penduduk__nik text-xs text-gray-500'></div>" +
                        "</div>"
                    );
                    
                    var name = penduduk.element.getAttribute('data-name') || penduduk.text;
                    var nik = penduduk.element.getAttribute('data-nik') || '';
                    
                    $container.find('.select2-result-penduduk__name').text(name);
                    $container.find('.select2-result-penduduk__nik').text(nik);
                    
                    return $container;
                },
                templateSelection: function(penduduk) {
                    if (penduduk.id === '') {
                        return penduduk.text;
                    }
                    var name = penduduk.element.getAttribute('data-name') || penduduk.text;
                    var nik = penduduk.element.getAttribute('data-nik') || '';
                    return name + ' - ' + nik;
                }
            });

            // Add event handler for Select2 change
            $('#pendudukSelect').on('change', function() {
                // This will be triggered when Select2 value changes
                console.log('Penduduk selected:', $(this).val());
            });

        });
    </script>
</x-layout>
