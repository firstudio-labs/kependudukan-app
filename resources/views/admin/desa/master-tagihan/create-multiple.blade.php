<x-layout>
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buat Tagihan Multiple</h1>
            <a href="{{ route('admin.desa.master-tagihan.tagihan.index') }}" 
               class="px-4 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke Daftar Tagihan
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form id="multipleTagihanForm" method="POST" action="{{ route('admin.desa.master-tagihan.tagihan.store-multiple') }}">
                @csrf
                
                <!-- Form Data Tagihan -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Data Tagihan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="kategoriSelect" required onchange="loadSubKategoris()"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori <span class="text-red-500">*</span></label>
                            <select name="sub_kategori_id" id="subKategoriSelect" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="">Pilih Sub Kategori</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Bulan Ini</label>
                            <input type="number" name="nominal" id="nominalInput" step="0.01" min="0" placeholder="Nominal Bulan Ini"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                            <p class="text-xs text-gray-500 mt-1">Carry-over tunggakan akan ditambahkan otomatis</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="statusSelect" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <option value="pending">Pending</option>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" id="tanggalInput" required
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keteranganInput" rows="2" placeholder="Keterangan"
                                class="block w-full p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Penduduk -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Pilih Penduduk</h2>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAll()" class="px-3 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="deselectAll()" class="px-3 py-1 text-xs rounded bg-gray-500 text-white hover:bg-gray-600">
                                Batal Pilih Semua
                            </button>
                        </div>
                    </div>

                    <!-- Filter dan Search Penduduk -->
                    <div class="mb-4 space-y-3">
                        <div class="flex flex-wrap gap-3">
                            <div class="flex-1 min-w-64 relative">
                                <input type="text" id="pendudukSearchInput" placeholder="Cari NIK atau nama penduduk..."
                                    class="block w-full p-2 pl-3 pr-10 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <select id="filterKepalaKeluarga" onchange="filterByKepalaKeluarga()"
                                    class="block p-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-[#7886C7] focus:border-[#7886C7]">
                                    <option value="all">Semua Penduduk</option>
                                    <option value="kepala_keluarga">Hanya Kepala Keluarga</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Penduduk dengan Checklist -->
                    <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-[#e6e8ed] sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 w-12">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleAllCheckboxes()" 
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">NIK</th>
                                    <th class="px-4 py-3">Nama Lengkap</th>
                                    <th class="px-4 py-3">Jenis Kelamin</th>
                                    <th class="px-4 py-3">Alamat</th>
                                </tr>
                            </thead>
                            <tbody id="pendudukTableBody">
                                @php
                                    $groupedByKK = collect($penduduks)->groupBy(function($p){
                                        return $p['kk'] ?? 'Tanpa KK';
                                    });
                                    $rowNo = 1;
                                @endphp
                                @forelse($groupedByKK as $kkNumber => $members)
                                    <!-- Header KK -->
                                    <tr class="bg-gray-100 kk-header" data-kk="{{ $kkNumber }}">
                                        <td class="px-4 py-2" colspan="6">
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs uppercase tracking-wide text-gray-700">
                                                    KK: <span class="font-semibold">{{ $kkNumber }}</span> 
                                                    <span class="ml-2 text-gray-500">( {{ count($members) }} anggota )</span>
                                                </div>
                                                <div>
                                                    <button type="button" class="text-xs text-blue-600 hover:underline" onclick="toggleKK('{{ $kkNumber }}')">Tutup/Buka</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($members as $index => $penduduk)
                                        <tr class="bg-white border-gray-300 border-b hover:bg-gray-50 penduduk-row kk-row-{{ $kkNumber }}" 
                                            data-name="{{ strtolower($penduduk['full_name'] ?? '') }}" 
                                            data-nik="{{ strtolower($penduduk['nik'] ?? '') }}"
                                            data-kk="{{ $kkNumber }}"
                                            data-kepala-keluarga="{{ $index === 0 ? 'true' : 'false' }}">
                                            <td class="px-4 py-3">
                                                <input type="checkbox" name="selected_niks[]" value="{{ $penduduk['nik'] ?? '' }}" 
                                                       class="penduduk-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            </td>
                                            <td class="px-4 py-3">{{ $rowNo++ }}</td>
                                            <td class="px-4 py-3 font-medium">{{ $penduduk['nik'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ $penduduk['full_name'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">
                                                @if(isset($penduduk['gender']))
                                                    @if(strtolower($penduduk['gender']) == 'l' || strtolower($penduduk['gender']) == 'laki-laki' || strtolower($penduduk['gender']) == 'male')
                                                        Laki-laki
                                                    @elseif(strtolower($penduduk['gender']) == 'p' || strtolower($penduduk['gender']) == 'perempuan' || strtolower($penduduk['gender']) == 'female')
                                                        Perempuan
                                                    @else
                                                        {{ $penduduk['gender'] }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $penduduk['address'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada data penduduk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-2 text-sm text-gray-600">
                        <span id="selectedCount">0</span> dari {{ count($penduduks) }} penduduk dipilih
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.desa.master-tagihan.tagihan.index') }}" 
                       class="px-6 py-2 text-sm rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn" disabled
                            class="px-6 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Buat Tagihan (<span id="submitCount">0</span> penduduk)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load sub kategoris saat kategori dipilih
        function loadSubKategoris() {
            const kategoriId = document.getElementById('kategoriSelect').value;
            const subKategoriSelect = document.getElementById('subKategoriSelect');
            subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
            
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
                    .catch(() => {});
            }
        }

        // Search penduduk
        function filterPenduduk() {
            const searchTerm = document.getElementById('pendudukSearchInput').value.toLowerCase();
            const kepalaKeluargaFilter = document.getElementById('filterKepalaKeluarga').value;
            const rows = document.querySelectorAll('.penduduk-row');
            const kkHeaders = document.querySelectorAll('.kk-header');

            // Sembunyikan/tampilkan baris penduduk sesuai pencarian dan filter kepala keluarga
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const nik = row.getAttribute('data-nik') || '';
                const isKepalaKeluarga = row.getAttribute('data-kepala-keluarga') === 'true';
                
                let shouldShow = true;
                
                // Filter berdasarkan pencarian
                if (searchTerm !== '' && !name.includes(searchTerm) && !nik.includes(searchTerm)) {
                    shouldShow = false;
                }
                
                // Filter berdasarkan kepala keluarga
                if (kepalaKeluargaFilter === 'kepala_keluarga' && !isKepalaKeluarga) {
                    shouldShow = false;
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });

            // Sembunyikan header KK jika semua anggota tersembunyi
            kkHeaders.forEach(header => {
                const kk = header.getAttribute('data-kk');
                const kkRows = document.querySelectorAll(`.kk-row-${CSS.escape(kk)}`);
                const anyVisible = Array.from(kkRows).some(r => r.style.display !== 'none');
                header.style.display = anyVisible ? '' : 'none';
            });

            updateSelectedCount();
        }

        // Filter berdasarkan kepala keluarga
        function filterByKepalaKeluarga() {
            filterPenduduk();
        }

        // Toggle semua checkbox
        function toggleAllCheckboxes() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.penduduk-checkbox');
            
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = selectAllCheckbox.checked;
                }
            });
            
            updateSelectedCount();
        }

        // Pilih semua
        function selectAll() {
            const checkboxes = document.querySelectorAll('.penduduk-checkbox');
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
            document.getElementById('selectAllCheckbox').checked = true;
            updateSelectedCount();
        }

        // Batal pilih semua
        function deselectAll() {
            const checkboxes = document.querySelectorAll('.penduduk-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateSelectedCount();
        }

        // Update count dan enable/disable submit button
        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.penduduk-checkbox:checked');
            const visibleRows = document.querySelectorAll('.penduduk-row:not([style*="display: none"])');
            const allVisibleChecked = visibleRows.length > 0 && 
                Array.from(visibleRows).every(row => {
                    const checkbox = row.querySelector('.penduduk-checkbox');
                    return checkbox && checkbox.checked;
                });
            
            document.getElementById('selectedCount').textContent = checkedBoxes.length;
            document.getElementById('submitCount').textContent = checkedBoxes.length;
            
            // Enable/disable submit button
            const submitBtn = document.getElementById('submitBtn');
            if (checkedBoxes.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled:bg-gray-300', 'disabled:cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled:bg-gray-300', 'disabled:cursor-not-allowed');
            }
            
            // Update select all checkbox
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            selectAllCheckbox.checked = allVisibleChecked;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Search input
            const searchInput = document.getElementById('pendudukSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', filterPenduduk);
            }
            
            // Filter kepala keluarga
            const kepalaKeluargaFilter = document.getElementById('filterKepalaKeluarga');
            if (kepalaKeluargaFilter) {
                kepalaKeluargaFilter.addEventListener('change', filterByKepalaKeluarga);
            }
            
            // Checkbox change events
            const checkboxes = document.querySelectorAll('.penduduk-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Toggle per KK
            window.toggleKK = function(kk) {
                const kkRows = document.querySelectorAll(`.kk-row-${CSS.escape(kk)}`);
                const willHide = Array.from(kkRows).some(r => r.style.display !== 'none');
                kkRows.forEach(r => {
                    // Jangan sembunyikan jika baris sedang match filter aktif
                    if (willHide) {
                        r.style.display = 'none';
                    } else {
                        // Tampilkan kembali baris, tapi tetap hormati filter aktif
                        const name = r.getAttribute('data-name') || '';
                        const nik = r.getAttribute('data-nik') || '';
                        const isKepalaKeluarga = r.getAttribute('data-kepala-keluarga') === 'true';
                        const term = document.getElementById('pendudukSearchInput').value.toLowerCase();
                        const kepalaKeluargaFilter = document.getElementById('filterKepalaKeluarga').value;
                        
                        let shouldShow = true;
                        
                        // Filter berdasarkan pencarian
                        if (term !== '' && !name.includes(term) && !nik.includes(term)) {
                            shouldShow = false;
                        }
                        
                        // Filter berdasarkan kepala keluarga
                        if (kepalaKeluargaFilter === 'kepala_keluarga' && !isKepalaKeluarga) {
                            shouldShow = false;
                        }
                        
                        r.style.display = shouldShow ? '' : 'none';
                    }
                });
                updateSelectedCount();
            }
            
            // Set default tanggal ke hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggalInput').value = today;
            
            // Initial count update
            updateSelectedCount();
        });

        // Form validation sebelum submit
        document.getElementById('multipleTagihanForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.penduduk-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu penduduk untuk membuat tagihan');
                return false;
            }
            
            if (!confirm(`Yakin ingin membuat tagihan untuk ${checkedBoxes.length} penduduk?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</x-layout>
